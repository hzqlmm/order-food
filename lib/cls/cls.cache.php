<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class cls_cache {
	//取缓存数据
	static function get($key , $path , $time = 60) {
		$filepath = KJ_DIR_CACHE . "/data/" . $path . "/" . md5($key) . ".php";
		if(!file_exists($filepath)) return null;
		$time = TIME - $time * 60;
		$arr_html=fun_file::get_file_perms($filepath);
		if(strtotime($arr_html["mtime"]) < $time ) return null;
		return include($filepath);
	}

	static function set($arr , $key , $path) {
		$filepath = KJ_DIR_CACHE . "/data/" . $path . "/" . md5($key) . ".php";
		$val = var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		$arr = fun_file::file_create($filepath , $val , 1);
		return $arr;
	}
}