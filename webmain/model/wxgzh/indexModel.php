<?php
class wxgzh_indexClassModel extends wxgzhModel
{
	/**
	*	发模版消息
	*/
	public function sendtpl($toid=0)
	{
		$body = '{
           "touser":"oE1rOs7yA-Jrq9ErW5fl5RRx2kCI",
           "template_id":"'.$this->tplid.'",
           "url":"http://weixin.qq.com/download",           
           "data":{
                   "first": {
                       "value":"恭喜你购买成功！",
                       "color":"#173177"
                   },
                   "modename":{
                       "value":"巧克力",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"请尽快处理查看！",
                       "color":"#173177"
                   }
           }
       }';
	   $token 	= $this->gettoken();
	   $url 	= $this->gettourl('URL_tplsend').'?access_token='.$token.'';
	   $result	= c('curl')->postcurl($url, $body);
	   
	   echo $result;
	}
}