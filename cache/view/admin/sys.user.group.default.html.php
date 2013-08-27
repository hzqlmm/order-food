<?php include cls_resolve::on_resolve('/admin\/header')?>
<script src="/webcss/common/js/kj.layer.js"></script>
<script>
kj.layer.move = function(id) {
	var obj = {};
	obj.title='移动分组';
	obj.type='iframe';
	obj.top = kj.y;
	obj.left = kj.x;
	obj.h=100;
	obj.w=<?php echo $arr_group["maxlayer"]*50+150;?>;
	if(obj.w < 200) obj.w=200;
	obj.url = "?app=<?php echo $app;?>&app_module=<?php echo $app_module;?>&app_act=move_open&id=" + id;
	obj.id = 'openmove_' + id;
	//obj.showbtnhide=true;
	kj.dialog(obj);
}
kj.onload(function(){
	kj.handler(document.documentElement,"mousedown",function(e){
		oEvent=e||event;
		kj.x=oEvent.clientX;
		kj.y=oEvent.clientY;
	});
});

</script>
<div class="pMenu" id="id_pMenu">
	<li class="sel" onclick="admin.act('');">管理</li>
</div>
<div class="pMain" id="id_main">
	<div class="pLayer" id="id_layer">
	<li style="display:none" id="id_layer_default">
		<span class="padding_1">&nbsp;<input type="hidden" name="" value="1"></span>
		<span class="xx_1"><input type="hidden" name="group_id[]" value=""><input type="hidden" name="group_id_layer[]" value="id_layer_default"><input type="hidden" name="pid[]" value=""><input type="text" name="group_sort[]" value="" class='pTxt1 pTxtL20'></span>
		<span class="xx_1"><input type="text" name="group_name[]" value="" class='pTxt1 pTxtL150'></span><span class="xx_1"><a href="javascript:kj.layer.add('#id_layer_default',50);"><img src="/webcss/admin/images/add.gif"></a>&nbsp;&nbsp;<a href="javascript:kj.layer.remove('id_layer_default');"><img src="/webcss/admin/images/romove.gif"></a></span></li>
	<?php foreach($arr_group["list"] as $item){ ?>
	<li id="id_layer_<?php echo $item['group_id'];?>">
		<span class="padding_1" style="width:<?php echo $item['layer']*50-50;?>px">&nbsp;<input type="hidden" name="" value="<?php echo $item['layer'];?>"></span>
		<span class="xx_1"><input type="hidden" name="group_id[]" value="<?php echo $item['group_id'];?>"><input type="hidden" name="group_id_layer[]" value="id_layer_<?php echo $item['group_id'];?>"><input type="hidden" name="pid[]" value="<?php if($item['group_pid']>0){?>id_layer_<?php echo $item['group_pid'];?><?php }?>"><input type="text" name="group_sort[]" value="<?php echo $item['group_sort'];?>" class='pTxt1 pTxtL20'></span>
		<span class="xx_1"><input type="text" name="group_name[]" value="<?php echo $item['group_name'];?>" class='pTxt1 pTxtL150'></span><span class="xx_1"><a href="javascript:kj.layer.add('#id_layer_<?php echo $item['group_id'];?>',50);"><img src="/webcss/admin/images/add.gif"></a>&nbsp;&nbsp;<a href="javascript:kj.layer.remove('id_layer_<?php echo $item['group_id'];?>');"><img src="/webcss/admin/images/romove.gif"></a>&nbsp;&nbsp;<a href="javascript:kj.layer.move(<?php echo $item['group_id'];?>);">移动</a>&nbsp;&nbsp;<a href="javascript:master_open({id:<?php echo $item['group_id'];?>,title:'设置权限',w:600,app_act:'limit_edit'});">编辑</a></span></li>
	<?php }?>
	</div>
	<div class="pLayerAdd"><a href="javascript:kj.layer.add();"><img src="/webcss/admin/images/add.gif"> 添加新组</a></div>
</div>
<div class="pFootAct" id="id_pFootAct">
<li>
<?php if($this_limit->chk_act("update")){?>&nbsp;<input type="button" name="dosubmit" value="保存" onclick="admin.frm_ajax('save_all');" class="pBtn"><?php }?>
</li>
</div>
<?php include cls_resolve::on_resolve('/admin\/footer')?>