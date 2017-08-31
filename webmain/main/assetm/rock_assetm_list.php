<?php if(!defined('HOST'))die('not access');?>
<script >
$(document).ready(function(){

	var modenum='assetm';
	var a = $('#view_{rand}').bootstable({
		tablename:modenum,celleditor:true,autoLoad:false,modenum:modenum,fanye:true,
		columns:[{
			text:'',dataIndex:'caozuo'
		},{
			text:'资产名称',dataIndex:'title',editor:false,align:'left'
		},{
			text:'编号',dataIndex:'num'
		},{
			text:'操作时间',dataIndex:'optdt'
		},{
			text:'状态',dataIndex:'state'
		},{
			text:'品牌',dataIndex:'brand'
		},{
			text:'仓库',dataIndex:'ckid'
		},{
			text:'使用人',dataIndex:'usename'
		}]
	});

	var c = {
		del:function(){
			a.del();
		},
		daochu:function(){
			a.exceldown(nowtabs.name);
		},
		adds:function(){
			openinput('固定资产',modenum);
		},
		search:function(){
			var s=get('key_{rand}').value;
			a.setparams({key:s},true);
		},
		daoru:function(){
			managelistassetm = a;
			addtabs({num:'daoruassetm',url:'flow,input,daoru,modenum=assetm',icons:'plus',name:'导入固定资产'});
		},
		
		mobj:a,
		title:'资产分类',
		stable:'assetm',
		optionview:'optionview_{rand}',
		optionnum:'assetstype',
		rand:'{rand}'
	};
	
	var c = new optionclass(c);
	
	js.initbtn(c);
});
</script>


<table width="100%">
<tr valign="top">
<td>
	<div style="border:1px #cccccc solid;width:220px">
	<div id="optionview_{rand}" style="height:400px;overflow:auto;"></div>
	</div>  
</td>
<td width="10" nowrap>&nbsp;</td>
<td width="95%">
	<div>
	<table width="100%"><tr>
		<td align="left" nowrap>
			<button class="btn btn-primary" click="adds"  type="button"><i class="icon-plus"></i> 新增</button>&nbsp; 
			<button class="btn btn-default" click="allshow"  type="button">所有资产</button>&nbsp; 
			
		</td>
		
		<td style="padding-left:10px">
		<input class="form-control" style="width:200px" id="key_{rand}"   placeholder="资产名称/编号/使用人">
		</td>
		<td style="padding-left:10px">
			<button class="btn btn-default" click="search" type="button">搜索</button> 
		</td>
		<td width="90%">
			&nbsp;&nbsp;<span id="megss{rand}"></span>
		</td>
		<td align="right" nowrap>
			<button class="btn btn-default"  click="daoru" type="button">导入资产</button>&nbsp;
			<button class="btn btn-default"  click="daochu" type="button">导出</button>
		</td>
	</tr></table>
	</div>
	<div class="blank10"></div>
	<div id="view_{rand}"></div>
</td>
</tr>
</table>
