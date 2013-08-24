<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo cls_config::get("site_title","sys");?></title>
<meta name="keywords" content="<?php echo cls_config::get("keywords","sys");?>"/>
<meta name="description" content="<?php echo cls_config::get("description","sys");?>"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/common.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/expand.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/default/images/css.css"/>
<script src="<?php echo cls_config::get("dirpath","base");?>/common.php?app=sys&app_act=web.config&app_ajax=1"></script>
<script src="/webcss/common/js/kj.js"></script>
<script src="/webcss/common/js/kj.page.js"></script>
<style>
body{background:#fff}
</style>
</head>
<body style="overflow-x:hidden">
<div class="myvip">
	<div class="info">尊敬的菜菜会员：<font class="txt_orangeB"><?php echo $this_login_user->uname;?></font> ，您的会员等级：<img src="/webcss/default/images/vip<?php echo $loginuser_level;?>.png" align="absmiddle">。<br>您现在累计的经验值为：<font class="txt_redB"><?php echo $loginuser_experience;?></font> 点，仅需 <font class="txt_redB"><?php echo $loginuser_experience_poor;?></font> 点经验值，即可晋升为<font class="txt_orangeB"> V<?php echo $loginuser_level_next;?> </font>会员。</div>
	<div class="process">
		<li style="width:<?php echo $progress;?>px;"><?php echo $loginuser_experience;?> <img src="/webcss/default/images/Vip_arr.png" align="absmiddle"></li>
	</div>
	<div class="process1"></div>
	<div class="process2">
		<span style="width:86px;">(<font class="txt_red">0</font>)</span>
		<span style="width:102px;">(<font class="txt_red">100</font>)</span>
		<span style="width:100px;">(<font class="txt_red">300</font>)</span>
		<span style="width:115px;">(<font class="txt_red">800</font>)</span>
		<span style="width:120px;">(<font class="txt_red">2000</font>)</span>
		<span style="width:100px;">(<font class="txt_red">5000</font>)</span>
		<span style="width:57px; text-align:right;">(<font class="txt_red">10000</font>)</span>
	</div>
	<div class="tit">
		<span style="width:150px;">时间</span>
		<span style="width:100px;">收入(点)</span>
		<span style="width:100px;">经验累积(点)</span>
		<span style="width:330px;">详情</span>
	</div>
	<?php $experience=0;?>
	<?php foreach($action_list["list"] as $item){ ?>
	<div class="list">
		<span class="x1"><?php echo $item["action_addtime"];?></span>
		<span class="x2">+<?php echo $item["action_experience"];?></span>
		<span class="x3"><?php echo $action_list["experience"]-$experience;?></span>
		<span class="x4"><?php echo $item["beta"];?></span>
		<?php $experience+=$item["action_experience"];?>
	</div>
	<?php }?>
</div>
<div class="pPage" id="id_pPage" style="margin-top:20px">
<?php echo $action_list['pagebtns'];?>
</div>
</body>
</html>