<?php
/**
 * 用户表操作类
 */
class tab_meal_menu {
	static $perms;
	/*
	 * 饭，汤，菜，套餐，饮料，甜点，
	 */
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"type" => array(
					"自选" => 3 ,
					cls_language::get("rice" , 'meal') => 1 ,
					cls_language::get("soup" , 'meal') => 2 ,
					cls_language::get("drink" , 'meal') => 4 ,
					cls_language::get("dessert" , 'meal') => 5 ,
				),
				"attribute" => array(
					"特价" => 1,
					"独家" => 2,
					"新品" => 3,
					"人气" => 4,
					"会员" => 5,
					"限时" => 6,
				),
				"state" => array( cls_language::get("online") => 1 , cls_language::get("downline") => 0) ,
				"mode" => array( "每天" => 0 , "按星期" => 1 , "按日期" => 3 , "自定义" => 2 ) ,
			);
			$shop_mode = cls_config::get("shop_mode" , "meal");
			if($shop_mode == "1") {
				self::$perms['type'] = array("单品" => 6);
			} else if($shop_mode != "2") {
				self::$perms['type'] = self::$perms['type']+array("单品" => 6);
			}
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}
	static function on_save($arr_fields,$where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['menu_id'])) {
			$arr_fields['id'] = $arr_fields['menu_id'];
			unset($arr_fields['menu_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " menu_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and menu_id='" . $arr_return['id'] . "'";
				}
			}
		}
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {

			//必填项检查
			if(!isset($arr_fields['menu_title']) || empty($arr_fields['menu_title'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = "菜谱名称不能为空";
				return $arr_return;
			}
			if(!isset($arr_fields['menu_price']) || $arr_fields['menu_price'] <= 0 ) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = "菜品价格不能为空";
				return $arr_return;
			}
			//初始必要值
			$arr_fields['menu_addtime'] = TIME;
			$arr_fields['menu_updatetime'] = TIME;

			//插入到用户表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."meal_menu",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$obj_rs = $obj_db->get_one("select menu_id from ".cls_config::DB_PRE."meal_menu where menu_title = '" . $arr_fields["menu_title"] . "' and menu_addtime = '" . $arr_fields["menu_addtime"] . "'");
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['menu_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {
			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select menu_id from ".cls_config::DB_PRE."meal_menu where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['menu_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "menu_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."meal_menu" , $arr_fields , $where);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		}
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
		$arr_fields = array("menu_isdel" => $isdel);
		if(is_numeric($str_id)) {
			$arr_return=cls_obj::db_w()->on_update(cls_config::DB_PRE."meal_menu",$arr_fields,"menu_id='".$str_id."'");
		} else {
			$arr_return=cls_obj::db_w()->on_update(cls_config::DB_PRE."meal_menu",$arr_fields,"menu_id in(".$str_id.")");
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
			(is_numeric($str_id)) ? $arr_where[] = "menu_id='".$str_id."'" : $arr_where[] = "menu_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);

		$arr_return=cls_obj::db_w()->on_delete(cls_config::DB_PRE."meal_menu" , $where);
		return $arr_return;
	}
	function get_opentime() {
		$arr = cls_config::get("day_opentime" , "meal");
		$arr_return = array("list" => array() );
		if(empty($arr)) {
			$arr = array();
		}
		$index = $nextindex = 1;
		$nowtime = (int)date("Hi");
		$nowindex = 0;
		foreach($arr as $item => $key) {
			$arr_1 = array();
			$arr_1["index"] = $index;
			$arr_1["title"] = $key;
			$arr_x = explode("," , $item);
			$arr_1["start"] = (int)$arr_x[0];
			$arr_1["end"] = (count($arr_x)>1)? (int)$arr_x[1] : 0;
			$arr_return["list"]['id_'.$index] = $arr_1;
			if($arr_1['start']<=$nowtime && $nowtime<=$arr_1['end']) $nowindex = $index;
			if($arr_1['start']<=$nowtime) $nextindex++;
			$index++;
		}
		$arr_return["nowindex"] = $nowindex;
		if($nowindex>0) {
			$arr_return["havenext"] = 0;//开放订餐状态
		} else if($nextindex==$index) {
			$arr_return["havenext"] = -1;//即将开始
		} else {
			$arr_return["havenext"] = 1;//明天继续
		}
		return $arr_return;
	}

}