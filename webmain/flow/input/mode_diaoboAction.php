<?php
/**
*	此文件是流程模块【diaobo.调拨单】对应控制器接口文件。
*/ 
class mode_diaoboClassAction extends inputAction{
	
	
	protected function savebefore($table, $arr, $id, $addbo){
		$data = $this->getsubtabledata(0);
		if(count($data)==0)return '至少要有一行记录';
		foreach($data as $k=>$rs){
			if(isset($rs['aid']))foreach($data as $k1=>$rs1){
				if($k!=$k1){
					if($rs['aid']==$rs1['aid'])
						return '行'.($k1+1).'的物品已在行'.($k+1).'上填写，不要重复填写';
				}
			}
		}
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
			