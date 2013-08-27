<?php
/*
 *
 *
 * 2013-03-24
 */
class tab_sys_cache_words {
	/* words_val 为主键，不能重复
	 * 默认修改，当不存在，则添加
	 */
	static function on_save($words_type , $words_val) {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(empty($words_val)) {
			$arr_return["code"] = 500;
			return $arr_return;
		}
		$obj_db = cls_obj::db_w();
		$arr_return = $obj_db->on_exe( "update " . cls_config::DB_PRE . "sys_cache_words set words_num=words_num+1,words_updatetime='" . TIME . "' where words_type='" . $words_type  . "' and words_val='" . $words_val . "' ");
		if($arr_return['code'] == 0 && $obj_db->affected_rows() < 1) {
			$arr_ping = cls_pinyin::get($words_val , cls_config::DB_CHARSET);
			$pin = $arr_ping["style2"];
			$jian = $arr_ping["style3"];

			$arr_fields = array(
				"words_type" => $words_type ,
				"words_val" => $words_val ,
				"words_pin" => $pin ,
				"words_jian" => $jian ,
				"words_updatetime" => TIME ,
				"words_num" => 1 ,
			);
			$arr_return = $obj_db->on_insert(cls_config::DB_PRE."sys_cache_words",$arr_fields);
		}
		return $arr_return;
	}

}