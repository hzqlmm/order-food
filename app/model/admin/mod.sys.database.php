<?php
/**
 * 用户模型类 关联表名：sys_user
 * 
 */
class mod_sys_database extends inc_mod_admin {
	/* 获取当前数据库所有表
	 * 
	 */
	function get_tables() {
		$arr_table = array();
		$obj_db = cls_obj::db();
		$obj_result = $obj_db->query("show table status");
		$code_mode = (int)cls_config::get("code_mode","base");
		$pre = strtolower(cls_config::DB_PRE);
		$len = strlen($pre);
		while ($obj_rs = $obj_db->fetch_array($obj_result) ) {
			if($code_mode != 1 && strtolower(substr($obj_rs['Name'],0,$len)) != $pre ) continue;
			$arr_table[]=$obj_rs;
		}
		return $arr_table;
	}
	function get_reback() {
		$backupname = fun_get::get("backupname");
		$arr_return = cls_database::get_backuplist($backupname);
		return $arr_return;
	}
	//优化表
	function on_optimize() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("optimize_ok"));
		$str_id = fun_get::get("id");
		$arr_id = fun_get::get("selid");
		if( empty($arr_id) && empty($str_id) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = cls_language::get("no_sel_table");
			return $arr_return;
		}
		if(empty($arr_id)) $arr_id[] = $str_id; //优先考虑 arr_id
		$obj_db = cls_obj::db_w();
		$ii = 0;
		foreach($arr_id as $item) {
			$arr = $obj_db->on_exe("optimize table " . $item);
			if($arr["code"] != 0) $ii++;
		}
		if($ii == count($arr_id) ) {
			$arr_return['code'] = $arr["code"];//见参数说明表
			$arr_return['msg']  = $arr["msg"];
		} else if($ii > 0) {
			$arr_return['code'] = 112;//见参数说明表
			$arr_return['msg']  = cls_language::get("optimize_part_err" );
		}
		return $arr_return;

	}
	//优化表
	function on_repair() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("repair_ok"));
		$str_id = fun_get::get("id");
		$arr_id = fun_get::get("selid");
		if( empty($arr_id) && empty($str_id) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = cls_language::get("no_sel_table" );
			return $arr_return;
		}
		if(empty($arr_id)) $arr_id[] = $str_id; //优先考虑 arr_id
		$obj_db = cls_obj::db_w();
		$ii = 0;
		foreach($arr_id as $item) {
			$arr = $obj_db->on_exe("repair table " . $item . " quick");
			if($arr["code"] != 0) $ii++;
		}
		if($ii == count($arr_id) ) {
			$arr_return['code'] = $arr["code"];//见参数说明表
			$arr_return['msg']  = $arr["msg"];
		} else if($ii > 0) {
			$arr_return['code'] = 112;//见参数说明表
			$arr_return['msg']  = cls_language::get("repair_part_err");
		}
		return $arr_return;
	}
	//备份
	function on_backup() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("backup_ok"));
		$tablename = fun_get::get("tablename");
		$backupname = fun_get::get("backupname");
		if( empty($backupname) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = cls_language::get("no_backupname" );
			return $arr_return;
		}
		if( empty($tablename) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = cls_language::get("no_sel_table" );
			return $arr_return;
		}
		$arr_return = cls_database::backup_table($backupname , $tablename);
		return $arr_return;
	}
	//备份行
	function on_backup_row() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("backup_ok"));
		$tablename = fun_get::get("tablename");
		$backupname = fun_get::get("backupname");
		if( empty($backupname) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = cls_language::get("no_backupname" );
			return $arr_return;
		}
		if( empty($tablename) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = cls_language::get("no_sel_table" );
			return $arr_return;
		}
		$page = (int)fun_get::get("page");
		$total = (int)fun_get::get("total");
		$arr_return = cls_database::backup_row($backupname , $tablename , $page , $total);
		return $arr_return;
	}
	//获取数据库所有表
	function on_reback_gettable() {
		$backupname = fun_get::get("backupname");
		$arr_return = cls_database::get_backuplist($backupname);
		return $arr_return;
	}
	//获取数据库所有表
	function on_reback_table() {
		$backupname = fun_get::get("backupname");
		$tablename = fun_get::get("tablename");
		$arr_return = cls_database::reback_table($backupname , $tablename);
		return $arr_return;
	}
	//获取数据库所有表
	function on_reback_row() {
		$backupname = fun_get::get("backupname");
		$tablename = fun_get::get("tablename");
		$page = fun_get::get("page");
		$arr_return = cls_database::reback_row($backupname , $tablename , $page);
		return $arr_return;
	}
	//删除备份
	function on_del_backup() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("delete_ok"));
		$backupname = fun_get::get("backupname");
		fun_file::dir_delete(cls_database::get_backupdir() . $backupname);
		return $arr_return;
	}
	//删除备份表
	function on_del_table() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("delete_ok"));
		$backupname = fun_get::get("backupname");
		$tablename = fun_get::get("tablename");
		fun_file::dir_delete(cls_database::get_backupdir() . $backupname . "/" . $tablename);
		return $arr_return;
	}
}
?>