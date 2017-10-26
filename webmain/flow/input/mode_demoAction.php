<?php
/**
*	此文件是流程模块【demo.演示测试】对应控制器接口文件。
*/ 
class mode_demoClassAction extends inputAction{
	
	/**
	*	重写函数：保存前处理，主要用于判断是否可以保存
	*	$table String 对应表名
	*	$arr Array 表单参数
	*	$id Int 对应表上记录Id 0添加时，大于0修改时
	*	$addbo Boolean 是否添加时
	*	return array('msg'=>'错误提示内容','rows'=> array()) 可返回空字符串，或者数组 rows 是可同时保存到数据库上数组
	*/
	protected function savebefore($table, $arr, $id, $addbo){
		
	}
	
	/**
	*	重写函数：保存后处理，主要保存其他表数据
	*	$table String 对应表名
	*	$arr Array 表单参数
	*	$id Int 对应表上记录Id
	*	$addbo Boolean 是否添加时
	*/	
	protected function saveafter($table, $arr, $id, $addbo){
		
	}
	
	
	//弹出下拉选择单选
	public function tanxuan()
	{
		$rows[] = array(
			'name' => '第一个数据'
		);
		$rows[] = array(
			'name' => '第二个数据'
		);
		return $rows;
	}
	
	//弹出下拉选择多选
	public function tanxuancheck()
	{
		$rows[] = array(
			'name' => '数据1'
		);
		$rows[] = array(
			'name' => '数据2'
		);
		for($i=3;$i<=500;$i++)$rows[] = array(
			'name' => '数据'.$i.''
		);
		return $rows;
	}
	
	//联动获取城市数据数据库表city,根据pid读取
	public function getcityAjax()
	{
		$sheng 	= $this->post('sheng');//省名称
		if(isempt($sheng))return array();//为空
		$dbs 	= m('city');
		//获取省ＩＤ
		$pid 	= $dbs->getmou('id',"`type`=1 and `name`='$sheng'");//type=1
		
		$rows 	 = $dbs->getall("`pid`='$pid'",'name','`sort`'); //查找数据
		
		return $rows;//返回数据
	}
	
	//联动获取城市数据数据库表city,根据pid读取
	public function getxianAjax()
	{
		$city 	= $this->post('city');//省名称
		if(isempt($city))return array();//为空
		$dbs 	= m('city');
		//获取城市ＩＤ
		$pid 	= $dbs->getmou('id',"`type`=2 and `name`='$city'");//type=2
		
		$rows 	 = $dbs->getall("`pid`='$pid'",'name','`sort`'); //查找数据
		
		return $rows;//返回数据
	}
	
	
	//下拉框市的数据源
	public function citydata()
	{
		return $this->getshegnxiandat(arrvalue($this->rs,'sheng'), 1);
	}
	
	//下拉框县的数据源
	public function xiandata()
	{
		return $this->getshegnxiandat(arrvalue($this->rs,'shi'), 2);
	}
	
	//获取下级
	private function getshegnxiandat($name, $type)
	{
		if(isempt($name))return array();
		$dbs 	= m('city');
		
		//获取城市ＩＤ
		$pid 	= $dbs->getmou('id',"`type`='$type' and `name`='$name'");
		
		$rows 	 = $dbs->getall("`pid`='$pid'",'name','`sort`'); //查找数据
		
		return $rows;//返回数据
	}
}	
			