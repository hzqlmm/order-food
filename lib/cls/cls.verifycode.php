<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 * 验证码处理类
 */
class cls_verifycode {
	/* 此验证码是由：背景图加各个字符图合成的
	 * val : 验证字符 , index : 位置,从零开始 , line : 是否加干扰线 , obj_pic : 合成原图
	 */
	function join_code($val , $index , $line = '' , $obj_pic = '') {
		$msg_pic = KJ_DIR_ROOT . "/plus/verify_code/style_1/bg1.gif";
		$msg_im = KJ_DIR_ROOT . "/plus/verify_code/style_1/".$val.".gif";
		if(empty($obj_pic)) {
			$obj_pic = imagecreatefromgif($msg_pic);
			imagealphablending($obj_pic, false); 
		}
		//加干扰线 
		if(!empty($line)) {
			$line = rand(1,3);
			$obj_im = imagecreatefromgif( KJ_DIR_ROOT . "/webcss/common/verify_code/style_1/line" . $line . ".gif" );
			imagecopymerge($obj_pic , $obj_im , 0 , 0 , 0 , 0 , 130 , 50 , 100);
			imagedestroy( $obj_im );
		}
		$obj_im = imagecreatefromgif($msg_im);
		$x = $index * 30 + 10;
		$y = rand(5,25);
		imagecopymerge($obj_pic , $obj_im , $x , $y , 0 , 0 , 25 , 25 , 100);
		imagedestroy( $obj_im );
		return $obj_pic;
	}
	/* 输出验证码图片
	 * name : 当需要多个验证码时，可以用 name 来命名区分
	 */
	function get_codepic( $name = '' ) {
		$str = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$len = strlen($str)-1;
		$arr_code = array();
		for($i=0 ; $i<4; $i++) {
			$rnd = rand(0 , $len);
			$num = rand(1,5);
			$code = substr($str , $rnd , 1);
			$arr_code[] = $code;
			$a[] = $code . $num;
		}
		$other_line = (int)cls_config::get( "verify_line" , 'other' );
		$obj_pic = self::join_code($a[0] , 0 , $other_line);
		$obj_pic = self::join_code($a[1] , 1 , $other_line , $obj_pic);
		$obj_pic = self::join_code($a[2] , 2 , $other_line , $obj_pic);
		$obj_pic = self::join_code($a[3] , 3 , $other_line , $obj_pic);
		imagegif($obj_pic);
		imagedestroy( $obj_pic );
		$code = implode("" , $arr_code);
		cls_obj::get("cls_session")->set("verify_code_" . $name , $code);
		return $code;
	}
	/* 检查验证码
	 * val : 需要验证的值 , name : 验证名称
	 */
	function on_verify($val , $name = '') {
		$code = cls_obj::get("cls_session")->get("verify_code_" . $name);
		if(strtolower($val) == strtolower($code) && !empty($code) ){
			//清空验证码
			cls_obj::get("cls_session")->destroy("verify_code_" . $name);
			return true;
		}
		return false;
	}
}