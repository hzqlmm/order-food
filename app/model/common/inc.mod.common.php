<?php
/*
 *
 *
 * 2013-03-24
 */
class inc_mod_common extends cls_base{

	/**
	 * admin 目录 初始类，启动 : 登录检查，权限检查
	 */
	function __construct($arr_v) {
		parent::__construct($arr_v);
	}

	/**
	 * 统一获取分页样式
	 * arr_info : 数组 , 值为 : 
	 * 返回 : 分页html字符串
	 */
	/**
	 * admin 统一获取分页样式
	 * arr_info : 数组 , 值为 : 
	 * 返回 : 分页html字符串
	 */
	function get_pagebtns( $arr_info ) {
		if($arr_info['total'] < 1) return '';
		$prepg = $arr_info['page']-1;
		$nextpg = $arr_info['page']+1;//$page == $pages ? 0 : ($page+1);
		$str_left="";
		$str_right="";
		$pagenav ='<li class="info">共:<font color="#ff6600">'.$arr_info['total'].'</font> 条&nbsp;页 '.$arr_info['page'].'/'.$arr_info['pages'].'&nbsp;&nbsp;页大小<input type="text" name="url_pagesize" value="' . $arr_info['pagesize'] . '" style="width:20px;color:#888888" onkeyup="kj.page.size(event,\''.$this->app_module . "." . $this->app . "." . $this->app_act . '\',\''.$this->app_dir.'\');" id="id_page_size"></li>';
		$str_x="";
		if($arr_info["pages"] > 10) {
			if($arr_info['page']>5){
				$lng_pre=$arr_info['page']-5;
				$lng_next=$arr_info['page']+5;
				$str_left="<li><a href='javascript:kj.page.go(1);'>首页</a></li>";
				$str_right="<li><a href='javascript:kj.page.go(".$arr_info['pages'].");'>尾页</a></li>";
			}else{
				$lng_pre=1;
				$lng_next=10;
				$str_right="<li><a href='javascript:kj.page.go(".$arr_info['pages'].");'>尾页</a></li>";
			}
		}else{
			$lng_pre=1;
			$lng_next=$arr_info['pages'];
		}
		if($lng_next>=$arr_info['pages']){
			$lng_next=$arr_info['pages'];
			$str_right="";
		}
		for($i=$lng_pre;$i<=$lng_next;$i++){
			$str_sel="";
			if($i==$arr_info['page']) $str_sel=" class='x_sel'";
			$str_x.="<li".$str_sel."><a href='javascript:kj.page.go(".$i.");'>[".$i."]</a></li>";
		}
		$pagenav.=$str_left.$str_x.$str_right."<li class='x_go'><input type='text' name='go_page' id='id_go_page' value='' class='pTxt1 x_txt' onkeyup='kj.page.page_keyup(event);'>&nbsp;&nbsp;<a href=\"javascript:kj.page.go(kj.obj('#id_go_page').value);\">跳转</a></li>";
		return $pagenav;
	}

}