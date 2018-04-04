var oldpass='',initlogo='images/logo.png',olduser,loginyzm='';

function getpassobj(){
	return $('input[type=password]');
}

function initbody(){
	
	form('adminuser').focus();
	oldpass	= getpassobj().val();
	olduser	= form('adminuser').value;
	if(form('adminuser').value!=''){
		getpassobj().focus();
	}
	
	resizewh();
	$(window).resize(resizewh);
	var sf = js.getoption('loginface');
	if(sf)get('imglogo').src=sf;
	$(form('adminuser')).change(function(){
		changeuserface(this.value);
	});
	yunanimate();
}
function yunanimate(){
	var whe=winWb();
	//$('#yun1').animate({'left':''+(whe)+'px'},10000);
	//$('#yun2').animate({'left':''+(whe)+'px'},20000);
}
function resizewh(){
	var h = ($(document).height()-500)*0.5;
	$('#topheih').css('height',''+h+'px');
}
function changeuserface(v){
	var sf = js.getoption('loginface');
	if(!sf)return;
	if(v==''||v!=olduser){
		get('imglogo').src=initlogo;
	}else{
		get('imglogo').src=sf;
	}
}
function loginsubmit(){
	if(js.bool)return false;
	var user = form('adminuser').value;
	var pass = getpassobj().val();
	if(user==''){
		js.setmsg('用户名不能为空','red');
		form('adminuser').focus();
		return false;
	}
	if(pass==''){
		js.setmsg('密码不能为空','red');
		getpassobj().focus();
		return false;
	}
	js.tanstyle = 1;
	try{localStorage.clear();}catch(e){}
	js.setmsg('登录中...','blue');
	form('button').disabled=true;
	var data	= js.getformdata();
	var url		= js.getajaxurl('check','login');
	data.jmpass	= 'false';
	data.device = device;
	data.adminuser = jm.base64encode(user);
	data.adminpass = jm.base64encode(pass);
	data.yanzm    = loginyzm;
	if(oldpass==pass)data.jmpass= 'true';
	js.bool		= true;
	loginyzm	= '';
	js.ajax(url,data,function(a){
		if(a.success){
			get('imglogo').src=a.face;
			js.setoption('loginface', a.face);
			var burl = js.request('backurl');
			var curl = (burl=='')?'?m=index':jm.base64decode(burl);
			js.setmsg('登录成功,<a href="'+curl+'">跳转中</a>...','green');
			js.location(curl);
		}else{
			js.setmsg(a.msg,'red');
			form('button').disabled=false;
			js.bool	= false;
			if(a.shouji){
				mobilejsho = a.mobile;
				js.prompt('输入手机验证码','手机号：'+a.shouji+'&nbsp;<span><a class="zhu" href="javascript:;" onclick="getcodes(this)">[获取验证码]</a></span>',function(jg,txt){
					if(jg=='yes' && txt){
						loginyzm = txt;
						loginsubmit();
					}
				});
			}
		}
	},'post,json');
}

function getcodes(o1){
	var da = {'mobile':mobilejsho,'device':device};
	var o2 = $(o1).parent();
	o2.html(js.getmsg('获取中...'));
	js.ajax('api.php?m=yanzm',da,function(a){
		if(a.success){
			o2.html(js.getmsg('获取成功','green'));
		}else{
			o2.html(js.getmsg(a.msg));
		}
	},'post,json');
}