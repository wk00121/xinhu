var userarr = {},deptarr={},agentarr={},connectbool=false,fromrecid='',fromwshost='',version='',relianshotime_time,otherlogin=false,grouparr= {},maindata={},tabsarr={},nowtabs={},opentabs=[],chatobj={},notifyobj,agentobj={},systemtitle,windowfocus=true,enterbool=false;
function initbody(){im.init();}
function addtabs(d){
	$('.rockmenu').remove();
	var d=js.apply({width:500,height:400,num:js.getrand(),rand:js.getrand(),scroll:true,face:'images/white.gif',onshow:function(){},onclose:function(){},reheiid:''},d),num=d.num,i,ura;
	nowtabs=d;
	if(changetabs(num))return true;
	var ura	= d.url.split(',');
	var url = 'html/xinhu_'+ura[0]+'.html?rnd='+Math.random()+'';
	var s='<div temp="tabs" onclick="changetabs(\''+num+'\')" id="tabs_'+num+'" class="lists active"><img src="'+d.face+'" align="absmiddle"> '+d.name+'<span class="close" onclick="closetabs(\''+num+'\')"><i class="icon-remove-sign"></i></span>';
	s+='<span id="tabs_stotal_'+num+'" class="badge bqs"></span>';
	s+='</div>';
	var tit = '<img src="'+d.face+'" height="24px" width="24px" align="absmiddle"> '+d.name+'';
	if(num=='home')s='';
	js.tanbody(num,d.name,d.width,d.height,{
		html:'<div id="content_'+num+'"><div style="padding:100px 50px" align="center"><img src="images/mloading.gif" align="absmiddle"></div></div>',
		bbar:'none',mode:'none',
		minbtn:true,resize:true,
		reheiid:d.reheiid,
		onclose:function(){
			$('#tabs_'+num+'').remove();
		}
	});
	$('#'+num+'_minbtn').click(function(){
		nowtabs={};
	});
	$('#tabs_'+num+'').remove();
	var s = '<div id="tabs_'+num+'" onclick="taskbarclick(\''+num+'\');">'+tit+' <span id="tabs_stotal_'+num+'" class="badge"></span></div>';
	$('.os_taskbar_list').append(s);
	var urlpms= '';
	for(i=1;i<ura.length;i++){
		var nus	= ura[i].split('=');
		urlpms += ",'"+nus[0]+"':'"+nus[1]+"'";
	}
	if(urlpms!='')urlpms = urlpms.substr(1);
	$.ajax({
		url:url,
		type:'get',
		success: function(da){
			modeltabs('none');
			var s = da;
				s = s.replace(/\{rand\}/gi, d.rand);
				s = s.replace(/\{params\}/gi, "var params={"+urlpms+"};");
			var obja = $('#content_'+num+'');
			obja.html(s);
			d.onshow();
			js.tanoffset(num);
		},
		error:function(){
			modeltabs('none');
			var s = 'Error:加载出错喽,'+url+'';
			$('#content_'+num+'').html(s);
		}
	});
	tabsarr[num] = d;
	changetabs(num);
	$('#'+num+'_main').mousedown(function(){
		var s = this.id.replace('_main','');
		changetabs(s);
	});
}
function taskbarclick(num){
	if(nowtabs.num!=num){
		changetabs(num);
	}else{
		var o = get(''+num+'_main');
		if(o.style.display=='none'){
			changetabs(num);
		}else{
			o.style.display='none';
		}
	}
}
function modeltabs(nr, sw){
	$('#tabsmodestese').remove();
	if(nr=='none')return;
	var s='<div id="tabsmodestese" oncontextmenu="return false" style="position:absolute;width:100%;height:100%;filter:Alpha(opacity=20);opacity:0.2;left:0px;top:0px;background-color:#000000;z-index:180;"><div style="margin-top:200px;color:white;text-align:center"><img src="images/mloading.gif" align="absmiddle">&nbsp;加载中...</div></div>';
	$('#content_'+sw+'').append(s);
}
function changetabs(num, uses){
	var bo = false;
	$('div[tanbody="rock"]').css('z-index', '95');
	$('#chatimdiv').css('z-index', '95');
	$('#tabs_stotal_'+num+'').html('');
	if(get('content_'+num+'')){
		var ass	= tabsarr[num];
		$('#'+num+'_main').css('z-index', '96').show();
		if(ass != nowtabs)ass.onshow();
		nowtabs	= ass;
		bo = true;
	}
	if(uses)$('#'+num+'').css('z-index', '96');
	return bo;
}
function closetabs(num){
	tabsarr[num] = false;
	js.tanclose(num);
	$('#tabs_'+num+'').remove();
}
function closenowtabs(){
	if(get('xpbg_bodydds')){
		js.tanclose($('#xpbg_bodydds').attr('xpbody'));
	}else{
		closetabs(nowtabs.num);
	}
}
function bodyunload(){
	nwjs.removetray();
}
function connectserver(){
	if(otherlogin)return;
	if(isempt(fromrecid)||fromwshost.indexOf('ws:')!=0){
		js.msg('msg','暂时无法使用即时通信功能<br>请先到后台设置服务器信息',-1);
		return;
	}
	setTimeout('linkbooschange()',10*1000);
	clearTimeout(relianshotime_time);
	if(typeof(websocketobj)!='undefined'){
		websocketobj.connect();
		js.msg('wait','连接中...');
		return false;
	}
	websocketobj = new websocketClass({
		adminid:adminid,
		reimfrom:fromrecid,
		wshost:fromwshost,
		sendname:adminname,
		onerror:function(o,ws){
			connectbool=false;
			js.msg('msg','无法连接服务器1<br><span id="lianmiao"></span><a href="javascript:;" onclick="return connectserver()">[重连]</a>',-1);
			relianshotime(30);
		},
		onmessage:function(str){
			connectbool=true;
			var a=js.decode(str);
			im.receivemesb(a);
		},
		onopen:function(){
			connectbool=true;
			js.msg('none');
			im.initnotify();
		},
		onclose:function(){
			if(otherlogin)return;
			connectbool=false;
			js.msg('msg','连接已经断开了<br><span id="lianmiao"></span><a href="javascript:;" onclick="return connectserver()">[重连]</a>',-1);
			notifyobj.playerrsound();
			relianshotime(30);
		}
	});
	return false;
}
function serversend(a){
	if(!connectbool)return false;
	websocketobj.send(a);
	return true;
}
function sendtype(receid,type){
	serversend({'receid':receid,'type':type});
}
function linkbooschange(){
	if(otherlogin)return;
	if(!connectbool){
		js.msg('msg','无法连接服务器2<br><span id="lianmiao"></span><a href="javascript:;" onclick="return connectserver()">[重连]</a>&nbsp;<a href="http://www.rockoa.com/view_server.html" target="_blank">[帮助]</a>',-1);
		relianshotime(30);
	}
}

js.downshow=function(id){
	if(nwjs.nw){
		im.downfile(id);
	}else{
		js.open(''+apiurl+'?a=down&id='+id+'',600,350);
	}
	return false;
}
function relianshotime(oi){
	clearTimeout(relianshotime_time);
	$('#lianmiao').html('('+oi+'秒后重连)');
	if(oi<=0){
		connectserver();
	}else{
		relianshotime_time=setTimeout('relianshotime('+(oi-1)+')',1000);
	}
}
var im={
	init:function(){
		this.showmyback();
		this.initdata();
		js.ajaxwurbo=true;
		date = js.now();
		$(window).focus(function(){windowfocus=true});
		$(window).blur(function(){windowfocus=false});
		systemtitle = js.getoption('systemtitle','信呼');
		document.title=systemtitle;
		notifyobj=new notifyClass({
			title:'系统提醒',
			sound:'res/sound/todo.ogg',
			sounderr:'',
			soundbo:true
		});
		righthistroboj = $.rockmenu({
			data:[],
			itemsclick:function(d){
				im.rightclick(d);
			}
		});
		
		$(window).click(function(e){
			return js.downbody(this, e);
		});
		$('#setbtns').click(function(){
			im.clickcog(this);
			return false;
		});
		$('#addbtns').click(function(){
			im.addapply(this);
			return false;
		});
		
		var _of = $('#chatimdiv'),_oh = winHb()-180-70;if(_oh>400)_oh=400;
		$('#headercenter').css('height',''+_oh+'px');
		_of.css({'top':''+((winHb()-_of.height()-40)*0.5)+'px'});
		
		$('body').keydown(im.onkeyup);
		nwjs.init();
		$('#keysou').keyup(function(){im.searchss();});
		$('#keysou').click(function(){im.searchss();});
		enterbool = (js.getoption('sendkey')=='1');
		$('#headercenter').perfectScrollbar();
		$('#tabslist').perfectScrollbar();
        document.ondragover=function(e){e.preventDefault();};
        document.ondrop=function(e){e.stopPropagation();e.preventDefault();im.dropfile(e);};
		viewheight = 400;
		this.shownowci=0;
		this.shownow();
		$(window).resize(im.resize);
		setTimeout('im.initnwjs()',1000);
		$(document.body).keydown(function(e){
			var lxs = e.target.nodeName.toLowerCase();
			var bo  = (lxs=='input' || lxs=='textarea');
			if(e.keyCode==8 && !bo)return false;
		});
		
		nwjs.serverdata=function(d){
			return im.serverdata(d);
		}
	},
	initnwjs:function(){
		if(!nwjs.nw)return;
		try{version = nwjs.fs.readFileSync('version.txt');}catch(e){}
	},
	downfile:function(fid){
		if(!nwjs.nw)return;
		js.msg('wait','初始化文件...');
		js.ajax('file','getfile',{id:fid}, function(ret){
			var fpath = ret.filepath,fid=ret.id;
			var durl  = js.apiurl('file','down',{id:ret.id});
			if(fpath.indexOf('.uptemp')<0)durl = ''+apiurl+''+fpath+'';
			ret.filename= ''+fid+'_'+ret.filename+'';
			var savefile= ''+im.getsavepath()+'\\'+ret.filename+'';
			var num = 'downfile'+adminid+'_'+fid+'';
			var zte = ''+QOM+''+num+'_zt';
			js.setoption(num,'{"savefile":"'+savefile.replace(/\\/gi, '|')+'","id":"'+fid+'","filename":"'+ret.filename+'","filesizecn":"'+ret.filesizecn+'"}');
			if(nwjs.fs.existsSync(savefile)){
				js.msg('success','文件已下载，<a onclick="im.opendownpath()" href="javascript:;">打开</a>');
				return;
			}
			js.msg('wait','下载中['+ret.filesizecn+'/0kb]0%...');
			nwjs.downfile({
				url:durl,
				fileid:fid,
				zte:zte,
				filesizecn:	ret.filesizecn,
				filename:	ret.filename,
				filesize:parseFloat(ret.filesize),
				savefile:savefile,
				onjindu:function(jd,jdsize){
					$('#icons_title_downfile').html('下载中('+jd+'%)');
					js.msg('wait','下载中['+this.filesizecn+'/'+js.formatsize(jdsize)+']'+jd+'%...');
					$('#'+this.zte+'').html('下载中('+jd+'%)');
				},
				onsuccess:function(){
					$('#icons_title_downfile').html('下载的文件');
					js.msg('success','下载完成，<a onclick="im.opendownpath()" href="javascript:;">打开</a>');
					$('#'+this.zte+'').html('打开');
				},
				onerror:function(er){
					js.msg('msg', er);
					$('#'+this.zte+'').html(er);
				}
			});
		},'none');
	},
	shownow:function(){
		$('#datenow').html(js.now('H:i:s(周W)<br>Y-m-d'));
		this.shownowci++;
		if(this.shownowci==300)this.indexlunxuns();//5分钟刷新一次
		setTimeout('im.shownow()',1000);
	},
	resize:function(){
		var hei = winHb(),wei = winWb();
		im.resizebgwh();
	},
	zuidhua:function(){
		js.location('mainnw.html');
	},
	initbackd:function(ret){
		if(!ret.userjson)return;
		maindata.darr 	= js.decode(ret.deptjson);
		maindata.uarr 	= js.decode(ret.userjson);
		maindata.garr  	= js.decode(ret.groupjson);
		maindata.aarr 	= js.decode(ret.agentjson);
		maindata.harr 	= js.decode(ret.historyjson);
		maindata.modearr= ret.modearr;
		this.myip 		= ret.ip;
		im.showuser(maindata.uarr);
		adminmyrs 	= userarr[adminid];
		adminname	= adminmyrs.name;
		adminface	= adminmyrs.face;
		nwjs.createtray(adminname, 1);
		
		im.showgroup(maindata.garr);
		im.showagent(maindata.aarr);
		im.showhistory(maindata.harr);
		
		
		get('myface').src=adminface;
		get('taskbarface').src=adminface;
		$('#myname').html(adminname);
		$('#taskbarname').html(adminname);
		$('#myinfor').html(adminmyrs.deptname+'，'+adminmyrs.ranking);
		this.shownowci = 0;
	},
	initdata:function(bo){
		js.ajax('indexreim','indexinit',{}, function(ret){
			im.initbackd(ret);
			js.servernow 	= ret.loaddt;
			js.getsplit();
			if(!bo){
				fromrecid=ret.config.recid;
				fromwshost=jm.base64decode(ret.config.wsurl);
				connectserver();
			}else{
				im.getonline();
			}
		},'none');
	},
	indexlunxuns:function(){
		this.ldata('history');
		this.getonline();
		this.shownowci = 0;
	},
	ldata:function(lx){
		js.ajax('indexreim','ldata',{type:lx,loaddt:jm.base64encode(js.servernow)}, function(ret){
			var lx = ret.type,a=js.decode(ret.json),len=a.length,i;
			js.servernow 	= ret.loaddt;
			if(lx=='history'){for(i=0;i<len;i++)im.showhistorys(a[i], true);}
			if(lx=='group'){maindata.garr = a;im.showgroup(a);}
			if(lx=='user'){maindata.uarr = a;im.showuser(a);}
			if(lx=='agent'){maindata.aarr = a;im.showagent(a);}
			if(lx=='dept'){maindata.darr = a;$('#showdept_0').html('');}
		},'none');
	},
	showuser:function(a){
		var i=0,len=a.length,d;
		for(i=0;i<len;i++){
			d 	= a[i];
			d.i = i;
			userarr[d.id] = d;
		}
	},
	showgroup:function(a){
		var i=0,len=a.length,s,d,o=$('#grouplist_1');
		o.html('');
		$('#grouptotal1').html('('+len+')');
		for(i=0;i<len;i++){
			d 	= a[i];
			s	= '<div style="padding-left:10px" id="group_'+d.id+'" onclick="im.opengroup('+d.id+')"><img src="'+d.face+'" align="absmiddle">'+d.name+'</div>';
			grouparr[d.id] = d;
			o.append(s);
		}	
	},
	tabchagne:function(oi,o1){
		$('.headertab div').removeClass();
		o1.className = 'active';
		$('#headercenter').find("div[tabdiv]").hide();
		$('#headercenter').find("div[tabdiv='"+oi+"']").show();
		if(oi=='1')this.showdept(0,0);
	},
	hidealislist:function(oi, o1){
		$('#grouplist_'+oi+'').toggle();
		var src = 'xiangyou';
		if(get('grouplist_'+oi+'').style.display != 'none')src = 'xiangyou1';
		$(o1).find('img').attr('src','images/im/'+src+'.png');
		if(oi==0)this.showdept(0,0);
	},
	
	showdept:function(pid, xu){
		im.clobbol=true;
		var i=0,len=maindata.darr.length,s='',a,wfj,cls;
		var o = $('#showdept_'+pid+'');
		var tx= o.text();
		if(tx){if(pid!=0){o.toggle();}return;}
		for(i=0;i<len;i++){
			a=maindata.darr[i];
			if(pid==a.pid){
				wfj = 'icon-folder-close-alt';
				if(a.ntotal=='0')wfj='icon-file-alt';
				s='<div style="padding-left:'+(xu*20+10)+'px" onclick="im.showdept('+a.id+','+(xu+1)+')">';
				s+='	<i class="'+wfj+'"></i> '+a.name+'';
				s+='</div>';
				s+='<span id="showdept_'+a.id+'"></span>';
				o.append(s);
				if(pid==0)im.showdept(a.id, xu+1);
			}
		}
		len=maindata.uarr.length;
		for(i=0;i<len;i++){
			a=maindata.uarr[i];
			if(pid==a.deptid){
				cls='offline';if(a.online==1)cls='online';
				s='<div uid="'+a.id+'" class="'+cls+'" style="padding-left:'+(xu*20+10)+'px" onclick="im.openuser('+a.id+')">';
				s+='	<img src="'+a.face+'" align="absmiddle"> '+a.name+' <font>('+a.ranking+')<font>';
				s+='</div>';
				o.append(s);
			}
		}
	},
	showhistory:function(a){
		var i,len=a.length;
		for(i=0;i<len;i++){
			this.showhistorys(a[i]);
		}
	},
	showhistorys:function(d,pad){
		if(!d)return;
		var s,ty,o=$('#historylist'),d1,st,attr,stst;
		ty	= d.type;
		if(ty=='user')d1=userarr[d.receid];
		if(ty=='group')d1=grouparr[d.receid];
		var num = ''+ty+'_'+d.receid+'';
		$('#history_'+num+'').remove();
		if(d1){
			var ops = d.optdt.substr(11,5);
			if(d.optdt.indexOf(date)!=0)ops=d.optdt.substr(5,5);
			attr = 'oncontextmenu="im.rightdivobj=this" tsaid="'+d.receid+'" tsaype="'+d.type+'" ';
			st	 = d.stotal;if(st=='0')st='';
			stst = '';
			if(d.type=='user'){
				stst='uid="'+d.receid+'"';
				if(d1.online==0)stst+=' class="offline"';
			}
			s	= '<div class="lists" '+attr+' rtype="hist" id="history_'+num+'" onclick="im.clickitems(\''+ty+'\','+d.receid+')">';
			s+='<table cellpadding="0" border="0" width="100%"><tr>';
			s+='<td style="padding-right:8px"><div '+stst+' style="height:34px;overflow:hidden"><img src="'+d1.face+'"></div></td>';
			s+='<td align="left" width="100%"><div class="name">'+d1.name+'</div><div class="huicont">'+jm.base64decode(d.cont)+'</div></td>';
			s+='<td align="center" nowrap><span id="histotal_'+num+'" class="badge">'+st+'</span><br><span style="color:#cccccc;font-size:10px">'+ops+'</span></td>';
			s+='</tr></table>';
			s+='</div>';
			if(!pad){o.append(s);}else{o.prepend(s)}
		}
		$('#historylist_tems').hide();
		this.changestotl(false,0);
	},
	changestotl:function(jg,lx){
		if(!jg)jg='histotal_';
		var o=$("span[id^='"+jg+"']"),oi=0,i,len=o.length,v1;
		for(i=0;i<len;i++){
			v1=$(o[i]).text();
			if(v1=='')v1='0';
			oi=oi+parseFloat(v1);
		}
		if(oi==0)oi='';
		$('#stotal_ss'+lx+'').html(''+oi);
		var v1=$('#stotal_ss0').text(),v2=$('#stotal_ss1').text();
		if(!v1)v1='0';if(!v2)v2='0';
		var v3=parseFloat(v1)+parseFloat(v2);
		nwjs.changeicon(v3);
	},
	getsicons:function(a){
		var arr = [{name:'IM交流',onclick:"$('#chatimdiv').toggle()",badgeid:'stotal_ss0',id:'im',face:'images/reim.png',num:'defim',type:0,stotal:''},{face:'images/cog.png',onclick:'im.clickcog(this,event)',name:'设置',stotal:'',type:0,id:'cog',num:'syscog'}];
		if(nwjs.nw)arr.push({face:'images/down.png',onclick:'im.opendownfile(this,event)',name:'下载的文件',stotal:'',type:0,id:'downfile',num:'downfile'});
		var i,len=a.length,d,d1,j;
		for(i=0;i<len;i++){
			d 	= a[i];d.type = 1;
			agentarr[d.id]=d;
			if(d.totals==0){
				arr.push(d);
			}else{
				for(j=0;j<d.totals;j++){
					d1 = d.subagent[j];
					d1.type = 1;
					agentarr[d1.id]=d1;
					arr.push(d1);
				}
			}
		}
		iconsarr = arr;
		this.reloadicons();
	},
	showicons:function(a){
		$('.os_icons').remove();
		var i,len=a.length,s='',d,t=10,l=10,zh=winHb(),jg=110,s1,s2,s3;
		for(i=0;i<len;i++){
			d = a[i];
			s1= d.stotal;if(s1==0)s1='';
			s2= 'agentstotal_'+d.id+''; if(d.badgeid)s2=d.badgeid;
			s3='im.openicons('+i+',event)';
			if(d.onclick)s3=d.onclick;
			s+='<div onclick="'+s3+'" style="top:'+t+'px;left:'+l+'px" class="os_icons">';
			s+='	<div class="os_icons_img">';
			if(d.iconfont){
				s+='		<div style="background:'+d.iconcolor+'" class="os_icons_font"><div><i class="icon iconfont icon-'+d.iconfont+'"></i></div></div>';
			}else{
				s+='		<img id="icons_face_'+d.num+'" src="'+d.face+'">';
			}
			s+='	</div>';
			s+='	<span id="icons_title_'+d.num+'" class="os_icons_text">'+d.name+'</span>';
			s+='	<span id="'+s2+'" class="badge">'+s1+'</span>';
			s+='</div>';
			t+=jg;
			if(t>zh-130){t=10;l+=jg}
		}
		$('body').append(s);
		$('.os_icons').click(function(){return false;});
		this.changestotl('agentstotal_',1);
	},
	reloadicons:function(){
		this.showicons(iconsarr);
	},
	onclickicons:function(a){
		if(a.num=='kqdaka'){
			im.adddaka(function(s){
				js.alert('打卡成功时间：<br>'+s+'');
			});
			return false;
		}
		return true;
	},
	openicons:function(oi,e){
		var d = iconsarr[oi];
		if(!this.onclickicons(d, e))return;
		if(d.type==1)this.openagent(d.id);
	},
	showagent:function(a){
		this.getsicons(a);
	},
	clickitems:function(ty,id){
		if(ty=='user')this.openuser(id);
		if(ty=='group')this.opengroup(id);
		if(ty=='agent')this.openagent(id);
	},
	openagent:function(id,e){
		var d=agentarr[id],a=[],i,d1,d2;
		if(!d)return;
		var url = d.url;
		if(url=='link' && !isempt(d.urlpc)){
			js.open(this.geturl(d.urlpc), 1000,550);
		}else{
			var rnd = js.getrand();
			addtabs({name:d.name,num:'agent_'+id+'',rand:rnd,reheiid:'agentshow_'+rnd+'',width:850,url:'agent,id='+d.id+'',face:d.face});
		}
		$('#agentstotal_'+id+'').html('');
		d.stotal=0;
		this.changestotl('agentstotal_',1);
	},
	geturl:function(url){
		if(url.substr(0,4)!='http'){
			url=apiurl+url;
		}
		return url;
	},
	openchat:function(name,type,id,face){
		var rnd = js.getrand();
		addtabs({name:name,num:''+type+'_'+id+'',rand:rnd,reheiid:'showview_'+rnd+'',url:'chat,uid='+id+',type='+type+'',face:face,onshow:function(){
			im.chatshow(this.num);
		}});
		$('#histotal_'+type+'_'+id+'').html('');
		this.changestotl(false,0);
	},
	openuserzl:function(id){
		var d=userarr[id];
		if(!d)return;
		if(isempt(d.tel))d.tel='';if(isempt(d.email))d.email='';
		if(isempt(d.sex))d.sex='?';
		var s = '<div>';
		s+='<div align="center" style="padding:10px;"><img id="myfacess" onclick="$(this).imgview()" src="'+d.face+'" height="80" width="80" style="border-radius:40px;border:1px #eeeeee solid">';
		if(id==adminid)s+='<br><a href="javascript:;" id="fupbgonet" onclick="im.upfaceobj.click()" style="font-size:12px">修改头像</a>';
		s+='</div>';
		s+='<div style="line-height:25px;padding:10px;padding-left:40px;"><font color=#888888>姓名：</font>'+d.name+'<br><font color=#888888>部门：</font>'+d.deptallname+'<br><font color=#888888>职位：</font>'+d.ranking+'<br><font color=#888888>性别：</font>'+d.sex+'<br><font color=#888888>电话：</font>'+d.tel+'<br><font color=#888888>手机：</font>'+d.mobile+'<br><font color=#888888>邮箱：</font>'+d.email+'</div>';
		s+='</div>';
		js.tanbody('userziliao',''+d.name+'', 330,350,{
			html:s
		});
		if(id==adminid){
			this.upfaceobj=$.rockupload({inputfile:'upfacess',uptype:'image',
				onsuccess:function(f,str){
					var a=js.decode(str);
					if(!a.id)return;
					js.ajax('reim','changeface',{id:a.id},function(tx){
						var face=''+apiurl+''+tx+'';
						get('myface').src=face+'?'+Math.random()+'';
						get('myfacess').src=face+'?'+Math.random()+'';
						adminface=face;
						js.setoption('adminface', face);
						$('#fupbgonet').html('修改成功');
					},'none');
				},
				onchange:function(){
					$('#fupbgonet').html('上传中...');
				}
			});
		};
	},
	openuser:function(id){
		var d=userarr[id];
		this.openchat(d.name,'user',id,d.face);
	},
	opengroup:function(id){
		var d=grouparr[id];
		this.openchat(d.name,'group',id,d.face);
	},
	otherlogin:function(){
		nwjs.winshow();
		otherlogin = true;
		var msg='您的帐号已在别的地方登录';
		notifyobj.showpopup(msg,{soundbo:false});
		js.confirm(msg,function(s){
			im.exitlogin();
		});
		setTimeout(function(){
			js.msg('none');
			clearTimeout(relianshotime_time);
		},100);
	},
	chatshow:function(nu){
		var ops=this.getchatobj(nu);
		if(ops){
			ops.addcont('');
			ops.inputfocus();
		}	
		$('#histotal_'+nu+'').html('');
	},
	getchatobj:function(num){
		var ops		= chatobj[num];
		if(ops)if(!ops.isexist())ops=false;
		return ops;
	},
	loadgroup:function(){
		this.ldata('group');
	},
	loadagent:function(){
		this.ldata('agent');
	},
	onoffline:function(sid,cont){
		var o1 = $('div[uid='+sid+']');
		o1.removeClass();
		o1.addClass(cont);
		var online = 0;
		if(cont=='online')online=1;
		var a 	= userarr[sid];
		if(a){
			userarr[sid].online=online;
			maindata.uarr[a.i].online=online;
		}
	},
	addhistory:function(lx,id,sot,cont,opt){
		var d={type:lx,receid:id,stotal:sot,cont:cont,optdt:opt};
		this.showhistorys(d, true);
	},
	yiduall:function(type,gid){
		js.ajax('reim','yiduall',{'type':type,'gid':gid},false,'none');
	},
	receivecmd:function(d){
		
	},
	//获取在线人员列表
	getonline:function(){
		serversend({atype:'getonline'});
	},
	getonlineb:function(str){
		var stra = ','+str+',',i,online;
		$('div[uid]').removeClass();
		$('div[uid]').addClass('offline');
		for(i in userarr){
			online = 'offline';
			if(stra.indexOf(','+i+',')>-1)online='online';
			this.onoffline(i, online);
		}
	},
	receivemesb:function(d){
		var lx=d.type,sendid=d.adminid,num,face,ops=false,msg='',ot,ots,garr,tits,gid;
		if(lx=='offoline'){
			this.otherlogin();
			return;
		}
		if(lx=='cmd'){
			this.receivecmd(d);
			return;
		}
		var a 	= userarr[sendid];
		if(a){
			d.sendname=a.name;
			d.face = a.face;
		}
		gid = d.gid;
		if(lx=='onoffline'){
			this.onoffline(sendid, d.cont);
		}
		if(lx=='getonline'){
			this.getonlineb(d.online);
		}
		if(lx == 'user' || lx == 'group'){
			num		= ''+lx+'_'+sendid+'';
			if(lx == 'group')num = ''+lx+'_'+gid+'';
			ops		= this.getchatobj(num);
			face 	= d.face;
			if(ops){
				ops.receivedata(d);
				if(num!=nowtabs.num){
					ot 		= $('#tabs_stotal_'+num+'');ots=ot.text();if(ots=='')ots='0';
					ots		= parseFloat(ots);
					ot.html(ots+1);
				}
			}
			if(lx == 'group'){
				garr= grouparr[gid];
				if(!garr){
					this.loadgroup();
					garr={face:'images/group.png'};
				}
				face= garr.face;
			}
			if(!ops || !windowfocus){
				if(lx == 'user'){
					msg = '人员['+d.sendname+']，发来一条信息';
					notifyobj.showpopup(msg,{icon:d.face,sendid:sendid,click:function(b){
						im.openuser(b.sendid);
						return false;
					}});
				}
				if(lx == 'group'){
					msg = '人员['+d.sendname+']，发来一条信息，来自['+d.gname+']';
					notifyobj.showpopup(msg,{icon:garr.face,gid:gid,click:function(b){
						im.opengroup(b.gid);
						return false;
					}});
				}
			}
			ot 		= $('#histotal_'+num+'');ots=ot.text();if(ots=='')ots='0';
			if(!ops)ots = parseFloat(ots)+1;
			if(lx=='user')this.addhistory(lx,sendid,ots,d.cont,d.optdt);
			if(lx=='group')this.addhistory(lx,gid,ots,d.cont,d.optdt);
		}
		if(lx == 'agent'){
			garr = agentarr[gid];
			if(!garr){
				this.loadagent();
				garr={face:'images/logo.png',pid:0};
			}
			msg		= ""+jm.base64decode(d.cont)+"";
			tits	= d.title;
			if(!tits)tits = d.gname;
			notifyobj.showpopup(msg,{icon:garr.face,title:tits,gid:gid,url:d.url,click:function(b){
				if(b.url){
					js.open(b.url,740,500);
					return true;
				}else{
					im.openagent(b.gid);
					return false;
				}
			}});
			ot = $('#agentstotal_'+gid+'');if(garr.pid!=0)ot = $('#agentstotal_'+garr.pid+'')
			ots=ot.text();if(ots=='')ots='0';
			if(!ops)ots = parseFloat(ots)+1;
			garr.stotal = garr.stotal+1;
			ot.html(''+ots);
			this.changestotl('agentstotal_',1);
		}
		if(lx == 'loadgroup'){
			this.loadgroup();
		}
	},
	openrecord:function(type,gid){
	},
	bodyright:function(e,lx){
		var rt = $(im.rightdivobj).attr('rtype');
		if(isempt(rt))return;
		var d=[{name:'发消息',lx:0}];
		if(rt.indexOf('agent')>-1){
			d.push({name:'打开窗口',lx:1});
		}
		if(rt.indexOf('hist')>-1){
			d.push({name:'删除此记录',lx:2});
		}
		righthistroboj.setData(d);
		righthistroboj.showAt(e.clientX-3,e.clientY-3);
	},
	rightclick:function(d){
		var lx=d.lx;
		if(lx==99)this.exitss();
		if(lx==98)this.addapplys(d);
		if(lx==21)this.creategroup();
		if(lx==22)this.indexsyscog();
		if(lx==23)this.initdata(true);
		if(lx==24)location.reload();
		if(lx==25)this.editpass();
		if(lx==26)this.openadminurl();
		if(lx>20)return;
		var o1 = $(im.rightdivobj);
		var tsaid = o1.attr('tsaid'),
			tsayp = o1.attr('tsaype');
		if(lx==0){
			if(tsayp=='agent')im.openagents(tsaid);
			if(tsayp=='user')im.openuser(tsaid);
			if(tsayp=='group')im.opengroup(tsaid);
		}
		if(lx==1){
			im.openagent(tsaid);
		}
		if(lx==2){
			o1.remove();
			var tst=$('#historylist').text();if(tst=='')$('#historylist_tems').show();
			js.ajax('reim','delhistory',{type:tsayp,gid:tsaid},false,'none');
		}
		if(lx==3)im.openrecord(tsayp, tsaid);
	},
	openadminurl:function(){
		var w=screen.width-100,h=screen.height-100;
		if(w>1200)w=1200;if(h>800)h=800;
		js.open(apiurl+'?afrom=client',w,h);
	},
	clickcog:function(o1,e){
		var d=[{name:'＋创建会话',lx:21}];
		d.push({name:'设置',lx:22});
		d.push({name:'修改密码',lx:25});
		d.push({name:'重新加载数据',lx:23});
		d.push({name:'刷新页面',lx:24});
		d.push({name:'更多功能...',lx:26});
		d.push({name:'退出',lx:99});
		righthistroboj.setData(d);
		if(!e){
			var off = $(o1).offset();
			righthistroboj.showAt(off.left,off.top-255);
		}else{
			righthistroboj.showAt(e.clientX,e.clientY);
		}
	},
	addapply:function(o1){
		var d = maindata.modearr,a=[],i,i1,len=d.length,s='',d1,d2={};
		for(i=0;i<len;i++){d1=d[i];if(!d2[d1.type])d2[d1.type]=[];d2[d1.type].push(d1);}
		for(i in d2){
			d1=d2[i];len=d1.length;
			if(len>0){
				s+='<div style="border-top:1px #eeeeee solid"><div style="padding-left:10px;padding-top:5px"><b>'+i+'</b></div></div>';
				s+='<ul style="display:inline-block;width:100%;padding-bottom:2px">';
				for(i1=0;i1<len;i1++){
					s+='<li style="float:left;width:25%;line-height:24px;"><div style="padding-left:10px"><a onclick="im.addapplys(\''+d1[i1].name+'\',\''+d1[i1].num+'\')">'+d1[i1].name+'</a></div></li>';
				}
				s+='</ul>';
			}
		}
		js.tanbody('applyadd','＋流程申请',500,310,{
			html:'<div style="height:260px;overflow:auto">'+s+'</div>'
		});
	},
	addapplys:function(name,num){
		js.tanclose('applyadd');
		var url = ''+apiurl+'index.php?a=lu&m=input&d=flow&num='+num+'';
		im.openurl('＋'+name, url);
	},
	changefolderdown:function(path){
		js.setoption('changefolderdown', path);
	},
	getsavepath:function(){
		var path = nwjsgui.App.dataPath+'\\downfile';
		if(!nwjs.fs.existsSync(path))nwjs.fs.mkdirSync(path);  
		var spath = js.getoption('savefilepath', path);
		return spath;
	},
	opendownfile:function(o1,e){
		var s = '<table width="100%">',key,val,i,d,oi=0;
		s+='<tr style="border-bottom:1px #dddddd solid"><td style="padding:5px"></td><td style="padding:5px">文件名</td><td style="padding:5px">大小</td><td style="padding:5px"></td></tr>';
		var storage = window.localStorage;
		for (var i=0, len = storage.length; i < len; i++){
			key = storage.key(i);
			if(key.indexOf('downfile'+adminid+'')>0){
				oi++;
				val = unescape(storage.getItem(key));
				d  = js.decode(val);
				s+='<tr height="30" style="border-bottom:1px #dddddd solid"><td width="40" style="padding:5px" align="center">'+oi+'</td><td style="padding:5px">'+d.filename+'</td><td style="padding:5px">'+d.filesizecn+'</td><td><a id="'+key+'_zt" href="javascript:;"  onclick="im.opendownpath()">打开</a></td></tr>';
			}
		}
		s+='</table>';
		js.tanbody('downfile','下载的文件',500,310,{
			html:'<div style="height:260px;overflow:auto">'+s+'</div>'
		});
		$('#msgview_downfile').html('下载路径：<input class="inputs" style="width:310px" type="text" value="'+this.getsavepath()+'">&nbsp;<a onclick="im.opendownpath()" href="javascript:;">打开</a>&nbsp;');
	},
	opendownpath:function(){
		var spath = this.getsavepath();
		nwjsgui.Shell.openItem(spath);
	},
	changedfolder:function(){
		this.openexe('getfolder');
	},
	backemts:function(s){
		var num=nowtabs.num;
		chatobj[num].backemts(s);
	},
	exitss:function(){
		js.confirm('确定要退出系统吗？',function(lx){
			if(lx=='yes')im.exitlogin();
		});
	},
	exitlogin:function(){
		js.ajax('login','loginexit',{},function(ret){
			js.location('login.html');
		});
	},
	nowsend:function(){
		var ops = this.getchatobj(nowtabs.num);
		if(ops)ops.sendcont();
	},
	entersend:function(){
		var ops = this.getchatobj(nowtabs.num);
		if(ops)ops.entersend();
	},
	onkeyup:function(event){
		var code	= event.keyCode;
		if(code==27){
			closenowtabs();
			return false;
		}
		if(enterbool && !event.ctrlKey && code == 13){im.nowsend();return false;}
		if(event.ctrlKey && code == 13){if(enterbool){im.entersend();}else{im.nowsend();}return false;}
		if(event.altKey){if(code == 83){im.nowsend();return false;}if(code == 67){closenowtabs();return false;}}
		return true;
	},
	editpass:function(){
		var s='<div style="padding:10px" align="center">';
		s+='<div>旧的密码：<input id="passoldPost" type="password" style="width:180px" class="input"></div>';
		s+='<div style="padding:5px 0px">新的密码：<input id="passwordPost" style="width:180px"  type="password" class="input"></div>';
		s+='<div>确认密码：<input id="password1Post" type="password" style="width:180px"  class="input"></div>';
		s+='</div>';
		js.tanbody('editpassa','修改密码', 320,200,{html:s,btn:[{
			text:'确认修改'
		}]});
		$('#editpassa_btn0').click(function(){
			im.editpassok();
		});
	},
	editpassok:function(){
		var opass	= get('passoldPost').value,pass	= get('passwordPost').value,pass1	= get('password1Post').value,msgview	= 'msgview_editpassa';
		if(opass==''){js.setmsg('旧密码不能为空','red', msgview);get('passoldPost').focus();return false;}
		if(pass.length <4){js.setmsg('新密码不能少于4个字符','red', msgview);get('passwordPost').focus();return false;}
		if(!/[a-zA-Z]{1,}/.test(pass) || !/[0-9]{1,}/.test(pass)){js.setmsg('新密码须字母+数字','red', msgview);get('passwordPost').focus();return false;}
		if(opass==pass){js.setmsg('新旧密码不能相同','red', msgview);get('passwordPost').focus();return false;}
		if(pass!=pass1){js.setmsg('确认密码不一致','red', msgview);get('password1Post').focus();return false;}
		var data	= {'passoldPost':opass,'passwordPost':pass,'password1Post':pass1};
		js.setmsg('修改中...','#ff6600', msgview);
		js.ajax('user','editpass',data,function(da){
			if(da=='success'){
				js.setmsg('密码修改成功','green', msgview);
				js.msg('success','密码修改成功');
				js.tanclose('editpassa');
			}else{
				if(da=='')da='修改失败';
				js.setmsg(da,'red', msgview);
			}
		},'none');
	},
	creategroup:function(){
		js.prompt('创建会话','请输入会话名称：',function(lx,v){
			if(lx=='yes'){
				if(!v){js.msg('msg','没有输入会话名称');return;}
				js.msg('wait','创建中...');
				js.ajax('reim','createlun',{val:jm.base64encode(v)}, function(da){
					if(da.indexOf('success')==0){
						js.msg('success','创建成功');
						im.loadgroup();
						js.tanclose('yaoqingla');
					}else{
						js.msg('msg',da);
					}
				},'none');
			}
		});
		return false;
	},
	changeuser:function(){
		
	},
	readclip:function(evt){
		chatobj[nowtabs.num].readclip(evt);
	},
	upbase64:function(nuid,nus){
		var o = get('jietuimg_'+nuid+'');
		uploadobj.sendbase64(o.src,{chatnum:nus});
	},
	initnotify:function(){
		var lx=notifyobj.getaccess();
		if(lx!='ok'){
			js.msg('msg','为了可及时收到信息通知 <br>请开启提醒,<span class="zhu cursor" onclick="im.indexsyscog()">[开启]</span>',-1);
		}
		setTimeout('im.getonline()',2000);
	},
	indexsyscogs:function(){
		var str = notifyobj.getnotifystr('im.indexsyscogss()');
		return '桌面通知提醒'+str+'';
	},
	indexsyscogss:function(){
		notifyobj.opennotify(function(){
			$('#indexsyscog_msg').html(im.indexsyscogs());
		});
	},
	getsound:function(){
		var lx = js.getoption('soundcog'),chs=false;
		if(lx=='')lx='1';
		if(lx==1)chs=true;
		return chs;
	},
	setsound:function(o1){
		var lx=(o1.checked)?'1':'2';
		js.setoption('soundcog', lx);
		notifyobj.setsound(o1.checked);
	},
	indexsyscog:function(){
		var chs= (this.getsound())?'checked':'';
		var s='<div style="height:160px;">';
		s+='<div style="padding:5px 0px;" id="indexsyscog_msg">'+this.indexsyscogs()+'</div>';
		s+='<div style="padding:5px 0px;border-top:1px #eeeeee solid"><label><input '+chs+' onclick="im.setsound(this)" type="checkbox">新信息声音提示</label></div>';
		if(nwjsgui){
			var ksj=js.getoption('kuaijj','Q');
			var ver=nwjsgui.App.manifest.version,strw='ABCEDFGHIJKLMNOPQRSTUVWYZ',s1,cls1='';
			s+='<div style="padding:5px 0px;border-top:1px #eeeeee solid">打开窗口快捷键Ctrl+Alt+<select onchange="nwjs.changekuai(this)">';
			for(var i=0;i<strw.length;i++){
				s1= strw.substr(i,1);
				cls1='';if(ksj==s1){cls1='selected';}
				s+='<option '+cls1+' value="'+s1+'">'+s1+'</option>';
			}
			s+='</select></div>';
			var ips = nwjs.getipmac();
			s+='<div style="padding:5px 0px;border-top:1px #eeeeee solid">我的IP：'+ips.ip+'</div>';
			s+='<div style="padding:5px 0px;border-top:1px #eeeeee solid">我的MAC地址：'+ips.mac+'</div>';
		}else{
			s+='<div style="padding:5px 0px;border-top:1px #eeeeee solid">我的IP：'+im.myip+'</div>';
		}
		s+='</div>';
		js.tanbody('syscogshow','设置',350,100,{html:s,bodystyle:'padding:5px 10px'});
	},
	searchss:function(){
		clearTimeout(this.searchsstime);
		this.searchsstime=setTimeout('im.searchssss()',500);
		if(!this.searchright)this.searchright=$.rockmenu({
			data:[],iconswh:20,
			itemsclick:function(d){
				im.clickitems(d.type,d.id);
			}
		});
	},
	searchssss:function(){
		var o = $('#keysou'),val=strreplace(o.val());
		var d=[];
		if(val==''){
			this.searchright.hide();
			return;
		}
		val=val.toLowerCase();
		var off=o.offset(),sid,a,s1;
		for(sid in userarr){
			a=userarr[sid];
			if(a.name.indexOf(val)>-1 || a.pingyin.indexOf(val)==0 || a.deptname.indexOf(val)>-1 || a.ranking.indexOf(val)>-1){
				s1=''+a.name+'<font color=#888888>('+a.ranking+')</font>';
				d.push({name:s1,id:a.id,icons:a.face,type:'user'});
			}
		}
		for(sid in grouparr){
			a=grouparr[sid];
			if(a.name.indexOf(val)>-1){
				s1=''+a.name+'<font color=#888888>(会话)</font>';
				d.push({name:s1,id:a.id,icons:a.face,type:'group'});
			}
		}
		for(sid in agentarr){
			a=agentarr[sid];
			if(a.name.indexOf(val)>-1){
				s1=''+a.name+'<font color=#888888>(应用)</font>';
				d.push({name:s1,id:a.id,icons:a.face,type:'agent'});
			}
		}
		if(d.length==0){
			this.searchright.hide();
			return;
		}
		this.searchright.setData(d);
		this.searchright.showAt(off.left+1,off.top+40,$('#headercenter').width()-2);
	},
	openurl:function(tit, url){
		var mxw= 900;
		var hm = winHb()-150;if(hm>800)hm=800;if(hm<400)hm=400;
		if(url.indexOf('wintype=max')>0){
			mxw= 1100;
			hm=winHb()-45;
		}
		var wi = winWb()-150;if(wi>mxw)wi=mxw;if(wi<700)wi=700;
		js.tanbody("winiframe",tit,wi,410,{html:'<div id="winiframe_hei" style="height:'+hm+'px"><iframe src="" name="winiframe" onload="im.openurlload(this)" width="100%" height="100%" frameborder="0"></iframe></div>',bbar:'none',resize:true,reheiid:'winiframe_hei'});
		var lxs = (url.indexOf('?')<0)?'?':'&';
		var ofrom = 'desk';
		if(nwjs.nw)ofrom='deskclient';
		url+=''+lxs+'ofrom='+ofrom+'';
		winiframe.location.href=url;
	},
	openurlload:function(o1){
		
	},
	forward:function(tuid, type, cont, fid){
		if(!tuid)return;if(!type)type='user';
		var d = {'tuid':tuid,'type':type,'cont':jm.base64encode(cont),'fileid':fid};
		js.ajax('reim','forward', d, function(){js.msg('success','已转发')},'');
	},
	adddaka:function(fus){
		js.msg('wait','打卡中...');
		var dacs=nwjs.getipmac();
		if(!fus)fus=function(){}
		js.ajax('kaoqin','adddkjl',dacs, function(s){js.msg('success','打卡成功时间：'+s+'');fus(s);},'');
	},
	//内置服务器,发送命令来控制客户端
	serverdata:function(a){
		var lx = a.atype;
		if(lx=='cropfinish' && !this.cropScreenbo && a.filepath){
			nwjs.winshow();
			if(chatobj[nowtabs.num])chatobj[nowtabs.num].readcropimg(a.filepath);
		}
		if(lx=='closenowtabs')closenowtabs();
		if(lx=='focus')nwjs.winshow();
		if(lx=='crop')this.cropScreen(true);
		if(lx=='notify')this.shownotify(a);
		if(lx=='changefolder' && a.folder)this.changefolderdown(a.folder);
		if(lx=='getlogin')return {uid:adminid,uname:adminname,face:adminface};
	},
	shownotify:function(d1){
		var d = js.apply({icon:'images/logo.png','title':'系统提醒',url:''}, d1);
		if(d.msg)notifyobj.showpopup(d.msg,{icon:d.icon,title:d.title,url:d.url,click:function(b){
			if(b.url)nwjs.openurl(b.url);
			return true;
		}});
	},
	dropfile:function(e){
		var o = chatobj[nowtabs.num];
		try{if(o)o.dropfile(e);}catch(e){}
	},
	winright:function(e){
		if(!this.winrobj)this.winrobj=$.rockmenu({
			data:[],
			itemsclick:function(d){
				im.winrightclick(d);
			}
		});
		var d = [{name:'<i class="icon-remove"></i> 关闭所有窗口',lx:0},{name:'<i class="icon-refresh"></i> 重新排列图标',lx:1},{name:'<i class="icon-magic"></i> 切换桌面背景',lx:2},{name:'<i class="icon-cog"></i> 设置',lx:3}];
		this.winrobj.setData(d);
		this.winrobj.showAt(e.clientX,e.clientY);
	},
	winrightclick:function(d){
		var lx = d.lx;
		if(lx==0){
			$('div[tanbody="rock"]').remove();
			$('.os_taskbar_list').html('');
		}
		if(lx==1)this.reloadicons();
		if(lx==2)this.changebgurl();
		if(lx==3)this.indexsyscog();
	},
	changebgurl:function(){
		var sid='bgfileurl';$('#'+sid+'').remove();
		$('body').append('<input type="file" accept="image/jpg,image/jpeg,image/png" id="'+sid+'" style="display:none">');
		$('#'+sid+'').change(function(){
			if(!this.files){
				js.msg('msg','当前浏览器不支持切换背景');
				$('#'+sid+'').remove();	
				return;
			}
			var f  		 = this.files[0],filename = f.name,
				fileext	 = filename.substr(filename.lastIndexOf('.')+1).toLowerCase();
			if(!js.isimg(fileext)){
				js.msg('msg','不是图片文件');
				$('#'+sid+'').remove();	
				return;
			}
			if(!nwjsgui){
				var fsur = URL.createObjectURL(f);
				im.showmyback(fsur);
				$('#'+sid+'').remove();	
				return;
			}
			var reader   = new FileReader();
			var sfile 	 = 'images/osbg/bg'+f.size+'.'+fileext+'';
			reader.onload=function(e){ 
				var base64Data = this.result;
				var fs		   = require('fs');
				base64Data	   = base64Data.substr(base64Data.indexOf(',')+1);
				var dataBuffer = new Buffer(base64Data, 'base64');
				fs.writeFile(sfile, dataBuffer, function(err) {
					if(err){
						js.msg('msg', err);
					}else{
						im.showmyback(sfile,true);
					}
				});
				$('#'+sid+'').remove();	
				reader=false;
			} 
			reader.readAsDataURL(f);
		});
		get(sid).click();
	},
	showmyback:function(surl,bo){
		if(!surl)surl = js.getoption('background');
		if(surl)document.body.style.background='url('+surl+') top left';
		if(bo)js.setoption('background', surl);
		if(!surl)return;
		get('os_bodybgimg').src  = surl;
		var img = new Image();
		img.src = surl;
		img.onload = function(){
			im.bgwidthhei = [this.width,this.height];
			im.resizebgwh();
		}
	},
	resizebgwh:function(){
		if(!this.bgwidthhei)return;
		var w 	= this.bgwidthhei[0],h = this.bgwidthhei[1];
		var zw 	= winWb(),zh = winHb(),bl,_nw,_nh;
		bl 		= zw/w;
		_nw		= zw;
		_nh		= h*bl;
		if(_nh<zh){
			bl  = zh/h;
			_nh = zh;
			_nw = w*bl;
		}
		$('#os_bodybgimg').css({width:''+_nw+'px',height:''+_nh+'px'});
	},
	openexe:function(lx){
		var oatg = this.getpath();
		nwjsgui.Shell.openItem(''+oatg+'/images/'+lx+'.exe');
	},
	cropScreen:function(bo){
		if(!nwjsgui){
			js.msg('msg','无法使用截屏，请使用客户端,<a href="http://www.rockoa.com/" target="_blank">[去下载]</a>');
			return;
		}
		this.cropScreenbo = bo;
		this.openexe('reimcaptScreen');
	},
	getpath:function(){
		var peiz= nwjsgui.App.manifest;
		if(peiz.localpath)return peiz.localpath;
		var url = peiz.main;
		var las = url.lastIndexOf('\\');
		var oatg = url.substr(0, las);
		if(oatg.substr(0,5)=='file:')oatg=oatg.substr(7)
		return oatg;
	},
	fileyulan:function(filename,fid){
		var url = ''+apiurl+'?m=public&a=fileviewer&id='+fid+'&wintype=max';
		this.openurl(filename, url);
	}
}
js.changeok=function(sna,sid, blx,plx){
	if(blx=='yq')chatobj[plx].yaoqing(sid);
	if(blx=='yy')agentobj[plx].changeuser(sna,sid);
}
var chatclass = function(opts){
	var me = this;
	var opts=js.apply({type:'user',gid:0},opts);
	this.receinfor={};
	this.minid = 999999999;
	this._init=function(){
		for(var a in opts)this[a]=opts[a];
		this.receinfor=userarr[this.gid];
		if(this.type=='group')this.receinfor=grouparr[this.gid];
		var h=280;
		this.showobj = $('#showview_'+this.rand+'');
		this.showobj.css('height',''+h+'px');
		this.inputobj = $('#input_content_'+this.rand+'');
		this.num 	  = ''+this.type+'_'+this.gid+'';
		this.inputobj.focus();
		this.showobj.perfectScrollbar();
		this.loaddata();
		var onlins = this.receinfor.online==1 ? '在线' : '离线';
		var sstr 	= '(PC端'+onlins+')';
		if(this.type!='user')sstr='';
		var s='<table width="100%" style="border-bottom:1px #dddddd solid" cellpadding="0"><tr><td nowrap>&nbsp;&nbsp;</td><td height="39"><div style="height:24px;overflow:hidden"><img src="'+this.receinfor.face+'" height="24"></div></td><td nowrap>&nbsp;'+this.receinfor.name+' <font style="font-size:12px;color:#888888">'+sstr+'</font></td><td width="100%"></td>';
		s+='<td nowrap style="display:none"><span><i class="icon-wrench"></i>&nbsp;功能 <i class="icon-angle-down"></i></span></td>';
		if(this.type=='user'){
			s+='<td nowrap><span onclick="im.openuserzl('+this.gid+')"><i class="icon-user"></i>&nbsp;资料</span></td>';
		}
		if(this.type=='group'){
			s+='<td nowrap><span onclick="chatobj[\''+this.num+'\'].groupuser()"><i class="icon-group"></i>&nbsp;人员 <i class="icon-angle-down"></i></span></td>';
			s+='<td nowrap><span onclick="js.changeuser(\'changecheckuser\',\'yq\',\''+this.num+'\',\'邀请人员\');"><i class="icon-plus"></i>&nbsp;邀请</span></td>';
		}
		s+='</tr></table>';
		$('#chatheader_'+this.rand+'').html(s);
		chatobj[this.num]=this;
		
		if(typeof(uploadobj)=='undefined')uploadobj = $.rockupload({
			inputfile:'allfileinput',
			initpdbool:true,
			onchange:function(f){
				chatobj[f.chatnum].sendfile(f);
			},
			onprogress:function(f,per,evt){
				chatobj[f.chatnum].onprogresss(f,per,evt);
			},
			onsuccess:function(f,str,o1){
				chatobj[f.chatnum].upfilesuccess(f,str,o1);
			}
		});
		this.showobj.click(function(){
			$('#showuserl_'+me.rand+'').remove();
		});
		
		this.charcontobj = $.rockmenu({
			data:[],
			itemsclick:function(d){
				me.clickmenuss(d);
			}
		});
	};
	this.dropfile=function(e){
		if(!this.isexist())return;
		uploadobj.setparams({chatnum:this.num});
		uploadobj.change(e.dataTransfer);
	};
	this.yaoqing=function(sid){
		if(this.type!='group')return;
		js.ajax('reim','yaoqinguid',{val:sid,gid:this.gid},function(da){
			if(da.indexOf('success')==0){
				var uids = da.replace('success','');
				js.msg('success','邀请成功');
				if(uids != ''){
					sendtype(uids,'loadgroup');
					me._regroupuser();
				}
			}else{
				js.msg('msg',da);
			}
		});
	};
	this.sendfile=function(f,snr){
		var nuid= js.now('time'),optdt = js.serverdt();
		this._sssnuid = nuid;
		this._sssoptdt = optdt;
		var nr = '<div id="showve_'+nuid+'">';
		
		if(f){
			var slx = f.fileext;
			if(js.fileall.indexOf(','+slx+',')<0)slx='wz';
			nr+= '<div><img src="images/fileicons/'+slx+'.gif" align="absmiddle">&nbsp;'+f.filename+' ('+f.filesizecn+')</div>';
		}
		if(snr){
			nr+= '<div><img src="'+snr+'" id="jietuimg_'+nuid+'" width="150" height="150"></div>';
			nr+= '<div><a onclick="im.upbase64(\''+nuid+'\',\''+this.num+'\')" href="javascript:;">[发送截图]</a> &nbsp; &nbsp; <a onclick="$(\'#ltcont_'+nuid+'\').remove();" href="javascript:;">[取消]</a>';
		}
		nr+='<div class="progresscls"><div  id="progresscls_'+nuid+'" class="progressclssse"></div><div class="progressclstext"  id="progresstext_'+nuid+'">0%</div></div>';
		nr+= '</div>';
		var cont= strformat.showqp('right','我',optdt, nr, nuid, adminface, nuid);
		this.addcont(cont);
	};
	this.readclipshow=function(cont){
		this.sendfile(false, cont);		
	};
	this.onprogresss=function(f,per){
		$('#progresscls_'+this._sssnuid+'').css('width',''+per+'%');
		$('#progresstext_'+this._sssnuid+'').html(''+per+'%');
	};
	this.upfilesuccess=function(f,str){
		var a=js.decode(str),contss='';
		$('.progresscls').remove();
		if(!a.id){
			this.senderror(this._sssnuid);
			js.msg('msg', str);
			return;
		}
		if(f.isimg){
			var s='<img src="'+apiurl+''+a.thumbpath+'" fid="'+a.id+'">';
			$('#showve_'+this._sssnuid+'').html(s);
			contss = '[图片 '+a.filesizecn+']';
		}else{
			$('#progresstext_'+this._sssnuid+'').html('上传成功');
			contss = '['+f.filename+' '+f.filesizecn+']';
		}
		this.sendconts(jm.base64encode(contss), this._sssnuid, this._sssoptdt, a.id);
	};
	this.groupuser=function(){
		var id= 'showuserl_'+this.rand+'';
		if(get(id)){
			$('#'+id+'').remove();
			return;
		}
		var s='<div id="'+id+'" style="background-color:#ffffff;border:1px #dddddd solid;position:absolute;left:0px;top:39px;z-index:10;width:100%;max-height:300px;overflow:auto;cursor:pointer;">';
		s+='<div align="center" style="padding:10px;"><img src="images/mloading.gif" align="absmiddle">&nbsp;加载中...</div>';
		s+='</div>';
		this.showobj.before(s);
		if(!this.receinfor.groupuser){
			this._regroupuser();
		}else{
			this._groupusershow(this.receinfor.groupuser);
		}
	};
	this._regroupuser=function(o1){
		if(o1)$(o1).html('<img src="images/loadings.gif" height="14" width="15" align="absmiddle"> 加载中...');
		js.ajax('reim','getgroupuser',{type:this.type,gid:this.gid},function(ret){
			me._groupusershow(ret.uarr);
		},'none');
	};
	this._groupusershow=function(a){
		this.receinfor.groupuser = a;
		grouparr[this.gid].groupuser = a;
		var i,len=a.length,d,s='',cls;
		s+='<table border="0" width="99%"><tr>';
		for(i=0;i<len;i++){
			d =a[i];
			cls='offline';
			if(userarr[d.id] && userarr[d.id].online==1)cls='';
			s+='<td align="center" style="padding:5px 0px" class="'+cls+'" onclick="im.openuserzl('+d.id+')" width="20%"><img src="'+d.face+'" height="40" width="40"><br><font color=#888888>'+d.name+'</font></td>';
			if((i+1)%5==0)s+='</tr><tr>';
		}
		s+='</tr></table>';
		s+='<div style="padding:5px; background-color:#f1f1f1">共'+len+'人,<a onclick="chatobj[\''+this.num+'\']._regroupuser(this)" href="javascript:;">刷新</a>&nbsp;,&nbsp;<a onclick="chatobj[\''+this.num+'\']._exitgroup(this)" href="javascript:;">退出会话</a></div>';
		$('#showuserl_'+this.rand+'').html(s);
	};
	this.inputfocus=function(){
		this.inputobj.focus();
	};
	this.loaddata=function(o1, iref){
		var iref = (!iref)?false:true;
		var minid= 0;
		if(iref)minid=this.minid;
		if(o1)$(o1).html('<img src="images/loadings.gif" height="14" width="15" align="absmiddle"> 加载中...');
		js.ajax('reim','getrecord',{type:this.type,gid:this.gid,minid:minid},function(ret){
			if(o1)$(o1).html('');
			me.loaddatashow(ret, iref);
		},'none');
	};
	this.loadmoreda=function(o1){
		this.loaddata(o1, true);
	};
	this._exitgroup=function(){
		js.confirm('确定要['+this.receinfor.name+']会话吗？',function(lx){
			if(lx=='yes'){
				me._exitgroups();
			}
		});
	};
	this._exitgroups=function(){
		if(this.type!='group')return;
		js.ajax('reim','exitgroup',{gid:this.gid}, function(da){
			im.loadgroup();
			closenowtabs();
			$('#history_'+me.num+'').remove();
		});
	};
	this._contright=function(o1,e){
		var o=$(o1),rnd=o.attr('rand');
		this.randmess = rnd;
		var d=[{name:'复制',lx:0},{name:'删除',lx:1}];
		if(this.type=='group')d.push({name:'@TA',lx:3});
		d.push({name:'清除1月前记录...',lx:2});
		this.charcontobj.setData(d);
		this.charcontobj.showAt(e.clientX,e.clientY,130);
	};
	this.clickmenuss=function(d){
		var lx=d.lx;
		if(lx==0){
			var cont = $('#qipaocont_'+this.randmess+'').text();
			if(cont)this.addinput(cont);
		}
		if(lx==1){
			$('#ltcont_'+this.randmess+'').remove();
			var ids=this.randmess.replace('mess_','');
			if(ids)js.ajax('reim','clearrecord',{type:this.type,gid:this.gid,ids:ids},false,'none');
		}
		if(lx==2){
			js.confirm('确定要清除1个月前的记录吗？',function(lx){
				if(lx=='yes')me.clearjilss(30);
			});
		}
		if(lx==3){
			var cont = $('#ltname_'+this.randmess+'').text();
			if(cont)this.addinput('@'+cont);
		}
		if(lx==90||lx==91)this.changesssssne(lx);
	};
	this.clearjilss=function(d){
		js.ajax('reim','clearrecord',{type:this.type,gid:this.gid,day:d},function(s){
			js.msg('success','清除成功');
		});
	};
	this.loaddatashow=function(ret,isbf){
		var a = ret.rows;
		$('#showviewload_'+this.rand+'').remove();
		var i,len = a.length,cont,lex,nas,fase,nr,d,na=[],rnd,sid;
		$('#loadmored_'+this.rand+'').remove();
		if(isbf){
			if(len>0)this.showobj.prepend('<div class="showblanks">---------↑以上是新加载---------</div>');
			na = a;
		}else{
			for(i= len-1; i>=0; i--)na.push(a[i]);
		}
		for(i= 0; i<len; i++){
			d   = na[i];
			sid = parseFloat(d.id);
			lex = 'right';
			nas = '我';
			fase= adminface;
			if(d.sendid!=adminid){
				lex='left';
				nas= d.sendname;
				fase= d.face;
			}
			nr  = this.contshozt(d.filers);
			if(nr=='')nr= jm.base64decode(d.cont);
			rnd = 'mess_'+sid+'';
			cont= strformat.showqp(lex,nas,d.optdt,nr ,'', fase, rnd);
			if(!isbf){
				this.addcont(cont, isbf);
			}else{
				this.showobj.prepend(cont);
			}
			$('#qipaocont_'+rnd+'').contextmenu(function(e){
				me._contright(this,e);
			});
			if(sid<this.minid)this.minid=sid;
		}
		if(len>0){
			var s = '<div id="histordiv_'+this.rand+'" class="showblanks" >';
			if(ret.wdtotal==0){
				s+='---------↑以上是历史记录---------';
				if(len>=5){
					this.showobj.prepend('<div id="loadmored_'+this.rand+'" class="showblanks" ><a href="javascript:;" onclick="chatobj[\''+this.num+'\'].loadmoreda(this)">点击加载更多...</a></div>');
				}
			}else{
				s+='---↑以上是历史,还有未读信息'+ret.wdtotal+'条,<a href="javascript:;" onclick="chatobj[\''+this.num+'\'].loaddata(this)">点击加载</a>---';
			}
			s+='</div>';
			if(!isbf)this.addcont(s);
			if(isbf)this._addclickf();
		}
	};
	this.clearping=function(){
		var s='';
		if(get('loadmored_'+this.rand+''))s='<div id="loadmored_'+this.rand+'" class="showblanks" ><a href="javascript:;" onclick="chatobj[\''+this.num+'\'].loadmoreda(this)">点击加载更多...</a></div>';
		this.showobj.html(s);
	};
	this.contshozt=function(d){
		return strformat.contshozt(d);
	};
	this.addcont=function(cont, isbf){
		var o	= this.showobj;
		if(cont){if(isbf){o.prepend(cont);}else{o.append(cont);}}
		clearTimeout(this.scrolltime);
		this.scrolltime	= setTimeout(function(){
			if(get('showview_'+me.rand+''))o.animate({scrollTop:get('showview_'+me.rand+'').scrollHeight},100);
			me._addclickf();
		}, 50);
	};
	this._addclickf=function(){
		var o = this.showobj.find('img[fid]');
		o.unbind('click');
		o.click(function(){
			me.clickimg(this);
		});
	};
	this.clickimg=function(o1){
		var o=$(o1);
		var fid=o.attr('fid');
		var src = o1.src.replace('_s.','.');
		$.imgview({url:src});
	};
	this.toolsclick=function(o1,oi,evt){
		var o = $(o1);
		var lx= o.attr('tools');
		if(lx=='send')this.sendcont();
		if(lx=='emts')this.getemts(o);
		if(lx=='clear')this.clearping();
		if(lx=='crop')im.cropScreen(false);
		if(lx=='close')closetabs(this.num);
		if(lx=='file')uploadobj.click({chatnum:this.num});
		if(lx=='change')this.changesend(o);
		if(lx=='cropput')js.msg('msg','请使用快捷键Ctrl+V');
	};
	this.changesend=function(o){
		var d=[{name:'Ctrl+Enter发送',icons:'images/white.gif',lx:90},{name:'Enter发送',icons:'images/white.gif',lx:91}];
		var sk = js.getoption('sendkey');
		if(sk=='1'){
			d[1].icons='images/ok.png';
		}else{
			d[0].icons='images/ok.png';
		}
		var off = o.offset();
		this.charcontobj.setData(d);
		this.charcontobj.showAt(off.left,off.top+30,150);
	};
	this.changesssssne=function(lx){
		var oi=lx-90;
		js.setoption('sendkey',''+oi);
		enterbool = (oi==1);
	};
	this.readclip=function(evt){
		this.clipevent =evt;
		var clipboardData = evt.clipboardData;
		if(!clipboardData)return;
		for(var i=0; i<clipboardData.items.length; i++){  
			var item = clipboardData.items[i];  
			if(item.kind=='file'&&item.type.match(/^image\//i)){  
				var blob = item.getAsFile(),reader = new FileReader();  
				reader.onload=function(){  
					var cont=this.result;
					me.readclipshow(cont);
				}  
				reader.readAsDataURL(blob);
			}  
		} 
	};
	//读取截图过来的
	this.readcropimg=function(path){
		nwjs.fs.readFile(path, function (err, data){
			if(data){
				me.readclipshow('data:image/jpeg;base64,'+data.toString('base64'));
			}
		});
	};
	this.isexist =function(){
		return get('showview_'+this.rand+'');
	};
	this.sendcont = function(ssnr){
		if(js.ajaxbool)return;
		js.msg('none');
		var o	= this.inputobj,len,se;
		var nr	= strformat.sendinstr(o.val());
		nr		= nr.replace(/</gi,'&lt;').replace(/>/gi,'&gt;').replace(/\n/gi,'<br>');
		if(ssnr)nr=ssnr;
		if(isempt(nr))return false;
		len 	= nr.length;se = nr.substr(len-4);if(se=='<br>')nr=nr.substr(0,len-4);
		len 	= nr.length;se = nr.substr(len-4);if(se=='<br>')nr=nr.substr(0,len-4);
		var conss = jm.base64encode(nr);
		if(conss.length>500){
			js.msg('msg','发送内容太多了');
			return;
		}
		var nuid= js.now('time'),optdt = js.serverdt();
		var cont= strformat.showqp('right','我',optdt, nr, nuid, adminface);
		this.addcont(cont);
		o.val('');
		o.focus();
		this.sendconts(conss, nuid, optdt, 0);
		return false;
	};
	this.sendconts=function(conss, nuid, optdt, fid){
		im.addhistory(this.type,this.gid,0,conss,optdt);
		var d 	 = {cont:conss,gid:this.gid,type:this.type,nuid:nuid,optdt:optdt,fileid:fid};
		js.ajax('reim','sendinfor',d,function(ret){
			me.sendsuccess(ret,nuid);
		},'none',false,function(){
			me.senderror(nuid);
		});
	};
	this.entersend=function(){
		this.addinput("\n");
	};
	this.sendsuccess=function(d,nuid){
		this.bool = false;
		if(!d.id){
			this.senderror(nuid);
			return;
		}
		$('#'+d.nuid+'').remove();
		var bo = false;
		d.messid=d.id;
		d.face  = adminface;
		var bo  = serversend(d);
		if(!bo)js.msg('msg','信息不能及时推送，但已保存到服务器');
	};
	this.receivedata=function(d){
		var nr 	= jm.base64decode(d.cont);
		if(d.filename){
			nr  = this.contshozt(d);
		}
		var rnd = 'mess_'+d.id+'';
		var cont= strformat.showqp('left',d.sendname,d.optdt,nr , '', d.face, rnd);
		this.addcont(cont);
		$('#qipaocont_'+rnd+'').contextmenu(function(e){
			me._contright(this,e);
		});
		im.yiduall(this.type, this.gid);
	};
	this.senderror=function(nuid){
		js.ajaxbool = false;
		get(nuid).src='images/error.png';
		get(nuid).title='发送失败';
	};
	this.getemts=function(o){
		if(!get('aemtsdiv')){
			var s = '<div id="aemtsdiv" style="width:400px;height:200px;overflow-y:auto;border:1px #cccccc solid;background:white;box-shadow:0px 0px 5px rgba(0,0,0,0.3);left:3px;top:5px;position:absolute;display:none;z-index:102">';
			s+='<div style="padding:5px">';
			s+=this.getemtsbq('qq',0, 104, 11, 24);
			s+='</div>';
			s+='</div>';
			$('body').append(s);
			js.addbody('emts','hide','aemtsdiv');
		}
		var o1  = $('#aemtsdiv');
		o1.toggle();
		var off = o.offset();
		o1.css({'top':''+(off.top-205)+'px','left':''+(off.left)+'px'});
	};
	this.getemtsbq=function(wj, oi1,oi2, fzd, dx){
		var i,oi=0,j1 = js.float(100/fzd);
		var s = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>';
		for(i=oi1; i<=oi2; i++){
			oi++;
			s+='<td width="'+j1+'%" title="'+strformat.emotsarr[i+1]+'" align="center"><img onclick="im.backemts(\''+strformat.emotsarr[i+1]+'\')" src="images/im/emots/'+wj+'/'+i+'.gif" width="'+dx+'" height="'+dx+'"></td>';
			if(oi % fzd==0)s+='</tr><tr>';
		}
		s+='</tr></table>';
		return s;
	};
	this.backemts=function(s){
		this.addinput(s);
		$('#aemtsdiv').hide();
	};
	this.addinput=function(s){
		var o = this.inputobj;
		o.val(o.val()+s);
		o.focus();
	};
	this._init();
};
function agentclass(opts){
	var me = this;
	var opts=js.apply({id:0},opts);
	this.receinfor={};
	this._init=function(){
		for(var a in opts)this[a]=opts[a];
		this.receinfor=agentarr[this.id];
		if(!this.receinfor){
			js.msg('msg','应用不存在，请[设置-从新加载数据]后在试');
			return;
		}
		this.sousoukey= '';
		this.nowpage  = 1;
		this.num 	  = this.receinfor.num;
		this.name 	  = this.receinfor.name;
		this.showobj  = $('#agentshow_'+this.rand+'');
		this.showheight = 0;
		this.setheight();
		var menus = this.receinfor.menu,len=menus.length,i;
		this.menus= menus;
		var s='<table width="100%" style="border-bottom:1px #dddddd solid" cellpadding="0"><tr><td nowrap>&nbsp;&nbsp;</td><td height="39"><div style="height:24px;overflow:hidden;padding-right:5px"><img src="'+this.receinfor.face+'" height="24"></div></td><td id="agenttitle_'+this.rand+'" nowrap>'+this.receinfor.name+'</td>';
		if(this.receinfor.url=='auto')s+='<td style="padding-left:10px"><input onkeyup="agentobj.'+this.num+'.sousousou(this,event)" style="border:1px #dddddd solid; width:150px; background:white;height:26px;padding:0px 5px" placeholder="输入关键词搜索" ></td>';
		s+='<td width="100%"></td>';
		for(i=0;i<len;i++){
			a=menus[i];
			s+='<td oimenu="'+i+'" nowrap><span>'+a.name+'';
			if(a.submenu.length>0)s+=' <i class="icon-angle-down"></i>';
			if(a.num)s+='	<font id="'+a.num+'_stotal_'+this.rand+'" class="badge"></font>';
			s+='</span></td>';
		}
		s+='</tr></table>';
		var offs=$('#agentheader_'+this.rand+'');
		offs.html(s);
		offs.find('[oimenu]').click(function(e){
			return me.clickmenu(this,e);
		});
		agentobj[this.num]=this;
		this.loaddata();
		
		this.submenuobj = $.rockmenu({
			data:[],
			itemsclick:function(d){
				me.clickmenuss(d);
			}
		});
	};
	this.setheight=function(iu){
		var h = this.showheight==0 ? 430 : parseFloat(this.showobj.height());
		if(!iu)iu=0;
		h	= h+iu;
		this.showheight = h;
		this.showobj.css('height',''+h+'px');
	};
	this.setTitle=function(tit){
		$('#agenttitle_'+this.rand+'').html(tit);
	};
	this.winTitle=function(tit){
		$('#agent_'+this.receinfor.id+'_title').html(tit);
	};
	this.setBgcolor=function(col){
		if(!col)col='white';
		this.showobj.css('background',col);
	};
	this.clickevent=function(a){
		this.getdata(a.url, 1);
	};
	this.disabledscroll=function(lx){
		if(!lx)lx='hidden';
		me.showobj.css('overflow',lx);
	};
	this.clickmenu=function(o1){
		var o = $(o1);
		var oi= parseFloat(o.attr('oimenu'));
		var a = this.menus[oi],slen=a.submenu.length;
		if(slen<=0){
			this.clickmenus(a);
		}else{
			var off=o.offset();
			this.submenuobj.setData(a.submenu);
			this.submenuobj.showAt(off.left,off.top+42,130);
			return false;
		}
	};
	this.clickmenuss=function(d){
		this.clickmenus(d);
	};
	this.callclickmenu=function(a){
		return true;
	};
	this.clickmenus=function(a){
		if(!this.callclickmenu(a))return;
		if(a.lx){this.showmenuclick(a);return;}
		if(a.type==0){this.clickevent(a);return;}
		if(a.type==1){
			var url=a.url,amod=this.num;
			if(url.substr(0,3)=='add'){
				if(url!='add')amod=url.replace('add_','');
				url='index.php?a=lu&m=input&d=flow&num='+amod+'';
			}
			if(url.indexOf('http')!=0)url = apiurl+url+'';
			im.openurl(a.name, url);
		}
	};
	this.getfirstnum=function(){
		var dbh = 'def',bh='';
		var a = this.menus[0];
		if(a){
			bh = a.url;
			if(a.submenu[0])bh=a.submenu[0].url;
		}
		if(isempt(bh))bh=dbh;
		return bh;
	};
	this.initagent=function(){
		this.getdata(this.getfirstnum(), 1);
	};
	this.loaddata=function(){
		var url = this.receinfor.url;
		if(url=='buin'){
			$.ajax({
				url:'html/yingyong/'+this.num+'.html?rnd='+Math.random()+'',
				success:function(s){
					s = s.replace(/\{rand\}/gi, me.rand);
					me.showobj.html(s);
					me.initagent();
				},
				error:function(e){
					me.initagent();
				}
			});
		}else{
			me.initagent();
		}
	};
	this.sousousou=function(o,e){
		if(e.keyCode==13){
			var key = o.value;
			if(this.sousoukey == key)return;
			this.sousoukey = key;
			this.regetdata(false,1);
		}
	};
	this.getdata=function(st,p, nob){
		this.nowevent=st;
		this.nowpage =p;
		if(!nob)modeltabs('','agent_'+this.id+'');
		var key = ''+this.sousoukey;
		if(key)key='basejm_'+jm.base64encode(key)+'';
		js.ajax('index','getyydata',{'page':p,'event':st,'num':this.num,'key':key},function(ret){
			me.showdata(ret);
		},'none');
	};
	this.reload=function(){
		this.getdata(this.nowevent, this.nowpage);
	};
	this.regetdata=function(o1,p){
		if(o1)$(o1).html('<img src="images/loadings.gif" height="14" width="15" align="absmiddle"> 加载中...');
		this.getdata(this.nowevent, p, o1);
	};
	this.xiang=function(oi,nbo){
		var d = this.data[oi-1];
		var ids = d.id,nus=d.modenum,modne=d.modename;
		if(!ids)return;
		if(!modne)modne=this.name;
		if(!nus||nus=='undefined')nus = this.num;
		var url=''+apiurl+'task.php?a=p&num='+nus+'&mid='+ids+'';
		if(!nbo){im.openurl(modne, url);}else{js.open(url, 750,500)}
	};
	this.printexcel=function(oi){
		var d = this.data[oi-1];
		var nus=d.modenum,modne=d.modename;
		if(!modne)modne=this.name;
		if(!nus||nus=='undefined')nus = this.num;
		var url=''+apiurl+'task.php?a=e&num='+nus+'&event='+this.nowevent+'';
		js.open(url, 800,500);
	};
	this._showstotal=function(d){
		var d1,v,s,o1;
		for(d1 in d){
			v=d[d1];
			if(v==0)v='';
			o1= $('#'+d1+'_stotal_'+this.rand+'');
			o1.html(v);
		}
	};
	this.data=[];
	this.suboptmenu={},
	this.listright=function(oi,event){
		var a = this.data[oi-1],ids = a.id,i;
		if(!ids)return;
		var nus=a.modenum;if(!nus||nus=='undefined')nus = this.num;
		this.tempid 	= ids;
		this.tempnum 	= nus;
		this.temparr 	= {oi:oi,evt:event};
		var da = [{name:'详情',lx:998,oi:oi,nbo:false},{name:'详情(新窗口)',lx:998,oi:oi,nbo:true}];
		var subdata = this.suboptmenu[''+nus+'_'+ids+''];
		if(!subdata){
			da.push({name:'<img src="images/loadings.gif" align="absmiddle"> 加载菜单中...',lx:999});
			this.loadoptnum(nus,ids);
		}else{
			for(i=0;i<subdata.length;i++)da.push(subdata[i]);
		}
		this.submenuobj.setData(da);
		this.submenuobj.showAt(event.clientX,event.clientY,130);
		da.push({name:'打印/导出...',lx:997,oi:oi});
	};
	this.loadoptnum=function(nus,id){
		js.ajax('agent','getoptnum',{num:nus,mid:id},function(ret){
			me.suboptmenu[''+nus+'_'+id+'']=ret;
			me.listright(me.temparr.oi,me.temparr.evt);
		},'none');
	};
	this.showmenuclick=function(d){
		d.num=this.num;d.mid=this.tempid;
		d.modenum = this.tempnum;
		var lx = d.lx;if(!lx)lx=0;
		if(lx==999)return;
		if(lx==998){this.xiang(d.oi, d.nbo);return;}
		if(lx==997){this.printexcel(d.oi);return;}
		if(lx==996){this.xiang(this.temparr.oi, false);return;}
		this.changdatsss = d;
		if(lx==2 || lx==3){
			var clx='changeuser';if(lx==3)clx='changeusercheck';
			js.changeuser(clx,'yy', this.num, d.name);
			return;
		}
		if(lx==1 || lx==9 || lx==10 || lx==13 || lx==15){
			var bts = (d.issm==1)?'必填':'选填';
			js.prompt(d.name,'请输入['+d.name+']说明('+bts+')：',function(index, text){
				if(index=='yes'){
					if(!text && d.issm==1){
						js.msg('msg','没有输入['+d.name+']说明');
					}else{
						me.showmenuclicks(d, text);
					}
				}
			});
			return;
		}
		if(lx==14){
			var url=''+apiurl+'index.php?a=lu&m=input&d=flow&num=remind&mid='+d.djmid+'&def_modenum='+d.modenum+'&def_mid='+d.mid+'&def_explain=basejm_'+jm.base64encode(d.smcont)+'';
			im.openurl(d.name, url);
			return;
		}
		if(lx==11){
			var url=''+apiurl+'index.php?a=lu&m=input&d=flow&num='+d.modenum+'&mid='+d.mid+'';
			im.openurl(d.name, url);
			return;
		}
		this.showmenuclicks(d,'');
	};
	this.changeuser=function(nas,sid){
		if(!sid)return;
		var d = this.changdatsss,sm='';
		d.changename 	= nas; 
		d.changenameid  = sid; 
		this.showmenuclicks(d,sm);
	};
	this.showmenuclicks=function(d,sm){
		if(!sm)sm='';
		d.sm = sm;
		for(var i in d)if(d[i]==null)d[i]='';
		js.ajax('index','yyoptmenu',d,function(ret){
			me.suboptmenu[''+d.modenum+'_'+d.mid+'']=false;
			me.getdata(me.nowevent, 1);
		});	
	};
	this._showdatass=function(s1,s2){
		var s='<div style="margin:8px"><table width="100%"><tr valign="top"><td width="50%">'+s1+'</td><td width="50%">'+s2+'</td></tr></table></div>';
		this.showobj.append(s);
	};
	this.showdata=function(a){
		var s='',i,len=a.rows.length,d,st='',oi,j=0,s1='',s2='';
		var sid = 'showblank_'+this.rand+'';
		if(get(sid))this.setheight(39);
		$('#'+sid+'').remove();
		$('#notrecord_'+this.rand+'').remove();
		if(typeof(a.stotal)=='object')this._showstotal(a.stotal);
		if(a.page==1){
			this.showobj.html('');
			this.data=[];
		}
		for(i=0;i<len;i++){
			d=a.rows[i];
			oi=this.data.push(d);
			if(d.showtype=='line' && d.title){
				if(s1!=''){
					this._showdatass(s1,s2);
					j=0;
				}
				s='<div class="contline">'+d.title+'</div>';
				this.showobj.append(s);
			}else{
				j++;
				if(!d.statuscolor)d.statuscolor='';
				st='';
				if(d.ishui==1)st='color:#aaaaaa;';
				s='<div oncontextmenu="agentobj.'+this.num+'.listright('+oi+',event)" style="'+st+'" class="contlist" onclick="agentobj.'+this.num+'.xiang('+oi+')">';
				if(d.title){
					if(d.face){
						s+='<div class="face"><img src="'+d.face+'" align="absmiddle">'+d.title+'</div>';
					}else{
						s+='<div class="tit">'+d.title+'</div>';
					}
				}
				if(d.optdt)s+='<div class="dt">'+d.optdt+'</div>';
				if(d.cont)s+='<div class="cont">'+d.cont.replace(/\n/g,'<br>')+'</div>';
				if(d.statustext)s+='<div style="background-color:'+d.statuscolor+';opacity:0.7" class="zt">'+d.statustext+'</div>';
				s+='</div>';
				if(j==1)s1=s;
				if(j==2){
					j=0;s2=s;
					this._showdatass(s1,s2);
					s1='';s2='';
				}
			}
		}
		if(s1!='')this._showdatass(s1,s2);
		
		var count=a.count;
		if(count==0)count=len;
		if(count>0){
			s = '<div class="showpage" style="position:static" id="'+sid+'">&nbsp; 共'+count+'条记录';
			if(a.maxpage>1)s+=',当前'+a.page+'/'+a.maxpage+'页';
			if(a.page<a.maxpage)s+=', <a onclick="agentobj.'+this.num+'.regetdata(this,'+(a.page+1)+')" href="javascript:;">点击加载</a>';
			s+= '</div>';
			if(a.count>0){
				this.showobj.after(s);
				this.setheight(-40);
			}
		}else{
			this.showobj.html('<div class="notrecord" id="notrecord'+this.rand+'">暂无记录</div>');
		}
	};
	this._init();
};
