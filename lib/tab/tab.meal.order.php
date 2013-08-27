<?php
/*
 *
 *
 * 2013-03-24
 */
class tab_meal_order {
	static $perms;

	//获取表配置参数
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"state" => array("已接受" => 1 , "未处理" => 0 , "已拒绝" => -1 , "待付款" => -2 ,"过期未付款" => -3 ,"过期已取消" => -4),
				"award" => array("已奖励" => 1 , "不奖励" => 0 , "未奖励" => -1),
			);
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}

	/* 保存操作
	 * arr_fields : 为字段数据，默认如果包函 id，则为修改，否则为插入
	 * where : 默认为空，用于有时候条件修改
	 */
	static function on_save($arr_fields , $where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['order_id'])) {
			$arr_fields['id'] = $arr_fields['order_id'];
			unset($arr_fields['order_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " order_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and order_id='" . $arr_return['id'] . "'";
				}
			}
		}
		//序列化明细
		if(isset($arr_fields["order_detail"]) && is_array($arr_fields["order_detail"]) ) {
			$arr_fields["order_detail"] = serialize($arr_fields["order_detail"]);
		}
		//序列化明细
		if(isset($arr_fields["order_act"]) && is_array($arr_fields["order_act"]) ) {
			$arr_fields["order_act"] = serialize($arr_fields["order_act"]);
		}
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {
			//必填项检查
			if(!isset($arr_fields['order_ids']) || empty($arr_fields['order_ids'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("order_ids_is_null" , "meal");//区域id不能为空
				return $arr_return;
			}
			$arr_fields["order_addtime"] = TIME;
			$arr_fields["order_day"] = date("Y-m-d",TIME);
			$arr_fields["order_time"] = date("Y-m-d H:i:s",TIME);
			$arr_fields["order_number"] = $arr_fields["order_shop_id"] . date("ymdHis") . $arr_fields["order_user_id"];//店铺id+时间+用户id
			//插入到用户表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."meal_order",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$where  = "order_ids='" . $arr_fields['order_ids'] . " and order_user_id='".$arr_fields['order_user_id'] . "' and order_addtime='".$arr_fields["order_addtime"]."'";
					$obj_rs = $obj_db->get_one("select order_id from ".cls_config::DB_PRE."meal_order where ".$where);
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['order_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {

			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select order_id from ".cls_config::DB_PRE."meal_order where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['order_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "order_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."meal_order" , $arr_fields , $where);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		}
		return $arr_return;
	}
	//刷新用户数据
	static function on_refresh_user($uid) {
		$arr_return = array("code"=>0,"msg"=>"");
		$obj_db = cls_obj::db_w();
		$obj_rs = $obj_db->get_one("SELECT order_user_id,sum(order_total) as total,count(1) as num FROM " . cls_config::DB_PRE . "meal_order where order_user_id='".$uid."' and order_state>0 group by order_user_id");
		if( !empty($obj_rs) ) {
			$arr_return = $obj_db->on_exe("update ".cls_config::DB_PRE."sys_user set user_order_num='".$obj_rs["num"]."',user_totalpay='".$obj_rs["total"]."' where user_id='" .$obj_rs["order_user_id"]."'");
		}
		return $arr_return;
	}
	/* 删除函数
	 * arr_id : 要删除的 id数组
	 * where : 删除附加条件
	 */
	static function on_delete($arr_id , $where = '') {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if( empty($str_id) && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]="id".cls_language::get("not_null");
			return $arr_return;
		}
		$obj_db = cls_obj::db_w();
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "order_id='".$str_id."'" : $arr_where[] = "order_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		//取用户id，刷新用户数据
		$arr_uid = array();
		$obj_result = $obj_db->select("select order_user_id from ".cls_config::DB_PRE."meal_order where order_state=1 and " . $where);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_uid[] = $obj_rs["order_user_id"];
		}
		$where = implode(" and " , $arr_where);
		$arr_return = $obj_db->on_delete(cls_config::DB_PRE."meal_order" , $where);
		if($arr_return["code"]==0) {
			foreach($arr_uid as $item) {
				self::on_refresh_user($item);
			}
		}
		return $arr_return;
	}


	/* 确认函数，送积分与经验
	 * arr_id : 要确认的 id数组
	 * where : 附加条件
	 */
	static function on_award($arr_id , $where = '') {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if( empty($str_id) && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]="id".cls_language::get("not_null");
			return $arr_return;
		}
		$arr_where = array();
		$obj_db = cls_obj::db_w();
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "order_id='".$str_id."'" : $arr_where[] = "order_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$arr_where[] = " order_state>0 and order_isaward=-1";
		$where = implode(" and " , $arr_where);
		$obj_db->begin("orderok");
		$obj_result = $obj_db->select("select order_id,order_user_id,order_total_pay,order_addtime,order_detail from ".cls_config::DB_PRE."meal_order where " . $where);
		while($obj_rs = $obj_db->fetch_array($obj_result) ) {
			$arr_score = array("score"=>$obj_rs['order_total_pay'] , "experience" => $obj_rs['order_total_pay']);
			if(!empty($obj_rs['order_detail'])) {
				$arr_detail = unserialize($obj_rs['order_detail']);
				if(isset($arr_detail['score_add'])) $arr_score['addscore'] = (int)$arr_detail['score_add'];
				if(isset($arr_detail['score_multiple'])) $arr_score['score'] = $arr_score['score']* (int)$arr_detail['score_multiple'];
			}
			$arr_re = tab_sys_user_action::on_action( $obj_rs["order_user_id"] , "meal_submit_order_ok" ,  $arr_score);
			if($arr_re["code"] != 0 ) {
				$obj_db->rollback("orderok");
				$arr_return["code"] = $arr_re["code"];
				$arr_return["msg"] = $arr_re["msg"];
				return $arr_return;
			}
		}
		//设置已奖励状态为：2
		$arr_return = $obj_db->on_update(cls_config::DB_PRE."meal_order" , array("order_isaward"=>1) ,  $where);
		if($arr_return["code"] != 0 ) {
			$obj_db->rollback("orderok");
			return $arr_return;
		}
		$obj_db->commit("orderok");
		return $arr_return;
	}

	/*
	 * 设置订单状态
	 */
	function on_state($arr_id , $state , $beta = '' , $msg_where = '') {
		$arr_state = self::get_perms("state");
		if(!in_array($state , $arr_state)) return array("code" => 500 , "msg" => "处理状态不存在");
		$str_id = fun_format::arr_id($arr_id);
		if( empty($str_id) && empty($msg_where) ) return array("code" => 500 , "msg" => "未指定要处理的订单");
		
		$arr_where = array();
		if(!empty($msg_where)) $arr_where[] = $msg_where;
		if(!empty($str_id)) {
			(is_numeric($str_id)) ? $arr_where[] = "order_id='".$str_id."'" : $arr_where[] = "order_id in(".$str_id.")";
		}
		
		$where = " where " . implode(" and " , $arr_where);
		$arr = cls_obj::db_w()->on_exe("update " . cls_config::DB_PRE . "meal_order set order_state=" . $state . ",order_state_time='" . date("Y-m-d H:i:s" , TIME) . "',order_beta='" . $beta . "'" . $where);
		if($state == 1 && $arr['code'] == 0) {
			//如果为接受处理，则同步用户订单量及金额统计信息
			$obj_result = cls_obj::db_w()->select("select order_user_id from " . cls_config::DB_PRE . "meal_order" . $where);
			while($obj_rs = cls_obj::db_w()->fetch_array($obj_result)) {
				tab_meal_order::on_refresh_user($obj_rs['order_user_id']);
			}
		}
		return $arr;
	}
}