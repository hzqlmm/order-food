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
<?php if(count($arr_list['list'])>0){?>
	<div class="mymsg">
		<?php foreach($arr_list['list'] as $item){ ?>
		<div class="info"> 姓名：<?php echo $item["msg_name"];?>&nbsp;&nbsp;&nbsp;&nbsp;电话：<?php echo $item["msg_tel"];?>&nbsp;&nbsp;&nbsp;&nbsp;邮箱：<?php echo $item["msg_email"];?></div>
		<div class="content"><?php echo $item["msg_cont"];?></div>
		<div class="beta">时间：<?php echo $item["msg_time"];?>　　IP：<?php echo $item["msg_ip"];?></div>
		<div class="return" id="id_return_<?php echo $item["msg_id"];?>">
		<?php if(!empty($item["msg_recont"])){?>
			<div class="return_content"><?php echo $item["msg_recont"];?></div>
			<div class="return_beta">&nbsp;&nbsp;时间：<?php echo $item["msg_retime"];?></div>
		<?php }?>
		</div>
		<?php }?>
	</div>
	<div class="pPage" id="id_pPage" style="margin-top:20px">
	<?php echo $arr_list['pagebtns'];?>
	</div>
<?php } else { ?>
	<center>
		<div class="shop_tips" id="id_shop_tips" style="position:static">
		<ul>
		<li><h1>您还没有留言</h1></li>
		<li style="margin-top:20px"><input type="button" name="btn" value="我要留言" class="button3" onclick="window.open('index.php?app_act=msg','_top');"></li>
		<ul>
		</div>
	</center>
<?php }?>
</body>
</html>