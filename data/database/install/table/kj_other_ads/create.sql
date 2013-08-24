#-----------------创建表--- kj_other_ads
DROP TABLE IF EXISTS `{DB_PRE}other_ads`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}other_ads` (
`ads_id` int(10) NOT NULL auto_increment,`ads_title` varchar(50) NOT NULL,`ads_type` varchar(10) NOT NULL,`ads_html` text NOT NULL,`ads_cont` text NOT NULL,`ads_starttime` int(10) NOT NULL,`ads_endtime` int(10) NOT NULL,`ads_state` smallint(2) NOT NULL COMMENT '״̬',`ads_user_id` int(10) NOT NULL,`ads_addtime` int(10) NOT NULL,`ads_updatetime` int(10) NOT NULL,PRIMARY KEY (`ads_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2

