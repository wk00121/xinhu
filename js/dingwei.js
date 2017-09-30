/**
*	定位文件
*/

//jssdk回调过来的
js.jssdkcall  = function(bo){
	js.dw.start();//开始定位
}
js.dw = {
	
	//开始定位
	init:function(isgzh){
		if(isgzh==1){
			js.jssdkwxgzh();
		}else{
			js.jssdkwixin();
		}
	},

	
	dwbool:false,
	dwtimeer:false,
	ondwcall:function(){},
	ondwstart:function(){},
	ondwerr:function(){},
	ondwwait:function(){return false},
	
	start:function(){
		if(this.dwbool)return;
		this.dwbool = true;
		this.chaoshi();
		this.ondwstart(js.jssdkstate);
		if(js.jssdkstate != 1){
			this.htmldingw();
		}else{
			this.wxdingw();
		}
	},
	
	//定位等待
	wait:function(msg){
		var bo = this.ondwwait(msg);
		if(!bo)js.msg('wait',msg);
	},
	
	chaoshi:function(){
		clearTimeout(this.dwtimeer);
		this.dwtimeer = setTimeout(function(){
			var msg = '定位超时，请重新定位';
			js.msg('msg', msg);
			js.dw.ondwerr(msg);
			js.jssdkstate = 2;
			js.dw.dwbool=false;
		},20*1000);
	},
	clearchao:function(){
		clearTimeout(this.dwtimeer);
		this.dwbool = false;
	},
	
	//html5定位
	htmldingw:function(){
		var msg;
		if(appobj1('startLocation','appbacklocation')){
			this.wait('原生app定位中...');
			return;
		}
		
		if(!navigator.geolocation){
			msg = '不支持浏览器定位';
			js.msg('msg',msg);
			this.clearchao();
			js.dw.ondwerr(msg);
		}else{
			this.wait('浏览器定位中...');
			navigator.geolocation.getCurrentPosition(this.showPosition,this.showError,{
				enableHighAccuracy: true,
				timeout: 19000,
				maximumAge: 3000
			});
		}
	},
	
	
	//微信定位
	wxdingw:function(){
		var msg = '微信定位中...';
		if(js.isqywx)msg='企业微信定位中...';
		this.wait(msg);
		wx.getLocation({
			type: 'gcj02',
			success: function (res){
				js.dw.dwsuccess(res);
			},
			error:function(){
				js.jssdkstate = 2;
				js.dw.dwbool=false;
				js.dw.start(); 
			}
		});
	},
	
	dwsuccess:function(res){
		this.wait('定位成功，获取位置信息...');
		this.clearchao();
		var lat 	= parseFloat(res.latitude); // 纬度，浮点数，范围为90 ~ -90
        var lng 	= parseFloat(res.longitude); // 经度，浮点数，范围为180 ~ -180。
        var jid 	= parseFloat(res.accuracy); // 位置精度
		this.geocoder(lat,lng, jid);
	},
		
	showError:function (error){
		js.dw.clearchao();
		js.setmsg('');
		var msg='无法定位';
		switch(error.code){
		case error.PERMISSION_DENIED:
			msg="用户拒绝对获取地理位置的请求。"
			break;
		case error.POSITION_UNAVAILABLE:
			msg="位置信息是不可用的。"
			break;
		case error.TIMEOUT:
			msg="请求用户地理位置超时。"
			break;
		case error.UNKNOWN_ERROR:
			msg="未知错误。"
			break;
		}
		var url = 'http://www.rockoa.com/view_wxgzh.html';
		js.wx.alert('无法定位？请看<a href="'+url+'">[帮助设置]</a>');
		js.msg('msg', msg);
		js.dw.ondwerr(msg);
	},
	
	showPosition:function(position){
		var res 		= position.coords;
		var latitude 	= res.latitude;
		var longitude 	= res.longitude;
		var accuracy 	= parseFloat(res.accuracy);
		var center 		= new qq.maps.LatLng(parseFloat(latitude), parseFloat(longitude));
		js.dw.translate(center, accuracy);
	},
	
	//坐标转化
	translate:function(center, accuracy){
		qq.maps.convertor.translate(center,1,function(res){
			var latitude 	= res[0].lat;
			var longitude 	= res[0].lng;
			js.dw.dwsuccess({
				latitude:latitude,
				longitude:longitude,
				accuracy:accuracy
			});
		});	
	},
	
	//搜索位置
	geocoder:function(lat,lng, jid){
		if(!this.geocoderObj)this.geocoderObj 	= new qq.maps.Geocoder();
		var center 	= new qq.maps.LatLng(lat, lng);
		this.geocoderObj.getAddress(center);
		this.geocoderObj.setComplete(function(result){
			var address = result.detail.address;
			var dzarr 	= result.detail.addressComponents;
			//address 	= ''+dzarr.province+''+dzarr.city+''+dzarr.district+''+dzarr.street+'';
			//if(dzarr.streetnumber)address+=dzarr.streetnumber;
			
			//范围内地址
			var near = result.detail.nearPois,dist = 500,naddress,addressinfo;
			for(var i=0;i<near.length;i++){
				if(near[i].dist<dist){
					dist 	 = near[i].dist;
					naddress = ''+near[i].name+'('+near[i].address+')';
				}
			}
			if(dist<500)address = naddress;
			addressinfo = ''+address;
			if(jid>0)addressinfo+='(精确'+jid+'米)';
			js.msg();
			js.dw.ondwcall({
				latitude:lat,
				longitude:lng,
				accuracy:jid,
				address:address,
				addressinfo:addressinfo,
				detail:result.detail,
				center:result.detail.location
			});
		});
		
		this.geocoderObj.setError(function() {
			var msg = '无法获取位置';
			js.msg('msg', msg);
			js.dw.ondwerr(msg);
		});
	}
};

//原生app定位中
appbacklocation=function(res){
	var latitude 	= res.latitude;
	var longitude 	= res.longitude;
	var accuracy 	= parseFloat(res.accuracy);
	js.dw.dwsuccess({
		latitude:latitude,
		longitude:longitude,
		accuracy:accuracy
	});
}