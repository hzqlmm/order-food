<?php 
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
class cls_pic {
	/** 生成缩图
	 *  msg_source: 源图路径 , msg_w:缩小到多宽 , msg_h:缩小到多高 , msg_to:生成略图的路径
	 *  当没有指定　宽与高必须指定一个，如果有一个未指定，则按别一个的大小算出比例
	 *  msg_to 如果没有填写，则替换原图
	 */
	function on_resize($msg_source , $msg_w , $msg_h , $msg_to = '') {
		$arr_return = array("code" => 0 , "msg" => "");
		//检查源图片是否存在
		if( empty($msg_source) || !is_file( $msg_source )) return array("code" => 333 , "msg" => cls_language::get("no_source_file"));
		//未设置目标图片，则替换当前图片
		if( empty($msg_to) ) $msg_to = $msg_source;
		if( $msg_w < 1 && $msg_h < 1 )  return array("code" => 333 , "msg" => cls_language::get("pic_size_err"));
		$int_data = getimagesize($msg_source,$str_info);
		switch ($int_data[2]) {
			case 1:
				if( !function_exists("imagecreatefromgif") ) {
					return array("code" => 302 , "msg" => cls_language::get("php_pic_ext_no"));
				}
				$im = imagecreatefromgif( $msg_source );
				break;
			case 2:
				if( !function_exists("imagecreatefromjpeg") ) {
					return array("code" => 302 , "msg" => cls_language::get("php_pic_ext_no"));
				}
				$im = imagecreatefromjpeg($msg_source);
				break;
			case 3:
				$im = imagecreatefrompng($msg_source);
				break;
			default:
				return array("code" => 341 , "msg" => cls_language::get("pic_ext_err"));
		}
		$int_w = imagesx($im);
		$int_h = imagesy($im);
		//如果没有设置高，则按比例缩放
		if($msg_h<1) $msg_h = ($msg_w * $int_h) / $int_w;
		//如果没有设置宽，则按比例缩放
		if($msg_w<1) $msg_w = ($int_w * $msg_h) / $int_h;
		//生成大小不能超过原图大小
		if($int_w<$msg_w || $int_h < $msg_h) return array("code" => 333 , "msg" => cls_language::get("pic_size_over"));
		if(function_exists("imagecreatetruecolor")) {
			$ni = imagecreatetruecolor($msg_w,$msg_h);
			if($ni) {
				imagecopyresampled($ni , $im , 0 , 0 , 0 , 0 , $msg_w , $msg_h , $int_w , $int_h);
			}else{
				$ni = imagecreate($msg_w , $msg_h);
				imagecopyresized($ni , $im , 0 , 0 , 0 , $msg_w , $msg_h , $int_w , $int_h);
			}
		} else {
			$ni = imagecreate( $msg_w , $msg_h );
			imagecopyresized($ni , $im , 0 , 0 , 0 , 0 , $msg_w , $msg_h , $int_w , $int_h);
		}
		if( function_exists('imagejpeg') ) {
			imagejpeg( $ni , $msg_to );
		} else {
			imagepng( $ni , $msg_to );
		}
		imagedestroy( $ni );
		imagedestroy( $im );
		return $arr_return;
	}

	/** 获取水印位置
	 *  返回健名：x , y 的数组
	 */
	function get_watemark_pos( $msg_val , $msg_w1 , $msg_h1 , $msg_w2 , $msg_h2 ) {
		switch($msg_val) {
			case 0:
				$int_x = rand(0 , ($msg_w1 - $msg_w2));
				$int_y = rand(0 , ($msg_h1 - $msg_h2));
				break;
			case 1:
				$int_x = 5;
				$int_y = 5;
				break;
			case 2:
				$int_x = ( $msg_w1 - $msg_w2 ) / 2;
				$int_y = 0;
				break;
			case 3:
				$int_x = $msg_w1 - $msg_w2;
				$int_y = 0;
				break;
			case 4:
				$int_x = 0;
				$int_y = $msg_h1/ 2;
				break;
			case 5:
				$int_x = ($msg_w1 - $msg_w2) / 2;
				$int_y = $msg_h1 / 2;
				break;
			case 6:
				$int_x = $msg_w1 - $msg_w2;
				$int_y = $msg_h1 / 2;
				break;
			case 7:
				$int_x = 0;
				$int_y = $msg_h1 - $msg_h2;
				break;
			case 8:
				$int_x = ( $msg_w1 - $msg_w2 ) / 2;
				$int_y = $msg_h1 - $msg_h2;
				break;
			case 9:
				$int_x = $msg_w1 - $msg_w2;
				$int_y = $msg_h1 - $msg_h2;
				break;
			default:
				$int_x = rand(0 , ($msg_w1 - $msg_w2));
				$int_y = rand(0 , ($msg_h1 - $msg_h2));
				break;
		}
		$arr_val = array(
			"x" => $int_x ,
			"y" => $int_y
		);
		return $arr_val;
	}
	/** 添加文字水印
	 *
	 */
	function on_font_watermark($msg_source , $msg_text , $msg_pos = 0 , $msg_font_size = 18 , $msg_color = '#ff0000'  ,$msg_quality = 100 , $msg_target = '' , $font_path = '')
	{
		$arr_return = array("code" => 0 , "msg" => "");
		//检查源图片是否存在
		if( empty($msg_source) || !is_file( $msg_source )) return array("code" => 333 , "msg" => cls_language::get("no_source_file"));
		if( empty($msg_text) ) return array("code" => 333 , "msg" => cls_language::get("watermark_no_font"));
		if($msg_text=="") return array("code" => 500 , "msg" => "水印文字不能为空！");
		if(! fun_is::pic($msg_source)) return array("code" => 333 , "msg" => cls_language::get("watermark_no_pic") . $msg_source);
		$pic_info = getimagesize($msg_source);
		if( !in_array($pic_info[2] , array(1,2,3) ) ) return array("code" => 333 , "msg" => cls_language::get("pic_ext_err"));
		($msg_target=="")? $str_target = $msg_source : $str_target = $msg_target;
		//没设置目标图片，则在原图片上加水印
		if( empty($msg_target) ) $msg_target = $msg_target;
		$int_w = $pic_info[0];
		$int_h = $pic_info[1];
		if(empty($font_path)) return  array("code" => 500 , "msg" => "字体文件不能为空！");
		$charset = strtolower(str_replace("_" , "" , cls_config::DB_CHARSET));
		$font_text = ( $charset != 'utf8' ) ? iconv( cls_config::DB_CHARSET , 'utf-8' , $msg_text) : $msg_text;
		if(!file_exists($font_path)) return array('code'=>500 , 'msg'=>"字体文件不存在");
		$arr_font = imagettfbbox( $msg_font_size , 0 , $font_path , $font_text );
		$int_w1 = $arr_font[2] - $arr_font[6]+20;
		$int_h1 = $arr_font[3] - $arr_font[7]+2;

		$arr_xy = self::get_watemark_pos($msg_pos , $int_w , $int_h , $int_w1 , $int_h1);
		$int_x = $arr_xy["x"];
		$int_y = $arr_xy["y"];
		if($int_w < $int_w1 || $int_h < $int_h1 )  return array("code"=>500 , "msg" => "目标图片小于水印图片，无法加水印！");
		$int_r = hexdec( substr($msg_color , 1 , 2) );
		$int_g = hexdec( substr($msg_color , 3 , 2) );
		$int_b = hexdec( substr($msg_color , 5) );

		$pic_themp = imagecreate($int_w1 , $int_h1);
		$font_bg = imagecolorallocate($pic_themp , 255 , 255 , 255);
		$font_color = imagecolorallocate($pic_themp , $int_r , $int_g , $int_b);
		imagettftext($pic_themp , $msg_font_size , 0 , 10 , $msg_font_size+2 , $font_color , $font_path , $font_text);

		switch($pic_info[2]) {
			case 1 :
				$im = imagecreatefromgif( $msg_source );
				imagecopymerge( $im , $pic_themp , $int_x , $int_y , 0 , 0 , $int_w1 , $int_h1 , 50 );
				imagegif( $im , $str_target );
				break;
			case 2 :
				$im = imagecreatefromjpeg($msg_source);
				imagecopymerge($im , $pic_themp , $int_x , $int_y , 0 , 0 , $int_w1 , $int_h1 , 50);
				imagejpeg($im , $str_target , $msg_quality);
				break;
			case 3 :
				$im = imagecreatefrompng($msg_source);
				imagecopymerge($im , $pic_themp , $int_x , $int_y , 0 , 0 , $int_w1 , $int_h1 , 50);
				imagepng($im , $str_target);
				break;
		}
		return $arr_return;
	}
	function on_pic_watermark( $msg_pic , $msg_im , $msg_pos = 0 , $msg_quality = 100 , $msg_target = "" ) {
		$arr_return = array("code" => 0 , "msg" => "");
		if(!is_file($msg_pic))  return array("code" => 333 , "msg" => cls_language::get("no_source_file"));
		if(!is_file($msg_im))  return array("code" => 333 , "msg" => cls_language::get("watermark_none_pic"));
		if(!fun_is::pic( $msg_pic )) return array("code" => 333 , "msg" => cls_language::get("watermark_no_pic"));
		if(!fun_is::pic( $msg_im )) return array("code" => 333 , "msg" => cls_language::get("watermark_no_pic"));
		//未指定目录图片，则默认替换当前图片
		$str_target = (empty($msg_target))? $msg_pic : $msg_target;
		$pic_info = getimagesize($msg_pic);
		$int_w = $pic_info[0];
		$int_h = $pic_info[1];
		$im_info = getimagesize($msg_im);
		$int_w1 = $im_info[0];
		$int_h1 = $im_info[1];
		if( $int_w < $int_w1 || $int_h < $int_h1 ) return array("code" => 333 , "msg" => cls_language::get("watermark_picsize_over"));
		if( !in_array($pic_info[2] , array(1,2,3) ) ) return array("code" => 333 , "msg" => cls_language::get("pic_ext_err"));
		switch($im_info[2])
		{
			case 1 :
				$obj_im = imagecreatefromgif($msg_im);
				break;
			case 2 :
				$obj_im = imagecreatefromjpeg($msg_im);
				break;
			case 3 :
				$obj_im = imagecreatefrompng($msg_im);
				break;
			default :
				return "图片格式不支持！";
		}


		$arr_xy=self::get_watemark_pos($msg_pos,$int_w,$int_h,$int_w1,$int_h1);
		$int_x = $arr_xy["x"];
		$int_y = $arr_xy["y"];
		switch( $pic_info[2] ) {
			case 1 :
				$obj_pic = imagecreatefromgif($msg_pic);
				imagecopymerge($obj_pic , $obj_im , $int_x , $int_y , 0 , 0 , $int_w1 , $int_h1 , 100);
				imagegif($obj_pic , $str_target);
				break;
			case 2 :
				$obj_pic = imagecreatefromjpeg($msg_pic);
				imagecopymerge($obj_pic,$obj_im,$int_x,$int_y,0,0,$int_w1,$int_h1,100);
				imagejpeg($obj_pic, $str_target, $msg_quality);
				break;
			case 3 :
				$obj_pic = imagecreatefrompng($msg_pic);
				imagecopymerge($obj_pic,$obj_im,$int_x,$int_y,0,0,$int_w1,$int_h1,100);
				imagepng($obj_pic, $str_target);
				break;
		}
		return $arr_return;
	}
}