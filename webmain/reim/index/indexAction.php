<?php
class indexClassAction extends ActionNot
{
	public function initAction()
	{
		if($this->adminid==0){
			$this->rock->location('?d=reim&m=login');
		}
	}
	
	public function defaultAction()
	{
		$this->title 	= getconfig('reimtitle','REIM');
		$my				= $this->db->getone('[Q]admin', "`id`='$this->adminid'",'`face`,`id`,`name`,`ranking`,`deptname`,`deptallname`,`type`,`style`');
		
		$this->smartydata['my']			= $my;
		$this->smartydata['face']		= $this->rock->repempt($my['face'], 'images/noface.png');
	}
}