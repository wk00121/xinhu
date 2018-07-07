/**
	edittable 选择人员插件
	caratename：chenxihu
	caratetime：2014-04-06 21:40:00
	email:admin@rockoa.com
	homepage:www.xh829.com
*/

(function ($) {
	
	function _getstyles(){
		var s='<style>.changeuserlist div.listsss{padding:10px; background:white;border-bottom:1px #eeeeee solid;cursor:default}.changeuserlist div:active{ background:#f1f1f1}.changeuserbotton{height:30px;width:50px; background:#d9534f;color:white;font-size:14px;border:none;padding:0px;margin:0px;line-height:20px;cursor:default;opacity:1}.changeuserbotton:active{color:white;border:none;opacity:0.8}</style>';
		return s;
	}
	
	function chnageuser(sobj, options){
		var obj		= sobj;
		var rand	= ''+parseInt(Math.random()*9999999)+''; 
		var me		= this;
		this.rand	= rand;
		
		this._init	= function(){
			for(var i in options)this[i]=options[i];
			this.oveob = false;
			if(this.showview!='' && get(this.showview))this.oveob=true;
			
			if(!this.oveob){
				window.onhashchange=function(){
					var has = location.hash;
					if(has.indexOf('#changeuser')==-1)me.hide();
				}
				js.location('#changeuser');
			}
	
			this.userarr = [];
			this.deptarr = [];
			if(isempt(this.changerange) && isempt(this.changerangeno)){
				var us = js.getoption('userjson');
				if(us)this.userarr = js.decode(us);
				
				us = js.getoption('deptjson');
				if(us)this.deptarr = js.decode(us);
			}
			this.show();
		};
		
		this.creatediv=function(){
			var type='checkbox';
			if(this.changetype.indexOf('check')==-1)type='radio';
			this.inputtype = type;
			$('#changeuser_'+rand+'').remove();
			var hei = $(window).height(),jhei=50,atts='position:fixed;';
			if(this.oveob){
				hei = $('#'+this.showview+'').height();
				atts='';
			}
			var s='<div style="'+atts+'z-index:100;width:100%;height:100%;overflow:hidden;left:0px;top:0px; background:white" id="changeuser_'+rand+'">';
			if(this.titlebool){
				s+='<div style="height:50px;line-height:50px;text-align:center; background:white;border-bottom:1px #cccccc solid"><b>'+this.title+'</b></div>';
				jhei+=50;
			}
			if(this.changetype.indexOf('user')>=0){
				s+='<div style="height:50px;overflow:hidden;border-bottom:1px #cccccc solid"><table width="100%"><tr><td width="100%" height="50"><input id="changekey_'+this.rand+'" placeholder="部门/姓名/职位" style="height:30px;border:none;background:none;width:100%;margin:0px 10px"></td><td><input style="background:none;border:none;color:#666666" class="changeuserbotton" id="changesoubtn_'+this.rand+'" value="查找" type="button" ></td></tr></table></div>';
				jhei+=50;
			}
			s+='<div style="-webkit-overflow-scrolling:touch;height:'+(hei-jhei)+'px;overflow:auto; background:#f1f1f1" class="changeuserlist">';
			s+='<span id="showdiv'+rand+'_0"></span>';
			s+='<span id="showdiv'+rand+'_search"></span>';
			s+='</div>';
			var s3= '<input id="changeboxs_'+rand+'" style="width:18px;height:18px;" align="absmiddle" type="checkbox" >';
			if(type!='checkbox1')s3='';
			s+='<div style="height:50px;line-height:50px;border-top:1px #cccccc solid" align="right"><table width="100%"><tr><td width="10" nowrap>&nbsp;</td><td width="80%">'+s3+'</td><td><input style="width:70px;border:none" type="button" id="changereload_'+rand+'" class="changeuserbotton" value="刷新数据" ></td><td width="20" nowrap>&nbsp;</td><td><input class="changeuserbotton" type="button" id="changecancl_'+rand+'" value="取消" ></td><td width="20" nowrap>&nbsp;</td><td height="50"><input style="background:#1389D3;" id="changeok_'+rand+'" type="button" value="确定" class="changeuserbotton"></td><td width="10" nowrap>&nbsp;</td></tr></table></div>';
			s+=_getstyles();
			s+='</div>';
			if(atts==''){
				$('#'+this.showview+'').html(s);
			}else{
				obj.append(s);
			}
			
			$('#changecancl_'+this.rand+'').click(function(){
				me._clickcancel();
			});
			$('#changereload_'+this.rand+'').click(function(){
				me._loaddata();
			});
			$('#changeok_'+this.rand+'').click(function(){
				me.queding();
			});
			$('#changesoubtn_'+this.rand+'').click(function(){
				me._searchkey(true);
			});
			$('#changekey_'+this.rand+'').keydown(function(e){
				me._searchkeys(e)
			});
			$('#changeboxs_'+this.rand+'').click(function(){
				me._changboxxuan(this)
			});
		};
		this.showlist=function(pid,oi){
			var type=this.inputtype,hw=24;
			var a=this.deptarr,len=a.length,i,s='',ssu='',wjj,s2='',sids;
			var s1='<div style="width:'+(hw*oi)+'px"></div>';
			var dob=this.changetype.indexOf('dept')==-1;
			var uob=this.changetype.indexOf('user')>=0;
			for(i=0;i<len;i++){
				if(a[i].pid==pid){
					wjj= 'images/files.png';
					if(a[i].ntotal=='0' && uob)wjj= 'images/file.png';
					s2 = '<input name="changeuserinput_'+rand+'" xls="d" xname="'+a[i].name+'" xu="'+i+'" value="'+a[i].id+'" style="width:18px;height:18px;" type="'+type+'">';
					if(dob)s2='';
					if(s2!='' && !this._isdeptcheck(a[i]))s2='';
					s+='<div class="listsss">';
					s+='<table width="100%"><tr><td>'+s1+'</td><td deptxu="'+i+'_'+oi+'" width="100%"><img align="absmiddle" height="20" height="20" src="'+wjj+'">&nbsp;'+a[i].name+'</td><td>'+s2+'</td></tr></table>';
					s+='</div>';
					s+='<span  show="false" id="showdiv'+rand+'_'+a[i].id+'"></span>';
				}
			}
			if(uob){
				a=this.userarr;
				len=a.length;
				for(i=0;i<len;i++){
					sids = ','+a[i].deptids+',';
					if(a[i].deptid==pid || sids.indexOf(','+pid+',')>=0){
						ssu+='<div class="listsss">';
						ssu+='<table width="100%"><tr><td>'+s1+'</td><td width="100%"><img align="absmiddle" height="24" height="24" src="'+a[i].face+'">&nbsp;'+a[i].name+'<span style="font-size:12px;color:#888888">('+a[i].ranking+')</span></td><td><input name="changeuserinput_'+rand+'"  xls="u" xname="'+a[i].name+'" value="'+a[i].id+'" style="width:18px;height:18px;" type="'+type+'"></td></tr></table>';
						ssu+='</div>';
					}
				}
			}
			
			$('#showdiv'+rand+'_'+pid+'').html(ssu+s).attr('show','true');
			if(pid==0)this.showlist(1, 1);
			$('#showdiv'+rand+'_0 [deptxu]').unbind('click').click(function(){
				me._deptclicks(this);
			});
		};
		this._searchkeys=function(e){
			clearTimeout(this._searchkeystime);
			this._searchkeystime=setTimeout(function(){
				me._searchkey(false);
			},500);
		};
		this._isdeptcheck=function(a){
			if(this.inputtype=='checkbox' && this.changetype.indexOf('user')>=0 && this.changetype.indexOf('dept')>=0){
				var stotal,i,nstotal=0,len=this.userarr.length,spath;
				stotal = parseFloat(a.stotal);
				for(i=0;i<len;i++){
					spath = this.userarr[i].deptpath;
					if(spath.indexOf('['+a.id+']')>=0)nstotal++;
				}
				return nstotal>=stotal;
			}else{
				return true;
			}
		},
		this._clickcheckbox=function(o1){
			var o = $(o1),xu,a,stotal,i,nstotal=0,len=this.userarr.length,spath;
			if(o.attr('xls')!='d')return;
			xu = parseFloat(o.attr('xu'));
			a  = this.deptarr[xu];
			stotal = parseFloat(a.stotal);
			for(i=0;i<len;i++){
				spath = this.userarr[i].deptpath;
				if(spath.indexOf('['+a.id+']')>=0)nstotal++;
			}
			if(nstotal<stotal){
				o1.checked=false;
				o1.disabled=true;
				js.msg('msg','无权选择部门['+a.name+']');
			}
		},
		this._searchkey = function(bo){
			var key = $('#changekey_'+this.rand+'').val(),s='',a=[],d=[],len,i;
			a=this.userarr;
			len=a.length;
			if(key!='')for(i=0;i<len;i++)if(a[i].name.indexOf(key)>-1 || a[i].pingyin.indexOf(key)==0 || a[i].deptname.indexOf(key)>-1 || a[i].ranking.indexOf(key)>-1)d.push(a[i]);
			len = d.length;
			for(i=0;i<len;i++){
				s+='<div class="listsss">';
				s+='<table width="100%"><tr><td></td><td width="100%"><img align="absmiddle" height="24" height="24" src="'+d[i].face+'">&nbsp;'+d[i].name+'<span style="font-size:12px;color:#888888">('+d[i].ranking+')</span></td><td><input name="changeuserinput_'+rand+'_soukey"  xls="u" xname="'+d[i].name+'" value="'+d[i].id+'" style="width:18px;height:18px;" type="'+this.inputtype+'"></td></tr></table>';
				s+='</div>';
			}
			if(bo && s=='' && key!='')js.msg('msg','无相关['+key+']的记录', 2);
			$('#showdiv'+rand+'_search').html(s);
			var o1 = $('#showdiv'+rand+'_0');
			if(s==''){o1.show();}else{o1.hide();}
		};
		this._clickcancel=function(){
			if(!this.oveob)history.back();
			this.hide();
		};
		this.hide=function(){
			$('#changeuser_'+rand+'').remove();
			this.oncancel();
		};
		this.show=function(){
			this.creatediv();
			if(this.deptarr.length>0){
				this.showlist(0,0);
			}else{
				this._loaddata();
			}
		};
		this._deptclicks=function(o){
			var sxu = $(o).attr('deptxu').split('_');
			var a 	= this.deptarr[sxu[0]];
			var o1	= $('#showdiv'+rand+'_'+a.id+'');
			var lx	= o1.attr('show');
			if(lx=='false'){
				this.showlist(a.id, parseFloat(sxu[1])+1);
			}else{
				o1.toggle();
			}
		};
		
		this._loaddata=function(){
			var o1 = $('#showdiv'+rand+'_0'),url;
			o1.html('<div align="center" style="padding:30px"><img src="images/mloading.gif"></div>');
			var url = 'index.php?a=deptuserjson&m=dept&d=system&ajaxbool=true&changerange='+this.changerange+'&changerangeno='+this.changerangeno+'&gtype=change';
			$.getJSON(url, function(ret){
				if(ret.code==200){
					ret = ret.data;
					me._loaddatashow(ret);	
				}else{
					o1.html(ret.msg);
				}
			});
		};
		this._loaddatashow=function(ret){
			if(isempt(this.changerange) && isempt(this.changerangeno)){
				js.setoption('deptjson', ret.deptjson);
				js.setoption('userjson', ret.userjson);
				js.setoption('groupjson', ret.groupjson);
			}
			this.userarr = js.decode(ret.userjson);
			this.deptarr = js.decode(ret.deptjson);
			this.showlist(0, 0);
		};
		this._changboxxuan=function(os){
			var ns= 'changeuserinput_'+rand+'';
			if($('#showdiv'+rand+'_search').html()!='')ns+='_soukey';
			var ob = os.checked,o=$("input[name='"+ns+"']"),i;
			for(i=0;i<o.length;i++)o[i].checked=ob;
		};
		this.queding=function(){
			var ns= 'changeuserinput_'+rand+'';
			if($('#showdiv'+rand+'_search').html()!='')ns+='_soukey';
			var o = $("input[name='"+ns+"']");
			var i,len=o.length,o1,xls,xna,xal,sid='',sna='',ob1=this.changetype.indexOf('dept')==-1,ob2=this.changetype.indexOf('user')==-1;
			var ob3=ob1 || ob2;
			for(i=0;i<len;i++){
				o1 = $(o[i]);
				if(o[i].checked){
					xls= o1.attr('xls');
					xna= o1.attr('xname');
					xal= o1.val();
					if(ob3)xls='';
					sid+=','+xls+''+xal+'';
					sna+=','+xna+'';
				}
			}
			if(sid!=''){
				sid=sid.substr(1);
				sna=sna.substr(1);
			}
			if(this.idobj)this.idobj.value=sid;
			if(this.nameobj){
				this.nameobj.value=sna;
				this.nameobj.focus();
			}
			this.onselect(sna, sid);
			this.hide();
		}
	}
	
	function selectdata(sobj, options){
		var obj		= sobj;
		var rand	= ''+parseInt(Math.random()*9999999)+''; 
		var me		= this;
		this.rand	= rand;
		this.ismobile = false;
		
		this._init	= function(){
			for(var i in options)this[i]=options[i];
			if(typeof(ismobile) && ismobile==1)this.ismobile = true;
			this._showcreate();
		};
		this._showcreate = function(){
			var ws = '300px';
			if(this.ismobile)ws='90%';
			var s='<div style="z-index:1;width:100%;height:100%;overflow:hidden;left:0px;top:0px; background:rgba(0,0,0,0.3);position:fixed;z-index:9" id="selectdata_'+rand+'">';
			s+='<div tsid="main" id="mints_'+rand+'" style="position:absolute;top:30%; background:white;width:'+ws+';box-shadow:0px 0px 5px rgba(0,0,0,0.3)">';
			s+='	<div onmousedown="js.move(\'mints_'+rand+'\')" style="line-height:40px; background:#2c3e50;color:white;font-size:16px"> &nbsp; &nbsp;'+this.title+'</div>';
			s+='	<div style="height:50px;overflow:hidden;border-bottom:1px #cccccc solid"><table width="100%"><tr><td width="100%" height="50"><input id="changekey_'+this.rand+'" placeholder="搜索关键词" style="height:30px;border:none;background:none;width:100%;margin:0px 10px"></td><td><input style="background:none;color:#666666" class="changeuserbotton" id="changesoubtn_'+this.rand+'" value="查找" type="button" ></td></tr></table></div>';
			s+='	<div style="-webkit-overflow-scrolling:touch;height:300px;overflow:auto; background:#f1f1f1" id="selectlist_'+rand+'" class="changeuserlist"></div>';
			s+='	<div style="height:50px;line-height:50px;border-top:1px #cccccc solid" align="right"><table width="100%"><tr><td width="10" nowrap>&nbsp;</td><td width="80%"><font color="#888888" tsid="count"></font></td><td><input type="button" id="changereload_'+rand+'" class="changeuserbotton" value="刷新" ></td><td width="10" nowrap>&nbsp;</td><td><input class="changeuserbotton" type="button" id="changecancl_'+rand+'" value="取消" ></td><td width="10" nowrap>&nbsp;</td><td height="50"><input style="background:#1389D3;" id="changeok_'+rand+'" type="button" value="确定" class="changeuserbotton"></td><td width="10" nowrap>&nbsp;</td></tr></table></div>';
			s+='</div>';
			s+='</div>';
			s+=_getstyles();
			$('body').append(s);
			this.showdata(this.data);
			var o = this._getobj('main');
			var l = ($(window).width()-o.width())*0.5,t = ($(window).height()-o.height())*0.5;
			o.css({'left':''+l+'px','top':''+t+'px'});
			$('#changecancl_'+this.rand+'').click(function(){
				me._clickcancel();
			});
			$('#changeok_'+this.rand+'').click(function(){
				me.queding();
			});
			$('#changereload_'+this.rand+'').click(function(){
				me.loaddata();
			});
			$('#changesoubtn_'+this.rand+'').click(function(){
				me._searchkey(true);
			});
			$('#changekey_'+this.rand+'').keydown(function(e){
				me._searchkeys(e)
			});
			$('#changekey_'+this.rand+'').keyup(function(e){
				me._searchkeys(e)
			});
		};
		this._getobj=function(lx){
			var o = $('#selectdata_'+rand+'').find("[tsid='"+lx+"']");
			return o;
		};
		this._clickcancel=function(){
			this.hide();
		};
		this.hide=function(){
			$('#selectdata_'+rand+'').remove();
			this.oncancel();
		};
		this.queding=function(){
			var ns= 'changeuserinput_'+rand+'';
			var o = $("input[name='"+ns+"']");
			var i,len=o.length,o1,xna,xu,xal,sid='',sna='',seld=[];
			for(i=0;i<len;i++){
				o1 = $(o[i]);
				if(o[i].checked){
					xna= o1.attr('xname');
					xu = parseFloat(o1.attr('xu'));
					if(this.checked){
						seld.push(this.nowdata[xu]);
					}else{
						seld=this.nowdata[xu];
					}
					xal= o1.val();
					sid+=','+xal+'';
					sna+=','+xna+'';
				}
			}
			if(sid!=''){
				sid=sid.substr(1);
				sna=sna.substr(1);
			}
			if(this.idobj)this.idobj.value=sid;
			if(this.nameobj){
				this.nameobj.value=sna;
				this.nameobj.focus();
			}
			this.onselect(seld,sna, sid);
			this.hide();
		};
		this.showdata=function(a,inb){
			if(!a)a=[];
			var s='',len=a.length,s1='';
			if(len==0){
				s='<div align="center" style="margin-top:30px;color:#cccccc;font-size:16px">无记录</div>';
				if(!inb)this.loaddata();
			}else{
				s = this.showhtml(a);
				s1='共'+len+'条';
			}
			this._getobj('count').html(s1);
			var o = $('#selectlist_'+rand+'');
			o.html(s);
		};
		this.showhtml=function(a){
			this.nowdata = a;
			var i,len=a.length,s='',s2,s1='',atr,oldvel='',d;
			if(this.nameobj)oldvel=this.nameobj.value;
			if(this.idobj)oldvel=this.idobj.value;
			var type='checkbox',ched='';
			if(!this.checked)type='radio';
			oldvel = ','+oldvel+',';
			for(i=0;i<len && i<this.maxshow;i++){
				ched='';
				d = a[i];
				if(!isempt(d.value) && oldvel.indexOf(','+d.value+',')>-1)ched='checked';
				s2 = '<input xu="'+i+'" '+ched+' name="changeuserinput_'+rand+'" xname="'+d.name+'" value="'+d.value+'" style="width:18px;height:18px;" align="absmiddle" type="'+type+'">';
				atr = '';
				if(d.padding)atr='style="padding-left:'+d.padding+'px"';
				if(!d.iconswidth)d.iconswidth=18;
				if(d.iconsimg)s2+=' <img align="absmiddle" src="'+d.iconsimg+'" height="'+d.iconswidth+'" width="'+d.iconswidth+'">';
				s+='<div class="listsss" '+atr+'><label>'+s2+'&nbsp;'+d.name+'</label></div>';
			}
			return s;
		};
		this.loaddata=function(){
			var url = this.url;
			if(url=='')return;
			$('#selectlist_'+rand+'').html('<div align="center" style="margin-top:30px"><img src="images/mloading.gif"></div>');
			$.getJSON(url, function(a){
				me.data = a;
				me.onloaddata(a);
				me.showdata(a, true);
			});
		};
		this._searchkeys=function(e){
			clearTimeout(this._searchkeystime);
			this._searchkeystime=setTimeout(function(){
				me._searchkey(false);
			},500);
		};
		this._searchkey = function(bo){
			var key = $('#changekey_'+this.rand+'').val(),a=[],d=[],len,i,oi=0,s;
			a=this.data;
			len=a.length;if(len==0)return;
			if(key!='')for(i=0;i<len;i++){
				if(a[i].name.indexOf(key)>-1 || a[i].value==key){
					d.push(a[i]);
					oi++;
					if(oi>20)break;//最多显示搜索
				}
			}
			len = d.length;
			if(len==0){
				s=this.showhtml(this.data);
			}else{
				s=this.showhtml(d);
			}
			$('#selectlist_'+rand+'').html(s);
			if(bo && len==0 && key!='')js.msg('msg','无相关['+key+']的记录', 2);
		};
	}
	
	$.fn.chnageuser	= function(options){
		var defaultVal = {
			'title' : '请选择...',
			'titlebool':true,
			'showview':'',
			'changerange':'', //从哪些人员中选择
			'changerangeno':'', //不从哪些人选择
			'changetype' : 'user',
			'idobj':false,'nameobj':false,
			'onselect':function(){},
			'oncancel':function(){}
		};
		var can = $.extend({}, defaultVal, options);
		var funcls = new chnageuser($(this), can);
		funcls._init();
		return funcls;
	};
	
	$.selectdata	= function(options){
		var defaultVal = {
			'showview': '',
			'title' : '请选择...',
			'maxshow' : 100, //最多显示防止卡死浏览器
			'data'	  : [], 'url' : '',
			'checked' : false,
			'idobj'	  : false, 'nameobj':false,
			'onselect': function(){},
			'oncancel': function(){},
			'onloaddata':function(){}
		};
		var can = $.extend({}, defaultVal, options);
		var funcls = new selectdata(false, can);
		funcls._init();
		return funcls;
	};
	
})(jQuery);