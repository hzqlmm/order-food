<?php
/*
 *
 *
 * 2013-03-24
 */
class fun_format {
	static function path($msg_path) {
		if($msg_path=="") return "";
		$str_path=str_replace("\\","/",$msg_path);
		while(strstr($str_path,"//"))
		{
			$str_path=str_replace("//","/",$str_path);
		}
		if(substr($str_path, -1,1) == '/') $str_path = substr($str_path,0,-1);
		return $str_path;
	}
	static function tohtml($msg_cont) {
		$msg_cont = nl2br($msg_cont);
		return $msg_cont;
	}
	static function utf8_gbk($value) { 
		if(is_null($value)||empty($value)) return "";
		return mb_convert_encoding($value,"gbk","UTF-8"); 
	} 
	static function gbk_utf8($value) { 
		if(is_null($value)||empty($value)) return "";
		return iconv("gbk", "UTF-8", $value); 
	}
	static function new_stripslashes($string) {
		if(!is_array($string)) return stripslashes($string);
		foreach($string as $key => $val) $string[$key] = self::new_stripslashes($val);
		return $string;
	}

	static function json($arr) {
		$arr_item = array();
		if( fun_is::assoc($arr) ) {
			$str_cont = "{";
			$str_end = "}";
			foreach( $arr as $item => $key ) {
				if( is_array($key) ) {
					$arr_item[] = '"' . $item . '":' . self::json($key);
				}else{
					if( is_numeric($key) && strlen($key) < 11) {
						$arr_item[] = '"' . $item . '":' . $key . '';
					}else{
						$key = str_replace(chr(10) , '' , $key);
						$key = str_replace(chr(13) , '' , $key);
						$key = str_replace('"' , '' , $key);
						$arr_item[] = '"' . $item . '":"' . $key . '"';
					}
				}
			}
		} else {
			$str_cont = "[";
			$str_end = "]";
			foreach( $arr as $item ) {
				if( is_array($item) ) {
					$arr_item[] = self::json($item);
				}else{
					$item = str_replace(chr(10) , '' , $item);
					$item = str_replace(chr(13) , '' , $item);
					$arr_item[] = '"' . $item . '"';
				}
			}
		}
		$str_cont .= implode(",",$arr_item) . $str_end;
		return $str_cont;
	}
	static function toarray($cont) {
		if(gettype($cont) == "string") $cont = json_decode($cont);
		$arr = (array)$cont;
		foreach($arr as $item=>$key) {
			if(gettype($key) == 'object' ) $key = self::toarray($key);
			$arr[$item] = $key;
		}
		return $arr;
	}
	static function arr_id($arr_id) {
		$str_id="";
		if(is_array($arr_id)) {
			$arr_x=array();
			foreach($arr_id as $item=>$key) {
				$arr_x[]=intval($key);
			}
			$str_id=implode(",",$arr_x);
		} else if ($arr_id != "") {
			$arr_id = explode(",",$arr_id);
			$lng_count = count($arr_id);
			if( $lng_count > 0 ) {
				for( $i = 0 ; $i < $lng_count ; $i++ ) {
					$arr_id[$i] = intval( $arr_id[$i] );
				}
				$str_id = implode(",",$arr_id);
			}
		}
		return $str_id;
	}
	static function pwd($val , $key = '') {
		return md5($val.$key);
	}
	static function url_query($query) {
		$new_query = $query;
		if(is_array($query)) {
			if(fun_is::assoc($query)) {
				$arr_x = array();
				foreach($query as $item => $key) {
					$arr_x[]=$item . "=" . urlencode($key);
				}
				$query = $arr_x;
			}
			$new_query = implode("&" , $query);
		}
		return $new_query;
	}
	static function url_encode($key) {
		return urlencode($key);
		if(is_array($key)) {
			if(fun_is::assoc($key)) {
				foreach($key as $item => $key_next) {
					$arr_return[$item] = self::url_encode($key_next);
				}
			} else {
				foreach($key as $item) {
					$arr_return[] = self::url_encode($item);
				}
			}
		} else {
			$arr_return = urlencode($key);
		}
		return $arr_return;
	}
	static function size($size) { 
		$unit = array('B','K','M','G','T','P'); 
		return @round( $size / pow( 1024 , ( $i = floor( log ( $size , 1024 ) ) ) ) , 2 ) . ' ' . $unit[$i]; 
	} 
	static function len($str , $len , $append = '') {
		for( $i = 0 ; $i < $len ; $i++ ) {
			$temp_str = substr($str , 0 , 1);
			if(ord($temp_str) > 127) {
				$i++;
				if( $i < $len ) {
					$new_str[] = substr($str , 0 , 3);
					$str = substr($str , 3);
				}
			} else {
				$new_str[] = substr($str , 0 , 1);
				$str = substr($str,1);
			}
		}
		return join($new_str).$append;
	}

	static function js($txt) {
		$txt = str_replace(chr(10) , '' , str_replace("'" , "\'" , $txt) );
		$txt = str_replace(chr(13) , "" , $txt) ;
		$txt = "document.write('" . $txt . "');";
		return $txt;
	}
	static function xml($arr_xml,$msg_len=0){
		$str_xml = $str_space = "";
		for($i = 0 ; $i < $msg_len ; $i++) {
			$str_space .= "	";
		}
		foreach($arr_xml as $item => $key) {
			$str_xml = $str_space . "<" . $item;
			$str_property = "";
			if( isset($key["property"]) && is_array($key["property"]) ) {
				$arr_x = array();
				foreach($key["property"] as $item_n => $key_n) {
					$arr_x[] = $item_n . '="' . $key_n . '"';
				}
				$str_property = implode( " " , $arr_x );
				if( !empty($str_property) ) $str_property = " " . $str_property;
			}
			$str_xml .= $str_property;
			$str_body = "";
			if( isset($key["body"]) && is_array($key["body"]) ) {
				foreach($key["body"] as $item_body) {
					$str_body .= chr(10) . self::xml( $item_body , $msg_len + 1 );
				}
			}
			if( !empty($str_body) ) {
				$str_xml .= ">" . $str_body.chr(10);
				$str_xml .= $str_space . "</" . $item . ">";
			}else{
				$str_xml .= " />";
			}
		}
		return $str_xml;
	}
}