<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_sys_config extends inc_mod_admin {

	// 从表类文件获取模块信息，以数组形式返回
	function get_module() {
		return tab_sys_config::get_module();
	}

	/* 按模块查询配置表信息并返回数组列表
	 * module : 指定查询模块
	 */
	function get_list( $module = '' ) {
		$arr_return = array("list" => array());
		//取排序字段
		$arr_config_info = tab_sys_user_config::get_info("sys.config"  , $this->app_dir);
		$sort = $arr_config_info["sortby"];
		$arr_return["sort"] = $arr_config_info["sort"];
		//取字段信息
		$arr_cfg_fields = tab_sys_user_config::get_fields("sys.config" , $this->app_dir , "sys");
		$arr_return['tabtd'] = $arr_cfg_fields["tabtd"];
		$arr_return['tabtit'] = $arr_cfg_fields["tabtit"];
		$obj_db = cls_obj::db();
		$str_where = " where config_module='" . $module . "'";
		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."sys_config" . $str_where . $sort);
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			if($obj_rs["config_list"]!="")	{
				$obj_rs["config_list"] = tab_sys_config::get_array($obj_rs["config_list"]);
			} else {
				$obj_rs["config_list"] = array();
			}
			if(fun_is::serialized($obj_rs['config_val'])) {
				$obj_rs['config_val'] = unserialize($obj_rs["config_val"]);
			}
			if($obj_rs["config_type"] == 'chk' && !is_array($obj_rs['config_val'])) $obj_rs['config_val'] = array();
			$arr_return["list"][] = $obj_rs;
		}
		return $arr_return;
	}

	/* 查询配置表指定id信息
	 * msg_id : sys_config 表中 config_id
	 */
	function get_editinfo($msg_id) {
		$obj_db = cls_obj::db();
		$obj_rs = $obj_db->edit(cls_config::DB_PRE."sys_config" , "config_id='".$msg_id."'");
		$get_module = fun_get::get("url_module");
		if( empty($msg_id) && empty($obj_rs["config_module"])) {
			$obj_rs["config_module"] = $get_module;
		}
		return $obj_rs;
	}

	/* 保存数据
	 * 
	 */
	function on_save() {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"成功保存");
		$arr_fields = array(
			"id"     => (int)fun_get::post("id"),
			"config_module" => fun_get::post("config_module"),
			"config_type"   => fun_get::post("config_type"),
			"config_name"   => trim(fun_get::post("config_name")),
			"config_list"   => fun_get::post("config_list"),
			"config_val"    => fun_get::post("config_val"),
			"config_intro"  => fun_get::post("config_intro"),
		);
		if( empty($arr_fields['config_module']) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = "请选择所属组";
			return $arr_return;
		}
		if( empty($arr_fields['config_type']) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = "请选择类型";
			return $arr_return;
		}
		if( empty($arr_fields['config_name']) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = "请输入变量名";
			return $arr_return;
		}
		if( empty($arr_fields['config_intro']) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = "说明不能为空";
			return $arr_return;
		}
		$arr = tab_sys_config::on_save($arr_fields);
		if($arr['code']==0) {
			if(isset($arr['id'])) $arr_return['id'] = $arr['id'];
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}

	/* 将sys_config表里数据以文件形式更新到 KJ_DIR_DATA/config 目录下
	 * 
	 */
	function on_update() {
		$arr_return   = array("code"=>0,"msg"=>"成功更新");
		$str_msginfo  = "";
		$arr_selid    = fun_get::get("selid");
		$arr_val      = fun_get::get("config_val");
		$arr_sort      = fun_get::get("config_sort");
		$arr_fields   = array();
		for($i = 0 ; $i < count($arr_selid) ; $i++)	{
			$lng_id = (int)$arr_selid[$i];
			(isset($arr_val[$i])) ? $str_config_val = $arr_val[$i] : $str_config_val = "";
			if($lng_id > 0) {
				//处理多选
				if(fun_is::set("config_val_".$lng_id)) {
					$arr = fun_get::get("config_val_" . $lng_id);
					$str_config_val = $arr;
				}
				//处理布尔
				if(fun_is::set("config_val_bool_" . $lng_id)) {
					$str_config_val = (int)fun_get::get("config_val_bool_" . $lng_id);
				}
				$arr_x=array();
				$arr_x["config_val"]        = $str_config_val;
				$arr_x["config_id"]         = $lng_id;
				if(!empty($arr_sort)) $arr_x["config_sort"] = $arr_sort[$i];
				$arr_fields[]=$arr_x;
			}
		}
		if( count($arr_fields) > 0) {
			$arr = tab_sys_config::on_saveall($arr_fields);
			if($arr['code']!=0) {
				$arr_return['code']=$arr['code'];
				$arr_return['msg']=$arr['msg'];
			}
		}
		return $arr_return;
	}
	function on_update_print() {
			$arr = array("printinfo" => fun_get::post("printinfo") , "width" => fun_get::post("width") );
			$val=var_export($arr,true);
			$val = '<'.'?php'.chr(10).'return '.$val.";";
			fun_file::file_create(KJ_DIR_DATA."/config/cfg/cfg.print.php",$val,1);
			return array("code" => 0 , "msg" => "更新完成");
	}

	/* 删除指定  config_id 数据
	 */
	function on_delete() {
		$arr_return = array("code"=>0,"msg"=>"成功删除");
		$str_id = fun_get::get("id");
		$arr_id = fun_get::get("selid");
		if( empty($arr_id) && empty($str_id) ) {
			$arr_return['code'] = 22;//见参数说明表
			$arr_return['msg']  = "请选择所属组";
			return $arr_return;
		}
		if(!empty($arr_id)) $str_id = $arr_id; //优先考虑 arr_id
		$arr = tab_sys_config::on_delete($str_id);
		if($arr['code'] != 0) {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
}