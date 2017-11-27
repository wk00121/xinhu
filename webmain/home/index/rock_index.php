<?php 
/**
*	桌面首页显示
*/
defined('HOST') or die ('not access');
?>
<script>
var todocontent = '',homeobject={},homenums=<?=json_encode($homearrs)?>;
</script>

<div style="padding:10px">

<div align="left">

	<table  border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr valign="top">
		
		<?php
		$bili = 100 / count($homeitems);
		echo '<td width="'.$bili.'%">';
		if(isset($homeitems[0]))foreach($homeitems[0] as $nums){
			$paths = ''.ROOT_PATH.'/'.P.'/home/desktop/items_'.$nums.'.php';
			if(file_exists($paths))include_once($paths);
		}
		
		echo '</td>';
		
		for($i=1;$i<=3;$i++)if(isset($homeitems[$i])){
			echo '<td width="'.$bili.'%" style="padding-left:20px;">';
			foreach($homeitems[$i] as $nums){
				$paths = ''.ROOT_PATH.'/'.P.'/home/desktop/items_'.$nums.'.php';
				if(file_exists($paths))include_once($paths);
			}
			echo '</td>';
		}
		?>		
	</tr>
	</table>	
</div>

<?php
$paths = ''.ROOT_PATH.'/'.P.'/home/desktop/footer.php';
if(file_exists($paths))include_once($paths);
?>
	
</div>

<script>
$(document).ready(function(){
	var optdt = '',loadci=0, taskarr={}, miao=200; //定时秒数
	var c= {
		gettotal:function(rlx){
			clearTimeout(this.timeteims);
			var nums = '',i;
			for(i=0;i<homenums.length;i++){
				nums+=','+homenums[i]+'';
			}
			if(!rlx)rlx='';
			var url  = js.getajaxurl('gettotal','index','home', {atype:rlx,loadci:loadci,optdt:optdt,nums:nums.substr(1)});
			$('#refresh_text').html('刷新统计中...');
			js.ajaxbool =false;
			js.ajax(url,{},function(da){
				c.gettotalshow(da);
			},'get,json');
		},
		//初始化
		init:function(){
			this.gettotal();
			var i,nust;
			for(i=0;i<homenums.length;i++){
				nust = homenums[i];
				if(homeobject[''+nust+'_init'])homeobject[''+nust+'_init']();
			}
		},
		refresh:function(){
			this.gettotal();
		},
		shumiao:function(oi){
			clearTimeout(this.timeteims);
			if(oi<=0){
				this.gettotal();
			}else{
				$('#refresh_text').html(''+oi+'秒后刷新&gt;&gt;');
				this.timeteims = setTimeout(function(){c.shumiao(oi-1)},1000);
			}
			if(homeobject.showtime)homeobject.showtime();
		},
		gettotalshow:function(a){
			this.shumiao(miao);
			loadci++;
			optdt = a.optdt;
			if(loadci==1){
				jm.setJmstr(jm.base64decode(a.showkey));
				admintoken = a.token;
				if(homeobject.showicons)homeobject.showicons(a.menuarr);
			}
			var oi,i,nust;
			//显示桌面项数据
			for(i=0;i<homenums.length;i++){
				nust = homenums[i];
				if(a[''+nust+'arr']){
					if(homeobject['show'+nust+'list'])homeobject['show'+nust+'list'](a[''+nust+'arr']);
					if(homeobject['show_'+nust+'_list'])homeobject['show_'+nust+'_list'](a[''+nust+'arr']);
				}
			}
			if(a.reimstotal=='0')a.reimstotal='';
			$('#reim_stotal').html(a.reimstotal+'');
			try{resizewh();}catch(e){}
			if(a.reimstotal!='' && a.notodo!='1'){
				notifyobj.show({
					icon:'images/todo.png',title:'REIM提醒',rand:'reimto',
					body:'未读REIM消息('+a.reimstotal+')条',
					click:function(){
						openreim();
					}
				});
			}
			if(a.total)for(oi in a.total)this.showtotal(a.total[oi],oi);
			var s=a.msgar[0],s1=a.msgar[1];
			if(s!=''){
				todocontent = s;
				var tx = this.opennewtx(1);
				if(tx=='0'  && a.notodo!='1'){
					$('#tishidivshow').fadeIn();
					$('#tishicontent').html(s);
					notifyobj.showpopup(s1,{icon:'images/todo.png',rand:'systodo',title:'系统提醒',click:function(b){
						opentixiangs();
						return true;
					}});
				}
			}
		},
		showtotal:function(to, sid){
			var o = $('#'+sid+'_{rand}');
			if(!o)return;
			if(to<=0){
				o.hide();
			}else{
				o.show();
				o.html(to);
			}
		},
		opennewtx:function(lx){
			return '0';
		}
	}
	
	js.initbtn(c);
	c.init();

	
	opentixiangs=function(){
		opentixiang();
		hideTishi();
		return false;
	}
	hideTishi=function(){
		$('#tishidivshow').fadeOut();
		return false;
	}

	openmobile=function(){
		js.tanbody('loginmobile','登录手机版', 300,200,{
			html:'<div  style="height:160px;padding:5px" align="center"><div><img id="logeweerew" src="images/logo.png" width="130" height="130"></div><div>直接扫一扫即可登录</div></div>'
		});
		var surl = js.getajaxurl('getqrcode','index','home'),surls = js.getajaxurl('getqrcores','index','home');
		$.get(surls,function(ass){
			if(ass!='ok'){
				$('#logeweerew').parent().html('<div style="padding:10px 20px;text-align:left">未开启gd库，不能生成二维码，<br>可手机浏览器输入地址:<br>'+ass+'</div>');
			}else{
				get('logeweerew').src=surl;
			}
		});
	}
	
	openwangyban=function(){
		window.open('web/login.html?user='+adminuser+'&token='+admintoken+'');
	}
	
	homereload=function(rlx){
		c.gettotal(rlx);
	}
	
	//打开REIM窗口界面
	openreim=function(){
		$('#reim_stotal').html('');
		var ops = js.openrun('reim','winfocus');
		if(!ops){
			js.cliendsend('focus',{},false,function(){
				js.confirm('可能没有使用REIM的PC客户端，是否打开网页版的？',function(jg){
					if(jg=='yes'){
						js.open('?d=reim',260,530,'reim');
					}
				});
				return true;
			});
		}else{
			js.open('?d=reim',260,530,'reim');
		}
	}
	
	notifyobj=new notifyClass({
		title:'系统提醒',
		sound:'web/res/sound/todo.ogg',
		sounderr:'',
		soundbo:true,
		showbool:true
	});
});
</script>