<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */

class ctl_sys_user_depart extends mod_sys_user_depart {

	//默认浏览页
	function act_default() {
		$this->arr_depart = tab_sys_user_depart::get_list_layer();
		return $this->get_view(); //显示页面
	}
	//移动显示页
	function act_move_open() {
		$this->depart_select_html = $this->get_depart_select();
		return $this->get_view(); //显示页面
	}
	//保存操作,返回josn数据
	function act_save_all() {
		$arr_return = $this->on_save_all();
		return fun_format::json($arr_return);
	}
	//保存操作,返回josn数据
	function act_move_save() {
		$arr_return = $this->on_move_save();
		return fun_format::json($arr_return);
	}
}