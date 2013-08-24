<?php
/** 积分配置类
 *  basescore : 即将：score = score*basescore 如：当充值100的时候，提成１积分，则设置：为:0.01
 *  addscore  : 即在算出 score 后，再加上的值
 *  level_10 : 多重定义，即当level值达到某一级别后，按此时这个 level_10 设置的算
 *  experience 同 score 一样，只不过是表示经验
 *  type : 0为系统配置， 1 为用户可以自配置
 */
return array (
  'meal_submit_order' => 
  array (
    'type' => 1,
    'title' => '下单抵扣',
    'score' => 0,
    'basescore' => '-1',
    'experience' => '1',
    'baseexperience' => 1,
    'addscore' => '0',
    'addexperience' => '0',
  ),
  'meal_submit_ticket' => 
  array (
    'type' => 1,
    'title' => '发票消耗',
    'score' => 0,
    'basescore' => '-1',
    'experience' => '0',
    'baseexperience' => 0,
    'addscore' => '0',
    'addexperience' => '0',
  ),
  'meal_admin' => 
  array (
    'type' => 0,
    'title' => '管理员后台操作',
    'score' => 0,
    'basescore' => 1,
    'experience' => 0,
    'baseexperience' => 1,
    'addscore' => 0,
    'addexperience' => 0,
  ),
  'meal_init' => 
  array (
    'type' => 1,
    'title' => '积分初始值',
    'score' => 0,
    'basescore' => '1',
    'experience' => '1',
    'baseexperience' => 1,
    'addscore' => '0',
    'addexperience' => '0',
  ),
  'meal_submit_order_ok' => 
  array (
    'type' => 1,
    'title' => '下单成功',
    'score' => 0,
    'basescore' => '5',
    'experience' => '1',
    'baseexperience' => 1,
    'addscore' => '0',
    'addexperience' => '0',
  ),
  'user_login_day' => 
  array (
    'type' => 1,
    'title' => '登录奖励',
    'score' => 0,
    'basescore' => '0',
    'experience' => '1',
    'baseexperience' => 1,
    'addscore' => '0',
    'addexperience' => '0',
  ),
  'user_login_continue' => 
  array (
    'type' => 1,
    'title' => '连续登录',
    'score' => 0,
    'basescore' => '0',
    'experience' => '1',
    'baseexperience' => 1,
    'addscore' => '0',
    'addexperience' => '0',
    'level_1' => 
    array (
      'experience' => 1,
    ),
    'level_2' => 
    array (
      'experience' => 3,
    ),
    'level_3' => 
    array (
      'experience' => 5,
    ),
    'level_4' => 
    array (
      'experience' => 7,
    ),
    'level_5' => 
    array (
      'experience' => 10,
    ),
  ),
  'level_up' => 
  array (
    'type' => 1,
    'title' => '升级奖励',
    'score' => 0,
    'basescore' => '1',
    'experience' => '1',
    'baseexperience' => 1,
    'addscore' => '0',
    'addexperience' => '0',
  ),
);