<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>klkkdj 安装向导</title>
<link rel="stylesheet" type="text/css" href="<?php echo $webcss_url;?>common/images/common.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $webcss_url;?>common/images/expand.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $webcss_url;?>install/images/css.css"/>
<script>
var web_config = {
	domain : '/',
	baseurl : './',
	basecss : './webcss',
	cookie_pre : 'kj_',
	rule_uname : '/^[a-z0-9\u4E00-\u9FA5_-]+$/i',
	rule_uname_tips : '账号长度在2-16位，不能包函特殊字符',
	rule_pwd : '/^.{4,16}$/i',
	rule_pwd_tips : '密码长度必须在4-16之间'
};
</script>
<script src="<?php echo $webcss_url;?>common/js/kj.js"></script>
<script src="<?php echo $webcss_url;?>common/js/kj.ajax.js"></script>
<script src="<?php echo $webcss_url;?>common/js/kj.alert.js"></script>
<script src="<?php echo $webcss_url;?>common/js/kj.progress.js"></script>

<script>
kj.onload(function(){
	kj.handler(".pTxt1","focus",function(){
		kj.delClassName(this , "pTxt1");
		kj.addClassName(this , "pTxt2");
	});
	kj.handler(".pTxt1","blur",function(){
		kj.delClassName(this , "pTxt2");
		kj.addClassName(this , "pTxt1");
	});
});
</script>

</head>
<body>
<div class="header"><li class="x_logo"><a href="http://www.klkkdj.com" target="_blank"><img src="<?php echo $webcss_url;?>/install/images/logo.png"></a></li><li class="x_txt"><?php echo $version_info['version_name'];?>&nbsp;&nbsp;&raquo;&nbsp;&nbsp;安装向导</li></div>