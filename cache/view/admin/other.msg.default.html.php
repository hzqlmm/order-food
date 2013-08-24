<?php include cls_resolve::on_resolve('/admin\/header')?>
<style>
.me_info{float:left;width:90%;text-align:left;padding-left:5px;margin-top:5px;font-weight:bold}
.me_beta{float:left;width:90%;text-align:left;padding-left:35px;margin-bottom:10px;margin-top:5px;color:#888888}
.me_content{float:left;width:90%;text-align:left;padding:8px 0px 5px 30px}
.me_act{float:left;width:90%;text-align:left;padding:8px;border-bottom:1px #cccccc solid}
.me_return{float:left;width:90%;text-align:left;padding:8px 0px 5px 0px}
.me_return .return_content{text-align:left;float:left;padding:5px;width:90%;color:#ff8800;border:1px #cccccc dotted;margin-left:30px}
.me_return .return_beta{width:90%;text-align:left;float:left;padding:5px 0px 0px 30px;color:#888888}
</style>
<div class="pMenu" id="id_pMenu">
	<input type="hidden" name="url_type" value="<?php echo $type;?>" id="id_url_type">
	<?php foreach($arr_type as $item=>$key){ ?>
	<li<?php if($type==$key){?> class="sel"<?php }?> onclick="kj.obj('#id_url_type').value='<?php echo $key;?>';admin.act('');"><?php echo $item;?></li>
	<?php }?>
	<li onclick="admin.menu_display(0);" class="x_btn">查找</li>
</div>
<div class="btnMenuDiv" id="id_btnMenuDiv"<?php if($arr_list['issearch']==0){?> style="display:none"<?php }?>>
<li>时间：<input type="text" id="s_time1" name="s_time1" value="<?php echo fun_get::get('s_time1');?>" class='pTxtDate' onfocus="new Calendar().show(this);"> 到 <input type="text" name="s_time2" id="s_time2" value="<?php echo fun_get::get('s_time2');?>" class='pTxtDate' onfocus="new Calendar().show(this);"></li>
<li>状　态：<select name="s_state">
	<option value="">不限</option>
		<option value='1'<?php if(fun_get::get("s_state")==1){?> selected<?php }?>>未回复</option>";
		<option value='2'<?php if(fun_get::get("s_state")==2){?> selected<?php }?>>已回复</option>";
	</select></li>
<li>关 键 字：<input type="text" id="s_key" name="s_key" value="<?php echo fun_get::get('s_key');?>" class='pTxt1'>　<input type="button" name="btn_s_ok" value="查找" class="pBtn" onclick="admin.search();"> 　<input type="button" name="btn_s_clear" value="清空" class="pBtn" onclick="admin.clear_search();"></li>
</div>
<div class="pMain" id="id_main">
	<?php foreach($arr_list['list'] as $item){ ?>
	<div class="me_info"><input type="checkbox" name="selid[]" value="<?php echo $item["msg_id"];?>"> 姓名：<?php echo $item["msg_name"];?>&nbsp;&nbsp;&nbsp;&nbsp;电话：<?php echo $item["msg_tel"];?>&nbsp;&nbsp;&nbsp;&nbsp;邮箱：<?php echo $item["msg_email"];?></div>
	<div class="me_content"><?php echo $item["msg_cont"];?></div>
	<div class="me_beta">时间：<?php echo $item["msg_time"];?>　　IP：<?php echo $item["msg_ip"];?></div>
	<div class="me_return" id="id_return_<?php echo $item["msg_id"];?>">
	<?php if(!empty($item["msg_recont"])){?>
		<div class="return_content"><?php echo $item["msg_recont"];?></div>
		<div class="return_beta">&nbsp;&nbsp;时间：<?php echo $item["msg_retime"];?></div>
	<?php }?>
	</div>
	<div class="me_act" onmousedown="frmSet('id','')"><a href="javascript:master_open({id:<?php echo $item['msg_id'];?>,app_act:'return',title:'回复',w:600});">回复</a>　<a href="javascript:admin.ajax_delete(<?php echo $item['msg_id'];?>);">删除</a></div>
	<?php }?>
</div>
<div class="pFootAct" id="id_pFootAct">
	<li>
	<label><input type='checkbox' name='selall' value='1'>全选</label>　
	<select name="selact" onchange="thisjs.selact(this.value)">
	<option value="">--操作--</option>
	<?php if($this_limit->chk_act("del")){?><option value="delete">删除</option><?php }?>
	</select>&nbsp;
	&nbsp;<input type="button" name="dosubmit" value="执行" onclick="admin.selact();" class="pBtn">
	</li>
</div>
<?php include cls_resolve::on_resolve('/admin\/footer')?>