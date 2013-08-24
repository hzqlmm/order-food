<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */

class ctl_report_order extends mod_report_order {

	//默认浏览页
	function act_default() {
		//订单量统计
		$this->report = $this->order_num();
		$this->mode = fun_get::get("mode");
		return $this->get_view(); //显示页面
	}
}