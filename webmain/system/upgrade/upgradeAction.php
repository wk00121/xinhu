<?php
class upgradeClassAction extends Action
{
	public function initAction()
	{
		$this->keyss = $this->option->getval('systemnum');
		if(isempt($this->keyss)){
			$rnd  	= md5(str_shuffle('abcedfghijk').rand(1000,9999));
			$this->option->setval('systemnum', $rnd);
			$this->keyss = $rnd;
		}
	}
	
	public function dataAjax()
	{
		$xinhu	= c('xinhu');
		$db 	= m('chargems');
		$wet	= $xinhu->getwebsite();
		$barr	= $xinhu->getdata('mode');
		if($barr['code']!=200)exit($barr['msg']);
		$rows  	= array();
		foreach($barr['data'] as $k=>$rs){
			$id = $rs['id'];
			$state  = 0;
			$key	= '';
			$ors 	= $db->getone("`type`=0 and `mid`='$id'");
			if($ors){
				$state = 1;
				if($rs['updatedt']>$ors['updatedt'])$state=2;
				$key= $ors['key'];
			}
			$view = '<a href="'.$wet.'view_'.$rs['num'].'.html" target="_blank" class="a">介绍</a>';
			if($state==0 && $rs['price']>0)$view.=',<a href="'.$wet.'view_'.$rs['num'].'.html" target="_blank" style="color:red">去购买</a>';
			$rows[] = array(
				'id' 	=> $id,
				'name' 	=> $rs['name'],
				'price' => $rs['price'],
				'isaz'  => $rs['isaz'],
				'explain' 	=> $rs['explain'],
				'updatedt' 	=> $rs['updatedt'],
				'key'		=> $key,
				'view' 	=> $view,
				'opt'	=> $state
			);
		}
		$arr['rows'] = $rows;
		$this->returnjson($arr);
	}
	
	/**
	*	获取需要更新的文件
	*/
	public function shengjianAjax()
	{
		$id 	= $this->post('id');
		$key 	= $this->post('key');
		$barr	= c('xinhu')->getdata('getinstallfile',array('id'=>$id,'sysnum'=>$this->keyss,'key'=>$key));
		if($barr['code']!=200)showreturn('',$barr['msg'],201);
		$data 	= $barr['data'];
		$mrs 	= $data['rs'];
		$rows 	= $data['rows'];
		$uparr  = array();
		$huira  = $this->gethuiarr($id);
		
		//获取模块忽略升级文件，停用也不升级
		$morrs	= $this->getmodeuprs();
		
		foreach($rows as $k=>$rs){
			$file 	= $rs['filepath'];
			$bo 	= true;
			if(file_exists($file)){
				$_size = filesize($file);
				if($_size == $rs['filesize'])$bo = false;
			}
			if($rs['isup']==1)$bo = true;
			if(isset($huira[$rs['id']]))$bo = false;
			
			//如果模块忽略升级，就不升级。
			if(in_array($file, $morrs))$bo = false;
			
			if($bo){
				$uparr[] = $rs;
			}
		}
		if(!$uparr){
			$this->upsueecc($id, $mrs['updatedt'], $key);
		}
		showreturn($uparr);
	}
	private function upsueecc($id, $updatedt, $key)
	{
		$where  = "`type`=0 and `mid`='$id'";
		$db 	= m('chargems');
		if($db->rows($where)==0)$where='';
		$db->record(array(
			'type' 		=> 0,
			'mid'  		=> $id,
			'modeid'  	=> $id,
			'updatedt'  => $updatedt,
			'optdt' 	=> $this->now,
			'key'		=> $this->jm->encrypt($key)
		),$where);
	}
	private function upsueeccmids($modeid, $mid, $updatedt, $type=1)
	{
		$where  = "`type`='$type' and `mid`='$mid'";
		$db 	= m('chargems');
		if($db->rows($where)==0)$where='';
		$db->record(array(
			'type' 		=> $type,
			'mid'  		=> $mid,
			'modeid'  	=> $modeid,
			'updatedt'  => $updatedt,
			'optdt' 	=> $this->now,
		),$where);
	}
	
	//获取模块忽略升级文件，停用也不升
	private function getmodeuprs()
	{
		$rows = m('flow_set')->getall('1=1', '`num`,`status`,`isup`','`sort`');
		$hurs = array();
		foreach($rows as $k=>$rs){
			$num = $rs['num'];
			//停用就不升级了
			if($rs['status']=='0' || $rs['isup']=='0'){
				$hurs[] = ''.P.'/flow/input/inputjs/mode_'.$num.'.js'; //模块录入js文件
				$hurs[] = ''.P.'/flow/input/mode_'.$num.'Action.php'; //模块控制器
				$hurs[] = ''.P.'/flow/page/input_'.$num.'.html'; //PC录入模版
				$hurs[] = ''.P.'/flow/page/view_'.$num.'_0.html'; //PC展示模版
				$hurs[] = ''.P.'/flow/page/view_'.$num.'_1.html'; //手机展示模版
				$hurs[] = ''.P.'/flow/page/viewpage_'.$num.'.html'; //子模版展示
				$hurs[] = ''.P.'/flow/page/viewpage_'.$num.'_0.html';//子模版PC展示
				$hurs[] = ''.P.'/flow/page/viewpage_'.$num.'_1.html';//子模版手机展示
				$hurs[] = ''.P.'/flow/page/rock_page_'.$num.'.php'; //列表页面
				$hurs[] = ''.P.'/model/flow/'.$num.'Model.php'; //模块接口文件
			}
		}
		return $hurs;
	}
	
	//弃用
	public function shengjianssAjax()
	{
		$mid 	= (int)$this->post('id');
		$fileid	= (int)$this->post('fileid');
		$key 	= $this->post('key');
		$oi 	= $this->post('oii');
		$len 	= $this->post('lens');
		$updatedt = $this->post('updatedt');
		$upbo 	= true;
		$ors 	= m('chargems')->getone("`type`=1 and `mid`='$fileid'");
		if($ors && $updatedt<=$ors['optdt'])$upbo = false;
		if(isempt($updatedt))$upbo = true;
		if($upbo){
			$barr	= c('xinhu')->getdata('getinstallfileid',array('fid'=>$fileid,'sysnum'=>$this->keyss,'key'=>$key));
			if($barr['code'] != 200)exit($barr['msg']);
			$data 	= $barr['data'];
			$type 	= $data['type'];
			$filepath = $data['filepath'];
			$fcont 	= $this->jm->base64decode($data['content']);
			if($fcont != ''){
				if($type==1){
					$bmsg = m('beifen')->updatefabric($fcont);
					if($bmsg != 'ok')exit($bmsg);
					$this->upsueeccmids($mid, $fileid, $updatedt, 1);
				}
				if($type==9){
					$this->rock->createdir($filepath);
					$this->beifenfile($filepath);//备份原来的文件到upload/当前月份
					@$bo = file_put_contents($filepath, $fcont);
					if(!$bo)exit('无法写入：'.$filepath.'');
				}
			}
			$udt = $data['updatedt'];
		}else{
			$udt = $this->now;
		}
		if($oi+1 == $len){
			$this->upsueecc($mid, $udt, $key);
		}
		echo 'ok';
	}
	
	//备份原来的文件
	private function beifenfile($path)
	{
		if(!file_exists($path))return;
		$wz 	= strripos($path, '/');
		$dir 	= '';
		if($wz===false){
			$file = $path;
		}else{
			$dir  = substr($path, 0, $wz);
			$file = substr($path, $wz+1);
		}
		$dir	= str_replace('/','-', $dir);
		$nfile 	= ''.$dir.''.date('YmdHis').'@'.$file.'';
		$topath = ''.UPDIR.'/logs/'.date('Y-m').'/'.$nfile.'';
		$this->rock->createdir($topath);
		@copy($path, $topath);
	}
	
	private function shengjifile($frs, $key, $modeid)
	{
		$fid 	= $frs['id'];
		$where  = "`type`=1 and `mid`='$fid'";
		$db 	= m('chargems');
		$ors 	= $db->getone($where);
		if($ors){
			if($frs['updatedt']<=$ors['optdt'])return;
		}else{
			$where = '';
		}
		$barr	= c('xinhu')->getdata('getfileinstall',array('id'=>$fid,'sysnum'=>$this->keyss,'key'=>$key));
		if($barr['code']!=200)exit($barr['msg']);
		$fcont 	= $this->jm->base64decode($barr['data']);
		if(isempt($fcont))return;
		$mkdir	= ''.UPDIR.'/'.date('Y-m');
		if($frs['type']==0){
			if(!is_dir($mkdir))mkdir($mkdir);
			$filemy = $mkdir.'/install'.time().rand(1000,9999).'.zip';
			file_put_contents($filemy, $fcont);
			$bmsg = c('zip')->unzip($filemy,'./');
			unlink($filemy);
			if($bmsg != 'ok')exit($bmsg);
		}
		if($frs['type']==1){
			$bmsg = m('beifen')->updatefabric($fcont);
			if($bmsg != 'ok')exit($bmsg);
		}
		$db->record(array(
			'type' 		=> 1,
			'mid'  		=> $fid,
			'modeid'  	=> $modeid,
			'updatedt'  => $frs['updatedt'],
			'optdt' 	=> $this->now,
		),$where);
	}
	
	
	public function tontbudataAjax()
	{
		$lx = (int)$this->get('lx');
		$barr	= c('xinhu')->getdata('getaneydata', array('lx'=>$lx));
		if($barr['code']!=200)exit($barr['msg']);
		$data 	= $barr['data'];
		if($lx==0)$this->tonbbumenu($data['menu']);
		if($lx==1)$this->tonbbumode($data['mode']);
		if($lx==4)$this->tonbbumodewq($data['mode']);//完全和官网一样
		if($lx==2)$this->tonbbuying($data['yydata']);
		if($lx==3)$this->tonbbutask($data['task']);
		
		echo '同步完成';
	}
	
	//同步菜单
	private function tonbbumenu($data)
	{
		$db = m('menu');
		foreach($data as $k=>$rs){
			$id = $rs['id'];
			if($db->rows('id='.$id.'')>0){
				unset($rs['status']);
				unset($rs['ispir']);
				unset($rs['ishs']);
				$db->update($rs, 'id='.$id.'');
			}else{
				$db->insert($rs);
			}
		}
	}
	
	//同步模块
	private function tonbbumode($data)
	{
		$db 	= m('flow_set');
		$db1 	= m('flow_element');
		$db2 	= m('flow_menu');
		$db3 	= m('flow_extent');
		$db5 	= m('flow_course');
		$db6 	= m('flow_where');
		foreach($data as $num=>$arr){
			$moders 	= $db->getone("`num`='$num'",'`id`,`isup`');
			$modeid		= 0;
			$isup		= 1;
			if($moders){
				$modeid	= (int)$moders['id'];
				$isup	= (int)$moders['isup'];
				if($isup==0)continue;
			}
			
			$flow_set 	= $arr['flow_set'];
			if(isset($flow_set['id']))unset($flow_set['id']);
			$isadd		= false;
			if($modeid==0){
				$modeid = $db->insert($flow_set);
				$isadd	= true;
			}else{
				$db->update(array(
					'where' 	=> $flow_set['where'],
					'sort' 		=> $flow_set['sort'],
					'type' 		=> $flow_set['type'],
					'summary' 	=> $flow_set['summary'],
					'summarx' 	=> $flow_set['summarx'],
					'tables' 	=> $flow_set['tables'],
					'names' 	=> $flow_set['names'],
					'isscl' 	=> $flow_set['isscl'],
					'statusstr' => $flow_set['statusstr']
				), $modeid);
				/*
				unset($flow_set['pctx']);
				unset($flow_set['mctx']);
				unset($flow_set['wxtx']);
				unset($flow_set['emtx']);
				unset($flow_set['isup']);
				unset($flow_set['receid']);
				unset($flow_set['recename']);
				unset($flow_set['status']);
				$db->update($flow_set, $modeid);*/
			}
			
			//流程模块条件
			$flow_where = $arr['flow_where'];
			foreach($flow_where as $k6=>$rs6){
				$rs6['setid'] = $modeid;
				if(isset($rs6['id']))unset($rs6['id']);
				$num 			= $rs6['num'];
				if(isempt($num))continue;
				$where 			= "`setid`='$modeid' and `num`='$num'";
				if($db6->rows($where)==0){
					$db6->insert($rs6);
				}else{
					$db6->update($rs6, $where);
				}
			}
			
			//if($isup==0)continue; //不同步更新，就跳过
			
			
			//字段
			$flow_element= $arr['flow_element'];
			foreach($flow_element as $k1=>$rs1){
				$rs1['mid'] = $modeid;
				if(isset($rs1['id']))unset($rs1['id']);
				$where 		= "`mid`='$modeid' and `fields`='".$rs1['fields']."' and `iszb`='".$rs1['iszb']."'";
				if($db1->rows($where)==0){
					$db1->insert($rs1);
				}else{
					unset($rs1['name']);
					$db1->update($rs1, $where);
				}
			}
			
			//权限
			$flow_extent= $arr['flow_extent'];
			foreach($flow_extent as $k3=>$rs3){
				$rs3['modeid'] = $modeid;
				$sid  = $rs3['id'];
				if($db3->rows('id='.$sid.'')>0){
					$db3->update($rs3, 'id='.$sid.'');
				}else{
					$db3->insert($rs3);
				}
			}
			
			//操作菜单
			$flow_menu= $arr['flow_menu'];
			if($flow_menu){
				$sids 	  = '0';
				foreach($flow_menu as $k2=>$rs2){
					$rs2['setid'] = $modeid;
					$sid  = $rs2['id'];
					if($db2->rows('id='.$sid.'')>0){
						$sids.=','.$sid.'';
						$db2->update($rs2, 'id='.$sid.'');
					}else{
						$db2->insert($rs2);
						$sids.=','.$this->db->insert_id().'';
					}
				}
				$db2->delete("`setid`='$modeid' and `id` not in($sids)");
			}
			
			//审核步骤
			if(isset($arr['flow_course'])){
				if($db5->rows('setid='.$modeid.'')==0){
					$flow_course = $arr['flow_course'];
					foreach($flow_course as $k5=>$rs5){
						if(isset($rs5['id']))unset($rs5['id']);
						$rs5['setid'] = $modeid;
						$db5->insert($rs5);
					}
				}
			}
			
		}
	}
	
	//跟官网完全一样同步模块
	private function tonbbumodewq($data)
	{
		$db 	= m('flow_set');
		$this->initstalltable('flow_set');
		$this->initstalltable('flow_element');
		$this->initstalltable('flow_menu');
		$this->initstalltable('flow_extent');
		$this->initstalltable('flow_course');
		$this->initstalltable('flow_where');
		
		foreach($data as $num=>$arr){
			$flow_set 		= $arr['flow_set'];
			$flow_element 	= $arr['flow_element'];
			$flow_menu 		= $arr['flow_menu'];
			$flow_extent 	= $arr['flow_extent'];
			$flow_course 	= $arr['flow_course'];
			$flow_where 	= $arr['flow_where'];
			
			
			$db->insert($flow_set);
			
			$this->insertdata($flow_element, 'flow_element');
			$this->insertdata($flow_menu, 'flow_menu');
			$this->insertdata($flow_extent, 'flow_extent');
			$this->insertdata($flow_course, 'flow_course');
			$this->insertdata($flow_where, 'flow_where');
		}
	}
	private function initstalltable($table)
	{
		$sql1 = "delete from `[Q]".$table."`";
		$sql2 = "alter table `[Q]".$table."` AUTO_INCREMENT=1";
		$this->db->query($sql1, false);
		$this->db->query($sql2, false);
	}
	private function insertdata($data, $table)
	{
		$db 	= m($table);
		if($data)foreach($data as $k=>$rs){
			$db->insert($rs);
		}
	}
	
	//同步应用
	private function tonbbuying($data)
	{
		$db 	= m('im_group');
		$dbs 	= m('im_menu');
		foreach($data as $k=>$yydata){
			$rs 	= $yydata['data'];
			$menu 	= $yydata['menu'];
			$name 	= $rs['name'];
			unset($rs['id']);
			$where 	= "`name`='$name' and `type`=2";
			if(!isempt($rs['num']))$where 	= "`num`='".$rs['num']."' and `type`=2";
			if($db->rows($where)==0){
				$mid = $db->insert($rs);
				$this->addyymenu($menu, $dbs, $mid, 0);
			}else{
				$mid = (int)$db->getmou('id', $where);
				$db->update(array(
					'face' 		=> $rs['face'],
					'url' 		=> $rs['url'],
					'types' 	=> $rs['types'],
					'sort' 		=> $rs['sort'],
					'urlpc' 	=> $rs['urlpc'],
					'urlm' 		=> $rs['urlm'],
					'yylx' 		=> $rs['yylx'],
					'name' 		=> $rs['name'],
					'iconfont' 	=> $rs['iconfont'],
					'iconcolor' => $rs['iconcolor'],
					'explain' 	=> $rs['explain'],
				),$where);
				$this->addyymenu($menu, $dbs, $mid, 0);
			}
		}
	}
	private function addyymenu($menu, $dbs, $mid, $pid)
	{
		$ssid = '0';
		foreach($menu as $k1=>$rs1){
			unset($rs1['id']);
			$menusub	 = false;
			if(isset($rs1['menusub'])){
				$menusub = $rs1['menusub'];
				unset($rs1['menusub']);
			}
			$rs1['mid'] = $mid;
			$rs1['pid'] = $pid;
			$where 		= "`name`='".$rs1['name']."' and `pid`='$pid' and `mid`='$mid'";
			$sid 		= (int)$dbs->getmou('id', $where);
			if($sid == 0){
				$where  = '';
			}
			$dbs->record($rs1, $where);
			if($sid==0)$sid = $this->db->insert_id();
			if($menusub)$this->addyymenu($menusub, $dbs, $mid, $sid);
			$ssid .= ','.$sid.'';
		}
		$dbs->delete("pid='$pid' and `mid`='$mid' and `id` not in($ssid)");
	}
	
	//同步计划任务
	private function tonbbutask($data)
	{
		$db 	= m('task');
		foreach($data as $k=>$rs){
			$where 		= "`url`='".$rs['url']."'";
			$sid 		= (int)$db->getmou('id', $where);
			if($sid == 0){
				$where  = '';
			}else{
				unset($rs['todoid']);
				unset($rs['todoname']);
			}
			unset($rs['id']);
			unset($rs['state']);
			unset($rs['lastdt']);
			$db->record($rs, $where);
		}
	}
	
	public function delmodelAjax()
	{
		$id = (int)$this->post('id');
		m('chargems')->delete("`modeid`='$id' and `type`<>2 and `modeid`>0");
		$this->showreturn('');
	}
	
	
	//更新文件对比显示
	public function datadubiAjax()
	{
		$id = (int)$this->get('id');
		$barr	= c('xinhu')->getdata('getinstallfile',array('id'=>$id,'sysnum'=>$this->keyss));
		if($barr['code']!=200)showreturn('',$barr['msg'],201);
		$data 	= $barr['data'];
		$mrs 	= $data['rs'];
		$rows 	= $data['rows'];
		$uparr  = array();
		
		$morrs	= $this->getmodeuprs();
		
		foreach($rows as $k=>$rs){
			$file 	= $rs['filepath'];
			$bo 	= true;
			$zt 	= 'add';
			if(file_exists($file)){
				$_size = filesize($file);
				if($_size == $rs['filesize'])$bo = false;
				$zt = 'edit';
			}
			if($rs['type']==1)$zt = '';
			if($rs['isup']==1)$bo = true;
			
			$rs['ting'] = 0;
			if(in_array($file, $morrs))$rs['ting'] = 1;
			
			if($bo){
				$rs['zt']= $zt;
				$uparr[] = $rs;
			}
		}
		$huira = $this->gethuiarr($id);
		foreach($uparr as $k=>$rs1){
			$ishui = 0;
			if(isset($huira[$rs1['id']]))$ishui = 1;
			if($rs1['ting']==1)$ishui=1;
			$uparr[$k]['ishui'] = $ishui;
		}
		$this->returnjson(array('rows'=>$uparr));
	}
	
	private function gethuiarr($id)
	{
		$hurs  = m('chargems')->getall("`modeid`='$id' and `type`=2",'mid');
		$huira = array();
		foreach($hurs as $k2=>$rs2)$huira[$rs2['mid']] = 1;
		return $huira;
	}
	
	//忽略更新文件
	public function hullueAjax()
	{
		$id = (int)$this->post('id');
		$lx = (int)$this->post('lx');
		$sid = $this->post('sid');
		$db  = m('chargems');
		$db->delete("`modeid`='$id' and `mid` in($sid) and `type`=2");
		$sad = explode(',', $sid);
		if($lx==0)foreach($sad as $sids){
			$db->insert(array(
				'optdt' 	=> $this->now,
				'type' 		=> 2,
				'mid' 		=> $sids,
				'modeid' 	=> $id
			));
		}
		echo 'ok';
	}
}