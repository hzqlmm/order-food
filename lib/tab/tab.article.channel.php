<?php
/*
 *
 *
 * 2013-03-24
 */
class tab_article_channel {
	static $perms;
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"state" => array( cls_language::get("normal") => 1 , cls_language::get("wait_check") => 0 , cls_language::get("close") => -1) ,
				"dirstyle" => array("Y" => 0 , "Y-m" => 1 , "Y-m-d" => 2 , "按目录名" => 3 , "按目录id" => 4) ,
				"ishtml" => array(cls_language::get("no") => 0 , cls_language::get("yes") => 1) ,
				"mode" => array(cls_language::get("article") => 0 , cls_language::get("pic") => 1, "内容" => 2) ,//频道模式，0=>文章，1=>图片
			);
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}
	static function on_save($arr_fields,$where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['channel_id'])) {
			$arr_fields['id'] = $arr_fields['channel_id'];
			unset($arr_fields['channel_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " channel_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and channel_id='" . $arr_return['id'] . "'";
				}
			}
		}
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {

			//必填项检查
			if(!isset($arr_fields['channel_name']) || empty($arr_fields['channel_name'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("channel_name_null" , "article");//菜谱名称不能为空
				return $arr_return;
			}

			//初始必要值
			$arr_fields['channel_addtime'] = TIME;
			$arr_fields['channel_updatetime'] = TIME;

			//插入到用户表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."article_channel",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$obj_rs = $obj_db->get_one("select channel_id from ".cls_config::DB_PRE."article_channel where channel_name = '" . $arr_fields["channel_name"] . "' and channel_addtime = '" . $arr_fields["channel_addtime"] . "'");
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['channel_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {
			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select channel_id from ".cls_config::DB_PRE."article_channel where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['channel_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "channel_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."article_channel" , $arr_fields , $where);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		}
		self::on_refresh();
		return $arr_return;
	}
	/* 将频道以数组形式保存到配置目录下面，方便调用
	 *
	 */
	static function on_refresh() {
		$obj_db = cls_obj::db();
		$arr_val = array();
		$obj_result = $obj_db->select("select channel_id,channel_name,channel_html,channel_html_dir,channel_html_dirstyle,channel_tpl,channel_article_tpl,channel_folder_tpl,channel_mode from " . cls_config::DB_PRE . "article_channel");
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_val["id_".$obj_rs["channel_id"]] = $obj_rs;
		}
		cls_config::set_date("article_channel" , "" , $arr_val);
	}
	/* 删除函数
	 * arr_id : 要删除的 id数组
	 * where : 删除附加条件
	 */
	static function on_delete($arr_id , $where = '') {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if( empty($str_id) && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]=cls_language::get("not_where");
			return $arr_return;
		}
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "channel_id='".$str_id."'" : $arr_where[] = "channel_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);

		$arr_return=cls_obj::db_w()->on_delete(cls_config::DB_PRE."article_channel" , $where);
		return $arr_return;
	}
}