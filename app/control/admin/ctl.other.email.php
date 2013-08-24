<?php
class ctl_other_email extends mod_other_email {

	//默认浏览页
	function act_default() {
		//分页列表
		$this->arr_type = tab_other_email::get_perms('type');
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
	function act_edit() {
		//用户类型数组
		$this->arr_user_type = tab_sys_user::get_perms("type");

		//用户状态数组
		$this->arr_user_state = tab_sys_user::get_perms("state");

		$this->editinfo = $this->get_editinfo( fun_get::get('id') );
		$this->arr_account_mode = tab_other_email::get_perms("account_mode");
		$this->serverinfo = array("host" => cls_config::get("host" , 'email'),"port" => cls_config::get("port" , 'email'),"from" => cls_config::get("from" , 'email'),"fromname" => cls_config::get("fromname" , 'email'),"username" => cls_config::get("username" , 'email'),"password" => cls_config::get("password" , 'email'));
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
	//取指定id邮件发送信息,返回josn数据
	function act_send_info() {
		$arr_return = $this->get_send_info();
		return fun_format::json($arr_return);
	}
	//取指定id邮件发送信息,返回josn数据
	function act_send() {
		$arr_return = $this->on_send();
		return fun_format::json($arr_return);
	}
	//取指邮件文件信息,返回josn数据
	function act_send_file_info() {
		$arr_return = $this->get_send_file_info();
		return fun_format::json($arr_return);
	}
	//取指条件用户邮件信息,返回josn数据
	function act_send_user_info() {
		$arr_return = $this->get_send_user_info();
		return fun_format::json($arr_return);
	}
}
?>