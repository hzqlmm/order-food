<?php include cls_resolve::on_resolve('/admin\/header')?>
<script src="/webcss/components/report/jquery.min.js"></script>
<style>
.me_div1{float:left;margin:10px 0px 10px 20px}
</style>
<div class="pMenu" id="id_pMenu">
	<li<?php if(fun_get::get("channel")==''){?> class="sel"<?php }?> onclick="admin.refresh_url(['channel=']);">订单量</li>
	<li<?php if(fun_get::get("channel")=='money'){?> class="sel"<?php }?> onclick="admin.refresh_url(['channel=money']);">成交额</li>
</div>
<div class="btnMenuDiv" id="id_btnMenuDiv">
		<div class="me_div1">
		统计模式：<label><input type="radio" name="mode" value="day" checked onclick="admin.refresh_url(['mode=']);">按天</label>&nbsp;&nbsp;<label><input type="radio" name="mode" value="month"<?php if($mode=='month'){?> checked<?php }?> onclick="admin.refresh_url(['mode=month']);">按月</label>&nbsp;&nbsp;<label><input type="radio" name="mode" value="year"<?php if($mode=='year'){?> checked<?php }?> onclick="admin.refresh_url(['mode=year']);">按年</label>
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
</div>
<div id="container" style="float:left;width:800px;height:300px">
</div>
<script type="text/javascript">
//店铺选择回调函数
function shop1_callback(o) {
	if("id" in o) {
		kj.set("#id_url_shop_id" , "value" , o.id);
		admin.refresh();
	}
}

$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'line'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: <?php echo $report['sub'];?>
            },
            yAxis: {
                title: {
                    text: '<?php if(fun_get::get("channel")=="money"){?>成效额<?php } else { ?>订单量<?php }?>'
                }
            },
            tooltip: {
                enabled: false,
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ this.y;
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                name: '<?php if(fun_get::get("channel")=="money"){?>成效额统计<?php } else { ?>订单量统计<?php }?>',
                data: <?php echo $report['data'];?>
            }]
        });
    });
    
});
		</script>
<script src="/webcss/components/report/highcharts.js"></script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>