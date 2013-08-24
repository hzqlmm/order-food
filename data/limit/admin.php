<?php
return array (
  'index' => 
  array (
    'name' => '全局',
    'list' => 
    array (
      'index' => 
      array (
        'act' => 
        array (
          'main' => '桌面',
          'help' => '帮助中心',
          'guide' => '开店向导',
          'msg' => '意见反馈',
          'call' => '来电显示',
        ),
        'name' => '首页',
      ),
    ),
  ),
  'sys' => 
  array (
    'name' => '系统',
    'list' => 
    array (
      'common' => 
      array (
        'act' => 
        array (
          0 => 'clear_cache',
          1 => 'update_pwd',
        ),
        'name' => '通用',
      ),
      'config' => 
      array (
        'act' => 
        array (
          0 => 'add',
          1 => 'delete',
          2 => 'edit',
          3 => 'update',
        ),
        'name' => '模块设置',
      ),
      'log' => 
      array (
        'act' => 
        array (
          0 => 'delete',
        ),
        'name' => '系统日志',
      ),
      'area' => 
      array (
        'act' => 
        array (
          0 => 'move',
          1 => 'save',
          2 => 'delete',
        ),
        'name' => '地区管理',
      ),
      'database' => 
      array (
        'act' => 
        array (
          0 => 'optimize',
          1 => 'repair',
          2 => 'backup',
          3 => 'reback',
          4 => 'del',
        ),
        'name' => '数据库',
      ),
      'user' => 
      array (
        'act' => 
        array (
          0 => 'add',
          1 => 'del',
          2 => 'delete',
          3 => 'edit',
          4 => 'dellist',
          5 => 'state',
          6 => 'clear_config',
        ),
        'name' => '注册用户',
      ),
      'user.group' => 
      array (
        'act' => 
        array (
          0 => 'move',
          1 => 'save',
          2 => 'limit',
        ),
        'name' => '用户组',
      ),
      'user.depart' => 
      array (
        'act' => 
        array (
          0 => 'move',
          1 => 'save',
        ),
        'name' => '组织架构',
      ),
      'user.action' => 
      array (
        'act' => 
        array (
          'config' => '配置',
        ),
        'name' => '积分日志',
      ),
      'user.log' => 
      array (
        'act' => 
        array (
          0 => 'delete',
        ),
        'name' => '用户日志',
      ),
      'components' => 
      array (
        'act' => 
        array (
          'not' => '未安装',
          'down' => '下载',
          'install' => '安装',
          'uninstall' => '卸载',
          'step1' => '安装步骤',
        ),
        'name' => '组件',
      ),
    ),
  ),
  'other' => 
  array (
    'name' => '组件',
    'list' => 
    array (
      'ads' => 
      array (
        'act' => 
        array (
          0 => 'edit',
          1 => 'save',
          2 => 'delete',
        ),
        'name' => '广告管理',
      ),
      'email' => 
      array (
        'act' => 
        array (
          0 => 'edit',
          1 => 'save',
          2 => 'delete',
          3 => 'send',
        ),
        'name' => '邮件管理',
      ),
      'msg' => 
      array (
        'act' => 
        array (
          0 => 'save',
          1 => 'delete',
          2 => 'return',
        ),
        'name' => '留言反馈',
      ),
      'uc' => 
      array (
        'act' => 
        array (
          0 => 'save',
        ),
        'name' => 'UCenter',
      ),
      'sms' => 
      array (
        'act' => 
        array (
          0 => 'delete',
        ),
        'name' => '短信发送记录',
      ),
      'sms.re' => 
      array (
        'act' => 
        array (
          0 => 'delete',
        ),
        'name' => '短信回复记录',
      ),
      'pay' => 
      array (
        'act' => 
        array (
          'not' => '未安装页',
          'config' => '配置页',
          'save' => '保存配置',
          'down' => '下载新接口',
          'install' => '安装接口',
          'uninstall' => '卸载',
          'record' => '充值记录',
        ),
        'name' => '支付接口',
      ),
    ),
  ),
  'article' => 
  array (
    'name' => '文章',
    'list' => 
    array (
      'article.channel' => 
      array (
        'act' => 
        array (
          0 => 'edit',
          1 => 'save',
          2 => 'state',
          3 => 'delete',
        ),
        'name' => '频道',
      ),
      'article.topic' => 
      array (
        'act' => 
        array (
          0 => 'showarticle',
          1 => 'edit',
          2 => 'save',
          3 => 'state',
          4 => 'delete',
        ),
        'name' => '专题',
      ),
      'article' => 
      array (
        'act' => 
        array (
          0 => 'selectfolder',
          1 => 'paste_folder',
          2 => 'edit_folder',
          3 => 'save_folder',
          4 => 'del_folder',
          5 => 'delete_folder',
          6 => 'paste_article',
          7 => 'reback_article',
          8 => 'topic',
          9 => 'list',
          10 => 'dellist',
          11 => 'edit_article',
          12 => 'save_article',
          13 => 'state',
          14 => 'del_article',
          15 => 'delete_article',
        ),
        'name' => '文章',
      ),
    ),
  ),
  'meal' => 
  array (
    'name' => '订餐',
    'list' => 
    array (
      'menu' => 
      array (
        'act' => 
        array (
          0 => 'edit',
          1 => 'save',
          2 => 'delete',
          3 => 'dellist',
          4 => 'del',
          5 => 'reback',
          6 => 'state',
          7 => 'group',
          8 => 'mode',
          9 => 'sort',
        ),
        'name' => '菜品管理',
      ),
      'menu.today' => 
      array (
        'act' => 
        array (
          0 => 'add',
          1 => 'save',
        ),
        'name' => '当日菜品',
      ),
      'order' => 
      array (
        'act' => 
        array (
          'confirm' => '奖励积分',
          'delete' => '删除订单',
          'state' => '处理订单',
          'detail' => '订单明细',
        ),
        'name' => '订单管理',
      ),
      'menu.group' => 
      array (
        'act' => 
        array (
          0 => 'save',
        ),
        'name' => '菜品分组',
      ),
      'act' => 
      array (
        'act' => 
        array (
          0 => 'add',
          1 => 'save',
          2 => 'delete',
        ),
        'name' => '活动列表',
      ),
       'comment' => 
      array (
        'act' => 
        array (
          0 => 'delete',
        ),
        'name' => '订单评论',
      ),
    ),
  ),
  'report' => 
  array (
    'name' => '统计报表',
    'list' => 
    array (
      'order' => 
      array (
        'act' => 
        array (
        ),
        'name' => '销售报表',
      ),
      'user' => 
      array (
        'act' => 
        array (
        ),
        'name' => '用户统计',
      ),
      'top' => 
      array (
        'act' => 
        array (
        ),
        'name' => '排行榜',
      ),
    ),
  ),
);