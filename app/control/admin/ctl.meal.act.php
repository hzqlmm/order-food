<?php
/* KLKKDJè®¢é¤ä¹‹å•åº—ç‰ˆ
 * ç‰ˆæœ¬å·ï¼š3.1ç‰ˆ
 * å®˜ç½‘ï¼šhttp://www.klkkdj.com
 * 2013-03-24
 */

class ctl_meal_act extends mod_meal_act {

	//Ä¬ÈÏä¯ÀÀÒ³
	function act_default() {
		//·ÖÒ³ÁĞ±í
		$this->arr_list = $this->get_pagelist();
		$this->arr_state = tab_meal_order::get_perms("state");
		return $this->get_view(); //ÏÔÊ¾Ò³Ãæ
	}
	//³¹µ×É¾³ı,·µ»ØjosnÊı¾İ
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}
	//±à¼­ ĞÂÔö Ò³Ãæ ,ÓĞidÊ±Îª±à¼­
	function act_edit() {
		$this->editinfo = $this->get_editinfo( fun_get::get('id') );
		$this->arr_state = tab_meal_act::get_perms("state");
		$this->arr_where = tab_meal_act::get_perms("where");
		$this->arr_method = tab_meal_act::get_perms("method");
		return $this->get_view();
	}
	//±£´æ²Ù×÷,·µ»ØjosnÊı¾İ
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}


}