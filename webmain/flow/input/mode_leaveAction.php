<?php
class mode_leaveClassAction extends inputAction{

	protected function savebefore($table, $arr, $id, $addbo){
		$msg 	= m('kaoqin')->leavepan($arr['uid'], $arr['qjkind'], $arr['stime'], $arr['etime'], $arr['totals'], $id);
		return $msg;
	}
	
	public function totalAjax()
	{
		$start	= $this->post('stime');
		$end	= $this->post('etime');
		$kq 	= m('kaoqin');
		//$date	= c('date', true);
		//$sj		= $date->datediff('H', $start, $end);
		$sj 	= $kq->getsbtime($this->adminid,$start, $end);
		$sbtime = $kq->getworktime($this->adminid, $start); //一天上班小时
		$sj 	= $this->qjshieuts($sj);
		return array($sj, '', $sbtime);
	}
	
	//请假最小单位0.5小时
	private function qjshieuts($jst)
	{
		$sts = explode('.', $jst.'');
		if(isset($sts[1])){
			$vss = floatval('0.'.$sts[1]);
			if($vss>0 && $vss<=0.5)$vss = 0.5;
			if($vss>0.5)$vss = 1;
			$jst = floatval($sts[0])+$vss;
		}else{
		}
		return $this->rock->number($jst, 1);
	}
	

	
	//统计剩余时间
	public function getshentime()
	{
		$mid = (int)$this->get('mid');
		$kqm = m('kaoqin');
		$str = $kqm->getqjsytimestr($this->adminid, '', $mid);
		return $str;
	}
}	
			