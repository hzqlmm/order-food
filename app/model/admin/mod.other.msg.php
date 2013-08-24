<?php
/**
 * 区域 关联表名：sys_area
 * 
 */
class mod_other_msg extends inc_mod_admin {

	function get_pagelist($type) {
		$arr_where = array('msg_type=' . $type);
		$arr_where_s = array();
		$str_where = '';
		$lng_issearch = 0;
		//取查询参数
		$arr_search_key = array(
			'time1' => fun_get::get("s_time1"),
			'time2' => fun_get::get("s_time2"),
			'state' => (int)fun_get::get("s_state"),
			'key' => fun_get::get("s_key"),
		);
		if( fun_is::isdate( $arr_search_key['time1'] ) ) $arr_where_s[] = "msg_time >= '" . $arr_search_key['time1'] . "'"; 
		if( fun_is::isdate( $arr_search_key['time2'] ) ) $arr_where_s[] = "msg_time <= '" . date("Y-m-d H:i:s",fun_get::endtime( $arr_search_key['time2'] )) . "'"; 
		if($arr_search_key['state'] == 1) {
			$arr_where_s[] = "msg_recont=''"; 
		} else if($arr_search_key['state'] == 2) {
			$arr_where_s[] = "msg_recont!=''"; 
		}
		if( $arr_search_key['key'] != '' ) $arr_where_s[] = "(msg_name like '%" . $arr_search_key['key'] . "%' or msg_cont like '%" . $arr_search_key['key'] . "%' or msg_tel like '%" . $arr_search_key['key'] . "%' or msg_email like '%" . $arr_search_key['key'] . "%')"; 
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
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("com.msg"  , $this->app_dir);
		$lng_pagesize = $arr_config_info["pagesize"];
		//取分页信息
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."other_msg" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."other_msg" . $str_where . " order by msg_id desc" . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$arr_return["list"][] = $obj_rs;
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}

	/* 查询配置表指定id信息
	 * msg_id : sys_config 表中 config_id
	 */
	function get_editinfo($msg_id) {
		$obj_rs = cls_obj::db()->edit(cls_config::DB_PRE."other_msg" , "msg_id='".$msg_id."'");
		return $obj_rs;
	}

	/* 保存数据
	 * 
	 */
	function on_save() {
		$arr_return = array("code" => 0 , "id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_fields = array(
			"msg_id"=>fun_get::post("id"),
			"msg_recont"=>fun_get::post("recont"),
			"msg_retime"=>date("Y-m-d H:i:s"),
		);
		$arr = cls_obj::get("cls_com")->msg("on_save" , $arr_fields);
		if($arr['code']==0) {
			if(isset($arr['id'])) $arr_return['id'] = $arr['id'];
			//是否短信通知用户
			$tel = fun_get::get("issms");
			if(fun_is::com('sms') && fun_is::mobile($tel)) {
				$arr = cls_obj::get('cls_com')->sms("on_send" , array("tel"=>$tel , "cont" => $arr_fields['msg_recont'] ,"id" => $arr_return["id"] , "type"=>0) );
			}
			//是否邮箱通知用户
			$email = fun_get::get("isemail");
			if(fun_is::com('email') && fun_is::email($email)) {
				$title =  cls_config::get("site_title" , "sys");
				if(!empty($title)) $title = '【' . $title . '】';
				$title .= "管理员回复了您的留言";
				$arr = cls_obj::get("cls_com")->email('send' , array('to_mail' => $email , 'title' => $title , 'content' => $arr_fields['msg_recont'] ,'save' => 1));
			}
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
		$arr = cls_obj::get("cls_com")->msg("on_delete" , $str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
}
?>