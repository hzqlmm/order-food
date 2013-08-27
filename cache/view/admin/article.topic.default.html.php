<?php include cls_resolve::on_resolve('/admin\/header')?>
<div class="pMenu" id="id_pMenu">
	<li class="sel" onclick="admin.act('');">管理</li>
	<li onclick="master_open({id:'add_config',title:'添加频道',url:'<?php echo fun_get::url(array('app_act'=>'edit','id'=>0));?>',w:500});" class="x_btn">添加</li>
	<li class="fdpic" onclick="master_open({url:'common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&key=article.topic&filename=article',title:'设置字段',w:400});">&nbsp;</li>
</div>
<div class="pMain" id="id_main">
<div class="pTableBox" id="id_table_box">
	<div class='pTableTit' id="id_table_title">
		<li><span class="x_tit" style="width:25px">&nbsp;</span></li>
		<?php foreach($arr_list["tabtit"] as $item){ ?>
			<li><span class="x_tit"<?php if($item["w"]>0){?> style="width:<?php echo $item["w"];?>px"<?php }?> onclick="admin.table.list1.sort('<?php echo $item['key'];?>')"><?php echo $item["name"];?>
			<?php if(isset($arr_list['sort'][$item['key']])){?><img src="/webcss/admin/images/sort_<?php echo $arr_list['sort'][$item['key']];?>.png"><?php }?></span><span class="x_split"></span></li>
		<?php }?>
		<li><span class="x_tit" style="cursor:none;width:100px">操作</span></li>
	</div>
	<div class="pTableList" id="id_table_list">
		<div class='pTable' id="id_table">
			<?php foreach($arr_list['list'] as $item){ ?>
			<div class='pTabRow'>
				<li><input type='checkbox' name='selid[]' value="<?php echo $item['topic_id'];?>"></li>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<li><?php if(empty($item[$field])){?>&nbsp;<?php } else { ?><?php echo $item[$field];?><?php }?></li>
				<?php }?>
				<li>
					<?php if($this_limit->chk_act("edit")){?><a href="?app_module=article&app=topic&app_act=showarticle&url_topic_id=<?php echo $item['topic_id'];?>">打开</a><?php }?>
					<?php if($this_limit->chk_act("edit")){?><a href="javascript:master_open({id:<?php echo $item['topic_id'];?>,title:'编辑配置',w:500});">编辑</a><?php }?>
					<?php if($this_limit->chk_act("del")){?><a href="javascript:admin.ajax_url({app_act:'delete',id:'<?php echo $item['topic_id'];?>'});">删除</a><?php }?>
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
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_resize&key=article.topic&filename=article&index=" + this.fieldsindex + "&w=" + lng_w , function(data){
		//alert(data);
	});
}
admin.table.list1.sort = function(key) {
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=sort&key=article.topic&filename=article&sortby=" + key , function(data){
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