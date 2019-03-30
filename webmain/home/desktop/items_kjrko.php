<?php 
/**
*	桌面首页项(快捷入口)
*/
defined('HOST') or die ('not access');

?>
<script>
homeobject.showicons=function(a){
	a.push({name:'刷新统计中...',icons:'refresh',num:'refresh',color:'#888888'});
	this.menuarr = a;
	var o = $('.homelishow'),s='';
	o.html('');
	for(var i=0; i<a.length;i++){
		s = '<li>';
		s+= '<div onclick="opentabsshowshwo('+i+')" class="homeiconss">';
		s+=	'<div class="div00"><span id="'+a[i].num+'_{rand}" style="display:none" class="badge red"></span></div>';
		s+=	'	<div style="background-color:'+a[i].color+'" class="homeiconss2">';
		s+=	'		<div class="div01"><i class="icon-'+a[i].icons+'"></i></div>';
		s+=	'		<div id="'+a[i].num+'_text">'+a[i].name+'<div>';
		s+=	'	</div>';
		s+=	'</div>';
		s+=	'</li>';
		o.append(s);
	}
}
opentabsshowshwo=function(oi,o1){
	var a = homeobject.menuarr[oi];
	if(a.num=='refresh'){
		homeobject.refresh();
	}else{
		$('#'+a.num+'_{rand}').remove();
		var anum = {num:a.num,url:a.url,name:a.name,icons:a.icons,id:a.id};
		addtabs(anum);
	}
	return false;
}
</script>
<style>
.homelishow{display:inline-block}
.homelishow li{float:left;text-align:center;}
.divlisssa li{float:left;padding:8px 0px;text-align:left;width:33%}
.divlisssa ul,.divlisssa{display:inline-block;width:100%}
</style>

<div align="left">
	<ul class="homelishow">
		<li>
			<div click="refresh" class="homeiconss">
				<div style="background-color:#888888" class="homeiconss2">
					<div class="div01"><i class="icon-refresh"></i></div>
					<div id="refresh_text">刷新中</div>
				</div>	
			</div>	
		</li>
	</ul>
</div>
<div class="blank1" style="margin:10px 0px"></div>
<div class="blank10"></div>