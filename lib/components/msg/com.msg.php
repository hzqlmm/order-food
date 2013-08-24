<?php
/**
 * 
 */
class com_msg {
	static $perms;

	//获取表配置参数
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"type" => array("在线留言" => 0 , "密码申诉" => 1),
			);
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}
	static function on_save($arr_fields) {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['msg_id'])) {
			$arr_fields['id'] = $arr_fields['msg_id'];
			unset($arr_fields['msg_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " msg_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and msg_id='" . $arr_return['id'] . "'";
				}
			}
		}
		if(!empty($arr_fields['msg_tel']) && !fun_is::tel($arr_fields['msg_tel'])) return array("code" => 500 , "msg" =>"留言电话格式不正确");
		if(!empty($arr_fields['msg_email']) && !fun_is::email($arr_fields['msg_email'])) return array("code" => 500 , "msg" =>"邮箱格式不正确");
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {
			//必填项检查
			if(!isset($arr_fields['msg_cont']) || empty($arr_fields['msg_cont'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = "内容不能为空";
				return $arr_return;
			}
			$arr_fields["msg_time"] = date("Y-m-d H:i:s",TIME);
			if(!isset($arr_fields["msg_ip"]) || empty($arr_fields["msg_ip"])) $arr_fields["msg_ip"] = fun_get::ip();
			if(!isset($arr_fields['msg_user_id'])) $arr_fields['msg_user_id'] = cls_obj::get("cls_user")->uid;
			//插入到表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."other_msg",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$where  = "msg_user_id='" . $arr_fields['msg_user_id'] . " and msg_name='".$arr_fields['msg_name'] . "' and msg_time='".$arr_fields["msg_time"]."'";
					$obj_rs = $obj_db->get_one("select msg_id from ".cls_config::DB_PRE."other_msg where ".$where);
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['msg_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {

			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select msg_id from ".cls_config::DB_PRE."other_msg where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['msg_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "msg_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."other_msg" , $arr_fields , $where);
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
			(is_numeric($str_id)) ? $arr_where[] = "msg_id='".$str_id."'" : $arr_where[] = "msg_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);
		$arr_return=$obj_db->on_delete(cls_config::DB_PRE."other_msg" , $where);
		return $arr_return;
	}

}
?>