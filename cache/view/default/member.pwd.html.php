	<form name="frmMpwd" method="post" action="?app=ajax&app_act=useredit" onsubmit="return jsmember.save_pwd();">
	<div class="loginbox">
		<ul class="xleft" style="width:500px;margin-left:50px">
			<li><span class="x1">登录账号：</span><span style="padding-top:10px"><?php echo $this_login_user->uname;?></span></li>
			<li><span class="x1">原密码：</span><span><input type="password" name="oldpwd" class="txt1"/></span></li>
			<li><span class="x1">新的密码：</span><span><input type="password" name="pwd1" class="txt1"/></span></li>
			<li><span class="x1">确认密码：</span><span><input type="password" name="pwd2" class="txt1"/></span></li>
			<li><span class="x1"></span><span><input type="submit" name="btn_submit" value="确认修改" class="button3">
			<input type="button" name="btn_submit" value="取 消" class="button3y" onclick="kj.dialog.close('#winmemberpwd');"><p>&nbsp;</p></span></li>
		</ul>
	</div>
	</form>