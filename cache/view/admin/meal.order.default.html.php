<?php include cls_resolve::on_resolve('/admin\/header')?>
<div class="pMenu" id="id_pMenu">
	<input type="hidden" value="<?php echo fun_get::get('url_type');?>" name="url_type" id="id_url_type">
	<input type="hidden" value="<?php echo fun_get::get('url_channel');?>" name="url_channel" id="id_url_channel">
	<li<?php if(fun_get::get('url_channel')!='all'){?> class="sel"<?php }?> onclick="kj.obj('#id_url_channel').value='';admin.act('');">今日订单</li>
	<li<?php if(fun_get::get('url_channel')=='all'){?> class="sel"<?php }?> onclick="kj.obj('#id_url_channel').value='all';admin.act('');">所有订单</li>
	<li onclick="admin.menu_display(0);" class = "x_btn">查找</li>
	<li class="fdpic" onclick="master_open({url:'common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&key=meal.order&filename=meal',title:'设置字段',w:400});">&nbsp;</li>
</div>
<div class="btnMenuDiv" id="id_btnMenuDiv"<?php if($arr_list['issearch']==0){?> style="display:none"<?php }?>>
<li>下单时间：<input type="text" id="s_addtime1" name="s_addtime1" value="<?php echo fun_get::get('s_addtime1');?>" class='pTxtDate' onfocus="new Calendar().show(this);"> 到 <input type="text" name="s_addtime2" id="s_addtime2" value="<?php echo fun_get::get('s_addtime2');?>" class='pTxtDate' onfocus="new Calendar().show(this);"></li>
<li>状　态：<select name="s_state">
	<option value="-999">不限</option>
	<?php foreach($arr_state as $item=>$val){ ?>
		<?php if(fun_get::get("s_state" , -999)==$val){?>
		<option value='<?php echo $val;?>' selected><?php echo $item;?></option>";
		<?php } else { ?>
		<option value='<?php echo $val;?>'><?php echo $item;?></option>";
		<?php }?>
	<?php }?>
	</select></li>
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
		<li><span class="x_tit" style="cursor:none;width:160px">操作</span></li>
	</div>
	<div class="pTableList" id="id_table_list">
		<div class='pTable' id="id_table">
			<?php foreach($arr_list['list'] as $item){ ?>
			<div class='pTabRow'>
				<input type="hidden" name="hide_state[]" value="<?php echo $item['state'];?>">
				<li><input type='checkbox' name='selid[]' value="<?php echo $item['order_id'];?>"></li>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<li><?php if(empty($item[$field])){?>&nbsp;<?php } else { ?><?php echo $item[$field];?><?php }?></li>
				<?php }?>
				<li>
					<?php if($this_limit->chk_act("detail")){?>
					<input type="button" name="btnedit" value="明细" onclick="thisjs.detail(<?php echo $item['order_id'];?>);" class="pBtn">&nbsp;
					<?php }?>
					<input type="button" name="btnedit" value="打印" onclick="thisjs.print(<?php echo $item['order_id'];?>);" class="pBtn">&nbsp;
					<?php if($this_limit->chk_act("delete")){?>
					<input type="button" name="btnedit" value="删除" onclick="admin.ajax_delete(<?php echo $item['order_id'];?>);" class="pBtn2">
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
<div class="pFootAct" id="id_pFootAct">
<li>
<label><input type='checkbox' name='selall' value='1'>全选</label>　
<select name="selact" onchange="thisjs.changeact(this.value)" id="id_selact">
	<option value="">--操作--</option>
		<?php if($this_limit->chk_act("confirm")){?><option value="award">奖励积分</option><?php }?>
		<?php if($this_limit->chk_act("state")){?><option value="state">处理订单</option><?php }?>
		<?php if($this_limit->chk_act("delete")){?><option value="delete">删除</option><?php }?>
	</select>&nbsp;<span id="id_selact_state" style="display:none">
	<input type="hidden" name="state_beta" value="">
	<label><input type="radio" name="state_val" value="1" checked onclick="kj.dialog.close('#wincancel_box');" id="id_state_val1">接授</label>&nbsp;
	<label><input type="radio" name="state_val" value="0" onclick="thisjs.show_cancel_box();" id="id_state_val2">取消</label>
	</span>
	&nbsp;<input type="button" name="dosubmit" value="执行" onclick="thisjs.selact();" class="pBtn">
</li>
</div>
<div id="id_cancel_order_html" style="display:none">
	<div class="me_div1">
		<li style="line-height:30px">
			输入取消原因<br>
			<textarea name="x" cols=30 rows=5 id="id_cancel_beta"></textarea>
		</li>
		<li><input type="button" name="btn_cancel" value="确定" class="pBtn" onclick="thisjs.cancel_ok()"></li>
	</div>
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
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_resize&filename=meal&key=meal.order&index=" + this.fieldsindex + "&w=" + lng_w , function(data){
		//alert(data);
	});
}
admin.table.list1.sort = function(key) {
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=sort&key=meal.order&filename=meal&sortby=" + key , function(data){
			var obj_data=kj.json(data);
			if(!obj_data.isnull) {
				admin.refresh();
			}
	});
}
//当前页面js对象
var thisjs = new function() {
	this.changeact = function(val) {
		kj.obj("#id_selact_state").style.display = (val == "state") ? "" : "none";
	}
	this.selact = function() {
		var act = kj.obj("#id_selact").value;
		if( act == 'state') {
			if(kj.obj("#id_state_val1").checked) document.frm_main.state_beta.value = '';
			kj.dialog.close('#wincancel_box');
		} else if(act == 'award') {
			var arr_id = kj.obj(":selid[]");
			var arr_state = kj.obj(":hide_state[]");
			var val;
			for(var i = 0 ; i < arr_id.length ; i++ ) {
				if(arr_id[i].checked && i < arr_state.length) {
					val = kj.toint(arr_state[i].value);
					if( val == 0 ) {
						alert("第"+ (i+1) + "条订单需要先处理后才能奖积分");
						return;
					} else if(val < 0) {
						alert("第"+ (i+1) + "条订单状态无效，无法奖励积分");
						return;
					}
				}
			}
		}
		admin.selact();
	}
	this.cancel_ok = function() {
		document.frm_main.state_beta.value = kj.obj("#id_cancel_beta").value;
		this.selact();
	}
	//显示修改密码窗口
	this.show_cancel_box = function(id,mobile,tel) {
		this.cancel_id = id;
		var obj = kj.obj('#id_cancel_order_html');
		if(obj) {
			this.cancel_box_html = obj.innerHTML;
			kj.remove(obj);
		}
		kj.dialog({'html':this.cancel_box_html,'id':'cancel_box','type':'html','title':'取消订单','w':300,'h':230,'showbtnmax':false});
		if(mobile == '') {
			kj.hide("#id_cancel_smstxt");
			kj.hide("#id_checkbox_issms");
			obj = kj.obj("#id_cancel_tel");
			if(obj) obj.innerHTML = tel;
		} else {
			kj.hide("#id_cancel_teltxt");
		}
	}
	this.print = function(id) {
		vReturnValue = window.showModelessDialog('./common.php?app_module=meal&app=call&app_act=print&order_id='+id, 'win_print', "dialogHeight:500px;dialogWidth:<?php echo $print_width;?>px");
	}

	this.detail = function(id) {
		master_open({'url':"<?php echo fun_get::url(array('id'=>'','app_act'=>'detail'));?>&id="+id,'id':'order_detail','type':'iframe','title':'订单明细','w':700,'showbtnmax':false});
	}
}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>