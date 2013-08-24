<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class ctl_ajax extends mod_ajax {
	//保存指定id收货信息
	function act_saveinfo() {
		$arr_info = $this->on_save_info();
		return fun_format::json($arr_info);
	}
	//删除收货信息
	function act_del_info() {
		$arr = fun_format::json( $this->on_del_info() );
		return $arr;
	}
	//提交定单
	function act_saveorder() {
		$menu_list = fun_format::json( $this->save_order() );
		return $menu_list;
	}
	//编辑用户信息
	function act_useredit() {
		$arr = fun_format::json( $this->on_useredit() );
		return $arr;
	}
	//注册
	function act_reg() {
		$arr = fun_format::json( $this->on_reg() );
		return $arr;
	}
	//店铺注册
	function act_shop_reg() {
		$arr = fun_format::json( $this->on_shop_reg() );
		return $arr;
	}
	//找回密码第一步
	function act_findpwd_step1() {
		$arr = fun_format::json( $this->on_findpwd_step1() );
		return $arr;
	}
	//找回密码第二步
	function act_findpwd_step2() {
		$arr = fun_format::json( $this->on_findpwd_step2() );
		return $arr;
	}
	//重置新密码
	function act_findpwd_step3() {
		$arr = fun_format::json( $this->on_findpwd_step3() );
		return $arr;
	}
	//验证信息
	function act_verify_mobile() {
		$arr = fun_format::json( $this->on_verify_mobile() );
		return $arr;
	}
	//顾客留言
	function act_msg_save() {
		$arr = fun_format::json( $this->on_msg_save() );
		return $arr;
	}

}