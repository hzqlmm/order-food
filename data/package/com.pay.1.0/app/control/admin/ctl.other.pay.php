<?php
class ctl_other_pay extends mod_other_pay {

	//默认浏览页
	function act_default() {
		$this->arr_list = $this->get_installed();
		return $this->get_view(); //显示页面
	}
	//未安装页
	function act_not() {
		$this->arr_list = $this->get_not_installed();
		return $this->get_view(); //显示页面
	}
	//配置页
	function act_config() {
		$payname = fun_get::get("payname");
		$this->edit_info = $this->get_edit_info($payname);
		return $this->get_view($payname);
	}
	//保存配置页
	function act_save() {
		$arr_return = $this->on_save();
		return fun_format::json($arr_return);
	}
	//下载操作
	function act_down() {
		$arr_return = $this->on_down();
		return fun_format::json($arr_return);
	}
	//获取安装步骤
	function act_install_steps() {
		$arr_return = $this->get_install_steps();
		return fun_format::json($arr_return);
	}
	//获取卸载步骤
	function act_uninstall_steps() {
		$arr_return = $this->get_uninstall_steps();
		return fun_format::json($arr_return);
	}
	//安装第一步，显示页
	function act_install_step1() {
		$payname = fun_get::get("payname");
		$arr = cls_klkkdj::get("version.pay");
		if(!isset($arr[$payname])) cls_error::on_error("exit" , "没有找到相关组件");
		//组件信息
		$pay_info = $arr[$payname];
		if(!stristr($pay_info["version"] , ".")) {
			$pay_info["version"] = $pay_info["version"] . ".0";
		}

		$pay_info["zipname"] = $payname . "." . $pay_info["version"];
		$this->pay_info = $pay_info;
		//关联账号信息
		$this->user_info = cls_klkkdj::official_login();
		//检测是否已有安装包
		$this->package = false;
		if(file_exists(KJ_DIR_DATA . "/package/pay/" . $payname . "." . $this->pay_info["version"] . "/install." . $payname . ".php" )) {
			$this->package = true;
		}
		//检测是否已下载
		$this->package_zip = false;
		if(file_exists(KJ_DIR_DATA . "/package/pay/" . $this->pay_info["zipname"] . ".zip" )) {
			$this->package_zip = true;
		}
		$this->down_url = cls_klkkdj::get_url() . "&app=down&app_act=pay&downname=" . urlencode($this->pay_info["zipname"]);
		$this->payname = $payname;
		return $this->get_view();
	}

	function act_install() {
		$arr_return = $this->on_install();
		return fun_format::json($arr_return);
	}
	function act_uninstall() {
		$arr_return = $this->on_uninstall();
		return fun_format::json($arr_return);
	}
	//充值记录
	function act_record() {
		$this->arr_list = $this->get_pagelist();
		return $this->get_view();
	}
}
?>