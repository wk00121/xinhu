<?php
class flow_goodlyClassModel extends flowModel
{
	
	//审核完成处理
	protected function flowcheckfinsh($zt){
		m('goodss')->update('status='.$zt.'',"`mid`='$this->id'");
		$aid  = '0';
		$rows = m('goodss')->getall("`mid`='$this->id'",'aid');
		foreach($rows as $k=>$rs)$aid.=','.$rs['aid'].'';
		m('goods')->setstock($aid);
	}

	
	
	//子表数据替换处理
	protected function flowsubdata($rows, $lx=0){
		$db = m('goods');
		foreach($rows as $k=>$rs){
			$one = $db->getone($rs['aid']);
			if($one){
				if($lx==1){
					$rows[$k]['aid'] 	= $one['name'];
					$rows[$k]['count'] 	= 0-$rs['count']; //负数显示为正数
				}
				$rows[$k]['unit'] 	= $one['unit'];
				$rows[$k]['temp_aid'] = $one['name'];
			}
		}
		return $rows;
	}
}