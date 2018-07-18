<?php
class fileClassModel extends Model
{
	
	public function initModel()
	{
		$this->fileall = ',aac,ace,ai,ain,amr,app,arj,asf,asp,aspx,av,avi,bin,bmp,cab,cad,cat,cdr,chm,com,css,cur,dat,db,dll,dmv,doc,docx,dot,dps,dpt,dwg,dxf,emf,eps,et,ett,exe,fla,ftp,gif,hlp,htm,html,icl,ico,img,inf,ini,iso,jpeg,jpg,js,m3u,max,mdb,mde,mht,mid,midi,mov,mp3,mp4,mpeg,mpg,msi,nrg,ocx,ogg,ogm,pdf,php,png,pot,ppt,pptx,psd,pub,qt,ra,ram,rar,rm,rmvb,rtf,swf,tar,tif,tiff,txt,url,vbs,vsd,vss,vst,wav,wave,wm,wma,wmd,wmf,wmv,wps,wpt,wz,xls,xlsx,xlt,xml,zip,';
		
		$this->mimitype =  c('file')->getAllMime();
	}
	
	public function getmime($lx)
	{
		if(!isset($this->mimitype[$lx]))$lx = 'unkown';
		return $this->mimitype[$lx];
	}
	
	public function getfile($mtype, $mid, $where='')
	{
		if($where=='')$where = "`mtype`='$mtype' and `mid` in($mid)";
		$rows	= $this->getall("$where order by `id`",'id,`mid`,filename,filepath,filesizecn,filesize,fileext,optname');
		return $rows;
	}
	
	public function addfile($fileid, $mtype, $mid)
	{
		if(!$this->isempt($fileid)){
			$this->update("`mtype`='$mtype',`mid`='$mid'", "`id` in($fileid) and `mid`=0");
		}
	}
	
	public function getstr($mtype, $mid, $lx=0, $where='')
	{
		$filearr 	= $this->getfile($mtype, $mid, $where);
		$fstr		= $this->getstrstr($filearr, $lx);
		return $fstr;
	}
	
	public function getstrstr($filearr, $lx)
	{
		$fstr		= '';
		if($filearr)foreach($filearr as $k=>$rs){
			if($k>0)$fstr.='<br>';
			$fstr .= $this->getfilestr($rs, $lx);
		}
		return $fstr;
	}
	
	//获取聚合文件
	public function getallstr($filearr, $mid, $lx=0)
	{
		$farr = array();
		if($filearr)foreach($filearr as $k=>$rs){
			if($rs['mid']==$mid)$farr[] = $rs;
		}
		return $this->getstrstr($farr, $lx);
	}
	
	public function isimg($ext)
	{
		return $this->contain('|jpg|png|gif|bmp|jpeg|', '|'.$ext.'|');
	}
	
	public function getfilestr($rs, $lx=0)
	{
		$fstr= '';
		if(!$rs)return $fstr;
		$str = $this->rock->jm->strrocktoken(array('a'=>'down','id'=>$rs['id']));
		$url = ''.URL.'index.php?rocktoken='.$str.'';
		$str = 'href="'.$url.'" target="_blank"';
		$strd= $str;
		if($lx==1)$str='href="javascript:;" onclick="return js.downshow('.$rs['id'].')"';
		if($lx==2){
			$paths = $rs['filepath'];
			if(!$this->isimg($rs['fileext']))$paths='';
			$str='href="javascript:;" onclick="return c.downshow('.$rs['id'].',\''.$rs['fileext'].'\',\''.$paths.'\')"';//详情上预览
		}
		$flx   = $rs['fileext'];
		if(!$this->contain($this->fileall,','.$flx.','))$flx='wz';
		$str1  = '';
		$fstr .='<img src="'.URL.'web/images/fileicons/'.$flx.'.gif" align="absmiddle" height=16 width=16> <a '.$str.' style="color:blue;">'.$rs['filename'].'</a>';
		if($lx==2 && !$this->rock->ismobile()){
			$fstr .= ' <a '.$strd.' class="zhu">[下载]</a>';
		}
		$fstr .= ' ('.$rs['filesizecn'].''.$str1.')';
		return $fstr;
	}
	
	public function getfiles($mtype, $mid)
	{
		$rows		= $this->getall("`mtype`='$mtype' and `mid`='$mid' order by `id`");
		foreach($rows as $k=>$rs){
			$rows[$k]['status'] = 4;
		}
		return $rows;
	}
	
	public function getfilepath($mtype, $mid)
	{
		$rows		= $this->getfiles($mtype, $mid);
		$str 		= '';
		$nas 		= '';
		foreach($rows as $k=>$rs){
			$path = $rs['filepath'];
			if(!isempt($path) && file_exists($path)){
				$str .= ','.$path.'';
				$nas .= ','.$rs['filename'].'';
			}
		}
		if($str!=''){
			$str = substr($str, 1);
			$nas = substr($nas, 1);
		}
		return array($str, $nas);
	}
	
	public function copyfile($mtype, $mid)
	{
		$rows	= $this->getall("`mtype`='$mtype' and `mid`='$mid' order by `id`");
		$arr 	= array();
		foreach($rows as $k=>$rs){
			$inuar  = $rs;
			if(isempt($rs['filepath']) || !file_exists($rs['filepath']))continue;
			unset($inuar['id']);
			$oid	= $rs['id'];
			$inuar['adddt'] 	= $this->rock->now;
			$inuar['optid'] 	= $this->adminid;
			$inuar['optname'] 	= $this->adminname;
			$inuar['downci'] 	= '0';
			$inuar['mtype'] 	= '';
			$inuar['mid'] 		= '0';
			$inuar['oid'] 		= $oid;
			
			$ids 				= (int)$this->getmou('id','oid='.$oid.' and `mid`=0');
			if($ids==0){
				$this->insert($inuar);
				$inuar['id'] = $this->db->insert_id();
			}else{
				$inuar['id'] = $ids;
			}
			$inuar['status'] = 4;
			$arr[] = $inuar;
		}
		return $arr;
	}
	
	public function delfiles($mtype, $mid)
	{
		$where = "`mtype`='$mtype' and `mid`='$mid'";
		$this->delfile('', $where);
	}
	
	public function delfile($sid='', $where='')
	{
		if($sid!='')$where = "`id` in ($sid)";
		if($where=='')return;
		$rows 	= $this->getall($where, 'filepath,thumbpath,pdfpath');
		foreach($rows as $k=>$rs){
			$path = $rs['filepath'];
			if(!$this->isempt($path) && file_exists($path))unlink($path);
			$path = $rs['thumbpath'];
			if(!$this->isempt($path) && file_exists($path))unlink($path);
			$path = $rs['pdfpath'];
			if(!$this->isempt($path) && file_exists($path))unlink($path);
		}
		$this->delete($where);
	}
	
	public function fileheader($filename,$ext='xls')
	{
		$mime 		= $this->getmime($ext);
		$filename 	= $this->iconvutf8(str_replace(' ','',$filename));
		header('Content-type:'.$mime.'');
		header('Accept-Ranges: bytes');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: no-cache');
		header('Expires: 0');
		header('Content-disposition:attachment;filename='.$filename.'');
		header('Content-Transfer-Encoding: binary');
	}
	
	//渣渣IE才需要转化，真是醉了
	public function iconvutf8($text) {
		if(contain($this->rock->web,'IE')){
			return iconv('utf-8','gb2312', $text);
		}else{
			return $text;
		} 
    }
	
	public function show($id)
	{
		if($id==0)exit('Sorry!');
		$rs	= $this->getone($id);
		if(!$rs)exit('504 Not find files');
		$this->update("`downci`=`downci`+1", $id);
		$filepath	= $rs['filepath'];
		$filename	= $rs['filename'];
		$filesize 	= $rs['filesize'];
		$fileext 	= $rs['fileext'];
		if($this->rock->contain($filepath,'http')){
			header('location:'.$filepath.'');
		}else{
			//2018-07-18只能下载upload/images下的文件
			$ielx  = substr($filepath,0,strlen(UPDIR));
			$ielx1 = substr($filepath,0,6);
			if($ielx!=UPDIR && $ielx1!='upload' && $ielx1!='images')exit('无效操作1');
			
			if(!file_exists($filepath))exit('404 Not find files');
			if(!contain($filename,'.'.$fileext.''))$filename .= '.'.$fileext.'';
			$this->fileheader($filename, $fileext);
			if(substr($filepath,-4)=='temp'){
				$content	= file_get_contents($filepath);
				echo base64_decode($content);
			}else{
				if($rs['filesize'] > 5*1024*1024){
					header('location:'.$filepath.'');
				}else{
					echo file_get_contents($filepath);
				}
			}
		}
	}
	
	public function download($id)
	{
		if($id==0)exit('Sorry!');
		$rs	= $this->getone($id);
		if(!$rs)exit('504 Not find files');
		$this->update("`downci`=`downci`+1", $id);
		$filepath	= $rs['filepath'];
		
		$ielx  = substr($filepath,0,strlen(UPDIR));
		$ielx1 = substr($filepath,0,6);
		if($ielx!=UPDIR && $ielx1!='upload' && $ielx1!='images')exit('无效操作2');
		
		if(!file_exists($filepath))exit('404 Not find files');
		$filename	= $rs['filename'];
		$filesize 	= $rs['filesize'];
		if(substr($filepath,-4)=='temp'){
			Header("Content-type: application/octet-stream");
			header('Accept-Ranges: bytes');
			Header("Accept-Length: ".$filesize);
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: no-cache');
			header('Expires: 0');
			$content	= file_get_contents($filepath);
			echo base64_decode($content);
		}
	}
}