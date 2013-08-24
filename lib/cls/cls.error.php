<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class cls_error{
	//当前错误保存
	static $this_val=array();
	/**错误类型
	 * exit:结束程序，save:保存错误日志，url:为出错时跳转到新的地址，urltime:指多少秒后跳转
	 * 
	 * 
	 */
	static $this_type=array (
		"db_connect" => array( "save" => true , "exit" => true , "code" => 111),		//数据库连接失败
		"db_query" => array( "save" => true , "exit" => false  , "jump_key" => array("back") , "code" => 112),		//查询出错
		"db_select" => array( "save" => true , "exit" => false  , "jump_key" => array("back") , "code" => 112),		//查询出错
		"fields_no" => array( "save" => false , "exit" => true  , "jump_key" => array("back") , "code" => 301),		//缺少字段配置信息
		"no_page_act" => array( "save" => false , "exit" => true , "jump_key" => array("back" , "refresh") , "code" => 201),	//ctl不在在app_act
		"no_page_app" => array( "save" => false , "exit" => true , "jump_key" => array("back" , "refresh") , "code" => 201),	//ctl不在在app
		"no_limit" => array( "save" => false , "exit" => true , "jump_key" => array("refresh","jump_relogin") , "code" => 202),		//没有控制权限
		"no_login" => array( "save" => false , "exit" => true , "jump_key" => array("jump_login") , "code" => 1),	//没有登录
		"exit" => array( "save" => false , "exit" => true , "code" => 500),	//其它错误
	);
	/** 配合上面 this_type 指定的跳转信息
	 *  url : 跳转到的网址 , time : 多少秒后自动跳转(0:立即跳转,小于0:则不自动跳转)
	 *  target : 跳转形式
	 */
	static $this_jump=array (
		"jump_login" => array("url" => "common.php?app=sys&app_act=login" , "time" => 0 , "target" => ""),
		"jump_relogin" => array("url" => "common.php?app=sys&app_act=login" , "time" => -1 , "target" => ""),
	);
	static function on_error($msg_key , $msg = "") {
		if( !isset(self::$this_type[$msg_key]) ) return;

		//保存日志
		if( self::$this_type[$msg_key]["save"] ) {
			self::on_save( $msg_key , $msg );
		}
		//退出系统
		if( self::$this_type[$msg_key]["exit"] ) {
			self::on_exit($msg_key , $msg);
		}
		self::$this_val[] = $msg;
	}
	static function on_exit($msg_key , $msg = '') {
		$str_errtips = $str_debugtips = '';
		if(is_array($msg)) {
			if(isset($msg["tips"])) $str_errtips = $msg["tips"];
			if(cfg_env::IS_TEST>0 && isset($msg["debug"])) $str_debugtips = $msg["debug"];
		} else {
			$str_errtips = $msg;
		}
		if( empty($str_errtips) ) {
			$str_errtips = cls_language::get($msg_key);
		}
		if(empty($str_errtips) || $str_errtips == $msg_key ) $str_errtips = cls_language::get("error");
		if( fun_get::get("app_ajax") == "1" ) {
			$arr = array(
				"code" => self::$this_type[$msg_key]["code"] ,
				"msg"  => $str_errtips
			);
			echo fun_format::json($arr);exit;
		}

		if(fun_get::get("error_tips") != '') $str_errtips = fun_get::get("error_tips");
		if(fun_get::get("error_type") != '') $msg_key     = fun_get::get("error_type");
		if(!empty($str_debugtips)) $str_errtips .= "<br>" . $str_debugtips;
		/* 传给 error 模板的变量
		 * app = error 表示指向错误模板，error_tips 表示　错误提示，error_type 表示错误类型
		 */
		$arr_url = array(
			"app=error",
			"error_tips=" . urlencode($str_errtips),
			"error_type=" . urlencode($msg_key)
		);
		self::on_show(self::$this_type[$msg_key] , $str_errtips);
	}
	/** 显示错误页面
	 *  默认优先显示　当前模块下的 error.default.html　模板 即 app=error 如果不存在，则显示以下html
	 */
	static function on_show($arr_type , $str_tips = '') {
		$jump_url = fun_get::get("jump_url");
		if(empty($jump_url)) $jump_url = $_SERVER["REQUEST_URI"];
		$arr_action = array();
		if(isset($arr_type["jump_key"]) && !empty($arr_type["jump_key"])) {
			$arr_urljumpfrom = parse_url($jump_url);
			foreach($arr_type["jump_key"] as $key) {
				$arr = array();
				if($key == "back") {
					$arr["url"]    = "javascript:history.back();";
					$arr["time"]   = -1;
					$arr["target"] = "_self";
					$arr["title"]  = cls_language::get($key);
				} else if($key == 'refresh') {
					$arr["url"]    = "javascript:window.location.reload();";
					$arr["time"]   = -1;
					$arr["target"] = "_self";
					$arr["title"]  = cls_language::get($key);
				} else {
					$arr["url"]    = self::$this_jump[$key]["url"];
					$arr["time"]   = self::$this_jump[$key]["time"];
					$arr["target"] = self::$this_jump[$key]["target"];
					$arr["title"]  = cls_language::get($key);
					$arr1 = parse_url($arr["url"]);
					if( $arr1["path"] != $arr_urljumpfrom["path"] ) {
						$arr["url"] = fun_base::url_add_query($arr["url"] , array("jump_url" => $jump_url ));
					}

				}
				if(empty($arr["target"])) $arr["target"] = "_self";
				if(empty($arr["title"])) $arr["title"] = cls_language::get("jump_url");
				$arr_action[] = $arr;
			}
		}
		//设置相关值
		echo cls_app::on_display("common" , '' , "error" , "default" , array("error_tips"=>$str_tips , "error_action"=>$arr_action) );
		exit;
	}
	//保存错误日志
	static function on_save( $msg_type , $msg_cont = "" , $arr_parms = array()) {
		$str_dir = KJ_DIR_DATA . "/error/" . $msg_type;
		$str_path = $str_dir."/" . date("Y_m_d") . ".txt";
		$str_cont = "";
		if( is_file($str_path) ) $str_cont = file_get_contents($str_path) . chr(10) . chr(10);
		$str_cont .= "===============" . date("Y-m-d H:i:s") . "===================";
		if( !empty($msg_cont) ) {
			if(is_array($msg_cont)) $msg_cont = "(" . implode(")" . chr(10) . "(" , $msg_cont) . ")";
			$msg_cont = str_replace("<br>",chr(10) , $msg_cont);
			$msg_cont = str_replace("&nbsp;" , " " , $msg_cont);
			$str_cont .= chr(10) . $msg_cont;
		}

		
		$str_cont .= chr(10) . "-------------------parms---------------------";
		$str_referer = "";
		if( isset($_SERVER["HTTP_REFERER"]) ) $str_referer = $_SERVER["HTTP_REFERER"];
		$arr_fields = array(
			"HTTP_REFERER" => $str_referer,
			"HTTP_USER_AGENT" => $_SERVER["HTTP_USER_AGENT"],
			"REMOTE_ADDR" => $_SERVER["REMOTE_ADDR"],
			"SCRIPT_FILENAME" => $_SERVER["SCRIPT_FILENAME"],
			"REQUEST_METHOD" => $_SERVER["REQUEST_METHOD"],
			"QUERY_STRING" => $_SERVER["QUERY_STRING"],
		);
		foreach( $arr_fields as $item => $key ){
			$str_cont .= chr(10) . $item." => " . $key;
		}
		foreach( $arr_parms as $item => $key ) {
			$str_cont .= chr(10) . $item . " => " . $key;
		}
		$str_cont .= chr(10)."-------------------GET---------------------";
		foreach( $_GET as $item => $key ){
			$str_cont .= chr(10) . $item . "=>" . $key;
		}
		$str_cont .= chr(10)."-------------------POST---------------------";
		foreach( $_POST as $item => $key ) {
			$str_cont .= chr(10) . $item . "=>" . $key;
		}
		fun_file::file_create( $str_path , $str_cont , 1 );
	}
}