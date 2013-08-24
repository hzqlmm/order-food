<?php include cls_resolve::on_resolve('/admin\/header')?>
<input type="hidden" value="<?php echo $this_pid;?>" name="url_pid" id="id_url_pid">
<div class="pMenu" id="id_pMenu">
	<li class="sel">管理</li>
	<li onclick="admin.table.row_insert()" class="x_btn">添加</li>
</div>
<div class="pMain_1" id="id_pMain_1">
	<div class="pPath">路径：<a href="javascript:kj.set('#id_url_pid','value','0');admin.refresh();">顶级</a><?php echo $this_path;?></div>
</div>
<div class="pMain" id="id_main">
<div class="pTableBox" id="id_table_box">
	<div class='pTableTit' id="id_table_title">
		<li><span class="x_tit" style="width:35px">ID</span></li>
		<?php foreach($arr_list["tabtit"] as $item){ ?>
			<li><span class="x_tit"<?php if($item["w"]>0){?> style="width:<?php echo $item["w"];?>px"<?php }?> onclick="admin.table.list1.sort('<?php echo $item['key'];?>')"><?php echo $item["name"];?>
			<?php if(isset($arr_list['sort'][$item['key']])){?><img src="/webcss/admin/images/sort_<?php echo $arr_list['sort'][$item['key']];?>.png"><?php }?></span><span class="x_split"></span></li>
		<?php }?>
		<li><span class="x_tit" style="cursor:none;width:80px">操作</span></li>
	</div>
	<div class="pTableList" id="id_table_list">
		<div class='pTable' id="id_table">
			<div class='pTabRow' style="display:none">
				<li><input type="hidden" name="area_id[]" value=""></li>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<li><input type="text" name="<?php echo $field;?>[]" value="" class="autosize"></li>
				<?php }?>
				<li>
					<a href="javascript:kj.remove('#THISID');">删除</a>
				</li>
			</div>
			<?php foreach($arr_list['list'] as $item){ ?>
			<div class='pTabRow' id="id_tabrow_<?php echo $item['area_id'];?>">
				<li><input type='hidden' name='area_id[]' value="<?php echo $item['area_id'];?>"><?php echo $item['area_id'];?></li>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<li><input type="text" name="<?php echo $field;?>[]" value="<?php echo $item[$field];?>" class="autosize"></li>
				<?php }?>
				<li>
				<a href="javascript:kj.set('#id_url_pid','value','<?php echo $item['area_id'];?>');admin.refresh();">下级</a>
				<?php if($this_limit->chk_act("delete")){?>&nbsp;&nbsp;<a href="javascript:thisjs.remove('<?php echo $item['area_id'];?>');">删除</a><?php }?>
				</li>
			</div>
			<?php }?>
		</div>
	</div>
</div>
</div>
<div class="pFootAct" id="id_pFootAct">
	<li><?php if($this_limit->chk_act("save")){?><input type="button" name="dosubmit" value="保存" onclick="admin.frm_ajax('save');" class="pBtn"><?php }?></li>
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
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_resize&filename=sys&key=sys.area&index=" + this.fieldsindex + "&w=" + lng_w , function(data){
		//alert(data);
	});
}
admin.table.list1.sort = function(key) {
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=sort&key=sys.area&filename=sys&sortby=" + key , function(data){
			var obj_data=kj.json(data);
			if(!obj_data.isnull) {
				admin.refresh();
			}
	});
}
//当前页面js对象
var thisjs = new function() {
	this.remove = function(id) {
		admin.ajax_delete( id , true , function() {kj.remove("#id_tabrow_" + id);} );
	}
}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>