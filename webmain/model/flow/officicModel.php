<?php
//公文查阅
class flow_officicClassModel extends flowModel
{

	
	public function initModel()
	{
		$this->logobj = m('log');
	}
	
	//打开详情时跳转到地理位置显示
	protected function flowchangedata()
	{
		/*
		if(!isajax()){
			$num = ($this->rs['type']=='1') ? 'officib' : 'officia';
			$url = $this->getxiangurl($num, $this->id, A);
			$this->rock->location($url);
			exit();
		}*/
	}
	
	public function flowrsreplace($rs, $lx=0)
	{
		if($lx==2){
			$zt = $this->logobj->isread($this->mtable, $rs['id'], $this->adminid);
			if($zt>0)$rs['ishui']=1;
		}
		//$rs['modenum'] = ($rs['type']=='1') ? 'officib' : 'officia';
		return $rs;
	}
	
	protected function flowdatalog($arr)
	{
		
		$arr['title'] 		= '';

		return $arr;
	}
	
}