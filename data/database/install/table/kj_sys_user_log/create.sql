#-----------------创建表--- kj_sys_user_log
DROP TABLE IF EXISTS `{DB_PRE}sys_user_log`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_user_log` (
`log_id` int(10) NOT NULL auto_increment,`log_user_id` int(10) NOT NULL COMMENT '用户id',`log_ip` varchar(20) NOT NULL COMMENT 'ip地址',`log_app_act` varchar(50) NOT NULL COMMENT '请求行为',`log_app` varchar(50) NOT NULL COMMENT '请求页面',`log_app_module` varchar(50) NOT NULL COMMENT '请求所属模块',`log_addtime` int(10) NOT NULL COMMENT '时间',`log_cont` text NOT NULL COMMENT '日志内容',`log_module` varchar(50) NOT NULL COMMENT '所属管理目录',`log_key` varchar(50) NOT NULL COMMENT '关健词',PRIMARY KEY (`log_id`),
UNIQUE KEY `log_key` (`log_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1

