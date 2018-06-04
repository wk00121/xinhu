<?php if(!defined('HOST'))die('not access');?>
<script >
$(document).ready(function(){
	
	var barr = {};
	var c={
		init:function(){
			js.ajax(js.getajaxurl('getinfo','{mode}','{dir}'),{},function(a){
				barr = a;
				for(var i in a)$('#'+i+'_{rand}').val(a[i]);
			},'get,json');
		},
		save:function(o){
			var d={};
			for(var i in barr){
				d[i] = $('#'+i+'_{rand}').val();
			}
			if(d.title==''){
				js.msg('msg','系统标题不能为空');
				return;
			}
			js.ajax(js.getajaxurl('savecong','{mode}','{dir}'), d, function(s){
				if(s!='ok')js.msg('msg', s);
			},'post',false,'保存中...,保存成功');
		},
		blurls:function(o){
			var val = strreplace(o.value);
			if(val=='')return;
			var la  = val.substr(val.length-1);
			if(la!='/')val+='/';
			o.value=val;
		}
	};
	js.initbtn(c);
	c.init();
	
	$('#url_{rand}').blur(function(){
		c.blurls(this);
	});
	$('#localurl_{rand}').blur(function(){
		c.blurls(this);
	});
});
</script>

<div align="left">
<div  style="padding:10px;">

		
		<table cellspacing="0" width="900" border="0" cellpadding="0">
		
		<tr>
			<td  colspan="4"><div class="inputtitle">基本信息
			<div style="padding:5px;line-height:18px;font-size:12px;color:#888888">此保存在配置文件下，也可以自己打开配置文件(webmain/webmainConfig.php)来修改</div>
			</div></td>
		</tr>
	
		<tr>
			<td  align="right" width="15%" width="180">系统标题：</td>
			<td class="tdinput"  width="35%" ><input id="title_{rand}" class="form-control"></td>
		
			<td  align="right"  width="15%" >APP移动端上标题：</td>
			<td class="tdinput"  width="35%"><input id="apptitle_{rand}" class="form-control"></td>
		</tr>
		
		<tr>
			<td  align="right">REIM即时通信标题：</td>
			<td class="tdinput"><input id="reimtitle_{rand}" class="form-control"></td>
			
			<td  align="right"><a target="_blank" href="<?=URLY?>">信呼官网</a>key：</td>
			<td class="tdinput"><input id="xinhukey_{rand}" class="form-control">
			<font color="#888888">用于在线升级使用,看<a target="_blank" href="<?=URLY?>view_xhkey.html">[帮助]</a>获取</font></td>
		</tr>
		
		<tr>
			<td  align="right">系统URL地址：</td>
			<td class="tdinput"><input id="url_{rand}" class="form-control"><font color="#888888">可以为空不用设置的，<a onclick="get('url_{rand}').value=''" href="javascript:;">[清空]</a></font></td>
		
			<td  align="right">系统本地地址：</td>
			<td class="tdinput"><input id="localurl_{rand}" class="form-control">
			<font color="#888888">用于计划任务异步任务使用，没有可跟系统URL一样</font></td>
		</tr>
		
	
		
		<tr>
			<td  colspan="4"><div class="inputtitle">高级设置</div></td>
		</tr>
		
		<tr>
			<td  align="right">debug模式：</td>
			<td class="tdinput"><select id="debug_{rand}"  class="form-control"><option value="0">上线模式</option><option value="1">开发调试模式</option></select></td>
			
			<td  align="right">操作数据库驱动：</td>
			<td class="tdinput"><select id="db_drive_{rand}"  class="form-control"><option value="mysql">mysql(不推荐)</option><option value="mysqli">mysqli</option><option value="pdo">pdo</option></select></td>
		</tr>
		
		<tr>
			<td  align="right">异步任务key：</td>
			<td class="tdinput"><input id="asynkey_{rand}" class="form-control"></td>
	
			<td  align="right">对外接口openkey：</td>
			<td class="tdinput"><input id="openkey_{rand}" class="form-control"></td>
		</tr>
		
	
		
		<tr>
			<td  align="right">提醒消息发送方式：</td>
			<td class="tdinput"><select id="asynsend_{rand}"  class="form-control"><option value="0">同步发送</option><option value="1">异步发送(自己服务端)</option></select>
			<font color="#888888">提醒消息发送微信消息提示发送，邮件提醒发送等，异步发送能大大提高效率。</font></td>
		
			<td  align="right">是否记录访问sql日志：</td>
			<td class="tdinput"><select id="sqllog_{rand}"  class="form-control"><option value="0">否</option><option value="1">是</option></select><font color="#888888">开启了日志将记录在目录<?=UPDIR?>/sqllog下</font></td>
		</tr>
		
		<tr>
			<td  align="right">详情上线条颜色：</td>
			<td class="tdinput"><input id="bcolorxiang_{rand}" placeholder="用于单据详情默认颜色" maxlength="7" class="form-control"></td>
		
			<td  align="right">PC首页显示：</td>
			<td class="tdinput">REIM：<select id="reim_show_{rand}" ><option value="0">不显示</option><option value="1">显示</option></select>&nbsp;&nbsp;手机版：<select id="mobile_show_{rand}" ><option value="0">不显示</option><option value="1">显示</option></select></td>
		</tr>
		
		<tr>
			<td  align="right">文档转PDF预览：</td>
			<td class="tdinput"><select id="officeyl_{rand}"  class="form-control"><option value="0">自己服务器安装转化插件</option><option value="1">使用官网插件(官网VIP专用)，不需要安装任何插件。</option></select></td>
			
			<td  align="right">登录方式：</td>
			<td class="tdinput"><select id="loginyzm_{rand}"  class="form-control"><option value="0">仅使用帐号+密码</option><option value="1">帐号+密码/手机+验证码</option><option value="2">帐号+密码+验证码</option><option value="3">仅使用手机+验证码</option></select></td>
			
		</tr>
		
		
		<tr>
			<td  align="right"></td>
			<td style="padding:15px 0px" colspan="3" align="left"><button click="save" class="btn btn-success" type="button"><i class="icon-save"></i>&nbsp;保存</button>
		</td>
		</tr>
		
		</table>
</div>
</div>