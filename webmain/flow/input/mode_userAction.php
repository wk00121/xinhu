<?php
/**
*	用户管理
*/ 
class mode_userClassAction extends inputAction{
	
	
	protected function savebefore($table, $cans, $id, $addbo){
		if(getconfig('systype')=='demo' && $id>0)return '演示请勿编辑';
		$user = strtolower(trimstr($cans['user']));
		$name = trimstr($cans['name']);
		$num  = trimstr($cans['num']);
		$email= trimstr($cans['email']);
		$pass = $cans['pass'];
		$check= c('check');
		$mobile 	= $cans['mobile'];
		$weixinid 	= $cans['weixinid'];
		$pingyin 	= $cans['pingyin'];
		if(!isempt($pass)){
			if(strlen($pass)<4)return '密码至少要4位数';
		}
		$msg  = '';	
		if($check->isincn($user))return '用户名不能有中文';
		if(strlen($user)<2)return '用户名必须2位字符以上';
		if(!isempt($email) && !$check->isemail($email))return '邮箱格式有误';
		if(!isempt($pingyin) && $check->isincn($pingyin))return '名字拼音不能有中文';
		if(!isempt($num) && $check->isincn($num))return '编号不能有中文';
		if(!isempt($mobile)){
			if(!$check->ismobile($mobile))return '手机格式有误';
		}
		if(isempt($mobile) && isempt($email))return '邮箱/手机号不能同时为空';
		if(!isempt($weixinid)){
			if(is_numeric($weixinid))return '微信号不能是数字';
			if($check->isincn($weixinid))return '微信号不能有中文';
		}
		$db  = m($table);

		if($msg=='')if($db->rows("`user`='$user' and `id`<>'$id'")>0)$msg ='用户名['.$user.']已存在';
		
		/*
			为啥这里注释了？已改成用表单元素管理下的唯一值设置来判断是否有重复。
		if($msg=='' && $num!='')if($db->rows("`num`='$num' and `id`<>'$id'")>0)$msg ='编号['.$num.']已存在';
		
		if($msg=='' && !isempt($mobile))if($db->rows("`mobile`='$mobile' and `id`<>'$id'")>0)$msg ='手机号['.$mobile.']已存在';
		if($msg=='' && !isempt($email))if($db->rows("`email`='$email' and `id`<>'$id'")>0)$msg ='邮箱['.$email.']已存在';
		if($msg=='' && !isempt($weixinid))if($db->rows("`weixinid`='$weixinid' and `id`<>'$id'")>0)$msg ='微信号['.$weixinid.']已存在';
		if($msg=='')if($db->rows("`name`='$name' and `id`<>'$id'")>0)$msg ='姓名['.$name.']已存在';
		*/
		
		$rows = array();
		if($msg == ''){
			$did  = $cans['deptid'];
			$sup  = $cans['superid'];
			$rows = $db->getpath($did, $sup);
		}
		if(isempt($pingyin))$pingyin = c('pingyin')->get($name,1);
		$rows['pingyin'] = $pingyin;
		$rows['user'] 	= $user;
		$rows['name'] 	= $name;
		$rows['email'] 	= $email;
		$notsave		= '';
		
		if($addbo){
			if(isempt($pass))$pass = '123456';
			$rows['pass'] 	= md5($pass);
		}else{
			if(isempt($pass)){
				$notsave	= 'pass';
			}else{
				$rows['pass'] 	= md5($pass);
			}
		}
		$arr = array('msg'=>$msg, 'rows'=>$rows,'notsave'=>$notsave);
		return $arr;
	}
	
	public function companydata()
	{
		return m('company')->getselectdata(1);
	}
	

	//保存后处理
	protected function saveafter($table, $cans, $id, $addbo){
		
		m($table)->record(array('superman'=>$cans['name']), "`superid`='$id'");
		
		$mygroup = $cans['groupname'];
		m('sjoin')->addgroupuid($id, $mygroup);
		
		return m('admin')->updateinfo('and a.id='.$id.'');
	}
	
	//组数据源
	public function groupdata()
	{
		return m('sjoin')->getgrouparrs();
	}
	
	//生成列表页，数据读取后处理
	protected function storeafter($table, $rows)
	{
		$barr['rows'] = $rows;
		if($this->loadci==1 && $this->post('atype')=='txlmy'){
			$this->depta = array();
			$drows = m('dept')->getdata($rows);
			$barr['deptdata'] = $this->depttreeshu($drows,'0');
		}
		return $barr;
	}
	//组织结构活动得到树形数据
	private function depttreeshu($rows, $pid)
	{
		$barr = array();
		foreach($rows as $k=>$rs){
			if($rs['pid']==$pid){
				$rs['children'] = $this->depttreeshu($rows, $rs['id']);
				$rs['expanded'] = $pid=='0';
				$barr[] = $rs;
			}
		}
		return $barr;
	}
	
	//修改上级
	public function editsuperAjax()
	{
		$sna	= $this->post('sna');
		$sid	= $this->post('sid');
		$xid	= $this->post('xid');
		m('admin')->update(array(
			'superid' => $sid,
			'superman' => $sna,
		),"`id` in($xid) and id not in($sid)");
		
		m('admin')->updateinfo(); //更新
		return 'ok';
	}
}	
			