<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_meal_menu_today extends inc_mod_meal {

	/* 实现按具体条件查询数据表，并返回分页信息
	 * str_where : sql 查询条件
	 */
	function sql_list( $today_date , $today_date_period) {
		if(!is_numeric($today_date)) $today_date = strtotime($today_date);
		$arr_return = array("list" => array() , "list_group" => array() );
		$str_where = " where today_date='" . $today_date . "' and today_date_period='" . $today_date_period . "'";
		if($this->admin_shop["id"] != -999) $str_where .= " and today_shop_id='" . $this->admin_shop["id"] . "'";
		$obj_db = cls_obj::db();
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("meal.menu.today" , $this->app_dir , "meal");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("meal.menu.today"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$arr_return["list"] = $arr_group_id = array();
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."meal_menu_today a left join " . cls_config::DB_PRE . "meal_menu b on a.today_menu_id=b.menu_id" . $str_where . $sort);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$arr_return["list"]["id_".$obj_rs["menu_group_id"]][] = $obj_rs;
			if(!empty($obj_rs["menu_group_id"])) $arr_group_id[] = $obj_rs["menu_group_id"];
		}
		if(count($arr_group_id) > 0) {
			$str_ids = implode("," , array_unique($arr_group_id));
			$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."meal_menu_group where group_id in(" . $str_ids . ") order by group_sort");
			while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
				$arr_return["list_group"]["id_".$obj_rs["group_id"]] = $obj_rs;
			}
			foreach($arr_group_id as $item) {
				if(!isset($arr_return["list_group"]["id_".$item])) {
					$arr_return["list_group"]["id_".$item] = array("group_id" => $item ,"group_name" => "未分组");
				}
			}
		}
		return $arr_return;
	}
	/** 获取未添加的菜品列表
	 *
	 */
	function menu_list() {
		$arr_return = array("list" => array() , "list_group" => array() );
		$get_today_date = strtotime(fun_get::get("url_date"));
		$get_today_date_period = (int)fun_get::get("url_date_period");
		$obj_db = cls_obj::db();
		$arr_id = $arr_group_id = array();
		$obj_result = $obj_db->select("SELECT today_menu_id FROM ".cls_config::DB_PRE."meal_menu_today where today_date='" . $get_today_date . "' and today_date_period='" . $get_today_date_period . "' and today_shop_id='" . $this->admin_shop["id"] . "'");
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$arr_id[] = $obj_rs["today_menu_id"];
		}
		$str_where = " where menu_mode=2";//只允许选择自定义模式的菜品
		if(count($arr_id) > 0 ) $str_where .= " and menu_id not in(" . implode(",", $arr_id) . ") and menu_shop_id='" . $this->admin_shop["id"] . "'";

		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."meal_menu" . $str_where);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$arr_return["list"]["id_".$obj_rs["menu_group_id"]][] = $obj_rs;
			$arr_group_id[] = $obj_rs["menu_group_id"];
		}
		if(count($arr_group_id) > 0) {
			$str_ids = implode("," , array_unique($arr_group_id));
			$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."meal_menu_group where group_id in(" . $str_ids . ") order by group_sort");
			while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
				$arr_return["list_group"]["id_".$obj_rs["group_id"]] = $obj_rs;
			}
			foreach($arr_group_id as $item) {
				if(!isset($arr_return["list_group"]["id_".$item])) {
					$arr_return["list_group"]["id_".$item] = array("group_id" => $item ,"group_name" => "未分组");
				}
			}
		}
		return $arr_return;
	}

	/* 保存数据
	 * 
	 */
	function on_save() {
		$arr_return = array("code" => 0 ,"id"=>0 , "msg" => cls_language::get("save_ok"));
		$get_today_date = fun_get::get("url_date");
		$get_today_date_period = (int)fun_get::get("url_date_period");
		if(!fun_is::isdate($get_today_date)) {
			$arr_return['code'] = 22;
			$arr_return['msg']  = cls_language::get("today_date_null", "meal");//所属日期不能为空
			return $arr_return;
		}
		$get_today_date = strtotime($get_today_date);
		$arr_today_id = fun_get::post("today_id");
		if(fun_is::set("today_num")) $arr_today_num = fun_get::post("today_num");
		if(fun_is::set("today_menu_id")) $arr_today_menu_id = fun_get::post("today_menu_id");
		//循环统计已有 id
		$lng_count = count($arr_today_id);
		$arr_id = array();
		for( $i = 1 ; $i < $lng_count ; $i++) {
			$lng_id = (int)$arr_today_id[$i];
			if($lng_id > 0) $arr_id[] = $lng_id;
		}
		$str_ids = fun_format::arr_id($arr_id);
		$str_where = "today_date='" . $get_today_date . "' and today_date_period='" . $get_today_date_period . "'";
		if( !empty($str_ids) ) {
			$str_where .= " and today_id not in(".$str_ids.")";
		}
		//首先删除没在保存id中的所有记录
		tab_meal_menu_today::on_delete(array(),$str_where);
		$arr_resave = array();
		$lng_count = count($arr_today_id);
		for( $i = 1 ; $i < $lng_count ; $i++) {
			$arr_fields = array(
				"today_id" => (int)$arr_today_id[$i],
				"today_date" => $get_today_date,
				"today_date_period" => $get_today_date_period,
				"today_shop_id" => $this->admin_shop["id"],
			);
			if(isset($arr_today_num)) $arr_fields["today_num"] = $arr_today_num[$i];
			if(isset($arr_today_menu_id)) $arr_fields["today_menu_id"] = $arr_today_menu_id[$i];
			$arr = tab_meal_menu_today::on_save($arr_fields);
			if($arr["code"] != 0) {
				$arr_return['code'] = $arr["code"];
				$arr_return['msg'] = $arr["msg"];
				return $arr_return;
			}
		}
		return $arr_return;
	}

}