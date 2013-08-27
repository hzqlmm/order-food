<?php
/*
 *
 *
 * 2013-03-24
 */
class fun_kj{
	/* 取指定id广告信息
	 * id 广告id
	 */
	static function get_ads($id) {
		$obj_db = cls_obj::db();
		$obj_rs = $obj_db->get_one("select ads_html from " . cls_config::DB_PRE . "other_ads where ads_id='" . $id . "'");
		if(!empty($obj_rs)) {
			return $obj_rs["ads_html"];
		} else {
			return '';
		}
	}
	//获取地区html列表
	static function get_area($pid = 0 , $depth = -1) {
		if($depth < 0 ) {
			$depth = (int)cls_config::get("area_depth", "meal");
		}
		$arr_return = array("list" => "" , "default" => array() ,"depth" => 0 , "area" => "");
		$obj_db = cls_obj::db();
		$arr_area = $arr_default = $arr_list = array();
		$obj_result = $obj_db->query("select area_id,area_name,area_pid,area_depth,area_val from " . cls_config::DB_PRE . "sys_area where " . cls_db::concat("," , "area_pids" , ",") ." like '%," . $pid . ",%' and area_depth<=" . $depth . " order by area_depth,area_sort,area_name");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			if(empty($obj_rs["area_val"])) $obj_rs["area_val"] = $obj_rs["area_name"];
			if(empty($obj_rs["area_name"])) $obj_rs["area_name"] = $obj_rs["area_val"];
			unset($obj_rs['area_val']);
			$arr_list["id_" . $obj_rs["area_pid"]][] = $obj_rs["area_id"];
			$arr_area["id_" . $obj_rs["area_id"]] = $obj_rs;
			if($obj_rs["area_pid"] == $pid) $arr_return["default"][] = $obj_rs;
		}
		if(count($arr_return["default"])>0) {
			$arr_return["depth"] = $depth - $arr_return["default"][0]["area_depth"] + 1;
		}
		$arr_return["area"] = fun_format::json($arr_area);
		$arr_return["list"] = fun_format::json($arr_list);
		return $arr_return;
	}
	/* 取缓存信息
	 * type : 缓存字段分类
     * format 表示输入格式：取值范围:空，json
	 */
	static function get_cache_words($type , $val , $format = '' ,$top = 0) {
		$arr_return = array();
		$obj_db = cls_obj::db();
		$arr_where = array("words_type like '" . $type . "%'");
		if(!empty($val)) {
			$arr_where[] = "(words_val like '" . $val . "%' or words_pin like '" . $val . "' or words_jian like '" . $val . "%')";
		}
		$limit = '';
		if(!empty($limit)) $limit = 'limit 0,' . $top;
		$where = implode(" and " , $arr_where);
		$obj_result = $obj_db->select("select words_val from " . cls_config::DB_PRE . "sys_cache_words where " . $where . " order by words_num desc" . $limit);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_return[] = $obj_rs["words_val"];
		}
		if($format == 'json') $arr_return = fun_format::json($arr_return);
		return $arr_return;
	}
}