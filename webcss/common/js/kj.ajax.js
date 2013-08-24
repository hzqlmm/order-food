/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 * 增加 ajax 相关功能
 */
kj.ajax = function(url , data , fun) {
	if( typeof data == 'function' ){
		return this.ajax.get(url,data);
	} else {
		return this.ajax.post(url , data , fun);
	}
}
kj.ajax.num=0;
kj.ajax.fun = new Array();
kj.ajax.get = function(url , fun) {
	var xmlhttp_request;
	var lng_index;
	kj.ajax.num++;
	lng_index = kj.ajax.num;
	kj.ajax.fun[lng_index] = fun;
	xmlhttp_request = kj.ajax.get_request_obj(); 
	xmlhttp_request.onreadystatechange = function(){
		kj.ajax.handle_response(xmlhttp_request,lng_index);
	};
	url = kj.urlencode(url , ['app_ajax=1']);
	xmlhttp_request.open('GET' , url , true);
	xmlhttp_request.setRequestHeader("If-Modified-Since","0");
	xmlhttp_request.send(null);
}
kj.ajax.post = function(key , data , fun ) {
	var xmlhttp_request;
	var lng_index;
	kj.ajax.num++;
	lng_index = kj.ajax.num;
	if(typeof fun == "function") {
		kj.ajax.fun[lng_index] = fun;
	} else {
		kj.ajax.fun[lng_index] = data;
	}
	if(typeof key == 'object') {
		msgtype=key.enctype;
		msginfo=kj.form_to_url(key);
		msgact=key.action;
	} else {
		msgtype = "application/x-www-form-urlencoded";
		var arr = new Array();
		if( typeof data == "object" ) {
			for(i in data){
				arr[arr.length] = i + '=' + encodeURIComponent(data[i]);
			}
		}
		msginfo = arr.join("&");
		msgact = key;
	}
	//"application/x-www-form-urlencoded"
	if(msgtype == '') msgtype = 'application/x-www-form-urlencoded';
	xmlhttp_request = kj.ajax.get_request_obj(); 
	xmlhttp_request.onreadystatechange = function(){
		kj.ajax.handle_response(xmlhttp_request,lng_index);
	};
	msgact = kj.urlencode(msgact , ['app_ajax=1']);
	xmlhttp_request.open('POST',msgact,true);
	xmlhttp_request.setRequestHeader("Content-Length", msginfo.length);
	xmlhttp_request.setRequestHeader('Content-Type',   msgtype); 
	xmlhttp_request.send(msginfo);
}

kj.ajax.get_request_obj = function() {
	if (window.ActiveXObject) {
		return (new ActiveXObject("Microsoft.XMLHTTP"));
	} else {
		if (window.XMLHttpRequest) {
			return (new XMLHttpRequest());
		} else {
			return (null);
		}
	}
}

kj.ajax.handle_response = function(xmlhttp,key) {

	if (xmlhttp.readyState == 4)// 收到完整的服务器响应 
	{
		if (xmlhttp.status == 200)
		{   //HTTP服务器响应的值OK
			returnval=xmlhttp.responseText;
			//将服务器返回的字符串写到页面中ID为message的区域 
			str_fun=kj.ajax.fun[key];
			eval(str_fun(returnval));
		}
	}

}