<?php
class emailClassAction extends Action
{
	public function setsaveAjax()
	{
		$this->option->setval('email_sendhost@-1', $this->post('sendhost'));
		$this->option->setval('email_sendport@-1', $this->post('sendport'));
		$this->option->setval('email_recehost@-1', $this->post('recehost'));
		$this->option->setval('email_sendsecure@-1', $this->post('sendsecure'));
		$this->option->setval('email_sysname@-1', $this->post('sysname'));
		$this->option->setval('email_sysuser@-1', $this->post('sysuser'));
		$this->option->setval('email_receyumi@-1', $this->post('receyumi'));
		$syspass	= $this->post('syspass');
		if(!isempt($syspass)){
			$this->option->setval('email_syspass@-1', $this->jm->encrypt($syspass));
		}
		$this->backmsg();
	}
	
	public function getsetAjax()
	{
		$arr= array();
		$arr['sendhost']	= $this->option->getval('email_sendhost');
		$arr['sendport']	= $this->option->getval('email_sendport');
		$arr['recehost']	= $this->option->getval('email_recehost');
		$arr['sendsecure']	= $this->option->getval('email_sendsecure');
		$arr['sysname']		= $this->option->getval('email_sysname');
		$arr['sysuser']		= $this->option->getval('email_sysuser');
		$arr['receyumi']	= $this->option->getval('email_receyumi');
		echo json_encode($arr);
	}
	
	public function savebeforecog($table, $cans)
	{
		$emailpass	= $this->post('emailpass');
		if(!isempt($emailpass)){
			$cans['emailpass'] = $this->jm->encrypt($emailpass);
		}
		return array(
			'rows' => $cans
		);
	}
	
	public function coguserbeforeshow($table)
	{
		$fields = '`id`,`name`,`user`,`deptallname`,`status`,`ranking`,`email`,`sort`,`face`';
		if(getconfig('systype')!='demo')$fields.=',`emailpass`';
		$s 		= '';
		$key 	= $this->post('key');
		if($key!=''){
			$s = m('admin')->getkeywhere($key);
		}
		return array(
			'fields'=> $fields,
			'where'	=> $s,
			'order'	=> '`sort`'
		);
	}
	
	public function testsendAjax()
	{
		$msg 	= m('email')->sendmail_test();
		echo $msg;
	}
	
	public function emailtotals($table, $rows)
	{
		return array(
			'rows' => $rows,
			'email'=> m('admin')->getone($this->adminid, 'email,emailpass'),
			'total'=> m('emailm')->zongtotal($this->adminid)
		);
	}
	
	//收信
	public function recemailAjax()
	{
		$barr 	= m('emailm')->receemail($this->adminid);
		if(is_array($barr)){
			$this->showreturn($barr['count']);
		}else{
			$this->showreturn('', $barr, 201);
		}
	}
	
	//标已读
	public function biaoydAjax()
	{
		$sid = $this->post('sid');
		m('emailm')->biaoyd($this->adminid, $sid);
		echo '成功标识';
	}
		
	/**
	*	删除邮件
	*/
	public function delyjAjax()
	{
		$sid 	= $this->post('sid');
		$atype 	= $this->post('atype');
		$uid 	= $this->adminid;
		//收件箱删除
		if($atype==''){
			m('emails')->update('isdel=1','`uid`='.$uid.' and `mid` in('.$sid.') and `type` in(0,1)');
		}
		//草稿箱删除
		if($atype=='cgx'){
			m('emailm')->delete('`id` in('.$sid.') and `sendid`='.$uid.' and `isturn`=0');
		}
		//已发送删除
		if($atype=='yfs'){
			m('emails')->update('isdel=1','`uid`='.$uid.' and `mid` in('.$sid.') and `type`=2');
		}
		//已删除删除
		if($atype=='ysc'){
			m('emails')->delete('`uid`='.$uid.' and `mid` in('.$sid.') and `isdel`=1 and `type` in(0,1)');
		}
		echo '删除成功';
	}
	
	//用户修改自己邮箱密码
	public function saveemaipassAjax()
	{
		$pass = $this->post('emailpass');
		if(getconfig('systype')!='demo')m('admin')->update("`emailpass`='$pass'", '`id`='.$this->adminid.'');
		$this->backmsg('','修改成功');
	}
}