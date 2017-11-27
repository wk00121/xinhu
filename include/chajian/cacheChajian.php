<?php 
/**
*	缓存，目前是使用磁盘文件存储
*/
class cacheChajian extends Chajian{
	
	private $dirvie = 'file'; //redis,file
	
	/**
	*	设置缓存
	* 	$time 缓存时间(秒)
	*/
	public function set($key, $data, $time=0)
	{
		$this->del($key); //删除原来的
		$sarr['key']  = $this->getkey($key);
		$sarr['data'] = $data;
		if($time>0){
			$time = time()+$time;
		}else{
			$time = 0;
		}
		$sarr['time'] = $time;
		if($time>0)$sarr['timedt'] = date('Y-m-d H:i:s', $time);
		$sarr['url']  = $this->rock->nowurl();
		return $this->rock->createtxt($this->getpath($key, $time), json_encode($sarr));
	}
	
	private function getkey($key)
	{
		return ''.QOM.''.$key.'';
	}
	
	private function getpath($key, $time=0)
	{
		$key = $this->getkey($key);
		$ske = '';
		if($time>0)$ske='_'.$time.'';
		return ''.UPDIR.'/cache/'.md5($key).''.$ske.'';
	}
	
	//获取文件名
	private function getpaths($key)
	{
		$key = $this->getkey($key);
		$file= ''.UPDIR.'/cache/'.md5($key).'';
		$bar = glob(''.$file.'*');
		foreach($bar as $k=>$fil1){
			if($k==0){
				$file = $fil1;
			}else{
				unlink($fil1);
			}
		}
		return $file;
	}
	
	/**
	*	获取缓存
	*/
	public function get($key, $dev='')
	{
		$file= $this->getpaths($key);
		$data= $dev;
		if(file_exists($file)){
			$filea= explode('_', $file);
			$time = (int)arrvalue($filea, count($filea)-1,'0');
			if($time==0 || $time>=time()){
				$cont = file_get_contents($file);
				if(!isempt($cont)){
					$sarr = json_decode($cont, true);
					$data = arrvalue($sarr, 'data');
				}
			}else{
				unlink($file); //已经过期了
			}
		}
		return $data;
	}
	
	/**
	*	删除缓存
	*/
	public function del($key)
	{
		$file= $this->getpaths($key);
		if(file_exists($file))@unlink($file);
		return true;
	}
	
	/**
	*	删除所有缓存
	*/
	public function delall()
	{
		$bar = glob(''.UPDIR.'/cache/*');
		foreach($bar as $k=>$fil1){
			unlink($fil1);
		}
	}
}                               