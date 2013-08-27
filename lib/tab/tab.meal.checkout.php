<?php
/*
 *
 *
 * 2013-03-24
 */
class tab_meal_checkout {

	static function on_save($arr_fields,$where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['checkout_id'])) {
			$arr_fields['id'] = $arr_fields['checkout_id'];
			unset($arr_fields['checkout_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " checkout_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and checkout_id='" . $arr_return['id'] . "'";
				}
			}
		}
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {
			//初始必要值
			$arr_fields['checkout_addtime'] = TIME;

			//插入到表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."meal_checkout",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$obj_rs = $obj_db->get_one("select checkout_id from ".cls_config::DB_PRE."meal_checkout where checkout_addtime='" . $arr_fields["checkout_addtime"] . " and checkout_date='" . $arr_fields["checkout_date"] . "' and checkout_shop_id='" . $arr_fields["checkout_shop_id"] . "'");
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['checkout_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {
			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select checkout_id from ".cls_config::DB_PRE."meal_checkout where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['checkout_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "checkout_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."meal_checkout" , $arr_fields , $where);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		}
		return $arr_return;
	}

	/* 删除函数
	 * arr_id : 要删除的 id数组
	 * where : 删除附加条件
	 */
	static function on_delete($arr_id , $where = '') {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if( empty($str_id) && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]=cls_language::get("not_where");
			return $arr_return;
		}
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "checkout_id='".$str_id."'" : $arr_where[] = "checkout_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);

		$arr_return=cls_obj::db_w()->on_delete(cls_config::DB_PRE."meal_checkout" , $where);
		return $arr_return;
	}
}