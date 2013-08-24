<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class tab_sys_user_action {
	/* 行为触发保存积分 ，无返回值
	 * uid : 用户id
	 * action : 为 程序中相应位置预先定义好了的，在cfg.sys.score.php 中配置奖励积分
	 * arr : 附带参数，包括：{score 当此值非0时，将优先于 配置信息里的值 , about_id 相关id ,}
	 */
	static function on_action( $uid , $action , $arr = array() ) {
		$arr_return = array('code' => 0 , "msg" => '');
		$arr_action = cls_config::get($action , 'user.action' , '' , '');
		if(empty($arr_action)) {
			$arr_return["code"] = 500;
			$arr_return["msg"] = "未指定操作行为";
			return $arr_return;
		}
		if(isset($arr["level"]) && isset($arr_action["level_". $arr["level"]]) ) {//多层次积分，如连续登录时可能用到
			$arr_level = $arr_action["level_". $arr["level"]];
			if(isset($arr_level["score"])) $arr_action["score"] = $arr_level["score"];
			if(isset($arr_level["basescore"])) $arr_action["basescore"] = $arr_level["basescore"];
			if(isset($arr_level["addscore"])) $arr_action["addscore"] = $arr_level["addscore"];
			if(isset($arr_level["experience"])) $arr_action["experience"] = $arr_level["experience"];
			if(isset($arr_level["baseexperience"])) $arr_action["baseexperience"] = $arr_level["baseexperience"];
			if(isset($arr_level["addexperience"])) $arr_action["addexperience"] = $arr_level["addexperience"];
		}

		if(isset($arr["score"])) $arr_action["score"] = $arr["score"];
		if(isset($arr["basescore"])) $arr_action["basescore"] = $arr["basescore"];
		if(isset($arr["addscore"])) $arr_action["addscore"] = $arr["addscore"];
		if(isset($arr["experience"])) $arr_action["experience"] = $arr["experience"];
		if(isset($arr["baseexperience"])) $arr_action["baseexperience"] = $arr["baseexperience"];
		if(isset($arr["addexperience"])) $arr_action["addexperience"] = $arr["addexperience"];
		$lng_about_id = 0;
		if(isset($arr["about_id"])) $lng_about_id = $arr["about_id"];
		$arr_fields = array(
			"action_user_id" => $uid,
			"action_about_id" => $lng_about_id,
			"action_key" => $action,
			"action_addtime" => TIME,
			"action_act_uid" => $uid
		);
		//增加积分
		if(isset( $arr_action["score"] )) {
			$score = $arr_action["score"];
			if(isset($arr_action["basescore"])) $score = $score * $arr_action["basescore"];//按配置基数换算
			if(isset($arr_action["addscore"])) $score += $arr_action["addscore"]; //参数传入的积分优先
			$arr_fields["action_score"] = $score;
		}
		//增加
		if(isset( $arr_action["experience"] )) {
			$experience = $arr_action["experience"];
			if(isset($arr_action["baseexperience"])) $experience = $experience * $arr_action["baseexperience"];//按配置基数换算
			if(isset($arr_action["addexperience"])) $experience += $arr_action["addexperience"]; //参数传入的积分优先
			$arr_fields["action_experience"] = $experience;
		}
		if(isset($arr["beta"])) $arr_fields["action_beta"] = $arr["beta"];
		$arr = self::on_insert($arr_fields);
		return $arr;
	}
	
	/* 保存操作
	 * arr_fields : 为字段数据，默认如果包函 id，则为修改，否则为插入
	 * where : 默认为空，用于有时候条件修改
	 */
	static function on_insert($arr_fields) {
		$arr_return = array("code"=>0 , "msg"=>'');
		//必填项检查
		if(!isset($arr_fields['action_user_id']) || empty($arr_fields['action_user_id'])) {
			$arr_return['code'] = 113;
			$arr_return['msg']  = cls_language::get("action_user_id_is_null");//id不能为空
			return $arr_return;
		}
		$obj_rs = cls_obj::db()->get_one("select user_score,user_experience from " . cls_config::DB_PRE . "sys_user where user_id='" . $arr_fields['action_user_id'] . "'");
		if(empty($obj_rs)) {
			$arr_return['code'] = 113;
			$arr_return['msg']  = cls_language::get("action_user_id_is_null");//id不能为空
			return $arr_return;
		}
		$new_score = $obj_rs["user_score"];
		$new_experience = $obj_rs["user_experience"];
		$obj_db = cls_obj::db_w();
		$arr_update = array();
		$arr_where = array();
		if(isset($arr_fields["action_score"])) {
			$arr_update[] = "user_score=user_score+" . $arr_fields['action_score'];
			$new_score = $new_score + $arr_fields["action_score"];
			if($arr_fields["action_score"]<0) {
				if($arr_fields["action_user_id"] == cls_user::$perms["uid"]) {
					if( $new_score < 0 ) {
						$arr_return['code'] = 500;
						$arr_return['msg']  = "积分不够";
						return $arr_return;
					}
				}
				$arr_where[] = "user_score>=" . ($arr_fields["action_score"] * -1);
			}
		}
		if(isset($arr_fields["action_experience"])) {
			$arr_update[] = "user_experience=user_experience+" . $arr_fields['action_experience'];
			$new_experience = $new_experience + $arr_fields["action_experience"];
			if($arr_fields["action_experience"]<0) {
				if($arr_fields["action_user_id"] == cls_user::$perms["uid"]) {
					if( $new_experience < 0 ) {
						$arr_return['code'] = 500;
						$arr_return['msg']  = "经验不够";
						return $arr_return;
					}
				}
				$arr_where[] = "action_experience>=" . ($arr_fields["action_experience"] * -1);
			}
		}

		if(count($arr_update) > 0) {
			//事条开始
			$obj_db->begin("update_action");
			//更新用户积分数据
			$str_update = implode("," , $arr_update);
			$str_where = "";
			$arr_where[] = "user_id='" . $arr_fields['action_user_id'] . "'";
			if(count($arr_where)) $str_where = implode(" and " , $arr_where);
			$arr = cls_obj::db_w()->on_exe("update ".cls_config::DB_PRE."sys_user set " . $str_update . " where " . $str_where);
			if( $arr["code"] != 0 ) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = $arr["msg"];
				$obj_db->rollback("update_action");//终断事务
				return $arr_return;
			}
			if($obj_db->affected_rows() < 1) {
				$arr_return['code'] = 500;
				$arr_return['msg']  = "操作失败";
				$obj_db->rollback("update_action");//终断事务
				return $arr_return;
			}
		}
		if(!isset($arr_fields["action_addtime"])) $arr_fields["action_addtime"] = TIME;
		if(!isset($arr_fields['action_act_uid'])) $arr_fields["action_act_uid"] = cls_user::$perms["uid"];
		$arr_fields["action_day"] = date("Y-m-d" , $arr_fields["action_addtime"]);
		$arr_fields["action_time"] = date("Y-m-d H:i:s" , $arr_fields["action_addtime"]);
		//插入到用户表
		$arr = $obj_db->on_insert(cls_config::DB_PRE."sys_user_action",$arr_fields);
		if($arr['code'] == 0) {
			if(count($arr_update) > 0) {
				$obj_db->commit("update_action");//完成事务
			}
			if($arr_fields["action_user_id"] == cls_user::$perms["uid"]) {
				cls_user::$perms["score"] = $new_score;
				cls_user::$perms["experience"] = $new_experience;
			}
			$arr_return['id'] = $obj_db->insert_id();
		} else {
			if(count($arr_update) > 0) {
				$obj_db->rollback("update_action");//完成事务
			}
			$arr_return['code'] = $arr['code'];
			$arr_return['msg']  = cls_language::get("db_edit");
		}
		return $arr_return;
	}
}