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
<style>
body{background:#fff}
</style>
</head>
<body>
<?php if(count($order_list['list'])>0){?>
	<?php foreach($order_list['list'] as $item=>$key){ ?>
	<table class="orderlist" align="center">
	<tr><td colspan=3 class="tit"><font class="txt_redB"><?php echo $item;?></font>（<?php echo fun_get::weekday($item);?>）</td></tr>
		<?php foreach($key as $order){ ?>
		<tr><td class="col1">
		<?php if($order['order_shop_id']>0){?><a href="index.php?app_act=shop&id=<?php echo $order['order_shop_id'];?>" style="color:#888888"><?php echo $order_list['shop']['id_'.$order['order_shop_id']];?></a><br><?php }?>
		<font class="txt_orangeB"><?php echo $order['addtime'];?></font><br><?php echo $order['order_name'];?>/<?php echo $order['order_sex'];?></td>
		<td>
		<?php $i=0;?>
		<?php foreach($order['menu'] as $menu){ ?>
			<div class="row1"<?php if($i==0){?> style="background:none"<?php }?>>
			<?php $price=0;?>
			<ul class="x1">
			<?php $ii=0;?>
			<?php foreach($menu as $item){ ?>
				<?php $price+=$order_list['price']['id_'.$item];?>
				<li><?php if($ii>0){?> + <?php }?><?php echo $order_list['menu']['id_'.$item]['menu_title'];?></li>
				<?php $ii++;?>
			<?php }?>
			</ul>
			<ul class="x2">
			￥<?php echo $price;?>×<?php echo $order['menunum'][implode(',',$menu)];?>=￥<?php echo $order['menunum'][implode(',',$menu)]*$price;?>
			</ul>
			</div>
			<?php $i++;?>
		<?php }?>
		</td><td class="col2">
		总计：<?php echo $order['order_total'];?><?php if(!empty($order['order_score_money'])){?><br>抵扣：<?php echo $order['order_score_money'];?><?php }?><?php if(!empty($order['order_favorable'])){?><br>优惠：<?php echo $order['order_favorable'];?><?php }?><br>应付：<font class="txt_redB"><?php echo $order['order_total_pay'];?></font>
		<?php if($order['order_state'] > 0){?>
		<input type="button" name="btncomment[]" <?php if($order['order_comment']){?>value="已评论" class="button5x"<?php } else { ?>value="评论" class="button5"<?php }?> onclick="parent.kj.dialog({id:'comment',title:'评论订单',url:'<?php echo fun_get::url(array('app_act'=>'comment','order_id'=>$order['order_id']));?>',w:500,showbtnhide:false,showbtnmax:false,top:0,type:'iframe'});">
		<?php } else { ?>
		<br><font color="#ff0000">(<?php echo $order['state'];?>)</font>
		<?php }?>
		</td></tr>
		<?php }?>
	</table>
	<?php }?>	
	<div class="pPage" id="id_pPage" style="margin-top:20px">
	<?php echo $order_list['pagebtns'];?>
	</div>
<?php } else { ?>
<center>
	<div class="shop_tips" id="id_shop_tips" style="position:static">
	<ul>
	<li><h1><br>还没有您的订单</h1></li>
	<ul>
	</div>
</center>
<?php }?>

</body>
</html>