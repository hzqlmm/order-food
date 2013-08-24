<?php include cls_resolve::on_resolve('/admin\/header')?>
<style>
.me_info{float:left;width:90%;text-align:left;padding-left:5px;margin-top:5px;font-weight:bold}
.me_beta{float:left;width:90%;text-align:left;padding-left:35px;margin-bottom:10px;margin-top:5px;color:#888888}
.me_content{float:left;width:90%;text-align:left;padding:8px 0px 5px 30px}
.me_act{float:left;width:90%;text-align:left;padding:8px;border-bottom:1px #cccccc solid}
.me_li{float:left;width:90%;padding-top:5px}
.me_li li{float:left;clear:both;padding:5px 0px 3px 40px;text-align:left}
.me_li span{float:left;}
.me_li .x1{float:left;width:100px;text-align:right}
</style>
<div class="pMenu" id="id_pMenu">
	<li>管理</li>
	<li onclick="admin.menu_display(0);" class="x_btn">查找</li>
</div>
<div class="btnMenuDiv" id="id_btnMenuDiv"<?php if($arr_list['issearch']==0){?> style="display:none"<?php }?>>
<li>时间：<input type="text" id="s_time1" name="s_time1" value="<?php echo fun_get::get('s_time1');?>" class='pTxtDate' onfocus="new Calendar().show(this);"> 到 <input type="text" name="s_time2" id="s_time2" value="<?php echo fun_get::get('s_time2');?>" class='pTxtDate' onfocus="new Calendar().show(this);"></li>
<li>关 键 字：<input type="text" id="s_key" name="s_key" value="<?php echo fun_get::get('s_key');?>" class='pTxt1'>　<input type="button" name="btn_s_ok" value="查找" class="pBtn" onclick="admin.search();"> 　<input type="button" name="btn_s_clear" value="清空" class="pBtn" onclick="admin.clear_search();"></li>
</div>
<div class="pMain" id="id_main">
	<?php foreach($arr_list['list'] as $key=>$item){ ?>
	<div class="me_info"><input type="checkbox" name="selid[]" value="<?php echo $item["comment_order_id"];?>"> 用户：<?php echo $item['user_name'];?>&nbsp;&nbsp;&nbsp;&nbsp;时间：<?php echo $item["addtime"];?></div>
	<div class="me_li">
	<?php foreach($item['menu_comments'] as $menu){ ?>
	<li><span class="x1"><?php echo $arr_list['menu']['id_'.$menu['comment_menu_id']];?>：</span><span><?php if($menu['comment_val']==0){?><img src="/webcss/admin/images/comment2.png"><?php } else if($menu['comment_val']==1) { ?><img src="/webcss/admin/images/comment1.png"><?php } else { ?><img src="/webcss/admin/images/comment3.png"><?php }?></span></li>
	<?php }?>
	</div>
	<div class="me_content"><?php echo $item["comment_beta"];?></div>
	<div class="me_act" onmousedown="frmSet('id','')"><a href="javascript:admin.ajax_delete(<?php echo $item['comment_order_id'];?>);">删除</a></div>
	<?php }?>
</div>
<div class="pFootAct" id="id_pFootAct">
	<li>
	<label><input type='checkbox' name='selall' value='1'>全选</label>　
	<select name="selact" onchange="thisjs.selact(this.value)">
	<option value="">--操作--</option>
	<?php if($this_limit->chk_act("del")){?><option value="delete">删除</option><?php }?>
	</select>&nbsp;
	&nbsp;<input type="button" name="dosubmit" value="执行" onclick="admin.selact();" class="pBtn">
	</li>
</div>
<?php include cls_resolve::on_resolve('/admin\/footer')?>