<?php include cls_resolve::on_resolve('/admin\/header')?>
<div class="pMenu" id="id_pMenu">
	<li class="sel" onclick="admin.act('');">管理</li>
	<li onclick="master_open({id:'add_config',title:'添加频道',url:'<?php echo fun_get::url(array('app_act'=>'edit','id'=>0));?>',w:500});" class="x_btn">添加</li>
	<li class="fdpic" onclick="master_open({url:'common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&key=article.channel&filename=article',title:'设置字段',w:400});">&nbsp;</li>
</div>
<div class="pMain" id="id_main">
<div class="pTableBox" id="id_table_box">
	<div class='pTableTit' id="id_table_title">
		<li><span class="x_tit" style="width:25px">&nbsp;</span></li>
		<?php foreach($arr_list["tabtit"] as $item){ ?>
			<li><span class="x_tit"<?php if($item["w"]>0){?> style="width:<?php echo $item["w"];?>px"<?php }?> onclick="admin.table.list1.sort('<?php echo $item['key'];?>')"><?php echo $item["name"];?>
			<?php if(isset($arr_list['sort'][$item['key']])){?><img src="/webcss/admin/images/sort_<?php echo $arr_list['sort'][$item['key']];?>.png"><?php }?></span><span class="x_split"></span></li>
		<?php }?>
		<li><span class="x_tit" style="cursor:none;width:150px">操作</span></li>
	</div>
	<div class="pTableList" id="id_table_list">
		<div class='pTable' id="id_table">
			<?php foreach($arr_list['list'] as $item){ ?>
			<?php if($this_limit->chk_article($item["channel_id"])){?>
			<div class='pTabRow'>
				<li><input type='checkbox' name='selid[]' value="<?php echo $item['channel_id'];?>"></li>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<?php if($field == 'channel_name'){?>
						<li><a href="common.php?app=article&app_act=channel&id=<?php echo $item['channel_id'];?>" target="_blank"><?php echo $item[$field];?></a></li>
					<?php } else { ?>
						<li><?php if(empty($item[$field])){?>&nbsp;<?php } else { ?><?php echo $item[$field];?><?php }?></li>
					<?php }?>
				<?php }?>
				<li>
					<input type="button" name="btnedit" value="打开" onclick="window.open('?app=article&url_channel_id=<?php echo $item['channel_id'];?>','_self');" class="pBtn">
					<?php if($this_limit->chk_act("edit")){?><input type="button" name="btnedit" value="编辑" onclick="master_open({id:<?php echo $item['channel_id'];?>,title:'编辑配置',w:500});" class="pBtn"><?php }?>
					<?php if($this_limit->chk_act("del")){?><input type="button" name="btnedit" value="删除" onclick="admin.ajax_url({app_act:'delete',id:'<?php echo $item['channel_id'];?>'});" class="pBtn2"><?php }?>
				</li>
			</div>
			<?php }?>
			<?php }?>
		</div>
	</div>
</div>
</div>
<div class="pPage" id="id_pPage">
<?php echo $arr_list['pagebtns'];?>
</div>
<div class="pFootAct" id="id_pFootAct">
<li>
<label><input type='checkbox' name='selall' value='1'>全选</label>　
<select name="selact" onchange="thisjs.selact(this.value)">
	<option value="">--操作--</option>
	<?php if($this_limit->chk_act("state")){?><option value="state">状态设置</option><?php }?>
	<?php if($this_limit->chk_act("delete")){?><option value="delete">删除</option><?php }?>
	</select>&nbsp;<span id="id_selact_state" style="display:none">
	<select name="state_val">
	<?php foreach($arr_list['state'] as $item=>$key){ ?>
	<option value="<?php echo $key;?>"><?php echo $item;?></option>
	<?php }?>
	</select>
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
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_resize&key=article.channel&filename=article&index=" + this.fieldsindex + "&w=" + lng_w , function(data){
		//alert(data);
	});
}
admin.table.list1.sort = function(key) {
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=sort&key=article.channel&filename=article&sortby=" + key , function(data){
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
	}
}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>