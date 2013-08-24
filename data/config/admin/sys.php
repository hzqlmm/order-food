<?php
/* 数据字典
 * 数据库 -> 表 -> 字段
 * 值：大于 10 表示不参与用户配置 , 1表示显示，0表示不显示
 */
return array(
	"sys.area" => array(
	//用户表,sys_user
		"area_id" => array("val" => 0,"w" => 0), //区域id
		"area_sort" => array("val" => 1,"w" => 50), //排序
		"area_name" => array("val" => 1,"w" => 120), //名称
		"area_val" => array("val" => 1,"w" => 100), //值
		"area_tag" => array("val" => 1,"w" => 100), //附近建筑
		"area_pin" => array("val" => 1,"w" => 100), //附近建筑
		"area_jian" => array("val" => 1,"w" => 100), //附近建筑
		"area_dispatch_price" => array("val" => 1,"w" => 100), //附近建筑
		"area_dispatch_time" => array("val" => 1,"w" => 100), //附近建筑
	),
	"sys.config" => array(
	//用户表,sys_user
		"config_id" => array("val" => 2,"w" => 0), //id
		"config_sort" => array("val" => 0,"w" => 100), //注册时间
		"config_intro" => array("val" => 1,"w" => 150), //注册时间
		"config_val" => array("val" => 1,"w" => 480), //值
		"config_name" => array("val" => 1,"w" => 100), //变量名称
	),
	"sys.user" => array(
	//用户表,sys_user
		"user_id" => array("val" => 0,"w" => 0), //用户id
		"user_name" => array("val" => 1,"w" => 100), //用户名
		"user_email" => array("val" => 1,"w" => 150), //电子邮箱
		"user_regtime" => array("val" => 1,"w" => 120), //注册时间
		"user_regip" => array("val" => 0,"w" => 50), //注册IP
		"user_loginip" => array("val" => 0,"w" => 50), //登录IP
		"user_netname" => array("val" => 0,"w" => 50), //昵称
		"user_logintime" => array("val" => 1,"w" => 120), //登录时间
		"user_loginnum" => array("val" => 1,"w" => 0), //登录次数
		"user_type" => array("val" => 0,"w" => 50), //类型
		"user_state" => array("val" => 1,"w" => 0), //状态
		"user_score" => array("val" => 0,"w" => 0), //当前积分
		"user_experience" => array("val" => 0,"w" => 0), //当前经验
		"user_email_verify" => array("val" => 0,"w" => 0), //是否邮件验证
		"user_birthday" => array("val" => 0,"w" => 80), //出生日期
		"user_sex" => array("val" => 0,"w" => 0), //性别
		"user_location" => array("val" => 0,"w" => 0), //当前所在地
		"user_house_location" => array("val" => 0,"w" => 0), //家乡
		"user_tel" => array("val" => 0,"w" => 0), //电话
		"user_mobile" => array("val" => 0,"w" => 0), //手机号
		"user_address" => array("val" => 0,"w" => 0), //联系地址
		"user_realname" => array("val" => 0,"w" => 0), //真实姓名
		"user_invite_uid" => array("val" => 0,"w" => 0), //邀请人
		"user_group_id" => array("val" => 10,"w" => 0), //用户组id
		"group_name" => array("val" => 1,"w" => 80), //用户组"
		"user_order_num" => array("val" => 1,"w" => 80), //订单数
		"user_totalpay" => array("val" => 1,"w" => 80), //订单金额
	),
	"sys.user.group" => array(
	//用户组 , sys_user_group;
		"group_id" => array("val" => 1,"w" => 0), //用户组id
		"group_name" => array("val" => 1,"w" => 0), //名称
		"group_addtime" => array("val" => 1,"w" => 0), //添加时间
		"group_updatetime" => array("val" => 1,"w" => 0), //更新时间
		"group_sort" => array("val" => 1,"w" => 0), //排序
		"group_pid" => array("val" => 1,"w" => 0), //上级id
		"group_limit_admin" => array("val" => 1,"w" => 0), //权限
	),
	"sys.user.log" => array(
	//用户日志 , sys_user_log;
		"log_id" => array("val" => 2,"w" => 0), //日志id
		"log_user_id" => array("val" => 2,"w" => 0), //用户id
		"user_name" => array("val" => 1,"w" => 50), //用户
		"log_app_module" => array("val" => 1,"w" => 80), //模型
		"log_app" => array("val" => 1,"w" => 80), //页面
		"log_app_act" => array("val" => 1,"w" => 80), //行为
		"log_cont" => array("val" => 1,"w" => 200), //明细
		"log_ip" => array("val" => 1,"w" => 50), //IP
		"log_addtime" => array("val" => 1,"w" => 120), //时间
		"log_module" => array("val" => 2,"w" => 0), //分类
		"log_key" => array("val" => 2,"w" => 0), //键
	),
	"sys.user.score" => array(
	//用户积分 , sys_user_score;
		"score_id" => array("val" => 1,"w" => 0), //日志id
		"score_user_id" => array("val" => 1,"w" => 0), //用户id
		"score_val" => array("val" => 1,"w" => 0), //积分
		"score_key" => array("val" => 1,"w" => 0), //来源
		"score_addtime" => array("val" => 1,"w" => 0), //时间
	),
	"sys.user.action" => array(
	//用户积分 , sys_user_score;
		"action_id" => array("val" => 1,"w" => 0), //id
		"action_user_id" => array("val" => 11,"w" => 100), //用户id
		"user_name" => array("val" => 1,"w" => 100), //用户id
		"action_score" => array("val" => 1,"w" => 100), //积分
		"action_experience" => array("val" => 1,"w" => 100), //经验值
		"action_key" => array("val" => 1,"w" => 200), //行为
		"action_addtime" => array("val" => 1,"w" => 150), //添加时间
		"action_beta" => array("val" => 1,"w" => 200), //行为
	),
	"sys.user.repayment" => array(
	//用户积分 , sys_user_score;
		"repayment_id" => array("val" => 1,"w" => 0), //id
		"repayment_user_id" => array("val" => 11,"w" => 100), //用户id
		"user_name" => array("val" => 1,"w" => 100), //用户名
		"repayment_val" => array("val" => 1,"w" => 100), //用户id
		"repayment_time" => array("val" => 1,"w" => 100), //积分
		"repayment_beta" => array("val" => 1,"w" => 100), //经验值
		"repayment_type" => array("val" => 11,"w" => 200), //行为
		"repayment_about_id" => array("val" => 11,"w" => 150), //添加时间
	),
	"sys.verify" => array(
	//验证记录表
		"verify_id" => array("val" => 0,"w" => 0), //id
		"verify_user_id" => array("val" => 0,"w" => 100), //用户id
		"user_name" => array("val" => 1,"w" => 100), //用户名
		"verify_type" => array("val" => 0,"w" => 100), //验证类型
		"verify_key" => array("val" => 1,"w" => 150), //验证值
		"verify_time" => array("val" => 1,"w" => 100), //生成时间
		"verify_retime" => array("val" => 1,"w" => 100), //验证时间
		"verify_state" => array("val" => 1,"w" => 200), //状态
	)
);
?>