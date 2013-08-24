<script src="<?php echo cls_config::get("dirpath","base");?>/common.php?app=sys&app_act=web.config&app_ajax=1"></script>
<script src="/webcss/common/js/kj.js"></script>
<script src="/webcss/common/js/kj.dialog.js"></script>
<script src="/webcss/common/js/kj.ajax.js"></script>
<div class="top">
	<ul class="main">
	<li class="logo"><img src="/webcss/default/images/logo.png"></li>
	<li class="menu">
		<a href="./" title="网站首页">主页</a>
		<!-- <a href="./?app_act=cart" title="我的购物车"><img src="/webcss/default/images/menu_3.png"></a> -->
		<a href="./?app_act=help" title="关于我们">关于我们</a>
		<a href="javascript:void(0);" onmouseover="jsheader.over(this,1)" onmouseout="jsheader.out(this,1)">账户</a>
	</li>
	</ul>
</div>
<div class="main">
<?php if($this_login_user->uid>0){?>
<div class="xiala" id="id_menu1" onmouseover="jsheader.sel(this);" onmouseout="jsheader.unsel(this);" style="display:none">
	<li class="x2"><img src="/webcss/default/images/menu_5.png"></li>
	<li class="x1">
		<?php if($this_login_user->is_admin()){?><a href="master.php" target="_blank">后台管理</a><?php }?>
		<a href="javascript:jsmember.act_pwd();">修改密码</a>
		<a href="javascript:jsmember.act_default();">我的订单</a>
		<a href="javascript:jsmember.act_myintegral();">我的积分</a>
		<a href="javascript:jsmember.act_myvip();">我的经验</a>
		<a href="javascript:jsmember.act_msg();">我的留言</a>
		<a href="javascript:jsmember.act_repayment();">我的预付款</a>
		<a href="common.php?app=sys&app_act=login.out">退出</a>
	</li>
</div>
<?php } else { ?>
<div class="xiala" id="id_menu1" onmouseover="jsheader.sel(this);" onmouseout="jsheader.unsel(this);" style="display:none">
	<li class="x2"><img src="/webcss/default/images/menu_5.png"></li>
	<li class="x1">
		<span class="y1"><input type='button' name='btn_login' value='登录' class="button2" onclick="jsheader.showlogin();"><br><br><input type='button' name='btn_reg' value='注册' class="button2x" onclick="jsheader.showreg();"></span>
	</li>
</div>
<div id="id_loginbox" style="display:none">
	<form name="frmlogin" method="post" action="common.php?app=sys&app_act=login.verify" onsubmit="return jsheader.on_login();">
	<div class="loginbox">
		<ul class="xleft">
			<li><span class="x1"><?php if(cls_config::get('rule_uname','user')=='email'){?>邮箱：<?php } else if(cls_config::get('rule_uname','user')=='mobile') { ?>手机：<?php } else { ?>用户名：<?php }?></span><span><input type="text" name="uname" class="txt1"/></span></li>
			<li><span class="x1">密码：</span><span><input name="pwd" type="password" class="txt1"/></span></li>
			<li id="id_verify_code"<?php if($this_login_user->is_verifycode()==false){?> style="display:none"<?php }?>><span class="x1">验证码：</span><span><input name="verifycode" type="text" class="txt2"  onfocus="jsheader.show_verify('#id_verify');"/>&nbsp;<img src="/webcss/default/" id="id_verify_pic" onclick="this.src='<?php echo cls_config::get("web_url","base");?>/common.php?app=sys&app_act=verifycode&app_contenttype=img&app_rnd='+Math.random();" style="display:none"></span></li>
			<li><span class="x1"></span><span><label><input type="checkbox" name="autologin" value="1" checked>下次自动登录</label>&nbsp;&nbsp;<a href="?app=index&app_act=findpwd">忘记密码?</a><br><br>
			<input type="submit" name="btn_submit" value="登 录" class="button1" style="font-size:22px">
			</span></li>
		</ul>
		<ul class="xright">
			<li>使用其它账号登录：</li>
			<li><img src="/webcss/default/images/btn_sina.png"></li>
			<li><img src="/webcss/default/images/btn_qq.png"></li>
		</ul>
		<ul class="xboot a1">
		<h1>还不是会员？</h1>一分钟轻松注册，就可以方便订餐<a href="javascript:jsheader.showreg();">【立即注册】</a>
		</ul>
	</div>
	<input type="hidden" name="jump_fromurl" value="<?php echo fun_get::get('jump_url');?>">
	</form>
</div>
<?php }?>
<script src="/webcss/default/header.js"></script>
<script src="/webcss/default/member.js"></script>
<script src="/webcss/common/js/kj.rule.js"></script>
<script src="/webcss/common/js/kj.alert.js"></script>
<script>
kj.onload(function(){
	var w = (kj.w()-980)/2;
	kj.set("#idmenu1" , "style.top" , '120px');
	kj.set("#idmenu1" , "style.left" , (w+390)+'px');
	jsheader.rule_uname = "<?php echo cls_config::get("rule_uname","user");?>";
	jsheader.reg_verify = "<?php echo cls_config::get("reg_verify","user");?>";
	jsheader.tempurl = "/webcss/default/";
	jsheader.is_verify = <?php if($this_login_user->is_verifycode()){?>true<?php } else { ?>false<?php }?>;
});
</script>
