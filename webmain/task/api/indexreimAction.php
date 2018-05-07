<?php 
class indexreimClassAction extends apiAction
{
	/**
	*	PC客户端首页读取
	*/
	public function indexinitAction()
	{
		$viewobj 	= m('view');
		$dbs 		= m('reim');
		
		$udarr 		= m('dept')->getdeptuserdata();
		$userarr 	= $udarr['uarr'];
		$deptarr 	= $udarr['darr'];
		
		$grouparr 	= $dbs->getgroup($this->adminid);
		$agentarr	= $dbs->getagent($this->adminid);
		$historyarr	= $dbs->gethistory($this->adminid);
		$applyarr	= m('mode')->getmoderows($this->adminid,'and islu=1');
		$modearr	= array();
		foreach($applyarr as $k=>$rs){
			if(!$viewobj->isadd($rs, $this->adminid))continue;
			$modearr[]=array('type'=>$rs['type'],'num'=>$rs['num'],'name'=>$rs['name']);
		}
		
		$arr['deptjson']	= json_encode($deptarr);
		$arr['userjson']	= json_encode($userarr);
		$arr['groupjson']	= json_encode($grouparr);
		$arr['agentjson']	= json_encode($agentarr);
		$arr['historyjson'] = json_encode($historyarr);
		$arr['modearr'] 	= $modearr;
		$arr['config'] 		= $dbs->getreims();
		$arr['loaddt'] 		= $this->now;
		$arr['ip'] 			= $this->ip;
		m('login')->uplastdt();
		
		$this->showreturn($arr);
	}
	
	/**
	*	REIM的初始化
	*/
	public function reiminitAction()
	{
		$dbs 		= m('reim');
		
		$udarr 		= m('dept')->getdeptuserdata();
		$userarr 	= $udarr['uarr'];
		$deptarr 	= $udarr['darr'];
		
		$grouparr 	= $dbs->getgroup($this->adminid);
		$historyarr	= $dbs->gethistory($this->adminid);
		$agentarr	= $dbs->getagent($this->adminid);
		
		$arr['deptjson']	= json_encode($deptarr);
		$arr['userjson']	= json_encode($userarr);
		$arr['groupjson']	= json_encode($grouparr);
		$arr['historyjson'] = json_encode($historyarr);
		$arr['agentjson']	= json_encode($agentarr);
		$arr['config'] 		= $dbs->getreims();
		$arr['loaddt'] 		= $this->now;
		$arr['ip'] 			= $this->ip;
		m('login')->uplastdt();
		
		$this->showreturn($arr);
	}
	
	/**
	*	手机网页版读取
	*/
	public function mwebinitAction()
	{
		$dbs 		= m('reim');
		$agentarr	= $dbs->getappagent($this->adminid);
		$historyarr	= $dbs->gethistory($this->adminid);
		
		$arr['agentjson']	= json_encode($agentarr['rows']);
		$arr['historyjson'] = json_encode($historyarr);
		$arr['loaddt'] 		= $this->now;
		m('login')->uplastdt();
		$this->showreturn($arr);
	}
	
	public function ldataAction()
	{
		$loaddt		= $this->rock->jm->base64decode($this->post('loaddt'));
		$type		= $this->post('type','history');
		$dbs 		= m('reim');
		$json		= array();
		if($type=='history')$json = $dbs->gethistory($this->adminid, $loaddt);
		if($type=='group')$json = $dbs->getgroup($this->adminid);
		if($type=='dept')$json 	= m('dept')->getdata();
		if($type=='user')$json 	= m('admin')->getuser();
		if($type=='agent')$json = $dbs->getagent($this->adminid);
		if($type=='config')$json = m('reim')->getreims();
		
		$arr['json'] 	= json_encode($json);	
		$arr['loaddt']  = $this->now;
		$arr['ip']  	= $this->ip;
		$arr['type']  	= $type;
		m('login')->uplastdt();
		$this->showreturn($arr);
	}

	
	public function indexupgetAction()
	{
		$historyarr			= m('reim')->gethistory($this->adminid);
		$arr['historyjson'] = json_encode($historyarr);
		m('login')->uplastdt();
		$this->showreturn($arr);
	}
	
	public function changewxtxAction()
	{
		$tx = (int)$this->post('tx','1');
		m('admin')->update('wxtx='.$tx.'', $this->adminid);
		$this->showreturn('');
	}
	
	public function showmyinfoAction()
	{
		$arr = m('admin')->getone($this->adminid,'`id`,`deptallname`,`ranking`,`face`,`name`,`user`,`mobile`');
		if(!$arr)$this->showreturn('','not user', 201);
		if(isempt($arr['face']))$arr['face']='images/noface.png';
		$arr['admintoken']  = $this->admintoken;
		//$arr['isgzh'] 		= m('wxgzh:index')->isusegzh(1); //判断是否有设置公众号
		
		$this->showreturn($arr);
	}
	
	//同步微信上头像
	public function tongbufaceAction()
	{
		$reim = m('reim');
		if($reim->isanwx()){
			$barr 	= m('weixin:user')->anayface($this->userrs['user'], true);
			if($barr['errcode'] != 0)$this->showreturn('',$barr['msg'],202);
			$this->showreturn($barr);
		}else{
			if($reim->installwx(1)){
				$barr 	= m('weixinqy:user')->anayface($this->userrs['user'], true);
				if($barr['errcode'] != 0)$this->showreturn('',$barr['msg'],202);
				$this->showreturn($barr);
			}else{
				$this->showreturn('','没安装微信企业号',201);
			}
		}
		
	}
	
	public function loadinfoAction()
	{
		$type 	= $this->get('type');
		$receid = $this->get('receid');
		$arr 	= array();
		if($type=='user'){
			$arr 	= m('admin')->getuser(0, $receid);
		}
		$this->showreturn($arr);
	}
}