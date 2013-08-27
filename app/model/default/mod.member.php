<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_member extends inc_mod_default {
	function __construct($arr_v) {
		//是否登录
		if(!cls_obj::get("cls_user")->is_login()) {
			cls_error::on_error("no_login");
		}
		parent::__construct($arr_v);
	}
	//初始化公共信息
	function init_info() {
		//$this->carts_num = $this->get_carts_num();
		$this->loginuser_experience = cls_obj::get("cls_user")->get_experience();
		$this->loginuser_experience_next = tab_sys_user::get_level_next( $this->loginuser_experience );
		$this->loginuser_experience_poor = $this->loginuser_experience_next - $this->loginuser_experience;
		$this->loginuser_level = cls_obj::get("cls_user")->get_level();
		$this->loginuser_level_next = $this->loginuser_level + 1;
		$this->experience_process = (int)$this->loginuser_experience / $this->loginuser_experience_next * 168;
		$this->paymethod = cls_config::get("paymethod" , "meal");

	}

	//获取当前登录用户信息
	function get_userinfo() {
		$obj_rs = cls_obj::db()->get_one("select user_email from " . cls_config::DB_PRE . "sys_user where user_id='" . cls_obj::get("cls_user")->uid . "'");
		return $obj_rs;
	}
	/*获取当前用户行为列表
	 * type: 0表示经验，1表示积分
	 */
	function get_action_list($type = 0) {
		$obj_db = cls_obj::db();
		$page = (int)fun_get::get("page");
		//取排序字段
		$str_where = " where action_user_id='" . cls_obj::get("cls_user")->uid . "'";
		if($type==0) {
			$str_key = ".member.myvip";
			$str_where .= " and action_experience!=0";
		} else {
			$str_key = ".member.myintegral";
			$str_where .= " and action_score!=0";
		}
		$arr_config_info = tab_sys_user_config::get_info($str_key  , $this->app_dir);
		$pagesize = $arr_config_info["pagesize"];

		$action_id = 0;
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."sys_user_action" , $str_where , $page , $pagesize);
		$obj_result = $obj_db->select("select * from " . cls_config::DB_PRE . "sys_user_action" . $str_where . " order by action_id desc" . $arr_return['pageinfo']['limit']);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			if(empty($action_id)) $action_id = $obj_rs["action_id"];
			if(empty($obj_rs["action_beta"])) {
				$arr = cls_config::get($obj_rs["action_key"] , 'user.action' , '' , '');
				$obj_rs["beta"] = (isset($arr["title"]))? $arr["title"] : "";
			} else {
				$obj_rs["beta"] = $obj_rs["action_beta"];
			}
			$obj_rs["action_addtime"] = date("Y-m-d H:i:s" , $obj_rs["action_addtime"]);
			$arr_return["list"][]= $obj_rs;
		}
		$obj_rs = $obj_db->get_one("select sum(action_score) as score,sum(action_experience) as experience from " . cls_config::DB_PRE . "sys_user_action where action_user_id='" . cls_obj::get("cls_user")->uid . "' and action_id<='" . $action_id . "'");
		if(!empty($obj_rs)) {
			$arr_return["score"] = $obj_rs["score"];
			$arr_return["experience"] = $obj_rs["experience"];
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);

		return $arr_return;
	}
	/*获取当前用户预付款记录
	 * 
	 */
	function get_repayment_list() {
		$obj_db = cls_obj::db();
		$page = (int)fun_get::get("page");
		//取排序字段
		$str_where = " where repayment_user_id='" . cls_obj::get("cls_user")->uid . "'";
		$arr_config_info = tab_sys_user_config::get_info(".member.repayment"  , $this->app_dir);
		$pagesize = $arr_config_info["pagesize"];

		$repayment_id = 0;
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."sys_user_repayment" , $str_where , $page , $pagesize);
		$obj_result = $obj_db->select("select * from " . cls_config::DB_PRE . "sys_user_repayment" . $str_where . " order by repayment_id desc" . $arr_return['pageinfo']['limit']);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			if(empty($repayment_id)) $repayment_id = $obj_rs["repayment_id"];
			if(empty($obj_rs["repayment_beta"])) {
				$arr = tab_sys_user_repayment::$this_type;
				$obj_rs["beta"] = array_search($obj_rs["repayment_type"] , $arr);
			} else {
				$obj_rs["beta"] = $obj_rs["repayment_beta"];
			}
			$obj_rs["repayment_addtime"] = date("Y-m-d H:i:s" , $obj_rs["repayment_addtime"]);
			$arr_return["list"][]= $obj_rs;
		}
		$arr_return["repayment"] = cls_obj::get("cls_user")->get_repayment();
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);

		return $arr_return;
	}
	//获取当前用户订单列表
	function get_order_list() {
		$obj_db = cls_obj::db();
		$page = (int)fun_get::get("page");
		$arr_day = $arr_menu_ids = array();
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info(".member.orderlist"  , $this->app_dir);
		$pagesize = $arr_config_info["pagesize"];

		$str_where = " where order_user_id='" . cls_obj::get("cls_user")->uid . "' and order_state>=0";
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."meal_order" , $str_where . " group by order_day" , $page , $pagesize);
		$obj_result = $obj_db->select("select order_day from " . cls_config::DB_PRE . "meal_order" . $str_where . " group by order_day" . $arr_return['pageinfo']['limit']);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_day[] = $obj_rs;
		}
		if(count($arr_day) > 0 ) {
			$start_day = $arr_day[0]["order_day"];
			$end_day = $arr_day[count($arr_day)-1]["order_day"];
			$arr_menu_ids = array();
			$arr_x = array();
			$arr_state = tab_meal_order::get_perms('state');
			$obj_result = $obj_db->select("select order_addtime,order_id,order_ids,order_day,order_total,order_score_money,order_favorable,order_comment,order_total_pay,order_name,order_sex,order_detail,order_state,order_shop_id from " . cls_config::DB_PRE . "meal_order" . $str_where . " order by order_id desc" . $arr_return['pageinfo']['limit']);
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$obj_rs["addtime"] = date("H:i",$obj_rs["order_addtime"]);
				$arr = explode("|" , $obj_rs["order_ids"]);
				$obj_rs['menunum'] = array_count_values($arr);
				$arr = array_unique($arr);
				foreach($arr as $item) {
					$obj_rs["menu"][] = explode("," , $item);
				}
				//取当时下单的定价
				if(!empty($obj_rs["order_detail"])) {
					$arr_detail = unserialize($obj_rs["order_detail"]);
					if(isset($arr_detail["menu_price"])) $arr_return["price"] = $arr_detail["menu_price"];
				}
				$obj_rs['state'] = array_search($obj_rs['order_state'] , $arr_state);
				$arr_menu_ids = array_merge($arr_menu_ids , explode("," , str_replace("|" , "," , $obj_rs["order_ids"])));
				$obj_rs['order_total'] = (float)$obj_rs['order_total'];
				$obj_rs['order_total_pay'] = (float)$obj_rs['order_total_pay'];
				$obj_rs['order_favorable'] = (float)$obj_rs['order_favorable'];
				$arr_return["list"][$obj_rs["order_day"]][] = $obj_rs;
				$arr_return['shop']['id_' . $obj_rs['order_shop_id']] = '';
			}
			$arr_menu_ids = array_unique($arr_menu_ids);
			$str_ids = implode("," , $arr_menu_ids);
			$obj_result = $obj_db->select("select menu_id,menu_title,menu_pic_small,menu_pic,menu_price from " . cls_config::DB_PRE . "meal_menu where menu_id in(" . $str_ids . ")");
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_day[] = $obj_rs;
				$arr_return["menu"]["id_".$obj_rs["menu_id"]] = $obj_rs;
				if(!isset($arr_return["price"]["id_".$obj_rs["menu_id"]])) $arr_return["price"]["id_".$obj_rs["menu_id"]] = $obj_rs["menu_price"];
			}
		}
		if(!isset($arr_return["list"])) $arr_return["list"] = array();
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}

	//获取指定id收货信息
	function get_info() {
		$id = (int)fun_get::get("id");
		$obj_rs = cls_obj::db()->get_one("select * from " . cls_config::DB_PRE  . "meal_info where info_user_id='" . cls_obj::get("cls_user")->uid . "' and info_id='" . $id . "'");
		return $obj_rs;
	}
	//
	function myvip_progress() {
		$experience = cls_obj::get("cls_user")->get_experience();
		$progress = 0;
		if($experience > 5000) {
			$lng_val = $experience - 5000;
			$lng_x = 5000;
			$lng_y = 500;
		} else if($experience > 2000) {
			$lng_val = $experience - 2000;
			$lng_x = 3000;
			$lng_y = 400;
		} else if($experience > 800) {
			$lng_val = $experience - 800;
			$lng_x = 1200;
			$lng_y = 300;
		} else if($experience > 300) {
			$lng_val = $experience - 300;
			$lng_x = 500;
			$lng_y = 200;
		} else if($experience > 100) {
			$lng_val = $experience - 100;
			$lng_x = 200;
			$lng_y = 100;
		} else {
			$lng_val = $experience;
			$lng_x = 100;
			$lng_y = 0;
		}

		$val = $lng_y + intval($lng_val/$lng_x * 100);
		return $val;
	}

	function get_msglist() {
		$arr_return = array("list" => array() , "pagebtns" => "");
		if(fun_is::com('msg') == false) return $arr_return;
		$obj_db = cls_obj::db();
		$lng_page = (int)fun_get::get("page");
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("com.msg"  , $this->app_dir);
		$lng_pagesize = $arr_config_info["pagesize"];
		$str_where = " where msg_user_id='" . cls_obj::get("cls_user")->uid . "'";
		//取分页信息
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."other_msg" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."other_msg" . $str_where . " order by msg_id desc" . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$arr_return["list"][] = $obj_rs;
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}

	function get_comment($order_id) {
		$arr_return = array("list" => array() , "comment" => array() );
		$obj_db = cls_obj::db();
		$obj_rs = $obj_db->get_one("select order_ids from " . cls_config::DB_PRE . "meal_order where order_id='" . $order_id . "'");
		if(empty($obj_rs)) return $arr_return;
		$ids = str_replace("|" , "," , $obj_rs['order_ids']);
		if(empty($ids)) return $arr_return;
		$arr_val = array();
		$obj_result = $obj_db->select('select comment_val,comment_menu_id,comment_id from ' . cls_config::DB_PRE . "meal_menu_comment where comment_menu_id in(" . $ids . ")");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_val["id_" . $obj_rs['comment_menu_id']] = $obj_rs;
		}
		$obj_result = $obj_db->select('select menu_title,menu_id from ' . cls_config::DB_PRE . "meal_menu where menu_id in(" . $ids . ")");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$obj_rs['comment'] = (isset($arr_val['id_' . $obj_rs['menu_id']])) ? $arr_val['id_' . $obj_rs['menu_id']] : array('comment_val'=>null,'comment_id'=>0);
			$arr_return["list"][] = $obj_rs;
		}
		$arr_return["comment"] = cls_obj::db()->edit(cls_config::DB_PRE."meal_order_comment" , "comment_order_id='".$order_id."' and comment_user_id='" . cls_obj::get("cls_user")->uid . "'");

		return $arr_return;
	}

	function save_comment() {
		$order_id = fun_get::get("order_id");
		$obj_db = cls_obj::db_w();
		$uid = cls_obj::get("cls_user")->uid;
		$obj_rs = $obj_db->get_one("select order_id,order_shop_id,order_ids from " . cls_config::DB_PRE . "meal_order where order_id='" . $order_id . "' and order_user_id='" . $uid . "'");
		if(empty($obj_rs)) return array("code" => 500 , "msg" => "订单存在");
		$ids = str_replace("|" , "," , $obj_rs['order_ids']);
		$arr_id = array_unique(explode("," , $ids));
		$arr_insert = array();
		$valall = 0;
		$arr_menuid = array();
		foreach($arr_id as $item) {
			if(!fun_is::set("comment". $item)) continue;
			$x = fun_get::get("id" . $item);
			$val = (int)fun_get::get("comment". $item);
			if($val>1 || $val < -1) $val = 0;
			if(empty($x)) {
				$arr_insert[] = array("comment_val" => $val , "comment_menu_id" => $item , "comment_user_id" => $uid , "comment_shop_id" => $obj_rs['order_shop_id'] , "comment_order_id" => $obj_rs["order_id"],"comment_addtime"=>TIME);
				$arr_menuid[] = $item;
			} else {
				$obj_db->on_exe("update " . cls_config::DB_PRE . "meal_menu_comment set comment_addtime='" . TIME . "',comment_val='" . $val . "' where comment_id='" . $x . "' and comment_order_id='" . $order_id . "'");
			}
			$valall += $val;
		}
		if(!empty($arr_insert)) {
			$obj_db->on_insert_all(cls_config::DB_PRE . "meal_menu_comment" , $arr_insert);
			$ids = implode("," , $arr_menuid);
			foreach($arr_insert as $item) {
				$obj_db->on_exe("update " . cls_config::DB_PRE . "meal_menu set menu_comment_num=menu_comment_num+1,menu_comment=menu_comment+" . $item['comment_val'] . " where menu_id='" . $item['comment_menu_id'] . "'");
			}
		}
		$id = fun_get::get("id");
		if($valall > 0 ) $valall = 1;
		if($valall < 0 ) $valall = -1;
		if(empty($id)) {
			$arr_val = array(
				"comment_user_id" => $uid ,
				"comment_shop_id" => $obj_rs['order_shop_id'] ,
				"comment_order_id" => $obj_rs['order_id'],
				"comment_addtime" => TIME,
				"comment_val" => $valall,
				"comment_beta" => fun_get::get("comment_beta")
			);
			$arr = $obj_db->on_insert(cls_config::DB_PRE . "meal_order_comment" , $arr_val);
			$obj_db->on_exe("update " . cls_config::DB_PRE . "meal_order set order_comment=1 where order_id='" . $order_id . "'");
		} else {
			$arr = $obj_db->on_exe("update " . cls_config::DB_PRE . "meal_order_comment set comment_val='" . $valall . "',comment_beta='" . fun_get::get("comment_beta") . "' where comment_id='" . $id . "' and comment_order_id='" . $order_id . "'");
		}
		return $arr;
	}
}