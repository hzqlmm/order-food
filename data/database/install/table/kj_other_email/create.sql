#-----------------创建表--- kj_other_email
DROP TABLE IF EXISTS `{DB_PRE}other_email`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}other_email` (
`email_id` int(10) NOT NULL auto_increment,`email_title` varchar(50) NOT NULL COMMENT '邮件主题',`email_cont` text NOT NULL COMMENT '内容',`email_account_mode` smallint(2) NOT NULL COMMENT '发送模式',`email_to` text NOT NULL COMMENT '收件箱，多个以;号分隔',`email_account_dir` varchar(100) NOT NULL COMMENT '多账号模式下，账号文件存放目录',`email_from` varchar(100) NOT NULL COMMENT '发件箱',`email_attachment` varchar(500) NOT NULL COMMENT '附件',`email_addtime` int(10) NOT NULL COMMENT '发送时间',`email_num` smallint(2) NOT NULL COMMENT '发送次数',`email_serverinfo` text NOT NULL,`email_userinfo` text NOT NULL COMMENT '用户条件信息',`email_type` smallint(2) NOT NULL COMMENT '邮件类型',PRIMARY KEY (`email_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

