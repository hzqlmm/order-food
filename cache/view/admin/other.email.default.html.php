<?php include cls_resolve::on_resolve('/admin\/header')?>
<div class="pMenu" id="id_pMenu">
	<input type="hidden" name="url_type" value="<?php echo $type;?>" id="id_url_type">
	<?php foreach($arr_type as $item=>$key){ ?>
	<li<?php if($type==$key){?> class="sel"<?php }?> onclick="kj.obj('#id_url_type').value='<?php echo $key;?>';admin.act('');"><?php echo $item;?></li>
	<?php }?>
	<?php if(!empty($type)){?><li onclick="master_open({id:'add_config',title:'新建邮件',url:'<?php echo fun_get::url(array('app_act'=>'edit','id'=>0));?>',w:1000});" class="x_btn">新建</li><?php }?>
	<li onclick="admin.menu_display(0);" class="x_btn">查找</li>
	<li class="fdpic" onclick="master_open({url:'common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&filename=other&key=other.email',title:'设置字段',w:400});">&nbsp;</li>
</div>
<div class="btnMenuDiv" id="id_btnMenuDiv"<?php if($arr_list['issearch']==0){?> style="display:none"<?php }?>>
<li>关 键 字：<input type="text" id="s_key" name="s_key" value="<?php echo fun_get::get('s_key');?>" class='pTxt1 pTxtL150'>　<input type="button" name="btn_s_ok" value="查找" class="pBtn" onclick="admin.search();"> 　<input type="button" name="btn_s_clear" value="清空" class="pBtn" onclick="admin.clear_search();"></li>
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
				<li><input type='checkbox' name='selid[]' value="<?php echo $item['email_id'];?>"></li>
				<?php foreach($arr_list["tabtd"] as $field){ ?>
					<li><?php if(empty($item[$field])){?>&nbsp;<?php } else { ?><?php echo $item[$field];?><?php }?></li>
				<?php }?>
				<li>
				<?php if($this_limit->chk_act("edit")){?><a href="javascript:master_open({id:<?php echo $item['email_id'];?>,title:'编辑邮件',w:1000});">编辑</a><?php }?>
				<?php if($this_limit->chk_act("delete")){?><a href="javascript:admin.ajax_delete(<?php echo $item['email_id'];?>);">删除</a><?php }?>
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
<input type='checkbox' name='selall' value='1'>全选　
<select name="selact" id="id_selact">
	<option value="">--操作--</option>
		<?php if($this_limit->chk_act("send")){?><option value="send">发送</option><?php }?>
		<?php if($this_limit->chk_act("delete")){?><option value="delete">删除</option><?php }?>
	</select>
	&nbsp;<span id="id_send_act" style="display:none"><input type="button" name="btnPause" value="暂停" onclick="thisjs.send_pause(this);" class="pBtn" id="id_pause">&nbsp;&nbsp;<input type="button" name="btnStop" value="停止" onclick="thisjs.send_stop();" class="pBtn" id="id_stop"></span>
	&nbsp;<input type="button" name="btnExe" value="执行" onclick="thisjs.selact();" class="pBtn">
</li>
</div>
<script src="/webcss/admin/admin.table.js"></script>
<script src="/webcss/common/js/kj.progress.js"></script>
<script>
//初始化表格控件
kj.onload(function(){
admin.table.list1.init('#id_table_title' , '#id_table');
});
//自动保存
admin.table.list1.save_resize = function() {
	var lng_w = (kj.w(this.field));
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_resize&filename=other&key=other.email&index=" + this.fieldsindex + "&w=" + lng_w , function(data){
		//alert(data);
	});
}
admin.table.list1.sort = function(key) {
	kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=sort&key=other.email&filename=other&sortby=" + key , function(data){
			var obj_data=kj.json(data);
			if(!obj_data.isnull) {
				admin.refresh();
			}
	});
}
//当前页面js对象
var thisjs = new function() {
	this.selid = [];
	this.send_to = [];
	this.total_file = 0;
	this.file_path = '';
	this.pause = false;
	this.send_index = 0;
	this.selact = function() {
		var val = kj.obj("#id_selact").value;
		if(val == 'send') {
			this.on_send();
		} else {
			admin.selact();
		}
	}
	this.on_send = function() {
		var arr = kj.obj("input<<name,selid[]");
		this.selid  = [];
		for(var i = 0 ; i < arr.length; i++ ){
			if(arr[i].checked) this.selid[this.selid.length] = arr[i].value;
		}
		if(thisjs.selid.length<1) {
			kj.alert("没有选择需要发送的邮件");
			return;
		}
		kj.show("#id_send_act");
		kj.progress.show1.open({id:'send_row',title:'发送进度',size:this.selid.length,w:500});
		thisjs.send_row();
	}
	//按行发送
	this.send_row = function() {
		if( thisjs.selid.length < 0 ) return;
		if( thisjs.selid.length == 0 ) {
			kj.progress.show1.close('send_row');
			this.send_stop();
			kj.alert.show("发送完成");
			return;
		}
		this.row_id = thisjs.selid[0];
		thisjs.selid.removeat(0);
		kj.ajax.get("?app=email&app_module=other&app_act=send_info&id=" + this.row_id , function(data){
			var obj_data=kj.json(data);
			if(obj_data.isnull) {
				kj.alert("发送失败");
				return;
			}
			if(obj_data.code==0) {
				thisjs.send_to = obj_data.send_to;
				thisjs.total_file = kj.toint(obj_data.total_file);
				thisjs.send_mode = obj_data.mode;
				if(thisjs.send_mode!='0') {
					kj.progress.show1.open({id:'send_file',title:obj_data.title,size:thisjs.total_file,w:300});
					thisjs.total_page = 0;
					thisjs.file_path = obj_data.file_path;
					thisjs.send_file_msg();
				} else {
					kj.progress.show1.open({id:'send',title:obj_data.title,size:thisjs.send_to.length,w:300});
					this.send_index = 0;
					thisjs.total_page = 0;
					thisjs.send_msg();
				}
			} else {
				alert(obj_data.msg);
				return;
			}
		});
	}
	//发送指定id , 记录邮件，并拆分
	this.send_file_msg = function() {
		if( this.total_page >= this.total_file ) {
			kj.progress.show1.close('send_file');
			thisjs.send_row();
			return;
		}
		if( this.total_page+1 >= this.total_file ) {
			kj.progress.show1.step('send_row');
		}		
		this.total_page++;
		var url;
		if(thisjs.send_mode==1) {
			url = "?app=email&app_module=other&app_act=send_file_info&path=" + this.file_path + "&page=" + this.total_page;
		} else if(thisjs.send_mode==2){
			url = "?app=email&app_module=other&app_act=send_user_info&id=" + this.row_id + "&page=" + this.total_page;
		} else {
			kj.progress.show1.close('send_file');
			kj.progress.show1.close('send_row');
			this.send_stop();
			alert("发送模式不存在");
			return;
		}
		kj.ajax.get(url , function(data){
			var obj_data=kj.json(data);
			if(obj_data.isnull) {
				kj.alert("发送失败");
				return;
			}
			if(obj_data.code==0) {
				thisjs.send_to = obj_data.send_to;
				kj.progress.show1.open({id:'send',title:'正在发送',size:thisjs.send_to.length,w:300});
				this.send_index = 0;
				thisjs.send_msg();
			} else {
				alert(obj_data.msg);
				return;
			}
		});
	}

	//发送指定id , 记录邮件，并拆分
	this.send_msg = function() {
		if(this.pause) return;
		if( thisjs.send_to.length < 1 ) {
			kj.progress.show1.close('send');
			if(this.total_file) {
				thisjs.send_file_msg();
			} else {
				thisjs.send_row();
			}
			return;
		}
		if( thisjs.send_to.length < 2 ) {
			if(this.total_file) {
				kj.progress.show1.step('send_file');
			} else {
				kj.progress.show1.step('send_row');
			}
		}		

		var send_to = this.send_to[0];
		this.send_to.removeat(0);
		//收件箱来源于文件模式
		kj.ajax.post("?app=email&app_module=other&app_act=send&id=" + this.row_id + "&page=" + this.total_page + "&index=" + this.send_index, {'send_to':send_to} ,function(data){
			kj.progress.show1.step('send');
			this.send_index++;
			thisjs.send_msg();
		});
	}
	//暂停
	this.send_pause = function(o) {
		if(o.value=='暂停') {
			this.pause = true;
			o.value = '开始';
		} else {
			this.pause = false;
			o.value = '暂停';
			this.send_msg();
		}
	}
	//结束
	this.send_stop = function() {
		this.pause = true;
		kj.hide("#id_send_act");
		kj.obj("#id_pause").value = '暂停';
		kj.progress.show1.close('send');
		kj.progress.show1.close('send_file');
		kj.progress.show1.close('send_row');
		this.selid = [];
		this.send_to = [];
		this.total_file = 0;
		this.file_path = '';
		this.pause = false;
	}

}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>