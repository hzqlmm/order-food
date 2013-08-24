<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */

class ctl_article_topic extends mod_article_topic {

	//默认浏览页
	function act_default() {


		//分页列表
		$this->arr_list = $this->get_pagelist();

		return $this->get_view(); //显示页面
	}
	//打开专题
	function act_showarticle() {
		//分页列表
		$this->arr_list = $this->get_articlelist();
		//取专题
		$this->arr_topic = $this->get_topic_list();
		return $this->get_view(); //显示页面
	}

	//编辑 新增 页面 ,有id时为编辑
	function act_edit() {

		$this->editinfo = $this->get_editinfo( fun_get::get('id') );
		$this->arr_state = tab_article_topic::get_perms("state");
		$this->arr_dirstyle = tab_article_topic::get_perms("dirstyle");
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
?>