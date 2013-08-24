<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class cls_update {
	/* 同频表结构
	 * arr_table : 新数据表结构
	 */
	function table($arr_table = array()) {
		$this_table = self::get_tables();
		$arr_add_table = $arr_add_fields = $arr_update_fields = array();
		$arr_primary_key = $arr_union_key = $arr_key = array();
		$obj_db = cls_obj::db_w();
		foreach($arr_table as $table => $item) {
			if(!isset($this_table[$table])) {
				$arr_add_table[] = $arr_table[$table];
				continue;
			}
			//类型不一至，则同步
			if($item['engine'] != $this_table[$table]['engine']) {
				$obj_db->on_exe("alter table " . cls_config::DB_PRE . $table . " engine " . $this_table[$table]['engine'] . ";");
			}
			foreach( $item['fields'] as $fields => $fields_val ) {
				if(!isset($this_table[$table]['fields'][$fields])) {
					$arr_add_fields[$table][] = $item['fields'][$fields];
					continue;
				}
				if($this_table[$table]['fields'][$fields] != $fields_val) {
					$arr_update_fields[$table][] = $item['fields'][$fields];
					continue;
				}
			}
			foreach( $item['primary_key'] as $key ) {
				if(!in_array($key , $this_table[$table]['primary_key'])) $arr_primary_key[$table] = $key;
			}
			foreach( $item['union_key'] as $key ) {
				if(!in_array($key , $this_table[$table]['union_key'])) $arr_union_key[$table] = $key;
			}
			foreach( $item['key'] as $key ) {
				if(!in_array($key , $this_table[$table]['key'])) $arr_key[$table] = $key;
			}
		}
		//添加表
		if(count($arr_add_table) > 0) {
			$arr = self::on_create_table($arr_add_table);
		}
		//添加字段
		if(count($arr_add_fields) > 0) {
			$arr = self::on_add_fields($arr_add_fields);
		}
		//修改字段
		if(count($arr_update_fields) > 0) {
			$arr = self::on_change_fields($arr_update_fields);
		}
		//添加索引
		if(count($arr_primary_key) > 0) {
			$arr = self::on_add_index($arr_primary_key , 'primary key');
		}
		//添加索引
		if(count($arr_union_key) > 0) {
			$arr = self::on_add_index($arr_union_key , 'union key');
		}
		//添加索引
		if(count($arr_key) > 0) {
			$arr = self::on_add_index($arr_key , 'key');
		}
	}
	/* 获取表结构
	 * 
	 */
	function get_tables() {
		$arr_table = array();
		$obj_db = cls_obj::db();
		$obj_result = $obj_db->query("show table status");
		$pre = strtolower(cls_config::DB_PRE);
		$len = strlen($pre);
		while ($obj_rs = $obj_db->fetch_array($obj_result) ) {
			if(strtolower(substr($obj_rs['Name'],0,$len)) != $pre ) continue;
			$arr_charset = explode("_" , $obj_rs["Collation"] );
			$charset = $arr_charset[0];
			$name = substr($obj_rs['Name'],$len);
			$arr_tableinfo = self::get_table_info($obj_rs['Name']);
			$arr_table[$name] = array_merge($arr_tableinfo , array('name' => $name ,'engine' => strtolower($obj_rs['Engine']) ,'charset' => $charset));
		}
		return $arr_table;
	}

	function get_table_info($tablename) {
		$arr_return = array("code" => 0);
		$obj_db = cls_obj::db();
		$result = $obj_db->query("SHOW FULL COLUMNS FROM ".$tablename);
		$arr_primary_key = array();
		$arr_union_key = $arr_key = $arr_fields = array();
		while( $obj_rs = $obj_db->fetch_array($result) ) {
			$str_comment = '';
			(strtolower($obj_rs["Null"])=="yes" && !is_null($obj_rs['Default'])) ? $str_default = " default '" . $obj_rs['Default'] . "'" : $str_default = " NOT NULL";
			if(!empty($obj_rs["Comment"])) $str_comment = " COMMENT '" . $obj_rs["Comment"] . "'";
			$str_extra="";
			if($obj_rs["Extra"] != "") $str_extra= $obj_rs["Extra"];
			if($obj_rs["Key"] != "") {
				if( $obj_rs["Key"] == "PRI" ) {
					$arr_primary_key[] = $obj_rs["Field"];
				} else if($obj_rs["Key"] == "UNI") {
					$arr_union_key[] = $obj_rs["Field"];
				} else {
					$arr_key[] = $obj_rs["Field"];
				}
			}
			$arr_fields[$obj_rs["Field"]] = "`" . $obj_rs["Field"] . "` " . $obj_rs["Type"] . $str_default . " " . $str_extra . $str_comment;
		}

		return array(
			'fields' => $arr_fields,
			'primary_key' => $arr_primary_key,
			'union_key' => $arr_union_key,
			'key' => $arr_key
		);

	}
	/* 创建表
	 *
	 */
	function on_create_table($arr_table) {
		$str_sql = '';
		$arr_sql = array();
		foreach($arr_table as $arr_fields) {
			$str_newtable = cls_config::DB_PRE . $arr_fields['name'];
			$str_fields = implode("," , $arr_fields['fields']);
			$arr_key = array();
			if( count($arr_fields['primary_key']) > 0 ){
				$arr_key[] = "PRIMARY KEY (`" . implode("`,`" , $arr_fields['primary_key']) . "`)";
			}
			if( count($arr_fields['primary_key']) > 0 ){
				$arr = array();
				foreach($arr_fields['primary_key'] as $item) {
					$arr[] = "UNIQUE KEY `" . $item . "` (`" . $item . "`)";
				}
				$arr_key[] = implode(",",$arr);
			}
			if( count($arr_fields['key']) > 0 ){
				$arr = array();
				foreach($arr_fields['key'] as $item) {
					$arr[] = "KEY `" . $item . "` (`" . $item . "`)";
				}
				$arr_key[] = implode(",",$arr);
			}

			$str_table_key = implode(",",$arr_key);
			if(!empty($str_table_key)) $str_table_key = "," . $str_table_key;

			cls_obj::db_w()->on_exe("CREATE TABLE IF NOT EXISTS `" . $str_newtable . "` (" . $str_fields  . $str_table_key . ") ENGINE=" . $arr_fields['engine'] . "  DEFAULT CHARSET=" . $arr_fields['charset']);
		}
	}
	/* 添加字段
	 *
	 */
	function on_add_fields($arr) {
		$obj_db = cls_obj::db_w();
		foreach($arr as $table => $fields) {
			$str_fields = " add " . implode(",add " , $fields);
			$arr = $obj_db->on_exe("ALTER TABLE " . cls_config::DB_PRE . $table . $str_fields);
		}
	}
	/* 修改字段
	 *
	 */
	function on_change_fields($arr) {
		$obj_db = cls_obj::db_w();
		foreach($arr as $table => $fields) {
			$str_fields = " modify " . implode(",modify " , $fields);
			$arr = $obj_db->on_exe("ALTER TABLE " . cls_config::DB_PRE . $table . $str_fields);
		}
	}
	function on_add_index($arr , $type) {
		return;
		$obj_db = cls_obj::db_w();
		foreach($arr as $table => $fields) {
			if($type == 'primary key') {
				$str_fields = " add primary key(`" . implode("`,`", $fields) . "`)";
			} else {
				$str_fields = " add " . $type . "(`" . implode("`,`" . $type, $fields) . "`)";
			}
			$arr = $obj_db->on_exe("ALTER TABLE " . cls_config::DB_PRE . $table . $str_fields);
		}
	}

	/* 同步配置文件
	 *
	 */
	static function config($arr_config) {
		$this_config = self::get_config();
		$is_refresh = false;
		foreach($arr_config as $key => $item) {
			if(!isset($this_config[$key])) {
				tab_sys_config::on_save($item);
				$is_refresh = true;
				continue;
			}
			if($item['config_type'] != $this_config[$key]['config_type'] || $item['config_list'] != $this_config[$key]['config_list']) {
				$is_refresh = true;
				tab_sys_config::on_save($item , "config_module='" . $item['config_module'] . "' and config_name='" . $item['config_name'] . "'");
			}
		}
		if($is_refresh) tab_sys_config::on_refresh();
	}

	/* 取当前配置
	 *
	 */
	static function get_config() {
		$obj_db = cls_obj::db();
		$arr = array();
		$obj_result = $obj_db->select("select config_name,config_val,config_intro,config_readonly,config_list,config_type,config_module,config_sort,config_env from " . cls_config::DB_PRE . "sys_config order by config_id");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$key = $obj_rs["config_module"] . ":" . $obj_rs["config_name"];
			$arr[$key] = $obj_rs;
		}
		return $arr;
	}
}