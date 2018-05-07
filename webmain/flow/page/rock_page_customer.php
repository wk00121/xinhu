<?php
/**
*	模块：customer.客户管理，
*	说明：自定义区域内可写您想要的代码，模块列表页面，生成分为2块
*	来源：流程模块→表单元素管理→[模块.客户管理]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'customer',modename='客户管理',isflow=0,modeid='7',atype = params.atype,pnum=params.pnum;
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"name","name":"\u5ba2\u6237\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"suoname","name":"\u6240\u5c5e\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"type","name":"\u5ba2\u6237\u7c7b\u578b","fieldstype":"rockcombo","ispx":"1","isalign":"0","islb":"1"},{"fields":"laiyuan","name":"\u6765\u6e90","fieldstype":"rockcombo","ispx":"0","isalign":"0","islb":"0"},{"fields":"unitname","name":"\u5ba2\u6237\u5355\u4f4d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"tel","name":"\u8054\u7cfb\u7535\u8bdd","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"mobile","name":"\u8054\u7cfb\u624b\u673a","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"sheng","name":"\u6240\u5728\u7701","fieldstype":"selectdatafalse","ispx":"0","isalign":"0","islb":"0"},{"fields":"shi","name":"\u6240\u5728\u5e02","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"email","name":"\u90ae\u7bb1","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"address","name":"\u5730\u5740","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"routeline","name":"\u4ea4\u901a\u8def\u7ebf","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"isstat","name":"\u662f\u5426\u6807\u2605","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"isgys","name":"\u4f9b\u5e94\u5546","fieldstype":"checkbox","ispx":"0","isalign":"0","islb":"0"},{"fields":"linkname","name":"\u8054\u7cfb\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"},{"fields":"htshu","name":"\u5408\u540c\u6570","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"moneyz","name":"\u9500\u552e\u603b\u989d","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"moneyd","name":"\u5f85\u6536\u91d1\u989d","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= {"columns_customer_":"name,suoname,type,unitname,tel,mobile,address,isstat,htshu,moneyz,moneyd,caozuo","columns_customer_dist":"name,suoname,type,laiyuan,unitname,isgys,linkname","columns_customer_ghai":"name,suoname,type,unitname,sheng,shi,status,caozuo","columns_customer_gys":"name,suoname,type,unitname,tel,address,status,linkname,explain,caozuo","columns_customer_shate":"name,suoname,type,laiyuan,unitname,isstat,caozuo"};
	
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
				title:'客户管理('+nowtabs.name+')',
				cont:'客户管理('+nowtabs.name+')的列表的',
				explain:'订阅[客户管理]的列表',
				objtable:a
			});
		},
		getacturl:function(act){
			return js.getajaxurl(act,'mode_customer|input','flow',{'modeid':modeid});
		},
		changatype:function(o1,lx){
			$("button[id^='changatype{rand}']").removeClass('active');
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
				str+='<button class="btn btn-default" click="changatype,'+d1[i].num+'" id="changatype{rand}_'+d1[i].num+'" type="button">'+d1[i].name+'</button>';
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
			window.managelistcustomer = a;
			addtabs({num:'daorucustomer',url:'flow,input,daoru,modenum=customer',icons:'plus',name:'导入客户管理'});
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
			return '<button class="btn btn-'+ys+'" id="btn'+click+'_{rand}" click="'+click+'" '+ots+' type="button">'+txt+'</button>';
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
		fanye:true,modenum:modenum,modename:modename,statuschange:false,tablename:jm.base64decode('Y3VzdG9tZXI:'),
		url:c.storeurl(),storeafteraction:'storeaftershow',storebeforeaction:'storebeforeshow',
		params:{atype:atype},
		columns:[{text:"客户名称",dataIndex:"name"},{text:"所属人",dataIndex:"suoname"},{text:"客户类型",dataIndex:"type",sortable:true},{text:"客户单位",dataIndex:"unitname"},{text:"是否标★",dataIndex:"isstat",sortable:true},{text:"合同数",dataIndex:"htshu",sortable:true},{text:"销售总额",dataIndex:"moneyz",sortable:true},{text:"待收金额",dataIndex:"moneyd",sortable:true},{
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

var chengsuid = '';
if(atype!='my')$('#daoruspan_{rand}').remove();
if(pnum==''){
	bootparams.checked = true;

	c.move=function(){
		var s= a.getchecked();
		if(s==''){js.msg('msg','没有选择记录');return;}
		chengsuid=s;
		js.confirm('是否客户转移给其他人，并客户下的合同和待收付款单和销售机会同时转移？', function(jg){
			if(jg=='yes')c.moveto();
		});
	}
	c.movetoss=function(sna,toid){
		js.ajax(js.getajaxurl('movecust',modenum,'main'),{'toid':toid,'sid':chengsuid},function(s){
			a.reload();
		},'post',false,'转移给:'+sna+'...,转移成功');
	}
	c.moveto=function(sid){
		var cans = {
			type:'user',
			title:'转移给...',
			callback:function(sna,sid){
				if(sid)c.movetoss(sna,sid);
			}
		}
		setTimeout(function(){js.getuser(cans);},10);
	}
	$('#tdright_{rand}').append('&nbsp; '+c.getbtnstr('客户转移','move'));
}else{
	if(pnum!='gys')$('#tdleft_{rand}').hide();
	if(pnum=='dist'){
		bootparams.checked = true;
		c.distss=function(o1,lx){
			var s = a.getchecked();
			if(s==''){js.msg('msg','没有选中行');return;}
			if(lx==0){
				js.confirm('确定要将选中标为未分配吗？',function(jg){
					if(jg=='yes')c.distssok(s, '','', 0);
				});
				return;
			}
			var cans = {
				type:'user',
				title:'选中分配给...',
				callback:function(sna,sid){
					if(sna=='')return;
					setTimeout(function(){
						js.confirm('确定要将选中记录分配给：['+sna+']吗？',function(jg){
							if(jg=='yes')c.distssok(s, sna,sid,1);
						});
					},10);
				}
			};
			js.getuser(cans);
		}
		c.distssok=function(s, sna,sid, lx){
			js.ajax(js.getajaxurl('distcust',modenum,'main'),{sid:s,sname:sna,snid:sid,lx:lx},function(s){
				a.reload();
			},'post','','处理中...,处理成功');
		}
		$('#tdright_{rand}').prepend(c.getbtnstr('标为未分配','distss,0')+'&nbsp;');
		$('#tdright_{rand}').prepend(c.getbtnstr('选中分配给','distss,1')+'&nbsp;&nbsp;');
	}
}

if(pnum!='gys' && pnum!='ghai'){
	$('#tdright_{rand}').prepend(c.getbtnstr('重新统计金额','retotal')+'&nbsp;');

	c.retotal=function(){
		js.ajax(js.getajaxurl('retotal',modenum,'main'),{},function(s){
			a.reload();
		},'get',false,'统计中...,统计完成')
	}
}
if(pnum=='gys'){	
	modename = '供应商管理';
	c.clickwin=function(o1,lx){
		openinput(modename,modenum,'0&def_isgys=1','opegs{rand}');
	}
}

//[自定义区域end]

	js.initbtn(c);
	var a = $('#viewcustomer_{rand}').bootstable(bootparams);
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
<!--SCRIPTend-->
<!--HTMLstart-->
<div>
	<table width="100%">
	<tr>
		<td style="padding-right:10px;" id="tdleft_{rand}" nowrap><button id="addbtn_{rand}" class="btn btn-primary" click="clickwin,0" disabled type="button"><i class="icon-plus"></i> 新增</button></td>
		<td>
			<input class="form-control" style="width:160px" id="key_{rand}" placeholder="关键字">
		</td>
		
		<td style="padding-left:10px">
			<div style="width:85px" class="btn-group">
			<button class="btn btn-default" click="searchbtn" type="button">搜索</button><button class="btn btn-default" id="downbtn_{rand}" type="button" style="padding-left:8px;padding-right:8px"><i class="icon-angle-down"></i></button> 
			</div>
		</td>
		<td  width="90%" style="padding-left:10px"><div id="changatype{rand}" class="btn-group"></div></td>
	
		<td align="right" id="tdright_{rand}" nowrap>
			<span style="display:none" id="daoruspan_{rand}"><button class="btn btn-default" click="daoru,1" type="button">导入</button>&nbsp;&nbsp;&nbsp;</span><button class="btn btn-default" id="daobtn_{rand}" disabled click="daochu" type="button">导出 <i class="icon-angle-down"></i></button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewcustomer_{rand}"></div>
<!--HTMLend-->