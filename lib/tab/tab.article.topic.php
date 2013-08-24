<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_article_topic {
	static $perms;
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"state" => array( cls_language::get("normal") => 1 , cls_language::get("wait_check") => 0 , cls_language::get("close") => -1) ,
			);
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}
	static function on_save($arr_fields , $where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		$obj_db = cls_obj::db_w();
		if(isset($arr_fields['topic_id'])) {
			$arr_fields['id'] = $arr_fields['topic_id'];
			unset($arr_fields['topic_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " topic_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and topic_id='" . $arr_return['id'] . "'";
				}
			}
		}

		if( empty($where) ) {

			//必填项检查
			if(!isset($arr_fields['topic_name']) || empty($arr_fields['topic_name'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("topic_name_null" , "article");//用户组名不能为空
				return $arr_return;
			}
			
			//初始默认值
			$arr_fields['topic_addtime'] = $arr_fields['topic_updatetime'] = TIME;
			if(!isset($arr_fields['topic_sort']) || empty($arr_fields['topic_sort'])) {
				$obj_rs = $obj_db->get_one("select max(topic_sort) as sort from " . cls_config::DB_PRE . "article_topic");
				(!empty($obj_rs))? $arr_fields['topic_sort'] = $obj_rs["sort"] + 1 : $arr_fields['topic_sort'] = 1;
			}
			//插入到用户表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."article_topic",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$where  = "topic_sort='" . $arr_fields['topic_sort'] . " and topic_addtime=" . $arr_fields['topic_addtime'] . " and topic_name='".$arr_fields['topic_name'] . "'";
					$obj_rs = $obj_db->get_one("select topic_id from ".cls_config::DB_PRE."article_topic where ".$where);
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['topic_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {

			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select topic_id from ".cls_config::DB_PRE."article_topic where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['topic_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "topic_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."article_topic" , $arr_fields , $where);
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
			$arr_return["msg"]=cls_language::get("not_where");
			return $arr_return;
		}
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "topic_id='".$str_id."'" : $arr_where[] = "topic_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);

		$arr_return=cls_obj::db_w()->on_delete(cls_config::DB_PRE."article_topic" , $where);
		return $arr_return;
	}
}