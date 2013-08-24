<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_sys_log extends inc_mod_admin {
	/* 按模块查询用户信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_log() {
		$get_dirpath = fun_get::get("url_dirpath");
		if(!empty($get_dirpath) && substr($get_dirpath,0,1) != "/" ) $get_dirpath = "/" . $get_dirpath;
		$arr_return["dir"] = fun_file::get_dirs( KJ_DIR_DATA . "/error" . $get_dirpath );
		$arr_return["file"] = fun_file::get_files( KJ_DIR_DATA . "/error" . $get_dirpath );
		$arr_return["pathdir"] = $get_dirpath;
		$arr= explode("/" , $get_dirpath);
		$arr_path = array( "<a href=\"javascript:thisjs.opendir('');\">根目录</a>" );
		$str_path = "";
		foreach($arr as $item) {
			if(empty($item)) continue;
			$str_path .= "/" . $item;
			$arr_path[] = "<a href=\"javascript:thisjs.opendir('" . $str_path . "');\">" . $item . "</a>";
		}
		$arr_return["path"] = implode(" -> " , $arr_path);
		return $arr_return;
	}
	function on_view() {
		$get_dirpath = fun_get::get("dirpath");
		$get_filename = fun_get::get("filename");
		if(!empty($get_dirpath) && substr($get_dirpath,0,1) != "/" ) $get_dirpath = "/" . $get_dirpath;
		$str_path = KJ_DIR_DATA . "/error" . $get_dirpath . "/" . $get_filename;
		$cont = fun_format::tohtml(fun_file::get_cont($str_path));
		return $cont;
	}
	//删除
	function on_delete() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("delete_ok"));
		$get_dirpath = fun_get::get("url_dirpath");
		$str_id = fun_get::get("id");
		$arr_id = fun_get::get("selid");
		if( empty($arr_id) && empty($str_id) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = cls_language::get("delete_no_id");
			return $arr_return;
		}
		if(empty($arr_id)) $arr_id[] = $str_id; //优先考虑 arr_id
		if(!empty($get_dirpath) && substr($get_dirpath,0,1) != "/" ) $get_dirpath = "/" . $get_dirpath;
		$str_path = KJ_DIR_DATA . "/error" . $get_dirpath . "/";
		$ii = 0;
		foreach($arr_id as $item) {
			$str_filepath = $str_path . $item;
			if(fun_file::isfile($str_filepath)) {
				$arr = fun_file::file_delete($str_filepath);
			} else {
				$arr = fun_file::dir_delete($str_filepath);
			}
			if($arr['code'] != 0) $ii++;
		}
		if($ii == count($arr_id) ) {
			$arr_return['code'] = $arr["code"];//见参数说明表
			$arr_return['msg']  = $arr["msg"];
			return $arr_return;
		} else if($ii > 0) {
			$arr_return['code'] = 331;//见参数说明表
			$arr_return['msg']  = cls_language::get("delete_part_err");
			return $arr_return;
		}
		return $arr_return;
	}
}