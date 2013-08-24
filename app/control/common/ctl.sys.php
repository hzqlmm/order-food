<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */

class ctl_sys extends mod_sys {

	//用户列表样式一
	function act_user_dialog1() {
		//是否为管理员
		if(!cls_obj::get("cls_user")->is_admin()) {
			cls_error::on_error("no_limit");
		}
		$this->arr_list = $this->get_user_dialog1();
		return $this->get_view(); //显示页面
	}
	//默认浏览页
	function act_login() {
		$jump_url = fun_get::get("jump_url");    //获取跳转地址
		if( empty($jump_url) && isset($_SERVER["HTTP_REFERER"]) ) $jump_url=$_SERVER["HTTP_REFERER"];
		$this->jump_fromurl = $jump_url;
		return $this->get_view(); //显示页面
	}
	//验证登录
	function act_login_verify() {
		$arr_return = $this->on_login_verify();
		return fun_format::json($arr_return);
	}
	//退出登录
	function act_login_out() {
		cls_obj::get("cls_user")->on_loginout(); //清除登录信息
		$jump_url = fun_get::get("jump_url");    //获取跳转地址
		if( empty($jump_url) && isset($_SERVER["HTTP_REFERER"]) ) $jump_url=$_SERVER["HTTP_REFERER"];
		fun_base::url_jump($jump_url);
	}
	//输出验证码
	function act_verifycode() {
		$name = fun_get::get("name");
		cls_verifycode::get_codepic($name);
		exit;
	}
	//输出缓存
	function act_cache_words() {
		$type = fun_get::get("cachetype");
		$id = fun_get::get("cacheid");
		$val = fun_get::get("cacheval");
		$arr_list = fun_kj::get_cache_words($type , $val);
		$arr_return = array(
			"cacheid" => $id , 
			"list" => $arr_list
		);
		return fun_format::json($arr_return);
	}
	//取站点配置信息
	function act_web_config() {
		$rule_uname = fun_get::rule_uname();
		$rule_pwd = fun_get::rule_pwd();
		$web_css = KJ_WEBCSS_PATH;
		if(substr($web_css , 0, 5) != "http:") $web_css = cls_config::get("dirpath" , "base") . $web_css;
		$var = "var web_config = {";
		$var .= "domain : '" . cls_config::get("domain" , "base") . "',";
		$var .= "dirpath : '" . cls_config::get("dirpath" , "base") . "',";
		$var .= "baseurl : '" . cls_config::get("url" , "base") . "',";
		$var .= "basecss : '" . $web_css . "',";
		$var .= "cookie_pre : '" . cls_config::COOKIE_PRE . "',";
		$var .= "rule_uname : '" . $rule_uname['js'] . "',";
		$var .= "rule_uname_tips : '" . $rule_uname['tips'] . "',";
		$var .= "rule_pwd : '" . $rule_pwd['js'] . "',";
		$var .= "rule_pwd_tips : '" . $rule_pwd['tips'] . "',";
		$var .= "uname:'" . cls_obj::get("cls_user")->uname . "'";
		$var .= "};";
		$var .= "if(window.screen.width<1200){";
		$var .= "document.write('<style>.main{width:1000px;margin:auto}.main_w1{width:998px}.main_w2{width:750px}.main_w3{width:750px}.main_w4{width:658px}.main_w5{width:857px}.main_w6{width:740px}.main_w7{width:80px;margin:auto}.main_w8{width:418px}</style>');}";
		return $var;
	}
	//注册验证码
	function act_verify_reg() {
		$arr_return = $this->on_verify_reg();
		return fun_format::json($arr_return);
	}
}