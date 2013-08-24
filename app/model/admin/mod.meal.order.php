<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */

class mod_meal_order extends inc_mod_meal {
	/* 按模块查询菜单信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_pagelist() {
		$arr_where = array();
		$arr_where_s = array();
		$str_where = '';
		$lng_issearch = 0;
		//取查询参数
		$arr_search_key = array(
			'addtime1' => fun_get::get("s_addtime1"),
			'addtime2' => fun_get::get("s_addtime2"),
			'user_id' => (int)fun_get::get("s_user_id"),
			'state' => (int)fun_get::get("s_state" , -999),
			'key' => fun_get::get("s_key"),
		);
		if( fun_is::isdate( $arr_search_key['addtime1'] ) ) $arr_where_s[] = "order_addtime >= '" . strtotime( $arr_search_key['addtime1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['addtime2'] ) ) $arr_where_s[] = "order_addtime <= '" . fun_get::endtime($arr_search_key['addtime2']) . "'"; 
		if(!fun_is::isdate( $arr_search_key['addtime1'] ) && !fun_is::isdate( $arr_search_key['addtime2'] ) && fun_get::get('url_channel')!='all') {
			$arr_where[] = "order_addtime >='" . strtotime(date("Y-m-d")) . "' and order_addtime<='" . fun_get::endtime(date("Y-m-d")) ."'"; 
		}
		if( $arr_search_key['state'] != -999 ) $arr_where_s[] = "order_state = '" . $arr_search_key['state'] . "'"; 
		if( $arr_search_key['user_id'] != 0 ) $arr_where_s[] = "order_user_id = '" . $arr_search_key['user_id'] . "'"; 
		if( $arr_search_key['key'] != '' ) $arr_where_s[] = "(order_name like '%" . $arr_search_key['key'] . "%' or order_tel like '%" . $arr_search_key['key'] . "%' or order_mobile like '%" . $arr_search_key['key'] . "%')";
		//合并查询数组
		//if($this->admin_shop["id"] != -999) $arr_where[] = "order_shop_id='" . $this->admin_shop["id"] . "'";
		$arr_where = array_merge($arr_where , $arr_where_s);
		if(count($arr_where)>0) $str_where = " where " . implode(" and " , $arr_where);
		$arr_return = $this->sql_list($str_where , (int)fun_get::get('page'));

		if( count($arr_where_s) > 0 ) $lng_issearch = 1;
		$arr_return['issearch'] = $lng_issearch;
		return $arr_return;
	}


	/* 实现按具体条件查询数据表，并返回分页信息
	 * str_where : sql 查询条件 , lng_page : 当前页
	 */
	function sql_list($str_where = "" , $lng_page = 1) {
		$arr_return = array("list" => array());
		$obj_db = cls_obj::db();
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("meal.order" , $this->app_dir , "meal");
		$arr_cfg_fields["sel"] = substr(str_replace(",user_name," , "," , "," . $arr_cfg_fields["sel"] . ","),1,-1);
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("meal.order"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];
		$arr_state = tab_meal_order::get_perms("state");
		$arr_award = tab_meal_order::get_perms("award");
		//取分页信息
		$arr_uid = $arr_act_id = array();
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."meal_order" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."meal_order" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			if(isset($obj_rs["order_addtime"])) $obj_rs["order_addtime"] = date("Y-m-d H:i:s" , $obj_rs["order_addtime"]);
			if(isset($obj_rs["order_act_ids"])) $arr_act_id[] = $obj_rs["order_act_ids"];
			if(isset($obj_rs["order_state"])) {
				$obj_rs['state'] = $obj_rs['order_state'];
				if($obj_rs["order_state"]>0) {
					$obj_rs["order_state"] = array_search($obj_rs["order_state"] , $arr_state);
				} else {
					$obj_rs["order_state"] = "<font color='#ff0000'>" . array_search($obj_rs["order_state"] , $arr_state) . "</font>";
				}
			}
			if(isset($obj_rs["order_isaward"])) {
				if($obj_rs["order_isaward"]>0) {
					$obj_rs["order_isaward"] = array_search($obj_rs["order_isaward"] , $arr_award);
				} else if($obj_rs["order_isaward"]<0) {
					$obj_rs["order_isaward"] = "<font color='#ff0000'>" . array_search($obj_rs["order_isaward"] , $arr_award) . "</font>";
				} else {
					$obj_rs["order_isaward"] = "<font color='#888888'>" . array_search($obj_rs["order_isaward"] , $arr_award) . "</font>";
				}
			}
			$arr_uid[] = $obj_rs['order_user_id'];
			$arr_return["list"][] = $obj_rs;
		}
		if(count($arr_uid)>0) {
			$user_info = cls_obj::get("cls_user")->get_user($arr_uid);
			$count = count($arr_return["list"]);
			for($i = 0 ; $i < $count ; $i++) {
				$arr_return["list"][$i]['user_name'] = array_search($arr_return["list"][$i]['order_user_id'] , $user_info);
			}
		}

		if(count($arr_act_id)>0) {
			$str_ids = implode(",", $arr_act_id);
			$arr_act = $arr_act_id = array();
			$arr = explode(",", $str_ids);
			foreach($arr as $item) {
				if(is_numeric($item)) $arr_act_id[] = $item;
			}
			$str_ids = implode(",", $arr_act_id);
			if(empty($str_ids)) $str_ids = '0';
			$obj_result = $obj_db->select("select act_name,act_id from " . cls_config::DB_PRE . "meal_act where act_id in(" . $str_ids . ")");
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_act["id_".$obj_rs["act_id"]] = $obj_rs["act_name"];
			}
			$arr = array();
			foreach($arr_return["list"] as $item) {
				if(!empty($item['order_act_ids'])){
					$str_ids = explode(",", $item['order_act_ids']);
					$arr_actname = array();
					foreach($str_ids as $actid) {
						if(isset($arr_act['id_' . $actid])) $arr_actname[] = $arr_act['id_' . $actid];
					}
					$item['order_act_ids'] = implode("<br>" , $arr_actname);
				}
				$arr[] = $item;
			}
			$arr_return["list"] = $arr;
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}

	/* 删除指定  order_id 数据
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
		$arr = tab_meal_order::on_delete($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	/* 确认定单并奖励
	 */
	function on_award() {
		$arr_return = array("code"=>0 , "msg"=> "奖励成功");
		$str_id = fun_get::get("id");
		$arr_id = fun_get::get("selid");
		if( empty($arr_id) && empty($str_id) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = "未指定要处理的订单";
			return $arr_return;
		}
		if(!empty($arr_id)) $str_id = $arr_id; //优先考虑 arr_id
		$arr = tab_meal_order::on_award($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	/* 确认定单并奖励
	 */
	function on_state() {
		$arr_return = array("code"=>0 , "msg"=> "处理成功");
		$str_id = fun_get::get("id");
		$arr_id = fun_get::get("selid");
		$beta = fun_get::get("state_beta");
		if(!empty($arr_id)) $str_id = $arr_id; //优先考虑 arr_id
		$state = (fun_get::get("state_val") == 1) ? 1 : -4;
		$arr = tab_meal_order::on_state($str_id , $state , $beta , "order_state=0");
		if($arr['code'] != 0) return $arr;
		return $arr_return;
	}

	function get_detail($id) {
		$obj_db = cls_obj::db();
		$arr_return = array("list" => array() , "arrivetime" => array() , "newid" => 0);
		$arr_day = $arr_menu_ids = $arr_act_id = array();
		$obj_rs = $obj_db->get_one("select * from " . cls_config::DB_PRE . "meal_order where order_id='" . $id . "' limit 0,1");
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
		if(!empty($obj_rs["order_detail"])) {
			$arr_detail = unserialize($obj_rs["order_detail"]);
			if(isset($arr_detail["menu_price"])) $arr_return["price"] = $arr_detail["menu_price"];
		}
		$arr_return["detail"] = $obj_rs;
		$arr_menu_ids = explode("," , str_replace("|" , "," , $obj_rs["order_ids"]));
		$str_ids = implode("," , $arr_menu_ids);
		$obj_result = $obj_db->select("select menu_id,menu_title,menu_pic_small,menu_pic,menu_price from " . cls_config::DB_PRE . "meal_menu where menu_id in(" . $str_ids . ")");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_day[] = $obj_rs;
			$arr_return["menu"]["id_".$obj_rs["menu_id"]] = $obj_rs;
			if(!isset($arr_return["price"]["id_".$obj_rs["menu_id"]])) $arr_return["price"]["id_".$obj_rs["menu_id"]] = $obj_rs["menu_price"];
		}
		if(!isset($arr_return["detail"])) $arr_return["detail"] = array();
		if( !empty($arr_return["detail"]["order_act_ids"]) ) {
			$arr_act = $arr_act_id = array();
			$arr = explode(",", $arr_return["detail"]["order_act_ids"]);
			foreach($arr as $item) {
				if(is_numeric($item)) $arr_act_id[] = $item;
			}
			$str_ids = implode(",", $arr_act_id);
			if(empty($str_ids)) $str_ids = '0';
			$obj_result = $obj_db->select("select act_name,act_id from " . cls_config::DB_PRE . "meal_act where act_id in(" . $str_ids . ")");
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_act["id_".$obj_rs["act_id"]] = $obj_rs["act_name"];
			}
			$arr_actname = array();
			foreach($arr_act_id as $actid) {
				if(isset($arr_act['id_' . $actid])) $arr_actname[] = $arr_act['id_' . $actid];
			}
			$arr_return["detail"]['order_act_ids'] = implode("<br>" , $arr_actname);
		}
		//取店铺送餐时间
		$arr_return["arrivetime"] = cls_config::get("arrive_time" , "meal");

		return $arr_return;
	}
}