<?php 
class loginClassAction extends apiAction
{
	public function checkAction()
	{
		$adminuser	= str_replace(' ','',$this->rock->jm->base64decode($this->post('user')));
		$adminpass	= $this->rock->jm->base64decode($this->post('pass'));
		$arr 		= m('login')->start($adminuser, $adminpass);
		if(is_array($arr)){
			$arrs = array(
				'uid' 	=> $arr['uid'],
				'name' 	=> $arr['name'],
				'user'	=> $arr['user'],
				'ranking'	=> $arr['ranking'],
				'deptname'  => $arr['deptname'],
				'deptallname' => $arr['deptallname'],
				'face'  	=> $arr['face'],
				'apptx'  	=> $arr['apptx'],
				'token'  	=> $arr['token'],
				'iskq'  	=> (int)m('userinfo')->getmou('iskq', $arr['uid']), //判断是否需要考勤
				'title'		=> getconfig('apptitle'),
				'weblogo'	=> getconfig('weblogo')
			);
			
			$uid 	= $arr['uid'];
			$name 	= $arr['name'];
			$user 	= $arr['user'];
			$token 	= $arr['token'];
			m('login')->setsession($uid, $name, $token, $user);
			$this->showreturn($arrs);
		}else{
			$this->showreturn('', $arr, 201);
		}
	}
	
	public function loginexitAction()
	{
		m('login')->exitlogin();
		$this->showreturn('');
	}
	
	/**
	*	下载图片
	*/
	public function downimgAction()
	{
		$paths= $this->getvals('path');
		$path = str_replace(URL, '', $paths);
		$obj  = c('upfile');
		$str  = '';
		$ext  = $obj->getext($path);
		if($obj->isimg($ext) && file_exists($path)){
			$str = base64_encode(file_get_contents($path));
		}
		$this->showreturn(array(
			'result' => $str,
			'path'	 => $paths
		));
	}
	
	/**
	*	读取可上传最大M
	*/
	public function getmaxupAction()
	{
		$maxup = c('upfile')->getmaxzhao();
		$this->showreturn(array(
			'maxup' => $maxup
		));
	}
	
	/**
	*	钉钉jssdk签名
	*/
	public function ddsignAction()
	{
		$bo		= m('dingding:signjssdk');
		$corpId	= $bo->readwxset();
		$agentid= $this->post('agentid');
		if(isempt($agentid))$agentid = $this->rock->session('wxqyagentid');
		if(isempt($corpId) || isempt($agentid)){
			$arr['corpId'] = '';
		}else{
			$url = $this->getvals('url');
			$arr = $bo->getsignsdk($url);
			$arr['agentId'] = $agentid;
		}
		$this->showreturn($arr);
	}
	
	/**
	*	钉钉获取登录
	*/
	public function dingcheckAction()
	{
		$code = $this->post('code');
		$barr = m('dingding:user')->getuserjssdk($code);
		if($barr['errcode']!=0){
			$this->showreturn('', $barr['msg'], 201);
		}else{
			$this->showreturn($barr);
		}
	}
	
	//初始化验证
	public function initsetAction()
	{
		$barr['title'] 	= getconfig('reimtitle','REIM');
		$this->showreturn($barr);
	}
}