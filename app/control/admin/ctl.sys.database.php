<?php
/*
 *
 *
 * 2013-03-24
 */
class ctl_sys_database extends mod_sys_database {

	//默认浏览页
	function act_default() {
		//分页列表
		$this->arr_list = $this->get_tables();
		$this->backupname = date("Y-m-d H:i");
		return $this->get_view(); //显示页面
	}
	//优化表
	function act_optimize_go() {
		$arr_return = $this->on_optimize();
		return fun_format::json($arr_return);
	}
	//修复表
	function act_repair() {
		$arr_return = $this->on_repair();
		return fun_format::json($arr_return);
	}
	//备份数据表
	function act_backup() {
		$arr_return = $this->on_backup();
		return fun_format::json($arr_return);
	}
	//备份数据行
	function act_backup_row() {
		$arr_return = $this->on_backup_row();
		return fun_format::json($arr_return);
	}
	//还原
	function act_reback() {
		//分页列表
		$this->arr_list = $this->get_reback();
		return $this->get_view(); //显示页面
	}
	//还原数据表
	function act_reback_table() {
		$arr_return = $this->on_reback_table();
		return fun_format::json($arr_return);
	}
	//还原数据表
	function act_reback_row() {
		$arr_return = $this->on_reback_row();
		return fun_format::json($arr_return);
	}
	//还原数据表
	function act_reback_gettable() {
		$arr_return = $this->on_reback_gettable();
		return fun_format::json($arr_return);
	}
	//删除备份
	function act_del_backup() {
		$arr_return = $this->on_del_backup();
		return fun_format::json($arr_return);
	}
	//删除备份表
	function act_del_table() {
		$arr_return = $this->on_del_table();
		return fun_format::json($arr_return);
	}
}