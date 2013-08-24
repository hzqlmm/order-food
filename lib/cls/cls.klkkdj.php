<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class cls_klkkdj {
	//根据账号与密码生成相关url
	function get_url() {
		$cfg_version = cls_config::get("" , "version" , "" , "");
		$arr = array(
			'uname' => $cfg_version["web_uname"] ,
			'pwd' => $cfg_version["web_pwd"] ,
			'module' => $cfg_version["module"] ,
			'version' => $cfg_version["version"] ,
			'site' => cls_config::get("url" , "base"),
		);
		$url = $cfg_version["web"] . "/api.php?verifyinfo=" . urlencode(json_encode($arr));
		return $url;
	}
	//登录官网www.klkkdj.com
	function official_login() {
		$url = self::get_url();
		$arr = fun_base::post( $url , array( "app_act" => "login" ) );
		if( $arr['code'] == 0 && !empty($arr['cont']) ) {
			$arr_return = fun_format::toarray($arr['cont']);
			return $arr_return;
		} else {
			return array();
		}
	}
	//下载安装包
	function down($name , $act = 'zip') {
		$arr_url = array(
			"app" => "down",
			"app_act" => $act,
			"app_ajax" => 1,
			"downname" => $name,
		);
		$url = self::get_url();
		$arr = fun_base::post($url , $arr_url);
		if( $arr['code'] == 0 && !empty($arr['cont']) ) {
			return $arr['cont'];
		} else {
			return '';
		}
	}
	function get($app_act , $app = '', $arr = array() ) {
		$arr_url = array(
			"app_ajax" => 1,
			"app_act" => $app_act,
		);
		if(!empty($app)) $arr_url['app'] = $app;
		$arr_url = array_merge($arr_url , $arr);
		$url = self::get_url();

		$cache_key = $url . "&app=" . $app . "&app_act=" . $app_act;
		$arr = cls_cache::get($cache_key , 'klkkdj.api');
		if($arr === null) {
			$arr = fun_base::post($url , $arr_url);
			if( $arr['code'] == 0 && !empty($arr['cont']) ) {
				$arr_return = fun_format::toarray($arr['cont']);
				if(isset($arr_return['code']) && $arr_return['code'] != '0') {
					return array("code"=>500,"msg"=>"获取数据失败");
				}
				cls_cache::set($arr_return , $cache_key , 'klkkdj.api');
				return $arr_return;
			} else {
				cls_cache::set(array() , $cache_key , 'klkkdj.api');
				return array();
			}
		} else {
			return $arr;
		}
	}

}