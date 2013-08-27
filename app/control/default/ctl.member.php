<?php
/*
 *
 *
 * 2013-03-24
 */
class ctl_member extends mod_member{
	function act_default(){
		//初始公共信息
		$this->init_info();
		//订单列表
		$this->order_list = $this->get_order_list();
		return $this->get_view(); //显示页面
	}
	//手机站有用到
	function act_pwd(){
		//初始公共信息
		$this->init_info();
		$this->editinfo = $this->get_userinfo();
		return $this->get_view();
	}
	//配送地址页
	function act_info() {
		//初始公共信息
		$this->init_info();
		//地址列表
		$this->this_info = $this->get_infolist();
		//地区
		$this->arr_area = $this->get_area_default();
		return $this->get_view(); //显示页面
	}
	//会员等级
	function act_myvip() {
		//初始公共信息
		$this->init_info();
		//积分明细记录
		$this->action_list = $this->get_action_list();
		//取进度条
		$this->progress = $this->myvip_progress();
		return $this->get_view(); //显示页面
	}
	//订单提交成功页
	function act_payok() {
		//初始公共信息
		$this->init_info();
		//积分明细记录
		$this->id = fun_get::get("id");
		$obj_order = cls_obj::db()->get_one("select order_pay_method , order_total_pay , order_state , order_number , order_addtime ,order_state from " . cls_config::DB_PRE . "meal_order where order_id='" . $this->id . "'");
		$obj_order['paymethod'] = '';
		//计算如果是在线支付，多久没有支付将取消订单
		$lng_i = (int)cls_config::get("pay_timeout" , "meal");
		if(empty($lng_i)) $lng_i = 10;
		$lng_i = $lng_i * 60 + $obj_order['order_addtime'];
		$this->delay_time = date("H:i" , $lng_i);
		//是否过期
		$this->timeout = 0;
		if(TIME > $lng_i) {
			$this->timeout = 1;
			if($obj_order['order_state'] < 0 && $obj_order['order_state'] != -2) cls_obj::db_w()->on_exe("update " . cls_config::DB_PRE . "meal_order set order_state=-3 where order_id='" . $this->id . "'");
		}
		//当前在线支付方式
		$this->arr_pay = cls_config::get("" , "pay" , array() , "");
		if(isset($this->arr_pay[$obj_order['order_pay_method']])) {
			$obj_order['paymethod'] = $this->arr_pay[$obj_order['order_pay_method']]['fields']['title'];
		} else if($obj_order['order_pay_method'] == 'afterpayment'){
			$obj_order['paymethod'] = "货到付款";
		} else if($obj_order['order_pay_method'] == 'repayment') {
			$obj_order['paymethod'] = "预付款(已支付)";
		}
		$this->obj_order = $obj_order;
		return $this->get_view(); //显示页面
	}
	//订单支付跳转页
	function act_order_pay() {
		$id = fun_get::get("id");
		$pay_method = fun_get::get("pay_method");
		$obj_order = cls_obj::db()->get_one("select order_pay_method , order_total_pay , order_state , order_id from " . cls_config::DB_PRE . "meal_order where order_id='" . $id . "'");
		if(empty($obj_order)) {
			cls_error::on_error("exit" , "订单不存在");
		}
		if($obj_order['order_state'] != -2) {
			cls_error::on_error("exit" , "该订单不能在线支付");
		}
		$arr = cls_obj::get('cls_com')->pay("on_pay" , array("pay_method"=> $pay_method , "pay_title" => "在线订餐" , "pay_about_id" => $obj_order['order_id'] , "pay_val" => $obj_order['order_total_pay'] , 'pay_type' => 1 ,'pay_user_id' => cls_obj::get("cls_user")->uid) );
		if($arr['code'] == 0) {
			echo $arr['html'];exit;
		} else {
			cls_error::on_error("exit" , $arr['msg']);
		}
	}
	//订单支付跳转页
	function act_repayment_pay() {
		$val = (int)fun_get::get("val");
		$pay_method = fun_get::get("pay_method");
		$arr = cls_obj::get('cls_com')->pay("on_pay" , array("pay_method"=> $pay_method , "pay_title" => "预付款充值" , "pay_about_id" => 0 , "pay_val" => $val , 'pay_type' => 2 ,'pay_user_id' => cls_obj::get("cls_user")->uid) );
		if($arr['code'] == 0) {
			echo $arr['html'];exit;
		} else {
			cls_error::on_error("exit" , $arr['msg']);
		}
	}
	//我的积分 
	function act_myintegral() {
		//初始公共信息
		$this->init_info();
		$this->score_money = intval(cls_obj::get("cls_user")->get_score() * cls_config::get("score_money_scale","meal"));
		//订单列表
		$this->action_list = $this->get_action_list(1);
		return $this->get_view(); //显示页面
	}
	//我的预付款 
	function act_repayment() {
		//初始公共信息
		$this->init_info();
		//订单列表
		$this->arr_list = $this->get_repayment_list(1);
		$this->arr_pay = cls_config::get("" , "pay" , array() , "");
		return $this->get_view(); //显示页面
	}
	//获取指定id收货信息
	function act_getinfo() {
		$arr_info = $this->get_info();
		return fun_format::json($arr_info);
	}
	//我的留言
	function act_msg() {
		//初始公共信息
		$this->init_info();
		$this->arr_list = $this->get_msglist();
		return $this->get_view();
	}
	//评论订单
	function act_comment() {
		$id = fun_get::get("order_id");
		$this->arr_list = $this->get_comment($id);
		return $this->get_view();
	}
	//保存评论
	function act_comment_save() {
		$arr = $this->save_comment();
		return fun_format::json($arr);
	}
}