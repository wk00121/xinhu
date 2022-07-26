<?php
class recordClassAction extends Action
{
	public function storebeforeshow($table)
	{
		$dt1	= $this->post('dt1');
		$dt2	= $this->post('dt2');
		$key	= $this->post('key');
		$atype 	= $this->post('atype');
		$receid = $this->post('receid');
		$whe 	= $this->rock->dbinstr('`receuid`', $this->adminid);
		$where = 'and '.$whe.'';
		
		if($atype=='all'){
			$where = '';
		}
		
		if(!isempt($dt1))$where.=" and `optdt`>='$dt1'";
		if(!isempt($dt2))$where.=" and `optdt`<='$dt2 23:59:59'";
		if(!isempt($receid)){
			$where.=" and ((`sendid` in($receid)) or (`type`='user' and `receid` in($receid)))";
		}
		if(!isempt($key)){
			$key = $this->rock->jm->base64encode($key);
			$where.=" and `cont` like '%$key%'";
		}
		
		return $where;
	}
	
	//数据显示后处理
	public function storeaftershow($table,$rows)
	{
		$suids 	 = '0';
		$guids 	 = '0';
		$fuids 	 = '0';
		$typearr = array('user'=>'单人','group'=>'群会话');
		foreach($rows as $k=>$rs){
			$suids.=','.$rs['sendid'].'';
			
			$rows[$k]['typetxt']= arrvalue($typearr, $rs['type']);
			if($rs['type']=='user'){
				$suids.=','.$rs['receid'].'';
			}else{
				$guids.=','.$rs['receid'].'';
			}
			if($rs['fileid']!='0')$fuids.=','.$rs['fileid'].'';
		}
		$warr	= $farr = $garr = array();
		
		if($suids!='0')$farr	= $this->db->getarr('[Q]admin', "`id` in($suids)",'`face`,`name`');
		if($guids!='0')$garr	= $this->db->getarr('[Q]im_group', "`id` in($guids)",'`face`,`name`');
		
		if($fuids!='0')$warr	= $this->db->getarr('[Q]file', "`id` in($fuids)",'filename,filesizecn,fileext,filepath,thumbpath,filenum'); //相关文件
		$fobj 	= m('file');
		$ztfo   = m('im_messzt');
		foreach($rows as $k=>$rs){
			$sendid = $rs['sendid'];
			$receid = $rs['receid'];
			$type 	= $rs['type'];
			if(isset($farr[$sendid])){
				$rows[$k]['sendname'] 	  = $farr[$sendid]['name'];
			}	
			if($type=='user'){
				if(isset($farr[$receid])){
					$rows[$k]['recename'] = $farr[$receid]['name'];
				}
			}else{
				if(isset($garr[$receid])){
					$rows[$k]['recename'] = $garr[$receid]['name']; //群名称
				}
			}
			//发送人是我判断是否已读未读
			$zttext = '';
			if($type=='user'){
				if($rs['zt']=='1'){
					$zttext = '已读';
					$rows[$k]['ishui']=1;
				}else{
					$zttext = '<font color=red>未读</font>';
				}
			}else{
				if($sendid == $this->adminid){
					$tos = $ztfo->rows('mid='.$rs['id'].'');
					if($tos==0){
						$zttext = '全部已读';
						$rows[$k]['ishui']=1;
					}else{
						$zttext = '<font color=red>'.$tos.'人未读</font>';
					}
				}
			}
			
			$rows[$k]['zttext'] = $zttext;
			
			$fileid 	= $rs['fileid'];
			
			
			if(isset($warr[$fileid])){
				$fileid = $fobj->getfilestr($warr[$fileid],1);
			}
			
			if($fileid=='0')$fileid = '';
			$rows[$k]['fileid'] = $fileid;
		}

		return array(
			'rows' => $rows
		);
	}
	
	//删除聊天记录
	public function delrecordAjax()
	{
		$id 	= $this->post('id');
		$atype  = $this->post('atype');
		if($atype!='all'){
			if(!isempt($id)){
				m('reim')->clearrecord('',0,$this->adminid, $id);
			}
		}else{
			//管理员删除
			if($this->getsession('isadmin')!='1')backmsg('非管理员不能操作');
			
			if(!isempt($id)){
				m('im_mess')->delete('id in('.$id.')');
				m('im_messzt')->delete('mid in('.$id.')');
			}
		}
		backmsg();
	}
}