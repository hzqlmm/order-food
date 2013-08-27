<div class="pMenu" id="id_pMenu">
<?php if($app_act!='dellist'){?>
	<li onclick="admin.act('');"<?php if($app_act!='list'){?> class="sel"<?php }?>>目录模式</li>
	<?php if($this_limit->chk_act("list")){?><li onclick="admin.act('list');"<?php if($app_act=='list'){?> class="sel"<?php }?>>列表模式</li><?php }?>
	<?php if($this_limit->chk_act("dellist")){?><li onclick="admin.act('dellist');">回收站</li><?php }?>
	<?php if($this_limit->chk_act("edit_article")){?><li onclick="master_open({id:'add_config',app_act:'edit_article',title:'添加文章',url:'<?php echo fun_get::url(array('app_act'=>'edit_article','id'=>0));?>',w:1000});" class="x_btn">添加文章</li><?php }?>
	<?php if($this_limit->chk_act("edit_folder")){?><li onclick="master_open({id:'add_config',title:'添加目录',url:'<?php echo fun_get::url(array('app_act'=>'edit_folder','id'=>0));?>',w:500});" class="x_btn">添加目录</li><?php }?>
<?php } else { ?>
	<li onclick="admin.act('');">目录模式</li>
	<?php if($this_limit->chk_act("list")){?><li onclick="admin.act('list');">列表模式</li><?php }?>
	<?php if($this_limit->chk_act("dellist")){?><li class="sel" onclick="admin.act('dellist');">回收站</li><?php }?>
<?php }?>
	<li onclick="admin.menu_display(0);" class="x_btn">查找</li>
	<li class="fdpic" onclick="master_open({url:'common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&key=article&filename=article',title:'设置字段',w:400});">&nbsp;</li>
</div>
<?php if($app_act=='default'){?>
<div class="pMain_1" id="id_pMain_1">
	<div class="pPath">
		<div style="float:left;margin-left:10px;display:inline;height:20px;overflow:hidden;line-height:20px;">
			<span onmouseover="kj.show('#id_channel_select');" onmouseout="kj.hide('#id_channel_select');" style="float:left"><img src="/webcss/admin/images/folder.gif">&nbsp;</span><span id="id_channel_menu" style="float:left;padding-top:3px"></span><br>
			<div class="channel_select" id="id_channel_select" style="display:none" onmouseover="kj.show('#id_channel_select');" onmouseout="kj.hide('#id_channel_select');">
			<?php foreach($arr_channel as $item){ ?>
				<?php if($item['channel_id']==fun_get::get('url_channel_id')){?>
					<li id="channel_val_<?php echo $item['channel_id'];?>" class="x_sel"><?php echo $item['channel_name'];?></li>
					<script>
						kj.obj("#id_channel_menu").innerHTML = '<a href="javascript:thisjs.opendir(\'0\');" style="font-weight:bold;color:#004499"><?php echo $item['channel_name'];?></a>';
					</script>
				<?php } else { ?>
					<li id="channel_val_<?php echo $item['channel_id'];?>"><?php echo $item['channel_name'];?></li>
				<?php }?>
			<?php }?>
			</div>
		</div>
		<div style="float:left;padding-top:2px;height:20px;line-height:20px;overflow:hidden"><?php if(!empty($folder_path)){?>&nbsp;->&nbsp;<?php }?><?php echo $folder_path;?></div>
	</div>
	
</div>
<?php }?>
<div class="btnMenuDiv" id="id_btnMenuDiv"<?php if($arr_list['issearch']==0){?> style="display:none"<?php }?>>
<li>添加时间：<input type="text" id="s_addtime1" name="s_addtime1" value="<?php echo fun_get::get('s_addtime1');?>" class='pTxtDate' onfocus="new Calendar().show(this);"> 到 <input type="text" name="s_addtime2" id="s_addtime2" value="<?php echo fun_get::get('s_addtime2');?>" class='pTxtDate' onfocus="new Calendar().show(this);">　修改时间：<input type="text" name="s_updatetime1" value="<?php echo fun_get::get('s_updatetime1');?>" class='pTxtDate' onfocus="new Calendar().show(this);"> 到 <input type="text" name="s_updatetime2" value="<?php echo fun_get::get('s_updatetime2');?>" class='pTxtDate' onfocus="new Calendar().show(this);"></li>
<li>状　态：<select name="s_state">
	<option value="-999">不限</option>
	<?php foreach($arr_list['state'] as $item=>$val){ ?>
		<?php if(fun_get::get("s_state")==$val){?>
		<option value='<?php echo $val;?>' selected><?php echo $item;?></option>";
		<?php } else { ?>
		<option value='<?php echo $val;?>'><?php echo $item;?></option>";
		<?php }?>
	<?php }?>
	</select></li>
<li>关 键 字：<input type="text" id="s_key" name="s_key" value="<?php echo fun_get::get('s_key');?>" class='pTxt1'>　<input type="button" name="btn_s_ok" value="查找" class="pBtn" onclick="admin.search();"> 　<input type="button" name="btn_s_clear" value="清空" class="pBtn" onclick="admin.clear_search();"></li>
</div>
<div class="pMain" id="id_main">
<div class="pTableBox" id="id_table_box">
	<?php if($app_act=='list'){?>
	<!--非目录浏览方式-->
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
				<li><input type='checkbox' name='selid[]' value="<?php echo $item['article_id'];?>"></li>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<?php if($field == 'article_title'){?>
						<li><a href="common.php?app=article&app_act=view&id=<?php echo $item['article_id'];?>" target="_blank"><?php echo $item[$field];?></a></li>
					<?php } else { ?>
						<li><?php if(empty($item[$field])){?>&nbsp;<?php } else { ?><?php echo $item[$field];?><?php }?></li>
					<?php }?>
				<?php }?>
				<li>
				<?php if($this_limit->chk_act("edit_article")){?><input type="button" name="btnedit" value="编辑" onclick="master_open({id:<?php echo $item['article_id'];?>,title:'编辑文章',w:1000});" class="pBtn"><?php }?>
				<?php if($this_limit->chk_act("del_article")){?><input type="button" name="btnedit" value="删除" onclick="admin.ajax_url({app_act:'del_article',id:'<?php echo $item['article_id'];?>'});" class="pBtn2"><?php }?>
				</li>
			</div>
			<?php }?>
		</div>
	</div>
	<?php } else { ?>
	<!--目录浏览方式-->
	<div class='pTableTit' id="id_table_title">
		<li><span class="x_tit" style="width:25px">&nbsp;</span></li>
		<li><span class="x_tit" style="width:600px">名称</span></li>
		<li><span class="x_tit" style="cursor:none;width:200px">操作</span></li>
	</div>
	<div class="pTableList" id="id_table_list">
		<div class='pTable' id="id_table">
			<?php foreach($arr_dirlist as $item){ ?>
			<div class='pTabRow'>
				<li><input type='checkbox' name='selid2[]' value="<?php echo $item['folder_id'];?>"></li>
				<li><img src="/webcss/admin/images/folder.gif">&nbsp;&nbsp;<a href="common.php?app=article&app_act=folder&id=<?php echo $item['folder_id'];?>" target="_blank"><?php echo $item["folder_name"];?></a></li>
				<li>
					<?php if($app_act!='dellist'){?>
						<input type="button" name="btnedit" value="打开" onclick="thisjs.opendir(<?php echo $item['folder_id'];?>);" class="pBtn">
						<?php if($this_limit->chk_act("edit_folder")){?>
						<input type="button" name="btnedit" value="编辑" onclick="master_open({id:<?php echo $item['folder_id'];?>,title:'编辑目录',w:500,app_act:'edit_folder'});" class="pBtn">
						<?php }?>
						<?php if($this_limit->chk_act("del_folder")){?>
						<input type="button" name="btnedit" value="删除" onclick="admin.ajax_url({url:'?app=<?php echo $app;?>&app_module=<?php echo $app_module;?>&app_act=del_folder&fid=<?php echo $item['folder_id'];?>'});" class="pBtn2">
						<?php }?>
					<?php } else { ?>
						<?php if($this_limit->chk_act("reback")){?>
						<input type="button" name="btnedit" value="还原" onclick="admin.ajax_url({url:'?app=<?php echo $app;?>&app_module=<?php echo $app_module;?>&app_act=reback&fid=<?php echo $item['folder_id'];?>'});" class="pBtn">
						<?php }?>
					<?php }?>
				</li>
			</div>
			<?php }?>
			<?php foreach($arr_list['list'] as $item){ ?>
			<div class='pTabRow'>
				<li><input type='checkbox' name='selid[]' value="<?php echo $item['article_id'];?>"></li>
				<li><span style="float:left"><img src="/webcss/admin/images/file.jpg">&nbsp;&nbsp;<a href="common.php?app=article&app_act=view&id=<?php echo $item['article_id'];?>" target="_blank"><?php echo $item["article_title"];?></a></span><?php if(isset($item['article_topic_id']) && !empty($item['article_topic_id'])){?><span style="float:right;padding-right:20px;color:#888888"><?php echo $item['article_topic_id'];?></span><?php }?></li>
				<li>
				<?php if($app_act!='dellist'){?>
					<?php if($this_limit->chk_act("edit_article")){?>
					<input type="button" name="btnedit" value="编辑" onclick="master_open({id:<?php echo $item['article_id'];?>,app_act:'edit_article',title:'编辑文章',w:1000});" class="pBtn">
					<?php }?>
					<?php if($this_limit->chk_act("del_article")){?><input type="button" name="btnedit" value="删除" onclick="admin.ajax_url({app_act:'del_article',id:'<?php echo $item['article_id'];?>'});" class="pBtn2"><?php }?>
				<?php } else { ?>
					<?php if($this_limit->chk_act("reback")){?><input type="button" name="btnedit" value="还原" onclick="admin.ajax_url({app_act:'reback',id:'<?php echo $item['article_id'];?>'});" class="pBtn"><?php }?>
				<?php }?>
				</li>
			</div>
			<?php }?>
		</div>
	</div>
	<?php }?>
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
	<?php if($app_act!='dellist'){?>
		<?php if($this_limit->chk_act("state")){?><option value="state">状态设置</option><?php }?>
		<?php if($this_limit->chk_act("topic")){?><option value="topic">专题设置</option><?php }?>
		<?php if($this_limit->chk_act("del_article") && $this_limit->chk_act("del_folder")){?>
			<option value="del_article_folder">删除</option>
		<?php } else if($this_limit->chk_act("del_article")) { ?>
			<option value="del_article">删除文章</option>
		<?php } else if($this_limit->chk_act("del_folder")) { ?>
			<option value="del_folder">删除目录</option>
		<?php }?>
	<?php } else { ?>
		<?php if($this_limit->chk_act("reback_article") && $this_limit->chk_act("reback_folder")){?>
			<option value="reback_article_folder">还原</option>
		<?php } else if($this_limit->chk_act("reback_article")) { ?>
			<option value="reback_article">还原文章</option>
		<?php } else if($this_limit->chk_act("reback_folder")) { ?>
			<option value="reback_folder">还原目录</option>
		<?php }?>
		<?php if($this_limit->chk_act("delete_article") && $this_limit->chk_act("delete_folder")){?>
			<option value="delete_article_folder">彻底删除</option>
		<?php } else if($this_limit->chk_act("delete_article")) { ?>
			<option value="delete_article">彻底删除文章</option>
		<?php } else if($this_limit->chk_act("delete_folder")) { ?>
			<option value="delete_folder">彻底删除目录</option>
		<?php }?>
	<?php }?>
	</select>&nbsp;<span id="id_selact_state" style="display:none">
	<select name="state_val">
	<?php foreach($arr_list['state'] as $item=>$key){ ?>
	<option value="<?php echo $key;?>"><?php echo $item;?></option>
	<?php }?>
	</select>
	</span>&nbsp;<span id="id_selact_topic" style="display:none">
	<select name="topic_val">
	<option value=""></option>
	<?php foreach($arr_topic as $item){ ?>
	<option value="<?php echo $item['topic_id'];?>"><?php echo $item['topic_name'];?></option>
	<?php }?>
	</select>
	</span>
	&nbsp;<input type="button" name="dosubmit" value="执行" onclick="admin.selact();" class="pBtn">
	<?php if($this_limit->chk_act("paste_article") || $this_limit->chk_act("paste_folder")){?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="btnCopy" value="复制" onclick="thisjs.copy();" class="pBtn">
	&nbsp;<input type="button" name="btnCut" value="剪贴" onclick="thisjs.cut();" class="pBtn">
	&nbsp;<input type="button" name="btnPaste" id="id_btnPaste" value="粘贴" class="pBtn" disabled="true" onclick="thisjs.paste();"><?php }?>
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
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_resize&key=article&filename=article&index=" + this.fieldsindex + "&w=" + lng_w , function(data){
		//alert(data);
	});
}
admin.table.list1.sort = function(key) {
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=sort&key=article&filename=article&sortby=" + key , function(data){
			var obj_data=kj.json(data);
			if(!obj_data.isnull) {
				admin.refresh();
			}
	});
}
</script>