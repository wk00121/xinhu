<?php
class mode_meetClassAction extends inputAction{
	

	protected function savebefore($table, $arr, $id, $addbo){
		if(arrvalue($arr,'type')=='1')return ''; //固定会议不需要判断
		return m('meet')->isapplymsg($arr['startdt'], $arr['enddt'], $arr['hyname'], $id);
	}
	

	protected function saveafter($table, $arr, $id, $addbo){
		
	}
}		