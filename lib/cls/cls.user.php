<?php
/*
 *
 *
 * 2013-03-24
 */
require_once KJ_DIR_ROOT."/lib/interface/interface.".cls_config::USER_CENTER.".php";
class cls_user extends interface_user {
	static $userinfo;
	static $perms;
    function __construct() {
		self::$perms = array(
			"uid"            => 0,   //用户id
			"uname"       => '', //用户名
			"name"          => "",  //昵称
			"group_id"       => 0,   //管理组id
			"depart_id"       => 0,   //部门id
			"type"           => "",  //用户类型
			"version"        => "" ,  //版本号
			"sid"            => "",  //会话id
			"lastlogintime"  => 0, // 上次登录时间
			"shop_id" => 0,//店铺id
		);
		self::$userinfo = array();

		//取当前登录信息
		$arr_logininfo = cls_obj::get("cls_session")->get("login_user");
		if(!empty($arr_logininfo)){
			self::$perms = $arr_logininfo;
			//每天刷新登录信息
			$login_time = (int)cls_session::get_cookie("login_time");
			if($login_time < strtotime( date("Y-m-d" ) ) ) {//说明是以前登录来的
				$this->on_login(array("user_id"=>self::$perms["uid"]) , 1);//刷新登录
			}
		}

		parent::__construct(self::$perms['uid']);

    }
	function __get($key) {
		if( isset(self::$perms[$key]) ) {
			return self::$perms[$key];
		} else {
			return "";
		}
	}
	function __set($key , $val) {
		self::$perms[$key] = $val;
	}

	//判断是否已经登录
	function is_login() {
		if(self::$perms["uid"]>0) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * 判断是否显示验证码
	 */
	function is_verifycode() {
			$arr_login_verify = cls_config::get('login_verify','user');//验证登录配置
			$is_login_verifycode = cls_session::get_cookie("login_verifycode");
			if($is_login_verifycode || isset($arr_login_verify["show_code"]) && $arr_login_verify["show_code"]=='0' ) {
				if(!$is_login_verifycode) cls_session::set_cookie("login_verifycode" , "1");//设置需要验证码
				return true;
			}
			return false;
	}
	/** 用户登录入口
	 *	arr_fields : 数组，包括：uname , pwd 等
	 *  no_verify  : 为1时，用arr_fields 直接包函 uid 登录 如：整合登录时用到
	 *               为2时，如果用户不存在，直接注册
	 *  is_synlogin : 是否为整合同步登录，如果是，则用户已经登录就不再重复登录
	 */
	function on_login($arr_fields , $no_verify = 0 , $is_synlogin = false) {
		$arr_return = array("code" => 0 , "msg" => "");
		if($is_synlogin && $this->is_login()) {
			if(isset($arr_fields['user_id']) && $arr_fields['user_id'] == self::$perms['uid']) return $arr_return;
			if(isset($arr_fields['user_name']) && $arr_fields['user_name'] == self::$perms['uname']) return $arr_return;
		}
		$arr = parent::on_login($arr_fields , $no_verify);
		if($arr['code']!=0) return $arr;
		$arr_user = $arr['userinfo'];
		cls_session::set_cookie("login_verifycode" , "");

		//是否为超管
		$is_super_admin = $this->is_super_admin($arr_user['user_id']);
		//验证状态
		if( $arr_user["user_state"] < 1 && !$is_super_admin ) {
			$str_state=array_search($arr_user["user_state"] , tab_sys_user::get_perms("state"));
			$arr_return["code"] = 6;
			if($arr_user['user_state']==0) {
				$arr_return["msg"]   = "您的账号还未通过审核,暂时不能登录";
			} else {
				$arr_return["msg"]   = sprintf(cls_language::get("login_state_tips") , $str_state);
			}
			return $arr_return;
		}
		$obj_db = cls_obj::db_w();
		//是否为管理员
		self::$perms["sid"]      = cls_obj::get("cls_session")->get("id");
		self::$perms["uid"]      = $arr_user["user_id"];
		self::$perms["name"]    = empty($arr_user['user_netname']) ? $arr_user['user_name'] : $arr_user['user_netname'];
		self::$perms["uname"]  = $arr_user['user_name'];
		self::$perms["type"]     = $arr_user["user_type"];
		self::$perms["group_id"] = $arr_user["user_group_id"];
		self::$perms["depart_id"] = $arr_user["user_depart_id"];
		self::$perms["lastlogintime"] = $arr_user["user_logintime"];
		self::$perms["verify_tel"] = $arr_user["user_verify_tel"];
		self::$perms["verify_email"] = $arr_user["user_verify_email"];
		if($arr_user['user_type'] == 'shop') {
			$obj_shop = $obj_db->get_one("select shop_id from " . cls_config::DB_PRE . "meal_shop where shop_user_id='" . $arr_user['user_id'] . "'");
			if(!empty($obj_shop)) self::$perms["shop_id"] = $obj_shop["shop_id"];
		}

		//统计连续登录次数
		$lng_continuenum=$arr_user["user_continuenum"];
		if($arr_user["user_logintime"]<date("Y-m-d",TIME)){
			//每日登录行为事件
			tab_sys_user_action::on_action( self::$perms["uid"] , 'user_login_day' );
			//插入登录日记
			(empty($arr_user['user_logintime']) || substr($arr_user['user_logintime'],0,10)==date("Y-m-d" , $arr_user['user_regtime']) ) ? $isreg = 1 : $isreg = 0;
			$obj_db->on_insert(cls_config::DB_PRE."sys_user_login",array('login_time' => date("Y-m-d H:i:s") , "login_day" => date("Y-m-d") , "login_ip" => fun_get::ip() , "login_user_id" => $arr_user["user_id"] , "login_isreg" => $isreg));
			if($arr_user["user_logintime"]>date("Y-m-d" , strtotime("-1 day",strtotime(date("Y-m-d",TIME))))){
				//连续登录行为事件
				$lng_continuenum=$arr_user["user_continuenum"]+1;
				tab_sys_user_action::on_action( self::$perms["uid"] , 'user_login_continue' , array('level'=>$lng_continuenum) );
			}else{
				$lng_continuenum=0;
			}
		}

		//更新登陆信息
		$obj_db->on_update(cls_config::DB_PRE."sys_user" , array(
				"user_loginnum"    => $arr_user["user_loginnum"] + 1,
				"user_loginip"     => fun_get::ip(),
				"user_logintime"   => date("Y-m-d H:i:s" , TIME),
				"user_continuenum" => $lng_continuenum
			) , "user_id='".$arr_user["user_id"]."'");
		cls_obj::get("cls_session")->set("login_user",self::$perms);
		$str_sid=fun_get::safecode(self::$perms['sid']);
		cls_session::set_cookie("s_id" , $str_sid );
		cls_session::set_cookie("login_time" , TIME);//保存本次登录时间

		if( $no_verify == 0 ) {//只有在验证登录时才保存自动登录状态
			if(isset($arr_fields['autologin']) && !empty($arr_fields['autologin'])) {//自动登录标识
				cls_session::set_cookie("autologin",1);
			} else {
				cls_session::set_cookie("autologin",1,0);
			}
		}
		return $arr_return;
	}
	//退出登录
	function on_loginout( $is_synlogout = false ) {
		if($is_synlogout && !$this->is_login()) return;
		cls_obj::get("cls_session")->set("login_user",'');
		parent::on_loginout();
	}
	/* 验证用户是否为超级管理员
	 * uid : 指定要验证的用户 id,如果没指定，默认验证当前用户
	 */
	function is_super_admin($uid = 0) {
		$str_uid = self::$perms["uid"];
		if(!empty($uid)) $str_uid = $uid;
		$str_admin_uids = cls_config::get("admin_uids");
		$arr = explode("," , $str_admin_uids);
		if(in_array($str_uid , $arr)) {
			return true;
		} else {
			return false;
		}
	}
	/* 验证用户是否为管理员
	 * uid : 指定要验证的用户 id,如果没指定，默认验证当前用户
	 */
	function is_admin() {
		if( (self::$perms["group_id"]>0 && self::$perms['type']!='shop') || $this->is_super_admin() ){
			return true;
		}else{
			return false;
		}
	}
	/** 修改密码
	 * uid 为被改密码用户id , oldpwd 为原密码 , newpwd 为新密码 , isverify 是否需要验证旧密码
	 */
	function on_update_pwd($oldpwd , $newpwd , $uid = 0 , $isverify = true ) {
		if(empty($uid)) $uid = $this->uid;
		return parent::on_update_pwd($oldpwd , $newpwd , $uid , $isverify);
	}

	//取用户当前积分
	function get_score() {
		if(isset(self::$userinfo["score"])) {
			return self::$userinfo["score"];
		}
		$this->_score_experience();
		return self::$userinfo["score"];
	}
	//取用户当前经验
	function get_experience() {
		if(isset(self::$userinfo["experience"])) {
			return self::$userinfo["experience"];
		}
		$this->_score_experience();
		return self::$userinfo["experience"];
	}
	//取用户当前预付款
	function get_repayment() {
		if(isset(self::$userinfo["repayment"])) {
			return self::$userinfo["repayment"];
		}
		$this->_score_experience();
		return self::$userinfo["repayment"];
	}
	//取用户 等级
	function get_level($experience = 0) {
		if(empty($experience)) $experience = $this->get_experience();
		return tab_sys_user::get_level($experience);
	}
	function _score_experience() {
		$obj_rs = cls_obj::db()->get_one("select user_score,user_experience,user_repayment from " . cls_config::DB_PRE . "sys_user where user_id='" . $this->uid . "'");
		if(!empty($obj_rs)) {
			self::$userinfo["score"] = $obj_rs["user_score"];
			self::$userinfo["experience"] = $obj_rs["user_experience"];
			self::$userinfo["repayment"] = $obj_rs["user_repayment"];
		} else {
			self::$userinfo["score"] = 0;
			self::$userinfo["experience"] = 0;
			self::$userinfo["repayment"] = 0;
		}
	}
}