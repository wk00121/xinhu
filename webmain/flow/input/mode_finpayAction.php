<?php
/**
*	此文件是流程模块【finpay.付款申请】对应控制器接口文件。
*/ 
class mode_finpayClassAction extends inputAction{
	
	public function selectcust()
	{
		$rows = m('crm')->getmycust($this->adminid, $this->rock->arrvalue($this->rs, 'custid'));
		return $rows;
	}
	
	public function getotherAjax()
	{
		$id = (int)$this->get('id','0');
		$rs = m('customer')->getone($id, 'id,shibieid,openbank,cardid,address,tel');
		return $rs;
	}
}	
			