<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_meal_act extends mod_meal_act {

	//Ĭ�����ҳ
	function act_default() {
		//��ҳ�б�
		$this->arr_list = $this->get_pagelist();
		$this->arr_state = tab_meal_order::get_perms("state");
		return $this->get_view(); //��ʾҳ��
	}
	//����ɾ��,����josn����
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}
	//�༭ ���� ҳ�� ,��idʱΪ�༭
	function act_edit() {
		$this->editinfo = $this->get_editinfo( fun_get::get('id') );
		$this->arr_state = tab_meal_act::get_perms("state");
		$this->arr_where = tab_meal_act::get_perms("where");
		$this->arr_method = tab_meal_act::get_perms("method");
		return $this->get_view();
	}
	//�������,����josn����
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}


}