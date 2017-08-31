//流程模块【finpay.付款申请】下录入页面自定义js页面,初始函数
function initbodys(){
	
}
function changesubmit(){
	var jg = parseFloat(form('money').value);
	if(jg<=0)return '付款金额不能小于0';
}