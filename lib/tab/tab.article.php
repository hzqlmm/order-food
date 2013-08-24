<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_article {
	static $perms;
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"state" => array( cls_language::get("normal") => 1 , cls_language::get("wait_check") => 0 , cls_language::get("close") => -1) ,
			);
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}
	static function on_save($arr_fields,$where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['article_id'])) {
			$arr_fields['id'] = $arr_fields['article_id'];
			unset($arr_fields['article_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " article_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and article_id='" . $arr_return['id'] . "'";
				}
			}
		}
		$obj_db = cls_obj::db_w();
		$arr_fields['article_updatetime'] = TIME;
		if( empty($where) ) {

			//必填项检查
			if(!isset($arr_fields['article_title']) || empty($arr_fields['article_title'])) {
				if(!empty($arr_fields['article_intro'])) {//取简介前50个字符
					$arr_fields["article_title"] = fun_format::len($arr_fields["article_intro"] , 25 , "..");
				} else {
					$arr =  cls_config::get_data("article_channel" , "id_" . $arr_fields["article_channel_id"] );
					if(!isset($arr["channel_mode"]) || $arr["channel_mode"] != 1) {//只有频道为图片模式标题才能为空
						$arr_return['code'] = 113;
						$arr_return['msg']  = cls_language::get("article_title_null" , "article");//名称不能为空
						return $arr_return;
					}
				}
			}

			//初始必要值
			$arr_fields['article_addtime'] = TIME;
			$arr_fields['article_uid'] = $arr_fields['article_updateuid'];
			if(!isset($arr_fields['article_sort']) || empty($arr_fields['article_sort'])) {
				$obj_rs = $obj_db->get_one("select max(article_sort) as sort from " . cls_config::DB_PRE . "article where article_folder_id=" . $arr_fields["article_folder_id"]);
				(!empty($obj_rs))? $arr_fields['article_sort'] = $obj_rs["sort"] + 1 : $arr_fields['article_sort'] = 1;
			}

			//插入到表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."article",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$obj_rs = $obj_db->get_one("select article_id from ".cls_config::DB_PRE."article where article_title = '" . $arr_fields["article_title"] . "' and article_addtime = '" . $arr_fields["article_addtime"] . "'");
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['article_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {
			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select article_id from ".cls_config::DB_PRE."article where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['article_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "article_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."article" , $arr_fields , $where);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		}
		//是否生成html
		if(isset($arr_fields["article_channel_id"])) {
			$arr_channel = cls_config::get_data("article_channel" , "id_".$arr_return["id"]);
			if(isset($arr_channel["channel_html"]) && $arr_channel["channel_html"] == "1" ) {
			}
		}
		return $arr_return;
	}

	/* 删除函数
	 * arr_id : 要删除的 id数组
	 * where : 删除附加条件
	 */
	static function on_delete($arr_id , $arr_where = array()) {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if( empty($str_id) && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]=cls_language::get("not_where");
			return $arr_return;
		}
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "article_id='".$str_id."'" : $arr_where[] = "article_id in(".$str_id.")";
		}
		$where = implode(" and " , $arr_where);

		$arr_return=cls_obj::db_w()->on_delete(cls_config::DB_PRE."article" , $where);
		return $arr_return;
	}
	/*
	 * 回收站或还原操作
	 * isdel 决定是回收还是还原，1:回收，0:还原 , isdel_from 是否级联影响删除
	 */
	static function on_del($arr_id , $isdel = 1 , $arr_where = array() , $isdel_from = 0) {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);

		if(!empty($str_id)) $arr_where[] = "article_id in(" . $str_id . ")";

		if( empty($arr_where) ){
			$arr_return["msg"]="id".cls_language::get("not_null");
			return $arr_return;
		}
		$arr_fields = array("article_isdel" => $isdel , "article_isdel_from" => $isdel_from);
		$str_where = implode(" and " , $arr_where);
		$arr_return = cls_obj::db_w()->on_update(cls_config::DB_PRE."article" , $arr_fields,$str_where);
		return $arr_return;
	}
	/* 复制文章
	 *
	 */
	static function on_copy($ids , $channel_id , $folder_id , $arr_where = array() ) {
		$arr_return = array("code"=>0,"msg"=>"");
		$obj_db = cls_obj::db_w();
		if(!empty($ids)) $arr_where[] = "article_id in(" . $ids . ")";
		$str_where = implode(" and " , $arr_where);
		if(empty($str_where)) return $arr_return;
		$str_sql="select * from " . cls_config::DB_PRE."article where " . $str_where;
		$obj_result = $obj_db->select($str_sql);
		$lng_uid = cls_obj::get("cls_user")->uid;
		while($obj_rs = $obj_db->fetch_array($obj_result))
		{
			$arr_fields["article_id"]=0;
			$arr_fields["article_title"]=$obj_rs["article_title"];
			$arr_fields["article_linkurl"]=$obj_rs["article_linkurl"];
			$arr_fields["article_islink"]=$obj_rs["article_islink"];
			$arr_fields["article_pic"]=$obj_rs["article_pic"];
			$arr_fields["article_pic_big"]=$obj_rs["article_pic_big"];
			$arr_fields["article_intro"]=$obj_rs["article_intro"];
			$arr_fields["article_content"]=$obj_rs["article_content"];

			$arr_fields["article_attribute"]=$obj_rs["article_attribute"];
			$arr_fields["article_htmlname"]=$obj_rs["article_htmlname"];
			$arr_fields["article_tpl"]=$obj_rs["article_tpl"];
			$arr_fields["article_source"]=$obj_rs["article_source"];
			$arr_fields["article_author"]=$obj_rs["article_author"];
			$arr_fields["article_state"]=$obj_rs["article_state"];
			$arr_fields["article_updateuid"]=$lng_uid;

			$arr_fields["article_css"]=$obj_rs["article_css"];
			$arr_fields["article_tag"]=$obj_rs["article_tag"];
			$arr_fields["article_isdel"]=$obj_rs["article_isdel"];
			$arr_fields["article_folder_id"]=$folder_id;
			$arr_fields["article_channel_id"]=$channel_id;
			
			$arr_return=self::on_save($arr_fields);
			if($arr_return["code"] != 0) return $arr_return;
		}
		return $arr_return;
	}
	/* 剪贴文章
	 *
	 */
	static function on_cut($ids , $channel_id , $folder_id) {
		$obj_db = cls_obj::db_w();
		$arr_return = cls_obj::db_w()->on_update(cls_config::DB_PRE."article" , array("article_folder_id" => $folder_id , "article_channel_id" => $channel_id) , "article_id in(" . $ids . ")");
		return $arr_return;
	}
	static function get_bykey($key) {
		$obj_rs = cls_obj::db()->get_one("select article_id,article_title,article_content from " . cls_config::DB_PRE . "article where article_key='" . $key . "'");
		if(empty($obj_rs)) return array("id"=>0 , "title"=>'',"cont"=>'');
		return array('id'=>$obj_rs['article_id'] , 'title' => $obj_rs['article_title'] , 'cont' => fun_get::filter($obj_rs['article_content'],true));
	}
}