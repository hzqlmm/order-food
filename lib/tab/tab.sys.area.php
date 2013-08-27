<?php
/*
 *
 *
 * 2013-03-24
 */
class tab_sys_area {
	static $perms;

	//获取表配置参数
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
			);
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}

	/* 保存操作
	 * arr_fields : 为字段数据，默认如果包函 id，则为修改，否则为插入
	 * where : 默认为空，用于有时候条件修改
	 */
	static function on_save($arr_fields , $where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['area_id'])) {
			$arr_fields['id'] = $arr_fields['area_id'];
			unset($arr_fields['area_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " area_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and area_id='" . $arr_return['id'] . "'";
				}
			}
		}
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {
			//必填项检查
			if(!isset($arr_fields['area_name']) || empty($arr_fields['area_name'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("area_name_is_null");//地区名称
				return $arr_return;
			}
			$val = (isset($arr_fields['area_val']) && !empty($arr_fields['area_val'])) ? $arr_fields['area_val'] : $arr_fields['area_name'];
			$arr_ping = cls_pinyin::get($val , cls_config::DB_CHARSET);
			if(!isset($arr_fields['area_pin']) || empty($arr_fields['area_pin'])) $arr_fields['area_pin'] = $arr_ping["style2"];
			if(!isset($arr_fields['area_jian']) || empty($arr_fields['area_jian'])) $arr_fields['area_jian'] = $arr_ping["style3"];

			//初始默认值
			if(!isset($arr_fields["area_pid"])) $arr_fields["area_pid"] = 0;
			if(!isset($arr_fields['area_sort']) || empty($arr_fields['area_sort'])) {
				$obj_rs = $obj_db->get_one("select max(area_sort) as sort from " . cls_config::DB_PRE . "sys_area where area_pid=" . $arr_fields["area_pid"]);
				(!empty($obj_rs))? $arr_fields['area_sort'] = $obj_rs["sort"] + 1 : $arr_fields['area_sort'] = 1;
			}
			//插入到用户表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."sys_area",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$where  = "area_sort='" . $arr_fields['area_sort'] . " and area_name='".$arr_fields['area_name'] . "' and area_pid='".$arr_fields["area_pid"]."'";
					$obj_rs = $obj_db->get_one("select area_id from ".cls_config::DB_PRE."sys_area where ".$where);
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['area_id'];
				}

				//计算：pids 与 depth
				$arr_update = array();
				if($arr_fields["area_pid"] > 0) {
					$area_childs = 0;
					$obj_rs = $obj_db->get_one("select area_pids,area_childs from ".cls_config::DB_PRE."sys_area where area_id=".$arr_fields["area_pid"]);
					if(!empty($obj_rs) && !empty($obj_rs["area_pids"])) {
						$arr_update["area_pids"] = $obj_rs["area_pids"] . "," . $arr_return["id"];
						$arr_update["area_depth"] = count(explode("," , $arr_update["area_pids"]));
						$area_childs = $obj_rs['area_childs'];
					} else {
						$arr_update["area_pids"] = $arr_return["id"];
						$arr_update["area_depth"] = 1;
					}
					//计算父级子集数量
					$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "sys_area where area_pid='" . $arr_fields['area_pid'] . "'");
					if(!empty($obj_rs) && $area_childs != $obj_rs['num']) {
						$obj_db->on_update(cls_config::DB_PRE."sys_area" , array('area_childs'=>$obj_rs['num']) , "area_id=" . $arr_fields['area_pid']);
					}
				} else {
					$arr_update["area_pids"] = $arr_return['id'];
					$arr_update["area_depth"] = 1;
				}
				$arr = $obj_db->on_update(cls_config::DB_PRE."sys_area" , $arr_update , "area_id=" . $arr_return['id']);

			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {

			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select area_id from ".cls_config::DB_PRE."sys_area where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['area_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "area_id='".$arr_return['id']."'";
			}
			if(isset($arr_fields['area_pin']) && empty($arr_fields['area_pin']) || isset($arr_fields['area_jian']) && empty( $arr_fields['area_jian'])) {
				$val = (isset($arr_fields['area_val']) && !empty($arr_fields['area_val'])) ? $arr_fields['area_val'] : $arr_fields['area_name'];
				$arr_ping = cls_pinyin::get($val , cls_config::DB_CHARSET);
				if(empty($arr_fields['area_pin'])) $arr_fields['area_pin'] = $arr_ping["style2"];
				if(empty($arr_fields['area_jian'])) $arr_fields['area_jian'] = $arr_ping["style3"];
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."sys_area" , $arr_fields , $where);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		}
		return $arr_return;
	}
	/* 删除函数
	 * arr_id : 要删除的 id数组
	 * where : 删除附加条件
	 */
	static function on_delete($arr_id , $where = '') {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if( empty($str_id) && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]="id".cls_language::get("not_null");
			return $arr_return;
		}
		$obj_db = cls_obj::db_w();
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "area_id='".$str_id."'" : $arr_where[] = "area_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
			$arr_id = array();
			$obj_result = $obj_db->select( "select area_id from " . cls_config::DB_PRE . "sys_area where " . $where );
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_id[] = $obj_rs["area_id"];
			}
		}
		//删除子项
		if(is_array($arr_id)) {
			foreach($arr_id as $item) {
				$arr = $obj_db->on_delete(cls_config::DB_PRE."sys_area" , $obj_db->concat(",",'area_pids',",") . " like '%," . $item . ",%'");
			}
		} else {
			$arr = $obj_db->on_delete(cls_config::DB_PRE."sys_area" , $obj_db->concat(",",'area_pids',",") . " like '%," . $arr_id . ",%'");
		}
		$where = implode(" and " , $arr_where);
		$arr_return=$obj_db->on_delete(cls_config::DB_PRE."sys_area" , $where);
		return $arr_return;
	}
}