function initbodys(){
	
	
}


//触发事件
function oninputblur(na,zb,obj){
	if(zb==0)return;
	if(na=='temp_aid')changeaidtssk(obj);
}

function changeaidtssk(o1){
	var nam = c.getxuandoi(o1.name);
	var val = form('aid'+nam[2]+'').value;
	js.ajax(geturlact('getgoods'),{aid:val},function(d){
		c.setrowdata(nam[0],nam[1],d);
		c.inputblur(form('money'), 0);
	},'get,json');
}