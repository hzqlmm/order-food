<?php
/*
 *
 *
 * 2013-03-24
 */

class ctl_article extends mod_article {

	//浏览指定id 的文章
	function act_view() {
		$id = (int)fun_get::get("id");
		$this->article_view_byid($id);
	}
	//浏览指定id 的频道
	function act_channel() {
		$id = (int)fun_get::get("id");
		$this->channel_view_byid($id);
	}
	//浏览指定id 的目录
	function act_folder() {
		$id = (int)fun_get::get("id");
		$this->folder_view_byid($id);
	}
	function act_hits() {
		$id=fun_get::get("id");
		if(empty($id)) return '';
		cls_obj::db_w()->on_exe("update " . cls_config::DB_PRE . "article set article_hits=article_hits+1 where article_id='" . $id . "'");
		return '';
	}
}