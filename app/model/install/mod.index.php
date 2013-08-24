<?php
/* KLKKDJ订餐之多店版
 * 版本号：3.1
 * 官网：http://www.klkkdj.com
 * 2013-03-10
 */
class mod_index extends inc_mod_install {
	//获取数据库所有表
	function on_install_gettable() {
		cls_database::$isinstall = true;
		$arr_return = cls_database::get_backuplist('install/table');
		return $arr_return;
	}
	//获取数据库所有表
	function on_install_table() {
		cls_database::$isinstall = true;
		$tablename = fun_get::get("tablename");
		$arr_return = cls_database::reback_table('install/table' , $tablename , 1);
		return $arr_return;
	}
	//获取数据库所有表
	function on_install_row() {
		cls_database::$isinstall = true;
		$tablename = fun_get::get("tablename");
		$page = fun_get::get("page");
		$arr_return = cls_database::reback_row('install/table' , $tablename , $page);
		return $arr_return;
	}
	//初始管理员数据
	function on_initdata() {
		$arr_user=array(
			"user_name" => fun_get::get("WEB_ADMIN"),
			"user_pwd" => fun_get::get("WEB_ADMIN_PWD"),
		);
		$arr = cls_obj::get("cls_user")->on_reg($arr_user);
		//生成安装文件
		fun_file::file_create(KJ_DIR_DATA."/install.inc",date("Y-m-d H:i:s"),1);
		return $arr;

	}
	//配置参数
	function on_config() {

		$arr_config = array(
			"DB_HOST" => fun_get::get("DB_HOST"),
			"DB_NAME" => fun_get::get("DB_NAME"),
			"DB_USER" => fun_get::get("DB_USER"),
			"DB_PWD" => fun_get::get("DB_PWD"),
			"DB_PRE" => fun_get::get("DB_PRE"),
			"WEB_ADMIN" => fun_get::get("WEB_ADMIN"),
			"WEB_ADMIN_PWD" => fun_get::get("WEB_ADMIN_PWD"),
			"WEB_DIR" => urldecode(fun_get::get("WEB_DIR")),
			"COOKIE_PRE" => fun_get::get("COOKIE_PRE"),
			"KLKKDJ_UNAME" => fun_get::get("KLKKDJ_UNAME"),
			"KLKKDJ_PWD" => fun_get::get("KLKKDJ_PWD"),
		);
		//连接数据库
		$obj_db = new cls_db_write( 
			array(
				"db_host"    => $arr_config['DB_HOST'],
				"db_user"    => $arr_config['DB_USER'],
				"db_pwd"     => $arr_config['DB_PWD'],
				"db_charset" => cls_config::DB_CHARSET
			)
		);
		$arr_msg = $obj_db->on_connect(false);
		if($arr_msg["code"] != 0){
			return array("code"=>500 , "msg"=>"连接数据库失败【原因:账号错误或数据库地址不正确】");
		}
		$arr = array();
		$obj_result = $obj_db->select("show grants");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr[]= $obj_rs;
		}
		if(empty($arr)) return array("code"=>500 , "msg" => "当前用户【" .$arr_config['DB_USER']. "】没有数据库操作权限");
		$arr_grand = array();
		$arr_dbs = $obj_db->get_dbs();
		if(!in_array(strtolower($arr_config['DB_NAME']) , $arr_dbs)) {
			//创建数据库
			$str_sql="CREATE DATABASE ".$arr_config['DB_NAME'];
			$arr_msg = $obj_db->on_exe($str_sql);
			if($arr_msg["code"] != 0){
				return array("code"=>500 , "msg"=>"创建数据库失败,请确认是否有权限创建数据库");
			}
		}
		$arr_msg = $obj_db->seldb($arr_config['DB_NAME']);
		if($arr_msg["code"] != 0){
			return array("code"=>500 , "msg"=>"连接数据库失败,请确认是否有权限操作数据库");
		}
		$str_config='<?' . 'php';
		$str_config.=chr(10)."class cfg_env {";
		$str_config.=chr(10)."const DB_HOST = '" . $arr_config['DB_HOST'] . "';";
		$str_config.=chr(10)."const DB_USER = '" . $arr_config['DB_USER'] . "';";
		$str_config.=chr(10)."const DB_PWD = '" . $arr_config['DB_PWD'] . "';";
		$str_config.=chr(10)."const DB_NAME = '" . $arr_config['DB_NAME'] . "';";
		$str_config.=chr(10)."const DB_PRE = '" . $arr_config['DB_PRE'] . "';";
		$str_config.=chr(10)."const DB_CHARSET = '" . cls_config::DB_CHARSET . "';";
		$str_config.=chr(10)."const IS_TEST = 0;//大于零为测试环境，小于或等于零为非测试环境";
		$str_config.=chr(10)."const DEFAULT_LANGUAGE = 'chinese';//系统默认语言";
		$str_config.=chr(10)."const SESSION_SAVE_HANNDLER = 'db'; // 会话保存模式 ，取值范围：1.file , 2.memcache , 3.db";
		$str_config.=chr(10)."const SESSION_SAVEPATH = '';// file模式下，session文件保存目录 ，默认为空，保存在 /data/session 目录下";
		$str_config.=chr(10)."const SESSION_MAXLIFETIME = '1440000'; // 默认过期时间,秒数";
		$str_config.=chr(10)."const SESSION_DIVISOR = 1000; // 回收机率 , 被除数";
		$str_config.=chr(10)."const USER_CENTER = 'user.klkkdj'; // 用户中心，默认：user.klkkdj ，目前还支持：user.uc 指discuz的ucenter";
		$str_config.=chr(10)."const COOKIE_PRE = '" . $arr_config['COOKIE_PRE'] . "';";
		$str_config.=chr(10)."const MD5_KEY = '" . rand(1,100000) . "';";
		$str_config.=chr(10)."}";
		$str_dirname=dirname(dirname(dirname(__FILE__)));
		$str_path = KJ_DIR_DATA."/config/cfg.env.online.php";
		file_put_contents($str_path,$str_config);
		
		$str_base = '<' . '?php';
		$str_base .=chr(10). '$domain = $_SERVER["HTTP_HOST"];';
		$str_base .=chr(10). '$dirpath = "' . $arr_config['WEB_DIR'] . '";';
		$str_base .=chr(10). 'return array(';
		$str_base .=chr(10). '	"domain"          => "http://" . $domain, //网站域名';
		$str_base .=chr(10). '	"dirpath"         => $dirpath, //二级目录';
		$str_base .=chr(10). '	"url"             => "http://" . $domain . $dirpath, //网址';
		$str_base .=chr(10). '	"admin_uids"      => "1",//超级管理员账号，多个用 , 号分隔';
		$str_base .=chr(10). ');';

		//生成安装文件
		fun_file::file_create(KJ_DIR_DATA."/config/cfg/cfg.base.php",$str_base,1);
		//生成版本文件
		$arr_version = cls_config::get("" , "version" , "" , "");
		$arr_version['web_uname'] = $arr_config['KLKKDJ_UNAME'];
		$arr_version['web_pwd'] = $arr_config['KLKKDJ_PWD'];
		$arr_version['installtime'] = date("Y-m-d H:i:s");
		$arr_version['updatetime'] = date("Y-m-d H:i:s");
		$str_base=var_export($arr_version,true);
		$str_base = '<'.'?php'.chr(10).'return '.$str_base.";";
		fun_file::file_create(KJ_DIR_DATA."/config/cfg.version.php",$str_base,1);
		return array("code"=>0);
	}
}