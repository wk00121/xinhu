<?php
class wordClassModel extends Model
{
	private $optionobj;
	
	public function initModel()
	{
		$this->optionobj = m('option');
	}
	
	/**
	*	读取对应顶级ＩＤ
	*/
	public function getfolderid($uid, $isdept=false)
	{
		$num = "folder".$uid."";
		$name= ''.$this->adminname.'文件夹目录';
		if($isdept){
			$drs = m('dept')->getudept($uid);
			$num = 'deptfolder'.$drs['nums'].'';
			$name= ''.$drs['name'].'文件夹目录';
		}
		$id  = $this->optionobj->getnumtoid($num, $name, false);
		return $id;
	}
	
	/**
	*	对应对应文件目录
	*/
	public function getfoldrows($uid, $isdept=false)
	{
		$pid 	= $this->getfolderid($uid, $isdept);
		$rows 	= $this->optionobj->gettreedata($pid);
		return $rows;
	}
	
	/**
	*	读取所有目录
	*/
	private $allfolder = array();
	public function getallfolder($idss='',$level=0)
	{
		$where = "`num` like 'folder%' or `num` like 'deptfolder%'";
		if(!isempt($idss))$where = "`pid` in($idss)";
		$rows = $this->db->getall("SELECT * FROM `[Q]option` where $where");
		$ids  = '';
		foreach($rows as $k=>$rs){
			$ids.=','.$rs['id'].'';
			$rs['level'] = $level;
			if(!isempt($idss))$this->allfolder[] = $rs;
		}
		if($ids!=''){
			$this->getallfolder(substr($ids, 1), $level+1);
		}
		return $this->allfolder;
	}
	
	/**
	*	获取分享给我的目录(太复杂了，无法进行了)
	*/
	public function getshatefolder($uid)
	{
		$allfolder 	= $this->getallfolder();
		$urs 		= $this->db->getone('[Q]admin','`id`='.$uid.'','id,deptpath');
		$uarr[]		= 'u'.$uid.'';
		$deptpath 	= arrvalue($urs, 'deptpath');
		if(!isempt($deptpath)){
			$depa = explode(',', str_replace(array('[',']'), array('',''), $deptpath));
			foreach($depa as $depas){
				$uarr[] = 'd'.$depas.'';
			}
		}
		$rows 		= $this->getshatefolders($allfolder, 1, $uarr, 0);
		return $rows;
	}
	private function getshatefolders($allfolder, $level, $uarr, $pid=0)
	{
		$rarr = array();
		foreach($allfolder as $k=>$rs){
			$receid = $rs['receid'];
			if($level==1 && $rs['level']!=$level)continue;
			if($level>1 && $pid!=$rs['pid'])continue;
			$bo 	 = false;
			if(!isempt($receid)){
				$receida = explode(',', $receid);
				foreach($uarr as $uarrs){
					if(in_array($uarrs, $receida))$bo=true;
				}
			}

			$rs['children'] = $this->getshatefolders($allfolder, $level+1, $uarr, $rs['id']);
			$rarr[] = $rs;
		}
		return $rarr;
	}
}