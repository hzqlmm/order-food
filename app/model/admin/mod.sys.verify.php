<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_sys_verify extends inc_mod_admin {

	/* 按模块查询配置表信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_list( $type = '' ) {
		$str_where = "";
		$arr_where = $arr_where_s = array();
		$arr_where[] = "verify_type='" . $type . "'";
		$lng_issearch = 0;
		//取查询参数
		$arr_search_key = array(
			'time1' => fun_get::get("s_time1"),
			'time2' => fun_get::get("s_time2"),
			'retime1' => fun_get::get("s_retime1"),
			'retime2' => fun_get::get("s_retime2"),
			'state' => (int)fun_get::get("s_state" , -999),
			'key' => fun_get::get("s_key"),
		);
		if( fun_is::isdate( $arr_search_key['time1'] ) ) $arr_where_s[] = "verify_time >= '" . $arr_search_key['time1'] . "'"; 
		if( fun_is::isdate( $arr_search_key['time2'] ) ) $arr_where_s[] = "verify_time <= '" . date("Y-m-d H:i:s",fun_get::endtime( $arr_search_key['time2'] )) . "'"; 
		if( fun_is::isdate( $arr_search_key['retime1'] ) ) $arr_where_s[] = "verify_retime >= '" . $arr_search_key['retime1'] . "'"; 
		if( fun_is::isdate( $arr_search_key['retime2'] ) ) $arr_where_s[] = "verify_retime <= '" . date("Y-m-d H:i:s",fun_get::endtime( $arr_search_key['retime2'] )) . "'"; 
		if( $arr_search_key['state'] != -999 ) $arr_where_s[] = "verify_state = '" . $arr_search_key['state'] . "'"; 
		if( $arr_search_key['key'] != '' ) $arr_where_s[] = "(user_name like '%" . $arr_search_key['key'] . "%' or verify_key like '%" . $arr_search_key['key'] . "%'"; 
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
		$arr_config_info = tab_sys_user_config::get_info("sys.verify"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("sys.verify" , $this->app_dir , "sys");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		$arr_state = tab_sys_verify::get_perms("state");
		$arr_type = tab_sys_verify::get_perms("type");
		//取对应菜单信息
		$arr_menu = $this->get_menuname();
		$arr_return["issearch"] = 0;
		$arr_return["list"] = array();
		$arr_uid = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."sys_verify" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."sys_verify" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$obj_rs['verify_state'] = array_search($obj_rs['verify_state'] , $arr_state);
			$obj_rs['verify_type'] = array_search($obj_rs['verify_type'] , $arr_type);
			$arr_uid[] = $obj_rs['verify_user_id'];
			$obj_rs['user_name'] = '';
			$arr_return["list"][] = $obj_rs;
		}
		if(count($arr_uid)>0) {
			$arr = cls_obj::get("cls_user")->get_user($arr_uid);
			$count = count($arr_return["list"]);
			for($i = 0 ; $i < $count ; $i++) {
				$arr_return["list"][$i]['user_name'] = array_search($arr_return["list"][$i]['verify_user_id'] , $arr);
			}
		}
		$arr_return['pagebtns'] = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}

	// 清除日志
	function on_delete() {
		$arr_return = array("code" => 0 , "msg" => cls_language::get("delete_ok"));
		$start_time1 = fun_get::get("del_time1");
		$start_time2 = fun_get::get("del_time2");
		$arr_where = array();
		if( !empty($start_time1) && fun_is::isdate($start_time1) ) {
			$arr_where[] = "verify_time>=" . $start_time1;
		}
		if( !empty($start_time2) && fun_is::isdate($start_time2) ) {
			$arr_where[] = "verify_time<=" . date("Y-m-d H:i:s" , fun_get::endtime($start_time2));
		}
		if( count($arr_where) < 1 )  {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg'] = cls_language::get("no_time");
			return $arr_return;
		}
		$str_where  = implode(" and " , $arr_where);
		$arr = tab_sys_verify::on_delete('' , $str_where);
		if($arr['code'] != 0) {
			$arr_return = $arr;
		}
		return $arr_return;
	}
}