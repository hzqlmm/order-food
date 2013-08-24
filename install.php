<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
require "inc.php";
if(file_exists(KJ_DIR_DATA."/install.inc")){
	exit('系统已安装，需要重新安装请删除：data/install.inc 文件');
}
cls_app::on_load("install");