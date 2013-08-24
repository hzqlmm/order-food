<?php
$domain = $_SERVER["HTTP_HOST"];
$dirpath = "";
return array(
	"domain"          => "http://" . $domain, //网站域名
	"dirpath"         => $dirpath, //二级目录
	"url"             => "http://" . $domain . $dirpath, //网址
	"admin_uids"      => "1",//超级管理员账号，多个用 , 号分隔
);