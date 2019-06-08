<?php
class flow_groupClassModel extends flowModel
{
	
	protected  $flowcompanyidfieds = 'companyid';
	
	protected function flowbillwhere($uid, $lx)
	{
		
		$carr		= $this->adminmodel->getcompanyinfo();
		$this->allid= $carr['companyallid'];
		$companywhere = ' and `companyid` in('.join(',', $this->allid).')';
		return array(
			'companywhere' => $companywhere
		);
	}
}