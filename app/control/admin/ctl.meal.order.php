<?php
/*
 *
 *
 * 2013-03-24
 */
class ctl_meal_order extends mod_meal_order {

	//默认浏览页
	function act_default() {
		//分页列表
		$this->arr_list = $this->get_pagelist();
		$this->print_width = cls_config::get("width" , "print" , 200);
		$this->arr_state = tab_meal_order::get_perms("state");
		return $this->get_view(); //显示页面
	}
	//彻底删除,返回josn数据
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}
	//确认定单,返回josn数据
	function act_award() {
		$arr_return = $this->on_award();
		return fun_format::json($arr_return);
	}
	//设置状态,返回josn数据
	function act_state() {
		$arr_return = $this->on_state();
		return fun_format::json($arr_return);
	}
	//订单明细
	function act_detail() {
		//订单列表
		$id = (int)fun_get::get("id");
		$this->print_width = cls_config::get("width" , "print" , 200);
		$this->order_list = $this->get_detail($id);
		return $this->get_view(); //显示页面
	}

}