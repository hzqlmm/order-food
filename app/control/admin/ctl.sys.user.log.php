<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class ctl_sys_user_log extends mod_sys_user_log {

	//默认浏览页
	function act_default() {
		$this->arr_list = $this->get_list();
		return $this->get_view(); //显示页面
	}

	//清除日志
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}
}