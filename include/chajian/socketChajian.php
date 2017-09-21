<?php
class socketChajian extends Chajian
{
	//UDP服务器主机，不需要修改
	private $serverhost = '127.0.0.1';
	
	//UDP服务端口，数字类型
	private $serverport = 780;			
	
	/**
	*	UDP发送文本
	*/
	public function udpsend($str)
	{
		if(!function_exists('socket_create'))return '没有开启Socket组件';
		$sock 	= socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		$len 	= strlen($str);
		$bo 	= socket_sendto($sock, $str, $len, 0, $this->serverhost, $this->serverport);
		socket_close($sock);
		return $bo;
	}
	
	/**
	*	转pdf发送命令
	*/
	public function topdf($path, $fid, $type)
	{
		$flx	= 'doc';
		if($type=='xls' || $type=='xlsx')$flx='xls';
		if($type=='ppt' || $type=='pptx')$flx='ppt';
		$topah 	= ''.ROOT_PATH.'/mode/pdfjs/topdf/'.$flx.'.js';
		if(!file_exists($topah))return '没有安装转化插件';
		$url  	= m('base')->getasynurl('asynrun', 'topdfok', array('id'=>$fid));;
		$path 	= ''.ROOT_PATH.'/mode/pdfjs/topdf/start.bat "'.$topah.'" "'.ROOT_PATH.'/'.$path.'" "'.$url.'"';
		$bo 	= $this->udpsend($path);
		return $bo;
	}
	
	/**
	*	用udp推送
	*/
	public function udppush($str, $host='', $port=0)
	{
		$msg 	= '';
		if(!function_exists('socket_create'))$msg= '没有开启Socket组件';
		if($msg==''){
			$sock 	= socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
			$len 	= strlen($str);
			socket_sendto($sock, $str, $len, 0, $host, $port);
			$bstr 	= @socket_read($sock, 8192);
			socket_close($sock);
			if(isempt($bstr))$msg = 'error';
		}
		if($msg!='')$bstr = '{"code":2,"msg":"'.$msg.'"}';
		return json_decode($bstr, true);
	}
}