<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_meal_call extends inc_mod_common {
	function __construct($arr_v = array() ) {
		//是否登录
		if(!cls_obj::get("cls_user")->is_login()) {
			cls_error::on_error("no_login");
		}
		//是否为管理员
		if(!cls_obj::get("cls_user")->is_admin() && cls_obj::get("cls_user")->type!='shop') {
			cls_error::on_error("no_limit" , "没有查看权限");
		}
		parent::__construct($arr_v);
	}
	//取当天未处理订单
	function get_new_order($id = 0 , $hide = 0 , $area_id = 0) {
		$obj_db = cls_obj::db();
		$arr_areaid = $arr_id = $arr_menu_ids = array();
		$arr_order = array("list" => array());
		$arr_order['endid'] = 0;
		$time = (int)cls_config::get("order_overtime" , "meal") * 60;
		$where = " where order_day='" . date("Y-m-d") . "'";
		if($hide==1) $where .= " and order_state=0";
		if(!empty($id)) $where .= " and order_id>" . $id;
		if(!empty($area_id)) $where .= " and " . $obj_db->concat("," , "order_area_allid" , ",") . " like '%," . $area_id . ",%'";

		$arr_state = tab_meal_order::get_perms("state");
		$obj_result = $obj_db->select("select order_id,order_area,order_louhao1,order_louhao2,order_addtime,order_isprint,order_name,order_time,order_arrive,order_total_pay,order_state,order_tel,order_mobile,order_telext,order_ids,order_detail,order_act from " . cls_config::DB_PRE . "meal_order" . $where . " order by order_id desc");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr = explode("|" , $obj_rs["order_ids"]);
			$arr_x = array();
			foreach($arr as $item) {
				if(!in_array($item , $arr_x)) {
					$obj_rs["menu"][$item] = array( 'id'=> explode("," , $item) , 'num' => 1);
					$arr_x[] = $item;
				} else {
					$obj_rs["menu"][$item]['num']++;
				}
			}
			//取当时下单的定价
			if(!empty($obj_rs["order_act"])) {
				$obj_rs["order_act"] = unserialize($obj_rs["order_act"]);
			} else {
				$obj_rs["order_act"] = array();
			}
			//看是否有活动优惠
			if(!empty($obj_rs["order_detail"])) {
				$arr_detail = unserialize($obj_rs["order_detail"]);
				if(isset($arr_detail["menu_price"])) $arr_order["price"] = $arr_detail["menu_price"];
			}
			$arr_menu_ids = array_merge($arr_menu_ids , explode("," , str_replace("|" , "," , $obj_rs["order_ids"])));

			if($obj_rs['order_id']>$arr_order['endid']) $arr_order['endid'] = $obj_rs['order_id'];
			if(empty($obj_rs["order_state"]) && $obj_rs['order_addtime']+$time<TIME) {
				$obj_rs['state'] = 1;
			} else {
				$obj_rs['state'] = array_search($obj_rs['order_state'] , $arr_state);
			}
			if(!empty($obj_rs["order_telext"])) $obj_rs["order_tel"] .= "转" . $obj_rs["order_telext"];
			if( empty($obj_rs["order_state"]) ) $arr_id[] = $obj_rs["order_id"];
			$arr_order['list'][] = $obj_rs;
		}
		if(count($arr_menu_ids)>0) {
			$arr_menu_ids = array_unique($arr_menu_ids);
			$str_ids = implode("," , $arr_menu_ids);
			$obj_result = $obj_db->select("select menu_id,menu_title,menu_pic_small,menu_pic,menu_price from " . cls_config::DB_PRE . "meal_menu where menu_id in(" . $str_ids . ")");
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_day[] = $obj_rs;
				$arr_order["menu"]["id_".$obj_rs["menu_id"]] = $obj_rs;
				if(!isset($arr_order["price"]["id_".$obj_rs["menu_id"]])) $arr_order["price"]["id_".$obj_rs["menu_id"]] = $obj_rs["menu_price"];
			}
		}
		$arr_order['ids'] = implode("," , $arr_id);
		return $arr_order;
	}
	//取指定ids 订单状态
	function get_order_state() {
		$obj_db = cls_obj::db();
		$str_ids = fun_get::post("ids");
		if(empty($str_ids)) return array();
		$time = (int)cls_config::get("order_overtime" , "meal") * 60;
		$arr = array();
		$arr_ids = explode("," , $str_ids);
		foreach($arr_ids as $item) {
			if(empty($item)) continue;
			$arr[] = $item;
		}
		$str_ids = implode("," , $arr);
		if(empty($str_ids)) $str_ids = '0';
		$arr = array();
		$where = " where order_id in(" . $str_ids . ")";
		$arr_state = tab_meal_order::get_perms("state");
		$obj_result = $obj_db->select("select order_id,order_addtime,order_state from " . cls_config::DB_PRE . "meal_order" . $where . " order by order_id");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			if(empty($obj_rs["order_state"]) && $obj_rs['order_addtime']+$time<TIME) {
				$arr['id_'.$obj_rs['order_id']] = array("order_state" => $obj_rs['order_state'] , "state" => 1);
			} else {
				$arr['id_'.$obj_rs['order_id']] = array('order_state'=>$obj_rs['order_state'] , 'state' => array_search($obj_rs['order_state'] , $arr_state));
			}
		}
		return $arr;
	}
	//接受预订
	function on_accept() {
		$id = (int)fun_get::get("id");
		$arr_return = array("code"=>0 , "msg"=> "接受成功" , "id" => $id);
		$arr = tab_meal_order::on_state($id , 1 , "" , "order_state=0");
		if($arr['code'] != 0) return $arr;
		//同步用户订单量及金额统计信息
		$obj_db = cls_obj::db();
		$obj_rs = $obj_db->get_one("select order_user_id from " . cls_config::DB_PRE . "meal_order where order_id='" . $id . "'");
		if(!empty($obj_rs)) {
			tab_meal_order::on_refresh_user($obj_rs['order_user_id']);
		}
		return $arr_return;
	}
	//取消预订
	function on_cancel() {
		$id = (int)fun_get::get("id");
		$closeshop = (int)fun_get::get("closeshop");
		$issms = (int)fun_get::get("issms");
		$beta = fun_get::get("beta");
		$arr_return = array("code"=>0 , "msg"=> "成功取消订单" , "id" => $id);
		$arr = tab_meal_order::on_state($id , -4 , $beta , "order_state=0");
		if($arr['code'] != 0) return $arr;
		if(!empty($issms) && fun_is::com('sms')) {
			$obj_shop = cls_obj::db()->get_one("select order_mobile from " . cls_config::DB_PRE . "meal_order" . $where);
			//发送手机短信
			if( !empty($obj_shop['order_mobile']) ) {
				$arr = cls_obj::get('cls_com')->sms("on_send" , array("tel"=>$obj_shop['order_mobile'] , "cont" => $beta ,"id" => $id , "type"=>2) );					
			}
		}
		return $arr_return;
	}
	function on_sms_return() {
			$arr = cls_obj::get('cls_com')->sms("get_recont");
			if($arr['code']!=0) return $arr;
			if(empty($arr['list'])) return array("code"=>0);
			foreach($arr['list'] as $row) {
				//将短信状态设置为已回复
				cls_obj::db_w()->on_exe("update " . cls_config::DB_PRE . "other_sms set sms_recont='" . $row['cont'] . "',sms_retime='" . $row['time'] . "' where sms_confirm_id='" . $row['confirm_id'] . "' and sms_day='" . date("Y-m-d") . "' and sms_tel='" . $row['tel'] . "'");
				//插入回复记录
				tab_other_sms_re::on_save(array('re_tel'=>$row['tel'] , "re_cont" => $row['cont'] , "re_time" => $row['time'] , "re_day" => date("Y-m-d" , strtotime($row['time']))));
				//更改订单状态
				(empty($row['cont']))? $state = 1 : $state = -1;
				$where = " where order_day='" . date("Y-m-d") . "' and right(order_id," . strlen($row['confirm_id']) . ")='" . $row['confirm_id'] . "'";
				cls_obj::db_w()->on_exe("update " . cls_config::DB_PRE . "meal_order set order_state='" . $state . "'" . $where);
				//已接受则同步用户订单量及金额统计信息
				if($state == 1) {
					$obj_rs = $obj_db->get_one("select order_user_id from " . cls_config::DB_PRE . "meal_order" . $where);
					if(!empty($obj_rs)) tab_meal_order::on_refresh_user($obj_rs['order_user_id']);
				}

				//如果已拒绝，是否通知用户
				$call_user = cls_config::get("cancel_call_user" , "sms");
				$shopname = cls_config::get("shop_name" , "view");
				if($call_user && $state==-1) {
					//取用户手机号
					$obj_rs = cls_obj::db()->get_one("select order_mobile,order_tel from " . cls_config::DB_PRE . "meal_order where order_day='" . date("Y-m-d") . "' and right(order_id," . strlen($row['confirm_id']) . ")='" . $row['confirm_id'] . "'");
					if(!empty($obj_rs)) {
						$tel = '';
						if(fun_is::tel($obj_rs['order_mobile'])) $tel = trim($obj_rs['order_mobile']);
						if(empty($tel) && fun_is::tel($obj_rs['order_tel']))  $tel = trim($obj_rs['order_tel']);
						if(!empty($tel)) {
							$cancel_user_beta = cls_config::get("cancel_user_beta" , "sms");
							(stristr($cancel_user_beta , "#cont#"))? $cancel_user_beta = str_replace("#cont#" , $row['cont'] , $cancel_user_beta) : $cancel_user_beta.=$row['cont'];
							$cancel_user_beta = str_replace("#shopname#" , $shopname , $cancel_user_beta);
							$arr = cls_obj::get('cls_com')->sms("on_send" , array("tel"=>$tel , "cont" => $cancel_user_beta ,"id" => $arr_return["id"] ,"confirm_id" => $id , "type"=>1) );
						}
					}
				}
			}
	}
}