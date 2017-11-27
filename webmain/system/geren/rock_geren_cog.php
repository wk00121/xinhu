<?php if(!defined('HOST'))die('not access');?>
<script>
$(document).ready(function(){
	
	var valchange = ''+adminstyle;
	
	var c = {
		init:function(){
			js.ajax(js.getajaxurl('getinit','{mode}','{dir}'),false,function(ret){
				get('gerentodo{rand}').checked = (ret.gerentodo=='1');
			},'get,json');
			$("input[name='_stylechange']:eq("+valchange+")")[0].checked=true;
		},
		savecog:function(){
			js.msg('wait','保存中...');
			js.ajax(js.getajaxurl('cogsave','{mode}','{dir}'),{
				gerentodo:get('gerentodo{rand}').checked ? 1 : 0,
			},function(ret){
				js.msg('success','保存成功');
			},'get');
		},
		savepass:function(o1){
			var fm		= 'form_{rand}';
			var msgview		= 'msgview_{rand}';
			
			var opass	= form('passoldPost',fm).value;
			var pass	= form('passwordPost',fm).value;
			var pass1	= form('password1Post',fm).value;
			
			if(opass==''){
				js.setmsg('旧密码不能为空','red', msgview);
				form('passoldPost',fm).focus();
				return false;
			}

			if(pass.length <4){
				js.setmsg('新密码不能少于4个字符','red', msgview);
				form('passwordPost',fm).focus();
				return false;
			}
			if(!/[a-zA-Z]{1,}/.test(pass) || !/[0-9]{1,}/.test(pass)){
				js.setmsg('新密码必须使用字母+数字','red', msgview);
				form('passwordPost',fm).focus();
				return false;
			}

			if(opass==pass){
				js.setmsg('新密码不能和旧密码相同','red', msgview);
				form('passwordPost',fm).focus();
				return false;
			}
			
			if(pass!=pass1){
				js.setmsg('确认密码不一致','red', msgview);
				form('password1Post',fm).focus();
				return false;
			}

			var data	= js.getformdata(fm);
			form('submitbtn',fm).disabled=true;
			js.setmsg('修改中...','#ff6600', msgview);
			$.post(js.getajaxurl('editpass','geren','system'),data,function(da){
				if(da=='success'){
					js.setmsg('密码修改成功','green', msgview);
				}else{
					if(da=='')da='修改失败';
					js.setmsg(da,'red', msgview);
					form('submitbtn',fm).disabled=false;
				}
			});
		},
		tesgs:function(o1,lx){
			$('#tagsl{rand}').find('li').removeClass('active');
			o1.className='active';
			$('#tablstal0{rand}').hide();
			$('#tablstal1{rand}').hide();
			$('#tablstal2{rand}').hide();
			
			$('#tablstal'+lx+'{rand}').show();
		},
		savestyle:function(){
			adminstyle = valchange;
			js.ajax(js.getajaxurl('changestyle','geren','system'),{style:valchange},function(s){
				js.msg('success','保存成功');
			});
		}
	};
	js.initbtn(c);
	c.init();
	
	$("input[name='_stylechange']").click(function(){
		var val = this.value;
		valchange=val;
		get('mainstylecss').href='webmain/css/style'+val+'.css?'+Math.random()+'';
	});
});
</script>
<div style="padding:10px">
	<ul id="tagsl{rand}" class="nav nav-tabs">
	  
	  <li click="tesgs,0" class="active">
		<a><i class="icon-cog"></i> 基本设置</a>
	  </li>
	  <li click="tesgs,1">
		<a><i class="icon-lock"></i> 修改密码</a>
	  </li>
	
	 <li click="tesgs,2">
		<a><i class="icon-magic"></i> 切换皮肤</a>
	  </li>
	  
	</ul>

	<div style="padding-top:20px">
	
		<table cellspacing="0" id="tablstal0{rand}" border="0" cellpadding="0">

	<tr>
		<td  align="right" width="100"></td>
		<td class="tdinput"><label><input id="gerentodo{rand}" type="checkbox"> 后台不显示提醒消息</label></td>
	</tr>
	
	
	
	<tr>
		<td  align="right"></td>
		<td style="padding:15px 0px" colspan="3" align="left"><button click="savecog" class="btn btn-success" type="button"><i class="icon-save"></i>&nbsp;保存</button>
	</td>
	</tr>
	
	
	</table>
	
	
	<form  id="tablstal1{rand}" style="display:none" name="form_{rand}">
	<table cellspacing="0"  cellpadding="0">
	<tr>
		<td width="100" align="right" height="50">旧密码：</td>
		<td><input style="width:250px" name="passoldPost" type="password" class="form-control"></td>
	</tr>
	
	<tr>
		<td align="right" height="70">新密码：</td>
		<td><input style="width:250px" name="passwordPost" type="password" class="form-control"></td>
	</tr>
	
	<tr>
		<td align="right" height="70">确认密码：</td>
		<td><input style="width:250px" name="password1Post" type="password" class="form-control"></td>
	</tr>
	

	
	<tr>
	<td height="60" align="right"></td>
	<td align="left"><input class="btn btn-success" click="savepass" name="submitbtn" value="修改" type="button">&nbsp;<span id="msgview_{rand}"></span>
	</td>
	</tr>
	
	</table>
	</form>
	
	
	<table  id="tablstal2{rand}" style="display:none;margin-left:70px">
		<tr>
			<td align="center" style="padding:15px"><label><div style="width:60px;height:60px;overflow:hidden; background-color:#1389D3"></div><input type="radio" name="_stylechange" value="0"></label></td>
			<td align="center" style="padding:15px"><label><div style="width:60px;height:60px;overflow:hidden; background-color:#1ABC9C"></div><input type="radio" name="_stylechange" value="1"></label></td>
			<td align="center" style="padding:15px"><label><div style="width:60px;height:60px;overflow:hidden; background-color:#E95420"></div><input type="radio" name="_stylechange" value="2"></label></td>
		</tr>
		
		<tr>
			<td align="center" style="padding:15px"><label><div style="width:60px;height:60px;overflow:hidden; background-color:#996699"></div><input type="radio" name="_stylechange" value="3"></label></td>
			<td align="center" style="padding:15px"><label><div style="width:60px;height:60px;overflow:hidden; background-color:#2C3E50"></div><input type="radio" name="_stylechange" value="4"></label></td>
			<td align="center" style="padding:15px"><label><div style="width:60px;height:60px;overflow:hidden; background-color:#CC0033"></div><input type="radio" name="_stylechange" value="5"></label></td>
		</tr>
		
		<tr>
			<td style="padding-left:20px"><input class="btn btn-success" click="savestyle" name="submitbtn" value="保存修改" type="button"></td>
		</tr>
	</table>

	</div>
</div>