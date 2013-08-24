<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
return array(
	"shop"    => array(
		"name" => "会员中心",
		"list" => array(
			"shop" => array( "act"=>array("info"=>'店铺资料',"extend"=>'订餐配置',"state"=>"运营状态") , "name"=>"店铺管理" ),
			"menu" => array( "act"=>array("edit","save","delete", "dellist" , "del" ,"reback" ,"state" , "group"=>"分组" , "mode") , "name"=>"菜品管理" ),
			"menu.today" => array( "act"=>array("add" , "save") , "name"=>"当日菜品" ),
			"order" => array( "act"=>array("confirm" , "delete") , "name"=>"订单管理" ),
			"menu.group" => array( "act"=>array("save") , "name"=>"菜品分组" ),
			"dispatch" => array( "act"=>array("add" , "save" ,"delete") , "name"=>"派送范围" ),
			"report" => array( "act"=>array() , "name"=>"报表统计" ),
		),
	),//订餐模块
);