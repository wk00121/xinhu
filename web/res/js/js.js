/**
*	createname：雨中磐石
*	homeurl：http://www.rockoa.com/
*	Copyright (c) 2016 rainrock (xh829.com)
*	Date:2016-01-01
*/
var PROJECT='',HOST='',token='',adminid=0,nwjsgui=false,adminface='images/noface.png',QOM='xinhu_',device='',DEBUG=false;
var windows	= null;
apiurl		= 'http://127.0.0.1/app/xinhu/';
function initbody(){}
function bodyunload(){}
$(document).ready(function(){
	try{if(typeof(nw)=='object'){nwjsgui = nw;}else{nwjsgui = require('nw.gui');}}catch(e){nwjsgui=false;}
	document.oncontextmenu=new Function("return false");
	adminid		= js.getoption('adminid');
	device		= js.getoption('device');
	if(device==''){
		device  = js.now('time')+'';
		js.setoption('device', device);
	}
	token		= js.getoption('admintoken');
	var lurl 	= location.href;
	HOST	 	= lurl.substr(lurl,lurl.lastIndexOf('/')+1);
	if(lurl.substr(0,4)=='http'){
		var sass = lurl.split('web/');
		apiurl=sass[0];
	}else if(nwjsgui){
		apiurl='http://demo.rockoa.com/';
	}	
	apiurl 		= js.getoption('apiurl', apiurl);
	js.splittime= parseFloat(js.getoption('splittime','0'));
	$(window).unload(function(){
		bodyunload();
		js.onunload();
	});
	try{
		var winobj = js.request('winobj');
		if(nwjsgui)window.focus=function(){nwjsgui.Window.get().focus()}
		if(winobj!='')opener.js.openarr[winobj]=window;
	}catch(e){}
	if(lurl.indexOf('127.0.0')>0)DEBUG=true;
	initbody();
});
var js={path:'index',url:'',bool:false,login:{},initdata:{},scroll:function(){}};
var isIE=true;
if(!document.all)isIE=false;
var get=function(id){return document.getElementById(id)};
var isempt=function(an){var ob	= false;if(an==''||an==null||typeof(an)=='undefined'){ob=true;}if(typeof(an)=='number'){ob=false;}return ob;}
var strreplace=function(str){if(isempt(str))return '';return str.replace(/[ ]/gi,'').replace(/\s/gi,'')}
var strhtml=function(str){if(isempt(str))return '';return str.replace(/\</gi,'&lt;').replace(/\>/gi,'&gt;')}
var form=function(an,fna){if(!fna)fna='myform';return document[fna][an]}
var xy10=function(s){var s1=''+s+'';if(s1.length<2)s1='0'+s+'';return s1;};
js.getarr=function(caa,bo){
	var s='';
	for(var a in caa)s+=' @@ '+a+'=>'+caa[a]+'';
	if(!bo)alert(s);
	return s;
}
js.getarropen=function(caa){
	jsopenararass = caa;
	js.open('js/array.shtml');
}
js.str=function(o){
	o.value	= strreplace(o.value);
}
function winHb(){
	var winH=(!isIE)?window.innerHeight:document.documentElement.offsetHeight;
	return winH;
}
function winWb(){
	var winH=(!isIE)?window.innerWidth:document.documentElement.offsetWidth;
	return winH;
}
js.scrolla	= function(){
	var top	= $(document).scrollTop();
	js.scroll(top);
}
js.request=function(name,dev,url){
	if(!dev)dev='';
	if(!name)return dev;
	if(!url)url=location.href;
	if(url.indexOf('\?')<0)return dev;
	var neurl=url.split('\?')[1];
	neurl=neurl.split('&');
	var value=dev,i,val;
	for(i=0;i<neurl.length;i++){
		val=neurl[i].split('=');
		if(val[0].toLowerCase()==name.toLowerCase()){
			value=val[1];
			break;
		}
	}
	if(!value)value='';
	return value;
}
js.now=function(type,sj){
	if(!type)type='Y-m-d';
	if(type=='now')type='Y-m-d H:i:s';
	var dt,ymd,his,weekArr,Y,m,d,w,H=0,i=0,s=0,W;
	if(typeof(sj)=='string')sj=sj.replace(/\//gi,'-');
	if(/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}/.test(sj)){
		sj=sj.split(' ');
		ymd=sj[0];
		his=sj[1];if(!his)his='00:00:00';
		ymd=ymd.split('-');
		his=his.split(':');
		H = his[0];if(his.length>1)i = his[1];if(his.length>2)s = his[2];
		dt=new Date(ymd[0],ymd[1]-1,ymd[2],H,i,s);
	}else{
		dt=(typeof(sj)=='number')?new Date(sj):new Date();
	}
	weekArr=new Array('日','一','二','三','四','五','六');
	Y=dt.getFullYear();
	m=xy10(dt.getMonth()+1);
	d=xy10(dt.getDate());
	w=dt.getDay();
	H=xy10(dt.getHours());
	i=xy10(dt.getMinutes());
	s=xy10(dt.getSeconds());
	W=weekArr[w];
	if(type=='time'){
		return dt.getTime();
	}else{
		return type.replace('Y',Y).replace('m',m).replace('d',d).replace('H',H).replace('i',i).replace('s',s).replace('w',w).replace('W',W);
	}
}
js.float=function(num,w){
	if(isNaN(num)||num==''||!num||num==null)num='0';
	num=parseFloat(num);
	if(!w&&w!=0)w=2;
	var m=num.toFixed(w);
	return m;	
}
js.splittime=0;
js.getsplit=function(){
	if(!js.servernow)return false;
	var dt=js.now('Y-m-d H:i:s');
	var d1=js.now('time',dt);	
	var d2=js.now('time',js.servernow);
	js.splittime=d1-d2;
}
js.serverdt=function(atype){
	if(!atype)atype='Y-m-d H:i:s';
	var d1=js.now('time')-js.splittime;
	var dt=js.now(atype,d1);
	return dt;
}
js.openarr={};
js.open=function(url,w,h,wina,can,wjcan){
	if(wina){try{var owina	= this.openarr[wina];owina.document.body;owina.focus();return owina;}catch(e){}}
	var ja=(url.indexOf('?')>=0)?'&':'?';
	if(!w)w=600;if(!h)h=500;
	var l=(screen.width-w)*0.5,t=(screen.height-h)*0.5-30;
	var rnd = parseInt(Math.random()*50);
	if(rnd%2==0){l=l+rnd;t=t-rnd;}else{l=l-rnd;t=t+rnd;}
	if(!can)can={};
	var s='resizable=yes,scrollbars=yes,toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,status=no';
	var a1={'left':''+l+'px','top':''+t+'px','width':''+w+'px','height':''+h+'px'};
	a1 = js.apply(a1,can);
	for(var o1 in a1)s+=','+o1+'='+a1[o1]+'';
	if(nwjsgui){
		var ocsn=js.apply({'frame':true,width:w,height:h,x:l,y:t,icon:'images/logo.png'},wjcan);
		if(typeof(nw)=='undefined')ocsn.toolbar=false;
		var opar=nw.Window.open(url, ocsn);
	}else{
		var opar=window.open(url,'',s);
	}
	if(wina)this.openarr[wina]=opar;
	return false;
}
js.onunload=function(){
	var a=js.openarr;
	for(var b in a){
		try{a[b].close()}catch(e){}
	}
	try{
		var winobj = js.request('winobj');
		if(winobj!='')opener.js.openarr[winobj]=false;
	}catch(e){}
}
js.decode=function(str){
	var arr	= {length:-1};
	try{
		arr	= new Function('return '+str+'')();
	}catch(e){}
	return arr;
}
js.move=function(id,rl){
	var _left=0,_top=0,_x=0,_right=0,_y=0;
	var obj	= id;if(!rl)rl='left';
	if(typeof(id)=='string')obj=get(id);
	var _Down=function(e){
		try{
			var s='<div id="divmovetemp" style="filter:Alpha(Opacity=0);opacity:0;z-index:99999;width:100%;height:100%;position:absolute;background-color:#000000;left:0px;top:0px;cursor:move"></div>';
			$('body').prepend(s);
			_x = e.clientX;_y = e.clientY;_left=parseInt(obj.style.left);_top=parseInt(obj.style.top);_right=parseInt(obj.style.right);
			document.onselectstart=function(){return false}
		}catch(e1){}		
	}
	var _Move=function(e){
		try{
			var c=get('divmovetemp').innerHTML;
			var x = e.clientX-_x,y = e.clientY-_y;
			if(rl=='left')obj.style.left=_left+x+'px';
			if(rl=='right')obj.style.right=_right-x+'px';
			obj.style.top=_top+y+'px';
		}catch(e1){_Down(e)}
	}
	var _Up=function(){
		document.onmousemove='';
		document.onselectstart='';
		$('#divmovetemp').remove();	
	}
	document.onmousemove=_Move;
	document.onmouseup=_Up;
}
js.tanresize=function(id,hid){
	var _x = 0,_y = 0,_cs=0;
	var obj	= id,hobj=false;
	if(typeof(id)=='string')obj=get(id);
	if(hid)hobj=get(hid);
	document.onselectstart=function(){return false}
	var _Move=function(e){
		var _x1 = e.clientX,_y1=e.clientY;
		if(_cs==0){_x = _x1;_y = _y1;}
		var x = _x1 - _x,y = _y1 - _y;
		var xy = parseInt(obj.style.width);
		xy+=x;
		obj.style.width = ''+xy+'px';
		if(hobj){
			var xy = parseInt(hobj.style.height);
			xy+=y;
			hobj.style.height = ''+xy+'px';
		}
		_x = _x1;_y = _y1;
		_cs++;
	}
	var _Up=function(){
		document.onmousemove='';
		document.onselectstart='';
		$('#divmovetemp').remove();	
	}
	document.onmousemove=_Move;
	document.onmouseup=_Up;
}
js.setdev=function(val,dev){
	var cv	= val;
	if(isempt(cv))cv=dev;
	return cv;
}
js.downshow=function(id){
	js.open(''+apiurl+'?a=down&id='+id+'',600,350);
	return false;
}
js.formatsize=function(size){
	if(size<=0)return '0 KB';
	var arr = new Array('Byte', 'KB', 'MB', 'GB', 'TB', 'PB');
	var e	= Math.floor(Math.log(size)/Math.log(1024));
	var fs	= size/Math.pow(1024,Math.floor(e));
	return js.float(fs)+' '+arr[e];
}
js.cookie=function(name){
	var str=document.cookie,cda,val='',arr,i;
	if(str.length<=0)return '';
	arr=str.split('; ');
	for(i=0;i<arr.length;i++){
		cda=arr[i].split('=');
		if(name.toLowerCase()==cda[0].toLowerCase()){
			val=cda[1];
			break;
		}
	}
	if(!val)val='';
	return val;
}
js.savecookie=function(name,value,d){
	var expires = new Date();
	if(!d)d=365;
	if(!value)d=-10;
	expires.setTime(expires.getTime()+d*24*60*60*1000);
	var str=''+name+'='+value+';expires='+expires.toGMTString()+';path=/';
	document.cookie = str;
}
js.backtop=function(ci){
	if(!ci)ci=0;
	$('body,html').animate({scrollTop:ci});
	return false;
}
js.backto = function(oid){
	if(!get(oid))return;
	var of	= $('#'+oid+'').offset();
	this.backtop(of.top);
	return false;
}
js.applyIf=function(a,b){
	if(!a)a={};
	if(!b)b={};
	for(var c in b)if(typeof(a[c])=='undefined')a[c]=b[c];
	return a;
}
js.apply=function(a,b){
	if(!a)a={};
	if(!b)b={};
	for(var c in b)a[c]=b[c];
	return a;
}
js.tanbody=function(act,title,w,h,can1){
	var can	= js.applyIf(can1,{minbtn:false,reheiid:'',resize:false,html:'',zindex:'160',showfun:function(){},onclose:function(){},bodystyle:'',guanact:'',titlecls:'',btn:[]});
	if(can.mode=='none')can.zindex='95';
	var l=(winWb()-w-50)*0.5,t=(winHb()-h-50)*0.5;
	var s	= '';
	var mid	= ''+act+'_main';
	$('#'+mid+'').remove();
	var posta= 'fixed';
	if(js.path == 'admin')posta='absolute';
	s+='<div id="'+mid+'" tanbody="rock" style="position:'+posta+';background-color:#ffffff;left:'+l+'px;width:'+w+'px;top:'+t+'px;z-index:'+can.zindex+';box-shadow:0px 0px 10px rgba(0,0,0,0.3);">';
	s+='	<div class="title '+can.titlecls+'" style="-moz-user-select:none;-webkit-user-select:none;user-select:none;">';
	s+='		<table border="0"  width="100%" cellspacing="0" cellpadding="0"><tr>';
	s+='			<td height="34" style="font-size:14px; font-weight:bold;color:white; padding-left:8px" width="100%" onmousedown="js.move(\''+mid+'\')" id="'+act+'_title">'+title+'</td>';
	if(can.minbtn)s+='			<td><div onclick="$(\'#'+mid+'\').hide();" id="'+act+'_minbtn" title="最小化" class="tantbtn">一</div></td>';
	s+='			<td><div title="关闭" id="'+act+'_spancancel" class="tantbtn" ><div style="height:16px;overflow:hidden;width:16px;background:url(images/wclose.png);margin-top:12px"></div></div></td>';
	s+='		</tr></table>';
	s+='	</div>';
	s+='	<div id="'+act+'_body" style="'+can.bodystyle+'">';
	s+=can.html;
	s+='	</div>';
	s+='	<div id="'+act+'_bbar" style="padding:5px 10px;background:#eeeeee;line-height:30px" align="right"><span id="msgview_'+act+'"></span>&nbsp;';
	for(var i=0; i<can.btn.length; i++){
		var a	= can.btn[i];
		s+='<a class="webbtn" id="'+act+'_btn'+i+'" onclick="return false" >';
		s+=''+a.text+'</a>&nbsp; ';
	}
	s+='		<a class="webbtn" id="'+act+'_cancel" onclick="return js.tanclose(\''+act+'\',\''+can.guanact+'\')" >取消</a>';
	s+='	</div>';
	if(can.resize)s+='<div onmousedown="js.tanresize(\''+mid+'\',\''+can.reheiid+'\')" id="'+act+'_resizebtn" style="cursor:se-resize" class="tanresize notsel">&nbsp;</div>';
	s+='</div>';
	js.xpbody(act,can.mode);
	$('body').prepend(s);
	var cso = $('#'+act+'_spancancel');
	if(can.closed=='none'){
		$('#'+act+'_cancel').remove();
		cso.remove();
	}else{
		cso.click(function(){
			js.tanclose(act, can.guanact);
			can.onclose();
		});
	}
	if(can.bbar=='none')$('#'+act+'_bbar').remove();
	this.tanoffset(act);
	can.showfun(act);
}
js.tanoffset=function(act){
	var mid=''+act+'_main';
	var lw = get(mid).offsetWidth,lh=get(mid).offsetHeight,l,t;
	l=(winWb()-lw)*0.5;t=(winHb()-lh-20)*0.5;
	if(t<0)t=1;
	$('#'+mid+'').css({'left':''+l+'px','top':''+t+'px'});
}
js.tanclose=function(act, guan){
	if(!isempt(guan)){
		var s= guan.split(',');
		for(var i=0;i<s.length;i++)$('#'+s[i]+'_main').remove();
	}
	$('#'+act+'_main').remove();
	js.xpbody(act,'none');
	return false;
}
js.xpbody=function(act,type){
	if(type=='none'){
		$("div[xpbody='"+act+"']").remove();
		return;
	}
	if(get('xpbg_bodydds'))return false;
	var H	= (document.body.clientHeight<winHb())?winHb()-5:document.body.clientHeight;
	var W	= document.documentElement.scrollWidth+document.body.scrollLeft;
	var bs='<div id="xpbg_bodydds" xpbody="'+act+'" oncontextmenu="return false" style="position:absolute;display:none;width:'+W+'px;height:'+H+'px;filter:Alpha(opacity=30);opacity:0.3;left:0px;top:0px;background-color:#000000;z-index:150"></div>';
	$('body').prepend(bs);	
	$('#xpbg_bodydds').fadeIn(300);
}
js.focusval	= '0';
js.number=function(obj){
	val=strreplace(obj.value);
	if(!val){
		obj.value=js.focusval;
		return false;
	}
	if(isNaN(val)){
		js.msg('msg','输入的不是数字');
		obj.value=js.focusval;
		obj.focus();
	}else{
		obj.value=val;
	}
}
js.setmsg=function(txt,col,ids){
	if(!ids)ids='msgview';
	$('#'+ids+'').html(js.getmsg(txt,col));
}
js.getmsg  = function(txt,col){
	if(!col)col='red';
	var s	= '';
	if(!txt)txt='';
	if(txt.indexOf('...')>0){
		s='<img src="images/loading.gif" height="16" width="16" align="absmiddle"> ';
		col = '#ff6600';
	}	
	s+='<span style="color:'+col+'">'+txt+'</span>';
	if(!txt)s='';
	return s;
}
js.msg = function(lx, txt,sj){
	clearTimeout(this.msgshowtime);
	if(typeof(sj)=='undefined')sj=5;
	$('#msgshowdivla').remove();
	if(lx == 'none' || !lx){
		return;
	}
	if(lx == 'wait'){
		txt	= '<img src="images/loadings.gif" height="14" width="15" align="absmiddle"> '+txt;
		sj	= 60;
	}
	if(lx=='msg')txt='<font color=red>'+txt+'</font>';var t=10;
	var s = '<div onclick="$(this).remove()" id="msgshowdivla" style="position:fixed;top:'+t+'px;z-index:200;" align="center"><div style="padding:8px 20px;background:rgba(0,0,0,0.7);color:white;font-size:16px;">'+txt+'</div></div>';
	$('body').append(s);
	var w=$('#msgshowdivla').width(),l=(winWb()-w)*0.5;
	$('#msgshowdivla').css('left',''+l+'px');
	if(sj>0)this.msgshowtime= setTimeout("$('#msgshowdivla').remove()",sj*1000);	
}
js.repempt=function(stt,v){
	var s = stt;
	if(isempt(s))s=v;
	return s;
}
js.getrand=function(){
	var r;
	r = ''+new Date().getTime()+'';
	r+='_'+parseInt(Math.random()*9999)+'';
	return r;
}
js._bodyclick = {};
js.downbody=function(o1, e){
	this.allparent = '';
	this.getparenta($(e.target),0);
	var a,s = this.allparent,a1;
	for(a in js._bodyclick){
		a1 = js._bodyclick[a];
		if(s.indexOf(a)<0){
			if(a1.type=='hide'){
				$('#'+a1.objid+'').hide();
			}else{
				$('#'+a1.objid+'').remove();
			}
		}
	}
	return true;
}
js.addbody = function(num, type,objid){
	js._bodyclick[num] = {type:type,objid:objid};
}
js.getparenta=function(o, oi){
	try{
	if(o[0].nodeName.toUpperCase()=='BODY')return;}catch(e){return;}
	var id = o.attr('id');
	if(!isempt(id)){
		this.allparent+=','+id;
	}
	this.getparenta(o.parent(), oi+1);
}
js.setoption=function(k,v){
	k=QOM+k;
	try{
		if(isempt(v)){
			localStorage.removeItem(k);
		}else{
			localStorage.setItem(k, escape(v));
		}
	}catch(e){
		js.savecookie(k,escape(v));
	}
	return true;
}
js.getoption=function(k,dev){
	var s = '';
	k=QOM+k;
	try{s = localStorage.getItem(k);}catch(e){s=js.cookie(k);}
	if(s)s=unescape(s);
	if(isempt(dev))dev='';
	if(isempt(s))s=dev;
	return s;
}
js.location = function(url){
	location.href = url;
}


js.apiurl = function(m,a,cans){
	var url=''+apiurl+'api.php?m='+m+'&a='+a+'&adminid='+adminid+'';
	var cfrom='reim';
	url+='&device='+device+'';
	url+='&cfrom='+cfrom+'';
	url+='&token='+token+'';
	if(!cans)cans={};
	for(var i in cans)url+='&'+i+'='+cans[i]+'';
	return url;
}
js.ajaxwurbo = false;
js.ajaxbool	 = false;
js.ajax 	 = function(m,a,d,funs, mod,checs, erfs){
	if(js.ajaxbool && !js.ajaxwurbo)return;
	clearTimeout(js.ajax_time);
	var url = js.apiurl(m,a);
	js.ajaxbool = true;
	if(!mod)mod='wait';
	if(typeof(erfs)!='function')erfs=function(){};
	if(typeof(funs)!='function')funs=function(){};
	if(!checs)checs=function(){};
	var bs = checs(d);
	if(typeof(bs)=='string'&&bs!=''){
		js.msg('msg', bs);
		return;
	}
	if(typeof(bs)=='object')d=js.apply(d,bs);
	if(d)for(var i in d)url+='&'+i+'='+d[i]+'';
	url+='&callback=?';
	var tsnr = '努力处理中...';
	if(mod=='wait')js.msg(mod, tsnr);
	$.getJSON(url,function(ret){
		try{modeltabs('none');}catch(e){}
		js.ajaxbool = false;
		clearTimeout(js.ajax_time);
		if(ret.code!=200){
			js.setmsg(ret.msg);	
			js.msg('msg', 'err1:'+ret.msg);
			erfs(ret.msg);
		}else{
			js.setmsg('');
			js.msg('none');
			funs(ret.data);
		}
	});
	js.ajax_time = setTimeout(function(){
		try{modeltabs('none');}catch(e){}
		if(js.ajaxbool){
			var str = 'Error:请求超时?';
			$('#listmenutishi').remove();
			js.setmsg(str);
			js.msg('msg',str);
			js.ajaxbool = false;
			erfs(str);
		}
	}, 1000*30);
}

js.initbtn = function(obj){
	var o = $("[click]"),i,o1,cl;
	for(i=0; i<o.length; i++){
		o1	= $(o[i]);
		cl	= o1.attr('clickadd');
		if(cl!='true'){
			o1.click(function(evt){
				var cls = $(this).attr('click');
				if(typeof(cls)=='string'){
					cls=cls.split(',');
					obj[cls[0]](this, cls[1], evt);
				}
				return false;
			});
		}
	}
	o.attr('clickadd','true');
}
js.isimg = function(lx){
	var ftype 	= '|png|jpg|bmp|gif|jpeg|';
	var bo		= false;
	if(ftype.indexOf('|'+lx+'|')>-1)bo=true;
	return bo;
}

js.setselectdata = function(o, data, vfs, devs){
	var i,ty = data,sv;
	if(!data)return;	
	if(!vfs)vfs='name';	
	if(typeof(devs)=='undefined')devs=-1;
	for(i=0;i<ty.length;i++){
		o.options.add(new Option(ty[i].name,ty[i][vfs]));
		if(i==devs)sv=ty[i][vfs];
	}
	if(sv)o.value=sv;
}
js.changeusers=function(tit,lx, oncls){
	return js.changeuser(lx, '', '',tit, {'onselect':oncls});
}
js.changeuser=function(lx, ots, ots1,tit, ocans){
	var h = winHb()-70;if(!ocans)ocans={};
	if(!ots)ots='';if(!ots1)ots1='';if(!tit)tit='请选择...';
	if(h>400)h=400;
	js.tanbody('changeaction',tit,300,h,{
		html:'<div id="showuserssvie" style="height:'+h+'px"></div>',
		bbar:'none'
	});
	var can = {
		'changetype': lx,
		'showview' 	: 'showuserssvie',
		'titlebool'	:false,
		'oncancel'	:function(){
			js.tanclose('changeaction');
		},
		'_tempbtype':ots,'_temptypes':ots1,
		'onselect':function(sna,sid){
			js.changeok(sna,sid,this._tempbtype,this._temptypes);
		}
	};
	for(var i in ocans)can[i]=ocans[i];
	$('#showuserssvie').chnageuser(can);
	return false;
}

js.changeok=function(sna,sid, blx,plx){
	
}
js.debug	= function(s){
	if(!DEBUG)return;
	if(typeof(console)!='object')return;
	console.log(s);
}
js.alert = function(txt,tit,fun){
	js.confirm(txt, fun, '', tit, 2, '');
}
js.wait	= function(txt,tit,fun){
	js.confirm(txt, fun, '', tit, 3, '');
}
js.confirm	= function(txt,fun, tcls, tis, lx,ostr,bstr){
	if(!lx)lx=0;
	var h = '<div style="padding:20px;line-height:30px" align="center">',w=300;
	if(lx==1){
		if(!tcls)tcls='';if(!ostr)ostr='';if(!bstr)bstr='';
		h+=''+ostr+'';
		h+='<div align="left" style="padding-left:10px">'+txt+'</div>';
		h+='<div ><input value="'+tcls+'" class="input" id="confirm_input" style="width:230px"></div>'+bstr+'';
	}else if(lx==3){
		h+='<img src="images/mloading.gif" height="32" width="32" align="absmiddle">&nbsp; '+txt+'';
	}else{
		h+='<img src="images/helpbg.png" align="absmiddle">&nbsp; '+txt+'';
	}
	h+='</div>';
	h+='<div style="padding:10px" align="center">';
	h+='	<button id="confirm_btn1" style="padding:5px 10px"  class="webbtn" sattr="yes" type="button"><i class="icon-ok"></i>&nbsp;确定</button>';
	if(lx<2)h+=' &nbsp;  &nbsp;  &nbsp;  &nbsp; <button sattr="no" style="padding:5px 10px; background-color:#888888" class="webbtn" id="confirm_btn" type="button"><i class="icon-remove"></i>&nbsp;取消</button>';
	h+='</div>';
	h+='<div class="blank10"></div>';
	if(!tcls)tcls='danger';if(lx==1)tcls='info';
	if(!tis)tis='<i class="icon-question-sign"></i>&nbsp;系统提示';
	js.tanbody('confirm', tis, w, 200,{closed:'none',bbar:'none',html:h,titlecls:tcls});
	function backl(e){
		var jg	= $(this).attr('sattr'),val=$('#confirm_input').val();
		if(val==null)val='';
		if(typeof(fun)=='function'){
			var cbo = fun(jg, val);
			if(cbo)return false;
		}
		js.tanclose('confirm');
		return false;
	}
	$('#confirm_btn1').click(backl);
	if(get('confirm_btn')){
	$('#confirm_btn').click(backl);
	get('confirm_btn').focus();}
	if(lx==1)get('confirm_input').focus();
}
js.prompt = function(tit,txt,fun, msg, ostr,bstr){
	js.confirm(txt, fun, msg, tit, 1, ostr,bstr);
}
js.fileall=',aac,ace,ai,ain,amr,app,arj,asf,asp,aspx,av,avi,bin,bmp,cab,cad,cat,cdr,chm,com,css,cur,dat,db,dll,dmv,doc,docx,dot,dps,dpt,dwg,dxf,emf,eps,et,ett,exe,fla,ftp,gif,hlp,htm,html,icl,ico,img,inf,ini,iso,jpeg,jpg,js,m3u,max,mdb,mde,mht,mid,midi,mov,mp3,mp4,mpeg,mpg,msi,nrg,ocx,ogg,ogm,pdf,php,png,pot,ppt,pptx,psd,pub,qt,ra,ram,rar,rm,rmvb,rtf,swf,tar,tif,tiff,txt,url,vbs,vsd,vss,vst,wav,wave,wm,wma,wmd,wmf,wmv,wps,wpt,wz,xls,xlsx,xlt,xml,zip,';
js.filelxext = function(lx){
	if(js.fileall.indexOf(','+lx+',')<0)lx='wz';
	return lx;
}