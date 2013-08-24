#-----------------创建表--- kj_meal_menu_comment
DROP TABLE IF EXISTS `{DB_PRE}meal_menu_comment`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}meal_menu_comment` (
`comment_id` int(10) NOT NULL auto_increment,`comment_user_id` int(10) default '0' COMMENT '用户id',`comment_menu_id` int(10) default '0' COMMENT '菜品id',`comment_shop_id` int(10) default '0' COMMENT '店铺id',`comment_order_id` int(10) default '0' COMMENT '订单id',`comment_val` smallint(2) default '0' COMMENT '评价值',`comment_addtime` int(10) NOT NULL,PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

