<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_sys_config {
	static function get_module() {
		//定义配置分模块
		$module = cls_config::get("config_module","sys");
		return $module;
	}
	static function on_save($arr_fields,$where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) {
				if( empty($where) ){
					$where = " config_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and config_id='" . $arr_return['id'] . "'";
				}
			}
		}

		if( isset($arr_fields['config_module']) ) {
			//检测模块是否有效
			$arr_module = self::get_module();
			if( !isset($arr_module[$arr_fields['config_module']]) ) {
				$arr_return['code'] = 115;
				$arr_return['msg']  = cls_language::get("no_config_module",'sys');//模块不在在
				return $arr_return;
			}
		}
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {
			//必填项检查
			if(!isset($arr_fields['config_module']) || empty($arr_fields['config_module'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("config_module",'sys') . cls_language::get("not_null");//所属组不能为空
				return $arr_return;
			}
			if(!isset($arr_fields['config_name']) || empty($arr_fields['config_name'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("name") . cls_language::get("not_null");//名称不能为空
				return $arr_return;
			}
			$arr_fields['config_name'] = trim($arr_fields['config_name']);
			//唯一性检查
			$where = "config_module='".$arr_fields['config_module']."' and config_name='".$arr_fields['config_name']."'";
			$obj_rs = $obj_db->get_one("select count(1) as num from ".cls_config::DB_PRE."sys_config where ".$where);
			if($obj_rs['num']>0) {
				$arr_return['code'] = 114;
				$arr_return['msg']  = cls_language::get("sys_config_repeat",'sys');//数据已经在在
				return $arr_return;
			}
			//添加
			$arr = $obj_db->on_insert(cls_config::DB_PRE."sys_config",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$obj_rs = $obj_db->get_one("select config_id from ".cls_config::DB_PRE."sys_config where ".$where);
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['config_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {
			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select config_id from ".cls_config::DB_PRE."sys_config where ".$where);
				if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['config_id'];
				$where = "config_id='".$arr_return['id']."'";
			}
			//唯一性检查
			if( isset($arr_fields['config_module']) && isset($arr_fields['config_name']) ) {
				$obj_rs = $obj_db->get_one("select config_id from ".cls_config::DB_PRE."sys_config where config_module='".$arr_fields['config_module']."' and config_name='".$arr_fields['config_name']."' and config_id!='".$arr_return['id']."'");
				if(!empty($obj_rs)) {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("sys_config_repeat",'sys');//数据已经在在
					return $arr_return;
				}
			} else if( isset($arr_fields['config_name']) ) {
				$obj_rs = $obj_db->get_one("select count(1) as num from ".cls_config::DB_PRE."sys_config where config_module in(select config_module from ".cls_config::DB_PRE."sys_config where config_id='".$arr_return['id']."') and config_name='".$arr_fields['config_name']."' and config_id!='".$arr_return['id']."'");
				if($obj_rs['num']>0) {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("sys_config_repeat",'sys');//数据已经在在
					return $arr_return;
				}
			}

			if(isset($arr_fields["config_val"])) $arr_fields["config_val"] = str_replace("&quot;" , '"' , $arr_fields["config_val"]);
			//修改
			$arr = $obj_db->on_update(cls_config::DB_PRE."sys_config" , $arr_fields , $where);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		}
		if($arr_return["code"]==0) self::on_refresh();
		return $arr_return;
	}
	function on_saveall( $arr_all = array() ) {
		$arr_return = array("code"=>0,"msg"=>"");
		$obj_db = cls_obj::db_w();
		$arr_err = array();
		$lng_ok = 0;
		foreach($arr_all as $item) {
			$arr_fields = $item;
			(!isset($arr_fields["config_id"])) ? $arr_fields["config_id"] = 0 : $arr_fields["config_id"] = (int)$arr_fields["config_id"];
			$lng_id=$arr_fields["config_id"];
			unset($arr_fields["config_id"]);//释放id
			if(is_array($arr_fields["config_val"])) $arr_fields["config_val"]=serialize($arr_fields["config_val"]);
			if($lng_id>1){
				$str_where="config_id='".$lng_id."'";
				$arr = $obj_db->on_update(cls_config::DB_PRE."sys_config" , $arr_fields , $str_where);
				if($arr['code'] != 0) {
					$arr_err[] = $arr['msg'];
				} else {
					$lng_ok++;
				}
			}
		}
		if( count($arr_err) > 0 ) {
			$arr_return['code'] = 116;
			$arr_return['msg'] = cls_language::get("db_edit");
			if( $lng_ok > 0 ) $arr_return['msg'] = cls_language::get('a_part') . $arr_return['msg'];
		}
		self::on_refresh();
		return $arr_return;
	}
	function on_refresh()
	{
		$obj_db = cls_obj::db();
		$arr_config = array();
		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."sys_config order by config_module,config_id");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr = array();
			if($obj_rs["config_type"]=="array"){
				$arr_config[$obj_rs["config_module"]][$obj_rs["config_name"]]=self::get_array($obj_rs["config_val"]);
			}else if( $obj_rs["config_type"] == "bool" || $obj_rs["config_type"] == "int" ) {
				$arr_config[$obj_rs["config_module"]][$obj_rs["config_name"]]=(float)$obj_rs["config_val"];
			}else if( $obj_rs["config_type"] == "chk" ){
				if(fun_is::serialized($obj_rs["config_val"])) {
					$arr_config[$obj_rs["config_module"]][$obj_rs["config_name"]]=unserialize($obj_rs["config_val"]);
				} else {
					$arr_config[$obj_rs["config_module"]][$obj_rs["config_name"]]=array();
				}
			} else {
				//$obj_rs["config_val"]=str_replace("'","\'",$obj_rs["config_val"]);
				$arr_config[$obj_rs["config_module"]][$obj_rs["config_name"]]=$obj_rs["config_val"];
			}
		}
		foreach($arr_config as $item=>$key) {
			$val=var_export($key,true);
			$val = '<'.'?php'.chr(10).'return '.$val.";";
			fun_file::file_create(KJ_DIR_DATA."/config/cfg/cfg.".$item.".php",$val,1);
		}
	}

	function get_array($msgval) {
		$msgval = str_replace(chr(10),chr(13),$msgval);
		$msgval = str_replace(chr(13).chr(13),chr(13),$msgval);
		$arr_val = explode(chr(13),$msgval);
		$arr_return = array();
		foreach($arr_val as $item) {
			if( trim($item)=="" ) continue;
			$arr_x=explode("=&gt;",$item);
			if( count($arr_x)==1 ) $arr_x[1]=$arr_x[0];
			$arr_return[$arr_x[0]]=$arr_x[1];
		}
		return $arr_return;
	}

	/* 删除函数
	 * arr_id : 要删除的 id数组
	 * where : 删除附加条件
	 */
	function on_delete($arr_id , $where = '') {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if( $str_id == "" && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]=cls_language::get("not_where");
			return $arr_return;
		}
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "config_id='".$str_id."'" : $arr_where[] = "config_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);

		$arr_return=cls_obj::db_w()->on_delete(cls_config::DB_PRE."sys_config" , $where);

		return $arr_return;
	}

}