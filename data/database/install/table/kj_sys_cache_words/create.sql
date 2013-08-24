#-----------------创建表--- kj_sys_cache_words
DROP TABLE IF EXISTS `{DB_PRE}sys_cache_words`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_cache_words` (
`words_val` varchar(50) NOT NULL,`words_pin` varchar(50) NOT NULL,`words_jian` varchar(50) NOT NULL,`words_type` varchar(50) NOT NULL,`words_updatetime` int(10) NOT NULL,`words_num` int(10) NOT NULL,PRIMARY KEY (`words_val`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8

