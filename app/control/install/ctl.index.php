<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_index extends mod_index{
	function act_default(){
		return $this->get_view();
	}
	function act_step2(){

		$this->config_info = array(
			"DB_HOST" => fun_get::get("DB_HOST"),
			"DB_NAME" => fun_get::get("DB_NAME"),
			"DB_USER" => fun_get::get("DB_USER"),
			"DB_PWD" => fun_get::get("DB_PWD"),
			"DB_PRE" => fun_get::get("DB_PRE"),
			"WEB_ADMIN" => fun_get::get("WEB_ADMIN"),
			"WEB_ADMIN_PWD" => fun_get::get("WEB_ADMIN_PWD"),
			"WEB_DIR" => fun_get::get("WEB_DIR"),
			"COOKIE_PRE" => fun_get::get("COOKIE_PRE"),
			"KLKKDJ_UNAME" => fun_get::get("KLKKDJ_UNAME"),
			"KLKKDJ_PWD" => fun_get::get("KLKKDJ_PWD"),
			'app_act'=>'step3'
		);
		$gd_info_version = '';
		if(extension_loaded("gd")) {
			$gd_info = gd_info();
			$arr = explode("(" , $gd_info["GD Version"]);
			if(count($arr)>1) {
				$arr = explode(" " , $arr[1]);
				$gd_info_version = $arr[0];
			}
		}
		$zip = false;
		if(extension_loaded("zip")) $zip = true;
		$this->server_info = array(
			"php_version" => PHP_VERSION,
			"gd_info" => $gd_info_version,
			"zip" => $zip,
		);
		$this->dir_info = array(
			"data" => array("name" => basename(KJ_DIR_DATA),"write" => fun_file::dir_limit(KJ_DIR_DATA) ),
			"cache" => array("name" => basename(KJ_DIR_CACHE),"write" => fun_file::dir_limit(KJ_DIR_CACHE) ),
			"upload" => array("name" => basename(KJ_DIR_UPLOAD),"write" => fun_file::dir_limit(KJ_DIR_UPLOAD) ),
			"lib" => array("name" => basename(KJ_DIR_LIB),"write" => fun_file::dir_limit(KJ_DIR_LIB) ),
			"app" => array("name" => basename(KJ_DIR_APP),"write" => fun_file::dir_limit(KJ_DIR_APP) ),
		);
		return $this->get_view();
	}
	//还原数据表
	function act_install_table() {
		$arr_return = $this->on_install_table();
		return fun_format::json($arr_return);
	}
	//还原数据表
	function act_install_row() {
		$arr_return = $this->on_install_row();
		return fun_format::json($arr_return);
	}
	//还原数据表
	function act_install_gettable() {
		$arr_return = $this->on_install_gettable();
		return fun_format::json($arr_return);
	}
	//配置数据库
	function act_config() {
		$arr_return = $this->on_config();
		return fun_format::json($arr_return);
	}
	//配置数据库
	function act_initdata() {
		$arr_return = $this->on_initdata();
		return fun_format::json($arr_return);
	}
}