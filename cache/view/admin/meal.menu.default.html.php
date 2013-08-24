<?php include cls_resolve::on_resolve('/admin\/header')?>
<div class="pMenu" id="id_pMenu">
	<input type="hidden" value="<?php echo $get_url_type;?>" name="url_type" id="id_url_type">
	<?php foreach($arr_menu_type as $item=>$key){ ?>
		<li <?php if($app_act!='dellist' && $key==$get_url_type){?> class="sel"<?php }?> onclick="kj.set('#id_url_type','value','<?php echo $key;?>');kj.set(':app_act','value','');admin.refresh();"><?php echo $item;?></li>
	<?php }?>
	<li <?php if($app_act=='dellist'){?> class="sel"<?php }?> onclick="admin.act('dellist');">回收站</li>
	<li onclick="master_open({id:'add_config',title:'添加菜谱',url:'<?php echo fun_get::url(array('app_act'=>'edit','id'=>0));?>',w:600});" class="x_btn">添加</li>
	<li onclick="admin.menu_display(0);" class = "x_btn">查找</li>
	<li class="fdpic" onclick="master_open({url:'common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&key=meal.menu&filename=meal',title:'设置字段',w:400});">&nbsp;</li>
</div>
<div class="btnMenuDiv" id="id_btnMenuDiv"<?php if($arr_list['issearch']==0){?> style="display:none"<?php }?>>
<li>分&nbsp;组：<?php echo $s_group_html;?></li>
<li>推&nbsp;荐：<select name="s_tj"><option value="0"></option><option value="1"<?php if(fun_get::get("s_tj")=='1'){?> selected<?php }?>>是</option><option value="-1"<?php if(fun_get::get("s_tj")=='-1'){?> selected<?php }?>>否</option></select></li>
<li>状&nbsp;态：<select name="s_state">
<option value="-999"></option>
<?php foreach($arr_state as $item=>$key){ ?>
	<option value="<?php echo $key;?>"<?php if(fun_get::get("s_state")==$key){?> selected<?php }?>><?php echo $item;?></option>
<?php }?>
</select></li>

<li>价格：<input type="text" name="s_price1" value="" style="width:50px">&nbsp;至&nbsp;<input type="text" name="s_price2" value="" style="width:50px"></li>
<li>关 键 字：<input type="text" id="s_key" name="s_key" value="<?php echo fun_get::get('s_key');?>">　<input type="button" name="btn_s_ok" value="查找" class="pBtn" onclick="admin.search();"> 　<input type="button" name="btn_s_clear" value="清空" class="pBtn" onclick="admin.clear_search();"></li>
</table>
</div>
<div class="pMain" id="id_main">
<div class="pTableBox" id="id_table_box">
	<div class='pTableTit' id="id_table_title">
		<li><span class="x_tit" style="width:45px">&nbsp;</span></li>
		<?php foreach($arr_list["tabtit"] as $item){ ?>
			<li><span class="x_tit"<?php if($item["w"]>0){?> style="width:<?php echo $item["w"];?>px"<?php }?> onclick="admin.table.list1.sort('<?php echo $item['key'];?>')"><?php echo $item["name"];?>
			<?php if(isset($arr_list['sort'][$item['key']])){?><img src="/webcss/admin/images/sort_<?php echo $arr_list['sort'][$item['key']];?>.png"><?php }?></span><span class="x_split"></span></li>
		<?php }?>
		<li><span class="x_tit" style="cursor:none;width:150px">操作</span></li>
	</div>
	<div class="pTableList" id="id_table_list">
		<div class='pTable' id="id_table">
			<?php foreach($arr_list['list'] as $item){ ?>
			<div class='pTabRow'>
				<li><input type='checkbox' name='selid[]' value="<?php echo $item['menu_id'];?>"><span class="css_sort" style="display:none"><input type='text' name='sortval_<?php echo $item['menu_id'];?>' value="<?php echo $item['menu_sort'];?>" style="width:20px" onfocus="thisjs.sortfocus(this);"></span></li>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<li><?php if(empty($item[$field])){?>&nbsp;<?php } else { ?><?php echo $item[$field];?><?php }?></li>
				<?php }?>
				<li>
				<?php if($app_act!='dellist'){?>
					<?php if($this_limit->chk_act("edit")){?>
					<input type="button" name="btnedit" value="编辑" onclick="master_open({id:<?php echo $item['menu_id'];?>,title:'编辑菜谱',w:600});" class="pBtn">
					<?php }?>
					<?php if($this_limit->chk_act("del")){?><input type="button" name="btnedit" value="删除" onclick="admin.ajax_url({app_act:'del',id:'<?php echo $item['menu_id'];?>'});" class="pBtn2"><?php }?>
				<?php } else { ?>
					<?php if($this_limit->chk_act("reback")){?><input type="button" name="btnedit" value="还原" onclick="admin.ajax_url({app_act:'reback',id:'<?php echo $item['menu_id'];?>'});" class="pBtn"><?php }?>
					<?php if($this_limit->chk_act("delete")){?>&nbsp;&nbsp;<input type="button" name="btnedit" value="彻底删除" onclick="admin.ajax_url({app_act:'delete',id:'<?php echo $item['menu_id'];?>'});" class="pBtn2"><?php }?>
				<?php }?>
				</li>
			</div>
			<?php }?>
		</div>
	</div>
</div>
</div>
<div class="pPage" id="id_pPage">
<?php echo $arr_list['pagebtns'];?>
</div>
<div id="id_selact_mode_day" style="display:none;position:absolute;width:100px;background:#fff;border:1px #cccccc solid">
<div style="float:left;width:100%;text-align:left;overflow-y:scroll;height:300px">
<?php for($i=1;$i<32;$i++){ ?>
<li style="float:left;width:90%"><label><input type="checkbox" name="mode_day[]" value="<?php echo $i;?>"><?php echo $i;?>号</label></li>
<?php }?>
</div>
</div>
<div id="id_selact_mode_week" style="display:none;position:absolute;width:100px;background:#fff;border:1px #cccccc solid">
<div style="float:left;width:100%;text-align:left;overflow:hidden;height:170px">
<li><label><input type="checkbox" name="mode_weekday[]" value="1" checked>周一</label></li>
<li><label><input type="checkbox" name="mode_weekday[]" value="2" checked>周二</label></li>
<li><label><input type="checkbox" name="mode_weekday[]" value="3" checked>周三</label></li>
<li><label><input type="checkbox" name="mode_weekday[]" value="4" checked>周四</label></li>
<li><label><input type="checkbox" name="mode_weekday[]" value="5" checked>周五</label></li>
<li><label><input type="checkbox" name="mode_weekday[]" value="6" checked>周六</label></li>
<li><label><input type="checkbox" name="mode_weekday[]" value="0" checked>周天</label></li>
<li><label><input type="checkbox" name="mode_holiday" value="1" checked>节假日</label></li>
</div>
</div>

<div class="pFootAct" id="id_pFootAct">
<li>
<label><input type='checkbox' name='selall' value='1'>全选</label>　
<select name="selact" onchange="thisjs.selact(this.value)">
	<option value="">--操作--</option>
		<?php if($this_limit->chk_act("mode")){?><option value="mode">提供模式</option><?php }?>
		<?php if($this_limit->chk_act("group")){?><option value="group">分组</option><?php }?>
		<?php if($this_limit->chk_act("state")){?><option value="state">状态</option><?php }?>
		<?php if($this_limit->chk_act("tj")){?><option value="tj">推荐</option><?php }?>
		<?php if($this_limit->chk_act("sort")){?><option value="sort">排序</option><?php }?>
		<?php if($this_limit->chk_act("del")){?><option value="del">删除</option><?php }?>
	</select>&nbsp;<span id="id_selact_group" style="display:none">
	<?php echo $group_html;?>
	</span>&nbsp;<span id="id_selact_state" style="display:none">
	<select name="state_val">
	<?php foreach($arr_state as $item=>$key){ ?>
	<option value="<?php echo $key;?>"><?php echo $item;?></option>
	<?php }?>
	</select>
	</span>
<span id="id_selact_tj" style="display:none">
	<select name="tj_val">
	<option value="1">是</option>
	<option value="0">否</option>
	</select>
	</span>
	<span id="id_selact_mode" style="display:none">
	<label><input type="radio" name="mode_val" value="0" checked onclick="kj.hide('#id_selact_mode_week');kj.hide('#id_selact_mode_day');">每天</label>&nbsp;&nbsp;<label><input type="radio" name="mode_val" value="2" onclick="kj.hide('#id_selact_mode_week');kj.hide('#id_selact_mode_day');">自定义</label>&nbsp;&nbsp;<label><input type="radio" name="mode_val" value="1" onclick="thisjs.show_week(this);">按星期</label>&nbsp;&nbsp;<label><input type="radio" name="mode_val" value="3" onclick="thisjs.show_day(this);">按日期</label>
	</span>
	&nbsp;<input type="button" name="dosubmit" value="执行" onclick="admin.selact();" class="pBtn">
</li>
</div>
<script src="/webcss/admin/admin.table.js"></script>
<script>

//初始化表格控件
kj.onload(function(){
admin.table.list1.init('#id_table_title' , '#id_table');
});
//自动保存
admin.table.list1.save_resize = function() {
	var lng_w = (kj.w(this.field));
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_resize&filename=meal&key=meal.menu&index=" + this.fieldsindex + "&w=" + lng_w , function(data){
		//alert(data);
	});
}
admin.table.list1.sort = function(key) {
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=sort&key=meal.menu&filename=meal&sortby=" + key , function(data){
			var obj_data=kj.json(data);
			if(!obj_data.isnull) {
				admin.refresh();
			}
	});
}
//当前页面js对象
var thisjs = new function() {
	this.selact = function(val) {
		kj.obj("#id_selact_state").style.display = (val == "state") ? "" : "none";
		kj.obj("#id_selact_group").style.display = (val == "group") ? "" : "none";
		kj.obj("#id_selact_mode").style.display = (val == "mode") ? "" : "none";
		kj.obj("#id_selact_tj").style.display = (val == "tj") ? "" : "none";
		if(val=='sort') {
			kj.show(".css_sort");
		} else {
			kj.hide(".css_sort");
		}
	}
	this.show_day = function(obj) {
		kj.hide('#id_selact_mode_week');
		kj.show("#id_selact_mode_day");
		var offset = kj.offset(obj);
		var o = kj.obj("#id_selact_mode_day");
		o.style.top = (offset.top-300)+"px";
		o.style.left = offset.left+"px";
	}
	this.show_week = function(obj) {
		kj.show('#id_selact_mode_week');
		kj.hide("#id_selact_mode_day");
		var offset = kj.offset(obj);
		var o = kj.obj("#id_selact_mode_week");
		o.style.top = (offset.top-170)+"px";
		o.style.left = offset.left+"px";
	}
	this.sortfocus = function(o) {
		o = kj.parent(o,'li');
		var obj = kj.obj(":selid[]" , o);
		if(obj.length>0 && obj[0].checked==false) {
			kj.event(obj[0],'click');
		}
	}
}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>