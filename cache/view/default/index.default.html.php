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
<div class="left mg1 w1" id="id_left">
	<?php echo fun_kj::get_ads(1);?>
	<?php if(count($arr_menu["tj"])>0){?>
	<div class="title w1"><h1>新品/推荐</h1></div>
	<img src="/webcss/default/images/dividing-line-740.png">
	<div class="list w1">
		<?php foreach($arr_menu["tj"] as $menu){ ?>
		<ul>
			<li onmouseover="jsshop.menumouseover(this);" onmouseout="jsshop.menumouseout(this);" class="info">
				<a href="javascript:jsshop.cart_add({id:'<?php echo $menu['menu_id'];?>',name:'<?php echo $menu['menu_title'];?>',pic:'<?php echo $menu['menu_pic_small'];?>',price:'<?php echo $menu['menu_price'];?>',type:'<?php echo $menu['menu_type'];?>'});">￥<?php echo $menu['price_int'];?>  <?php echo $menu['menu_title'];?>
				</a>
			</li>
<!-- 			<li><h2><a href="javascript:jsshop.cart_add({id:'<?php echo $menu['menu_id'];?>',name:'<?php echo $menu['menu_title'];?>',pic:'<?php echo $menu['menu_pic_small'];?>',price:'<?php echo $menu['menu_price'];?>',type:'<?php echo $menu['menu_type'];?>'});"><?php echo $menu['menu_title'];?></a></h2></li> -->
			<li class="pic"><img src="<?php echo $menu['menu_pic'];?>" onclick="jsshop.cart_add({id:'<?php echo $menu['menu_id'];?>',name:'<?php echo $menu['menu_title'];?>',pic:'<?php echo $menu['menu_pic_small'];?>',price:'<?php echo $menu['menu_price'];?>',type:'<?php echo $menu['menu_type'];?>'});"></li>
			<!-- <li><span style="float:left"><font style="color:#CC0000;font-size:24px">￥<?php echo $menu['price_int'];?></font><font style="color:#CC0000;font-size:16px">.<?php echo $menu['price_float'];?></font><br><a href="javascript:jsshop.comment(<?php echo $menu['menu_id'];?>);">评论：<?php echo $menu['menu_comment_num'];?>条</a></span><span style="float:right"><input type="button" name="btnse" value="订一份" class="button1" onclick="jsshop.cart_add({id:'<?php echo $menu['menu_id'];?>',name:'<?php echo $menu['menu_title'];?>',pic:'<?php echo $menu['menu_pic_small'];?>',price:'<?php echo $menu['menu_price'];?>',type:'<?php echo $menu['menu_type'];?>'});"></span></li> -->
		</ul>
		<?php }?>
	</div>
	<?php }?>
	<div class="w1" style="float:left" id="id_grouplist">
	<?php foreach($arr_menu["list"] as $item){ ?>
	<div class="title w1"><h1><?php echo $item["name"];?><a name="hash_price_<?php echo $item['id'];?>"></a></h1></div>
	<img src="/webcss/default/images/dividing-line-740.png">
	<div class="list2 w1">
	<?php foreach($item['list'] as $menu){ ?>
		<li onmouseover="jsshop.mouseover('<?php echo $menu['menu_id'];?>');" onmouseout="jsshop.mouseout('<?php echo $menu['menu_id'];?>');" onclick="jsshop.cart_add({id:'<?php echo $menu['menu_id'];?>',name:'<?php echo $menu['menu_title'];?>',pic:'<?php echo $menu['menu_pic_small'];?>',price:'<?php echo $menu['menu_price'];?>',type:'<?php echo $menu['menu_type'];?>'});" id="id_li_<?php echo $menu['menu_id'];?>">
		<span class="x_nosel" id="id_nosel_<?php echo $menu['menu_id'];?>">&nbsp;</span>
		<span class="tit" id="id_title_<?php echo $menu['menu_id'];?>"><?php echo $menu['menu_title'];?></span>
		<?php if($index_group!='price'){?><span class="price">￥<?php echo $menu['price_int'];?><?php if(intval($menu['price_float'])>0){?>.<?php echo $menu['price_float'];?><?php }?></span><?php }?></li>
		<li class="xcomment">(<a href="javascript:jsshop.comment(<?php echo $menu['menu_id'];?>);"><font color="#FC0062"><?php echo $menu['menu_comment_num'];?></font>评论</a>)</li>
	<?php }?>
	</div>
	<?php }?>
	</div>
</div>
<div class="right" id="id_right">
	<li class="pic"><span><img src="<?php echo cls_config::get("shop_logo","view");?>">&nbsp;</span></li>
    <img src="/webcss/default/images/dividing-line-240.png">
    <li class="info"><span class='xtit'><?php echo cls_config::get("shop_name","view");?></span><span><font color="#fc0062">餐品：</font><?php echo $shop_menunum;?></span><span><a href="javascript:jsheader.comment_shop()" style="color:#fc0062"><font color="#fc0062">评论：</font><?php echo $shop_commentnum;?></span></a></li>
	<?php if(!empty($opentime['cont'])){?>
	<li class="tit"><?php echo $opentime['title'];?></li>
	<img src="/webcss/default/images/dividing-line-240.png">
	<li><?php echo $opentime['cont'];?></li>
	<?php }?>
	<li class="tit">活动公告</li>
	<img src="/webcss/default/images/dividing-line-240.png">
	<li class="li">
	<?php foreach($arr_activitie as $item){ ?>
	<a href="?app_act=news.view&id=<?php echo $item['article_id'];?>"><?php echo $item['article_title'];?></a>
	<?php }?>
	</li>
	<?php if(!empty($shopintro['cont'])){?>
	<li class="tit"><?php echo $shopintro['title'];?></li>
	<img src="/webcss/default/images/dividing-line-240.png">
	<li><?php echo $shopintro['cont'];?></li>
	<?php }?>
</div>
<div class='cart_menu' id="id_cart_menu" style="display:none">
	<div class="x_top">
		<li class="x_1" onclick="jsshop.showcart_fixed(kj.obj('#id_btn_fixed'));" style="cursor:pointer">我的饭盒</li>
		<li class="x_4" onclick="jsshop.showcart_fixed(this);" id="id_btn_fixed"></li>
		<li class="x_5" onclick="jsshop.cart_submit();">立即结算</li>
		<li class="x_3">￥0</li>
		<li class="x_2">共 0  份，合计</li>
	</div>
	<div class="x_tit" onmouseover="jsshop.showcart(1);"><li><span class="col1">餐品</span><span class="col2">单价</span><span class="col3">数量</span><span class="col4">小计</span><span class="col5 x_clear" onclick="jsshop.clear()">清空</span></li></div>
	<div class="x_list" id="id_cart_box"></div>
</div>
<div id="labelright" style='display:none'>
	<li class="up"><a href="javascript:jsshop.top();"><img src="/webcss/default/images/arrow.png"></a></li>
	<li class="group" onmouseover="jsshop.menugroup(this);" onmouseout="kj.hide('#menugroup');"><a href="javascript:void(0)"><img src="/webcss/default/images/label1.png"><br>分类</a></li>
</div>
<div id="menugroup" style="display:none" onmouseover="kj.show(this);" onmouseout="kj.hide(this);">
	<div>
	<li class="tit">按价格</li>
	<li>
	<?php foreach($arr_menu["price"] as $item){ ?>
	<a href="javascript:jsshop.hash('hash_price_<?php echo $item['id'];?>');"><?php echo $item['name'];?></a>
	<?php }?>
	</li>
	<li class="tit">按分类</li>
	<li>
	<?php foreach($arr_menu["group"] as $item){ ?>
	<a href="javascript:jsshop.hash('hash_group_<?php echo $item['id'];?>');"><?php echo $item['name'];?></a>
	<?php }?>
	</li>
	<li class="tit">排序</li>
	<li>
	<a href="javascript:jsshop.sort('price');">按价格</a>
	<a href="javascript:jsshop.sort('comment');">按评分</a>
	<a href="javascript:jsshop.sort('sold');">按销量</a>
	</li>
	</div>
</div>
<?php include cls_resolve::on_resolve('/default\/footer')?>
<div id="cart_menu_opacity_bg" class="cart_menu_opacity_bg">&nbsp;</div>
<script src="/webcss/default/shop.default.js"></script>
<script>
kj.onload(function(){
	jsshop.mintotal = kj.toint('<?php echo $dispatch_min_price;?>');//最低起送价
	jsshop.tempurl = "/webcss/default/";
	jsshop.cart_init = function() {
		<?php foreach($arr_menu['cart'] as $cart){ ?>
			<?php foreach($cart as $menu){ ?>
				<?php if(isset($arr_menu["cart_menu"]["id_".$menu])){?>
				this.cart_add({id:'<?php echo $arr_menu["cart_menu"]["id_".$menu]['menu_id'];?>',name:'<?php echo $arr_menu["cart_menu"]["id_".$menu]['menu_title'];?>',pic:'<?php echo $arr_menu["cart_menu"]["id_".$menu]['menu_pic_small'];?>',price:'<?php echo $arr_menu["cart_menu"]["id_".$menu]['menu_price'];?>',type:'<?php echo $arr_menu["cart_menu"]["id_".$menu]['menu_type'];?>'});
				<?php }?>
			<?php }?>
		<?php }?>
	}
	jsshop.cart_init();
	jsfooter.align_height();
});
var jsfooter = new function() {
	this.align_height = function() {
		var h_left = kj.h("#id_left");
		var h_right = kj.h("#id_right");
		(h_left>h_right) ? kj.set("#id_right",'style.height' , h_left+"px") : kj.set("#id_left",'style.height' , h_right+"px");
	}
}
</script>
</body>
</html>
