<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_meal_call extends mod_meal_call {

	//来单显示页
	function act_default() {
		$this->agree_print = tab_sys_user_config::get_var("call.agree.print"  , $this->app_dir);
		$this->hide_handle = tab_sys_user_config::get_var("call.hide.handle"  , $this->app_dir);
		$this->hide_detail = tab_sys_user_config::get_var("call.hide.detail"  , $this->app_dir);
		//非管理员
		$shop_id = 0;
		$area_id = 0;
		$area_name = tab_sys_user_config::get_var("call.area"  , $this->app_dir);
		if(empty($area_name)) {
			$area_name = "所有地区";
		}
		$area_pid = 0;
		$arr_area = array('');
		$obj_result = cls_obj::db()->select("select area_name,area_id from " . cls_config::DB_PRE . "sys_area where area_pid='" . $area_pid . "'");
		while($obj_rs = cls_obj::db()->fetch_array($obj_result)) {
			$arr_area[] = $obj_rs['area_name'];
			if($obj_rs['area_name'] == $area_name) $area_id = $obj_rs['area_id'];
		}
		$this->area_name = $area_name;
		$this->area_list = fun_format::json($arr_area);

		$this->arr_list = $this->get_new_order(0 , $this->hide_handle , $area_id , $shop_id);
		return $this->get_view();
	}

	//自动刷新来单显示页
	function act_refresh() {
		//取新单列表
		$id = (int)fun_get::get("endid");
		//非管理员
		$shop_id = 0;
		$area_id = 0;
		$area_name = tab_sys_user_config::get_var("call.area"  , $this->app_dir);
		$area_pid = 0;
		$obj_rs = cls_obj::db()->get_one("select area_name,area_id from " . cls_config::DB_PRE . "sys_area where area_pid='" . $area_pid . "' and area_name='" . $area_name . "'");
		if(!empty($obj_rs)) {
			$area_id = $obj_rs['area_id'];
		}
		//处理短信回复信息
		$this->on_sms_return();

		$hide_handle = tab_sys_user_config::get_var("call.hide.handle"  , $this->app_dir);
		$arr_return = $this->get_new_order($id , $hide_handle , $area_id);
		$arr_return['orderstate'] = $this->get_order_state();
		return fun_format::json($arr_return);
	}
	//接受预定
	function act_accept() {
		$arr_return = $this->on_accept();
		return fun_format::json($arr_return);
	}
	//取消预定
	function act_cancel() {
		$arr_return = $this->on_cancel();
		return fun_format::json($arr_return);
	}
	//测试打印页
	function act_print_test() {
		$print_temp = fun_get::get("printinfo");
		$shop_id = fun_get::get("shop_id");
		$width = fun_get::get("width");
		$this->width = (empty($width)) ? cls_config::get("width" , "print" , 200) : $width;
		$print_info = cls_config::get("printinfo" , "print");
		$print_temp = empty($print_temp) ? fun_get::filter($print_info,true) : fun_get::filter($print_temp , true);
		$print_source = array(
			"{订单号}"=> "000001" ,
			"{大厦}" => "x大厦" ,
			"{楼层}"=>"x楼x层" ,
			"{公司}"=>"x公司" ,
			"{部门}" => "x部",
			"{客户称呼}"=>"张/先生" ,
			"{送餐地址}"=>"x大厦x楼x层" ,
			"{客户电话}"=>"固话：1111111转222 手机：18600000000",
			"{固话}"=>"1111111转222" ,
			"{手机}"=>"18600000000",
			"{指定时间信息}"=>"指定时间：12:00之前",
			"{下单时间}"=> date("Y-m-d H:i:s"),
			"{打印时间}"=> date("Y-m-d H:i:s"),
			"{收款信息}"=>"应收：38",
			"{菜品列表}"=>"<table><tr><td>名称</td><td>数量</td><td>小计</td></tr><tr><td>套餐一</td><td>1</td><td>10</td></tr><tr><td>套餐二</td><td>1</td><td>13</td></tr><tr><td>套餐三</td><td>1</td><td>15</td></tr></table>",
			chr(10)=>"<br>",
			" "=>"&nbsp;"
		);
		foreach($print_source as $item=>$key) {
			$print_temp = str_replace($item,$key,$print_temp);
		}
		$this->print_cont = $print_temp;
		return $this->get_view("print");
	}
	//测试打印页
	function act_print() {
		$order_id = (int)fun_get::get("order_id");
		$obj_order = cls_obj::db()->get_one("select * from " . cls_config::DB_PRE . "meal_order where order_id='" . $order_id . "'");
		$print_info = cls_config::get("printinfo" , "print");
		$this->width = cls_config::get("width" , "print" , 200);
		$print_temp = fun_get::filter($print_info,true);
		$tel = $tel_mobile = '';
		if(!empty($obj_order['order_tel'])) {
			$tel = "电话：" . $obj_order['order_tel'];
			if(!empty($obj_order['order_telext'])) $tel.="转" . $obj_order['order_telext'];
			$tel_mobile = $tel;
		}
		if(!empty($obj_order['order_mobile'])) {
			if(!empty($tel_mobile)) $tel_mobile .= " ";
			$tel_mobile .= "手机：" . $obj_order['order_mobile'];
		}
		$lou_hao = $obj_order['order_louhao1'] . "层";
		if(!empty($obj_order['order_louhao2'])) $lou_hao .= $obj_order['order_louhao2'] . "室";
		//取列表
		$arr = explode("|" , $obj_order["order_ids"]);
		$arr_x = array();
		foreach($arr as $item) {
			if(!in_array($item , $arr_x)) {
				$arr_menu_id[$item] = array( 'id'=> explode("," , $item) , 'num' => 1);
				$arr_x[] = $item;
			} else {
				$arr_menu_id[$item]['num']++;
			}
		}
		$arr_menu = $arr_price = array();
		//取当时下单的定价
		if(!empty($obj_order["order_detail"])) {
			$arr_detail = unserialize($obj_order["order_detail"]);
			if(isset($arr_detail["menu_price"])) $arr_price = $arr_detail["menu_price"];
		}
		$arr_menu_ids = array_unique(explode("," , str_replace("|" , "," , $obj_order["order_ids"])));
		$str_ids = implode("," , $arr_menu_ids);
		$obj_result = cls_obj::db()->select("select menu_id,menu_title,menu_pic_small,menu_pic,menu_price from " . cls_config::DB_PRE . "meal_menu where menu_id in(" . $str_ids . ")");
		while($obj_rs = cls_obj::db()->fetch_array($obj_result)) {
			$arr_day[] = $obj_rs;
			$arr_menu["id_".$obj_rs["menu_id"]] = $obj_rs;
			if(!isset($arr_price["id_".$obj_rs["menu_id"]])) $arr_price["id_".$obj_rs["menu_id"]] = $obj_rs["menu_price"];
		}

		$arr_tr = array();
		foreach($arr_menu_id as $item => $key) {
			$arr_name = array();
			$price = 0;
			foreach($key['id'] as $id) {
				$arr_name[] = $arr_menu["id_".$id]['menu_title'];
				$price += $arr_price["id_".$id];
			}
			$price = $key['num'] * $price;
			$arr_tr[] = '<tr><td>' . implode("+" , $arr_name) . '</td><td>' . $key['num'] . '</td><td>￥' . $price . '</td></tr>';
		}
		$list = implode("" , $arr_tr);
		//积分抵扣信息
		$order_score_money = $order_ticket = '';
		if(!empty($obj_order['order_score_money'])) {
			$order_score_money = '积分抵扣￥' . $obj_order['order_score_money'];
		}
		if(!empty($obj_order['order_ticket'])) {
			$order_ticket = '(需提供面值￥' . $obj_order['order_ticket']."发票)";
		}
		$order_act = '';
		if(!empty($obj_order['order_act'])) {
			$order_act = implode("<br>" , unserialize($obj_order['order_act']));
		}
		$print_source = array(
			"{订单号}"=> $obj_order['order_number'] ,
			"{大厦}" => $obj_order["order_area"] ,
			"{楼层}"=>  $lou_hao,
			"{公司}"=>$obj_order['order_company'] ,
			"{部门}" =>$obj_order['order_depart'] ,
			"{客户称呼}"=>$obj_order['order_name'] . "/" . $obj_order['order_sex'] ,
			"{送餐地址}"=> $obj_order["order_area"] . " " . $lou_hao ,
			"{客户电话}"=>$tel_mobile,
			"{固话}"=>$tel ,
			"{手机}"=>$obj_order['order_mobile'],
			"{指定时间信息}"=>"指定时间：12:00之前",
			"{下单时间}"=> date("Y-m-d H:i:s" , $obj_order['order_addtime']),
			"{打印时间}"=> date("Y-m-d H:i:s"),
			"{应收金额}"=>"￥" . $obj_order['order_total_pay'],
			"{积分抵扣}"=>$order_score_money,
			"{优惠活动}"=>$order_act,
			"{发票信息}"=>$order_ticket,
			"{菜品列表}"=>"<table><tr><td>名称</td><td>数量</td><td>小计</td></tr>" . $list . "</table>",
			chr(10)=>"<br>"
		);
		foreach($print_source as $item=>$key) {
			$print_temp = str_replace($item,$key,$print_temp);
		}
		$this->print_cont = $print_temp;
		//更新订单打印状态
		if(empty($obj_order['order_isprint'])){
			cls_obj::db_w()->on_exe("update " . cls_config::DB_PRE . "meal_order set order_isprint=1 where order_id='" . $obj_order['order_id'] . "'");
		}
		return $this->get_view("print");
	}
}