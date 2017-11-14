<?php
class goodsClassModel extends Model
{
	//$lx=0入库,$lx=1出
	public function crkstate($zt, $lx=0)
	{
		$arrs = array('入','出');
		$ztna = array('待'.$arrs[$lx].'库','已'.$arrs[$lx].'库','已部分'.$arrs[$lx].'库');
		$ztnc = array('red','green','#ff6600');
		
		return '<font color="'.$ztnc[$zt].'">'.$ztna[$zt].'</font>';
	}
	
	//设置库存
	public function setstock($id='', $lsx='1')
	{
		$where = '';
		if($id!='')$where=' and `aid` in('.$id.')';
		$sql = 'SELECT sum(count)stock,aid FROM `[Q]goodss` where `status`=1 '.$where.' GROUP BY aid';
		if($id=='')$this->update('stock=0','id>0');
		$rows= $this->db->getall($sql);
		foreach($rows as $k=>$rs){
			$this->update('`stock`='.$rs['stock'].'', $rs['aid']);
		}
	}
	
	//根据仓库日期获取库存
	public function getstock($id='', $dt='')
	{
		$where= '';
		if($id!='')$where='`aid` in('.$id.') and ';
		if($dt!='')$where.="`applydt`<='$dt' and "; //日期
		$sql = 'SELECT sum(count)stock,`aid`,`depotid` FROM `[Q]goodss` where '.$where.' `status`=1  GROUP BY `aid`,`depotid`';
		$rows= $this->db->getall($sql);
		$arra = array();
		foreach($rows as $k=>$rs){
			$aid = $rs['aid'];
			$arra[$aid][$rs['depotid']] = $rs['stock'];
			if(!isset($arra[$aid][0])) $arra[$aid][0]= 0;
			$arra[$aid][0]+=floatval($rs['stock']);
		}
		return $arra;
	}
	
	//待出入库数量
	public function getdaishu()
	{
		return $this->db->rows('`[Q]goodm`','`status`=1 and `state`<>1');
	}
	
	//判断是否存在相同库存
	public function existsgoods($rs, $id=0)
	{
		$where 	= "`id`<>".$id." and `typeid`=".$rs['typeid']." and `name`='".$rs['name']."' and ifnull(`guige`,'')='".$rs['guige']."' and ifnull(`xinghao`,'')='".$rs['xinghao']."'";
		$to 	= $this->rows($where);
		return $to>0;
	}
	
	public function getgoodstype()
	{
		$dbs 	= m('option');
		$rowss  = $dbs->getdata('goodstype');
		$rows	= array();
		foreach($rowss as $k=>$rs){
			$rows[] = array(
				'name' => $rs['name'],
				'value' => $rs['id'],
			);
			$rowsa = $dbs->getdata($rs['id']);
			if($rowsa)foreach($rowsa as $k1=>$rs1){
				$rows[] = array(
					'name' => '	&nbsp;	&nbsp; ├'.$rs1['name'],
					'value' => $rs1['id'],
				);
			}
		}
		return $rows;
	}
	
	public function getgoodsdata()
	{
		$rowss  = m('goods')->getall('1=1','id,name,xinghao');
		$rows	= array();
		foreach($rowss as $k=>$rs){
			$name 	= $rs['name'];
			if(!isempt($rs['xinghao']))$name.='('.$rs['xinghao'].')';
			$rows[] = array(
				'name' 	=> $name,
				'value' => $rs['id'],
			);
		}
		return $rows;
	}
	
	/**
	*	主表goodm部分出入库状态更新
	*/
	public function upstatem($ids='')
	{
		$dbm  = m('goodm');
		$where= '';
		if($ids!='')$where="`id` in($ids) and ";
		$rows = $dbm->getall(''.$where.'`status`=1 and `state` in(0,2)');
		foreach($rows as $k=>$rs){
			$id 	= $rs['id'];
			$state 	= $rs['state'];
			$rsone 	= $this->db->getone('[Q]goodn','`mid`='.$id.'','sum(`count`)count,sum(`couns`)couns');
			$count 	= floatval($rsone['count']);
			$couns 	= floatval($rsone['couns']);
			if($couns==0){
				$zt = 0;
			}else if($couns>=$count){
				$zt = 1;
			}else{
				$zt = 2;
			}
			if($state!=$zt)$dbm->update('`state`='.$zt.'', $id);
		}
	}
	
	/**
	*	 供应商列表
	*/
	public function getgys()
	{
		$arows 	= m('customer')->getall('`status`=1 and `isgys`=1','id as value,name');
		return $arows;
	}
}