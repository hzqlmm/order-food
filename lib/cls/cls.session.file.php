<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 * 名称 ：会话操作实类
 * 功能 ：存储与获取相关信息
 */
class cls_session extends cls_session_base {
	//获取当前会话，指定关健
	function get($key) {
		self::init_session();
		if( isset($_SESSION[$key]) ) {
			return $_SESSION[$key];
		} else {
			return '';
		}
	}
	function init_session() {
		if(isset($_SESSION)) return;
		$str_path = KJ_DIR_DATA."/session";
		if(cls_config::SESSION_SAVEPATH!="") {
			$str_path = cls_config::SESSION_SAVEPATH;
		}
		session_save_path( $str_path );
		ini_set("session.gc_probability",1);
		ini_set("session.gc_divisor", cls_config::SESSION_DIVISOR);
		ini_set("session.gc_maxlifetime",cls_config::SESSION_MAXLIFETIME);
		session_start();
	}
}