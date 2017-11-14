<?php

class flow_goodsClassModel extends flowModel
{
	
	protected function flowchangedata(){
		$this->rs['typeid']	 = $this->db->getpval('[Q]option','pid','name', $this->rs['typeid'],'/','id',2);;
	}

	//导入数据的测试显示
	public function flowdaorutestdata()
	{
		return array(
			'typeid' 		=> '办公耗材/笔',
			'name' 			=> '红色粉笔',
			'num' 			=> 'WP-829',
			'guige' 		=> '红色',
			'xinghao' 		=> '5厘米',
			'price' 		=> '0.1',
			'unit' 			=> '盒',
			'stockcs' 		=> '20',
		);
	}
	
	//导入之前
	public function flowdaorubefore($rows)
	{
		$inarr = array();
		$db 	= m('goods');
		foreach($rows as $k=>$rs){
			$rs['typeid'] 	= $this->option->gettypeid('goodstype',$rs['typeid']);
			
			//判断是否存在
			$odi 			= $db->existsgoods($rs);
			if($odi)continue;
			
			$rs['price']	= floatval($this->rock->repempt($rs['price'],'0')); //金额
			$rs['stockcs']	= (int)$this->rock->repempt($rs['stockcs'],'0'); //初始库存

			$inarr[] = $rs;
		}
		
		return $inarr;
	}
	
	//导入后处理（刷新库存）
	public function flowdaoruafter()
	{
		m('goods')->setstock();
	}
}