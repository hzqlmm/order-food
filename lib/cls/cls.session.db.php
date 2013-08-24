<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 * 名称 ：会话类
 * 功能 ：操作客户端与服务器之间的会话信息，如登录状态
 */
class cls_session extends cls_session_base {
	private $is_update = false;
    function __construct() {
		parent::__construct();
		$this->_init();
	}

	//结束时判断是否有更新，有则同步到session表
    function __destruct() {
		if($this->is_update) $this->_save_update();
	}

	//初始化session值
	private function _init() {
		$this->session["id"] = '';
		$str_sid = $this->get_cookie("s_id");
		if(!empty($str_sid)) {
			$str_sid = fun_get::safecode($str_sid,"decode");//解码
			$str_sql = "select * from " . cls_config::DB_PRE . "sys_session where session_id='" . $str_sid . "'";
			$arr_session  = cls_obj::db()->get_one($str_sql);
			if(!empty($arr_session)) {
				if(!empty($arr_session["session_val"])) {
					$arr = unserialize($arr_session["session_val"]);
					//非自动登录
					$autologin = $this->get_cookie("autologin");
					if(empty($autologin)) $arr['login_user'] = array();
					$this->session = $arr;
				}
				$this->session["id"] = $arr_session["session_id"];
			} else {
				$str_sid = '';
			}
		}
		if(empty($this->session["id"])) {
			$arr_msg=tab_sys_session::on_save(array());//生成session
			$this->session["id"] = $arr_msg["id"];
			$str_sid=fun_get::safecode($this->session["id"]);
			$this->set_cookie("s_id" , $str_sid );   //session_id 对于一台设备标识id ,可以永久保存
		}
	}
	//将更新同步到 session表
	private function _save_update() {
		$arr_fields = array(
			"session_id"      => $this->session["id"],
			"session_user_id" => 0,
			"session_group_id"=> 0,
			"session_val"     => ''
		);
		if(isset($this->session["login_user"]) && !empty($this->session["login_user"]) ) {
			$arr_fields["session_user_id"] = $this->session["login_user"]["uid"];
			$arr_fields["session_group_id"] = $this->session["login_user"]["group_id"];
		}
		$arr_fields["session_val"] = serialize($this->session);
		$arr_msg = tab_sys_session::on_save($arr_fields);
	}

	//获取当前会话，指定关健
	function get($key) {
		if(isset($this->session[$key])) {
			return $this->session[$key];
		} else {
			return "";
		}
	}
	//设置session值
	function set($name , $val) {
		$this->session[$name] = $val;
		$this->is_update = true;
	}
	//注销变量
	function destroy($name) {
		if(!isset($this->session[$name])) return;
		$this->is_update = true;
		unset($this->session[$name]);
	}

}