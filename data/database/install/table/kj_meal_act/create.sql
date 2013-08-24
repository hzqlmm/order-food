#-----------------创建表--- kj_meal_act
DROP TABLE IF EXISTS `{DB_PRE}meal_act`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}meal_act` (
`act_id` int(10) NOT NULL auto_increment,`act_shop_id` int(10) NOT NULL,`act_name` varchar(50) NOT NULL,`act_where` smallint(2) default '0',`act_where_val` varchar(255) NOT NULL,`act_method` smallint(2) default '0',`act_method_val` varchar(255) NOT NULL,`act_starttime` datetime NOT NULL,`act_endtime` datetime NOT NULL,`act_addtime` int(10) NOT NULL,`act_state` smallint(2) default '0',`act_beta` varchar(255) NOT NULL,`act_isdel` tinyint(1) default '0',PRIMARY KEY (`act_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

