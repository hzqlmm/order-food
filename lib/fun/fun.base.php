<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.0测试版
 * 官网：http://www.klkkdj.com
 * 2012-12-04
 */
class fun_base {
	// request 是否有值
	static function url_jump($url) {
		Header("Location:".$url); 
		exit;
	}

	/** 在已有链接基础上增加新：参数
	 *  url : 已有链接 , query : 新增参数，可为数组或字符串或带键名的数组
	 */
	static function url_add_query($url , $query) {
		$arr = parse_url($url);
		$new_url = '';
		if(isset($arr['scheme'])) $new_url .= $arr['scheme']."://";
		if(isset($arr['host'])) $new_url .= $arr['host'];
		if(isset($arr['path'])) $new_url .= $arr['path'];
		$arr_query = array( fun_format::url_query($query) );
		if(isset($arr['query'])) {
			$arr_query[] = $arr['query'];
		}
		$new_url .= "?" . implode("&" , $arr_query);
		return $new_url;
	}
	/* 提交post数据
	 * url : 提交的地址 , arr_data : 提交的数据数组 , arr_file : 上传的文件
	 */
	static function post($url, $data = array() , $arr_file = array() ) {
		if(!function_exists("curl_init")){
			return self::post_http($url,$data,$arr_file);
		}
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec($curl); // 执行操作
		if (curl_errno($curl)) {
		   return array("code" => 500 , "msg" => curl_error($curl) );
		}
		curl_close($curl); // 关闭CURL会话
	    return array("code" => 0 , "cont" => $tmpInfo);
	}

	static function post_http($url, $arr_data = array() , $arr_file = array() ) {
		$data = "";
		$boundary = "---------------------".substr(md5(rand(0,32000)), 0, 10);

		//Collect Postdata
		foreach($arr_data as $key => $val) {
			$data .= "--$boundary\r\n";
			$data .= "Content-Disposition: form-data; name=\"".$key."\"\r\n\r\n".$val."\r\n";
		}
		$data .= "--$boundary\r\n";

		//Collect Filedata
		foreach($arr_file as $key => $file)
		{
			$file_cont = file_get_contents($file['tmp_name']);
			$data .= "Content-Disposition: form-data; name=\"{$key}\"; filename=\"{$file['name']}\"\r\n";
			$data .= "Content-Type: ".$file['type']."\r\n";
			$data .= "Content-Transfer-Encoding: binary\r\n\r\n";
			$data .= $file_cont."\r\n";
			$data .= "--$boundary--\r\n";
		}

		$params = array('http' => array(
			   'method' => 'POST',
			   'header' => 'Content-Type: multipart/form-data; boundary='.$boundary,
			   'content' => $data
		));

	   $ctx = stream_context_create($params);
	   $fp = @fopen($url, 'rb', false, $ctx);

	   if (!$fp) {
		  return array("code" => 500 , "msg" => "can not open server!" );
	   }

	   $response = @stream_get_contents($fp);
	   if ($response === false) {
		  return array("code" => 500 , "msg" => "can not get message form server!" );
	   }
	   return array("code" => 0 , "cont" => $response);
	}
	static function array_remove($arr , $val) {
		if(!in_array($val , $arr)) return $arr;
		foreach($arr as $item => $key) {
			if($key == $val) unset($arr[$item]);
		}
		return $arr;
	}
}