<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>来单显示</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<meta name="generator" content="" />
<meta name="author" content="" />
<meta name="copyright" content="2009-2012 XXGO NET" />
<meta name="MSSmartTagsPreventParsing" content="True" />
<meta http-equiv="MSThemeCompatible" content="Yes" />
<link rel="stylesheet" type="text/css" href="/webcss/common/images/common.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/expand.css"/>
<link rel="stylesheet" type="text/css" href="/webcss/common/images/css.css"/>
<script src="<?php echo cls_config::get("dirpath","base");?>/common.php?app=sys&app_act=web.config&app_ajax=1"></script>
<script src="/webcss/common/js/kj.js"></script>
<script src="/webcss/common/js/kj.ajax.js"></script>
<script src="/webcss/common/js/kj.dialog.js"></script>
<script src="/webcss/common/js/kj.alert.js"></script>
<script src="/webcss/common/js/kj.table.js"></script>
<script src="/webcss/common/js/kj.windiv.js"></script>
<script src="/webcss/common/admin.js"></script>
<style>
.header{float:left;width:100%;height:58px;background:#3A6EA5;border-bottom:5px #4CB1EF solid}
.header .x_logo{float:left;width:126px;text-align:center;padding-top:10px;font-family:微软雅黑;color:#fff}
.me_table{float:left;width:100%;margin-top:10px;border-collapse: collapse;}
.me_table td{border-top:1px #cccccc solid;padding:5px 0px 3px 0px;font-size:14px;font-weight:bold}
.me_table .x_0{background:#FFFF00}
.me_table .x_2{background:#fff}
.me_table .x_1{background:#aaaa00;color:#fff}
.me_table .x_3{background:#ccc}
.me_table .x_tit{font-weight:bold}
.me_table .x_tit td{height:30px}
.me_div1{float:left;width:300px}
.me_div1 li{float:left;width:300px;margin:10px 0px 5px 0px;line-height:25px}
.me_row{float:left;width:600px;}
.me_row li{float:left;width:100%;text-align:left;padding:5px 0px 3px 0px}
.me_row .x_col1{float:left;}
.me_row .x_col2{float:right;width:100px}
.me_row .x_col3{float:right;width:100px}
.me_detail{}
.me_detail td{border:0px;font-size:12px;font-weight:200}
h2{float:left}
.meTxt{background:#3A6EA5;color:#fff;font-size:18px;height:20px;border:0px;font-weight:bold;cursor:pointer}
div{text-align:left}
</style>
</head>
<body>
<div class="header">
	<div class="x_logo"><a href="<?php echo cls_config::get("dirpath","base");?>/" target="frame_main"><img src="/webcss/admin/images/logo.png"></a><br>订&nbsp;&nbsp;&nbsp;&nbsp;餐</div>
	<div style="color:#fff;float:left;margin:20px 0px 0px 20px">
	<input type="text" name="area" value="【<?php echo $area_name;?>】" class="meTxt" id="id_area_box" onclick="thisjs.display_area();">
	</div>
	<div style="color:#fff;float:left;margin:10px 0px 0px 20px"><h2>刷新计时：</h2><h2 id="id_time_view"></h2></div>
	<div style="color:#fff;float:right;text-align:left"><label><input type="checkbox" name="agree_print" id="id_agree_print" value=1 onclick="thisjs.agree_print(this.checked);" <?php if($agree_print){?> checked<?php }?>>接受订单时自动打印小票</label><br><label><input type="checkbox" name="handle" id="id_handle" value=1<?php if($hide_handle){?> checked<?php }?> onclick="thisjs.hide_handle(this.checked);">隐藏已处理订单</label>
	<br><label><input type="checkbox" name="detail" id="id_detail" value=1<?php if($hide_detail){?> checked<?php }?> onclick="thisjs.hide_detail(this.checked);">隐藏订单详情</label>
	</div>
</div>
<table class="me_table" id="id_tab_list">
<tr class="x_tit"><td width="50">订单id</td><td>区域</td><td>楼层</td><td>订餐人</td><td>下单时间</td><td>要求到达时间</td><td>应付</td><td>状态</td><td>操作</td></tr>
<?php foreach($arr_list['list'] as $item){ ?>
<tr<?php if($item['state']==1){?> class="x_1"<?php } else if($item['order_state']>0) { ?> class="x_2"<?php } else if($item['order_state']<0) { ?> class="x_3"<?php } else { ?> class="x_0"<?php }?>><td width="50"><input type="hidden" name="selid[]"  id="id_order_<?php echo $item['order_id'];?>" value="<?php echo $item['order_id'];?>"><?php echo $item['order_id'];?></td><td><?php echo $item['order_area'];?></td><td><?php echo $item['order_louhao1'];?>/<?php echo $item['order_louhao2'];?></td><td><?php echo $item['order_name'];?></td><td><?php echo $item['order_time'];?></td><td><?php echo $item['order_arrive'];?></td><td>￥<?php echo $item['order_total_pay'];?></td><td id="id_state_<?php echo $item['order_id'];?>"><?php if($item['state']==1){?><span style="color:#ff0000">超时未处理</span><?php } else { ?><?php echo $item['state'];?><?php }?></td><td>
<?php if(empty($item['order_state'])){?>&nbsp;&nbsp;<input type="button" name="id_btn_print_<?php echo $item['order_id'];?>" id="id_btn_print_<?php echo $item['order_id'];?>" value="<?php if(empty($item['order_isprint'])){?>打印订单<?php } else { ?>重新打印<?php }?>" class="pBtn2" onclick="thisjs.print(<?php echo $item['order_id'];?>);" style="display:none">&nbsp;&nbsp;<input type="button" name="btn_accept_<?php echo $item['order_id'];?>" id="id_btn_accept_<?php echo $item['order_id'];?>" value="接受预订" class="pBtn" onclick="thisjs.accept(<?php echo $item['order_id'];?>);">&nbsp;&nbsp;<input type="button" name="btn_cancel_<?php echo $item['order_id'];?>" id="id_btn_cancel_<?php echo $item['order_id'];?>" value="取消订单" class="pBtn2" onclick="thisjs.show_cancel_box(<?php echo $item['order_id'];?>,'<?php echo $item['order_mobile'];?>','<?php echo $item['order_tel'];?>');"><?php } else { ?>&nbsp;&nbsp;<input type="button" name="id_btn_print_<?php echo $item['order_id'];?>" id="id_btn_print_<?php echo $item['order_id'];?>" value="<?php if(empty($item['order_isprint'])){?>打印订单<?php } else { ?>重新打印<?php }?>" class="pBtn2" onclick="thisjs.print(<?php echo $item['order_id'];?>);"><?php }?></td></tr>
<tr id="id_detail_<?php echo $item['order_id'];?>" class="me_detail"<?php if($hide_detail){?> style="display:none"<?php }?>><td>&nbsp;</td><td colspan='9'>
<div class="me_row">
<?php foreach($item['menu'] as $key => $menu){ ?>
	<li>
		<span class="x_col1">
		<?php $price=0;?>
		<?php foreach($menu['id'] as $item_menu){ ?>
			<?php $price+=$arr_list['price']['id_'.$item_menu];?>
			<?php echo $arr_list['menu']['id_'.$item_menu]['menu_title'];?>&nbsp;&nbsp;
		<?php }?>
		</span>
		<span class="x_col3"><?php echo $menu['num'];?>份</span>
		<span class="x_col2">￥<?php echo $price*$menu['num'];?></span>
	</li>
<?php }?>
</div>
<?php if(count($item['order_act'])>0){?>
<div class="me_row" style="width:400px">
<?php foreach($item['order_act'] as $act){ ?>
<li style="color:#ff8800"><?php echo $act;?></li>
<?php }?>
</div>
<?php }?>
</td></tr>
<?php }?>
</table>
<div id="id_cancel_order_html" style="display:none">
	<div class="me_div1">
		<li id="id_cancel_smstxt">
			请输入通知用户短信内容<br>
			<textarea name="cont" cols=30 rows=5 id="id_cancel_for"></textarea>
		</li>
		<li id="id_cancel_teltxt">用户未填写手机号，无法短信通知，请电话通知：<span id="id_cancel_tel"></span></li>
		<li>
		<label id="id_checkbox_issms"><input type="checkbox" name="issms" value=1 checked id="id_cancel_issms">短信通知用户</label>&nbsp;&nbsp;<label><input type="checkbox" name="closeshop" value=1 id="id_cancel_closeshop">关闭店铺</label>
		</li>
		<li><input type="button" name="btn_cancel" value="确定取消" class="pBtn" onclick="thisjs.cancel_ok()"></li>
	</div>
</div>
<div id="id_shopinfo_html" style="display:none">
	<div class="me_div1">
		<li>联 系 人：<span id="id_shopinfo_linkname"></span></li>
		<li>联系电话：<span id="id_shopinfo_linktel"></span></li>
		<li>订餐电话：<span id="id_shopinfo_tel"></span></li>
	</div>
</div>

<script>
var thisjs = new function() {
	this.endid = <?php echo $arr_list['endid'];?>;
	this.arr_id = "<?php echo $arr_list['ids'];?>".split(",");
	this.timeval = 15;//秒
	//显示修改密码窗口
	this.show_cancel_box = function(id,mobile,tel) {
		this.cancel_id = id;
		var obj = kj.obj('#id_cancel_order_html');
		if(obj) {
			this.cancel_box_html = obj.innerHTML;
			kj.remove(obj);
		}
		kj.dialog({'html':this.cancel_box_html,'id':'cancel_box','type':'html','title':'取消订单(订单号：'+id+')','w':300,'h':230,'showbtnmax':false});
		if(mobile == '') {
			kj.hide("#id_cancel_smstxt");
			kj.hide("#id_checkbox_issms");
			obj = kj.obj("#id_cancel_tel");
			if(obj) obj.innerHTML = tel;
		} else {
			kj.hide("#id_cancel_teltxt");
		}
	}
	//显示店铺负责人信息
	this.show_shopinfo = function(shopname,linkname,linktel,tel) {
		var obj = kj.obj('#id_shopinfo_html');
		if(obj) {
			this.shopinfo_box_html = obj.innerHTML;
			kj.remove(obj);
		}
		kj.dialog({'html':this.shopinfo_box_html,'id':'shopinfo_box','type':'html','title':shopname,'w':300,'h':230,'showbtnmax':false});
		kj.obj("#id_shopinfo_linkname").innerHTML = linkname;
		kj.obj("#id_shopinfo_linktel").innerHTML = linktel;
		kj.obj("#id_shopinfo_tel").innerHTML = tel;
	}

	this.refresh = function() {
		var ids = this.arr_id.join(",");
		var objdata = {'ids':ids , 'endid':this.endid};
		kj.ajax.post("<?php echo fun_get::url(array('app_act'=>'refresh'));?>",objdata,function(data){
			var objdata = kj.json(data);
			var obj;
			if('list' in objdata) {
				var i,j,x,btn,menu;
				//更新状态
				for( i = 0 ; i < thisjs.arr_id.length; i++ ) {
					x = 'id_'+thisjs.arr_id[i];
					if(x in objdata.orderstate) {
						obj = kj.parent("#id_order_"+thisjs.arr_id[i] , "tr");
						if(kj.toint(objdata.orderstate[x].order_state) > 0) {//已处理
							<?php if($hide_handle){?>
							kj.table.row_del("#id_tab_list" , obj);
							kj.table.row_del("#id_tab_list" , '#id_detail_'+thisjs.arr_id[i]);
							i--;
							<?php } else { ?>
								kj.set("#id_state_" + thisjs.arr_id[i] , "innerHTML" , "已处理");
								if(obj) obj.className = "x_2";
								kj.hide("#id_btn_accept_" + thisjs.arr_id[i]);
								kj.hide("#id_btn_cancel_" + thisjs.arr_id[i]);
								kj.show("#id_btn_print_" + thisjs.arr_id[i]);
							<?php }?>
						} else if(objdata.orderstate[x].state == '1') {//已过期
							if(obj) obj.className = "x_1";
							kj.set("#id_state_" + thisjs.arr_id[i] , "innerHTML" , "<span style=\"color:#ff0000\">超时未处理</span>");
						} else if(objdata.orderstate[x].order_state!='0') {
							if(obj) obj.className = "x_3";
							kj.set("#id_state_" + thisjs.arr_id[i] , "innerHTML" , objdata.orderstate[x].state);
							kj.hide("#id_btn_accept_"+thisjs.arr_id[i]);
							kj.hide("#id_btn_cancel_"+thisjs.arr_id[i]);
						}
						if(kj.toint(objdata.orderstate[x].order_state)>0 || kj.toint(objdata.orderstate[x].order_state)<0) {
							thisjs.arr_id.removeat(i);
						}
					}
				}
				var state,selid,btn;
				var length = 0;
				if('length' in objdata.list) length = objdata.list.length;
				//处理新增
				for( i = 0; i < length ; i++) {
					var perms1 = [];
					//添加详情

					x = '<div class="me_row">';
					for(menu in objdata.list[i].menu){
						x+='<li><span class="x_col1">';
						price = 0;
						for(j = 0;j<objdata.list[i].menu[menu].id.length;j++) {
							id = "id_" + objdata.list[i].menu[menu].id[j];
							x+=objdata.menu[id]['menu_title']+'&nbsp;&nbsp;';
							if( id in objdata.menu ) price += kj.toint(objdata.menu[id]['menu_price']);
						}
						x+='</span><span class="x_col3">'+objdata.list[i].menu[menu].num;
						price = price * objdata.list[i].menu[menu].num;
						x+='份</span><span class="x_col2">￥'+price;
						x+='</span></li>';
					}
					x+='</div>';

					if(objdata.list[i].order_act.length>0){
						x+='<div class="me_row" style="width:400px">';
						for(j=0;j<objdata.list[i].order_act.length;j++) {
							x+='<li style="color:#ff8800">'+objdata.list[i].order_act[j]+'</li>';
						}
						x+='</div>';
					}
					perms1[perms1.length] = {'attribute':{'innerHTML':''}};
					perms1[perms1.length] = {'attribute':{'innerHTML':x,'colSpan':9}};
					obj = kj.table.row_insert("#id_tab_list",perms1,'',1,2);
					obj.className = "me_detail";
					if(kj.obj("#id_detail").checked) kj.hide(obj);
					//添加信息
					perms1 = [];
					if(objdata.list[i].state=='1') {
						state = '<span style="color:#ff0000">超时未处理</span>';
					} else {
						state = '待处理';
					}
					btn = '';
					
					btn += '<input type="button" name="accept_' + objdata.list[i].order_id + '" id="id_btn_accept_' + objdata.list[i].order_id + '" value="接受预订" class="pBtn"  onclick="thisjs.accept(' + objdata.list[i].order_id + ');">&nbsp;&nbsp;<input type="button" name="btn_cancel_' + objdata.list[i].order_id + '" id="id_btn_cancel_' + objdata.list[i].order_id + '" value="取消订单" class="pBtn" onclick="thisjs.show_cancel_box('+objdata.list[i].order_id+',\''+objdata.list[i].order_mobile+'\',\''+objdata.list[i].order_tel+'\');">&nbsp;&nbsp;<input type="button" name="btn_print_' + objdata.list[i].order_id + '" id="id_btn_print_' + objdata.list[i].order_id + '" value="打印订单" class="pBtn2" onclick="thisjs.print('+objdata.list[i].order_id+');" style="display:none">';
					selid = '<input type="hidden" name="selid[]"  id="id_order_'+objdata.list[i].order_id+'" value="'+objdata.list[i].order_id+'">' + objdata.list[i].order_id;
					perms1 = [];
					perms1[perms1.length] = {'attribute':{'innerHTML':selid}};
					perms1[perms1.length] = {'attribute':{'innerHTML':objdata.list[i].order_area}};
					perms1[perms1.length] = {'attribute':{'innerHTML':objdata.list[i].order_louhao1+"/"+objdata.list[i].order_louhao2}};
					perms1[perms1.length] = {'attribute':{'innerHTML':objdata.list[i].order_name}};
					perms1[perms1.length] = {'attribute':{'innerHTML':objdata.list[i].order_time}};
					perms1[perms1.length] = {'attribute':{'innerHTML':objdata.list[i].order_arrive}};
					
					perms1[perms1.length] = {'attribute':{'innerHTML':objdata.list[i].order_total_pay}};
					perms1[perms1.length] = {'attribute':{'innerHTML':state,'id':'id_state_'+objdata.list[i].order_id}};
					perms1[perms1.length] = {'attribute':{'innerHTML':btn}};
					obj = kj.table.row_insert("#id_tab_list",perms1,'',1,10);
					thisjs.arr_id[thisjs.arr_id.length] = objdata.list[i].order_id;
					if(objdata.list[i].state=='1') {
						obj.className = 'x_1';
					} else if(objdata.list[i].state == '0') {
						obj.className = 'x_0';
					} else if( kj.toint(objdata.list[i].state) < 0) {
						obj.className = 'x_3';
					}
					//铃声
					kj.obj("#id_ding_music").innerHTML = '<embed src="/webcss/common/ding.wav" hidden="true" border="0" width="20" height="20" autostart="true" loop="false">';
				}
				if(objdata.endid!='0') thisjs.endid = objdata.endid;
				obj = kj.obj("#id_time_view");
				obj.innerHTML = "获取到" + length + "笔新订单";
			}
			window.setTimeout("thisjs.setinterval()" , 3000);
		});
	}
	this.setinterval = function() {
		obj = kj.obj("#id_time_view");
		obj.innerHTML = thisjs.timeval;
		thisjs.timeinterval = window.setInterval("thisjs.timer()" , 1000);//自动刷新
	}
	this.timer = function() {
		var obj = kj.obj("#id_time_view");
		var val = kj.toint(obj.innerHTML);
		if(val <= 0) {
			//获取新订单
			clearTimeout(thisjs.timeinterval);
			obj.innerHTML = "获取新订单";
			thisjs.refresh();
			val = thisjs.timeval;
		} else {
			val--;
			obj.innerHTML = val;
		}
	}
	this.accept = function(id) {
		kj.ajax.get("<?php echo fun_get::url(array('app_act'=>'accept','id'=>''));?>&id="+id,function(data){
			var obj_data = kj.json(data);
			if(obj_data.isnull) {
				alert("处理失败");
			} else {
				if('code' in obj_data && obj_data.code=='0') {
					//删除行
					var obj = kj.parent("#id_order_"+obj_data.id , "tr");
					if(kj.obj("#id_handle").checked) {
						kj.table.row_del("#id_tab_list" , obj);
					} else {
						var obj_state = kj.obj("#id_state_"+obj_data.id);
						obj.className = "x_2";
						obj_state.innerHTML = "已接受";
						kj.hide("#id_btn_cancel_" + obj_data.id);
						kj.hide("#id_btn_accept_" + obj_data.id);
						kj.show("#id_btn_print_" + obj_data.id);
					}
					thisjs.arr_id.remove(obj_data.id);
					kj.alert.show("接受成功");
					//是否启动打印
					if(kj.obj("#id_agree_print").checked) {
						kj.set("#id_btn_print_" + obj_data.id , 'value' , "重新打印");
						thisjs.print(obj_data.id);
					}
				}else{
					if("msg" in obj_data){
						alert(obj_data.msg);
					}else{
						alert("处理失败");
					}
				}
			}
		});
	}
	this.cancel_ok = function() {
		var id = this.cancel_id;
		if(id<1) {
			alert("没有选择要取消的订单");
			return;
		}
		this.refuse_id = 0;
		var beta = kj.obj("#id_cancel_for").value;
		var issms = (kj.obj("#id_cancel_issms").checked)? 1 : 0;
		var closeshop = (kj.obj("#id_cancel_closeshop").checked)? 1 : 0;
		kj.ajax.get("<?php echo fun_get::url(array('app_act'=>'cancel','id'=>''));?>&id="+id+"&beta="+beta+"&issms=" + issms + "&closeshop=" + closeshop,function(data){
			var obj_data = kj.json(data);
			if(obj_data.isnull) {
				alert("处理失败");
			} else {
				if('code' in obj_data && obj_data.code=='0') {
					kj.dialog.close('#winstate_show');
					var obj = kj.parent("#id_order_"+obj_data.id , "tr");
					if(kj.obj("#id_handle").checked) {
						//删除行
						kj.table.row_del("#id_tab_list" , obj);
					} else {
						var obj_state = kj.obj("#id_state_"+obj_data.id);
						obj.className = "x_3";
						obj_state.innerHTML = "已取消";
						kj.hide("#id_btn_cancel_" + obj_data.id);
						kj.hide("#id_btn_accept_" + obj_data.id);
					}
					thisjs.arr_id.remove(obj_data.id);
					kj.alert.show("订单成功取消");
					kj.dialog.close("#wincancel_box");
				}else{
					if("msg" in obj_data){
						alert(obj_data.msg);
					}else{
						alert("处理失败");
					}
				}
			}
		});
	}
	this.agree_print = function(val) {
		(val)? val = 1 : val = 0;
		kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_var&var=call.agree.print&val=" + val , function(data){
			//alert(data);
		});
	}
	this.hide_handle = function(val) {
		(val)? val = 1 : val = 0;
		kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_var&var=call.hide.handle&val=" + val , function(data){
			//alert(data);
			location.reload(true);
		});
	}
	this.hide_detail = function(val) {
		if(val) {
			val = 1;
			kj.hide(".me_detail");
		} else {
			kj.show(".me_detail");
			val = 0;
		}
		kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_var&var=call.hide.detail&val=" + val , function(data){
		});
	}
	this.print = function(val) {
		var agent = kj.agent();
		if(agent == 'ie') {
			vReturnValue = window.showModelessDialog('<?php echo fun_get::url(array("app_act"=>"print"));?>&order_id='+val, 'win_print', "dialogHeight:500px;dialogWidth:300px");
		} else {
			window.open('<?php echo fun_get::url(array("app_act"=>"print"));?>&order_id='+val,"_blank");
		}
	}
	this.display_area = function(val) {
		var arr = <?php echo $area_list;?>;
		kj.windiv.cache.init({'id':"id_area_box",'datalist':arr,'display':'','selfun':function(){thisjs.area_change();}});
	}
	this.area_change = function() {
		var val = kj.obj("#id_area_box").value;
		kj.ajax.get("common.php?app=config&app_module=user&dir=<?php echo $app_dir;?>&app_act=save_var&var=call.area&val=" + val , function(data){
			//alert(data);
			location.reload(true);
		});
	}
}
kj.onload(function(){
	thisjs.setinterval();//自动刷新
});
</script>
<div id="id_ding_music" style="width:0px;height:0px;float:left;"></div>
</body></html>