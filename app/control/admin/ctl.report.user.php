<?php
/*
 *
 *
 * 2013-03-24
 */
class ctl_report_user extends mod_report_user {

	//默认浏览页
	function act_default() {
		//订单量统计
		$this->report = $this->regin_count();
		$this->mode = fun_get::get("mode");
		return $this->get_view(); //显示页面
	}
}