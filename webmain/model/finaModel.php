<?php
class finaClassModel extends Model
{

	public function initModel()
	{
		$this->settable('fininfom');
	}
	
	//统计我未还款
	public function totaljie($uid, $id=0)
	{
		$where 	= 'and id<>'.$id.'';
		$to1  	= floatval($this->getmou('sum(money)money',"`uid`='$uid' and `type`=2 and `status`=1"));
		$to2  	= floatval($this->getmou('sum(money)money',"`uid`='$uid' and `type`=3 and `status`<>5 $where"));
		$to 	= $to1-$to2;
		return $to;
	}
	
	public function getjkdwhere()
	{
		return '(select `uid` from `[Q]fininfom` where `type`=2 and `status`=1)';
	}
	
	//统计
	public function totalfkd($rows, $uids)
	{
		$carr = $barr = array();
		
		//借款
		$hkto = $this->db->getall("select uid,sum(money)money from `[Q]fininfom` where `uid` in($uids) and `type`=2 and `status`=1");
		foreach($hkto as $k=>$rs)$carr[$rs['uid']] = $rs['money'];
		
		//还的
		$hkto = $this->db->getall("select uid,sum(money)money from `[Q]fininfom` where `uid` in($uids) and `type`=3 and `status`=1");
		foreach($hkto as $k=>$rs)$barr[$rs['uid']] = $rs['money'];
		
		
		foreach($rows as $k=>$rs){
			$uid = $rs['id'];
			$moneyjk = floatval(arrvalue($carr, $uid, 0));
			$moneyhk = floatval(arrvalue($barr, $uid, 0));
			$moneyhx = $moneyjk - $moneyhk;
			
			if($moneyjk==0)$moneyjk='';
			if($moneyhk==0)$moneyhk='';
			if($moneyhx==0)$moneyhx='';
			
			$rows[$k]['moneyjk']	= $moneyjk;
			$rows[$k]['moneyhk']	= $moneyhk;
			$rows[$k]['moneyhx']	= $moneyhx;
		}
		
		return $rows;
	}
}