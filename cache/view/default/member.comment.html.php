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
<script src="/webcss/common/js/kj.dialog.js"></script>
<script src="/webcss/common/js/kj.ajax.js"></script>
<script src="/webcss/common/js/kj.alert.js"></script>
<style>
body{background:#fff}
.comment{float:left;width:360px;padding:10px 40px}
.comment li{float:left;width:360px;padding:5px 0px 5px 10px}
.comment .col1{float:left;width:100px}
.comment .col2{float:left;}
.comment .col2 img{vertical-align:middle}
.comment .col2 input{vertical-align:middle}
.comment .xsel{background:#efefef}
.comment textarea{width:350px;height:80px;background:#f8f8f8;border:1px #666 solid}
.comment .good{color:#CC0000;background:url(/webcss/default/images/comment1.png) no-repeat 20px}
.comment .general{color:#78D417;background:url(/webcss/default/images/comment2.png) no-repeat 20px}
.comment .fail{color:#666666;background:url(/webcss/default/images/comment3.png) no-repeat 20px}
</style>
</head>
<body style="overflow-x:hidden">
<form name="frmMain" method="post" action="<?php echo fun_get::url(array('app_act'=>'comment.save'));?>">
<input type="hidden" name="id" value="<?php echo $arr_list['comment']['comment_id'];?>">
<div class="comment">
	<?php foreach($arr_list['list'] as $item){ ?>
		<input type="hidden" name="id<?php echo $item['menu_id'];?>" value="<?php echo $item['comment']['comment_id'];?>">
		<li onmouseover="kj.addClassName(this,'xsel');" onmouseout="kj.delClassName(this,'xsel');"><span class="col1"><?php echo $item['menu_title'];?></span>
		<span class="col2">
		<label class="good"><input type="radio" name="comment<?php echo $item['menu_id'];?>" value="1"<?php if($item['comment']['comment_val']==1){?> checked<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;好吃</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<label class="general"><input type="radio" name="comment<?php echo $item['menu_id'];?>" value="0"<?php if($item['comment']['comment_val']==='0'){?> checked<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;一般</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<label class="fail"><input type="radio" name="comment<?php echo $item['menu_id'];?>" value="-1"<?php if($item['comment']['comment_val']==-1){?> checked<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;难吃</label></span></li>
	<?php }?>
	<li><textarea name="comment_beta"><?php echo $arr_list['comment']['comment_beta'];?></textarea></li>
	<li style="text-align:right"><input type="button" name="" value="评论" class="button4" onclick="thisjs.comment();"></li>
</div>
</form>
<script>
var thisjs = new function() {
	this.comment = function() {
		kj.ajax.post(document.frmMain , function(data) {
			var arr = kj.json(data);
			if(arr.code == 0) {
				kj.alert.show("评论成功，谢谢您^_^",function(){
					parent.kj.dialog.close("#wincomment");
				});
			} else {
				alert(arr.msg);
			}
		});
	}
}
</script>
</body>
</html>