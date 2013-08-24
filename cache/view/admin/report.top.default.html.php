<?php include cls_resolve::on_resolve('/admin\/header')?>
<script src="/webcss/components/report/jquery.min.js"></script>
<style>
.me_div1{float:left;margin:10px 0px 10px 20px}
.me_line{float:left;width:99%}
.me_table{float:left;width:400px;border-collapse: collapse;border:1px #cccccc solid}
.me_table td{border-top:1px #cccccc solid;border-bottom:1px #cccccc solid;padding:8px 0px 5px 0px}
.me_table .x_tit{font-weight:bold}
.me_table .x_tit td{height:20px}
.me_div2{float:left;width:402px;margin:10px;}
.me_tit{float:left;width:100%;font-weight:bold;text-align:left;font-size:14px;margin:0px 0px 10px 0px;color:#4CB1EF}

</style>
<div class="me_div1">
统计模式：<label><input type="radio" name="mode" value="day" checked onclick="admin.refresh_url(['mode=']);">按天</label>&nbsp;&nbsp;<label><input type="radio" name="mode" value="month"<?php if($mode=='month'){?> checked<?php }?> onclick="admin.refresh_url(['mode=month']);">按月</label><?php if(fun_get::get("channel")==''){?>&nbsp;&nbsp;<label><input type="radio" name="mode" value="year"<?php if($mode=='year'){?> checked<?php }?> onclick="admin.refresh_url(['mode=year']);">按年</label><?php }?>
</div>
<div class="me_div1">
	<span id="id_mode_day" style="float:left;padding-left:60px<?php if($mode=='year' || $mode=='month'){?>;display:none<?php }?>">日期：<input type="text" id="id_day_date" name="date" value="<?php echo fun_get::get('date');?>" class='pTxtDate' onfocus="new Calendar().show(this,null,function(){admin.refresh_url(['date='+kj.obj('#id_day_date').value]);});"> </span>
	<span id="id_mode_month" style="float:left;padding-left:60px<?php if($mode!='year' && $mode!='month'){?>;display:none<?php }?>">
		<select name="year" onchange="admin.refresh_url('year='+this.value);">
		<?php for($i=2012;$i<=date("Y");$i++){ ?>
		<option value="<?php echo $i;?>"<?php if(fun_get::get("year")==$i){?> selected<?php }?>><?php echo $i;?>年</option>
		<?php }?>
		</select>
	</span>
	<span id="id_mode_year" style="float:left;padding-left:20px<?php if($mode!='month'){?>;display:none<?php }?>">
		<select name="month" onchange="admin.refresh_url('month='+this.value);">
		<?php for($i=1;$i<13;$i++){ ?>
		<option value="<?php echo $i;?>"<?php if(fun_get::get("month")==$i){?> selected<?php }?>><?php echo $i;?>月</option>
		<?php }?>
		</select>
	</span>
</div>
<div class="me_line">&nbsp;</div>
<div class="me_div2">
	<div class="me_tit">用户消费排行榜</div>
	<table class="me_table">
	<tr class="x_tit"><td width=60>排名</td><td>用户名</td><td>订单量</td><td>消费额(￥)</td></tr>
	<?php for($i=0;$i<count($arr_user_top);$i++){ ?>
	<tr><td><?php echo $i+1;?></td><td><?php echo $arr_user_top[$i]['user_name'];?></td><td><?php echo $arr_user_top[$i]['num'];?></td><td><?php echo $arr_user_top[$i]['total'];?></td></tr>
	<?php }?>
	</table>
</div>
<?php include cls_resolve::on_resolve('/admin\/footer')?>