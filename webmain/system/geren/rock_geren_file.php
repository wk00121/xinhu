<?php if(!defined('HOST'))die('not access');?>
<script >
$(document).ready(function(){
	{params};
	var atype=params.atype;
	var a = $('#veiw_{rand}').bootstable({
		tablename:'file',celleditor:true,sort:'id',dir:'desc',modedir:'{mode}:{dir}',params:{'atype':atype},fanye:true,
		storebeforeaction:'filebefore',storeafteraction:'fileafter',
		columns:[{
			text:'类型',dataIndex:'fileext',renderer:function(v, d){
				if(!isempt(d.thumbpath))return '<img src="'+d.thumbpath+'" width="24" height="24">';
				var lxs = js.filelxext(v);
				return '<img src="web/images/fileicons/'+lxs+'.gif">';
			}
		},{
			text:'名称',dataIndex:'filename',align:'left'
		},{
			text:'大小',dataIndex:'filesizecn',sortable:true
		},{
			text:'上传时间',dataIndex:'adddt',sortable:true
		},{
			text:'创建人',dataIndex:'optname',sortable:true
		},{
			text:'IP',dataIndex:'ip'
		},{
			text:'浏览器',dataIndex:'web'
		},{
			text:'下载次数',dataIndex:'downci',sortable:true
		},{
			text:'关联表',dataIndex:'mtype'
		},{
			text:'关联表ID',dataIndex:'mid'
		},{
			text:'ID',dataIndex:'id',sortable:true
		},{
			text:'',dataIndex:'opt',renderer:function(v,d,oi){
				return '<a href="javascript:;" onclick="showvies{rand}('+oi+',0)">预览</a>&nbsp;<a href="javascript:;" onclick="showvies{rand}('+oi+',1)"><i class="icon-arrow-down"></i></a>';
			}
		}],
		itemclick:function(){
			btn(false);
		}
	});
	
	showvies{rand}=function(oi,lx){
		var d=a.getData(oi);
		if(lx==1){
			js.downshow(d.id)
		}else{
			if(js.isimg(d.fileext)){
				$.imgview({url:d.filepath,downbool:false});
			}else{
				var urls = '?m=public&a=fileviewer&id='+d.id+'&wintype=max';
				openxiangs(d.filename, urls);
			}
		}
	}
	
	var c = {
		del:function(){
			a.del({url:js.getajaxurl('delfile','{mode}','{dir}')});
		},
		search:function(){
			var s=get('key_{rand}').value;
			a.setparams({key:s},true);
		}
	};
	
	function btn(bo){
		get('del_{rand}').disabled = bo;
	}
	js.initbtn(c);
});
</script>


<div>


<table width="100%"><tr>
	<td>
		<input class="form-control" style="width:180px" id="key_{rand}"   placeholder="文件名/创建人/关联表">
	</td>
	<td style="padding-left:10px">
		<button class="btn btn-default" click="search" type="button">搜索</button> 
	</td>
	
	
	
	<td width="80%"></td>
	<td align="right" nowrap>
	
		<button class="btn btn-danger" id="del_{rand}" click="del" disabled type="button"><i class="icon-trash"></i> 删除</button>
	</td>
</tr>
</table>
</div>
<div class="blank10"></div>
<div id="veiw_{rand}"></div>
<div class="tishi">提示：上传的文件可能会在某些单据上，删除请谨慎。</div>
