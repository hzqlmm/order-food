<?php include cls_resolve::on_resolve('/admin\/header')?>
<style>
.beta{float:left;width:400px;padding:10px 0px 0px 10px;line-height:25px};
.me_div1{}
.me_div1 li{float:left;margin:5px 0px 5px 10px;border:1px #cccccc solid;padding:5px;background:#efefef}
</style>
<div class="pMenu" id="id_pMenu">
<!--切换保留数据-->
<input type="hidden" value="" name="url_module">
<?php foreach($arr_module as $item=>$key){ ?>
	<li onclick="kj.set(':url_module','value','<?php echo $item;?>');admin.act('');"><?php echo $key;?></li>
<?php }?>
<li class="sel" onclick="admin.act('print');">打印设置</li>
</div>
<div class="pMain" id="id_main">
<table class='pEditTable'>
<tr class='pTabTitRow'><td class='pTabTitCol' colspan="3"></td></tr>
<tr>
	<td class="pTabColName">小票内容：</td>
	<td class="pTabColVal"><textarea name="printinfo" id="shop_printinfo" cols="60" rows="22"><?php echo $print_info;?></textarea></td>
	<td class="pTabColVal" valign="top">
	<div style="float:left;width:90%;color:#888888;line-height:25px">格式如：{订单号} 这样的文字在这里称为变量，系统预先设置好的，在打印时会根据相关信息自动替换过来,目前支持以下变量，请点击插入</div>
	<div class="me_div1" style="float:left;width:100%">
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{订单号}');" title="点击插入变量">订单号</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{大厦}');" title="点击插入变量">大厦</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{楼层}');" title="点击插入变量">楼层</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{公司}');" title="点击插入变量">公司</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{部门}');" title="点击插入变量">部门</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{客户称呼}');" title="点击插入变量">客户称呼</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{送餐地址}');" title="点击插入变量">送餐地址</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{客户电话}');" title="点击插入变量">客户电话</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{固话}');" title="点击插入变量">固话</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{手机}');" title="点击插入变量">手机</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{下单时间}');" title="点击插入变量">下单时间</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{指定时间信息}');" title="点击插入变量">指定时间信息</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{打印时间}');" title="点击插入变量">打印时间</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{应收金额}');" title="点击插入变量">应收金额</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{菜品列表}');" title="点击插入变量">菜品列表</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{优惠活动}');" title="点击插入变量">优惠活动</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{积分抵扣}');" title="点击插入变量">积分抵扣</a></li>
	<li><a href="javascript:kj.textarea_insertstr('#shop_printinfo','{发票信息}');" title="点击插入变量">发票信息</a></li>
	</div>
	</td></tr>
<tr>
	<td class="pTabColName">宽度：</td>
	<td class="pTabColVal" colspan="2"><input type="text" name="width" value="<?php echo $width;?>"  class='pTxt1 pTxtL150'>
	</td></tr>
<tr>
	<td class="pTabColName"></td>
	<td class="pTabColVal" colspan="2"><input type="button" name="print_test" value="测试打印" onclick="thisjs.print();" class="pBtn">
	</td></tr>
</table>
</div>
<div class="pFootAct" id="id_pFootAct">
<li><?php if($this_limit->chk_act("update")){?>&nbsp;<input type="button" name="dosubmit" value="更新" onclick="admin.frm_ajax('update_print');" class="pBtn"><?php }?></li>
</div>
<script>
var thisjs = new function() {
	this.print = function() {
		var action = document.frm_main.action;
		document.frm_main.action = "./common.php?app_module=meal&app=call&app_act=print.test";
		document.frm_main.target = "_blank";
		document.frm_main.submit();
		document.frm_main.action = action;
		document.frm_main.target = '_self';
	}
}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>