	<form name="frmReg" method="post" action="?app=ajax&app_act=reg" onsubmit="return jsreg.on_reg(true);">
	<input type="hidden" name="verifycode" value="">
	<div class="loginbox">
		<ul class="xleft">
			<li><span class="x1"><?php if(cls_config::get('rule_uname','user')=='email'){?>邮箱：<?php } else if(cls_config::get('rule_uname','user')=='mobile') { ?>手机：<?php } else { ?>用户名：<?php }?></span><span><input name="uname" type="text" class="txt1"/> <font class="txt_red">*</font></span></li>
			<li><span class="x1">密码：</span><span><input type="password" name="pwd1" class="txt1"/> <font class="txt_red">*</font></span></li>
			<li><span class="x1">确认密码：</span><span><input type="password" name="pwd2" class="txt1"/> <font class="txt_red">*</font></span></li>
			<?php if(cls_config::get('rule_uname','user')!='email' && cls_config::get('rule_uname','user')!='mobile'){?>
			<li><span class="x1">邮箱：</span><span><input type="text" name="email" class="txt1"/> <font class="txt_red">*</font></span></li>
			<?php }?>
			<?php if($reg_switch==2){?>
			<li><span class="x1">邀请码：</span><span><input type="password" name="invite_code" class="txt2"/> 
	    <font class="txt_red">*</font> <font class="txt_gary"><?php echo $reg_switch_info;?></font></span></li>
			<?php }?>
			<li><span class="x1"></span><span><label><input type="checkbox" name="autologin" value="1" checked>下次自动登录</label><br><br>
			<input type="submit" name="btn_submit" value="注 册" class="button1" style="font-size:22px">
			</span></li>
		</ul>
		<ul class="xboot a1">
		<h1>已是会员？</h1>我已经是本站会员，点击<a href="javascript:jsreg.showlogin();">【立即登录】</a>
		</ul>
	</div>
	</form>
<div id="id_verify_box_reg" style="display:none">
	<div class="me_box1" style="float:left;padding:10px;line-height:40px">
	<li style="float:left;width:90%"><img src="/webcss/default/" id="id_verify_pic_reg"  onclick="jsreg.verify_refresh();"></li>
	<li style="float:left;width:90%"><input name="verifycode" type="text" class="pTxt1 pTxtL100" id="id_verifycode"/></li>
	<li style="float:left;width:90%"><input type="submit" name="btn_verify" value="确 定" class="btn1" id="id_btn_verify" onclick="jsreg.verify_ok();"></li>
	</div>
</div>
<?php if(cls_config::get('rule_uname','user')=='email'){?>
<div id="id_verify_email_box" style="display:none">
	<div class="me_box1" style="float:left;padding:10px;line-height:40px">
	<li style="float:left;width:90%">验证码已发送到您的邮箱，请登录邮箱获取</li>
	<li style="float:left;width:90%"><input name="emailcode" type="text" class="pTxt1 pTxtL100" id="id_emailcode"/></li>
	<li style="float:left;width:90%"><span style="float:left"><input type="submit" name="btn_verify_email" value="确 定" class="btn1" id="id_btn_verify_email" onclick="jsreg.verify_email_ok();"></span><span style="float:left;padding:5px 0px 0px 10px"><a href="javascript:kj.dialog.close('#winemailcode');jsreg.on_reg()" style="color:#888888;">重新发送</a></span></li>
	</div>
</div>
<?php } else if(cls_config::get('rule_uname','user')=='mobile') { ?>
<div id="id_verify_email_box" style="display:none">
	<div class="me_box1" style="float:left;padding:10px;line-height:40px">
	<li style="float:left;width:90%">请输入您手机收到的验证码&nbsp;(<span id="id_email_timer_box" style="color:#ff0000">剩余：<font id="id_email_timer"></font>秒</span>)</li>
	<li style="float:left;width:90%"><input name="emailcode" type="text" class="pTxt1 pTxtL100" id="id_emailcode"/></li>
	<li style="float:left;width:90%"><span style="float:left"><input type="submit" name="btn_verify_email" value="确 定" class="btn1" id="id_btn_verify_email" onclick="jsreg.verify_email_ok();"></span><span style="float:left;padding:5px 0px 0px 10px"><a href="javascript:kj.dialog.close('#winemailcode');jsreg.on_reg()" style="color:#888888;">重新发送</a></span></li>
	</div>
</div>
<?php }?>
