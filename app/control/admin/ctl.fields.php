<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_fields extends mod_fields{
	function act_default(){
		//系统自带菜单模块列表
		$this->arr_fields = $this->get_fields();
		return $this->get_view();
	}
	//保存操作,返回josn数据
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}
	//当用户拖动表格列时，自动保存当前配置
	function act_save_resize() {
		$arr_return = $this->on_save_resize();
		return fun_format::json($arr_return);
	}
}