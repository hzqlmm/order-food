<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class cls_zip {
	//创建压缩文件
	function create($files = array(), $destination = '', $overwrite = false) {
		if (file_exists($destination) && !$overwrite) {
			return false;
		}
		$valid_files = array();
		if (is_array($files)) {
			foreach ($files as $file) {
				if (file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		} else {
			$valid_files[] = $files;
		}
		if (count($valid_files)) {
			$zip = new ZipArchive();
			if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			foreach ($valid_files as $file) {
				$zip->addFile($file, $file);
			}
			$zip->close();
			return file_exists($destination);
		} else {
			return false;
		}
	}
	//解压文件
	function unzip($file, $destination = '') {
		if(empty($destination)) $destination = substr($file , 0 , -4);
		$zip = new ZipArchive();
		if ($zip->open($file) !== TRUE) {
			return array("code"=>500 , "msg"=>'Could not open archive');
		}
		$zip->extractTo($destination);
		$zip->close();
		return array("code"=>0 , "path"=>$destination);
	}
}