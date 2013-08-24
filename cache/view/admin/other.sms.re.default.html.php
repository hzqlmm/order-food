<?php include cls_resolve::on_resolve('/admin\/header')?>
<div class="pMenu" id="id_pMenu">
	<li class="sel">管理</li>
	<li onclick="admin.menu_display(0);" class = "btnMenu">查找</li>
	<li class="fdpic" onclick="master_open({url:'common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&filename=other&key=other.sms.re',title:'设置字段',w:400});">&nbsp;</li>
</div>
<div class="btnMenuDiv" id="id_btnMenuDiv"<?php if($arr_list['issearch']==0){?> style="display:none"<?php }?>>
<li>回复时间：<input type="text" name="s_retime1" value="<?php echo fun_get::get('s_retime1');?>" class='pTxtDate' onfocus="new Calendar().show(this);"> 到 <input type="text" name="s_retime2" value="<?php echo fun_get::get('s_retime2');?>" class='pTxtDate' onfocus="new Calendar().show(this);"></li>
<li>关 键 字：<input type="text" name="s_key" value="<?php echo fun_get::get('s_key');?>" class='pTxt1'>　<input type="button" name="btn_s_ok" value="查找" class="pBtn" onclick="admin.search();"> 　<input type="button" name="btn_s_clear" value="清空" class="pBtn" onclick="admin.clear_search();"></li>
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
	</div>
	<div class="pTableList" id="id_table_list">
		<div class='pTable' id="id_table">
			<?php foreach($arr_list['list'] as $item){ ?>
			<div class='pTabRow'>
				<li><input type='checkbox' name='selid[]' value="<?php echo $item['re_id'];?>"></li>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<li><?php if(empty($item[$field])){?>&nbsp;<?php } else { ?><?php echo $item[$field];?><?php }?></li>
				<?php }?>
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
<input type='checkbox' name='selall' value='1'>全选　
<select name="selact" onchange="thisjs.selact(this.value)">
	<option value="">--操作--</option>
	<?php if($this_limit->chk_act("delete")){?><option value="delete">删除</option><?php }?>
	</select>&nbsp;<input type="button" name="btnExe" value="执行" onclick="admin.selact();" class="pBtn">
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
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_resize&filename=other&key=other.sms.re&index=" + this.fieldsindex + "&w=" + lng_w , function(data){
		//alert(data);
	});
}
admin.table.list1.sort = function(key) {
	kj.ajax.get("common.php?app=config&app_module=user&filename=other&dir=<?php echo $app_dir;?>&app_act=sort&key=other.sms.re&sortby=" + key , function(data){
			var obj_data=kj.json(data);
			if(!obj_data.isnull) {
				admin.refresh();
			}
	});
}

</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>