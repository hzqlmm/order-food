<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_sys_session {
	/* 保存操作
	 * arr_fields : 为字段数据，默认如果包函 id，则为修改，否则为插入
	 * where : 默认为空，用于有时候条件修改
	 */
	static function on_save($arr_fields , $where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['session_id'])) {
			$arr_fields['id'] = $arr_fields['session_id'];
			unset($arr_fields['session_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = $arr_fields['id'];
			unset($arr_fields['id']);
			if( !empty($arr_return['id']) ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " session_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and session_id='" . $arr_return['id'] . "'";
				}
			}
		}
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {
			//初始默认值,长度不能超过30个字符
			$arr_return["id"] = $arr_fields["session_id"] = fun_get::ip().TIME.rand(1,1000);
			$arr_fields['session_addtime'] = $arr_fields['session_updatetime'] = TIME;
			//插入到表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."sys_session",$arr_fields);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = $arr['msg'];
			}
		} else {
			$arr_fields['session_updatetime'] = TIME;
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."sys_session" , $arr_fields , $where);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = $arr['msg'];
			}
			//清除过期session , 三天
			$time = TIME - 86400*3;
			if(cls_obj::get("cls_user")->is_admin()) {
				$time2 = TIME - 86400*30;//一个月
				$obj_db->on_exe("delete from " . cls_config::DB_PRE . "sys_session where (session_user_id=0 and session_updatetime<" . $time . ") or session_updatetime<" . $time2);
			}
			//清除同用户其它地方三天未登录session信息
			if(isset($arr_fields['session_id']) && isset($arr_fields['session_user_id']) && !empty($arr_fields['session_user_id'])) {
				$obj_db->on_exe("delete from " . cls_config::DB_PRE . "sys_session where session_user_id=" . $arr_fields['session_user_id'] . " and session_updatetime<" . $time);
			}

		}
		return $arr_return;
	}
	/* 清除过期session
	 *
	 */
	static function on_clear() {
		$arr_return = array("code"=>0,"msg"=>"");

		return $arr_return;
	}
}