<?php if(!defined('HOST'))die('not access');?>
<script >
$(document).ready(function(){
	
	
	var c={
		init:function(){
			var o = get('btnss{rand}');
			o.value='加载中...';
			js.ajax(js.getajaxurl('gettotal','{mode}','{dir}'),false,function(ret){
				var curl = '<?=URLY?>user_index_sms_a.html';
				if(ret.success){
					$('#stotal{rand}').html(ret.data.smsinfo);
					$('#typetext{rand}').html(ret.data.typetext);
					if(ret.data.automy=='1')$('#tessh{rand}').show();
					curl = ret.data.chongurl;
				}else{
					js.msg('msg', ret.msg);
				}
				get('chong{rand}').href=curl;
				get('sms_iscb_{rand}').value=ret.sms_iscb;
				get('sms_cbnum_{rand}').value=ret.sms_cbnum;
				get('sms_apikey_{rand}').value=ret.sms_apikey;
				
				o.value='刷新';
			},'get,json');
		},
		ceshi:function(){
			js.prompt('测试发短信','输入手机号码(测试成功会扣除一条短信)',function(lx,txt){
				if(lx=='yes'&&txt)c.sheniokx(txt)
			});
		},
		sheniokx:function(sj){
			js.msg('wait','发送中...');
			js.ajax(js.getajaxurl('testsend','{mode}','{dir}'),{mobile:sj},function(ret){
				if(ret.success){
					js.msg('success','测试发送成功');
					c.init();
				}else{
					js.msg('msg', ret.msg);
				}
			},'get,json');
		},
		
		save:function(){
			js.msg('wait','保存中...');
			js.ajax(js.getajaxurl('cogsave','{mode}','{dir}'),{
				sms_iscb:get('sms_iscb_{rand}').value,
				sms_cbnum:get('sms_cbnum_{rand}').value,
				sms_apikey:get('sms_apikey_{rand}').value
			},function(ret){
				js.msg('success','保存成功');
			},'get');
		}
	};
	js.initbtn(c);
	c.init();
});
</script>

<div align="left">
<div  style="padding:10px;">
		
	<table cellspacing="0" width="550" border="0" cellpadding="0">
	
	<tr>
		<td  colspan="2"><div class="inputtitle">基本信息
		</div></td>
	</tr>

	<tr>
		<td  align="right" width="180">剩余短信：</td>
		<td class="tdinput"><span id="stotal{rand}">0.00元(0条)</span>，<a id="chong{rand}" class="btn btn-success btn-xs" href="<?=URLY?>user_index_sms_a.html" target="_blank">去充值</a></td>
	</tr>
	
	<tr>
		<td  align="right" width="180">官网用户类型：</td>
		<td class="tdinput"><span id="typetext{rand}">普通用户</span></td>
	</tr>
	
	
	<tr style="display:none" id="tessh{rand}">
		<td  align="right">短信APIKEY：</td>
		<td class="tdinput"><input id="sms_apikey_{rand}"  style="width:250px" class="form-control">&nbsp;<a href="<?=URLY?>view_smsapi.html" target="_blank">如何获取?</a></td>
	</tr>
	
	<tr>
		<td  align="right" ></td>
		<td class="tdinput">
		<input type="button" click="init" id="btnss{rand}" value="刷新" class="btn btn-default">&nbsp; &nbsp;
		<input type="button" click="ceshi" id="test{rand}" value="测试发送" class="btn btn-default">&nbsp;&nbsp;
		<a href="<?=URLY?>view_dxto.html" target="_blank">[使用帮助]</a>
		</td>
	</tr>
	
	<tr>
		<td  colspan="2"><div class="inputtitle">功能设置</div></td>
	</tr>
	
	<tr>
		<td  align="right">是否开短信催办：</td>
		<td class="tdinput"><select id="sms_iscb_{rand}" class="form-control"><option value="0">不开启</option><option value="1">开启</option></select></td>
	</tr>
	
	<tr>
		<td  align="right">短信催办模版编号：</td>
		<td class="tdinput"><input id="sms_cbnum_{rand}" maxlength="8" value="defnum" class="form-control"></td>
	</tr>
	
	<tr>
		<td  align="right"></td>
		<td style="padding:15px 0px" colspan="3" align="left"><button click="save" class="btn btn-success" type="button"><i class="icon-save"></i>&nbsp;保存</button>
	</td>
	</tr>
	
	
	</table>
	<div class="tishi"></div>
</div>
</div>
