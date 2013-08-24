var jsshop = new function() {
	this.mintotal = 0;//最低起送价
	this.total = 0;//合计金额
	this.cart_show = 0;
	this.cart_lock = false;
	this.cart_add = function(o) {
		var obj = kj.obj("#id_cart_box");
		var obj_cart_num = kj.obj("#id_cart_num_" + o.id);
		if(obj_cart_num) {
			var obj_cart_price = kj.obj("#id_cart_price_" + o.id);
			obj_cart_num.value = kj.toint(obj_cart_num.value) + 1;
			obj_cart_price.innerHTML = "￥"+kj.toint(obj_cart_num.value) * o.price;
		} else {
			var obj_li=document.createElement("li");
			obj_li.id = "id_cart_" + o.id;
			obj_li.innerHTML = '<input type="hidden" name="cartid[]" value="'+o.id+'"><input type="hidden" name="price[]" id="id_price_'+o.id+'" value="'+o.price+'"><span class="col1">'+o.name+'</span><span class="col2">￥'+kj.toint(o.price)+'</span><span class="col3"><input type="button" name="btn_jian" value="" class="btn_jian" onclick="jsshop.change_num('+o.id+',-1);"> <input type="text" name="num'+o.id+'[]" value="1" id="id_cart_num_'+o.id+'" class="x_num" onkeyup="jsshop.change_num('+o.id+')"> <input type="button" name="btn_jian" value="" class="btn_jia" onclick="jsshop.change_num('+o.id+',1);"></span><span class="col4" id="id_cart_price_'+o.id+'">￥'+kj.toint(o.price)+'</span><span class="col5"><input type="button" name="btn_del" value="" class="x_del" onclick="jsshop.del('+o.id+')"></span>';
			obj.appendChild(obj_li);
		}
		this.refresh_price();
	}
	//删除
	this.del = function(id) {
		kj.remove("#id_cart_"+id);
		this.refresh_price();
	}
	//改变数量
	this.change_num = function(id , num) {
		var obj_cart_num = kj.obj("#id_cart_num_" + id);
		val = kj.toint(obj_cart_num.value);
		if(num) {
			val+=num;
			if(val<1) return;
			obj_cart_num.value = val;
		}
		if(obj_cart_num) {
			var obj_cart_price = kj.obj("#id_cart_price_" + id);
			var obj_price = kj.obj("#id_price_"+id);
			if(obj_cart_price) obj_cart_price.innerHTML = "￥"+val * kj.toint(obj_price.value);
			this.refresh_price();
		}
	}
	//刷新价格
	this.refresh_price = function() {
		var obj = kj.obj("#id_cart_box .col4");
		var price = 0;
		for(var i = obj.length-1 ; i >=0 ; i--) {
			price += kj.toint(obj[i].innerHTML);
		}
		kj.set("#id_cart_menu .x_2" , 'innerHTML' , '共 <font style="font-size:14px;color:#FF821E">'+obj.length+'</font> 份，合计：');
		kj.set("#id_cart_menu .x_3" , 'innerHTML' , '￥'+price);
		this.total = price;
		if(price == 0) {
			this.showcart(0);
		} else {
			this.showcart(1);
		}
		return price;
	}
	//清空
	this.clear = function() {
		var obj = kj.obj("#id_cart_box");
		obj.innerHTML = '';
		this.refresh_price();
	}
	//提交，保存到cookie
	this.cart_submit = function() {
		var obj = kj.obj("#id_cart_box li");
		//检查是否已点餐
		if(obj.length<1) {
			alert("温馨提示：您的购物车是空的，请先点餐！");
			return false;
		}
		//点餐价格是否达到起送价
		if(this.mintotal>0 && this.total < this.mintotal) {
			alert("温馨提示：由于人力成本等问题，外卖定餐需起送不得低于"+this.mintotal+"元，不便之处还请您多多包涵！");
			return false;
		}
		this.save_cookie();
		window.location.href = "?app_act=cart";

	}
	this.save_cookie = function() {
		var i,val,j,arr_1=[];
		obj = kj.obj("#id_cart_box :cartid[]");
		for(i = 0 ; i < obj.length ; i++ ) {
			val = kj.toint(kj.obj("#id_cart_num_"+obj[i].value).value);
			for(j=0;j<val;j++) {
				arr_1[arr_1.length] = obj[i].value;
			}
		}
		var  str_ids = "0:" + arr_1.join("|");
		kj.cookie_set("cart_ids" , str_ids , 24);
	}

	this.mouseover = function(id) {
		var obj = kj.obj("#id_nosel_"+this.orderid);
		if(obj) obj.className = 'x_nosel';
		kj.obj("#id_nosel_"+id).className = 'x_sel1';
		this.orderid = id;
		kj.addClassName("#id_li_"+id , "x_sel2");
	}
	this.mouseout = function(id) {
		var obj = kj.obj("#id_nosel_"+this.orderid);
		if(obj) obj.className = 'x_nosel';
		kj.delClassName("#id_li_"+id , "x_sel2");
	}
	this.showcart_fixed = function(obj) {
		if(obj.className == 'x_4') {
			this.showcart(1);
		} else {
			this.showcart(0);
		}
	}
	this.showcart = function(show) {
		if(this.cart_lock) return;
		if(this.cart_show == show) return;
		this.cart_show = show;
		var h = document.documentElement.clientHeight - 32;
		var obj = kj.obj("#id_cart_menu");
		var offset = kj.offset(obj);
		var top = offset.top;
		this.cart_lock = true;
		if(show) {
			kj.set("#id_cart_menu .x_top .x_4" , 'className' , 'x_6');
			this.showcart_time('#id_cart_menu' , top , h-120 , -10);
			this.showcart_time('#cart_menu_opacity_bg' , top , h-127 , -11);
		} else {
			kj.set("#id_cart_menu .x_top .x_6" , 'className' , 'x_4');
			this.showcart_time('#id_cart_menu' , top , h-22 , 10);
			this.showcart_time('#cart_menu_opacity_bg' , top , h-33 , 9);
		}
	}
	this.showcart_time = function(id , top , top_target , val) {
		var obj = kj.obj(id);
		var x = top -  top_target;
		if(val > 0) x = x * -1;
		if( x > 0 ) {
			kj.set(obj, 'style.top' , top+'px');
			top+=val;
			window.setTimeout("jsshop.showcart_time('" + id + "'," + top + "," + top_target + "," + val + ")", 20);   
		} else {
			kj.set(obj, 'style.top' , top_target+'px');
			this.cart_lock = false;
		}
	}
	this.menuInfo = "";
	this.menumouseover = function(o) {
		if(!o) return;
		this.menuInfo = o.getElementsByTagName('a')[0].innerHTML;
		o.getElementsByTagName('a')[0].innerHTML = "来一份";	
	}
	this.menumouseout = function(o){
		if(!o) return;
		o.getElementsByTagName('a')[0].innerHTML = this.menuInfo;
	}
	this.resize = function(){
		var h = document.documentElement.clientHeight;
		var offset = kj.offset("#id_left");
		kj.set("#id_cart_menu" , 'style.top' , (h-55) + "px");
		kj.set("#cart_menu_opacity_bg" , 'style.top' , (h-65) + "px");
		var left = document.documentElement.scrollLeft || document.body.scrollLeft;
		kj.set("#id_cart_menu" , 'style.left' , (offset.left-left) + "px");
		//定位label
		kj.set("#labelright",'style.top',(h/2)+'px');
		kj.set("#id_cart_menu .x_top .x_6" , 'className' , 'x_4');
		this.cart_show = 0;
		this.label_position();
	}
	this.label_position = function() {
		var offset = kj.offset("#id_right");
		var left = document.documentElement.scrollLeft || document.body.scrollLeft;
		left = (offset.left+kj.w('#id_right'))-left;
		kj.set("#labelright" , "style.left" , left+"px");
		var t = document.documentElement.scrollTop || document.body.scrollTop;
		if(t>300) {
			kj.set("#labelright .up",'style.visibility' , '');
		} else {
			kj.set("#labelright .up",'style.visibility' , 'hidden');
		}
		if(kj.agent(true) == 'MSIE6.0') {
			t += 300;
			kj.set("#labelright" , "style.top" , t+"px");
		}
	}
	this.top = function() {
		if(document.documentElement.scrollTop) {
			document.documentElement.scrollTop = '0px';
		} else {
			document.body.scrollTop = '0px';
		}
	}
	this.menugroup = function(o) {
		if(!o) return;
		kj.show('#menugroup');
		var offset = kj.offset(o);
		var left = offset.left-kj.w('#menugroup');
		var top = offset.top+kj.h(o)-kj.h('#menugroup');
		kj.set('#menugroup' , 'style.left' , left+'px');
		kj.set('#menugroup' , 'style.top' , top+'px');
	}
	this.hash = function (name) {
		var obj = kj.obj(":"+name);
		if(obj.length<1) {
			var arr = name.split("_");
			this.hashname = name;
			kj.ajax.get("?app_act=grouplist&index_group="+arr[1],function(data){
				kj.obj("#id_grouplist").innerHTML = data;
				window.location.hash=jsshop.hashname;
				if(jsfooter) jsfooter.align_height();
			});
		} else {
			window.location.hash=name;
		}
	}
	this.sort = function (name , val) {
		this.hashname = 'hash_sort';
		var val = (!val)? '' : '&sortval='+val;
		kj.ajax.get("?app_act=sortlist&sort="+name+val,function(data){
			kj.obj("#id_grouplist").innerHTML = data;
			window.location.hash=jsshop.hashname;
			if(jsfooter) jsfooter.align_height();
		});
	}
	this.comment = function(menu_id) {
		kj.dialog({id:'comment' + menu_id,title:'查看评论',url:'?app_act=comment&menu_id='+menu_id,w:500,'max_h':650,showbtnhide:false,showbtnmax:false,type:'iframe'});
	}
}
kj.onresize(function() {
	jsshop.resize();
	if(kj.agent(true) == 'MSIE6.0') {
		kj.set("#labelright",'style.position','absolute');
	}
});
kj.onload(function() {
	jsshop.resize();
	kj.show("#id_cart_menu");
	kj.show("#labelright");
});
window.onscroll = function(){ 
	jsshop.resize();
}
window.onbeforeunload = function() {
	jsshop.save_cookie();
}
