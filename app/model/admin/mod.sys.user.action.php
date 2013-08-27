<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_sys_user_action extends inc_mod_admin {
	/* 按模块查询菜单信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_pagelist() {
		$arr_where = array();
		$arr_where_s = array();
		$str_where = '';
		$lng_issearch = 0;
		//取查询参数
		$arr_search_key = array(
			'addtime1' => fun_get::get("s_addtime1"),
			'addtime2' => fun_get::get("s_addtime2"),
			'user_id' => (int)fun_get::get("s_user_id"),
		);
		if( fun_is::isdate( $arr_search_key['addtime1'] ) ) $arr_where_s[] = "action_addtime >= '" . strtotime( $arr_search_key['addtime1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['addtime2'] ) ) $arr_where_s[] = "action_addtime <= '" . fun_get::endtime($arr_search_key['addtime2']) . "'"; 
		if( $arr_search_key['user_id'] != 0 ) $arr_where_s[] = "action_user_id = '" . $arr_search_key['user_id'] . "'"; 
		//合并查询数组
		$arr_where = array_merge($arr_where , $arr_where_s);
		if(count($arr_where)>0) $str_where = " where " . implode(" and " , $arr_where);
		$arr_return = $this->sql_list($str_where , (int)fun_get::get('page'));

		if( count($arr_where_s) > 0 ) $lng_issearch = 1;
		$arr_return['issearch'] = $lng_issearch;
		return $arr_return;
	}


	/* 实现按具体条件查询数据表，并返回分页信息
	 * str_where : sql 查询条件 , lng_page : 当前页 , lng_pagesize : 分页大小
	 */
	function sql_list($str_where = "" , $lng_page = 1) {
		$arr_return = array("list" => array());
		$obj_db = cls_obj::db();
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("sys.user.action" , $this->app_dir , "sys");
		$arr_cfg_fields["sel"] = substr(str_replace(",user_name," , "," , "," . $arr_cfg_fields["sel"] . ","),1,-1);
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("sys.user.action"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];

		//取分页信息
		$arr_return["list"] = array();
		$arr_uid = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."sys_user_action" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."sys_user_action" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			if(isset($obj_rs["action_addtime"])) $obj_rs["action_addtime"] = date("Y-m-d H:i:s" , $obj_rs["action_addtime"]);
			if(isset($obj_rs["action_key"])) {
				$arr = cls_config::get($obj_rs["action_key"] , 'user.action' , '' , '');
				if(isset($arr["title"])) $obj_rs["action_key"] = $arr["title"];
			}
			$arr_uid[] = $obj_rs['action_user_id'];
			$obj_rs['user_name'] = '';
			$arr_return["list"][] = $obj_rs;
		}
		if(count($arr_uid)>0) {
			$arr = cls_obj::get("cls_user")->get_user($arr_uid);
			$count = count($arr_return["list"]);
			for($i = 0 ; $i < $count ; $i++) {
				$arr_return["list"][$i]['user_name'] = array_search($arr_return["list"][$i]['action_user_id'] , $arr);
			}
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}
	function config_save() {
		$ids = fun_get::get("ids");
		$arr_fields = cls_config::get("" , "user.action" , "" , "");
		foreach($arr_fields as $item => $list) {
			if($list == 0 ) continue;
			foreach($list as $key => $val) {
				if(fun_is::set($key . "_" . $item)) $arr_fields[$item][$key] = fun_get::get($key . "_" . $item);
			}
		}
		$val=var_export($arr_fields,true);
		//取之前的备注
		$cont = file_get_contents(KJ_DIR_DATA."/config/cfg.user.action.php");
		$arr = explode("*/" , $cont);
		if(count($arr)>1) {
			$val = $arr[0] . "*/" . chr(10) . 'return ' . $val . ";";
		} else {
			$val = '<' . '?php' . chr(10) . 'return ' . $val . ";";
		}
		fun_file::file_create(KJ_DIR_DATA."/config/cfg.user.action.php",$val,1);
		return array("code" => 0 , "msg" => "保存成功");
	}
}