var jsshop = new function() {
	//默认加载米饭
	this.rice = false;
	this.cart_show = 0;
	this.cart_lock = false;
	this.cart = [];
	this.index = -1;
	this.cart_add = function(o) {
		var ii , i ;
		if(this.cart.length < 1) {
			(o.type != 6) ? this.cart_new(true) : this.cart_new(false);
		}
		var index = this.cart[this.index].length;
		//规则检测
		if(index >= this.cfg.maxnum) {
			alert("温馨提示：为了您的健康，我们建议您单人用餐 最好不要超过" + this.cfg.maxnum + "种！");
			return;
		}
		if(o.type == 3) {
			ii = 0;
			for(i = 0; i < index ; i++) {
				if(this.cart[this.index][i].type == 3) ii++;
			}
			if(ii >= this.cfg.maxcainum) {
				alert("温馨提示：为了您的健康，我们建议您用餐 菜品 最好控制在" + this.cfg.maxcainum + "种以内！");
				return;
			}
		}
		//检查如果是套餐,并且此次已经点有餐时，则自动增加一人用餐
		if(index>0 && (o.type==6 || this.cart[this.index][0].type==6)) {
			if(!this.cart_new(false)) return false;
			index = 0;
		}
		this.cart[this.index][index] = o;
		var objbox = kj.obj("#id_cart_box .x_picli");
		//添加选中菜品缩略图
		var html = '';
		if( o.type==6) {
			html = '<span style="background:url'+'(' + o.pic + ') no-repeat;" onmouseover="kj.show(kj.obj(\'img\',this));"  onmouseout="kj.hide(kj.obj(\'img\',this));" onclick="jsshop.cart_del(this)" title="'+o.name+'"><input type="hidden" name="cartval" value="'+o.id+','+o.price+'"><img src="'+this.tempurl+'/images/btn_del.gif" style="display:none"></span>';
		} else {
			html = '<span style="background:url'+'(' + o.pic + ') no-repeat;" onmouseover="kj.show(kj.obj(\'img\',this));"  onmouseout="kj.hide(kj.obj(\'img\',this));" onclick="jsshop.cart_del(this)" title="'+o.name+'"><input type="hidden" name="cartval" value="'+o.id+','+o.price+'"><img src="'+this.tempurl+'/images/btn_del.gif" style="display:none"></span>';
			//非第三个时，显示加号
			i = index+1;
			html += '<span class="x_plus">&nbsp;</span>';
		}
		objbox[this.index].innerHTML += html;
		//总金额加
		this.refresh_price(this.index);
	}
	//新增一人 , type=3 表示套餐，不加饭
	this.cart_new = function(rice) {
		var price = 0;
		//检查当前订餐是否合格
		if(this.index>=0) {
			for(i = 0; i < this.cart[this.index].length ; i++) {
				price+=kj.toint(this.cart[this.index][i].price);
			}
			if(price < this.cfg.minprice) {
				alert("温馨提示：单份消费价格不得低于"+this.cfg.minprice+"元，不便之处还请您多多包涵！");
				return false;
			}
		}
		this.index = 0;
		var len = this.cart.length;
		for(var i = len ; i > 0 ; i--) {
			this.cart[i] = this.cart[i-1];
		}
		this.cart[0] = [];
		var objbox = kj.obj("#id_cart_box");
		var html = '<div class="x_row"><li class="x_picli"></li><li class="col3">';
		html += '<input type="button" name="btn_jian" value="" class="btn_jian" onclick="jsshop.change_num(\''+this.index+'\',-1);"> <input type="text" name="num'+this.index+'[]" value="1" id="id_cart_num_'+this.index+'" class="x_num" onkeyup="jsshop.change_num('+this.index+')"> <input type="button" name="btn_jian" value="" class="btn_jia" onclick="jsshop.change_num('+this.index+',1);">';
		html += '</li><li class="col4" id="id_cart_price_'+this.index+'"></li><li class="col5"><input type="button" name="btn_del" value="" class="x_del" onclick="jsshop.cart_remove(this,'+this.index+')"></li></div>';
		objbox.innerHTML = html + objbox.innerHTML;
		//自动加饭
		if(rice && this.rice) {
			this.cart_add(this.rice);
		} else {
			this.refresh_price(this.index);
		}
		return true;
	}
	//删除选中菜品
	this.cart_del = function(objspan) {
		var obj_cart = kj.obj("#id_cart_box .x_row");
		var obj_row = kj.parent(objspan , 'div');
		var index = kj.index(obj_cart , obj_row);
		var objbox = kj.obj("#id_cart_box .x_picli");
		var obj = kj.obj("span" , objbox[index]);
		var i = kj.index(obj , objspan);
		var j = i+1;
		kj.remove(obj[j]);
		kj.remove(obj[j-1]);
		i = parseInt(i/2);
		this.cart[index].removeat(i);
		this.refresh_price(index);
	}
	//刷新提示信息
	this.refresh_price = function(index) {
		//刷新当前 index 价格
		var objbox = kj.obj("#id_cart_box .col4");
		var objnum = kj.obj("#id_cart_box .col3 input<<name,/^num/i");
		var price = 0;
		var i,n;
		if(index >= 0) {
			for(i = 0; i < this.cart[index].length ; i++) {
				price+=kj.toint(this.cart[index][i].price);
			}
			n = kj.toint(objnum[index].value);
			objbox[index].innerHTML = "￥" + (price*n);
		}
		var total = 0;
		var num = 0;
		for(i = 0 ; i <objbox.length ; i++ ){
			total += kj.toint(objbox[i].innerHTML);
			num += kj.toint(objnum[i].value);
		}
		kj.set("#id_cart_menu .x_2" , 'innerHTML' , '共 <font style="font-size:14px;color:#FF821E">'+num+'</font> 份，合计：');
		kj.set("#id_cart_menu .x_3" , 'innerHTML' , '￥' + total);
		if(price == 0) {
			this.showcart(0);
		} else {
			this.showcart(1);
		}
		return total;

	}
	//删除当前份
	this.cart_remove = function(obj , index) {
		kj.remove(kj.parent(obj , 'div'));
		if(index < this.index) this.index--;
		this.cart.removeat(index);
		if( this.cart.length<=this.index) this.index = this.cart.length-1;
		this.refresh_price(-1);
	}
	//提交，保存到cookie
	this.cart_submit = function() {
		//检查是否已点餐
		if(this.cart.length<1 || this.cart[0].length<1) {
			alert("温馨提示：您的购物车是空的，请先点餐！");
			return false;
		}
		//检查当前点餐是否已满足条件
		var price = 0;
		for(i = 0; i < this.cart[this.index].length ; i++) {
			price+=kj.toint(this.cart[this.index][i].price);
		}
		if(this.cart[this.index]>0 && price < this.cfg.minprice) {
			alert("温馨提示：单份消费价格不得低于"+this.cfg.minprice+"元，不便之处还请您多多包涵！");
			return false;
		}
		var total = this.refresh_price(-1);
		//点餐价格是否达到起送价
		if(total < this.mintotal) {
			alert("温馨提示：由于人力成本等问题，外卖定餐需起送不得低于"+this.mintotal+"元，不便之处还请您多多包涵！");
			return false;
		}
		var i,j,num,arr_1=[],arr_2=[];
		var objnum = kj.obj("#id_cart_box .col3 input<<name,/^num/i");

		for(i = 0 ; i < this.cart.length ; i++ ) {
			arr_2=[] ;
			for(j = 0 ; j < this.cart[i].length ; j++) {
				arr_2[arr_2.length] = this.cart[i][j].id;
			}
			if(this.cart[i].length>0) {
				num = objnum[i].value;
				for(j = 0 ; j < num ; j++ ) {
					arr_1[arr_1.length] = arr_2.join(",");
				}
			}
		}
		var str = kj.cookie_get("cart_ids");
		var arr = [];
		if(str) arr = str.split("||");
		for(i = 0 ; i < arr.length ; i++) {
			arr_2 = arr[i].split(":");
			if(arr_2[0] == '0') {
				arr.removeat(i);break;
			}
		}
		arr[arr.length] = "0:" + arr_1.join("|");
		var str_ids = arr.join("||");
		kj.cookie_set("cart_ids" , str_ids , 24);
		window.location.href = "?app_act=cart";

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
			this.showcart_time('#id_cart_menu' , top , h-138 , -10);
		} else {
			kj.set("#id_cart_menu .x_top .x_6" , 'className' , 'x_4');
			this.showcart_time('#id_cart_menu' , top , h , 10);
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
			var obj1 = kj.parent(obj_cart_num , 'li');
			var obj2 = kj.obj("#id_cart_box .col3");
			var index = kj.index(obj2 , obj1);
			this.refresh_price(index);
		}
	}
	this.resize = function(){
		var h = document.documentElement.clientHeight;
		var offset = kj.offset("#id_left");
		kj.set("#id_cart_menu" , 'style.top' , (h-32) + "px");
		var left = document.documentElement.scrollLeft || document.body.scrollLeft;
		kj.set("#id_cart_menu" , 'style.left' , (offset.left-left) + "px");
		//定位label
		kj.set("#labelright",'style.top',(h/2)+'px');
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
		kj.dialog({id:'comment' + menu_id,title:'查看评论',url:'?app_act=comment&menu_id='+menu_id,w:500,showbtnhide:false,showbtnmax:false,top:0,type:'iframe'});
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