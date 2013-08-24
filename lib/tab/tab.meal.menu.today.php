<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_meal_menu_today {
	static $perms;

	//获取表配置参数
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"date_period" => array( "id_1" => array("time"=>"10:00","val"=>1,"title"=>"上午") , "id_2" => array("time"=>"15:00","val"=>2,"title"=>"下午") )
			);
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}
	/* 保存操作
	 * arr_fields : 为字段数据，默认如果包函 id，则为修改，否则为插入
	 * where : 默认为空，用于有时候条件修改
	 */
	static function on_save($arr_fields , $where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['today_id'])) {
			$arr_fields['id'] = $arr_fields['today_id'];
			unset($arr_fields['today_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " today_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and today_id='" . $arr_return['id'] . "'";
				}
			}
		}
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {

			//必填项检查
			if(!isset($arr_fields['today_menu_id']) || empty($arr_fields['today_menu_id']) || !is_numeric($arr_fields['today_menu_id'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("today_menu_is_null" , "meal");//区域id不能为空
				return $arr_return;
			}
			//必填项检查
			if(!isset($arr_fields['today_date']) || empty($arr_fields['today_date'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("today_date_null", "meal");//区域id不能为空
				return $arr_return;
			}
			$arr_fields["today_addtime"] = $arr_fields["today_updatetime"] = TIME;
			//插入到用户表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."meal_menu_today",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$where  = "today_menu_id='" . $arr_fields['today_menu_id'] . " and today_date='".$arr_fields['today_date'] . "' and today_date_period='".$arr_fields["today_date_period"]."'";
					$obj_rs = $obj_db->get_one("select today_id from ".cls_config::DB_PRE."meal_menu_today where ".$where);
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['today_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {

			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select today_id from ".cls_config::DB_PRE."meal_menu_today where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['today_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "today_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."meal_menu_today" , $arr_fields , $where);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		}
		if(isset($arr_fields["today_date"]) && isset($arr_fields["today_date_period"])) {
			self::create_xml($arr_fields["today_date"],$arr_fields["today_date_period"]);
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
			$arr_return["msg"]="id".cls_language::get("not_null");
			return $arr_return;
		}
		$obj_db = cls_obj::db_w();
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "today_id='".$str_id."'" : $arr_where[] = "today_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);
		$arr_return = $obj_db->on_delete(cls_config::DB_PRE."meal_menu_today" , $where);
		return $arr_return;
	}
	//生成xml文件
	function create_xml($msg_date,$msg_date_period){
		$arr_province = $arr_type= $arr_store=$arr_today=$arr_property =array();
		$arr_menu_ids = array();
		$arr_foodinfo = array();
		$obj_db = cls_obj::db();
		if(!is_numeric($msg_date)) $msg_date = strtotime($msg_date);
		$obj_result=$obj_db->select("select * from " . cls_config::DB_PRE . "meal_menu_today a left join " . cls_config::DB_PRE . "meal_menu b on a.today_menu_id=b.menu_id where today_date='".$msg_date."' and today_date_period='".$msg_date_period."' order by menu_number desc");
		while($obj_rs=$obj_db->fetch_array($obj_result)){
			$arr_menu_ids[]=$obj_rs["today_menu_id"];
			$arr_today[]=$obj_rs;
		}
		if(!empty($arr_menu_ids)){
			$arr=array_unique($arr_menu_ids);
			$str_ids=implode(",",$arr);
			$obj_result = $obj_db->select("select * from " . cls_config::DB_PRE . "meal_menu where menu_id in(" . $str_ids . ")");
			while($obj_rs=$obj_db->fetch_array($obj_result)){
				$arr_foodinfo["id_".$obj_rs["menu_id"]]=$obj_rs;
			}
		}
		foreach($arr_today as $item){
			if(!isset($arr_foodinfo["id_".$item['today_menu_id']])) continue;
			$arr_property[]=array("Food"=>array("property"=>array("id"=>$arr_foodinfo["id_".$item['today_menu_id']]["menu_number"],"name"=>$arr_foodinfo["id_".$item['today_menu_id']]["menu_title"],"price"=>$arr_foodinfo["id_".$item['today_menu_id']]["menu_price"],"Status"=>"true","Quantity"=>$item['today_num'])));
		}
		$arr_xml=array(
			"FoodList"=>array("body"=>$arr_property),
		);

		if($msg_date_period==1){
			$msg_date_period="a";
		}else{
			$msg_date_period="b";
		}
		$str_xml_path='/'.date("Y",$msg_date)."/".date("m",$msg_date)."/".date("d",$msg_date).$msg_date_period."/TodayFood.xml";
		$str_path=KJ_DIR_ROOT."/XML".$str_xml_path;


		$str_xml=fun_get::xml($arr_xml);
		fun_file::file_create($str_path,$str_xml,1);
	}

}