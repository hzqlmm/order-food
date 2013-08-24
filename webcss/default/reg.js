var jsreg = new function() {
	this.timer = 90;
	this.verifycode_html = '';
	this.reg_verify = false;
	this.rule_uname = '';
	this.on_reg = function(is_re) {
		//是否需要验证码
		if(this.reg_verify=='1') {
			this.show_verify();
		}else{
			this.on_is_verify();
		}
		if(is_re) return false;
	}
	this.on_is_verify = function() {
	  if(this.rule_uname == 'email' || this.rule_uname == 'mobile') {
		this.reg_email();
	  }else{
		this.reg();
	  }
	}
	//邮箱验证
	this.reg_email = function() {
		var key = document.frmReg.uname.value;
		var verifycode = document.frmReg.verifycode.value;
		var type = '';
		if(this.rule_uname == 'email') {
			type = 'email';
		}else if(this.rule_uname=='mobile'){
			type = 'mobile';
		}
		kj.ajax.get(kj.cfg('baseurl') + '/common.php?app=sys&app_act=verify_reg&type=' + type + '&key=' + key + '&verifycode=' + verifycode, function(data) {
			var obj_data=kj.json(data);
			if(obj_data.isnull) {
				alert("系统繁忙，请稍后再来试试");
			} else {
				if(jsreg.reg_verify) {
					kj.dialog.close("#winverifycode");
				}
				if(obj_data.code == 0) {
					jsreg.show_verify_email();
					jsreg.start_time(1);
				} else {
					if('msg' in obj_data && obj_data.msg!='') {
						alert(obj_data.msg);
					} else {
						alert("系统繁忙，请稍后再来试试");
					}
					if(obj_data.code == '11') {
						jsreg.show_verify();
					} else {
						if(jsreg.reg_verify) kj.hide('#winverifycodereg');
					}
				}
			}
		});
	}
	//显示验证码
	this.show_verify = function() {
		var obj = kj.obj('#id_verify_box_reg');
		if(obj) {
			this.verifycode_html = obj.innerHTML;
			kj.remove(obj);
		}
		kj.dialog({'html':this.verifycode_html,'id':'verifycodereg','type':'html','title':'请输入验证码','w':300,'showbtnmax':false,'showbtnhide':false});
		this.verify_refresh();
	}
	//显示邮箱验证框
	this.show_verify_email = function() {
		var obj = kj.obj('#id_verify_email_box');
		if(obj) {
			this.emailcode_html = obj.innerHTML;
			kj.remove(obj);
		}
		kj.dialog({'html':this.emailcode_html,'id':'emailcode','type':'html','title':'注册验证','w':330,'showbtnmax':false,'showbtnhide':false});
	}
	this.verify_email_ok = function() {
		var val = kj.obj("#id_emailcode").value;
		if(val == '') {
			alert("请输入验证码");
			kj.obj("#id_emailcode").focus();
			return;
		}
		document.frmReg.verifycode.value = val;
		kj.obj("#id_btn_verify_email").className = '';
		kj.obj("#id_btn_verify_email").disabled = true;
		kj.obj("#id_btn_verify_email").value="正在提交，请不要关闭..";
		this.reg();
	}
	this.verify_refresh = function() {
		kj.obj("#id_verify_pic_reg").src = kj.cfg('dirpath') + '/common.php?app=sys&app_act=verifycode&app_contenttype=img&app_rnd='+Math.random();
	}
	this.verify_ok = function() {
		var val = kj.obj("#id_verifycode").value;
		if(val == '') {
			alert("请输入验证码");
			kj.obj("#id_verifycode").focus();
			return;
		}
		document.frmReg.verifycode.value = val;
		kj.obj("#id_btn_verify").className = '';
		kj.obj("#id_btn_verify").disabled = true;
		kj.obj("#id_btn_verify").value="正在提交，请不要关闭..";
		this.on_is_verify();
	}
	this.reg = function() {
		kj.ajax.post(document.frmReg , function(data) {
			var obj_data=kj.json(data);
			if(obj_data.isnull) {
				alert("系统繁忙，请稍后再来试试");
			} else {
				if(obj_data.code == 0) {
					var url = (kj.obj("#id_jump_fromurl")) ? kj.obj("#id_jump_fromurl").value : "";
					if('msg' in obj_data && obj_data.msg!='') {
						alert(obj_data.msg);
					}
					if(url) {
						window.open(url , "_self");
					} else {
						location.reload(true);
					}
				} else {
					if('msg' in obj_data && obj_data.msg!='') {
						alert(obj_data.msg);
					} else {
						alert("系统繁忙，请稍后再来试试");
					}
					if(this.rule_uname == 'email') {
						kj.obj("#id_btn_verify_email").className = 'btn1';
						kj.obj("#id_btn_verify_email").disabled = false;
						kj.obj("#id_btn_verify_email").value="确 定";
					}
					if(jsreg.reg_verify) {
						if(kj.obj("#id_btn_verify")) {
							kj.obj("#id_btn_verify").className = 'btn1';
							kj.obj("#id_btn_verify").disabled = false;
							kj.obj("#id_btn_verify").value="确 定";
						}
					}
					if(obj_data.code == '11') jsreg.show_verify();
				}
			}
		});
	}
	this.start_time = function(isstart) {
		var obj = kj.obj("#id_email_timer");
		if(!obj) return;
		var timer = kj.toint(obj.innerHTML);
		//if(timer <= 0) timer = this.timer;
		timer--;
		if(isstart) timer = this.timer;
		if(timer<0) {
			kj.obj("#id_email_timer_box").innerHTML = '已过期';
			kj.obj("#id_btn_verify_email").className = '';
			kj.obj("#id_btn_verify_email").disabled = true;
			kj.obj("#id_btn_verify_email").value="已过期..";

		} else {
			obj.innerHTML = timer;
			setTimeout('jsreg.start_time()' , 1000);
		}
	}
	this.showlogin = function() {
		kj.hide("#winshowreg");
		if(jsheader) jsheader.showlogin();
	}
}