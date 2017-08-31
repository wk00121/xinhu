<?php
/**
*	对外开发接口文件
*	createname：信呼
*	homeurl：http://www.rockoa.com/
*	Copyright (c) 2016 rainrock (www.rockoa.com)
*	Date:2016-11-01
*	explain：返回200为正常
*	post需开启：always_populate_raw_post_data = On
*/
class openapiAction extends ActionNot
{
	private $openkey = '';
	public 	$postdata= '';
	
	public function initAction()
	{
		$this->display= false;
		$openkey 		= $this->post('openkey');
		$this->openkey 	= getconfig('openkey');
		if(HOST != '127.0.0.1' && $this->openkey != ''){
			if($openkey != md5($this->openkey))$this->showreturn('', 'openkey not access', 201);
		}
		if(isset($GLOBALS['HTTP_RAW_POST_DATA']))$this->postdata = $GLOBALS['HTTP_RAW_POST_DATA'];
	}
	
	public function getvals($nae, $dev='')
	{
		$sv = $this->rock->jm->base64decode($this->post($nae));
		if($this->isempt($sv))$sv=$dev;
		return $sv;
	}
}