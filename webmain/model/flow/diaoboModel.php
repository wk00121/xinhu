<?php
//调拨单
class flow_diaoboClassModel extends flowModel
{
	public $minwidth	= 600;//子表最小宽
	
	public function initModel()
	{
		$this->goodsobj = m('goods');
		$this->cangobj = m('godepot');
	}
	
	//审核完成处理,要通知仓库管理员出入库
	protected function flowcheckfinsh($zt){
		
	}

	
	//子表数据替换处理
	protected function flowsubdata($rows, $lx=0){
		$db = m('goods');
		foreach($rows as $k=>$rs){
			$one = $db->getone($rs['aid']);
			if($one){
				$name = $one['name'];
				if(!isempt($one['xinghao']))$name.='('.$one['xinghao'].')';
				if($lx==1)$rows[$k]['aid'] = $name; //1展示时
				$rows[$k]['temp_aid'] = $name;
			}
		}
		return $rows;
	}
	
	//$lx,0默认,1详情展示，2列表显示
	public function flowrsreplace($rs,$lx=0)
	{
		$rs['states']= $rs['state'];
		$rs['state'] = $this->goodsobj->crkstate($rs['state']);
		if(!isempt($rs['custname'])){
			$rs['custid'] = $rs['custname'];
		}else{
			$rs['custid']= $this->cangobj->getmou('depotname', $rs['custid']);
		}
		
		//读取物品
		if($lx==2){
			$rs['wupinlist'] = $this->goodsobj->getgoodninfo($rs['id'], 1);
		}
		return $rs;
	}
}