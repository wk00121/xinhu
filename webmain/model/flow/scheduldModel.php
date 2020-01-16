<?php
//日程待办
class flow_scheduldClassModel extends flowModel
{
	protected function flownexttodo($type){
		
		if($type=='daiban'){
			
			$cont = '发起人：{optname}\n时间：{startdt}';
			if(!isempt($this->rs['explain']))$cont.= '\n说明：{explain}';
			$cont.= '\n请尽快去处理';
			return array(
				'title' => '日程待办:{title}',
				'cont'	=> $cont
			);
		}
	}
}