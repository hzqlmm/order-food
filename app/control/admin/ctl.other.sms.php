<?php
class ctl_other_sms extends mod_other_sms {

	//默认浏览页
	function act_default() {
		$this->arr_type = tab_other_sms::get_perms('type');
		$url_type = fun_get::get("url_type");
		if(!fun_is::set("url_type")) {
			foreach($this->arr_type as $item => $key) {
				$url_type = $key;break;
			}
		}
		$this->type = $url_type;

		$this->arr_list = $this->get_list($url_type);
		return $this->get_view(); //显示页面
	}

	//清除短信
	function act_delete() {
		$arr_return = $this->on_delete();
		return fun_format::json($arr_return);
	}
}
?>