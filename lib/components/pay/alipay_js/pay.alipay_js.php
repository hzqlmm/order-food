<?php
require_once(dirname(__FILE__) . "/alipay_service.class.php");
class pay_alipay_js
{
	function get_config() {
		$arr_paymethod = cls_config::get("alipay_js" , "pay" , "" , "");
		$arr_config = array();
		$arr_config["partner"] = $arr_paymethod["fields"]["parterid"];
		$arr_config["key"] = $arr_paymethod["fields"]["key"];
		$arr_config["email"] = $arr_paymethod["fields"]["email"];
		$arr_config["return_url"] = cls_config::get("url" , "base")."/common.php?app=pay&app_act=return&paymethod=alipay_js";
		$arr_config["notify_url"] = cls_config::get("url" , "base")."/common.php?app=pay&app_act=notify&paymethod=alipay_js";
		$arr_config["goods_count"]=1;
		$arr_config["input_charset"] = "utf-8"; //字符编码格式  目前支持 GBK 或 utf-8
		$arr_config["sign_type"] = "MD5"; //加密方式  系统默认(不要修改)
		$arr_config["transport"]= "http";//访问模式,你可以根据自己的服务器是否支持ssl访问而选择http以及https访问模式(系统默认,不要修改)
		return $arr_config;
	}
	function on_pay($arr_fields=array())
	{
		$arr_return=array("html" => "" ,"code" => 0 , "msg" => "");
		$arr_config = self::get_config();
		$arr_config["show_url"] = ""; //你网站商品的展示地址,可以为空
		$arr_config["subject"] = $arr_fields["subject"];
		$arr_config["orderid"] = $arr_fields["orderid"];
		$arr_config["price"] = $arr_fields["price"];

		if(!isset($arr_config["partner"]) || empty($arr_config["partner"]) ) return array('code' => 500 , 'msg' => '合作伙伴ID不能为空！');

		if(!isset($arr_config["key"]) || empty($arr_config["key"]) ) return array('code' => 500 , 'msg' => '安全检验码不能为空！');

		if(!isset($arr_config["email"]) || empty($arr_config["email"]) )  return array('code' => 500 , 'msg' => '支付宝帐户不能为空！');

		if(!isset($arr_config["subject"]) || empty($arr_config["subject"]))  return array('code' => 500 , 'msg' => '支付标题不能为空！');

		if(!isset($arr_config["orderid"]) || empty($arr_config["orderid"]) )  return array('code' => 500 , 'msg' => '定单id不能为空！');

		if(!isset($arr_config["price"])) return array('code' => 500 , 'msg' => '商品金额不能为空！');

		if(!isset($arr_config["detail"])) $arr_config["detail"]="";
		if(!isset($arr_config["body"])) $arr_config["body"]="";

		//构造要请求的参数数组
		$parameter = array(
				"service"			=> "create_direct_pay_by_user",	//接口名称，不需要修改
				"payment_type"		=> "1",               				//交易类型，不需要修改

				//获取配置文件(alipay_config.php)中的值
				"partner"			=> trim($arr_config["partner"]),
				"seller_email"		=> trim($arr_config["email"]),
				"return_url"		=> trim($arr_config["return_url"]),
				"notify_url"		=> trim($arr_config["notify_url"]),
				"_input_charset"	=> trim($arr_config["input_charset"]),
				"show_url"			=> trim($arr_config["show_url"]),

				//从订单数据中动态获取到的必填参数
				"out_trade_no"		=> $arr_config["orderid"],
				"subject"			=> $arr_config["subject"],
				"body"				=> $arr_config["body"],
				"total_fee"				=> $arr_config["price"],
				//扩展功能参数——网银提前
				"paymethod"	      => "directPay",
				"defaultbank"	  => "",

				//扩展功能参数——防钓鱼
				"anti_phishing_key"=> "",
				"exter_invoke_ip"  => fun_get::ip(),
				//扩展功能参数——自定义参数
				"buyer_email"	   => "",
				"extra_common_param" => "" ,
				"royalty_type"		=> "",
				"royalty_parameters"=> ""
			
		);

		$alipay = new AlipayService($arr_config);
		$arr_return["html"] = $alipay->create_direct_pay_by_user($parameter);
		return $arr_return;
	}
	function on_return() {
		$arr_config = self::get_config();
		require_once("alipay_notify.class.php");
		$alipayNotify = new AlipayNotify($arr_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			$arr_return = array(
				'code' => 0,
			    'number' => fun_get::get('out_trade_no'),	//获取订单号
			    'tradeid' => fun_get::get('trade_no'),		//获取支付宝交易号
			    'val' => (float)fun_get::get('total_fee')		//获取总价格
			);
			return $arr_return;
		} else {
			return array('code' => 500 , 'msg' => '验证失败');
		}

	}
	function on_notify() {
		$arr_config = self::get_config();
		require_once("alipay_notify.class.php");
		$alipayNotify = new AlipayNotify($arr_config);
		$verify_result = $alipayNotify->verifyNotify();
		if($verify_result) {//验证成功
			$arr_return = array(
				'code' => 0,
			    'number' => fun_get::post('out_trade_no'),	//获取订单号
			    'tradeid' => fun_get::post('trade_no'),		//获取支付宝交易号
			    'val' => (float)fun_get::post('total_fee')		//获取总价格
			);
			return $arr_return;
		} else {
			return array('code' => 500 , 'msg' => '验证失败');
		}

	}
}
?>