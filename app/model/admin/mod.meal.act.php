<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_meal_act extends inc_mod_meal {
	/* 按模块查询菜单信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_pagelist() {
		$arr_where = array("act_isdel=0");
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
		if( fun_is::isdate( $arr_search_key['addtime1'] ) ) $arr_where_s[] = "act_addtime >= '" . strtotime( $arr_search_key['addtime1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['addtime2'] ) ) $arr_where_s[] = "act_addtime <= '" . fun_get::endtime($arr_search_key['addtime2']) . "'"; 
		if( $arr_search_key['state'] != -999 ) $arr_where_s[] = "act_state = '" . $arr_search_key['state'] . "'"; 
		if( $arr_search_key['key'] != '' ) $arr_where_s[] = "(act_name like '%" . $arr_search_key['key'] . "%' or act_beta like '%" . $arr_search_key['key'] . "%')";
		//合并查询数组
		//if($this->admin_shop["id"] != -999) $arr_where[] = "act_shop_id='" . $this->admin_shop["id"] . "'";
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
		$arr_cfg_fields = tab_sys_user_config::get_fields("meal.act" , $this->app_dir , "meal");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("meal.act"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];
		$arr_state = tab_meal_act::get_perms("state");
		$arr_where = tab_meal_act::get_perms("where");
		$arr_method = tab_meal_act::get_perms("method");
		//取分页信息
		$arr_return["list"] = $arr_areaid = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."meal_act" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."meal_act" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			if(isset($obj_rs["act_addtime"])) $obj_rs["act_addtime"] = date("Y-m-d H:i:s" , $obj_rs["act_addtime"]);
			if(isset($obj_rs["act_state"])) {
				$obj_rs["state:style"] = $this->get_state_style($obj_rs["act_state"]);
				$obj_rs["act_state"] = array_search($obj_rs["act_state"],$arr_state);
				if(!empty($obj_rs["state:style"])) $obj_rs["act_state"] = "<font ".$obj_rs["state:style"].">" . $obj_rs["act_state"] . "</font>";
			}
			if(isset($obj_rs["act_where"])) $obj_rs["act_where"] = array_search($obj_rs["act_where"],$arr_where);
			if(isset($obj_rs["act_method"])) $obj_rs["act_where"] = array_search($obj_rs["act_method"],$arr_method);

			$arr_return["list"][] = $obj_rs;
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}

	/* 删除指定  act_id 数据
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
		$arr = tab_meal_act::on_del($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	/* 查询配置表指定id信息
	 * msg_id : sys_config 表中 config_id
	 */
	function get_editinfo($msg_id) {
		$obj_rs = cls_obj::db()->edit(cls_config::DB_PRE."meal_act" , "act_id='".$msg_id."'");
		if( empty($obj_rs["act_id"]) ) {
			$obj_rs["act_state"] = 1;
		}
		if($obj_rs['act_where'] == 2 || $obj_rs['act_where'] == 4) {
			$arr = explode(",",$obj_rs['act_where_val']);
			$obj_rs['act_where_val1'] = $arr[0];
			$obj_rs['act_where_val2'] = (count($arr)>1)? $arr[1] : '';
			$obj_rs['act_where_val3'] = (count($arr)>2)? $arr[2] : '';
		}
		return $obj_rs;
	}
	//保存
	function on_save() {
		$arr_return = array("code" => 0 , "id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_fields = array(
			"id"     => (int)fun_get::post("id"),
			"act_shop_id" => fun_get::post("act_shop_id"),
			"act_name" => fun_get::post("act_name"),
			"act_where" => fun_get::post("act_where"),
			"act_where_val"  => fun_get::post("act_where_val"),
			"act_method"  => (int)fun_get::post("act_method"),
			"act_method_val"   => fun_get::post("act_method_val"),
			"act_starttime"    => fun_get::post("act_starttime"),
			"act_endtime"  => fun_get::post("act_endtime"),
			"act_state"  => fun_get::post("act_state"),
			"act_beta"  => fun_get::post("act_beta"),
		);
		$arr = tab_meal_act::on_save($arr_fields);
		if($arr['code']==0) {
			if(isset($arr['id'])) $arr_return['id'] = $arr['id'];
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
}