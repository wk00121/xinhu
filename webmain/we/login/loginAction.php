<?php 
class loginClassAction extends ActionNot{
	
	public function defaultAction()
	{
		//判断是否可以快捷登录
		$iskj 	= $this->weiuser();
		
		if($this->rock->iswebbro(1)){
			$token = $this->option->getval('dingding_token0');
			if(!isempt($token))$iskj=3;
		}
		$this->assign('iskj', $iskj);
		//print_r($_COOKIE);
		//echo $_SERVER['HTTP_USER_AGENT'];
		$ptoken		= $this->get('ptoken');
		$loginyzm	= (int)getconfig('loginyzm','0');
		if(!isempt($ptoken))$loginyzm = 0;
		$this->assign('loginyzm', $loginyzm); //登录类型	
	}
	
	//判断当前使用微信啥的
	public function weiuser()
	{
		$iskj = 0;
		if($this->rock->iswebbro(0)){
			$qycrid	= $this->option->getval('weixinqy_corpid');
			if($this->rock->isqywx){
				if(!isempt($qycrid))$iskj=2;
			}else{
				$coppid = $this->option->getval('weixin_corpid');
				if(!isempt($coppid))$iskj=1;
				if($iskj==0 && !isempt($qycrid))$iskj=2;
			}
		}
		return $iskj;
	}
	
	/**
	*	微信快捷登录
	*/
	public function wxloginAction()
	{
		$this->display= false;
		$iskj 	= $this->weiuser();
		if($iskj==2){
			m('weixinqy:oauth')->login();
		}else{
			m('weixin:oauth')->login();
		}
	}
	
	public function wxlogincodeAction()
	{
		$this->display= false;
		$iskj 	= $this->weiuser();
		if($iskj==2){
			m('weixinqy:oauth')->logincode();
		}else{
			m('weixin:oauth')->logincode();
		}
	}
	
	
	/**
	*	微信授权
	*/
	public function oauthtoAction()
	{
		$this->display= false;
		m('wxgzh:oauth')->oauthto();
	}
	public function oauthbackAction()
	{
		$state	= $this->get('state','bang');
		$ubarr 	= m('wxgzh:oauth')->oauthback();
		if(!is_array($ubarr)){
			$this->assign('backstate', '0');
			$this->assign('backerror', $ubarr);
		}else{
			$this->assign('backstate', '1');
			$this->assign('backarr', $ubarr);
		}
	}
	
	
	
	/**
	*	无登录页面的快捷登录
	*/
	public function qywxloginAction()
	{
		$this->display= false;
		m('weixinqy:oauth')->login('qy');
	}
	public function qywxlogincodeAction()
	{
		m('weixinqy:oauth')->logincode('qy');
	}
}