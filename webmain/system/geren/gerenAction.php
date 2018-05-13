<?php
class gerenClassAction extends Action
{
	public function getinitAjax()
	{
		$uid = $this->adminid;
		
		return array(
			'gerentodo' => $this->option->getval('gerennotodo_'.$uid.''),
			'qmimgstr' 	=> $this->option->getval('qmimgstr_'.$uid.'')
		);
	}
	
	public function cogsaveAjax()
	{
		$uid = $this->adminid;
		$this->option->setval('gerennotodo_'.$uid.'', $this->get('gerentodo','0'));
	}
	
	//保存图片
	public function qmimgsaveAjax()
	{
		$uid = $this->adminid;
		$str = '';
		$qmimgstr = $this->post('qmimgstr');
		if(!isempt($qmimgstr)){
			if(contain($qmimgstr,'.')){
				$str = $qmimgstr;
			}else{
				$qma = explode(',', $qmimgstr);
				$str = ''.UPDIR.'/'.date('Y-m').'/'.$uid.'qming_'.rand(1000,9999).'.png';
				$this->rock->createtxt($str, base64_decode($qma[1]));
			}
		}
		$this->option->setval('qmimgstr_'.$uid.'', $str);
	}
	
	public function filebefore($table)
	{
		$key	= $this->post('key');
		$atype	= $this->post('atype');
		$where	 = 'and optid='.$this->adminid.'';
		if($atype=='all'){
			$where='';
		}
		if($key!=''){
			$where.=" and (`optname` like '%$key%' or `filename` like '%$key%' or `mtype`='$key')";
		}
		return array(
			'where' => $where,
			'fields' => '`id`,fileext,filename,filesizecn,thumbpath,filepath,adddt,optname,downci,ip,web,mtype,mid',
		);
	}
	public function fileafter($table, $rows)
	{
		foreach($rows as $k=>&$rs){
			if(!isempt($rs['thumbpath'])){
				if(!file_exists($rs['thumbpath']))$rs['thumbpath']='';
			}
		}
		return array(
			'rows' => $rows
		);
	}
	
	public function delfileAjax()
	{
		$id = $this->post('id','0');
		m('file')->delfile($id);
		backmsg();
	}
	
	public function defaultAction()
	{
		$this->title	= '修改头像';
		$face			= $this->db->getmou($this->T('admin'),'face',"`id`='$this->adminid'");
		$imgurl = '';	
		if(!$this->rock->isempt($face)){
			$imgurl='../../'.preg_replace("/_crop\d{4}/",'',$face);
		}
		//$face			= $this->rock->repempt($face,'images/white.gif');
		$this->smartydata['face']		= $face;
		$this->smartydata['imgurl']		= $imgurl;
	}
	
	public function changestyleAjax()
	{
		$style = $this->post('style','0');
		m('admin')->update('`style`='.$style.'', 'id='.$this->adminid.'');
	}


	public function editpassAjax()
	{
		$id			= $this->adminid;
		if(getconfig('systype')=='demo')exit('演示上不要修改');
		$oldpass	= $this->rock->post('passoldPost');
		$pasword	= $this->rock->post('passwordPost');
		$msg		= '';
		if($this->rock->isempt($pasword))$msg ='新密码不能为空';
		if($msg == ''){
			$oldpassa	= $this->db->getmou($this->T('admin'),"`pass`","`id`='$id'");
			if($oldpassa != md5($oldpass))$msg ='旧密码不正确';
			if($msg==''){
				if($oldpassa == md5($pasword))$msg ='新密码不能和旧密码相同';
			}
		}
		if($msg == ''){
			if(!$this->db->record($this->T('admin'), "`pass`='".md5($pasword)."'", "`id`='$id'"))$msg	= $this->db->error();
		}
		if($msg=='')$msg='success';
		echo $msg;
	}
	
	
	/**
		保存头像
	*/
	public function savefaceAjax()
	{
		$id			= $this->adminid;
		$arr		= array('face'=>$this->rock->post('facePost'));
		$msg		= '';
		if(!$this->db->record($this->T('admin'),$arr, "`id`='$id'"))$msg= $this->db->error();
		if($msg=='')$msg='success';
		echo $msg;
	}
	
	public function todoydAjax()
	{
		m('todo')->update("status=1,`readdt`='$this->now'", "`id` in(".$this->post('s').") and `status`=0");
	}
	
	public function totaldaetods($table, $rows)
	{
		$wdtotal	= m('todo')->rows("`uid`='$this->adminid' and `status`=0 and `tododt`<='$this->now'");
		return array('wdtotal'=>$wdtotal);
	}
	
	public function beforetotaldaetods($table)
	{
		$s = " and `uid`='$this->adminid' and `tododt`<='$this->now'";
		return $s;
	}
	
	public function getlinksAjax()
	{
		$rows = m('links')->getrows('1=1','*','`type`,`sort`');
		echo json_encode($rows);
	}
	
	
	//导入个人通讯录
	public function piliangaddAjax()
	{
		$rows  	= c('html')->importdata('name,unitname,tel,mobile,email,gname,address','name');
		$oi 	= 0;
		$db 	= m('vcard');
		foreach($rows as $k=>$rs){
			$rs['optdt']	= $this->now;
			$rs['uid']		= $this->adminid;
			$rs['optname']	= $this->adminname;
			$db->insert($rs);
			$oi++;
		}
		backmsg('','成功导入'.$oi.'条数据');
	}
}