<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */

class ctl_other extends mod_other {
	//以js的方式输出广告内容
	function act_ads_js() {
		$id = (int)fun_get::get("id");
		$return = fun_kj::get_ads($id);
		return fun_format::js($return); //显示页面
	}

	//上传页
	function act_upload() {
		//是否登录
		if(!cls_obj::get("cls_user")->is_login()) {
			cls_error::on_error("no_login");
		}
		$this->uploadinfo = array();
		if(fun_get::get("subok") != '') {
			$this->uploadinfo = $this->on_upload();
		}
		$this->objid = fun_get::get('objid');
		$this->objpic = fun_get::get('objpic');
		if(fun_get::get("callback") == "" ) $this->get_callback = "upload_callback";
		return $this->get_view(); //显示页面
	}
	//附件页
	function act_attatch() {
		//是否登录
		if(!cls_obj::get("cls_user")->is_login()) {
			cls_error::on_error("no_login");
		}
		$attatch_channel = fun_get::get("channel");
		if(empty($attatch_channel)) {
			$attatch_channel = cls_session::get_cookie("d_a_c");
		} else {
			cls_session::set_cookie("d_a_c" , $attatch_channel);//将当前选择保有到cookie
		}
		//只有管理员才有权力从服务器选择
		if($attatch_channel == "server" && cls_obj::get("cls_user")->is_admin() ) {
			//选择服务器
			$dirpath = fun_get::get("url_dirpath");
			if(empty($dirpath)) {
				$dirpath = cls_session::get_cookie("d_a_p");
			} else {
				cls_session::set_cookie("d_a_p" , $dirpath);//将当前选择保有到cookie
			}
			$this->arr_list = $this->get_server_upload(KJ_DIR_UPLOAD , $dirpath);
		} else {
			//选择空间
			$this->arr_list = $this->get_attatch();
		}

		$this->channel = $attatch_channel;
		return $this->get_view(); //显示页面
	}
	//模板
	function act_templates() {
		//是否为管理员
		if(!cls_obj::get("cls_user")->is_admin()) {
			cls_error::on_error("no_limit");
		}
		$dirpath = fun_get::get("url_dirpath");
		if(empty($dirpath)) {
			$dirpath = cls_session::get_cookie("d_t_p");
		} else {
			cls_session::set_cookie("d_t_p" , $dirpath);//将当前选择保有到cookie
		}
		//选择服务器
		$this->arr_list = $this->get_server_upload(KJ_DIR_APP . "/view" , $dirpath);
		return $this->get_view(); //显示页面
	}
}