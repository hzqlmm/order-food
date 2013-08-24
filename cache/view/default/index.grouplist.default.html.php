<?php foreach($arr_menu["list"] as $item){ ?>
<div class="title w1"><h1><?php echo $item["name"];?><a name="hash_<?php echo $index_group;?>_<?php echo $item['id'];?>"></a></h1></div>
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