#-----------------创建表--- kj_meal_checkout
DROP TABLE IF EXISTS `{DB_PRE}meal_checkout`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}meal_checkout` (
`checkout_id` int(10) NOT NULL auto_increment,`checkout_shop_id` int(10) NOT NULL COMMENT '店铺id',`checkout_money` decimal(10,2) NOT NULL COMMENT '结算金额',`checkout_date` date NOT NULL COMMENT '结算结束日(不包括)',`checkout_user_id` int(10) NOT NULL COMMENT '操作人',`checkout_addtime` int(10) NOT NULL COMMENT '添加时间',`checkout_beta` varchar(255) NOT NULL,PRIMARY KEY (`checkout_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

