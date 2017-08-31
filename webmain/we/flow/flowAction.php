<?php 
class flowClassAction extends ActionNot{
	
	public function initAction()
	{
		$this->mweblogin(0, true);
	}
	
	public function applyAction()
	{
		$this->title = '申请流程';
		
		$viewobj 	= m('view');
		$applyarr	= m('mode')->getmoderows($this->adminid,'and islu=1');
		$modearr	= array();
		$otyle		= '';
		$oi 		= 0;
		foreach($applyarr as $k=>$rs){
			if(!$viewobj->isadd($rs, $this->adminid))continue;
			if($otyle!=$rs['type']){
				$oi = 0;
			}
			$otyle = $rs['type'];
			$oi++;
			$modearr[$rs['type']][]=array('modenum'=>$rs['num'],'name'=>$rs['name'],'title'=>''.$oi.'.'.$rs['name']);
		}
		$this->assign('modearr', $modearr);
	}
}