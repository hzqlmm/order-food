<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_article extends mod_article {

	//列表模式
	function act_list() {
		//分页列表
		$this->arr_list = $this->get_pagelist( array("article_isdel=0") );
		//取专题
		$this->arr_topic = $this->get_topic_list();
		$this->channel_mode = $this->get_channel_mode(fun_get::get("url_channel_id"));
		return $this->get_view('default'); //显示页面
	}
	//回收站数据
	function act_dellist() {
		$folder_id = (int)fun_get::get("url_folder_id");
		$arr_where = array(
			"article_isdel=1",//回收站数据
			"article_isdel_from=0",//非级联删除
		);
		$this->channel_mode = $this->get_channel_mode(fun_get::get("url_channel_id"));
		//文章分页列表
		$this->arr_list = $this->get_pagelist( $arr_where );
		//目录列表
		$this->arr_dirlist = $this->get_dirlist(-1 , 1 , array("folder_isdel_from=0") , $this->channel_mode);
		//频道列表
		$this->arr_channel = $this->get_channel_list();
		//当前路径
		$str_path = $this->get_folder_path($folder_id);
		$this->folder_path = $str_path;
		return $this->get_view('default'); //显示页面
	}
	//目录模式
	function act_default() {
		//是否为指定用户组类型频道
		$channel_user_type = $this->get_channel_user_type(fun_get::get("url_channel_id"));
		$article_about_id = fun_get::get("url_about_id");
		if(!empty($channel_user_type) && empty($article_about_id) ) {
			return $this->act_default_user_type(fun_get::get("url_channel_id") , $channel_user_type);
		}
		$this->about_name = (empty($channel_user_type)) ? '' : $this->get_about_name(fun_get::get("url_channel_id") , $article_about_id);
		$folder_id = (int)fun_get::get("url_folder_id");
		$arr_where = array(
			"article_isdel=0",//非回收站数据
			"article_folder_id=" . $folder_id,//所属目录id
		);
		$this->channel_mode = $this->get_channel_mode(fun_get::get("url_channel_id"));
		//文章分页列表
		$this->arr_list = $this->get_pagelist( $arr_where );
		//目录列表
		$this->arr_dirlist = $this->get_dirlist($folder_id , 0 , array() , $this->channel_mode);
		//频道列表
		$this->arr_channel = $this->get_channel_list();
		//取专题
		$this->arr_topic = $this->get_topic_list();
		//当前路径
		$str_path = $this->get_folder_path($folder_id);
		$this->folder_path = $str_path;
		return $this->get_view(); //显示页面
	}
	function act_default_user_type($channel_id , $type) {
		$this->arr_list = $this->get_user_type_list($channel_id , $type);
		return $this->get_view('user.type'); //显示页面
	}
	//编辑 新增 页面 ,有id时为编辑
	function act_edit_article() {
		$this->editinfo = $this->get_editinfo( fun_get::get('id') );
		$this->arr_state = tab_article::get_perms("state");
		$this->arr_attribute = cls_config::get("attribute" , "article" , array());
		$this->channel_user_type = $this->get_channel_user_type($this->editinfo["article_channel_id"]);
		//取频道列表
		$this->arr_channel = $this->get_channel_list($this->editinfo["article_channel_id"]);
		//取目录列表
		$this->select_folder = $this->get_folder_select("article_folder_id" , $this->editinfo["article_folder_id"] , '', $this->editinfo["article_channel_id"]);
		//取专题
		$this->arr_topic = $this->get_topic_list($this->editinfo["article_topic_id"]);
		$this->channel_mode = $this->get_channel_mode($this->editinfo["article_channel_id"]);
		return $this->get_view();
	}
	//目录 编辑 新增 页面 ,有id时为编辑
	function act_edit_folder() {
		$id = fun_get::get('id');
		$this->editinfo = $this->get_editinfo_folder( $id );
		//取目录列表
		$this->select_folder = $this->get_folder_select("folder_pid" , $this->editinfo["folder_pid"] , $id , $this->editinfo["folder_channel_id"]);
		$this->channel_mode = $this->get_channel_mode($this->editinfo["folder_channel_id"]);
		return $this->get_view();
	}
	//保存操作,返回josn数据
	function act_save_article() {
		$arr_return = $this->on_save_article();
		return fun_format::json($arr_return);
	}
	//保存操作,返回josn数据
	function act_save_folder() {
		$arr_return = $this->on_save_folder();
		return fun_format::json($arr_return);
	}

	//设置状态
	function act_state() {
		$arr_return = $this->on_state();
		return fun_format::json($arr_return);
	}
	//设置专题
	function act_topic() {
		$arr_return = $this->on_topic();
		return fun_format::json($arr_return);
	}
	//从回收站回收文章操作,返回josn数据
	function act_reback_article() {
		$arr_return = $this->on_del_article(0);
		return fun_format::json($arr_return);
	}
	//从回收站回收目录操作,返回josn数据
	function act_reback_folder() {
		$arr_return = $this->on_del_folder(0);
		return fun_format::json($arr_return);
	}
	//从回收站回收目录操作,返回josn数据
	function act_reback_article_folder() {
		$arr_id = fun_get::get("selid");
		$arr_id2 = fun_get::get("selid2");
		if(!empty($arr_id)) $arr_return = $this->on_del_article(0);
		if(!empty($arr_id2)) $arr_return = $this->on_del_folder(0);
		return fun_format::json($arr_return);
	}

	//删除文章到回收站操作,返回josn数据
	function act_del_article() {
		$arr_return = $this->on_del_article();
		return fun_format::json($arr_return);
	}
	//删除文章,返回josn数据
	function act_delete_article() {
		$arr_return = $this->on_delete_article();
		return fun_format::json($arr_return);
	}
	//删除目录到回收站操作,返回josn数据
	function act_del_folder() {
		$arr_return = $this->on_del_folder();
		return fun_format::json($arr_return);
	}
	//删除目录,返回josn数据
	function act_delete_folder() {
		$arr_return = $this->on_delete_folder();
		return fun_format::json($arr_return);
	}
	//删除目录与文章,返回josn数据
	function act_delete_article_folder() {
		$arr_return = array("code"=>0,"msg" => cls_language::get("delete_ok") );
		$arr_id = fun_get::get("selid");
		$arr_id2 = fun_get::get("selid2");
		if(!empty($arr_id)) $arr_return = $this->on_delete_article();
		if(!empty($arr_id2)) $arr_return = $this->on_delete_folder();
		return fun_format::json($arr_return);
	}
	//删除目录与文章到回收站,返回josn数据
	function act_del_article_folder() {
		$arr_return = array("code"=>0,"msg" => cls_language::get("del_ok") );
		$arr_id = fun_get::get("selid");
		$arr_id2 = fun_get::get("selid2");
		if(!empty($arr_id)) $arr_return = $this->on_del_article();
		if(!empty($arr_id2)) $arr_return = $this->on_del_folder();
		return fun_format::json($arr_return);
	}
	//粘贴目录
	function act_paste_folder() {
		$arr_return = $this->on_paste_folder();
		return fun_format::json($arr_return);
	}
	//粘贴文章
	function act_paste_article() {
		$arr_return = $this->on_paste_article();
		return fun_format::json($arr_return);
	}
	//粘贴文章或目录
	function act_paste_article_folder() {
		$arr_return = $this->on_paste_article();
		if($arr_return["code"] == 0) {
			$arr_return = $this->on_paste_folder();
		}
		return fun_format::json($arr_return);
	}
	//切换频道，获取目录列表 , 返回字符串内容
	function act_selectfolder() {
		$arr_return["code"] = 0;
		$arr_return["cont"] = $this->get_folder_select("article_folder_id" , 0 , '', (int)fun_get::get("cid") );
		return fun_format::json($arr_return);
	}
}