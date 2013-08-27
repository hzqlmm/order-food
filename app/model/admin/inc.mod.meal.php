<?php
/*
 *
 *
 * 2013-03-24
 */
class inc_mod_meal extends inc_mod_admin {
	function __construct($arr_v) {
		parent::__construct($arr_v);
		$this->admin_shop = $this->get_admin_shop();
	}
	//获取当前管理的店铺信息
	function get_admin_shop() {
		$arr_return = array("id"=>0 , "name" => "默认" );
		return $arr_return;
	}
}