<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_other_attatch {
	static $perms;
	static function get_perms($key , $val = -999) {
		if( empty(self::$perms) ) {
			self::$perms["allow_maxsize"] = (int)cls_config::get("allow_maxsize" , "upload");
			self::$perms["allow_ext"] = cls_config::get("allow_ext" , "upload" , array());
			self::$perms["allow_no_ext"] = cls_config::get("allow_no_ext" , "upload" , array());
			self::$perms["need_login"] = cls_config::get("need_login" , "upload" , 1);
			self::$perms["pic_watermark"] = cls_config::get("pic_watermark" , "upload" , 1);
		}
		if($val != -999 ) self::$perms[$key] = $val;
		$return = "";
		if(isset(self::$perms[$key])) $return = self::$perms[$key];
		return $return;
	}
	/** 上传函数
	 *  arr_files = 要上传的附件信息
	 *  参数包括：file自带数组 + small 表示生成略图 , small_wh 数组　表示略图大小，未设置则按系统配置，未配置则生成出错
	 */
	function on_upload( $arr_files ) {
		$arr_return=array("code" => 0 , "msg" => "" , "url" => "" , "url_small" => "");
		//检查是否可以上传
		$arr = self::chk_allow( $arr_files );
		if($arr["code"] != 0) return $arr;
		//保存数据
		$arr_fields=array( 
			"attatch_user_id" => cls_obj::get("cls_user")->uid,
			"attatch_filename" => fun_get::basename($arr_files['name']),
			"attatch_ext"  => end(explode("." , $arr_files['name'])),
			"attatch_size" => $arr_files["size"],
		);
		//取附件类型
		$arr_fields["attatch_type"] = fun_get::file_type( $arr_fields["attatch_ext"] );
		//检查是否限制上传类型
		$attatch_type = self::get_perms("attatch_type");
		if(!empty($attatch_type) && is_array($attatch_type) && !in_array($arr_fields["attatch_type"] , $attatch_type) ) {
			$arr_return["code"] = 403;
			$arr_return["msg"] = cls_language::get("upload_type_err");
			return $arr_return;
		}
		//检查指定允许上传的扩展名
		$attatch_ext = self::get_perms("attatch_ext");
		if(!empty($attatch_ext) && is_array($attatch_ext) && !in_array($arr_fields["attatch_ext"] , $attatch_ext) ) {
			$arr_return["code"] = 403;
			$arr_return["msg"] = sprintf(cls_language::get("upload_ext_err" , "other") , implode("," , $attatch_ext) );
			return $arr_return;
		}

		$arr_path=self::get_upload_path( $arr_fields['attatch_user_id'] , $arr_files['name'] );
		$str_path = $arr_path['path'];
		$arr_fields["attatch_path"] =  $arr_path['url'];
		$str_new_path = $arr_path['path_real'];
		//获取上传路径
		move_uploaded_file($arr_files['tmp_name'], $str_new_path );
		if(file_exists($str_new_path)){
			$arr_return["url"] = $arr_fields["attatch_path"];
			//是否生成缩图
			if( isset($arr_files["small"]) && $arr_files["small"] == 1) {
				$w = $h = 0;
				if( isset($arr_files["small_wh"]) ) {
					$w = $arr_files["small_wh"]["w"];
					$h = $arr_files["small_wh"]["h"];
				} else {
					$w = cls_config::get("pic_autosmall_w" , "upload" , 100);
					$h = cls_config::get("pic_autosmall_h" , "upload" , 80);
				}
				$ext = end(explode(".", $str_path));
				$str_small_name = substr(fun_get::basename($str_new_path) , 0 , -1 * strlen($ext)-1 ) . "_samll." . $ext;
				$str_small_name2 = substr(fun_get::basename($str_path) , 0 , -1 * strlen($ext)-1 ) . "_samll." . $ext;
				$str_small_path = dirname($str_new_path) . "/" . $str_small_name;
				$arr = cls_pic::on_resize($str_new_path , $w , $h , $str_small_path);
				if($arr["code"] == 0 ) {
					$arr_return["url_small"] = dirname($arr_return["url"]) . "/" . $str_small_name2;
					$arr_fields["attatch_small"] = $arr_return["url_small"];
					$arr_fields["attatch_small_name"] = $str_small_name2;
				}
			}
			//是否水印
			if( self::$perms["pic_watermark"] == 1) {
				$pic_watermark_type = cls_config::get("pic_watermark_type" , "upload");
				$pic_watermark_font = cls_config::get("pic_watermark_font" , "upload");
				$pic_watermark_pos = cls_config::get("pic_watermark_pos" , "upload");
				$pic_watermark_quality = cls_config::get("pic_watermark_quality" , "upload");
				if($pic_watermark_type == 'pic') {
					if(substr($pic_watermark_font,0,1)!='/') $pic_watermark_font = '/' . $pic_watermark_font;
					$pic_watermark_font = KJ_DIR_ROOT . $pic_watermark_font;
					$arr = cls_pic::on_pic_watermark( $str_new_path , $pic_watermark_font , $pic_watermark_pos , $pic_watermark_quality );
				} else {
					$pic_watermark_font_path= cls_config::get("pic_watermark_font_path" , "upload");
					if(substr($pic_watermark_font_path,0,1)!='/') $pic_watermark_font_path = '/' . $pic_watermark_font_path;
					$pic_watermark_font_path = KJ_DIR_ROOT . $pic_watermark_font_path;
					$pic_watermark_color = cls_config::get("pic_watermark_color" , "upload");
					$pic_watermark_size = cls_config::get("pic_watermark_size" , "upload" , 18);
					$arr = cls_pic::on_font_watermark($str_new_path , $pic_watermark_font , $pic_watermark_pos , $pic_watermark_size , $pic_watermark_color  ,$pic_watermark_quality , "" , $pic_watermark_font_path);
				}
			}
			$arr = self::on_save($arr_fields);
			if( $arr["code"] != 0 ) return $arr;
		}
		return $arr_return;
	}
	/*
	 * arr_fields : 为字段数据，默认如果包函 id，则为修改，否则为插入
	 * where : 默认为空，用于有时候条件修改
	 */
	static function on_save($arr_fields , $where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		$obj_db = cls_obj::db_w();
		$arr_fields["attatch_addtime"] = TIME;
		$arr_fields["attatch_ip"] = fun_get::ip();
		//插入到用户表
		$arr = $obj_db->on_insert(cls_config::DB_PRE."other_attatch",$arr_fields);
		if($arr['code'] == 0) {
			$arr_return['id'] = $obj_db->insert_id();
			//其它非mysql数据库不支持insert_id 时
			if(empty($arr_return['id'])) {
				$where  = "attatch_addtime='" . $arr_fields['attatch_addtime'] . " and attatch_ip='".$arr_fields['attatch_ip'] . "' and attatch_path='".$arr_fields["attatch_path"]."'";
				$obj_rs = $obj_db->get_one("select attatch_id from ".cls_config::DB_PRE."meal_attatch where ".$where);
				if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['attatch_id'];
			}
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = cls_language::get("db_edit");
		}
		return $arr_return;
	}
	/** 获取上传路径 **/
	function get_upload_path( $msg_uid , $filename ) {
		$arr['path'] = "/attatch/" . $msg_uid . "/" . $filename;
		$arr['url'] = KJ_DIR_UPLOAD_UEL . $arr['path'];
		$arr['path_real'] = fun_file::iconv(KJ_DIR_UPLOAD . $arr['path']);
		if(file_exists($arr['path_real'])) {
			$ext = end(explode(".", $filename));
			$arr = self::get_upload_path($msg_uid , substr($filename , 0 , -1 * (strlen($ext)+1) ) . "_" . TIME . "." . $ext);
		} else {
			fun_file::dir_create(dirname($arr['path_real']));
		}
		return $arr;
	}

	/*
	 * 检查是否允许上传
	 */
	function chk_allow( $arr_files ) {
		$arr_return=array("code" => 0 , "msg" => "");
		//检查是否登录
		$is_need_login = self::get_perms("need_login");
		if( $is_need_login == 1 &&  !cls_obj::get("cls_user")->is_login() ) {
			$arr_return["code"] = 1;
			$arr_return["msg"]  = cls_language::get("no_login");
			return $arr_return;
		}
		//检查是否有文件上传
		if( $arr_files["size"] < 1 ) {
			$arr_return["code"] = 401;
			$arr_return["msg"]  = cls_language::get("upload_err" , 'other');
			return $arr_return;
		}
		//检查上传文件大小
		$lng_maxsize = self::get_perms("allow_maxsize") * 1024 * 1024;
		if( $arr_files["size"] > $lng_maxsize ) {
			$arr_return["code"] = 402;
			$arr_return["msg"]  =  sprintf(cls_language::get("upload_maxsize" , "other") , fun_format::size( $lng_maxsize ));
			return $arr_return;
		}
		//检查扩展名是否允许
		$arr_allow_ext = self::get_perms("allow_ext");
		$arr_no_allow_ext =  self::get_perms("allow_no_ext");
		$ext = strtolower(end(explode("." , $arr_files["name"])));
		//如果在不允许列表中，则返回不允许
		if(in_array($ext , $arr_no_allow_ext)) {
			$arr_return["code"] = 403;
			$arr_return["msg"]  =  sprintf(cls_language::get("upload_no_allow" , "other") , $ext);
			return $arr_return;
		}
		//如果不在允许列表中，则返回不允许
		if(!in_array($ext , $arr_allow_ext)) {
			$arr_return["code"] = 403;
			$arr_return["msg"]  =  sprintf(cls_language::get("upload_only_allow" , "other") , implode("," , $arr_allow_ext) );
			return $arr_return;
		}

	}
}