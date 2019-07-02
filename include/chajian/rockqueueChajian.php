<?php
/**
*	信呼中node队列的处理
*/

class rockqueueChajian extends Chajian
{
	//队列服务器主机
	private $rockqueue_host = '127.0.0.1';
	//private $rockqueue_host = '192.168.130.132';
	
	//队列服务端口，数字类型
	private $rockqueue_port = 7898;	
	
	
	//初始化配置读取
	protected function initChajian()
	{
		$this->rockqueue_host = getconfig('rockqueue_host', $this->rockqueue_host);
		$this->rockqueue_port = getconfig('rockqueue_port', $this->rockqueue_port);
	}
	
	/**
	*	发送队列信息
	*	$cont 内容可以是http地址，也可以如:cli,run
	*	$param 参数
	*	使用 c('rockqueue')->push('cli,run');
	*/
	public function push($cont, $param=array(), $runtime=0)
	{
		$type 	= 'cmd';
		$url	= $cont;
		if(substr($cont,0,4)=='http'){
			$type='url';
			$jg  = contain($url,'?')?'&':'?';
			$st1 = '';
			foreach($param as $k=>$v)$st1.='&'.$k.'='.$v.'';
			if($st1!='')$url.=''.$jg.''.substr($st1,1).'';
		}else{
			$st1 = '';
			if(!contain($url, ','))$url='cli,'.$url.'';
			foreach($param as $k=>$v)$st1.=' -'.$k.'='.$v.'';
			$url = 'php '.ROOT_PATH.'/task.php '.$url.''.$st1.'';
		}
		$rarr[] = array(
			'type'		=> $type,
			'runtime'	=> $runtime,
			'url'		=> $url,
			'id'		=> rand(1,99999)
		);
		c('socket')->udppush(json_encode($rarr), $this->rockqueue_host, $this->rockqueue_port);
		return returnsuccess();
	}
	
	/**
	*	发送文件到信呼文件管理平台上
	*	调用：c('rockqueue')->sendfile(文件Id);
	*/
	public function sendfile($fileid, $runtime=0)
	{
		return $this->push('uptoxinhudoc,run', array('fileid'=>$fileid), $runtime);
	}
	
	/**
	*	在信呼文件管理平台上删除对应文件
	*	调用：c('rockqueue')->delfile(文件编号);
	*/
	public function delfile($filenum)
	{
		return $this->push('uptoxinhudoc,del', array('filenum'=>$filenum));
	}
}