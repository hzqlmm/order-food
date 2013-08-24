<?php
/* KLKKDJ订餐之单店版
 * 版本号：3.1版
 * 官网：http://www.klkkdj.com
 * 2013-03-24
 */

class ctl_pay extends cls_base {

	//支付成功跳转页
	function act_return() {
		$this->payinfo = cls_obj::get('cls_com')->pay("on_return");
		return $this->get_view(); //显示页面
	}
	//支付消息处理页
	function act_notify() {
		cls_obj::get('cls_com')->pay("on_notify");
		return '';
	}
}