<?php
/*
 *
 *
 * 2013-03-24
 */
class tab_sys_user_var {
	/* 更新用户某变量值
	 * var : 变量, val : 值
	 */
	static function on_save($var , $val , $user_id) {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(empty($words_val)) {
			$arr_return["code"] = 500;
			return $arr_return;
		}
		$obj_db = cls_obj::db_w();
		$arr = array();
		$obj_var = $obj_db->get_one("select * from " . cls_config::DB_PRE . "sys_user_var where var_user_id='" . $user_id . "'");
		if(!empty($obj_var) && !empty($obj_var["var_val"]) ) $arr = unserialize($obj_var["var_val"]);
		$arr[$var] = $val;
		$str_var = serialize($arr);
		if(!empty($obj_var)) {
			$arr_return = $obj_db->on_exe( "update " . cls_config::DB_PRE . "sys_user_var set var_val='" . $str_var . "' where var_user_id='" . $user_id  . "'" );
		} else {
			$arr_fields = array(
				"var_user_id" => $user_id ,
				"var_val" => $str_var ,
			);
			$arr_return = $obj_db->on_insert(cls_config::DB_PRE."sys_user_var",$arr_fields);
		}
		return $arr_return;
	}
	static function get($var , $user_id) {
		$obj_db = cls_obj::db_w();
		$arr = array();
		$obj_var = $obj_db->get_one("select * from " . cls_config::DB_PRE . "sys_user_var where var_user_id='" . $user_id . "'");
		if(!empty($obj_var) && !empty($obj_var["var_val"]) ) $arr = unserialize($obj_var["var_val"]);
		if(isset($arr[$var])) return $arr[$var];
		return "";
	}
}