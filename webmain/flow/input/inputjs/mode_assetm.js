//初始函数
function initbodys(){
	$(form('typeid')).change(function(){
		getfilenum();
	});
}

//得到文件编号：类别+年份+三位编号
function getfilenum(){
	var type = form('typeid').value;
	if(type==''){
		return;
	}
	
	js.ajax(geturlact('getfilenum'),{type:type},function(s){
		form('num').value=s;
	},'get');
}
