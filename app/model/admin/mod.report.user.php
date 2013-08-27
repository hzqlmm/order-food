<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_report_user extends inc_mod_meal {
	/* 注册统计
	 * 默认为当天统计
	 */
	function regin_count() {
		$mode = fun_get::get("mode");
		switch($mode) {
			case "year":
				//按年
				$arr_return = $this->regin_count_byyear();
				break;
			case "month":
				//按月
				$arr_return = $this->regin_count_bymonth();
				break;
			default:
				//按天
				$arr_return = $this->regin_count_byday();
		}
		return $arr_return;
	}
	//按年
	function regin_count_byyear() {
		$arr_return = array("list" => '' , "sub"=> '' , "splitX" => 12);
		$date = fun_get::get("year" , date("Y"));
		$obj_db = cls_obj::db();
		$arr_list = $arr_sub = array();
		for($i = 1; $i <= $arr_return["splitX"] ; $i++ ) {
			$ii = $i;
			if($i<10) $ii = "0" . $i;
			$arr_list[$ii] = 0;
			$arr_sub[] = $i . "月";
		}
		$where = " where left(user_regdate,4)='" . $date . "'";		
		$obj_result = $obj_db->select("SELECT  left(user_regdate,7) as 'tips',count(1) as val FROM " . cls_config::DB_PRE . "sys_user" . $where . " group by left(user_regdate,7)");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$tips = substr($obj_rs["tips"] , -2);
			$arr_list[$tips] = $obj_rs['val'];
		}
		$arr_list = array_values($arr_list);
		$arr_return['data'] = str_replace('"' , '' , fun_format::json( $arr_list ));
		$arr_return['sub'] = fun_format::json( $arr_sub );
		return $arr_return;
	}
	//按月
	function regin_count_bymonth() {
		$arr_return = array("list" => '' , "sub"=> '' , "max" => 0 , "min" => 0);
		$year = fun_get::get("year" , date("Y"));
		$month = fun_get::get("month" , date("m"));
		if(strlen($month)<2) $month = "0" . $month;
		$date = $year . "-" . $month;
		$arr_return["splitX"] = (int)fun_get::end_day();
		$obj_db = cls_obj::db();
		$arr_list = $arr_sub = array();
		for($i = 1; $i <= $arr_return["splitX"] ; $i++ ) {
			$ii = $i;
			if($i<10) $ii = "0" . $i;
			$arr_list[$ii] = 0;
			$arr_sub[] = $i;
		}
		$channel = fun_get::get("channel");
		if($channel == "login") {
			$where = " where left(login_day,7)='" . $date . "'";
			$sql = "SELECT  login_day as 'tips',count(1) as 'val' FROM " . cls_config::DB_PRE . "sys_user_login" . $where . "  group by login_day";
		} else if($channel == "login_back") {
			$where = " where left(login_day,7)='" . $date . "' and login_isreg=0";
			$sql = "SELECT  login_day as 'tips',count(1) as 'val' FROM " . cls_config::DB_PRE . "sys_user_login" . $where . "  group by login_day";
		} else {
			$where = " where left(user_regdate,7)='" . $date . "'";
			$sql = "SELECT  left(user_regdate,10) as 'tips',count(1) 'val' FROM " . cls_config::DB_PRE . "sys_user" . $where . "  group by left(user_regdate,10)";
		}


		$obj_result = $obj_db->select($sql);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$tips = substr($obj_rs["tips"] , -2);
			$arr_list[$tips] = $obj_rs["val"];
		}
		$arr_list = array_values($arr_list);
		$arr_return['data'] = str_replace('"' , '' , fun_format::json( $arr_list ));
		$arr_return['sub'] = fun_format::json( $arr_sub );
		return $arr_return;
	}
	//按天
	function regin_count_byday() {
		$arr_return = array("list" => '' , "sub"=> '' , "splitX" => 24);
		$date = fun_get::get("date" , date("Y-m-d"));
		$obj_db = cls_obj::db();
		$arr_list = $arr_sub = array();
		for($i=0;$i<24;$i++) {
			$ii = $i;
			if($i<10) $ii = "0" . $i;
			$arr_list[$ii] = 0;
			$arr_sub[] = $ii;
		}
		$channel = fun_get::get("channel");
		if($channel == "login") {
			$where = " where login_day='" . $date . "'";
			$sql = "SELECT  left(login_time,13) as 'tips',count(1) as 'val' FROM " . cls_config::DB_PRE . "sys_user_login" . $where . "  group by left(login_time,13)";
		} else if($channel == "login_back") {
			$where = " where login_day='" . $date . "' and login_isreg=0";
			$sql = "SELECT  left(login_time,13) as 'tips',count(1) as 'val' FROM " . cls_config::DB_PRE . "sys_user_login" . $where . "  group by left(login_time,13)";
		} else {
			$where = " where left(user_regdate,10)='" . $date . "'";
			$sql = "SELECT  left(user_regdate,13) as 'tips',count(1) as 'val' FROM " . cls_config::DB_PRE . "sys_user" . $where . "  group by left(user_regdate,13)";
		}


		$obj_result = $obj_db->select($sql);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$tips = substr($obj_rs["tips"] , -2);
			$arr_list[$tips] = $obj_rs["val"];
		}
		$arr_list = array_values($arr_list);
		$arr_return['data'] = str_replace('"' , '' , fun_format::json( $arr_list ));
		$arr_return['sub'] = fun_format::json( $arr_sub );
		return $arr_return;
	}
}