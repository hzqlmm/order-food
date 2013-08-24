<?php
/**
 * 用户组模型类 关联表名：sys_user_log
 * 
 */
class mod_other_sms extends inc_mod_admin {

	/* 按模块查询配置表信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_list( $type = '' ) {
		$str_where = "";
		$arr_where = $arr_where_s = array();
		$arr_where[] = "sms_type='" . $type . "'";
		$lng_issearch = 0;
		//取查询参数
		$arr_search_key = array(
			'time1' => fun_get::get("s_time1"),
			'time2' => fun_get::get("s_time2"),
			'retime1' => fun_get::get("s_retime1"),
			'retime2' => fun_get::get("s_retime2"),
			'isre' => (int)fun_get::get("s_isre"),
			'key' => fun_get::get("s_key"),
		);
		if( fun_is::isdate( $arr_search_key['time1'] ) ) $arr_where_s[] = "sms_time >= '" . $arr_search_key['time1'] . "'"; 
		if( fun_is::isdate( $arr_search_key['time2'] ) ) $arr_where_s[] = "sms_time <= '" . date("Y-m-d H:i:s",fun_get::endtime( $arr_search_key['time2'] )) . "'"; 
		if( fun_is::isdate( $arr_search_key['retime1'] ) ) $arr_where_s[] = "sms_retime >= '" . strtotime($arr_search_key['retime1']) . "'"; 
		if( fun_is::isdate( $arr_search_key['retime2'] ) ) $arr_where_s[] = "sms_retime <= '" . fun_get::endtime( $arr_search_key['retime2'] ) . "'"; 
		if( $arr_search_key['isre']==1 ) $arr_where_s[] = "sms_retime>0"; 
		if( $arr_search_key['isre']==2 ) $arr_where_s[] = "sms_retime=0"; 
		if( $arr_search_key['key'] != '' ) $arr_where_s[] = "(sms_content like '%" . $arr_search_key['key'] . "%' or sms_tel like '%" . $arr_search_key['key'] . "%' or sms_recont like '%" . $arr_search_key['key'] . "%')"; 
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
		$arr_config_info = tab_sys_user_config::get_info("other.sms"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("other.sms" , $this->app_dir , "other");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		$arr_type = tab_other_sms::get_perms("type");
		$arr_return["issearch"] = 0;
		$arr_return["list"] = $arr_uid = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."other_sms" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."other_sms" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$obj_rs['sms_type'] = array_search($obj_rs['sms_type'] , $arr_type);
			if(isset($obj_rs["sms_send_uid"]) && !empty($obj_rs["sms_send_uid"])) $arr_uid[] = $obj_rs["sms_send_uid"];
			if(isset($obj_rs["sms_receive_uid"]) && !empty($obj_rs["sms_receive_uid"])) $arr_uid[] = $obj_rs["sms_send_uid"];
			$arr_return["list"][] = $obj_rs;
		}
		if(count($arr_uid)>0) {
			$arr_uid = array_unique($arr_uid);
			$str_ids = implode(",", $arr_uid);
			$arr_uname = array();
			$obj_result = $obj_db->select("select user_name,user_id from " . cls_config::DB_PRE . "sys_user where user_id in(". $str_ids.")");
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_uname["id_". $obj_rs["user_id"]] = $obj_rs["user_name"];
			}
			for($i = count($arr_return["list"])-1 ; $i>=0 ; $i--) {
				if(isset($arr_uname["id_".$arr_return["list"][$i]['sms_send_uid']])) $arr_return["list"][$i]['sms_send_uid'] = $arr_uname["id_".$arr_return["list"][$i]['sms_send_uid']];
				if(isset($arr_uname["id_".$arr_return["list"][$i]['sms_receive_uid']])) $arr_return["list"][$i]['sms_receive_uid'] = $arr_uname["id_".$arr_return["list"][$i]['sms_receive_uid']];
			}
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
		$arr = tab_other_sms::on_delete($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
}
?>