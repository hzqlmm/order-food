<div class="title w1"><h1>排序<a name="hash_sort"></a></h1>
<span<?php if($sortby=='price'){?> class="sort_<?php echo $sortval;?>"<?php }?> style="margin-left:20px" onclick="jsshop.sort('price','<?php if($sortval=='asc'){?>desc<?php } else { ?>asc<?php }?>');">按价格</span><span<?php if($sortby=='comment'){?> class="sort_<?php echo $sortval;?>"<?php }?> onclick="jsshop.sort('comment','<?php if($sortval=='asc'){?>desc<?php } else { ?>asc<?php }?>');">按评分</span><span <?php if($sortby=='sold'){?>class="sort_<?php echo $sortval;?>"<?php }?> onclick="jsshop.sort('sold','<?php if($sortval=='asc'){?>desc<?php } else { ?>asc<?php }?>');">按销量</span></div>
<div class="list2 w1">
<img src="/webcss/default/images/dividing-line-740.png">
<?php foreach($arr_menu['list'] as $menu){ ?>
	<li onmouseover="jsshop.mouseover('<?php echo $menu['menu_id'];?>');" onmouseout="jsshop.mouseout('<?php echo $menu['menu_id'];?>');" onclick="jsshop.cart_add({id:'<?php echo $menu['menu_id'];?>',name:'<?php echo $menu['menu_title'];?>',pic:'<?php echo $menu['menu_pic_small'];?>',price:'<?php echo $menu['menu_price'];?>',type:'<?php echo $menu['menu_type'];?>'});" id="id_li_<?php echo $menu['menu_id'];?>">
	<span class="x_nosel" id="id_nosel_<?php echo $menu['menu_id'];?>">&nbsp;</span>
	<span class="tit" id="id_title_<?php echo $menu['menu_id'];?>"><?php echo $menu['menu_title'];?></span>
	<span class="price">￥<?php echo $menu['price_int'];?><?php if(intval($menu['price_float'])>0){?>.<?php echo $menu['price_float'];?><?php }?></span></li>
	<li class="xcomment"><?php if($sortby=='sold'){?>(销量：<font color="#ff0000"><?php echo $menu['menu_sold'];?></font>)<?php } else { ?>(<a href="javascript:jsshop.comment(<?php echo $menu['menu_id'];?>);"><font color="#FC0062"><?php echo $menu['menu_comment_num'];?></font>评论</a>)<?php }?></li>
<?php }?>
</div>
