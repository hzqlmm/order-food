<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */

class ctl_sys_common extends mod_sys_common{
	//更新缓存
	function act_clear_cache(){
		$arr_return = $this->on_clear_cache();
		return fun_format::json($arr_return);
	}
	//修改密码
	function act_update_pwd(){
		$arr_return = $this->on_update_pwd();
		return fun_format::json($arr_return);
	}
}