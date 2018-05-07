<?php
/**
*	模块：custract.客户合同，
*	说明：自定义区域内可写您想要的代码，模块列表页面，生成分为2块
*	来源：流程模块→表单元素管理→[模块.客户合同]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'custract',modename='客户合同',isflow=0,modeid='35',atype = params.atype,pnum=params.pnum;
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"num","name":"\u5408\u540c\u7f16\u53f7","fieldstype":"num","ispx":"0","isalign":"0","islb":"1"},{"fields":"custid","name":"\u5ba2\u6237\u540d\u79f0","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"custname","name":"\u5ba2\u6237\u540d\u79f0","fieldstype":"selectdatafalse","ispx":"0","isalign":"0","islb":"1"},{"fields":"optname","name":"\u62e5\u6709\u8005","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"saleid","name":"\u9500\u552e\u673a\u4f1a","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"signdt","name":"\u7b7e\u7ea6\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"money","name":"\u5408\u540c\u91d1\u989d","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u751f\u6548\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"content","name":"\u5408\u540c\u5185\u5bb9","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"},{"fields":"type","name":"\u5408\u540c\u7c7b\u578b","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"moneys","name":"\u5f85\u6536\/\u4ed8\u91d1\u989d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"statetext","name":"\u72b6\u6001","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"createname","name":"\u521b\u5efa\u4eba","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [];
	
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
				title:'客户合同('+nowtabs.name+')',
				cont:'客户合同('+nowtabs.name+')的列表的',
				explain:'订阅[客户合同]的列表',
				objtable:a
			});
		},
		getacturl:function(act){
			return js.getajaxurl(act,'mode_custract|input','flow',{'modeid':modeid});
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
			window.managelistcustract = a;
			addtabs({num:'daorucustract',url:'flow,input,daoru,modenum=custract',icons:'plus',name:'导入客户合同'});
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
		fanye:true,modenum:modenum,modename:modename,statuschange:false,tablename:jm.base64decode('Y3VzdHJhY3Q:'),
		url:c.storeurl(),storeafteraction:'storeaftershow',storebeforeaction:'storebeforeshow',
		params:{atype:atype},
		columns:[{text:"合同编号",dataIndex:"num"},{text:"客户名称",dataIndex:"custname"},{text:"拥有者",dataIndex:"optname",sortable:true},{text:"签约日期",dataIndex:"signdt",sortable:true},{text:"合同金额",dataIndex:"money",sortable:true},{text:"生效日期",dataIndex:"startdt",sortable:true},{text:"截止日期",dataIndex:"enddt",sortable:true},{text:"合同类型",dataIndex:"type"},{text:"待收/付金额",dataIndex:"moneys"},{text:"说明",dataIndex:"explain"},{text:"状态",dataIndex:"statetext"},{text:"创建人",dataIndex:"createname",sortable:true},{
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

c.initpage=function(){
	$('#key_{rand}').parent().before('<td style="padding-right:10px;"><input onclick="js.datechange(this,\'month\')" style="width:110px" placeholder="签约月份" readonly class="form-control datesss" id="dt_{rand}" ></td>');
}
c.searchbtn=function(){
	var dt = get('dt_{rand}').value;
	this.search({dt:dt});
}
$('#tdright_{rand}').prepend(c.getbtnstr('待收/付金额更新','retotal')+'&nbsp;');

c.retotal=function(){
	js.ajax(publicmodeurl(modenum,'remoney'),{},function(s){
		a.reload();
	},'get',false,'更新中...,更新完成')
}

//[自定义区域end]

	js.initbtn(c);
	var a = $('#viewcustract_{rand}').bootstable(bootparams);
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
			<button class="btn btn-default" id="daobtn_{rand}" disabled click="daochu" type="button">导出 <i class="icon-angle-down"></i></button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewcustract_{rand}"></div>
<!--HTMLend-->