#-----------------创建表--- kj_meal_menu_today
DROP TABLE IF EXISTS `{DB_PRE}meal_menu_today`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}meal_menu_today` (
`today_id` int(10) NOT NULL auto_increment,`today_menu_id` int(10) NOT NULL COMMENT '菜单id',`today_num` int(10) NOT NULL COMMENT '数量',`today_sold` int(10) NOT NULL,`today_date` int(10) NOT NULL COMMENT '日期',`today_date_period` smallint(2) NOT NULL COMMENT '当天时间段',`today_shop_id` int(10) NOT NULL COMMENT '店铺id',`today_addtime` int(10) NOT NULL,`today_updatetime` int(10) NOT NULL,PRIMARY KEY (`today_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

