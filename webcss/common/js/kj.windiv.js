/*
 *
 *
 * 2013-03-24
 *普通div,数据选择框
 */
kj.windiv = function(o){
	if(!('id' in o) || !('src' in o)) return;//未指定id

	var obj = {"id":"windiv"+o.id,"top":0,"left":0,"w":300,"h":300 , "fw":0, "fh":0 , 'src':o.src};
	var objdiv = kj.obj("#"+obj.id);
	if(objdiv) {
		kj.show(objdiv);
		return;
	}
	if('fid' in o) {
		var offset = kj.offset(o.fid);
		obj.top = offset.top;
		obj.left = offset.left;
		obj.fw = kj.w(o.fid);
		obj.fh = kj.h(o.fid);
		if('addleft' in o) obj.left += o.addleft;
		if('addtop' in o) obj.top += o.addtop;
		obj.fid = o.fid;
	}
	('w' in o)? obj.w = o.w : obj.w = kj.w() - obj.left-20;
	('h' in o)? obj.h = o.h : obj.h = kj.h() - obj.top-20;
	if('max_w' in o && obj.w>o.max_w) obj.w = o.max_w;
	if('max_h' in o && obj.h>o.max_h) obj.h = o.max_h;
	kj.windiv.show(obj);
};
kj.windiv.show = function(o) {
	var obj_div=document.createElement("div");
	obj_div.id = o.id;
	obj_div.style.cssText = "position:fixed;top:" + o.top + "px;left:" + o.left + "px;width:" + o.w + "px;height:" + o.h + "px";
	var html = '';
	var w = o.w-o.fw-30;
	var h = o.h-o.fh-30;
	html = '<div style="float:left;width:'+o.w+'px;height:'+o.h+'px;overflow:hidden;" id="'+o.id+'_body"><div style="float:right;border-bottom:1px #888888 solid;width:'+w+'px;height:'+o.fh+'px"></div><div style="float:left;width:'+(o.w-2)+'px;height:'+h+'px;border:1px #888888 solid;background:#ffffff;border-top:0px"><iframe src="'+o.src+'" width="100%" height="100%" frameborder=0></iframe></div></div>';
	obj_div.innerHTML = html;
	document.body.appendChild(obj_div);
	kj.handler("#"+o.id+"_body","mouseout",function() {
		kj.hide(this.parentNode);
	});
	kj.handler("#"+o.id+"_body","mouseover",function() {
		kj.show(this.parentNode);
	});
}

//缓存框
kj.windiv.cache = new function() {
	this.obj = [];
	this.objid = [];
	this.initBody = false;//是否初始化body 的click 事件
	this.create = function(o) {
		this.obj[o.id] = o;
		this.objid[this.objid.length] = o.id;
		var offset = kj.offset('#'+o.id);
		var obj = {};
		obj.top = offset.top;
		obj.left = offset.left;
		obj.w = kj.w('#'+o.id)-2;
		obj.h = kj.h('#'+o.id);
		var obj_div=document.createElement("div");
		obj_div.id = "windiv_cache_" + o.id;
		obj_div.style.cssText = "position:absolute;top:0px;left:0px;display:none";
		var html = '';
		var html_scroll = 'overflow:hidden;';
		if("scroll" in o) html_scroll = "overflow-y:scroll;";
		var html_body = '';
		//mode 模式为1时
		if('datalist' in o) {
			html_body = this.get_html_body(o.id , o.datalist);
		}
		html = '<div style="float:left;width:'+obj.w+'px;border:1px #cccccc solid;background:#ffffff;'+html_scroll+'" id="'+obj_div.id+'_body">'+html_body+'</div>';
		obj_div.innerHTML = html;
		document.body.appendChild(obj_div);
		if(kj.obj("#"+o.id).type == 'text') {
			if('src' in o && 'mode' in o && o.mode == 1) {
				kj.handler("#"+o.id,"change",function() {
					kj.windiv.cache.refresh(this.id , this.value);
				});
			}
			kj.handler("#"+o.id,"focus",function() {
				kj.windiv.cache.show(this.id);
			});
		} else {
			kj.handler("#"+o.id,"click",function() {
				kj.windiv.cache.show(this.id);
			});
		}
		if(this.initBody == false) {
			kj.handler(document.documentElement,"click",function(e){
				var arr = new Array('img' , 'input');
				var target = kj.event_target(e);
				if('id' in target && kj.windiv.cache.objid.indexOf(target.id)>=0 ) return;
				kj.windiv.cache.close();
			});
		}
	}
	this.init = function(o) {
		if(o.id in this.obj) {
			if('datalist' in o) {
				var html_body = this.get_html_body(o.id , o.datalist);
				kj.obj("#windiv_cache_" + o.id + "_body").innerHTML = html_body;
			}
		} else {
			this.create(o);
		}
		if('display' in o && o.display != 'none') this.show(o.id);
	}
	this.get_html_body = function(id , arr) {
		var html_body = '';
		this.obj[id].rows = arr.length;
		for(var i = 0 ; i < arr.length; i++ ) {
			html_body += '<li style="float:left;list-style-type:none;width:100%;height:15px;padding:5px 0px 3px 0px;" onmouseover="this.style.backgroundColor=\'#3399FF\';this.style.color=\'#fff\'" onmouseout="this.style.backgroundColor=\'#fff\';this.style.color=\'#000000\'" onmousedown="kj.windiv.cache.sel(\''+id+'\',\''+arr[i]+'\')">&nbsp;&nbsp;' + arr[i] + '</li>';
		}
		return html_body;
	}
	this.sel = function(id , val) {
		var obj = kj.obj("#"+id);
		if('value' in obj) {
			obj.value = val;
		} else if("innerHTML" in obj) {
			obj.innerHTML = val;
		}
		if( this.obj[id] && 'selfun' in  this.obj[id]) {
			var fun = this.obj[id].selfun;
			fun();
		}
	}
	this.show = function(id) {
		if(!(id in this.obj)) return false;
		//关闭其它
		this.close();
		var obj_div = kj.obj("#windiv_cache_" + id);
		if(!obj_div) return;
		var offset = kj.offset('#'+id);
		var obj_h = kj.h('#'+id);
		var top = offset.top+obj_h;
		var h = this.obj[id].rows * 23;
		var body_h = document.documentElement.clientHeight;
		var isover = false;
		if(top+h>body_h) {
			var h2 = body_h - top;
			if(h2 < 100) {
				var top2 = top - document.documentElement.scrollTop;
				if(h > top2) {isover = true;h = top2-obj_h;}
				top = top - h - obj_h;
			} else {
				h = h2;
				isover = true;
			}
		}
		if(isover) kj.set("#windiv_cache_" + id + "_body" , 'style.overflowY' , 'scroll');
		obj_div.style.top = top+"px";
		obj_div.style.left = offset.left+"px";
		kj.h("#windiv_cache_" + id + "_body" , h);
		obj_div.style.display="";
	}
	this.close = function(id) {
		if( id && id in this.obj) {
			kj.hide("#windiv_cache_" + id);
		} else {
			//关闭所有
			for(var i = 0 ; i < this.objid.length ; i++ ) {
				kj.hide("#windiv_cache_" + this.objid[i]);
			}
		}
	}
	this.refresh = function(id , val , type) {
		if( !(id in this.obj) ) return;
		if(!( 'src' in this.obj[id] ) ) return;
		if(kj.ajax) {
			var arr = ['cacheid='+id];
			if(val) arr[arr.length] = 'cacheval=' + val;
			if(type) arr[arr.length] = 'cachetype=' + type;
			var url = kj.urlencode(this.obj[id].src , arr);
			this.obj[id].src = url;
			kj.ajax.get( url , function(data) {
				var obj_data = kj.json(data);
				if(!obj_data) return;
				var id = obj_data.cacheid;
				var obj_div = kj.obj("#windiv_cache_" + id + "_body");
				obj_div.innerHTML = kj.windiv.cache.get_html_body(id , obj_data.list);
			});
		}
	}
}