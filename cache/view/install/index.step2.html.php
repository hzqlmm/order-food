<?php include cls_resolve::on_resolve('/install\/header')?>
<div class="left">
<li class="x_over">1.安装协议</li>
<li class="x_sel">2.环境检测</li>
<li>3.配置参数</li>
<li>4.开始安装</li>
<li>5.完成安装</li>
</div>
<div class="div_box1">
	<table class="x_table1">
		<tr class="x_tit"><td>项目名称</td><td>所需要环境</td><td>当前环境</td></tr>
		<tr><td>PHP版本</td><td>>=4.3 <font style="color:#ff0000">*</font></td><td id="id_php_version"><?php if($server_info['php_version']>4.3){?><img src="<?php echo $webcss_url;?>/install/images/yes.gif"><?php } else { ?><img src="<?php echo $webcss_url;?>/install/images/no.gif"><?php }?> <span class="pBeta"><?php echo $server_info['php_version'];?></span></td></tr>
		<tr><td>/<?php echo $dir_info['data']['name'];?> 目录</td><td>可读写 <font style="color:#ff0000">*</font></td><td id="id_dir_data"><?php if($dir_info['data']['write']>2){?><img src="<?php echo $webcss_url;?>/install/images/yes.gif"><?php } else { ?><img src="<?php echo $webcss_url;?>/install/images/no.gif"><?php }?></td></tr>
		<tr><td>/<?php echo $dir_info['cache']['name'];?> 目录</td><td>可读写 <font style="color:#ff0000">*</font></td><td id="id_dir_cache"><?php if($dir_info['cache']['write']>2){?><img src="<?php echo $webcss_url;?>/install/images/yes.gif"><?php } else { ?><img src="<?php echo $webcss_url;?>/install/images/no.gif"><?php }?></td></tr>
		<tr><td>/<?php echo $dir_info['upload']['name'];?> 目录</td><td>可读写 <font style="color:#ff0000">*</font></td><td id="id_dir_upload"><?php if($dir_info['upload']['write']>2){?><img src="<?php echo $webcss_url;?>/install/images/yes.gif"><?php } else { ?><img src="<?php echo $webcss_url;?>/install/images/no.gif"><?php }?></td></tr>
		<tr><td>zip压缩</td><td>建议开启</td><td>
		<?php if($server_info['zip']){?><img src="<?php echo $webcss_url;?>/install/images/yes.gif"><?php } else { ?><img src="<?php echo $webcss_url;?>/install/images/no.gif"><?php }?>
		</td></tr>
		<tr><td>GD库版本</td><td>建议开启</td><td id="id_gd_version">
		<?php if(!empty($server_info['gd_info'])){?>
		<?php if($server_info['gd_info']>'1.0'){?><img src="<?php echo $webcss_url;?>/install/images/yes.gif"><?php } else { ?><img src="<?php echo $webcss_url;?>/install/images/no.gif"><?php }?> <span class="pBeta"><?php echo $server_info['gd_info'];?></span>
		<?php } else { ?>
		<img src="<?php echo $webcss_url;?>/install/images/no.gif"> <span class="pBeta">未开启</span>
		<?php }?>
		</td></tr>
		<tr><td>/<?php echo $dir_info['lib']['name'];?> 目录</td><td>可读写</td><td><?php if($dir_info['lib']['write']>2){?><img src="<?php echo $webcss_url;?>/install/images/yes.gif"><?php } else { ?><img src="<?php echo $webcss_url;?>/install/images/no.gif"><?php }?></td></tr>
		<tr><td>/<?php echo $dir_info['app']['name'];?> 目录</td><td>可读写</td><td><?php if($dir_info['app']['write']>2){?><img src="<?php echo $webcss_url;?>/install/images/yes.gif"><?php } else { ?><img src="<?php echo $webcss_url;?>/install/images/no.gif"><?php }?></td></tr>
	</table>
	<div class="div_action"><input type="button" name="btn1" value="上一步" class="btn_1" onclick="location='<?php echo fun_get::url(array('app_act'=>''));?>';">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="btn1" value="下一步" class="btn_1" onclick="thisjs.next();"></div>
</div>
<script>
var thisjs = new function() {
	this.next = function() {
		var html = kj.obj("#id_php_version").innerHTML;
		if( html.indexOf("no.gif")>=0 ) {
			alert("PHP版本必须大于4.3");
			return false;
		}
		html = kj.obj("#id_dir_data").innerHTML;
		if( html.indexOf("no.gif")>=0 ) {
			alert("需要支持<?php echo $dir_info['data']['name'];?>目录可读写");
			return false;
		}
		html = kj.obj("#id_dir_cache").innerHTML;
		if( html.indexOf("no.gif")>=0 ) {
			alert("需要支持<?php echo $dir_info['cache']['name'];?>目录可读写");
			return false;
		}
		html = kj.obj("#id_dir_upload").innerHTML;
		if( html.indexOf("no.gif")>=0 ) {
			alert("需要支持<?php echo $dir_info['upload']['name'];?>目录可读写");
			return false;
		}
		location = "<?php echo fun_get::url($config_info);?>";
	}
}
</script>
</body>
</html>