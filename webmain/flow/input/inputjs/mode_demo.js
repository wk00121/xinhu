//1、当页面加载完成会调用函数 initbodys()，里面可以写初始信息，绑定事件等。
function initbodys(){
	$(form('custid')).change(function(){
		//绑定下拉框custid的触发事件
		changedata();
	});
	$(form('custname')).blue(function(){
		//绑定文本框custname失去焦点事件
		changedata();
	});
	
	//当元素类型是[弹框下拉选择]时，绑定选择触发事件用如下方法绑定custname选择文本框的触发
	c.onselectdata['custname']=function(d){
		js.getarr(d);
	}
}
/**
*	常用的方法
*	1、geturlact('abcfangfa');参数方法名 获取访问url，访问方法写在webmain/flow/input/mode_模块编号Action.php 下 abcfangfaAjax方法
*/

//2、异步加载数据，demo
function changedata(){
	
	var gtype = 'get'; //为时get请求得到数据字符串，为：get,json返回josn对象
	js.ajax(geturlact('initdatas'),{'参数1':'参数值'},function(ret){
		alert(ret);
		//赋值
	},gtype);
}

//3、提交保存时触发事件，常用于判断数据是否完整性
function changesubmit(d){
	if(!d.name)return '名称不能为空';
};

//4、保存提交成功触发
function savesuccess(){
	
};


//下拉框联动例子
function liandong(){
	js.ajax(geturlact('initdatas'),{'参数1':'参数值'},function(ret){
		//返回ret数据是个json数组，格式[{name:'',value:''},{...}]
		var o = form('下拉框名称');
		o.length = 1;
		js.setselectdata(o,ret,'value'); //给下拉框设置数据源
	},'get,json');
}