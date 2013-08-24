<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo cls_config::get("site_title","sys");?>-<?php echo $thisinfo['article_title'];?></title>
<meta name="keywords" content="<?php echo cls_config::get("keywords","sys");?>"/>
<meta name="description" content="<?php echo cls_config::get("description","sys");?>"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/common.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/expand.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/default/images/main.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/default/images/css.css"/>
<script src="<?php echo cls_config::get("dirpath","base");?>/common.php?app=sys&app_act=web.config&app_ajax=1"></script>
<script src="/webcss/common/js/kj.js"></script>
<script src="/webcss/common/js/kj.ajax.js"></script>
<script src="/webcss/common/js/kj.dialog.js"></script>
<style>
.metable{float:left;width:100%}
.metable td{}
.mebox1{float:left;width:240px;background:#fff;font-size:14px}
.mebox1 a{float:left;width:218px;border-top:1px #ccc dotted;padding:8px 0px 5px 20px}
.mebox1 .xtit{float:left;border-top:0px;padding:8px 0px 5px 10px;width:228px;font-size:14px;font-weight:bold}
.mebox2{float:left;width:698px;background:#fff;margin:0px 0px 10px 10px;border:1px #ccc solid;padding:0px 15px 20px 15px}
.mebox2 .xtitle{float:left;width:100%;border-bottom:1px #ccc dotted;font-size:18px;font-weight:bold;padding:5px 0px 5px 0px}
.mebox2 .xcont{float:left;width:100%;line-height:22px;margin:10px 0px 10px 0px;font-size:14px;color:#333;height:400px}
.mebox1 ul{float:left;width:240px}
.mebox2 td{padding:5px;}
</style>
</head>
<body>
<?php include cls_resolve::on_resolve('/default\/header')?>
<div class="mebox1">
<ul>
<li class="xtit">帮助中心</li>
</ul>
<ul>
<?php foreach($arr_help as $item){ ?>
<a href="?app_act=help&id=<?php echo $item['article_id'];?>"><?php echo $item['article_title'];?></a>
<?php }?>
<?php if(!empty($channel_name)){?><a href="?app_act=news&channel_id=<?php echo $thisinfo['article_channel_id'];?>" style="color:#ff0000;font-weight:bold"><?php echo $channel_name;?></a><?php }?>
</ul>
</div>
<div class="mebox2">
	<div class="xtitle"><?php echo $thisinfo['article_title'];?></div>
	<div class="xcont" id="id_xcont">
	<?php echo $thisinfo['article_content'];?>
	</div>
</div>
<?php include cls_resolve::on_resolve('/default\/footer')?>
<script src="<?php echo cls_config::get("dirpath","base");?>/common.php?app=article&app_act=hits&app_ajax=1&id=<?php echo $thisinfo['article_id'];?>"></script>
</body>
</html>