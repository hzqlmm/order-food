<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>上传附件</title>
<meta name="keywords" content="" />
<link rel="stylesheet" type="text/css" href="/webcss/common/images/common.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/css.css"/>
<script src="<?php echo cls_config::get("dirpath","base");?>/common.php?app=sys&app_act=web.config&app_ajax=1"></script>
<script src="/webcss/common/js/kj.js"></script>
<style>
body{text-align:left}
</style>
</head>
<body>
<form name="frmupload" method="post" action="<?php echo fun_get::url();?>" enctype="multipart/form-data">
<input type="hidden" name="objid" value="<?php echo $objid;?>"><input type="hidden" name="objpic" value="<?php echo $objpic;?>">
<input type="file" name="file_1" id="file_1"><input type="submit" name="subok" value="上传">
</form>
<?php if(!empty($uploadinfo)){?> 
<script>
	<?php if($uploadinfo["code"] == 0){?>
		//如果有传callback，则自动调用回调函数 默认：upload_callback
		if(window.parent) {
			if( '<?php echo $get_callback;?>' in window.parent ) window.parent.<?php echo $get_callback;?>('<?php echo $uploadinfo["info"];?>');
			if('<?php echo $objid;?>'!='') {
				var objid = window.parent.document.getElementById("<?php echo $objid;?>");
				objid.value = '<?php echo $uploadinfo["list"]["url"];?>';
			}
			if('<?php echo $objpic;?>'!='') {
				var objpic = window.parent.document.getElementById("<?php echo $objpic;?>");
				objpic.value = kj.url_view('<?php echo $uploadinfo["list"]["url"];?>');
			}
		} else if(window.opener){
			if( '<?php echo $get_callback;?>' in window.opener ) window.opener.<?php echo $get_callback;?>('<?php echo $uploadinfo["info"];?>');
			if('<?php echo $objid;?>'!='') {
				var objid = window.opener.document.getElementById("<?php echo $objid;?>");
				objid.value = '<?php echo $uploadinfo["list"]["url"];?>';
			}
			if('<?php echo $objpic;?>'!='') {
				var objpic = window.opener.document.getElementById("<?php echo $objpic;?>");
				objpic.value = kj.url_view('<?php echo $uploadinfo["list"]["url"];?>');
			}
		}
	<?php } else { ?>
		alert("<?php echo $uploadinfo['msg'];?>");
	<?php }?>
</script>
<?php }?>
</body>
</html>