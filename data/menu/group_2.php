<?php
return array (
  '系统' => 
  array (
    0 => 
    array (
      'url' => '?app_module=sys&app=config',
      'name' => '模块设置',
      'app_module' => 'sys',
      'app' => 'config',
    ),
    1 => 
    array (
      'url' => '?app_module=sys&app=user.log',
      'name' => '管理日志',
      'app_module' => 'sys',
      'app' => 'user.log',
    ),
    2 => 
    array (
      'url' => '?app_module=sys&app=log',
      'name' => '系统日志',
      'app_module' => 'sys',
      'app' => 'log',
    ),
    3 => 
    array (
      'url' => '?app_module=sys&app=verify',
      'name' => '验证记录',
      'app_module' => 'sys',
      'app' => 'verify',
    ),
    4 => 
    array (
      'url' => '?app_module=sys&app=database',
      'name' => '数 据 库',
      'app_module' => 'sys',
      'app' => 'database',
    ),
  ),
  '用户' => 
  array (
    0 => 
    array (
      'url' => '?app_module=sys&app=user.action',
      'name' => '经验积分',
      'app_module' => 'sys',
      'app' => 'user.action',
    ),
    1 => 
    array (
      'url' => '?app_module=sys&app=user.repayment',
      'name' => '预付款记录',
      'app_module' => 'sys',
      'app' => 'user.repayment',
    ),
    2 => 
    array (
      'url' => '?app_module=other&app=pay&app_act=record',
      'name' => '充值记录',
      'app_module' => 'sys',
      'app' => 'pay',
    ),
  ),
  '组件' => 
  array (
    0 => 
    array (
      'url' => '?app_module=sys&app=user.group',
      'name' => '用 户 组',
      'app_module' => 'sys',
      'app' => 'user.group',
    ),
    1 => 
    array (
      'url' => '?app_module=sys&app=user',
      'name' => '注册用户',
      'app_module' => 'sys',
      'app' => 'user',
    ),
    2 => 
    array (
      'url' => '?app_module=sys&app=components',
      'name' => '组件管理',
      'app_module' => 'sys',
      'app' => 'components',
    ),
  ),
  '文章' => 
  array (
    0 => 
    array (
      'url' => '?app_module=article&app=channel',
      'name' => '频道管理',
      'app_module' => 'article',
      'app' => 'channel',
    ),
    1 => 
    array (
      'url' => '?app_module=article&app=topic',
      'name' => '专题管理',
      'app_module' => 'article',
      'app' => 'topic',
    ),
    2 => 
    array (
      'name' => '文章管理',
      'app' => 'article',
      'app_module' => '',
      'list' => 
      array (
        'app' => 'index',
        'app_act' => 'menu_article',
      ),
    ),
  ),
);