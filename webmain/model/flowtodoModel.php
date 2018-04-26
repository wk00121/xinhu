<?php
/**
*	流程通知
*/
class flowtodoClassModel extends Model
{

	public function initModel()
	{
		$this->settable('flow_todos');
	}
	
	/**
	*	添加人员提醒表里，让他必读查看
	*/
	public function addtotouids($uids, $uarr=array())
	{
		if(isempt($uids) || !$uarr)return;
		$mid		= arrvalue($uarr,'mid');
		$modenum	= arrvalue($uarr,'modenum');
		$uidsa 		= explode(',',$uids);
		$isuar		= array();
		$uarrs 		= $this->getrows("`modenum`='$modenum' and `uid` in($uids)", '`uid`,`id`');
		foreach($uarrs as $k=>$rs)$isuar[$rs['uid']]=$rs['id'];
		foreach($uidsa as $uid){
			$where = '';
			if(isset($isuar[$uid])){
				$where = $isuar[$uid];
			}
			$adda['adddt'] 	= $this->rock->now;
			$adda['uid'] 	= $uid;
			$adda['readdt'] = null;
			$adda['isread'] = 0;
			foreach($uarr as $k=>$v)$adda[$k] = $v;
			$this->record($adda, $where);
		}
	}
	
	/**
	*	标识已读
	*/
	public function biaoyidu($uid, $mode, $mid)
	{
		$where 	= "`modenum`='$mode' and `uid`='$uid' and `mid`='$mid'";
		$this->update(array(
			'isread'    => 1,
			'readdt'	=> $this->rock->now
		),"$where and `isread`=0");
		m('todo')->update(array(
			'status'    => 1,
			'readdt'	=> $this->rock->now
		), "$where and `status`=0");
	}
	
	public function getwdtotals($uid)
	{
		$to = $this->rows("`uid`='$uid' and `isread`=0");
		return $to;
	}
	
}