<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_article_channel extends inc_mod_admin {
	/* 按模块查询用户信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_pagelist() {
		$str_where = "";
		$arr_return = $this->sql_list($str_where , (int)fun_get::get('page'));

		return $arr_return;
	}

	/* 实现按具体条件查询数据表，并返回分页信息
	 * str_where : sql 查询条件 , lng_page : 当前页
	 */
	function sql_list($str_where = "" , $lng_page = 1) {
		$arr_return = array("list" => array());
		$obj_db = cls_obj::db();
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("article.channel" , $this->app_dir , "article");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("article.channel"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];
		//取分页信息
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."article_channel" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."article_channel" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			if(isset($obj_rs["channel_addtime"])) $obj_rs["channel_addtime"] = date("Y-m-d H:i" , $obj_rs["channel_addtime"]);
			if(isset($obj_rs["channel_state"])) $obj_rs["channel_state"] = array_search($obj_rs["channel_state"],tab_article_channel::get_perms("state"));
			if(isset($obj_rs["channel_html_dirstyle"])) $obj_rs["channel_html_dirstyle"] = array_search($obj_rs["channel_html_dirstyle"],tab_article_channel::get_perms("dirstyle"));
			if(isset($obj_rs["channel_html"])) $obj_rs["channel_html"] = array_search($obj_rs["channel_html"],tab_article_channel::get_perms("ishtml"));
			if(isset($obj_rs["channel_mode"])) $obj_rs["channel_mode"] = array_search($obj_rs["channel_mode"],tab_article_channel::get_perms("mode"));
			$arr_return["list"][] = $obj_rs;
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		$arr_return['state'] = tab_article_channel::get_perms("state");
		return $arr_return;
	}
	/* 查询配置表指定id信息
	 */
	function get_editinfo($msg_id) {
		$obj_rs = cls_obj::db()->edit(cls_config::DB_PRE."article_channel" , "channel_id='".$msg_id."'");
		if( empty($obj_rs["channel_id"]) ) {
			$obj_rs["channel_state"]=1;
		}
		return $obj_rs;
	}
	/* 保存数据
	 * 
	 */
	function on_save() {
		$arr_return = array("code" => 0 , "id"=>0 , "msg" => cls_language::get("save_ok"));
		$channel_user_type = (fun_get::get("usertype")!='') ? fun_get::post("channel_user_type") : '';
		$arr_fields = array(
			"id"     => (int)fun_get::post("id"),
			"channel_name" => fun_get::post("channel_name"),
			"channel_html" => fun_get::post("channel_html"),
			"channel_html_dir" => fun_get::post("channel_html_dir"),
			"channel_html_dirstyle"   => fun_get::post("channel_html_dirstyle"),
			"channel_html_ext"   => fun_get::post("channel_html_ext"),
			"channel_state"   => fun_get::post("channel_state"),
			"channel_tpl"   => fun_get::post("channel_tpl"),
			"channel_article_tpl"   => fun_get::post("channel_article_tpl"),
			"channel_folder_tpl"   => fun_get::post("channel_folder_tpl"),
			"channel_mode"   => fun_get::post("channel_mode" , 0),
			"channel_key"   => fun_get::post("channel_key"),
			"channel_user_type"   => $channel_user_type,
		);
		$arr = tab_article_channel::on_save($arr_fields);
		if($arr['code']==0) {
			if(isset($arr['id'])) $arr_return['id'] = $arr['id'];
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}

	/* 设置状态
	 */
	function on_state() {
		$arr_return = array("code" => 0 , "msg" => cls_language::get("set_ok"));
		$arr_id = fun_get::get("selid");
		$state_val = (int)fun_get::get("state_val");
		$str_id = fun_format::arr_id($arr_id);
		if(empty($str_id)) {
			$arr_return["code"] = 22;
			$arr_return["msg"] = cls_language::get("no_id");
			return $arr_return;
		}
		$arr = cls_obj::db_w()->on_update(cls_config::DB_PRE."article_channel" , array("channel_state" => $state_val) , "channel_id in(" . $str_id . ")");
		if($arr["code"] != 0) {
			$arr_return["code"] = $arr["code"];
			$arr_return["msg"] = $arr["msg"];
		}
		return $arr_return;
	}
	/* 删除指定  id 数据
	 */
	function on_delete() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("delete_ok"));
		$str_id = fun_get::get("id");
		$arr_id = fun_get::get("selid");
		if( empty($arr_id) && empty($str_id) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = cls_language::get("delete_no_id");
			return $arr_return;
		}
		if(!empty($arr_id)) $str_id = $arr_id; //优先考虑 arr_id
		$arr = tab_article_channel::on_delete($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}

}