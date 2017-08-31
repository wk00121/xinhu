<?php
class agent_daibanClassModel extends agentModel
{
	public function initModel()
	{
		$this->settable('flow_bill');
	}
	
	public function gettotal()
	{
		$stotal	= $this->getdbtotal($this->adminid);
		$titles	= '';
		return array('stotal'=>$stotal,'titles'=> $titles);
	}
	
	private function getdbtotal($uid)
	{
		$stotal	= m('flowbill')->daibanshu($uid);
		return $stotal;
	}
	
	protected function agenttotals($uid)
	{
		return array(
			'daiban' => $this->getdbtotal($uid)
		);
	}
	
	protected function agentdata($uid, $lx)
	{
		$arr 	= m('flowbill')->getrecord($uid, $this->agentnum.'_'.$lx, $this->page, $this->limit, 0);
		return $arr;
	}
}