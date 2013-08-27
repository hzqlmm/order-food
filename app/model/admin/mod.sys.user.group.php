<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_sys_user_group extends inc_mod_admin {

	// 获取，移动分组列表
	function get_group_select() {
		$id  = (int)fun_get::get("id");
		$arr = tab_sys_user_group::get_list_layer( 0 , 1 , " group_id!='".$id."'");
		$arr_select = array();
		//添加默认
		$arr_select[] = array("val" => 0 , "title" => cls_language::get("layer_top") , "layer" => 0);
		foreach($arr["list"] as $item) {
			$arr_select[] = array("val" => $item['group_id'] , "title" => $item['group_name'] , "layer" => $item["layer"]);
		}
		$str = fun_html::select("group_id",$arr_select);
		return $str;
	}
	//取当前组菜单
	function menu_list($is_default = false) {
		$arr_return = array();
		$id = fun_get::get("id");
		$str_menu_path = (!empty($id) && !$is_default) ? KJ_DIR_DATA."/menu/group_".$id.".php" : '';
		$arr_dir = explode("/" , $this->perms["app_dir"]);
		$str_dir = $arr_dir[0];
		if( empty($str_menu_path) || !file_exists($str_menu_path) ) $str_menu_path = KJ_DIR_DATA."/menu/".$str_dir.".php";
		$arr_menu = include ( $str_menu_path );
		//检查是否拥有此模块管理权限
		$this->this_limit->init_group($id , $str_dir);
		foreach($arr_menu as $menu => $list) {
			foreach($list as $item) {
				if($this->this_limit->chk_app($item['app'] , $item['app_module'] , $id) ) {
					$item["key"] = md5($item['url']);
					$arr_return[$menu][] = $item;
				}
			}
		}
		return $arr_return;
	}
	//保存自定义菜单
	function on_menu_save() {
		$id = (int)fun_get::get("id");
		if(empty($id)) return array("code" => 500 , "msg" => "保存失败，请刷新重试");
		$arr_app = $this->menu_app($id);
		$arr_menu_id = fun_get::get("menu_id");
		$arr_sort = fun_get::get("sort");
		$arr_name = fun_get::get("name");
		$count = count($arr_menu_id);
		$arr_menu = array();
		for($i = 1; $i < $count ; $i++ ) {
			$key_next = fun_get::get("key_next" . $arr_menu_id[$i]);
			$name_next = fun_get::get("name_next" . $arr_menu_id[$i]);
			$sort_next = fun_get::get("sort_next" . $arr_menu_id[$i]);
			$count_next = count($key_next);
			$arr_next = array();
			for($j = 0; $j < $count_next ; $j++) {
				if(!isset($arr_app[$key_next[$j]])) continue;
				$arr = $arr_app[$key_next[$j]];
				$arr['name'] = $name_next[$j];
				$arr_next['id_' . $sort_next[$j]][] = $arr;
			}
			ksort($arr_next);
			$menu = array();
			foreach($arr_next as $key => $item) {
				foreach($item as $app) {
					$menu[] = $app;
				}
			}
			$arr_menu['id_' .  $arr_sort[$i] ][] = array($arr_name[$i] => $menu);
		}
		ksort($arr_menu);
		foreach($arr_menu as $key => $menu) {
			foreach($menu as $item) {
				foreach($item as $val => $app) {
					$arr_return[$val] = $app;
				}
			}
		}
		//保存
		if(empty($arr_return)) {
			fun_file::file_delete(KJ_DIR_DATA."/menu/group_" . $id . ".php",$val,1);
		} else {
			$val = var_export($arr_return,true);
			$val = '<'.'?php'.chr(10).'return '.$val.";";
			fun_file::file_create(KJ_DIR_DATA."/menu/group_" . $id . ".php",$val,1);
		}
		return array("code" => 0 , "msg" => "保存成功");
	}
	//取app
	function menu_app($id) {
		$arr_return = array();
		$arr_dir = explode("/" , $this->perms["app_dir"]);
		$str_dir = $arr_dir[0];
		$str_menu_path = KJ_DIR_DATA."/menu/".$str_dir.".php";
		$arr_menu = include ( $str_menu_path );
		//检查是否拥有此模块管理权限
		$this->this_limit->init_group($id , $str_dir);
		foreach($arr_menu as $menu => $list) {
			foreach($list as $item) {
				if($this->this_limit->chk_app($item['app'] , $item['app_module'] , $id) ) {
					$key = md5($item['url']);
					$arr_return[$key] = $item;
				}
			}
		}
		return $arr_return;
	}
	function on_move_save() {
		$arr_return = array("code"=>0 , "id"=>0 , "msg" => cls_language::get("save_ok"));
		$id = (int)fun_get::get("id");
		$pid = (int)fun_get::get("group_id");
		if(empty($id)) {
			$arr_return["code"] = 22;
			$arr_return['msg']  = cls_language::get("no_id");
			return $arr_return;
		}
		$arr = tab_sys_user_group::on_move($id , $pid);
		if($arr['code']==0) {
			if(isset($arr['id'])) $arr_return['id'] = $arr['id'];
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	/* 保存数据
	 * 
	 */
	function on_save_all() {
		$arr_return = array("code" => 0 ,"id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_group_name = fun_get::get("group_name");
		$arr_group_sort = fun_get::get("group_sort");
		$arr_group_pid  = fun_get::get("pid");
		$arr_group_id   = fun_get::get("group_id");
		$arr_group_id_layer   = fun_get::get("group_id_layer");

		
		$arr_resave = array();
		$lng_count = count($arr_group_name);
		
		//开始事务
		cls_obj::db_w()->begin("save_group");
		//循环统计已有 id
		$arr_id = array();
		for( $i = 1 ; $i < $lng_count ; $i++) {
			$lng_id = (int)$arr_group_id[$i];
			if($lng_id > 0) $arr_id[] = $lng_id;
		}
		$str_ids = fun_format::arr_id($arr_id);
		if( !empty($str_ids) ) {
			$str_where = "not group_id in(".$str_ids.")";
		} else {
			$str_where = "1>0";//绝对成立条件
		}
		//首先删除没在保存id中的所有记录
		tab_sys_user_group::on_delete(array(),$str_where);
		for( $i = 1 ; $i < $lng_count ; $i++) {
			$arr_fields = array(
				"group_id" => (int)$arr_group_id[$i],
				"group_name" => $arr_group_name[$i],
				"group_sort" => $arr_group_sort[$i]
			);

			if($arr_fields["group_id"]<1 && empty($arr_group_name[$i])) continue;
			//不直接修改 pid,只在新增时保存 pid
			if( $arr_fields["group_id"]<1 && !empty($arr_group_pid[$i]) && isset($arr_resave[$arr_group_pid[$i]]) ) {
				$arr_fields["group_pid"] = $arr_resave[$arr_group_pid[$i]]["id"];
			}
			$arr_resave[$arr_group_id_layer[$i]] = tab_sys_user_group::on_save($arr_fields);
			if($arr_resave[$arr_group_id_layer[$i]]["code"]!=0) {
				cls_obj::db_w()->rollback("save_group");//回滚
				$arr_return['code'] = $arr_resave[$arr_group_id_layer[$i]]["code"];
				$arr_return['msg'] = $arr_resave[$arr_group_id_layer[$i]]["msg"];
				return $arr_return;
			}
		}
		//完成事务
		cls_obj::db_w()->commit("save_group");
		return $arr_return;
	}
	function on_limit_edit() {
		$dir = fun_get::get("url_limit_dir");
		if(empty($dir)) $dir = $this->app_dir;
		$arr_limit = array();
		$id = (int)fun_get::get("id");
		$obj_rs = cls_obj::db()->get_one("select group_limit from ".cls_config::DB_PRE . "sys_user_group where group_id='".$id."'");
		if(isset($obj_rs["group_limit"]) && !empty($obj_rs["group_limit"])) {
			$arr = unserialize($obj_rs["group_limit"]);
			if(isset($arr[$dir])) $arr_limit = $arr[$dir];
		}
		return $arr_limit;
	}
	/*
	 * 保存权限
	 */
	function on_limit_save() {
		$arr_return = array("code" => 0 ,"id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_module = fun_get::get("module");
		$id = (int)fun_get::get("id");
		$arr_limit = array();
		if(is_array($arr_module)) {
			foreach($arr_module as $module) {
				$arr_limit[$module]=array();
				$isall = fun_get::get("all_".$module);
				if(!empty($isall)) {
					$arr_limit[$module]["all"] = true;
				} else {
					$arr_app = fun_get::get($module."_page");
					if(empty($arr_app)) continue;
					foreach($arr_app as $app) {
						$arr_limit[$module]["app_".$app] = array();
						$isall = fun_get::get("all_".$module."_".$app);
						if(!empty($isall)) {
							$arr_limit[$module]["app_".$app]["all"] = true;
						} else {
							$arr_act = fun_get::get($module."_".$app."_act");
							if(empty($arr_act)) continue;
							foreach($arr_act as $act) {
								$arr_limit[$module]["app_".$app]["act_".$act] = 1;
							}
						}
					}
				}
			}
		}
		$dir = fun_get::get("url_limit_dir");
		if(empty($dir)) $dir = $this->app_dir;
		$arr = array();
		$id = (int)fun_get::get("id");
		$obj_rs = cls_obj::db()->get_one("select group_limit from ".cls_config::DB_PRE . "sys_user_group where group_id='".$id."'");
		if(isset($obj_rs["group_limit"]) && !empty($obj_rs["group_limit"])) {
			$arr = unserialize($obj_rs["group_limit"]);
		}
		$arr[$dir] = $arr_limit;
		$str_limit = serialize($arr);
		$obj_db = cls_obj::db_w();
		$arr = $obj_db->on_update(cls_config::DB_PRE . "sys_user_group" , array("group_limit" => $str_limit) , "group_id=" . $id) ;
		if($arr["code"] != 0) {
			$arr_return["code"] = $arr["code"];
			$arr_return["msg"] = cls_language::get("save_err");
		}
		return $arr_return;
	}
	//获取文章频道及目录列表
	function get_limit_article() {
		$obj_db = cls_obj::db();
		$obj_result = $obj_db->select('select channel_id,channel_name from ' . cls_config::DB_PRE . "article_channel");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$obj_rs["next"] = self::get_folder($obj_rs["channel_id"] );
			$arr[] = $obj_rs;
		}
		return $arr;
	}
	//获取文章权限数组
	function get_article_limit($group_id) {
		$arr = array();
		$arr_group = cls_obj::db()->get_one("select group_limit_article from " . cls_config::DB_PRE . "sys_user_group where group_id='" . $group_id . "'");
		if(!empty($arr_group) && !empty($arr_group["group_limit_article"])) {
			$arr = unserialize( $arr_group["group_limit_article"] );
		}
		return $arr;
	}
	//递归获取目录
	function get_folder($channel_id , $pid = 0 , $level = 0) {
		$obj_db = cls_obj::db();
		$arr = array();
		$level++;
		$obj_result = $obj_db->select("select folder_name,folder_id,folder_pids,folder_pid from " . cls_config::DB_PRE . "article_folder where folder_channel_id='". $channel_id . "' and folder_pid='".$pid."' and folder_isdel=0");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$obj_rs["isfirst"] = 0;
			if(empty($arr)) $obj_rs["isfirst"] = 1;
			$obj_rs["level"] = $level * 30;
			$obj_rs["folder_pids"] = str_replace("," , "_" , $obj_rs["folder_pids"]);
			$obj_rs["pids"] = $obj_rs["folder_pids"] . "_" . $obj_rs["folder_id"];
			$arr[] = $obj_rs;
			$arr_x = self::get_folder($channel_id , $obj_rs["folder_id"] , $level);
			if(!empty($arr_x)) $arr = array_merge($arr , $arr_x);
		}
		return $arr;
	}
	//保存文章权限
	/*
	 * 保存权限
	 */
	function on_limit_article_save() {
		$arr_return = array("code" => 0 ,"id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_channel = fun_get::get("channel");
		$id = (int)fun_get::get("id");
		$arr_limit = array();
		foreach($arr_channel as $channel) {
			$isall = fun_get::get("all_" . $channel . "_0");
			if(!empty($isall) ) {
				$arr_limit["c_" . $channel]["all"] = 1;//拥有些频道下所有权限
				continue;
			}
			$arr_folder = fun_get::get("folder_" . $channel , array());
			foreach($arr_folder as $folder) {
				$arr = explode("_" , $folder);
				$folder_id = end($arr);
				$isall = fun_get::get("all_" . $channel . "_" . $folder_id);
				if( !empty($isall) ) {
					$arr_limit["f_" . $folder_id]["all"] = 1;
					continue;
				}
				$arr_limit["f_" . $folder_id] = array();
			}
			$arr_limit["c_".$channel]  = array();
		}
		$str_limit = serialize($arr_limit);
		$obj_db = cls_obj::db_w();
		$arr = $obj_db->on_update(cls_config::DB_PRE . "sys_user_group" , array("group_limit_article" => $str_limit) , "group_id=" . $id) ;
		if($arr["code"] != 0) {
			$arr_return["code"] = $arr["code"];
			$arr_return["msg"] = cls_language::get("save_err");
		}
		return $arr_return;
	}
}