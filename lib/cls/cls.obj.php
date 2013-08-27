<?php
/*
 *
 *
 * 2013-03-24
 */
class cls_obj {
	static $obj_db;
	static $obj_db_w;
	static $perms;
	static function & db() {
		if(empty(self::$obj_db)){
			if(defined("cls_config::DB_SLAVE_HOST")) {
				//开启读写分离
				self::$obj_db = new cls_db(
											array(
												"db_host"=>cls_config::DB_SLAVE_HOST,
												"db_user"=>cls_config::DB_SLAVE_USER,
												"db_pwd"=>cls_config::DB_SLAVE_PWD,
												"db_name"=>cls_config::DB_SLAVE_NAME,
												"db_charset"=>cls_config::DB_CHARSET
												)
										   );
			} else {
				self::$obj_db = new cls_db( 
											array(
												"db_host"=>cls_config::DB_HOST,
												"db_user"=>cls_config::DB_USER,
												"db_pwd"=>cls_config::DB_PWD,
												"db_name"=>cls_config::DB_NAME,
												"db_charset"=>cls_config::DB_CHARSET
												)
										   );
			}
		}
		return self::$obj_db;
	}

	static function & db_w() {
		if(empty(self::$obj_db_w)){
			self::$obj_db_w = new cls_db_write( 
												array(
													"db_host"    => cls_config::DB_HOST,
													"db_user"    => cls_config::DB_USER,
													"db_pwd"     => cls_config::DB_PWD,
													"db_name"    => cls_config::DB_NAME,
													"db_charset" => cls_config::DB_CHARSET
												)
											  );
		}
		return self::$obj_db_w;
	}

	static function & get($msg_name) {
		if(empty(self::$perms["obj_".$msg_name])) {
			$str_x = $msg_name;
			self::$perms["obj_".$msg_name] = new $str_x;
		}
		return self::$perms["obj_".$msg_name];
	}
	//头信息
	static function header_info($cont = '') {
		self::$perms["header_info"] = (isset(self::$perms["header_info"])) ? self::$perms["header_info"] . $cont : $cont;
		return self::$perms["header_info"] . $cont;
	}
	//尾信息
	static function footer_info($cont = '') {
		self::$perms["footer_info"] = (isset(self::$perms["footer_info"])) ? self::$perms["footer_info"] . $cont : $cont;
		return self::$perms["footer_info"];
	}
}