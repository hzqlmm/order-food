<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class mod_report_top extends inc_mod_meal {
	//用户销售排行榜
	function get_user_top() {
		$obj_db = cls_obj::db();
		$where  = " where order_state>=0";

		$mode = fun_get::get("mode");
		if($mode == 'year') {
			$year = fun_get::get("year" , date("Y"));
			$where .= " and left(order_day,4)='" . $year . "'";
		} else if($mode == 'month') {
			$year = fun_get::get("year" , date("Y"));
			$month = fun_get::get("month" , date("m"));
			if(strlen($month)<2) $month = "0" . $month;
			$date = $year . "-" . $month;
			$where .= " and left(order_day,7)='" . $date . "'";
		} else {
			$date = fun_get::get("date" , date("Y-m-d"));
			$where .= " and order_day='" . $date . "'";
		}
		$arr_top = $arr_user_id = array();
		$obj_result = $obj_db->select("select order_user_id,sum(order_total_pay) as total,count(1) as num from " . cls_config::DB_PRE . "meal_order" . $where . " group by order_shop_id order by total limit 0,10");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_user_id[] = $obj_rs["order_user_id"];
			$arr_top[] = $obj_rs;
		}
		if(count($arr_user_id)>0) {
			$arr_username = cls_obj::get("cls_user")->get_user($arr_user_id);
			for($i = 0; $i < count($arr_top) ; $i++) {
				$arr_top[$i]['user_name'] = array_search($arr_top[$i]['order_user_id'],$arr_username);
			}
		}
		return $arr_top;
	}
}