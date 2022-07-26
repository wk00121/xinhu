<?php
/**
*	模块：user.用户
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.用户]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'user',modename='用户',isflow=0,modeid='10',atype = params.atype,pnum=params.pnum,modenames='';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"face","name":"\u5934\u50cf","fieldstype":"uploadimg","ispx":"0","isalign":"0","islb":"1"},{"fields":"name","name":"\u59d3\u540d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"user","name":"\u7528\u6237\u540d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"pass","name":"\u5bc6\u7801","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"num","name":"\u7f16\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"workdate","name":"\u5165\u804c\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"0"},{"fields":"sex","name":"\u6027\u522b","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"mobile","name":"\u624b\u673a\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"deptid","name":"\u90e8\u95e8Id","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"deptname","name":"\u90e8\u95e8","fieldstype":"changedept","ispx":"0","isalign":"0","islb":"0"},{"fields":"deptallname","name":"\u90e8\u95e8\u5168\u79f0","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"ranking","name":"\u804c\u4f4d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"deptnames","name":"\u591a\u90e8\u95e8","fieldstype":"changedeptcheck","ispx":"0","isalign":"0","islb":"0"},{"fields":"rankings","name":"\u591a\u804c\u4f4d","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"superman","name":"\u4e0a\u7ea7\u4e3b\u7ba1","fieldstype":"changeusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"superid","name":"\u4e0a\u7ea7\u4e3b\u7ba1id","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"pingyin","name":"\u540d\u5b57\u62fc\u97f3","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"tel","name":"\u7535\u8bdd","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"email","name":"\u90ae\u7bb1","fieldstype":"email","ispx":"0","isalign":"0","islb":"0"},{"fields":"weixinid","name":"\u5fae\u4fe1\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"companyid","name":"\u6240\u5c5e\u5355\u4f4d","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"status","name":"\u542f\u7528","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"type","name":"\u7ba1\u7406\u5458","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"id","name":"\u7528\u6237Id","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"sort","name":"\u6392\u5e8f\u53f7","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"groupname","name":"\u6240\u5728\u7ec4","fieldstype":"checkboxall","ispx":"0","isalign":"0","islb":"0"},{"fields":"isvcard","name":"\u901a\u8baf\u5f55","fieldstype":"checkbox","ispx":"0","isalign":"0","islb":"1"},{"fields":"dwid","name":"\u66f4\u591a\u5355\u4f4d","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"temp_dwid","name":"\u66f4\u591a\u6240\u5c5e\u5355\u4f4d","fieldstype":"selectdatatrue","ispx":"0","isalign":"0","islb":"0"}],fieldsselarr= [];
	
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
		daochu:function(o1,lx,lx1,e){
			publicdaochuobj({
				'objtable':a,
				'modename':modename,
				'fieldsarr':fieldsarr,
				'modenum':modenum,
				'modenames':modenames,
				'isflow':isflow,
				'btnobj':o1
			});
		},
		getacturl:function(act){
			return js.getajaxurl(act,'mode_user|input','flow',{'modeid':modeid});
		},
		changatype:function(o1,lx){
			$("button[id^='changatype{rand}']").removeClass('active');
			$('#changatype{rand}_'+lx+'').addClass('active');
			a.setparams({atype:lx},true);
			var tit = $(o1).html();if(tit.indexOf(modename)<0)tit=modename+'('+tit+')';
			nowtabssettext(tit);
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
			if(d.isdaochu)$('#daobtn_{rand}').show();
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
			window.managelistuser = a;
			addtabs({num:'daoruuser',url:'flow,input,daoru,modenum=user',icons:'plus',name:'导入用户'});
		},
		initcolumns:function(bots){
			var num = 'columns_'+modenum+'_'+pnum+'',d=[],d1,d2={},i,len=fieldsarr.length,bok;
			var nstr= fieldsselarr[num];if(!nstr)nstr='';
			if(nstr)nstr=','+nstr+',';
			if(nstr=='' && isflow>0){
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
			if(isflow>0)d.push({text:'状态',dataIndex:'statustext'});
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
	
	var bootparams = {
		fanye:true,modenum:modenum,modename:modename,statuschange:false,tablename:jm.base64decode('YWRtaW4:'),
		url:c.storeurl(),storeafteraction:'storeaftershow',storebeforeaction:'storebeforeshow',
		params:{atype:atype},
		columns:[{text:"头像",dataIndex:"face"},{text:"姓名",dataIndex:"name"},{text:"用户名",dataIndex:"user"},{text:"性别",dataIndex:"sex"},{text:"部门全称",dataIndex:"deptallname",align:"left"},{text:"职位",dataIndex:"ranking"},{text:"上级主管",dataIndex:"superman"},{text:"电话",dataIndex:"tel"},{text:"启用",dataIndex:"status",sortable:true},{text:"管理员",dataIndex:"type",sortable:true},{text:"用户Id",dataIndex:"id"},{text:"排序号",dataIndex:"sort"},{text:"通讯录",dataIndex:"isvcard"},{
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

bootparams.statuschange = true;
bootparams.celleditor = (atype=='all');
if(ISDEMO)bootparams.celleditor=false;
c.setcolumns('status',{
	editor:true,
	type:'checkbox',
	editorafter:function(){
		a.reload();
	},
	editorbefore:function(d){
		if(d.id=='1'){
			js.msg('msg','ID=1的用户不能编辑');
			return false;
		}else{
			return true;
		}
	}
});

c.setcolumns('isvcard',{
	editor:true,
	type:'checkbox'
});

c.setcolumns('sex',{
	editor:true,
	editor:true,type:'select',store:[['男','男'],['女','女']]
});

c.setcolumns('sort',{
	editor:true
});
c.setcolumns('tel',{
	editor:true
});
c.setcolumns('face',{
	renderer:function(v,d){
		if(isempt(v))v='images/noface.png';
		return '<img src="'+v+'" id="faceviewabc_'+d.id+'" height="24" width="24">';
	}
});
if(atype=='all'){
	bootparams.checked=true;
	$('#tdright_{rand}').prepend(c.getbtnstr('修改上级','editsuper')+'&nbsp;&nbsp;');
	$('#tdright_{rand}').prepend(c.getbtnstr('修改头像','editface','','disabled')+'&nbsp;&nbsp;');
	$('#tdright_{rand}').prepend(c.getbtnstr('更新数据','gengxin','success')+'&nbsp;&nbsp;');

	c.gengxin=function(){
		js.msg('wait', '更新中...');
		$.get(js.getajaxurl('updatedata','admin','system'), function(da){
			js.msg('success', da);
		});
	}
	c.editface=function(){
		editfacechang(a.changeid, a.changedata.name);
	}
	bootparams.itemclick=function(){
		get('btneditface_{rand}').disabled=false;
	}
	bootparams.beforeload=function(){
		get('btneditface_{rand}').disabled=true;
	}
	c.editsuper=function(){
		var xid = a.getchecked();
		if(xid==''){js.msg('msg','请先用复选框选择行');return;}
		var cans = {
			type:'usercheck',
			title:'选择新的上级主管',
			callback:function(sna,sid){
				if(sna=='')return;
				js.msg('wait','修改中...');
				js.ajax(c.getacturl('editsuper'),{sna:sna,sid:sid,xid:xid}, function(ret){
					js.msg('success', '修改成功');
					a.reload();
				},'post');
				
			}
		};
		js.getuser(cans);
	}
}

//[自定义区域end]

	js.initbtn(c);
	var a = $('#viewuser_{rand}').bootstable(bootparams);
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
			<div style="white-space:nowrap">
			<button style="border-right:0;border-top-right-radius:0;border-bottom-right-radius:0" class="btn btn-default" click="searchbtn" type="button">搜索</button><button class="btn btn-default" id="downbtn_{rand}" type="button" style="padding-left:8px;padding-right:8px;border-top-left-radius:0;border-bottom-left-radius:0"><i class="icon-angle-down"></i></button> 
			</div>
		</td>
		<td  width="90%" style="padding-left:10px"><div id="changatype{rand}" class="btn-group"></div></td>
	
		<td align="right" id="tdright_{rand}" nowrap>
			<span style="display:none" id="daoruspan_{rand}"><button class="btn btn-default" click="daoru,1" type="button">导入</button>&nbsp;&nbsp;&nbsp;</span><button class="btn btn-default" style="display:none" id="daobtn_{rand}" disabled click="daochu" type="button">导出 <i class="icon-angle-down"></i></button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewuser_{rand}"></div>
<!--HTMLend-->