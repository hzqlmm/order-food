<?php
/*
 *
 *
 * 2013-03-24
 */
class cls_user_action {
	/* 行为触发保存积分 ，无返回值
	 * uid : 用户id
	 * action : 为 程序中相应位置预先定义好了的，在cfg.sys.score.php 中配置奖励积分
	 * arr : 附带参数，包括：{score 当此值非0时，将优先于 配置信息里的值 , about_id 相关id ,}
	 */
	static function on_action( $uid , $action , $arr = array() ) {
		$arr_action = cls_config::get($action , 'user.action');
		if(empty($arr_action)) return;
		$score = $arr_action["score"];
		if(isset($arr["score_key"]) && isset($arr_action["score_key"]) && isset($arr_action["score_key"]["key".$arr["score_key"]]) ) {//多层次积分，如连续登录时可能用到
			$score = $score + $arr_action["score_key"]["key".$arr["score_key"]]; //在原积分上附加
		}
		if(isset($arr["score"])) $score = (int)$arr["score"]; //参数传入的积分优先
		if(isset($arr_action["basescore"])) $score = $score * $arr_action["basescore"];//按配置基数换算

		//插入到积分记录表
		$lng_about_id = 0;
		if(isset($arr["about_id"])) $lng_about_id = $arr["about_id"];
		$arr_fields = array(
			"score_user_id" => $uid,
			"score_about_id" => $lng_about_id,
			"score_val" => $score,
			"score_key" => $action,
			"score_addtime" => TIME,
			"score_act_uid" => cls_obj::get("cls_user")->uid
		);
		$arr = tab_sys_user_score::on_insert($arr_fields);
		return $arr;
	}

}