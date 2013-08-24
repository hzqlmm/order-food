<?php
require_once dirname(__FILE__) . "/phpmailer/class.phpmailer.php";
class com_email{
	static $perms;
	static function get_perms($key , $val = null) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"serverinfo" => array(
					"host"=> cls_config::get('host','email'),
					"port"=> cls_config::get('port','email'),
					"from"=> cls_config::get('from','email'),
					"fromname"=> cls_config::get('fromname','email'),
					"username"=> cls_config::get('username','email'),
					"password"=> cls_config::get('password','email')
				),
				"charset" => 'gb2312',
			);
		}
		$arr_return = array();
		if($val != null) self::$perms[$key] = $val;
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}
	/* 发送邮件
	 * to_mail,title,content,attachment=array(),replyto="",replytoname=""
	 */
	function send($arr_fields){
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
		if(fun_is::email($arr_fields["to_mail"]) == false){
			$arr_return["code"] = 500;
			$arr_return["msg"]="收件箱格式不正确!";
			return $arr_return;
		}
		if(!isset($arr_fields['title']) && empty($arr_fields['title'])){
			$arr_return["code"] = 500;
			$arr_return["msg"]="邮件主题不能为空！";
			return $arr_return;
		}
		if(!isset($arr_fields['content']) && empty($arr_fields['content'])){
			$arr_return["code"] = 500;
			$arr_return["msg"]="邮件内容不能为空！";
			return $arr_return;
		}
		if(!isset($arr_fields['attachment']) ) $arr_fields['attachment'] = array();
		//保存邮件
		if(isset($arr_fields['save']) && $arr_fields['save']) {
			$serverinfo["type"] = 0;
			$arr_save_email = array(
				'email_title' => $arr_fields['title'],
				'email_cont' => $arr_fields['content'],
				'email_account_mode' => 0,
				'email_to' => $arr_fields['to_mail'],
				'email_from' => $serverinfo["from"],
				'email_attachment' => $arr_fields['attachment'],
				'email_num' => '1',
				'email_serverinfo' => $serverinfo,
				'email_type' => '0',
			);
		}
		//字符编码转换
		$charset = strtolower(cls_config::DB_CHARSET);
		if(!in_array($charset , array('gbk' , 'gb2312'))) {
			$serverinfo['fromname'] = fun_format::utf8_gbk($serverinfo['fromname']);
			$arr_fields['title'] = fun_format::utf8_gbk($arr_fields['title']);
			$arr_fields['content'] = fun_format::utf8_gbk($arr_fields['content']);
			if(isset($arr_fields['replytoname'])) $arr_fields['replytoname'] = fun_format::utf8_gbk($arr_fields['replytoname']);
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
		$mail->Subject = $arr_fields['title'];                                 //设置邮件的标题
		$mail->AltBody = "text/html";                                // optional, comment out and test
		$mail->Body = $arr_fields['content'];                    
		$mail->IsHTML(true);                                        //设置内容是否为html类型
		//$mail->WordWrap = 50;                                 //设置每行的字符数
		if(isset($arr_fields['replyto']) && !empty($arr_fields['replyto']) ){
			if($arr_fields['replytoname'] == "") $arr_fields['replytoname']=$arr_fields['replyto'];
			$mail->AddReplyTo($arr_fields['replyto'],$arr_fields['replytoname']);     //设置回复的收件人的地址
		}
		$mail->AddAddress($arr_fields['to_mail']);     //设置收件的地址
		//$mail-> Mailer       =   "smtp "; 
		//$mail-> SetLanguage( "en ",   "language/ "); 
		if(count($arr_fields['attachment'])>0){
			foreach($arr_fields['attachment'] as $item){
				$mail->AddAttachment($item["path"],$item["name"]);  // optional name
			}
		}
		if(!$mail->Send()) {                    //发送邮件
			$arr_return["code"] = 500;
			$arr_return["msg"]="发送失败！";
		}else{
			//保存邮件
			if(isset($arr_fields['save']) && $arr_fields['save']) {
				$arr = tab_other_email::on_save($arr_save_email);
			}
		}
		return $arr_return;
	}
}
?>