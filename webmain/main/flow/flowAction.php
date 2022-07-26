<?php
class flowClassAction extends Action
{
	public function loaddataAjax()
	{
		$id		= (int)$this->get('id');
		$data	= m('flow_set')->getone($id);
		$arr 	= array(
			'data'		=> $data
		);
		echo json_encode($arr);
	}
	
	public function modeafter($table, $rows)
	{
		return array(
			'qian' => PREFIX
		);
	}
	
	public function modebefore($table)
	{
		$where 	= '';
		$key 	= $this->post('key');
		if(!isempt($key)){
			$where = "and (`type`='$key' or `name` like '%$key%' or `table` like '$key%' or `num` like '$key%' or `sericnum` like '$key%')";
		}
		return $where;
	}
	
	private function getwherelist($setid)
	{
		return m('flow_where')->getall('setid='.$setid.'','id,name','sort');
	}
	
	public function loaddatacourseAjax()
	{
		$id		= (int)$this->get('id');
		$setid	= (int)$this->get('setid');
		$data	= m('flow_course')->getone($id);
		$arr 	= array(
			'data'		=> $data,
			'wherelist' => $this->getwherelist($setid),
			'statusstr'	=> m('flow_set')->getmou('statusstr', $setid)
		);
		echo json_encode($arr);
	}
	
	public function loaddatawhereAjax()
	{
		$id		= (int)$this->get('id');
		$data	= m('flow_where')->getone($id);
		$arr 	= array(
			'data'		=> $data,
		);
		echo json_encode($arr);
	}
	
	public function flowsetsavebefore($table, $cans)
	{
		$tab = $cans['table'];
		$tabs= trim($cans['tables']);
		$name= $this->rock->xssrepstr($cans['name']);
		$num = strtolower($cans['num']);
		$cobj= c('check');
		if(!$cobj->iszgen($tab))return '表名格式不对';
		if($cobj->isnumber($num))return '编号不能为数字';
		if(strlen($num)<4)return '编号至少要4位';
		if($cobj->isincn($num))return '编号不能包含中文';
		
		if($cans['isflow']>0 && isempt($cans['sericnum'])) return '有流程必须有写编号规则，请参考其他模块填写';
		$rows['num']= $this->rock->xssrepstr($num); 
		$rows['name']= $name;
		if(!isempt($tabs)){
			if($cobj->isincn($tabs))return '多行子表名不能包含中文';
			$tabsa 		= explode(',', $tabs);
			foreach($tabsa as $tabsas){
				if(isempt($tabsas))return '多行子表名('.$tabs.')不规范';
			}
		}
		$rows['tables']= $tabs;
		return array(
			'rows' => $rows
		);
	}
	
	private function setsubtsta($tabs, $alltabls, $tab, $slxbo, $ssm)
	{
		if(isempt($tabs))return;
		if(!in_array(''.PREFIX.''.$tabs.'', $alltabls)){
			$sql = "CREATE TABLE `[Q]".$tabs."` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`mid` int(11) DEFAULT '0' COMMENT '对应主表".$tab.".id',
`sort` int(11) DEFAULT '0' COMMENT '排序号',
`comid` smallint(6) DEFAULT '0' COMMENT '对应单位id',
PRIMARY KEY (`id`),KEY `mid` (`mid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
			$bo = $this->db->query($sql);
		}else{
			$fields = $this->db->getallfields(''.PREFIX.''.$tabs.'');
			$str 	= '';
			if(!in_array('mid', $fields))$str.=",add `mid` int(11) DEFAULT '0' COMMENT '对应主表".$tab.".id'";
			if(!in_array('sort', $fields))$str.=",add `sort` int(11) DEFAULT '0' COMMENT '排序号'";
			if(!in_array('comid', $fields))$str.=",add `comid` smallint(6) DEFAULT '0' COMMENT '对应单位id'";
			if($slxbo && !in_array('sslx', $fields)){
				$ssma = explode(',', $ssm);
				$ss1  = '';
				foreach($ssma as $k=>$ssmas)$ss1.=','.$k.''.$ssmas.'';
				if($ss1!='')$ss1 = substr($ss1, 1);
				$str.=",add `sslx` tinyint(1) DEFAULT '0' COMMENT '".$ss1."'";
			}
			if($str!=''){
				$sql = 'alter table `'.PREFIX.''.$tabs.'` '.substr($str,1).'';
				$this->db->query($sql);
			}
		}
	}
	
	public function flowsetsaveafter($table, $cans)
	{
		$isflow = $cans['isflow'];
		$tab  	= $cans['table'];
		$tabs  	= $cans['tables'];
		$alltabls = array();
		//创建保存多行子表
		if(!isempt($tabs)){
			$alltabls 	= $this->db->getalltable();
			$tabsa 		= explode(',', $tabs);
			$addsts 	= array();
			foreach($tabsa as $tabsas){
				$this->setsubtsta($tabsas, $alltabls, $tab, in_array($tabsas, $addsts), $cans['names']);
				$alltabls[] = ''.PREFIX.''.$tabsas.'';
				$addsts[]	= $tabsas;
			}
		}
		
		if(isempt($tab))return;
		if(!$alltabls)$alltabls 	= $this->db->getalltable();
		if($isflow==0){
			if(!in_array(''.PREFIX.''.$tab.'', $alltabls)){
				$sql = "CREATE TABLE `[Q]".$tab."` (`id` int(11) NOT NULL AUTO_INCREMENT,`comid` smallint(6) DEFAULT '0' COMMENT '对应单位id',PRIMARY KEY (`id`))ENGINE=MyISAM  AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
				$bo = $this->db->query($sql);
			}else{
				$fields = $this->db->getallfields(''.PREFIX.''.$tab.'');
				$str 	= '';
				if(!in_array('comid', $fields) && !in_array('companyid', $fields))$str.=",add `comid` smallint(6) DEFAULT '0' COMMENT '对应单位id'";
				if($str!=''){
					$sql = 'alter table `'.PREFIX.''.$tab.'` '.substr($str,1).'';
					$this->db->query($sql);
				}
			}
			return;
		}
		
		if(!in_array(''.PREFIX.''.$tab.'', $alltabls)){
			$sql = "CREATE TABLE `[Q]".$tab."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `optdt` datetime DEFAULT NULL COMMENT '操作时间',
  `optid`  int(11) DEFAULT '0',
  `optname` varchar(20) DEFAULT NULL COMMENT '操作人',
  `applydt` date DEFAULT NULL COMMENT '申请日期',
  `explain` varchar(500) DEFAULT NULL COMMENT '说明',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `isturn` tinyint(1) DEFAULT '1' COMMENT '是否提交',
  `comid` smallint(6) DEFAULT '0' COMMENT '对应单位id',
  PRIMARY KEY (`id`)
) ENGINE=".getconfig('db_engine','MyISAM')." AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
			$bo = $this->db->query($sql);
		}else{
			$fields = $this->db->getallfields(''.PREFIX.''.$tab.'');
			$str 	= '';
			if(!in_array('uid', $fields))$str.=",add `uid` int(11) DEFAULT '0'";
			if(!in_array('optdt', $fields))$str.=",add `optdt` datetime DEFAULT NULL COMMENT '操作时间'";
			if(!in_array('optid', $fields))$str.=",add `optid` int(11) DEFAULT '0'";
			if(!in_array('optname', $fields))$str.=",add `optname` varchar(20) DEFAULT NULL COMMENT '操作人'";
			if(!in_array('applydt', $fields))$str.=",add `applydt` date DEFAULT NULL COMMENT '申请日期'";
			if(!in_array('explain', $fields))$str.=",add `explain` varchar(500) DEFAULT NULL COMMENT '说明'";
			if(!in_array('status', $fields))$str.=",add `status` tinyint(1) DEFAULT '1' COMMENT '状态'";
			if(!in_array('isturn', $fields))$str.=",add `isturn` tinyint(1) DEFAULT '1' COMMENT '是否提交'";
			if(!in_array('comid', $fields) && !in_array('companyid', $fields))$str.=",add `comid` smallint(6) DEFAULT '0' COMMENT '对应单位id'";
			if($str!=''){
				$sql = 'alter table `'.PREFIX.''.$tab.'` '.substr($str,1).'';
				$this->db->query($sql);
			}
		}
	}
	
	public function elementafter($table, $rows)
	{
		$moders = m('flow_set')->getone($this->mid);
		$farrs 	= array();
		if($this->mid>0){
			$tass 	= $moders['table'];
			$tasss 	= $moders['tables'];
			$farr	= $this->db->gettablefields('[Q]'.$tass.'');
			$farrs[]= array('id'=>'','name'=>'————↓以下主表('.$tass.')的字段————');
			foreach($farr as $k=>$rs){
				$farrs[]= array('id'=>$rs['name'],'name'=>'['.$rs['name'].']'.$rs['explain'].'');
			}
			if(!isempt($tasss)){
				$tasssa = explode(',', $tasss);
				foreach($tasssa as $k=>$tasss){
					$farr	= $this->db->gettablefields('[Q]'.$tasss.'');
					$farrs[]= array('id'=>'','name'=>'————↓以下第'.($k+1).'个多行子表('.$tasss.')的字段————');
					foreach($farr as $k=>$rs){
						$farrs[]= array('id'=>$rs['name'],'name'=>'['.$rs['name'].']'.$rs['explain'].'');
					}
				}
			}
		}
		return array(
			'flowarr'=>$this->getmodearr(),
			'moders'=>$moders,
			'fieldsarr' => $farrs,
			'fieldstypearr'=> $this->option->getdata('flowinputtype')
		);
	}
	
	public function elementbefore($table)
	{
		$mid = (int)$this->post('mid');
		$this->mid = $mid;
		return array(
			'where' => 'and `mid`='.$mid.'',
			'order'	=> 'iszb,sort,id'
		);
	}
	
	
	//单据操作菜单
	public function flowmenubefore($table)
	{
		$mid = (int)$this->post('mid');
		$this->mid = $mid;
		return 'and `setid`='.$mid.'';
	}
	
	public function flowmenuafter($table, $rows)
	{
		
		return array(
			'flowarr'=>$this->getmodearr()
		);
	}
	
	//条件where
	public function flowwhereafter($table, $rows)
	{
		return array(
			'flowarr'=> $this->getmodearr()
		);
	}
	public function flowwherebefore($table)
	{
		return array(
			'table' => '`[Q]'.$table.'` a left join `[Q]flow_set` b on a.setid=b.id',
			'fields'=> 'a.*,b.num as modenum,b.name as modename'
		);
	}
	
	//单据通知设置
	public function flowtodobefore($table)
	{
		$mid = (int)$this->post('mid');
		$this->mid = $mid;
		$where = '';
		if($mid>0)$where = 'and `setid`='.$mid.'';
		return array(
			'where' => $where,
			'table' => '`[Q]'.$table.'` a left join `[Q]flow_set` b on a.setid=b.id',
			'fields'=> 'a.*,b.name as modename'
		);
	}
	
	public function flowtodoafter($table, $rows)
	{
		$fielslist = m('flow_element')->getrows("mid='$this->mid' and iszb=0 and islu=1",'fields,name','sort');
		foreach($fielslist as &$v){
			$v['name'] = ''.$v['fields'].'.'.$v['name'].'';
		}
		
		$courselist	= m('flow_course')->getrows("setid='$this->mid' and `status`=1",'id,name','pid,sort');
		foreach($courselist as &$v1){
			$v1['name'] = ''.$v1['id'].'.'.$v1['name'].'';
		}
		$dbss = m('remind');
		foreach($rows as $k=>$rs){
			$whereid = '';
			if($rs['whereid']>'0')$whereid = $this->db->getmou('[Q]flow_where','name', $rs['whereid']);
			
			$rows[$k]['whereidstr'] = $whereid;
			
			if($rs['botask']=='1'){
				$rows[$k]['remindrs'] = $dbss->getremindrs('flow_todo', $rs['id']);
			}
		}
		
		return array(
			'flowarr'	=> $this->getmodearr(),
			'wherelist' => $this->getwherelist($this->mid),
			'fielslist' => $fielslist,
			'courselist' => $courselist,
			'rows'		=> $rows
		);
	}
	
	//多模版设置
	public function flowmodetpl_after($table, $rows)
	{
		
		return array(
			'flowarr'	=> $this->getmodearr(),
			'rows'		=> $rows
		);
	}
	
	
	private function getmodearr()
	{
		return m('mode')->getmodearr();
	}
	
	
	
	public function inputzsAction()
	{
		$setid	= $this->get('setid');
		$atype	= (int)$this->get('atype','0');
		$rs 	= m('flow_set')->getone("`id`='$setid'");
		if(!$rs)exit('sorry!');
		$this->smartydata['rs'] = $rs;
		$atypea = array('PC端','手机端','PC端打印');
		$this->title  = $rs['name'].'_'.$atypea[$atype].'展示页面设置';
		$fleftarr 	= m('flow_element')->getrows("`mid`='$setid' and `iszb`=0",'`fields`,`name`','`iszb`,`sort`');
		$modenum	= $rs['num'];
		$fleft[]= array('base_name', '申请人',0);
		$fleft[]= array('base_deptname', '申请部门',0);
		$fleft[]= array('base_sericnum', '单号',0);
		$fleft[] = array('file_content', '相关文件',0);
		$iszb 	= 0;
		foreach($fleftarr as $k=>$brs){
			$fleft[]= array($brs['fields'], $brs['name'], $iszb);
		}
		if(!isempt($rs['tables'])){
			$tablea = explode(',', $rs['tables']);
			$namesa = explode(',', $rs['names']);
			$fleft[]= array('', '<font color=#ff6600>↓多行子表</font>', 0);
			foreach($tablea as $k=>$rs1){
				$fleft[]= array('subdata'.$k.'', $namesa[$k], 0);
			}
		}
		if($rs['isflow']>0){
			$fleft[]= array('', '<font color=#ff6600>↓流程审核步骤</font>', 0);
			$rows 	= m('flow_course')->getrows('setid='.$setid.' and `status`=1','id,name','pid,sort');
			foreach($rows as $k=>$rs){
				$fleft[]= array('course'.$rs['id'].'_all', ''.$rs['name'].'处理意见', 0);
				$fleft[]= array('course'.$rs['id'].'_name', ''.$rs['name'].'处理人', 0);
				$fleft[]= array('course'.$rs['id'].'_zt', ''.$rs['name'].'处理状态', 0);
				$fleft[]= array('course'.$rs['id'].'_dt', ''.$rs['name'].'处理时间', 0);
				$fleft[]= array('course'.$rs['id'].'_sm', ''.$rs['name'].'处理说明', 0);
			}
		}

		
		$this->smartydata['fleft'] = $fleft;
		$this->smartydata['atype'] = $atype;

		$path 		= ''.P.'/flow/page/view_'.$modenum.'_'.$atype.'.html';
		$content 	= '';
		if(file_exists($path)){
			$content = file_get_contents($path);
		}
		$this->smartydata['content'] = $content;
	}
	
	
	
	
	public function inputAction()
	{
		$setid	= $this->get('setid');
		$atype	= $this->get('atype');
		$rs 	= m('flow_set')->getone("`id`='$setid'");
		if(!$rs)exit('sorry!');
		$rs['zibiaoshu'] = count(explode(',', $rs['tables']));
		$this->smartydata['rs'] = $rs;
		$this->title  = $rs['name'].'_录入页面设置';
		$fleftarr 	= m('flow_element')->getrows("`mid`='$setid'",'*','`iszb`,`sort`');
		$modenum	= $rs['num'];
		$fleft[]= array('base_name', '申请人',0);
		$fleft[]= array('base_deptname', '申请部门',0);
		$fleft[]= array('base_sericnum', '单号',0);
		$fleft[] = array('file_content', '相关文件',0);
		$iszb 	= 0;
		foreach($fleftarr as $k=>$brs){
			$bt='';
			if($brs['isbt']==1)$bt='*';
			$iszbs = $brs['iszb'];
			if($iszbs>0&&$iszb != $iszbs){
				$fleft[]= array('', '<font color=#ff6600>—第'.$iszbs.'个多行子表—</font>', $iszbs);
				$fleft[]= array('xuhao', '序号', $iszbs);
			}
			$iszb	= $iszbs;
			$fleft[]= array($brs['fields'], $bt.$brs['name'], $iszb);
		}

		
		$this->smartydata['fleft'] = $fleft;
		
		$path 		= ''.P.'/flow/page/input_'.$modenum.'.html';
		$content 	= '';
		if(file_exists($path)){
			$content = file_get_contents($path);
		}
		$this->smartydata['content'] = $content;
		$apaths		= ''.P.'/flow/input/inputjs/mode_'.$modenum.'.js';
		if(!file_exists($apaths)){
			$stra='//流程模块【'.$modenum.'.'.$rs['name'].'】下录入页面自定义js页面,初始函数
function initbodys(){
	
}';
			$this->rock->createtxt($apaths, $stra);
		}
		
		$apaths		= ''.P.'/flow/input/mode_'.$modenum.'Action.php';
		$apath		= ''.ROOT_PATH.'/'.$apaths.'';
		if(!file_exists($apath)){
			$stra = '<?php
/**
*	此文件是流程模块【'.$modenum.'.'.$rs['name'].'】对应控制器接口文件。
*/ 
class mode_'.$modenum.'ClassAction extends inputAction{
	
	/**
	*	重写函数：保存前处理，主要用于判断是否可以保存
	*	$table String 对应表名
	*	$arr Array 表单参数
	*	$id Int 对应表上记录Id 0添加时，大于0修改时
	*	$addbo Boolean 是否添加时
	*	return array(\'msg\'=>\'错误提示内容\',\'rows\'=> array()) 可返回空字符串，或者数组 rows 是可同时保存到数据库上数组
	*/
	protected function savebefore($table, $arr, $id, $addbo){
		
	}
	
	/**
	*	重写函数：保存后处理，主要保存其他表数据
	*	$table String 对应表名
	*	$arr Array 表单参数
	*	$id Int 对应表上记录Id
	*	$addbo Boolean 是否添加时
	*/	
	protected function saveafter($table, $arr, $id, $addbo){
		
	}
}	
			';
			$this->rock->createtxt($apaths, $stra);
			
		}
		if(!file_exists($apath))echo '<div style="background:red;color:white;padding:10px">无法创建文件：'.$apaths.'，会导致录入数据无法保存，请手动创建！代码内容如下：</div><div style="background:#caeccb">&lt;?php<br>class mode_'.$modenum.'ClassAction extends inputAction<br>{<br>}</div>';
	}
	
	public function pagesaveAjax()
	{
		$content = $this->post('content');
		$num 	 = $this->post('num');
		$path 	 = ''.P.'/flow/page/input_'.$num.'.html';
		$bo 	 = $this->rock->createtxt($path, $content);
		if(!$bo){
			echo '无法写入文件:'.$path.'';
		}else{
			echo 'success';
		}
	}
	
	public function viewsaveAjax()
	{
		$content = $this->post('content');
		$num 	 = $this->post('num');
		$atype 	 = (int)$this->post('atype','0');
		$path 	 = ''.P.'/flow/page/view_'.$num.'_'.$atype.'.html';
		if(isempt($content)){
			@unlink($path);
			return 'success';
		}
		$bo 	 = $this->rock->createtxt($path, $content);
		if(!$bo){
			echo '无法写入文件:'.$path.'';
		}else{
			echo 'success';
		}
	}
	
	public function getinputAjax()
	{
		$num 	 = $this->post('num');
		$path 	 = ''.P.'/flow/page/input_'.$num.'.html';
		$cont 	 = '';
		if(file_exists($path)){
			$cont = file_get_contents($path);
			$cont = str_replace('*','', $cont);
		}
		echo $cont;
	}
	
	
	
	public function getsubtableAjax()
	{
		$iszb 	= (int)$this->post('iszb');
		$hang 	= (int)$this->post('hang');
		$modeid = (int)$this->post('modeid');
		$str 	= m('input')->getsubtable($modeid, $iszb, $hang);
		if($str=='')$this->backmsg('没有设置第'.$iszb.'个多行子表');
		$this->backmsg('','ok', $str);
	}
	
	
	
	
	
	
	
	public function getmodearrAjax()
	{
		$arr = $this->getmodearr();
		$this->backmsg('','ok', $arr);
	}
	
	
	
	
	public function viewshowbefore($table)
	{
		$this->modeid = (int)$this->post('modeid');
		if($this->modeid==0){
			return 'and 1=2';
		}
		$this->moders = m('flow_set')->getone($this->modeid);
		$this->isflow = $this->moders['isflow'];
		$table = $this->moders['table'];
		$where = $this->moders['where'];
		if(!isempt($where)){
			$where = $this->rock->covexec($where);
			$where = "and $where";
		}
		return array(
			'table' => '[Q]'.$table,
			'where' => $where
		);
	}
	
	public function viewshowafter($table, $rows)
	{
		$arr 	= array();
		if($rows){
			$flow 	= m('flow')->initflow($this->moders['num']);
			$mbil 	= m('flowbill');
			foreach($rows as $k=>$rs){
				$zt 	= '';
				if(isset($rs['status']))$zt = $rs['status'];
				$narr['id'] 		= $rs['id'];
				$narr['ishui'] 		= ($zt=='5')?1:0;
				$narr['optname'] 	= arrvalue($rs,'optname');
				$narr['modenum'] 	= $this->moders['num'];
				$narr['modename'] 	= $this->moders['name'];
				$narr['table'] 		= $this->moders['table'];
				$narr['optdt'] 		= arrvalue($rs,'optdt');
				$nors 	= $flow->flowrsreplace($rs, 2);
				$narr['summary'] 	= $this->rock->reparr($this->moders['summary'], $nors);
				$otehsr = '';
				if($flow->isflow>0){
					$billrs = $flow->billmodel->getone("`table`='$flow->mtable' and `mid`='".$rs['id']."'");
					$otehsr = arrvalue($billrs, 'nowcheckname');
				}
				$narr['status']		= $flow->getstatus($rs,'',$otehsr,1);
				$narr['chushu']		= $flow->flogmodel->rows("`table`='".$flow->mtable."' and `mid`='".$rs['id']."'");
				
				$arr[] = $narr;
			}
		}
		return array('rows'=>$arr);
	}
	
	public function viewlogshowbefore($table)
	{
		$where = "and `table`='".$this->post('num')."' and `mid`='".$this->post('mid')."'";
		return array(
			'where' => $where
		);
	}
	
	//删除单据，用户=1不需要权限判断
	public function delmodeshujuAjax()
	{
		$this->modeid 	= (int)$this->post('modeid');
		$mid 			= (int)$this->post('mid');
		$modenum 		= $this->post('modenum');
		if($this->modeid>0){
			$this->moders 	= m('flow_set')->getone($this->modeid);
			if(!$this->moders)backmsg('sorry!');
			$modenum = $this->moders['num'];
		}
		if(isempt($modenum))backmsg('操作有误');
		
		$msg	= m('flow')->deletebill($modenum, $mid, '', $this->adminid!=1);
		if($msg=='ok')$msg='';
		backmsg($msg);
	}
	
	//元素保存之前判断
	public function elemensavefieldsbefore($table, $cans, $id)
	{
		$iszb 	= (int)$cans['iszb'];
		$fields = $cans['fields'];
		if(c('check')->isincn($fields))return '对应字段不能有汉字';
		$mid 	= $cans['mid'];
		$this->mmoders 	= m('flow_set')->getone($mid);
		$tablessa = explode(',', $this->mmoders['tables']);
		if($iszb>0){
			$tabsss = $this->rock->arrvalue($tablessa, $iszb-1);
			if(isempt($tabsss))return '模块没有设置第'.$iszb.'个多行子表';
		}
		if(m($table)->rows("`mid`='$mid' and `iszb`='$iszb' and `fields`='$fields' and `id`<>'$id'")>0){
			return '字段['.$fields.']已存在了';
		}
	}
	
	//保存字段判断，自动创建字段
	public function elemensavefields($table, $cans)
	{
		$fields = $cans['fields'];
		$name 	= $cans['name'];
		$mid 	= $cans['mid'];
		$type 	= $cans['fieldstype'];
		$lens 	= $cans['lens'];
		$dev 	= $cans['dev'];
		$data 	= $cans['data'];
		$iszb 	= (int)$cans['iszb'];
		
		$mrs 	= $this->mmoders;
		$tables 	= $mrs['table'];
		if($iszb>0){
			$tables = '';
			$tablessa = explode(',', $mrs['tables']);
			if(isset($tablessa[$iszb-1]))$tables = $tablessa[$iszb-1];
		}
		$fiesss = substr($fields,0,5);
		if($fiesss == 'base_' || $fiesss == 'temp_')return;
		if(!isempt($tables) && $cans['islu']==1){
			$_fieldsa = $this->db->gettablefields('[Q]'.$tables.'');$allfields = array();
			foreach($_fieldsa as $k2=>$rs2)$allfields[$rs2['name']] =  $rs2;
			$this->createfields($allfields, $tables, $fields, $type, $lens, $dev, $name);
			if(substr($type,0,6)=='change' && !isempt($data)){
				if($type=='changeuser' || $type=='changedept'){
					$type='number';
				}
				$this->createfields($allfields, $tables, $data, $type, $lens, '', $name.'的ID');
			}
		}
	}
	
	//创建字段
	private function createfields($allfields, $tables, $fields, $type, $lens, $dev, $name)
	{
		if(isempt($lens))$lens='0';
		$lens = (int)$lens;
		if(!isset($allfields[$fields])){
			$str = "ALTER TABLE `[Q]".$tables."` ADD `$fields` ";
			if($type=='date' || $type=='datetime' || $type=='time'){
				$str .= ' '.$type.'';
			}else if($type=='number'){
				if($lens>6){
					$str .= ' int('.$lens.')';
				}else{
					$str .= ' smallint(6)';
				}
			}else if($type=='checkbox'){
				$str .= ' tinyint(1)';	
			}else if($type=='textarea'){
				$str .= ' varchar(2000)';
			}else{
				if($lens=='0')$lens='50';
				$str .= ' varchar('.$lens.')';
			}
			if(!isempt($dev) && !contain($dev,'{'))$str.= " DEFAULT '$dev'";
			$str.= " COMMENT '$name'";
			$this->db->query($str);
		}else{
			$farr = $allfields[$fields];
			$ustr = '';
			$len  = (int)$farr['lens'];
			if($farr['type']=='varchar'){
				if($lens>$len)$ustr='varchar('.$lens.')';
			}
			if($farr['type']=='smallint' || $farr['type']=='int'){
				if($lens>6 && $lens>$len)$ustr='int('.$lens.')';
			}
			if($ustr!=''){
				if(!isempt($dev) && !contain($dev,'{'))$ustr.= " DEFAULT '$dev'";
				$ustr= "ALTER TABLE `[Q]".$tables."` MODIFY column `$fields` ".$ustr." COMMENT '$name'";
				$this->db->query($ustr);
			}
		}
	}
	
	
	
	
	
	
	public function reloadpipeiAjax()
	{
		$mid 	= (int)$this->post('mid');
		$whe	= '';
		if($mid>0)$whe=' and id='.$mid.'';
		echo m('flow')->repipei($whe);
	}
	
	public function setwherelistafter($table, $rows)
	{
		$dbs = m('flow_where');
		foreach($rows as $k=>$rs){
			$shu = $dbs->rows("`setid`='".$rs['id']."'");
			if($shu>0)$rows[$k]['shu'] = $shu;
		}
		return array('rows'=>$rows);
	}
	
	public function setcourselistafter($table, $rows)
	{
		$dbs = m('flow_course');
		foreach($rows as $k=>$rs){
			$shu = $dbs->rows("`setid`='".$rs['id']."'");
			if($shu>0)$rows[$k]['shu'] = $shu;
		}
		return array('rows'=>$rows);
	}
	
	public function setcourselistbefore($table)
	{
		$where 	= '';
		$key 	= $this->post('key');
		if(!isempt($key)){
			$where = "and (`type`='$key' or `name` like '%$key%' or `table` like '$key%' or `num` like '$key%' or `sericnum` like '$key%')";
		}
		return $where;
	}
	
	
	//删除模块
	public function delmodeAjax()
	{
		$id = (int)$this->post('id','0');
		return $this->delmode($id, true);
	}
	
	private function delmode($id, $dm=false)
	{
		if($this->getsession('isadmin')!='1')return '非管理员不能操作';
		$mrs = m('flow_set')->getone($id);
		if(!$mrs)return '模块不存在';
		$num 	= $mrs['num'];
		if($num!='demo' && $mrs['type']=='系统')return '系统类型模块不能删除清空';
		$flow	= m('flow')->initflow($num);
		$table 	= $mrs['table'];
		$where 	= $mrs['where'];
		if(!isempt($where)){
			$where = $this->rock->covexec($where);
			$where = "and $where";
		}else{
			$where = '';
		}
		
		$rows  = m($table)->getrows('1=1 '.$where.'');
		foreach($rows as $k=>$rs){
			$ssid 	= $rs['id'];
			$flow->loaddata($ssid, false);
			$flow->deletebill('清空模块数据', false);
		}
		
		$name 	= $mrs['name'];
		if($dm){
			m('flow_set')->delete("`id`='$id'");
			m('flow_course')->delete("`setid`='$id'");
			m('flow_element')->delete("`mid`='$id'");
			m('flow_extent')->delete("`modeid`='$id'");
			m('flow_where')->delete("`setid`='$id'");
			m('flow_menu')->delete("`setid`='$id'");
			m('flow_todo')->delete("`setid`='$id'");
			m('flow_todos')->delete("`modenum`='$num'");
			
			m('log')->addlog('模块','删除模块['.$name.']');
		}else{
			m('log')->addlog('模块','清空模块['.$name.']的数据');
		}
		
		$this->db->query("alter table `[Q]$table` AUTO_INCREMENT=1");
		return 'ok';
	}
	
	//清空模块上数据
	public function clearallmodeAjax()
	{
		$id = (int)$this->post('id','0');
		return $this->delmode($id, false);
	}
	
	//刷新序号
	public function rexuhaoAjax()
	{
		$mid 	= (int)$this->get('modeid');
		$db 	= m('flow_element');
		
		$rows 	= $db->getall('mid='.$mid.' and iszb=0','id','sort asc,id asc');
		foreach($rows as $k=>$rs)$db->update('sort='.$k.'',$rs['id']);
		
		$rows 	= $db->getall('mid='.$mid.' and iszb=1','id','sort asc,id asc');
		foreach($rows as $k=>$rs)$db->update('sort='.$k.'',$rs['id']);
		
		$rows 	= $db->getall('mid='.$mid.' and iszb=2','id','sort asc,id asc');
		foreach($rows as $k=>$rs)$db->update('sort='.$k.'',$rs['id']);
		
		$rows 	= $db->getall('mid='.$mid.' and iszb=3','id','sort asc,id asc');
		foreach($rows as $k=>$rs)$db->update('sort='.$k.'',$rs['id']);
	}
	
	public function flowcourselistbefore($rows)
	{
		return array('order'=>'pid,sort');
	}
	
	//流程步骤显示
	public function flowcourselistafter($table, $rows)
	{
		$arr = array();$pid = -1;$maxpid = -1;
		foreach($rows as $k=>$rs){
			if($rs['pid'] != $pid){
				$recename 	= $this->rock->arrvalue($rs, 'recename');
				if(isempt($recename))$recename = '全体人员';
				$arr[] 		= array(
					'name' 	=> '流程'.($rs['pid']+1).'，适用：'.$recename.'',
					'level'	=> 1,
					'stotal'=> 1,
					'status'=> 1,
					'iszf'	=> 0,
					'id'		=> $rs['id'],
					'pid'		=> $rs['pid'],
					'sort'		=> 0,
					'recename'	=> '',
				);
			}
			$rs['level'] 	= 2;
			$rs['stotal'] 	= 0;
			$arr[] 	= $rs;
			$pid 	= $rs['pid'];
			$maxpid = $pid;
		}
		return array(
			'rows' => $arr,
			'maxpid' => $maxpid+1,
		);
	}
	
	
	
	

	
	
	//生成列表页面
	public function changeliebAjax()
	{
		$modeid = (int)$this->post('modeid');
		$path 	= m('mode')->createlistpage($modeid);
		if($path=='')$path	= '无法生成，可能没权限写入'.P.'/flow/page目录';
		echo $path;
	}
	
	//生成所有
	public function allcreateAjax()
	{
		$dbs  = m('mode');
		$rows = $dbs->getall("`status`=1");
		$oi   = 0;
		$msg  = '';
		foreach($rows as $k=>$rs){
			$path 	= $dbs->createlistpage($rs,1);
			if($path=='none')continue;
			if($path==''){
				if($path=='')$msg	= '无法生成，可能没权限写入'.P.'/flow/page目录';
				break;
			}else{
				$oi++;
			}
		}
		if($msg=='')$msg='已生成'.$oi.'个模块，可到'.P.'/flow/page下查看';
		echo $msg;
	}
	
	public function savecolunmsAjax()
	{
		$num 	= $this->post('num');
		$modeid = (int)$this->post('modeid');
		$str 	= $this->post('str');
		$this->option->setval($num.'@'.(-1*$modeid-1000), $str,'模块列定义');
		$path 	= m('mode')->createlistpage($modeid);
		$msg 	= 'ok';
		if($path=='')$msg='已保存,但无法从新生成列表页,自定义列将不能生效';
		echo $msg;
	}
	
	
	//选择人员组
	public function getcnameAjax()
	{
		$arr = array();
		$rows = m('flow_cname')->getall("`pid`=0 and `num` is not null",'num,name','`sort`');
		foreach($rows as $k=>$rs)$arr[] = array('name'=>$rs['name'],'value'=>$rs['num']);
		return $arr;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//图形的流程管理
	public function courseflowinitAjax()
	{
		$setid = (int)$this->get('setid','0');
		
		return m('flowcourse')->getCoursedata($setid);
	}
	
	public function courseflowdelAjax()
	{
		$id = (int)$this->get('id','0');
		
		m('flowcourse')->delete($id);
	}
	public function coursesavebefore($table, $arr)
	{
		$mid 	= (int)arrvalue($arr,'mid','0');
		$setid 	= (int)arrvalue($arr,'setid','0');
		$nid 	= (int)arrvalue($arr,'nid','0');
		if($mid>0 && m($table)->rows("`setid`='$setid' and `id`='$mid'")==0)return '上级步骤ID['.$mid.']不存在';
		if($nid>0 && m($table)->rows("`setid`='$setid' and `id`='$nid'")==0)return '下级步骤ID['.$nid.']不存在';
		
		
	}
	
	public function getfieldsAjax()
	{
		$setid 	= (int)$this->get('setid','0');
		$rows  	= m('flow_element')->getrows('`mid`='.$setid.' and `iszb`=0','name,fields,data,fieldstype','`sort`');
		$arr 	= array();
		foreach($rows as $k=>$rs){
			//$arr[] = array(
			//	'name' => $rs['name'].'('.$rs['fields'].')',
			//	'value' => $rs['fields'],
			//);
			$fieldstype	= $rs['fieldstype'];
			if(in_array($fieldstype, array('changeuser','changeusercheck')) && !isempt($rs['data'])){
				$arr[] = array(
					'name' => $rs['name'].'('.$rs['data'].')',
					'value' => $rs['data'],
				);
			}
		}
		return $arr;
	}
	
	public function savebeforecname($table, $arr, $id)
	{
		$num = $arr['num'];
		$to  = m('flowcname')->rows("`id`<>'$id' and `num`='$num'");
		if($to>0)return '编号['.$num.']已存在';
	}
	
	/**
	*	复制模块
	*/
	public function copymodeAjax()
	{
		$id 	= (int)$this->post('id','0');
		$bhnu 	= strtolower(trim($this->post('name')));
		if(isempt($bhnu))return '新模块编号不能为空';
		if(is_numeric($bhnu))return '模块编号不能用数字';
		if(strlen($bhnu)<4)return '编号至少要4位';
		if(c('check')->isincn($bhnu))return '编号不能包含中文';
		
		$dbs 	= m('mode');
		if($dbs->rows("`num`='$bhnu'")>0)return '模块编号['.$bhnu.']已存在';
		$mrs 	= $dbs->getone($id);
		if(!$mrs)return '模块不存在';
		$ars 	= $mrs;
		$name	= $mrs['name'].'复制';
		$biaom	= $bhnu;
		$obha 	= $mrs['num'];
		unset($ars['id']);
		$ars['name'] = $name;
		$ars['num']  = $bhnu;
		$ars['table']= $biaom;
		$tablea[]	 = $mrs['table'];
		$tables		 = '';
		if(!isempt($ars['tables'])){
			$staba = explode(',', $ars['tables']);
			foreach($staba as $kz=>$zb1){
				$tables.=','.$biaom.'zb'.($kz+1).'';
				if(!in_array($zb1, $tablea))$tablea[]=$zb1;
			}
			$tables = substr($tables, 1);
		}
		$ars['tables'] = $tables;
		$modeid  = $dbs->insert($ars);
		
		//复制表
		foreach($tablea as $kz=>$tabs){
			$sqla 	   = $this->db->getall('show create table `[Q]'.$tabs.'`');
			$createsql = $sqla[0]['Create Table'];
			$biaom1	   = ''.PREFIX.''.$biaom.'';
			if($kz>0)$biaom1	   = ''.PREFIX.''.$biaom.'zb'.$kz.'';
			$createsql = str_replace('`'.PREFIX.''.$tabs.'`','`'.$biaom1.'`',$createsql);
			$this->db->query($createsql);
			$this->db->query('alter table `'.$biaom1.'` AUTO_INCREMENT=1');
		}
		//复制表单元素
		$db1  = m('flow_element');
		$rows = $db1->getall('mid='.$id.'');
		foreach($rows as $k1=>$rs1){
			$rs2 = $rs1;
			unset($rs2['id']);
			$rs2['mid'] = $modeid;
			$db1->insert($rs2);
		}
		//复制相关布局文件
		$hurs[] = ''.P.'/model/flow/{bh}Model.php'; //模块接口文件
		$hurs[] = ''.P.'/flow/input/mode_{bh}Action.php'; //模块控制器
		$hurs[] = ''.P.'/flow/input/inputjs/mode_{bh}.js'; //模块录入js文件
		$hurs[] = ''.P.'/flow/page/input_{bh}.html'; //PC录入模版
		$hurs[] = ''.P.'/flow/page/view_{bh}_0.html'; //PC展示模版
		$hurs[] = ''.P.'/flow/page/view_{bh}_1.html'; //手机展示模版
		$hurs[] = ''.P.'/flow/page/view_{bh}_2.html'; //打印布局
		$hurs[] = ''.P.'/flow/page/viewpage_{bh}.html'; //子模版展示
		$hurs[] = ''.P.'/flow/page/viewpage_{bh}_0.html';//子模版PC展示
		$hurs[] = ''.P.'/flow/page/viewpage_{bh}_1.html';//子模版手机展示
		
		foreach($hurs as $k=>$file){
			$from = str_replace('{bh}',$obha,$file);
			$to   = str_replace('{bh}',$bhnu,$file);
			if(file_exists($from)){
				if($k<=1){
					$fstr = file_get_contents($from);
					if($k==0)$fstr = str_replace('flow_'.$obha.'ClassModel','flow_'.$bhnu.'ClassModel',$fstr);
					if($k==1)$fstr = str_replace('mode_'.$obha.'ClassAction','mode_'.$bhnu.'ClassAction',$fstr);
					$this->rock->createtxt($to, $fstr);
				}else{
					@copy($from, $to);
				}
			}
		}
		
		echo 'ok';
	}
}