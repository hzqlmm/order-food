<?php
/**
 * 区域 关联表名：sys_area
 * 
 */
class mod_other_pay extends inc_mod_admin {

	function get_installed() {
		$arr_return['installed'] = cls_config::get("" , "pay" , "" , "");
		$arr_return['all'] = cls_klkkdj::get("version.pay");
		return $arr_return;
	}
	function get_not_installed() {
		$arr_return['installed'] = cls_config::get("" , "pay" , "" , "");
		$arr_return['all'] = cls_klkkdj::get("version.pay");
		$arr_return['not'] = array();
		foreach($arr_return['all'] as $item => $key) {
			if(!isset($arr_return['installed'][$item])) {
				$arr_return['not'][$item] = $key;
			}
		}
		return $arr_return;
	}
	//下载安装包
	function on_down() {
		$zipname = fun_get::get("zipname");
		$cont = cls_klkkdj::down($zipname , 'pay');
		if(!empty($cont)) {
			$path = KJ_DIR_DATA . "/package/pay/" . $zipname . ".zip";
			fun_file::file_create($path , $cont , 1);
			if(file_exists($path)) {
				//在线解压
				$arr = cls_zip::unzip($path);
				if($arr["code"]!=0) {
					return array("code"=>500 , "msg"=>$arr['msg']);
				}
			}
			return array("code" => 0);
		} else {
			return array("code"=>500,"msg" => "下载安装包失败，请尝试手动下载");
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
	//获取卸载步骤
	function get_uninstall_steps() {
		$arr_return = array("code"=>0);
		$arr = $this->get_install_obj(false);
		if($arr["code"]!=0) return $arr;
		$obj_com = $arr["obj"];
		$arr_return["steps"] = $obj_com->get_uninstall_steps();
		return $arr_return;
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
	//卸载
	function on_uninstall() {
		$arr = $this->get_install_obj(false);
		if($arr["code"]!=0) return $arr;
		$step = (int)fun_get::get("step");
		$obj_com = $arr["obj"];
		$arr_steps = $obj_com->get_uninstall_steps();
		$step = "uninstall_" . $arr_steps[$step]['step'];
		$arr = $obj_com->$step();
		return $arr;
	}
	function get_install_obj($install = true) {
		$pay = fun_get::get("payname");
		if($install) {
			$arr = cls_klkkdj::get("version.pay");
		} else {
			$arr = cls_config::get("" , "pay" , "" , "");
		}
		if(!isset($arr[$pay])) return array("code" => 500 , "msg" => "没有找到支付方式");
		//组件信息
		$pay_info = $arr[$pay];
		if(!stristr($pay_info["version"] , ".")) {
			$pay_info["version"] = $pay_info["version"] . ".0";
		}
		$file = KJ_DIR_DATA . "/package/pay/" . $pay . "." . $pay_info["version"] . "/install." . $pay . ".php";
		if(!file_exists( $file )) return array("code" => 500 , "msg" => "未找到支付方式安装包");
		include_once($file);
		$cls = "install_pay_" . $pay;
		$obj_pay = new $cls();
		return array("code"=>0 , "obj" => &$obj_pay);
	}
	//获取支付配置信息
	function get_edit_info($pay) {
		$cfg = cls_config::get("alipay_js" , "pay" , "" , "");
		if(empty($cfg) && !isset($cfg['fields']) ) return '';
		$cfg['fields']['state'] = $cfg['state'];
		return $cfg['fields'];
	}
	//保存配置信息
	function on_save() {
		$payname = fun_get::get("payname");
		$arr = cls_config::get('' , 'pay' , '' , '');
		if(!isset($arr[$payname]) || !isset($arr[$payname]['fields'])) {
			return array("code" => 500 , "msg" => '没有找到相关支付接口');
		}
		foreach( $arr[$payname]['fields'] as $item => $key) {
			$arr[$payname]['fields'][$item] = fun_get::get($item);
		}
		//设置状态
		$arr[$payname]['state'] = fun_get::get("state");
		$val=var_export($arr,true);
		$val = '<'.'?php'.chr(10).'return '.$val.";";
		$arr_return = fun_file::file_create(KJ_DIR_DATA . "/config/cfg.pay.php",$val,1);
		if($arr_return['code'] != 0) {
			return $arr_return;
		}
		return array("code" => 0 , "msg" => "保存成功");
	}

	/* 按模块查询菜单信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_pagelist() {
		$arr_where = array();
		$arr_where_s = array();
		$str_where = '';
		$lng_issearch = 0;
		//取查询参数
		$arr_search_key = array(
			'addtime1' => fun_get::get("s_addtime1"),
			'addtime2' => fun_get::get("s_addtime2"),
			'user_id' => (int)fun_get::get("s_user_id"),
		);
		if( fun_is::isdate( $arr_search_key['addtime1'] ) ) $arr_where_s[] = "pay_addtime >= '" . strtotime( $arr_search_key['addtime1'] ) . "'"; 
		if( fun_is::isdate( $arr_search_key['addtime2'] ) ) $arr_where_s[] = "pay_addtime <= '" . fun_get::endtime($arr_search_key['addtime2']) . "'"; 
		if( $arr_search_key['user_id'] != 0 ) $arr_where_s[] = "pay_user_id = '" . $arr_search_key['user_id'] . "'"; 
		//合并查询数组
		$arr_where = array_merge($arr_where , $arr_where_s);
		if(count($arr_where)>0) $str_where = " where " . implode(" and " , $arr_where);
		$arr_return = $this->sql_list($str_where , (int)fun_get::get('page'));

		if( count($arr_where_s) > 0 ) $lng_issearch = 1;
		$arr_return['issearch'] = $lng_issearch;
		return $arr_return;
	}


	/* 实现按具体条件查询数据表，并返回分页信息
	 * str_where : sql 查询条件 , lng_page : 当前页 , lng_pagesize : 分页大小
	 */
	function sql_list($str_where = "" , $lng_page = 1) {
		$arr_return = array("list" => array());
		$obj_db = cls_obj::db();
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("other.pay" , $this->app_dir , "other");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("other.pay"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		$lng_pagesize = $arr_config_info["pagesize"];
		//相关属性
		$arr_return['pay_type'] =  cls_obj::get('cls_com')->pay("get_config" , "type" );
		$arr_return['pay_state'] =  cls_obj::get('cls_com')->pay("get_config" , "state" );
		$arr_pay_method = cls_config::get("" , "pay" , "" , "");
		//取分页信息
		$arr_return["list"] = array();
		$arr_return["pageinfo"] = $obj_db->get_pageinfo(cls_config::DB_PRE."other_pay" , $str_where , $lng_page , $lng_pagesize);
		$obj_result = $obj_db->select("SELECT " . $arr_cfg_fields["sel"] . " FROM ".cls_config::DB_PRE."other_pay a left join ".cls_config::DB_PRE."sys_user b on a.pay_user_id=b.user_id" . $str_where . $sort . $arr_return['pageinfo']['limit']);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$obj_rs['pay_type'] = array_search($obj_rs['pay_type'] , $arr_return['pay_type']);
			$obj_rs['pay_state'] = array_search($obj_rs['pay_state'] , $arr_return['pay_state']);
			if(isset($obj_rs['pay_method']) && isset($arr_pay_method[$obj_rs['pay_method']])) $obj_rs['pay_method'] = $arr_pay_method[$obj_rs['pay_method']]["fields"]['title'];
			$arr_return["list"][] = $obj_rs;
		}
		$arr_return['pagebtns']   = $this->get_pagebtns($arr_return['pageinfo']);
		return $arr_return;
	}
}
?>