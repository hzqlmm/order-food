<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_sys_user_log {
	/** 定义日志模块 */
	static $perms = array( "module" => array("admin") );
	/* 行为触发保存积分 ，无返回值
	 * uid : 用户id
	 * action : 为 程序中相应位置预先定义好了的，在cfg.sys.score.php 中配置奖励积分
	 * arr : 附带参数，包括：{score 当此值非0时，将优先于 配置信息里的值 , about_id 相关id ,}
	 */
	static function on_save( $module ) {
		if(!cls_obj::get("cls_user")->is_login()) return;
		if( !in_array($module , self::$perms["module"]) ) return;
		$arr_cont = array();
		$id = fun_get::get("id");
		$app = fun_get::get("app");
		$app_module = fun_get::get("app_module");
		if(!empty($id)) $arr_cont[]="id=" . $id;
		$arr_noin = array( "app" , "app_module" , "app_act" );
		$arr = array();
		if(!empty($app_module)) $arr[] = $app_module;
		if(!empty($app)) $arr[] = $app;
		$lang = implode("." , $arr);
		$arr_language = cls_language::get( $lang , "database" ) ;
		//取匹配前缀
		$pre = str_replace("." , "_" , fun_get::get("app"));
		$pre = end(explode("_" , $pre));
		$len = strlen($pre);
		foreach($_GET as $item => $key) {
			if(in_array($item , $arr_noin)) continue;
			if(!is_string($key)) continue;
			if(!is_array($arr_language)) {
				$arr_cont[] = $item . '=' . fun_format::len($key,200);
			} else {
				if(substr($item , 0, $len) != $pre ) continue;
				if(isset($arr_language[$item])) $arr_cont[] = $arr_language[$item] . '=' . fun_format::len($key,50);;
			}
		}
		foreach($_POST as $item => $key) {
			if(in_array($item , $arr_noin)) continue;
			if(!is_string($key)) continue;
			if(!is_array($arr_language)) {
				$arr_cont[] = $item . '=' . fun_format::len($key,50);
			} else {
				if(substr($item , 0, $len) != $pre ) continue;
				if(isset($arr_language[$item])) $arr_cont[] = $arr_language[$item] . '=' . fun_format::len($key,50);;
			}
		}
		$arr_fields = array(
			"log_user_id" => cls_obj::get("cls_user")->uid,
			"log_ip" => fun_get::ip(),
			"log_app_act" => fun_get::get("app_act"),
			"log_app" =>  $app,
			"log_app_module" =>  $app_module,
			"log_addtime" => TIME,
			"log_cont" => serialize($arr_cont),
			"log_module" => $module
		);
		if($arr_fields["log_app"] != '' && $arr_fields["log_app_act"] == '') $arr_fields["log_app_act"] = "default";
		/** 获取 key 值
		 *  key 唯一的，用来防止重复记录
		 */
		$arr_fields["log_key"] = date("Ymd") . "_" . $arr_fields["log_app_module"] . "_" . $arr_fields["log_app"] . "_" . $arr_fields["log_app_act"];
		if( count($_POST) > 0 ) {
			$arr_fields["log_key"] .= "_" . substr(TIME,-6) . rand(0 , 9999);
		}
		//插入到表
		$arr = cls_obj::db_w()->on_insert( cls_config::DB_PRE."sys_user_log" , $arr_fields , 1 );
		return $arr ;
	}

	/* 删除函数
	 * arr_id : 要删除的 id数组
	 * where : 删除附加条件
	 */
	static function on_delete($arr_id , $where = '') {
		$arr_return = array("code"=>0,"msg"=>"");

		$str_id = fun_format::arr_id($arr_id);
		if( $str_id == "" && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]=cls_language::get("not_where");
			return $arr_return;
		}
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "log_id='".$str_id."'" : $arr_where[] = "log_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);

		$arr_return=cls_obj::db_w()->on_delete(cls_config::DB_PRE."sys_user_log" , $where);
		return $arr_return;
	}

}