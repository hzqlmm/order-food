<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_sys_components extends inc_mod_admin {

	function get_installed() {
		$arr_return['installed'] = cls_config::get("components" , "version" , "" , "");
		$arr_return['all'] = cls_klkkdj::get("version.com");
		return $arr_return;
	}
	function get_not_installed() {
		$arr_return['installed'] = cls_config::get("components" , "version" , "" , "");
		$arr_return['all'] = cls_klkkdj::get("version.com");
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
		$cont = cls_klkkdj::down($zipname);
		if(!empty($cont)) {
			$path = KJ_DIR_DATA . "/package/" . $zipname . ".zip";
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
		$com = fun_get::get("com");
		if($install) {
			$arr = cls_klkkdj::get("version.com");
		} else {
			$arr = cls_config::get("components" , "version" , "" , "");
		}
		if(!isset($arr[$com])) return array("code" => 500 , "msg" => "没有找到相关组件");
		//组件信息
		$com_info = $arr[$com];
		if(!stristr($com_info["version"] , ".")) {
			$com_info["version"] = $com_info["version"] . ".0";
		}
		$file = KJ_DIR_DATA . "/package/com." . $com . "." . $com_info["version"] . "/install.com." . $com . ".php";
		if(!file_exists( $file )) return array("code" => 500 , "msg" => "未找到组件安装包");
		include_once($file);
		$cls = "install_com_" . $com;
		$obj_com = new $cls();
		return array("code"=>0 , "obj" => &$obj_com);
	}
}