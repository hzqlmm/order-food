<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class cls_com {
	function __call($com , $p) {
		if(self::is($com)) {
			require_once KJ_DIR_LIB.'/components/' . $com . '/com.' . $com . '.php';
			$obj_com = cls_obj::get("com_" . $com);
			if(count($p) == 1) {
				//print_r($p);exit;
				$arr = $obj_com->$p[0]();
			} else {
				$arr = $obj_com->$p[0]($p[1]);
			}
			return $arr;
		} else {
			return array('code'=>500 , 'msg'=>'组件未安装');
		}
	}
	function is($com) {
		static $coms = array();
		if(empty($coms)) $coms = cls_config::get("components" , "version" , "" , "");
		return (isset($coms[$com]))? true : false;
	}
}