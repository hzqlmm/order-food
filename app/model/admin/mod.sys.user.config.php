<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_sys_user_config extends inc_mod_admin {
	function get_fields() {
		$key = fun_get::get("key");
		$filename = fun_get::get("filename");
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields_show($key , $this->app_dir , $filename);
		return $arr_cfg_fields;
	}
	//保存用户拖动宽度值
	function on_save_resize() {
		$arr_return = array("code" => 0 , "msg" => cls_language::get("save_ok"));
		$get_key = fun_get::get("key");
		$get_w = (int)fun_get::get("w");
		$filename = fun_get::get("filename");
		$get_index = fun_get::get("index");
		$arr_cfg_fields = tab_sys_user_config::get_fields($get_key , $this->app_dir , $filename);
		$str_key = $arr_cfg_fields["tabtit"][$get_index]["key"];
		$arr_fields = tab_sys_user_config::get_user_fields($get_key , $this->app_dir , 0 , $filename);
		$arr_fields[$str_key]["w"] = $get_w;
		$arr_config = tab_sys_user_config::get_config("config_fields");
		$arr_config[$this->app_dir][$get_key] = $arr_fields;
		$arr = tab_sys_user_config::on_save( array( "config_fields" => serialize($arr_config) ));
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	//保存分页大小
	function on_save_pagesize() {
		$arr_return = array("code" => 0 , "msg" => cls_language::get("save_ok"));
		$get_key = fun_get::get("key");
		$get_val = (int)fun_get::get("val");
		$arr_config = tab_sys_user_config::get_config("config_info");
		$arr_config[$this->app_dir][$get_key]["pagesize"] = $get_val;
		$arr = tab_sys_user_config::on_save( array( "config_info" => serialize($arr_config) ));
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	//保存编辑字段配置信息
	function on_save() {
		$arr_return = array("code" => 0 , "msg" => cls_language::get("save_ok"));
		$get_key = fun_get::get("key");
		$filename = fun_get::get("filename");
		$arr_fields = tab_sys_user_config::get_user_fields($get_key , $this->app_dir , 0 ,$filename);
		$arr_name = fun_get::post("name");
		foreach($arr_fields as $item => $key) {
			$arr_fields[$item]["val"] = 0;
		}
		foreach($arr_name as $item) {
			$arr_fields[$item]["val"] = 1;
			$arr_fields[$item]["w"] = (int)fun_get::post("w_" . $item);
		}
		$arr_config = tab_sys_user_config::get_config("config_fields");
		$arr_config[$this->app_dir][$get_key] = $arr_fields;
		$arr = tab_sys_user_config::on_save( array( "config_fields" => serialize($arr_config) ));
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	//保存排序
	function on_sort() {
		$sortby = fun_get::get("sortby");
		$key = fun_get::get("key");
		$arr_return = tab_sys_user_config::save_sort($sortby , $key  , $this->app_dir);
		return $arr_return;
	}
}