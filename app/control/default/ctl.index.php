<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_index extends mod_index{
	function act_default(){
		//活动公告
		$this->arr_activitie = $this->get_activitie();
		//首页默认分组
		$index_group = cls_config::get("index_group" , "view");
		$this->index_group = (empty($index_group)) ? "price" : $index_group;
		$this->arr_menu = $this->get_menu_list($index_group);
		$this->dispatch_min_price = (int)cls_config::get("dispatch_min_price" , "meal");
		$act = 'default';
		$this->shop_mode = (int)cls_config::get("shop_mode" , "meal");
		$this->food_rice_default = cls_config::get("food_rice_default" , "meal");
		if($this->shop_mode == 2 || $this->shop_mode == 3) {
			$this->optional_select = cls_config::get("optional_select" , "meal");
			$act = 'optional';
		}
		$this->opentime = tab_article::get_bykey("opentime");
		$this->shopintro = tab_article::get_bykey("shopintro");
		$this->cfg_opentime = $this->get_opentime();
		//评论数
		$obj_rs = cls_obj::db()->get_one("select count(1) as 'num' from " . cls_config::DB_PRE . "meal_order_comment");
		$this->shop_commentnum = (empty($obj_rs)) ? 0 : $obj_rs['num'];
		//菜品数
		$obj_rs = cls_obj::db()->get_one("select count(1) as 'num' from " . cls_config::DB_PRE . "meal_menu where menu_state>0 and menu_isdel=0");
		$this->shop_menunum = (empty($obj_rs)) ? 0 : $obj_rs['num'];

		return $this->get_view($act);
	}
	//分组显示
	function act_grouplist() {
		$index_group = fun_get::get("index_group");
		if(empty($index_group)) $index_group = 'price';
		$this->index_group = $index_group;
		$this->shop_mode = (int)cls_config::get("shop_mode" , "meal");
		$this->arr_menu = $this->get_menu_list($index_group);
		$act = 'default';
		if($this->shop_mode == 2 || $this->shop_mode == 3) {
			$act = 'optional';
		}
		return $this->get_view("grouplist." . $act);
	}
	//排序显示
	function act_sortlist() {
		$sortby = fun_get::get("sort");
		$sortval = fun_get::get("sortval");
		if(empty($sortby)) $sortby = 'price';
		if(empty($sortval)) {
			$sortval = 'asc';
			if($sortby != 'price') $sortval = 'desc';
		}
		$sort = "menu_" . $sortby;
		$this->sortby = $sortby;
		$this->sortval = $sortval;
		$this->shop_mode = (int)cls_config::get("shop_mode" , "meal");
		$this->arr_menu = $this->get_menu_list('' , $sort . " " . $sortval);
		$act = 'default';
		if($this->shop_mode == 2 || $this->shop_mode == 3) {
			$act = 'optional';
		}
		return $this->get_view("sortlist." . $act);
	}
	/* 购物车页
	 * 分单店与多店，当多店时，则先列出所有店及相关所点的菜品，然后选某店以单店的形式结算
	 */
	function act_cart(){
		$this->cart_list = $this->get_cart_list();
		$this->score_total = cls_obj::get("cls_user")->get_score();
		$this->verfifycode = cls_obj::get("cls_user")->is_verifycode();
		//积分选项
		$score_money_scale = cls_config::get("score_money_scale" , "meal");
		$this->score_money = intval($this->score_total * $score_money_scale);
		$this->score_money_scale = cls_config::get("score_money_scale" , "meal");
		$this->score_money_scale = (int)cls_config::get("score_mode" , "meal");
		$this->shop_mode = (int)cls_config::get("shop_mode" , "meal");
		$act = 'cart.default';
		$this->dispatch_min_price = (int)cls_config::get("dispatch_min_price" , "meal");
		if(count($this->cart_list['cart'])>0 && $this->cart_list['num'] > 0) {
			$this->this_info = $this->get_infolist();
			$x = tab_sys_user_var::get("last.area.id" , cls_obj::get("cls_user")->uid);
			if(empty($x) && count($this->this_info['list'])>0) $x = $this->this_info['list'][0]['info_id'];
			$this->last_area_id = $x;
			$this->areainfo = $this->get_area();
			//取付款方式
			$this->paymethod = cls_config::get("paymethod" , "meal");
			$this->arr_pay = cls_config::get("" , "pay" , array() , "");
			//取用户当前预付款
			$this->user_repayment = cls_obj::get("cls_user")->get_repayment();
			//取店铺营销活动
			$this->shop_act = $this->get_shop_act($this->cart_list['price'] , $this->cart_list['num']);
			if($this->shop_mode == 2 || $this->shop_mode == 3) {
				$act = 'cart.optional';
				$this->optional_select = cls_config::get("optional_select" , "meal");
			}
			return $this->get_view($act);
		} else {
			return $this->get_view("cart.null");
		}
	}

	//获取指定id收货信息
	function act_getinfo() {
		$id = (int)fun_get::get("id");
		$arr_info = cls_obj::db()->get_one("select * from " . cls_config::DB_PRE  . "meal_info where info_user_id='" . cls_obj::get("cls_user")->uid . "' and info_id='" . $id . "'");
		return fun_format::json($arr_info);
	}
	//登录页
	function act_login() {
		$jump_url = fun_get::get("jump_url");    //获取跳转地址
		if( empty($jump_url) && isset($_SERVER["HTTP_REFERER"]) ) $jump_url=$_SERVER["HTTP_REFERER"];
		$this->jump_fromurl = $jump_url;
		$this->verfifycode = cls_obj::get("cls_user")->is_verifycode();
		return $this->get_view(); //显示页面
	}
	//找回密码
	function act_findpwd() {
		$jump_url = '';
		if(isset($_SERVER["HTTP_REFERER"]) ) $jump_url=$_SERVER["HTTP_REFERER"];
		if(stristr($jump_url,"app_act=reg") || stristr($jump_url,"app_act=login") || stristr($jump_url,"app_act=findpwd")) {
			$jump_url = './';
		}
		fun_get::get("jump_url" , $jump_url);
		return $this->get_view(); //显示页面
	}
	//邮件回调找回密码
	function act_findpwd_email() {
		$key = fun_get::get("key");
		//是否为邮件认证
		$arr = array("code"=>500,'msg' => '传递参数有误' ,'uid' =>0);
		$isverify = cls_obj::get("cls_session")->get('sms_verify');
		if(!empty($key)) {
			$isverify = ($isverify>0) ? false : true;
			$arr = tab_sys_verify::on_verify($key , 0 , 1 , $isverify);
			if($arr['code'] == 0) {
				$isverify = cls_obj::get("cls_session")->set('sms_verify' , $arr['uid']);//设置已验证标识
			}
		}
		$this->info = $arr;
		return $this->get_view(); //显示页面
	}
	//注册页
	function act_reg() {
		$jump_url = fun_get::get("jump_url");    //获取跳转地址
		if( empty($jump_url) && isset($_SERVER["HTTP_REFERER"]) ) $jump_url=$_SERVER["HTTP_REFERER"];
		if(stristr($jump_url,"app_act=reg") || stristr($jump_url,"app_act=login")) {
			$jump_url = '/';
		}
		$this->jump_fromurl = $jump_url;
		$this->reg_switch = cls_config::get("reg_switch" , "user");
		$this->reg_switch_info = cls_config::get("reg_switch_info" , "user");
		//取注册协议
		$this->reg_content = tab_article::get_bykey("regargreement");
		return $this->get_view(); //显示页面
	}
	//登录页
	function act_reg_shop() {
		$jump_url = fun_get::get("jump_url");    //获取跳转地址
		if( empty($jump_url) && isset($_SERVER["HTTP_REFERER"]) ) $jump_url=$_SERVER["HTTP_REFERER"];
		$this->jump_fromurl = $jump_url;
		$this->list_area = fun_kj::get_area();
		return $this->get_view(); //显示页面
	}
	//帮助
	function act_help() {
		$this->arr_help = $this->get_folder_article('default');
		//当前文章信息
		$id = (int)fun_get::get("id");
		if(empty($id)) $id = $this->arr_help[0]['article_id'];
		$this->id = $id;
		$this->thisinfo = $this->get_article($id);
		return $this->get_view(); //显示页面
	}
	//留言
	function act_msg() {
		$this->arr_help = $this->get_folder_article('default');
		$this->verfifycode = cls_obj::get("cls_user")->is_verifycode();
		$this->options = cls_config::get("msg_options","sys");
		return $this->get_view(); //显示页面
	}
	//文章列表
	function act_news() {
		$channel_id = (int)fun_get::get("channel_id");
		$channel_key = fun_get::get("channel_key");
		$channel_name = '';
		$where = (empty($channel_key))? " where channel_id='" . $channel_id . "'" : " where channel_key='" . $channel_key . "'";
		$obj_rs = cls_obj::db()->get_one("select channel_name,channel_id from " . cls_config::DB_PRE . "article_channel" . $where);
		if(!empty($obj_rs)) {
			$channel_name = $obj_rs['channel_name'];
			$channel_id = $obj_rs['channel_id'];
		}
		$this->arr_help = $this->get_folder_article('default');
		$this->arr_list = $this->get_article_list($channel_id);
		$this->channel_name = $channel_name;
		$this->channel_id = $channel_id;
		return $this->get_view(); //显示页面
	}
	//文章列表
	function act_news_view() {
		$this->arr_help = $this->get_folder_article('default');
		//当前文章信息
		$info = $this->get_article(fun_get::get("id") , 23);//24为当前帮助目录id
		$channel_name = '';
		if(!empty($info)) {
			$obj_rs = cls_obj::db()->get_one("select channel_name from " . cls_config::DB_PRE . "article_channel where channel_id='" . $info['article_channel_id'] . "'");
			if(!empty($obj_rs)) $channel_name = $obj_rs['channel_name'];
		}
		$this->channel_name = $channel_name;
		$this->thisinfo = $info;
		return $this->get_view(); //显示页面
	}
	function act_comment() {
		//评论总计
		$obj_db = cls_obj::db();
		$id = (int)fun_get::get("menu_id");
		$obj_rs = $obj_db->get_one("select count(1) as 'num' from " . cls_config::DB_PRE . "meal_menu_comment where comment_menu_id='" . $id . "' and comment_val=1");
		$this->goodnum = (!empty($obj_rs)) ? $obj_rs['num'] : 0;
		$obj_rs = $obj_db->get_one("select count(1) as 'num' from " . cls_config::DB_PRE . "meal_menu_comment where comment_menu_id='" . $id . "' and comment_val=0");
		$this->generalnum = (!empty($obj_rs)) ? $obj_rs['num'] : 0;
		$obj_rs = $obj_db->get_one("select count(1) as 'num' from " . cls_config::DB_PRE . "meal_menu_comment where comment_menu_id='" . $id . "' and comment_val=-1");
		$this->failnum = (!empty($obj_rs)) ? $obj_rs['num'] : 0;
		$this->arr_list = $this->get_comment_list($id);
		return $this->get_view();
	}
	function act_comment_shop() {
		//评论总计
		$obj_db = cls_obj::db();
		$obj_rs = $obj_db->get_one("select count(1) as 'num' from " . cls_config::DB_PRE . "meal_order_comment where comment_val=1");
		$this->goodnum = (!empty($obj_rs)) ? $obj_rs['num'] : 0;
		$obj_rs = $obj_db->get_one("select count(1) as 'num' from " . cls_config::DB_PRE . "meal_order_comment where comment_val=0");
		$this->generalnum = (!empty($obj_rs)) ? $obj_rs['num'] : 0;
		$obj_rs = $obj_db->get_one("select count(1) as 'num' from " . cls_config::DB_PRE . "meal_order_comment where comment_val=-1");
		$this->failnum = (!empty($obj_rs)) ? $obj_rs['num'] : 0;
		$this->arr_list = $this->get_comment_shop_list();
		return $this->get_view();
	}
}