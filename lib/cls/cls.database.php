<?php
/*
 *
 *
 * 2013-03-24
 */
class cls_database{
	static $perms = array(
			"no_reback" => array("sys_session" , "sys_user_log") ,
		);
	static $isinstall = false;
	static function get_backupdir() {
		if(self::$isinstall) {
			return KJ_DIR_DATA .  "/database/" ;
		} else {
			return KJ_DIR_DATA .  "/database/bak/" ;
		}
	}
	/** 备份数据行
	 *	backupname 备份名称，即目录名，tablename 表名 , page 将一个表分成多页存储
	 */
	function backup_row($backupname , $tablename , $page = 1 , $int_total= 0 , $pagesize = 10000) {
		$arr_return=array("code" => 0);
		$obj_db = cls_obj::db_w();
		if( $int_total == 0 ) {
			$obj_rs    = $obj_db->get_one( "SELECT count(1) as num FROM ".$tablename );
			$int_total = $obj_rs["num"];
		}
		$int_pages = ceil( $int_total / $pagesize );
		$page      = max(min($int_pages , $page) , 1);
		$int_offset = $pagesize * ($page - 1);
		$result = $obj_db->select("select * from " . $tablename . " limit " . $int_offset . "," . $pagesize);
		$str_keys = $str_values = $str_insert_sql = $str_loadfile = "";
		$str_newtable='{DB_PRE}' . substr($tablename , strlen(cls_config::DB_PRE) );
		while($obj_rs = $obj_db->fetch_array($result)) {
			$str_keys = $str_values = "";
			$arr_loadvalues = array();
			foreach($obj_rs as $item=>$key) {
				$str_keys .= "`" . $item . "`,";
				$str_values .= "'" . $obj_db->get_escape($key) . "',";
				$arr_loadvalues[] = $key;
			}
			$str_keys   = substr($str_keys , 0 , -1);
			$str_values = substr($str_values , 0 , -1);
			$str_insert_sql .= "INSERT INTO `" . $str_newtable . "` (" . $str_keys . ") values(" . $str_values . ");" . chr(10);
			$str_loadfile   .= implode("	" , $arr_loadvalues) . chr(10);
		}
		$str_sql  = "#-----------------添加记录表--- " . $tablename . chr(10) . $str_insert_sql;
		$str_path = self::get_backupdir() . $backupname . "/" . $tablename . "/" . $page . ".sql";
		$arr_return = fun_file::file_create($str_path , $str_sql , 1);
		//生成加载文件
		$str_path = self::get_backupdir() . $backupname . "/".$tablename . "/loadfile/" . $page . ".sql";
		fun_file::file_create($str_path , $str_loadfile , 1);
		$arr_return["page_count"] = $int_pages;
		$arr_return["next_page"] = $page + 1;
		$arr_return["total"] = $int_total;
		if($page == $int_pages) {
			$str_path1 = self::get_backupdir() . $backupname . "/".$tablename . "/innodb.sql";
			$str_path = self::get_backupdir() . $backupname . "/".$tablename . "/" . $arr_return["next_page"] . ".sql";
			$arr = fun_file::file_rename($str_path1 , $str_path);

		}
		return $arr_return;
	}
	//创建备份目录，与备份sql
	function backup_table($backupname,$tablename) {
		$arr_return = array("code" => 0);
		$obj_db = cls_obj::db_w();
		$result = $obj_db->query("SHOW FULL COLUMNS FROM ".$tablename);
		$arr_primary_key = array();
		$str_union_key = $str_key = $str_fields = $str_primary_key = '';
		while( $obj_rs = $obj_db->fetch_array($result) ) {
			$str_comment = '';
			(strtolower($obj_rs["Null"])=="yes" && !is_null($obj_rs['Default'])) ? $str_default = "default '" . $obj_rs['Default'] . "'" : $str_default = "NOT NULL";
			if(!empty($obj_rs["Comment"])) $str_comment = " COMMENT '" . $obj_rs["Comment"] . "'";
			$str_extra="";
			if($obj_rs["Extra"] != "") {
				$str_extra=" " . $obj_rs["Extra"];
			}
			if($obj_rs["Key"] != "") {
				if( $obj_rs["Key"] == "PRI" ) {
					$arr_primary_key[] = $obj_rs["Field"];
				} else if($obj_rs["Key"] == "UNI") {
					$str_union_key = "UNIQUE KEY `" . $obj_rs["Field"] . "` (`" . $obj_rs["Field"] . "`)," . chr(10);
				} else {
					if($str_key == "") {
						$str_key = "KEY `" . $obj_rs["Field"] . "` (`" . $obj_rs["Field"] . "`)";
					} else {
						$str_key .= "," . chr(10)  ."KEY `" . $obj_rs["Field"] . "` (`" . $obj_rs["Field"] . "`)";
					}
				}
			}
			$str_fields .= "`" . $obj_rs["Field"] . "` " . $obj_rs["Type"] . " " . $str_default . $str_extra . $str_comment . ",";
		}
		$result = $obj_db->query("show table status ");
		while($obj_rs = $obj_db->fetch_array($result)) {
			$arr_table_status[$obj_rs["Name"]] = $obj_rs;
		}
		if( count($arr_primary_key) > 0 ){
			$str_primary_key = "PRIMARY KEY (`" . implode("`,`" , $arr_primary_key) . "`)," . chr(10);
		}
		$str_table_key = $str_primary_key . $str_union_key . $str_key;
		$arr_charset = explode("_" , $arr_table_status[$tablename]["Collation"] );
		$str_charset = $arr_charset[0];
		if($str_table_key != "") {
			$str_table_key = trim($str_table_key);
			if(substr($str_table_key , -1) == ",") $str_table_key = substr($str_table_key , 0 , -1);
			$str_fields .= $str_table_key;
		} else {
			$str_fields = trim($str_fields);
			if(substr($str_fields , -1) == ",") $str_fields = substr($str_fields , 0 , -1);
		}
		
		$engine = $arr_table_status[$tablename]["Engine"];
		if(strtolower($engine) == 'innodb' ) $engine = 'myisam';
		$str_newtable = '{DB_PRE}' . substr($tablename , strlen(cls_config::DB_PRE));
		$str_create_sql="DROP TABLE IF EXISTS `" . $str_newtable . "`;" . chr(10) . "CREATE TABLE IF NOT EXISTS `" . $str_newtable . "` (" . chr(10) . $str_fields . chr(10) . ") ENGINE=" . $engine . "  DEFAULT CHARSET=" . $str_charset;
		if( isset($arr_table_status[$tablename]["Auto_increment"]) && $arr_table_status[$tablename]["Auto_increment"] > 0 ) {
			$str_create_sql .= " AUTO_INCREMENT=".$arr_table_status[$tablename]["Auto_increment"];
		}
		$str_sql="#-----------------创建表--- " . $tablename . chr(10) . $str_create_sql . chr(10) . chr(10);
		$str_path = self::get_backupdir() . $backupname . "/" . $tablename . "/create.sql";
		$arr_return = fun_file::file_create($str_path , $str_sql , 1);
		//如果是innodb类型将先转成myisam 然后导入完后再转回
		if( strtolower($arr_table_status[$tablename]["Engine"])=="innodb" ){
			$str_sql = 'alter table ' . $tablename . ' engine innodb;';
			$str_path = self::get_backupdir() . $backupname . "/" . $tablename . "/innodb.sql";
			fun_file::file_create($str_path , $str_sql , 1);
		}
		return $arr_return;
	}
	//获取备份目录列表
	static function get_backuplist( $backupname = '' ) {
		$arr = fun_file::get_dirs( self::get_backupdir() . $backupname );
		$arr_return = array();
		foreach($arr as $item) {
			$arr_return[] = array("name" => $item["name"]);
		}
		return $arr_return;
	}
	/**还原表
	 * backupname : 备份名称，tablename : 表名称 , no_reback : 0表示不还原 perms['no_reback'] 里包函的表
	 */
	static function reback_table($backupname , $tablename , $no_reback = 0) {
		$arr_return = array("code" => 0 , "msg" => "" , "len" => 0);
		$str_name = substr( $tablename , strlen(cls_config::DB_PRE) );
		if($no_reback == 0 && in_array($str_name , self::$perms["no_reback"]) ) return $arr_return;
		$str_path = self::get_backupdir() . $backupname . "/" . $tablename;
		$str_file = self::get_backupdir() . $backupname . "/" . $tablename . "/create.sql";
		if(!is_dir($str_path) || !file_exists($str_file)) {
			$arr_return["code"] = 323;
			$arr_return["msg"] = cls_language::get("backup_table_none");
			return $arr_return;
		}
		$str_sql = file_get_contents($str_file);
		$arr_return = self::on_exesql($str_sql);
		$arr_return["len"] = 0;
		$arr_file = fun_file::get_files( self::get_backupdir() . $backupname . "/" . $tablename);
		foreach($arr_file as $item) {
			$arr = explode( "." , $item["name"]);
			if( is_numeric($arr[0]) && strtolower($arr[1]) == "sql" ) $arr_return["len"]++;

		}
		return $arr_return;

	}
	//执行sql语句
	function on_exesql( $str_sql , $arr_replace = array() ) {
		$arr_return=array("code" => 0 , "msg" => "");
		$obj_db = cls_obj::db_w();
		$str_sql = str_replace("{DB_PRE}" , cls_config::DB_PRE , $str_sql);
		foreach( $arr_replace as $item => $key) {
			$str_sql = str_replace($item , $key , $str_sql);
		}
		$arr_sql = explode(";" . chr(10) , $str_sql);
		foreach( $arr_sql as $item ) {
			$item = trim($item);
			if( empty($item) ) continue;
			$arr = $obj_db->on_exe($item);
			if($arr['code'] != 0) return $arr;
		}
		return $arr_return;
	}
	//还原数据行，在备份文件将一个表分为多个文件存储
	static function reback_row($backupname , $tablename , $page , $no_reback = 0) {
		$arr_return = array("code" => 0 , "msg" => "" , "next" => 0);
		$str_path = self::get_backupdir() . $backupname . "/" . $tablename;
		$str_file = self::get_backupdir() . $backupname . "/" . $tablename . "/" . $page . ".sql";
		if(!is_dir($str_path) || !file_exists($str_file)) {
			return $arr_return;
		}
		$str_sql = file_get_contents($str_file);
		$arr = self::on_exesql($str_sql);
		if($arr["code"] != 0) {
			$arr_return["code"] = $arr["code"];
			$arr_return["msg"] = $arr["msg"];
		}
		$str_file = self::get_backupdir() . $backupname . "/" . $tablename . "/" . ($page+1) . ".sql";
		if( file_exists($str_file) ) $arr_return["next"] = $page+1;
		return $arr_return;

	}
}