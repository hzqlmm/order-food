<?php
/*
 *
 *
 * 2013-03-24
 *
 * 数据字典
 * 数据库 -> 表 -> 字段
 * 值：大于 10 表示不参与用户配置 , 1表示显示，0表示不显示
 */
return array(
	"article" => array(
	//文章表
		"article_id" => array("val" => 0,"w" => 0), //id
		"article_title" => array("val" => 1,"w" => 120), //标题
		"article_addtime" => array("val" => 0,"w" => 50), //添加时间
		"article_updatetime" => array("val" => 0,"w" => 0), //修改时间
		"article_attribute" => array("val" => 0,"w" => 50), //属性
		"folder_name" => array("val" => 1,"w" => 100), //所属目录
		"article_hits" => array("val" => 1,"w" => 100), //浏览次数
		"channel_name" => array("val" => 1,"w" => 100), //频道名称
		"article_source" => array("val" => 1,"w" => 100), //来源
		"article_author" => array("val" => 1,"w" => 100), //作者
		"article_state" => array("val" => 1,"w" => 100), //状态
		"article_tag" => array("val" => 1,"w" => 100), //标签
		"article_isread" => array("val" => 1,"w" => 100), //是否已查看
		"article_uid" => array("val" => 1,"w" => 100), //添加人
		"article_updateuid" => array("val" => 1,"w" => 100), //最后修改人
		"article_topic_id" => array("val" => 1,"w" => 100), //
		"article_channel_id" => array("val" => 11,"w" => 0), //
		"article_folder_id" => array("val" => 11,"w" => 0), //
		"article_pic" => array("val" => 11,"w" => 0), //
		"article_pic_big" => array("val" => 11,"w" => 0), //
	),
	"article.channel" => array(
	//文章频道表
		"channel_id" => array("val" => 0,"w" => 0), //id
		"channel_name" => array("val" => 1,"w" => 120), //名称
		"channel_html" => array("val" => 0,"w" => 50), //是否生成html
		"channel_html_dir" => array("val" => 0,"w" => 0), //html目录
		"channel_html_dirstyle" => array("val" => 0,"w" => 50), //html目录分隔方式
		"channel_addtime" => array("val" => 1,"w" => 100), //添加时间
		"channel_state" => array("val" => 1,"w" => 100), //状态
		"channel_mode" => array("val" => 0,"w" => 100), //模式
	),
	"article.topic" => array(
	//文章专题表
		"topic_id" => array("val" => 0,"w" => 0), //id
		"topic_name" => array("val" => 1,"w" => 120), //名称
		"topic_tpl" => array("val" => 11,"w" => 50), //专题模板
		"topic_addtime" => array("val" => 1,"w" => 100), //添加时间
		"topic_state" => array("val" => 1,"w" => 100), //状态
	),
);