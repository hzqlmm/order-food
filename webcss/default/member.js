var jsmember = new function() {
	this.html = [];
	this.act_pwd = function() {
		jsheader.close();
		if("act_pwd" in this.html) {
			kj.dialog({'html':this.html['act_pwd'],'id':'memberpwd','type':'html','title':'修改密码','w':600,'showbtnmax':false,'showbtnhide':false});
		} else {
			kj.ajax.get("?app=member&app_act=pwd" , function(data) {
				jsheader.html['act_pwd'] = data;
				kj.dialog({'html':data,'id':'memberpwd','type':'html','title':'修改密码','w':600,'showbtnmax':false,'showbtnhide':false});
			});
		}
	}
	this.save_pwd = function() {
		if(document.frmMpwd.oldpwd.value=="") {
			alert("请输入原密码");
			document.frmMpwd.oldpwd.focus();
			return false;
		}
		if(document.frmMpwd.pwd1.value=="") {
			alert("请输入新密码");
			document.frmMpwd.pwd1.focus();
			return false;
		}
		if(document.frmMpwd.pwd1.value != document.frmMpwd.pwd2.value) {
			alert("两次输入密码不正确");
			document.frmMpwd.pwd1.focus();
			return false;
		}
		if(!kj.rule.types.pwd(document.frmMpwd.pwd1.value)) {
			alert(kj.cfg('rule_pwd_tips'));
			document.frmMpwd.pwd1.focus();
			return false;
		}
		kj.ajax.post(document.frmMpwd,function(data){
			var obj_data=kj.json(data);
			if(obj_data.isnull) {
				alert("操作失败");
			} else {
				if('code' in obj_data && obj_data.code=='0') {
					document.frmMpwd.pwd1.value = '';
					document.frmMpwd.pwd2.value = '';
					document.frmMpwd.oldpwd.value = '';
					kj.alert.show("更新成功",function(){kj.dialog.close('#winmemberpwd');});
				} else {
					if("msg" in obj_data) {
						kj.alert(obj_data.msg);
					} else {
						kj.alert("保存失败");
					}
				}
			}
		});
		return false;
	}
	this.act_default = function() {
		jsheader.close();
		kj.dialog({'url':'?app=member','id':'member','type':'iframe','title':'我的订单','w':700,'max_h':650,'showbtnmax':false,'showbtnhide':false});
	}
	this.act_myvip = function() {
		jsheader.close();
		kj.dialog({'url':'?app=member&app_act=myvip','id':'membermyvip','type':'iframe','title':'我的经验','w':750,'max_h':650,'showbtnmax':false,'showbtnhide':false});
	}
	this.act_myintegral = function() {
		jsheader.close();
		kj.dialog({'url':'?app=member&app_act=myintegral','id':'membermymyintegral','type':'iframe','title':'我的积分','w':750,'max_h':650,'showbtnmax':false,'showbtnhide':false});
	}
	this.act_msg = function() {
		jsheader.close();
		kj.dialog({'url':'?app=member&app_act=msg','id':'membermymymsg','type':'iframe','title':'我的留言','w':720,'max_h':650,'showbtnmax':false,'showbtnhide':false});
	}
	this.act_repayment = function() {
		jsheader.close();
		kj.dialog({'url':'?app=member&app_act=repayment','id':'membermymyrepayment','type':'iframe','title':'我的预付款','w':720,'max_h':650,'showbtnmax':false,'showbtnhide':false});
	}
}