<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_other extends inc_mod_common {
	/* 上传附件
	 * GET 参数：small=1 表示生成缩略图,small_w 与 small_h 指定时则按些值缩小图大小，否则按系统设置值
	 *			 type : {pic,media,flash,rar,doc} 指定则限制只能传相关类型文件 (注意此处限制与系统限制是叠加的作用)
				 ext  : 指定允许上传的扩展名，(注意此处限制与系统限制是叠加的作用)
	 * 返回： 数组：其中 info 为上传相关信息，以json格式传送，键值包括：url , url_small 或上传失败 code != 0
	 */
	function on_upload() {
		$arr_return = array("code" => 0 , "msg" => cls_language::get("upload_ok"));
		//是否生成略图
		if( fun_get::get("small") != "" ) {
			$_FILES["file_1"]["small"] = 1;
			if( fun_get::get("small_w") != '' ) $_FILES["file_1"]["small_wh"]["w"] = fun_get::get("small_w");
			if( fun_get::get("small_h") != '' ) $_FILES["file_1"]["small_wh"]["h"] = fun_get::get("small_h");
		}
		//是否上传类型限制
		$get_type = fun_get::get("type");
		if( $get_type != '' ) tab_other_attatch::get_perms("attatch_type" , implode("," , $get_type) ) ;
		//是否上传扩展名限制
		$get_ext = fun_get::get("ext");
		if( $get_ext != '' ) tab_other_attatch::get_perms("attatch_ext" , implode("," , $get_ext) ) ;

		$arr_upload = tab_other_attatch::on_upload($_FILES["file_1"]);
		$arr_return['list'] = $arr_upload;
		if( $arr_upload["code"] == 0 ) {
			$arr_return["info"] = fun_format::json($arr_upload);
		} else {
			return $arr_upload;
		}
		return $arr_return;
	}
	/* 取服务器上文件
	 */
	function get_server_upload($base_dir , $dirpath = '') {
		if(!empty($dirpath) && substr($dirpath,0,1) != "/" ) $dirpath = "/" . $dirpath;
		if($dirpath == "/") $dirpath = "";
		$arr_return["pathdir"] = $dirpath;
		$arr_return["dir"] = fun_file::get_dirs( $base_dir  . $dirpath );
		$arr_file = fun_file::get_files( $base_dir . $dirpath );
		$arr_return["file"] = array();
		//获取访问url
		foreach($arr_file as $item) {
			if($base_dir == KJ_DIR_UPLOAD) {
				$item["url"] = KJ_DIR_UPLOAD_UEL . $arr_return["pathdir"] . "/" . $item["name"];
			} else {
				$item["url"] = $arr_return["pathdir"] . "/" . $item["name"];
			}
			$item["url_view"] = fun_get::html_url($item["url"]);
			$arr_return["file"][] = $item;
		}
		$arr= explode("/" , $dirpath);
		$arr_path = array( "<a href=\"javascript:thisjs.opendir('/');\">" . cls_language::get("dir_root" ) . "</a>" );
		$str_path = "";
		foreach($arr as $item) {
			if(empty($item)) continue;
			$str_path .= "/" . $item;
			$arr_path[] = "<a href=\"javascript:thisjs.opendir('" . $str_path . "');\">" . $item . "</a>";
		}
		$arr_return["path"] = implode(" -> " , $arr_path);
		return $arr_return;
	}

	/* 取当前用户所上传的附件列表
	 * 
	 */
	function get_attatch() {
		$arr_return = array("list" => array());
		$obj_db = cls_obj::db();
		$arr_where = array();
		$arr_where_s = array();
		$lng_issearch = 0;
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info(".dialog.attatch"  , $this->app_dir );
		$lng_pagesize = $arr_config_info["pagesize"];
		//取分页信息
		$str_where = "";
		$lng_page = (int)fun_get::get("page");
		$sort = " order by attatch_id desc";
		//取查询参数
		$arr_search_key = array(
			'time1' => fun_get::get("s_time1"),
			'time2' => fun_get::get("s_time2"),
			'key' => fun_get::get("s_key"),
			'type' => fun_get::get("s_type"),
		);
		if( !empty($arr_search_key['type']) && is_array($arr_search_key['type']) ) $arr_search_key["type"] = implode(",",$arr_search_key['type']);
		$arr_return["type"] = explode(",",$arr_search_key["type"]);
		if( fun_is::isdate( $arr_search_key['time1'] ) ) $arr_where_s[] = "attatch_addtime >= '" . strtotime( $arr_search_key['time1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['time2'] ) ) $arr_where_s[] = "attatch_addtime <= '" . fun_get::endtime( $arr_search_key['time2'] ) . "'"; 
		if( !empty($arr_search_key["type"]) )  $arr_where_s[] = "'" . $arr_search_key['type'] . "' like " . $obj_db->concat("%","attatch_type","%"); 
		if( $arr_search_key['key'] != '' ) $arr_where_s[] = "(attatch_filename like '%" . $arr_search_key['key'] . "%' or attatch_ip like '%" . $arr_search_key['key'] . "%')"; 
		$arr_where = array_merge($arr_where , $arr_where_s);
		if(count($arr_where)>0) $str_where = " where " . implode(" and " , $arr_where);

		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."other_attatch" , $str_where , $lng_page , $lng_pagesize);

		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."other_attatch" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$obj_rs["img"] = fun_get::html_url($obj_rs["attatch_path"]);
			$obj_rs["img_small"] = fun_get::html_url($obj_rs["attatch_small"]);
			$arr_return["list"][] = $obj_rs;
		}
		if( count($arr_where_s) > 0 ) $lng_issearch = 1;
		$arr_return['issearch'] = $lng_issearch;
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}
}