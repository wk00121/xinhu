<?php
/**
*	对接中控考勤机
* 	请求地址如：http://127.0.0.1/app/xinhu/api.php?m=openzktime&a=get
*/
class openzktimeClassAction extends openapiAction
{
	private $snid = 0;
	private $snrs;
	
	private function getsnid()
	{
		$num	= $this->get('sn'); //设备号
		$snid	= 0;
		if(!isempt($num)){
			$dbs 	= m('kqjsn');
			$snid	= (int)$dbs->getmou('id',"`num`='$num'");
			if($snid==0)$snid = $dbs->insert(array(
				'num' 		=> $num,
				'optdt' 	=> $this->rock->now,
				'status' 	=> 1
			));
			$this->snid = $snid;
			$this->snrs	= $dbs->getone($snid);
		}
		return $snid;
	}
	
	public function testAction()
	{
		return 'ok';
	}
	
	/**
	*	考勤机定时请求命令
	*/
	public function getAction()
	{
		$snid	= $this->getsnid();
		if($snid==0)return 'notdata';

		$data= m('kqjcmd')->getcmd($snid);
		if($data){
			$batr = array();
			foreach($data as $k=>$bar){
				if(!isset($bar['data']))$bar['data']='none';
				$bar['atype'] = $bar['do'];
				unset($bar['do']);
				$batr[] = json_encode($bar, JSON_UNESCAPED_UNICODE);
			}
			$data = join('ROCKZK', $batr);
		}
		
		return $data;
	}
	
	/**
	*	命令状态更新
	*/
	public function getcAction()
	{
		$this->getsnid();
		$id = $this->get('id');
		if(isempt($id))return;
		$status = $this->get('status');
		$cmds 	= m('kqjcmd');
		$cmds->update(array(
			'status'=>$status,
			'cjtime'=>$this->rock->now
		), $id);
		
		$cmds->returnchuli($id, $this->snid);
	}
	
	/**
	*	中控考勤机插件推送提交过来数据
	*/
	public function postAction()
	{
		$this->getsnid();
		$atype 	= $this->get('atype');
		$str 	= $this->postdata;
		if(isempt($str))return 'not data';
		$arr 		= json_decode($str, true);
		$fenxiarr	= array();
		
		//打卡
		if($atype=='daka'){
			$db 	= m('kqdkjl');
			$kqcmd	= m('kqjcmd');
			$kqcmd->getsninfo($this->snid);
			$type 	= 9;
			$oi 	= 0;
			$ids 	= '';
			foreach($arr as $k=>$rs){
				$barr =  $kqcmd->adddkjl($this->snid, $rs, $type, $db);
				$oi++;
				foreach($barr as $k1=>$v1)$fenxiarr[$k1] = $v1;
				$ids .= ','.$rs['id'].'';
			}
			//考勤分析
			if($fenxiarr){
				$kqobj = m('kaoqin');
				foreach($fenxiarr as $keys=>$uid){
					$keysa = explode('|', $keys);
					$kqobj->kqanay($uid, $keysa[0]);
				}
			}
			
			if($ids!='')$ids = substr($ids,1);
			return array(
				'msg' => 'upload add('.$oi.')record',
				'ids' => $ids
			);
		}
		
		//已存在用户Id
		if($atype=='user'){
			$uids = $arr['uids'];
			m('kqjsn')->update(array(
				'userids' => $uids
			), $this->snid);
			echo '上传用户id成功';
		}
		
		//上传设备信息
		if($atype=='info'){
			m('kqjsn')->update(array(
				'usershu' => $arr['usershu'],
				'fingerprintshu' => $arr['fingerprintshu'],
				'clockinshu' => $arr['clockinshu'],
			), $this->snid);
			echo '上传设备信息成功';
		}
		
		//上传指纹模版
		if($atype=='fingerprint'){
			$kqjdb = m('kqjcmd');
			foreach($arr as $k=>$rs){
				$kqjdb->savefingerprint($this->snid, $rs['uid'], array($rs['fingerprint1'],$rs['fingerprint2']));
			}
			echo '上传指纹模版成功';
		}
		
	}
}