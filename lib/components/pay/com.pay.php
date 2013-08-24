<?php
/* 支付接口 */
class com_pay {
	function get_config($val) {
		static $config = array();
		if(empty($config)) {
			$config = array(
				'type' => array("订餐付款" => 1 , "预付款充值" => 2),
				'state' => array("充值成功" => 1 , "等待付款" => 0 , "充值失败" => -1)
			);
		}
		 return (isset($config[$val]))? $config[$val] : '';
	}
	/* 支付操作
	 * arr_fields 包括：pay_method => 支付方式 ，pay_title => 支付标题，pay_about_id => 相关id ，pay_val => 价格，pay_beta => 备注 , pay_user_id => 用户id
	 * pay_type => 支付类型
	 */
	function on_pay($arr_fields = array()) {
		$arr_return=array("url"=>"","err"=>"");

		if(!isset($arr_fields["pay_method"])) return array('code' => 500 , 'msg' => '支付接口不能为空！');
		if(!isset($arr_fields['pay_type']) || empty($arr_fields['pay_type']) ) return array('code' => 500 , 'msg' => '支付类型不能为空');
		$arr_paymethod = cls_config::get("" , "pay" , "" , "");
		if(!isset($arr_paymethod[$arr_fields["pay_method"]])) return array('code' => 500 , 'msg' => '支付接口不存在！');
		if(!isset($arr_fields['pay_val']) || empty($arr_fields['pay_val']) ) return array('code' => 500 , 'msg' => '支付金额不正确');
		if(!isset($arr_fields['pay_beta'])) $arr_fields['pay_beta'] = '';
		//保存支付记录
		$arr_payinfo = self::on_insert($arr_fields);
		if($arr_payinfo['code']!=0) return $arr_payinfo;

		//取支付接口需要的参数
		$arr_config = array(
			'pay_method' => $arr_fields['pay_method'],
			'subject' => $arr_fields['pay_title'],
			'detail' => $arr_fields['pay_beta'],
			'price' => $arr_fields['pay_val'],
			'orderid' => $arr_payinfo['number'],
		);

		$file_path = dirname(__FILE__) . "/" . $arr_fields["pay_method"] . "/pay." .  $arr_fields["pay_method"] . ".php";
		if(!file_exists($file_path))  return array('code' => 500 , 'msg' => '支付接口不存在！');
		require_once( $file_path );
		$class_name = "pay_" . $arr_fields["pay_method"];
		$obj_pay=new $class_name;
		$arr_return=$obj_pay->on_pay($arr_config);
		return $arr_return;
	}
	
	//保存支付记录
	function on_insert($arr_fields) {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		$arr_fields["pay_addtime"] = TIME;
		$obj_db = cls_obj::db_w();
		$arr_fields["pay_number"] = $arr_fields["pay_about_id"] . date("ymdHis") . $arr_fields["pay_user_id"];//相关id+时间+用户id
		$arr = $obj_db->on_insert(cls_config::DB_PRE."other_pay",$arr_fields);
		if($arr['code'] == 0) {
			$arr_return['id'] = $obj_db->insert_id();
			//其它非mysql数据库不支持insert_id 时
			if(empty($arr_return['id'])) {
				$where  = "pay_user_id='" . $arr_fields['pay_user_id'] . " and pay_about_id='".$arr_fields['pay_about_id'] . "' and pay_addtime='".$arr_fields["pay_addtime"]."'";
				$obj_rs = $obj_db->get_one("select pay_id from ".cls_config::DB_PRE."other_pay where ".$where);
				if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['pay_id'];
			}
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = cls_language::get("db_edit");
		}
		$arr_return["number"] = $arr_fields["pay_number"];
		return $arr_return;
	}

	//处理支付返回消息
	function on_return() {
		$pay_method = fun_get::get("paymethod");
		if(empty($pay_method)) return array('code'=>500 , 'msg' => '支付方式不正确');

		$file_path = dirname(__FILE__) . "/" . $pay_method . "/pay." .  $pay_method . ".php";
		if(!file_exists($file_path))  return array('code' => 500 , 'msg' => '支付接口不存在！');
		require_once( $file_path );
		$class_name = "pay_" . $pay_method;
		$obj_pay=new $class_name;
		//调用接口，处理消息 , 返回：number => 订单流水号 , val => 充值金额
		$arr_info = $obj_pay->on_return();
		if($arr_info['code']!=0) return $arr_info;
		
		$arr_msg = self::_return_exe($arr_info);

		return $arr_msg;
	}
	//处理支付返回消息
	function on_notify() {
		$pay_method = fun_get::get("paymethod");
		if(empty($pay_method)) return array('code'=>500 , 'msg' => '支付方式不正确');

		$file_path = dirname(__FILE__) . "/" . $pay_method . "/pay." .  $pay_method . ".php";
		if(!file_exists($file_path))  return array('code' => 500 , 'msg' => '支付接口不存在！');
		require_once( $file_path );
		$class_name = "pay_" . $arr_fields["pay_method"];
		$obj_pay=new $class_name;
		//调用接口，处理消息 , 返回：number => 订单流水号 , val => 充值金额
		$arr_info = $obj_pay->on_notify();
		if($arr_info['code']!=0) return $arr_info;
		
		$arr_msg = self::_return_exe($arr_info);

		return $arr_msg;
	}

	//处理返回
	function _return_exe($arr_info) {

		//取支付信息
		$obj_db = cls_obj::db_w();
		$obj_pay = $obj_db->get_one("select pay_id , pay_number, pay_val , pay_method , pay_type , pay_about_id , pay_state ,pay_user_id from " . cls_config::DB_PRE . "other_pay where pay_number='" . $arr_info['number'] . "'");
		if(empty($obj_pay)) return array('code' => 500 , 'msg' => '支付信息错误，支付订单未找到');
		if($obj_pay['pay_state']>0) return array('code' => 0 , 'msg' => '支付成功');

		 //订餐支付
		if($obj_pay['pay_type']==1) {
			//将订单设为已支付状态
			$arr_msg = $obj_db->on_exe("update " . cls_config::DB_PRE . "meal_order set order_state=0 , order_pay_time='" . date("Y-m-d H:i:s") . "',order_pay_val=" . $arr_info['val'] . "',order_pay_id=" . $obj_pay['pay_id'] . " where order_id='" . $obj_pay['pay_about_id'] . "'");
		}
		 //预付款支付
		if($obj_pay['pay_type']==2) {
			//新增预付款
			$arr_repayment_fields = array(
				"repayment_user_id" => $obj_pay['pay_user_id'],
				"repayment_val" => $arr_info['val'],
				"repayment_beta" => '充值',
				"repayment_type" => 2,
				"repayment_about_id" => $obj_pay['pay_id'],
			);

			$arr_msg = tab_sys_user_repayment::on_recharge($arr_repayment_fields);
		}

		$arr_msg = $obj_db->on_exe("update " . cls_config::DB_PRE . "other_pay set pay_state=1 , pay_day='" . date("Y-m-d") . "',pay_time='" . date("Y-m-d H:i:s") . "',pay_return_val='" . $arr_info['val'] . "',pay_return_id='" . $arr_info['tradeid'] . "' where pay_id='" . $obj_pay['pay_id'] . "'");
		return $arr_msg;
	}
}