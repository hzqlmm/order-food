<?php include cls_resolve::on_resolve('/admin\/header')?>
<div class="pMain" id="id_main">
<div class="pTableBox" id="id_table_box">
	<div class='pTableTit' id="id_table_title">
		<li><span class="x_tit" style="width:25px">&nbsp;</span></li>
		<li><span class="x_tit" style="width:250px">名称</span><span class="x_split"></span></li>
		<li><span class="x_tit" style="cursor:none;width:80px">操作</span></li>
	</div>
	<div class="pTableList" id="id_table_list">
		<div class='pTable' id="id_table">
			<?php foreach($arr_list["list_group"] as $group_key => $group){ ?>
				<div class='pTabRow' id="id_tit_<?php echo $group['group_id'];?>" style="background:#cccccc">
					<li></li>
					<li style="font-weight:bold;font-size:14px"><?php echo $group["group_name"];?></li>
					<li></li>
				</div>
				<?php foreach($arr_list['list']["id_" . $group['group_id']] as $item){ ?>
				<div class='pTabRow' id="id_tabrow_<?php echo $item['menu_id'];?>">
					<li><input type='checkbox' name='selid[]' value="<?php echo $group['group_id'];?>|<?php echo $group['group_name'];?>|<?php echo $item['menu_id'];?>|<?php echo $item['menu_title'];?>|<?php echo $item['menu_num'];?>"></li>
					<li><?php echo $item["menu_title"];?></li>
					<li>
					<a href="javascript:thisjs.add('<?php echo $group['group_id'];?>','<?php echo $group['group_name'];?>','<?php echo $item['menu_id'];?>','<?php echo $item['menu_title'];?>','<?php echo $item['menu_num'];?>');">添加</a>
					</li>
				</div>
				<?php }?>
			<?php }?>
		</div>
	</div>
</div>
</div>
<div class="pFootAct" id="id_pFootAct">
	<li><label><input type='checkbox' name='selall' value='1'>全选</label>　<?php if($this_limit->chk_act("save")){?><input type="button" name="dosubmit" value="添加" onclick="thisjs.add_all();" class="pBtn"><?php }?></li>
</div>
<script src="/webcss/admin/admin.table.js"></script>
<script>
//初始化表格控件
kj.onload(function(){
admin.table.list1.init('#id_table_title' , '#id_table');
});
//当前页面js对象
var thisjs = new function() {
	this.add = function(group_id , group_name , menu_id , menu_name , menu_num ,isall) {
		var frm = window.parent;
		if(frm && typeof frm  == 'object') {
			frm.thisjs.add(group_id , group_name , menu_id , menu_name , menu_num);
			kj.remove("#id_tabrow_" + menu_id);
			if(!isall) frm.inc_resize();
		}
	}
	this.add_all = function() {
		var arr = kj.obj("input<<name,selid[]");
		var arr_x;
		for(var i=0 ; i < arr.length ; i++ ) {
			if(arr[i].checked) {
				arr_x = arr[i].value;
				arr_x = arr_x.split("|");
				if(arr_x.length == 5) {
					this.add(arr_x[0],arr_x[1],arr_x[2],arr_x[3],arr_x[4] ,true);
				}
			}
		}
		var frm = window.parent;
		if(frm && typeof frm  == 'object') frm.inc_resize();
	}
}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>