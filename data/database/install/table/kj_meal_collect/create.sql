#-----------------创建表--- kj_meal_collect
DROP TABLE IF EXISTS `{DB_PRE}meal_collect`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}meal_collect` (
`collect_id` int(10) NOT NULL auto_increment,`collect_user_id` int(10) NOT NULL COMMENT '用户id',`collect_for_id` int(10) default '0' COMMENT '目标id',`collect_type` smallint(2) default '0' COMMENT '类型',`collect_addtime` int(10) NOT NULL COMMENT '收藏时间',PRIMARY KEY (`collect_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

