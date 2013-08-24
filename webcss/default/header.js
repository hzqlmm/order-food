var jsheader = new function() {
	this.is_verify = false;
	this.obj = null;
	this.html = [];
	this.over = function(o,id) {
		var offset = kj.offset(o);
		kj.set('#id_menu'+id , 'style.left' , (offset.left-90)+'px');
		kj.show('#id_menu'+id);
		this.obj = o;
	}
	this.out = function(o,id) {
		kj.hide('#id_menu'+id);
	}
	this.sel = function(o) {
		kj.show(o);
		if(this.obj) kj.addClassName(this.obj,'xsel');
	}
	this.unsel = function(o) {
		kj.hide(o);
		if(this.obj) kj.delClassName(this.obj,'xsel');
	}
	this.showlogin = function() {
		kj.dialog.close("#winshowreg");
		var obj = kj.obj('#id_loginbox');
		if(obj) {
			this.login_html = obj.innerHTML;
			kj.remove(obj);
		}
		kj.dialog({'html':this.login_html,'id':'showlogin','type':'html','title':'会员登录','w':500,'showbtnmax':false,'showbtnhide':false});
	}
	this.show_verify = function() {
		var objpic = kj.obj("#id_verify_pic");
		if(objpic.src.indexOf("verifycode")<0) {
			objpic.src = kj.cfg('dirpath') + '/common.php?app=sys&app_act=verifycode';
			kj.handler(document.documentElement,"click",function(e){
				var arr = new Array('img' , 'input');
				var target = kj.event_target(e);
				if(target.name!='verifycode' && target.id!='id_verify_pic') {
					kj.hide('#id_verify_pic');
				}
			});
		}
		kj.show('#id_verify_pic');
	}
	this.on_login = function() {
		kj.ajax.post(document.frmlogin , function(data) {
			var obj_data=kj.json(data);
			if(obj_data.isnull) {
				alert("系统繁忙，请稍后再来试试");
			} else {
				if(obj_data.code == 0) {
					if('jump_fromurl' in document.frmlogin && document.frmlogin.jump_fromurl.value!='') {
						window.open(document.frmlogin.jump_fromurl.value , "_self");
					} else {
						location.reload(true);
					}
				} else {
					if(jsheader.is_verify) {
						jsheader.verify_refresh();
					}
					if('msg' in obj_data && obj_data.msg) {
						alert(obj_data.msg);
					} else {
						alert("系统繁忙，请稍后再来试试");
					}
					if(jsheader.is_verify) {
						jsheader.verify_refresh();
						document.frmlogin.verifycode.value='';
					}
					if('show_code' in obj_data && obj_data.show_code == '1') {
						jsheader.is_verify = true;
						kj.show("#id_verify_code");
					}
					if(obj_data.code == '4') document.frmlogin.uname.focus();
					if(obj_data.code == '3') document.frmlogin.pwd.focus();
					if(obj_data.code == '11') document.frmlogin.verifycode.focus();
				}
			}
		});
		return false;
	}
	this.verify_refresh = function() {
		kj.obj("#id_verify_pic").src = kj.cfg('dirpath') + '/common.php?app=sys&app_act=verifycode&app_contenttype=img&app_rnd='+Math.random();
	}
	this.showreg = function() {
		kj.dialog.close("#winshowlogin");
		if("reg" in this.html) {
			kj.dialog({'html':this.html['reg'],'id':'showreg','type':'html','title':'注册会员','w':400,'showbtnmax':false,'showbtnhide':false});
		} else {
			kj.ajax.get("?app_act=reg" , function(data) {
				jsheader.html['reg'] = data;
				kj.dialog({'html':data,'id':'showreg','type':'html','title':'注册会员','w':400,'showbtnmax':false,'showbtnhide':false});
				kj.loadjs(jsheader.tempurl + "reg.js",function() {
					jsreg.reg_verify = jsheader.reg_verify;
					jsreg.rule_uname = jsheader.rule_uname;
				});
			});
		}
	}
	this.close = function() {
		var obj = kj.dialog.objid;
		for(var i = 0 ; i < obj.length ; i++) {
			kj.dialog.close("#"+obj[i]);
			i--;
		}
	}
	this.comment_shop = function(shop_id) {
		kj.dialog({id:'comment' + shop_id,title:'店铺评论',url:'?app_act=comment.shop',w:600,h:500,showbtnhide:false,showbtnmax:false,type:'iframe'});
	}

}
