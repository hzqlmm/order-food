<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class cls_excel {
	function save_excel($name,$arr) {
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=".$name);  //在此设置导出数据文件名称
		header("content-Type=text/html;charset='gbk'");
		$str_cont="";
		foreach($arr as $item) {
			$str_cont .= implode("\t",$item)."\t\n";
		}
		if(cls_config::DB_CHARSET == "utf8" ) $str_cont = fun_format::utf8_gbk($str_cont);
		echo $str_cont;
		exit;
	}
}