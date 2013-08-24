<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class cls_base{
	protected $perms;
	protected $perms_obj;
	function __construct( $arr_v = array() ) {
		$this->on_init($arr_v);
	}
	function __get($msg_name) {
		if(isset($this->perms[$msg_name])) {
			return $this->perms[$msg_name];
		}else{
			return "";
		}
	}
	function __set($msg_name,$msg_val) {
		$this->perms[$msg_name] = $msg_val;
	}
	function __call($msg_name,$msg_perms){
		return $this->get_view();
	}
	function on_init($arr_v) {
		foreach($arr_v as $item=>$key) {
			$this->perms[$item] = $key;
		}
	}
	function get_view( $act='' ) {
		$str_act = "";
		($act == '')? $str_act = $this->perms["app_act"] : $str_act = $act;
		$str_act = "." . $str_act;
		$str_mod = "";
		if(!empty($this->perms["app_module"])) $str_mod = $this->perms["app_module"] . ".";
		$str_path = $this->perms["app_viewdir"]."/" . $str_mod . $this->perms["app"] . $str_act;
		$str_path = cls_resolve::on_resolve($str_path);
		$arr_perms = $this->perms;
		return cls_view::on_load($str_path,$arr_perms);
	}
}