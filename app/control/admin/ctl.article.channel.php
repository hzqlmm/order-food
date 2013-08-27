<?php
/*
 * 版本号：3.0正式版
 *
 * 2012-12-31
 */


class ctl_article_channel extends mod_article_channel {

	//默认浏览页
	function act_default() {


		//分页列表
		$this->arr_list = $this->get_pagelist();

		return $this->get_view(); //显示页面
	}

	//编辑 新增 页面 ,有id时为编辑
	function act_edit() {

		$this->editinfo = $this->get_editinfo( fun_get::get('id') );
		$this->arr_state = tab_article_channel::get_perms("state");
		$this->arr_dirstyle = tab_article_channel::get_perms("dirstyle");
		$this->arr_mode = tab_article_channel::get_perms("mode");
		return $this->get_view();
	}
	//保存操作,返回josn数据
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}

	//设置状态
	function act_state() {
		$arr_return = $this->on_state();
		return fun_format::json($arr_return);
	}
	//删除,返回josn数据
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}

}