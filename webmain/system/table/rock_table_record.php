<?php if(!defined('HOST'))die('not access');?>
<script >
$(document).ready(function(){
	{params}
	var tabs = params.table,bool=false;
	var a = $('#view_{rand}').bootstable({
		tablename:'option',fanye:true,modedir:'{mode}:{dir}',storebeforeaction:'tablerecord_before',
		storeafteraction:'tablerecord_after',params:{'stable':jm.encrypt(tabs)},checked:false,statuschange:false,
		columns:[{
			text:'ID',dataIndex:'id',sortable:true
		}],
		itemclick:function(){
			btn(false);
		},
		beforeload:function(){
			btn(true);
		},
		loadbefore:function(d){
			if(!bool){
				a.setColumns(d.fieldsarr);
				bool = true;
			}
		}
	});
	
	function btn(bo){
		
	}
	var  c={
		
		search:function(){
			a.setparams({
				key:get('key_{rand}').value
			},true);
		}
	};
	js.initbtn(c);
});
</script>


<div>
	<table width="100%">
	<tr>
	<td >
		<input class="form-control" style="width:180px" id="key_{rand}"  placeholder="只能搜索ID">
	</td>
	
	<td  style="padding-left:10px">
		<button class="btn btn-default" click="search" type="button">搜索</button>
	</td>
	
	<td  width="80%" style="padding-left:10px">
		
		
	</td>
	
	
	<td align="right" nowrap>
		
	</td>
	</tr>
	</table>
	
</div>
<div class="blank10"></div>
<div id="view_{rand}"></div>
<div class="tishi">数据库记录管理请谨慎操作！</div>
