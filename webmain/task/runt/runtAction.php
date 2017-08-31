<?php
class runtAction extends ActionNot
{
	public $runid = 0;
	public $runrs;
	public $splitlast = 0; //距离上次提醒秒数0上次没有运行
	
	public $todoarr		= array();
	
	public function initAction()
	{
		$this->runid	= (int)$this->get('runid','0');
		$this->runrs	= m('task')->getone($this->runid);
		$this->display 	= false;
		if($this->runrs && !isempt($this->runrs['lastdt'])){
			$this->splitlast = time() - strtotime($this->runrs['lastdt']);
		}
	}
	
	/**
	*	运行完成后判断运行状态
	*/
	public function afterAction()
	{
		if($this->runid > 0){
			$state	= 2;
			$cont  	= ob_get_contents();	
			if($cont=='success')$state=1;
			m('task')->update(array(
				'lastdt'	=> $this->rock->now,
				'lastcont' 	=> $cont,
				'state' 	=> $state
			), $this->runid);
			
			//提醒的
			$todoid = arrvalue($this->runrs,'todoid');
			if(!isempt($todoid) && $this->todoarr){
				$modenum	= arrvalue($this->todoarr, 'modenum');
				$agentname	= arrvalue($this->todoarr, 'agentname');
				$title		= arrvalue($this->todoarr, 'title');
				$cont		= arrvalue($this->todoarr, 'cont');
				if(!isempt($modenum)){
					$flow 	= m('flow')->initflow($modenum);
					$flow->push($todoid, $agentname, $cont, $title);
				}else{
					m('todo')->add($todoid, $title, $cont);
				}
			}
		}
	}
}
class runtClassAction extends runtAction
{
	public function runAction()
	{
		$mid	= (int)$this->get('mid','0');
		m('task')->baserun($mid);
		echo 'success';
	}
	public function getlistAction()
	{
		$dt 	= $this->get('dt', $this->date);
		$barr 	= m('task')->getlistrun($dt);
		$this->option->setval('systaskrun', $this->now);
		$this->returnjson($barr);
	}
	
	/**
	*	运行定时任务用于cli模式的
	*	Linux 使用crontab php task.php runt,task
	*	win 使用计划任务 php task.php runt,task
	*/
	public function taskAction()
	{
		echo m('task')->runjsonlist();
	}
}