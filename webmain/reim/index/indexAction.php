<?php
class indexClassAction extends ActionNot
{
	public function initAction()
	{
		
	}
	
	public function defaultAction()
	{
		$ybarr	 = $this->option->authercheck();
		if(is_string($ybarr))return $ybarr;
		
		if($this->adminid==0){
			$this->rock->location('?d=reim&m=login');
		}
		$this->title 	= getconfig('reimtitle','REIM');
		$my				= $this->db->getone('[Q]admin', "`id`='$this->adminid'",'`face`,`id`,`name`,`ranking`,`deptname`,`deptallname`,`type`,`style`');
		
		$this->smartydata['my']			= $my;
		$this->smartydata['face']		= $this->rock->repempt($my['face'], 'images/noface.png');
	}
	
	public function xinAction()
	{
		$ybarr	 = $this->option->authercheck();
		if(is_string($ybarr))return $ybarr;
		
		if($this->adminid==0){
			$this->rock->location('?d=reim&m=login&a=xin');
		}
		$this->title 	= getconfig('reimtitle','REIM');
		$my				= $this->db->getone('[Q]admin', "`id`='$this->adminid'",'`face`,`id`,`name`,`ranking`,`deptname`,`deptallname`,`type`,`style`');
		
		$this->smartydata['my']			= $my;
		$this->smartydata['face']		= $this->rock->repempt($my['face'], 'images/noface.png');
		$this->smartydata['logo']		= 'images/logo.png';
		$this->smartydata['showtitle']	= TITLE;
		
		if(ISMORECOM){
			$companyinfo = m('admin')->getcompanyinfo($this->adminid, 1);
			$oaname 	 = $companyinfo['oaname'];
			if(!isempt($oaname))$this->smartydata['showtitle'] = $oaname;
			$this->smartydata['logo'] = $companyinfo['logo'];
		}
	}
}