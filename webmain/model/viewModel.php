<?php
class viewClassModel extends Model
{
	private $modeid = 0;
	private $isflow = 0;
	
	private $ursarr	= array();
	
	public function initModel()
	{
		$this->settable('flow_extent');
		$this->addb 	= m('admin');
		$this->whereobj = m('where');
	}
	
	private function getursss($mid, $uid=0)
	{
		if($uid==0)$uid = $this->adminid;
		if(isset($this->ursarr[$uid])){
			$this->urs	= $this->ursarr[$uid];
		}else{
			$this->urs 	= $this->db->getone('[Q]admin',$uid);
			$this->ursarr[$uid] = $this->urs;
		}
		if(is_array($mid)){
			$this->modrs = $mid;
		}else{
			$this->modrs = $this->db->getone('[Q]flow_set'," (`id`='$mid' or `num`='$mid')");
		}
		if($this->modrs){
			$this->modeid = $this->modrs['id'];
			$this->isflow = $this->modrs['isflow'];
		}
	}
	
	//返回可查看条件
	public function viewwhere($mid, $uid=0, $ufid='', $glx=0)
	{
		$this->getursss($mid, $uid);
		return $this->getsswhere(0, $ufid, $glx);
	}
	
	//是否有新增权限
	public function isadd($mid, $uid=0)
	{
		$this->getursss($mid, $uid);
		$bo  = false;
		$type = $this->urs['type'];
		if($type==1)return true; //管理员有添加
		$bo  = $this->getsswhere(1);
		return $bo;
	}
	
	//是否有导入权限
	public function isdaoru($mid, $uid=0)
	{
		$this->getursss($mid, $uid);
		$bo  = $this->getsswhere(4);
		return $bo;
	}
	
	//是否有导出权限
	public function isdaochu($mid, $uid=0)
	{
		$this->getursss($mid, $uid);
		$bo  = $this->getsswhere(5);
		return $bo;
	}
	
	//是否有编辑数据权限
	public function editwhere($mid, $uid=0)
	{
		$this->getursss($mid, $uid);
		return $this->getsswhere(2);
	}
	
	//是否有删除数据权限
	public function deletewhere($mid, $uid=0)
	{
		$this->getursss($mid, $uid);
		return $this->getsswhere(3);
	}
	
	//$type类型0查看,1新增 $ufid 用户ID $glx0返回类型
	private function getsswhere($type, $ufid='', $glx=0)
	{
		$mid	= $this->modeid;
		$where 	= $this->addb->getjoinstr('receid', $this->urs);
		if($ufid=='')$ufid = 'uid';
		$uid	= $this->urs['id'];
		$companyid	= arrvalue($this->urs, 'companyid','0');
		$rows 	= $this->getall('`type`='.$type.' and `modeid`='.$mid.' and `status`=1 '.$where.'','wherestr,whereid');
		$wehs	= array();
		$count  = $this->db->count;
		if($type==1 || $type==4 || $type==5){
			return $count>0;
		}
		$qomss  = ($glx==0)?'':'{asqom}';
		if($type== 0 && $count==0 && $this->isflow==1){
			$rows[] = array('wherestr'=>$this->rock->jm->base64encode('`uid`={uid}'),'whereid'=>0);
		}
		foreach($rows as $k=>$rs){
			$sw = $this->rock->jm->base64decode($rs['wherestr']);
			if($sw=='{receid}'){
				$sw = $this->addb->getjoinstr('receid', $this->urs, 1);
			}
			//我所有下属的下属
			if($sw=='{allsuper}'){
				$sw = "`$ufid` in(select `id` from `[Q]admin` where instr(`superpath`,'[$uid]')>0)";
			}
			//我的直属下属
			if($sw=='{super}'){
				$sw1= $this->rock->dbinstr('superid',$uid);
				$sw = "`$ufid` in(select `id` from `[Q]admin` where $sw1)";
			}
			//同一个部门下人员
			if($sw=='{dept}' && !isempt($this->urs['deptid'])){
				$sw = "`$ufid` in(select `id` from `[Q]admin` where `deptid`=".$this->urs['deptid'].")";
			}
			
			//同一个部门下人员(包括子部门)
			if($sw=='{deptall}' && !isempt($this->urs['deptid'])){
				$sw = "`$ufid` in(select `id` from `[Q]admin` where instr(`deptpath`,'[".$this->urs['deptid']."]')>0)";
			}
			
			//同一个单位
			if($sw=='{company}'){
				$sw = "`$ufid` in(select `id` from `[Q]admin` where `companyid`=".$companyid.")";
			}
			
			//所有数据
			if($sw=='all'){
				return ' and 1=1';
			}
			if(!isempt($sw)){
				$sw 	= $this->whereobj->getstrwhere($sw, $uid, $ufid);
				$sw 	= str_replace('{asqom}', $qomss, $sw);
				$sw 	= '('.$sw.')';
				$wehs[] = $sw;
			}
			$whereid = (int)$rs['whereid'];
			if($whereid>0){
				$sww = $this->whereobj->getwherestr($whereid, $uid, $ufid, 1);
				if($sww!='')$wehs[] = '('.$sww.')';
			}
		}
		$s = join(' or ', $wehs);
		if($s!=''){
			$s = ' and ('.$s.')';
		}else{
			$s = ' and 1=2';
		}
		return $s;
	}
}