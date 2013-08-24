<?php
/* versionbeta:name
 * versionbeta:number
 * versionbeta:site
 * versionbeta:pubtime
 */
class mod_meal_comment extends inc_mod_admin {

	function get_pagelist() {
		//取查询参数
		$arr_where_s = array();
		$arr_search_key = array(
			'addtime1' => fun_get::get("s_time1"),
			'addtime2' => fun_get::get("s_time2"),
			'key' => fun_get::get("s_key"),
		);
		$obj_db = cls_obj::db();
		$arr_return = array("list" => array() , "menu" => array() , "shop" => array() , "issearch" => 0);
		if( fun_is::isdate( $arr_search_key['addtime1'] ) ) $arr_where_s[] = "comment_addtime >= '" . strtotime( $arr_search_key['addtime1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['addtime2'] ) ) $arr_where_s[] = "comment_addtime <= '" . fun_get::endtime($arr_search_key['addtime2']) . "'"; 
		if(!empty($arr_search_key['key'])) {
			if(cls_config::USER_CENTER=='user.klkkdj') {
				$arr_uid = array();
				$obj_result = $obj_db->select("select user_id from " . cls_config::DB_PRE . "user where user_name like '%" . $arr_search_key['key'] . "%'");
				while($obj_rs = $obj_db->fetch_array($obj_result)) {
					$arr_uid[] = $obj_rs['user_id'];
				}
				if(!empty($arr_uid)) {
					$ids = implode("," , $arr_uid);
					$arr_where_s[] = "(comment_beta like '%" . $arr_search_key['key'] . "%' or comment_user_id in(" . $ids . "))";
				} else {
					$arr_where_s[] = "comment_beta like '%" . $arr_search_key['key'] . "%'";
				}
			} else {
				$arr_x = cls_obj::get("cls_user")->get_user($arr_search_key['key']);
				if(isset($arr_x[$arr_search_key['key']])) {
					$arr_where_s[] = "(comment_beta like '%" . $arr_search_key['key'] . "%' or comment_user_id='" . $arr_x[$arr_search_key['key']] . "')";
				} else {
					$arr_where_s[] = "comment_beta like '%" . $arr_search_key['key'] . "%'";
				}
			}
		}
		$where = '';
		if( count($arr_where_s) > 0 ) {
			$arr_return['issearch'] = 1;
			$where = " where " . implode(" and " , $arr_where_s);
		}
		$arr_order_id = $arr_shopid = $arr_menu_id = $arr_user_id = array();
		$arr_config_info = tab_sys_user_config::get_info("meal.order.comment"  , $this->app_dir);
		$lng_pagesize = $arr_config_info["pagesize"];
		$lng_page = (int)fun_get::get("page");
		$sort = " order by comment_id desc";
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."meal_order_comment" , $where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."meal_order_comment" . $where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$arr_shopid[] = $obj_rs['comment_shop_id'];
			$arr_order_id[] = $obj_rs['comment_order_id'];
			$arr_user_id[] = $obj_rs['comment_user_id'];
			$obj_rs["addtime"] = date("Y-m-d H:i:s" , $obj_rs["comment_addtime"]);
			$obj_rs['menu_comments'] = array();
			$obj_rs['user_name'] = '';
			$arr_return['list']['id_' . $obj_rs['comment_order_id']] = $obj_rs;
		}
		if(!empty($arr_order_id)) {
			$ids = implode("," , $arr_order_id);
			$obj_result = $obj_db->select("select * from " . cls_config::DB_PRE . "meal_menu_comment where comment_order_id in(" . $ids . ")");
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_menu_id[] = $obj_rs['comment_menu_id'];
				$arr_return['list']['id_' . $obj_rs['comment_order_id']]['menu_comments'][] = $obj_rs;
			}
			$ids = implode("," , $arr_shopid);
			$obj_result = $obj_db->select("select shop_id,shop_name from " . cls_config::DB_PRE . "meal_shop where shop_id in(" . $ids . ")");
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_return['shop']['id_' . $obj_rs['shop_id']] = $obj_rs['shop_name'];
			}
			$ids = implode("," , $arr_menu_id);
			$obj_result = $obj_db->select("select menu_id,menu_title from " . cls_config::DB_PRE . "meal_menu where menu_id in(" . $ids . ")");
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_return['menu']['id_' . $obj_rs['menu_id']] = $obj_rs['menu_title'];
			}
			$user_info = cls_obj::get("cls_user")->get_user($arr_user_id);
			foreach($arr_return["list"] as $key=>$item) {
				$arr_return["list"]['id_' . $item['comment_order_id']]['user_name'] = array_search($arr_return["list"]['id_' . $item['comment_order_id']]['comment_user_id'] , $user_info);
			}
		}
		return $arr_return;
	}

	/* 删除指定  ads_id 数据
	 */
	function on_delete() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("delete_ok"));
		$str_id = fun_get::get("id");
		$arr_id = fun_get::get("selid");
		if( empty($arr_id) && empty($str_id) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = cls_language::get("delete_no_id");
			return $arr_return;
		}
		if(!empty($arr_id)) $str_id = $arr_id; //优先考虑 arr_id
		$str_id = fun_format::arr_id($str_id);
		if(empty($str_id)) return $arr_return;
		$obj_db = cls_obj::db_w();
		$arr_menu_id = array();
		//还原店铺评分
		$obj_result = $obj_db->select("select comment_menu_id from " . cls_config::DB_PRE . "meal_menu_comment where comment_order_id in(" . $str_id . ")");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_menu_id[] = $obj_rs['comment_menu_id'];
		}
		$obj_db->on_exe("delete from " . cls_config::DB_PRE . "meal_order_comment where comment_order_id in(" . $str_id . ")");
		$arr_msg = $obj_db->on_exe("delete from " . cls_config::DB_PRE . "meal_menu_comment where comment_order_id in(" . $str_id . ")");
		if(!empty($arr_menu_id) && $arr_msg['code']==0 ) {
			$ids = implode("," , $arr_menu_id);
			$arr_msg = $obj_db->on_exe("update " . cls_config::DB_PRE . "meal_menu set menu_comment_num=menu_comment_num-1 where menu_id in(" . $ids . ")");
		}
		return $arr_return;
	}
}