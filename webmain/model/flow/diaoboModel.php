<?php
//调拨单
class flow_diaoboClassModel extends flowModel
{
	public $minwidth	= 600;//子表最小宽
	
	public function initModel()
	{
		$this->goodsobj = m('goods');
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
	public function flowrsreplace($rs)
	{
		$rs['states']= $rs['state'];
		$rs['state'] = $this->goodsobj->crkstate($rs['state']);
		
		$rs['custid']= m('godepot')->getmou('depotname', $rs['custid']);
		
		return $rs;
	}
}