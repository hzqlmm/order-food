<?php
/*
 *
 *
 * 2013-03-24
 */
class tab_other_link {
	static $perms;

	//获取表配置参数
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			$group = cls_config::get("link_group" , "other" , array() );
			$group = array_merge(array("默认"), $group);
			self::$perms = array(
				"group" => $group
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
		if(isset($arr_fields['link_id'])) {
			$arr_fields['id'] = $arr_fields['link_id'];
			unset($arr_fields['link_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " link_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and link_id='" . $arr_return['id'] . "'";
				}
			}
		}
		if(isset($arr_fields["link_url"])) $arr_fields['link_url'] = fun_get::html_url($arr_fields['link_url']);
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {

			//必填项检查
			if(!isset($arr_fields['link_name']) || empty($arr_fields['link_name'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("link_name_is_null");//地区名称
				return $arr_return;
			}
			
			//初始默认值
			if(!isset($arr_fields['link_sort']) || empty($arr_fields['link_sort'])) {
				$obj_rs = $obj_db->get_one("select max(link_sort) as sort from " . cls_config::DB_PRE . "other_link where link_group='" . $arr_fields["link_group"] . "'");
				(!empty($obj_rs))? $arr_fields['link_sort'] = $obj_rs["sort"] + 1 : $arr_fields['link_sort'] = 1;
			}
			//插入到用户表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."other_link",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$where  = "link_sort='" . $arr_fields['link_sort'] . " and link_name='".$arr_fields['link_name'] . "' and link_group='".$arr_fields["link_group"]."'";
					$obj_rs = $obj_db->get_one("select link_id from ".cls_config::DB_PRE."other_link where ".$where);
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['link_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {

			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select link_id from ".cls_config::DB_PRE."other_link where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['link_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "link_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."other_link" , $arr_fields , $where);
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
			(is_numeric($str_id)) ? $arr_where[] = "link_id='".$str_id."'" : $arr_where[] = "link_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
			$arr_id = array();
			$obj_result = $obj_db->select( "select link_id from " . cls_config::DB_PRE . "other_link where " . $where );
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_id[] = $obj_rs["link_id"];
			}
		}
		$where = implode(" and " , $arr_where);
		$arr_return=$obj_db->on_delete(cls_config::DB_PRE."other_link" , $where);
		return $arr_return;
	}
}