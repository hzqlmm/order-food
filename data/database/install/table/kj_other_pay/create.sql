#-----------------创建表--- kj_other_pay
DROP TABLE IF EXISTS `{DB_PRE}other_pay`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}other_pay` (
`pay_id` int(10) NOT NULL auto_increment,`pay_user_id` int(10) NOT NULL COMMENT '支付用户id',`pay_val` decimal(10,2) NOT NULL COMMENT '支付金额',`pay_addtime` int(10) NOT NULL COMMENT '添加时间',`pay_day` date NOT NULL COMMENT '支付日期',`pay_time` datetime NOT NULL COMMENT '支付时间',`pay_return_id` varchar(30) NOT NULL COMMENT '支付返回id',`pay_return_val` decimal(10,2) NOT NULL COMMENT '实际支付金额',`pay_type` smallint(2) NOT NULL COMMENT '支付类型',`pay_state` smallint(2) NOT NULL COMMENT '支付状态',`pay_about_id` int(10) NOT NULL COMMENT '相关支付id',`pay_method` varchar(50) NOT NULL COMMENT '支付方式',`pay_title` varchar(50) NOT NULL COMMENT '标题',`pay_beta` varchar(255) NOT NULL COMMENT '备注',`pay_number` varchar(30) NOT NULL COMMENT '支付流水号',PRIMARY KEY (`pay_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

