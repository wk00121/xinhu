<?php
/**
*	此文件是流程模块【diaobo.调拨单】对应控制器接口文件。
*/ 
class mode_diaoboClassAction extends inputAction{
	
	
	protected function savebefore($table, $arr, $id, $addbo){
		
	}
	
		
	protected function saveafter($table, $arr, $id, $addbo){
		
	}
	
	//仓库列表
	public function godepot()
	{
		
	}
	
	public function getgoodsdata()
	{
		return m('goods')->getgoodsdata();
	}
}	
			