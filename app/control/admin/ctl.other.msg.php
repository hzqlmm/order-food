<?php
class ctl_other_msg extends mod_other_msg {

	//默认浏览页
	function act_default() {
		$this->arr_type = cls_obj::get("cls_com")->msg('get_perms','type');
		$url_type = fun_get::get("url_type");
		if(empty($url_type)) {
			foreach($this->arr_type as $item => $key) {
				$url_type = $key;break;
			}
		}
		$this->type = $url_type;
		$this->arr_list = $this->get_pagelist($url_type);
		return $this->get_view(); //显示页面
	}
	//编辑 新增 页面 ,有id时为编辑
	function act_return() {

		$this->editinfo = $this->get_editinfo( fun_get::get('id') );
		return $this->get_view();
	}

	//保存操作,返回josn数据
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}
	//彻底删除,返回josn数据
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}

}
?>