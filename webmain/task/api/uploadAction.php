<?php 
header('Access-Control-Allow-Origin: *');
class uploadClassAction extends apiAction
{
	/**
	*	上传文件
	*/
	public function upfileAction()
	{
		if(!$_FILES)exit('sorry!');
		$upimg	= c('upfile');
		$maxsize= (int)$this->get('maxsize', $upimg->getmaxzhao());//上传最大M
		$uptypes= '*';
		$upimg->initupfile($uptypes, ''.UPDIR.'|'.date('Y-m').'', $maxsize);
		$upses	= $upimg->up('file');
		if(!is_array($upses))exit($upses);
		$arr 	= c('down')->uploadback($upses);
		$this->returnjson($arr);
	}
	
	/**
	*	这个是用来在线编辑文档上传的
	*/
	public function upfilevbAction()
	{
		$fileid = $this->get('fileid','0');
		if($fileid==0)exit('fileid=0');
		
		$data 	= $this->getpostdata();
		if(isempt($data))return '没有数据';
		$fileext= $this->get('fileext');
		$uptype = '|doc|docx|xls|xlsx|ppt|pptx|';
		if(!contain($uptype,'|'.$fileext.'|'))$fileext='doc';
		$filepath = ''.UPDIR.'/'.date('Y-m').'/'.date('d_His').''.rand(10,99).'.'.$fileext.'';
		$this->rock->createtxt($filepath, base64_decode($data));
		
		$filesize 			  	= filesize($filepath);
		$filesizecn 		  	= $this->rock->formatsize($filesize);
		
		//更新文件
		m('file')->update(array(
			'filepath' 		=> $filepath,
			'filesize' 		=> $filesize,
			'filesizecn' 	=> $filesizecn,
			'pdfpath' 		=> '',
		),$fileid);
		return 'ok,'.md5(URL).'_'.$filesize.'_'.$fileid.'.'.$fileext.'';
		
		return 'ok';
		if(!$_FILES)exit('sorry!');
		
		$upimg	= c('upfile');
		$upimg->initupfile($uptype, ''.UPDIR.'|'.date('Y-m').'', 20);
		$upses	= $upimg->up('file');
		if(!is_array($upses))exit($upses);
		//更新文件
		m('file')->update(array(
			'filepath' 		=> $upses['allfilename'],
			'filesize' 		=> $upses['filesize'],
			'filesizecn' 	=> $upses['filesizecn'],
			'pdfpath' 		=> '',
		),$fileid);
		return 'ok,'.md5(URL).'_'.$upses['filesize'].'_'.$fileid.'.'.$upses['fileext'].'';
	}
	
	/**
	*	上传时初始化看是不是存在文件
	*/
	public function initfileAction()
	{
		$filesize	= $this->post('filesize');
		$fileext	= $this->post('fileext');
		$filename	= $this->getvals('filename');
		$where 		= "`fileext`='$fileext' and `filesize`=$filesize";
		if(!isempt($filename))$where.=" and `filename`='$filename'";
		$frs 		= m('file')->getone($where,'*','`id` desc');
		$bo 		= false;
		if($frs){
			$filepath = $frs['filepath'];
			if(!isempt($filepath) && file_exists($filepath))$bo=true;
		}
		if($bo){
			$this->showreturn(json_encode($frs));
		}else{
			$this->showreturn('','not found', 201);
		}
	}
	
	public function upfileappAction()
	{
		if(!$_FILES)$this->showreturn('', '禁止访问', 201);
		$upimg	= c('upfile');
		$maxsize= (int)$this->get('maxsize', $upimg->getmaxzhao());//上传最大M
		$uptypes= '*';
		$upimg->initupfile($uptypes, ''.UPDIR.'|'.date('Y-m').'', $maxsize);
		$upses	= $upimg->up('file');
		if(!is_array($upses))$this->showreturn('', $upses, 202);
		$arr 	= c('down')->uploadback($upses);
		$this->showreturn($arr);
	}
	
	public function upcontAction()
	{
		$cont = $this->post('content');
		if(isempt($cont))exit('sorry not cont');
		$cont 	= str_replace(' ','', $cont);
		$cont	= $this->rock->jm->base64decode($cont);
		$arr 	= c('down')->createimage($cont,'png','截图');
		$this->returnjson($arr);
	}
	
	
	public function getfileAjax()
	{
		$cont = '';
		$path = ''.UPDIR.'/uptxt'.$this->adminid.'.txt';
		if(file_exists($path)){
			@$cont = file_get_contents($path);
		}
		$data = array();
		if($cont!=''){
			$arr = json_decode($cont, true);
			$msg 	= $arr['msg'];
			$data 	= $arr['data'];
			@unlink($path);
		}else{
			$msg = 'sorry,not infor!';
		}
		$this->showreturn($data, $msg);
	}
	
	public function getfileAction()
	{
		$fileid = (int)$this->post('fileid',0);
		$rs 	= m('file')->getone($fileid);
		$this->showreturn($rs);
	}
	
	public function downAction()
	{
		$id  = (int)$this->jm->gettoken('id');
		m('file')->show($id);
	}
}