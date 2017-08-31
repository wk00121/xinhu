function initbodys(){
	$(form('sealid')).change(function(){
		var val = this.value,txt='';
		if(val!=''){
			txt = this.options[this.selectedIndex].text;
		}
		form('sealname').value=txt;
	});
}