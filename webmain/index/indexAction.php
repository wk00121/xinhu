<?php 
class indexClassAction extends Action{
	
	public function defaultAction()
	{
		$afrom 			= $this->get('afrom');
		$this->tpltype	= 'html';
		$my			= $this->db->getone('[Q]admin', "`id`='$this->adminid'",'`face`,`id`,`name`,`ranking`,`deptname`,`deptallname`,`type`,`style`');
		$allmenuid	= m('sjoin')->getuserext($this->adminid, $my['type']);
		m('dept')->online(1);
		$mewhere	= '';
		$isadmin	= 1;
		$myext		= $allmenuid;
		if($myext != '-1'){
			$isadmin	= 0;	
			$mewhere	= ' and `id` in('.str_replace(array('[',']'), array('',''), $myext).')';
		}
		$this->rock->savesession(array(
			'adminallmenuid'	=> $allmenuid,
			'isadmin'			=> $isadmin
		));
		$this->smartydata['topmenu'] 	= m('menu')->getall("`pid`=0 and `status`=1 $mewhere order by `sort`");
		$homeurl 						= $this->jm->base64decode($this->get('homeurl'));
		$homename 						= $this->jm->base64decode($this->get('homename'));
		$menuid 						= (int)$this->jm->base64decode($this->get('menuid'));
		$showkey						= $this->jm->base64encode($this->jm->getkeyshow());
		if($menuid<1)$menuid = '';
		if($homeurl=='')$showkey = '';
		if(!isempt($homeurl) && isempt($menuid))return '无权限打开['.$homename.']的页面1';
		if(!isempt($menuid) && $isadmin==0){
			if(!contain($myext,'['.$menuid.']'))return '无权限打开['.$homename.']的页面2';
		}
		$this->smartydata['showkey']	= $showkey;
		$this->smartydata['homeurl']	= $homeurl;
		$this->smartydata['homename']	= $homename;
		$this->smartydata['admintype']	= $isadmin;
		$this->smartydata['my']			= $my;
		$this->smartydata['afrom']		= $afrom;
		$this->smartydata['face']		= $this->rock->repempt($my['face'], 'images/noface.png');
		$this->smartydata['style']		= $this->rock->repempt($my['style'], '0');
		if(!isempt($homename))$this->title = $homename;
	}
	
	private function menuwheres()
	{
		$this->menuwhere = '';
		$myext	= $this->getsession('adminallmenuid');
		if($myext != '-1'){	
			$this->menuwhere	= ' and `id` in('.str_replace(array('[',']'), array('',''), $myext).')';
		}
	}
	
	/**
	*	搜索菜单
	*/
	public function getmenusouAjax()
	{
		$key = $this->post('key');
		$this->menuwheres();
		$this->addmenu = m('menu')->getall("`status`=1 $this->menuwhere and `name` like '%$key%' and ifnull(`url`,'')<>'' order by `pid`,`sort` limit 10",'`id`,`num`,`url`,`icons`,`name`,`pid`');
		$arr	= $this->getmenu(0, 1);
		$this->returnjson($arr);
	}
	
	/**
	*	获取菜单
	*/
	public function getmenuAjax()
	{
		$pid 	= $this->get('pid');
		$this->menuwheres();
		$this->addmenu = m('menu')->getall("`status`=1 $this->menuwhere order by `sort`,`id`",'`id`,`num`,`url`,`icons`,`name`,`pid`');
		$arr	= $this->getmenu($pid,0);
		$this->returnjson($arr);
	}
	
	private function getmenu($pid, $lx=0)
	{
		$menu	= $this->addmenu;
		$rows	= array();
		foreach($menu as $k=>$rs){
			if($lx == 0 && $pid != $rs['pid'])continue;
			$num			= $rs['num'];
			$sid			= $rs['id'];
			$icons			= $rs['icons'];
			if(isempt($num))$num 		= 'num'.$sid;
			if(isempt($icons))$icons 	= 'bookmark-empty';
			$rs['icons']	= $icons;
			$rs['num']		= $num;
			if($lx == 0){
				$children		= $this->getmenu($sid);
				$rs['children']	= $children;
				$rs['stotal']	= count($children);
			}else{
				$rs['stotal']	= 0;
			}
			$rows[] = $rs;
		}
		return $rows;
	}
	
	public function downAction()
	{
		$this->display = false;
		$id  = (int)$this->jm->gettoken('id');
		m('file')->show($id);
	}
	
	/**
	*	单页显示
	*/
	public function showAction()
	{
		$url 	= $this->get('url');
		if($url=='')exit('无效请求');
		$this->defaultAction();
	}
	
	/**
	*	获取模版文件
	*/
	public function getshtmlAction()
	{
		$surl = $this->jm->base64decode($this->get('surl'));
		if(isempt($surl))exit('not found');
		$file = ''.P.'/'.$surl.'.php';
		if(!file_exists($file))$file = ''.P.'/'.$surl.'.shtml';
		if(!file_exists($file))exit('404 not found '.$surl.'');
		if(contain($surl,'home/index/rock_index'))$this->showhomeitems();//首页的显示
		$this->displayfile = $file;
	}
	//显示桌面项目
	private function showhomeitems()
	{
		$rows = m('homeitems')->getmyshow();
		if(!$rows)$rows = json_decode('[{"num":"kjrk","row":"0","name":"快捷入口","sort":"0"},{"num":"gong","row":"0","name":"通知公告","sort":"1"},{"num":"kqdk","row":"0","name":"考勤打卡","sort":"2"},{"num":"gwwx","row":"0","receid":"u1","recename":"管理员","name":"微信办公","sort":"10"},{"num":"apply","row":"1","name":"我的申请","sort":"0"},{"num":"meet","row":"1","name":"今日会议","sort":"2"},{"num":"syslog","receid":"u1","recename":"管理员","row":"1","name":"系统日志","sort":"3"},{"num":"about","row":"1","receid":"u1","recename":"管理员","name":"关于信呼","sort":"10"}]', true);
		$homeitems  = $homearrs = array(); 
		foreach($rows as $k=>$rs){
			$homeitems[$rs['row']][] = $rs['num'];
			$homearrs[] = $rs['num'];
		}
		$this->assign('homeitems', $homeitems);
		$this->assign('homearrs', $homearrs);
	}
	
	//开发时快速打开文件
	public function openfileAjax()
	{
		$file = $this->rock->jm->base64decode($this->get('file'));
		$str  = ''.ROOT_PATH.'/'.$file.'';
		$bo   = c('socket')->udpsend($str,'cmd');
		if(is_string($bo))return $bo;
		return 'ok';
	}
	
	public function testAjax()
	{
		//header("HTTP/1.1 500 Not Found");
		echo $this->get('abc');
	}
}