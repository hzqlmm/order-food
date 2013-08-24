<?php

require_once KJ_DIR_DATA . '/config/cfg.uc.php';
require_once KJ_DIR_LIB . '/components/uc_client/client.php';

class interface_user {
    function __construct($uid) {
		if( cls_session::get_cookie("syninfo") == '1' ) {
			self::on_synlogin($uid);
		}
		if( cls_session::get_cookie("syninfo") == '2' ) {
			self::on_synlogout(true);
		}
	}
	//注册
	/*
	 * 大于0:返回用户 ID，表示用户注册成功
	 * -1:用户名不合法
	 * -2:包含不允许注册的词语
	 * -3:用户名已经存在
	 * -4:Email 格式有误
	 * -5:Email 不允许注册
	 * -6:该 Email 已经被注册
	 */
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
		$arr_fields['user_email'] = isset($arr_msg['user_email']) ? $arr_msg['user_email'] : '';
		//同步uc注册
		$uid = uc_user_register($arr_fields['user_name'],$arr_fields['user_pwd'],$arr_fields['user_email']);
		if($uid > 0) {
			$arr_return['id'] = $uid;
			//注册本站用户信息
			tab_sys_user::on_insert(array('user_id' => $arr_return['id'] , 'user_netname' => $arr_msg['user_name'] ));
		} else {
			switch($uid) {
				case -1:
					$arr_return['msg'] = '用户名不合法';
					break;
				case -2:
					$arr_return['msg'] = '包含不允许注册的词语';
					break;
				case -3:
					$arr_return['msg'] = '用户名已经存在';
					break;
				case -4:
					$arr_return['msg'] = 'Email 格式有误';
					break;
				case -5:
					$arr_return['msg'] = 'Email 不允许注册';
					break;
				case -6:
					$arr_return['msg'] = '该 Email 已经被注册';
					break;
				default :
					$arr_return['msg'] = "注册失败";
			}
			$arr_return['code'] = 500;
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
		if($no_verify != 1 && (!isset($arr_fields["user_name"]) || !isset($arr_fields["user_pwd"]) ) ) {
			$arr_return["code"] = 7;
			$arr_return["msg"] = cls_language::get("login_perms_err");
			return $arr_return;
		}
		//为了统计某用户登录出错次数
		if(isset($arr_fields['user_id'])) {
			$arr = self::get_user($arr_fields['user_id']);
			if(!empty($arr)) $arr_fields['user_name'] = array_search($arr_fields['user_id'] , $arr);
		} else {
			$arr = self::get_user($arr_fields['user_name'] , false);
		}
		if( empty($arr) ) {
			if($no_verify == 2) {//自动注册
				$arr_msg=self::on_reg( array('user_name'=>$arr_fields['user_name'] , 'user_pwd'=>$arr_fields['user_pwd']) );
				if($arr_msg["code"]==0){
					$user_id = $arr_msg['id'];
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
		} else {
			$user_id = $arr[$arr_fields['user_name']]['user_id'];
		}
		$obj_db = cls_obj::db();
		$sql = "select user_id,user_type,user_group_id,user_depart_id,user_netname,user_logintime,user_regtime,user_loginerr,user_state,user_continuenum,user_loginnum,user_verify_tel,user_verify_email from " . cls_config::DB_PRE . "sys_user where user_id='" . $user_id . "'";
		$obj_sys_user = $obj_db->get_one($sql);
		if(empty($obj_sys_user)) {
			//如果用户信息不存在，则自动添加
			tab_sys_user::on_insert(array('user_id' => $user_id , 'user_netname' => $arr_fields['user_name'] ));
			$obj_sys_user = $obj_db->get_one($sql);
			if(empty($obj_sys_user)) return array('code' => 500 , 'msg' => '登录失败');
		}
		$obj_sys_user['user_name'] = $arr_user['user_name'];
		if( $no_verify == 0 ) {
			$arr = self::_chk_login($user_id , $arr_fields['user_pwd'] , $obj_sys_user['user_loginerr']);
			if($arr['code'] != 0 ) return $arr;
		}
		//同步uc登录
		self::on_synlogin($user_id);
		return array('code' => 0 , 'userinfo' => $obj_sys_user);
	}
	private function _chk_login($user_id , $user_pwd , $user_loginerr) {
		$lng_errtime = $lng_errnum = 0;
		$arr_login_verify = cls_config::get('login_verify','user');//验证登录配置
		$is_login_verifycode = false;
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
			if(!empty($user_loginerr)) {
				$arr_loginerr = explode(",",$user_loginerr);//验证当天登录错误记录
				$lng_errtime = intval($arr_loginerr[0]);
				if(TIME-$lng_errtime < $lng_stop_time){
					$lng_errnum = intval($arr_loginerr[1]);
				}else{
					$lng_errtime=0;
				}
			}
			$is_login_verifycode = cls_session::get_cookie("login_verifycode");				
			if(isset($arr_login_verify["show_code"]) && intval($arr_login_verify["show_code"])<=$lng_errnum && $is_login_verifycode) {//一定错误次数后，需要验证码
				if(cls_verifycode::on_verify($arr_fields["verifycode"]) == false) {
					$arr_return["code"] = 11;
					$arr_return["msg"]  = cls_language::get("verify_code_err");
					return $arr_return;
				}
			}
			if(isset($arr_login_verify["stop_num"]) && $lng_errnum>=intval($arr_login_verify["stop_num"])) {//超出一定错误后，禁止登录
				$arr_return["code"] = 6;
				$arr_return["msg"]   = sprintf(cls_language::get("login_stop_num") , $str_unit_name);
				return $arr_return;
			}
		}
		$arr = uc_user_login($user_id , $user_pwd , 1 , 0);
		$user_id = $arr[0];
		if($user_id > 0) return array("code" => 0 , 'id' => $user_id);
		if($user_id == -1) {
			$arr_return = array('code' => 500 , 'msg' => '用户不存在，或者被删除');
		} else if($user_id == -2) {
			$arr_return = array('code' => 500 , 'msg' => '密码错误');
		} else if($user_id == -3) {
			$arr_return = array('code' => 500 , 'msg' => '安全提问错');
		} else {
			$arr_return = array('code' => 500 , 'msg' => '登录失败');
		}
		//设置登录错误信息
		$lng_errnum++;
		if(intval($arr_login_verify["show_code"])<=$lng_errnum && $is_login_verifycode) {
			cls_session::set_cookie("login_verifycode" , "1");//设置需要验证码
			$arr_return["show_code"] = 1;
		}
		cls_obj::db_w()->on_exe("update ".cls_config::DB_PRE."sys_user set user_loginerr='".TIME.",".$lng_errnum."' where user_id=".$user_id);
		return $arr_return;
	}
	/* 
	 * 取用户信息
	 * arr 为用户名或id 数组
	 * 返回：arr['id_'.id] = array();
	 */
	function get_user( $ids , $isuid = true) {
		if(empty($ids)) return array();
		$arr_return = array();
		$isuid = ($isuid) ? 1 : 0;
		if(is_array($ids)) {
			foreach($ids as $item) {
				$arr = uc_get_user($item , $isuid);
				if(!empty($arr)) {
					$arr_return[$arr[1]] = $arr[0];
				}
			}
		} else {
			$arr = uc_get_user($ids , $isuid);
			if(!empty($arr)) {
				$arr_return[$arr[1]] = $arr[0];
			}
		}
		return $arr_return;
	}
	
	/** 修改密码
	 * uid 为被改密码用户id , oldpwd 为原密码 , newpwd 为新密码 , isverify 是否需要验证旧密码
	 * 1:更新成功
	 * 0:没有做任何修改
	 * -1:旧密码不正确
	 * -4:Email 格式有误
	 * -5:Email 不允许注册
	 * -6:该 Email 已经被注册
	 * -7:没有做任何修改
	 * -8:该用户受保护无权限更改
	 */
	function on_update_pwd($oldpwd , $newpwd , $uid , $isverify = true ) {
		$arr_return=array("code" => 0 , "msg" => '');
		if( !fun_is::pwd($newpwd) ) {
			$arr_return['code'] = 113;
			$arr_return['msg']  = fun_get::rule_pwd("tips");//密码格式不对
			return $arr_return;
		}

		$arr = self::get_user($uid);
		$uname = array_search($uid , $arr);
		if(empty($uname)) {
			return array('code' => 500 , 'msg' => '用户不存在，或者被删除');
		}
		$isverify = ($isverify) ? 0 : 1;
		$code = uc_user_edit($uname , $oldpwd , $newpwd , '' , $isverify );
		switch($code) {
			case 1:
				return $arr_return;
			case 0:
				return array('code'=>0,'msg'=>'更新失败');
			case -1:
				return array('code'=>0,'msg'=>'旧密码不正确');
			case -4:
				return array('code'=>0,'msg'=>'Email 格式有误');
			case -5:
				return array('code'=>0,'msg'=>'Email 不允许注册');
			case -6:
				return array('code'=>0,'msg'=>'该 Email 已经被注册');
			case -7:
				return array('code'=>0,'msg'=>'没有做任何修改');
			case -8:
				return array('code'=>0,'msg'=>'该用户受保护无权限更改');
			default:
				return array('code'=>0,'msg'=>'更新失败');
		}
	}

	function delete_user( $ids , $isuid = true) {
		if(empty($ids)) return array('code'=>0,'msg'>'');
		$obj_db = cls_obj::db_w();
		if(is_array($ids)) {
			if(!$isuid) {
				$arr = self::get_user($ids , false);
				$ids = array();
				foreach($arr as $item) {
					$ids[] = $item['user_id'];
				}
				if(empty($ids)) return array('code' => 500 , 'msg' => '删除用户不存在');
			}
			foreach($ids as $item) {
				$code = uc_user_delete($ids);
				if($code != 1) return array('code' => 500 , 'msg' => '删除用户失败');
			}
		} else {
			$code = uc_user_delete($ids);
			if($code != 1) return array('code' => 500 , 'msg' => '删除用户失败');
		}
		return array('code'=>0,'msg'>'');
	}

	//同步登录
	function on_synlogin($user_id) {
		if(fun_get::get("app_ajax") != '1') {
			$html = uc_user_synlogin($user_id);
			cls_obj::footer_info($html);
			cls_session::set_cookie("syninfo" , '');
		} else {
			//设置cookie下次打开页面自动登录
			cls_session::set_cookie("syninfo" , '1');
		}
	}
	//同步退出
	function on_synlogout($logout = false) {
		if($logout) {
			$html = uc_user_synlogout();
			cls_obj::footer_info($html);
			cls_session::set_cookie("syninfo" , "");
		} else {
			//设置cookie下次打开页面自动登录
			cls_session::set_cookie("syninfo" , '2');
		}
	}
	//退出
	function on_loginout() {
		self::on_synlogout();
	}

}