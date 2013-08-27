<?php
/**
 *
 *
 *
 * 2013-03-24
 * 用户配置相关集
 * 保存用户当前操作页大小，字段信息，字段宽度 ,默认排序信息
 */
class mod_user_config extends inc_mod_common {
	function get_fields() {
		$dir = fun_get::get("dir");
		if(empty($dir)) $dir = $this->app_dir;
		$key = fun_get::get("key");
		$filename = fun_get::get("filename");
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields_show($key , $dir , $filename);
		return $arr_cfg_fields;
	}
	//保存用户拖动宽度值
	function on_save_resize() {
		$arr_return = array("code" => 0 , "msg" => cls_language::get("save_ok"));
		$dir = fun_get::get("dir");
		if(empty($dir)) $dir = $this->app_dir;
		$get_key = fun_get::get("key");
		$get_w = (int)fun_get::get("w");
		$filename = fun_get::get("filename");
		$get_index = fun_get::get("index");
		$arr_cfg_fields = tab_sys_user_config::get_fields($get_key , $dir , $filename);
		$str_key = $arr_cfg_fields["tabtit"][$get_index]["key"];
		$arr_fields = tab_sys_user_config::get_user_fields($get_key , $dir , 0 , $filename);
		$arr_fields[$str_key]["w"] = $get_w;
		$arr_config = tab_sys_user_config::get_config("config_fields");
		$arr_config[$dir][$get_key] = $arr_fields;
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
		$dir = fun_get::get("dir");
		if(empty($dir)) $dir = $this->app_dir;
		$get_key = fun_get::get("key");
		if(end( explode(".", $get_key) ) == "default") $get_key = substr($get_key , 0 , -8);
		$get_val = (int)fun_get::get("val");
		$arr_config = tab_sys_user_config::get_config("config_info");
		$arr_config[$dir][$get_key]["pagesize"] = $get_val;
		$arr = tab_sys_user_config::on_save( array( "config_info" => serialize($arr_config) ));
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	//保存指定变量
	function on_save_var() {
		$arr_return = array("code" => 0 , "msg" => cls_language::get("save_ok"));
		$dir = fun_get::get("dir");
		if(empty($dir)) $dir = $this->app_dir;
		$get_val = fun_get::get("val");
		$get_var = fun_get::get("var");
		$arr_config = tab_sys_user_config::get_config("config_info");
		$arr_config[$dir]['var'][$get_var] = $get_val;
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
		$dir = fun_get::get("dir");
		if(empty($dir)) $dir = $this->app_dir;

		$get_key = fun_get::get("key");
		$filename = fun_get::get("filename");
		$arr_fields = tab_sys_user_config::get_user_fields($get_key , $dir , 0 ,$filename);
		$arr_name = fun_get::post("name");
		$arr_isshow = fun_get::post("is_show" , array());
		foreach($arr_fields as $item => $key) {
			$arr_fields[$item]["val"] = 0;
		}
		foreach($arr_name as $item) {
			if(isset($arr_fields[$item])) {
				$arr_new_fields[$item] = $arr_fields[$item];
				(in_array($item,$arr_isshow)) ? $arr_new_fields[$item]["val"] = 1 : $arr_new_fields[$item]["val"] = 0;
				$arr_new_fields[$item]["w"] = (int)fun_get::post("w_" . $item);
			}
		}
		$arr_config = tab_sys_user_config::get_config("config_fields");
		$arr_config[$dir][$get_key] = $arr_new_fields;
		$arr = tab_sys_user_config::on_save( array( "config_fields" => serialize($arr_config) ));
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	//保存排序
	function on_sort() {
		$dir = fun_get::get("dir");
		if(empty($dir)) $dir = $this->app_dir;
		$sortby = fun_get::get("sortby");
		$key = fun_get::get("key");
		$arr_return = tab_sys_user_config::save_sort($sortby , $key  , $dir);
		return $arr_return;
	}
}