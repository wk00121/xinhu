<?php if(!defined('HOST'))die('not access');?>
<script >
$(document).ready(function(){
	

	var a = $('#veiw_{rand}').bootstable({
		tablename:'wotpl',
		celleditor:true,
		modedir:'{mode}:{dir}',
		storeafteraction:'wotpl_after',
		storebeforeaction:'wotpl_before',
		columns:[{
			text:'关联OA系统模版',dataIndex:'modename'
		},{
			text:'模版id',dataIndex:'template_id',width:'120px',renderer:function(v){
				return '<div class="wrap">'+v+'</div>';
			}
		},{
			text:'模板标题',dataIndex:'title'
		},{
			text:'所属行业',dataIndex:'primary_industry'
		},{
			text:'所属行业',dataIndex:'deputy_industry'
		},{
			text:'模板内容',dataIndex:'content',align:'left',renderer:function(v){
				return v.replace(/\n/gi,'<br>');
			}
		},{
			text:'模板示例',dataIndex:'example',renderer:function(v){
				return v.replace(/\n/gi,'<br>');
			}
		},{
			text:'状态',dataIndex:'status',type:'checkbox',editor:true
		}],
		itemclick:function(){
			btn(false); //选中记录让按钮可用
		},
		beforeload:function(){
			btn(true); //刷新数据让按钮不可用
		},
		load:function(){
			if(!ISDEMO && NOWURL!='http://127.0.0.1/app/xinhu/' && NOWURL!='http://127.0.0.1/rockwuye/')js.msg('msg', '目前模版消息并没有应用到系统中，我们这个只是测试开发而已');
		}
	});
	
	//编辑和删除按钮可用状态切换
	function btn(bo){
		get('edit_{rand}').disabled = bo;
		get('fstest_{rand}').disabled = bo;
	}
	
	
	//给按钮添加事件
	var c={
		
		//刷新
		refresh:function(){
			a.reload();//刷新列表的方法
		},
		
		//删除方法
		del:function(){
		},
		
		//编辑
		clickwin:function(){
			var d = a.changedata;
			addtabs({name:'模版消息['+d.title+']编辑',url:'system,wxgzh,tpledit,id='+d.id+'',num:'tpledit'+d.id+''});
		},
		
		//获取消息模版列表，使用ajax去获取
		getlist:function(){
			js.msg('wait','处理中...');
			js.ajax(js.getajaxurl('getlisttpl','{mode}','{dir}'),false,function(ret){
				if(ret.errcode=='0'){
					js.msg('success', '获取成功');
					a.reload();//刷新列表
				}else{
					js.msg('msg', ret.msg);//失败就提醒
				}
			},'get,json');
		},
		testcs:function(){
			var d=a.changedata;
			if(isempt(d.modeparams)){
				js.msg('msg','此模版消息没有关联OA系统模版，无法发送');
				return;
			}
			js.prompt('发模版消息测试看看','接收人微信openid(系统→微信公众号→授权微信人)下获取：',function(lx,txt){
				if(lx=='yes'&&txt)c.testcss(txt)
			});
		},
		testcss:function(exopid){
			var d=a.changedata;
			js.msg('wait','发送中...');
			js.ajax(js.getajaxurl('testsendtpl','{mode}','{dir}',{id:d.id,openid:exopid}),false,function(ret){
				if(ret.success){
					js.msg('success', '发送状态：'+ret.data+'');
				}else{
					js.msg('msg', ret.msg);
				}
			},'get,json');
		}
	};
	
	js.initbtn(c); //这个是将上面的方法绑定到对应按钮上
});
</script>
<div>


<table width="100%"><tr>
	<td nowrap>
		<button class="btn btn-primary" click="getlist" type="button">获取模板消息列表</button> &nbsp; 
		<button class="btn btn-default" click="refresh" type="button"><i class="icon-refresh"></i> 刷新</button> &nbsp; 
	</td>
	
	<td width="80%"></td>
	<td align="right" nowrap>
		<button class="btn btn-default" id="fstest_{rand}" disabled click="testcs" type="button">测试发模板消息</button> &nbsp; 
		<button class="btn btn-info" id="edit_{rand}" click="clickwin,1" disabled type="button"><i class="icon-edit"></i> 编辑 </button>
	</td>
</tr>
</table>
</div>
<div class="blank10"></div>
<div id="veiw_{rand}"></div>
<div class="tishi">模版消息使用：微信规定需认证的微信服务号。</div>