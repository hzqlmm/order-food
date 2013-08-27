<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_sys_log extends mod_sys_log {

	//默认浏览页
	function act_default() {
		//分页列表
		$this->arr_list = $this->get_log();
		return $this->get_view(); //显示页面
	}
	//默认浏览页
	function act_view() {
		//分页列表
		$this->content = $this->on_view();
		return $this->get_view(); //显示页面
	}
	//彻底删除,返回josn数据
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}
}