<?php
/*
 * KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 * 功能 ：存储与获取相关信息
 */
class cls_session_base {
	protected $perms,$session;
    function __construct() {
		$this->perms["session_time"] = (int)cls_config::get("session_time","user")*60;
		if(empty($this->perms["session_time"])) $this->perms["session_time"] = cls_config::SESSION_MAXLIFETIME;//默认过期时间
	}
	/**当前访问客户端环境信息
	 * language : 语言
	 */
	public function get_env($key , $env = '') {
		if(!isset($this->perms["env"])) {
			$this->perms["env"]['list'] = include(KJ_DIR_DATA . "/config/cfg.env.php");
			$val = fun_get::get("app_env");
			if( fun_is::set("app_env") ) {
				$this->perms["env"]['sel'] = $val;
				self::set_cookie('env' , $val);
			} else {
				$this->perms["env"]['sel'] = self::get_cookie('env');
			}
			$v = $this->perms["env"]['sel'];
			if(isset($this->perms["env"]['list'][$v]['cfg_set'])) {
				foreach($this->perms["env"]['list'][$v]['cfg_set'] as $item) {
					cls_config::set($item['key'] , $item['val'] , $item['type'] , $item['dir']);
				}
			}
		}
		if(empty($env)) $env = $this->perms["env"]['sel'];
		if(empty($env)) return '';
		return isset($this->perms["env"]['list'][$env][$key]) ? $this->perms["env"]['list'][$env][$key] : '';
	}
	//取cookie值
	static function get_cookie($key) {
		if(defined('cls_config::COOKIE_PRE')) $key = cls_config::COOKIE_PRE . $key;
		if(isset($_COOKIE[$key])) return $_COOKIE[$key];
		return "";
	}
	/** 保存cookie值
	 * msg_name : cookie名称，msg_val : cookie值，msg_expire : 有效期时间(秒)，msg_path : 保存目录，msg_domain : cookie域
	 * 无返回值
	 */
	static function set_cookie($msg_name , $msg_val = "" , $msg_expire = -999 , $msg_path = "" , $msg_domain = '' , $msg_httponly=false) {
		if(empty($msg_domain)) $msg_domain = cls_config::get("domain");
		if(empty($msg_path)) {
			$arr = parse_url(cls_config::get("url"));
			if(!isset($arr["path"])) $arr["path"] = "/";
			$msg_path = $arr['path'];
		}
		if(stristr(strtolower($msg_domain),"http:")){
			$arr_x=parse_url($msg_domain);
			$arr_y=explode(".",$arr_x["host"]);
			$lng_x=count($arr_y)-1;
			if($arr_x["host"]=="localhost" || is_numeric($arr_y[$lng_x]) ){
				$msg_domain="";
			}else{
				if($lng_x>1){
					$msg_domain=$arr_y[$lng_x-1].".".$arr_y[$lng_x];
				}else{
					$msg_domain=$arr_x["host"];
				}
			}
		}
		if(isset($_COOKIE["agent_time"]) && fun_is::isdate($_COOKIE["agent_time"])){
			$int_expire=strtotime($_COOKIE["agent_time"]);
		}else if(isset($_SERVER["REQUEST_TIME"])){
			$int_expire=$_SERVER["REQUEST_TIME"];
		}else{
			$int_expire=TIME;
		}
		if($msg_expire == 0) {
			$int_expire = 0;//随浏览器关闭而消失
		} else {
			if($msg_expire == -999)	$msg_expire = cls_config::SESSION_MAXLIFETIME;
			$int_expire=$int_expire+$msg_expire;
		}

		$str_source = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
		if(defined('cls_config::COOKIE_PRE')) $msg_name=cls_config::COOKIE_PRE.$msg_name;
		if(PHP_VERSION < '5.2.0') {
			setcookie($msg_name,$msg_val,$int_expire,$msg_path,$msg_domain,$str_source);
		}else{
			setcookie($msg_name,$msg_val,$int_expire,$msg_path,$msg_domain,$str_source,$msg_httponly);
		}
	}
	/** 设置session时间
	 *  默认不做任何操作，留给继承类操作
	 */
	function session_time($msg_val) {
	}
}