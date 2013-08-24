/** kj 下 page 对象
 *  
 *
 */

kj.page = new function() {
	this.go = function(val) {
		var arr;
		if( val ) {
			arr= new Array("page=" + val);
		}
		var url = window.location.href;
		url = kj.urlencode(url , arr);
		window.location = url;
	}
	//分页跳转回车
	this.page_keyup = function(e) {
		var e1;
		(document.all) ? e1 = window.event : e1 = e;
		if( e1.keyCode == 13 ) {
			this.go(kj.obj('#id_go_page').value);
		}
	}
	//设置分页大小
	this.size = function(e , key , dir) {
		var e1;
		(document.all) ? e1 = window.event : e1 = e;
		if( e1.keyCode == 13 ) {
			var val = kj.toint(kj.obj("#id_page_size").value);
			if(val < 1) {
				alert("设置分页大小不合法");
				return;
			}
			var domain = kj.cfg("baseurl");
			kj.ajax.get(domain + "/common.php?app=config&app_module=user&dir="+dir+"&app_act=save_pagesize&key=" + key + "&val=" + val , function(data){
				kj.page.go();
			});
		}
	}
}