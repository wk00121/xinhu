<?php
class flowbillClassModel extends Model
{
	public $statustext;
	public $statuscolor;
	
	public function initModel()
	{
		$this->settable('flow_bill');
		$this->statustext	= explode(',','待处理,已审核,处理不通过,,,已作废');
		$this->statuscolor	= explode(',','blue,green,red,,,gray');
	}
	
	/**
	*	获取状态
	*/
	public function getstatus($zt, $lx=0)
	{
		$a1	= $this->statustext;
		$a2	= $this->statuscolor;
		$str 		= '<font color='.$a2[$zt].'>'.$a1[$zt].'</font>';
		if($lx==0){
			return $str;
		}else{
			return array($a1[$zt], $a2[$zt]);
		}
	}
	
	/**
	*	读取单据数据
	*	$glx 0是应用上读取，1后台读取
	*/
	public function getrecord($uid, $lx, $page, $limit, $glx=0)
	{
		$srows	= array();
		$where	= '(`uid`='.$uid.' or `optid`='.$uid.')';
		$isdb	= 0;
		//未通过
		if($lx=='flow_wtg'){
			$where .= ' and `status`=2';
		}
		
		if($lx=='flow_dcl'){
			$where .= ' and `status` not in(1,5)';
		}
		
		//已完成
		if($lx=='flow_ywc'){
			$where .= ' and `status`=1';
		}
		
		//异常
		if($lx=='flow_error'){
			$where .= ' and '.$this->errorwhere().'';
		}
		
		//待办
		if($lx=='daiban_daib' || $lx=='daiban_def'){
			$where	= '`status` not in(1,2) and '.$this->rock->dbinstr('nowcheckid', $uid);
			$isdb	= 1;
		}
		
		//经我处理
		if($lx=='daiban_jwcl'){
			$where	= $this->rock->dbinstr('allcheckid', $uid);
		}
		
		//我全部下级申请
		if($lx=='daiban_myxia'){
			$where 	= m('admin')->getdownwheres('uid', $uid, 0);
		}
		
		//我直属下级申请
		if($lx=='daiban_mydown'){
			$where 	= m('admin')->getdownwheres('uid', $uid, 1);
		}
		
		//抄送
		if($lx=='flow_chaos'){
			$where ='1=2';
			$crows = $this->db->getall("select * from `[Q]flow_chao` where ".$this->rock->dbinstr('csnameid', $uid)."");
			if($crows){
				$modeids = '';
				$mids 	 = '';
				foreach($crows as $k1=>$rs1){
					$modeids.=','.$rs1['modeid'].'';
					$mids.=','.$rs1['mid'].'';
				}
				$where = "`modeid` in(".substr($modeids,1).") and `mid` in(".substr($mids,1).")";
			}
		}
		
		$key 	= $this->rock->post('key');
		if(!isempt($key))$where.=" and (`optname` like '%$key%' or `modename` like '%$key%' or `sericnum` like '$key%')";
		
		$arr 	= $this->getlimit('`isdel`=0 and '.$where, $page,'*','`optdt` desc', $limit);
		$rows 	= $arr['rows'];
		$modeids= '0';
		foreach($rows as $k=>$rs)$modeids.=','.$rs['modeid'].'';
		$modearr= array();
		if($modeids!='0'){
			$moders = m('flow_set')->getall("`id` in($modeids)");
			foreach($moders as $k=>$rs)$modearr[$rs['id']] = $rs;
		}
		foreach($rows as $k=>$rs){
			$modename	= $rs['modename'];
			$summary	= '';
			$summarx 	= '';
			$modenum 	= '';
			$statustext	= '记录不存在';
			$statusstr	= '不存在';
			$statuscolor= '#888888';
			$ishui		= 0;
			$optdt 		= $rs['optdt'];
			if(isset($modearr[$rs['modeid']])){
				$mors 		= $modearr[$rs['modeid']];
				$modenum 	= $mors['num'];
				$flow 		= m('flow:'.$modenum.'')->initdata($mors);
				$modename 	= $mors['name'];
				$rers 		= $this->db->getone('[Q]'.$rs['table'].'', $rs['mid']);
				if($rers){
					$tihsrs  = $flow->flowrsreplace($rers, 2);
					$summary = $this->rock->reparr($mors['summary'], $tihsrs);
					$summarx = $this->rock->reparr($mors['summarx'], $tihsrs);
	
					$ztarr 	 = $flow->getstatus($rers, $mors['statusstr'], $rs['nowcheckname']);
					$statustext  = $ztarr[0];
					$statuscolor = $ztarr[1];
					$statusstr	 = $ztarr[3];
					if($rers['status']==5)$ishui = 1;
				}else{
					$this->update('isdel=1', $rs['id']);
				}
			}
			
			$title 		= '['.$rs['optname'].']'.$modename.'';
			$cont 		= '申请人：'.$rs['optname'].'<br>单号：'.$rs['sericnum'].'';
			$cont.='<br>申请日期：'.$rs['applydt'].'';
			if(!isempt($summary))$cont.='<br>摘要：'.$summary.'';
			//if(!isempt($rs['nstatustext']))$cont.='<br>状态：'.$rs['nstatustext'].'';
			if(!isempt($rs['checksm']))$cont.='<br>处理说明：'.$rs['checksm'].'';
			
			//应用摘要
			if(!isempt($summarx)){
				$suarr  = $this->zhaiyaoar($summarx);
				foreach($suarr as $f=>$nr){
					$str  		= $this->rock->reparr($nr, $rers);
					if($f=='cont')$str = $this->contreplaces($str);
					$$f 	= $str;
				}
				if(isset($suarr['cont'])){
					if(isset($suarr['title']))
						$cont 	= '模块：'.$modename.'<br>申请人：'.$rs['optname'].'<br>'.$cont;
					//if(!isempt($rs['nstatustext']))$cont.='<br>状态：'.$rs['nstatustext'].'';
					if(!isempt($rs['checksm']))$cont.='<br>处理说明：'.$rs['checksm'].'';
				}
			}
			
			$srows[]= array(
				'title' => $title,
				'cont' 	=> $cont,
				'ishui' => $ishui,
				'id' 	=> $rs['mid'],
				'uid' 	=> $rs['uid'],
				'optdt' 	=> $optdt,
				'sericnum' 	=> $rs['sericnum'],
				'applydt' 	=> $rs['applydt'],
				'statustext' 	=> $statustext,
				'statuscolor' 	=> $statuscolor,
				'statusstr' 	=> $statusstr,
				'modenum'		=> $modenum,
				'modename'		=> $modename
			);
		}
		$arr['rows'] 	= $srows;
		
		return $arr;
	}
	
	private function zhaiyaoar($str)
	{
		$stra = explode("\n", $str);
		$arr  = array();
		foreach($stra as $nr){
			if(strpos($nr,'title:')===0)$arr['title'] = substr($nr, 6);
			if(strpos($nr,'optdt:')===0)$arr['optdt'] = substr($nr, 6);
			if(strpos($nr,'cont:')===0)$arr['cont'] = substr($nr, 5);
		}
		if(!$arr)$arr['cont'] = $str;
		return $arr;
	}
	
	private function contreplaces($str)
	{
		$stra 	= explode('[br]', $str);
		$s1 	= '';
		foreach($stra as $s){
			$a1 = explode('：', $s);
			if(isset($a1[1]) && $a1[1]==''){
			}else{
				$s1.='$%#'.$s.'';
			}
		}
		if($s1!=''){
			$s1 = str_replace('$%#', "\n", substr($s1, 3));
		}
		return $s1;
	}
	
	//获取待办处理数字
	public function daibanshu($uid)
	{
		$where	= '`status` not in(1,2) and isdel=0 and '.$this->rock->dbinstr('nowcheckid', $uid);
		$to 	= $this->rows($where);
		return $to;
	}
	
	//未通过的
	public function applymywgt($uid)
	{
		$where	= '`status`=2 and isdel=0 and `uid`='.$uid.'';
		$to 	= $this->rows($where);
		return $to;
	}
	
	//异常单据条件，审核人中有停用的帐号
	public function errorwhere($qz='')
	{
		$where	= ''.$qz.'`status` not in(1,5) and '.$qz.'`isdel`=0 and '.$qz.'`isturn`=1 and (('.$qz.'`nowcheckid` is null) or ('.$qz.'`nowcheckid` not in(select `id` from `[Q]admin` where `status`=1)))';
		return $where;
	}
	
	//异常数
	public function errortotal()
	{
		$where	= $this->errorwhere();
		$to 	= $this->rows($where);
		return $to;
	}
	
	//单据数据
	public function getbilldata($rows)
	{
		$srows	= array();
		$modeids= '0';
		foreach($rows as $k=>$rs)$modeids.=','.$rs['modeid'].'';
		$modearr= array();
		if($modeids!='0'){
			$moders = m('flow_set')->getall("`id` in($modeids)");
			foreach($moders as $k=>$rs)$modearr[$rs['id']] = $rs;
		}
		$flow = m('flow:user');
		foreach($rows as $k=>$rs){
			$modename	= $rs['modename'];
			$summary	= '';
			$modenum 	= '';
			$statustext	= '记录不存在';
			$statuscolor= '#888888';
			$ishui 		= 0;
			$statusstr	= '不存在';
			if(isset($modearr[$rs['modeid']])){
				$mors 		= $modearr[$rs['modeid']];
				$modename 	= $mors['name'];
				$summary 	= $mors['summary'];
				$modenum 	= $mors['num'];	
				$rers 		= $this->db->getone('[Q]'.$rs['table'].'', $rs['mid']);
				$summary	= $this->rock->reparr($summary, $rers);
				if($rers){
					$nowsets	 = $rs['nowcheckname']; //当前审核人
					$ztarr 		 = $flow->getstatus($rers, $mors['statusstr'], $nowsets);
					$statustext  = $ztarr[0];
					$statuscolor = $ztarr[1];
					if($rers['status']==5)$ishui = 1;
					$statusstr	 = $ztarr[3];
				}else{
					$this->update('isdel=1', $rs['id']); //记录已经不存在了
				}
			}
			
			$srows[]= array(
				'id' 		=> $rs['mid'],
				'optdt' 	=> $rs['optdt'],
				'applydt' 	=> $rs['applydt'],
				'optname' 	=> arrvalue($rs,'optname'),
				'name' 		=> $rs['name'],
				'deptname' 	=> $rs['deptname'],
				'sericnum' 	=> $rs['sericnum'],
				'nowcheckid'=> $rs['nowcheckid'],
				'nowcourseid'=> $rs['nowcourseid'], //当前步骤
				'ishui' 	=> $ishui,
				'modename' 	=> $modename,
				'modenum' 	=> $modenum,
				'summary' 	=> $summary,
				'status'	=> $statusstr
			);
		}
		return $srows;
	}
	
	/**
	* 首页上显示我的申请
	*/
	public function homelistshow()
	{
		$arr 	= $this->getrecord($this->adminid, 'flow_dcl', 1, 5,1);
		$rows  	= $arr['rows'];
		$arows 	= array();
		foreach($rows as $k=>$rs){
			$cont = '【'.$rs['modename'].'】单号:'.$rs['sericnum'].',日期:'.$rs['applydt'].'，'.$rs['statusstr'].'';
			$arows[] = array(
				'cont' 		=> $cont,
				'modename' 	=> $rs['modename'],
				'modenum' 	=> $rs['modenum'],
				'id' 		=> $rs['id'],
				'count'		=> $arr['count']
			);
		}
		return $arows;
	}
	
	/*
	*	更新记录
	*/
	public function updatebill()
	{
		$rows = $this->db->getall('SELECT a.`id`,a.`uname`,a.`udeptname`,a.`status`,b.`name`,b.`deptname` FROM `[Q]flow_bill` a left join `[Q]admin` b on a.`uid`=b.id');
		$ztara= array(1,5);
		foreach($rows as $k=>$rs){
			if(isempt($rs['name']))continue;
			$zt = $rs['status'];
			if(isempt($rs['uname']) || isempt($rs['udeptname']) || !in_array($zt, $ztara)){
				$this->update(array(
					'uname' 	=> $rs['name'],
					'udeptname' => $rs['deptname'],
				), $rs['id']);
			}
		}
	}
	
	
	/**
	*	超过几分钟自动作废
	*/
	public function autocheck()
	{
		//要作废的流程模块
		$rows = $this->db->getall('select `id`,`zfeitime`,`num` from `[Q]flow_set` where `status`=1 and `isflow`=1 and `zfeitime`>0');
		$this->rock->adminid 	= 0;
		$this->rock->adminname 	= '系统';
		foreach($rows as $k=>$rs){
			$modeid = $rs['id'];
			$dtfei	= date('Y-m-d H:i:s', time()-(int)$rs['zfeitime']*60);
			$data 	= $this->getall("`modeid`='$modeid' and `isturn`=1 and `status` not in(1,5) and `updt`<'$dtfei'");
			if($data){
				$flow 	= m('flow')->initflow($rs['num']);
				foreach($data as $k1=>$rs1){
					$flow->loaddata($rs1['mid'], false);
					$flow->zuofeibill('超'.$rs['zfeitime'].'分钟未处理自动作废');
				}
			}
		}
		
		//超过几分钟自动审核通过/不通过
		$dats 		= $this->db->getarr('[Q]flow_course','`zshtime`>0 and `status`=1','zshtime,zshstate');
		$custids 	= '';
		if($dats)foreach($dats as $cid=>$rs)$custids.=','.$cid.'';
		if($custids=='')return;
		$custids	= substr($custids, 1);
		$rows 		= $this->getall('`isturn`=1 and `status`=0 and `nowcourseid` in('.$custids.')');
		if(!$rows)return;
		$modeids 	= '';
		foreach($rows as $k=>$rs)$modeids.=','.$rs['modeid'].'';
		$modearr 	= $this->db->getarr('[Q]flow_set','id in('.substr($modeids, 1).')','`num`');
		
		foreach($rows as $k=>$rs){
			if(isempt($rs['nowcheckid']))continue;
			$cusrs 	= arrvalue($dats,$rs['nowcourseid'], false);
			$modrs 	= arrvalue($modearr, $rs['modeid'], false);
			if(!$modrs || !$cusrs)continue;
			$dtfei	= time()-(int)$cusrs['zshtime']*60;
			$updt 	= $rs['updt'];
			if(isempt($updt))$updt = $rs['optdt'];
			$nowcheckida1 	= explode(',', $rs['nowcheckid']);
			$nowcheckida2 	= explode(',', $rs['nowcheckname']);
			
			$this->rock->adminid 	= arrvalue($nowcheckida1,0);
			$this->rock->adminname 	= arrvalue($nowcheckida2,0);
			
			//超时了
			if(strtotime($updt)<$dtfei){
				$sm = '超'.$cusrs['zshtime'].'分钟未处理自动审核';
				$zt = 1;
				if($cusrs['zshstate']=='2')$zt = 2;
				m('flow')->opt('check', $modrs['num'], $rs['mid'], $zt, $sm);//审核
			}
		}
		
	}
}