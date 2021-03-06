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
<style>
body{background:#fff}
.comment{float:left;width:500px;padding:10px 20px}
.comment .good{color:#CC0000;background:url(/webcss/default/images/comment1.png) no-repeat right center;padding-right:20px}
.comment .general{color:#78D417;background:url(/webcss/default/images/comment2.png) no-repeat right center;padding-right:20px}
.comment .fail{color:#666666;background:url(/webcss/default/images/comment3.png) no-repeat right center;padding-right:20px}
.comment .tit{float:left;500px}
.comment .tit span{float:left;margin-right:20px}
.comment table{float:left;width:450px;margin-top:20px}
.comment table .tit2{}
.comment table td{padding:8px 0px 7px 5px;border-bottom:1px #eee solid}
.comment table .tit2 td{background:#efefef;font-weight:bold;border-bottom:0px}
.page{width:500px}
</style>
</head>
<body style="overflow-x:hidden">
<div class="comment">
	<div class="tit"><span class="good"><?php echo $goodnum;?>人觉得好吃</span><span class="general"><?php echo $generalnum;?>人觉得一般</span><span class="fail"><?php echo $failnum;?>人觉得难吃</span></div>
	<table>
	<tr class="tit2"><td width='80'>美食者</td><td width='80'>点评</td><td>时间</td></tr>
	<?php foreach($arr_list['list'] as $item){ ?>
	<tr><td><?php echo $item['user_name'];?></td><td style="color:<?php if($item['comment_val']==0){?>#78D417<?php } else if($item['comment_val']==1) { ?>#CC0000<?php } else { ?>#666666<?php }?>"><?php echo $item['val'];?></td><td><?php echo $item['addtime'];?></td></tr>
	<?php }?>
	</table>
	<div class="pPage page" id="id_pPage" style="margin-top:20px">
	<?php echo $arr_list['pagebtns'];?>
	</div>
</div>
</body>
</html>