<?php
class xinhuClassAction extends Action
{

	
	public function setsaveAjax()
	{
		$this->option->setval('reimhostsystem', $this->post('host'));
		$this->option->setval('reimrecidsystem', $this->post('receid'));
		$this->option->setval('reimpushurlsystem', $this->post('push'));
		$this->option->setval('reimchehuisystem', $this->post('chehui'));
		$this->backmsg();
	}
	
	public function getsetAjax()
	{
		$arr= array();
		$arr['reimhost']= $this->option->getval('reimhostsystem');
		$arr['reimrecid']= $this->option->getval('reimrecidsystem');
		$arr['reimpushurl']= $this->option->getval('reimpushurlsystem');
		$arr['reimchehui']= $this->option->getval('reimchehuisystem');
		echo json_encode($arr);
	}
	
	public function testsendAjax()
	{
		$barr  = m('reim')->sendpush($this->adminid, $this->adminid,array(
			'cont' 	=> $this->jm->base64encode('测试内容:'.$this->now.''),
			'type' 	=> 'user',
			'optdt' => $this->now,
			'messid' => 0
		));
		$msg 	= '';
		if($barr['code']==0){
			$msg='服务端推送地址可以使用';
		}else{
			$msg='<font color=red>服务端推送地址不能使用：'.$barr['msg'].'</font>';
		}
		echo $msg;
	}
	
	public function testsendsAjax()
	{
		$obj = m('reim');
		$url = $obj->serverhosturl;
		$msg = '';
		if(isempt($url))$msg = '请先设置地址后保存在测试';
		if($msg==''){
			$url = str_replace('ws:','http:', $url);
			$cont = c('curl')->getcurl($url);
			if(!contain($cont,'400 Bad Request'))$msg = '通信地址不能用，请看当前页面提示';
		}
		if($msg=='')$msg = '通信地址可以用';
		echo $msg;
	}
}