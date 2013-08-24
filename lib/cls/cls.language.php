<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class cls_language{
	static $perms = array();
	static function get($msg_name,$msg_type="sys"){
		if( !isset(self::$perms[$msg_type]) ) {
			self::on_load($msg_type);
		}
		if( isset( self::$perms[$msg_type][$msg_name] ) ) {
			return self::$perms[$msg_type][$msg_name];
		} else {
			$arr = explode("_" , $msg_name);
			if(count($arr)>1) {
				$arr_x = array();
				foreach($arr as $item) {
					$arr_x[] = self::get($item , $msg_type);
				}
				$msg_name = implode("" , $arr_x);
			} else {
				foreach(self::$perms as $item) {
					if(isset($item[$msg_name])) return $item[$msg_name];
				}
			}
			return $msg_name;
		}
	}
	static function on_load($msg_type) {
		$str_lang = cls_obj::get("cls_session")->get_env("language");
		if( empty( $str_lang ) ) {
			$str_lang = cls_config::DEFAULT_LANGUAGE;
		}
		self::$perms[$msg_type] = include( KJ_DIR_DATA."/language/".$str_lang."/".$msg_type.".php" );
	}
}