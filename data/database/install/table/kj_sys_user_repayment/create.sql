#-----------------创建表--- kj_sys_user_repayment
DROP TABLE IF EXISTS `{DB_PRE}sys_user_repayment`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_user_repayment` (
`repayment_id` int(11) NOT NULL auto_increment,`repayment_user_id` int(10) NOT NULL COMMENT '用户id',`repayment_val` decimal(10,2) NOT NULL COMMENT '金额',`repayment_addtime` int(10) NOT NULL COMMENT '添加时间',`repayment_day` date NOT NULL COMMENT '添加日期',`repayment_time` datetime NOT NULL COMMENT '添加时间',`repayment_beta` varchar(255) NOT NULL COMMENT '备注',`repayment_type` smallint(2) NOT NULL COMMENT '类型',`repayment_about_id` int(10) NOT NULL COMMENT '相关id',PRIMARY KEY (`repayment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

