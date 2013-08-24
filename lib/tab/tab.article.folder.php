<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_article_folder {
	static $perms;
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
			);
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}
	static function on_save($arr_fields , $where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		$obj_db = cls_obj::db_w();
		if(isset($arr_fields['folder_id'])) {
			$arr_fields['id'] = $arr_fields['folder_id'];
			unset($arr_fields['folder_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " folder_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and folder_id='" . $arr_return['id'] . "'";
				}
			}
		}
		if( isset($arr_fields["folder_pid"]) > 0) {
			$obj_rs = $obj_db->get_one("select folder_pids from ".cls_config::DB_PRE."article_folder where folder_id=".$arr_fields["folder_pid"]);
			if(!empty($obj_rs) && !empty($obj_rs["folder_pids"])) {
				$arr_fields["folder_pids"] = $obj_rs["folder_pids"] . "," . $arr_fields["folder_pid"];
			} else {
				$arr_fields["folder_pids"] = $arr_fields["folder_pid"];
			}
		}

		if( empty($where) ) {

			//必填项检查
			if(!isset($arr_fields['folder_name']) || empty($arr_fields['folder_name'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("folder_name_null" , "article");//用户组名不能为空
				return $arr_return;
			}
			
			//初始默认值
			$arr_fields['folder_addtime'] = $arr_fields['folder_updatetime'] = TIME;
			if(!isset($arr_fields["folder_pid"])) $arr_fields["folder_pid"] = 0;
			if(!isset($arr_fields['folder_sort']) || empty($arr_fields['folder_sort'])) {
				$obj_rs = $obj_db->get_one("select max(folder_sort) as sort from " . cls_config::DB_PRE . "article_folder where folder_pid=" . $arr_fields["folder_pid"]);
				(!empty($obj_rs))? $arr_fields['folder_sort'] = $obj_rs["sort"] + 1 : $arr_fields['folder_sort'] = 1;
			}
			//插入到用户表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."article_folder",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$where  = "folder_sort='" . $arr_fields['folder_sort'] . " and folder_addtime=" . $arr_fields['folder_addtime'] . " and folder_name='".$arr_fields['folder_name'] . "' and folder_pid='".$arr_fields["folder_pid"]."'";
					$obj_rs = $obj_db->get_one("select folder_id from ".cls_config::DB_PRE."article_folder where ".$where);
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['folder_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {

			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select folder_id from ".cls_config::DB_PRE."article_folder where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['folder_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "folder_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."article_folder" , $arr_fields , $where);
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
		if( empty($str_id) && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]=cls_language::get("not_where");
			return $arr_return;
		}
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "folder_id='".$str_id."'" : $arr_where[] = "folder_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
			$where = implode(" and " , $arr_where);
			//取要删除的id列表
			$arr_id = array();
			$obj_result = $obj_db->select("select folder_id from " . cls_config::DB_PRE . "article_folder where " . $where);
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_id[] = $obj_rs["folder_id"];
			}
		} else {
			//取要删除的id列表
			$arr_id = explode("," , $str_id);
		}
		$obj_db = cls_obj::db_w();
		//默认删除指定当前id
		$arr_where = array();
		//获取子目录id
		foreach($arr_id as $item) {
			$arr_where[] = $obj_db->concat(",","folder_pid",",") . " like ('%,".$item.",%')";
		}
		$arr_new_id = array();
		$str_where = implode(" or " , $arr_where);
		$obj_result = $obj_db->select("select folder_id from " . cls_config::DB_PRE . "article_folder where " . $str_where);
		while($obj_rs = $obj_db->fetch_array($obj_result)) {
			$arr_new_id[] = $obj_rs["folder_id"];
		}
		//合并当前删除的id及子目录id
		$arr_id = array_merge($arr_id , $arr_new_id);
		$str_id = implode(",",$arr_id);

		$arr_return=cls_obj::db_w()->on_delete(cls_config::DB_PRE."article_folder" , " folder_id in (" . $str_id . ")");

		if($arr_return["code"] == 0) {
			//同时删除文章
			tab_article::on_delete('' , array("article_folder_id in(" . $str_id . ")"));
		}

		return $arr_return;

	}
	/*
	 * 回收站或还原操作
	 * isdel 决定是回收还是还原，1:回收，0:还原
	 */
	static function on_del($arr_id , $isdel = 1  , $arr_where = array() , $delfrom = 0) {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if(!empty($str_id)) $arr_where[] = "folder_id in(" . $str_id . ")";
		if( empty($arr_where) ){
			$arr_return["msg"]="id".cls_language::get("not_null");
			return $arr_return;
		}
		$obj_db = cls_obj::db_w();
		$arr_fields = array("folder_isdel" => $isdel , "folder_isdel_from" => $delfrom);

		$str_where = implode(" and " , $arr_where);
		if(empty($str_id) || count($arr_where) > 1) {
			$obj_result = $obj_db->select("select folder_id from " . cls_config::DB_PRE . "article_folder where " . $str_where);
			$arr_id = array();
			while($obj_rs = $obj_db->fetch_array($obj_result)) {
				$arr_id[] = $obj_rs["folder_id"];
			}
			$str_id = implode("," , $arr_id);
			if(empty($str_id)) return $arr_return;
			$str_where = "folder_id in(".$str_id.")";
		}

		$isdel_n = ($isdel - 1) * -1;
		$arr_return = $obj_db->on_update(cls_config::DB_PRE."article_folder" , $arr_fields , $str_where );
		if($arr_return["code"] == 0) {
			//同时删除文章到回收站
			tab_article::on_del('' , $isdel , array("article_folder_id in(" . $str_id . ") and article_isdel=" . $isdel_n . " and article_isdel_from=" . $isdel_n ), $isdel);
			//删除子目录
			self::on_del("" , $isdel , array(" folder_isdel=" . $isdel_n . " and folder_isdel_from=" . $isdel_n . " and folder_pid in(" . $str_id . ")" ) , $isdel);
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
		$str_where = " where folder_pid=".$pid;
		if($where != '') $str_where .= " and " . $where;
		$obj_result = $obj_db->select("SELECT * FROM ".cls_config::DB_PRE."article_folder" . $str_where . " order by folder_sort,folder_id");
		while( $obj_rs = $obj_db->fetch_array($obj_result) ) {
			$obj_rs["layer"] = $layer;
			if($layer > $max_layer) $max_layer = $layer;
			$arr_list[] = $obj_rs;
			$arr = self::get_list_layer($obj_rs["folder_id"] , $layer+1 , $where);
			if( count($arr["list"])>0 ) {
				$arr_list = array_merge($arr_list , $arr["list"]);
				if($arr["maxlayer"] > $max_layer) $max_layer = $arr["maxlayer"];
			}
		}
		$arr_return=array("list" => $arr_list , "maxlayer" => $max_layer);
		return $arr_return ;
	}
	/* 复制目录
	 *
	 */
	static function on_copy($ids , $channel_id , $folder_id , $arr_where = array()) {
		$arr_return = array("code"=>0,"msg"=>"");
		if(!empty($ids)) $arr_where[] = "folder_id in(" . $ids . ")";
		$str_where = implode(" and " , $arr_where);
		if(empty($str_where)) return $arr_return;

		$obj_db = cls_obj::db_w();
		$str_sql="select * from " . cls_config::DB_PRE."article_folder where " . $str_where;
		$obj_result = $obj_db->select($str_sql);
		$lng_uid = cls_obj::get("cls_user")->uid;
		while($obj_rs = $obj_db->fetch_array($obj_result))
		{
			$arr_fields["folder_id"]=0;
			$arr_fields["folder_name"]=$obj_rs["folder_name"];
			$arr_fields["folder_pid"]=$folder_id;
			$arr_fields["folder_channel_id"]=$channel_id;
			$arr_fields["folder_sort"]=$obj_rs["folder_sort"];
			$arr_fields["folder_tpl"]=$obj_rs["folder_tpl"];
			$arr_fields["folder_article_tpl"]=$obj_rs["folder_article_tpl"];
			$arr_fields["folder_uid"]=$lng_uid;
			$arr_fields["folder_isdel"]=$obj_rs["folder_isdel"];
			$arr_fields["folder_pic"]=$obj_rs["folder_pic"];
			$arr_fields["folder_url"]=$obj_rs["folder_url"];
			$arr_return=self::on_save($arr_fields);
			if($arr_return["code"] != 0) {
				return $arr_return;
			} else {
				//复制目录下面的文章
				tab_article::on_copy('' , $channel_id , $arr_return["id"] , array("article_folder_id=" . $obj_rs["folder_id"] ) );
				//复制子目录
				self::on_copy('' , $channel_id , $arr_return["id"] , array( "folder_pid=" . $obj_rs["folder_id"] ) );
			}
		}
		return $arr_return;
	}
	/* 剪贴目录
	 *
	 */
	static function on_cut($ids , $channel_id , $folder_id) {
		$arr_return = array("code"=>0,"msg"=>"");
		$ids = fun_format::arr_id($ids);
		$arr_id  = explode("," , $ids);
		foreach($arr_id as $item) {
			$arr_return = self::on_save(array("folder_pid" => $folder_id , "folder_channel_id" => $channel_id , "folder_id" => $item));
			if($arr_return["code"] != 0) return $arr_return;
		}
		return $arr_return;
	}
}