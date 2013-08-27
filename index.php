<?php
/*
 *
 *
 * 2013-03-24
 */
require "inc.php";

if(!file_exists(KJ_DIR_DATA."/install.inc")){
	fun_base::url_jump("install.php");
	exit;
}
$mod_dir = cls_obj::get("cls_session")->get_env('mod_dir');
$view_dir = cls_obj::get("cls_session")->get_env('view_dir');
cls_app::on_load($mod_dir , $view_dir);