<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class ctl_meal_menu extends mod_meal_menu {

	//默认浏览页
	function act_default() {
		//类型数组
		$this->arr_menu_type = tab_meal_menu::get_perms("type");
		$get_url_type = fun_get::get("url_type");
		if(empty($get_url_type)) foreach($this->arr_menu_type as $item=> $key) {$get_url_type = $key ;break;}; 
		//分页列表
		$this->arr_list = $this->get_pagelist( $get_url_type );
		$this->get_url_type = $get_url_type;
		$this->arr_state = tab_meal_menu::get_perms("state");
		$this->group_html = $this->get_group_select("group_val");
		$this->s_group_html = $this->get_group_select("s_group_id" , (int)fun_get::get("s_group_id"));
		return $this->get_view(); //显示页面
	}
	//回收站数据
	function act_dellist() {
		$get_url_type = fun_get::get("url_type");
		if(empty($get_url_type)) $get_url_type = 1;
		//分页列表
		$this->arr_list = $this->get_pagelist( 0 , 1 );
		$this->get_url_type = $get_url_type;
		//类型数组
		$this->arr_menu_type = tab_meal_menu::get_perms("type");
		$this->arr_state = tab_meal_menu::get_perms("state");
		$this->group_html = $this->get_group_select("group_val");
		$this->s_group_html = $this->get_group_select("s_group_id" , (int)fun_get::get("s_group_id"));
		return $this->get_view("default"); //显示页面
	}

	//编辑 新增 页面 ,有id时为编辑
	function act_edit() {

		$this->editinfo = $this->get_editinfo( fun_get::get('id') );
		$this->arr_menu_type = tab_meal_menu::get_perms("type");
		$this->arr_attribute = tab_meal_menu::get_perms("attribute");
		$this->arr_unit = cls_config::get("menu_unit" , "meal");
		$this->arr_state = tab_meal_menu::get_perms("state");
		return $this->get_view();
	}

	//保存操作,返回josn数据
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}
	//删除到回收站操作,返回josn数据
	function act_del() {
		$arr_return = $this->on_del();
		return fun_format::json($arr_return);
	}
	//从回收站回收操作,返回josn数据
	function act_reback() {
		$arr_return = $this->on_del(0);
		return fun_format::json($arr_return);
	}

	//彻底删除,返回josn数据
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}
	//设置状态
	function act_state() {
		$arr_return = $this->on_state();
		return fun_format::json($arr_return);
	}
	//推荐
	function act_tj() {
		$arr_return = $this->on_tj();
		return fun_format::json($arr_return);
	}
	//设置分组
	function act_group() {
		$arr_return = $this->on_group();
		return fun_format::json($arr_return);
	}
	//设置模式
	function act_mode() {
		$arr_return = $this->on_mode();
		return fun_format::json($arr_return);
	}
	//排序
	function act_sort() {
		$arr_return = $this->on_sort();
		return fun_format::json($arr_return);
	}

}