<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
//环境配置文件
if(file_exists(KJ_DIR_DATA . '/config/env.php')) {
    include KJ_DIR_DATA . '/config/env.php';
}

if(!defined("ENV_SERVER")) {
	define("ENV_SERVER","online");//正式版
}

include KJ_DIR_DATA."/config/cfg.env.".ENV_SERVER.".php";
class cls_config extends cfg_env{
	static $perms = array();
	static function get($key , $type="base" , $default = '' , $dir = 'cfg/' ){
		if(!isset(self::$perms["get"][$type])){
			if(substr($dir , -1 , 1) != "/") $dir .= "/";
			if(file_exists(KJ_DIR_DATA . "/config/" . $dir . "cfg.".$type.".php")) {

				self::$perms["get"][$type] = include(KJ_DIR_DATA . "/config/" . $dir . "cfg.".$type.".php");
			} else {
				self::$perms["get"][$type] = array();
			}
		}
		if(empty($key)) {
			return self::$perms["get"][$type];
		} else {
			if(isset(self::$perms["get"][$type][$key])){
				return self::$perms["get"][$type][$key];
			}else{
				return $default;
			}
		}
	}
	static function set($key , $val = '' , $type="base" , $dir = 'cfg/' ){
		if(!isset(self::$perms["get"][$type])) {
			self::get($key , $type , '' , $dir);
		}
		self::$perms['get'][$type][$key] = $val;
	}
	static function get_data($type , $key = '' , $default = '') {
		if(!isset(self::$perms["get_data"][$type])){
			if(file_exists(KJ_DIR_DATA . "/config/data/".$type.".php")) {
				self::$perms["get_data"][$type] = include(KJ_DIR_DATA . "/config/data/".$type.".php");
			} else {
				self::$perms["get_data"][$type] = array();
			}
		}
		if(!empty($key) && isset(self::$perms["get_data"][$type][$key])){
			return self::$perms["get_data"][$type][$key];
		} else if(empty($key)) {
			return self::$perms["get_data"][$type];
		} else {
			return $default;
		}
	}
	static function set_date($type , $key , $val) {
		if(empty($key)) {
			$arr = $val;
		} else {
			$arr = self::get_data($type);
			$arr[$key] = $val;
		}
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA."/config/data/".$type.".php",$val,1);
	}

}