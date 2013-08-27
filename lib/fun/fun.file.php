<?php 
/*
 * 版本号：3.0测试版
 *
 * 2012-12-04
 */
class fun_file{
	//创建目录
	static function dir_create($msg_path , $msg_mode = 0777) {
		$msg_path = self::iconv($msg_path);
		$arr_return = array("code" => 0 , "msg" => "");
		$str_path=$msg_path;
		if(substr($str_path, -1) != '/') $str_path = $str_path.'/';
		if(is_dir($str_path)) return $arr_return;
		$arr_temp = explode('/' , $str_path);
		$cur_dir = '';
		$int_max = count($arr_temp) - 1;
		for($i = 0; $i < $int_max; $i++) {
			$cur_dir .= $arr_temp[$i].'/';
			if($cur_dir == '/') continue;
			if(@is_dir($cur_dir)) continue;
			@mkdir($cur_dir, $msg_mode);
		}
		return $arr_return;;
	}

	static function dir_copy($msg_fromdir,$msg_name,$msg_todir) {
		$msg_fromdir = self::iconv($msg_fromdir);
		$msg_todir = self::iconv($msg_todir);
		$msg_name = self::iconv($msg_name);
		$arr_return = array("code" => 0 , "msg" => "");
		$return_val="";
		$str_fromdir = $msg_fromdir;
		$str_todir = $msg_todir;
		if(!is_dir($str_fromdir)){
			$arr_return["code"] = 323;
			$arr_return["msg"] = cls_language::get("no_source_dir");
			return $arr_return;
		}
		if(substr($str_fromdir , -1)!="/") $str_fromdir = $str_fromdir . "/";
		if(substr($str_todir , -1)!="/") $str_todir = $str_todir . "/";
		$i = $ii = 0;
		$str_topath = $str_todir . $msg_name;
		if(!is_dir($str_topath)) self::dir_create($str_topath);
		$arr_list = glob($str_fromdir . '*');
		$str_topath .= "/";
		foreach($arr_list as $item) {
			$str_path = $str_topath . fun_get::basename($item);
			if(file_exists($str_path) && !is_writable($str_path)) self::dir_chmod($str_path);
			if(is_dir($item)) {
				self::dir_copy($item , fun_get::basename($item) , $str_topath);
			}
			else {
				copy($item , $str_path);
				chmod($str_path , 0777);
			}
		}
		return $arr_return;
	}
	static function dir_rename($msg_oldname , $msg_newname)
	{
		$msg_oldname = self::iconv($msg_oldname);
		$msg_newname = self::iconv($msg_newname);
		$arr_return = array("code" => 0 , "msg" => "");
		$return_msg="";
		$str_oldname = $msg_oldname;
		$str_newname = $msg_newname;
		if( !is_dir($str_oldname) ) {
			$arr_return["code"] = 323;
			$arr_return["msg"] = cls_language::get("no_source_dir");
			return $arr_return;
		}
		if( is_dir($str_newname) ) {
			$arr_return["code"] = 322;
			$arr_return["msg"] = cls_language::get("dir_repeat");
			return $arr_return;
		}
		if( !rename($str_oldname , $str_newname) ){
			$arr_return["code"] = 323;
			$arr_return["msg"] = cls_language::get("dir_rename_err");
			return $arr_return;
		}
		return $arr_return;
	}
	//改变文件模式 使允许修改
	static function dir_chmod($msg_dir, $msg_mode = 777, $msg_require = 0) {
		$msg_dir = self::iconv($msg_dir);
		if(strlen($msg_mode) == 3) $msg_mode = '0' . $msg_mode;
		@chmod($msg_dir , $msg_mode);
		if($msg_require) {
			$arr_files = self::get_dir_list($msg_dir);
			foreach($arr_files as $item) {
				@chmod($item, $msg_mode);
			}
		}
	}
	//删除目录
	static function dir_delete($msg_dir) {
		$msg_dir = self::iconv($msg_dir);
		$arr_return = array("code" => 0 , "msg" => "");
		$str_dir = $msg_dir;
		if( !is_dir($str_dir) ) {
			$arr_return["code"] = 323;
			$arr_return["msg"] = cls_language::get("no_source_dir");
			return $arr_return;
		}
		if(substr($str_dir, 0, 1) == '.') {
			$arr_return["code"] = 321;
			$arr_return["msg"] = cls_language::get("dir_noallow_del");
			return $arr_return;
		}
		if(substr($str_dir , -1) != "/") $str_dir = $str_dir . "/";
		$arr_list = glob($str_dir . '*');
		foreach($arr_list as $item) {
			is_dir($item) ? self::dir_delete($item) : @unlink($item);
		}
		if(!@rmdir($str_dir)) {
			$arr_return["code"] = 321;
			$arr_return["msg"] = cls_language::get("dir_del_err");
		}
		return $arr_return;
	}
	/* 获取目录权限
	 *
	 */
	static function dir_limit($msg_dir) {
		$msg_dir = self::iconv($msg_dir);
		$dir = @opendir($msg_dir);
		$mark=0;
		if ($dir === false)	return $mark;
		if (@readdir($dir) !== false) $mark ^= 1; //目录可读 001，目录不可读 000
		@closedir($dir);
		/* 检查目录是否可写 */
		$test_file = KJ_DIR_CACHE."/install_test.php";
		$fp = @fopen($test_file, 'wb');
		if ($fp === false)	return $mark; //如果目录中的文件创建失败，返回不可写。
		if (@fwrite($fp, '//directory access testing.') !== false)	$mark ^= 2; //目录可写可读011，目录可写不可读 010
		@fclose($fp);
		@unlink($test_file);
		return $mark;
	}
	/** 复制文件
	 *  frompath 被复制文件路径, name 被复制文件名称 ,todir 将要粘贴到的路径　　isrename 当文件已经存在，是否重命名 1生成以 复件开始的新名称 0 跳出
	 */
	static function file_copy($msg_frompath , $msg_name , $msg_todir , $msg_isrename = 1)
	{
		$msg_frompath = self::iconv($msg_frompath);
		$msg_name = self::iconv($msg_name);
		$msg_todir = self::iconv($msg_todir);
		$arr_return = array("code" => 0 , "msg" => "");
		$str_topath="";
		$str_frompath = $msg_frompath;
		$str_todir = $msg_todir;
		if( !is_dir($str_todir) ) self::dir_create($str_todir); //目录不存在，则创建
		if( substr($str_todir , -1) != "/" ) $str_todir = $str_todir . "/";
		$str_topath = $str_todir . $msg_name;
		$i = $ii = 0;
		if( !file_exists($str_frompath) ) {
			$arr_return["code"] = 333;
			$arr_return["msg"] = cls_language::get("no_source_file");
			return $arr_return;
		}
		if($msg_isrename == 1 && file_exists($str_topath)) {
			$arr_return["code"] = 332;
			$arr_return["msg"] = cls_language::get("file_repeat");
			return $arr_return;
		} else {
			$copies = self::iconv( cls_language::get("copies") . " " );
			while(file_exists($str_topath)) {
				if($i == 0) {
					$str_topath = $str_todir . $copies . $msg_name;
					$arr_return["name"] = $copies . $msg_name;
				} else {
					$ii = "(".$i.") ";
					$str_topath = $str_todir . $copies . $ii . $msg_name;
					$arr_return["name"] = $copies . $ii . $msg_name;
				}
				$i++;
			}
		}
		if( @copy($str_frompath, $str_topath) != 1) {
			$arr_return["code"] = 331;
			$arr_return["msg"] = cls_language::get("file_copy_err");
		}
		return $arr_return;
	}

	static function file_rename($msg_oldname,$msg_newname) {
		$msg_oldname = self::iconv($msg_oldname);
		$msg_newname = self::iconv($msg_newname);
		$arr_return = array("code" => 0 , "msg" => "");
		$str_oldname = $msg_oldname;
		$str_newname = $msg_newname;
		if( !file_exists($str_oldname) ) {
			$arr_return["code"] = 333;
			$arr_return["msg"] = cls_language::get("no_source_file");
			return $arr_return;
		}
		if( file_exists($str_newname) ) {
			$arr_return["code"] = 332;
			$arr_return["msg"] = cls_language::get("file_repeat");
			return $arr_return;
		}
		if( !@rename($str_oldname,$str_newname) ){
			$arr_return["code"] = 331;
			$arr_return["msg"] = cls_language::get("file_rename_err");
		}
		return $arr_return;
	}
	/** 创建文件
	 *  msgtype : 1 表示当文件所在目录不存在时，创建目录，　否则提示错误
	 */
	static function file_create($msg_path , $msg_content , $msgtype = 0) {
		$arr_return = array("code" => 0 , "msg" => "");
		$str_path = self::iconv($msg_path);
		$str_dir=dirname($msg_path);
		if($msgtype == 1) {
			self::dir_create($str_dir);//目录不存在，则创建
		}
		if(!@file_put_contents($str_path , $msg_content)) {
			$arr_return["code"] = 334;
			$arr_return["msg"] = cls_language::get("file_save_err");
		}
		return $arr_return;
	}

	static function file_delete($msg_file)
	{
		$msg_file = self::iconv($msg_file);
		$arr_return = array("code" => 0 , "msg" => "");
		$str_file = $msg_file;
		if( !file_exists($str_file) ) {
			$arr_return["code"] = 333;
			$arr_return["msg"] = cls_language::get("no_source_file");
			return $arr_return;
		}
		if(!@unlink($str_file)) {
			$arr_return["code"] = 331;
			$arr_return["msg"] = cls_language::get("file_del_err");
		}

		return $arr_return;
	}

	//获取指定路径下，所有文件，继承
	static function get_files_all($msg_path)
	{
		$arr_dir = fun_file::get_dir_list( $msg_path );
		$arr_file = array();
		$arr_dir[] = array("path" => $msg_path);
		foreach($arr_dir as $item) {
			$arr_1 = fun_file::get_files($item['path']);
			for($i=count($arr_1)-1; $i>=0 ; $i--) {
				$arr_1[$i]['path'] = $item['path'] . "/" . $arr_1[$i]["name"];
			}
			$arr_file = array_merge($arr_file,$arr_1);
		}
		return $arr_file;
	}
	//获取指定路径下，所有目录
	static function get_dir_list($msg_path) {
		$arr_dir = self::get_dirs($msg_path);
		foreach($arr_dir as $item) {
			$arr = self::get_dir_list($msg_path . '/' . $item['name']);
			if(count($arr_dir)>0) $arr_dir = array_merge($arr_dir , $arr);
		}
		return $arr_dir;
	}
	//获取指定路径，下级目录
	static function get_dirs($msg_path) {
		$str_path = self::iconv($msg_path);
		if( !is_dir($str_path) ) return array();
		if( substr($str_path , -1 , 1) != "/" ) {
			$str_path = $str_path . "/";
			$msg_path = $msg_path . "/";
		}
		$arr_items=array();
		$arr_x=array();
		if (is_dir($str_path)) {
			if ($obj_dir = opendir($str_path)) {
			   while (($str_file = readdir($obj_dir))) {
				 if(filetype($str_path.$str_file) == "dir" && $str_file != "." && $str_file != "..") {
					 $arr_x = self::get_dir_perms($msg_path. self::iconv($str_file , 1) );
					 $arr_items[]=$arr_x;
				 }
			   }
			   closedir($obj_dir);
			}
		}
		return $arr_items;
	}
	static function iconv($val , $type = 0) {
		$strxxx = $val;
		$val = str_replace(":\\" , ":/" , $val);
		$arr = explode(":/" , $val);
		if(count($arr) > 1) {
			$str_folder = $arr[0];
			$arr = array_splice($arr , 1);
			$val = implode(":/" , $arr);
			$val = $str_folder . ":/" . preg_replace("/[\||:|*|'|?|\"\%]/is", "", $val);
		}
		if(PHP_OS=='WINNT') {
			if($type == 0) {
				return fun_format::utf8_gbk($val);
			} else {
				return fun_format::gbk_utf8($val);
			}
		} else {
			return $val;
		}
	}
	static function get_files($msg_path) {
		$str_path = self::iconv($msg_path);
		if( substr($str_path,-1,1) != "/" ) {
			$str_path = $str_path . "/";
			$msg_path = $msg_path . "/";
		}
		if(!is_dir($str_path)) return array();
		$arr_items=array();
		if ($obj_dir = opendir($str_path)) {
			while( ($str_file = readdir($obj_dir)) !== false) {
				if( filetype($str_path . $str_file) == "file" ) {
				 $arr_items[] = self::get_file_perms($msg_path . self::iconv($str_file , 1));
				}
			}
			closedir($obj_dir);
		}
		return $arr_items;
	}
	static function get_file_perms($msg_path) {
		$str_path = self::iconv($msg_path);
		if(!is_file($str_path)) return array();
		$arr_file=array();
		$arr_file["name"] = fun_get::basename($msg_path);
		$arr_file["size"] = self::get_file_size($msg_path);
		$arr_file["atime"] = date("y-m-d H:i:s" , fileatime($str_path));
		$arr_file["ctime"] = date("y-m-d H:i:s" , filectime($str_path));
		$arr_file["mtime"] = date("y-m-d H:i:s" , filemtime($str_path));
		$arr = explode(".",$str_path);
		$str = $arr[count($arr)-1];
		$arr_file["ext"] = strtolower($str);
		$arr_file["type"] = fun_get::file_type($arr_file["ext"]);
		return $arr_file;
	}
	static function get_dir_perms($msg_path) {
		$arr_dir = array();
		$str_path = self::iconv($msg_path);
		if( !is_dir($str_path) ) return $arr_dir;
		$arr_dir["name"] = fun_get::basename($str_path);
		$arr_dir["atime"] = date("y-m-d H:i:s" , fileatime($str_path));
		$arr_dir["ctime"] = date("y-m-d H:i:s" , filectime($str_path));
		$arr_dir["mtime"] = date("y-m-d H:i:s" , filemtime($str_path));
		$arr_dir["type"] = "dir";
		$arr_dir["path"] = $msg_path;
		return $arr_dir;
	}
	static function get_file_size($msg_path , $msg_flag="K") {
		$msg_path = self::iconv($msg_path);
		$int_bytes = filesize($msg_path);
		$str_flag = strtoupper($msg_flag);
		switch($str_flag)
		{
			case 'K':
				$int_size = round($int_bytes / 1024 , 2);
				break;
			case "M":
				$int_size = round($int_bytes / (1024 * 1024) , 2);
				break;
			case "G":
				$int_size = round($int_bytes / (1024 * 1024 * 1024) , 2);
				break;
		}
		($int_bytes > 0 && $int_size < 1)? $int_size = 1 : "";
		return $int_size.$str_flag;

	}
	static function get_cont($msg_path) {
		$msg_path = self::iconv($msg_path);
		if(!file_exists($msg_path)) return "";
		return file_get_contents($msg_path);
	}
	static function isfile($msg_path) {
		$msg_path = self::iconv($msg_path);
		return is_file($msg_path);
	}
}