<?php 
class indexClassAction extends apiAction
{
	public function indexAction()
	{
		$this->showreturn('','error', 203);
	}
	
	/**
	*	手机app读取
	*/
	public function indexinitAction()
	{
		$dbs 			= m('reim');
		$ntime			= floatval($this->post('ntime'));
		$uid 			= $this->adminid;
		$arr['loaddt']  	= $this->now;
		$arr['splittime'] 	= (int)($ntime/1000-time());
		$arr['reimarr']		= $dbs->gethistory($uid);
		$this->showreturn($arr);
	}
	
	/**
	* app首页接口截止
	*/
	public function indexappAction()
	{
		$dbs 			= m('reim');
		$ntime			= floatval($this->post('ntime'));
		$uid 			= $this->adminid;
		$agent 			= $dbs->getappagent($uid);
		$arr['loaddt']  	= $this->now;
		$arr['splittime'] 	= (int)($ntime/1000-time());
		$arr['reimarr']		= $dbs->gethistory($uid);
		$arr['agentarr']	= $agent['rows'];
		$arr['agentstotal']	= $agent['stotal'];
		$arr['maxupsize']	= c('upfile')->getmaxzhao();//最大上传大小M
		$arr['appversion']	= $this->get('appversion');
		$arr['xinhuver']	= VERSION;
		$arr['wsconfig']	= $dbs->getreims();
		
		$this->showreturn($arr);
	}
	
	public function lunxunAction()
	{
		$uid 			= $this->adminid;
		$loaddt			= $this->post('loaddt');
		//$reimarr 		= m('reim')->getwdarr($uid, $loaddt);
		$reimarr 		= m('reim')->gethistory($uid, $loaddt);
		$arr['reimarr'] = $reimarr;
		$arr['loaddt']  = $this->now;
		m('login')->uplastdt();
		$this->showreturn($arr);
	}
	
	
	//应用获取数据
	public function getyydataAction()
	{
		$num 	= $this->post('num');
		$event 	= $this->post('event');
		$page 	= (int)$this->post('page');
		$rows 	= m('agent:'.$num.'')->getdata($this->adminid, $num, $event, $page);
		
		$this->showreturn($rows);
	}
	
	public function yyoptmenuAction()
	{
		$num 	= $this->post('modenum');
		$sm 	= $this->post('sm');
		$optid 	= (int)$this->post('optmenuid');
		$zt 	= (int)$this->post('statusvalue');
		$mid 	= (int)$this->post('mid');
		$msg 	= m('flow')->opt('optmenu', $num, $mid, $optid, $zt, $sm);
		if($msg != 'ok')$this->showreturn('', $msg, 201);
		$this->showreturn('');
	}
	
	public function pushtestAction()
	{
		m('reim')->pushagent('1','会议','关于端午节放假通知');
		//$a = c('apiCloud')->send(1,'通知','内容');
		//$a = c('JPush')->send('2','发来一条消息', '内容');
		//print_r($a);
		echo 'ok';
	}
	
	public function changetxAction()
	{
		$apptx = (int)$this->post('apptx');
		m('admin')->update("`apptx`='$apptx'", $this->adminid);
		$this->showreturn('');
	}
	
	
	public function checkewmAction()
	{
		$randkey = $this->get('randkey');
		$lx 	 = (int)$this->get('lx');
		$val 	 = $this->adminid;
		$lxarr 	 = array('已取消','已确认');
		if($lx==0)$val='-1';
		$this->option->setval($randkey, $val);
		$this->showreturn($lxarr[$lx]);
	}
	
	/**
	*	切换公司
	*/
	public function changecompanyAction()
	{
		$id = (int)$this->get('id');
		$db = m('admin');
		$db->update('comid='.$id.'', '`id`='.$this->adminid.'');
		$db->getcompanyinfo();
		return returnsuccess();
	}
	public function getcompanyAction()
	{
		$carr = m('admin')->getcompanyinfo($this->adminid);
		$this->showreturn($carr);
	}
	
	/**
	*	华为设置客户端token
	*/
	public function updateTokenIpAction()
	{
		$hwtoken = $this->get('hwtoken');
		if(!isempt($hwtoken)){
			m('login')->update("`ip`='$hwtoken'", "`token`='$this->admintoken'");
		}
		return returnsuccess();
	}
}