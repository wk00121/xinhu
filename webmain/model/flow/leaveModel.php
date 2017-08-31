<?php
//请假条的
class flow_leaveClassModel extends flowModel
{
	
	public function flowrsreplace($rs)
	{
		$rs['modenum']  = $this->modenum;
		$totday			= floatval(arrvalue($rs,'totday','0'));
		if($totday>0)$rs['totals'].='('.$totday.'天)';
		return $rs;
	}

	protected function flowbillwhere($uid, $lx)
	{
		$month	= $this->rock->post('month');
		$where 	= '';
		if($month!=''){
			$where.=" and `stime` like '$month%'";
		}

		return array(
			'where' => $where
		);
	}
	
	/**
	*	年假添加设置(自动添加)，可计划任务没有运行一次，兑换为小时的，默认一天8小时
	*/
	public function autoaddleave()
	{
		$type = 0; //根据哪个类型计算年：0根据入职日期，1根据转正日期
		$nian = array(
			//	 开始   截止    年假天数
			array(0, 	0, 		0), //0-0年，0天 
			array(1, 	10, 	5), //1年(含)-10年(含)，5天 
			array(11, 	20, 	10), //11年(含)-20年(含)，10天 
			array(21, 	9999, 	15), //21年(含)以上，15天 
		);
		//配置可根据自己情况修改
		$adlx = array('workdate','positivedt');
		
	}
}