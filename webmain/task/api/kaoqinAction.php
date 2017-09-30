<?php 
class kaoqinClassAction extends apiAction
{
	public function adddkjlAction()
	{
		$mac 	= $this->post('mac');
		$ip 	= $this->post('ip');
		$msg 	= m('kaoqin')->adddkjl($this->adminid,0,'',$ip,$mac);
		if($msg!='')$this->showreturn('', $msg, 201);
		$this->showreturn($this->now);
	}
	
	public function getshangAction()
	{
		$kq 	= m('kaoqin');
		$dt		= $this->rock->date;
		$sbarr 	= $kq->getsbanay($this->adminid, $dt);
		$dkarr 	= $kq->getdkjl($this->adminid, $dt);
		$barr['sbarr'] = $sbarr;
		$barr['dkarr'] = $dkarr;
		
		$this->showreturn($barr);
	}
}