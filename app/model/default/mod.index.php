<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_index extends inc_mod_default {

	/* 获取店铺菜品列表，按分类分组
	 * 
	 */
	function get_menu_list($index_group = 'price' , $sort = ''){
		if(empty($index_group)) $index_group = 'price';
		$obj_db = cls_obj::db();
		$arr_menu = $arr_cart = $arr_cart_id = $arr_price_list = array();
		$arr = $this->get_cart_info();
		if(isset($arr['ids'])) $arr_cart_id = $arr['ids'];
		if(isset($arr['cart'])) $arr_cart = $arr['cart'];
		$arr_return = array("list"=>array(),"tj"=>array(),'price'=>array() , "group" => array());
		$arr_group_id = $arr_price = array();
		$where = $this->get_menu_today_where();
		if(!empty($sort)) {
			$sortby = $sort;
		} else {
			$sortby = "b.group_sort,a.menu_sort";
		}
		//当前星期值
		$weekday = date("w");
		$day = (int)date("d");
		$obj_result = $obj_db->select("select menu_id,menu_type,menu_intro,menu_group_id,menu_title,menu_price,menu_comment_num,menu_sold,menu_tj,menu_pic,menu_pic_small,menu_num,menu_attribute,menu_mode,menu_holiday,menu_weekday,menu_mode,menu_weekday,group_name,menu_date,menu_sold_time,menu_sold_today from " . cls_config::DB_PRE . "meal_menu a left join " . cls_config::DB_PRE . "meal_menu_group b on a.menu_group_id=b.group_id " . $where ." order by " . $sortby);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			//如果是按星期上，不在范围内就排除
			if($obj_rs["menu_mode"]==1 && !stristr("," . $obj_rs["menu_weekday"] . "," , ",".$weekday.",")) continue;
			//如果是按日期上，不在范围内就排除
			if($obj_rs["menu_mode"]==3 && !stristr("," . $obj_rs["menu_date"] . "," , ",".$day."," )) continue;
			if(empty($obj_rs["menu_pic_small"])) $obj_rs["menu_pic_small"] = $obj_rs["menu_pic"];
			if(empty($obj_rs["menu_pic"])) $obj_rs["menu_pic"] = $obj_rs["menu_pic_small"];
			$obj_rs["menu_pic_small"] = fun_get::html_url($obj_rs["menu_pic_small"]);
			$obj_rs["menu_pic"] = fun_get::html_url($obj_rs["menu_pic"]);
			$arr = explode("." , $obj_rs["menu_price"]);
			$obj_rs["price_int"] = $arr[0];
			$obj_rs["price_float"] = $arr[1];
			//当前数量
			if(empty($obj_rs['menu_num'])) {
				$obj_rs['num'] = 9999;
			} else {
				$obj_rs['num'] = ($obj_rs['menu_sold_time']> strtotime(date("Y-m-d")) ) ? $obj_rs['menu_num']-$obj_rs['menu_sold_today'] : $obj_rs['menu_num'];
			}
			if(empty($sort)) {
				if($index_group == 'group') {
					$arr_return["list"]["group_" . $obj_rs["menu_group_id"]]["list"][] = $obj_rs;
				} else {
					$arr_price_list[$obj_rs["menu_price"]][] = $obj_rs;
				}
				//初始购物车时用到
				if(in_array($obj_rs["menu_id"] , $arr_cart_id)) $arr_menu["id_". $obj_rs["menu_id"]] = $obj_rs;
				if(!in_array( $obj_rs["menu_group_id"] , $arr_group_id) ) {
					if($index_group == 'group') {
						$arr_return["list"]["group_" . $obj_rs["menu_group_id"]]["id"] = $obj_rs["menu_group_id"];
						$arr_return["list"]["group_" . $obj_rs["menu_group_id"]]["name"] = $obj_rs["group_name"];
					}
					$arr_group_id[] = $obj_rs["menu_group_id"];
					$arr_return['group'][] = array("id"=>$obj_rs['menu_group_id'] , "name" => $obj_rs['group_name']);
				}
				if(!empty($obj_rs['menu_tj'])) {
					$arr_return['tj'][] = $obj_rs;
				}
			} else {
				$arr_return["list"][] = $obj_rs;
			}
		}
		if($index_group == 'price' && empty($sort)) {
			krsort($arr_price_list);
			foreach($arr_price_list as $item => $key) {
				$arr_return["list"]["group_" . $item]["id"] = $item;
				$arr_return["list"]["group_" . $item]["name"] = "￥" . $item;
				$arr_return["list"]["group_" . $item]["list"] = $key;
				if(!in_array( $item , $arr_price) ) {
					$arr_return['price'][] = array("id"=>$item,"name"=>"￥" . $item);
					$arr_price[] = $item;
				}
			}
		}

		$arr_return['cart_menu'] = $arr_menu;
		$arr_return['cart'] = $arr_cart;
		return $arr_return;
	}
	/* 获取购物车列表
	 *
	 */
	function get_cart_list() {
		$arr_cart = $this->get_cart_info();
		$menuids = implode("," , $arr_cart["menu_ids"]);
		$arr_menu = array();
		$obj_db = cls_obj::db();
		//取菜品信息
		if(!empty($menuids)) {
			$obj_result = $obj_db->select("select menu_price,menu_id,menu_title,menu_pic,menu_pic_small from " . cls_config::DB_PRE . "meal_menu where menu_id in(".$menuids.")");
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$obj_rs["menu_price"] = $obj_rs["menu_price"];
				if(empty($obj_rs["menu_pic_small"])) $obj_rs["menu_pic_small"] = $obj_rs["menu_pic"];
				if(empty($obj_rs["menu_pic"])) $obj_rs["menu_pic"] = $obj_rs["menu_pic_small"];
				$obj_rs["menu_pic_small"] = fun_get::html_url($obj_rs["menu_pic_small"]);
				$obj_rs["menu_pic"] = fun_get::html_url($obj_rs["menu_pic"]);
				$arr_menu["id_" . $obj_rs["menu_id"]] = $obj_rs;
			}
		}
		$arr_dispatch = $arr_arrive = array();
		$lng_delay = 0;
		$dispatch_isnull = 0;//支持的送货地区是否为空，只要有一家店为空，则为空
		$ticket = 1;
		$arr_1 = array();
		$jj = 1;
		$num = $total_price = 0;
		$shop_mode = (int)cls_config::get("shop_mode" , "meal");
		foreach($arr_cart['cart'] as $item) {
			sort($item);
			$arr = array('index' => $jj , "ids" => $item , "price" => 0);
			foreach($item as $menu) {
				$arr['price'] += $arr_menu["id_" . $menu]['menu_price'];
			}
			$num++;
			$x = implode("_" , $item);
			if(isset($arr_1[$x])) {
				$arr_1[$x]['price'] = $arr_1[$x]['price']/$arr_1[$x]['num'];
				$arr_1[$x]['num']++;
				$arr_1[$x]['price'] = $arr_1[$x]['price']*$arr_1[$x]['num'];
			} else {
				$arr['num'] = 1;
				$arr_1[$x] = $arr;
				$jj++;
			}
			$total_price += $arr['price'];
		}
		//发票
		$arr_ticket = array("0"=>"暂不提供");
		if($ticket) {
			$arr_ticket = cls_config::get("ticket_list","meal");
		}
		$lng_delay = (int)cls_config::get("arrive_delay" , "meal");
		$arr_arrive = cls_config::get("arrive_time" , "meal");
		$arr_arrive = $this->get_arrive_time($arr_arrive , $lng_delay);
		$arr_return =  array("cart"=>$arr_1 , "arrivetime" => $arr_arrive , "menu"=>$arr_menu , "ticket" => $arr_ticket ,"price" => $total_price , "num" => $num);
		return $arr_return;
	}
	/* 购物车信息
	 *
	 */
	function get_cart_info() {
		//取当购物车信息
		$cart_ids = fun_get::get("cart_ids");
		if(empty($cart_ids)) $cart_ids = cls_obj::get("cls_session")->get_cookie("cart_ids");
		$arr_return = $this->format_cart($cart_ids);
		return $arr_return;
	}


	//取送餐时间
	function get_arrive_time($arr , $delay) {
		$lng_time = date("Hi")+40+$delay;
		$lng_time_now = date("Hi");
		$arr_new = array();
		foreach($arr as $item=>$key) {
			if($item < $lng_time) continue;
			$arr_new[$item] = $key;
		}
		return $arr_new;
	}
	//根据指定id 获取文章内容
	function get_article($id , $fid = 0) {
		$obj_db = cls_obj::db();
		if(!empty($id)) {
			$where = " where article_id='" . $id . "'";
		} else {
			$where = " where article_folder_id='" . $fid . "' order by article_id desc";
		}
		$obj_rs = $obj_db->get_one("select * from " . cls_config::DB_PRE ."article" . $where);
		if(!empty($obj_rs)) {
			$obj_rs["article_content"] = fun_get::filter($obj_rs["article_content"] , true);
		}
		return $obj_rs;
	}
	//获取地区html列表
	function get_area($pid = 0) {
		$arr_return = array("list" => "" , "default" => array() ,"depth" => 0 , "area" => "");
		$obj_db = cls_obj::db();
		$arr_area = $arr_default = $arr_list = array();
		$str_where = "";
		if($pid>0) $str_where = " where " . cls_db::concat("," , "area_pids" , ",") ." like '%," . $pid . ",%'";
		$obj_result = $obj_db->query("select area_id,area_name,area_pid,area_depth,area_val,area_dispatch_price as 'dispatch_price',area_dispatch_time as 'dispatch_time' from " . cls_config::DB_PRE . "sys_area" . $str_where . " order by area_depth,area_sort,area_name");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			if(empty($obj_rs["area_name"])) $obj_rs["area_name"] = $obj_rs["area_val"];
			if(empty($obj_rs["area_val"])) $obj_rs["area_val"] = $obj_rs["area_name"];
			if($obj_rs['area_val'] == $obj_rs['area_name']) unset($obj_rs['area_val']);
			$arr_list["id_" . $obj_rs["area_pid"]][] = $obj_rs["area_id"];
			$arr_area["id_" . $obj_rs["area_id"]] = $obj_rs;
			if($obj_rs["area_pid"] == $pid) $arr_return["default"][] = $obj_rs;
			if($arr_return["depth"]<$obj_rs['area_depth']) $arr_return["depth"] = $obj_rs['area_depth'];
		}
		$arr_return["area"] = $arr_area;
		$arr_return["list"] = $arr_list;
		return $arr_return;
	}
	//取首页活动信息
	function get_activitie() {
		$arr = array();
		$obj_db = cls_obj::db();
		$obj_result = $obj_db->select("select channel_name,channel_id,article_title,article_id from " . cls_config::DB_PRE . "article_channel a left join " . cls_config::DB_PRE . "article b on a.channel_id=b.article_channel_id where channel_key='activitie' and article_state>0 and article_isdel=0 order by b.article_id limit 0,10");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr[] = $obj_rs;
		}
		return $arr;
	}
	function get_article_list($channel_id) {
		$arr_return = array("list" => array() , "pagebtns" => "");
		$obj_db = cls_obj::db();
		$lng_page = (int)fun_get::get("page");
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("com.msg"  , $this->app_dir);
		$lng_pagesize = $arr_config_info["pagesize"];
		$str_where = " where article_isdel=0 and article_state>0 and article_channel_id='" . $channel_id . "'";
		//取分页信息
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."article" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."article" . $str_where . " order by article_id desc" . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$arr_return["list"][] = $obj_rs;
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}
	function get_comment_list($id) {
		$lng_pagesize = 10;
		$arr_return = array("list" => array() , "pagebtns" => "");
		$obj_db = cls_obj::db();
		$lng_page = (int)fun_get::get("page");
		$str_where = " where comment_menu_id='" . $id . "'";
		//取分页信息
		$arr_val = array("好吃" => 1 , "一般" => 0 , "难吃" => -1);
		$arr_return["list"] = array();
		$arr_uid = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."meal_menu_comment" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT comment_addtime,comment_val,comment_user_id FROM ".cls_config::DB_PRE."meal_menu_comment" . $str_where . " order by comment_id desc" . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$obj_rs['val'] = array_search($obj_rs['comment_val'] , $arr_val);
			$obj_rs['addtime'] = date("Y-m-d H:i" , $obj_rs['comment_addtime']);
			$obj_rs['user_name'] = '';
			$arr_uid[] = $obj_rs['comment_user_id'];
			$arr_return["list"][] = $obj_rs;
		}
		if(count($arr_uid)>0) {
			$user_info = cls_obj::get("cls_user")->get_user($arr_uid);
			$count = count($arr_return["list"]);
			for($i = 0 ; $i < $count ; $i++) {
				$arr_return["list"][$i]['user_name'] = array_search($arr_return["list"][$i]['comment_user_id'] , $user_info);
			}
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}
	function get_comment_shop_list() {
		$lng_pagesize = 10;
		$arr_return = array("list" => array() , "pagebtns" => "");
		$obj_db = cls_obj::db();
		$lng_page = (int)fun_get::get("page");
		$str_where = "";
		//取分页信息
		$arr_val = array("好吃" => 1 , "一般" => 0 , "难吃" => -1);
		$arr_return["list"] = array();
		$arr_uid = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."meal_order_comment" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT comment_addtime,comment_val,comment_user_id,comment_beta FROM ".cls_config::DB_PRE."meal_order_comment" . $str_where . " order by comment_id desc" . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$obj_rs['val'] = array_search($obj_rs['comment_val'] , $arr_val);
			$obj_rs['addtime'] = date("Y-m-d H:i" , $obj_rs['comment_addtime']);
			$obj_rs['user_name'] = '';
			$arr_uid[] = $obj_rs['comment_user_id'];
			$arr_return["list"][] = $obj_rs;
		}
		if(count($arr_uid)>0) {
			$user_info = cls_obj::get("cls_user")->get_user($arr_uid);
			$count = count($arr_return["list"]);
			for($i = 0 ; $i < $count ; $i++) {
				$arr_return["list"][$i]['user_name'] = array_search($arr_return["list"][$i]['comment_user_id'] , $user_info);
			}
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}

}