<?php
class flow_tovoidClassModel extends flowModel
{
	
	//展示是替换一下
	public function flowrsreplace($rs)
	{
		$str= '作废';
		if($rs['type']==1)$str= '删除';
		$rs['type'] = $str;
		return $rs;
	}

	//审核完成了处理单据，删除还是作废
	protected function flowcheckfinsh($zt)
	{
		$type = $this->rs['type'];
		$bilrs= $this->billmodel->getone("`sericnum`='".$this->rs['tonum']."'");
		if(!$bilrs)return;
		$mors = $this->db->getone('[Q]flow_set',"`id`='".$bilrs['modeid']."'");
		if(!$mors)return;
		//作废
		if($type==0){
			m('flow')->zuofeibill($mors['num'], $bilrs['mid'], $this->rs['explain']);
		}
		//删除
		if($type==1){
			m('flow')->deletebill($mors['num'], $bilrs['mid'], $this->rs['explain'], false);
		}
	}
	
}