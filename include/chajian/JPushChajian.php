<?php 
/**
* 最新系统推送1.9.0后
*/
class JPushChajian extends Chajian{
	
	//小米推送，注意：
	//1、你自己编译的app，没有改包名就不要设置以下2个
	//2、你自己编译的app，有改了包名就要去设置以下2个
	private $xiaomi_secret 		= '';
	private $xiaomi_package 	= '';

	
	protected function initChajian()
	{
	}

	
	//-------------最新原生app推送app是1.2.3版本+---------------
	public function push($title, $desc, $cont, $palias)
	{
		
		$alias		= $palias['alias'];
		$xmalias	= $palias['xmalias']; //小米的
		$newalias	= $palias['newalias']; //最新使用的
		$oldalias	= $palias['oldalias']; //一般自己编译
		$uids		= $palias['uids'];
		
		//使用官网来推送
		if($this->xiaomi_package=='' || $this->xiaomi_secret==''){
			$arr 	= array(
				'alias' 	=> join(',', $alias),
				'xmalias' 	=> join(',', $xmalias),
				'newalias' 	=> join(',', $newalias),
				'oldalias' 	=> join(',', $oldalias),
				'uids'  => $uids,
				'title' => $this->rock->jm->base64encode($title),
				'cont'  => $this->rock->jm->base64encode($cont),
				'desc'  => $desc
			);
			$runurl = c('xinhu')->geturlstr('jpushplat', $arr);
			return  c('curl')->getcurl($runurl);
		}else{
			$barr = c('xiaomiPush')->push($this->xiaomi_secret, $this->xiaomi_package, $oldalias,  $title, $this->rock->jm->base64decode($desc), $cont);
			$this->rock->debugs(json_encode($barr), 'xiaomipush');
			return $barr;
		}	
	}
	
}