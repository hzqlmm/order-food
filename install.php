<?php
/*
 *
 *
 * 2013-03-24
 */
require "inc.php";
if(file_exists(KJ_DIR_DATA."/install.inc")){
	exit('系统已安装，需要重新安装请删除：data/install.inc 文件');
}
cls_app::on_load("install");