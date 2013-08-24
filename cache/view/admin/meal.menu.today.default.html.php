<?php include cls_resolve::on_resolve('/admin\/header')?>
<div class="pMenu" id="id_pMenu">
	<input type="hidden" value="<?php echo fun_get::get('url_type');?>" name="url_type" id="id_url_type">
	<li class="sel">管理</li>
	<li onclick="kj.dialog({id:'add_today',title:'添加菜品',url:'<?php echo fun_get::url(array('app_act'=>'add','url_date'=>$this_date,'url_date_period'=>$this_date_period));?>',w:500,showbtnclose:false,showbtnhide:true,top:0,type:'iframe'});" class="x_btn">添加</li>
</div>
<div class="pMain_1" id="id_pMain_1">
	<div class="pPath">&nbsp;&nbsp;日期：<input type="text" id="id_url_date" name="url_date" value="<?php echo $this_date;?>" class='pTxtDate' onfocus="new Calendar().show(this,null,function(){admin.refresh();});">
	&nbsp;&nbsp;<?php echo fun_get::weekday($this_date);?>&nbsp;&nbsp;
	<label><input type="radio" name="url_date_period" value="0" checked onclick="admin.refresh();">全天</label>
	<?php foreach($this_period['list'] as $item=>$key){ ?>
		<label><input type="radio" name="url_date_period" value="<?php echo $key['index'];?>"<?php if($key['index']==$this_date_period){?> checked<?php }?> onclick="admin.refresh();"><?php echo $key['title'];?></label>
	<?php }?>
	</div>
</div>
<div class="pMain" id="id_main">
<div class="pTableBox" id="id_table_box">
	<div class='pTableTit' id="id_table_title">
		<li><span class="x_tit" style="width:25px">&nbsp;</span></li>
		<?php foreach($arr_list["tabtit"] as $item){ ?>
			<li><span class="x_tit"<?php if($item["w"]>0){?> style="width:<?php echo $item["w"];?>px"<?php }?> onclick="admin.table.list1.sort('<?php echo $item['key'];?>')"><?php echo $item["name"];?>
			<?php if(isset($arr_list['sort'][$item['key']])){?><img src="/webcss/admin/images/sort_<?php echo $arr_list['sort'][$item['key']];?>.png"><?php }?></span><span class="x_split"></span></li>
		<?php }?>
		<li><span class="x_tit" style="cursor:none;width:80px">操作</span></li>
	</div>
	<div class="pTableList" id="id_table_list">
		<div class='pTable' id="id_table">
			<div class='pTabRow' id="id_tabrow_THISMENUID" style="display:none">
				<li><input type='hidden' name='today_id[]' value=""><input type='hidden' name='today_menu_id[]' value="THISMENUID"></li>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<?php if($field=='menu_title'){?>
					<li>THISMENUNAME</li>
					<?php } else { ?>
					<li><input type="text" name="<?php echo $field;?>[]" value="THISMENUVALUE" class="autosize"></li>
					<?php }?>
				<?php }?>
				<li>
				<?php if($this_limit->chk_act("delete")){?>&nbsp;&nbsp;<input type="button" name="btnedit" value="移除" onclick="kj.remove('#id_tabrow_THISMENUID');" class="pBtn2"><?php }?>
				</li>
			</div>
			<?php foreach($arr_list["list_group"] as $group_key => $group){ ?>
				<div class='x_title' id="id_title_<?php echo $group['group_id'];?>">
					&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $group['group_name'];?>
				</div>
				<?php foreach($arr_list['list']["id_".$group['group_id']] as $item){ ?>
				<div class='pTabRow' id="id_tabrow_<?php echo $item['today_menu_id'];?>">
					<li><input type='hidden' name='today_id[]' value="<?php echo $item['today_id'];?>"><input type='hidden' name='today_menu_id[]' value="<?php echo $item['today_menu_id'];?>"></li>
					<?php foreach($arr_list["tabtd"] as $field){ ?>
						<?php if($field=='menu_title'){?>
						<li><?php echo $item[$field];?></li>
						<?php } else { ?>
						<li><input type="text" name="<?php echo $field;?>[]" value="<?php echo $item[$field];?>" class="autosize"></li>
						<?php }?>
					<?php }?>
					<li>
					<?php if($this_limit->chk_act("delete")){?>&nbsp;&nbsp;<input type="button" name="btnedit" value="移除" onclick="kj.remove('#id_tabrow_<?php echo $item['today_menu_id'];?>');" class="pBtn2"><?php }?>
					</li>
				</div>
				<?php }?>
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
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_resize&filename=meal&key=meal.menu.today&index=" + this.fieldsindex + "&w=" + lng_w , function(data){
		//alert(data);
	});
}
admin.table.list1.sort = function(key) {
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=sort&key=meal.menu.today&filename=meal&sortby=" + key , function(data){
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
	this.add = function(group_id , group_name , menu_id , menu_name , menu_default) {
		var obj_tit = kj.obj("#id_title_" + group_id);
		var obj_menu = kj.obj("#id_tabrow_" + menu_id);
		if(obj_menu) {
			alert("已经添加过此菜品");
			return;
		}
		if(!obj_tit) {
			this.addtit(group_id , group_name);
			obj_tit = kj.obj("#id_title_" + group_id);
		}
		var obj_default = kj.obj("#id_tabrow_THISMENUID");
		var str_html = obj_default.innerHTML.replace(/THISMENUID/g,menu_id);
	    str_html = str_html.replace(/THISMENUNAME/g,menu_name);
	    str_html = str_html.replace(/THISMENUVALUE/g,menu_default);
		var obj_div = document.createElement("div");
		obj_div.id = "id_tabrow_" + menu_id;
		obj_div.className = "pTabRow";
		obj_div.innerHTML = str_html;
		//kj.obj("#id_table").appendChild(obj_div);
		var obj_next = this.last_row(obj_tit);
		kj.insert_after(kj.obj("#id_table") , obj_next , obj_div);
	}
	this.last_row = function(obj) {
		if('nextSibling' in obj && obj.nextSibling) {
			var obj_next = obj.nextSibling;
			if('className' in obj_next && obj_next.className == 'x_title') {
				return obj;
			} else {
				return this.last_row(obj_next);
			}
		} else {
			return obj;
		}
	}
	this.addtit = function(group_id , group_name) {
		var obj_div = document.createElement("div");
		obj_div.id = "id_title_" + group_id;
		obj_div.className = "x_title";
		obj_div.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;" + group_name;
		kj.obj("#id_table").appendChild(obj_div);
	}

}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>