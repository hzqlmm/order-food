<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */

class ctl_sys_user_action extends mod_sys_user_action {

	//默认浏览页
	function act_default() {
		//分页列表
		$this->arr_list = $this->get_pagelist();
		return $this->get_view(); //显示页面
	}
	//配置
	function act_config() {
		$this->arr_list = cls_config::get("" , "user.action" , "" , "");
		return $this->get_view(); //显示页面
	}
	//保存配置
	function act_config_save() {
		$arr = $this->config_save();
		return fun_format::json($arr);
	}
}