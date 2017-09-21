<?php
/**
*	添加方法日志，和IP限制判断
*/
function ipwhiteshow($ip, $rock){
	$iplist = ''.ROOT_PATH.'/config/iplist.php';
	$bool 	= 0;
	if(file_exists($iplist)){
		$iparr 	= require($iplist);
	}else{
		$iparr 	= array(
			'blackip' 	=> '',
			'whiteip'	=> '' 
		);
	}

	//白名单判断
	$whiteip = $iparr['whiteip'];
	if($whiteip!=''){
		$whiteipa = explode(',', $whiteip);
		foreach($whiteipa as $ips){
			$bo = strpos($ip, $ips);
			if($bo===0 || $ips=='*'){
				$bool = 1; //可以访问
				break;
			}
		}
	}
	
	//黑名单判断
	if($bool==0){
		$blackip = $iparr['blackip'];
		if($blackip!=''){
			$blackipa = explode(',', $blackip);
			foreach($blackipa as $ips){
				$bo = strpos($ip, $ips);
				if($bo===0 || $ips=='*'){
					$bool = 2;//不能访问
					break;
				}
			}
		}
	}

	$str = '';
	foreach($_SERVER as $k=>$v)$str.='['.$k.']：'.$v.chr(10).'';
	
	$str1 = '';
	foreach($_GET as $k=>$v)$str1.='['.$k.']：'.$v.chr(10).'';
	
	$str2 = '';
	foreach($_POST as $k=>$v)$str2.='['.$k.']：'.$v.chr(10).'';
	$act  = arrvalue($_SERVER,'REQUEST_METHOD');
	if($act=='POST' && $str2==''){
		$str2 = arrvalue($GLOBALS,'HTTP_RAW_POST_DATA');
	}
	
	//创建访问日志
	$logs = ''.UPDIR.'/logs/'.date('Y-m-d').'/'.date('H').'/'.date('H.i.s').'_'.$act.'_'.$ip.'_'.rand(100,999).'.log';
	$logstr = '【datetime】：'.$rock->now.'
【URL】：'.$rock->nowurl().'	
【ACTION】：'.$act.'
【IP】：'.$ip.'
【GET】
'.$str1.'
【POST】
'.$str2.'
【SERVER】
'.$str.'	
';
	if(DEBUG)$rock->createtxt($logs, $logstr);
	
	
	if($bool==2)exit('您IP['.$ip.']禁止访问我们站点，可联系我们admin@rockoa.com');
}

function ipwhiteshows($ips, $rock){
	$ipa = explode(',', $ips); 
	foreach($ipa as $ip)ipwhiteshow($ip, $rock);
}
ipwhiteshows($rock->ip, $rock);