<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_meal_act extends mod_meal_act {

	//默认浏览页
	function act_default() {
		//分页列表
		$this->arr_list = $this->get_pagelist();
		$this->arr_state = tab_meal_order::get_perms("state");
		return $this->get_view(); //显示页面
	}
	//彻底删除,返回josn数据
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}
	//编辑 新增 页面 ,有id时为编辑
	function act_edit() {
		$this->editinfo = $this->get_editinfo( fun_get::get('id') );
		$this->arr_state = tab_meal_act::get_perms("state");
		$this->arr_where = tab_meal_act::get_perms("where");
		$this->arr_method = tab_meal_act::get_perms("method");
		return $this->get_view();
	}
	//保存操作,返回josn数据
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}


}