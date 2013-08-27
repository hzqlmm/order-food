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
	<div class="info">尊敬的会员：<font class="txt_orangeB"><?php echo $this_login_user->uname;?></font> ，您现在累计的积分为：<font class="txt_redB"><?php echo $this_login_user->get_score();?></font> 分，可等值抵扣消费金额 <font class="txt_redB"><?php echo $score_money;?></font> 元。</div>
	<div class="tit">
		<span style="width:150px;">时间</span>
		<span style="width:100px;">收入(分)</span>
		<span style="width:100px;">支出(分)</span>
		<span style="width:100px;">账户余额(分)</span>
		<span style="width:230px;">详情</span>
	</div>
	<?php $score=0;?>
	<?php foreach($action_list["list"] as $item){ ?>
	<div class="list">
		<span class="x1"><?php echo $item["action_addtime"];?></span>
		<span class="x2"><?php if($item["action_score"]>0){?>+<?php echo $item["action_score"];?><?php }?></span>
		<span class="x3"><?php if($item["action_score"]<0){?><?php echo $item["action_score"];?><?php }?></span>
		<span class="x5"><?php echo $action_list["score"]-$score;?></span>
		<span class="x6"><?php echo $item["beta"];?></span>
		<?php $score+=$item["action_score"];?>
	</div>
	<?php }?>
</div>
<div class="pPage" id="id_pPage" style="margin-top:20px">
<?php echo $action_list['pagebtns'];?>
</div>
</body>
</html>