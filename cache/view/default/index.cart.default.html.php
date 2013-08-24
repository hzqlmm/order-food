<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo cls_config::get("site_title","sys");?>-购物车</title>
<meta name="keywords" content="<?php echo cls_config::get("keywords","sys");?>"/>
<meta name="description" content="<?php echo cls_config::get("description","sys");?>"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/common.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/expand.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/default/images/css.css"/>
<script src="<?php echo cls_config::get("dirpath","base");?>/common.php?app=sys&app_act=web.config&app_ajax=1"></script>
<script src="/webcss/common/js/kj.js"></script>
<script src="/webcss/common/js/kj.ajax.js"></script>
<script src="/webcss/common/js/kj.dialog.js"></script>
<script src="/webcss/common/js/kj.alert.js"></script>
<script src="/webcss/common/js/kj.rule.js"></script>
</head>
<body>
<?php include cls_resolve::on_resolve('/default\/header')?>
<script>
	thisdata = [];
</script>
<div class="cartlist mg1">
<form name="frm_main" method="post" action="?app=ajax&app_act=saveorder" onsubmit="return thisjs.save();">
<input type="hidden" name="cart_ids" value="">
	<div style="float:left;width:100%" id="id_shop_0">
		<div id="id_list_0">
		<table class="mlist"><tr class="tit"><td class="sort">序号</td><td>菜品/用品</td><td class="num">数量</td><td class="count">金额小计</td><td class="act">操作</td></tr>
		<?php foreach($cart_list['cart'] as $key=>$cart){ ?>
		<tr class="x_list1"><td class="sort"><?php echo $cart['index'];?></td><td>
			<?php foreach($cart['ids'] as $menu){ ?>
				<?php echo $cart_list['menu']['id_'.$menu]['menu_title'];?>
				<input type="hidden" name="menuprice[]" value="<?php echo $cart_list['menu']['id_'.$menu]['menu_price'];?>">
			<?php }?>
		</td><td>
		<input type="button" name="btn_jian" value="" class="btn_jian" onclick="jscartlist.change_num('#id_num_<?php echo $key;?>',-1);"> <input type="text" name="menunum[]" value="<?php echo $cart['num'];?>" id="id_num_<?php echo $key;?>" class="x_num" onkeyup="jscartlist.change_num('#id_num_<?php echo $key;?>')"> <input type="button" name="btn_jian" value="" class="btn_jia" onclick="jscartlist.change_num('#id_num_<?php echo $key;?>',1);">
		</td><td>￥<span class="menu_price"><?php echo $cart['price'];?></span></td><td><img src="/webcss/default/images/pic_del.png" onclick="jscartlist.cart_remove(this , 0,<?php echo $menu;?>)" style="cursor:pointer"></td></tr>
		<script>
			<?php for($i=0;$i<$cart['num'];$i++){ ?>
			thisdata[thisdata.length] = "<?php echo $key;?>";
			<?php }?>
		</script>
		<?php }?>
		</table>
		</div>
		<table class="tabact" id="id_score_0"<?php if(empty($score_money_scale)){?>style='display:none'<?php }?>}>
			<tr><td class="col1">积分抵扣<br>100积分可抵1元</td><td><input type='text' name='score' value="" class="txtbox1 pTxtL100" onblur="jscartlist.score_input(0);" onkeyup="jscartlist.score_input(0);" id="id_score_input"> × 100&nbsp;&nbsp;您还有 <span class="x_sel3" id="id_my_score"><?php echo $score_total;?></span> 积分，可抵扣 <span class="x_sel3"><?php echo $score_money;?></span> 元</td></tr>
			<?php if(count($shop_act)>0){?>
			<tr><td class="col1">优惠活动</td><td id="id_act_0"></td></tr>
			<?php }?>
		</table>
		<div class="total" id="id_total_0">
			合计金额：<span class="x_sel1">￥</span><span id="id_shop_total_0" class="x_sel1"><?php echo $cart_list['price'];?></span> (菜品金额) - <span class="x_sel1"<?php if(empty($score_money_scale)){?>style='display:none'<?php }?>}>￥</span><span id="id_shop_score_0" class="x_sel1"<?php if(empty($score_money_scale)){?>style='display:none'<?php }?>}>0</span> <?php if(!empty($score_money_scale)){?>(积分抵扣)- <?php }?> <span class="x_sel1">￥</span><span id="id_shop_act_0" class="x_sel1">0</span> (优惠金额) = <span class="x_sel2">￥</span><span id="id_shop_price_0" class="x_sel2"><?php echo $cart_list['price'];?></span>&nbsp;&nbsp;&nbsp;
		</div>
	</div>
	<table class="tabact">
		<tr><td class="col1" valign="top">配送信息：(<font style="color:#ff0000">*</font>)</td>
		<td id="id_address_info">
		<?php foreach($this_info["list"] as $item){ ?>
			<table <?php if($last_area_id==$item['info_id']){?>class="x_sel1"<?php }?> id="id_info_<?php echo $item['info_id'];?>">
				<tr><td style="color:#0099FF" width="150px" valign="top"><label><input name="area_select" type="radio" id="id_info_radior<?php echo $item['info_id'];?>" value="<?php echo $item['info_id'];?>" <?php if($last_area_id==$item['info_id']){?> checked<?php }?> onclick="jscartlist.infosel(this ,<?php echo $item['info_id'];?>);">&nbsp;<span  id="id_info_name<?php echo $item['info_id'];?>"><?php echo $item['info_name'];?>（<?php echo $item['info_sex'];?>）</span></label></td>
				<td valign="top" id="id_info_base<?php echo $item['info_id'];?>">
				<?php echo $item['info_area'];?>&nbsp; — &nbsp;<?php echo $item['info_louhao1'];?><?php if(!empty($item['info_company'])){?> — <?php echo $item['info_company'];?>/<?php echo $item['info_depart'];?><?php }?><br>
				固话：<?php echo $item['info_tel'];?> <?php if($item['info_telext']!=''){?>转 <?php echo $item['info_telext'];?><?php }?> / 手机：<?php echo $item['info_mobile'];?>
				<?php $arr=explode(",",$item["info_area_allid"]);?>
				<?php $dispatch=$dispatch_min_price;?>
				<?php foreach($arr as $areaid){ ?>
					<?php if(isset($areainfo['area']['id_' . $areaid]['dispatch_price']) && $areainfo['area']['id_' . $areaid]['dispatch_price']>0 ){?> 
					<?php $dispatch=$areainfo['area']['id_'.$areaid]['dispatch_price'];?>
					<?php }?>
				<?php }?>
				<span class="x_sel2" id="id_dispatch_<?php echo $item['info_id'];?>"><br>起送价：<?php echo $dispatch;?></span>
				</td>
				<td width="200px" valign="top"><input type="button" name="btnedit[]" value="编辑" class="button4" onclick="jscartlist.info_edit(<?php echo $item['info_id'];?>);">&nbsp;<input type="button" name="btndel[]" value="删除" class="button4y" onclick="jscartlist.info_del(<?php echo $item['info_id'];?>);"></td></tr>
			</table>
		<?php }?>
		
			<table <?php if(count($this_info['list'])<1){?>class=" x_sel1"<?php }?> <?php if($this_info['isover']){?> style='display:none'<?php }?>>
				<tr><td style="color:#0099FF" width="150px" valign="top"><label><input type="radio" name="area_select" value="0" id="id_new_info_radior"<?php if(count($this_info['list'])<1){?> checked<?php }?> onclick="jscartlist.infosel(this,'0');" >使用新地址</label></td>
				<td valign="top" id="id_new_infocol">
				<table <?php if(count($this_info['list'])>0){?> style="display:none"<?php }?> id="id_new_infosel">
				<tr><td>所在区域：<span style="color:#ff0000">*</span></td><td>
					<input type="hidden" name="area_id" id="id_area_id" value="">
					<input type="hidden" name="area_allid" id="id_area_allid" value="">
					<input type="hidden" name="area" id="id_area" value="">
					<select name="info_area_id[]" onchange="jscartlist.changearea(this.value,0);" id="id_area_0">
						<option value=""></option>
					<?php foreach($areainfo["default"] as $item){ ?>
						<option value="<?php echo $item['area_id'];?>"><?php echo $item['area_name'];?></option>
					<?php }?>
					</select>
					<?php for($i=1;$i<$areainfo["depth"];$i++){ ?>
						<select name="info_area_id[]" onchange="jscartlist.changearea(this.value,<?php echo $i;?>);" id="id_area_<?php echo $i;?>">
						</select>
					<?php }?>					
					&nbsp;&nbsp;<span class="x_sel2">起送价：</span><span class="x_sel2" id="id_dispatch_0"><?php echo $dispatch_min_price;?></span>
					</td></tr>
				<tr><td>具体位置：<span style="color:#ff0000">*</span></td><td>
						<input name="louhao1" type="text" maxlength="50" value="" style="width:200px">&nbsp;
						<input name="louhao2" type="hidden" value="">				
				</td></tr>
				<tr><td>收 件 人：<span style="color:#ff0000">*</span></td><td>
					<input name="name" type="text" maxlength="5">
					<label><input name="sex" type="radio" value="先生" checked>先生</label> 
					<label><input type="radio" name="sex" value="小姐">小姐</label><span class="txt_gary"> (如果有同事与您同姓，建议您填全名!)</span>				
				
				</td></tr>
				<tr><td>手机号码：</td><td>
					<input name="mobile" type="text" maxlength="11">
				
				</td></tr>
				<tr style="display:none"><td>固定电话：</td><td>
							  <input name="tel" type="text" maxlength="8" value="八位数字不带区号" onFocus="if(this.value=='八位数字不带区号')this.value='';" onBlur="if(this.value=='')this.value='八位数字不带区号';">
							  <input name="telext" type="text" maxlength="4" value="分机选填" onFocus="if(this.value=='分机选填')this.value='';" onBlur="if(this.value=='')this.value='分机选填';">
				</td></tr>
				<tr style="display:none"><td>公司部门：</td><td>
					<input name="company" type="text" maxlength="12" value="公司名称-选填"  onFocus="if(this.value=='公司名称-选填')this.value='';" onBlur="if(this.value=='')this.value='公司名称-选填';">
					<input name="depart" type="text" maxlength="5" value="部门-选填"  onFocus="if(this.value=='部门-选填')this.value='';" onBlur="if(this.value=='')this.value='部门-选填';">				
				</td></tr>
				</table>
				</td></tr>
			</table>
			<table class="x_sel1" id="id_edit_infodiv" style="display:none">
				<tr><td style="color:#0099FF" width="150px" valign="top"><input type="radio" name="area_select" id="id_new_edit_radior">编辑信息</td>
				<td valign="top" id="id_info_editcol">
				</td></tr>
				<tr><td style="color:#0099FF" width="150px" valign="top"></td>
				<td valign="top"><input type="button" name="btn_saveinfo" value="保 存" class="button4" onclick="jscartlist.info_save();">&nbsp;&nbsp;<input type="button" name="btn_saveinfo" value="取 消" class="button4x" onclick="jscartlist.info_cancel(true);"></td></tr>
			</table>
		</td>
	</tr></table>
	<table class="tabact">
		<tr><td class="col1" valign="top">送餐时间(<font style="color:#ff0000">*</font>)</td>
		<td>
		<select name="arrive">
		<option value="0">订单确认成功后，30分钟之内送到</option>
		<!--<?php if(count($cart_list["arrivetime"])>0){?>
			<option value="">请选择您期望外卖送达的时间</option>
			<?php foreach($cart_list["arrivetime"] as $item=>$key){ ?>
			<option value="<?php echo $item;?>"><?php echo $key;?></option>
			<?php }?>
		<?php } else { ?>
			<option value="">今天送餐已结束，明天再来吧</option>
		<?php }?>-->
		</select>
		</td></tr>
	</table>
	<table class="tabact">
		<tr><td class="col1" valign="top">支付方式(<font style="color:#ff0000">*</font>)</td>
		<td>
		<?php if(in_array('afterpayment',$paymethod)){?><label><input type="radio" name="paymethod" value="afterpayment" checked>餐到付款</label><br><?php }?>
		<?php if(in_array('repayment',$paymethod)){?><label><input type="radio" name="paymethod" id="id_repayment" value="repayment"<?php if($user_repayment<$cart_list['price']){?> disabled<?php }?>>预付款支付</label>&nbsp;&nbsp;<span style="color:#888888">您当前预付款为 <?php echo $user_repayment;?></span> <span id="id_repayment_beta" style="color:#888888"><?php if($user_repayment<$cart_list['price']){?>不够支付<?php }?></span><br><?php }?>
		<?php if(in_array('onlinepayment',$paymethod)){?>
			<?php foreach($arr_pay as $item => $key){ ?>
				<?php if(!empty($key['state'])){?>
					<label><input type="radio" name="paymethod" value="<?php echo $item;?>"><?php echo $key['fields']['title'];?></label><br>
				<?php }?>
			<?php }?>
		<?php }?></td></tr>
	</table>
	<table class="tabact" style="display:none">
		<tr><td class="col1" valign="top">索取发票</td>
		<td>
		<select name="ticket" id="id_ticket" style="margin-right:3px; width:252px" onchange="jscartlist.score_refresh(1)">
		<?php foreach($cart_list["ticket"] as $item=>$key){ ?>
		<option value="<?php echo $item;?>"><?php echo $key;?></option>
		<?php }?>
		</select></td></tr>
	</table>
	<table class="tabact">
		<tr><td class="col1" valign="top">备注</td>
		<td><textarea name="beta" cols='50' rows='2'></textarea></td></tr>
	</table>
	<div class="confirm">
	<?php if($this_login_user->uid>0){?>
		<input type="button" name="btn_saveinfo" value="提交订单" class="button3" onclick="jscartlist.submit(0);">
	<?php } else { ?>
		<input type="button" name="btn_saveinfo" value="请先登录" class="button3" onclick="jsheader.showlogin();">
	<?php }?>
	</div>
</form>
</div>
<script src="/webcss/default/cartlist.js"></script>
<script>
<?php if($this_login_user->uid<1){?>
jsheader.showlogin();
<?php }?>
kj.onload(function(){
	jscartlist.shopid = 0;
	jscartlist.ordernum = <?php if(empty($cart_list['num'])){?>0<?php } else { ?><?php echo $cart_list['num'];?><?php }?>;
	jscartlist.shop_minleast = kj.toint("<?php echo $dispatch_min_price;?>");
	jscartlist.minleast = jscartlist.shop_minleast;

	jscartlist.score = <?php echo $score_total;?>;//当前积分
	jscartlist.act_list = <?php if(count($shop_act)>0){?><?php echo fun_format::json($shop_act);?><?php } else { ?>[]<?php }?>;//时间条件优惠金额
	jscartlist.arealist = <?php echo fun_format::json($areainfo["list"]);?>;//json格式，指定id包函的子地区
	jscartlist.areainfo = <?php echo fun_format::json($areainfo["area"]);?>;//对应id地区详细信息
	jscartlist.depth = <?php echo $areainfo["depth"];?>;//当前地区深度
	jscartlist.init(thisdata);
	jscartlist.refresh_price();
});
</script>
<?php include cls_resolve::on_resolve('/default\/footer')?>
</body>
</html>