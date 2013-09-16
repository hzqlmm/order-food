<?php
class install_com_email{
	static $com_name = "com.email.1.0";
	public $config = array(
				"limit" => array( 'email' => array( "act"=>array("edit","save","delete","send") , "name"=>"邮件管理" )),
				"menu" => array( array( "url"=>"?app_module=other&app=email" , "name" => "邮件管理" , "app" => "email" ,"app_module" => "other")
				)
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
			$path = str_replace(KJ_DIR_DATA . "/package/" . self::$com_name . "/lib" , KJ_DIR_LIB , $item['path']);
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
		$arr = include(KJ_DIR_DATA . "/config/cfg.version.php");
		$arr['components']['email'] = array('name' => '邮件' , 'version' => '1.0' , 'installtime' => date("Y-m-d H:i:s") , 'updatetime' => date("Y-m-d H:i:s") , 'author' => '麦兜');
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/cfg.version.php",$val,1);

		//添加字段信息
		$arr = include(KJ_DIR_DATA . "/config/admin/other.php");
		$arr["other.email"] = array(
			"email_id" => array("val" => 0,"w" => 0), //id
			"email_title" => array("val" => 1,"w" => 300), //标题
			"email_account_mode" => array("val" => 0,"w" => 50), //模式
			"email_to" => array("val" => 1,"w" => 80), //收件箱
			"email_from" => array("val" => 1,"w" => 80), //发件箱
			"email_addtime" => array("val" => 1,"w" => 80), //发送时间
			"email_num" => array("val" => 1,"w" => 80), //发送次数
		);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/admin/other.php",$val,1);

		//添加语言信息
		$arr = include(KJ_DIR_DATA . "/language/chinese/database.php");
		$arr["other.email"] = array(
			"email_id" => "id",
			"email_title" => "标题",
			"email_account_mode" => "模式",
			"email_to" => "收件箱",
			"email_from" => "发件箱",
			"email_addtime" => "发送时间",
			"email_num" => "发送次数",
		);
		
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/language/chinese/database.php",$val,1);
		//添加配置信息
		$arr = cls_obj::db()->get_one("select config_val,config_id from " . cls_config::DB_PRE . "sys_config where config_module='sys' and config_name='config_module'");
		if(!empty($arr["config_id"]) && !stristr($arr['config_val'] , 'email=&gt;') )	{
			$arr["config_val"] .= chr(10) . "email=&gt;邮件";
			tab_sys_config::on_saveall(array($arr));
		}
		$arr_config = array(
			array("config_name"=>"from ","config_val"=>"","config_intro"=>"设置发件人的邮箱地址" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"str","config_module"=>"email"),
			array("config_name"=>"fromname","config_val"=>"","config_intro"=>"设置发件人的姓名" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"str","config_module"=>"email"),
			array("config_name"=>"host ","config_val"=>"","config_intro"=>"设置邮件服务器的地址" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"str","config_module"=>"email"),
			array("config_name"=>"password","config_val"=>"","config_intro"=>"邮件服务器登陆密码" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"str","config_module"=>"email"),
			array("config_name"=>"port","config_val"=>"25","config_intro"=>"设置邮件服务器的端口，默认为25" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"str","config_module"=>"email"),
			array("config_name"=>"username","config_val"=>"","config_intro"=>"邮件服务器登陆账号" ,"config_readonly"=>"0","config_list"=>"","config_type"=>"str","config_module"=>"email"),
		);
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
		if(isset($arr["other"]["email"])) unset($arr["other"]["email"]);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/limit/admin.php",$val,1);
		
		//标识已安装
		$arr = include(KJ_DIR_DATA . "/config/cfg.version.php");
		if(isset($arr['components']['email'])) unset($arr['components']['email']);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/cfg.version.php",$val,1);

		//移除字段信息
		$arr = include(KJ_DIR_DATA . "/config/admin/other.php");
		if(isset($arr["other.email"])) unset($arr["other.email"]);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/admin/other.php",$val,1);

		//移除语言信息
		$arr = include(KJ_DIR_DATA . "/language/chinese/database.php");
		if(isset($arr["other.email"])) unset($arr["other.email"]);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/language/chinese/database.php",$val,1);
		//删除配置信息
		$arr = cls_obj::db()->get_one("select config_val,config_id from " . cls_config::DB_PRE . "sys_config where config_module='sys' and config_name='config_module'");
		if(!empty($arr["config_val"]) && stristr($arr['config_val'] , 'sms=&gt;') )	{
			$arr["config_val"] = str_replace(chr(10) . "email=&gt;邮件" , "" , $arr["config_val"]);
			$arr["config_val"] = str_replace("email=&gt;邮件" , "" , $arr["config_val"]);
			tab_sys_config::on_saveall(array($arr));
		}
		cls_obj::db_w()->on_exe("delete from " . cls_config ::DB_PRE . "sys_config where config_module='email'");

		return array("code"=>0);
	}
}
?>