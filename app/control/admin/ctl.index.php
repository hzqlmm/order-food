<?php
/*
 *
 *
 * 2013-03-24
 */
class ctl_index extends mod_index{
	function act_default(){
		//系统自带菜单模块列表
		$this->arr_menu = $this->get_model_menu();
		$this->this_index_url = cls_config::get("dirpath");
		$this->klkkdj_url = cls_klkkdj::get_url();
		$this->hide_left = tab_sys_user_config::get_var("admin.hide.left"  , $this->app_dir);
		$this->hide_guide = tab_sys_user_config::get_var("admin.hide.guide"  , "api");
		return $this->get_view();
	}
	function act_main(){
		$this->login_info = $this->get_login_info();
		$this->server_info = $this->get_server_info();
		$this->count_info = $this->get_count_info();
		$this->version_info = cls_config::get("" , "version" , "" , "");
		$this->sms_info = $this->get_sms_info();
		$this->user_repayment = $this->get_user_repayment();
		$this->klkkdj_url = cls_klkkdj::get_url();
		return $this->get_view();
	}
	function act_left() {
		$menu_list = fun_format::json( $this->get_model_menu( fun_get::get("key") ) );
		return $menu_list;
	}
	//文章模块，菜单增量
	function act_menu_article() {
		$arr = $this->get_article_menu();
		return	$arr;
	}
	//获取官方信息
	function act_official_login() {
		$arr_info = cls_klkkdj::official_login();
		return fun_format::json($arr_info);
	}

	//下载升级包
	function act_down() {
		$arr_return = $this->on_down();
		return fun_format::json($arr_return);
	}
	//获取安装步骤
	function act_install_steps() {
		$arr_return = $this->get_install_steps();
		return fun_format::json($arr_return);
	}
	//升级第一步，显示页
	function act_update() {
		//关联账号信息
		$this->user_info = cls_klkkdj::official_login();
		$module = cls_config::get("module" , "version" , "" , "");
		$version = fun_get::get("version");
		if(!stristr($version , ".")) {
			$version = $version . ".0";
		}

		$zipname = "sys." . $module . "." . $version;
		//检测是否已有安装包
		$this->package = false;
		if(file_exists(KJ_DIR_DATA . "/package/" . $zipname . "/install.php" )) {
			$this->package = true;
		}
		//检测是否已下载
		$this->package_zip = false;
		if(file_exists(KJ_DIR_DATA . "/package/sys." . $module . "." . $version . ".zip" )) {
			$this->package_zip = true;
		}
		$this->zipname = $zipname;
		//下载到本地用到的链接
		$this->down_url = cls_klkkdj::get_url() . "&app=down&app_act=zip&downname=" . urlencode($zipname);
		return $this->get_view();
	}
	function act_install() {
		$arr_return = $this->on_install();
		return fun_format::json($arr_return);
	}
}