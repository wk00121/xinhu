//1、当页面加载完成会调用函数 initbodys()，里面可以写初始信息，绑定事件等。
function initbodys(){
	
	
	//当元素类型是[弹框下拉选择]时
	c.onselectdata['tanxuan']=function(d){
		console.log(d);
		js.msg('success','选中的数据：'+JSON.stringify(d)+'');
	}
	
	//弹出多选触发
	c.onselectdata['tanxuancheck']=function(d){
		console.log(d);
		js.msg('success','选中的数据：'+JSON.stringify(d)+'');
	}
	
	//绑定省用来联动
	$(form('sheng')).change(function(){
		form('shi').length=1;//清空市下拉框的数据
		form('xian').length=1;//清空县下拉框的数据
		var val = this.value;
		if(val=='')return;
		
		//ajax获取对应城市数据，在webmain/flow/input/mode_demoAction.php 下方法getcityAjax 查找数据库返回
		js.ajax(geturlact('getcity'),{'sheng':val},function(ret){
			//得到数据填充到下拉框
			js.msg('success','城市数据：'+JSON.stringify(ret)+'');
			js.setselectdata(form('shi'),ret,'name');
		},'post,json');
	});
	
	//绑定市用来联动
	$(form('shi')).change(function(){
		form('xian').length=1;//清空县下拉框的数据
		var val = this.value;
		if(val=='')return;
		
		//ajax获取对应县数据，在webmain/flow/input/mode_demoAction.php 下方法getxianAjax 查找数据库返回
		js.ajax(geturlact('getxian'),{'city':val},function(ret){
			
			//得到数据填充到下拉框
			js.msg('success','县(区)数据：'+JSON.stringify(ret)+'');
			js.setselectdata(form('xian'),ret,'name');
		},'post,json');
	});
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
	//if(!d.name)return '名称不能为空';
};

//4、保存提交成功触发
function savesuccess(){
	alert('保存成功，我在页面上自己写的');
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