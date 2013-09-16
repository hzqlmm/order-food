<?php include cls_resolve::on_resolve('/install\/header')?>
<div class="left">
<li class="x_sel">1.安装协议</li>
<li>2.环境检测</li>
<li>3.配置参数</li>
<li>4.开始安装</li>
<li>5.完成安装</li>
</div>
<div class="right">
	<div class="div_action"><input type="button" name="btn1" value="同意协议，并开始安装" class="btn_1" onclick="location='<?php echo fun_get::url(array('app_act'=>'step2'));?>';"></div>
</div>
</body>
</html>