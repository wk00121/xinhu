<?php
class worcClassModel extends Model
{
	
	/**
	*	获取我的分区
	*/
	public function getmywroc($uid=0)
	{
		if($uid==0)$uid = $this->adminid;
		$db   = m('admin');
		$dbs  = m('word');
		$str  = $db->getjoinstr('receid', $uid, 1);
		$utype= arrvalue($db->nowurs,'type','0');
		$rows = $this->getall('1=1 and ('.$str.')','id,name,guanname,guanid,uptype','`sort`');
		$ids  = '';
		foreach($rows as $k=>$rs){
			$ids.=','.$rs['id'].'';
			//判断是否管理权限
			$ismanage = 0;
			if($utype=='1')$ismanage=1;
			if($ismanage==0 && !isempt($rs['guanid'])){
				if($db->containjoin($rs['guanid'], $uid))$ismanage=1;
			}
			$rows[$k]['ismanage'] = $ismanage;
			
			$wcount = $dbs->rows('`cid`='.$rs['id'].' and `type`=0');
			
			$rows[$k]['wcount'] = $wcount;
			if(isempt($rs['uptype']))$rs['uptype'] = '';
			$rows[$k]['uptype'] = $rs['uptype'];
		}
		if($ids!='')$ids = substr($ids, 1);
		return array(
			'rows' => $rows,
			'ids' => $ids,
		);
	}
	
}