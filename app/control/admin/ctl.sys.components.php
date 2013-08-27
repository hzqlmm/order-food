<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_sys_components extends mod_sys_components {

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
	function act_step1() {
		$com = fun_get::get("com");
		$arr = cls_klkkdj::get("version.com");
		if(!isset($arr[$com])) cls_error::on_error("exit" , "没有找到相关组件");
		//组件信息
		$com_info = $arr[$com];
		if(!stristr($com_info["version"] , ".")) {
			$com_info["version"] = $com_info["version"] . ".0";
		}
		$com_info["zipname"] = "com.". $com . "." . $com_info["version"];
		$this->com_info = $com_info;
		//关联账号信息
		$this->user_info = cls_klkkdj::official_login();
		//检测是否已有安装包
		$this->package = false;
		if(file_exists(KJ_DIR_DATA . "/package/com." . $com . "." . $this->com_info["version"] . "/install.com." . $com . ".php" )) {
			$this->package = true;
		}
		//检测是否已下载
		$this->package_zip = false;
		if(file_exists(KJ_DIR_DATA . "/package/" . $this->com_info["zipname"] . ".zip" )) {
			$this->package_zip = true;
		}

		//下载到本地用到的链接
		$this->down_url = cls_klkkdj::get_url() . "&app=down&app_act=zip&downname=" . urlencode($com_info["zipname"]);
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
}