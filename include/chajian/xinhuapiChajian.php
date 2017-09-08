<?php 
/**
*	连接官网API，短信发送
*/

class xinhuapiChajian extends Chajian{
	
	private $updatekey 	= '';
	private $updatekeys = '';
	private $systemnum  = '';
	
	
	protected function initChajian()
	{
		if(getconfig('systype')=='dev'){
			$this->updatekeys  = 'aHR0cDovLzEyNy4wLjAuMS9hcHAvcm9ja2FwaS8:';
		}else{
			$this->updatekeys  = 'aHR0cDovL2FwaS5yb2Nrb2EuY29tLw::';
		}
		$this->updatekey	=$this->rock->jm->base64decode($this->updatekeys);
	}
	
	private function getsysnum()
	{
		$dbs 	= m('option');
		$this->systemnum  = $dbs->getval('systemnum');
		if(isempt($this->systemnum)){
			$rnd  	= md5(str_shuffle('abcedfghijk').rand(1000,9999));
			$dbs->setval('systemnum', $rnd);
			$this->systemnum = $rnd;
		}
		return $this->systemnum;
	}
	
	
	public function geturlstr($mod, $act, $can=array())
	{
		$url 	= $this->updatekey;
		$sysnum = $this->getsysnum();
		$url.= '?m='.$mod.'&a='.$act.'';
		$url.= '&host='.$this->rock->jm->base64encode(HOST).'&version='.VERSION.'&randtime='.time().'&ip='.$this->rock->ip.'&xinhukey='.getconfig('xinhukey').'&sysnum='.$sysnum.'';
		foreach($can as $k=>$v)$url.='&'.$k.'='.$v.'';
		return $url;
	}
	
	/**
	*	get获取数据
	*/
	public function getdata($mod, $act, $can=array())
	{
		$url 	= $this->geturlstr($mod, $act, $can);
		$cont 	= c('curl')->getcurl($url);
		if(!isempt($cont) && contain($cont, 'success')){
			$data  	= json_decode($cont, true);
		}else{
			$data 	= returnerror('无法访问到信呼官网的'.$this->updatekey.','.$cont.'');
		}
		return $data;
	}
	
	/**
	*	post发送数据
	*/
	public function postdata($mod, $act, $can=array())
	{
		$url 	= $this->geturlstr($mod, $act);
		$cont 	= c('curl')->postcurl($url, $can);
		if(!isempt($cont) && contain($cont, 'success')){
			$data  	= json_decode($cont, true);
		}else{
			$data 	= returnerror('无法访问到信呼官网的'.$this->updatekey.','.$cont.'');
		}
		return $data;
	}
	
	/**
	*	发送短信
	*	$tomobile 手机号
	*	$qiannum 短信签名编号
	*	$tplnum 短信模版编号
	*	$params 模版上的参数
	*	$url 详情URL
	*	$addlog 是否添加日志
	*/
	public function send($tomobile,$qiannum, $tplnum, $params=array(), $url='', $addlog=true)
	{
		$para['sys_tomobile'] = $tomobile;
		$para['sys_tplnum']   = $tplnum;
		
		$para['sys_url']   	  = $this->rock->jm->base64encode($url); //详情的URL
		foreach($params as $k=>$v)$para['can_'.$k.''] = $v;
		
		$barr 	= $this->postdata('sms','send', $para);
		if(!$barr['success'] && $addlog)m('log')->addlogs('短信', $barr['msg'],2);
		return $barr;
	}
	
	
	/**
	*	根据用户ID获取手机号，发送短信
	*/
	public function sendsms($receid, $qiannum, $tplnum, $params=array(), $url='', $addlog=true, $isyb=true)
	{
		if(isempt($receid))return returnerror('没有接收人');
		$tomobile = m('admin')->getjoinfields($receid, 'mobile');
		
		if(isempt($tomobile))return returnerror('接收人['.$receid.']可能没设置手机号');
		
		//异步发送
		if(getconfig('asynsend') && $isyb){
			$ybbo = m('reim')->asynurl('asynrun','sendsms', array(
				'tomobile' 		=> $tomobile,
				'qiannum' 		=> $qiannum,
				'tplnum' 		=> $tplnum,
				'url' 			=> $this->rock->jm->base64encode($url),
				'params' 		=> $this->rock->jm->base64encode(json_encode($params)),
			));
			if($ybbo)return returnsuccess('异步发送');
		}
		
		return $this->send($tomobile, $qiannum, $tplnum, $params, $url, $addlog);
	}
	
	/**
	*	添加异步
	*/
	public function sendanay($m, $a,$can=array(), $runtime=0)
	{
		$runurl = m('base')->getasynurl($m, $a,$can, 1);
		$barr 	= $this->sendanayurl($runurl, $runtime);
		return $barr;
	}
	
	public function sendanayurl($runurl, $runtime=0)
	{
		if(isempt($runurl))return returnerror('异步调用地址不能为空');
		$para	= array(
			'runurl' => $this->rock->jm->base64encode($runurl),
			'runtime' => $runtime,
		);
		$barr 	= $this->postdata('anay','send', $para);
		
		if(!$barr['success'])m('log')->addlogs('调用官网异步', $barr['msg'],2);
		
		return $barr;
	}
}