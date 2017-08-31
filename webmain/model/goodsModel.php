<?php
class goodsClassModel extends Model
{
	public function setstock($id='', $lsx='1')
	{
		$where = '';
		if($id!='')$where=' and `aid` in('.$id.')';
		$sql = 'SELECT sum(count)stock,aid FROM `[Q]goodss` where `status` in('.$lsx.') '.$where.' GROUP BY aid';
		if($id=='')$this->update('stock=stockcs','id>0');
		$rows= $this->db->getall($sql);
		foreach($rows as $k=>$rs){
			$this->update('`stock`=`stockcs`+'.$rs['stock'].'', $rs['aid']);
		}
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
		$rowss  = m('goods')->getall('1=1','id,name');
		$rows	= array();
		foreach($rowss as $k=>$rs){
			$rows[] = array(
				'name' => $rs['name'],
				'value' => $rs['id'],
			);
		}
		return $rows;
	}
}