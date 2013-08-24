<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */

class ctl_other_link extends mod_other_link {

	//默认浏览页
	function act_default() {
		$this->arr_group = tab_other_link::get_perms("group");
		$group = fun_get::get("group");
		if(empty($group)) $group = $this->arr_group[0];
		$this->group = $group;
		$this->arr_list = $this->sql_list($group);
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