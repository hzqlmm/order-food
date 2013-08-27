<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_meal_menu_today extends mod_meal_menu_today {

	//默认浏览页
	function act_default() {
		$get_url_date = fun_get::get("url_date");
		$get_url_date_period = (int)fun_get::get("url_date_period");
		if(!fun_is::isdate($get_url_date)) $get_url_date = date("Y-m-d");
		//取默认时间段
		$this->this_period = tab_meal_menu::get_opentime();
		if(!isset($_REQUEST["url_date_period"])) $get_url_date_period = $this->this_period["nowindex"];
		$this->this_date = $get_url_date;
		$this->this_date_period = $get_url_date_period;
		$this->arr_list = $this->sql_list( $this->this_date , $this->this_date_period );
		return $this->get_view(); //显示页面
	}
	//移动显示页
	function act_add() {
		$this->arr_list = $this->menu_list();
		return $this->get_view(); //显示页面
	}
	//保存操作,返回josn数据
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}
}