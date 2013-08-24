<?php include cls_resolve::on_resolve('/admin\/header')?>
<style>
.me_div1{float:left;width:95%;border-bottom:1px #cccccc dotted;padding:10px 0px 5px 0px}
.me_div1 li{float:left;text-align:left;padding:28px 0px 5px 20px;color:#888888;line-height:25px}
.me_div1 .x_tit{font-weight:bold;color:#004499;width:200px;text-align:center;padding-top:0px}
.me_div1 .x_tit img{width:160px;height:68px;}
.me_div1 .x_more{float:left;color:#000000}
.me_div1 .x_new{color:#ff8800}
</style>
<div class="pMenu" id="id_pMenu">
	<li onclick="admin.act('');">已安装</li>
	<li class="sel">未安装</li>
</div>
<div class="pMain" id="id_main">
<?php foreach($arr_list['not'] as $com => $item){ ?>
	<div class="me_div1">
		<li class="x_tit"><img src="<?php echo $item['pic'];?>"><br><?php echo $item['name'];?></li>
		<li>发布时间：<?php echo $item['pubtime'];?></li>
		<li>
			<input type="button" name="btnExe" value="安装" onclick="thisjs.install('<?php echo $com;?>');" class="pBtn">&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:parent.thisjs.klkkdj_open('<?php echo $item['name'];?> 版本：<?php echo $item['version'];?>','app=service&app_act=logo&key=pay.<?php echo $com;?>.<?php echo $item['version'];?>');">查看介绍</a>
		</li>
	</div>
<?php }?>
</div>
<script>
var thisjs = new function() {
	this.install = function(payname) {
		kj.dialog({id:'update',title:'安装支付接口',url:'<?php echo fun_get::url(array("app_act"=>"install.step1"));?>&payname='+payname,w:600,showbtnhide:false,h:300,type:'iframe'});
	}
	//安装完成，刷新
	this.refresh = function() {
		parent.thisjs.reload();
	}
	this.dialog = function(data , fun) {
		data.id = 'id_dialog_step';
		kj.dialog(data);
		this.dialog_fun = fun;
	}
	this.dialog_back = function(data) {
		kj.dialog.close("#winid_dialog_step");	
		if(!this.dialog_fun) return;
		var func = this.dialog_fun;
		func(data);
	}

}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>