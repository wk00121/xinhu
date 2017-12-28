<?php
class deptClassModel extends Model
{
	/**
	*	$uarr 相应人员才能查看对应部门数据
	*/
	public function getdata($uarr=array())
	{
		$darr = $dtotal =array();
		$gids = '0';
		//要权限判断
		if(is_array($uarr)){
			$did  = '0';
			if($uarr)foreach($uarr as $k=>$rs){
				$dpath = str_replace(array('[',']'), array('',''), $rs['deptpath']);
				if(!isempt($dpath)){
					$dpatha = explode(',', $dpath);
					foreach($dpatha as $dpatha1){
						if(isempt($dpatha1))continue;
						$darr[$dpatha1]=$dpatha1;
						if(!isset($dtotal[$dpatha1]))$dtotal[$dpatha1]=0;
						$dtotal[$dpatha1]++;
					}
				}
				if(!isempt(arrvalue($rs,'groupname')))$gids.=','.$rs['groupname'].'';
			}
			foreach($darr as $k1=>$v1)$did.=','.$k1.'';
			$where= 'id in('.$did.')';
			$dbs  = m('admin');
			if(isempt($this->rock->get('changerange'))){
				if((int)$dbs->getmou('type', $this->adminid)==1){
					$where = '1=1'; //管理员可看全部
				}else{
					$where1= m('view')->viewwhere('dept', $this->adminid, 'id');
					if(contain($where1,'1=1')){
						$where = '1=1'; //全部
					}else{
						$where = '`id`>0 and ((1 '.$where1.') or (id in('.$did.')))';
					}
				}
			}
		}else{
			$where = '1=1';
		}
		$rows = $this->getall($where,'`id`,`name`,`pid`,`sort`','`pid`,`sort`');
		if(is_array($uarr))foreach($rows as $k=>$rs){
			$stotal = $dbs->rows("`status`=1 and instr(`deptpath`,'[".$rs['id']."]')>0");
			$rows[$k]['stotal'] = $stotal; //对应部门下有多少人
			$rows[$k]['ntotal']	= $this->rock->arrvalue($dtotal, $rs['id'], '0');
		}
		$this->groupids = $gids;
		return $rows;
	}
	
	public function getdeptrows($ids)
	{
		if(isempt($ids))return array();
		$rows = $this->getall("`id` in($ids)",'`id`,`name`,`pid`,`sort`','`pid`,`sort`');
		return $rows;
	}
	
	/**
	*	获取部门和人员数据
	*/
	public function getdeptuserdata()
	{
		$userarr 	= m('admin')->getuser(1);
		$deptarr 	= $this->getdata($userarr);
		//$grouparr	= m('group')->getall('id in('.$this->groupids.')','id,name','`sort`');
		$grouparr	= m('group')->getall('id >0','id,name','`sort`');
		
		return array(
			'uarr' => $userarr,
			'darr' => $deptarr,
			'garr' => $grouparr,
		);
	}
	
	//获取某个人对应部门Id
	public function getudept($uid)
	{
		$urs 	= $this->db->getone('[Q]admin', "`id`='$uid'",'deptid,deptpath');
		$deptid = arrvalue($urs,'deptid','0');
		$drs 	= $this->getone('`id`='.$deptid.'');
		
		if(!$drs)$drs = array('name'=>'','id'=>0,'num'=>'');
		$nums = $drs['num'];
		if(isempt($nums))$nums = $drs['id'];
		$drs['nums'] = $nums;
		return $drs;
	}
	
	//人员在线离线更新
	public function online($lx)
	{
		$ustr = "online=".$lx.",`lastonline`='".$this->rock->now."'";
		$this->db->update('[Q]admin', $ustr,'`id`='.$this->adminid.'');
		if($lx==1)m('login')->uplastdt('pc', $this->rock->session('admintoken'));
	}
	
}