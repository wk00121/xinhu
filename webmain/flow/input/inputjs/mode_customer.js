//初始函数
function initbodys(){
	
	c.onselectdata['sheng'] = function(d){
		if(!d.shengname)return;
		if(form('sheng'))form('sheng').value = d.shengname;
		if(form('shi'))form('shi').value = d.cityname;
	}
}