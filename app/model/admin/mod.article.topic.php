<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_article_topic extends inc_mod_admin {
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
		$arr_cfg_fields = tab_sys_user_config::get_fields("article.topic" , $this->app_dir , "article");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("article.topic"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];
		//取分页信息
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."article_topic" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."article_topic" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			if(isset($obj_rs["topic_addtime"])) $obj_rs["topic_addtime"] = date("Y-m-d H:i" , $obj_rs["topic_addtime"]);
			if(isset($obj_rs["topic_state"])) $obj_rs["topic_state"] = array_search($obj_rs["topic_state"],tab_article_topic::get_perms("state"));
			$arr_return["list"][] = $obj_rs;
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		$arr_return['state'] = tab_article_topic::get_perms("state");
		return $arr_return;
	}
	/* 查询配置表指定id信息
	 */
	function get_editinfo($msg_id) {
		$obj_rs = cls_obj::db()->edit(cls_config::DB_PRE."article_topic" , "topic_id='".$msg_id."'");
		if( empty($obj_rs["topic_id"]) ) {
			$obj_rs["topic_state"]=1;
		}
		return $obj_rs;
	}
	/* 保存数据
	 * 
	 */
	function on_save() {
		$arr_return = array("code" => 0 , "id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_fields = array(
			"id"     => (int)fun_get::post("id"),
			"topic_name" => fun_get::post("topic_name"),
			"topic_tpl" => fun_get::post("topic_tpl"),
			"topic_state" => fun_get::post("topic_state"),
			"topic_pic" => fun_get::post("topic_pic"),
		);
		$arr = tab_article_topic::on_save($arr_fields);
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
		$arr = cls_obj::db_w()->on_update(cls_config::DB_PRE."article_topic" , array("topic_state" => $state_val) , "topic_id in(" . $str_id . ")");
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
		$arr = tab_article_topic::on_delete($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}


	/* 取指定id 专题 下的文章
	 * 
	 * 
	 */
	function get_articlelist( $arr_where = array() ) {
		$arr_where_s = array();
		$str_where = '';
		$lng_issearch = 0;
		$topic_id = (int)fun_get::get("url_topic_id");
		if(empty($topic_id)) $topic_id = -1;
		$arr_where[] = "article_topic_id='" . $topic_id ."'";
		//取查询参数
		$arr_search_key = array(
			'addtime1' => fun_get::get("s_addtime1"),
			'addtime2' => fun_get::get("s_addtime2"),
			'updatetime1' => fun_get::get("s_updatetime1"),
			'updatetime2' => fun_get::get("s_updatetime2"),
			'state' => (int)fun_get::get("s_state",-999),
			'key' => fun_get::get("s_key"),
			'islink' => fun_get::get("s_islink"),
			'attribute' => fun_get::get("s_attribute"),
		);
		if( fun_is::isdate( $arr_search_key['addtime1'] ) ) $arr_where_s[] = "article_addtime >= '" . strtotime( $arr_search_key['addtime1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['addtime2'] ) ) $arr_where_s[] = "article_addtime <= '" . fun_get::endtime( $arr_search_key['addtime2'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['updatetime1'] ) ) $arr_where_s[] = "article_updatetime >= '" . strtotime( $arr_search_key['updatetime1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['updatetime2'] ) ) $arr_where_s[] = "article_updatetime <= '" . fun_get::endtime( $arr_search_key['updatetime2'] ) . "'"; 
		if( $arr_search_key['state'] != -999 ) $arr_where_s[] = "article_state = '" . $arr_search_key['state'] . "'"; 
		if( $arr_search_key['key'] != '' ) $arr_where_s[] = "(article_title like '%" . $arr_search_key['key'] . "%' or article_source like '%" . $arr_search_key['key'] . "%' or article_author like '%" . $arr_search_key['key'] . "%' or article_tag like '%" . $arr_search_key['key'] . "%')"; 
		if($arr_search_key["islink"] != '' ) $arr_where_s[] = "article_islink='" . $arr_search_key["islink"] . "'";
		if(!empty($arr_search_key["attribute"])) $arr_where_s[] = "article_attribute like '%" . $arr_search_key["attribute"] . "%'";

		$arr_where = array_merge($arr_where , $arr_where_s);
		if(count($arr_where)>0) $str_where = " where " . implode(" and " , $arr_where);
		$arr_return = $this->sql_articlelist($str_where , (int)fun_get::get('page'));

		if( count($arr_where_s) > 0 ) $lng_issearch = 1;
		$arr_return['issearch'] = $lng_issearch;

		return $arr_return;
	}


	/* 实现按具体条件查询数据表，并返回分页信息
	 * str_where : sql 查询条件 , lng_page : 当前页 
	 */
	function sql_articlelist($str_where = "" , $lng_page = 1) {
		$arr_return = array("list" => array());
		$obj_db = cls_obj::db();
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("article" , $this->app_dir , "article");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("article.topic.article"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];

		$arr_uid = array();
		//取分页信息
		$arr_return["list"] = $arr_list = $arr_topic_id = $arr_uname = $arr_topic = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."article" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."article a left join " . cls_config::DB_PRE . "article_folder b on a.article_folder_id=b.folder_id left join " . cls_config::DB_PRE . "article_channel c on a.article_channel_id=c.channel_id" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			if(isset($obj_rs["article_addtime"])) $obj_rs["article_addtime"] = date("Y-m-d H:i" , $obj_rs["article_addtime"]);
			if(isset($obj_rs["article_state"])) $obj_rs["article_state"] = array_search($obj_rs["article_state"],tab_article::get_perms("state"));
			if(isset($obj_rs["article_uid"])) $arr_uid[] = $obj_rs["article_uid"];
			if(isset($obj_rs["article_updateuid"])) $arr_uid[] = $obj_rs["article_updateuid"];
			if(isset($obj_rs["article_topic_id"])) $arr_topic_id[] = $obj_rs["article_topic_id"];
			$arr_list[] = $obj_rs;
		}
		if(count($arr_uid)>0) {
			//根据用户 id 取用户 名
			$arr_uid = array_unique($arr_uid);
			$str_id = implode(",", $arr_uid);
			$obj_result = $obj_db->select("select user_name,user_id from " . cls_config::DB_PRE . "sys_user where user_id in(" . $str_id . ")");
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_uname["id_" . $obj_rs["user_id"]] = $obj_rs["user_name"];
			}
		}
		if(count($arr_topic_id)>0) {
			//根据专题 id 取专题名称
			$arr_topic_id = array_unique($arr_topic_id);
			$str_id = implode(",", $arr_topic_id);
			$obj_result = $obj_db->select("select topic_name,topic_id from " . cls_config::DB_PRE . "article_topic where topic_id in(" . $str_id . ")");
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_topic["id_" . $obj_rs["topic_id"]] = $obj_rs["topic_name"];
			}
		}
		if(count($arr_uname)>0 || count($arr_topic)>0) {
			$arr_x = array();
			foreach($arr_list as $item) {
				if(isset($item["article_uid"]) && isset($arr_uname["id_".$item["article_uid"]])) $item["article_uid"] = $arr_uname["id_".$item["article_uid"]];
				if(isset($item["article_updateuid"]) && isset($arr_uname["id_".$item["article_updateuid"]])) $item["article_updateuid"] = $arr_uname["id_".$item["article_updateuid"]];
				if(isset($item["article_topic_id"]) && isset($arr_topic["id_".$item["article_topic_id"]])) $item["article_topic_id"] = $arr_topic["id_".$item["article_topic_id"]];
				$arr_x[] = $item;
			}
			$arr_list = $arr_x;
		}
		$arr_return["list"] = $arr_list;
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		$arr_return['state'] = tab_article::get_perms("state");
		return $arr_return;
	}
}