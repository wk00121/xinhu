<?php
/**
*	获取验证码接口
*/ 
class yanzmClassAction extends apiAction
{
	
	public function initAction()
	{
		$this->display= false;
	}
	
	public function indexAction()
	{
		$mobile = $this->rock->jm->uncrypt($this->post('mobile'));
		if(!c('check')->ismobile($mobile))return returnerror('手机号格式有误');
		
		$device	= $this->post('device');
		if(isempt($device))return returnerror('无效访问');
		
		return c('xinhuapi')->getvercode($mobile, $device);
	}
	
	
}