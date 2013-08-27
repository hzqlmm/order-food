<?php
/*
 *
 *
 * 2013-03-24
 */
class tab_sys_user_depart {
	static $perms;

	//获取表配置参数
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
			);
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}

	/* 保存操作
	 * arr_fields : 为字段数据，默认如果包函 id，则为修改，否则为插入
	 * where : 默认为空，用于有时候条件修改
	 */
	static function on_save($arr_fields , $where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['depart_id'])) {
			$arr_fields['id'] = $arr_fields['depart_id'];
			unset($arr_fields['depart_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " depart_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and depart_id='" . $arr_return['id'] . "'";
				}
			}
		}
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {

			//必填项检查
			if(!isset($arr_fields['depart_name']) || empty($arr_fields['depart_name'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("user_depart_is_null");//用户组名不能为空
				return $arr_return;
			}
			
			//初始默认值
			$arr_fields['depart_addtime'] = $arr_fields['depart_updatetime'] = TIME;
			if(!isset($arr_fields["depart_pid"])) $arr_fields["depart_pid"] = 0;
			if(!isset($arr_fields['depart_sort']) || empty($arr_fields['depart_sort'])) {
				$obj_rs = $obj_db->get_one("select max(depart_sort) as sort from " . cls_config::DB_PRE . "sys_user_depart where depart_pid=" . $arr_fields["depart_pid"]);
				(!empty($obj_rs))? $arr_fields['depart_sort'] = $obj_rs["sort"] + 1 : $arr_fields['depart_sort'] = 1;
			}
			if($arr_fields["depart_pid"] > 0) {
				$obj_rs = $obj_db->get_one("select depart_pids from ".cls_config::DB_PRE."sys_user_depart where depart_id=".$arr_fields["depart_pid"]);
				if(!empty($obj_rs) && !empty($obj_rs["depart_pids"])) {
					$arr_fields["depart_pids"] = $obj_rs["depart_pids"] . "," . $arr_fields["depart_pid"];
				} else {
					$arr_fields["depart_pids"] = $arr_fields["depart_pid"];
				}
			}
			//插入到用户表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."sys_user_depart",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$where  = "depart_sort='" . $arr_fields['depart_sort'] . " and depart_addtime=" . $arr_fields['depart_addtime'] . " and depart_name='".$arr_fields['depart_name'] . "' and depart_pid='".$arr_fields["depart_pid"]."'";
					$obj_rs = $obj_db->get_one("select depart_id from ".cls_config::DB_PRE."sys_user_depart where ".$where);
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['depart_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {

			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select depart_id from ".cls_config::DB_PRE."sys_user_depart where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['depart_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "depart_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."sys_user_depart" , $arr_fields , $where);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		}
		return $arr_return;
	}
	/* 删除函数
	 * arr_id : 要删除的 id数组
	 * where : 删除附加条件
	 */
	static function on_delete($arr_id , $where = '') {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if( $str_id == "" && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]="id".cls_language::get("not_null");
			return $arr_return;
		}
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "depart_id='".$str_id."'" : $arr_where[] = "depart_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);
		$arr_return=cls_obj::db_w()->on_delete(cls_config::DB_PRE."sys_user_depart" , $where);
		return $arr_return;
	}
	/* 移动分组
	 *  id : 要移动的分组id , pid : 要移向的父id
	 */
	static function on_move($id , $pid , $pids='') {
		$obj_db = cls_obj::db_w();
		if(empty($pids)) {
			$obj_rs = $obj_db->get_one("select depart_pids from ".cls_config::DB_PRE."sys_user_depart where depart_id=".$pid);
			if(!empty($obj_rs) && !empty($obj_rs["depart_pids"])) {
				$pids = $obj_rs["depart_pids"] . "," . $pid;
			} else {
				$pids = $pid;
			}
		} else {
			$pids .=  "," . $pid;
		}
		$arr_return = $obj_db->on_exe("update ".cls_config::DB_PRE."sys_user_depart set depart_pid=".$pid.",depart_pids='".$pids."' where depart_id='".$id."'");
		if($arr_return["code"] == 0) {
			//循环移动子集
			$obj_result = $obj_db->select("select depart_id from " . cls_config::DB_PRE . "sys_user_depart where depart_pid='".$id."'");
			while($obj_rs = $obj_db->fetch_array($obj_result)){
				self::on_move($obj_rs["depart_id"] , $id , $pids);
			}
		}
		return $arr_return;
	}

	/** 按层次返回列表记录
	 *	pid : 指定父级id , layer : 当前层次 ，where : 附加条件
	 */
	static function get_list_layer($pid = 0 , $layer = 1 , $where = '') {
		$arr_list = array();
		$max_layer = 0;
		$obj_db = cls_obj::db_w();
		$str_where = " where depart_pid=".$pid;
		if($where != '') $str_where .= " and " . $where;
		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."sys_user_depart" . $str_where . " order by depart_sort,depart_id");
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$obj_rs["layer"] = $layer;
			if($layer > $max_layer) $max_layer = $layer;
			$arr_list[] = $obj_rs;
			$arr = self::get_list_layer($obj_rs["depart_id"] , $layer+1 , $where);
			if( count($arr["list"])>0 ) {
				$arr_list = array_merge($arr_list , $arr["list"]);
				if($arr["maxlayer"] > $max_layer) $max_layer = $arr["maxlayer"];
			}
		}
		$arr_return=array("list" => $arr_list , "maxlayer" => $max_layer);
		return $arr_return ;
	}
}