<?php include cls_resolve::on_resolve('/admin\/header')?>
<style>
.me_div1{float:left;border-bottom:1px #cccccc dotted;clear:both;margin:10px 0px 10px 0px}
.me_div1 td{text-align:left;color:#888888;line-height:25px;padding:0px 20px}
.me_div1 .x_tit{font-weight:bold;color:#004499;width:200px;text-align:center;padding-top:0px}
.me_div1 .x_tit img{width:160px;height:68px;}
.me_div1 .x_more{float:left;color:#000000}
.me_div1 .x_new{color:#ff8800}
</style>
<div class="pMenu" id="id_pMenu">
	<li class="sel">已安装</li>
	<li onclick="admin.act('not');">未安装</li>
</div>
<div class="pMain" id="id_main">
<?php foreach($arr_list['installed'] as $com => $item){ ?>
	<table class="me_div1">
		<tr>
		<td class="x_tit" valign="center"><img src="<?php echo $item['pic'];?>"><br><?php echo $item['name'];?></td>
		<td valign="center">
		安装时间：<?php echo $item['installtime'];?><br>当前版本：<?php echo $item['version'];?>
		<?php if(isset($arr_list['all'][$com]) && $arr_list['all'][$com]['version']>$item['version']){?>
		<br><span style="color:#ff8800">最新版本：<?php echo $arr_list['all'][$com]['version'];?><?php if(!stristr($arr_list['all'][$com]['version'],'.')){?>.0<?php }?><br><a href="javascript:thisjs.update('<?php echo $com;?>');">开始更新</a>&nbsp;&nbsp;<a href="javascript:thisjs.update('<?php echo $com;?>');">查看详情</a></span>
		<?php }?>
		</td>
		<td valign="center">状态：<?php if(!empty($item['state'])){?>已开启<?php } else { ?><font style="color:#ff0000">已关闭</font><?php }?></td>
		<td valign="center">
			<input type="button" name="btnExe" value="配置" onclick="master_open({id:'<?php echo $com;?>',title:'编辑配置',w:800,url:'<?php echo fun_get::url(array('payname'=>$com,'app_act'=>'config'));?>'});" class="pBtn">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="btnExe" value="卸载" onclick="thisjs.unstall('<?php echo $com;?>','<?php echo $item['name'];?>');" class="pBtn">
		</td>
		</tr>
	</table>
<?php }?>
</div>
<script src="/webcss/common/js/kj.progress.js"></script>
<script>
var thisjs = new function() {
	this.steps = [];
	this.step = 0;
	this.com = '';
	this.com_name = '';
	this.is_step = false;
	this.update = function(com) {
		kj.dialog({id:'update',title:'更新组件',url:'<?php echo fun_get::url(array("app_act"=>"step1"));?>&payname='+com,w:600,showbtnhide:false,h:300,type:'iframe'});
	}

	this.unstall = function(com , com_name) {
		if( this.is_step ) {
			alert("正在卸载" + this.com_name + "...");
			return;
		}
		this.com = com;
		this.com_name = com_name;
		this.is_step = true;
		kj.ajax.get("<?php echo fun_get::url(array('app_act'=>'uninstall_steps'));?>&payname="+com , function(data) {
			var obj_data = kj.json(data);
			if(obj_data.isnull) {
				alert("卸载失败");
				thisjs.is_step = false;
			} else {
				if('code' in obj_data && obj_data.code=='0') {
					var html = '';
					thisjs.steps = obj_data.steps;
					kj.progress.show1.open({id:'unstall',title:'正在卸载',size:thisjs.steps.length,w:500});
					thisjs.unstall_step();
				} else {
					if('msg' in obj_data) {
						alert(obj_data.msg);
					} else {
						alert("卸载失败");
					}
					thisjs.is_step = false;
				}
			}
		});
	}
	this.unstall_step = function() {
		if(this.step>=this.steps.length) {
			kj.progress.show1.close('unstall');
			kj.alert.show("卸载完成", function(){thisjs.refresh()});
			thisjs.is_step = false;
			return;
		}
		kj.ajax.get("<?php echo fun_get::url(array('app_act'=>'uninstall'));?>&step=" + this.step + "&payname=" + this.com , function(data){
			var obj_data = kj.json(data);
			if(obj_data.isnull) {
				alert("卸载失败");
				kj.progress.show1.close('unstall');
				thisjs.is_step = false;
			} else {
				if('code' in obj_data && obj_data.code=='0') {
					thisjs.step++;
					kj.progress.show1.step('unstall');
					thisjs.unstall_step();
				} else {
					if('msg' in obj_data) {
						alert(obj_data.msg);
					} else {
						alert("卸载失败");
					}
					kj.progress.show1.close('unstall');
					thisjs.is_step = false;
				}
			}

		});
	}
	//更新
	this.update = function(com) {
		kj.dialog({id:'update',title:'更新',url:'<?php echo fun_get::url(array("app_act"=>"step1"));?>&payname='+com,w:600,showbtnhide:false,h:300,type:'iframe'});
	}
	//安装，刷新
	this.refresh = function() {
		parent.thisjs.reload();
	}

}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>