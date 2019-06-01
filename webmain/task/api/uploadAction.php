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
		$updir		= $this->get('updir');
		if(isempt($updir)){
			$updir=date('Y-m');
		}else{
			$updir=str_replace(array(' ','.'),'', trim($updir));
			$updir=str_replace('{month}',date('Y-m'), $updir);
			$updir=str_replace('{Year}',date('Y'), $updir);
			$updir=str_replace(array('{','}'),'', $updir);
			$updir=str_replace(',','|', $updir);
		}
		$upimg->initupfile($uptypes, ''.UPDIR.'|'.$updir.'', $maxsize);
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
		$fileid = (int)$this->get('fileid','0');
		if($fileid==0)exit('fileid=0');
		$data 	= $this->getpostdata();
		if(isempt($data))return '没有数据';
		$fileext= $this->get('fileext');
		$uptype = '|doc|docx|xls|xlsx|ppt|pptx|';
		if(!contain($uptype,'|'.$fileext.'|'))$fileext='doc';
		$fileobj  = m('file');
		$frs 	  = $fileobj->getone($fileid); //记录
		if(!$frs)exit('文件记录不存在了');
		
		$frs['oldfilepath'] = $frs['filepath'];
		
		$filepath = ''.UPDIR.'/'.date('Y-m').'/'.date('d_His').''.rand(10,99).'.'.$fileext.'';
		$this->rock->createtxt($filepath, base64_decode($data));
		
		$filesize 			  	= filesize($filepath);
		$filesizecn 		  	= $this->rock->formatsize($filesize);
		
		//更新文件
		$fileobj->update(array(
			'filepath' 		=> $filepath,
			'filesize' 		=> $filesize,
			'filesizecn' 	=> $filesizecn,
			'pdfpath' 		=> '',
		),$fileid);
		
		//告诉上传人说编辑了他的附件
		$mknums = arrvalue($frs,'mknum');
		if(!isempt($mknums) && $frs['mid']>0){
			
			$mid = $frs['mid'];
			$mknumsa = explode('|', $mknums);
			$modenum = $mknumsa[0];
			if(isset($mknumsa[1]))$mid = $mknumsa[1];
			$flow = m('flow')->initflow($modenum, $mid, false);
			
			$ssid = $flow->addlog(array(
				'name' => '在线编辑'
			));
			
			$ufrs = $frs;
			$ufrs['filepath'] = $frs['oldfilepath'];
			unset($ufrs['oldfilepath']);
			unset($ufrs['id']);
			$ufrs['mtype']  = 'flow_log';
			$ufrs['mid'] 	= $ssid;
			$ufrs['mknum'] 	= ''.$modenum.'|'.$mid.'';
			$ufrs['filename'] 	= str_replace('.'.$ufrs['fileext'].'','(备份).'.$ufrs['fileext'].'', $ufrs['filename']);
			$fileobj->insert($ufrs); //记录原来的文件
			
			//不是我创建就告诉创建人
			if($this->adminid<>$frs['optid'])
				$flow->push($frs['optid'],'', ''.$this->adminname.'在线编辑文件“'.$frs['filename'].'”', '文件在线编辑');
			
			
			$flow->floweditoffice($frs, $ufrs);
			
		}else if($this->adminid<>$frs['optid']){ //不知道关联哪个模块
			$flow = m('flow')->initflow('word');
			$flow->push($frs['optid'],'文档', ''.$this->adminname.'在线编辑文件“'.$frs['filename'].'”', '文件在线编辑',0, array(
				'wxurl' => ''
			));
		}
		
		
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
	
	//记录预览记录
	public function logsAction()
	{
		$fileid = (int)$this->post('fileid',0);
		$type 	= (int)$this->post('type',0);
		m('file')->addlogs($fileid, $type);
	}
	
	
	/**
	*	发送编辑权限
	*/
	public function rockofficeeditAction()
	{
		$fileid = (int)$this->get('id');
		$lx 	= (int)$this->get('lx');
		$frs 	= m('file')->getone($fileid);
		if(!$frs)return returnerror('文件不存在了');
		$filepath = $frs['filepath'];
		
		if(substr($filepath,0,4)!='http' && !file_exists($filepath))return returnerror('文件不存在了1');
		
		$uptype = '|doc|docx|xls|xlsx|ppt|pptx|';
		if(!contain($uptype,'|'.$frs['fileext'].'|'))return returnerror('不是文档类型无法在线编辑');
		$filename 	= $frs['filename'];
		$utes		= 'edit';
		if($lx==1){
			$filename = '(只读)'.$filename.'';
			$utes     = 'yulan';
		}
		$arr	 = array();
		$arr[0]  = URL; 
		$arr[1]  = $filename;
		$arr[2]  = ''.md5(URL).'_'.$frs['filesize'].'_'.$fileid.'.'.$frs['fileext'].'';//生成键值
		$arr[3]  = $this->rock->gethttppath($filepath); //下载地址
		$arr[4]  = $fileid;
		$arr[5]  = $this->adminid;
		$arr[6]  = $this->token;
		$arr[7]  = $utes;
		$arr[8]  = $frs['fileext'];
		
		$str 	= '';
		foreach($arr as $s1)$str.=','.$s1.'';
		
		return returnsuccess(substr($str,1));
	}
}