<?php
class carmClassAction extends runtAction
{
	//车辆提醒，每天运行
	public function runAction()
	{
		echo m('flow')->initflow('carms')->todocarms($this->runrs['todoid']);
	}
	
}