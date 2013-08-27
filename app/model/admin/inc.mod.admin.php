<?php
/*
 *
 *
 * 2013-03-24
 */

class inc_mod_admin extends cls_base{

	/**
	 * admin 目录 初始类，启动 : 登录检查，权限检查
	 */
	function __construct($arr_v) {
		parent::__construct($arr_v);
		//是否登录
		if(!cls_obj::get("cls_user")->is_login()) {
			cls_error::on_error("no_login");
		}
		//是否为管理员
		if(!cls_obj::get("cls_user")->is_admin()) {
			cls_error::on_error("no_limit");
		}
		//权限控制
		$this->this_limit = new cls_limit($this->app_dir);
		if( !$this->this_limit->chk_mod($this->app_module) ) {
			cls_error::on_error("no_limit");
		}
		if( !$this->this_limit->chk_app($this->app) ) {
			cls_error::on_error("no_limit");
		}
		if( !$this->this_limit->chk_act($this->app_act) ) {
			cls_error::on_error("no_limit");
		}
		if($this->get_id=="" && fun_get::get('id')=='') $this->get_id="";
		//记录后台日志
		if( $this->app_act != '' && $this->app_act != 'default'  && ($this->app != 'index' || $this->app_module != '') ) {
			$arr = tab_sys_user_log::on_save( $this->app_dir );
		}
		$this->user = cls_obj::get("cls_user");
	}
	/*
	 * 获取文章栏目列表
	 */
	function get_article_menu() {
		$obj_db = cls_obj::db();
		$arr = array();
		$obj_result = $obj_db->select("select channel_id,channel_name,channel_mode from " . cls_config::DB_PRE . "article_channel where channel_state>0");
		$arr_channel = cls_config::get_data("article_channel");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			if($this->this_limit->chk_article($obj_rs["channel_id"])) {
				$arr[] = array("url"=>"?app=article&url_channel_id=" . $obj_rs["channel_id"] , "name" => $obj_rs["channel_name"] , "app" => "article" );
			}
		}
		return $arr;
	}
	/**
	 * admin 统一获取分页样式
	 * arr_info : 数组 , 值为 : 
	 * 返回 : 分页html字符串
	 */
	function get_pagebtns( $arr_info ) {
		if($arr_info['total'] < 1) return '';
		$prepg = $arr_info['page']-1;
		$nextpg = $arr_info['page']+1;//$page == $pages ? 0 : ($page+1);
		$str_left="";
		$str_right="";
		$pagenav ='<li class="info">共:<font color="#ff6600">'.$arr_info['total'].'</font> 条&nbsp;页 '.$arr_info['page'].'/'.$arr_info['pages'].'&nbsp;&nbsp;页大小<input type="text" name="url_pagesize" value="' . $arr_info['pagesize'] . '" style="width:20px;color:#888888" onkeyup="kj.page.size(event,\''.$this->app_module . "." . $this->app . "." . $this->app_act . '\',\''.$this->app_dir.'\');" id="id_page_size"></li>';
		$str_x="";
		if($arr_info["pages"] > 10) {
			if($arr_info['page']>5){
				$lng_pre=$arr_info['page']-5;
				$lng_next=$arr_info['page']+5;
				$str_left="<li><a href='javascript:kj.page.go(1);'>首页</a></li>";
				$str_right="<li><a href='javascript:kj.page.go(".$arr_info['pages'].");'>尾页</a></li>";
			}else{
				$lng_pre=1;
				$lng_next=10;
				$str_right="<li><a href='javascript:kj.page.go(".$arr_info['pages'].");'>尾页</a></li>";
			}
		}else{
			$lng_pre=1;
			$lng_next=$arr_info['pages'];
		}
		if($lng_next>=$arr_info['pages']){
			$lng_next=$arr_info['pages'];
			$str_right="";
		}
		for($i=$lng_pre;$i<=$lng_next;$i++){
			$str_sel="";
			if($i==$arr_info['page']) $str_sel=" class='x_sel'";
			$str_x.="<li".$str_sel."><a href='javascript:kj.page.go(".$i.");'>[".$i."]</a></li>";
		}
		$pagenav.=$str_left.$str_x.$str_right."<li class='x_go'><input type='text' name='go_page' id='id_go_page' value='' class='pTxt1 x_txt' onkeyup='kj.page.page_keyup(event);'>&nbsp;&nbsp;<a href=\"javascript:kj.page.go(kj.obj('#id_go_page').value);\">跳转</a></li>";
		return $pagenav;
	}
	/*
	 * 获取状态样式
	 */
	function get_state_style($val) {
		$style = "";
		if($val < 1) {
			$style = " style='color:#ff0000'";
		}
		return $style;
	}
	/*
	 * 获取菜单列表
	 */
	function get_model_menu($key = '') {

		$arr_return = array();
		$arr_dir = explode("/" , $this->perms["app_dir"]);
		$str_dir = $arr_dir[0];
		$group_id = cls_obj::get("cls_user")->group_id;
		$str_menu_path = KJ_DIR_DATA."/menu/group_".$group_id.".php";
		if( !file_exists($str_menu_path) ) $str_menu_path = KJ_DIR_DATA."/menu/".$str_dir.".php";
		if( file_exists($str_menu_path) ) {
			$arr_menu = include ( $str_menu_path );
			if( isset($arr_menu[$key]) ){
				foreach($arr_menu[$key] as $item) {
					if($this->this_limit->chk_app($item['app'] , $item['app_module']) ) {
						$arr_return[$menu][] = $item;
					}
				}

			} else {

				//检查是否拥有此模块管理权限
				foreach($arr_menu as $menu => $list) {
					foreach($list as $item) {
						if(isset($item["list"])){
							$arr2 = cls_app::on_display($this->app_dir , '' , $item["list"]["app"] , $item["list"]["app_act"]);
						if(!isset($arr_return[$menu])) $arr_return[$menu] = array();
							$arr_return[$menu] = array_merge($arr_return[$menu] , $arr2);
						} else if($this->this_limit->chk_app($item['app'] , $item['app_module']) ) {
							$arr_return[$menu][] = $item;
						}
					}
				}
			}
		}
		return $arr_return;
	}

}