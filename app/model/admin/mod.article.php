<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_article extends inc_mod_admin {
	/* 按模块查询用户信息并返回数组列表
	 * module : 指定查询模块
	 * isdel : 是否为回收站 , 1:是，0:非
	 */
	function get_pagelist( $arr_where = array() ) {
		$arr_where_s = array();
		$str_where = '';
		$lng_issearch = 0;
		$channel_id = (int)fun_get::get("url_channel_id");
		$arr_where[] = "article_channel_id='" . $channel_id ."'";
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
		$article_about_id = fun_get::get("url_about_id");
		if(!empty($article_about_id)) $arr_where[] = "article_about_id='" . $article_about_id . "'";
		$arr_where = array_merge($arr_where , $arr_where_s);
		if(count($arr_where)>0) $str_where = " where " . implode(" and " , $arr_where);
		$arr_return = $this->sql_list($str_where , (int)fun_get::get('page'));

		if( count($arr_where_s) > 0 ) $lng_issearch = 1;
		$arr_return['issearch'] = $lng_issearch;

		return $arr_return;
	}


	/* 实现按具体条件查询数据表，并返回分页信息
	 * str_where : sql 查询条件 , lng_page : 当前页 
	 */
	function sql_list($str_where = "" , $lng_page = 1) {
		$arr_return = array("list" => array());
		$obj_db = cls_obj::db();
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("article" , $this->app_dir , "article");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info(".article"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];

		$arr_uid = array();
		//取分页信息
		$arr_return["list"] = $arr_list = $arr_topic_id = $arr_uname = $arr_topic = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."article" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . ",folder_pids FROM ".cls_config::DB_PRE."article a left join " . cls_config::DB_PRE . "article_folder b on a.article_folder_id=b.folder_id left join " . cls_config::DB_PRE . "article_channel c on a.article_channel_id=c.channel_id" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			//检查权限
			if($this->this_limit->chk_article($obj_rs["article_channel_id"] , $obj_rs["article_folder_id"] , $obj_rs["folder_pids"])) {
				if(isset($obj_rs["article_addtime"])) $obj_rs["article_addtime"] = date("Y-m-d H:i" , $obj_rs["article_addtime"]);
				if(isset($obj_rs["article_state"])) $obj_rs["article_state"] = array_search($obj_rs["article_state"],tab_article::get_perms("state"));
				if(isset($obj_rs["article_uid"])) $arr_uid[] = $obj_rs["article_uid"];
				if(isset($obj_rs["article_updateuid"])) $arr_uid[] = $obj_rs["article_updateuid"];
				if(isset($obj_rs["article_topic_id"])) $arr_topic_id[] = $obj_rs["article_topic_id"];
				if(!empty($obj_rs["article_pic"])) $obj_rs["article_pic"] = fun_get::html_url($obj_rs["article_pic"]);
				if(!empty($obj_rs["article_pic_big"])) $obj_rs["article_pic_big"] = fun_get::html_url($obj_rs["article_pic_big"]);
				if(empty($obj_rs["article_pic_big"])) $obj_rs["article_pic_big"] = $obj_rs["article_pic"];
				if(empty($obj_rs["article_pic"])) $obj_rs["article_pic"] = $obj_rs["article_pic_big"];
				$arr_list[] = $obj_rs;
			}
		}
		if(count($arr_uid)>0) {
			//根据用户 id 取用户 名
			$arr_uid = array_unique($arr_uid);
			$arr_uname = cls_obj::get("cls_user")->get_user($arr_uid);
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
				if(isset($item["article_uid"])) $item["article_uid"] = array_search($item['article_uid'] , $arr_uname);
				if(isset($item["article_updateuid"])) $item["article_updateuid"] = array_search($item['article_uid'] , $arr_uname);
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
	/* 查询配置表指定id信息
	 */
	function get_editinfo($msg_id) {
		$obj_rs = cls_obj::db()->edit(cls_config::DB_PRE."article" , "article_id='".$msg_id."'");
		if( empty($obj_rs["article_id"]) ) {
			$obj_rs["article_state"]=1;
			$obj_rs["article_channel_id"]=fun_get::get("url_channel_id");
			$obj_rs["article_folder_id"]=fun_get::get("url_folder_id");
			$obj_rs['article_about_id'] = fun_get::get("url_about_id");
		}
		$obj_rs["article_attribute"] = explode("|",$obj_rs["article_attribute"]);
		$obj_rs["article_css"] = explode(";",$obj_rs["article_css"]);
		$obj_rs["pic"] = fun_get::html_url($obj_rs["article_pic"]);
		$obj_rs["pic_big"] = fun_get::html_url($obj_rs["article_pic_big"]);

		if(!empty($obj_rs['article_about_id'])) {
			$obj_rs['article_about_name'] = $this->get_about_name($obj_rs['article_channel_id'] , $obj_rs['article_about_id']);
		}
		return $obj_rs;
	}
	function get_about_name($channel_id , $about_id) {
		$name = '';
		$arr = cls_config::get_data("article_channel" , "id_" . $channel_id );
		if(isset($arr["channel_user_type"]) && $arr["channel_user_type"] == 'shop') {
			$obj_shop = cls_obj::db()->get_one("select shop_name from " . cls_config::DB_PRE . "meal_shop where shop_id='" . $about_id . "'");
			if(!empty($obj_shop)) $name = "来自店铺：" . $obj_shop['shop_name'];
		} else if(isset($arr["channel_user_type"]) && !empty($arr["channel_user_type"])) {
			$obj_user = cls_obj::get("cls_user")->get_user($about_id);
			$name = "来自用户：" . array_search($about_id , $obj_user);
		}
		return $name;
	}
	/* 查询配置表指定id信息
	 */
	function get_editinfo_folder($msg_id) {
		$channel_id = (int)fun_get::get("url_channel_id");
		$obj_rs = cls_obj::db()->edit(cls_config::DB_PRE."article_folder" , "folder_id='".$msg_id."'");
		if( empty($obj_rs["folder_id"]) ) {
			$obj_rs["folder_channel_id"]=fun_get::get("url_channel_id");
			$obj_rs["folder_pid"]=fun_get::get("url_folder_id");
		}
		return $obj_rs;
	}
	/*取频道列表
	 */
	function get_channel_list() {
		$obj_db = cls_obj::db();
		$arr_list = array();
		$obj_result = $obj_db->select("select channel_id,channel_name from " . cls_config::DB_PRE . "article_channel where channel_state>0");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_list[] = $obj_rs;
		}
		return $arr_list;
	}
	/*取频道列表
	 */
	function get_topic_list() {
		$obj_db = cls_obj::db();
		$arr_list = array();
		$obj_result = $obj_db->select("select topic_name,topic_id from " . cls_config::DB_PRE . "article_topic where topic_state>0");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_list[] = $obj_rs;
		}
		return $arr_list;
	}
	/*取频道列表
	 * pid : 上级目录id , isdel : 为０查正常目录，为1查回收站目录 , arr_where : 条件数组 , channel_mode : 频道模式　当为1即图片模式时，分页查询，否则不分页
	 */
	function get_dirlist($pid = 0 , $isdel = 0 , $arr_where = array() , $channel_mode = 0) {
		$arr_return = array();
		$obj_db = cls_obj::db();
		$channel_id = (int)fun_get::get("url_channel_id");
		$arr_where[] = "folder_channel_id='" . $channel_id ."'";
		if($pid>=0) $arr_where[] = "folder_pid='" . $pid . "'";
		$arr_where[] = "folder_isdel='" . $isdel ."'";

		//取查询参数
		$arr_search_key = array(
			'addtime1' => fun_get::get("s_addtime1"),
			'addtime2' => fun_get::get("s_addtime2"),
			'updatetime1' => fun_get::get("s_updatetime1"),
			'updatetime2' => fun_get::get("s_updatetime2"),
			'key' => fun_get::get("s_key"),
		);
		if( fun_is::isdate( $arr_search_key['addtime1'] ) ) $arr_where[] = "folder_addtime >= '" . strtotime( $arr_search_key['addtime1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['addtime2'] ) ) $arr_where[] = "folder_addtime <= '" . fun_get::endtime( $arr_search_key['addtime2'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['updatetime1'] ) ) $arr_where[] = "folder_updatetime >= '" . strtotime( $arr_search_key['updatetime1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['updatetime2'] ) ) $arr_where[] = "folder_updatetime <= '" . fun_get::endtime( $arr_search_key['updatetime2'] ) . "'"; 
		if( $arr_search_key['key'] != '' ) $arr_where[] = "folder_name like '%" . $arr_search_key['key'] . "%'"; 

		$str_where  = " where " . implode(" and " , $arr_where);
		$str_limit = '';
		if($channel_mode == 1 ) {
			//取分页大小
			$arr_config_info = tab_sys_user_config::get_info(".article"  , $this->app_dir);
			$lng_pagesize = $arr_config_info["pagesize"];
			//取分页信息
			$arr_return["list"] = array();
			$page = (int)fun_get::get("page");
			$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."article_folder" , $str_where , $page , $lng_pagesize);
			$str_limit = $arr_return["pageinfo"]["limit"];
		}


		$arr_list = array();
		$obj_result = $obj_db->select("select * from " . cls_config::DB_PRE . "article_folder " . $str_where . $str_limit);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			if($this->this_limit->chk_article($obj_rs["folder_channel_id"] , $obj_rs["folder_id"] , $obj_rs["folder_pids"])) {
				$arr_list[] = $obj_rs;
			}
		}
		if($channel_mode == 1 ) {
			$arr_return["list"] = $arr_list;
			$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
			return $arr_return;
		} else {
			return $arr_list;
		}
	}
	/*取目录路径
	 */
	function get_folder_path($pid) {
		$path = '';
		$obj_rs = cls_obj::db()->get_one("select folder_name,folder_pid,folder_id from " . cls_config::DB_PRE . "article_folder where folder_id='".$pid."'");
		if(!empty($obj_rs)) {
			$path = '<a href="javascript:thisjs.opendir(\'' . $obj_rs["folder_id"] . '\');">' . $obj_rs["folder_name"] . "</a>";
			$folder = $this->get_folder_path($obj_rs["folder_pid"]);
			if(!empty($folder)) $path = $folder . " -> " . $path;
		}
		return $path;
	}
	/*取频道列表
	 */
	function get_folder_select($name = 'folder_id', $default = '' , $no_id = '' , $channel_id = 0) {
		if(empty($channel_id)) $channel_id = (int)fun_get::get("url_channel_id");
		$str_where  = " folder_channel_id='" . $channel_id ."'";
		if(!empty($no_id)) $str_where .= " and folder_id not in(".$no_id.")";
		$arr = tab_article_folder::get_list_layer( 0 , 1 , $str_where);
		$arr_select = array();
		//添加默认
		$arr_select[] = array("val" => 0 , "title" => cls_language::get("layer_top") , "layer" => 0);
		foreach($arr["list"] as $item) {
			$arr_select[] = array("val" => $item['folder_id'] , "title" => $item['folder_name'] , "layer" => $item["layer"]);
		}
		$str = fun_html::select($name , $arr_select ,$default);
		return $str;
	}
	/* 保存数据
	 * 
	 */
	function on_save_article() {
		$arr_return = array("code" => 0 , "id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_fields = array(
			"id"     => (int)fun_get::post("id"),
			"article_title" => fun_get::post("article_title"),
			"article_intro" => fun_get::post("article_intro"),
			"article_content" => fun_get::post("article_content"),
			"article_pic_big"   => fun_get::post("article_pic_big"),
			"article_pic"   => fun_get::post("article_pic"),
			"article_islink"   => fun_get::post("article_islink"),
			"article_linkurl"   => fun_get::post("article_linkurl"),
			"article_attribute"   => implode("|" , fun_get::post("article_attribute" , array())),
			"article_folder_id"   => fun_get::post("article_folder_id"),
			"article_channel_id"   => (int)fun_get::post("article_channel_id"),
			"article_source"   => fun_get::post("article_source"),
			"article_author"   => fun_get::post("article_author"),
			"article_state"   => fun_get::post("article_state"),
			"article_updateuid"   => cls_obj::get("cls_user")->uid,
			"article_css"   => implode(";" , fun_get::post("article_css" , array())),
			"article_tag"   => fun_get::post("article_tag"),
			"article_topic_id"   => fun_get::post("article_topic_id"),
			"article_tpl"   => fun_get::post("article_tpl"),
			"article_key"   => fun_get::post("article_key"),
		);
		if(fun_is::set('article_about_id')) {
			$arr_fields['article_about_id'] = fun_get::get("article_about_id");
		}
		$arr = tab_article::on_save($arr_fields);
		if($arr['code']==0) {
			if(isset($arr['id'])) $arr_return['id'] = $arr['id'];
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	/* 保存目录数据
	 * 
	 */
	function on_save_folder() {
		$arr_return = array("code" => 0 , "id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_fields = array(
			"id"     => (int)fun_get::post("id"),
			"folder_name" => fun_get::post("folder_name"),
			"folder_pic" => fun_get::post("folder_pic"),
			"folder_pid" => fun_get::post("folder_pid"),
			"folder_url" => fun_get::post("folder_url"),
			"folder_tpl" => fun_get::post("folder_tpl"),
			"folder_article_tpl" => fun_get::post("folder_article_tpl")
		);
		if(empty($arr_fields["id"])) {
			$arr_fields["folder_channel_id"] = (int)fun_get::post("url_channel_id");
		}
		$arr = tab_article_folder::on_save($arr_fields);
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
		$arr = cls_obj::db_w()->on_update(cls_config::DB_PRE."article" , array("article_state" => $state_val) , "article_id in(" . $str_id . ")");
		if($arr["code"] != 0) {
			$arr_return["code"] = $arr["code"];
			$arr_return["msg"] = $arr["msg"];
		}
		return $arr_return;
	}
	/* 设置专题
	 */
	function on_topic() {
		$arr_return = array("code" => 0 , "msg" => cls_language::get("set_ok"));
		$arr_id = fun_get::get("selid");
		$topic_val = (int)fun_get::get("topic_val");
		$str_id = fun_format::arr_id($arr_id);
		if(empty($str_id)) {
			$arr_return["code"] = 22;
			$arr_return["msg"] = cls_language::get("no_id");
			return $arr_return;
		}
		$arr = cls_obj::db_w()->on_update(cls_config::DB_PRE."article" , array("article_topic_id" => $topic_val) , "article_id in(" . $str_id . ")");
		if($arr["code"] != 0) {
			$arr_return["code"] = $arr["code"];
			$arr_return["msg"] = $arr["msg"];
		}
		return $arr_return;
	}
	/* 删除或还原指定  文章 数据
	 * isdel 决定是删除还是还原,1为删除，0为回收
	 */
	function on_del_article($isdel = 1) {
		$arr_return = array("code"=>0,"msg" => cls_language::get("delete_ok") );
		if($isdel == 0 ) $arr_return["msg"] = cls_language::get("act_ok") ;
		$str_id = fun_get::get("id");
		$arr_id = fun_get::get("selid");

		//未指定删除id
		if( empty($arr_id) && empty($str_id) ) {
			$arr_return['code'] = 22;//见参数说明表
			($isdel == 1)? $arr_return['msg'] = cls_language::get("delete_no_id") : $arr_return['msg'] = cls_language::get("reback_no_id");
			return $arr_return;
		}

		 //删除文章
		if(!empty($arr_id)) $str_id = $arr_id; //优先考虑 arr_id
		if(!empty($str_id)) {
			$arr = tab_article::on_del($str_id,$isdel);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = $arr['msg'];
			}
		}

		return $arr_return;
	}
	/* 删除或还原指定  目录 数据
	 * isdel 决定是删除还是还原,1为删除，0为回收
	 */
	function on_del_folder($isdel = 1) {
		$arr_return = array("code"=>0,"msg" => cls_language::get("delete_ok") );
		if($isdel == 0 ) $arr_return["msg"] = cls_language::get("act_ok") ;
		$str_fid = fun_get::get("fid");
		$arr_folder_id = fun_get::get("selid2");

		//未指定删除id
		if( empty($arr_folder_id) && empty($str_fid) ) {
			$arr_return['code'] = 22;//见参数说明表
			($isdel == 1)? $arr_return['msg'] = cls_language::get("delete_no_id") : $arr_return['msg'] = cls_language::get("reback_no_id");
			return $arr_return;
		}
		 //删除目录
		if(!empty($arr_folder_id)) $str_fid = $arr_folder_id; //优先考虑 arr_folder_id
		if(!empty($str_fid)) {
			//删除目录
			$arr = tab_article_folder::on_del($str_fid,$isdel);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = $arr['msg'];
			}
		}
		return $arr_return;
	}
	/* 删除指定  id 数据
	 */
	function on_delete_article() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("delete_ok"));
		$str_id = fun_get::get("id");
		$arr_id = fun_get::get("selid");

		//未指定删除id
		if( empty($arr_id) && empty($str_id) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = cls_language::get("delete_no_id");
			return $arr_return;
		}
		//删除文章
		if(!empty($arr_id)) $str_id = $arr_id; //优先考虑 arr_id
		if(!empty($str_id)) {
			$arr = tab_article::on_delete($str_id);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = $arr['msg'];
			}
		}
		return $arr_return;
	}
	/* 删除指定  id 数据
	 */
	function on_delete_folder() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("delete_ok"));
		$str_fid = fun_get::get("fid");
		$arr_folder_id = fun_get::get("selid2");

		//未指定删除id
		if( empty($arr_folder_id) && empty($str_fid) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = cls_language::get("delete_no_id");
			return $arr_return;
		}
		//删除目录
		if(!empty($arr_folder_id)) $str_fid = $arr_folder_id; //优先考虑 arr_id
		if(!empty($str_fid)) {
			$arr = tab_article_folder::on_delete($str_fid);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = $arr['msg'];
			}
		}
		return $arr_return;
	}
	/* 粘贴文章
	 */
	function on_paste_article() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("paste_ok"));
		$ids = fun_get::get("ids");
		if(empty($ids)) return $arr_return;
		$pastetype = fun_get::get("pastetype");
		$channel_id = fun_get::get("channel_id");
		$folder_id = fun_get::get("folder_id");
		$about_id = fun_get::get("about_id");
		if($pastetype == "copy") {
			$arr_return = tab_article::on_copy($ids , $channel_id , $folder_id , '' , $about_id);
		} else {
			$arr_return = tab_article::on_cut($ids , $channel_id , $folder_id , $about_id);
		}
		return $arr_return;
	}
	/* 粘贴目录 
	 */
	function on_paste_folder() {
		$arr_return = array("code"=>0 , "msg"=> cls_language::get("paste_ok"));
		$ids_folder = fun_get::get("ids_folder");
		if(empty($ids_folder)) return $arr_return;
		$pastetype = fun_get::get("pastetype");
		$channel_id = fun_get::get("channel_id");
		$folder_id = fun_get::get("folder_id");
		if($pastetype == "copy") {
			$arr_return = tab_article_folder::on_copy($ids_folder , $channel_id , $folder_id);
		} else {
			$arr_return = tab_article_folder::on_cut($ids_folder , $channel_id , $folder_id);
		}
		return $arr_return;
	}
	//获取指定频道模式
	function get_channel_mode($channel_id) {
		$arr =  cls_config::get_data("article_channel" , "id_" . $channel_id );
		if(isset($arr["channel_mode"])) return $arr["channel_mode"];
		return 0;
	}
	//获取指定频道类型
	function get_channel_user_type($channel_id) {
		$arr =  cls_config::get_data("article_channel" , "id_" . $channel_id );
		if(isset($arr["channel_user_type"])) return $arr["channel_user_type"];
		return "";
	}
	function get_user_type_list($channel_id , $type) {
		//取排序字段
		$obj_db = cls_obj::db();
		$arr_config_info = tab_sys_user_config::get_info("article.user.type"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];
		$page = fun_get::get("page");
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."article" ," where article_channel_id='" . $channel_id . "' group by article_about_id" , (int)$page , $lng_pagesize);
		if($type == 'shop') {
			$sql = "SELECT shop_name as name,shop_id as id FROM " . cls_config::DB_PRE . "meal_shop  where shop_id in(select article_about_id from " . cls_config::DB_PRE ."article where article_channel_id='" . $channel_id . "' group by article_about_id) order by shop_id" . $arr_return['pageinfo']['limit'];
			$obj_result = $obj_db->select($sql);
			while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
				$arr_return["list"][] = $obj_rs;
			}
		} else {
			$sql = "select article_about_id from " . cls_config::DB_PRE ."article where article_channel_id='" . $channel_id . "' group by article_about_id" . $arr_return['pageinfo']['limit'];
			$obj_result = $obj_db->select($sql);
			$arr_uid = array();
			while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
				$arr_uid = $obj_rs['article_about_id'];
			}
			$arr_uname = cls_obj::get("cls_user")->get_user($arr_uid);
			foreach($arr_uname as $item => $key) {
				$arr_return['list'][] = array("id" => $key , "name" => $item);
			}
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}
}