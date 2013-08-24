#-----------------创建表--- kj_meal_info
DROP TABLE IF EXISTS `{DB_PRE}meal_info`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}meal_info` (
`info_id` int(10) NOT NULL auto_increment,`info_area_id` int(10) NOT NULL,`info_area_allid` varchar(50) NOT NULL,`info_area` varchar(50) NOT NULL,`info_louhao1` varchar(20) NOT NULL,`info_louhao2` varchar(20) NOT NULL,`info_company` varchar(50) NOT NULL,`info_depart` varchar(50) NOT NULL,`info_name` varchar(50) NOT NULL,`info_sex` varchar(10) NOT NULL,`info_tel` varchar(20) NOT NULL,`info_telext` varchar(10) NOT NULL,`info_mobile` varchar(20) NOT NULL,`info_email` varchar(50) NOT NULL,`info_arrive` varchar(50) NOT NULL,`info_addtime` int(10) NOT NULL,`info_user_id` int(10) NOT NULL,PRIMARY KEY (`info_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

