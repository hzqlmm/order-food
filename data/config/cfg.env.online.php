<?php
class cfg_env {
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PWD = '123456';
const DB_NAME = 'kj_meal';
const DB_PRE = 'kj_';
const DB_CHARSET = 'utf8';
const IS_TEST = 0;//大于零为测试环境，小于或等于零为非测试环境
const DEFAULT_LANGUAGE = 'chinese';//系统默认语言
const SESSION_SAVE_HANNDLER = 'db'; // 会话保存模式 ，取值范围：1.file , 2.memcache , 3.db
const SESSION_SAVEPATH = '';// file模式下，session文件保存目录 ，默认为空，保存在 /data/session 目录下
const SESSION_MAXLIFETIME = '1440000'; // 默认过期时间,秒数
const SESSION_DIVISOR = 1000; // 回收机率 , 被除数
const USER_CENTER = 'user.klkkdj'; // 用户中心，默认：user.klkkdj ，目前还支持：user.uc 指discuz的ucenter
const COOKIE_PRE = 'kj_';
const MD5_KEY = '62183';
}