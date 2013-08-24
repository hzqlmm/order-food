<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_sys_user_log extends inc_mod_admin {

	/* 按模块查询配置表信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_list( $module = '' ) {
		$str_where = "";
		$arr_where = array();
		if(!empty($module)) $arr_where[] = "config_module='" . $module . "'";
		if( count($arr_where) > 0 ) {
			$str_where = " where " . implode(" and " , $arr_where);
		}
		$page = (int)fun_get::get("page");
		$arr_return  = $this->sql_list( $str_where , $page);
		return $arr_return;
	}
	/* 实现按具体条件查询数据表，并返回分页信息
	 * str_where : sql 查询条件 , lng_page : 当前页
	 */
	function sql_list($str_where = "" , $lng_page = 1) {
		$arr_return = array("list" => array());
		$obj_db = cls_obj::db();
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("sys.user.log"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("sys.user.log" , $this->app_dir , "sys");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		$arr_cfg_fields["sel"] = substr(str_replace(",user_name," , "," , "," . $arr_cfg_fields["sel"] . ","),1,-1);
		//取对应菜单信息
		$arr_uid = array();
		$arr_menu = $this->get_menuname();
		$arr_return["issearch"] = 0;
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."sys_user_log" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."sys_user_log" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$str_module = $obj_rs["log_app_module"];
			$str_app = $obj_rs["log_app_module"];
			if(isset($arr_menu[ $obj_rs["log_app_module"] . "_" . $obj_rs["log_app"] ])) {
				$app_module = $obj_rs["log_app_module"];
				$obj_rs['log_app_module'] = $arr_menu[ $obj_rs["log_app_module"] . "_" . $obj_rs["log_app"] ]["pname"];
				$obj_rs['log_app'] = $arr_menu[ $app_module . "_" . $obj_rs["log_app"] ]["name"];
			}
			$obj_rs['log_app_act'] = cls_language::get( $obj_rs["log_app_act"] );
			$obj_rs['log_addtime'] = date("Y-m-d H:i:s" , $obj_rs["log_addtime"]) ;
			$arr_x = '';
			if(!empty($obj_rs["log_cont"])) $arr_x = @unserialize($obj_rs["log_cont"]);
			if( is_array($arr_x) ) $obj_rs['log_cont'] = implode("<br>",$arr_x);
			$arr_return["list"][] = $obj_rs;
			$arr_uid[] = $obj_rs['log_user_id'];
		}
		if(count($arr_uid)>0) {
			$user_info = cls_obj::get("cls_user")->get_user($arr_uid);
			$count = count($arr_return["list"]);
			for($i = 0 ; $i < $count ; $i++) {
				$arr_return["list"][$i]['user_name'] = array_search($arr_return["list"][$i]['log_user_id'] , $user_info);
			}
		}
		$arr_return['pagebtns'] = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}
	
	// 获取菜单，并转换成一维数组
	function get_menuname() {
		//取对应菜单信息
		$arr_menu = $this->get_model_menu();
		foreach($arr_menu as $item => $key) {
			foreach($key as $app_item) {
				if(!isset($app_item["app_module"])) $app_item["app_module"] = '';
				if(!isset($app_item["app"])) $app_item["app"] = '';
				$arr[$app_item["app_module"]."_".$app_item["app"]]["name"] = $app_item["name"];
				$arr[$app_item["app_module"]."_".$app_item["app"]]["pname"] = $item;
			}
		}
		return $arr;
	}

	// 清除日志
	function on_delete() {
		$arr_return = array("code" => 0 , "msg" => cls_language::get("delete_ok"));
		$start_time1 = fun_get::get("del_time1");
		$start_time2 = fun_get::get("del_time2");
		$arr_where = array();
		if( !empty($start_time1) && fun_is::isdate($start_time1) ) {
			$arr_where[] = "log_addtime>=" . strtotime($start_time1);
		}
		if( !empty($start_time2) && fun_is::isdate($start_time2) ) {
			$arr_where[] = "log_addtime<=" . fun_get::endtime($start_time2);
		}
		if( count($arr_where) < 1 )  {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg'] = cls_language::get("no_time");
			return $arr_return;
		}
		$str_where  = implode(" and " , $arr_where);
		$arr = tab_sys_user_log::on_delete( '' , $str_where );
		if($arr['code'] != 0) {
			$arr_return = $arr;
		}
		return $arr_return;
	}
}