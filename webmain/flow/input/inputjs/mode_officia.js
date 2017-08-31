//初始函数
function initbodys(){
	$(form('zinum')).change(function(){
		getfilenum();
	});
}

//得到文件编号：类别+年份+三位编号
function getfilenum(){
	var type = form('zinum').value;
	if(type==''){
		form('num').value='';
		return;
	}
	
	js.ajax(geturlact('getfilenum'),{type:type},function(s){
		form('num').value=s;
	},'post');
}
