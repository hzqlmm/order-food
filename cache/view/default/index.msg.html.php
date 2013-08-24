<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo cls_config::get("site_title","sys");?></title>
<meta name="keywords" content="<?php echo cls_config::get("keywords","sys");?>"/>
<meta name="description" content="<?php echo cls_config::get("description","sys");?>"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/common.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/expand.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/default/images/main.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/default/images/css.css"/>
<script src="<?php echo cls_config::get("dirpath","base");?>/common.php?app=sys&app_act=web.config&app_ajax=1"></script>
<script src="/webcss/common/js/kj.js"></script>
<script src="/webcss/common/js/kj.ajax.js"></script>
<script src="/webcss/common/js/kj.dialog.js"></script>
<style>
.metable{float:left;width:100%}
.metable td{}
.mebox1{float:left;width:240px;background:#fff;font-size:14px}
.mebox1 a{float:left;width:218px;border-top:1px #ccc dotted;padding:8px 0px 5px 20px}
.mebox1 .xtit{float:left;border-top:0px;padding:8px 0px 5px 10px;width:228px;font-size:14px;font-weight:bold}
.mebox2{float:left;width:698px;background:#fff;margin:0px 0px 10px 10px;border:1px #ccc solid;padding:0px 15px 20px 15px}
.mebox2 .xtitle{float:left;width:100%;border-bottom:1px #ccc dotted;font-size:18px;font-weight:bold;padding:5px 0px 5px 0px}
.mebox2 .xcont{float:left;width:100%;line-height:22px;margin:10px 0px 10px 0px;font-size:14px;color:#333;height:400px}
.mebox1 ul{float:left;width:240px}
.mebox2 td{padding:5px;}
</style>
</head>
<body>
<?php include cls_resolve::on_resolve('/default\/header')?>
<div class="mebox1">
<ul>
<li class="xtit">帮助中心</li>
</ul>
<ul>
<?php foreach($arr_help as $item){ ?>
<a href="?app_act=help&id=<?php echo $item['article_id'];?>"><?php echo $item['article_title'];?></a>
<?php }?>
<a href="?app_act=msg" style="color:#ff0000;font-weight:bold">顾客留言</a>
</ul>
</div>
<div class="mebox2">
	<div class="xtitle">欢迎您加盟我们一起发展</div>
	<div class="xcont" id="id_xcont">
	<form name="frm_2" action="?app=ajax&app_act=msg_save" method="post">
			<li id="id_msg_cont"><span class="x_1"></span><span class="x_2" style='padding-left:20px'>
				<table><tr><td>留言内容：</td><td><textarea name="cont" cols="50" rows="5" class="pTxt1" ruletips='留言内容不能为空' required></textarea></td></tr>
				<tr><td>您的姓名：</td><td><input type="text" name="name" value="" class="pTxt1 pTxtL200"<?php if(in_array('name',$options)){?> ruletips='请填写您的姓名' required<?php }?>>
				<?php if(in_array('name',$options)){?><span style="color:#ff0000">*</span><?php } else { ?><span class="pBeta">&nbsp;(选填)</span><?php }?></td></tr>
				<tr><td>电子邮箱：</td><td>
				<input type="text" name="email" value="" class="pTxt1 pTxtL200" ruletips='电子邮箱填写不正确' rule='email' <?php if(in_array('email',$options)){?> required<?php }?>>
				<?php if(in_array('email',$options)){?><span style="color:#ff0000">*</span><?php } else { ?><span class="pBeta">&nbsp;(选填)</span><?php }?>
				</td></tr>
				<tr><td>联系电话：</td><td>
				<input type="text" name="tel" value="" class="pTxt1 pTxtL200" ruletips='联系电话填写不正确' rule='tel' <?php if(in_array('tel',$options)){?> required<?php }?>>
				<?php if(in_array('tel',$options)){?><span style="color:#ff0000">*</span><?php } else { ?><span class="pBeta">&nbsp;(选填)</span><?php }?>
				</td></tr>
				<tr><td>&nbsp;</td><td><input type="button" name="btn1" value="提 交" class="button4" onclick="thisjs.save();"></td></tr>
				</table>
			</span></li>
		</form>
	</div>
</div>
<script>
kj.onload(function(){
	kj.handler(".pTxt1","focus",function(){
		kj.delClassName(this , "pTxt1");
		kj.addClassName(this , "pTxt2");
	});
	kj.handler(".pTxt1","blur",function(){
		kj.delClassName(this , "pTxt2");
		kj.addClassName(this , "pTxt1");
	});
	<?php if($this_login_user->uid<1 && in_array('login',$options)){?>
		jsheader.showlogin();
	<?php }?>
});

var thisjs = new function() {
	this.save = function() {
		if(kj.rule.form(document.frm_2) == false) {
			return false;
		}
		kj.ajax.post(document.frm_2 , function(data) {
			var obj_data = kj.json(data);
			if(obj_data.isnull || !('code' in obj_data)) {
				alert("操作失败");
			} else if(obj_data.code!='0') {
				if('msg' in obj_data) {
					alert(obj_data.msg);
				} else {
					alert('操作失败');
				}
			} else {
				kj.alert.show('留言成功',function(){window.location='<?php echo cls_config::get("dirpath","base");?>/';});
				
			}
		});
	}
}
</script>
<?php include cls_resolve::on_resolve('/default\/footer')?>
</body>
</html>