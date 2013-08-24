<?php
class interface_user {
	function __construct($uid) {
	}
	//注册
	function on_reg( $arr_msg = array() ) {
		$arr_return = array("code" => 0 , "msg" => "");
		$arr_fields = array();
		if(isset($arr_msg['user_name'])) $arr_fields['user_name'] = $arr_msg['user_name'];
		if(isset($arr_msg['user_pwd'])) $arr_fields['user_pwd'] = $arr_msg['user_pwd'];
		//必填项检查
		if(!isset($arr_fields['user_name']) || !fun_is::uname($arr_fields['user_name'])) {
			$arr_return['code'] = 113;
			$arr_return['msg']  = fun_get::rule_uname("tips");//账号为空或格式不对
			return $arr_return;
		}
		if(!isset($arr_fields['user_pwd']) || !fun_is::pwd($arr_fields['user_pwd'])) {
			$arr_return['code'] = 113;
			$arr_return['msg']  = fun_get::rule_pwd("tips");//密码为空或格式不对
			return $arr_return;
		}

		//是否为分站登录
		if(isset($GLOBALS['sites']) && !empty($GLOBALS['sites']['id'])) {
			$arr_fields["user_name"] = "shop" . $GLOBALS['sites']['id'] . "_" . $arr_fields["user_name"];
		}

		$obj_db = cls_obj::db_w();
		//唯一性检查
		$where = "user_name='".$arr_fields['user_name']."'";
		$obj_rs = $obj_db->get_one("select count(1) as num from ".cls_config::DB_PRE."user where ".$where);
		if($obj_rs['num']>0) {
			$arr_return['code'] = 114;
			$arr_return['msg']  = cls_language::get("repeat_uname");//注册账号已在在
			return $arr_return;
		}
		//生成密码
		$arr_fields['user_pwd_key'] = rand(1000,9999);//返回四位数加密字符
		$arr_fields["user_pwd"] = fun_format::pwd($arr_fields['user_pwd'],$arr_fields['user_pwd_key']);//加密

		//初始必要值
		$arr_fields['user_addtime'] = TIME;
		//插入到用户表
		$arr = $obj_db->on_insert(cls_config::DB_PRE."user",$arr_fields);
		if($arr['code'] == 0) {
			$arr_return['id'] = $obj_db->insert_id();
			//其它非mysql数据库不支持insert_id 时
			if(empty($arr_return['id'])) {
				$obj_rs = $obj_db->get_one("select user_id from ".cls_config::DB_PRE."user where ".$where);
				if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['user_id'];
			}

			//注册本站用户信息
			tab_sys_user::on_insert(array('user_id' => $arr_return['id'] , 'user_netname' => $arr_msg['user_name'] ));
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = cls_language::get("db_edit");
		}
		return $arr_return;
	}

	/** 用户登录入口
	 *	arr_fields : 数组，包括：uname , pwd 等
	 *  no_verify  : 为1时，用arr_fields 直接包函 uid 登录 如：整合登录时用到
	 *               为2时，如果用户不存在，直接注册
	 * 返回 sys_user 表信息
	 */
	function on_login( $arr_fields = array() , $no_verify = 0) {
		$arr_return = array("code" => 0 , "msg" => "");
		$str_where = "";//登录查询条件

		//是否为分站登录
		if(isset($GLOBALS['sites']) && !empty($GLOBALS['sites']['id']) && $arr_fields["user_name"]) {
			$uname_old = $arr_fields["user_name"];
			$arr_fields["user_name"] = "shop" . $GLOBALS['sites']['id'] . "_" . $arr_fields["user_name"];
		}
		$obj_db = cls_obj::db_w();
		if($no_verify == 1) {
			if(isset($arr_fields["user_id"]) && is_numeric($arr_fields["user_id"])) {
				$str_where = " where user_id='" . (int)$arr_fields["user_id"] . "'";
			} else if(isset($arr_fields["user_name"])){
				$str_where = " where user_name='" . $arr_fields["user_name"] . "'";
			} else {
				$arr_return["code"] = 7;
				$arr_return["msg"] = cls_language::get("login_perms_err");
				return $arr_return;
			}
		} else {
			if(!isset($arr_fields["user_name"]) || !isset($arr_fields["user_pwd"])) {
				$arr_return["code"] = 7;
				$arr_return["msg"] = cls_language::get("login_perms_err");
				return $arr_return;
			}
			$str_where = " where user_name='" . $arr_fields["user_name"] . "'";
		}
		//查询用户
		$str_sql = "select user_id,user_name,user_pwd_key,user_pwd from ".cls_config::DB_PRE."user".$str_where;
		$arr_user=$obj_db->get_one($str_sql);
		if(empty($arr_user)) {
			if($no_verify == 2) {//自动注册
				if(isset($uname_old)) $arr_fields['user_name'] = $uname_old;
				$arr_msg=self::on_reg( array('user_name'=>$arr_fields['user_name'] , 'user_pwd'=>$arr_fields['user_pwd']) );
				if($arr_msg["code"]==0){
					$arr_user=$obj_db->get_one($str_sql);
					if(empty($arr_user)) return array("code" => 4 , "msg" => "用户不存在");
				}else{
					$arr_return["code"] = 4;
					$arr_return["msg"] = cls_language::get("login_no_user");
					return $arr_return;
				}
			} else {
				$arr_return["code"] = 4;
				$arr_return["msg"] = cls_language::get("login_no_user");
				return $arr_return;
			}
		}
		$sql = "select user_id,user_type,user_group_id,user_depart_id,user_netname,user_logintime,user_regtime,user_loginerr,user_state,user_continuenum,user_loginnum,user_verify_tel,user_verify_email from " . cls_config::DB_PRE . "sys_user where user_id='" . $arr_user['user_id'] . "'";
		$obj_sys_user = $obj_db->get_one($sql);
		if(empty($obj_sys_user)) {
			//如果用户信息不存在，则自动添加
			tab_sys_user::on_insert(array('user_id' => $arr_user['user_id'] , 'user_netname' => $arr_user['user_name'] ));
			$obj_sys_user = $obj_db->get_one($sql);
			if(empty($obj_sys_user)) return array('code' => 500 , 'msg' => '登录失败');
		}
		$obj_sys_user['user_name'] = $arr_user['user_name'];
		if($no_verify == 0) {//验证密码
			$lng_errtime = $lng_errnum = 0;
			$arr_login_verify = cls_config::get('login_verify','user');//验证登录配置
			if(!empty($arr_login_verify)) {
				( isset($arr_login_verify["stop_time"]) )? $lng_stop_time = intval($arr_login_verify["stop_time"]) : $lng_stop_time = 15;
				$arr_unit = array("d"=>60*60*24,"h"=>60*60,"i"=>60,"s"=>1);
				$arr_unit_name = array("d" => cls_language::get("day") , "h" => cls_language::get("hour") , "i" => cls_language::get("minute") , "s" => cls_language::get("second")) ;
				$stt_unit_name = $lng_stop_time . cls_language::get("minute");
				if( isset($arr_login_verify["stop_unit"]) && isset($arr_unit[$arr_login_verify["stop_unit"]])) {
					if( isset($arr_unit_name[$arr_login_verify["stop_unit"]]) ) $str_unit_name = $lng_stop_time . $arr_unit_name[$arr_login_verify["stop_unit"]];
					$lng_stop_time = $lng_stop_time * $arr_unit[$arr_login_verify["stop_unit"]];
				} else {
					$lng_stop_time = 60 * $lng_stop_time;
				}
				//取配置信息
				if(!empty($obj_sys_user["user_loginerr"])) {
					$arr_loginerr = explode(",",$obj_sys_user["user_loginerr"]);//验证当天登录错误记录
					$lng_errtime = intval($arr_loginerr[0]);
					if(TIME-$lng_errtime < $lng_stop_time){
						$lng_errnum = intval($arr_loginerr[1]);
					}else{
						$lng_errtime=0;
					}
				}
				$is_login_verifycode = cls_session::get_cookie("login_verifycode");
				if($is_login_verifycode) {//一定错误次数后，需要验证码
					if(cls_verifycode::on_verify($arr_fields["verifycode"]) == false) {
						$arr_return["code"] = 11;
						$arr_return["msg"]  = cls_language::get("verify_code_err");
						//cls_session::set_cookie("login_verifycode" , "1");
						return $arr_return;
					}
				}
				if(isset($arr_login_verify["stop_num"]) && $lng_errnum>=intval($arr_login_verify["stop_num"])) {//超出一定错误后，禁止登录
					$arr_return["code"] = 6;
					$arr_return["msg"]   = sprintf(cls_language::get("login_stop_num") , $str_unit_name);
					return $arr_return;
				}
			}
			$str_pwd = fun_format::pwd($arr_fields['user_pwd'],$arr_user['user_pwd_key']);
			if($str_pwd != $arr_user["user_pwd"]) {//登录密码错误
				$arr_return["code"] = 3;
				$arr_return["msg"] = cls_language::get("login_pwd_err");
				//设置登录错误信息
				$lng_errnum++;
				if(intval($arr_login_verify["show_code"])<=$lng_errnum) {
					cls_session::set_cookie("login_verifycode" , "1");//设置需要验证码
					$arr_return["show_code"] = 1;
				}
				$obj_db->on_exe("update ".cls_config::DB_PRE."sys_user set user_loginerr='".TIME.",".$lng_errnum."' where user_id=".$arr_user["user_id"]);
				return $arr_return;
			}
		}
		return array('code' => 0 , 'userinfo' => $obj_sys_user);
	}

	/* 
	 * 取用户信息
	 * arr 为用户名或id 数组
	 * 返回：arr['id_'.id] = array();
	 */
	function get_user( $ids , $isuid = true) {
		if(empty($ids)) return array();
		$obj_db = cls_obj::db();
		if(is_array($ids)) {
			if($isuid) {
				$ids = fun_format::arr_id($ids);
				if(empty($ids)) return array();
				$where = " user_id in(" . $ids . ")" ;
			} else {
				$where = " user_name in('" . implode("','" , $ids ) . "')";
			}
		} else {
			$where = ($isuid) ? "user_id='" . $ids . "'" : "user_name='" . $ids . "'";
		}
		$arr = array();
		$obj_result = $obj_db->select("select user_id,user_name from " . cls_config::DB_PRE .  "user where " . $where);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr[$obj_rs['user_name']] = $obj_rs['user_id'];
		}
		return $arr;
	}
	
	/** 修改密码
	 * uid 为被改密码用户id , oldpwd 为原密码 , newpwd 为新密码 , isverify 是否需要验证旧密码
	 */
	function on_update_pwd($oldpwd , $newpwd , $uid = 0 , $isverify = true ) {
		$arr_return=array("code" => 0 , "msg" => '');
		if( !fun_is::pwd($newpwd) ) {
			$arr_return['code'] = 113;
			$arr_return['msg']  = fun_get::rule_pwd("tips");//密码格式不对
			return $arr_return;
		}
		//检查
		$obj_rs=cls_obj::db()->get_one("select user_pwd,user_pwd_key from " . cls_config::DB_PRE . "user where user_id='" . $uid . "'");
		if(!empty($obj_rs))	{
			$oldpwd = fun_format::pwd($oldpwd,$obj_rs['user_pwd_key']);
			$newpwd = fun_format::pwd($newpwd,$obj_rs['user_pwd_key']);
			if($isverify && $obj_rs["user_pwd"]!=$oldpwd){
				$arr_return['code'] = 500;
				$arr_return['msg']  = cls_language::get("old_pwd_err");//登录密码不对
				return $arr_return;
			}
		} else {
			$arr_return['code'] = 500;
			$arr_return['msg']  = cls_language::get("session_user_err");//用户不存在
			return $arr_return;
		}
		$arr_return=cls_obj::db_w()->on_update(cls_config::DB_PRE . "user" , array("user_pwd" => $newpwd) , "user_id=".$uid);
		return $arr_return;
	}

	function delete_user( $ids , $isuid = true) {
		if(empty($ids)) return array('code'=>0,'msg'>'');
		$obj_db = cls_obj::db_w();
		if(is_array($ids)) {
			if($isuid) {
				$ids = fun_format::arr_id($ids);
				if(empty($ids)) return array();
				$where = " user_id in(" . $ids . ")" ;
			} else {
				$where = " user_name in('" . implode("','" , $ids ) . "')";
			}
		} else {
			$where = ($isuid) ? "user_id='" . $ids . "'" : "user_name='" . $ids . "'";
		}
		$arr = $obj_db->on_exe("delete from " . cls_config::DB_PRE .  "user where " . $where);
		return $arr;
	}
	//退出
	function on_loginout() {
	}

}