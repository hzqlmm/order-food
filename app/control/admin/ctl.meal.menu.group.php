<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_meal_menu_group extends mod_meal_menu_group {

	//默认浏览页
	function act_default() {
		$this->arr_group = tab_meal_menu_group::get_list_layer(0 , 1);
		return $this->get_view(); //显示页面
	}
	//保存操作,返回josn数据
	function act_save_all() {
		$arr_return = $this->on_save_all();
		return fun_format::json($arr_return);
	}

}
?>