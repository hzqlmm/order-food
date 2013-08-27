<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_sys_area extends mod_sys_area {

	//默认浏览页
	function act_default() {
		$this->this_pid = (int)fun_get::get("url_pid");
		$this->this_path = $this->get_path( $this->this_pid );
		$this->arr_list = $this->sql_list($this->this_pid);
		return $this->get_view(); //显示页面
	}
	//移动显示页
	function act_move_open() {
		$this->area_select_html = $this->get_area_select();
		return $this->get_view(); //显示页面
	}
	//保存操作,返回josn数据
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}
	//保存操作,返回josn数据
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}

}