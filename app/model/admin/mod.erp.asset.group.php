<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_erp_asset_group extends inc_mod_meal {

	// 获取，移动分组列表
	function get_group_select() {
		$id  = (int)fun_get::get("id");
		$shop_id = $this->admin_shop["id"];
		if($shop_id <0 ) $shop_id = 0;
		$where = " group_id!='" . $id . "' and group_shop_id='" . $shop_id . "'";

		$arr = tab_erp_asset_group::get_list_layer( 0 , 1 , $where);
		$arr_select = array();
		//添加默认
		$arr_select[] = array("val" => 0 , "title" => cls_language::get("layer_top") , "layer" => 0);
		foreach($arr["list"] as $item) {
			$arr_select[] = array("val" => $item['group_id'] , "title" => $item['group_name'] , "layer" => $item["layer"]);
		}
		$str = fun_html::select("group_id",$arr_select);
		return $str;
	}

	function on_move_save() {
		$arr_return = array("code"=>0 , "id"=>0 , "msg" => cls_language::get("save_ok"));
		$id = (int)fun_get::get("id");
		$pid = (int)fun_get::get("group_id");
		if(empty($id)) {
			$arr_return["code"] = 22;
			$arr_return['msg']  = cls_language::get("no_id");
			return $arr_return;
		}
		$arr = tab_erp_asset_group::on_move($id , $pid);
		if($arr['code']==0) {
			if(isset($arr['id'])) $arr_return['id'] = $arr['id'];
		} else {
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = $arr['msg'];
		}
		return $arr_return;
	}
	/* 保存数据
	 * 
	 */
	function on_save_all() {
		$arr_return = array("code" => 0 ,"id"=>0 , "msg" => cls_language::get("save_ok"));
		$arr_group_name = fun_get::get("group_name");
		$arr_group_sort = fun_get::get("group_sort");
		$arr_group_jian = fun_get::get("group_jian");
		$arr_group_pid  = fun_get::get("pid");
		$arr_group_id   = fun_get::get("group_id");
		$arr_group_id_layer   = fun_get::get("group_id_layer");

		
		$arr_resave = array();
		$lng_count = count($arr_group_name);

		//开始事务
		cls_obj::db_w()->begin("save_group");
		//循环统计已有 id
		$arr_id = array();
		for( $i = 1 ; $i < $lng_count ; $i++) {
			$lng_id = (int)$arr_group_id[$i];
			if($lng_id > 0) $arr_id[] = $lng_id;
		}
		$str_ids = fun_format::arr_id($arr_id);
		if( !empty($str_ids) ) {
			$str_where = "not group_id in(".$str_ids.")";
		} else {
			$str_where = "1>0";//绝对成立条件
		}
		//首先删除没在保存id中的所有记录
		tab_erp_asset_group::on_delete(array(),$str_where);
		for( $i = 1 ; $i < $lng_count ; $i++) {
			$arr_fields = array(
				"group_id" => (int)$arr_group_id[$i],
				"group_name" => $arr_group_name[$i],
				"group_sort" => $arr_group_sort[$i],
				"group_jian" => $arr_group_jian[$i],
			);

			if($arr_fields["group_id"]<1 && empty($arr_group_name[$i])) continue;
			//不直接修改 pid,只在新增时保存 pid
			if( $arr_fields["group_id"]<1 && !empty($arr_group_pid[$i]) && isset($arr_resave[$arr_group_pid[$i]]) ) {
				$arr_fields["group_pid"] = $arr_resave[$arr_group_pid[$i]]["id"];
			}
			$arr_resave[$arr_group_id_layer[$i]] = tab_erp_asset_group::on_save($arr_fields);
			if($arr_resave[$arr_group_id_layer[$i]]["code"]!=0) {
				cls_obj::db_w()->rollback("save_group");//回滚
				$arr_return['code'] = $arr_resave[$arr_group_id_layer[$i]]["code"];
				$arr_return['msg'] = $arr_resave[$arr_group_id_layer[$i]]["msg"];
				return $arr_return;
			}
		}
		//完成事务
		cls_obj::db_w()->commit("save_group");
		return $arr_return;
	}
}