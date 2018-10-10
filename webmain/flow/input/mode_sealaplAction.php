<?php

class mode_sealaplClassAction extends inputAction{
	

	protected function savebefore($table, $arr, $id, $addbo){
		
	}
	
	
	protected function saveafter($table, $arr, $id, $addbo){
		
	}
	
	//获取印章
	public function getsealdata()
	{
		$rows = m('seal')->getall('1=1','`id`as value,`name`,`type`','`sort`');
		$aaar = $barr = array();
		foreach($rows as $k=>$rs){
			$optgroup = '印章';
			if(!contain($rs['type'],'章'))$optgroup='证照';
			$rs['optgroup'] = $optgroup;
			if($optgroup=='印章'){
				$aaar[] = $rs;
			}else{
				$barr[] = $rs;
			}
		}
		
		return array_merge($aaar, $barr);
	}
}	
			