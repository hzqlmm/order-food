<?php
class install_com_sms{
	static $com_name = "com.sms.1.0";
	public $config = array(
			"limit" => array( 'sms' => array( "act"=>array("delete") , "name"=>"短信发送记录" ) , 'sms.re' => array( "act"=>array("delete") , "name"=>"短信回复记录" )),
			"menu" => array( array( "url"=>"?app_module=other&app=sms" , "name" => "短信发送记录" , "app" => "sms" , "app_module" => "other"),
							 array( "url"=>"?app_module=other&app=sms.re" , "name" => "短信回复记录" , "app" => "sms.re" , "app_module" => "other")
			),
		);

	function get_install_steps() {
		return array(
			array("name"=>"复制文件","step"=>"copy"),
			array("name"=>"创建数据","step"=>"data"),
			array("name"=>"配置文件","step"=>"config"),
		);
	}
	function get_uninstall_steps() {
		return array(
			array("name"=>"删除文件","step"=>"copy"),
			array("name"=>"清除数据","step"=>"data"),
			array("name"=>"清除配置","step"=>"config"),
		);
	}
	//安装：复制文件
	function install_copy() {
		//复制app目录
		fun_file::dir_copy( KJ_DIR_DATA . "/package/" . self::$com_name . "/app" , basename(KJ_DIR_APP) , KJ_DIR_ROOT);
		//复制lib目录
		fun_file::dir_copy( KJ_DIR_DATA . "/package/" . self::$com_name . "/lib" , basename(KJ_DIR_LIB) , KJ_DIR_ROOT);
		return array("code"=>0);
	}
	//卸载：删除文件
	function uninstall_copy() {
		//删除app目录下文件
		$arr_file = fun_file::get_files_all( KJ_DIR_DATA . "/package/" . self::$com_name . "/app");
		foreach($arr_file as $item) {
			$path = str_replace(KJ_DIR_DATA . "/package/" . self::$com_name . "/app" , KJ_DIR_APP , $item['path']);
			fun_file::file_delete($path);
		}
		//复制lib目录
		$arr_file = fun_file::get_files_all( KJ_DIR_DATA . "/package/" . self::$com_name . "/lib");
		foreach($arr_file as $item) {
			$path = str_replace(KJ_DIR_DATA . "/package/" . self::$com_name . "/lib" , KJ_DIR_APP , $item['path']);
			fun_file::file_delete($path);
		}
		return array("code"=>0);
	}

	//安装：数据
	function install_data() {
		$file = KJ_DIR_DATA . "/package/" . self::$com_name . "/install.sql";
		if(!file_exists($file)) return array("code"=>0);
		$sql = file_get_contents($file);
		$arr = cls_database::on_exesql($sql);
		if($arr["code"]!=0) return $arr;
		return array("code"=>0);
	}
	//卸载：清除数据
	function uninstall_data() {
		$file = KJ_DIR_DATA . "/package/" . self::$com_name . "/uninstall.sql";
		if(!file_exists($file)) return array("code"=>0);
		$sql = file_get_contents($file);
		$arr = cls_database::on_exesql($sql);
		if($arr["code"]!=0) return $arr;
		return array("code"=>0);
	}
	//安装：配置
	function install_config() {
		$arr_config = $this->config;
		//添加后台菜单
		$arr = include(KJ_DIR_DATA . "/menu/admin.php");
		if(!isset($arr["组件"])) $arr["组件"] = array();
		foreach($arr_config['menu'] as $item) {
			$is_in = false;
			foreach($arr["组件"] as $menu) {
				if($item['url'] == $menu['url'] ) {
					$is_in = true;
					break;
				}
			}
			if(!$is_in) $arr["组件"][] = $item;
		}
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/menu/admin.php",$val,1);

		//添加权限
		$arr = include(KJ_DIR_DATA . "/limit/admin.php");
		if(!isset($arr["other"])) $arr["other"] = array( "name" => "组件" , "list" => array() );
		foreach($arr_config['limit'] as $item => $key) {
			$arr["other"]["list"][$item] = $key;
		}
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/limit/admin.php",$val,1);
		
		//标识已安装
		$arr_version = include(KJ_DIR_DATA . "/config/cfg.version.php");
		$arr_version['components']['sms'] = array('name' => '短信' , 'version' => '1.0' , 'installtime' => date("Y-m-d H:i:s") , 'updatetime' => date("Y-m-d H:i:s") , 'author' => '由克');
		$val=var_export($arr_version,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/cfg.version.php",$val,1);

		//添加字段信息
		$arr = include(KJ_DIR_DATA . "/config/admin/other.php");
		$arr["other.sms"] = array(
			"sms_id" => array("val" => 0,"w" => 0), //id
			"sms_content" => array("val" => 1,"w" => 200), //发送内容
			"sms_tel" => array("val" => 1,"w" => 100), //接收号码
			"sms_type" => array("val" => 0,"w" => 80), //类型
			"sms_time" => array("val" => 1,"w" => 120), //发送时间
			"sms_about_id" => array("val" => 1,"w" => 80), //相关id
			"sms_recont" => array("val" => 1,"w" => 100), //回复内容
			"sms_retime" => array("val" => 1,"w" => 120), //回复时间
		);
		$arr["other.sms.re"] = array(
			"re_id" => array("val" => 0,"w" => 0), //id
			"re_tel" => array("val" => 1,"w" => 100), //回复号码
			"re_cont" => array("val" => 1,"w" => 300), //回复内容
			"re_time" => array("val" => 1,"w" => 120), //回复时间
		);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/admin/other.php",$val,1);

		//添加语言信息
		$arr = include(KJ_DIR_DATA . "/language/chinese/database.php");
		$arr["other.sms"] = array(
			"sms_id" => "id",
			"sms_content" => "发送内容",
			"sms_tel" => "接收号码",
			"sms_type" => "类型",
			"sms_time" => "发送时间",
			"sms_about_id" => "相关id",
			"sms_recont" => "回复内容",
			"sms_retime" => "回复时间",
		);
		$arr["other.sms.re"] = array(
			"re_id" => "id",
			"re_tel" => "回复号码",
			"re_cont" => "回复内容",
			"re_time" => "回复时间",
		);
		
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/language/chinese/database.php",$val,1);
		//添加配置信息
		$arr = cls_obj::db()->get_one("select config_val,config_id from " . cls_config::DB_PRE . "sys_config where config_module='sys' and config_name='config_module'");
		if(!empty($arr["config_id"]) && !stristr($arr['config_val'] , 'sms=&gt;') )	{
			$arr["config_val"] .= chr(10) . "sms=&gt;短信";
			tab_sys_config::on_saveall(array($arr));
		}
		$arr_config = array(
			array("config_name"=>"state","config_val"=>"1","config_intro"=>"开启短信" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"bool","config_module"=>"sms","config_sort"=>1),
			array("config_name"=>"count_id","config_val"=>"","config_intro"=>"短信账号(需要从官网购买)" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"str","config_module"=>"sms","config_sort"=>2),
			array("config_name"=>"count_pwd","config_val"=>"","config_intro"=>"短信密码" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"str","config_module"=>"sms","config_sort"=>3),
			array("config_name"=>"cancel_call_user ","config_val"=>"1","config_intro"=>"店铺短信取消订单时，是否将取消信息转发给用户" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"bool","config_module"=>"sms","config_sort"=>4),
			array("config_name"=>"cancel_user_beta ","config_val"=>"#shopname#提醒您：由于#cont#，您可以登录网站重新订餐","config_intro"=>"当店铺取消订单时，提示用户的消息内容 #cont# 为店铺回复的内容,可以调整其位置" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"textarea","config_module"=>"sms","config_sort"=>5),
		);
		if($arr_version['module']=='meal_mall') {
			$arr_config[] = array("config_name"=>"neworder_shop_tips","config_val"=>"","config_intro"=>"收到新订单时发给店有的短信内容（非短信详情店铺)" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"textarea","config_module"=>"sms","config_sort"=>6);
		}
		if($arr_version['module']=='meal') {
			$arr_config[] = array("config_name"=>"neworder_sms_tel","config_val"=>"","config_intro"=>"收到新订单时，短信通知的手机号，可为多个手机将随机发送，为空则不发短信" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"str","config_module"=>"sms","config_sort"=>6);
		}
		foreach($arr_config as $item) {
			$arr = tab_sys_config::on_save($item);
		}
		return array("code"=>0);
	}

	//卸载：清除配置
	function uninstall_config() {
		$arr_config = $this->config;
		//清除后台菜单
		$arr = include(KJ_DIR_DATA . "/menu/admin.php");

		if(isset($arr["组件"])) {
			$arr_list = array();
			foreach($arr['组件'] as $item) {
				$is_in = false;
				foreach($arr_config['menu'] as $menu) {
					if($item['url'] == $menu['url'] ) {
						$is_in = true;
						break;
					}
				}
				if(!$is_in) $arr_list[] = $item;
			}
			$arr['组件'] = $arr_list;
		}
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/menu/admin.php",$val,1);

		//添加权限
		$arr = include(KJ_DIR_DATA . "/limit/admin.php");
		if(isset($arr["other"]["list"]["sms"])) unset($arr["other"]["list"]["sms"]);
		if(isset($arr["other"]["list"]["sms.re"])) unset($arr["other"]["list"]["sms.re"]);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/limit/admin.php",$val,1);
		
		//标识已安装
		$arr = include(KJ_DIR_DATA . "/config/cfg.version.php");
		if(isset($arr['components']['sms'])) unset($arr['components']['sms']);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/cfg.version.php",$val,1);

		//移除字段信息
		$arr = include(KJ_DIR_DATA . "/config/admin/other.php");
		if(isset($arr["other.sms"])) unset($arr["other.sms"]);
		if(isset($arr["other.sms.re"])) unset($arr["other.sms.re"]);		
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/admin/other.php",$val,1);

		//移除语言信息
		$arr = include(KJ_DIR_DATA . "/language/chinese/database.php");
		if(isset($arr["other.sms"])) unset($arr["other.sms"]);
		if(isset($arr["other.sms.re"])) unset($arr["other.sms.re"]);		
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/language/chinese/database.php",$val,1);
		//删除配置信息
		$arr = cls_obj::db()->get_one("select config_val,config_id from " . cls_config::DB_PRE . "sys_config where config_module='sys' and config_name='config_module'");
		if(!empty($arr["config_val"]) && stristr($arr['config_val'] , 'sms=&gt;') )	{
			$arr["config_val"] = str_replace(chr(10) . "sms=&gt;短信" , "" , $arr["config_val"]);
			$arr["config_val"] = str_replace("sms=&gt;短信" , "" , $arr["config_val"]);
			tab_sys_config::on_saveall(array($arr));
		}
		cls_obj::db_w()->on_exe("delete from " . cls_config ::DB_PRE . "sys_config where config_module='sms'");
		return array("code"=>0);
	}
}
?>