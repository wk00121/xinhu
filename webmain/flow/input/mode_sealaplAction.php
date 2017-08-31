<?php

class mode_sealaplClassAction extends inputAction{
	

	protected function savebefore($table, $arr, $id, $addbo){
		
	}
	
	
	protected function saveafter($table, $arr, $id, $addbo){
		
	}
	
	//获取印章
	public function getsealdata()
	{
		return  m('seal')->getall('1=1','`id`as value,`name`','`sort`');
	}
}	
			