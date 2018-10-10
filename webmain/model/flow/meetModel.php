<?php

class flow_meetClassModel extends flowModel
{
	public function initModel()
	{
		$this->hyarra 	= array('正常','会议中','结束','取消');
		$this->hyarrb 	= array('green','blue','#ff6600','#888888');
		$this->dbobj	= c('date');
	}
	
	public function flowrsreplace($rs, $lx=0)
	{
		$rs['week']  = $this->dbobj->cnweek($rs['startdt']);
		$zt 		 = $rs['state'];
		$nzt 		 = $zt;
		$time 		 = time();
		
		$stime 	= strtotime($rs['startdt']);
		$etime 	= strtotime($rs['enddt']);
		if($zt < 2){
			if($etime<$time){
				$nzt = 2;
			}else if($stime>$time){
				$nzt = 0;
			}else{
				$nzt = 1;
			}
		}
		
		if($zt != $nzt){
			$this->update('state='.$nzt.'', $rs['id']);
			$zt = $nzt;
		}
		
		$rs['ishui'] = ($zt>=2) ? 1 : 0;
		if($lx==1){
			$content 	 = '';
			$inpurl 	 = $this->getinputurl('meetjy',0,'def_mid='.$this->id.'');
			$rows 		 = $this->getrows('`mid`='.$this->id.' and `type`=2','id,content,optname,optdt,optid','id');
			//是否可以加会议纪要
			$dtss   = c('date')->adddate($this->rock->date,'d',-10).' 00:00:00';
			$addbo 	= $rs['startdt']>$dtss && $zt>0;
			$fobj   = m('file');
			foreach($rows as $k=>$rs1){
				$content.= '<div style="border-bottom:1px #cccccc solid;padding:5px">['.$rs1['optname'].']纪要';
				$inpurl1 = $this->getinputurl('meetjy',$rs1['id']);
				if($addbo && $rs1['optid']==$this->adminid)$content.= '&nbsp;<a href="'.$inpurl1.'" class="blue">[编辑]</a>';
				$content.= '：<br>'.$rs1['content'].'';
				$fstr 	 = $fobj->getstr('meet', $rs1['id'], 2);
				if($fstr!='')$content.= '<br>'.$fstr.'';
				$content.= '</div>';
			}
			
			if($addbo){
				 $content.='&nbsp;<a href="'.$inpurl.'" class="blue">＋新增纪要</a>';
			}
			$rs['content']= $content;
			$rs['content_style'] = 'padding:0px';
		}
		$rs['state'] = $this->getstatezt($zt);
		$rs['nzt']	 = $zt;
		if(isset($rs['issms'])){
			$issms 		 = '否';
			if($rs['issms']==1)$issms = '是';
			$rs['issms'] = $issms;
		}
		return $rs;
	}
	
	public function getstatezt($zt)
	{
		return '<font color="'.$this->hyarrb[$zt].'">'.$this->hyarra[$zt].'</font>';
	}
	
	protected function flowsubmit($na, $sm)
	{
		if($this->rs['status']==1){
			$this->tisongtodo();
		}
	}
	
	//审核完成后发通知
	protected function flowcheckfinsh($zt)
	{
		if($zt==1)$this->tisongtodo();
	}
	
	private function tisongtodo()
	{
		//$cont  = '{optname}发起会议预定从{startdt}→{enddt},在{hyname},主题:{title}';
		//$start = date('Y年m月d日H:s',strtotime($this->rs['startdt']));
		//$end = date('Y年m月d日H:s',strtotime($this->rs['enddt']));
		//$end = $this->dbobj->stringdt($this->rs['enddt']);
		$cont  = '{optname}发起会议“{title}”在{hyname}，时间{startdt}至{enddt}';
		$this->push($this->rs['joinid'], '会议', $cont);
		
		$this->sendsms($this->rs, 'meetapply', array(
			'optname' 	=> $this->adminname,
			'title' 	=> $this->rs['title'],
			'hyname' 	=> $this->rs['hyname'],
			'startdt' 	=> $this->rs['startdt'],
			'enddt' 	=> $this->rs['enddt'],
		));
	}
	
	protected function flowaddlog($a)
	{
		$actname = $a['name'];
		if($actname == '取消会议'){
			$this->push($this->rs['joinid'], '会议', ''.$this->adminname.'取消会议“{title}”，时间{startdt}至{enddt}，请悉知。');
			$this->update('`state`=3', $this->id);
			
			$this->sendsms($this->rs, 'meetcancel', array(
				'optname' 	=> $this->adminname,
				'title' 	=> $this->rs['title'],
				'hyname' 	=> $this->rs['hyname'],
				'startdt' 	=> $this->rs['startdt'],
				'enddt' 	=> $this->rs['enddt'],
			));
		}
		if($actname == '结束会议'){
			$this->update('`state`=2', $this->id);
		}
	}
	
	//发短信提醒
	public function sendsms($rs, $tplnum, $params)
	{
		$receid = $rs['joinid'];
		$issms  = arrvalue($rs,'issms');
		
		if(isempt($receid) || $issms!='1')return;
		$jyid	= $rs['jyid'];
		if(!isempt($jyid))$receid.=','.$jyid.''; //发个纪要人
		
		$qiannum= ''; //签名编号，可以为空
		$barr = c('xinhuapi')->sendsms($receid, $qiannum, $tplnum, $params);
		return $barr;
	}
	
	protected function flowbillwhere($uid, $lx)
	{
		$dt 	= $this->rock->post('dt');
		$where 	= '';
		if($dt!='')$where.=" and startdt like '$dt%'";
		$fields	= 'id,startdt,enddt,optname,state,title,hyname,joinname,`explain`,jyname';
		return array(
			'fields' => $fields,
			'where'	 => $where,
			'order' => 'startdt desc'
		);
	}
}