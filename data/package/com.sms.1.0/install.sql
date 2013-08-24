CREATE TABLE IF NOT EXISTS `{DB_PRE}other_sms` (
  `sms_id` int(10) NOT NULL AUTO_INCREMENT,
  `sms_content` varchar(1000) NOT NULL COMMENT '短信内容',
  `sms_tel` varchar(11) NOT NULL COMMENT '接收号码',
  `sms_type` smallint(2) NOT NULL COMMENT '信短类型',
  `sms_addtime` int(10) NOT NULL COMMENT '发送时间',
  `sms_day` date NOT NULL COMMENT '发送日期',
  `sms_time` datetime NOT NULL COMMENT '发送时间',
  `sms_about_id` int(10) NOT NULL COMMENT '相关id',
  `sms_recont` varchar(50) NOT NULL COMMENT '回复内容',
  `sms_retime` int(10) NOT NULL COMMENT '回复时间',
  `sms_confirm_id` varchar(10) NOT NULL COMMENT '认确码',
  PRIMARY KEY (`sms_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{DB_PRE}other_sms_re` (
  `re_id` int(11) NOT NULL AUTO_INCREMENT,
  `re_cont` varchar(255) NOT NULL COMMENT '回复内容',
  `re_tel` varchar(20) NOT NULL COMMENT '回复电话',
  `re_addtime` int(11) NOT NULL COMMENT '回复保存时间',
  `re_time` datetime NOT NULL COMMENT '回复时间',
  `re_day` date NOT NULL COMMENT '回复日期',
  PRIMARY KEY (`re_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;