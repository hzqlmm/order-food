<?php include cls_resolve::on_resolve('/admin\/header')?>
<script src="/webcss/common/js/kj.draw.js"></script>
<script src="/webcss/common/js/kj.chart.js"></script>
<style>
.me_unset{color:#888888}
.me_unset li{color:#888888}
</style>
<table width="100%" style="margin:0px;padding:0px;">
	<tr><td width="137px">&nbsp;</td><td style="padding-top:20px" valign="top">
		<table width="1000px" style="margin:0px;padding:0px;">
		<tr><td width="400px" valign="top">
		<div class="main_box1">
			<li>您好，<?php echo $user->uname;?><br>所属角色：<?php echo $login_info['group_name'];?></li>
			<li class="x_line">&nbsp;</li>
			<li>上次登录时间：<?php echo $login_info['lastlogintime'];?><br>上次登录IP：<?php echo $login_info['loginip'];?><br>登录总次数：<?php echo $login_info['loginnum'];?></li>
		</div>
		<div style="float:left;width:90%;height:10px">&nbsp;</div>
		<div class="main_box1">
			<li><b>服务器信息</b><br>操作系统：<?php echo $server_info['os'];?><br>PHP版本：<?php echo $server_info['php_version'];?><br>服务器软件：<?php echo $server_info['software'];?><br>Mysql版本：<?php echo $server_info['mysql_version'];?><br>Mysql最大连接数：<?php echo $server_info['mysql_maxlink'];?><br>最大上传：<?php echo $server_info['max_upload'];?><br>最大占用内存：<?php echo $server_info['max_memory'];?><br>最大执行时间：<?php echo $server_info['max_time'];?></li>
			<li class="x_line">&nbsp;</li>
			<li><b>系统版本信息</b><br>系统名称：<a href="javascript:thisjs.open_klkkdj()"><?php echo $version_info['name'];?></a><br>当前版本：<?php echo $version_info['version_name'];?><span id="id_version_new" style="color:#ff8800"></span><br>官方账号：<?php echo $version_info['web_uname'];?>&nbsp;&nbsp;<span style="color:#ff8800;cursor:pointer" id="id_new_msg" onclick="thisjs.ucenter();"></span></li>
		</div>
		</td><td valign="top">
			<div class="main_list1">
				<li>今日订单：<?php echo $count_info['today_order_allnum'];?></li>
				<li>已 处 理：<?php echo $count_info['today_order_num'];?></li>
				<li>未 处 理：<?php echo $count_info['today_order_0'];?></li>
				<li>已 拒 绝：<?php echo $count_info['today_order_1'];?></li>
				<li>有效总额：<?php echo $count_info['today_order_total'];?></li>
				<li>抵扣金额：<?php echo $count_info['today_order_score_money'];?></li>
				<li>时收金额：<?php echo $count_info['today_order_total_pay'];?></li>
				<li>发票积分：<?php echo $count_info['today_order_ticket'];?></li>
			</div>
			<div class="main_list1">
				<li>订单总量：<?php echo $count_info['order_allnum'];?></li>
				<li>有效订单：<?php echo $count_info['order_num'];?></li>
				<li>有效总额：<?php echo $count_info['order_total'];?></li>
				<li>抵扣金额：<?php echo $count_info['order_score_money'];?></li>
				<li>实收金额：<?php echo $count_info['order_total_pay'];?></li>
				<li>发票积分：<?php echo $count_info['order_ticket'];?></li>
			</div>
			<div class="main_list1">
				<li>会员总数：<?php echo $count_info['user_num'];?></li>
				<li>新增用户：<?php echo $count_info['user_new'];?></li>
				<li>回头用户：<?php echo $count_info['user_continue'];?></li>
				<li>用户积分：<?php echo $count_info['score_total'];?></li>
				<li>今日送出：<?php echo $count_info['today_score_send'];?></li>
				<li>今日消耗：<?php echo $count_info['today_score_consume'];?></li>
			</div>
			<div style="float:left;width:90%;height:10px">&nbsp;</div>
			<div class="main_list1<?php if($sms_info['code']!=0){?> me_unset<?php }?>">
				<li>短信余量：<?php echo $sms_info['over'];?><?php if($sms_info['code']==500){?>&nbsp;&nbsp;<a href="javascript:thisjs.open('帮助中心：安装短信','app=service&app_act=help&key=sms');" style="color:#ff8800">如何安装？</a><?php } else if($sms_info['code']!=0) { ?>&nbsp;&nbsp;<a href="javascript:thisjs.open('帮助中心：短信问题','app=service&app_act=help&key=sms.err');" style="color:#ff8800">如何解决?</a><?php }?></li>
				<li>发送总量：<?php echo $sms_info['total'];?></li>
				<li>今日发送：<?php echo $sms_info['today'];?></li>
				<li>今日确认：<?php echo $sms_info['today_order'];?></li>
				<li>今日未确认：<?php echo $sms_info['today_re'];?></li>
			</div>
			<div class="main_list1">
				<li>预存总额：<?php echo $user_repayment['total'];?></li>
				<li>消耗总额：<?php echo $user_repayment['over'];?></li>
				<li>当前总额：<?php echo $user_repayment['now'];?></li>
				<li>今日预存：<?php echo $user_repayment['today_total'];?></li>
				<li>今日消耗：<?php echo $user_repayment['today_over'];?></li>
			</div>
		</td></tr>
		</table>
	</td></tr></table>
<script>
var thisjs = new function() {
	this.update = function(com) {
		kj.dialog({id:'update',title:'在线升级',url:'<?php echo fun_get::url(array("app_act"=>"update"));?>&version='+thisjs.version,w:500,showbtnhide:false,showbtnmax:false,h:300,type:'iframe'});
	}
	this.klkkdj_logo = function() {
		var url = this.get_klkkdj_url("app_act=logo&app=service");
		var obj;
		(parent.kj)? obj = parent.kj : obj = kj;
		obj.dialog({'id':'klkkdj_help','type':'iframe','url':url , 'title':'更新日志','w':600,'showbtnmax':true,'notoolbar':'1'});
	}
	//生成官网链接字符串
	this.get_klkkdj_url = function(url) {
		url = kj.urlencode("<?php echo $klkkdj_url;?>" , url);
		return url;
	}
	//生成官网链接字符串
	this.open_klkkdj = function() {
		var url = this.get_klkkdj_url();
		window.open(url,"_blank");
	}
	this.open = function(title,url) {
		parent.thisjs.klkkdj_open(title,url);
	}
	this.ucenter = function() {
		parent.thisjs.klkkdj_open("KLKKDJ会员中心",'app_act=user');
	}
}
kj.onload(function(){
	//授权级别信息
	kj.ajax.get("<?php echo fun_get::url(array('app_act'=>'official_login'));?>" , function(data){
		var obj_data = kj.json(data);
		if('code' in obj_data && obj_data.code=='0') {
			var obj=kj.obj("#id_grade_info");
			if(obj) obj.innerHTML = obj_data.grade.name;
			thisjs.version = obj_data.version;
			var version = kj.toint(obj_data.version);
			var message = kj.toint(obj_data.message);
			if(version>kj.toint("<?php echo $version_info['version'];?>")) {
				obj = kj.obj("#id_version_new");
				var html = "<br>最新版本：" + obj_data.version_name + "&nbsp;&nbsp;&nbsp;<a href=\"javascript:thisjs.update();\" style='color:#004499'>在线升级</a>&nbsp;&nbsp;&nbsp;<a href='javascript:thisjs.klkkdj_logo();' style='color:#004499'>了解详情</a>";
				if(obj) obj.innerHTML = html;
			}
			if(message>0) {
				kj.obj("#id_new_msg").innerHTML = message + "条未读消息";
			}
		}
	});
});
</script>
<?php include cls_resolve::on_resolve('/admin\/footer')?>