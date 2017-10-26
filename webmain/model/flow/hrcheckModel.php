<?php
//模块 hrcheck.考核评分
class flow_hrcheckClassModel extends flowModel
{
	//审核完成处理
	protected function flowcheckfinsh($zt){
		//最后得分计算，
		if($zt==1){
			$fenzp = floatval($this->rs['fenzp']);
			$fensj = floatval($this->rs['fensj']);
			$fenrs = floatval($this->rs['fenrs']);
			
			//默认分数=自己评分*50% + 上级评分*25% + 人事评分*25%
			$fen   = $fenzp*0.5 + $fensj*0.25 + $fenrs*0.25; 
			
			//3个平均分
			//$fen 	= ($fenzp+$fensj+$fenrs)/3;

			
			$this->update(array('fen' => $fen), $this->id);		
		}
	}

}