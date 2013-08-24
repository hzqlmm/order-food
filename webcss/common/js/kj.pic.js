/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 * 增加 pic 相关功能
 */
kj.pic = new function(){
	this.preview = function(pic , intro) {

		var s_top = document.body.scrollTop;
		if(!s_top) s_top = document.documentElement.scrollTop;
		var obj_div;
		var w = kj.w();
		var h = kj.h();
		var id = "id_pic_preview";
		if(s_top==0) {
			id = "id_pic_preview_box";
			kj.bglayer();
			obj_div = document.createElement("div");
			obj_div.id="id_pic_preview_box";
			obj_div.style.cssText="position:absolute;overflow:scroll;width:" + w + "px;height:" + h + "px;top:0px;left:0px;z-index:101;";
			document.body.appendChild(obj_div);
			kj.handler("#id_pic_preview_box","click",function(){
				kj.remove("#id_pic_preview_box");
				kj.remove("#id_bglayer");
			});
		} else {
			kj.bglayer(id);
		}
		obj_div=document.createElement("div");
		w = kj.w()-100;
		obj_div.id="id_pic_preview";
		obj_div.style.cssText="position:absolute;z-index:101";
		var html = '<div style="float:left;border:1px #000000 solid;padding:3px"><img src="' + pic + '" id="id_pic_preview_img" style="max-width:' + w + 'px;_zoom:expression(function(x){ if(x.width>' + w + '){x.width=' + w + ';}}(this));vertical-align:middle" onclick="kj.remove(\'#'+id+'\');kj.remove(\'#id_bglayer\');"></div>';
		obj_div.innerHTML = html;
		if(s_top==0) {
			var obj = kj.obj("#id_pic_preview_box");
			obj.appendChild(obj_div);
		} else {
			document.body.appendChild(obj_div);
		}
		if(intro) {
			w = kj.w("#id_pic_preview")-20;
			html += "<div style='float:left;line-height:25px;text-align:left;width:"+w+"px;background:#ffffff;clear:both;word-break:break-all;padding:10px;'>" + intro + "</div>";
			kj.obj("#id_pic_preview").innerHTML = html;
		}

		this.show("#id_pic_preview");
	}
	this.show = function(obj , msg_top , msg_left) {
		obj  = kj.obj(obj);
		lng_top = kj.top(obj , msg_top);
		lng_left = kj.left(obj , msg_left);
		int_height=document.body.scrollTop;
		if(!int_height)int_height = document.documentElement.scrollTop;
		lng_top = lng_top + int_height;
		lng_top = lng_top+"px";
		lng_left = lng_left+"px";
		obj.style.top = lng_top;
		obj.style.left = lng_left;
	}
};