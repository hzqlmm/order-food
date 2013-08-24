<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo cls_config::get("site_title","sys");?></title>
<meta name="keywords" content="<?php echo cls_config::get("keywords","sys");?>"/>
<meta name="description" content="<?php echo cls_config::get("description","sys");?>"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/common.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/expand.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/default/images/css.css"/>
</head>
<body>
<?php include cls_resolve::on_resolve('/default\/header')?>
<div class="payok mg1">
	<table align="center"><tr><td>
		<div class="tipsright">
		<li class="tit">恭喜！您的订单已经提交！</li>
		<li>订单编号：<span style="color:#ff8800;"><?php echo $obj_order['order_number'];?></span></li>
		<li>订单金额：<span style="color:#ff8800;font-size:20px">￥<?php echo $obj_order['order_total_pay'];?></span></li>
		<?php if($obj_order['order_pay_method'] =='afterpayment'){?>
		<li>支付方式：<span style="color:#ff8800;">货到付款</span></li>
		<li><input type="button" name="btn_detail" value="查看详情" class="button2" onclick="jsmember.act_default();">&nbsp;&nbsp;<a href="./">返回首页</a></li>
		<?php } else if($obj_order['order_pay_method'] == 'repayment') { ?>
		<li>支付方式：<span style="color:#ff8800;">预付款</span><span style="color:#888888">(已支付)</span></li>
		<li><input type="button" name="btn_detail" value="查看详情" class="button2" onclick="jsmember.act_default();">&nbsp;&nbsp;<a href="./">返回首页</a></li>
		<?php } else { ?>
		<li>支付方式：<span style="color:#ff8800;"><?php echo $obj_order['paymethod'];?></span></li>
			<?php if($timeout == 1){?>
				<li class="tipsred">订单已过期，请重新点餐</li>
				</div>
				</td></tr>
				<tr><td>
				<div class="info">
				<li><input type="button" name="btn_pay" value="重新点餐" class="button2" onclick="window.open('./','_self');">&nbsp;&nbsp;<a href="javascript:jsmember.act_default();">查看订单</a></li>
			<?php } else { ?>
				</div>
				</td></tr>
				<tr><td>
				<div class="info">
				<li><input type="button" name="btn_pay" value="立即支付" class="button2" onclick="window.open('<?php echo fun_get::url(array('id'=>$id,'app_act'=>'order_pay'));?>','_blank');">&nbsp;&nbsp;<a href="javascript:jsmember.act_default();">查看订单</a></li>
				<li class="tipsred">请在<?php echo $delay_time;?> 前完成支付，否则订单将被取消</li>
			<?php }?>
		<?php }?>
		</div>
		</td></tr>
		</table>
</div>
<?php include cls_resolve::on_resolve('/default\/footer')?>
</body>
</html>