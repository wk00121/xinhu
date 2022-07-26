<?php
/**
*	此文件是流程模块【finpay.付款申请】对应控制器接口文件。
*/ 
class mode_finpayClassAction extends inputAction{
	
	
	protected function savebefore($table, $arr, $id, $addbo){
		$rows['type'] = '4';//一定要是4，不能去掉
		return array(
			'rows'=>$rows
		);
	}
	
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
			