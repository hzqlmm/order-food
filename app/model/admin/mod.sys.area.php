<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_sys_area extends inc_mod_admin {

	/* 实现按具体条件查询数据表，并返回分页信息
	 * str_where : sql 查询条件
	 */
	function sql_list( $pid = 0 ) {
		$arr_return = array("list" => array());
		$str_where = " where area_pid='" . $pid . "'";
		$obj_db = cls_obj::db();
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("sys.area" , $this->app_dir , "sys");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("sys.area"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		//取分页信息
		$arr_return["list"] = array();
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."sys_area" . $str_where . $sort);
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
		$arr_area_id = fun_get::get("area_id");
		if(fun_is::set("area_sort")) $arr_area_sort = fun_get::get("area_sort");
		if(fun_is::set("area_name")) $arr_area_name = fun_get::get("area_name");
		if(fun_is::set("area_val")) $arr_area_val = fun_get::get("area_val");
		if(fun_is::set("area_tag")) $arr_area_tag = fun_get::get("area_tag");
		if(fun_is::set("area_pin")) $arr_area_pin = fun_get::get("area_pin");
		if(fun_is::set("area_jian")) $arr_area_jian = fun_get::get("area_jian");
		if(fun_is::set("area_dispatch_price")) $arr_dispatch_price = fun_get::get("area_dispatch_price");
		if(fun_is::set("area_dispatch_time")) $arr_dispatch_time = fun_get::get("area_dispatch_time");
		$get_url_pid = (int)fun_get::get("url_pid");
		$arr_resave = array();
		$lng_count = count($arr_area_id);

		//循环统计已有 id
		$arr_id = array();
		for( $i = 1 ; $i < $lng_count ; $i++) {
			$lng_id = (int)$arr_area_id[$i];
			if($lng_id > 0) $arr_id[] = $lng_id;
		}
		$str_where = "area_pid='" . $get_url_pid . "'";
		$str_ids = fun_format::arr_id($arr_id);
		if( !empty($str_ids) ) {
			$str_where .= " and area_id not in(".$str_ids.")";
		}
		//开始事务
		$obj_db = cls_obj::db_w();
		$obj_db->begin("sys_area");
		//首先删除没在保存id中的所有记录
		$arr_result = tab_sys_area::on_delete(array(),$str_where);
		if($arr_result['code'] != 0) {
			$obj_db->rollback("sys_area");
			return array("code" => 500 , "msg" => "网络繁忙，请稍后重试");
		}
		for( $i = 1 ; $i < $lng_count ; $i++) {
			$arr_fields = array(
				"area_id" => (int)$arr_area_id[$i],
			);
			if(isset($arr_area_sort)) $arr_fields["area_sort"] = $arr_area_sort[$i];
			if(isset($arr_area_name)) $arr_fields["area_name"] = $arr_area_name[$i];
			if(isset($arr_area_val)) $arr_fields["area_val"] = $arr_area_val[$i];
			if(isset($arr_area_tag)) $arr_fields["area_tag"] = $arr_area_tag[$i];
			if(isset($arr_area_pin)) $arr_fields["area_pin"] = $arr_area_pin[$i];
			if(isset($arr_area_jian)) $arr_fields["area_jian"] = $arr_area_jian[$i];
			if(isset($arr_dispatch_price)) $arr_fields["area_dispatch_price"] = $arr_dispatch_price[$i];
			if(isset($arr_dispatch_time)) $arr_fields["area_dispatch_time"] = $arr_dispatch_time[$i];
			//不直接修改 pid,只在新增时保存 pid
			if( $arr_fields["area_id"]<1 ) {
				$arr_fields["area_pid"] = $get_url_pid;
			}
			$arr = tab_sys_area::on_save($arr_fields);
			if($arr["code"] != 0) {
				$obj_db->rollback("sys_area");
				$arr_return['code'] = $arr["code"];
				$arr_return['msg'] = $arr["msg"];
				return $arr_return;
			}
		}
		$obj_db->commit("sys_area");
		return $arr_return;
	}
	function get_path($pid) {
		$str_val = "";
		$str_sql="select area_id,area_name,area_pid from " . cls_config::DB_PRE . "sys_area where area_id='".$pid."'";
		$obj_result = cls_obj::db()->query($str_sql);
		if($obj_rs = cls_obj::db()->fetch_array($obj_result))	{
			$str_val = " -> <a href=\"javascript:kj.set('#id_url_pid','value','" . $obj_rs['area_id'] . "');admin.refresh();\">" . $obj_rs['area_name'] . "</a>";
			$str_val = $this->get_path($obj_rs['area_pid']) . $str_val;
		}
		return $str_val;
	}
	/* 删除指定  area_id 数据
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
		$arr = tab_sys_area::on_delete($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
}