<?php
/*
 *
 *
 * 2013-03-24
 */
class tab_sys_user_config {
	static $perms;
	/* 保存操作
	 * arr_fields : 为字段数据 ,除 config_user_id 其它全是数组，需要序列化后保存到数据库
	 */
	static function on_save($arr_fields) {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(!isset($arr_fields['config_user_id']) || empty($arr_fields['config_user_id']) ) {
			$arr_fields['config_user_id'] = cls_obj::get("cls_user")->uid;
		}
		if(isset($arr_fields["config_fields"]) && is_array($arr_fields["config_fields"]) ) $arr_fields["config_fields"] = serialize($arr_fields["config_fields"]);
		if(isset($arr_fields["config_info"]) && is_array($arr_fields["config_info"]) ) $arr_fields["config_info"] = serialize($arr_fields["config_info"]);
		$obj_db = cls_obj::db_w();
		$arr = $obj_db->get_one("select config_user_id from " . cls_config::DB_PRE . "sys_user_config where config_user_id='" . $arr_fields["config_user_id"] . "'");
		if( empty($arr) ) {			
			//插入到表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."sys_user_config",$arr_fields);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = $arr['msg'];
			}
		} else {
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE . "sys_user_config" , $arr_fields , "config_user_id=" . $arr_fields["config_user_id"]);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = $arr['msg'];
			}
		}
		return $arr_return;
	}

	//取指定字段信息
	static function get_config($field , $uid = 0 ) {
		if(isset(self::$perms[$field])) return self::$perms[$field];
		if(empty($uid)) $uid = cls_obj::get("cls_user")->uid;
		$arr = cls_obj::db()->get_one("select " . $field . " from " . cls_config::DB_PRE . "sys_user_config where config_user_id='" . $uid . "'");
		self::$perms[$field] = array();
		if(!empty($arr) && !empty($arr[$field])) {
			self::$perms[$field] = unserialize($arr[$field]);
		}
		return self::$perms[$field];
	}
	/* 取指定表格显示列信息
	 * msg_name 为表名，msg_type 为文件名
	 * 相同表保存到同一个配置文件里，相同表是指：sys_user, sys_user_log 等　这样
	 */
	static function get_fields($msg_name , $msg_type , $msg_filename = ''){
		self::get_user_fields($msg_name , $msg_type , 0 , $msg_filename);
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
	/* 取指定用户显示列信息
	 * 返回数组
	 */
	static function get_user_fields($msg_name , $msg_type , $msg_uid = 0 , $filename = '') {
		if( isset(self::$perms[$msg_type][$msg_name]) ) return self::$perms[$msg_type][$msg_name];
		if( empty($msg_uid) ) $msg_uid = cls_obj::get("cls_user")->uid;
		$arr = self::get_config("config_fields" , $msg_uid);
		$arr_userconfig = array();
		if(isset($arr[$msg_type])) $arr_userconfig = $arr[$msg_type];
		if( !isset(self::$perms[$msg_type][$msg_name]) ){
			//没有保存过，则从配置文件取
			if(empty($filename)) {
				$arr = explode(".",$msg_name);
				(count($arr)>2)? $filename = $arr[0] . "." . $arr[1] : $filename = $msg_name;
			}
			$arr_new_type = array();
			$arr_type = include( KJ_DIR_DATA . "/config/" . $msg_type . "/" . $filename . ".php" );
			if(isset($arr_userconfig[$msg_name])) {
				foreach($arr_userconfig[$msg_name] as $item => $key) {
					if(!isset($arr_type[$msg_name][$item])) continue;//当文件配置发生改变，时清除数据库没在文件中存在的项
					if($key["val"] >= 10 && $arr_type[$msg_name][$item]["val"]<10) {//当文件配置发生改变，同步数据库在文件中项
						$key["val"] = $arr_type[$msg_name][$item]["val"];
					}
					unset($arr_type[$msg_name][$item]);
					$arr_new_type[$item] = $key;
				}
				$arr_new_type = array_merge($arr_new_type , $arr_type[$msg_name]);
			} else if(isset($arr_type[$msg_name])) {
				$arr_new_type = $arr_type[$msg_name];
			}
			self::$perms[$msg_type][$msg_name] = $arr_new_type;
		}
		if( !isset(self::$perms[$msg_type][$msg_name]) ){
			cls_error::on_error( 'fields_no' , array("debug"=>"Page:" . fun_get::basename(__FILE__) . "<br>Line:" . __LINE__));
		}
		return self::$perms[$msg_type][$msg_name];
	}
	//取所有能设置的字段包括未显示的
	static function get_fields_show($msg_name , $msg_type , $filename = '') {
		self::get_user_fields($msg_name , $msg_type , 0 , $filename);
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
	/** 取用户 排序字段,默认页大小
	 *  msg_key 表名 , $msg_type 目录名
	 *  返回带键名的数组 健:sort 数组，　sortby 字符串 , pagesize 默认页大小
	 */
	static function get_info($msg_key , $msg_type , $uid = 0) {
		$arr = self::get_config("config_info" , $uid);
		$arr_config_info = array();
		(isset($arr[$msg_type][$msg_key]["pagesize"])) ? $arr_return["pagesize"] = $arr[$msg_type][$msg_key]["pagesize"] : $arr_return["pagesize"] = cls_config::get("pagesize" , "sys" , 10);
		if(isset($arr[$msg_type][$msg_key]["sort"]) && count($arr[$msg_type][$msg_key]["sort"]) > 0 ) {
			$arr_config_info = $arr[$msg_type][$msg_key]["sort"];
		} else {
			if(file_exists(KJ_DIR_DATA . "/config/" . $msg_type . "/sort.php")) {
				$arr = include( KJ_DIR_DATA . "/config/" . $msg_type . "/sort.php" );
				if(isset($arr[$msg_key]) ) $arr_config_info = $arr[$msg_key];
			}
		}
		$arr_x = array();
		foreach($arr_config_info as $item => $key) {
			if(empty($key)) $key = "asc";
			$arr_x[] = $item . " " . $key;
		}
		$str_sortby = implode("," , $arr_x);
		if(!empty($str_sortby)) $str_sortby = " order by " . $str_sortby;
		$arr_return["sort"] = $arr_config_info;
		$arr_return["sortby"] = $str_sortby;
		return $arr_return;
	}
	/** 取用户 排序字段,默认页大小
	 *  msg_key 表名 , $msg_type 目录名
	 *  返回带键名的数组 健:sort 数组，　sortby 字符串 , pagesize 默认页大小
	 */
	static function get_var($msg_var , $msg_type , $uid = 0) {
		$arr = self::get_config("config_info" , $uid);
		(isset($arr[$msg_type]['var'][$msg_var])) ? $val = $arr[$msg_type]['var'][$msg_var] : $val = '';
		return $val;
	}
	/** 保存排序
	 *
	 */
	static function save_sort($msg_sortby , $msg_key , $msg_type , $uid = 0) {
		$arr_config = self::get_config("config_info" , $uid);
		$arr_config_info = array();
		if(isset($arr_config[$msg_type][$msg_key]["sort"])) $arr_config_info = $arr_config[$msg_type][$msg_key]["sort"];
		$bysort = 'asc';
		if(isset($arr_config[$msg_type][$msg_key]["sort"][$msg_sortby])) {
			$bysort = $arr_config[$msg_type][$msg_key]["sort"][$msg_sortby];
			unset($arr_config_info[$msg_sortby]);
			if($bysort == "asc") {
				$bysort = "desc";
			} else if($bysort == "desc" ) {
				$bysort = "";
			}
		}
		if(!empty($bysort)) {
			$arr_config_info[$msg_sortby] = $bysort;
		}
		$arr_config[$msg_type][$msg_key]["sort"] = $arr_config_info;
		$arr_return = self::on_save( array("config_info" => $arr_config , "config_user_id" => $uid ) ) ;
		return $arr_return;
	}
	/* 删除函数
	 * arr_id : 要删除的 id数组
	 * where : 删除附加条件
	 */
	static function on_delete($arr_id , $where = '') {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if( $str_id == "" && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]="id".cls_language::get("not_null");
			return $arr_return;
		}
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "config_user_id='".$str_id."'" : $arr_where[] = "config_user_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);
		$arr_return=cls_obj::db_w()->on_delete(cls_config::DB_PRE."sys_user_config" , $where);
		return $arr_return;
	}

}