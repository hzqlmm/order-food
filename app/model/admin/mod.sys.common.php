<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_sys_common extends inc_mod_admin {
	/* 当前用户修改密码
	 *
	 */
	function on_update_pwd() {
		$oldpwd = fun_get::get("oldpwd");
		$newpwd = fun_get::get("newpwd");
		$arr_return = cls_obj::get("cls_user")->on_update_pwd($oldpwd , $newpwd);
		return $arr_return;
	}
	/* 更新缓存
	 * 
	 */
	function on_clear_cache() {
		$arr_return = array("code" => "" , "msg" => "清除完成");
		$type = fun_get::get("type");
		if(!is_array($type)) $type = array($type);
		//清除模板
		if( in_array('1',$type) ) {
			$arr_return = fun_file::dir_delete(KJ_DIR_CACHE . "/view");
		}
		//清除数据
		if( in_array('2',$type) ) {
			$arr_return = fun_file::dir_delete(KJ_DIR_CACHE . "/data");
		}
		//清除配置
		if( in_array('3',$type) ) {
			$arr_return = tab_sys_config::on_refresh();
		}
		//过期日志
		if( in_array('4',$type) ) {
			//管理日志
			$lng_num = cls_config::get("admin_log" , 'cache');
			if(empty($lng_num)) $lng_num = 30;
			$lng_time  = TIME - 86400*$lng_num;
			$arr = tab_sys_user_log::on_delete( '' , "log_addtime<'" . $lng_time . "'" );
			//系统日志，获取所有目录
			$arr = fun_file::get_dir_list(KJ_DIR_DATA . "/error");
			$lng_num =  cls_config::get("sys_log " , 'cache');
			if(empty($lng_num)) $lng_num = 5;
			foreach($arr as $item) {
				$arr_file = fun_file::get_files($item['path']);
				$arr_file_sort = array();
				foreach($arr_file as $file) {
					$arr_file_sort[$file["mtime"]] = $item['path']."/".$file["name"];
				}
				krsort($arr_file_sort);
				$i = 0;
				foreach($arr_file_sort as $file=>$key) {
					//删除过期日志文件
					if( $i >= $lng_num) fun_file::file_delete($key);
					$i++;
				}
			}
		}
		//多余备份
		if( in_array('5',$type) ) {
			//清除数据备份,只删除系统自动命名的备份目录，以日期格式识别
			$lng_num =  cls_config::get("data_back " , 'cache');
			if(empty($lng_num)) $lng_num = 2;
			$arr = fun_file::get_dirs(KJ_DIR_DATA . "/database/bak");
			$arr_dir = array();
			foreach($arr as $item) {
				if(preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{4}$/is" , $item["name"])) {//匹配日期目录名
					$arr_dir[$item['mtime']] = $item['path'];
				}
			}
			krsort($arr_dir);
			$i = 0;
			foreach($arr_dir as $file=>$key) {
				//删除过期日志文件
				if( $i >= $lng_num) fun_file::dir_delete($key);
				$i++;
			}
		}
		return $arr_return;
	}
}