<?php
class wxgzh_oauthClassModel extends wxgzhModel
{
	
	public function initWxgzh()
	{
		$this->settable('wouser');
	}
	
	/**
	*	调整到获取
	*/
	public function oauthto()
	{
		$this->readwxset();
		if($this->appid==''){
			$url = '?d=we#notappid';
			$this->rock->location($url);
			return false;
		}
		$state			= $this->rock->get('state','bang');
		$redurl			= ''.URL.'?d=we&a=oauthback&m=login';
		$redirect_uri	= urlencode($redurl);
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state='.$state.'#wechat_redirect';
		$this->rock->location($url);
		return true;
	}
	
	/**
	*	得到openid获取用户信息
	*/
	public function oauthback()
	{
		$code	= $this->rock->get('code');
		$state	= $this->rock->get('state');
		if($code=='')return;
		$this->readwxset();
		$url 	= 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->secret.'&code='.$code.'&grant_type=authorization_code';
		$result = c('curl')->getcurl($url);
		$openid = '';
		$access_token = '';
		if($result != ''){
			$arr	= json_decode($result);
			if(isset($arr->openid))$openid = $arr->openid;
			if(isset($arr->access_token))$access_token = $arr->access_token;
		}
		
		$errmsg 	= '无法获取用户openid';
		if($openid != ''){
		//拉取用户信息
		$this->rock->savecookie('wxopenid', $openid);
		$gurl  	= 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		$result = c('curl')->getcurl($gurl);
		if($result != ''){
			$arr	= json_decode($result, true);
			if(isset($arr['errcode']) && $arr['errcode']!=0){
				$errmsg	= $arr['errmsg'];
			}else{
				$uoid 				= (int)$this->getmou('id', "`openid`='$openid'");
				$where 				= 'id='.$uoid.'';
				$uarr['uid'] 		= $this->adminid;
				$uarr['openid'] 	= $arr['openid'];
				$uarr['nickname'] 	= $arr['nickname'];
				$uarr['sex'] 		= $arr['sex'];
				$uarr['province'] 	= $arr['province'];
				$uarr['city'] 		= $arr['city'];
				$uarr['country'] 	= $arr['country'];
				$uarr['headimgurl'] = $arr['headimgurl'];
				$uarr['optdt'] 		= $this->rock->now;
				$uarr['ip'] 		= $this->rock->ip;
				if($uoid==0){
					$uarr['adddt'] 	= $this->rock->now;
					$where			= '';
				}
				$bo 	= $this->record($uarr, $where);
				$errmsg	= '已成功绑定微信['.$uarr['nickname'].']';
				if(!$bo)$errmsg = $this->db->error();
			}
		}else{
			$errmsg	= '无法获取用户信息';
		}}
		$url 		= '?d=we&errmsg='.$this->rock->jm->base64encode($errmsg).'';
		$this->rock->location($url);
	}
	
	/**
	*	读取当前绑定微信用户信息
	*/
	public function getbdwx($uid)
	{
		$rs = $this->getone('`uid`='.$uid.'','`openid`,`nickname`,`headimgurl`');
		if(!$rs)$rs['nickname'] = '';
		return $rs;
	}
}