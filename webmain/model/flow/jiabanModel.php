<?php
//加班
class flow_jiabanClassModel extends flowModel
{
	
	public function flowrsreplace($rs, $lx=0)
	{
		$rs['modenum'] = $this->modenum;
		$type 			= arrvalue($rs,'jiatype','0');
		$types			= array('调休','加班费');
		$rs['jiatype']  = $types[$type];
		$dakatime		= '未打卡';
		if($type=='1'){
			if($lx==1)$rs['jiatype'].=''.$rs['jiafee'].'元';
			$kqdkjl		= m('kqdkjl')->getall("`uid`='".$rs['uid']."' and `dkdt`>='".$rs['stime']."' and `dkdt`<='".$rs['etime']."'",'dkdt','`dkdt` desc');
			if($kqdkjl)$dakatime='';
			foreach($kqdkjl as $k=>$rs){
				if($k>0)$dakatime.=',&nbsp;';
				$dakatime.=''.$rs['dkdt'].'';
			}
		}else{
			$rs['jiafee'] = '';
		}
		$rs['dakatime']	= $dakatime;
		
		return $rs;
	}
	
	protected function flowgetfields($lx)
	{
		$arr['dakatime'] 		= '此时间段打卡';
		return $arr;
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
}