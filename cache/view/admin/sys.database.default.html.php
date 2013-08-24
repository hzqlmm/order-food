<?php include cls_resolve::on_resolve('/admin\/header')?>
<input type="hidden" name="url_dirpath" value="<?php echo $arr_list['pathdir'];?>">
<div class="pMenu" id="id_pMenu">
	<li class="sel" onclick="admin.act('');">备份</li>
	<li onclick="admin.act('reback');">还原</li>
</div>
<div class="pMain" id="id_main">
	<?php foreach($arr_list as $item){ ?>
		<div class="pli1"><span class='x_left'><label><input type="checkbox" name="selid[]" value="<?php echo $item['Name'];?>"> <?php echo $item['Name'];?></label></span><span class='x_r'><?php if($item['Engine']=='MyISAM' && $item["Data_free"]>0){?><a href="javascript:admin.ajax_url({url:'?app=<?php echo $get_app;?>&app_module=<?php echo $get_app_module;?>&app_act=optimize_go&id=<?php echo $item['Name'];?>'});">优化</a> | <?php }?><a href="javascript:admin.ajax_url({url:'?app=<?php echo $get_app;?>&app_module=<?php echo $get_app_module;?>&app_act=repair&id=<?php echo $item['Name'];?>'});">修复</a></span></div>
	<?php }?>
</div>
<div class="pFootAct" id="id_pFootAct">
<li>
<label><input type='checkbox' name='selall' value='1'>全选</label>　
<select name="selact" onchange="thisjs.selact(this.value)">
	<option value="">--操作--</option>
	<?php if($this_limit->chk_act("backup")){?><option value="backup" selected>备份</option><?php }?>
	<?php if($this_limit->chk_act("optimize")){?><option value="optimize_go">优化</option><?php }?>
	<?php if($this_limit->chk_act("repair")){?><option value="repair">修复</option><?php }?>
	</select>
	&nbsp;<span id="id_selact_backup"><input type="text" name="backupname" value="<?php echo $backupname;?>" class="pTxt1"></span>
	&nbsp;<input type="button" name="dosubmit" value="执行" onclick="thisjs.selact_go();" class="pBtn">
</li>
</div>
<script src="/webcss/common/js/kj.progress.js"></script>
<script>
var thisjs = new function() {
	this.selid = [];
	this.selact_go = function() {
		if(document.frm_main.selact.value == 'backup') {
			this.on_backup();
		} else {
			admin.selact();
		}
	}
	this.on_backup = function() {
		var arr = kj.obj("input<<name,selid[]");
		this.selid  = [];
		this.backupname = document.frm_main.backupname.value;
		if(this.backupname == '') this.backupname = "<?php echo $backupname;?>";
		for(var i = 0 ; i < arr.length; i++ ){
			if(arr[i].checked) this.selid[this.selid.length] = arr[i].value;
		}
		if(thisjs.selid.length<1) {
			kj.alert("没有需要要备份的表");
			return;
		}
		kj.progress.show1.open({id:'backup',title:'正在备份数据库',size:this.selid.length,w:500});
		thisjs.backup_table();
	}
	this.backup_table = function() {
		if( thisjs.selid.length < 0 ) return;
		if( thisjs.selid.length == 0 ) {
			kj.progress.show1.close('backup');
			kj.alert.show("备份完成");
			return;
		}
		var tablename = thisjs.selid[0];
		thisjs.selid.removeat(0);
		kj.ajax.get("?app=database&app_module=sys&app_act=backup&tablename=" + tablename + "&backupname=" + thisjs.backupname , function(data){
			var obj_data=kj.json(data);
			if(obj_data.isnull) {
				kj.alert("备份[ " + tablename + " ]失败");
				return;
			}
			thisjs.backup_row(tablename , 0 , 0);
			kj.progress.show1.step('backup');
		});
	}
	this.backup_row = function(tablename , row , total , pages ) {
		if(pages > 10 ) kj.progress.show1.step('backup_row');
		kj.ajax.get("?app=database&app_module=sys&app_act=backup_row&tablename=" + tablename + "&backupname=" + thisjs.backupname + "&page=" + row + "&total=" + total, function(data){
			var obj_data=kj.json(data);
			if(obj_data.isnull) {
				kj.alert("备份失败");
				return;
			}
			obj_data.page_count = kj.toint(obj_data.page_count);
			if(obj_data.page_count>10 && kj.progress.show1._objid.indexOf("backup_row") < 0 ) {
				kj.progress.show1.open({id:'backup_row',title:'备份表:' + tablename ,size:obj_data.page_count,w:100});
				kj.progress.show1.step('backup_row');
			}
			obj_data.next_page = kj.toint(obj_data.next_page);
			if(obj_data.page_count > obj_data.next_page) {
				thisjs.backup_row(tablename , obj_data.next_page , obj_data.total , obj_data.page_count);
			} else {
				if(obj_data.page_count> 10) kj.progress.show1.close('backup_row');
				thisjs.backup_table();
			}
		});
	}
	this.selact = function(val) {
		kj.obj("#id_selact_backup").style.display = (val == "backup") ? "" : "none";
	}

}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>