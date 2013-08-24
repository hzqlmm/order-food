<?php
class install_com_pay{
	static $com_name = "com.pay.1.0";
	public $config = array(
			"limit" => array( 
				'pay' => array( "act"=>array("not"=>"未安装页","config"=>"配置页","save"=>"保存配置","down"=>"下载新接口","install"=>"安装接口","uninstall"=>"卸载","record"=>"充值记录") , "name"=>"支付接口" )
				),
			"menu" => array( 
				'用户' => array( "url"=>"?app_module=other&app=pay&app_act=record" , "name" => "充值记录" , "app" => "pay" , "app_module" => "other"),
				'组件' => array( "url"=>"?app_module=other&app=pay" , "name" => "支付接口" , "app" => "pay" , "app_module" => "other")
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
		foreach($arr_config['menu'] as $key => $item) {
			$is_in = false;
			if(is_numeric($key)) {
				$arr_x = $arr["组件"];
			} else {
				$arr_x = (isset($arr[$key])) ? $arr[$key] : array();
			}
			foreach($arr_x as $menu) {
				if($item['url'] == $menu['url'] ) {
					$is_in = true;
					break;
				}
			}
			if(!$is_in) {
				(is_numeric($key)) ?  $arr["组件"][] = $item : $arr[$key][] = $item;
			}
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
		$arr['components']['pay'] = array('name' => '支付接口' , 'version' => '1.0' , 'installtime' => date("Y-m-d H:i:s") , 'updatetime' => date("Y-m-d H:i:s") , 'author' => '由克');
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/cfg.version.php",$val,1);

		//添加字段信息
		$arr = include(KJ_DIR_DATA . "/config/admin/other.php");
		$arr["other.pay"] = array(
			"pay_id" => array("val" => 0,"w" => 0), //id
			"pay_number" => array("val" => 1,"w" => 100), //订单号
			"pay_user_id" => array("val" => 1,"w" => 60), //用户id
			"pay_val" => array("val" => 1,"w" => 60), //充值金额
			"pay_time" => array("val" => 1,"w" => 120), //充值时间
			"pay_return_id" => array("val" => 0,"w" => 80), //第三方返回id
			"pay_type" => array("val" => 1,"w" => 80), //支付类型
			"pay_state" => array("val" => 1,"w" => 80), //充值状态
			"pay_about_id" => array("val" => 0,"w" => 100), //相关id
			"pay_method" => array("val" => 1,"w" => 100), //支付方式
			"pay_title" => array("val" => 1,"w" => 200), //标题
			"pay_beta" => array("val" => 1,"w" => 300), //备注
		);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/admin/other.php",$val,1);

		//添加语言信息
		$arr = include(KJ_DIR_DATA . "/language/chinese/database.php");
		$arr["other.pay"] = array(
			"pay_id" => "id",
			"pay_number" => "订单号",
			"pay_user_id" => "用户id",
			"pay_val" => "充值金额",
			"pay_return_id" => "第三方返回id",
			"pay_type" => "支付类型",
			"pay_state" => "充值状态",
			"pay_about_id" => "相关id",
			"pay_method" => "支付方式",
			"pay_title" => "标题",
			"pay_beta" => "备注",
			"pay_time" => "充值时间",
		);
		
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/language/chinese/database.php",$val,1);

		return array("code"=>0);
	}

	//卸载：清除配置
	function uninstall_config() {
		$arr_config = $this->config;
		//清除后台菜单
		$arr = include(KJ_DIR_DATA . "/menu/admin.php");
		foreach($arr_config['menu'] as $key => $item) {
			$is_in = false;
			$arr_list = array();
			if(is_numeric($key)) {
				$arr_x = $arr["组件"];
			} else {
				$arr_x = (isset($arr[$key])) ? $arr[$key] : array();
			}
			foreach($arr_x as $menu) {
				if($item['url'] != $menu['url'] ) $arr_list[] = $menu;
			}
			if(is_numeric($key)) {
				$arr["组件"] = $arr_list ;
			} else {
				$arr[$key] = $arr_list;
				if(count($arr_list)<1) unset($arr[$key]);
			}
		}

		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/menu/admin.php",$val,1);

		//添加权限
		$arr = include(KJ_DIR_DATA . "/limit/admin.php");
		if(isset($arr["other"]["list"]["pay"])) unset($arr["other"]["list"]["pay"]);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/limit/admin.php",$val,1);
		
		//标识已安装
		$arr = include(KJ_DIR_DATA . "/config/cfg.version.php");
		if(isset($arr['components']['pay'])) unset($arr['components']['pay']);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/cfg.version.php",$val,1);

		//移除字段信息
		$arr = include(KJ_DIR_DATA . "/config/admin/other.php");
		if(isset($arr["other.pay"])) unset($arr["other.pay"]);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/admin/other.php",$val,1);

		//移除语言信息
		$arr = include(KJ_DIR_DATA . "/language/chinese/database.php");
		if(isset($arr["other.pay"])) unset($arr["other.pay"]);
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/language/chinese/database.php",$val,1);

		return array("code"=>0);
	}
}
?>