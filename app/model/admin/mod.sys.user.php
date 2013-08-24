<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_sys_user extends inc_mod_admin {
	/* 按模块查询用户信息并返回数组列表
	 * module : 指定查询模块
	 * isdel : 是否为回收站 , 1:是，0:非
	 */
	function get_pagelist($is_del = 0) {
		$arr_where = array('user_isdel=' . $is_del);
		$arr_where_s = array();
		$str_where = '';
		$lng_issearch = 0;
		//取查询参数
		$arr_search_key = array(
			'regtime1' => fun_get::get("s_regtime1"),
			'regtime2' => fun_get::get("s_regtime2"),
			'logintime1' => fun_get::get("s_logintime1"),
			'logintime2' => fun_get::get("s_logintime2"),
			'type' => fun_get::get("s_type"),
			'state' => (int)fun_get::get("s_state",-999),
			'key' => fun_get::get("s_key"),
		);
		if( fun_is::isdate( $arr_search_key['regtime1'] ) ) $arr_where_s[] = "user_regtime >= '" . strtotime( $arr_search_key['regtime1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['regtime2'] ) ) $arr_where_s[] = "user_regtime <= '" . fun_get::endtime( $arr_search_key['regtime2'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['logintime1'] ) ) $arr_where_s[] = "user_logintime >= '" . $arr_search_key['logintime1'] . "'"; 
		if( fun_is::isdate( $arr_search_key['logintime2'] ) ) $arr_where_s[] = "user_logintime <= '" . date("Y-m-d H:i:s" , fun_get::endtime( $arr_search_key['logintime2'] ) ) . "'"; 
		if( $arr_search_key['type'] != '' ) $arr_where_s[] = "user_type = '" . $arr_search_key['type'] . "'"; 
		if( $arr_search_key['state'] != -999 ) $arr_where_s[] = "user_state = '" . $arr_search_key['state'] . "'"; 
		if( $arr_search_key['key'] != '' ) $arr_where_s[] = "(user_email like '%" . $arr_search_key['key'] . "%' or user_netname like '%" . $arr_search_key['key'] . "%')"; 
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
		//相关属性
		$arr_return["state"] = tab_sys_user::get_perms("state");
		$arr_type = tab_sys_user::get_perms("type");
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("sys.user" , $this->app_dir , "sys");
		//取除 user_name 字段
		$arr_cfg_fields["sel"] = substr(str_replace(",user_name," , "," , "," . $arr_cfg_fields["sel"] . ","),1,-1);
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("sys.user"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];
		//取分页信息
		$arr_uid = array();
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."sys_user" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."sys_user a left join ".cls_config::DB_PRE."sys_user_group b on a.user_group_id=b.group_id" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			if(isset($obj_rs["user_state"])) {
				$obj_rs["state:style"] = $this->get_state_style($obj_rs["user_state"]);
				$obj_rs["user_state"] = array_search($obj_rs["user_state"],$arr_return["state"]);
				if(!empty($obj_rs["state:style"])) $obj_rs["user_state"] = "<font ".$obj_rs["state:style"].">" . $obj_rs["user_state"] . "</font>";
			}
			if(isset($obj_rs["user_type"])) $obj_rs["user_type"] = array_search($obj_rs["user_type"],$arr_type);
			if(isset($obj_rs["user_regtime"])) $obj_rs["user_regtime"] = date("Y-m-d H:i:s" , $obj_rs["user_regtime"]);
			if(isset($obj_rs["user_sex"])) $obj_rs["user_sex"] = fun_get::sex($obj_rs["user_sex"]);
			$arr_uid[] = $obj_rs['user_id'];
			$arr_return["list"][] = $obj_rs;
		}
		if(count($arr_uid)>0) {
			$user_info = cls_obj::get("cls_user")->get_user($arr_uid);
			$count = count($arr_return["list"]);
			for($i = 0 ; $i < $count ; $i++) {
				$arr_return["list"][$i]['user_name'] = array_search($arr_return["list"][$i]['user_id'] , $user_info);
			}
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}

	/* 查询配置表指定id信息
	 * msg_id : sys_config 表中 config_id
	 */
	function get_editinfo($msg_id) {
		$obj_rs = cls_obj::db()->edit(cls_config::DB_PRE."sys_user" , "user_id='".$msg_id."'");
		if( empty($obj_rs["user_id"]) ) {
			$obj_rs["user_state"]=1;
			$obj_rs["user_name"] = '';
		} else {
			$arr = cls_obj::get("cls_user")->get_user($obj_rs['user_id']);
			$obj_rs['user_name'] = array_search( $obj_rs['user_id'] , $arr );
		}
		$obj_rs["html_group"] = $this->get_group_select("user_group_id" , $obj_rs["user_group_id"]);
		$obj_rs["html_depart"] = $this->get_depart_select("user_depart_id" , $obj_rs["user_depart_id"]);
		return $obj_rs;
	}

	/* 获取，用户分组列表 select组件
	 * name : 组件名称 , default : 默认选择值
	 */
	function get_group_select($name = 'user_group_id' , $default = '') {
		$arr = tab_sys_user_group::get_list_layer( 0 , 1);
		$arr_select = array();
		$arr_select[] = array("val" => '' , "title" => '' , "layer" => 1);
		foreach($arr["list"] as $item) {
			$arr_select[] = array("val" => $item['group_id'] , "title" => $item['group_name'] , "layer" => $item["layer"]);
		}
		$str = fun_html::select($name , $arr_select , $default);
		return $str;
	}
	/* 获取，用户部门列表 select组件
	 * name : 组件名称 , default : 默认选择值
	 */
	function get_depart_select($name = 'user_depart_id' , $default = '') {
		$arr = tab_sys_user_depart::get_list_layer( 0 , 1);
		$arr_select = array();
		$arr_select[] = array("val" => '' , "title" => '' , "layer" => 1);
		foreach($arr["list"] as $item) {
			$arr_select[] = array("val" => $item['depart_id'] , "title" => $item['depart_name'] , "layer" => $item["layer"]);
		}
		$str = fun_html::select($name , $arr_select , $default);
		return $str;
	}

	/* 保存数据
	 * 
	 */
	function on_save() {
		$arr_return = array("code" => 0 , "id"=>0 , "msg" => cls_language::get("save_ok"));
		$user_id = (int)fun_get::post("id");
		$user_pwd = '';
		if(empty($user_id)) {
			//注册用户
			$arr = cls_obj::get("cls_user")->on_reg(
				array("user_name" => fun_get::post("user_name") , "user_pwd" => fun_get::post("user_pwd") , "user_email" => fun_get::post("user_email"))
			);
			if($arr['code'] != 0) return $arr;
			$user_id = $arr['id'];
		} else {
			$user_pwd = fun_get::post("user_pwd");
		}
		$arr_fields = array(
			"id"     => $user_id,
			"user_type" => fun_get::post("user_type"),
			"user_state"   => fun_get::post("user_state"),
			"user_email"   => fun_get::post("user_email"),
			"user_netname"   => fun_get::post("user_netname"),
			"user_realname"    => fun_get::post("user_realname"),
			"user_sex"  => fun_get::post("user_sex"),
			"user_birthday"  => fun_get::post("user_birthday"),
			"user_house_location"  => fun_get::post("user_house_location"),
			"user_location"  => fun_get::post("user_location"),
			"user_tel"  => fun_get::post("user_tel"),
			"user_mobile"  => fun_get::post("user_mobile"),
			"user_address"  => fun_get::post("user_address"),
			"user_group_id"  => (int)fun_get::post("user_group_id"),
			"user_depart_id"  => (int)fun_get::post("user_depart_id")
		);
		$arr = tab_sys_user::on_save($arr_fields);
		if($arr['code']==0) {
			if(!empty($user_pwd)) {
				//修改密码
				$arr = cls_obj::get("cls_user")->on_update_pwd('' , $user_pwd , $user_id , false );
				if($arr['code'] != 0) return $arr;
			}
			if(isset($arr['id'])) $arr_return['id'] = $arr['id'];
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}

	/* 删除指定  user_id 数据
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
		//删除用户
		$arr = cls_obj::get("cls_user")->delete_user($str_id);
		if($arr['code'] != 0) return $arr;
		//删除用户信息
		$arr = tab_sys_user::on_delete($str_id);
		if($arr['code'] != 0) return $arr;
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
		$arr = cls_obj::db_w()->on_update(cls_config::DB_PRE."sys_user" , array("user_state" => $state_val) , "user_id in(" . $str_id . ")");
		if($arr["code"] != 0) {
			$arr_return["code"] = $arr["code"];
			$arr_return["msg"] = $arr["msg"];
		}
		return $arr_return;
	}
	/* 清除用户配置
	 */
	function on_clear_config() {
		$arr_return = array("code" => 0 , "msg" => cls_language::get("clear_ok"));
		$arr_id = fun_get::get("selid");
		$config_val = fun_get::get("config_val");
		$str_id = fun_format::arr_id($arr_id);
		if(empty($str_id)) {
			$arr_return["code"] = 22;
			$arr_return["msg"] = cls_language::get("no_id");
			return $arr_return;
		}
		if(empty($config_val)) return $arr_return;
		if(count($config_val)==2) {
			//清除所有，删除行
			$arr = tab_sys_user_config::on_delete($str_id);
		} else {
			$arr_fields = array();
			if(in_array(1,$config_val)) $arr_fields['config_fields'] = "";
			if(in_array(2,$config_val)) $arr_fields['config_info'] = "";
			if(empty($arr_fields)) return array('code' => 22 , 'msg' => cls_language::get("no_id"));
			$arr = cls_obj::db_w()->on_update(cls_config::DB_PRE."sys_user_config" , $arr_fields , "config_user_id in(" . $str_id . ")");
		}
		if($arr["code"] != 0) {
			$arr_return["code"] = $arr["code"];
			$arr_return["msg"] = $arr["msg"];
		}
		return $arr_return;
	}
}