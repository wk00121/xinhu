var isedit = 1,qmimgstr='',isxiang=1;
function othercheck(){}

//函数触发
function oninputblur(name,zb,obj){};

function initbody(){
	$('body').click(function(){
		$('.menullss').hide();
	});
	$('body').keydown(c.onkeydown);
	$('#showmenu').click(function(){
		$('.menullss').toggle();
		return false;
	});
	$('.menullss li').click(function(){
		c.mencc(this);
	});
	c.initinput();
	if(document.myform && form('fileid')){
		if(typeof(FormData)=='function'){
			f.fileobj = $.rockupload({
				autoup:false,
				fileview:'filedivview',
				allsuccess:function(){
					check(1);
				}
			});
		}else{
			$('#filedivview').parent().html('<font color="#888888">当前浏览器不支持上传</font>');
		}
	}
	js.tanstyle=1;
	if(document.myform && typeof(initbodys)=='function')initbodys();
	
	if(receiptrs){
		var s = '<div style="position:fixed;top:40%;right:5px;padding:10px;border-radius:4px;z-index:5px;background:#555555;color:white" id="receiptrsdiv"><div>此单据需要回执确认<br>请将页面拉到最后</div><div style="margin-top:5px"><input type="button"  onclick="c.receiptque()" value="回执确认" class="webbtn btn-danger"></div></div>';
		$('body').append(s);
	}
}
function showchayue(opt, st){
	alert('总查阅:'+st+'次\n最后查阅：'+opt+'');
}
function geturlact(act,cns){
	var url=js.getajaxurl(act,'mode_'+modenum+'|input','flow',cns);
	return url;
}

var f={
	change:function(o1){
		f.fileobj.change(o1);
	}
};

//拨打电话
function callPhone(o1){
	if(appobj1('callPhone',$(o1).text())){
		return false;
	}else{
		return true;
	}
}

//提交处理
function check(lx){
	var da = {'sm':form('check_explain').value,'tuiid':'0','fileid':'','mid':mid,'modenum':modenum,'zt':_getaolvw('check_status'),'qmimgstr':qmimgstr};
	if(form('fileid'))da.fileid=form('fileid').value;
	if(form('check_tuiid'))da.tuiid=form('check_tuiid').value;
	if(da.zt==''){js.setmsg('请选择处理动作');return;}if(da.zt=='2'&&isempt(da.sm)){js.setmsg('此动作必须填写说明');return;}
	var isqm = form('isqianming').value;
	var qbp  = true;
	
	if(form('zhuanbanname')){
		da.zyname 	= form('zhuanbanname').value;
		da.zynameid = form('zhuanbannameid').value;
	}
	
	//手写签名判断
	if(isqm=='1' && qmimgstr=='')qbp=false;
	if(isqm=='2' && da.zt=='1' && qmimgstr=='')qbp=false;
	if(isqm=='3' && da.zt=='2' && qmimgstr=='')qbp=false;
	if(!qbp && !da.zynameid){js.setmsg('此动作必须手写签名');return;}
	
	
	if(form('nextnameid') && da.zt=='1' && !da.zynameid){
		da.nextname 	= form('nextname').value;
		da.nextnameid 	= form('nextnameid').value;
		if(da.nextnameid==''){
			js.setmsg('请选择下一步处理人');return;
		}
	}
	if(!da.zynameid && da.zt!='2'){
		var fobj=$('span[fieidscheck]'),i,fid,flx,fiad,val;
		var subdat = js.getformdata();
		for(i=0;i<fobj.length;i++){
			fiad = $(fobj[i]);
			fid	 = fiad.attr('fieidscheck');
			val  = subdat[fid];
			da['cfields_'+fid]=val;
			if(val==''){js.setmsg(''+fiad.text()+'不能为空');return;}
		}
	}
	var ostr=othercheck(da);
	if(typeof(ostr)=='string'&&ostr!=''){js.setmsg(ostr);return;}
	if(typeof(ostr)=='object')for(var csa in ostr)da[csa]=ostr[csa];
	js.setmsg('处理中...');
	var o1 = get('check_btn');
	o1.disabled = true;
	if(lx==0 && f.fileobj && f.fileobj.start())return js.setmsg('上传相关文件中...');//有上传相关文件
	var url = c.gurl('check');
	js.ajax(url,da,function(a){
		if(a.success){
			js.setmsg(a.data,'green');
			c.callback();
			if(get('autocheckbox'))if(get('autocheckbox').checked)c.close();
		}else{
			js.setmsg(a.msg);
			o1.disabled = false;
		}
	},'post,json',function(estr){
		js.setmsg('处理失败:'+estr+'');o1.disabled = false;
	});
}
function _getaolvw(na){
	var v = '',i,o=$("input[name='"+na+"']");
	for(i=0;i<o.length;i++)if(o[i].checked)v=o[i].value;
	return v;
}

/**
*	nae记录名称 
*	zt状态名称 
*	ztid 状态id 
*	ztcol 状态颜色 
*	ocan 其他参数
*	las 说明字段Id默认other_explain
*/
function _submitother(nae,zt,ztid,ztcol,ocan,las){
	if(!las)las='other_explain';
	if(!nae||!get(las)){js.setmsg('sorry;不允许操作','','msgview_spage');return;}
	var sm=$('#'+las+'').val();
	if(!ztcol)ztcol='';
	if(!zt)zt='';if(!ocan)ocan={};
	if(!ztid){js.setmsg('没有选择状态','','msgview_spage');return;}
	if(!sm){js.setmsg('没有输入备注/说明','','msgview_spage');return;}
	var da = js.apply({'name':nae,'mid':mid,'modenum':modenum,'ztcolor':ztcol,'zt':zt,'ztid':ztid,'sm':sm},ocan);
	js.setmsg('处理中...','','msgview_spage');
	js.ajax(c.gurl('addlog'),da,function(s){
		js.setmsg('处理成功','green', 'msgview_spage');
		$('#spage_btn').hide();
	},'post',function(s){
		js.setmsg(s,'','msgview_spage');
	});
	return false;
}
var c={
	callback:function(cs){
		var calb = js.request('callback');
		if(!calb)return;
		try{parent[calb](cs);}catch(e){}
		try{opener[calb](cs);}catch(e){}
		try{parent.js.tanclose('openinput');}catch(e){}
	},
	gurl:function(a){
		var url=js.getajaxurl(a,'flowopt','flow');
		return url;
	},
	showtx:function(msg){
		js.setmsg(msg);
		if(ismobile==1)js.msg('msg', msg);
	},
	close:function(){
		var ofrom = js.request('ofrom');
		if(ofrom=='deskclient'){
			js.cliendsend('closenowtabs');
		}else{
			window.close();
			try{parent.js.tanclose('winiframe');}catch(e){}
		}
	},
	other:function(nae,las){
		_submitother(nae,'','1','',las);
	},
	others:function(nae,zt,ztid,ztcol,ocan,las){
		_submitother(nae,zt,ztid,ztcol,ocan,las);
	},
	mencc:function(o1){
		var lx=$(o1).attr('lx');
		if(lx=='2')c.delss();
		if(lx=='3')c.close();
		if(lx=='4')location.reload();
		if(lx=='0')c.clickprint(false);
		if(lx=='6')c.clickprint(true);
		if(lx=='5')c.daochuword();
		if(lx=='1'){
			var url='index.php?a=lu&m=input&d=flow&num='+modenum+'&mid='+mid+'';
			js.location(url);
		}
	},
	clickprint:function(bo){
		c.hideoth();
		if(bo){
			$('#recordss').remove();
			$('#checktablediv').remove();
			$('#recordsss').remove();
			$('.statustext').remove();
		}
		window.print();
	},
	daochuword:function(){
		var url='task.php?a=p&num='+modenum+'&mid='+mid+'&stype=word';
		js.location(url);
	},
	hideoth:function(){
		$('.menulls').hide();
		$('.menullss').hide();
		$('a[temp]').remove();
	},
	delss:function(){
		js.confirm('删除将不能恢复，确定要<font color=red>删除</font>吗？',function(lx){
			if(lx=='yes')c.delsss();
		});
	},
	delsss:function(){
		var da = {'mid':mid,'modenum':modenum,'sm':''};
		js.ajax(c.gurl('delflow'),da,function(a){
			js.msg('success','单据已删除,3秒后自动关闭页面,<a href="javascript:;" onclick="c.close()">[关闭]</a>');
			c.callback();
			setTimeout('c.close()',3000);
		},'post');
	},
	onkeydown:function(e){
		var code	= e.keyCode;
		if(code==27){
			c.close();
			return false;
		}
		if(e.altKey){
			if(code == 67){
				c.close();
				return false;
			}
		}
	},
	changeshow:function(lx){
		$('#showrecord'+lx+'').toggle();
	},
	loacdis:false,
	showviews:function(o1){
		this.loadicons();
		$.imgview({'url':o1.src,'ismobile':ismobile==1});
	},
	loadicons:function(){
		if(!this.loacdis){
			$('body').append('<link rel="stylesheet" type="text/css" href="web/res/fontawesome/css/font-awesome.min.css">');
			this.loacdis= true;
		}
	},
	//初始上传框
	initinput:function(){
		var o,o1,sna,i,tsye,tdata,uptp,far;
		var o = $('div[id^="filed_"]');
		if(isedit==1)o.show();
		for(i=0;i<o.length;i++){
			o1 = o[i];sna= $(o1).attr('tnam');tsye=$(o1).attr('tsye');tdata=$(o1).attr('tdata');
			if(isedit==1){
				uptp = 'image';
				if(tsye=='file'){
					uptp='*';
					if(!isempt(tdata))uptp=tdata;
				}
				$.rockupload({
					'inputfile':''+o1.id+'_inp',
					'initremove':false,'uptype':uptp,
					'oparams':{sname:sna,snape:tsye},
					'onsuccess':function(f,gstr){
						var sna= f.sname,tsye=f.snape,d=js.decode(gstr);
						if(tsye=='img'){
							get('imgview_'+sna+'').src = d.filepath;
							form(sna).value=d.filepath;
						}else if(tsye=='file'){
							$('#fileview_'+sna+'').html(c.showfilestr(d));
							form(sna).value=d.id;
						}
					}
				});
			}
		}
	},
	showfilestr:function(d){
		var flx = js.filelxext(d.fileext);
		var s = '<img src="web/images/fileicons/'+flx+'.gif" align="absmiddle" height=16 width=16> <a href="javascript:;" onclick="js.downshow('+d.id+')">'+d.filename+'</a> ('+d.filesizecn+')';
		return s;
	},
	//撤回操作
	chehui:function(){
		js.prompt('确定撤回吗？','要撤回上一步处理结果说明(选填)',function(jg,txt){
			if(jg=='yes')c.chehuito(txt);
		});
	},
	chehuito:function(sm){
		js.msg('wait','撤回中...');
		js.ajax(c.gurl('chehui'),{'mid':mid,'modenum':modenum,'sm':sm},function(a){
			if(a.success){
				js.msg('success', '撤回成功');
				location.reload();
			}else{
				js.msg('msg', a.msg);
			}
		},'post,json',function(s){
			js.msg('msg','操作失败');
		});
	},
	
	//预览文件
	downshow:function(id, ext,pts){
		var url = 'index.php?m=public&a=fileviewer&id='+id+'&wintype=max';
		if(pts!=''&&js.isimg(ext)){
			this.loadicons();
			$.imgview({'url':pts,'ismobile':ismobile==1});
			return false;
		}
		if(ismobile==1){
			var docsx = ',doc,docx,ppt,pptx,xls,xlsx,pdf,txt,html,';
			if(docsx.indexOf(','+ext+',')==-1)
				if(appobj1('openfile', id))return;
			js.location(url);
		}else{
			js.winiframe('文件预览',url);
		}
		return false;
	},
	changecheck_status:function(o1){
		var zt = _getaolvw('check_status');
		if(zt=='2'){
			$('#tuihuidiv').show();
		}else{
			$('#tuihuidiv').hide();
		}
		if(zt=='1'){
			$('#zhuangdiv').show();
			$('#nextxuandiv').show();
		}else{
			$('#zhuangdiv').hide();
			$('#nextxuandiv').hide();
		}
	},
	//手写签名
	qianming:function(o1){
		this.qianmingbo=false;
		js.tanbody('qianming','请在空白区域写上你的姓名',300,200,{
			html:'<div data-width="280" data-height="120" data-border="1px dashed #cccccc" data-line-color="#000000" data-auto-fit="true" id="qianmingdiv" style="margin:10px;height:120px;cursor:default"></div>',
			btn:[{text:'确定签名'},{text:'重写'}]
		});
		$('#qianmingdiv').jqSignature().on('jq.signature.changed', function() {
			c.qianmingbo=true;
		});
		
		if(ismobile==1)get('qianmingdiv').addEventListener('touchmove',function(e){
			e.preventDefault();
		},false);
	
		$('#qianming_btn0').click(function(){
			c.qianmingok();
		});
		$('#qianming_btn1').click(function(){
			$('#imgqianming').remove();
			$('#qianmingdiv').jqSignature('clearCanvas');
			c.qianmingbo = false;
			qmimgstr	 = '';
		});
	},
	qianmingok:function(){
		if(!this.qianmingbo)return;
		$('#imgqianming').remove();
		var dataUrl = $('#qianmingdiv').jqSignature('getDataURL');
		var s = '<br><img id="imgqianming" src="'+dataUrl+'"  height="90">';
		qmimgstr = dataUrl;
		$('#qianmingshow').append(s);
		js.tanclose('qianming');
	},
	qianyin:function(){
		js.msg('wait','引入中...');
		js.ajax(c.gurl('qianyin'),{},function(a){
			if(a.success){
				js.msg('success', '引入成功');
				$('#imgqianming').remove();
				var dataUrl = a.data;
				var s = '<br><img id="imgqianming" src="'+dataUrl+'"  height="90">';
				qmimgstr = dataUrl;
				$('#qianmingshow').append(s);
			}else{
				js.msg('msg', a.msg);
			}
		},'get,json',function(s){
			js.msg('msg','操作失败');
		});
	},
	optmenu:function(o1){
		var o = $(o1);
		var issm = o.attr('issm'),optmenuid = o.attr('optmenuid');
		var smts = (issm=='1') ? '(必填)' : '(选填)';
		var d  = {'modenum':modenum,'mid':mid,'name':o1.value,'issm':issm,'optmenuid':optmenuid};
		js.prompt(d.name,'请输入['+d.name+']说明'+smts+'：',function(jg,text){
			if(jg=='yes'){
				if(!text && d.issm==1){
					js.msg('msg','没有输入['+d.name+']说明');
					return true;
				}else{
					o1.disabled=true;
					o1.style.background='#888888';
					c.optmenusubmit(d, text);
				}
			}
		});
	},
	optmenusubmit:function(d,sm){
		d.sm = sm;
		js.msg('wait','处理中...');
		js.ajax(js.getajaxurl('yyoptmenu','flowopt','flow'),d,function(ret){
			if(ret.code==200){
				js.msg('success','处理成功');
			}else{
				js.msg('msg',ret.msg);
			}
		},'post,json');	
	},
	
	inputblur:function(o1,zb){
		var nae = o1.name;
		oninputblur(nae,zb,o1);
	},
	getselobj:function(fv){
		var o = form(fv);
		if(!o)return;
		var o1= o.options[o.selectedIndex];
		return o1;
	},
	getseltext:function(fv){
		var o1 = this.getselobj(fv);
		if(!o1)return '';
		return o1.text;
	},
	getselattr:function(fv,art){
		var o1 = this.getselobj(fv);
		if(!o1)return '';
		return $(o1).attr(art);
	},
	
	selectdatadata:{},
	onselectdata:{},
	selectdata:function(s1,ced,fid,tit,zbis){
		if(isedit==0)return;
		if(!tit)tit='请选择...';
		var a1 = s1.split(','),idobj=false;
		var fids = a1[1];
		if(fids){
			if(zbis==1){
				var gezs = this.getxuandoi(fid);
				fids+=gezs[2];
			}
			idobj=form(fids);
		}
		$.selectdata({
			data:this.selectdatadata[fid],title:tit,
			fid:fid,
			url:geturlact('getselectdata',{act:a1[0],sysmodenum:modenum,sysmid:mid}),
			checked:ced, nameobj:form(fid),idobj:idobj,
			onloaddata:function(a){
				c.selectdatadata[fid]=a;
			},
			onselect:function(seld,sna,sid){
				if(c.onselectdata[this.fid])c.onselectdata[this.fid](seld,sna,sid);
			}
		});
	},
	
	
	//回执确认
	receiptque:function(){
		$('#receiptrsdiv').remove();
		js.prompt('回执确认','确认说明(选填)', function(jg,txt){
			if(jg=='yes'){
				c.receiptqueok(txt);
			}
		});
	},
	receiptqueok:function(sm){
		js.msg('wait','回执确认确认提交中...');
		var da = {'mid':mid,'modenum':modenum,'sm':sm,'receiptid':receiptrs.id};
		js.ajax(c.gurl('receiptcheck'),da,function(a){
			js.msg('success','回执确认提交成功');
		},'post');
	}
};