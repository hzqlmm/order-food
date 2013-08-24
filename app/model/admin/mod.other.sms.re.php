<?php
/**
 * 用户组模型类 关联表名：sys_user_log
 * 
 */
class mod_other_sms_re extends inc_mod_admin {

	/* 按模块查询配置表信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_list() {
		$str_where = "";
		$arr_where = $arr_where_s = array();
		$lng_issearch = 0;
		//取查询参数
		$arr_search_key = array(
			'time1' => fun_get::get("s_time1"),
			'time2' => fun_get::get("s_time2"),
			'key' => fun_get::get("s_key"),
		);
		if( fun_is::isdate( $arr_search_key['time1'] ) ) $arr_where_s[] = "re_time >= '" . $arr_search_key['time1'] . "'"; 
		if( fun_is::isdate( $arr_search_key['time2'] ) ) $arr_where_s[] = "re_time <= '" . date("Y-m-d H:i:s",fun_get::endtime( $arr_search_key['time2'] )) . "'"; 
		if( $arr_search_key['key'] != '' ) $arr_where_s[] = "(re_cont like '%" . $arr_search_key['key'] . "%' or re_tel like '%" . $arr_search_key['key'] . "%')"; 
		$arr_where = array_merge($arr_where , $arr_where_s);
		if(count($arr_where)>0) $str_where = " where " . implode(" and " , $arr_where);
		$page = (int)fun_get::get("page");
		$arr_return  = $this->sql_list( $str_where , $page);
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
		$arr_config_info = tab_sys_user_config::get_info("other.sms.re"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("other.sms.re" , $this->app_dir , "other");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		$arr_return["issearch"] = 0;
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."other_sms_re" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."other_sms_re" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$arr_return["list"][] = $obj_rs;
		}
		$arr_return['pagebtns'] = $this->get_pagebtns($arr_return['pageinfo']);
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
		$arr = tab_other_sms_re::on_delete($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
}
?>