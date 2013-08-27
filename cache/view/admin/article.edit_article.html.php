<?php include cls_resolve::on_resolve('/admin\/header')?>
<input type="hidden" name="url_channel_id" value="<?php echo fun_get::get('url_channel_id');?>">
<input type="hidden" name="url_folder_id" value="<?php echo fun_get::get('url_folder_id');?>">

<?php if($channel_mode==1){?>
	<?php include cls_resolve::on_resolve('/admin\/article.edit.pic')?>
<?php } else if($channel_mode==2) { ?>
	<?php include cls_resolve::on_resolve('/admin\/article.edit.cont')?>
<?php } else { ?>
	<?php include cls_resolve::on_resolve('/admin\/article.edit.article')?>
<?php }?>

<script>
function upload_callback(info){
	var obj = kj.json(info);
	var objx;
	if('url' in obj) {
		kj.obj("#id_article_pic_big").value=obj.url;
		objx = kj.obj("#id_img_pic_big");
		if(objx) objx.src = kj.url_view(obj.url);
	}
	if('url_small' in obj) {
		kj.obj("#id_article_pic").value=obj.url_small;
		objx = kj.obj("#id_img_pic");
		if(objx) objx.src = kj.url_view(obj.url_small);
	}
}
function upload_callback_small(info){
	var obj = kj.json(info);
	if('url' in obj) kj.obj("#id_article_pic").value=obj.url;
}
function attatch_callback(data) {
	if( 'objid' in data && 'url' in data ) {
		var obj
		if(data.objid == 'id_article_pic_big') {
			obj = kj.obj("#id_img_pic_big");
			if(obj) obj.src = kj.url_view(data.url);
		}
		if(data.objid == 'id_article_pic') {
			obj = kj.obj("#id_img_pic");
			if(obj) obj.src = kj.url_view(data.url);
		}
	}
	kj.dialog.close("#windialog_attatch");
}

function templates_callback(info) {
	kj.dialog.close("#windialog_attatch");
}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>