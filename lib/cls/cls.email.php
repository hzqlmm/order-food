<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */
require_once KJ_DIR_LIB.'/components/phpmailer/class.phpmailer.php';
class cls_email{
	static $perms;
	static function get_perms($key , $val = null) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"serverinfo" => array(
					"host"=>"",
					"port"=>"25",
					"from"=>"",
					"fromname"=>"",
					"username"=>"",
					"password"=>""
				),
				"charset" => 'gb2312',
			);
		}
		$arr_return = array();
		if($val != null) self::$perms[$key] = $val;
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}

	function send($to_mail,$msg_title,$msg_content,$msg_attachment=array(),$msg_replyto="",$msg_replytoname=""){
		$arr_return=array("code"=> 0 , "msg" => "");
		$serverinfo = self::get_perms("serverinfo");
		foreach($serverinfo as $item=>$key){
			if($key==""){
				$arr_return["code"] = 500;
				$arr_return["msg"]="发送邮件服务器信息未设置，发送失败!";
				return $arr_return;
			}
		}
		if(fun_is::email($serverinfo["from"]) == false){
			$arr_return["code"] = 500;
			$arr_return["msg"]="发送邮箱格式不正确!";
			return $arr_return;
		}
		if(fun_is::email($serverinfo["from"]) == false){
			$arr_return["code"] = 500;
			$arr_return["msg"]="收件箱格式不正确!";
			return $arr_return;
		}
		if($msg_title == ""){
			$arr_return["code"] = 500;
			$arr_return["msg"]="邮件主题不能为空！";
			return $arr_return;
		}
		if($msg_content == "" && count($msg_attachment)<1){
			$arr_return["code"] = 500;
			$arr_return["msg"]="邮件内容不能为空！";
			return $arr_return;
		}
		$charset = self::get_perms("charset");
		$mail = new PHPMailer();
		$mail->CharSet = $charset;		 //设置采用中文编码
		$mail->IsSMTP();				 //设置采用SMTP方式发送邮件
		$mail->Host = $serverinfo["host"];    //设置邮件服务器的地址
		$mail->Port = $serverinfo["port"];                           //设置邮件服务器的端口，默认为25
		$mail->From     = $serverinfo["from"]; //设置发件人的邮箱地址
		$mail->FromName = $serverinfo["fromname"];                       //设置发件人的姓名
		$mail->SMTPAuth = true;                                    //设置SMTP是否需要密码验证，true表示需要
		$mail->Username=$serverinfo["username"];
		$mail->Password = $serverinfo["password"];
		$mail->Subject = $msg_title;                                 //设置邮件的标题
		$mail->AltBody = "text/html";                                // optional, comment out and test
		$mail->Body = $msg_content;                    
		$mail->IsHTML(true);                                        //设置内容是否为html类型
		//$mail->WordWrap = 50;                                 //设置每行的字符数
		if($msg_replyto!=""){
			if($msg_replytoname=="") $msg_replytoname=$msg_replyto;
			$mail->AddReplyTo($msg_replyto,$msg_replytoname);     //设置回复的收件人的地址
		}
		$mail->AddAddress($to_mail);     //设置收件的地址
		//$mail-> Mailer       =   "smtp "; 
		//$mail-> SetLanguage( "en ",   "language/ "); 
		if(count($msg_attachment)>0){
			foreach($msg_attachment as $item){
				$mail->AddAttachment($item["path"],$item["name"]);  // optional name
			}
		}
		if(!$mail->Send()) {                    //发送邮件
			$arr_return["code"] = 500;
			$arr_return["msg"]="发送失败！";
		}else{
			//file_put_contents(self::get_autopath("cache.txt"),$to_mail);
		}
		return $arr_return;
	}
}