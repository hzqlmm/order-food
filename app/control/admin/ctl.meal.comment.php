<?php
/* versionbeta:name
 * versionbeta:number
 * versionbeta:site
 * versionbeta:pubtime
 */
class ctl_meal_comment extends mod_meal_comment {

	//默认浏览页
	function act_default() {
		$this->arr_list = $this->get_pagelist();
		return $this->get_view(); //显示页面
	}
	//彻底删除,返回josn数据
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}

}