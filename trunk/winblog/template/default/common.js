function id(i){return document.getElementById(i)}
function register_submit(){
	if(id("username").value.length < 3){alert("用户名长度务必在3个字符以上。");return false;}
	if(id("username").value.length > 15){alert("用户名长度务必在15个字符以下。");return false;}
	if(id("nickname").value.length < 3){alert("昵称长度务必在3个字符以上。");return false;}
	if(id("nickname").value.length > 15){alert("昵称长度务必在15个字符以下。");return false;}
	if(id("password").value.length < 6){alert("密码长度务必在6个字符以上。");return false;}
	if(id("password").value.length > 30){alert("密码长度务必在30个字符以下。");return false;}
	if(id("email").value.length < 6){alert("电子邮件长度务必在6个字符以上。");return false;}
	if(id("email").value.length > 30){alert("电子邮件长度务必在30个字符以下。");return false;}
	if(id("repassword").value != id("password").value){alert("两次输入的密码不一致。");return false;}
	return true;
}
function chgpw_submit(){
	if(id("oldpw").value.length < 6){alert("密码长度务必在6个字符以上。");return false;}
	if(id("oldpw").value.length > 30){alert("密码长度务必在30个字符以下。");return false;}
	if(id("newpw").value.length < 6){alert("密码长度务必在6个字符以上。");return false;}
	if(id("newpw").value.length > 30){alert("密码长度务必在30个字符以下。");return false;}
	if(id("newpw").value != id("compw").value){alert("两次输入的密码不一致。");return false;}
	return true;
}
function forgetcheck_submit(){
	if(id("username").value.length < 3){alert("用户名长度务必在3个字符以上。");return false;}
	if(id("username").value.length > 15){alert("用户名长度务必在15个字符以下。");return false;}
	if(id("password").value.length < 6){alert("密码长度务必在6个字符以上。");return false;}
	if(id("password").value.length > 30){alert("密码长度务必在30个字符以下。");return false;}
	if(id("password").value != id("compassword").value){alert("两次输入的密码不一致。");return false;}
	return true;
}
function info_submit(){
	if("" == id("selProvince").value || "" == id("selCity").value){alert("请选择所在城市！");return false;}
	return true;
}
function post_submit(){
	id('wininput').value = id('wininput').value.replace(/ |　|\r\n|\n/ig,'');
	if(id('wininput').value.length < 1){alert("请输入微博内容！");id('wininput').focus();return false;}
	return true;
}
function forgetpw_submit(){
	if(id("username").value.length < 3){alert("用户名长度务必在3个字符以上。");return false;}
	if(id("username").value.length > 15){alert("用户名长度务必在15个字符以下。");return false;}
	if(id("email").value.length < 6){alert("电子邮件长度务必在6个字符以上。");return false;}
	if(id("email").value.length > 30){alert("电子邮件长度务必在30个字符以下。");return false;}
	return true;
}
function check_len(){
	var obj=id("wininput");
	var maxLen=parseInt(obj.getAttribute('maxlength'));
	var len=obj.value.replace(/[^\x00-\xff]/g,'oo').length; 
	var llen=maxLen-len;
	if(len>maxLen) {
		var i=0; 
		for(var z=0;z<len;z++) {
			if(obj.value.charCodeAt(z)>255) {i=i+2;}else {i=i+1;} 
			if(i>=maxLen) {obj.value=obj.value.slice(0,(z + 1)); break; } 
		} 
	} 
	if(llen<0)llen=0;
	id("leftlen").innerHTML=llen;
}
function showform(formtype, wid, wcontent){
	id('formtype').value=formtype;
	id('wid').value=wid;
	id('wcontent').innerHTML = wcontent + '<u style="text-decoration:none;padding-left:10px;"><a href="javascript:void(0);" onclick="hideform();" style="color:#AAA">x</a></u>';
	id('postform').style.display="block";
	id('wininput').focus();
}
function hideform(){
	id('postform').style.display="none";
}
function comdel(url){
	if(window.confirm("确定删除吗?")){
		window.location.href = url;
	}
}
