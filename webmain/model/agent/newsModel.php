<?php
/**
	新闻资讯
*/
class agent_newsClassModel extends agentModel
{
	
	
	protected function agentrows($rows, $rowd, $uid){
		$typearr = array();
		if($rows){
			foreach($rowd as $k=>$rs){
				$rows[$k]['picurl'] = $rs['fengmian'];
			}
		}
		if($this->loadci==1)$typearr = m('option')->getselectdata('newstype', true);
		return array(
			'rows' =>$rows,
			'typearr' =>$typearr,
		);
	}
}