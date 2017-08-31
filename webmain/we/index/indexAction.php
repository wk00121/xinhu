<?php 
class indexClassAction extends ActionNot{
	
	public function initAction()
	{
		$this->mweblogin(0, true);
	}
	
	public function defaultAction()
	{
		$this->title = getconfig('apptitle','信呼');
	}
	
	public function editpassAction()
	{
		
	}
	
	public function testAction()
	{
		
	}
	
	/**
	*	用户信息
	*/
	public function userinfoAction()
	{
		$uid = (int)$this->get('uid');
		$urs = m('admin')->getone($uid, '`id`,`name`,`deptallname`,`ranking`,`tel`,`email`,`mobile`,`sex`,`face`');
		if(!$urs)exit('not user');
		if(isempt($urs['face']))$urs['face']='images/noface.png';
		$this->assign('arr', $urs);
	}
}