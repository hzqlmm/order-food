<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_meal_act {
	static $perms;
	static $value;
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"state" => array( "发布" => 1 , "关闭" => 0) ,
				"where" => array( "达到指定金额" => 1 , "指定下单时间" => 2 , "达到指定数量" => 3 , "指定时间达指定数量" => 4) ,
				"method" => array( "赠送礼品" => 1 , "打折" => 2 , "送指定积分" => 3 , "积分翻倍" => 4 , "立减多少金额" => 5 , "每份优惠金额" => 6) ,
			);
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}
	static function on_save($arr_fields,$where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['act_id'])) {
			$arr_fields['id'] = $arr_fields['act_id'];
			unset($arr_fields['act_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " act_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and act_id='" . $arr_return['id'] . "'";
				}
			}
		}
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {

			//初始必要值
			if(!isset($arr_fields['act_addtime'])) $arr_fields['act_addtime'] = TIME;

			//插入到表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."meal_act",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$obj_rs = $obj_db->get_one("select act_id from ".cls_config::DB_PRE."meal_act where act_addtime='" . $arr_fields["act_addtime"] . " and act_name='" . $arr_fields["act_name"] . "' and act_shop_id='" . $arr_fields['act_shop_id'] . "'");
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['act_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {
			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select act_id from ".cls_config::DB_PRE."meal_act where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['act_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "act_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."meal_act" , $arr_fields , $where);
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
			(is_numeric($str_id)) ? $arr_where[] = "act_id='".$str_id."'" : $arr_where[] = "act_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);

		$arr_return=cls_obj::db_w()->on_delete(cls_config::DB_PRE."meal_act" , $where);
		return $arr_return;
	}
	/*
	 * 回收站或还原操作
	 * isdel 决定是回收还是还原，1:回收，0:还原
	 */
	static function on_del($arr_id , $isdel = 1) {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if($str_id == ""){
			$arr_return["msg"]="id".cls_language::get("not_null");
			return $arr_return;
		}
		$arr_fields = array("act_isdel" => $isdel);
		if(is_numeric($str_id)) {
			$arr_return=cls_obj::db_w()->on_update(cls_config::DB_PRE."meal_act",$arr_fields,"act_id='".$str_id."'");
		} else {
			$arr_return=cls_obj::db_w()->on_update(cls_config::DB_PRE."meal_act",$arr_fields,"act_id in(".$str_id.")");
		}
		return $arr_return;
	}

}