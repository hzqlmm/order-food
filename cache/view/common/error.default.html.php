<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>提示</title>
<meta name="keywords" content="" />
<link rel="stylesheet" type="text/css" href="/webcss/common/images/common.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/css.css"/>
<script src="<?php echo cls_config::get("dirpath","base");?>/common.php?app=sys&app_act=web.config&app_ajax=1"></script>
<script src="/webcss/common/js/kj.js"></script>
<style>
.me_tips{float:left;font-size:14px;line-height:20px;width:100%;text-align:left;margin:20px 0px 0px 0px}
.me_tips li{float:left;margin:10px;}
.me_action{float:left;width:100%;}
.me_action li{float:left;margin:10px;}
</style>
</head>
<body>
<!--提示内容-->
<?php if( !empty($error_tips) ){?><div class="me_tips"><li><?php echo $error_tips;?></li></div><?php }?>
<!--相关操作-->
<div class="me_action">
<?php foreach($error_action as $item){ ?>
	<?php if($item['time']==0){?><?php echo fun_base::url_jump($item['url']);?><?php }?>
<li>
	<a href="<?php echo $item['url'];?>" target="<?php echo $item['target'];?>"><?php echo $item['title'];?></a>
	<?php if($item['time']>0){?>
		(<font id="id_time"><?php echo $item['time'];?></font>)
		<script>
		var jump_time=<?php echo $item['time'];?>;
		function me_refresh_time() {
			jump_time--;
			kj.set("#id_time","innerHTML",jump_time);
			if(jump_time<=0) {
				window.open("<?php echo $item['url'];?>","<?php echo $item['target'];?>");
			}
		}
		setInterval("me_refresh_time()",1000);
		</script>
	<?php }?>
</li>
<?php }?>
</div>
</body>
</html>