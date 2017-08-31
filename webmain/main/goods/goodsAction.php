<?php
class goodsClassAction extends Action
{
	public function aftershow($table, $rows)
	{
		$typearr = array();
		if($rows)foreach($rows as $k=>$rs){
			$tid = $rs['typeid'];
			if(isset($typearr[$tid])){
				$rows[$k]['typeid']= $typearr[$tid];
			}else{
				$rows[$k]['typeid']		= $this->db->getpval('[Q]option','pid','name', $tid,'/','id',2);
				$typearr[$tid] = $rows[$k]['typeid'];
			}
		}
		$type	= (int)$this->post('type');
		$typearr= $this->option->getdata('kutype'.$type.'');
		return array('rows' => $rows,'typearr'=>$typearr);
	}
	
	public function beforeshow($table)
	{
		$key 	= $this->post('key');
		$typeid = (int)$this->post('typeid');
		$where 	 	= '';
		if($typeid != 0){
			$alltpeid = $this->option->getalldownid($typeid);
			$where .= ' and `typeid` in('.$alltpeid.')';
		}
		if($key!=''){
			$where .= " and (`name` like '%$key%') ";
		}
		return $where;
	}
	
	public function xiangbeforeshow($table)
	{
		$key = $this->post('key');
		$dt  = $this->post('dt');
		$typeid  = (int)$this->post('typeid', 0);
		
		$where 	 = '';
		if($typeid>0){
			$alltpeid = $this->option->getalldownid($typeid);
			$where.=" and b.typeid in($alltpeid)";
		}
		if($key!=''){
			$where .= " and (b.`name` like '%$key%' or a.optname  like '%$key%' )";
		}
		if($dt!=''){
			$where .= " and a.`applydt` like '$dt%' ";
		}
		
		$table	= '`[Q]goodss` a left join `[Q]goods` b on a.aid=b.id';
		$fields	= 'a.id,b.name,a.count,a.type,a.kind,a.status,a.optname,b.typeid,a.applydt,a.explain,a.mid';
		return array(
			'where' => $where,
			'table' => $table,
			'fields' => $fields,
		);
	}
	
	public function xiangaftershow($table, $rows)
	{
		if($rows){
			$typearr0= $this->option->getdata('kutype0');
			$typearr1= $this->option->getdata('kutype1');
			$tyeparr = array();
			foreach($typearr0 as $k=>$rs)$tyeparr['a0_'.$rs['value'].''] = $rs['name'];
			foreach($typearr1 as $k=>$rs)$tyeparr['a1_'.$rs['value'].''] = $rs['name'];
			$statusar= array('<font color=blue>待审核</font>','<font color=green>已审核</font>','<font color=red>审核未通过</font>');
			$typearr = array();
			
			foreach($rows as $k=>$rs){
				
				$tid = $rs['typeid'];
				if(isset($typearr[$tid])){
					$rows[$k]['typeid'] = $typearr[$tid];
				}else{
					$rows[$k]['typeid']	= $this->db->getpval('[Q]option','pid','name', $tid,'/','id',2);
					$typearr[$tid] = $rows[$k]['typeid'];
				}
				
				$skey = 'a'.$rs['type'].'_'.$rs['kind'].'';
				$kind = '';
				if(isset($tyeparr[$skey]))$kind = $tyeparr[$skey];
				$rows[$k]['kind']	= $kind;
				$rows[$k]['status']	= $statusar[$rs['status']];
				if($rs['mid']>0)$rows[$k]['checkdisabled'] = true;//有主表ID，不能删除
			}
		}
		return array('rows' => $rows);
	}
	
	/**
	*	删除出入库详情
	*/
	public function delxiangAjax()
	{
		$ids	= $this->post('id','0');
		m('goodss')->delete("id in($ids) and `mid`=0");
		backmsg();
	}
	
	public function chukuoptAjax()
	{
		$dt 	= $this->post('dt');
		$type 	= (int)$this->post('type');
		$kind 	= (int)$this->post('kind');
		$sm 	= $this->post('sm');
		$cont 	= $this->post('cont');
		$sharr	= c('array')->strtoarray($cont);
		$arr['applydt'] = $dt;
		$arr['type'] 	= $type;
		$arr['kind'] 	= $kind;
		$arr['explain'] = $sm;
		$arr['uid'] 	= $this->adminid;
		$arr['optid'] 	= $this->adminid;
		$arr['optdt'] 	= $this->now;
		$arr['optname'] = $this->adminname;
		$arr['status'] 	= 1;
		$aid 			= '0';
		foreach($sharr as $k=>$rs){
			$arr['aid'] = $rs[0];
			$count = $rs[1];
			if($type==1)$count = 0-$count;
			$arr['count'] = $count;
			$this->db->record('[Q]goodss', $arr);
			$aid.=','.$rs[0].'';
		}
		if($aid!='0')m('goods')->setstock($aid);
		echo 'success';
	}
	
	
	
	
	
	
	
	
	//2017-08-20 后弃用了
	public function addplgoodsAjax()
	{
		$rows  	= c('html')->importdata('name,typeid,price,unit,guige,xinghao,stockcs','name,typeid');
		$oi 	= 0;
		$db 	= m('goods');
		foreach($rows as $k=>$rs){
			$rs['typeid'] 	= $this->option->gettypeid('goodstype',$rs['typeid']);
			
			//判断是否存在
			$odi 			= $db->existsgoods($rs);
			if($odi)continue;
			
			$rs['price']	= floatval($this->rock->repempt($rs['price'],'0')); //金额
			$rs['stockcs']	= (int)$this->rock->repempt($rs['stockcs'],'0'); //初始库存
			$rows[$k]		= $rs;
			$rs['adddt']	= $this->now;
			$rs['optdt']	= $this->now;
			$rs['optid']	= $this->adminid;
			$rs['optname']	= $this->adminname;
			$db->insert($rs);
			$oi++;
		}
		$this->reloadkcAjax();
		backmsg('','成功导入'.$oi.'条数据');
	}
	
	//刷新库存
	public function reloadkcAjax()
	{
		m('goods')->setstock();
	}
}