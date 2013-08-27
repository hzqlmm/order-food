<?php
/*
 *
 *
 * 2013-03-24
 */
class cls_limit {
	private $perms = array("limit" =>array());

	//初始化
	function __construct($dir) {
		$group_id = cls_obj::get("cls_user")->group_id;
		$this->perms['group_id'] = $group_id;
		if(empty($group_id)) return;
		$this->init_group($group_id , $dir);
	}
	function init_group($group_id , $dir) {
		if(isset($this->perms["limit"]['id_' . $group_id])) return;
		$arr_group = cls_obj::db()->get_one("select group_limit,group_limit_article from " . cls_config::DB_PRE . "sys_user_group where group_id=".$group_id);
		if(!empty($arr_group) && !empty($arr_group["group_limit"])) {
			$arr = unserialize( $arr_group["group_limit"] );
			if(isset($arr[$dir])) $this->perms["limit"]['id_' . $group_id] = $arr[$dir];
			$this->perms["limit_article"]['id_' . $group_id] = unserialize( $arr_group["group_limit_article"] );
		}
	}
	/** 检查 mod 权限
	 *  mod : 要检查的mod值
	 *  返回：true 或 false , true 表示拥有权限 ,　false 表示没有权限
	 */
	function chk_mod($mod , $group_id = 0) {
		$this->perms["chk_mod"] = $mod;
		if(cls_obj::get("cls_user")->is_super_admin() && empty($group_id)) return true;//超管
		if(empty($group_id)) $group_id = $this->perms['group_id'];
		if( empty($mod) && cls_obj::get("cls_user")->is_admin() ) return true; //mod 为空时,只要是管理员就有权限
		if( !isset($this->perms["limit"]['id_' . $group_id][$mod]) ) return false;
		return true;
	}
	/** 检查 app 权限
	 *  app : 要检查的app值，mod : app所属模块,默认为空，即表示为当前mod,来源 chk_mod函数
	 *  返回：true 或 false , true 表示拥有权限 ,　false 表示没有权限
	 */
	function chk_app($app , $mod = '' , $group_id = 0) {
		if(empty($app)) return true;
		$this->perms["chk_app"] = $app;
		if(cls_obj::get("cls_user")->is_super_admin() && empty($group_id)) return true;//超管
		if(empty($mod)) $mod = $this->perms["chk_mod"];
		if(empty($mod)) {
			//主页框架不验证
			if( $app == 'index' && cls_obj::get("cls_user")->is_admin() ) return true; //mod 为空时,只要是管理员就有权限
			$mod = $app;
		}
		if(empty($group_id)) $group_id = $this->perms['group_id'];
		if( !isset($this->perms["limit"]['id_' . $group_id][$mod]) || (!isset($this->perms["limit"]['id_' . $group_id][$mod]["app_".$app]) && !isset($this->perms["limit"]['id_' . $group_id][$mod]["all"]) ) ) return false;
		return true;
	}
	/** 检查 act 权限
	 *  act : 要检查的act值，app : act所属app,默认为空，即表示为当前app,来源 chk_app函数 , mod 同理
	 *  返回：true 或 false , true 表示拥有权限 ,　false 表示没有权限
	 */
	function chk_act($act , $app = '' , $mod = '' , $group_id = 0) {
		/*以下划线分隔，如：edit_article_folder 如果拥有edit 权限，则拥有edit_article , edit_article_folder 权限
		  同理拥有edit_article 则拥有edit_article_folder权限
		*/
		if(empty($act)) return true;
		$arr = explode("_" , $act);
		$lngx = count($arr);
		if( $lngx > 1) {
			$lngx--;
			unset($arr[$lngx]);
			$str = implode("_" , $arr);
			if($this->chk_act($str , $app , $mod ,$group_id)) return true;
		}
		$this->perms["chk_act"] = $act;
		if(cls_obj::get("cls_user")->is_super_admin()) return true;//超管
		if(empty($group_id)) $group_id = $this->perms['group_id'];
		if(empty($app)) $app = $this->perms["chk_app"];
		if(empty($mod)) $mod = $this->perms["chk_mod"];
		if(empty($mod)) {
			//主页框架不验证
			if( $app == 'index' && cls_obj::get("cls_user")->is_admin() ) return true; //mod 为空时,只要是管理员就有权限
			$mod = $app;
		}
		if( $act == "default" ) return true;
		if( !isset($this->perms["limit"]['id_' . $group_id][$mod]) || (!isset($this->perms["limit"]['id_' . $group_id][$mod]["app_".$app]) && !isset($this->perms["limit"]['id_' . $group_id][$mod]["all"])) ) return false;
		if( !isset($this->perms["limit"]['id_' . $group_id][$mod]["app_".$app]["all"]) && !isset($this->perms["limit"]['id_' . $group_id][$mod]["app_".$app]["act_".$act]) && !isset($this->perms["limit"]['id_' . $group_id][$mod]["all"]) ) return false;
		return true;
	}
	/*
	 * 返回指定目录权限列表
	 * dir : 指定目录 , key : 
	 */
	function get_dir_limit($dir = '') {
		$arr_dir = explode("/" , $dir);
		$str_dir = $arr_dir[0];
		$str_menu_path = KJ_DIR_DATA."/limit/".$str_dir.".php";
		if( file_exists($str_menu_path) ) {
			$arr_menu = include ( $str_menu_path );
			return $arr_menu;
		} else {
			return array();
		}
	}
	/* 检测文章权限
	 *
	 */
	function chk_article($channel_id , $folder_id = 0 , $folder_pids = ''  , $group_id = 0) {
		if(cls_obj::get("cls_user")->is_super_admin()) return true;//超管
		if(empty($group_id)) $group_id = $this->perms['group_id'];
		if(isset($this->perms["limit_article"]['id_' . $group_id]["c_" . $channel_id]["all"])) return true;
		if(!isset($this->perms["limit_article"]['id_' . $group_id]["c_" . $channel_id])) return false;
		if(empty($folder_id)) return true;//未指定目录，则只检查频道权限
		if(isset($this->perms["limit_article"]['id_' . $group_id]["f_" . $folder_id])) return true;
		if(empty($folder_pids)) return false; //没有指定目录树，则不继承
		//指定了目录树，则循环查找上级是否有全选的，有表示有权限
		$arr = explode("," , $folder_pids);
		foreach($arr as $item) {
			if(isset($this->perms["limit_article"]['id_' . $group_id]["f_" . $item]["all"])) return true;
		}
		return false;
	}
}