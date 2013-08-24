<?php include cls_resolve::on_resolve('/admin\/header')?>
<div class="pMenu" id="id_pMenu">
<!--切换保留数据-->
<input type="hidden" value="<?php echo $get_url_module;?>" name="url_module">
<?php foreach($arr_module as $item=>$key){ ?>
	<li <?php if($item==$get_url_module){?> class="sel"<?php }?> onclick="kj.set(':url_module','value','<?php echo $item;?>');admin.refresh();"><?php echo $key;?></li>
<?php }?>
<li onclick="admin.act('print');">打印设置</li>
<?php if(cls_config::get("code_mode","base")==1){?>
	<li onclick="master_open({id:'add_config',title:'添加配置',url:'<?php echo fun_get::url(array('app_act'=>'edit','id'=>0));?>',w:500});" class="x_btn">添加</li>
<?php }?>
</div>
<div class="pMain" id="id_main">
<div class="pTableBox" id="id_table_box">
	<div class='pTableTit' id="id_table_title">
		<?php if(cls_config::get("code_mode","base")==1){?>
		<li><span class="x_tit" style="width:40px">排序</span><span class="x_split"></span></li>
		<?php }?>
		<?php foreach($arr_list["tabtit"] as $item){ ?>
			<li><span class="x_tit"<?php if($item["w"]>0){?> style="width:<?php echo $item["w"];?>px"<?php }?>  onclick="admin.table.list1.sort('<?php echo $item['key'];?>')"><?php echo $item["name"];?><?php if(isset($arr_list['sort'][$item['key']])){?><img src="/webcss/admin/images/sort_<?php echo $arr_list['sort'][$item['key']];?>.png"><?php }?></span><span class="x_split"></span></li>
		<?php }?>
		<?php if(cls_config::get("code_mode","base")==1){?>
		<li><span class="x_tit" style="cursor:none;width:100px">操作</span></li>
		<?php }?>
	</div>
	<div class="pTableList" id="id_table_list">
		<div class='pTable' id="id_table">
			<?php foreach($arr_list['list'] as $item){ ?>
			<input type="hidden" name="selid[]" value="<?php echo $item['config_id'];?>">
			<div class='pTabRow'>
				<?php if(cls_config::get("code_mode","base")==1){?>
				<li><input type="text" name="config_sort[]" value="<?php echo $item["config_sort"];?>" style="width:30px"></li>
				<?php }?>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<li>
					<?php if($field == 'config_val'){?>
						<?php if($item["config_type"]=="textarea"||$item["config_type"]=="array"){?>
							<textarea name='config_val[]' id='config_val[]' style='width:400px;height:120px' class="autosize"><?php echo $item["config_val"];?></textarea>
						<?php } else if($item["config_type"]=="list") { ?>
							<select name="config_val[]">
							<?php foreach($item["config_list"] as $item2=>$key2){ ?>
							<option value="<?php echo $item2;?>" <?php if($item2==$item["config_val"]){?> selected<?php }?>><?php echo $key2;?></option>
							<?php }?>
							</select>
						<?php } else if($item["config_type"]=="chk") { ?>
							<?php foreach($item["config_list"] as $item2=>$key2){ ?>
							<label><input type="checkbox" name="config_val_<?php echo $item['config_id'];?>[]" value="<?php echo $item2;?>" <?php if(in_array($item2,$item["config_val"])){?> checked<?php }?>><?php echo $key2;?></label>
							<?php }?>
							<input type="hidden" name="config_val[]" value="">
						<?php } else if($item["config_type"]=="bool") { ?>
							<label><input type="radio" name="config_val_bool_<?php echo $item['config_id'];?>" value="0" checked>否</label> <label><input type="radio" name="config_val_bool_<?php echo $item['config_id'];?>" value="1" <?php if($item['config_val']=='1'){?> checked<?php }?>>是</label>
							<input type="hidden" name="config_val[]" value="">
						<?php } else { ?>
						<input type="input" name="config_val[]" value="<?php echo $item['config_val'];?>" size=40 class="autosize">
						<?php }?>
					<?php } else { ?>
						<?php if(empty($item[$field])){?>&nbsp;<?php } else { ?><?php echo $item[$field];?><?php }?>
					<?php }?>
					</li>
				<?php }?>
				<?php if(cls_config::get("code_mode","base")==1){?>
				<li>
					<?php if($this_limit->chk_act("edit")){?><input type="button" name="btnedit" value="编辑" onclick="master_open({id:<?php echo $item['config_id'];?>,title:'编辑配置',w:500});" class="pBtn"><?php }?>
					<?php if($this_limit->chk_act("del")){?><input type="button" name="btndel" value="注销" onclick="admin.ajax_delete('<?php echo fun_get::url(array('app_act'=>'delete','id'=>$item['config_id']));?>');" class="pBtn2"><?php }?>
				</li>
			    <?php }?>
			</div>
			<?php }?>
		</div>
	</div>
</div>
</div>
<div class="pFootAct" id="id_pFootAct">
<li><?php if($this_limit->chk_act("update")){?>&nbsp;<input type="button" name="dosubmit" value="更新" onclick="admin.frm_ajax('update');" class="pBtn"><?php }?></li>
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
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_resize&filename=sys&key=sys.config&index=" + this.fieldsindex + "&w=" + lng_w , function(data){
		//alert(data);
	});
}
admin.table.list1.sort = function(key) {
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=sort&filename=sys&key=sys.config&sortby=" + key , function(data){
			var obj_data=kj.json(data);
			if(!obj_data.isnull) {
				admin.refresh();
			}
	});
}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>