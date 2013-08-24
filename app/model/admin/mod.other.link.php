<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_other_link extends inc_mod_admin {

	/* 实现按具体条件查询数据表，并返回分页信息
	 * str_where : sql 查询条件
	 */
	function sql_list( $group = 0 ) {
		$arr_return = array("list" => array());
		$str_where = " where link_group='" . $group . "'";
		$obj_db = cls_obj::db();
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("other.link"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		//取分页信息
		$arr_return["list"] = array();
		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."other_link" . $str_where . $sort);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$arr_return["list"][] = $obj_rs;
		}
		return $arr_return;
	}
	/* 保存数据
	 * 
	 */
	function on_save() {
		$arr_return = array("code" => 0 ,"id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_link_id = fun_get::get("link_id");
		if(fun_is::set("link_sort")) $arr_link_sort = fun_get::get("link_sort");
		if(fun_is::set("link_name")) $arr_link_name = fun_get::get("link_name");
		if(fun_is::set("link_pic")) $arr_link_pic = fun_get::get("link_pic");
		if(fun_is::set("link_url")) $arr_link_url = fun_get::get("link_url");
		$get_link_group = fun_get::get("url_group");
		$arr_resave = array();
		$lng_count = count($arr_link_id);

		//循环统计已有 id
		$arr_id = array();
		for( $i = 1 ; $i < $lng_count ; $i++) {
			$lng_id = (int)$arr_link_id[$i];
			if($lng_id > 0) $arr_id[] = $lng_id;
		}
		$str_where = "link_group='" . $get_link_group . "'";
		$str_ids = fun_format::arr_id($arr_id);
		if( !empty($str_ids) ) {
			$str_where .= " and link_id not in(".$str_ids.")";
		}
		//首先删除没在保存id中的所有记录
		tab_other_link::on_delete(array(),$str_where);
		for( $i = 1 ; $i < $lng_count ; $i++) {
			$arr_fields = array(
				"link_id" => (int)$arr_link_id[$i],
			);
			if(isset($arr_link_sort)) $arr_fields["link_sort"] = $arr_link_sort[$i];
			if(isset($arr_link_name)) $arr_fields["link_name"] = $arr_link_name[$i];
			if(isset($arr_link_pic)) $arr_fields["link_pic"] = $arr_link_pic[$i];
			if(isset($arr_link_url)) $arr_fields["link_url"] = $arr_link_url[$i];
			//不直接修改 pid,只在新增时保存 pid
			if( $arr_fields["link_id"]<1 ) {
				$arr_fields["link_group"] = $get_link_group;
			}
			$arr = tab_other_link::on_save($arr_fields);
			if($arr["code"] != 0) {
				$arr_return['code'] = $arr["code"];
				$arr_return['msg'] = $arr["msg"];
				return $arr_return;
			}
		}
		return $arr_return;
	}
	/* 删除指定  link_id 数据
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
		$arr = tab_other_link::on_delete($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
}