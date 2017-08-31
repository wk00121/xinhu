<?php 
/**
*	文件下载相关接口
*/
class fileClassAction extends apiAction
{
	
	/**
	*	获取文件信息
	*/
	public function getfileAction()
	{
		$id 	= (int)$this->post('id',0);
		$rs 	= m('file')->getone($id);
		if(!$rs)$this->showreturn('', '文件不存在1', 201);
		$path 	= $rs['filepath'];
		if(isempt($path) || !file_exists($path))$this->showreturn('', '文件['.$rs['filename'].']不存在', 202);
		$rs['filetype']	= m('file')->getmime($rs['fileext']);
		$this->showreturn($rs);
	}
	
	/**
	*	下载文件
	*/
	public function downAction()
	{
		$id  = (int)$this->jm->gettoken('id');
		m('file')->download($id);
	}
}