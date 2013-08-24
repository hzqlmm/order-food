<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_sys_verify {
	static $perms;

	//获取表配置参数
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"type" => array("邮箱找回密码" => 1 , "短信找回密码" => 2),
				"state" => array("未验证" => 0 , "验证成功" => 1),
			);
			if(fun_is::com("email")) {
				if(cls_config::get("rule_uname" , "user") == 'email') {
					self::$perms['type']['邮箱注册'] = 3;
				}
				self::$perms['type']['邮箱找回密码'] = 1;
			}
			if(fun_is::com("sms")) {
				if(cls_config::get("rule_uname" , "user") == 'mobile') {
					self::$perms['type']['短信注册'] = 4;
				}
				self::$perms['type']['短信找回密码'] = 2;
			}
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}
	//生成唯一认证关键字符
	static function get_key($uid , $type) {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		$obj_db = cls_obj::db_w();
		$arr_fields = array('verify_type'=>$type , "verify_user_id" => $uid);
		$arr_fields["verify_time"] = date("Y-m-d H:i:s",TIME);
		if(empty($arr_fields['verify_user_id'])) $arr_fields['verify_user_id'] = cls_obj::get("cls_user")->uid;
		if($arr_fields["verify_type"] == 1) {
			//邮件认证字符串
			$arr_return['key'] = $arr_fields['verify_key'] = md5($arr_fields['verify_user_id'].$arr_fields["verify_time"].rand(10000,99999));
		} else if( $arr_fields["verify_type"] == 2) {
			//短信认证字符
			$arr_return['key'] = $arr_fields['verify_key'] = rand(10000,99999);
		} else if( $arr_fields["verify_type"] == 3) {
			//邮箱注册
			$arr_return['key'] = $arr_fields['verify_key'] = dechex(rand(100000,999999));
		} else if( $arr_fields["verify_type"] == 4) {
			//短信注册
			$arr_return['key'] = $arr_fields['verify_key'] = dechex(rand(1000,9999));
		} else {
			return array("code" => 500 , "msg" => "认证类型不存在");
		}

		//插入到表
		$arr = $obj_db->on_insert(cls_config::DB_PRE."sys_verify",$arr_fields);
		if($arr['code'] == 0) {
			$arr_return['id'] = $obj_db->insert_id();
			//其它非mysql数据库不支持insert_id 时
			if(empty($arr_return['id'])) {
				$where  = "verify_user_id='" . $arr_fields['verify_user_id'] . " and verify_type='".$arr_fields['verify_type'] . "' and verify_time='".$arr_fields["verify_time"]."'";
				$obj_rs = $obj_db->get_one("select verify_id from ".cls_config::DB_PRE."sys_verify where ".$where);
				if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['verify_id'];
			}
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = cls_language::get("db_edit");
		}
		return $arr_return;
	}

	static function on_verify($key , $uid = 0 , $type = 0 , $is_verify = true) {
		$where = " where verify_key='" . $key . "'";
		if(!empty($type)) $where .= " and verify_type=" . $type;
		if(!empty($uid)) {
			$where .= " and verify_user_id='" . $uid . "'";
		} else {
			//未指定用户id，十分钟超时
			$where .= " and verify_time>'" . date("Y-m-d H:i:s" , TIME-600) . "'";
		}
		$obj_rs = cls_obj::db()->get_one("select verify_user_id,verify_time,verify_state,verify_type from " . cls_config::DB_PRE . "sys_verify " . $where);
		if(empty($obj_rs)) return array("code"=>500 , "msg"=>"验证失败");
		if($is_verify) {
			//已认证过
			if( $obj_rs['verify_state'] != 0 ) {
				 return array("code"=>500 , "msg"=>"验证无效");
			}
			//短信10分钟算过期
			if( $obj_rs["verify_type"] == 2 && $obj_rs['verify_time'] < date("Y-m-d H:i:s" , TIME-600) ) {
				 return array("code"=>500 , "msg"=>"验证超时");
			}
			//邮件超一天算过期
			if( $obj_rs["verify_type"] == 1 && $obj_rs['verify_time'] < date("Y-m-d H:i:s" , strtotime('-1 day',TIME) ) ) {
				 return array("code"=>500 , "msg"=>"验证已过期");
			}
			cls_obj::db_w()->on_exe("update " . cls_config::DB_PRE . "sys_verify set verify_state=1,verify_retime='" . date("Y-m-d H:i:s") . "'" . $where);
		}
		return array("code"=>0,"msg"=>'',"uid"=>$obj_rs['verify_user_id']);
	}
}