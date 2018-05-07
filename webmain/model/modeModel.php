<?php
class modeClassModel extends Model
{
	public function initModel()
	{
		$this->settable('flow_set');
	}
	public function getmodearr($whe='')
	{
		$arr = $this->getall('status=1 '.$whe.'','`id`,`num`,`name`,`table`,`type`,`isflow`','sort');
		foreach($arr as $k=>$rs){
			$arr[$k]['name'] = ''.$rs['id'].'.'.$rs['name'].'('.$rs['num'].')';
		}
		return $arr;
	}
	
	public function getmoderows($uid, $sww='')
	{
		$where	= m('admin')->getjoinstr('receid', $uid);
		$arr 	= $this->getall("`status`=1 and `type`<>'系统' $sww $where",'`id`,`num`,`name`,`table`,`type`,`isflow`,`isscl`','`sort`');
		return $arr;
	}
	
	public function getmodemyarr($uid=0, $whe='')
	{
		$where = '';
		if($whe!='')$where = $whe;
		if($uid>0)$where = m('admin')->getjoinstr('receid', $uid);
		$arr = $this->getall('status=1 and isflow=1 '.$where.'','`id`,`name`,`type`','sort');
		return $arr;
	}
	
	//生成列表页面
	public function createlistpage($modeid, $lxss=0)
	{
		if(is_array($modeid)){
			$mors	= $modeid;
		}else{
			$mors 	= m('flow_set')->getone($modeid,'`id`,`table`,`num`,`name`,`isflow`,`lbztxs`');
		}
		$num	= $mors['num'];
		$path 	= ''.P.'/flow/page/rock_page_'.$num.'.php';
		
		//当是一键生成时，不存在就不要生成了
		if($lxss==1 && !file_exists($path))return 'none';
		
		$flow	= m('flow')->initflow($num);
		
		$table	= $mors['table'];
		$name	= $mors['name'];
		$modeid	= $mors['id'];
		$isflow	= $mors['isflow'];
		$lbztxs	= $mors['lbztxs'];
		$columnsstr = '';
		$showzt	= false;
		if($isflow==1){
			$columnsstr = '{text:"申请人",dataIndex:"base_name",sortable:true},{text:"申请人部门",dataIndex:"base_deptname",sortable:true},{text:"单号",dataIndex:"sericnum"},';
		}
		$farr[] = array('name'=>'申请人','fields'=>'base_name');
		$farr[] = array('name'=>'申请人部门','fields'=>'base_deptname');
		$farr[] = array('name'=>'单号','fields'=>'sericnum');
		$farrs 	= m('flow_element')->getall("`mid`='$modeid' and `iszb`=0",'`fields`,`name`,`fieldstype`,`ispx`,`isalign`,`islb`','`sort`');
		foreach($farrs as $k=>$rs){
			$farr[] = $rs;
			if($rs['islb']==0)continue;
			$columnsstr.='{text:"'.$rs['name'].'",dataIndex:"'.$rs['fields'].'"';
			if($rs['ispx']==1)$columnsstr.=',sortable:true';
			if($rs['isalign']==1)$columnsstr.=',align:"left"';
			if($rs['isalign']==2)$columnsstr.=',align:"right"';
			$columnsstr.='},';
			if($rs['fields']=='status')$showzt=true;
		}
		if($isflow==1){
			$columnsstr.='{text:"状态",dataIndex:"statustext"},';
		}
		$jgpstr 	= '<!--SCRIPTend-->';
		$hstart 	= '<!--HTMLstart-->';
		$hendts 	= '<!--HTMLend-->';
		$oldcont 	= @file_get_contents($path);
		$autoquye	= $this->rock->getcai($oldcont,'//[自定义区域start]','//[自定义区域end]');
		
		$isdaoru 	= m('flow_element')->rows("`mid`='$modeid' and `isdr`=1");
		$drstrbtn	= '';
		if($isdaoru>0){
			$drstrbtn	= "<span style=\"display:none\" id=\"daoruspan_{rand}\"><button class=\"btn btn-default\" click=\"daoru,1\" type=\"button\">导入</button>&nbsp;&nbsp;&nbsp;</span>";
		}
		
		//读取流程模块的条件
		$whtml 		= '<div id="changatype{rand}" class="btn-group"></div>';
		$zthtml		= '';
		if($isflow==1)$showzt = true;
		if($lbztxs==1)$showzt = true;
		if($lbztxs==2)$showzt = false;
		if($showzt){
			$ztarr	= $flow->getstatusarr();
			$zthtml = '<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option>';
			foreach($ztarr as $zt=>$ztv){
				if($isflow==0 && $zt==23)continue;
				$zthtml .= '<option style="color:'.arrvalue($ztv, 1).'" value="'.$zt.'">'.$ztv[0].'</option>';
			}
			$zthtml .= '</select></td>';
		}
		$fselarr	= array();
		$bear		= $this->db->getrows('[Q]option',"`num` like 'columns_".$num."_%'",'`num`,`value`');
		foreach($bear as $k2=>$rs2)$fselarr[$rs2['num']]=$rs2['value'];
		$placeholder= '关键字';
		if($isflow==1)$placeholder= '关键字/申请人/单号';
		
		
$html= "".$hstart."
<div>
	<table width=\"100%\">
	<tr>
		<td style=\"padding-right:10px;\" id=\"tdleft_{rand}\" nowrap><button id=\"addbtn_{rand}\" class=\"btn btn-primary\" click=\"clickwin,0\" disabled type=\"button\"><i class=\"icon-plus\"></i> 新增</button></td>
		<td>
			<input class=\"form-control\" style=\"width:160px\" id=\"key_{rand}\" placeholder=\"".$placeholder."\">
		</td>
		$zthtml
		<td style=\"padding-left:10px\">
			<div style=\"width:85px\" class=\"btn-group\">
			<button class=\"btn btn-default\" click=\"searchbtn\" type=\"button\">搜索</button><button class=\"btn btn-default\" id=\"downbtn_{rand}\" type=\"button\" style=\"padding-left:8px;padding-right:8px\"><i class=\"icon-angle-down\"></i></button> 
			</div>
		</td>
		<td  width=\"90%\" style=\"padding-left:10px\">$whtml</td>
	
		<td align=\"right\" id=\"tdright_{rand}\" nowrap>
			".$drstrbtn."<button class=\"btn btn-default\" id=\"daobtn_{rand}\" disabled click=\"daochu\" type=\"button\">导出 <i class=\"icon-angle-down\"></i></button> 
		</td>
	</tr>
	</table>
</div>
<div class=\"blank10\"></div>
<div id=\"view".$num."_{rand}\"></div>
".$hendts."";		
$str = "<?php
/**
*	模块：".$num.".".$name."，
*	说明：自定义区域内可写您想要的代码，模块列表页面，生成分为2块
*	来源：流程模块→表单元素管理→[模块.".$name."]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = '".$num."',modename='".$name."',isflow=".$isflow.",modeid='".$modeid."',atype = params.atype,pnum=params.pnum;
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = ".json_encode($farr).",fieldsselarr= ".json_encode($fselarr).";
	
	var c = {
		reload:function(){
			a.reload();
		},
		clickwin:function(o1,lx){
			var id=0;
			if(lx==1)id=a.changeid;
			openinput(modename,modenum,id,'opegs{rand}');
		},
		view:function(){
			var d=a.changedata;
			openxiangs(modename,modenum,d.id,'opegs{rand}');
		},
		searchbtn:function(){
			this.search({});
		},
		search:function(cans){
			var s=get('key_{rand}').value,zt='';
			if(get('selstatus_{rand}'))zt=get('selstatus_{rand}').value;
			var canss = js.apply({key:s,keystatus:zt,search_value:''}, cans);
			a.setparams(canss,true);
		},
		//高级搜索
		searchhigh:function(){
			new highsearchclass({
				modenum:modenum,
				oncallback:function(d){
					c.searchhighb(d);
				}
			});
		},
		searchhighb:function(d){
			d.key='';
			d.search_value='';
			get('key_{rand}').value='';
			a.setparams(d,true);
		},
		searchuname:function(d){
			js.getuser({
				type:'deptusercheck',
				title:'搜索'+d.name,
				changevalue:this.search_value,
				callback:function(sna,sid){
					c.searchunames(d,sna,sid);
				}
			});
		},
		search_value:'',
		searchunames:function(d,sna,sid){
			get('key_{rand}').value=sna;
			this.search_value = sid;
			var cs = {key:'','search_fields':d.fields,'search_value':sid};
			a.setparams(cs,true);
		},
		//导出
		daochu:function(o1,lx,lx1,e){
			if(!this.daochuobj)this.daochuobj=$.rockmenu({
				width:120,top:35,donghua:false,data:[],
				itemsclick:function(d, i){
					c.daonchuclick(d);
				}
			});
			var d = [{name:'导出全部',lx:0},{name:'导出当前页',lx:1},{name:'订阅此列表',lx:2}];
			this.daochuobj.setData(d);
			var lef = $(o1).offset();
			this.daochuobj.showAt(lef.left, lef.top+35);
		},
		daonchuclick:function(d){
			if(d.lx==0)a.exceldown();
			if(d.lx==1)a.exceldownnow();
			if(d.lx==2)this.subscribelist();
		},
		subscribelist:function(){
			js.subscribe({
				title:'".$name."('+nowtabs.name+')',
				cont:'".$name."('+nowtabs.name+')的列表的',
				explain:'订阅[".$name."]的列表',
				objtable:a
			});
		},
		getacturl:function(act){
			return js.getajaxurl(act,'mode_".$num."|input','flow',{'modeid':modeid});
		},
		changatype:function(o1,lx){
			$(\"button[id^='changatype{rand}']\").removeClass('active');
			$('#changatype{rand}_'+lx+'').addClass('active');
			a.setparams({atype:lx},true);
			nowtabssettext($(o1).html());
		},
		init:function(){
			$('#key_{rand}').keyup(function(e){
				if(e.keyCode==13)c.searchbtn();
			});
			this.initpage();
		},
		initpage:function(){
			
		},
		loaddata:function(d){
			this.setdownsodata(d.souarr);
			if(!d.atypearr)return;
			get('addbtn_{rand}').disabled=(d.isadd!=true);
			get('daobtn_{rand}').disabled=(d.isdaochu!=true);
			if(d.isdaoru)$('#daoruspan_{rand}').show();
			var d1 = d.atypearr,len=d1.length,i,str='';
			for(i=0;i<len;i++){
				str+='<button class=\"btn btn-default\" click=\"changatype,'+d1[i].num+'\" id=\"changatype{rand}_'+d1[i].num+'\" type=\"button\">'+d1[i].name+'</button>';
			}
			$('#changatype{rand}').html(str);
			$('#changatype{rand}_'+atype+'').addClass('active');
			js.initbtn(c);
		},
		setdownsodata:function(darr){
			var ddata = [{name:'高级搜索',lx:0}],dsd,i;
			if(darr)for(i=0;i<darr.length;i++){
				dsd = darr[i];
				dsd.lx=3;
				ddata.push(dsd);
			}
			if(admintype==1)ddata.push({name:'自定义列显示',lx:2});
			ddata.push({name:'打印',lx:1});
			this.soudownobj.setData(ddata);
		},
		setcolumns:function(fid, cnas){
			var d = false,i,ad=bootparams.columns,len=ad.length,oi=-1;
			for(i=0;i<len;i++){
				if(ad[i].dataIndex==fid){
					d = ad[i];
					oi= i;
					break;
				}
			}
			if(d){
				d = js.apply(d, cnas);
				bootparams.columns[oi]=d;
			}
		},
		daoru:function(){
			window.managelist".$num." = a;
			addtabs({num:'daoru".$num."',url:'flow,input,daoru,modenum=".$num."',icons:'plus',name:'导入".$name."'});
		},
		initcolumns:function(bots){
			var num = 'columns_'+modenum+'_'+pnum+'',d=[],d1,d2={},i,len=fieldsarr.length,bok;
			var nstr= fieldsselarr[num];if(!nstr)nstr='';
			if(nstr)nstr=','+nstr+',';
			if(nstr=='' && isflow==1){
				d.push({text:'申请人',dataIndex:'base_name',sortable:true});
				d.push({text:'申请人部门',dataIndex:'base_deptname',sortable:true});
			}
			for(i=0;i<len;i++){
				d1 = fieldsarr[i];
				bok= false;
				if(nstr==''){
					if(d1['islb']=='1')bok=true;
				}else{
					if(nstr.indexOf(','+d1.fields+',')>=0)bok=true;
				}
				if(bok){
					d2={text:d1.name,dataIndex:d1.fields};
					if(d1.ispx=='1')d2.sortable=true;
					if(d1.isalign=='1')d2.align='left';
					if(d1.isalign=='2')d2.align='right';
					d.push(d2);
				}
			}
			if(isflow==1)d.push({text:'状态',dataIndex:'statustext'});
			if(nstr=='' || nstr.indexOf(',caozuo,')>=0)d.push({text:'',dataIndex:'caozuo',callback:'opegs{rand}'});
			if(!bots){
				bootparams.columns=d;
			}else{
				a.setColumns(d);
			}
		},
		setparams:function(cs){
			var ds = js.apply({},cs);
			a.setparams(ds);
		},
		storeurl:function(){
			var url = this.getacturl('publicstore')+'&pnum='+pnum+'';
			return url;
		},
		printlist:function(){
			js.msg('success','可使用导出，然后打开在打印');
		},
		getbtnstr:function(txt, click, ys, ots){
			if(!ys)ys='default';
			if(!ots)ots='';
			return '<button class=\"btn btn-'+ys+'\" id=\"btn'+click+'_{rand}\" click=\"'+click+'\" '+ots+' type=\"button\">'+txt+'</button>';
		},
		setfieldslist:function(){
			new highsearchclass({
				modenum:modenum,
				modeid:modeid,
				type:1,
				isflow:isflow,
				pnum:pnum,atype:atype,
				fieldsarr:fieldsarr,
				fieldsselarr:fieldsselarr,
				oncallback:function(str){
					fieldsselarr[this.columnsnum]=str;
					c.initcolumns(true);
					c.reload();
				}
			});
		}
	};	
	
	//表格参数设定
	var bootparams = {
		fanye:true,modenum:modenum,modename:modename,statuschange:false,tablename:jm.base64decode('".$this->rock->jm->base64encode($table)."'),
		url:c.storeurl(),storeafteraction:'storeaftershow',storebeforeaction:'storebeforeshow',
		params:{atype:atype},
		columns:[".$columnsstr."{
			text:'',dataIndex:'caozuo',callback:'opegs{rand}'
		}],
		itemdblclick:function(){
			c.view();
		},
		load:function(d){
			c.loaddata(d);
		}
	};
	c.initcolumns(false);
	opegs{rand}=function(){
		c.reload();
	}
	
//[自定义区域start]

$autoquye

//[自定义区域end]

	js.initbtn(c);
	var a = $('#view".$num."_{rand}').bootstable(bootparams);
	c.init();
	c.soudownobj = $('#downbtn_{rand}').rockmenu({
		width:120,top:35,donghua:false,
		data:[{name:'高级搜索',lx:0}],
		itemsclick:function(d, i){
			if(d.lx==0)c.searchhigh();
			if(d.lx==1)c.printlist();
			if(d.lx==2)c.setfieldslist();
			if(d.lx==3)c.searchuname(d);
		}
	});
	
	
});
</script>
".$jgpstr."";	
		$bstrs = '<!--HTML-->';
		if(!isempt($oldcont) && contain($oldcont, $jgpstr) && contain($oldcont, $bstrs)){
			$strarr = explode($jgpstr, $oldcont);
			$nstr 	= $strarr[1];
			$htmlqy = $this->rock->getcai($nstr, $hstart, $hendts);
$rstr 	= "".$hstart."
".$htmlqy."
".$hendts."";
			$nstr 	= str_replace($rstr, '', $nstr);
			$nstr 	= str_replace($bstrs, $html.$bstrs, $nstr);
			$str	.= "\n".$nstr;
		}else{
			$str.= "\n".$html;
		}
		$bo = $this->rock->createtxt($path, $str);
		if(!$bo)$path='';
		return $path;
	}
	
	
	
	
	
	
	
	
	
	
	
	/**
	*	统计
	*/
	public function flowtotal($modeid, $fields, $type)
	{
		
	}
}