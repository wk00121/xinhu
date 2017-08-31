var myScroll=false,yy={
	sousoukey:'',
	resizehei:function(){
		var hei= this.getheight();
		var ob = this.showobj.css({'height':''+hei+'px'});
		return ob;
	},
	getheight:function(ss){
		var hei = 0;if(!ss)ss=0;
		if(get('searsearch_bar'))hei+=45;
		if(get('header_title'))hei+=50;
		if(get('footerdiv'))hei+=50;
		return $(window).height()-hei+ss;
	},
	initScroll:function(){
		if(get('searsearch_bar')){
			this.touchobj = $('#mainbody').rockdoupull({
				upbool:true,
				onupbefore:function(){
					return yy.onupbefore();
				},
				upmsgdiv:'showblank',
				onupsuccess:function(){
					yy.scrollEndevent();
				}
			});
		}
	},
	init:function(){
		this.num = json.num;
		this.showobj = $('#mainbody');
		$('.weui_navbar').click(function(){return false;});
		$('body').click(function(){
			$("div[id^='menushoess']").remove();
		});
		this.initScroll();
		this.resizehei();
		$(window).resize(function(){
			yy.resizehei();
		});
	},
	clickmenu:function(oi,o1){
		var sid='menushoess_'+oi+'';
		if(get(sid)){
			$('#'+sid+'').remove();
			return;
		}
		$("div[id^='menushoess']").remove();
		var a = json.menu[oi],slen=a.submenu.length,i,a1;
		this.menuname1 = a.name;
		this.menuname2 = '';
		if(slen<=0){
			this.clickmenus(a);
		}else{
			var o=$(o1),w=1/json.menu.length*100;
			var s='<div id="'+sid+'" style="position:fixed;z-index:5;left:'+(o.offset().left)+'px;bottom:50px; background:white;width:'+w+'%" class="menulist r-border-r r-border-l">';
			for(i=0;i<slen;i++){
				a1=a.submenu[i];
				s+='<div onclick="yy.clickmenua('+oi+','+i+')" class="r-border-t" style="color:'+a1.color+';">'+a1.name+'</div>';
			}
			s+='</div>';
			$('body').append(s);
		}
	},
	searchuser:function(){
		$('#searsearch_bar').addClass('weui_search_focusing');
		$('#search_input').focus();
	},
	searchcancel:function(){
		$('#search_input').blur();
		$('#searsearch_bar').removeClass('weui_search_focusing');
	},
	souclear:function(){
		$('#search_input').val('').focus();
	},
	sousousou:function(){
		var key = $('#search_input').blur().val();
		this.keysou(key);
	},
	clickmenua:function(i,j){
		var a = json.menu[i].submenu[j];
		this.menuname2 = a.name;
		this.clickmenus(a);
	},
	onclickmenu:function(a){
		return true;
	},
	clickmenus:function(a){
		$("div[id^='menushoess']").remove();
		if(!this.onclickmenu(a))return;
		var tit = this.menuname1;
		if(this.menuname2!='')tit+='→'+this.menuname2+'';
		document.title = tit;
		$('#header_title').html(tit);
		if(a.type==0){
			this.searchcancel();
			this.sousoukey='';
			this.clickevent(a);return;
		}
		if(a.type==1){
			var url=a.url,amod=this.num;
			if(url.substr(0,3)=='add'){
				if(url!='add')amod=url.replace('add_','');
				url='index.php?a=lum&m=input&d=flow&num='+amod+'&show=we';
			}
			js.location(url);
		}
	},
	clickevent:function(a){
		this.getdata(a.url, 1);
	},
	data:[],
	_showstotal:function(d){
		var d1,v,s,o1;
		for(d1 in d){
			v=d[d1];
			if(v==0)v='';
			o1= $('#'+d1+'_stotal');
			o1.html(v);
		}
	},
	regetdata:function(o,p){
		var mo = 'mode';
		if(o){
			o.innerHTML='<img src="images/loading.gif" align="absmiddle">';
			mo = 'none';
		}
		this.getdata(this.nowevent,p, mo);
	},
	getdata:function(st,p, mo){
		this.nowevent=st;
		this.nowpage = p;
		if(!mo)mo='mode';
		var key = ''+this.sousoukey;
		if(key)key='basejm_'+jm.base64encode(key)+'';
		js.ajax('index','getyydata',{'page':p,'event':st,'num':this.num,'key':key},function(ret){
			yy.showdata(ret);
		},mo, false,false, 'get');
	},
	getfirstnum:function(d){
		var dbh = 'def',bh='';
		var a = d[0];
		if(a){
			bh = a.url;
			if(a.submenu[0])bh=a.submenu[0].url;
		}
		if(isempt(bh))bh=dbh;
		return bh;
	},
	reload:function(){
		this.getdata(this.nowevent,this.nowpage);
	},
	keysou:function(key){
		if(this.sousoukey == key)return;
		this.sousoukey = key;
		this.regetdata(false,1);
	},
	xiang:function(oi){
		var d = this.data[oi-1];
		var ids = d.id,nus=d.modenum,modne=d.modename;
		if(!ids)return;
		if(!nus||nus=='undefined')nus = this.num;
		var url='task.php?a=x&num='+nus+'&mid='+ids+'&show=we';
		js.location(url);
	},
	suboptmenu:{},
	showmenu:function(oi){
		var a = this.data[oi-1],ids = a.id,i;
		var nus=a.modenum;if(!nus||nus=='undefined')nus = this.num;
		if(a.type=='applybill' && nus){
			var url='index.php?a=lum&m=input&d=flow&num='+nus+'&show=we';
			js.location(url);return;
		}
		if(!ids)return;
		this.tempid 	= ids;
		this.tempnum 	= nus;
		this.temparr 	= {oi:oi};
		var da = [{name:'详情',lx:998,oi:oi}];
		var subdata = this.suboptmenu[''+nus+'_'+ids+''];
		if(typeof(subdata)=='object'){
			for(i=0;i<subdata.length;i++)da.push(subdata[i]);
		}else{
			da.push({name:'<img src="images/loadings.gif" align="absmiddle"> 加载菜单中...',lx:999});
			this.loadoptnum(nus,ids);
		}
		js.showmenu({
			data:da,
			width:150,
			onclick:function(d){
				yy.showmenuclick(d);
			}
		});
	},
	loadoptnum:function(nus,id){
		js.ajax('agent','getoptnum',{num:nus,mid:id},function(ret){
			yy.suboptmenu[''+nus+'_'+id+'']=ret;
			yy.showmenu(yy.temparr.oi);
		},'none',false,function(estr){
			yy.suboptmenu[''+nus+'_'+id+'']=[];
			yy.showmenu(yy.temparr.oi);
		});
	},
	showmenuclick:function(d){
		d.num=this.num;d.mid=this.tempid;
		d.modenum = this.tempnum;
		var lx = d.lx;if(!lx)lx=0;
		if(lx==999)return;
		if(lx==998){this.xiang(d.oi);return;}
		if(lx==996){this.xiang(this.temparr.oi);return;}
		this.changdatsss = d;
		if(lx==2 || lx==3){
			var clx='changeuser';if(lx==3)clx='changeusercheck';
			$('body').chnageuser({
				'changetype':clx,
				'titlebool':get('header_title'),
				'onselect':function(sna,sid){
					yy.xuanuserok(sna,sid);
				}
			});
			return;
		}
		if(lx==1 || lx==9 || lx==10 || lx==13 || lx==15 || lx==16 || lx==17){
			var bts = (d.issm==1)?'必填':'选填';
			js.wx.prompt(d.name,'请输入['+d.name+']说明('+bts+')：',function(text){
				if(!text && d.issm==1){
					js.msg('msg','没有输入['+d.name+']说明');
				}else{
					yy.showmenuclicks(d, text);
				}
			});
			return;
		}
		//添加提醒设置
		if(lx==14){
			var url='index.php?a=lum&m=input&d=flow&num=remind&mid='+d.djmid+'&def_modenum='+d.modenum+'&def_mid='+d.mid+'&def_explain=basejm_'+jm.base64encode(d.smcont)+'&show=we';
			js.location(url);
			return;
		}
		if(lx==11){
			var url='index.php?a=lum&m=input&d=flow&num='+d.modenum+'&mid='+d.mid+'&show=we';
			js.location(url);
			return;
		}
		this.showmenuclicks(d,'');
	},
	xuanuserok:function(nas,sid){
		if(!sid)return;
		var d = this.changdatsss,sm='';
		d.changename 	= nas; 
		d.changenameid  = sid; 
		this.showmenuclicks(d,sm);
	},
	showmenuclicks:function(d, sm){
		if(!sm)sm='';
		d.sm = sm;
		for(var i in d)if(d[i]==null)d[i]='';
		js.ajax('index','yyoptmenu',d,function(ret){
			yy.suboptmenu[''+d.modenum+'_'+d.mid+'']=false;
			yy.getdata(yy.nowevent, 1);
		});	
	},
	showdata:function(a){
		this.overend = true;
		var s='',i,len=a.rows.length,d,st='',oi;
		$('#showblank').remove();
		$('#notrecord').remove();
		if(typeof(a.stotal)=='object')this._showstotal(a.stotal);
		if(a.page==1){
			this.showobj.html('');
			this.data=[];
		}
		for(i=0;i<len;i++){
			d=a.rows[i];
			oi=this.data.push(d);
			if(d.showtype=='line' && d.title){
				s='<div class="contline">'+d.title+'</div>';
			}else{
				if(!d.statuscolor)d.statuscolor='';
				st='';
				if(d.ishui==1)st='color:#aaaaaa;';
				s='<div style="'+st+'" class="r-border contlist">';
				if(d.title){
					if(d.face){
						s+='<div onclick="yy.showmenu('+oi+')" class="face"><img src="'+d.face+'" align="absmiddle">'+d.title+'</div>';
					}else{
						s+='<div onclick="yy.showmenu('+oi+')" class="tit">'+d.title+'</div>';
					}
				}
				if(d.optdt)s+='<div class="dt">'+d.optdt+'</div>';
				if(d.picurl)s+='<div onclick="yy.showmenu('+oi+')" class="imgs"><img src="'+d.picurl+'" width="100%"></div>';
				if(d.cont)s+='<div  onclick="yy.showmenu('+oi+')" class="cont">'+d.cont.replace(/\n/g,'<br>')+'</div>';
				if(d.id && d.modenum){
					s+='<div class="xq r-border-t"><font onclick="yy.showmenu('+oi+')">操作<i class="icon-angle-down"></i></font><span onclick="yy.xiang('+oi+')">详情&gt;&gt;</span>';
					s+='</div>';
				}
				if(d.statustext)s+='<div style="background-color:'+d.statuscolor+';opacity:0.7" class="zt">'+d.statustext+'</div>';
				s+='</div>';
			}
			this.showobj.append(s);
		}
		var count=a.count;
		if(count==0)count=len;
		if(count>0){
			this.nowpage = a.page;
			s = '<div class="showblank" id="showblank">共'+count+'条记录';
			if(a.maxpage>1)s+=',当前'+a.maxpage+'/'+a.page+'页';
			if(a.page<a.maxpage){
				s+=', <a id="showblankss" onclick="yy.regetdata(this,'+(a.page+1)+')" href="javascript:;">点击加载</a>';
				this.overend = false;
			}
			s+= '</div>';
			this.showobj.append(s);
			if(a.count==0)$('#showblank').html('');
		}else{
			this.showobj.html('<div class="notrecord" id="notrecord">暂无记录</div>');
		}
		if(this.touchobj)this.touchobj.onupok();
	},
	onupbefore:function(){
		if(this.overend)return false;
		var a={
			'msg':'↑ 继续上拉加载第'+(this.nowpage+1)+'页',
			'msgok' : '<a id="showblankss">↓ 释放后</a>加载第'+(yy.nowpage+1)+'页...'
		};
		return a;
	},
	scrollEndevent:function(){
		yy.regetdata(get('showblankss'),yy.nowpage+1);
	}
}