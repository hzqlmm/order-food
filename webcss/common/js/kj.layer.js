/*
 *
 *
 * 2013-03-24
 */
kj.layer = new function(){
	/* 层自动增加
	 * id : #id (注意需要带#号)
	 * msg_w : 子项缩进单位宽度
	 */
	this.split_tag = "li"; //默认分隔 html层标识
	this.add = function(id,msg_w) {
		var obj_default = kj.obj("#id_layer_default");
		if(!obj_default) return;
		var obj_li = document.createElement(this.split_tag);
		var str_id = "id_layer_" + Math.random();
		str_id = str_id.replace(/[.]/g,'');
		var str_html = obj_default.innerHTML;
		str_html = str_html.replace(/id_layer_default/g,str_id);
		obj_li.innerHTML = str_html;
		obj_li.style.display = "";
		obj_li.id = str_id;

		if(id) {
			var arr = kj.obj(".padding_1 input",id);
			var obj_padding = arr[0];
			var lng_lay = kj.toint(obj_padding.value);
			var obj_before;
			var obj_after;
			//计算当前层同层最后一个对象
			arr = kj.obj(":pid[]",id);
			if('length' in arr && arr.length>0) {
				val = arr[0].value;
				arr = kj.obj(":pid[]<<value,/^"+val+"$/i");
				arr = kj.parent(arr , this.split_tag);
				var index=kj.index(arr,id);
				if(index>=0 && index < arr.length) {
					if(arr.length>index+1) {
						obj_before = arr[index+1];
					} else {
						obj_after = this.get_after_layer(id);
					}
				}
			}
			//在同一层次最后元素添加新对象
			if(obj_before) {
				kj.insert_before("#id_layer" , obj_before , obj_li);
			} else {
				if(!obj_after) obj_after = id;
				kj.insert_after("#id_layer" , obj_after , obj_li);
			}

			arr = kj.obj(".padding_1 input","#"+str_id);
			obj_padding = arr[0];
			kj.w(obj_padding.parentNode,lng_lay * msg_w);
			obj_padding.value=lng_lay+1;
			arr = kj.obj("input<<name,/^pid/i","#"+str_id);
			obj_pid = arr[0];
			obj_pid.value=id.substring(1);
		} else {
			kj.obj("#id_layer").appendChild(obj_li);
		}
	}
	//循环取得当前层最后位置
	this.get_after_layer = function(o) {
		o = kj.obj(o);
		var arr = kj.parent(":pid[]<<value,/^"+o.id+"$/i" , this.split_tag);
		if(arr.length>0) {
			var obj_after = arr[arr.length-1];
			return this.get_after_layer(obj_after);
		} else {
			return o;
		}
	}
	//删除节点
	this.remove = function(id) {
		var o = kj.obj("#" + id);
		if(!o) return;
		var arr = kj.obj(":pid[]<<value,/^"+id+"$/i");
		var obj_x;
		if('length' in arr && arr.length>0){
			for(var i = 0 ; i < arr.length ; i++){
				obj_x = kj.parent(arr[i],this.split_tag);
				if('id' in obj_x) kj.layer.remove(obj_x.id);
			}
		}
		kj.remove(o);
	}
}