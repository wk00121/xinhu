<?php
/**
*	来自：信呼开发团队
*	作者：磐石(rainrock)
*	网址：http://www.rockoa.com/
*	系统的核心文件之一，处理工作流程模块的。
*/
class flowModel extends Model
{
	public $modenum;		//当前模块编号
	public $editcont= '';	//修改的记录
	public $id		= 0;	//当前单据ID
	public $moders;			//当前模块数组
	public $modeid;			//当前模块Id
	public $modename;		//当前模块名称
	public $sericnum;		//当前单据单号
	public $rs			= array();	//当前单据记录信息
	public $urs			= array();	//当前单据对应用户
	public $drs			= array();	//当前单据对应用户
	public $fieldsarr	= array();	//主表元素字段数组
	public $fieldsarra	= array();  //元素数组
	public $mwhere;				
	public $mtable;				//当前模块对应表
	public $uname;				//当前单据对应用户姓名
	public $uid		= 0;		//当前单据对应用户Id
	public $optid	= 0;		//当前当街对应操作用Id，如提交人Id
	public $isflow	= 0;		//当前模块是否有流程审核步骤
	public $ismobile= 0;		//是否移动的页面请求的
	public $minwidth	= 100;	//录入页面子表最小宽度
	
	
	//当初始化模块后调用
	protected function flowinit(){}
	
	//当初始化单据调用
	protected function flowchangedata(){}
	
	//当触发通知时处理
	protected function flowchangetodo(){}
	
	//删除单据时调用，$sm删除说明
	protected function flowdeletebill($sm){}
	
	//作废单据时调用，$sm作废说明
	protected function flowzuofeibill($sm){}
	
	//提交时调用
	protected function flowsubmit($na, $sm){}
	
	//添加日志记录调用$arr 添加数组
	protected function flowaddlog($arr){}
	
	protected function flowdatalog($arr){}
	
	//审核之前调用$zt 状态， $sm说明
	protected function flowcheckbefore(){}
	
	//审核完成后调用
	protected function flowcheckafter($zt, $sm){}
	
	//流程全部完成后调用
	protected function flowcheckfinsh($zt){}
	
	
	protected function flowgetfields($lx){}
	protected function flowgetoptmenu($opt){}
	
	//自定义审核人重新的方法$num 步骤单号
	protected function flowcheckname($num){}
	
	//审核步骤根据$num 编号判断是否需要审核
	protected function flowcoursejudge($num){}
	
	//操作单据
	protected function flowoptmenu($ors, $crs){}
	
	//自定义是否可查看本单据
	protected function flowisreadqx(){return false;}
	
	//自定义是否可以删除权限，如返回0不能删除,1可删除，其他值无效
	protected function flowisdeleteqx(){return false;}
	
	//已弃用
	protected function flowprintrows($r){return $r;}
	
	//子表数据替换处理$lx=0编辑时,1展示时
	protected function flowsubdata($r, $lx=0){return $r;}
	
	//默认推送格式从写
	protected function flownexttodo($type){return false;}
	
	//单据判断条件从写$lx类型，$uid用户Id
	protected function flowbillwhere($lx, $uid){return '';}
	
	protected $flowweixinarr	= array();
	protected $flowviewufieds	= 'uid';
	
	protected $flowfieldstype	= 0; //获取字段类型0默认*，1根据字段元素设置得到字段
	
	//初始化单据可替换其他属性，$lx,0默认,1详情展示，2列表显示
	public function flowrsreplace($rs){return $rs;}
	
	//编辑的时候替换
	protected function flowrsreplaceedit($rs){return $rs;}
	
	//默认状态1时可以就算审核通过，默认是1
	protected $flowstatusarr	= array(1);
	
	//子表样式，可以改成print打印
	protected $subsubdatastyle	= '';
	
	//默认排序如：id,desc
	public $defaultorder	= '';
	
	public function echomsg($msg)
	{
		if(!isajax())exit($msg);
		showreturn('', $msg, 201);
		exit();
	}
	
	/**
	*	初始化流程信息
	*/
	public function initdata($num, $id=null)
	{
		if(is_array($num)){
			$this->moders= $num;
		}else{
			$this->moders 	= m('flow_set')->getone(is_numeric($num) ? $num : "`num`='$num'");
			if(!$this->moders)$this->echomsg('模块['.$num.']不存在，请到[流程模块列表]下添加');
		}
		$table 			= $this->moders['table'];
		$this->modeid	= $this->moders['id'];
		$this->modenum	= $this->moders['num'];
		$this->modename	= $this->moders['name'];
		$this->isflow	= (int)$this->moders['isflow'];
		$this->settable($table);
		$this->mtable		= $table;
		$this->viewmodel	= m('view');
		$this->chaomodel	= m('flow_chao');
		$this->billmodel	= m('flow_bill');
		$this->todomodel	= m('flow_todo');
		$this->todosmodel	= m('flowtodo');
		$this->flogmodel 	= m('flow_log');
		$this->checksmodel	= m('flow_checks');
		$this->cnamemodel	= m('flowcname');
		$this->wheremodel	= m('where');
		$this->adminmodel	= m('admin');
		$this->remindmodel	= m('remind'); //单据提醒表
		$this->option		= m('option');
		$this->tfieldsarra();
		$this->flowinit();
		if($id==null)return $this;
		$this->loaddata($id, true);
		return $this;
	}
	
	private function tfieldsarra()
	{
		$rows	= m('flow_element')->getrows("`mid`='$this->modeid' and `iszb`=0",'`name`,`fields`,`isbt`,`fieldstype`,`savewhere`,`data`,`iszb`,`issou`,`islu`,`islb`,`isonly`','`sort`');
		$this->fieldsarr = array();
		if($rows)foreach($rows as $k=>$rs){
			if($rs['islu']==1)$this->fieldsarr[] = $rs;
		}
		$this->fieldsarra	= $rows;
	}
	
	/**
	*	列表上要搜索的的，此方法仅用到在：webmain/flow/input/inputAction.php 行607上返回，模块接口可重写这个方法
	*/
	public function flowsearchfields()
	{
		$arr = array();
		if($this->isflow==1){
			$arr[] = array('name'=>'申请人...','fields'=>'uid');
			$arr[] = array('name'=>'提交人...','fields'=>'optid');
		}
		return $arr;
	}
	
	/**
	*	初始化单据数据
	*	id 单据Id $ispd 是否判断有查看权限
	*/
	public function loaddata($id, $ispd=true)
	{
		$this->id		= (int)$id;
		$this->mwhere	= "`table`='$this->mtable' and `mid`='$id'";
		$this->rs 		= $this->getone($id);
		$this->uname	= '';
		if(!$this->rs)$this->echomsg('数据记录不存在了');
		$this->rs['base_name'] 		= '';
		$this->rs['base_deptname'] 	= '';
		if(isset($this->rs['uid']))$this->uid = $this->rs['uid'];
		if(!isset($this->rs['applydt']))$this->rs['applydt'] = '';
		if(!isset($this->rs['status']))$this->rs['status']	 = 1;
		$uisfield 		= property_exists($this, 'uidfields') ? $this->uidfields : 'optid';
		if($this->uid==0 && isset($this->rs[$uisfield]))$this->uid = $this->rs[$uisfield];
		$this->optid 	= isset($this->rs['optid']) ? $this->rs['optid'] : $this->uid;
		$this->urs 		= $this->adminmodel->getone($this->uid,'id,name,user,deptid,deptname,deptallname,face,ranking,superid,superpath,superman,deptpath');
		if($this->isempt($this->rs['applydt'])&&isset($this->rs['optdt']))$this->rs['applydt']=substr($this->rs['optdt'],0,10);
		if($this->urs){
			$this->drs		= $this->db->getone('[Q]dept',"`id`='".$this->urs['deptid']."'");
			$this->uname	= $this->urs['name'];
			$this->rs['base_name']		= $this->uname;
			if($this->drs){
				$this->rs['base_deptname']	= $this->drs['name'];
			}
		}
		$this->sericnum	= '';
		$this->billrs 	= $this->billmodel->getone($this->mwhere);
		if($this->billrs){
			$this->sericnum = $this->billrs['sericnum'];
			if(isempt(arrvalue($this->billrs,'uname'))){
				$this->savebill();
			}
			if($this->billrs['status']!=$this->rs['status'])$this->billmodel->update('`status`='.$this->rs['status'].'', $this->billrs['id']);//状态不一样更新一下
		}else{
			if($this->isflow==1)$this->savebill();
		}
		
		if($ispd)$this->isreadqx();

		$this->rssust	= $this->rs;
		$this->flowchangedata();
		
		$this->rs['base_systitle']	= TITLE;//系统名称
		$this->rs['base_modename']	= $this->modename;
		$this->rs['base_sericnum']	= $this->sericnum;
		$this->rs['base_summary']	= $this->getsummary();
	}
	
	/**
	*	当前模块我待办数量
	*/
	public function getdaiban()
	{
		$s 	= $this->rock->dbinstr('nowcheckid', $this->adminid);
		$to = $this->billmodel->rows('`modeid`='.$this->modeid.' and `isdel`=0 and `status` not in(1,2) and '.$s.'');
		return $to;
	}
	
	/**
	*	判断单据是否可以查看
	*/
	public function isreadqx($glx=0)
	{
		$bo = false;
		if($this->adminid==1)$bo=true;//为1的超级管理员可查看
		if($this->uid==$this->adminid && $this->adminid>0)$bo=true;
		if(!$bo && $this->isflow==1){
			if($this->billrs){
				$allcheckid = $this->billrs['allcheckid'];
				if(contain(','.$allcheckid.',',','.$this->adminid.','))$bo = true;
			}
		}
		//可以查看下属人员的
		if(!$bo){
			if($this->urs && contain($this->urs['superpath'],'['.$this->adminid.']'))$bo = true;
		}
		//通知表上的权限
		if(!$bo){
			$tos 	= $this->todosmodel->rows($this->mwhere);
			if($tos>0)$bo=true;
		}
		//抄送表上的
		if(!$bo){
			$tos 	= $this->chaomodel->rows($this->mwhere.' and '.$this->rock->dbinstr('csnameid', $this->adminid).'');
			if($tos>0)$bo=true;
		}
		if(!$bo)$bo = $this->flowisreadqx(); //自定义查看权限
		//权限设置上的
		if(!$bo){
			$where 	= $this->viewmodel->viewwhere($this->moders, $this->adminid, $this->flowviewufieds);
			$tos 	= $this->rows("`id`='$this->id' $where ");
			if($tos>0)$bo=true;
		}
		if($glx==1)return $bo;
		if(!$bo)$this->echomsg('无权限查看模块['.$this->modenum.'.'.$this->modename.']'.$this->uname.'的数据，'.c('xinhu')->helpstr('cxqx').'');
	}
	
	/**
	*	判断单据是否可以编辑
	*/
	public function iseditqx()
	{
		$bo = 0;
		if($bo==0 && $this->isflow==1){
			if($this->billrs && ($this->uid == $this->adminid || $this->optid==$this->adminid)){
				if($this->billrs['nstatus']==0 || $this->billrs['nstatus']==2){
					$bo = 1;
				}
			}
			if($this->rs['status']==1)$bo=0;
		}
		if($bo==0){
			$where 	= $this->viewmodel->editwhere($this->moders, $this->adminid);
			$tos 	= $this->rows("`id`='$this->id'  $where ");
			if($tos>0)$bo=1;
		}
		return $bo;
	}
	
	/**
	*	判断当前单据是否可以删除
	*/
	public function isdeleteqx()
	{
		$bo = 0;
		if($bo==0 && $this->isflow==1){
			if($this->billrs && ($this->uid == $this->adminid || $this->optid==$this->adminid)){
				if($this->billrs['nstatus']==0 || $this->billrs['nstatus']==2){
					$bo = 1;
				}
			}
			if($this->rs['status']==1)$bo=0;
		}
		if($bo==0){
			$where 	= $this->viewmodel->deletewhere($this->moders, $this->adminid);
			$tos 	= $this->rows("`id`='$this->id'  $where ");
			if($tos>0)$bo=1;
		}
		
		$isd 	= $this->flowisdeleteqx();
		if(is_numeric($isd) && $isd <= 1)$bo = $isd;
		
		return $bo;
	}
	
	/**
	*	获取录入字段
	*/
	public function getfields($lx=0)
	{
		$fields = array();
		$farr 	= $this->db->getrows('[Q]flow_element',"`mid`='$this->modeid' and `iszb`=0 and `iszs`=1",'`fields`,`name`','sort,id');
		foreach($farr as $k=>$rs)$fields[$rs['fields']] = $rs['name'];
		$fters	= $this->flowgetfields($lx);
		if(is_array($fters))$fields = array_merge($fields, $fters);
		return $fields;
	}
	
	/**
	*	获取录入页面地址
	*/
	public function getinputurl($num='',$mid=0,$can=array())
	{
		if($num=='')$num = $this->modenum;
		$xa  = 'lu';
		if($this->ismobile==1)$xa = 'lum';
		$url = 'index.php?a='.$xa.'&m=input&d=flow&num='.$num.'&mid='.$mid.'';
		if(is_array($can)){
			foreach($can as $k=>$v)$url.='&'.$k.'='.$v.'';
		}else{
			$url .= '&'.$can.'';
		}
		return $url;
	}
	
	public function replacepbr(&$arr, $k)
	{
		$val = arrvalue($arr, $k);
		if(!c('html')->ishtml($val))$arr[$k] = str_replace("\n",'<br>', $val);
		return $arr;
	}
	
	/**
	*	读取展示数据
	*	$lx 0pc, 1移动
	*/
	public function getdatalog($lx=0)
	{
		m('log')->addread($this->mtable, $this->id);
		$this->todosmodel->biaoyidu($this->adminid, $this->modenum, $this->id);
		$fobj	 		 = m('file');
		$this->ismobile  = $lx;
		$arr['modename'] = $this->modename;
		$arr['title'] 	 = $this->modename;
		$arr['modeid']   = $this->modeid;
		$arr['modenum']  = $this->modenum;
		$arr['mid']  	 = $this->id;
		$arr['status']   = arrvalue($this->rs,'status');
		$arr['logarr']	 = $this->getlog();
		$contview 	 	 = '';
		$path 			 = ''.P.'/flow/page/view_'.$this->modenum.'_'.$lx.'.html';
		$fstr			 = $fobj->getstr($this->mtable, $this->id, 2);
		$issubtabs		 = 0;
		if($fstr != ''){
			$this->rs['file_content'] 	= $fstr;
		}
		
		//$this->replacepbr($this->rs, 'explain');
		//$this->replacepbr($this->rs, 'content');
		
		$data 			= $this->flowrsreplace($this->rs, 1);
		//读取多行子表
		$subdata 		= $this->getsuballdata(1);
		foreach($subdata as $zb=>$da){
			$sub 						= $da['fields'];
			$data[$sub] 				= $this->getsubdata($zb,$da['data'],arrvalue($data,'subdatays'.$zb.'',$lx));
			$data[''.$sub.'_style'] 	= 'padding:0';
		}
		
		//文件字段替换上传和上传图片的
		foreach($this->fieldsarra as $k=>$rs){
			$fid 	= $rs['fields'];
			$fty	= $rs['fieldstype'];
			if($fty=='uploadfile'){
				$fval 	= $this->rock->arrvalue($data, $fid);
				if(isempt($fval))$fval='0';
				$data[$fid] = '';
				if($fval!='0'){
					$data[$fid] = $fobj->getstr('', '', 2, "`id` in($fval)");
				}
			}
			if($fty=='uploadimg'){
				$fval 	= $this->rock->arrvalue($data, $fid);
				if(!isempt($fval) && substr($fval,0,4)!='<img'){
					if(substr($fval,0,4)!='http')$fval=''.URL.$fval.'';
					$data[$fid] = '<img src="'.$fval.'" onclick="c.showviews(this)" height="100">';	
				}
			}
			//文本域自动换行
			if($fty=='textarea'){
				$this->replacepbr($data, $fid);
			}
		}

		//使用了自定的展示模板
		if(file_exists($path)){
			$contview 	 = file_get_contents($path);
			$_logarr	 = array();
			foreach($arr['logarr'] as $k1=>$rs1)$_logarr[$rs1['id']] = $rs1;			
			//读取流程审核步骤信息
			$logrows 	 	= $this->flogmodel->getrows($this->mwhere.' and `courseid`>0 GROUP BY `courseid`','`courseid`,max(id)id','id desc');
			foreach($logrows as $k2=>$rs2){
				$rs3 		= $_logarr[$rs2['id']];
				$_coid 		= $rs2['courseid'];
				if(!isempt($rs3['qmimg']))$rs3['name'] = '<img height="30" width="70" src="'.$rs3['qmimg'].'">';
				$data['course'.$_coid.'_name'] 	= $rs3['name'];
				$data['course'.$_coid.'_zt'] 	= '<font color="'.$rs3['color'].'">'.$rs3['statusname'].'</font>';
				if(isempt($rs3['sm']))$rs3['sm'] = $rs3['statusname'];
				$data['course'.$_coid.'_sm'] 	= $rs3['sm'];
				$data['course'.$_coid.'_dt'] 	= $rs3['checkdt'];
			}
			$contview 	 	= $this->rock->reparr($contview, $data);
		}
		$arr['isdefaultview'] = 0;
		if($this->isempt($contview)){
			$arr['isdefaultview'] = 1;
			$_fields		 = array();
			if($this->isflow==1){
				$_fields['base_sericnum'] 	= '单号';
				$_fields['base_name'] 		= '申请人';
				$_fields['base_deptname'] 	= '申请人部门';
			}
			$fields			 = array_merge($_fields, $this->getfields($lx));
			if($lx==0)foreach($fields as $k=>$rs){$data[''.$k.'_style'] = 'width:75%';break;}
			if($fstr!='')$fields['file_content'] 			= '相关文件';
			foreach($subdata as $zb=>$da){
				$fields[$da['fields']]	= $da['name'];
			}
			//if(!isset($fields['optdt']))$fields['optdt']	= '操作时间';
			
			$contview 	= c('html')->xiangtable($fields, $data, getconfig('bcolorxiang'));
			$contview 	= '<div align="center">'.$contview.'</div>';
			
			//移动默认展示
			if($lx==1){
				$contview	= '';
				if($this->isflow=='1'){
					$contview.='<div class="r-border-b">
					<div class="blank5"></div>
					<table><tr>
						<td width="50" align="left"><img style="height:40px;width:40px;border-radius:50%" src="'.$this->urs['face'].'"></td>
						<td><div>'.$this->urs['name'].'<font color="#888888">('.$this->urs['ranking'].')</font></div><div style="padding-top:5px">'.$this->urs['deptallname'].'</div></td>
					</tr></table>
					<div style="margin:5px 0px" class="blank10"></div>
					</div>';
					unset($fields['base_name']);
					unset($fields['base_deptname']);
				}
				$contview 	.= '<div><table width="100%">';
				foreach($fields as $f=>$n){
					$vs = arrvalue($data, $f);
					if(!isempt($vs)){
						//子表
						if(substr($f,0,7)=='subdata'){
							$contview .= '<tr><td colspan="2"><div style="padding:5px" align="left"><div><b>'.$n.'</b></div>'.$vs.'</div></td></tr>';
						}else{
							$contview .= '<tr><td nowrap><div align="right" style="color:#888888;padding:5px 0px">'.$n.'：</div></td><td width="95%"><div style="padding:5px" align="left">'.$vs.'</div></td></tr>';
						}
					}
				}
				$contview  .= '</table></div>';
			}
		}
		$arr['contview'] = $contview;
		$arr['readarr']	 = m('log')->getreadarr($this->mtable, $this->id); //已读人员
		
		$arr['isedit'] 	 = $this->iseditqx();
		$arr['isdel'] 	 = $this->isdeleteqx();
		$arr['isflow'] 	 = $this->isflow;
		$arr['modename'] = $this->modename;
		$arr['ischehui'] = $this->ischehui();
		
		$ztass 				= $this->getnowstatus();
		$arr['statustext']	= $ztass[0];
		$arr['statuscolor']	= $ztass[1];
		$arr['optmenu']		= $this->getoptmenu(1); //获取在详情上展示菜单
		
		$arr['isgbjl']		= (int)$this->rock->arrvalue($this->moders,'isgbjl','0'); //是否关闭操作记录
		$arr['isgbcy']		= (int)$this->rock->arrvalue($this->moders,'isgbcy','0'); //是否不显示查阅记录
		
		//判断是否需要回执(2018-07-06)新增
		$receiptrs	= false;
		$receiptrow	= m('receipt')->getall("`modenum`='$this->modenum' and `mid`='$this->id' and `status`=1");
		foreach($receiptrow as $k=>$hrs){
			$uid1 = ','.$this->adminid.',';
			if(!contain(','.$hrs['receid'].',', $uid1))continue;
			if(!contain(','.$hrs['receids'].',', $uid1)){
				$receiptrs = array(
					'id'	  => $hrs['id'],
					'optname' => $hrs['optname'],
				);
				break;
			}
		}
		
		
		$arr['flowinfor']	= array(); //流程信息
		$arr['readunarr']	= array(); //未读人员
		$arr['receiptrs']	= $receiptrs; //回执确认
		
		if($this->isflow==1)$arr['flowinfor']= $this->getflowinfor();
		if(isset($data['title']))$arr['title'] = $data['title'];
		$_oarr 			 = $this->flowdatalog($arr);
		if(is_array($_oarr))foreach($_oarr as $k=>$v)$arr[$k]=$v;
		return $arr;
	}
	//$lx=0PC，1移动
	private function getsubdata($xu, $rows, $lx=0)
	{
		$iscz			= 0;
		$iszb			= $xu+1;
		$fields			= 'subdata'.$xu.'';
		$subrows 		= $this->db->getrows('[Q]flow_element','`mid`='.$this->modeid.' and `iszb`='.$iszb.' and `iszs`=1','`fields`,`name`','`sort`');
		$cont 			= '';
		if($this->db->count > 0){
			$iscz		= 1;
			$headstr	= '@xuhaos,,center'; $colorbb = getconfig('bcolorxiang', '#cccccc');
			//if($lx==1){$headstr = '';$colorbb = 'black';}
			foreach($subrows as $k=>$rs)$headstr.='@'.$rs['fields'].','.$rs['name'].'';
			foreach($rows as $k=>$rs)$rows[$k]['xuhaos'] = $k+1;
			$slex 		= ($lx==0) ? 'noborder':'';
			if($this->subsubdatastyle!='')$slex = $this->subsubdatastyle;
			$cont 	 	= c('html')->createrows($rows, substr($headstr,1), $colorbb, $slex);
		}
		return $cont;
	}
	
	/**
	*	判断当前是否可以撤回
	*	撤回条件，审核未同意，最后一步是当前人审核的而为同意，2小时之内
	*/
	public function ischehui()
	{
		$is 	= 0;
		if($this->rs['status']==1)return $is;
		$where 	= "".$this->mwhere." and `courseid`>0 order by `id` desc";
		$rs 	= $this->flogmodel->getone($where);
		$time 	= time()-2*3600;
		if($rs && $rs['checkid']==$this->adminid && $rs['status']!=2 && strtotime($rs['optdt'])>$time )$is = $rs['id'];
		return $is;
	}
	
	/**
	*	撤回操作
	*/
	public function chehui($sm='')
	{
		$id = $this->ischehui();
		if($id==0)return '当前不允许撤回操作';
		$this->flogmodel->update('courseid=0', "`id`='$id'");
		$this->addlog(array(
			'explain' 	=> $sm,
			'name'		=> '撤回'
		));
		$barr = $this->getflow(false);
		$this->checksmodel->delete($this->mwhere.' and `optid`='.$this->adminid.'');//删除我指定的人
		//当前审核人空
		if(isempt($barr['nowcheckid'])){
			$courseid = $barr['nowcourseid'];
			$this->addcheckname($courseid, $this->adminid, $this->adminname, false,2);
			$barr = $this->getflow(false);
		}
		$this->getflowsave($barr);
		return 'ok';
	}
	
	/**
	*	读取编辑数据
	*/
	public function getdataedit()
	{
		$fobj 			= m('file');
		$arr['data'] 	= $this->flowrsreplaceedit($this->rssust);
		$arr['table'] 	= $this->mtable;
		$arr['tables'] 	= $this->moders['tables'];
		$arr['modeid'] 	= $this->modeid;
		$arr['isedit'] 	= $this->iseditqx();
		$arr['isflow'] 	= $this->isflow;
		$arr['user'] 	= $this->urs;
		$arr['status'] 	= $this->rs['status'];
		$arr['filers'] 	= $fobj->getfile($this->mtable,$this->id);
		$arr['subdata'] = $this->getsuballdata();
		$uploadfile		= $this->rock->post('uploadfile');
		$filearr 		= array();
		foreach($this->fieldsarr as $k=>$rs){
			
			//读取文件详情
			if($rs['fieldstype']=='uploadfile'){
				$fid 	= $rs['fields'];
				$fval 	= arrvalue($this->rssust, $fid);
				if(isempt($fval))$fval='0';
				if($fval != '0'){
					$fvalsa = explode(',', $fval);
					foreach($fvalsa as $fval1)$filearr['f'.$fval1.''] = $fobj->getone($fval1,'filename,id,filesizecn,fileext,optname,thumbpath');
				}
			}
		}
		$arr['filearr'] = $filearr;
		$ztarr 			= $this->getnowstatus();
		$arr['statustext'] 	= $ztarr[0];
		$arr['statuscolor'] = $ztarr[1];
		return $arr;
	}
	
	/*
	*	读取流程信息
	*/
	public function getflowinfor()
	{
		$ischeck = 0;
		$ischange= 0;
		$str	 = '';
		$arr 	 = $this->getflow();
		$nstatus = $this->rs['status'];
		$isturn  = $this->rs['isturn'];
		$nowcheckid = ','.$arr['nowcheckid'].',';
		if($isturn==1 && $nstatus !=1 && contain($nowcheckid, ','.$this->adminid.',') && !in_array($nstatus, array(2,5))){
			$ischeck = 1;
		}
		$logarr = $this->getlog();
		$nowcur = $this->nowcourse;
		if(arrvalue($this->nextcourse,'checktype')=='change'){
			$ischange = 1; //需要自己选择下一步处理人
		}
		$sarr['ischeck'] 		= $ischeck;
		$sarr['ischange'] 		= $ischange;
		$sarr['nowcourse'] 		= $nowcur;
		$sarr['iszhuanban'] 	= arrvalue($nowcur,'iszf',0);
		$sarr['nextcourse'] 	= $this->nextcourse;
		$sarr['nstatustext'] 	= $arr['nstatustext'];
		if($isturn==0)$sarr['nstatustext'] = '<font color="#ff6600">待提交</font>';
		
		//读取当前审核表单
		$_checkfields	= arrvalue($nowcur,'checkfields');
		$checkfields	= array();
		if($ischeck == 1 && !isempt($_checkfields)){
			
			$inputobj			= c('input');
			$inputobj->flow 	= $this;
			$inputobj->mid 		= $this->id;
			$inputobj->urs		= $this->urs;
			$elwswhere			= "`mid`='$this->modeid' and `iszb`=0 and instr(',$_checkfields,', concat(',',`fields`,','))>0";
			$infeidss  = $inputobj->initFields($elwswhere);
			foreach($infeidss as $_fs=>$fsva){
				$_sfes = $fsva['fields'];
				$_type = $fsva['fieldstype'];
				$showinpus = 1;
				if($_type=='hidden' || $_type=='fixed')$showinpus=2;
				$_val  = arrvalue($this->rssust, $_sfes);
				$cheo  = (substr($_type,0,6)=='change' && !isempt($fsva['data']));
				if($cheo)$_val.='|'.arrvalue($this->rssust, $fsva['data']).''; //默认值
				$checkfields[$_sfes] = array(
					'inputstr' 	=> $inputobj->getfieldcontval($_sfes, $_val),
					'name' 		=> $fsva['name'],
					'fieldstype'=> $_type,
					'fieldsarr' => $fsva,
					'showinpus' => $showinpus
				);
				if($cheo){
					$_sfes = $fsva['data'];
					$checkfields[$_sfes] = array(
						'inputstr' 	=> '',
						'name' 		=> $fsva['name'].'id',
						'fieldstype'=> $_type,
						'fieldsarr' => false,
						'showinpus' => 2
					);
				}
			}
		}
		$sarr['checkfields']	= $checkfields;
		if($nstatus==2)$sarr['nstatustext'] ='<font color="#AB47F7">待提交人处理('.$this->urs['name'].')</font>';
		$loglen 				= count($logarr);
		//<img align="absmiddle" class="face" src="'.$rs['face'].'">
		foreach($logarr as $k=>$rs){
			if($rs['courseid']>0){
				$sty = '';
				$col = $rs['color'];
				if($str!='')$str.=' → ';
				$str.='<span style="'.$sty.'">'.$rs['actname'].'('.$rs['name'].'<font color="'.$col.'">'.$rs['statusname'].'</font>)</span>';
			}
		}
		//未同意
		if($nstatus=='2'){
			if($str!='')$str.=' → ';
			$str.= $sarr['nstatustext'];
		}else if($nstatus=='5'){
			if($str!='')$str.=' → ';
			$sarr['nstatustext'] = '<font color="#888888">已作废</font>';
			$str.= '<font color="#888888">已作废</font>';
		}else{
			foreach($this->flowarr as $k=>$rs){
				if($rs['ischeck']==0){
					$sty = 'color:#888888';
					if($rs['isnow']==1)$sty='font-weight:bold;color:#800000';
					if($str!='')$str.=' <font color=#888888>→</font> ';
					$str.='<span style="'.$sty.'">'.$rs['name'].'';
					if(!isempt($rs['nowcheckname']))$str.='('.$rs['nowcheckname'].')';
					$str.='</span>';
				}
			}
		}	
		
		$sarr['flowcoursestr'] 	= $str;
		if($nstatus==1)$sarr['nstatustext'] = $this->getnowstatus(1); //完成后状态
		
		$actstr	= ',同意|green,不同意|red';
		if(isset($nowcur['courseact']) ){
			$actstrt = $nowcur['courseact'];
			if(!isempt($actstrt))$actstr = ','.$actstrt;
		}
		$act 	= c('array')->strtoarray($actstr);
		foreach($act as $k=>$as1)if($k>0 && $as1[0]==$as1[1])$act[$k][1]='';
		$sarr['courseact'] 		= $act;
		$nowstatus				= $this->rs['status'];
		if($this->isflow==1 && $this->rs['isturn']==0)$nowstatus=3;
		$sarr['nowstatus']		= $nowstatus;
		
		//不同意退回可选择人员
		$step = $this->rock->arrvalue($nowcur, 'step','0');
		$tuicourse = $this->flogmodel->getall($this->mwhere.' and `courseid`>0 and `valid`=1 and `status`=1 and `step`<'.$step.'','`id`,`checkname`,`name`','`step` desc');
		$sarr['tuicourse']		= $tuicourse;
		
		return $sarr;
	}
	
	/**
	*	更新单据状态
	*/
	public function updatestatus($zt)
	{
		$this->update('`status`='.$zt.'', $this->id);
		$this->billmodel->update('`status`='.$zt.'', $this->mwhere);
	}
	
	/**
	*	获取状态列表数组
	*/
	public function getstatusarr()
	{
		return $this->getstatus(null, '','',2);
	}
	
	/**
	*	获取某单据当前状态
	*	$rs 单据数据，$statusstr设置的状态，$other 当前审核人
	*/
	public function getstatus($rs, $statusstr='',$other='', $glx=0)
	{
		$statustext	= $statuscolor = '';
		if($statusstr=='')$statusstr=$this->rock->arrvalue($this->moders,'statusstr');
		$statusara 	= array();
		$colorsa   	= array('blue','green','red','#ff6600','#526D08','#888888','','','','','','','','','','','','','');
		if(isempt($statusstr))$statusstr =  '待?处理|blue,已审核|green,不同意|red';
		$nowcheckname= arrvalue($rs,'nowcheckname');
		if(isempt($other))$other = $nowcheckname;

		$statusar  	= c('array')->strtoarray($statusstr);
		foreach($statusar as $k=>$v){
			if($v[0]==$v[1])$v[1]= $colorsa[$k];
			$statusara[$k] = $v;
		}
		$statusara[5] 	= array('已作废','#888888');
		$statusara[23] 	= array('退回','#17B2B7');
		if($glx==2)return $statusara;
		
		$isturn 	= -1;
		if(isset($rs['isturn']))$isturn = (int)$rs['isturn'];
		$zt 		= $this->rock->arrvalue($rs, 'status');
		if($isturn==0){
			$statustext	= '待提交';
			$statuscolor= '#ff6600';
		}elseif(!isempt($zt)){
			if(isset($statusara[$zt])){
				$statustext  = $statusara[$zt][0];
				$statuscolor = $statusara[$zt][1];
			}
		}
		
		if(contain($statustext,'?')){
			$statusstr = str_replace('?', '<font color="'.$statuscolor.'">'.$other.'</font>', $statustext);
		}else{
			$statusstr = '<font color="'.$statuscolor.'">'.$statustext.'</font>';
		}
		if($glx==1){
			return $statusstr;
		}
		return array(str_replace('?', $other, $statustext), $statuscolor, $zt, $statusstr);
	}
	
	public function getstatusstr($rs)
	{
		return $this->getstatus($this->rs, '','', $glx);
	}
	
	/**
	*	当前单击状态
	*/
	public function getnowstatus($glx=0)
	{
		return $this->getstatus($this->rs, '','', $glx);
	}
	
	private $getlogrows = array();
	public function getlog()
	{
		if($this->getlogrows)return $this->getlogrows;
		$rows = $this->flogmodel->getrows($this->mwhere, '`checkname` as `name`,`checkid`,`name` as actname,`optdt`,`explain`,`statusname`,`courseid`,`color`,`id`,`qmimg`','`id` asc');
		$uids = $idss = '';
		$dts  = c('date');
		$fo   = m('file');
		foreach($rows as $k=>$rs){
			$uids.=','.$rs['checkid'].'';
			$idss.=','.$rs['id'].'';
			$col = $rs['color'];
			if(isempt($col))$col='green';
			if(contain($rs['statusname'],'不'))$col='red';
			$rows[$k]['color'] 		= $col;
			$rows[$k]['checkdt'] 	= $rs['optdt'];
			$rows[$k]['sm']  		= $rs['explain'];	
			if(!isempt($rs['qmimg'])){
				//签名是手写的
				if(!contain($rs['qmimg'],'.')){
					$qmimg 	= ''.UPDIR.'/'.date('Y-m').'/qmimg'.$rs['id'].'.png';
					$bo 	= true;
					if(!file_exists($qmimg)){
						$bar= explode(',', $rs['qmimg']);
						$bo = $this->rock->createtxt($qmimg, base64_decode($bar[1]));
					}
					if(!$bo){
						$qmimg = $rs['qmimg'];
					}else{
						$qmimg = ''.URL.''.$qmimg.'';
					}
				}else{
					$qmimg	= $rs['qmimg'];
					if(!file_exists($qmimg)){
						$qmimg = '';
					}else{
						$qmimg = ''.URL.''.$qmimg.'';
					}
				}
				$rows[$k]['qmimg']  = $qmimg;
				if(!isempt($qmimg))$rows[$k]['explain']= '<img height="24" src="'.$qmimg.'">'.$rs['explain'].'';	
				
			}				
		}
		//读取相关文件
		if($idss!=''){
			$farr = $fo->getfile('flow_log', substr($idss, 1));
			if($farr)foreach($rows as $k=>$rs){
				$fstr 		= $fo->getallstr($farr, $rs['id'],2);
				$rows[$k]['explain']= $this->strappend($rs['explain'], $fstr, '<br>');
			}
		}
		//读取对应人员头像
		if($uids!=''){
			$rows = m('admin')->getadmininfor($rows, substr($uids, 1), 'checkid');
		}
		$this->getlogrows = $rows;
		return $rows;
	}
	
	public function addlog($arr=array())
	{
		$addarr	= array(
			'table'		=> $this->mtable,
			'mid'		=> $this->id,
			'checkname'	=> $this->adminname, 
			'checkid'	=> $this->adminid, 
			'optdt'		=> $this->rock->now,
			'courseid'	=> '0',
			'status'	=> '1',
			'ip'		=> $this->rock->ip,
			'web'		=> $this->rock->web,
			'modeid'	=> $this->modeid
		);
		foreach($arr as $k=>$v)$addarr[$k]=$v;
		$this->flogmodel->insert($addarr);
		$ssid = $this->db->insert_id();
		$fileid			= $this->rock->post('fileid');
		if($fileid!='')m('file')->addfile($fileid, 'flow_log', $ssid);
		$logfileid		= $this->rock->post('logfileid');
		if($logfileid!='')m('file')->addfile($logfileid, $this->mtable, $this->id);
		$addarr['id'] 	= $ssid;
		$this->flowaddlog($addarr);
		return $ssid;
	}
	
	public function submit($na='', $sm='')
	{
		if($na=='')$na='提交';
		$isturn	 = 1;
		if($na=='保存')$isturn	= 0;
		$this->addlog(array(
			'name' 		=> $na,
			'explain' 	=> $sm
		));
		if($this->isflow == 1){
			$marr['isturn'] = $isturn;
			$marr['status'] = 0;
			$this->rs['status'] = 0;
			$this->update($marr, $this->id);
			
			//每次编辑判断是否重新开始走审批。
			if(arrvalue($this->moders,'isflowlx')=='1'){
				$this->checksmodel->delete($this->mwhere);
				$this->flogmodel->update('`valid`=0', ''.$this->mwhere.' and `courseid`>0 and `valid`=1');
			}
			$farr = $this->getflow();
			//第一步自定义审核人
			if($farr['nowcourseid']>0){
				$sysnextoptid 	= $this->rock->post('sysnextoptid'); $sysnextopt 	= $this->rock->post('sysnextopt'); $sysnextcustidid = (int)$this->rock->post('sysnextcustidid');
				if($sysnextcustidid == $farr['nowcourseid'] && !isempt($sysnextoptid) && !isempt($sysnextopt)){
					$this->addcheckname($sysnextcustidid, $sysnextoptid, $sysnextopt, true, 1);
					$farr = $this->getflow(); //重新在读取匹配流程
				}
			}
			$farr['status'] = 0;
			$this->savebill($farr);
			if($isturn == 1){
				$this->nexttodo($farr['nowcheckid'],'submit');
			}
		}
		$this->flowsubmit($na, $sm);
		//抄送保存
		$csname 	= $this->rock->post('syschaosong');
		$csnameid 	= $this->rock->post('syschaosongid');
		if(!isempt($csnameid)){
			$csid 			= (int)$this->chaomodel->getmou('id', $this->mwhere);
			$where 			= ''.$this->mwhere;
			if($csid==0)$where = '';
			$this->chaomodel->record(array(
				'modeid' => $this->modeid,
				'table'  => $this->mtable,
				'mid'  => $this->id,
				'uid'  => $this->uid,
				'csname'  	=> $csname,
				'csnameid'  => $csnameid,
			),$where);
			if($isturn==1)$this->nexttodo($csnameid,'chao', $na);//发送通知
		}
		if($na=='编辑'){
			$this->gettodosend('boedit');
		}else{
			$this->gettodosend('boturn');//提交
		}
	}
	
	/**
	*	获取抄送人姓名
	*/
	public function getcsname($id=0)
	{
		if($id>0){
			$where= "`table`='$this->mtable' and `mid`='$id'";
		}else{
			$where= "`modeid`={$this->modeid} and `uid`={$this->adminid}";
		}
		$ors 	= $this->chaomodel->getone($where,'*','`id` desc');
		$csname = $csnameid = '';
		if($ors){
			$csname = $ors['csname'];
			$csnameid = $ors['csnameid'];
		}
		return array(
			'csname' => $csname,
			'csnameid' => $csnameid,
			'id' => $id,
		);
	}
	
	/**
	* 追加说明
	*/
	public function zhuijiaexplain($sm='')
	{
		$this->addlog(array(
			'explain' 	=> $sm,
			'name'		=> '追加说明',
			'status'	=> 1,
		));
		$zt = $this->rs['status'];
		if($zt==2 && $this->isflow==1 && $this->adminid==$this->uid){
			$marr['status'] 	= 0;
			$this->rs['status'] = 0;
			$this->update($marr, $this->id);
			$farr = $this->getflow();
			$farr['status'] 	= 0;
			$this->savebill($farr);
			$this->nexttodo($farr['nowcheckid'],'zhui', $sm);
		}
		$this->gettodosend('bozhui','', $sm);
	}
	
	/**
	*	催办
	*/
	public function chuiban($sm='')
	{
		$this->addlog(array(
			'explain' 	=> $sm,
			'name'		=> '催办',
			'status'	=> 1,
		));
		$farr = $this->getflow(true);
		$this->nexttodo($farr['nowcheckid'],'cuiban', $sm);
		$this->gettodosend('cuiban','', $sm);
	}
	
	/**
	*	匹配流程读取
	*/
	public function getflowpipei($uid=0)
	{
		$urs 			= $this->urs;
		if(!$urs)$urs 	= $uid;
		if(!is_array($urs))$urs = $this->db->getone('[Q]admin', "`id`='$urs'", '`deptid`,`deptpath`,`id`');
		$barr 	= m('flowcourse')->pipeiCourse($this->modeid);
		$rows 	= $barr['rows'];
		$this->pipeiCoursearrc = $barr['rowd'];
		$this->pipeiCoursearrs = array();
		$this->pipeiCoursearr  = array();
		$kqobj	= m('kaoqin');
		$this->getflowpipeis($rows, $urs, $kqobj);
		unset($this->pipeiCoursearrs);
		unset($this->pipeiCoursearrc);
		return $this->pipeiCoursearr;
	}
	private function getflowpipeis($rows,$urs, $kqobj)
	{
		$shiyong = array();
		$defix 	 = $xuhao 	 = 0; //默认是0的
		$uid 	 = arrvalue($urs,'id',0);
		$zshu	 = count($rows);
		//print_r($rows);
		foreach($rows as $k=>$rs){
			$whereid = (int)$rs['whereid'];
			$receid  = $rs['receid'];
			$wherestr= arrvalue($rs, 'where');
			
			//停用了
			if($rs['status']=='0')continue;
			
			//where条件字段
			if(!isempt($wherestr)){
				$wherestr = m('base')->strreplace($this->rock->jm->base64decode($wherestr), $uid);
				$to 	  = $this->rows("`id`='$this->id' and $wherestr ");
				if($to==0)continue;//条件不成立
			}
			
			//有条件的【流程模块条件】下的ID
			if($whereid > 0){
				$bo = $this->wheremanzhu($whereid);
				if(!$bo)continue;
			}
			
			//特殊判断过滤
			if(!isempt($rs['num'])){
				$bo = $this->flowcoursejudge($rs['num'], $rs);
				if(is_bool($bo) && !$bo)continue;
			}
			
			$xuhao++;
			
			if($defix==0 && (isempt($receid) || contain($receid,'all'))){
				$defix = $xuhao;
			}
			
			//当有设置审核条件满足时就用这个做默认，2018-07-10 新增，如果是多个满足就取最后一个
			if(isempt($receid) && (!isempt($wherestr) || $whereid > 0)){
				$defix = $xuhao;
			}
			
			$rs['xuhao'] = $xuhao;
			$shiyong[] 	 = $rs;
		}
		
		//在根据receid匹配到哪个流程
		if($shiyong){
			$gxuha 		= $kqobj->getpipeimid($urs, $shiyong, 'xuhao', $defix); //这个匹配出来是大于0的
			if($gxuha>0){
				$gxuha	= $gxuha-1;
				$prs 	= $shiyong[$gxuha];
				unset($prs['children']);
				$this->pipeiCoursearrs[]= $prs['id'];
				$this->pipeiCoursearr[] = $prs;
				
				if($prs['childshu']>0){
					$this->getflowpipeis($shiyong[$gxuha]['children'], $urs, $kqobj);
				}else{
					//有nid下级就读取
					$nid = arrvalue($prs,'nid','0');
					if($nid>0 && !in_array($nid, $this->pipeiCoursearrs) && isset($this->pipeiCoursearrc, $nid)){
						$this->getflowpipeis(array($this->pipeiCoursearrc[$nid]), $urs, $kqobj);
					}
				}
			}
		}else{
			//可能是跳过
			if($zshu==1 && $rows[0]['checkshu']>0){
				$this->getflowpipeis($rows[0]['children'], $urs, $kqobj);
			}
		}
	}
	
	/*
	*	获取流程
	*/
	public function getflow($sbo=false)
	{
		$this->flowarr 	= array();
		$allcheckid 	= $nowcheckid 	=  $nowcheckname  = $nstatustext = '';
		$allcheckids	= array();
		$nowcourseid	= 0;
		$nstatus 		= $this->rs['status'];
		$this->nowcourse	= array();
		$this->nextcourse	= array();
		$this->flowisend	= 0;
		
		$curs 	= $this->flogmodel->getrows("$this->mwhere and `courseid`>0",'checkid,checkname,courseid,`valid`,`status`,`statusname`,`name`','id desc');
		$cufss  =  $ztnas  = $chesarr	= array();
		foreach($curs as $k=>$rs){
			$_su  = ''.$rs['courseid'].'';
			$_su1 = ''.$rs['courseid'].'_'.$rs['checkid'].'';
			//$rs['status']为1就一走下一步
			if($rs['valid']==1 && in_array($rs['status'], $this->flowstatusarr)){
				if(!isset($cufss[$_su]))$cufss[$_su]=0;
				$cufss[$_su]++;
				$chesarr[$_su1] = 1; //标识已审核
			}
			if(!in_array($rs['checkid'], $allcheckids))$allcheckids[] = $rs['checkid'];
			if($nstatustext=='' && $rs['courseid']>0){
				$nstatustext = ''.$rs['checkname'].'处理'.$rs['statusname'].'';
				$nstatus	 = $rs['status'];
			}
			$ztnas[$rs['courseid']] = ''.$rs['checkname'].''.$rs['statusname'].'';
		}
		$nowstep = $zongsetp  = -1;
		$isend 	= 0;
		$czt 	= $this->rs['status'];
		$coutye = 0; //判断是否需要前后审批处理
		$rows  	= ($czt==1 || $czt==5)? array() : $this->getflowpipei($this->uid);
		if($rows){
			//读取flow_checks是否比审核状态(退回的)
			$checksa = $this->checksmodel->getrows($this->mwhere.' and `addlx`=3');
			$coursea = $nrows = array();
			foreach($checksa as $k=>$rs)$coursea[$rs['courseid']]='1';
			$nrows 	 = array();
			
			//获取审核人
			$allcheckid= '';
			foreach($rows as $k=>$rs){
				$uarr 	= $this->getcheckname($rs);
				$rows[$k]['checkid'] 	= $uarr[0];
				$rows[$k]['checkname'] 	= $uarr[1];
				$allcheckid .= ','.$uarr[0].'';
			}

			foreach($rows as $k=>$rs){
				$nrows[] = $rs;
				
				//全部直属上级
				if($rs['checktype']=='superall'){
					$suparr 		 = $this->adminmodel->getsuperarr($this->urs['id']);
					if($suparr)foreach($suparr as $k1=>$surs){
						if(!contain(','.$allcheckid.',', ','.$surs['id'].',')){
							$rs['id']		 = $rs['id'] * 99999 + $surs['id'];
							$rs['checkid']   = $surs['id'];
							$rs['checkname'] = $surs['name'];
							$nrows[] = $rs;
							$allcheckid .= ','.$surs['id'].'';
						}
					}
				}
			}
	
			foreach($nrows as $k=>$rs){
				$whereid 	= (int)$rs['whereid'];
				$checkshu 	= $rs['checkshu'];
				
				//不在审核人子表中
				/*
				if(!isset($coursea[$rs['id']])){
					if($whereid > 0){
						$bo = $this->wheremanzhu($whereid);
						if(!$bo)continue;
					}
					
					if(!isempt($rs['num'])){
						$bo = $this->flowcoursejudge($rs['num']);
						if(is_bool($bo) && !$bo)continue;
					}
				}
				*/
				
				$zongsetp++;
				//$uarr 		= $this->getcheckname($rs);
				$checkid	= $rs['checkid'];
				$checkname	= $rs['checkname'];
				$ischeck 	= 0;
				$checkids	= $checknames = '';
				
				$_su  		= ''.$rs['id'].'';
				$nowshu		= 0;
				if(isset($cufss[$_su]))$nowshu = $cufss[$_su];
				
				if(!$this->isempt($checkid)){
					$checkida 	= explode(',', $checkid);
					$checkidna 	= explode(',', $checkname);
					$_chid		= $_chna	= '';
					
					foreach($checkida as $k1=>$chkid){
						$_su1 = ''.$rs['id'].'_'.$chkid.'';
						if(!in_array($chkid, $allcheckids))$allcheckids[] = $chkid;
						if(!isset($chesarr[$_su1])){
							$_chid.=','.$chkid.'';
							$_chna.=','.$checkidna[$k1].'';
						}
					}
					if($_chid!='')$_chid = substr($_chid, 1);
					if($_chna!='')$_chna = substr($_chna, 1);
					
					if($_chid==''){
						$ischeck	= 1;
					}else{
						if($checkshu>0&&$nowshu>=$checkshu)$ischeck	= 1;
					}
					$checkids 	= $_chid;
					$checknames = $_chna;
				}else{
					if($checkshu>0&&$nowshu>=$checkshu)$ischeck	= 1;				
					//需要全部审核时 同时已有审核过了 也没有审核人了
					if($checkshu == 0 && $nowshu>0)$ischeck = 1;
				}
				
				if($ischeck==0 && $coutye==0){
					if((int)arrvalue($rs,'coursetype','0')>0)$coutye = 1;
				}
				
				$rs['ischeck'] 		= $ischeck;
				$rs['islast'] 		= 0;
				$rs['checkid'] 		= $checkid;
				$rs['checkname'] 	= $checkname;
				$rs['nowcheckid'] 	= $checkids;
				$rs['nowcheckname'] = $checknames;
				$rs['isnow'] 		= 0;
				$rs['nowstep']	 	= $zongsetp;
				$rs['step']	 		= $k+1;
				
				if($ischeck==0 && $nowstep==-1){
					$rs['isnow']= 1;
					$nowstep = $zongsetp;
					$this->nowcourse = $rs;	//当前审核步骤信息
					$nowcourseid	 = $rs['id'];
					$nowcheckid		 = $checkids;
					$nowcheckname	 = $checknames;
				}
				
				if($nowstep>-1 && $zongsetp==$nowstep+1)$this->nextcourse = $rs; //下一步信息
				$this->flowarr[]= $rs;
			}
		}
		
		//前置后置审批的处理(比较麻烦，先不做了，怕有问题)
		if($coutye==133){
			$nsarr	= array();
			$qzar	= $hzrr = array();
			foreach($this->flowarr as $k=>$rs){
				if($rs['ischeck'] == 1)continue;
				$coursetype = $rs['coursetype'];
				//前置审批
				if($coursetype=='1'){
					$qzar[$k] = $rs;
				}
				//后置
				if($coursetype=='2'){
					$hzrr[$k] = $rs;
				}
			}
		}
		
		if($zongsetp>-1)$this->flowarr[$zongsetp]['islast']=1;
		if($nowstep == -1){
			$isend = 1;
		}else{
			$nstatustext 	= '待'.$nowcheckname.'处理';
		}
		$this->flowisend 	= $isend;
		$allcheckid			= join(',', $allcheckids);
		$arrbill['allcheckid'] 		= $allcheckid;
		$arrbill['nowcourseid'] 	= $nowcourseid;
		$arrbill['nowcheckid'] 		= $nowcheckid;
		$arrbill['nowcheckname']	= $nowcheckname;
		$arrbill['nstatustext']		= $nstatustext;
		$arrbill['nstatus']			= $nstatus;
		$arrbill['status']			= $this->rs['status'];
		$arrbill['isturn']			= $this->rs['isturn'];
		if($sbo)$this->getflowsave($arrbill);
		return $arrbill;
	}
	public function wheremanzhu($id)
	{
		$uid = $this->uid;
		$ser = $this->wheremodel->getflowwhere($id, $uid);
		if(!$ser)return true;
		$str = $ser['ntr'];
		if(!isempt($str)){
			$to = $this->db->rows('[Q]admin',"`id`='$uid' and ($str)");
			if($to>0)return false;
		}
		$str = $ser['str'];
		if(!isempt($str)){
			$to = $this->rows("`id`='$this->id' and $str");
			if($to==0)return false;
		}
		$str = $ser['utr'];
		if(!isempt($str)){
			$to = $this->db->rows('[Q]admin',"`id`='$uid' and $str");
			if($to==0)return false;
		}
		return true;
	}
	
	public function getflowsave($sarr, $suvu=false)
	{
		if(!$sarr)return;
		if($suvu)$sarr['updt'] = $this->rock->now;
		$this->billmodel->update($sarr, $this->mwhere);
	}
	
	//获取审核人
	private function getcheckname($crs)
	{
		$type	= $crs['checktype'];
		$cuid 	= $name = '';
		$courseid = $crs['id'];
		if(!$this->isempt($crs['num'])){
			$uarr	= $this->flowcheckname($crs['num']);
			if(is_array($uarr)){
				if(!$this->isempt($uarr[0]))return $uarr;
			}
		}
		
		$cheorws= $this->checksmodel->getall($this->mwhere.' and courseid='.$courseid.'','checkid,checkname');
		if($cheorws){
			foreach($cheorws as $k=>$rs){
				$cuid.=','.$rs['checkid'].'';
				$name.=','.$rs['checkname'].'';
			}
			if($cuid != ''){
				$cuid = substr($cuid, 1);
				$name = substr($name, 1);
				return array($cuid, $name);
			}
		}
		
		if($type=='super' || $type=='superall'){
			$cuid = $this->urs['superid'];
			$name = $this->urs['superman'];
		}
		if($type=='dept' || $type=='super' || $type=='superall'){
			if($this->isempt($cuid) && $this->drs){
				$cuid = $this->drs['headid'];
				$name = $this->drs['headman'];
			}
		}
		if($type=='apply'){
			$cuid = $this->urs['id'];
			$name = $this->urs['name'];
		}
		if($type=='opt'){
			$cuid = $this->rs['optid'];
			$name = $this->rs['optname'];
			if(isempt($cuid)){
				$cuid = $this->urs['id'];
				$name = $this->urs['name'];
			}
		}
		if($type=='user'){
			$cuid = $crs['checktypeid'];
			$name = $crs['checktypename'];
		}
		if($type=='rank'){
			$rank = $crs['checktypename'];
			if(!$this->isempt($rank)){
				$rnurs	= $this->db->getrows('[Q]admin',"`status`=1 and `ranking`='$rank'",'id,name','sort');
				foreach($rnurs as $k=>$rns){
					$cuid.=','.$rns['id'].'';
					$name.=','.$rns['name'].'';
				}
				if($cuid != ''){
					$cuid = substr($cuid, 1);
					$name = substr($name, 1);
				}
			}
		}
		if($type=='cname'){
			$cnbar 	= $this->cnamemodel->getcheckname($crs['checktypeid'], $this->uid);
			$cuid	= arrvalue($cnbar, 0);
			$name	= arrvalue($cnbar, 1);
		}
		
		//主表上字段，一般都是ID字段
		if($type=='field'){
			$fids = $crs['checktypeid'];
			if(!isempt($fids)){
				$vals = '';
				$farrs= explode(',', $fids);
				foreach($farrs as $fid){
					$vid = arrvalue($this->rs, $fid);
					if(!isempt($vid))$vals.=','.$vid.'';
				}
				if($vals!=''){
					$vals = substr($vals, 1);
					$uarr = $this->adminmodel->getrows('`status`=1 and `id` in('.$vals.')','id,name');
					foreach($uarr as $k=>$rs){
						$cuid.=','.$rs['id'].'';
						$name.=','.$rs['name'].'';
					}
					if($cuid != ''){
						$cuid = substr($cuid, 1);
						$name = substr($name, 1);
					}
				}
			}
		}
		
		$cuid	= $this->rock->repempt($cuid);
		$name	= $this->rock->repempt($name);
		return array($cuid, $name);
	}
	
	
	/**
	*	创建编号
	*/
	public function createbianhao($num, $fid)
	{
		if(isempt($num))$num=''.$this->modenum.'-';
		@$appdt = $this->rs['applydt'];
		if(isempt($appdt))$appdt = $this->rock->date;
		$apdt 	= str_replace('-','', $appdt);
		$num	= str_replace('Ymd',$apdt,$num);
		return $this->db->sericnum($num,'[Q]'.$this->mtable.'', $fid,3);
	}
	
	
	/**
	*	创建流程单号
	*/
	public function createnum()
	{
		$num = $this->moders['sericnum'];
		if($num=='无'||$this->isempt($num))$num='TM-Ymd-';
		@$appdt = $this->rs['applydt'];
		if(isempt($appdt))$appdt = $this->rock->date;
		$apdt 	= str_replace('-','', $appdt);
		$num	= str_replace('Ymd',$apdt,$num);
		return $this->db->sericnum($num,'[Q]flow_bill', 'sericnum', 3);
	}
	public function savebill($oarr=array())
	{
		$dbs = $this->billmodel;
		$whes= $this->mwhere;
		$biid= (int)$dbs->getmou('id', $whes);
		$arr = array(
			'table' => $this->mtable,
			'mid' 	=> $this->id,
			'optdt' => isset($this->rs['optdt']) ? $this->rs['optdt'] : $this->rock->now,
			'optname' 	=> $this->adminname,
			'optid' 	=> $this->adminid,
			'modeid'  	=> $this->modeid,
			'updt'  	=> $this->rock->now,
			'isturn'  	=> $this->rs['isturn'],
			'nstatus'	=> $this->rs['status'],
			'applydt'	=> $this->rs['applydt'],
			'modename'  => $this->modename,
			'uname'  	=> $this->rs['base_name'],
			'udeptname' => $this->rs['base_deptname'],
			'uid' 		=> $this->uid,
		);
		foreach($oarr as $k=>$v)$arr[$k]=$v;
		if($biid==0){
			$arr['isdel'] 	= '0';
			$arr['status'] 	= $arr['nstatus'];
			$arr['createdt']= $arr['optdt'];
			$arr['sericnum']= $this->createnum();
			$whes			= '';
			$this->sericnum	= $arr['sericnum'];
		}
		$dbs->record($arr, $whes);
		return $arr;
	}
	
	/**
	*	下一步发通知
	*/
	public function nexttodo($nuid, $type, $sm='', $act='')
	{
		$cont	= '';
		$tit  	= '';
		$gname	= '流程待办';
		$summary= $this->getsummary();
		if($type=='submit' || $type=='next' || $type == 'cuiban'){
			$cont = '你有['.$this->uname.']的['.$this->modename.',单号:'.$this->sericnum.']需要处理';
			if($sm!='')$cont.='，说明:'.$sm.'';
			
			//短信提醒
			if($type != 'cuiban'){
				$txnum 	= $this->option->getval('sms_txnum');
				if(!isempt($txnum)){
					$mknum 	= $this->option->getval('sms_mknum');
					if($mknum=='all' || contain(','.$mknum.',',','.$this->modenum.',')){
						$wxurl		= $this->getxiangurl();
						$barr 		= c('xinhuapi')->sendsms($nuid, '', $txnum, array(
							'modename' => $this->modename,
							'sericnum' => $this->sericnum,
							'applyname'=> $this->uname,
							'deptname' => $this->rs['base_deptname'],
						),$wxurl);
					}
				}
			}
			$type = 'daiban';
		}
		
		
		
		//审核不同意
		if($type == 'nothrough'){
			$cont = '你提交['.$this->modename.',单号:'.$this->sericnum.']'.$this->adminname.'处理['.$act.']，原因:['.$sm.']';
			$gname= '流程申请';
		}
		if($type == 'finish'){
			$cont = '你提交的['.$this->modename.',单号:'.$this->sericnum.']已全部处理完成';
		}
		if($type == 'zhui'){
			$cont = ''.$this->adminname.'追加单据说明['.$this->modename.',单号:'.$this->sericnum.']，说明:['.$sm.']';
		}
		//退回
		if($type == 'tui'){
			$cont = '['.$this->adminname.']退回单据['.$this->modename.',单号:'.$this->sericnum.']到你这请及时处理，说明:'.$sm.'';
		}
		//提交抄送
		if($type == 'chao'){
			$cont = ''.$this->adminname.''.$sm.'了“'.$this->modename.'”';
			if(!isempt($summary))$cont.='，摘要“'.$summary.'”';
			if($this->isflow==1)$cont.='，单号“'.$this->sericnum.'”';
			$gname= '';
		}
		//评论
		if($type=='pinglun'){
			$cont  = ''.$this->adminname.''.$act.'你的['.$this->modename.']单据，说明:'.$sm.'';
			$gname = '';
		}
		
		//回执确认
		if($type=='receipt'){
			$cont  = ''.$this->adminname.''.$act.'['.$this->modename.']的单据';
			if($sm!='')$cont.='，说明:'.$sm.'';
			$gname = '回执确认';
			$tit   = '回执确认';
		}
		
		$nbis = $this->flownexttodo($type); //自定义推送内容
		if($nbis){
			if(is_string($nbis))$cont = $nbis;
			if(is_array($nbis)){
				$tit 	= arrvalue($nbis, 'title', $tit);
				$gname 	= arrvalue($nbis, 'gname', $gname);
				$cont 	= arrvalue($nbis, 'cont', $cont);
			}
		}
		if($cont!='')$this->push($nuid, $gname, $cont, $tit);
	}
	
	/**
	*	获取摘要
	*/
	public function getsummary()
	{
		return $this->rock->reparr($this->moders['summary'], $this->rs);
	}
	
	private function addcheckname($courseid, $uid, $uname, $onbo=false, $addlx=0)
	{
		$uida 	= explode(',', ''.$uid.'');
		$uidan 	= explode(',', $uname);
		if($onbo)$this->checksmodel->delete($this->mwhere.' and `courseid`='.$courseid.'');
		foreach($uida as $k=>$uid){
			$uname	= $this->rock->arrvalue($uidan, $k);
			$zyarr 	= array(
				'table' 	=> $this->mtable,
				'mid' 		=> $this->id,
				'modeid' 	=> $this->modeid,
				'courseid' 	=> $courseid,
				'optid' 	=> $this->adminid,
				'optname' 	=> $this->adminname,
				'addlx' 	=> $addlx, //添加类型:1自定义,2撤回添加,3退回添加,4转移添加
				'optdt' 	=> $this->rock->now,
				'status' 	=> 0
			);
			$this->checksmodel->delete($this->mwhere.' and `checkid`='.$uid.' and `courseid`='.$courseid.'');
			$zyarr['checkid'] 	= $uid;
			$zyarr['checkname'] = $uname;
			$this->checksmodel->insert($zyarr);
		}
		if($addlx==4)$this->checksmodel->delete($this->mwhere.' and `checkid`='.$this->adminid.' and `courseid`='.$courseid.'');//删除别人转给我的
	}
	
		
	/**
	*	判断保存的数据是否
	*/
	public function savedatastr($fval, $farr, $data=array())
	{
		$str 		= '';
		if(!$farr)return $str;
		$savewhere 	= $farr['savewhere'];
		$name 		= $farr['name'];
		$types 		= $farr['fieldstype'];
		if(isempt($savewhere) || isempt($fval))return $str;
		$savewhere	= str_replace(array('{0}','{date}','{now}'), array($name, $this->rock->date,$this->rock->now), $savewhere);
		$savewhere	= $this->rock->reparr($savewhere, $data);
		$saees		= explode(',', $savewhere);
		if($types=='date' || $types=='datetime')$fval = strtotime($fval);
		if($types=='number')$fval = floatval($fval);
		foreach($saees as $saeess){
			$fsaed 	= explode('|', $saeess);
			$msg 	= isset($fsaed[2]) ? $fsaed[2] : ''.$name.'数据不符号';
			$val 	= isset($fsaed[1]) ? $fsaed[1] : '';
			$lfs 	= $fsaed[0];
			if($val != ''){
				if($types=='date' || $types=='datetime')$val = strtotime($val);
				if($types=='number')$val = floatval($val);
				if($lfs=='gt'){$bo = $fval>$val;if(!$bo)return $msg;}
				if($lfs=='egt'){$bo = $fval>=$val;if(!$bo)return $msg;}
				if($lfs=='lt'){$bo = $fval<$val;if(!$bo)return $msg;}
				if($lfs=='elt'){$bo = $fval<=$val;if(!$bo)return $msg;}
				if($lfs=='eg'){$bo = $fval==$val;if(!$bo)return $msg;}
				if($lfs=='neg'){$bo = $fval!=$val;if(!$bo)return $msg;}
			}
		}
		return $str;
	}
	
	/**
	*	重写更新一下方法
	*/
	public function update($arr, $where)
	{
		if(is_array($arr)){
			foreach($arr as $k=>$v)$this->rs[$k]=$v;
		}
		return parent::update($arr,$where);
	}
	
	/**
	*	将操作记录标识无效状态
	*/
	public function updatelogvalid($whe)
	{
		$this->flogmodel->update('valid=0', $this->mwhere.' '.$whe);
	}
	
	/**
	*	说明追加
	*/
	public function strappend($sm, $str, $fh=',')
	{
		if(isempt($str))return $sm;
		if(!isempt($sm))$sm.=$fh;
		$sm.=$str;
		return $sm;
	}
	
	/**
	*	异常直接标识已完成
	*/
	public function checkerror($sm='')
	{
		$msg = $this->check(1, $sm, 1);
		return $msg;
	}

	/**
	*	提交审核处理
	*	$zt 状态 $sm 说明 $lx=0默认1异常标识
	*/
	public function check($zt, $sm='', $lx=0)
	{
		if($this->rs['status']==1)return '流程已处理完成,无需操作';
		$arr 	 	= $this->getflow();
		$flowinfor 	= $this->getflowinfor();
		
		if($flowinfor['ischeck']==0 && $lx==0)return '当前是['.$arr['nowcheckname'].']处理';
		
		$nowcourse	= $this->nowcourse;
		
		//异常的审核
		if($lx==1){
			if($arr['nowcourseid']!=0)return '当前有审核步骤ID是存在的不能直接标识已完成';
			$to 	= $this->flogmodel->rows($this->mwhere.' and `courseid`>0 and `status`=1');
			if($to==0)return '该单据没有任何通过审核处理不能直接标识已完成或已审核';
			$nowcourse = array('id'=>0,'name'=>'异常处理','step'=>0);
		}
		
		$nextcourse	= $this->nextcourse;
		$zynameid	= $this->rock->post('zynameid');
		$zyname		= $this->rock->post('zyname');
		$nextname	= $this->rock->post('nextname');
		$nextnameid	= $this->rock->post('nextnameid');
		$qmimgstr	= $this->rock->post('qmimgstr'); //签名图片
		$tuiid		= (int)$this->rock->post('tuiid'); //退回到哪个flowlog.id上
		$iszhuanyi	= $ischangenext = 0;
		if($zt==1 && isempt($zynameid) && $this->rock->arrvalue($nextcourse,'checktype')=='change'){
			if($nextnameid=='')return '请选择下一步处理人';
			$ischangenext = 1;
		}
		if($zt!=2)$tuiid = 0;//只有2的状态才能退回
		
		$istongyi		 = in_array($zt, $this->flowstatusarr); //是否审核本步骤同意
		
		if($zynameid!='' && $istongyi){
			if($zynameid==$this->adminid)return '不能转给自己';
			$sm 	= $this->strappend($sm, '转给：'.$zyname.'');
			$iszhuanyi 		 = 1;
			$this->rs['zb_name'] 	= $zyname;
			$this->rs['zb_nameid'] 	= $zynameid;
		}
		$ufied 	= array();
		if($iszhuanyi == 0 && $zt!=2){
			foreach($flowinfor['checkfields'] as $chef=>$chefv){
				$ufied[$chef] = $this->rock->post('cfields_'.$chef.'');
				if(isempt($ufied[$chef]))$this->echomsg(''.$chefv['name'].'不能为空');
				$_str = $this->savedatastr($ufied[$chef], $chefv['fieldsarr'], $this->rs);
				if($_str!='')$this->echomsg($_str);
			}
		}
		
		$this->checkiszhuanyi = $iszhuanyi;//是否为转移的
		
		$barr 		= $this->flowcheckbefore($zt, $ufied, $sm);
		$msg 		= '';
		if(is_array($barr) && isset($barr['msg']))$msg = $barr['msg'];
		
		//更新字段
		if(is_array($barr) && isset($barr['update'])){
			foreach($barr['update'] as $_k=>$_v)$ufied[$_k] = $_v;
		}
		if(is_string($barr))$msg = $barr;
		if(!isempt($msg))return $msg;
		
		if($ufied){
			$bo = $this->update($ufied, $this->id);
			if(!$bo)return 'dberr:'.$this->db->error();
		}
		
		$courseact 	= $flowinfor['courseact'];
		$act 		= $courseact[$zt];
		$statusname	= $act[0];//状态名称
		$statuscolor= $act[1];//状态颜色
		$nzt		= $act[2];//处理后对应状态
		$courseid	= $nowcourse['id'];
		
		$this->checksmodel->update('`status`='.$zt.'', $this->mwhere.' and `checkid`='.$this->adminid.' and `courseid`='.$courseid.'');
		if($iszhuanyi == 1){
			$this->addcheckname($courseid, $zynameid, $zyname, false, 4);
			$nowcourse['id'] = 0;
		}
		
		//指定下步审核
		if($ischangenext==1){
			
			$this->addcheckname($nextcourse['id'], $nextnameid, $nextname, true, 1);
			
			//$_nesta = explode(',', $nextnameid);
			//$_nestb = explode(',', $nextname);
			//foreach($_nesta as $_i=>$_nes)$this->addcheckname($nextcourse['id'], $_nesta[$_i], $_nestb[$_i]);
		}
		
		//读取退回记录
		$tuirs 	= array();
		if($tuiid > 0)$tuirs = $this->flogmodel->getone($tuiid);
		if(!$tuirs)$tuiid = 0;
		if($tuiid>0){
			$sm = $this->strappend($sm, '退回到['.$tuirs['name'].'('.$tuirs['checkname'].')]');
			$statusname 	= '退回';
			$statuscolor	= '#17B2B7';
		}
		$this->checkistui 	= $tuiid;//是否为退回的
		$this->lastlogid 	= $this->addlog(array(
			'courseid' 	=> $nowcourse['id'],
			'name' 		=> $nowcourse['name'],
			'step' 		=> $nowcourse['step'],
			'status'	=> $zt,
			'statusname'=> $statusname,
			'color'		=> $statuscolor,
			'explain'	=> $sm,
			'qmimg'		=> $qmimgstr
		));
		
		
		
		//退回处理
		if($tuiid > 0){
			$this->addcheckname($tuirs['courseid'], $tuirs['checkid'], $tuirs['checkname'], true, 3);
			$this->updatelogvalid('and `courseid`>0 and `status`=1 and `step`>='.$tuirs['step'].'');
		}
		
		$lzt 		= $this->rock->repempt($nzt, $zt);//最后状态
		
		$uparr		= $nexttodoarr = $nexttodoarc = array();
		$bsarr 	 	= $this->getflow();
		$bsarr['tuiid'] = $tuiid;
		$nextcheckid = $bsarr['nowcheckid']; //下一步审核人
		
		if($istongyi){//同意
			if($iszhuanyi==0){
				$uparr['status']= $this->rock->repempt($nzt,'0');
			}
			$nexttodoarr		= array($nextcheckid, 'next', $sm, $statusname);//通知下一步处理人
		}else{
			if($tuiid>0){
				$lzt = 23; //退回固定状态23
				$nexttodoarr	= array($nextcheckid, 'tui', $sm, $statusname); //通知到退回的人员
			}elseif($zt==2){//2固定不同意
				$nexttodoarr	= array($this->optid, 'nothrough', $sm, $statusname);
			}
			$uparr['status'] 	= $lzt;
		}
		$this->flowcheckafter($zt, $sm, $nowcourse);
		
		$bsarr['nstatus'] = $lzt;
		$bsarr['checksm'] = $sm;
		
		//没有当前步骤就是结束完成了
		if(!$this->nowcourse){
			$uparr['status'] 	= $lzt;
			$nexttodoarc		= array($this->optid, 'finish', $sm, '');
		}
		
		//最后状态更新
		if($uparr){
			$this->update($uparr, $this->id);
		}
		
		
		//审核完成了调用对应函数接口
		if(!$this->nowcourse){
			$this->flowcheckfinsh($zt);
			if($istongyi){
				$this->checksmodel->delete($this->mwhere);//完成了删除设置审核人
			}
		}
		$bsarr['status'] = $this->rs['status'];//状态
		$this->getflowsave($bsarr, true);
		
		//通知给相应人员
		if($this->db->backsql()){
			if($nexttodoarr)$this->nexttodo($nexttodoarr[0],$nexttodoarr[1],$nexttodoarr[2],$nexttodoarr[3]); 
			if($nexttodoarc)$this->nexttodo($nexttodoarc[0],$nexttodoarc[1],$nexttodoarc[2],$nexttodoarc[3]); 
		}
		
		//通知发送的
		if($iszhuanyi == 1){
			$this->gettodosend('bozhuan','', $sm, 0, ''.$this->adminname.'将['.$nowcourse['name'].']转给:'.$zyname.'');
		}else{
			if($istongyi)$this->gettodosend('botong', $statusname, $sm, $nowcourse['id']);
			if($zt==2)$this->gettodosend('bobutong',$statusname, $sm, $nowcourse['id']);
			if(!$this->nowcourse && $istongyi)$this->gettodosend('bofinish', '', $sm);	//全部完成了
		}
		return 'ok';
	}
	
	public function pushs($receid, $cont, $title='', $params=array())
	{
		return $this->push($receid,'', $cont, $title, 0, $params);
	}
	
	/**
	*	详情的URL
	*/
	public function getxiangurl($num='', $id=0, $lx='x')
	{
		if($num=='')$num = $this->modenum;
		if($id==0)$id 	 = $this->id;
		$url 	= ''.URL.'task.php?a='.$lx.'&num='.$num.'&mid='.$id.'';
		return $url;
	}
	
	
	/**
	*	推送提醒
	*/
	public function push($receid, $gname='', $cont, $title='', $wkal=0, $params=array())
	{
		if(isempt($receid) && $wkal==1)$receid='all';
		if(isempt($receid))return false;
		if(contain(','.$receid.',', ',d1,'))$receid='all'; //如果选顶级d1就是全员
		$modenum 	= arrvalue($params, 'modenum', $this->modenum);
		$modename 	= arrvalue($params, 'modename', $this->modename);
		$id 		= (int)arrvalue($params, 'id', $this->id);
		$moders		= arrvalue($params, 'moders');
		if(!is_array($moders))$moders = $this->moders;	
		if($gname=='')$gname = $modename;
		$reim	= m('reim');
		$url 	= $this->getxiangurl($modenum, $id, 'p');
		$wxurl 	= $this->getxiangurl($modenum, $id, 'x');
		$emurl 	= $this->getxiangurl($modenum, $id, 'a');
		if($id==0){
			$url = '';$wxurl = '';$emurl='';
		}
		$url 	= arrvalue($params, 'url', $url);	 //PC上URL
		$wxurl 	= arrvalue($params, 'wxurl', $wxurl); //移动端URL
		$emurl 	= arrvalue($params, 'emurl', $emurl); //邮件上URL
		
		$slx	= 0;
		$pctx	= $moders['pctx'];
		$mctx	= $moders['mctx'];
		$wxtx	= $moders['wxtx'];
		$ddtx	= $moders['ddtx'];
		$emtx	= $moders['emtx'];
		if($pctx==0 && $mctx==1)$slx=2;
		if($pctx==1 && $mctx==0)$slx=1;
		if($pctx==0 && $mctx==0)$slx=3;
		$this->rs['now_adminname'] 	= $this->adminname;
		$this->rs['now_modename'] 	= $modename;
		$cont	= $this->rock->reparr($cont, $this->rs);
		$receid = $this->adminmodel->gjoins($receid);
		$uids 	= m('todo')->addtodo($receid, $modename, $cont, $modenum, $id);
		if($uids!='' && $moders['type'] != '系统' && $id > 0)$this->todosmodel->addtotouids($uids, array(
			'table' 	=> $this->mtable,
			'mid'		=> $id,
			'modename'	=> $modename,
			'modenum'	=> $modenum,
		));
		$reim->pushagent($uids, $gname, $cont, $title, $url, $wxurl, $slx);
		$this->flowchangetodo($uids, $gname);
		
		if(isempt($title))$title = $modename;
		//邮件提醒发送不发送全体人员的，太多了
		if($emtx == 1 && $receid != 'all'){
			$emcont = '您好：<br>'.$cont.'(邮件由系统自动发送)';
			if($emurl!=''){
				$emcont.='<br><a href="'.$emurl.'" target="_blank" style="color:blue"><u>详情&gt;&gt;</u></a>';
			}
			$msg = m('email')->sendmail($title, $emcont, $receid);
			if($msg!='ok')m('log')->addlogs('邮件', $msg , 2);
		}
		
		//微信提醒发送
		if($wxtx==1){
			$wxarra  = $this->flowweixinarr;
			$wxarr	 = array(
				'title' 		=> $title,
				'description' 	=> $cont,
				'url' 			=> $wxurl
			);
			$picurl  = arrvalue($this->rs, 'fengmian');
			if($picurl != ''){
				if(substr($picurl,0,4) != 'http')$picurl = URL.$picurl;
				$wxarr['picurl'] = $picurl;
			}
			foreach($wxarra as $k=>$v)$wxarr[$k]=$v;
			//微信企业号
			if($reim->installwx(0)){
				$barr = m('weixin:index')->sendnews($receid, ''.$gname.',0', $wxarr);
				m('log')->todolog('微信提醒', $barr);
			}
			//企业微信提醒
			if($reim->installwx(1)){
				$gnames = $gname;
				if($gname != $modename)$gnames =''.$modename.','.$gname.'';
				$barr = m('weixinqy:index')->sendxiao($receid, ''.$gnames.',办公助手', $wxarr);
				m('log')->todolog('企业微信提醒', $barr);
			}
		}
		//钉钉提醒发送
		if($ddtx==1 && $reim->installwx(2)){
			$barr = m('dingding:index')->sendoa($receid, $gname, array(
				'title' 		=> $title,
				'content' 		=> $cont,
				'wxurl' 		=> arrvalue($this->flowweixinarr, 'url', $wxurl),
				'modename'		=> $modename
			));
			m('log')->todolog('钉钉提醒', $barr);
		}
		$this->flowweixinarr=array();
		return $receid;
	}
	
	public function getwxurl($num='')
	{
		if($num=='')$num = $this->modenum;
		$str = ''.URL.'?m=ying&d=we&num='.$num.'';
		return $str;
	}
	
	/**
	*	删除单据
	*/
	public function deletebill($sm='', $qxpd=true)
	{
		if(getconfig('systype')=='demo')return '演示请勿删除';
		if($qxpd){
			$is = $this->isdeleteqx();
			if($is==0)return '无权删除';
		}
		
		//删除前判断
		if(method_exists($this,'flowdeletebillbefore')){
			$bstr = $this->flowdeletebillbefore($sm);
			if(!isempt($bstr))return $bstr;
		}
		
		$this->flogmodel->delete($this->mwhere);
		m('reads')->delete($this->mwhere);
		m('file')->delfiles($this->mtable, $this->id);
		$tables 	= $this->moders['tables'];
		if(!isempt($tables)){
			$arrse = explode(',', $tables);
			foreach($arrse as $arrses)m($arrses)->delete('mid='.$this->id.'');
		}
		$this->billmodel->delete($this->mwhere);
		$this->todosmodel->delete($this->mwhere);
		$this->checksmodel->delete($this->mwhere);
		m('remind')->delete($this->mwhere); //单据提醒的
		m('todo')->delete($this->mwhere);
		m('receipt')->delete($this->mwhere); //回执
		$this->delete($this->id);
		$this->flowdeletebill($sm);
		$this->flowzuofeibill($sm);
		$this->gettodosend('bodel','', $sm);
		
		$dels = '删除模块['.$this->modename.']id为'.$this->id.'的单据';
		$ztss = arrvalue($this->rs,'base_summary');
		if(!isempt($ztss))$dels.=',摘要['.$ztss.']';
		if(!isempt($sm))$dels.=',说明:'.$sm.'';
		
		m('log')->addlogs('删除单据', $dels, 3);
		return 'ok';
	}
	
	/**
	*	单据作废处理
	*/
	public function zuofeibill($sm='')
	{
		$this->addlog(array(
			'explain' 	=> $sm,
			'name'		=> '作废',
			'status'	=> 1,
		));
		$this->update('`status`=5', $this->id);
		$zfarr = array(
			'status' 		=> 5,
			'nstatus' 		=> 5,
			'checksm' 		=> '作废：'.$sm.'',
			'nowcheckid' 	=> '',
			'nowcheckname' 	=> '',
			'nstatustext' 	=> '作废',
			'updt' 			=> $this->rock->now,
		);
		$this->billmodel->update($zfarr, $this->mwhere);
		$tables 	= $this->moders['tables'];
		if(!isempt($tables)){
			$arrse = explode(',', $tables);
			foreach($arrse as $arrses)m($arrses)->delete('mid='.$this->id.'');
		}
		$this->flowzuofeibill($sm);
		$this->gettodosend('bozuofei','', $sm);
		return 'ok';
	}
	
	/**
	*	短信催办，需要有短信功能
	*/
	public function smschuiban($sm='')
	{
		//有流程和是我催办
		if($this->isflow==1 && $this->adminid==$this->uid){
			$farr 		= $this->getflow(true);
			$nowcheckid = $farr['nowcheckid']; //当前处理人
			$tplnum		= $this->option->getval('sms_cbnum','defnum');//短信模版
			$wxurl		= $this->getxiangurl();
			$barr 		= c('xinhuapi')->sendsms($nowcheckid, '', $tplnum, array(
				'modename' => $this->modename,
				'sericnum' => $this->sericnum,
				'applyname'=> $this->uname,
				'deptname' => $this->rs['base_deptname'],
			),$wxurl, false, false);//短信催办不需要日志和异步
			if(!$barr['success'])return $barr['msg'];
			$this->addlog(array(
				'explain' 	=> $sm,
				'name'		=> '短信催办',
				'status'	=> 1,
			));
		}
		return 'ok';
	}
	
	/**
	*	加入日程提醒
	*/
	public function addschedule($sm='')
	{
		$txdt = $this->rock->post('txdt');
		if(isempt($sm))return '说明不能为空';
		if(isempt($txdt))return '提醒时间不能为空';
		$barr['title'] 		= $sm;
		$barr['startdt'] 	= $txdt;
		$barr['uid'] 	= $this->adminid;
		$barr['optdt'] 	= $this->rock->now;
		$barr['optname']= $this->adminname;
		$barr['txsj'] 	= 1;
		m('schedule')->insert($barr);
		return 'ok';
	}
	
	/*
	*	获取操作菜单 $flx=0 从操作上点出来 1详情上展示的
	*/
	public function getoptmenu($flx=0)
	{
		$where	= '';
		if($flx==1)$where='and `iszs`=1';
		$rows 	= $this->db->getrows('[Q]flow_menu',"`setid`='$this->modeid' ".$where." and `status`=1",'id,wherestr,name,statuscolor,statusvalue,num,islog,issm,type,upgcont','`sort`');
		$arr 	= array();
		$bfrom	= $this->rock->post('bfrom');
		
		foreach($rows as $k=>$rs){
			$wherestr 	= $rs['wherestr'];
			$bo 		= false;
			if(isempt($wherestr)){
				$bo = true;
			}else{
				$ewet	= $this->wheremodel->getstrwhere($this->rock->jm->base64decode($wherestr));
				$ewet	= str_replace('{asqom}','', $ewet);
				$tos 	= $this->rows("`id`='$this->id' and $ewet");
				if($tos>0)$bo = true;
			}
			$rs['lx']	  = $rs['type'];
			$rs['optnum'] = $rs['num'];
			if(!isempt($rs['num'])){
				$glx = $this->flowgetoptmenu($rs['num']);
				if(is_bool($glx))$bo = $glx;
			}
			$rs['optmenuid'] = $rs['id'];
			if(!isempt($rs['statuscolor']))$rs['color']  = $rs['statuscolor'];
			unset($rs['id']);unset($rs['num']);unset($rs['wherestr']);unset($rs['type']);unset($rs['statuscolor']);
			if($rs['lx']==5){
				$upgcont		= $this->rock->jm->base64decode($rs['upgcont']);
				$rs['upgcont'] 	= $this->rock->reparr($upgcont, $this->rs);
			}else{
				unset($rs['upgcont']);
			}
			if($bo)$arr[] = $rs;
		}
		if($flx==1)return $arr;
		
		
		$status 	= (int)arrvalue($this->rs,'status','0');
		$isturn 	= (int)arrvalue($this->rs, 'isturn','0');
		$ismy 		= ($this->uid == $this->adminid or $this->optid == $this->adminid);
		
		$isreadbo 	= $this->isreadqx(1);
		//菜单上参数说明lx 标识类型,nup是否不显示上传框,optmenuid菜单Id负数，issm是否需要说明
		if($this->isflow==1 && $isturn==1){
			
			//流程参与人都可以追加说明
			if(!in_array($status, array(1,5)) && $isreadbo){
				$arr[] = array('name'=>'追加说明...','lx'=>1,'issm'=>1,'optmenuid'=>-12);
			}
			
			if(!in_array($status, array(1,5)) && $ismy){
				$arr[] = array('name'=>'作废申请...','lx'=>16,'issm'=>1,'nup'=>1,'optmenuid'=>-16); //可直接作废
			}
			
			if(!in_array($status, array(1,2,5)) && $ismy){
				$arr[] = array('name'=>'催办...','lx'=>13,'issm'=>1,'nup'=>1,'optmenuid'=>-13);
				if($this->option->getval('sms_iscb')=='1')$arr[] = array('name'=>'短信催办...','lx'=>17,'issm'=>1,'nup'=>1,'optmenuid'=>-17);
			}
			$chearr = $this->getflowinfor();
			if($chearr['ischeck']==1){
				$arr[] = array('name'=>'<b>去处理单据...</b>','color'=>'#1389D3','lx'=>996);
				if(1==2)foreach($chearr['courseact'] as $zv=>$dz){
					if($zv>0){
						$assar =  array('name'=>$dz[0],'color'=>$dz[1],'optnum'=>'check','issm'=>1,'islog'=>0,'statusvalue'=>$zv,'lx'=>'10','optmenuid'=>-10);
						if($zv==1)$assar['issm'] = 0;
						$arr[] = $assar;
					}
				}
			}
		}
		
		if($status != 5 && arrvalue($this->moders,'ispl','1')=='1' && $isreadbo){
			$arr[] = array('name'=>'评论','lx'=>15,'nup'=>1,'issm'=>1,'optmenuid'=>-15);
		}
		
		//回执确认
		if($ismy && arrvalue($this->moders,'ishz')=='1' && $status==1){
			$smcont	= $this->getsummary();
			$mid	= (int)m('receipt')->getmou('id',"`uid`='$this->adminid' and `modenum`='$this->modenum' and `mid`='$this->id'");
			$namess	= '回执确认设置';
			if($mid>0)$namess	= '回执确认编辑';
			$arr[] = array('name'=>$namess,'djmid'=>$mid,'optnum'=>'receipt','lx'=>'18','optmenuid'=>-18,'modename'=>$this->modename,'smcont'=>$smcont);
		}
		
		//定时提醒设置
		if($isreadbo && arrvalue($this->moders,'istxset','1')=='1' && $this->modenum != 'remind' && !in_array($status, array(2,5))){
			$smcont	= ''.$this->modename.'：'.$this->getsummary();
			$mid	= (int)m('remind')->getmou('id',"`uid`='$this->adminid' and `modenum`='$this->modenum' and `mid`='$this->id'");
			$namess	= '＋添加提醒设置';
			if($mid>0)$namess	= '提醒设置编辑';
			$arr[] 	= array('name'=>$namess,'djmid'=>$mid,'smcont'=>$smcont,'issm'=>1,'optnum'=>'tixing','lx'=>'14','optmenuid'=>-14);
		}
		
		
		
		if($this->iseditqx()==1 && $isreadbo){
			$arr[] = array('name'=>'编辑','optnum'=>'edit','lx'=>'11','optmenuid'=>-11);
		}
		
		if($this->isdeleteqx()==1){
			$arr[] = array('name'=>'删除','color'=>'red','optnum'=>'del','nup'=>1,'issm'=>0,'islog'=>0,'statusvalue'=>9,'lx'=>'9','optmenuid'=>-9);
		}
		
		return $arr;
	}
	
	/**
	*	操作菜单操作
	*/
	public function optmenu($czid, $zt, $sm='')
	{
		$msg 	 = '';
		$cname 	 = $this->rock->post('changename');
		$cnameid = $this->rock->post('changenameid');
		$cdate   = $this->rock->post('changedate');
		if($czid==-9){
			$msg = $this->deletebill($sm);
		}else if($czid==-10){
			$msg 	 = $this->check($zt, $sm);
			if(contain($msg,'成功'))$msg = 'ok';
		}else if($czid==-12){
			$this->zhuijiaexplain($sm);
		}else if($czid==-13){
			$this->chuiban($sm);
		}else if($czid==-14){
			$msg = $this->addschedule($sm);	
		}else if($czid==-15){
			$actname = $this->rock->post('name');
			$this->addlog(array(
				'explain' 	=> $sm,
				'name'		=> $actname,
			));
			//评论时提醒给相关人员
			$uids = arrvalue($this->rs,'uid','0');
			if(isset($this->rs['optid']))$uids.=','.$this->rs['optid'].'';
			$this->nexttodo($uids, 'pinglun', $sm, $actname);
			$this->gettodosend('boping','', $sm);
		}else if($czid==-16){
			$this->zuofeibill($sm); //撤销申请也就是作废了
		}else if($czid==-17){
			$msg = $this->smschuiban($sm); //短信催办
		}else{
			$ors 	 = m('flow_menu')->getone("`id`='$czid' and `setid`='$this->modeid' and `status`=1");
			if(!$ors)return '菜单不存在';
			$name	 = str_replace('.', '', $ors['name']);
			$actname = $ors['actname'];if(isempt($actname))$actname=$name;
			if($ors['islog']==1){
				if(!isempt($cname)){
					if(!isempt($sm))$sm.=',';
					$sm.=''.$name.':'.$cname.'';
				}
				$this->addlog(array(
					'explain' 	=> $sm,
					'name'		=> $actname,
					'statusname'=> $ors['statusname'],
					'status'	=> $ors['statusvalue'],
					'color'		=> $ors['statuscolor']
				));
			}
			$barrs = array(
				'cname' 	=> $cname,
				'sm'    	=> $sm,
				'cnameid' 	=> $cnameid,
				'cdate' 	=> $cdate
			);
			if($ors['type']==4 && !isempt($ors['fields'])){
				$fielsa = explode(',', $ors['fields']);
				$uarrs  = array();
				foreach($fielsa as $fielsas){
					$fsdiwe = 'fields_'.$fielsas.'';
					if(isset($_REQUEST[$fsdiwe])){
						$uarrs[$fielsas]=$this->rock->post($fsdiwe);
						$barrs[$fsdiwe] = $uarrs[$fielsas];
					}
				}
				if($uarrs)$this->update($uarrs, $this->id);
			}
			$upgcont		= $ors['upgcont'];
			//需要判断当前用户有没有编辑权限
			if(!isempt($upgcont)){
				$upgcont	= $this->rock->jm->base64decode($upgcont);
				$upgcont 	= str_replace(array('{now}','{date}','{uid}','{adminid}','{admin}','{sm}','{cname}','{cnameid}'),array($this->rock->now,$this->rock->date, $this->adminid, $this->adminid, $this->adminname, $sm, $cname, $cnameid), $upgcont);
				$this->update($upgcont, $this->id);
			}
			$this->flowoptmenu($ors, $barrs);
		}
		if($msg=='')$msg='ok';
		return $msg;
	}
	
	/**
	*	单据展示条件搜索
	*/
	public function billwhere($uid, $lx)
	{
		$arr['table'] 	= $this->mtable;
		$arr['fields'] 	= '';
		$arr['order'] 	= '';
		$arr['group'] 	= '';
		$arr['keywhere']= '';
		$arr['asqom'] 	= ''; //主表别名
		$this->atype 	= $lx;
		$nas 			= $this->flowbillwhere($uid, $lx);
		$inwhere		= '';
		if(substr($lx,0,5)=='grant'){
			$inwhere	= $this->viewmodel->viewwhere($this->moders, $this->adminid, $this->flowviewufieds, 1);
		}
		//抄送的
		if($lx=='chaos'){
			$inwhere	= "and {asqom}`id` in(select `mid` from `[Q]flow_chao` where `table`='{$this->mtable}' and ".$this->rock->dbinstr('csnameid', $this->adminid).")";
		}
		//经我处理
		if($lx=='mychuli'){
			$inwhere	= "and {asqom}`id` in(select bill.`mid` from `[Q]flow_bill` as `bill` where bill.`modeid`='{$this->modeid}' and ".$this->rock->dbinstr('bill.`allcheckid`', $this->adminid).")";
		}
		$_wehs			= '';
		if(is_array($nas)){
			if(isset($nas['where']))$_wehs = $nas['where'];
			if(isset($nas['asqom']))$arr['asqom']  = $nas['asqom'];
			if(isset($nas['order']))$arr['order']  = $nas['order'];
			if(isset($nas['fields']))$arr['fields']= $nas['fields'];
			if(isset($nas['table']))$arr['table']  = $nas['table'];
			if(isset($nas['group']))$arr['group']  = $nas['group'];
			if(isset($nas['keywhere']))$arr['keywhere']  = $nas['keywhere'];
		}else{
			$_wehs	= $nas;
		}
		$fwhere			= $this->getflowwhere($uid, $lx);//流程模块上条件
		$path 			= ''.P.'/flow/page/rock_page_'.$this->modenum.'.php';
	
		$table 			= $arr['table'];
		$temsao			= 0;
		if(!contain($table, ' ') && $this->isflow==1){
			$arr['table'] = '`[Q]'.$this->mtable.'` a left join `[Q]flow_bill` b on a.`id`=b.`mid` and b.`table`=\''.$this->mtable.'\'';
			$arr['asqom'] = 'a.';
			$arr['fields']= 'a.*,b.`uname` as base_name,b.`udeptname` as base_deptname,b.`sericnum`,b.`nowcheckname`';
			$arr['order'] = 'a.`optdt` desc'; //默认操作时间倒序
			$temsao		  = 1;
			if($this->defaultorder){
				$defa 			= explode(',', $this->defaultorder);
				$desc		 	= arrvalue($defa, 1, 'desc');
				$arr['order'] 	= 'a.`'.$defa[0].'` '.$desc.'';
			}
		}
		
		if(isempt($arr['order'])){
			$arr['order'] = '{asqom}`id` desc';//没有流程默认id倒序
			if($this->defaultorder){
				$defa 			= explode(',', $this->defaultorder);
				$desc		 	= arrvalue($defa, 1, 'desc');
				$arr['order'] 	= '{asqom}`'.$defa[0].'` '.$desc.'';
			}
		}
		
		if(isempt($fwhere) && isempt($inwhere) && $this->moders['isscl']==1){
			$fwhere	 	= 'and 1=2';
		}
		$where 			= $inwhere;
		$wherestr 		= $this->moders['where'];
		if(!isempt($wherestr)){
			$wherestr = $this->rock->covexec($wherestr);
			$where .= ' and '.$wherestr;
		}
		
		if($fwhere!='')$where .= ' '.$fwhere; 
		if($_wehs!='')$where .= ' '.$_wehs;
		$highwhere		= $this->gethighwhere();//高级搜索
		$allfields		= array();
		//关键词搜索
		$key 			= $this->rock->post('key');
		$pnum 			= $this->rock->post('pnum');
		$status 		= $this->rock->post('keystatus');
		$ztfields		= arrvalue($nas, 'ztfields', 'status');
		if(!isempt($status))$where .= ' and {asqom}`'.$ztfields.'`='.$status.'';
		if(!isempt($key) && isempt($arr['keywhere'])){
			$check 	   	= c('check');
			$allfields 	= $this->db->getallfields('[Q]'.$this->mtable.'');
			$_kearr 	= array();
			
			//关键词是日期
			if($check->isdate($key) || $check->ismonth($key)){
				$skeay 	= array('date','datetime','month');
				foreach($this->fieldsarra as $k=>$rs){
					$flx = $rs['fieldstype'];
					$fid = $rs['fields'];
					if($rs['issou']==1 && in_array($flx, $skeay) && in_array($fid, $allfields)){
						if($check->isdate($key)){
							if($flx=='date')$_kearr[] = "{asqom}`".$fid."`='$key'";
							if($flx=='datetime')$_kearr[] = "{asqom}`".$fid."` like '$key%'";
							if($flx=='month')$_kearr[] = "{asqom}`".$fid."`='".substr($key,0,7)."'";
							if($temsao==1){
								$_kearr[] = "b.`applydt`='$key'";
							}
						}
						if($check->ismonth($key)){
							if($flx=='month'){
								$_kearr[] = "{asqom}`".$fid."`='$key'";
							}else{
								$_kearr[] = "{asqom}`".$fid."` like '$key%'";
							}
							if($temsao==1){
								$_kearr[] = "b.`applydt` like '$key%'";
							}
						}
					}
				}
			}
			if(!$_kearr){
				$skeay 	= array('text','textarea','htmlediter','changeuser','changeusercheck','changedept','changedeptusercheck','selectdatafalse','selectdatatrue');
				foreach($this->fieldsarra as $k=>$rs){
					if($rs['issou']==1 && in_array($rs['fieldstype'], $skeay) && in_array($rs['fields'], $allfields) && substr($rs['fields'],-2) != 'dt'){
						$_kearr[] = "{asqom}`".$rs['fields']."` like '%".$key."%'";
					}
				}
				
				/*
				if($check->isnumber($key)){
					$_kearr[] = "{asqom}`id`='$key'";
					if($temsao==1)$_kearr[] = "b.`uid`='$key'";
				}else if($temsao==1){
					
				}*/
				if($temsao==1){
					$_kearr[] = "b.`uname` like '%".$key."%'";
					$_kearr[] = "b.`udeptname` like '%".$key."%'";
					$_kearr[] = "b.`sericnum` = '$key'";
				}
				
				//其他or字段条件格式：name@1,title
				if(isset($nas['orlikefields'])){
					$owhee = explode(',', $nas['orlikefields']);
					foreach($owhee as $owhees){
						$_owhees  = explode('@', $owhees);
						$sle 	  = arrvalue($_owhees, 1);
						if($sle=='1'){
							$_kearr[] = "".$_owhees[0]." ='$key'";
						}else{
							$_kearr[] = "".$_owhees[0]." like '%".$key."%'";
						}
					}
				}
			}
			if($_kearr)$arr['keywhere'] = "and (".join($_kearr, ' or ').")";
		}
		if(!isempt($arr['keywhere']))$where .= ' '.$arr['keywhere'];

		if($highwhere!='')$where .= ' '.$highwhere;
		$where 			= str_replace('{asqom}', $arr['asqom'], $where);
		$arr['order'] 	= str_replace('{asqom}', $arr['asqom'], $arr['order']);
		$where 			= str_replace('[A]', $arr['asqom'], $where);
		
		//字段显示
		$fields 		= $arr['fields'];
		if($this->flowfieldstype==1 && (isempt($fields) || $fields=='*') && $this->moders['isscl']==1){
			if(!$allfields)$allfields = $this->db->getallfields('[Q]'.$this->mtable.'');
			$fields	= '{asqom}`id`';
			$odlvs	= $this->option->getval('columns_'.$this->modenum.'_'.$pnum.'');
			if(isempt($odlvs)){
				foreach($this->fieldsarra as $k=>$rs){
					if($rs['islb']==1 && in_array($rs['fields'],$allfields)){
						$fields.=',{asqom}`'.$rs['fields'].'`';
					}
				}
			}else{
				$odlvsa = explode(',', $odlvs);
				foreach($odlvsa as $odlvs1){
					if(in_array($odlvs1, $allfields)){
						$fields.=',{asqom}`'.$odlvs1.'`';
					}
				}
			}
			if($this->isflow==1){
				if(!contain($fields,'`status`'))$fields.=',{asqom}`status`';
			}
			$fields = str_replace('{asqom}', $arr['asqom'], $fields);
			$arr['fields'] = $fields;
		}
		
		//字段添加上``
		$fields 		= $arr['fields'];
		if(!isempt($fields) && $fields!='*'){
			$fieldsa 	= explode(',', $fields);
			$fieldss	= '';
			foreach($fieldsa as $fieldsas){
				if(contain($fieldsas,'`') || contain($fieldsas,'.') || contain($fieldsas,' ') || contain($fieldsas,'(')){
					$fieldss.=','.$fieldsas.'';
				}else{
					$fieldss.=',`'.$fieldsas.'`';
				}
			}
			$arr['fields'] = substr($fieldss, 1);
		}
		$arr['where'] 	= str_replace('{asqom}','', $where);//替换一下
		return $arr;
	}
	
	/**
	*	获取高级搜索条件
	*/
	public function gethighwhere()
	{
		$s = '';
		foreach($this->fieldsarra as $k=>$rs){
			if($rs['issou']!=1)continue;
			$type 	= $rs['fieldstype'];
			$fields = $rs['fields'];
			$val  	= $this->rock->post('soufields_'.$fields.'');
			if($type=='date' || $type=='datetime' || $type=='number' || substr($fields,-2)=='dt'){
				$val1  	= $this->rock->post('soufields_'.$fields.'_start');
				$val2  	= $this->rock->post('soufields_'.$fields.'_end');
				$val3  	= $this->rock->post('soufields_'.$fields.'_equal');
				if($type!='number'){
					if(!isempt($val1))$val1 = "'$val1'";
					if(!isempt($val3))$val3 = "'$val3'";
					if(!isempt($val2)){
						if($type=='datetime')$val2 = "$val2 23:59:59";
						$val2 = "'$val2'";
					}
				}
				if(!isempt($val1))$s.=" and {asqom}`$fields`>=".$val1."";
				if(!isempt($val2))$s.=" and {asqom}`$fields`<=".$val2."";
				if(!isempt($val3))$s.=" and {asqom}`$fields`=".$val3."";
			}else{
				if(isempt($val))continue;
				if($type=='select' || $type=='radio' || $type=='rockcombo'){
					$s.=" and {asqom}`$fields`='$val'";
				}else if($type=='month'){
					$s.=" and {asqom}`$fields` like '$val%'";
				}else{
					$s.=" and {asqom}`$fields` like '%$val%'";
				}
			}
		}
		$ofied = $this->rock->post('search_fields');
		$ofval = $this->rock->post('search_value');
		if(!isempt($ofied) && !isempt($ofval)){
			$ofval = $this->adminmodel->gjoin($ofval,'', 'all');
			$s = " and {asqom}`$ofied` in($ofval)";
		}
		return $s;
	}
	
	/**
	*	根据编号获取对应条件flow_where
	*/
	public function getflowwhere($uid, $num)
	{
		$where = '';
		$rs 	= $this->wheremodel->getone("`setid`='$this->modeid' and `num`='$num'");
		if(!$rs)return $where;
		$where = $this->wheremodel->getwherestr($rs, $uid, $this->flowviewufieds);
		return $where;
	}
	
	/**
	*	根据流程模块条件读取记录和统计
	*/
	public function getflowrows($uid, $lx, $limit=5)
	{
		$nas 	= $this->billwhere($uid, $lx);
		$table 	= $nas['table'];
		if(!contain($table,' '))$table='[Q]'.$table.'';
		if(isempt($nas['fields']))$nas['fields'] = '*';
		if($limit==0)return $this->db->rows($table, '1=1 '.$nas['where'].''); 
		$rows 	= $this->db->getrows($table, '1=1 '.$nas['where'].'', $nas['fields'], $nas['order'], $limit);
		foreach($rows as $k=>$rs){
			$rows[$k] = $this->flowrsreplace($rs, 2);
		}
		return $rows;
	}
	
	/**
	*	读取要提醒的单据
	*/
	public function gettodorows($whereid)
	{
		$where = $this->wheremodel->getwherestr($whereid, $this->adminid, $this->flowviewufieds);
		$wherestr 		= $this->moders['where'];
		if(!isempt($wherestr)){
			$wherestr = $this->rock->covexec($wherestr);
			$where .= ' and '.$wherestr;
		}
		$rows 	= $this->getall('1=1 '.$where.'');
		foreach($rows as $k=>$rs){
			$rows[$k] = $this->flowrsreplace($rs, 1);
		}
		return $rows;
	}
	
	
	
	/**
	*	打印导出
	*/
	public function printexecl($event)
	{
		$arr['moders'] = $this->moders;
		$arr['fields'] = $this->getfields();
		$cell = 1;
		foreach($arr['fields'] as $k=>$v)$cell++;
		$arr['cell']	= $cell;
		
		$where 			= '1=1';
		$str1		 	= $this->moders['where'];
		if(!isempt($str1)){
			$str1 = $this->rock->covexec($str1);
			$where = $str1;
		}
		
		$vwhere 		= $this->viewmodel->viewwhere($this->moders, $this->adminid);
		$rows 			= $this->getrows(''.$where.' '.$vwhere.'', '*', 'id desc', 100);
		$arr['rows']	= $this->flowprintrows($rows);
		$arr['count']	= $this->db->count;
		return $arr;
	}
	
	/**
	*	获取所有多行子表数据
	*	$lx=0编辑时读取，1展示时读取
	*/
	public function getsuballdata($lx=0)
	{
		$tabless	= $this->moders['tables'];
		$subdata	= array();
		if(!isempt($tabless)){
			$tablessa = explode(',', $tabless);
			$namessa  = explode(',', $this->moders['names']);
			$tabless1 = '['.str_replace(',','],[', $tabless).']';
			foreach($tablessa as $zbx=>$tables){
				$cis 	= substr_count($tabless1, '['.$tables.']');
				$whes	= '';
				if($cis>1)$whes=' and `sslx`='.$zbx.'';
				$data 	= m($tables)->getall('mid='.$this->id.''.$whes.'','*','`sort`');
				$data 	= $this->flowsubdata($data, $lx);
				if($lx == 0){
					$subdata['subdata'.$zbx.''] 	 = $data;
				}else{
					$subdata[$zbx] = array(
						'data' 	=> $data,
						'fields'=> 'subdata'.$zbx.'',
						'name'	=> $this->rock->arrvalue($namessa, $zbx)
					);
				}
			}
		}
		return $subdata;
	}
	
	
	/**
	*	读取通知
	*	$act 当前动作有(boturn,boedit,bochang,bodel)
	*/
	private $gettodolistarr = null;
	public function gettodolist($act)
	{
		if($this->gettodolistarr === null){
			$rows = $this->todomodel->getrows("`setid`='".$this->modeid."' and `status`=1");
			$barr = array();
			foreach($rows as $k=>$rs){
				$whereid = (int)$rs['whereid'];
				if($whereid > 0){
					$bo = $this->wheremanzhu($whereid);
					if(!$bo)continue;
				}
				$barr[] = $rs;
			}
			$this->gettodolistarr = $barr;
		}else{
			$barr = $this->gettodolistarr;
		}
		$garr = array();
		if($barr)foreach($barr as $k=>$rs){
			if($this->rock->arrvalue($rs,$act)=='1')$garr[] = $rs;
		}
		return $garr;
	}
	
	/**
	*	发送设置的通知
	*/
	public function gettodosend($act, $actname='',$sm='', $courseid=0, $conts='')
	{
		$barr = $this->gettodolist($act);
		if(!$barr)return;
		$changearr	= array('boturn'=>'提交','boedit'=>'编辑','bozhuan'=>'转办','bochang'=>'修改字段','bodel'=>'删除','bozuofei'=>'作废','botong'=>'处理同意','bobutong'=>'处理不同意','bofinish'=>'全部处理完成','bozhui'=>'追加说明','boping'=>'评论','bohuiz'=>'回执确认');
		if($actname=='')$actname = $this->rock->arrvalue($changearr, $act);
		if(isempt($actname))return; //没有动作名就不
		$cheo	= c('check');
		foreach($barr as $k=>$rs){
			$receid = $rs['receid'];
			
			if($act=='botong' || $act=='bobutong'){
				$changewe = $rs['changecourse'];
				if(!isempt($changewe) && !contain(','.$changewe.',',','.$courseid.','))continue;
			}
			
			if($rs['toturn']==1)$receid.=','.$this->uid.''; //通知给提交人
			if($rs['tocourse']==1 && $this->billrs){
				$allcheckid = $this->billrs['allcheckid'];
				if(!isempt($allcheckid))$receid.=','.$allcheckid.''; //通知流程所有参与人
			}
			//其他字段
			$todofields = $rs['todofields'];
			if(!isempt($todofields)){
				$toad = explode(',', $todofields);
				foreach($toad as $toads){
					$ttv = $this->rock->arrvalue($this->rs, $toads);
					if(!isempt($ttv) && $cheo->isinnumber($ttv))$receid.=','.$ttv.'';
				}
			}
			if(isempt($receid))continue;
			if(substr($receid,0,1)==',')$receid = substr($receid, 1);
			$cont = $rs['summary'];
			if(isempt($cont))$cont = $conts;
			if(isempt($cont)){
				$cont = ''.$this->adminname.''.$actname.'['.$this->modename.',单号:'.$this->sericnum.']';
				if($sm!='')$cont.=',说明:'.$sm.'';
			}
			$this->push($receid, '', $cont, '');
		}
	}
	
	
	/**
	*	统计报表
	*/
	public function flowtotal($fields='', $type='')
	{
		if($fields=='')$fields = $this->rock->post('total_fields');
		if($type=='')$type 	   = $this->rock->post('total_type','jls');
		$typea	= explode('|', $type);
		$typefields = '';
		$type 	= $typea[0]; $typefields = arrvalue($typea, 1);
		$rowa 	= array();
		$rowa[] = array(
			'name' 	=> '暂无数据',
			'value' => 0,
			'bili'	=> ''
		);
		$tofiels= 'count(1)';
		if($type=='sum')$tofiels = 'sum([A]`'.$typefields.'`)';
		if($type=='avg')$tofiels = 'avg([A]`'.$typefields.'`)';
		$atype	= $this->rock->post('atype');
		$table 	= '[Q]'.$this->mtable.'';
		
		$narr 	= $this->billwhere($this->adminid, $atype);
		$where 	= $narr['where'];
		$table	= $narr['table'];
		if(!contain($table,' '))$table = '[Q]'.$table.'';
		
		$sql 	= 'select '.$fields.' as `name`,'.$tofiels.' as value from '.$table.' where 1=1 '.$where.' group by '.$fields.'';
		$sql 	= str_replace('[A]', $narr['asqom'], $sql);
		
		$rows 	= $this->db->getall($sql);
		$total	= 0;
		if($rows){
			foreach($rows as $k=>$rs)$total+=floatval($rs['value']);
			if($total>0)foreach($rows as $k=>$rs){
				$rows[$k]['bili'] = $this->rock->number($rs['value']*100/$total).'%';
			}
			if($type!='avg' && count($rows)>1)$rows[] = array('name' 	=> '合计','value' => $total,'bili'	=> '');
		}else{
			$rows = $rowa;
		}
		return $rows;
	}
	
	
	
	/**
	*	回执确认
	*/
	public function receiptcheck($hid, $sm)
	{
		$hrs  	= m('receipt')->getone("`id`='$hid' and `modenum`='$this->modenum' and `mid`='$this->id'");
		if(!$hrs)return;
		$receid 	= $hrs['receid'];
		$receids	= $hrs['receids'];
		if(isempt($receids))$receids = '';
		$uid 	= $this->adminid;
		if(!contain(','.$receid.',', ','.$uid .','))return;
		
		$reaa 	= explode(',', $receids);
		if(!$reaa)$reaa = array();
		if(!in_array($reaa, $uid)){
			if($receids!='')$receids.=',';
			$receids.=''.$uid.'';
		}
		
		$reaa 	= explode(',', $receids);
		$ushuy  = count($reaa);
		$receids= join(',', $reaa);
		
		m('receipt')->update(array(
			'receids' 	=> $receids,
			'ushuy' 	=> $ushuy,
		), $hid);
		
		$this->addlog(array(
			'explain' => $sm,
			'name'	=> '回执确认'
		));
		
		//通知给提交人
		$this->rs = $hrs;
		$this->nexttodo($hrs['uid'],'receipt', $sm, '回执确认');
		return 'ok';
	}
}