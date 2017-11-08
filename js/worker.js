/**
*	异步url使用
*/

//接收发送过来的数据，一般是运行URl
onmessage =function (evt){
	var url = evt.data;
	gettourl(url);
}

//用Ajax访问数据
function gettourl(s){
	var xmlhttp =new XMLHttpRequest();
	xmlhttp.open("GET",s,true);
	xmlhttp.send();
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4){
			var bdat = {'status':xmlhttp.status,'content':xmlhttp.responseText};
			postMessage(bdat)
		}
	}
}

/**
*	异步队列
*/
var queue = {
	addqueuearr:[],
	yunoi:0,
	maxxu:10, //每次最多运行进程
	start:function(){
		setInterval('queue.starts()', 1000);
	},
	starts:function(){
		var i,len = this.addqueuearr.length,oi=0;
		for(i=this.yunoi;i<len;i++){
			if(oi>=this.maxxu)break;
			oi++;
			this.runqueue(i);
		}
		this.yunoi += oi; 
	},
	//添加返回对应序号
	add:function(cans){
		if(typeof(Worker)!='function'){
			js.msg('msg','当前浏览器不支持，请换Firefox或chrome的');
			return -1;
		}
		if(!cans)cans={};
		var url = cans.url;
		if(!url)return -1;
		if(url.substr(0,4)!='http')url=NOWURL+url;
		cans.url = url;
		this.addqueuearr.push(cans);
		return this.addqueuearr.length-1;
	},
	//运行
	runqueue:function(i){
		var da = this.addqueuearr[i];
		if(!da)return;
		var wker =new Worker('js/worker.js');
		wker.postMessage(da.url);//只能发送字符串?
		wker.onmessage=function(evt){
			var barr = evt.data;
			var str  = barr.content;
			if(barr.status==200){
				if(da.type=='json')str = js.decode(str);
				if(da.success)da.success(str, da, i);
			}else{
				if(da.error)da.error(str, da, i);
			}
			queue.addqueuearr[i]=false;
		};
	}
}