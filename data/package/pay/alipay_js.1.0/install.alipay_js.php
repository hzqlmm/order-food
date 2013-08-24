<?php
class install_pay_alipay_js{
	static $com_name = "alipay_js.1.0";
	public $config = array('alipay_js' => 
						  array (
							'name' => '支付宝即时到账交易接口',
							'pic' => 'http://www.klkkdj.com/webcss/api/images/pay_alipay.gif',
							'version' => '1.0',
							'installtime' => '2012-08-26',
							'currency' => 'rmb',
							'state' => '1',
							'fields' => 
							array (
							  'email' => '',
							  'title' => '支付宝支付',
							  'parterid' => '',
							  'key' => '',
							  'feetype' => '1',
							  'feeval' => '0',
							  'intro' => '',
							),
						  ),
					);

	function get_install_steps() {
		return array(
			array("name"=>"复制文件","step"=>"copy"),
			array("name"=>"配置文件","step"=>"config"),
		);
	}
	function get_uninstall_steps() {
		return array(
			array("name"=>"删除文件","step"=>"copy"),
			array("name"=>"清除配置","step"=>"config"),
		);
	}
	//安装：复制文件
	function install_copy() {
		//复制app目录
		$arr = fun_file::dir_copy( KJ_DIR_DATA . "/package/pay/" . self::$com_name . "/alipay_js" , "alipay_js" , KJ_DIR_LIB . "/components/pay");
		return array("code"=>0);
	}
	//卸载：删除文件
	function uninstall_copy() {
		//删除app目录下文件
		$dir_path = KJ_DIR_LIB . "/components/pay/alipay_js";
		fun_file::dir_delete($dir_path);
		return array("code"=>0);
	}
	//安装：配置
	function install_config() {
		$arr_config = $this->config;		
		//标识已安装
		$arr = include(KJ_DIR_DATA . "/config/cfg.pay.php");
		foreach($arr_config as $key => $item) {
			$arr[$key] = $item;
		}
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/cfg.pay.php",$val,1);

		return array("code"=>0);
	}

	//卸载：清除配置
	function uninstall_config() {
		$arr_config = $this->config;		
		//标识已安装
		$arr = include(KJ_DIR_DATA . "/config/cfg.pay.php");
		foreach($arr_config as $key => $item) {
			if(isset($arr[$key])) unset($arr[$key]);
		}
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		fun_file::file_create(KJ_DIR_DATA . "/config/cfg.pay.php",$val,1);

		return array("code"=>0);
	}
}
?>