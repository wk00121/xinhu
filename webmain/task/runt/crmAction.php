<?php
class crmClassAction extends runtAction
{
	//客户合同到期，每天运行
	public function runAction()
	{
		m('flow')->initflow('custract')->custractdaoqi();
		echo 'success';
	}
	
}