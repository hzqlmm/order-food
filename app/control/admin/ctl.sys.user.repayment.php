<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */

class ctl_sys_user_repayment extends mod_sys_user_repayment {

	//默认浏览页
	function act_default() {
		//分页列表
		$this->arr_list = $this->get_pagelist();
		return $this->get_view(); //显示页面
	}
}