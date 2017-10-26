<?php
class kaoqinClassAction extends runtAction
{
	/**
	*	定时任务发送昨天考勤异常的啊
	*/
	public function todoAction()
	{
		$dt 	= date('Y-m-d', time()-3600*20);//昨天
		$sql  	= "SELECT a.uid FROM `[Q]kqanay` a left join `[Q]userinfo` b on a.uid=b.id where a.dt='$dt' and b.iskq=1 and a.state<>'正常' and a.states is null and a.iswork=1 group by a.uid;";
		$rows 	= $this->db->getall($sql);
		$ids 	= '';
		foreach($rows as $k=>$rs){
			$ids .=','.$rs['uid'].'';
		}
		if($ids!=''){
			$flow 	= m('flow')->initflow('leavehr');
			$flow->push(substr($ids, 1),'考勤','昨天['.$dt.']的你考勤存在异常，此消息仅供参考！','考勤异常提醒');
		}
		echo 'success';
	}
	
	public function anayAction()
	{
		$dt 	= date('Y-m-d', time()-3600*20);//昨天
		m('kaoqin')->kqanayalldt($dt);
		echo 'success';
	}
	
	//每月分析上月
	public function lmanayAction()
	{
		$month = c('date')->adddate($this->rock->date, 'm', -1,'Y-m');
		m('kaoqin')->kqanayall($month);
		echo 'success';
	}
	
	//分析工作日报统计
	public function dailyfxAction()
	{
		$dt 	= c('date')->adddate($this->rock->date, 'd', -1);
		$flow 	= m('flow')->initflow('daily');
		$flow->dailyanay(0, $dt);
		$flow->dailytodo($dt); 	//未写日报通知
		echo 'success';
	}

	public function dayAction()
	{
		m('flow:leave')->autoaddleave(); //年假自动添加
		echo 'success';
	}
}