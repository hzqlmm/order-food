<?php include cls_resolve::on_resolve('/admin\/header')?>
<style>
.me_div1{float:left;width:95%;border-bottom:1px #cccccc dotted;padding:10px 0px 5px 0px}
.me_div1 li{float:left;text-align:left;padding:8px 0px 5px 20px;color:#888888;clear:both;}
.me_div1 .x_1{float:left;width:100px}
.me_div1 .x_2{float:left;width:100px}
.me_div1 .x_3{float:left;width:200px}
.me_div1 .x_4{float:left;width:100px}
.me_div1 .x_tit{float:left;font-weight:bold;color:#004499;width:100px}
.me_div1 .x_more{float:left;color:#000000}
.me_div1 .x_new{color:#ff8800}
</style>
<div class="pMenu" id="id_pMenu">
	<li class="sel">已安装</li>
	<li onclick="admin.act('not');">未安装</li>
</div>
<div class="pMain" id="id_main">
<?php foreach($arr_list['installed'] as $com => $item){ ?>
	<div class="me_div1">
		<li><span class="x_tit"><?php echo $item['name'];?></span><span class="x_more"><img src="/webcss/admin/images/1.png">&nbsp;<a href="javascript:parent.thisjs.klkkdj_open('<?php echo $item['name'];?> 版本：<?php echo $item['version'];?>','app=service&app_act=logo&key=com.<?php echo $com;?>.<?php echo $item['version'];?>');" style="color:#ff8800">介绍</a></span></li>
		<li><span class="x_1">作者：<?php echo $item['author'];?></span><span class="x_2">版本：<?php echo $item['version'];?></span><span class="x_3">安装时间：<?php echo $item['updatetime'];?></span>
			<span class="x_4"><input type="button" name="dosubmit" value="卸载" onclick="thisjs.unstall('<?php echo $com;?>','<?php echo $item['name'];?>');" class="pBtn"></span>
		</li>
		<?php if(isset($arr_list['all'][$com]) && $arr_list['all'][$com]['version']>$item['version']){?>
		<li class="x_new">最新版本：<?php echo $arr_list['all'][$com]['version'];?><?php if(!stristr($arr_list['all'][$com]['version'],'.')){?>.0<?php }?>&nbsp;&nbsp;发布时间：<?php echo $arr_list['all'][$com]['pubtime'];?>&nbsp;&nbsp;<input type="button" name="dosubmit" value="开始更新" onclick="thisjs.update('<?php echo $com;?>');" class="pBtn">&nbsp;&nbsp;<input type="button" name="dosubmit" value="查看详情" onclick="admin.selact();" class="pBtn"> </li>
		<?php }?>
	</div>
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
		kj.dialog({id:'update',title:'更新组件',url:'<?php echo fun_get::url(array("app_act"=>"step1"));?>&com='+com,w:600,showbtnhide:false,h:300,type:'iframe'});
	}

	this.unstall = function(com , com_name) {
		if(!confirm('确定要卸载吗？')) return;
		if( this.is_step ) {
			alert("正在卸载" + this.com_name + "...");
			return;
		}
		this.com = com;
		this.com_name = com_name;
		this.is_step = true;
		kj.ajax.get("<?php echo fun_get::url(array('app_act'=>'uninstall_steps'));?>&com="+com , function(data) {
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
			kj.alert.show("卸载完成" , function(){thisjs.refresh()});
			thisjs.is_step = false;
			return;
		}
		kj.ajax.get("<?php echo fun_get::url(array('app_act'=>'uninstall'));?>&step=" + this.step + "&com=" + this.com , function(data){
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
		kj.dialog({id:'update',title:'升级组件',url:'<?php echo fun_get::url(array("app_act"=>"step1"));?>&com='+com,w:600,showbtnhide:false,h:300,type:'iframe'});
	}
	//安装，刷新
	this.refresh = function() {
		parent.thisjs.reload();
	}

}
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>