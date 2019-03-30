<?php
class wxgzh_indexClassModel extends wxgzhModel
{
	public function initWxgzh()
	{
		$this->settable('wotpl');
	}
	
	/**
	*	获取系统上模版消息
	*/
	public function getxinhutpl()
	{
		$barr[] = array(
			'title' => '流程待办',
			'params'=> array(
				'name' 		=> '姓名',
				'applyname' => '申请人',
				'deptname'	=> '申请人部门',
				'sericnum'	=> '单号',
				'modename'	=> '模块名称',
				'summary'	=> '备注摘要'
			),
			'paramsdefault'=> array(
				'name' 		=> '赵子龙',
				'applyname' => '张飞',
				'deptname'	=> '技术部',
				'sericnum'	=> 'TEST-20190326',
				'modename'	=> '请假条',
				'summary'	=> '请假时间2019-03-26的9时到18共8小时'
			),
		);
		$xhtype = getconfig('xinhutype');
		if(!isempt($xhtype)){
			$obj = m($xhtype);
			if(method_exists($obj, 'getwxgzhtpl')){
				$narr = $obj->getwxgzhtpl();
				if(is_array($narr))foreach($narr as $k=>$rs1)$barr[]=$rs1;
			}
		}
		return $barr;
	}
	
	/**
	*	发模版消息
	*/
	private $tplidarr = array();
	public function sendtpl($openid, $tplid, $params=array(),$url='',$istest=false)
	{
		if(isset($this->tplidarr[$tplid])){
			$tpidrs = $this->tplidarr[$tplid];
		}else{
			$where  = "`template_id`='$tplid' or `modename`='$tplid'";
			if(is_numeric($tplid))$where  = "`id`='$tplid'";
			$tpidrs = $this->getone($where);
			$this->tplidarr[$tplid] = $tpidrs;
		}
		if(!$tpidrs)return $this->setbackarr('模版消息['.$tplid.']不存在');
		$modename 	= $tpidrs['modename'];
		$modeparams = $tpidrs['modeparams'];
		$template_id= $tpidrs['template_id'];
		if(isempt($modeparams))return $this->setbackarr('模版消息没有关联OA系统模版');
		
		$modefar = false;
		$dearr = $this->getxinhutpl();
		foreach($dearr as $k1=>$rs1){
			if($rs1['title']==$modename){
				if(!$params && $istest)$params = $rs1['paramsdefault'];
				$modefar = $rs1['params'];
			}
		}
		
		if(!$params)return $this->setbackarr('没有模版参数');
		if(!$modefar)return $this->setbackarr('没有找到OA系统的模块('.$modename.')');
		
		//替换参数
		foreach($modefar as $fid=>$fv){
			$modeparams = str_replace('{'.$fid.'}', arrvalue($params, $fid), $modeparams);
		}

		$body = '{
           "touser":"'.$openid.'",
           "template_id":"'.$template_id.'",
           "url":"'.$url.'",           
           "data":'.$modeparams.'
		}';
		$token 	= $this->gettoken();
		$url 	= $this->gettourl('URL_tplsend').'?access_token='.$token.'';
		$result	= c('curl')->postcurl($url, $body);
		
		if(isempt($result))return $this->setbackarr('无法发送模版消息');
		$barr 	= json_decode($result, true);
		
		return $this->setbackarr($barr['errmsg'],$barr['errcode']);
	}
	
	/**
	*	获取我消息模版列表
	*/
	public function gettpllist()
	{
		$token 	= $this->gettoken();
		$url 	= $this->gettourl('URL_tplgetlist').'?access_token='.$token.'';
		$result	= c('curl')->getcurl($url);
		if(isempt($result))return $this->setbackarr('无法获取');
		$barr = json_decode($result, true);
		if(isset($barr['template_list'])){
			$arrlis= $barr['template_list']; //列表数组
			$this->setbackarr('ok', 0);//成功
			$ids  = '0';
			//写入到表里
			foreach($arrlis as $k=>$rs){
				$tpl = $rs['template_id'];
				$uarr= array(
					'template_id' 	=> $tpl,
					'title' 		=> $rs['title'],
					'primary_industry' => $rs['primary_industry'],
					'deputy_industry' => $rs['deputy_industry'],
					'content' => $rs['content'],
					'example' => $rs['example'],
				);
				$where 	= "`template_id`='$tpl'";
				$id1	= (int)$this->getmou('id',$where);
				if($id1==0)$where='';
				$this->record($uarr, $where);
				if($id1==0)$id1 = $this->db->insert_id();
				$ids.=','.$id1.'';
			}
			$this->delete('`id` not in('.$ids.')');
		}else{
			$this->setbackarr($barr['errmsg'], $barr['errcode']);
		}
		return $this->backarr;
	}
}