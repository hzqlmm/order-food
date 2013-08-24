<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_ajax extends inc_mod_default {
	//保存定单
	function save_order(){
		$arr_return=array("code"=>0,"msg"=>"下单成功");
		//是否登录
		if(!cls_obj::get("cls_user")->is_login()) {
			return array("code" => 500 , "msg" => "请先登录再来订餐");
		}

		$obj_db = cls_obj::db_w();
		//取定单信息
		$cart_ids = fun_get::get("cart_ids");
		$arr_cart = $this->format_cart($cart_ids);
		if(count($arr_cart['ids'])<1) {
			$arr_return["code"]=1;
			$arr_return["msg"]="请选择您要点的菜品";
			return $arr_return;
		}
		$arr = explode(":" , $cart_ids);
		$menu_ids = str_replace("|" , "," , $arr[1]);
		$str_ids = $arr[1];


		$area_select = (int)fun_get::get("area_select");
		if($area_select > 0) {
			//取已有地址信息
			$arr_info = $obj_db->get_one("select * from " . cls_config::DB_PRE . "meal_info where info_id='".$area_select."'");
			if(empty($arr_info)) {
				$arr_return["code"]=1;
				$arr_return["msg"]="选择的送货信息不存在";
				return $arr_return;
			}
		} else {
			//保存新地址
			$arr_info=array(
				"info_user_id" => cls_obj::get("cls_user")->uid,
				"info_name" => fun_get::post("name"),
				"info_area_id"  => fun_get::post("area_id"),
				"info_area_allid"  => fun_get::post("area_allid"),
				"info_area"  => fun_get::post("area"),
				"info_louhao1" => fun_get::post("louhao1"),
				"info_louhao2" => fun_get::post("louhao2"),
				"info_company" => fun_get::post("company"),
				"info_depart" => fun_get::post("depart"),
				"info_sex" => fun_get::post("sex"),
				"info_tel" => fun_get::post("tel"),
				"info_telext" => fun_get::post("telext"),
				"info_mobile" => fun_get::post("mobile"),
			);
			if(empty($arr_info['info_area'])) {
				return array("code"=>500,"msg"=>"没有选择区域");
			}
			$arr = $this->save_info($arr_info);
			if($arr["code"] != 0){
				$arr_return['code']=101;
				$arr_return['msg']=$arr['msg'];
				return $arr_return;
			}
		}
		$arr_order=array(
			"order_user_id" => cls_obj::get("cls_user")->uid,
			"order_name" => $arr_info["info_name"],
			"order_shop_id" => "0",
			"order_area_id" => $arr_info["info_area_id"],
			"order_area_allid" => $arr_info["info_area_allid"],
			"order_area" => $arr_info["info_area"],
			"order_louhao1" => $arr_info["info_louhao1"],
			"order_louhao2" => $arr_info["info_louhao2"],
			"order_company" => $arr_info["info_company"],
			"order_depart" => $arr_info["info_depart"],
			"order_sex" => $arr_info["info_sex"],
			"order_tel" => $arr_info["info_tel"],
			"order_telext" => $arr_info["info_telext"],
			"order_mobile" => $arr_info["info_mobile"],
			"order_arrive" => fun_get::post("arrive"),
			"order_ticket" => (int)fun_get::post("ticket"),
			"order_ids" => $str_ids,
			"order_beta" => fun_get::post("beta"),
			"order_pay_method" => fun_get::post("paymethod"),
		);
		$score_mode = (int)cls_config::get("score_mode" , "meal");
		if($score_mode > 0) $arr_order['order_isaward'] = -1;
		if(!in_array($arr_order['order_pay_method'] , array('afterpayment' , 'repayment'))) $arr_order['order_state'] = -2;//等待支付
		//计价
		//取菜品信息
		$arr_today_menu = array();
		$weekday = date("w");
		$mode_day = date("d");
		$today_date = strtotime(date('Y-m-d' , TIME));
		$str_ids = implode("," , $arr_cart["menu_ids"]);
		$obj_result = $obj_db->select("select * from " . cls_config::DB_PRE . "meal_menu where menu_id in(" . $str_ids . ")");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			if($obj_rs["menu_state"]<1) {
				$arr_return['code']=500;
				$arr_return['msg']="选择菜品【" . $obj_rs["menu_name"] . "】不存在,请重新选择后再提交";
				return $arr_return;
			}
			//按星期
			if($obj_rs['menu_mode'] == 1 && !stristr(",".$weekday."," , "," . $obj_rs["menu_weekday"] . ",")) {
				$arr_return['code']=500;
				$arr_return['msg']="选择菜品【" . $obj_rs["menu_name"] . "】今天暂不提供,请重新选择后再提交";
				return $arr_return;
			}
			//按日期
			if($obj_rs['menu_mode'] == 3 && !stristr(",".$mode_day."," , "," . $obj_rs["menu_date"] . ",")) {
				$arr_return['code']=500;
				$arr_return['msg']="选择菜品【" . $obj_rs["menu_name"] . "】今天暂不提供,请重新选择后再提交";
				return $arr_return;
			}
			//计算当前剩余量
			$sold = 0;
			if($obj_rs["menu_sold_time"] > strtotime(date("Y-m-d"))) $sold = $obj_rs["menu_sold_today"];
			($obj_rs["menu_num"]>0) ? $obj_rs["num"] = $obj_rs["menu_num"] - $sold : $obj_rs["num"] = 0;
			//如果是自定义的，则按自定义数量
			if($obj_rs["menu_mode"] == 2) {
				$arr_today_menu[] = $obj_rs["menu_id"];
			} else {
				$num1 = substr_count("," . $menu_ids . "," , $obj_rs["menu_id"]);
				if($obj_rs["menu_num"]>0 && $obj_rs["num"] < $num1) {
					$arr_return["code"]=1;
					if($obj_rs["num"]>0) {
						$arr_return["msg"]="【".$obj_rs["menu_title"]."】当前只剩" . $obj_rs["num"] . "份，请重新选择";
					} else {
						$arr_return["msg"]="【".$obj_rs["menu_title"]."】当天已售完，请选择其它美食";
					}
				return $arr_return;
				}
			}
			$arr_menu["id_".$obj_rs["menu_id"]] = $obj_rs;
		}

		//如果有当天菜品
		if(count($arr_today_menu)>0) {
			//取每天自定义菜品
			$arr_opentime = $this->get_opentime();
			$date_period = $arr_opentime['nowindex'];
			if($date_period>0) {
				$where_today = " and (today_date_period='".$date_period."' or today_date_period=0)";
			} else {
				$where_today = " and today_date_period='".$date_period."'";
			}

			$str_ids = implode("," , $arr_today_menu);
			$obj_result = $obj_db->select("select today_menu_id,today_num,today_sold from " . cls_config::DB_PRE . "meal_menu_today where today_menu_id in(" . $str_ids . ") and today_date='" . $today_date . "'" . $where_today);
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$num = 0;
				if($obj_rs["today_num"]>0) $num = $obj_rs["today_num"]-$obj_rs["today_sold"];
				$num1 = substr_count("," . $menu_ids . "," , $obj_rs["today_menu_id"]);
				if($obj_rs["today_num"] > 0 && $num < $num1) {
					$arr_return["code"]=1;
					if($num>0) {
						$arr_return["msg"]="【".$arr_menu["id_" . $obj_rs["today_menu_id"]]["menu_title"]."】当前只剩" . $num . "份，请重新选择";
					} else {
						$arr_return["msg"]="【".$arr_menu["id_" . $obj_rs["today_menu_id"]]["menu_title"]."】当天已售完，请选择其它美食";
					}
					return $arr_return;
				}
				$arr_menu["id_" . $obj_rs["today_menu_id"]]["num"] = $num;
			}
		}
		$lng_score_money = (int)fun_get::post("score") * 100;
		$lng_total=0;
		//保存当前菜普价格
		$menu_price = array();
		$arr_a = explode("," , $menu_ids);
		foreach($arr_a as $item){
			if(!isset($arr_menu["id_".$item])) {
				$arr_return["code"] = 500;
				$arr_return["msg"] = "选择菜品没找到，清空购物车重新选择";
				return $arr_return;
			}
			$lng_total+=$arr_menu["id_".$item]["menu_price"];
			$menu_price['id_'.$item] = $arr_menu["id_".$item]['menu_price'];
		}
		$arr_order["order_detail"] = array("menu_price" => $menu_price);
		//总价
		$arr_order["order_total"]=$lng_total;
		if($lng_score_money>0) {
			//扣除积分
			$score_money_scale = cls_config::get("score_money_scale" , "meal");
			if(empty($score_money_scale)) {
				$arr_return["code"]=1;
				$arr_return["msg"]="未设置积分兑换比例，无法完成订单";
				return $arr_return;
			}
			if(empty($score_mode)) {
				$arr_return["code"]=500;
				$arr_return["msg"]="本店暂不支持积分抵扣";
				return $arr_return;
			}
			$arr_order["order_score_money"] = $lng_score_money * $score_money_scale;
			$lng_total = $lng_total - $arr_order["order_score_money"];
			if($lng_total<0) {
				$arr_return["code"]=500;
				$arr_return["msg"]="兑换积分不能超出定单价";
				return $arr_return;
			}

		}
		$score_total = $lng_score_money + $arr_order["order_ticket"];
		if($score_total>0) {
			$user_score = cls_obj::get("cls_user")->get_score();
			if($user_score < $score_total) {
				$arr_return["code"]=500;
				$arr_return["msg"]="本次订单需要消耗【" . $score_total . "】积分，当前积分仅为【" . $user_score . "】" . "请重新选择再提交";
				return $arr_return;
			}
		}
		$arr_order["order_favorable"] = 0;
		//是否满足活动
		$shop_act_id = fun_get::post("shop_act_id");
		if(!empty($shop_act_id) && is_array($shop_act_id)) {
			$num = count(explode("|" , $arr_order['order_ids']));
			$shop_act_id= $shop_act = array();
			$arr_act_where = $this->get_shop_act($arr_order['order_total'] , $num);
			foreach($arr_act_where as $item) {
				if($item['act_method'] == '2') {//达到指定金额
					$arr_order["order_favorable"] += $arr_order["order_total"] - $arr_order["order_total"]*(float)$item['act_method_val'];
				} else if($item['act_method'] == '5') {//按立减
					$arr_order["order_favorable"] += (float)$item['act_method_val'];
				} else if($item['act_method'] == '6') {//每份减
					$item['act_method_val'] = (float)$item['act_method_val'];
					$arr_order["order_favorable"] = $arr_order["order_favorable"] + $item['act_method_val'] * $num;
				} else if($item['act_method'] == '3') {//赠送固定积分
					$arr_order["order_detail"]['score_add'] = (int)$item['act_method_val'];
				} else if($item['act_method'] == '4') {//积分翻倍
					$arr_order["order_detail"]['score_multiple'] = (float)$item['act_method_val'];
				}
				$shop_act_id[] = $item['act_id'];
				$shop_act[] = $item['act_name'];
			}
			$lng_total = $lng_total - $arr_order["order_favorable"];//减优惠
			$arr_order['order_act_ids'] = implode(",",$shop_act_id);
			$arr_order['order_act'] = $shop_act;
		}
		$obj_db->begin("saveorder");
		if($lng_score_money>0) {
			//扣分
			$arr = tab_sys_user_action::on_action($arr_order["order_user_id"] , "meal_submit_order" , array("score" => $lng_score_money) );
			if($arr["code"]!=0) {
				$obj_db->rollback("saveorder");
				return $arr;
			}
		}
		//扣减发票分
		if($arr_order["order_ticket"]>0) {
			$arr = tab_sys_user_action::on_action($arr_order["order_user_id"] , "meal_submit_ticket" , array("score" => $arr_order["order_ticket"]) );
			if($arr["code"]!=0) {
				$obj_db->rollback("saveorder");
				return $arr;
			}
		}
		//去除优惠与抵扣后的应付
		$arr_order["order_total_pay"] = $lng_total;
		$arr = tab_meal_order::on_save($arr_order);
		if($arr["code"] != 0){
			$obj_db->rollback("saveorder");
			$arr_return['code']=101;
			$arr_return['msg']=$arr['msg'];
			return $arr_return;
		}
		//如果是预付款支付
		if($arr_order['order_pay_method'] == 'repayment') {
			$repayment = cls_obj::get("cls_user")->get_repayment();
			if($lng_total>$repayment) {
				$obj_db->rollback("saveorder");
				return array("code"=>500 , "msg"=>"您当前预付款金额不足以本次支付");
			}
			$arr_msg = tab_sys_user_repayment::on_order_pay($arr_order['order_user_id'] , $lng_total , $arr["id"] , "订餐支付");
			if($arr_msg['code']!=0) {
				$obj_db->rollback("saveorder");
				return $arr_msg;
			}
			//设置支付信息
			$arr_order['order_pay_over'] = 1;
			$arr_order['order_pay_time'] = date("Y-m-d H:i:s");
			if(isset($arr_msg['id'])) $arr_order['order_pay_id'] = $arr_msg['id'];
		}
		$arr_return["id"] = $arr["id"];
		//确认事务
		$obj_db->commit("saveorder");
		//修改已定数量
		$arr= $arr_cart["menu_ids"];
		foreach($arr as $item){
			if($arr_menu['id_'.$item]['menu_num']<1) continue;
			if(in_array($item , $arr_today_menu)) {
				//自定义菜品
				$num = substr_count("," . $menu_ids . "," ,"," . $item . ",");
				$arr_x = $obj_db->on_exe("update ".cls_config::DB_PRE."meal_menu_today set today_sold=today_sold+'".$num."' where today_menu_id='".$item."' and today_date='".$today_date."' and today_date_period='".$today_date_period."'" . $where_today);
				$arr_x = $obj_db->on_exe("update ".cls_config::DB_PRE."meal_menu set menu_sold=menu_sold+" . $num . " where menu_id='".$item."'");
			}else {
				$num1 = substr_count("," . $menu_ids . "," ,"," . $item . ",");
				$num = $arr_menu['id_'.$item]['menu_num'] - $arr_menu['id_'.$item]['num']+$num1;
				$arr_x=$obj_db->on_exe("update ".cls_config::DB_PRE."meal_menu set menu_sold_today='".$num."',menu_sold=menu_sold+" . $num1 . ",menu_sold_time='".TIME."' where menu_id='".$item."'");
			}
		}
		//清除购物车信息
		cls_session::set_cookie("cart_ids","");
		//发送短信提醒
		$neworder_sms_tel = cls_config::get("neworder_sms_tel" , "sms");
		if(!empty($neworder_sms_tel) ) {//订单提醒短信
			$ii = 1;
			$arr_i = array();
			foreach($arr as $item) {
				$arr_x = array();
				$price = 0;
				foreach($item as $next) {
					$arr_x[] = $arr_menu["id_" . $next]["menu_title"];
					$price+=$arr_menu["id_".$next]["menu_price"];
				}
				$str = implode(" + " , $arr_x);
				if(!isset($arr_i[$str])) {
					$arr_i[$str] = array('name' => $str , "num" => 1 , "price" => $price);
				} else {
					$arr_i[$str]['num']++;
				}
				$ii++;
			}
			$arr_j = array();
			foreach($arr_i as $key =>$item) {
				$str = $item['name'];
				if($item['num']>1) $str .= " " . $item['num'] . "份";
				$str .=  " ￥" . $item['price']*$item['num'];
				$arr_j[] = $str;
			}
			$score_money = (isset($arr_order["order_score_money"])) ? $arr_order["order_score_money"] : 0;
			$cont = "新订单：" . implode("；" , $arr_j) . " 合计：" . $arr_order["order_total"] . "；抵扣：" . $score_money . "；应收：" . $arr_order["order_total_pay"];
			if($arr_order["order_ticket"]>0) $cont .= "；发票：" . $arr_order["order_ticket"];
			//抵达时间
			$arr = unserialize($obj_shop["shop_extend"]);
			if( isset($arr["arr_arrivetime"]) && isset($arr["arr_arrivetime"][$arr_order['order_arrive']] )) {
				$cont .= "；" . $arr["arr_arrivetime"][$arr_order['order_arrive']] . "之前";
			}
			$cont .= "；" . $arr_info["info_name"] . "/" . $arr_info["info_sex"] . "；" . $this->area_lou_name;
			//取地区
			if(!empty($arr_order["order_area3"])) {
				$obj_rs = $obj_db->get_one("select area_val,area_name from " . cls_config::DB_PRE . "sys_area where area_id='" . $arr_order["order_area3"] . "'");
				if(!empty($obj_rs)) {
					$cont .= "/" . (empty($obj_rs["area_val"])) ? $obj_rs["area_name"] : $obj_rs["area_val"];
				}
			}
			if(!empty($arr_order["order_louhao1"])) {
				$cont .= "；" . $arr_order["order_louhao1"] . "楼";
				if(!empty($arr_order["order_louhao2"])) {
					$cont .= $arr_order["order_louhao2"] . "室";
				}
			}
			if(!empty($arr_order["order_company"])) {
				$cont .= "；" . $arr_order["order_company"];
				if(!empty($arr_order["order_depart"])) {
					$cont .= "/" . $arr_order["order_depart"];
				}
			}

			if(!empty($arr_order["order_tel"])) {
				$cont .= "；固话:" . $arr_order["order_tel"];
				if(!empty($arr_order["order_telext"])) {
					$cont .= "转" . $arr_order["order_telext"];
				}
			} 
			if(!empty($arr_order["order_mobile"])) {
				$cont .= "；手机:" . $arr_order["order_mobile"];
			}
			//取订单后五位为确认码，每天最大只能处理99999条订单
			$id = $arr_return["id"];
			if($id>=100000) $id = substr($id,-5);
			$cont .= "；确认码：" . $id;
			$arr_tel = explode("," , $neworder_sms_tel);
			$tel = $arr_tel[rand(0,count($arr_tel)-1)];//随机一个电话
			$arr = cls_obj::get('cls_com')->sms("on_send" , array("tel"=>$tel , "cont" => $cont ,"id" => $arr_return["id"] ,"confirm_id" => $id , "type"=>1) );
			
		}
		return $arr_return;
	}


	//删除指定id信息
	function on_del_info() {
		$get_id = (int)fun_get::get("id");
		$arr_info = tab_meal_info::on_delete('' , "info_id='" . $get_id . "' and info_user_id='" . cls_obj::get("cls_user")->uid . "'");
		return $arr_info;
	}
	//保存指定id收货信息
	function on_save_info() {
		//保存新地址
		$arr_info=array(
			"info_id" => (int)fun_get::get("id"),
			"info_user_id" => cls_obj::get("cls_user")->uid,
			"info_name" => fun_get::post("name"),
			"info_area_id"  => fun_get::post("area_id"),
			"info_area_allid"  => fun_get::post("area_allid"),
			"info_area"  => fun_get::post("area"),
			"info_louhao1" => fun_get::post("louhao1"),
			"info_louhao2" => fun_get::post("louhao2"),
			"info_company" => fun_get::post("company"),
			"info_depart" => fun_get::post("depart"),
			"info_sex" => fun_get::post("sex"),
			"info_tel" => fun_get::post("tel"),
			"info_telext" => fun_get::post("telext"),
			"info_mobile" => fun_get::post("mobile"),
			"info_email" => fun_get::post("email"),
		);
		$arr = $this->save_info($arr_info);
		if($arr["code"] != 0){
			$arr_return['code']=101;
			$arr_return['msg']=$arr['msg'];
			return $arr_return;
		}
		return $arr;
	}
	function save_info($arr_info) {
		$arr_return=array("code"=>0,"msg"=>"保存成功");
		if(empty($arr_info["info_name"])){
			$arr_return["code"]=1;
			$arr_return["msg"]="请填写收件人信息";
			return $arr_return;
		}
			
		if(empty($arr_info["info_louhao1"])){
			$arr_return["code"]=1;
			$arr_return["msg"]="请填写您所在的具体位置";
			return $arr_return;
		}
		if(empty($arr_info["info_tel"]) && empty($arr_info["info_mobile"])){
			$arr_return["code"]=1;
			$arr_return["msg"]="手机号码与固定电话必须填一项";
			return $arr_return;
		}
		$arr = tab_meal_info::on_save($arr_info);
		if($arr["code"] != 0){
			$arr_return['code']=101;
			$arr_return['msg']=$arr['msg'];
			return $arr_return;
		} else {
			$arr_return["id"] = $arr["id"];
		}
		return $arr_return;
	}
	//用户中心修改密码
	function on_useredit(){
		$lng_user_id = cls_obj::get('cls_user')->uid;
		$id=(int)fun_get::get("id");
		if(cls_obj::get("cls_user")->is_admin() && $id>0){
			$lng_user_id=$id;
		}
		$arr_return=array("code"=>0,"msg"=>"保存成功");
		$obj_db = cls_obj::db_w();
		$arr_pwd=array(
			"oldpwd" => fun_get::post("oldpwd"),
			"pwd1" => fun_get::post('pwd1'),
			"pwd2" =>fun_get::post("pwd2")
		);
		if($arr_pwd["pwd1"]!=$arr_pwd["pwd2"]){
			$arr_return['code'] = 500;
			$arr_return['msg'] = "两次输入密码不一至！";
			return $arr_return;
		}
		$arr = cls_obj::get("cls_user")->on_update_pwd($arr_pwd['oldpwd'],$arr_pwd['pwd1']);
		return $arr;
	}

	function on_reg(){
		$arr_return=array("code"=>0,"msg"=>"注册成功");
		$str_pwd1 = fun_get::post('pwd1');
		$str_pwd2 = fun_get::post('pwd2');
		if($str_pwd1 != $str_pwd2){
			$arr_return["code"]=1;
			$arr_return["msg"]="两次输入密码不一致";
			return $arr_return;
		}
		$reg_switch = (int)cls_config::get("reg_switch" , "user");
		$reg_invite_code = cls_config::get("reg_invite_code" , "user");
		$reg_switch_info = cls_config::get("reg_switch_info" , "user");
		if($reg_switch == 1) {
			$arr_return["code"]=500;
			if(empty($reg_switch_info)) $reg_switch_info = "网站关闭了新用户注册功能";
			$arr_return["msg"]=$reg_switch_info;
			return $arr_return;
		} else if($reg_switch == 2) {
			$invite_code = fun_get::post('invite_code');
			if($invite_code != $reg_invite_code) {
				$arr_return["code"]=500;
				$arr_return["msg"] = "邀请码输入不正确";
				return $arr_return;
			}
		}
		$verifycode = fun_get::post("verifycode");
		$isverify = (cls_obj::get("cls_session")->get('verify_reg') > 0) ? false : true;
		if(cls_config::get('rule_uname','user')=='email') {
			$arr = tab_sys_verify::on_verify($verifycode , 0 , 3 , $isverify);
			if($arr['code'] != 0) return array('code' => 500 , 'msg' => '邮箱验证码有误');
			cls_obj::get("cls_session")->set('verify_reg' , 1);//设置已验证标识
		} else if(cls_config::get('rule_uname','user')=='mobile') {
			$arr = tab_sys_verify::on_verify($verifycode , 0 , 4 , $isverify);
			if($arr['code'] != 0) return array('code' => 500 , 'msg' => '短信验证码有误');
			cls_obj::get("cls_session")->set('verify_reg' , 1);//设置已验证标识
		} else if(cls_config::get('reg_verify' , 'user')){
		//是否需要验证码
			if(cls_verifycode::on_verify($verifycode) == false) {
				$arr_return["code"] = 11;
				$arr_return["msg"]  = cls_language::get("verify_code_err");
				return $arr_return;
			}
		}
		$arr_user=array(
			"user_name" => fun_get::post("uname"),
			"user_pwd" => $str_pwd1,
		);
		//注册用户
		$arr = cls_obj::get("cls_user")->on_reg($arr_user);
		if($arr["code"] != 0){
			return $arr;
		} else {
			$arr_login=array( "user_name"=>$arr_user["user_name"],"user_pwd"=>$arr_user["user_pwd"]);
			$arr=cls_obj::get("cls_user")->on_login($arr_login);
			if($arr["code"]!=0){
				return $arr;
			}
		}
		return $arr_return;
	}

	function on_findpwd_step1() {
		$verifycode = fun_get::get("verifycode");
		if(cls_verifycode::on_verify($verifycode) == false) {
			return array('code' => '11' , 'msg' => '验证码输入错误');
		}
		$uname = fun_get::get("uname");
		$arr_user = cls_obj::get("cls_user")->get_user($uname , false);
		if(empty($arr_user) || !isset($arr_user[$uname])) return array('code' => '500' , 'msg' => '输入账号不存在');
		$obj_rs = cls_obj::db()->get_one("select user_email,user_mobile from " . cls_config::DB_PRE . "sys_user where user_id='" . $arr_user[$uname] . "'");
		if(empty($obj_rs)) {
			return array('code' => '500' , 'msg' => '注册账号没有邦定个人信息');
		}
		$arr_return = array('code' => '0');
		$arr_return['is_sms'] = (fun_is::com('sms'))? "1" : "0";
		$arr_return['is_email'] = (fun_is::com('email'))? "1" : "0";
		$arr_return['is_msg'] = (fun_is::com('msg'))? "1" : "0";
		$arr = explode("@" , $obj_rs['user_email']);
		if(strlen($arr[0])>3) {
			$arr_return['email'] = str_pad(substr($arr[0],0,3),strlen($arr[0]),"*") . "@" . $arr[1];
		} else {
			$arr_return['email'] = str_pad(substr($arr[0],0,1),strlen($arr[0]),"*") . "@" . $arr[1];
		}
		$arr_return['mobile'] = str_pad(substr($obj_rs['user_mobile'],0,3),strlen($obj_rs['user_mobile'])-4,"*") . substr($obj_rs['user_mobile'],-4);
		return $arr_return;
	}
	function on_findpwd_step2() {
		$method = (int)fun_get::get("method");
		$uname = fun_get::get("uname");
		$arr_user = cls_obj::get("cls_user")->get_user($uname , false);
		if(empty($arr_user) || !isset($arr_user[$uname])) return array('code' => '500' , 'msg' => '输入账号不存在');

		$obj_user = cls_obj::db()->get_one("select user_id,user_email,user_mobile from " . cls_config::DB_PRE . "sys_user where user_id='" . $arr_user[$uname] . "'");
		if(empty($obj_user)) {
			return array('code' => '500' , 'msg' => '输入账号不存在');
		}
		if($method == 1) {
			$arr_key = tab_sys_verify::get_key($obj_user['user_id'],1);
			if($arr_key['code']!=0) return $arr_key;
			$url = cls_config::get("url" , 'base') . "/index.php?app_act=findpwd.email&key=" . $arr_key['key'];
			//取邮件内容
			$obj_cont = cls_obj::db()->get_one("select article_title,article_content from " . cls_config::DB_PRE . "article where article_key='findpwdwords'");
			if(empty($obj_cont)) {
				$obj_cont['article_title'] = cls_config::get("site_title" , "sys") . "找回密码";
				$obj_cont['article_content'] = "<a href='".$url."'>请点击链接重置登录密码</a>，如果未操作，系统将保留原密码<br>如果无法点击请复制以下代码，粘贴到浏览器地址栏访问<br>" . $url;
			} else {
				$obj_cont['article_content'] = fun_get::filter($obj_cont['article_content'] , true);
				$obj_cont['article_content'] = str_replace('{$url}' , $url , $obj_cont['article_content']);
			}
			$arr = cls_obj::get("cls_com")->email('send' , array('to_mail' => $obj_user['user_email'] , 'title' => $obj_cont['article_title'] , 'content' => $obj_cont['article_content'] ,'save' => 1));
			return $arr;
		} else if($method == 2) {
			$arr_key = tab_sys_verify::get_key($obj_user['user_id'],2);
			if($arr_key['code']!=0) return $arr_key;
			$arr = cls_obj::get('cls_com')->sms("on_send" , array("tel"=>$obj_user['user_mobile'] , "cont" => "【" . cls_config::get("site_title" , "sys") . "】您的验证码：" . $arr_key['key'] . ",请在网页上输入此号码，如非本人操作请忽略" ) );
			return $arr;
		} else if($method == 3) {
			$arr_fields = array(
				"msg_name" => fun_get::get('name'),
				"msg_tel" => fun_get::get('tel'),
				"msg_cont" => fun_get::get('cont'),
				"msg_type" => 1,
				"msg_user_id" => $obj_user['user_id']
			);
			$arr = cls_obj::get("cls_com")->msg('on_save',$arr_fields);
			return $arr;
		} else {
			return array("code" => 500 , "msg" =>"传递参数有误");
		}
	}

	function on_findpwd_step3() {
		$isverify = cls_obj::get("cls_session")->get('sms_verify');
		$key = fun_get::get("key");
		$uname = fun_get::get("uname");
		$uid = fun_get::get("uid");
		$pwd = fun_get::get("pwd");
		if($uid < 1 ) {
			$arr_user = cls_obj::get("cls_user")->get_user($uname , false);
			if(empty($arr_user) || !isset($arr_user[$uname])) return array('code' => '500' , 'msg' => '验证账号不存在');
			$uid = $arr_user[$uname];
		} else {
			$arr_user = cls_obj::get("cls_user")->get_user($uid);
			if(empty($arr_user) || !in_array($uid , $arr_user)) return array('code' => '500' , 'msg' => '验证账号不存在');
			$uname = array_search($uid , $arr_user);
		}
		if($isverify != $uid) return array("code"=>500 , "msg" => "验证已过期，请重新验证");
		
		$arr = cls_obj::get("cls_user")->on_update_pwd('' , $pwd , $uid , false);
		if($arr["code"] != 0){
			$arr_return['code']=500;
			$arr_return['msg']=$arr['msg'];
			return $arr_return;
		}
		//注销标识
		cls_obj::get("cls_session")->destroy('sms_verify');
		return array("code"=>0,"msg"=>'');
	}

	function on_verify_mobile() {
		$uname = fun_get::get("uname");
		$key = fun_get::get("key");
		$arr_user = cls_obj::get("cls_user")->get_user($uname , false);
		if(empty($arr_user) || !isset($arr_user[$uname])) return array('code' => '500' , 'msg' => '验证账号不存在');
		$arr = tab_sys_verify::on_verify($key , $arr_user[$uname] , 2);
		if($arr['code'] == 0) {
			$isverify = cls_obj::get("cls_session")->set('sms_verify' , $obj_user['user_id']);//设置已验证标识
		}
		return $arr;
	}
	function on_msg_save() {
		$options = cls_config::get("msg_options","sys");
		if(in_array('login',$options) && cls_obj::get("cls_user")->is_login()==false) {
			return array("code" => 500 , "msg" => "需要登录才能留言");
		}
		$arr_fields = array(
			"msg_email" => fun_get::get('email'),
			"msg_name" => fun_get::get('name'),
			"msg_tel" => fun_get::get('tel'),
			"msg_cont" => fun_get::get('cont'),
			"msg_user_id" => cls_obj::get("cls_user")->uid
		);
		if(in_array('email',$options) && empty($arr_fields['msg_email'])) {
			return array("code" => 500 , "msg" => "邮箱不能为空");
		}
		if(in_array('tel',$options) && empty($arr_fields['msg_tel'])) {
			return array("code" => 500 , "msg" => "电话不能为空");
		}
		if(in_array('name',$options) && empty($arr_fields['msg_name'])) {
			return array("code" => 500 , "msg" => "名称不能为空");
		}
		$arr = cls_obj::get("cls_com")->msg('on_save',$arr_fields);
		return $arr;

	}
}