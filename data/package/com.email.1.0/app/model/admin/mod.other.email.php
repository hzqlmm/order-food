<?php
/**
 * 菜单模型类 关联表名：other_email
 * 
 */
class mod_other_email extends inc_mod_admin {
	/* 按模块查询菜单信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_pagelist($type) {
		$arr_where = array("email_type='" . $type . "'");
		$arr_where_s = array();
		$str_where = '';
		$lng_issearch = 0;
		//取查询参数
		$arr_search_key = array(
			'email_title' => fun_get::get("s_key"),
		);
		if( !empty($arr_search_key['email_title']) ) $arr_where_s[] = "email_title like '%" . $arr_search_key['email_title'] . "%'"; 
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
	function sql_list($str_where = "" , $lng_page = 1 , $lng_pagesize = 10) {
		$arr_return = array("list" => array());
		$obj_db = cls_obj::db();
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("other.email" , $this->app_dir , "other");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("other.email"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		//取分页信息
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."other_email" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."other_email" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			if(isset($obj_rs["email_addtime"]) && !empty($obj_rs["email_addtime"])) $obj_rs["email_addtime"] = date("Y-m-d H:i:s" , $obj_rs["email_addtime"]);
			if(isset($obj_rs["email_account_mode"])) $obj_rs["email_account_mode"] = array_search($obj_rs["email_account_mode"] , tab_other_email::get_perms("account_mode"));
			$arr_return["list"][] = $obj_rs;
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}

	/* 查询配置表指定id信息
	 * msg_id : sys_config 表中 config_id
	 */
	function get_editinfo($msg_id) {
		$obj_rs = cls_obj::db()->edit(cls_config::DB_PRE."other_email" , "email_id='".$msg_id."'");
		if( empty($obj_rs["email_id"]) ) {
			$obj_rs["email_attachment"] = array();
		} else {
			(!empty($obj_rs["email_attachment"]))? $obj_rs["email_attachment"] = explode("|" , $obj_rs["email_attachment"]) : $obj_rs["email_attachment"] = array();
			if(!empty($obj_rs["email_serverinfo"])) $obj_rs["email_serverinfo"] = unserialize($obj_rs["email_serverinfo"]);
			if(!empty($obj_rs["email_userinfo"])) $obj_rs["email_userinfo"] = unserialize($obj_rs["email_userinfo"]);
		}
		//初始化服务器信息
		if(empty($obj_rs["email_serverinfo"]) || !isset($obj_rs["email_serverinfo"]["host"])) {
			$obj_rs["email_serverinfo"] = array("type"=>0 , "host" => cls_config::get("host" , 'email'),"port" => cls_config::get("port" , 'email' , 25),"from" => cls_config::get("from" , 'email'),"fromname" => cls_config::get("fromname" , 'email'),"username" => cls_config::get("username" , 'email'),"password" => cls_config::get("password" , 'email'));
			if(empty($obj_rs["email_serverinfo"]["host"])) $obj_rs["email_serverinfo"]['type'] = 1;
		}
		//初始化用户查询条件
		if(empty($obj_rs["email_userinfo"]) || !isset($obj_rs["email_userinfo"]["regtime1"])) {
			$obj_rs["email_userinfo"] = array("regtime1"=>"" , "regtime2" => '',"logintime1" => '',"logintime2" => '',"type" => '',"state" => '-999',"key" => '');
		}
		return $obj_rs;
	}

	/* 保存数据
	 * 
	 */
	function on_save() {
		$arr_return = array("code" => 0 , "id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_fields = array(
			"email_id"=>fun_get::post("id"),
			"email_to"=>fun_get::post("email_to"),
			"email_account_mode"=>fun_get::post("email_account_mode"),
			"email_account_dir"=>fun_get::post("email_account_dir"),
			"email_title"=>fun_get::post("email_title"),
			"email_cont"=>fun_get::post("email_cont"),
			"email_type"=>fun_get::get("email_type"),
		);
		$arr_fields["email_serverinfo"] = array(
			"type" => fun_get::post("serverinfo_type"),
			"host" => fun_get::post("serverinfo_host"),
			"port" => fun_get::post("serverinfo_port"),
			"from" => fun_get::post("serverinfo_from"),
			"fromname" => fun_get::post("serverinfo_fromname"),
			"username" => fun_get::post("serverinfo_username"),
			"password" => fun_get::post("serverinfo_password"),
		);
		$arr_fields["email_userinfo"] = array(
			"regtime1" => fun_get::post("userinfo_regtime1"),
			"regtime2" => fun_get::post("userinfo_regtime2"),
			"logintime1" => fun_get::post("userinfo_logintime1"),
			"logintime2" => fun_get::post("userinfo_logintime2"),
			"type" => fun_get::post("userinfo_type"),
			"state" => fun_get::post("userinfo_state"),
			"key" => fun_get::post("userinfo_key"),
		);
		$email_attachment=fun_get::post("email_attachment");
		foreach($email_attachment as $item) {
			if(!empty($item)) $arr_fields["email_attachment"][] = $item;
		}
		$arr = tab_other_email::on_save($arr_fields);
		if($arr['code']==0) {
			if(isset($arr['id'])) $arr_return['id'] = $arr['id'];
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}

	/* 删除指定  ads_id 数据
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
		$arr = tab_other_email::on_delete($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	/* 取指定id邮件发送信息
	 *
	 */
	function get_send_info() {
		$id = (int)fun_get::get("id");
		$page = (int)fun_get::get("page");
		$obj_rs = cls_obj::db()->get_one("select * from " . cls_config::DB_PRE . "other_email where email_id='" . $id . "'");
		$total_file = 0;
		$arr_send_to = array();
		if($obj_rs["email_account_mode"] == 0) {//默认模式
			$arr_send_to = explode(";" , $obj_rs["email_to"]);
		} else if($obj_rs["email_account_mode"] == 1){//文件模式
			$str_dir = KJ_DIR_ROOT . $obj_rs["email_account_dir"];
			if(is_dir($str_dir)) {
				$arr_files = fun_file::get_files($str_dir);
				$total_file = count($arr_files);
			}
		} else if($obj_rs['email_account_mode'] == 2){//用户模式
			$arr_search_key = array();
			if(!empty($obj_rs["email_userinfo"])) $arr_search_key = unserialize($obj_rs["email_userinfo"]);
			$str_where = $this->get_user_info_where($arr_search_key);
			$pageinfo = cls_obj::db()->get_pageinfo(cls_config::DB_PRE."sys_user" , $str_where , 1 , 100);
			$total_file = $pageinfo['pages'];
		}
		$arr_return = array(
			'code' => 0,
			'total_file' => $total_file,
			'send_to' => $arr_send_to,
			'file_path' => $obj_rs["email_account_dir"],
			'title' => $obj_rs["email_title"],
			'mode' => $obj_rs['email_account_mode'],
		);
		//发送次数加一
		cls_obj::db_w()->on_exe("update " . cls_config::DB_PRE . "other_email set email_num=email_num+1 where email_id='" . $id . "'");
		return $arr_return;
	}
	/* 发送邮件
	 *
	 */
	function on_send() {
		$arr_return = array('code'=>0 , 'msg' => '');
		$id = (int)fun_get::get("id");
		$send_to = fun_get::get("send_to");
		$obj_email = cls_obj::db()->get_one("select * from " . cls_config::DB_PRE . "other_email where email_id='" . $id . "'");
		if(empty($obj_email)) return array("code" => 500 , "msg" => "发送邮件不存在");
		$arr_serverinfo = array();
		if(!empty($obj_rs["email_serverinfo"])) $arr_serverinfo = unserialize($obj_rs["email_serverinfo"]);
		if($obj_email["email_account_mode"] == 1) {
			//设置发送服务器信息
			cls_obj::get("cls_com")->email('get_perms' , array("serverinfo" , $arr_serverinfo));
		}
		$arr_return = cls_obj::get("cls_com")->email('send' , array('to_mail' => $send_to , 'title' => $obj_email['email_title'] , 'content' => fun_get::filter($obj_email['email_cont'],true)));
		return $arr_return;
	}
	/* 取指定路径收件箱信息
	 *
	 */
	function get_send_file_info() {
		$str_dir = KJ_DIR_ROOT . fun_get::get("path");
		$page = (int)fun_get::get("page");
		$arr_send_to = array();
		if(is_dir($str_dir)) {
			$arr_files = fun_file::get_files($str_dir);
			$total_file = count($arr_files);
			if($page <= $total_file) {
				$str_file = $str_dir . "/" . $page . ".txt";
				if(is_file($str_file)) {
					$arr_send_to = explode(chr(10) , str_replace(chr(13) , chr(10) , file_get_contents($str_file)));
				}
			}
		}
		$arr_return = array(
			'code' => 0,
			'send_to' => $arr_send_to,
		);
		return $arr_return;
	}
	/* 取指定条件用户email信息
	 *
	 */
	function get_send_user_info() {
		$id = (int)fun_get::get("id");
		$page = (int)fun_get::get("page");
		$obj_db = cls_obj::db();
		$obj_rs = $obj_db->get_one("select * from " . cls_config::DB_PRE . "other_email where email_id='" . $id . "'");
		if(empty($obj_rs)) return array("code" => 500 , "msg" => "发送邮件不存在");

		$arr_search_key = array();
		if(!empty($obj_rs["email_userinfo"])) $arr_search_key = unserialize($obj_rs["email_userinfo"]);
		$str_where = $this->get_user_info_where($arr_search_key);
		$pageinfo = $obj_db->get_pageinfo(cls_config::DB_PRE."sys_user" , $str_where , $page , 100);
		//取用户email
		$arr_email = array();
		$obj_result = $obj_db->select("SELECT user_email FROM ".cls_config::DB_PRE."sys_user" . $str_where . $pageinfo['limit']);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_email[] = $obj_rs['user_email'];
		}
		$arr_return = array(
			'code' => 0,
			'send_to' => $arr_email,
		);
		return $arr_return;
	}
	function get_user_info_where($arr_search_key) {
		$arr_where_s = array("user_email!=''");
		if( fun_is::isdate( $arr_search_key['regtime1'] ) ) $arr_where_s[] = "user_regtime >= '" . strtotime( $arr_search_key['regtime1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['regtime2'] ) ) $arr_where_s[] = "user_regtime <= '" . fun_get::endtime( $arr_search_key['regtime2'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['logintime1'] ) ) $arr_where_s[] = "user_logintime >= '" . $arr_search_key['logintime1'] . "'"; 
		if( fun_is::isdate( $arr_search_key['logintime2'] ) ) $arr_where_s[] = "user_logintime <= '" . date("Y-m-d H:i:s", fun_get::endtime( $arr_search_key['logintime2'] )) . "'"; 
		if( $arr_search_key['type'] != '' ) $arr_where_s[] = "user_type = '" . $arr_search_key['type'] . "'"; 
		if( $arr_search_key['state'] != -999 ) $arr_where_s[] = "user_state = '" . $arr_search_key['state'] . "'"; 
		if( $arr_search_key['key'] != '' ) $arr_where_s[] = "(user_name like '%" . $arr_search_key['key'] . "%' or user_email like '%" . $arr_search_key['key'] . "%' or user_netname like '%" . $arr_search_key['key'] . "%')";
		$str_where = '';
		if(count($arr_where_s)>0) $str_where = " where " . implode(" and " , $arr_where_s);
		return $str_where;
	}
}
?>