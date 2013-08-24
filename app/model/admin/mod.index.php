<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_index extends inc_mod_admin {
	//登录信息
	function get_login_info() {
		$obj_rs = cls_obj::db()->get_one("select user_loginnum,user_loginip,group_name from " . cls_config::DB_PRE . "sys_user a left join " . cls_config::DB_PRE . "sys_user_group b on a.user_group_id=b.group_id where user_id='" . cls_obj::get("cls_user")->uid . "'");
		$lastlogintime = cls_obj::get("cls_user")->lastlogintime;
		$arr_return = array(
			"lastlogintime" => $lastlogintime,
			"loginnum"  => $obj_rs["user_loginnum"],
			"loginip" => $obj_rs["user_loginip"],
			"group_name" => $obj_rs["group_name"]
		);
		return $arr_return;
	}
	//服务器信息
	function get_server_info() {
		$arr_return = array(
			"php_version" => PHP_VERSION,
			"os" => PHP_OS,
			"software" => $_SERVER ['SERVER_SOFTWARE'],
			"max_memory" => get_cfg_var ("memory_limit")?get_cfg_var("memory_limit") : "",
			"max_time" => get_cfg_var("max_execution_time")."秒 ",
			"max_upload" =>  get_cfg_var("upload_max_filesize") ? get_cfg_var("upload_max_filesize") : "禁止上传",
			"mysql_maxlink" =>  @get_cfg_var("mysql.max_links")==-1 ? "不限" : @get_cfg_var("mysql.max_links"),
			"mysql_version" =>  cls_obj::db()->version(),
		);
		return $arr_return;
	}

	function get_count_info() {
		$obj_db = cls_obj::db();
		//店铺数量
		//$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "meal_shop");
		$arr_return["shop_num"] = 0;//$obj_rs["num"];
		//运营中店铺
		//$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "meal_shop where shop_state>0");
		$arr_return["shop_onlinenum"] = 0;//$obj_rs["num"];

		//所有订单
		$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "meal_order");
		$arr_return["order_allnum"] = $obj_rs["num"];
		//有效 订单总量,订单总额,实收金额,抵扣金额,发票积分
		$obj_rs = $obj_db->get_one("select count(1) as num,sum(order_total) as total,sum(order_total_pay) as total_pay,sum(order_score_money) as score_money,sum(order_ticket) as order_ticket from " . cls_config::DB_PRE . "meal_order where order_state>0");
		$arr_return["order_num"] = $obj_rs["num"];
		$arr_return["order_total"] = $obj_rs["total"];
		$arr_return["order_total_pay"] = $obj_rs["total_pay"];
		$arr_return["order_score_money"] = $obj_rs["score_money"];
		$arr_return["order_ticket"] = $obj_rs["order_ticket"];
		
		//所有 今日
		$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "meal_order where order_day='" . date('Y-m-d') . "'");
		$arr_return["today_order_allnum"] = $obj_rs["num"];
		//有效 今日
		$obj_rs = $obj_db->get_one("select count(1) as num,sum(order_total) as total,sum(order_total_pay) as total_pay,sum(order_score_money) as score_money,sum(order_ticket) as order_ticket from " . cls_config::DB_PRE . "meal_order where order_state>=0 and order_day='" . date('Y-m-d') . "'");
		$arr_return["today_order_num"] = $obj_rs["num"];
		$arr_return["today_order_total"] = $obj_rs["total"];
		$arr_return["today_order_total_pay"] = $obj_rs["total_pay"];
		$arr_return["today_order_score_money"] = $obj_rs["score_money"];
		$arr_return["today_order_ticket"] = $obj_rs["order_ticket"];
		//今日 未处理
		$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "meal_order where order_state=0 and order_day='" . date('Y-m-d') . "'");
		$arr_return["today_order_0"] = $obj_rs["num"];
		//今日 已拒绝
		$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "meal_order where order_state<0 and order_day='" . date('Y-m-d') . "'");
		$arr_return["today_order_1"] = $obj_rs["num"];
		//用户总数,积分总数
		$obj_rs = $obj_db->get_one("select count(1) as num,sum(user_score) as score from " . cls_config::DB_PRE . "sys_user where user_type='default'");
		$arr_return["user_num"] = $obj_rs["num"];
		$arr_return["score_total"] = $obj_rs["score"];
		$today_time = strtotime(date('Y-m-d'));
		//今日 新增用户总数
		$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "sys_user where user_type='default' and user_regtime>'" . $today_time . "'");
		$arr_return["user_new"] = $obj_rs["num"];
		//今日 回头用户
		$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "sys_user where user_type='default' and user_regtime<='" . $today_time . "' and user_logintime>'" . date("y-m-d" , $today_time) . "'");
		$arr_return["user_continue"] = $obj_rs["num"];
		//今日 送出积分
		$obj_rs = $obj_db->get_one("select sum(action_score) as score from " . cls_config::DB_PRE . "sys_user_action where action_day='" . date("Y-m-d") . "' and action_score>0");
		$arr_return["today_score_send"] = $obj_rs["score"];
		//今日 消耗积分
		$obj_rs = $obj_db->get_one("select sum(action_score) as score from " . cls_config::DB_PRE . "sys_user_action where action_day='" . date("Y-m-d") . "' and action_score<0");
		$arr_return["today_score_consume"] = $obj_rs["score"];
		return $arr_return;
	}
	//获取短信统计信息
	function get_sms_info() {
		$arr = array('code' => 0 ,'over' => "" , 'total' => "" , "today" => "" , "today_order" => "" , "today_re" => "" );
		$arr['code'] = (cls_obj::get('cls_com')->is('sms'))? 0 : 500;
		if($arr['code'] != 0) {
			$arr['over'] = "未安装";
			return $arr;
		}
		$arr_x = cls_obj::get('cls_com')->sms('get_over');
		$arr['code'] = $arr_x['code'];
		if($arr_x['code']==0) {
			$arr_x['num'];
			$arr['over'] = $arr_x['num'];
		} else if($arr_x['code']==502) {
			$arr['over'] = "未开通";
		} else if($arr_x['code']==503) {
			$arr['over'] = "连接失败";
			return $arr;
		} else {
			$arr['over'] = "连接失败";
			return $arr;
		}
		$obj_db = cls_obj::db();
		//发送总量
		$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "other_sms");
		$arr['total'] = $obj_rs['num'];
		//今日发送
		$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "other_sms where sms_day='" . date("Y-m-d") . "'");
		$arr['today'] = $obj_rs['num'];
		//今日订单短信
		$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "other_sms where sms_day='" . date("Y-m-d") . "' and sms_type=1");
		$arr['today_order'] = $obj_rs['num'];
		//今日订单回复
		$obj_rs = $obj_db->get_one("select count(1) as num from " . cls_config::DB_PRE . "other_sms where sms_day='" . date("Y-m-d") . "' and sms_type=1 and sms_retime=0");
		$arr['today_re'] = $obj_rs['num'];
		return $arr;
	}
	//下载安装包
	function on_down() {
		$zipname = fun_get::get("zipname");
		$cont = cls_klkkdj::down($zipname);
		if(!empty($cont)) {
			$path = KJ_DIR_DATA . "/package/" . $zipname . ".zip";
			file_put_contents($path , $cont);
			if(file_exists($path)) {
				//在线解压
				$arr = cls_zip::unzip($path);
				if($arr["code"]!=0) {
					return array("code"=>500 , "msg"=>$arr['msg']);
				}
			}
			return array("code" => 0);
		} else {
			return array("code"=>500,"msg" => "下载升级包失败，请尝试方法二手动下载");
		}
	}
	//获取安装步骤
	function get_install_steps() {
		$arr_return = array("code"=>0);
		$arr = $this->get_install_obj();
		if($arr["code"]!=0) return $arr;
		$obj_com = $arr["obj"];
		$arr_return["steps"] = $obj_com->get_install_steps();
		return $arr_return;
	}
	function get_install_obj(){
		$zipname = fun_get::get("zipname");
		$file = KJ_DIR_DATA . "/package/" . $zipname . "/install.php";
		if(!file_exists( $file )) return array("code" => 500 , "msg" => "未找到升级包");
		include_once($file);
		$cls = "install_sys";
		$obj_com = new $cls();
		return array("code"=>0 , "obj" => &$obj_com);
	}
	//安装
	function on_install() {
		$arr = $this->get_install_obj();
		if($arr["code"]!=0) return $arr;
		$step = (int)fun_get::get("step");
		$obj_com = $arr["obj"];
		$arr_steps = $obj_com->get_install_steps();
		$step = "install_" . $arr_steps[$step]['step'];
		$arr = $obj_com->$step();
		return $arr;
	}
	//预存款
	function get_user_repayment() {
		$obj_db = cls_obj::db();
		//预付总金额
		$obj_rs = $obj_db->get_one("select sum(repayment_val) as val from " . cls_config::DB_PRE . "sys_user_repayment where repayment_val>0");
		$arr['total'] = $obj_rs['val'];
		//已消费金额
		$obj_rs = $obj_db->get_one("select sum(repayment_val) as val from " . cls_config::DB_PRE . "sys_user_repayment where repayment_val<0");
		$arr['over'] = $obj_rs['val'];
		//当前金额
		$obj_rs = $obj_db->get_one("select sum(repayment_val) as val from " . cls_config::DB_PRE . "sys_user_repayment");
		$arr['now'] = $obj_rs['val'];
		//今日预存
		$obj_rs = $obj_db->get_one("select sum(repayment_val) as val from " . cls_config::DB_PRE . "sys_user_repayment where repayment_day='" . date("Y-m-d") . "' and repayment_val>0");
		$arr['today_total'] = $obj_rs['val'];
		//今日消费
		$obj_rs = $obj_db->get_one("select sum(repayment_val) as val from " . cls_config::DB_PRE . "sys_user_repayment where repayment_day='" . date("Y-m-d") . "' and repayment_val<0");
		$arr['today_over'] = $obj_rs['val'];
		return $arr;
	}

}