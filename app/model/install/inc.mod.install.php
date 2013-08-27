<?php
/*
 *
 *
 * 2013-03-24
 */
class inc_mod_install extends cls_base{

	/**
	 * admin 目录 初始类，启动 : 登录检查，权限检查
	 */
	function __construct($arr_v) {
		$str_x = dirname($_SERVER['REQUEST_URI']);
		$this->dirpath = ($str_x == '/' || $str_x == '\\') ? '' : $str_x;
		$this->webcss_url = $this->dirpath . "/webcss/";
		$this->version_info = cls_config::get("" , "version" , "" , "");
		parent::__construct($arr_v);

	}
}