<?php
class salaryClassAction extends Action
{
	public function biaoshiffAjax()
	{
		$sid = $this->post('sid');
		if($sid=='')return;
		m('flow')->initflow('hrsalary')->gongzifafang($sid);
	}
	
	public function createdataAjax()
	{
		$month = $this->post('month');
		if($month=='')return;
		$lastdt = c('date')->getenddt($month);
		if($lastdt>$this->date)exit(''.$month.'月份超前了');
		$barr 	= m('flow')->initflow('hrsalary')->createdata($month);
		echo $barr;
	}
	
	public function xinzlfafter($table, $rows)
	{
		$uids = '';
		foreach($rows as $k=>$rs){
			$uids.=','.$rs['xuid'].'';
		}
		if($uids!=''){
			$uids = substr($uids,1);
			$barr = $this->db->getarr('[Q]userinfo','id in('.$uids.')','`bankname`,`banknum`');
			foreach($rows as $k=>$rs){
				$brs = $this->rock->arrvalue($barr, $rs['xuid']);
				if($brs){
					$rows[$k]['bankname'] = $brs['bankname'];
					$rows[$k]['banknum'] = $brs['banknum'];
				}
			}
		}
		return array(
			'rows' => $rows
		); 
	}
}