<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class ctl_sys_config extends mod_sys_config {

	//默认页面
	function act_default() {
		$this->arr_module = $this->get_module();
		$this->get_url_module = fun_get::get("url_module");
		$str_module = $this->get_url_module;
		if(empty($str_module)) {
			list($key , $val) = each(array_slice($this->arr_module, 0,1));
			$this->get_url_module = $key;
		}
		$this->arr_list = $this->get_list( $this->get_url_module );
		return $this->get_view();
	}
	//打印设置
	function act_print() {
		$this->arr_module = $this->get_module();
		$this->get_url_module = fun_get::get("url_module");
		$str_module = $this->get_url_module;
		if(empty($str_module)) {
			list($key , $val) = each(array_slice($this->arr_module, 0,1));
			$this->get_url_module = $key;
		}
		$this->arr_list = $this->get_list( $this->get_url_module );
		$this->print_info = cls_config::get("printinfo" , "print");
		$this->width = cls_config::get("width" , "print");
		return $this->get_view();
	}
	//编辑页面
	function act_edit() {
		$this->arr_module = $this->get_module();
		$this->editinfo = $this->get_editinfo( fun_get::get('id') );
		return $this->get_view();
	}

	//保存操作
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}

	//刷新操作
	function act_update() {
		$arr_return = $this->on_update();
		return fun_format::json($arr_return);
	}
	//刷新操作
	function act_update_print() {
		$arr_return = $this->on_update_print();
		return fun_format::json($arr_return);
	}

	//删除操作
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}
}