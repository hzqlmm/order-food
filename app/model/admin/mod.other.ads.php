<?php
/**
 * 菜单模型类 关联表名：other_ads
 * 
 */
class mod_other_ads extends inc_mod_admin {
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
			'ads_title' => fun_get::get("s_key"),
		);
		if( !empty($arr_search_key['ads_title']) ) $arr_where_s[] = "ads_title like '%" . $arr_search_key['ads_title'] . "%'"; 
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
		$arr_cfg_fields = tab_sys_user_config::get_fields("other.ads" , $this->app_dir , "other");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("other.ads"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		//取分页信息
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."other_ads" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."other_ads" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			if(isset($obj_rs["ads_addtime"]) && !empty($obj_rs["ads_addtime"])) $obj_rs["ads_addtime"] = date("Y-m-d H:i:s" , $obj_rs["ads_addtime"]);
			if(isset($obj_rs["ads_starttime"]) && !empty($obj_rs["ads_starttime"])) $obj_rs["ads_starttime"] = date("Y-m-d H:i:s" , $obj_rs["ads_starttime"]);
			if(isset($obj_rs["ads_state"])) $obj_rs["ads_state"] = array_search($obj_rs["ads_state"] , tab_other_ads::get_perms("state"));
			if(isset($obj_rs["ads_type"])) $obj_rs["ads_type"] = array_search($obj_rs["ads_type"] , tab_other_ads::get_perms("type"));
			$arr_return["list"][] = $obj_rs;
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}

	/* 查询配置表指定id信息
	 * msg_id : sys_config 表中 config_id
	 */
	function get_editinfo($msg_id) {
		$obj_rs = cls_obj::db()->edit(cls_config::DB_PRE."other_ads" , "ads_id='".$msg_id."'");
		if( empty($obj_rs["ads_id"]) ) {
			$obj_rs["ads_state"] = 1;
		}
		(empty($obj_rs["ads_starttime"])) ? $obj_rs["ads_starttime"] = '' : $obj_rs["ads_starttime"] = fun_get::showdate($obj_rs["ads_starttime"]);
		(empty($obj_rs["ads_endtime"])) ? $obj_rs["ads_endtime"] = '' : $obj_rs["ads_endtime"] = fun_get::showdate($obj_rs["ads_endtime"]);
		(!empty($obj_rs["ads_cont"])) ? $obj_rs["fields"] = unserialize($obj_rs["ads_cont"]) : $obj_rs["fields"] = array();
		//图片
		if(!isset($obj_rs["fields"]["pic_url"])) {
			$obj_rs["fields"] = array_merge($obj_rs["fields"] , array("pic_url" => '' , "pic_w" => '' , "pic_h" => '' , "pic_link" => '' ) );
		}
		//flash
		if(!isset($obj_rs["fields"]["flash_url"])) {
			$obj_rs["fields"] = array_merge($obj_rs["fields"] , array("flash_url" => '' , "flash_w" => '' , "flash_h" => '' , "flash_link" => '' ) );
		}
		//文本
		if(!isset($obj_rs["fields"]["txt_cont"])) {
			$obj_rs["fields"] = array_merge($obj_rs["fields"] , array("txt_cont" => '') );
		}
		//幻灯片
		if(!isset($obj_rs["fields"]["slide1"])) $obj_rs["fields"] =  array_merge($obj_rs["fields"] , array("slide1_w"=>'' , "slide1_h"=>'' , "slide1"=>array() ) );
		//店铺名
		$obj_rs["shop_name"] = '';
		if(!empty($obj_rs["menu_shop_id"])) {
			$obj_rs2 = cls_obj::db()->get_one("select shop_id,shop_name from " . cls_config::DB_PRE . "meal_shop where shop_id='" .$obj_rs["menu_shop_id"] . "'");
			if(!empty($obj_rs)) {
				$obj_rs["shop_name"] = $obj_rs2["shop_name"];
			}
		}
		return $obj_rs;
	}

	/* 保存数据
	 * 
	 */
	function on_save() {
		$arr_return = array("code" => 0 , "id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_fields = array(
			"ads_id"=>fun_get::post("id"),
			"ads_type"=>fun_get::post("ads_type"),
			"ads_state"=>fun_get::post("ads_state"),
			"ads_title"=>fun_get::post("ads_title"),
			"ads_starttime"=>fun_get::post("ads_starttime"),
			"ads_endtime"=>fun_get::post("ads_endtime"),
		);
		if($arr_fields["ads_type"]=="pic") {
			$arr = array(
				"pic_url" => fun_get::post("pic_url"),
				"pic_w" => fun_get::post("pic_w"),
				"pic_h" => fun_get::post("pic_h"),
				"pic_link" => fun_get::post("pic_link"),
			);
			$arr_fields["ads_cont"] = $arr;
		} else if($arr_fields["ads_type"]=="flash"){
			$arr = array(
				"flash_url" => fun_get::post("flash_url"),
				"flash_w" => fun_get::post("flash_w"),
				"flash_h" => fun_get::post("flash_h"),
				"flash_link" => fun_get::post("flash_link"),
			);
			$arr_fields["ads_cont"] = $arr;
		} else if($arr_fields["ads_type"]=="txt"){
			$arr = array(
				"txt_cont" => fun_get::post("txt_cont"),
			);
			$arr_fields["ads_cont"] = $arr;
		} else if($arr_fields["ads_type"]=="slide1") {
			$get_slide1_url = fun_get::post("slide1_url" , array());
			$get_slide1_txt = fun_get::post("slide1_txt" , array());
			$get_slide1_link = fun_get::post("slide1_link" , array());
			$arr_slide1 = array();
			$lng_c = count($get_slide1_url);
			for($i = 0; $i < $lng_c ; $i++) {
				if(empty($get_slide1_url[$i])) continue;
				(isset($get_slide1_txt[$i])) ? $txt = $get_slide1_txt[$i] : $txt='';
				(isset($get_slide1_link[$i])) ? $link = $get_slide1_link[$i] : $link='';
				$arr_slide1[] = array("url" => $get_slide1_url[$i] , "txt" => $txt , "link" => $link);
			}
			$arr = array(
				"slide1_w" => fun_get::post("slide1_w"),
				"slide1_h" => fun_get::post("slide1_h"),
				"slide1" => $arr_slide1,
			);
			$arr_fields["ads_cont"] = $arr;
		} else {
			$arr_return["code"] = 500;
			$arr_return["msg"] = cls_language::get("ads_type_no");
		}
		if(fun_is::set("ads_shop_id")) $arr_fields['ads_shop_id'] = (int)fun_get::get("ads_shop_id");
		$arr = tab_other_ads::on_save($arr_fields);
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
		$arr = tab_other_ads::on_delete($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}

}
?>