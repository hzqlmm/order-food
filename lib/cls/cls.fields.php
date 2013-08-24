<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class cls_fields{
	static $perms = array();
	static function get($msg_name,$msg_type){
		self::get_user_fields($msg_name,$msg_type);
		$arr_language = cls_language::get( $msg_name , "database" ) ;
		$arr = $arr_tit = $arr_td = array();
		foreach(self::$perms[$msg_type][$msg_name] as $item => $key) {
			$arr[] = $item;
			if($key["val"] == 1) {
				$arr_tit[] = array("name" => $arr_language[$item] , "w" => $key["w"] , "key" => $item);
				$arr_td[] = $item;
			}
		}
		$arr_return = array(
			"sel" => implode("," , $arr) ,
			"tabtit" => $arr_tit ,
			"tabtd" => $arr_td
		);
		return $arr_return;
	}
	static function get_user_fields($msg_name , $msg_type , $msg_uid = 0) {
		if( isset(self::$perms[$msg_type][$msg_name]) ) return self::$perms[$msg_type][$msg_name];
		if( empty($msg_uid) ) $msg_uid = cls_obj::get("cls_user")->uid;
		$arr = tab_sys_user_config::get_config("config_fields");
		$arr_userconfig = array();
		if(isset($arr[$msg_type])) $arr_userconfig = $arr[$msg_type];
		if( !isset(self::$perms[$msg_type][$msg_name]) ){
			//没有保存过，则从配置文件取
			$arr_type = include( KJ_DIR_DATA."/fields/".$msg_type.".php" );
			foreach($arr_userconfig as $item => $key) {
				foreach($key as $keynext=>$keyval) {
					if(!isset($arr_type[$item][$keynext])) continue;
					if($keyval["val"] >= 10 && $arr_type[$item][$keynext]["val"]<10) continue;
					$arr_type[$item][$keynext] = $keyval;
				}
			}
			self::$perms[$msg_type] = $arr_type;
		}
		if( !isset(self::$perms[$msg_type][$msg_name]) ){
			cls_error::on_error( 'fields_no' );
		}
		return self::$perms[$msg_type][$msg_name];
	}
	//取所有能设置的字段包括未显示的
	static function get_show($msg_name,$msg_type) {
		self::get_user_fields($msg_name,$msg_type);
		$arr_language = cls_language::get( $msg_name , "database" ) ;
		$arr_return = array();
		foreach(self::$perms[$msg_type][$msg_name] as $item => $key) {
			if($key["val"] < 10) {
				if(!isset($arr_language[$item])) $arr_language[$item] = $item;
				$arr_return[$item] = array("name" => $arr_language[$item] , "w" => $key["w"] , "key" => $item , "val" => $key["val"] );
			}
		}
		return $arr_return;
	}
}