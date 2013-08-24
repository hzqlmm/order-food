<?php include cls_resolve::on_resolve('/admin\/header')?>
<div class="pMenu" id="id_pMenu">
	<li class="sel">管理</li>
	<li onclick="master_open({id:'add_config',title:'添加广告',url:'<?php echo fun_get::url(array('app_act'=>'edit','id'=>0));?>',w:800});" class="x_btn">添加</li>
	<li onclick="admin.menu_display(0);" class = "x_btn">查找</li>
</div>
<div class="btnMenuDiv" id="id_btnMenuDiv"<?php if($arr_list['issearch']==0){?> style="display:none"<?php }?>>
<li>关 键 字：<input type="text" id="s_key" name="s_key" value="<?php echo fun_get::get('s_key');?>" class='pTxt1'>　<input type="button" name="btn_s_ok" value="查找" class="pBtn" onclick="admin.search();"> 　<input type="button" name="btn_s_clear" value="清空" class="pBtn" onclick="admin.clear_search();"></li>
</table>
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
			<div class='pTabRow'>
				<li><input type='checkbox' name='selid[]' value="<?php echo $item['ads_id'];?>"></li>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<li><?php if(empty($item[$field])){?>&nbsp;<?php } else { ?><?php echo $item[$field];?><?php }?></li>
				<?php }?>
				<li>
				<?php if($this_limit->chk_act("edit")){?><a href="javascript:master_open({id:<?php echo $item['ads_id'];?>,title:'编辑广告',w:800});">编辑</a><?php }?>
				<?php if($this_limit->chk_act("delete")){?><a href="javascript:admin.ajax_delete(<?php echo $item['ads_id'];?>);">删除</a><?php }?>
				<a href="javascript:kj.copy('{'+'fun_kj::get_ads(<?php echo $item['ads_id'];?>)'+'}');">调用</a>&nbsp;&nbsp;
				<a href="javascript:kj.copy('<script src=\''+'{'+'cfg:(url,base)'+'}/common.php?app=other&app_act=ads_js&id=<?php echo $item['ads_id'];?>\'></script>');">js调用</a>
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
<select name="selact">
	<option value="">--操作--</option>
		<?php if($this_limit->chk_act("delete")){?><option value="delete">删除</option><?php }?>
	</select>
	&nbsp;<input type="button" name="btnExe" value="执行" onclick="admin.selact();" class="pBtn">
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
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_resize&filename=other&key=other.ads&index=" + this.fieldsindex + "&w=" + lng_w , function(data){
		//alert(data);
	});
}
admin.table.list1.sort = function(key) {
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=sort&key=other.ads&filename=other&sortby=" + key , function(data){
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