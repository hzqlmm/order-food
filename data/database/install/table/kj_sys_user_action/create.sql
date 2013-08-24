#-----------------创建表--- kj_sys_user_action
DROP TABLE IF EXISTS `{DB_PRE}sys_user_action`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_user_action` (
`action_id` int(10) NOT NULL auto_increment,`action_user_id` int(10) NOT NULL COMMENT '用户id',`action_about_id` int(10) NOT NULL COMMENT '相关id',`action_score` int(10) NOT NULL COMMENT '分值',`action_experience` int(10) NOT NULL,`action_key` varchar(30) NOT NULL COMMENT '积分关键词',`action_addtime` int(10) NOT NULL COMMENT '时间',`action_act_uid` int(10) NOT NULL,`action_beta` varchar(100) NOT NULL,`action_day` date NOT NULL COMMENT '添加日期',`action_time` datetime NOT NULL COMMENT '具体时间',PRIMARY KEY (`action_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

