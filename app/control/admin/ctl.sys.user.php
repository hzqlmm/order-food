<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_sys_user extends mod_sys_user {

	//默认浏览页
	function act_default() {


		//分页列表
		$this->arr_list = $this->get_pagelist();

		//用户类型数组
		$this->arr_user_type = tab_sys_user::get_perms("type");

		//用户状态数组
		$this->arr_user_state = tab_sys_user::get_perms("state");

		return $this->get_view(); //显示页面
	}
	//回收站数据
	function act_dellist() {
		//分页列表
		$this->arr_list = $this->get_pagelist(1);
		//用户类型数组
		$this->arr_user_type = tab_sys_user::get_perms("type");

		//用户状态数组
		$this->arr_user_state = tab_sys_user::get_perms("state");

		return $this->get_view("default"); //显示页面
	}


	//编辑 新增 页面 ,有id时为编辑
	function act_edit() {

		$this->editinfo = $this->get_editinfo( fun_get::get('id') );
		$this->arr_user_type = tab_sys_user::get_perms("type");
		$this->arr_user_state = tab_sys_user::get_perms("state");
		return $this->get_view();
	}

	//保存操作,返回josn数据
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}
	//从回收站回收操作,返回josn数据
	function act_reback() {
		$arr_return = $this->on_del(0);
		return fun_format::json($arr_return);
	}
	//彻底删除,返回josn数据
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}
	//彻底删除,返回josn数据
	function act_del() {
		$arr_return = $this->on_del();
		return fun_format::json($arr_return);
	}

	//设置状态
	function act_state() {
		$arr_return = $this->on_state();
		return fun_format::json($arr_return);
	}
	//清除用户配置
	function act_clear_config() {
		$arr_return = $this->on_clear_config();
		return fun_format::json($arr_return);
	}

}