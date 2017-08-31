<?php 
class yingClassAction extends ActionNot{
	
	public function initAction()
	{
		$this->mweblogin(0, true);
	}
	
	public function defaultAction()
	{
		$num = $this->get('num');
		$arr = m('reim')->getagent(0, "and `num`='$num'");
		if(!$arr)exit('sorry not found agent['.$num.']!');
		$rs  = $arr[0];
		$this->title = $rs['name'];
		$yyurl 	= ''.P.'/we/ying/yingyong/'.$num.'.html';
		if(!file_exists($yyurl))$yyurl='';
		$yyurljs 	= ''.P.'/we/ying/yingyong/'.$num.'.js';
		if(!file_exists($yyurljs))$yyurljs='';
		$this->assign('arr', $rs);
		$this->assign('openfrom', $this->get('openfrom'));
		$this->assign('yyurl', $yyurl);
		$this->assign('yyurljs', $yyurljs);
	}
	
	public function locationAction()
	{
		$this->title = '考勤定位';
		$kq 	= m('kaoqin');
		$arr 	= m('waichu')->getoutrows($this->date,$this->adminid);
		$this->assign('rows', $arr);
		$dt 	= $this->rock->date;
		$dwarr	= m('location')->getrows("uid='$this->adminid' and `optdt` like '$dt%'",'*','`id` desc');
		$this->assign('dwarr', $dwarr);
		$kqrs 	= $kq->dwdkrs($this->adminid, $this->date);
		$isgzh	= m('wxgzh:index')->isusegzh();
		$this->assign('isgzh', $isgzh);
		$this->assign('kqrs', $kqrs);
		$dwids	= arrvalue($kqrs, 'dwids');
		$kqors	= array();
		if(!isempt($dwids)){
			$kqors = m('kqdw')->getrows("id in($dwids) and `id`<>".$kqrs['id']."");
		}
		$this->assign('kqors', $kqors);
		
		//$sbarr 	= $kq->getsbanay($this->adminid, $this->rock->date);
		//$this->assign('sbarr', $sbarr);
	}
	
	public function locationsAction()
	{
		$this->title = '考勤定位';
		$kq 	= m('kaoqin');
		$arr 	= m('waichu')->getoutrows($this->date,$this->adminid);
		$this->assign('rows', $arr);
		$dt 	= $this->rock->date;
		$dwarr	= m('location')->getrows("uid='$this->adminid' and `optdt` like '$dt%'",'*','`id` desc');
		$this->assign('dwarr', $dwarr);
		$kqrs 	= $kq->dwdkrs($this->adminid, $this->date);
		$isgzh	= m('wxgzh:index')->isusegzh();
		$this->assign('isgzh', $isgzh);
		$this->assign('kqrs', $kqrs);
		$dwids	= arrvalue($kqrs, 'dwids');
		$kqors	= array();
		if(!isempt($dwids)){
			$kqors = m('kqdw')->getrows("id in($dwids) and `id`<>".$kqrs['id']."");
		}
		$this->assign('kqors', $kqors);
		
		$sbarr 	= $kq->getsbanay($this->adminid, $this->rock->date);
		$this->assign('sbarr', $sbarr);
	}
}