<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_sys_user_repayment {
	static $this_type = array("订餐" => 1 , "充值" => 2);
	//订单支付
	static function on_order_pay($user_id , $val , $about_id = 0 , $beta = '订餐支付') {
		$obj_db = cls_obj::db_w();
		$val = abs($val);
		$obj_db->begin('repayment_order_pay');
		$arr = $obj_db->on_exe("update " . cls_config::DB_PRE . "sys_user set user_repayment=user_repayment-" . $val . " where user_repayment>" . $val . " and user_id='" . $user_id . "'");
		if($arr['code'] != 0) {
			$obj_db->rollback('repayment_order_pay');
			return array('code' => 500 , "msg" => "预付款支付失败");
		}
		if($obj_db->affected_rows()<1) return array("code" => 500 , "msg" => "预付款支付失败(余额不足)");
		$arr_fields = array(
			"repayment_user_id" => $user_id,
			"repayment_val" => -1*$val,
			"repayment_beta" => $beta,
			"repayment_type" => 1,
			"repayment_about_id" => $about_id,
		);
		$arr = self::on_save($arr_fields);
		if($arr['code'] != 0) {
			$obj_db->rollback('repayment_order_pay');
			return array('code' => 500 , "msg" => "预付款支付失败");
		}
		$obj_db->commit('repayment_order_pay');
		return array('code' => 0 , 'id' => $arr['id']);
	}
	//预付款充值
	static function on_recharge($arr_fields) {
		if(!isset($arr_fields['repayment_val'])) return array('code'=>500 , 'msg'=>'请输入充值金额');
		$arr_fields['repayment_val'] = abs($arr_fields['repayment_val']);
		$arr_return = self::on_save($arr_fields);
		return $arr_return;
	}
	//保存预付款
	static function on_save($arr_fields = array() ) {
		$obj_db = cls_obj::db_w();
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");

		$arr_fields['repayment_addtime'] = TIME ;
		$arr_fields['repayment_day'] = date("Y-m-d" , TIME );
		$arr_fields['repayment_time'] = date("Y-m-d H:i:s" , TIME );
		//插入到表
		$arr = $obj_db->on_insert(cls_config::DB_PRE."sys_user_repayment",$arr_fields);
		if($arr['code'] == 0) {
			$arr_return['id'] = $obj_db->insert_id();
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = cls_language::get("db_edit");
		}
		return $arr_return;
	}
}