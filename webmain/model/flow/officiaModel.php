<?php
class flow_officiaClassModel extends flowModel
{

	protected function flowdatalog($arr)
	{
		
		$arr['title'] 		= '';

		return $arr;
	}
	
	public function flowsearchfields()
	{
		$arr[] = array('name'=>'拟办人...','fields'=>'uid');
		return $arr;
	}
}