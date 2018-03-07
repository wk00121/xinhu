<?php 
class minute5ClassAction extends runtAction
{
	
	public function runAction()
	{
		$time 	= time();
		$time1 	= $time;
		$time2 	= $time1+5*60;
		$time3 	= $time1-5*60;
		$this->startdt	= date('Y-m-d H:i:s', $time1);	
		$this->enddt	= date('Y-m-d H:i:s', $time2);
		$this->enddtss	= date('Y-m-d H:i:s', $time3);
		$this->scheduletodo();
		$this->meettodo();
		$this->todologs();
		m('flowbill')->autocheck(); //自动审批作废
		m('reim')->chatpushtowx($this->enddtss);
		echo 'success';
	}
	
	private function scheduletodo()
	{
		m('schedule')->gettododata();
		m('remind')->todorun();//单据提醒设置
	}
	
	private function meettodo()
	{
		$db		= m('meet');
		$rows 	= $db->getall("`state` in(0,1) and `type`=0 and `startdt` like '".$this->date."%' and `status`=1");
		$time	= time();
		$adm	= m('admin');
		$flow	= m('flow')->initflow('meet');
		foreach($rows as $k=>$rs){
			$zt 	= $rs['state'];
			$dts	= explode(' ', $rs['startdt']);
			$sttime = strtotime($rs['startdt']);
			$ettime = strtotime($rs['enddt']);

			$nzt	= -1;
			if($ettime <= $time){
				$nzt = 2;
			}else{
				if($time >= $sttime && $time< $ettime){
					if($zt==0)$nzt = 1;
				}else{
					$jg = $sttime - $time;
					if($jg <= 600 && $zt==0){
						$ssj 	= floor($jg/60);
						$tzuid 	= $adm->gjoin($rs['joinid']);
						$cont  	= '['.$rs['title'].']会议将在'.$ssj.'分钟后的'.$dts[1].'开始请做好准备，会议室['.$rs['hyname'].']';
						$flow->id = $rs['id'];
						$flow->push($tzuid, '', $cont);
					}
				}
			}
			if($nzt != -1)$db->update("`state`='$nzt'", $rs['id']);
		}
	}
	
	//错误日志通知给管理员提醒
	private function todologs()
	{
		$opddt 	= date('Y-m-d H:i:s',time()-5*60);
		$rows  	= $this->db->getall("select `type`,count(1)stotal from `[Q]log` where `level`=2 and `optdt`>'$opddt' group by `type`");
		$str 	= '';
		foreach($rows as $k=>$rs)$str.=''.$rs['type'].'('.$rs['stotal'].'条);';
		
		if($str!='')m('todo')->add(1,'错误日志', $str.'请到[系统→系统工具→日志查看]下查看。');
	}
}