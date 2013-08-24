<?php
/**
 * 
 */
class tab_other_ads {
	static $perms;

	//获取表配置参数
	static function get_perms($key) {
		if( empty(self::$perms) ) {
			self::$perms = array(
				"state" => array( cls_language::get("normal") => 1 , cls_language::get("wait_check") => 0) ,
				"type"  => array( cls_language::get("pic") => "pic" , cls_language::get("flash") => "flash" , cls_language::get("txt") => "txt" , cls_language::get("slide1" , "other") => "slide1"),
			);
		}
		$arr_return = array();
		if(isset(self::$perms[$key])) $arr_return = self::$perms[$key];
		return $arr_return;
	}

	/* 保存操作
	 * arr_fields : 为字段数据，默认如果包函 id，则为修改，否则为插入
	 * where : 默认为空，用于有时候条件修改
	 */
	static function on_save($arr_fields , $where = '') {
		$arr_return = array("code"=>0,"id"=>0,"msg"=>"");
		if(isset($arr_fields['ads_id'])) {
			$arr_fields['id'] = $arr_fields['ads_id'];
			unset($arr_fields['ads_id']);
		}
		if( isset($arr_fields['id']) ) {
			$arr_return['id'] = (int)$arr_fields['id'];
			unset($arr_fields['id']);
			if( $arr_return['id'] > 0 ) { //大于零，为修改状态
				if( empty($where) ){
					$where = " ads_id='" . $arr_return['id'] . "'";
				} else {
					$where = "(" . $where . ") and ads_id='" . $arr_return['id'] . "'";
				}
			}
		}
		if(isset($arr_fields["ads_starttime"]) && fun_is::isdate($arr_fields["ads_starttime"])) $arr_fields["ads_starttime"] = strtotime($arr_fields["ads_starttime"]);
		if(isset($arr_fields["ads_endtime"]) && fun_is::isdate($arr_fields["ads_endtime"])) $arr_fields["ads_endtime"] = strtotime($arr_fields["ads_endtime"]);
		//如果更新了cont 则新生成html
		if(isset($arr_fields["ads_cont"])) {
			$arr = array();
			if(is_array($arr_fields["ads_cont"])) {
				$arr = $arr_fields["ads_cont"];
				$arr_fields["ads_cont"] = serialize($arr_fields["ads_cont"]);
			} else if(!empty($arr_fields["ads_cont"])) {
				$arr = unserialize($arr_fields["ads_cont"]);
			}
			$arr_fields["ads_html"] = fun_get::sql_escape(self::get_cont2html($arr));
		}
		$obj_db = cls_obj::db_w();
		if( empty($where) ) {

			//必填项检查
			if(!isset($arr_fields['ads_title']) || empty($arr_fields['ads_title'])) {
				$arr_return['code'] = 113;
				$arr_return['msg']  = cls_language::get("other_ads_is_null" , "other");//用户组名不能为空
				return $arr_return;
			}
			if(!isset($arr_fields["ads_addtime"])) $arr_fields["ads_addtime"] = TIME;
			//插入到表
			$arr = $obj_db->on_insert(cls_config::DB_PRE."other_ads",$arr_fields);
			if($arr['code'] == 0) {
				$arr_return['id'] = $obj_db->insert_id();
				//其它非mysql数据库不支持insert_id 时
				if(empty($arr_return['id'])) {
					$where  = "ads_title='" . $arr_fields['ads_title'] . " and ads_addtime='".$arr_fields['ads_addtime'] . "'";
					$obj_rs = $obj_db->get_one("select ads_id from ".cls_config::DB_PRE."other_ads where ".$where);
					if(!empty($obj_rs)) $arr_return['id'] = $obj_rs['ads_id'];
				}
			} else {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		} else {

			if($arr_return['id'] < 1) {
				$obj_rs = $obj_db->get_one("select ads_id from ".cls_config::DB_PRE."other_ads where ".$where);
				if(!empty($obj_rs)) {
					$arr_return['id'] = $obj_rs['ads_id'];
				} else {
					$arr_return['code'] = 114;
					$arr_return['msg']  = cls_language::get("no_editinfo");//修改信息不在在
					return $arr_return;
				}
				$where = "ads_id='".$arr_return['id']."'";
			}
			//修改数据表
			$arr = $obj_db->on_update(cls_config::DB_PRE."other_ads" , $arr_fields , $where);
			if($arr['code'] != 0) {
				$arr_return['code'] = $arr['code'];
				$arr_return['msg']  = cls_language::get("db_edit");
			}
		}
		return $arr_return;
	}
	/* 生成直接可用的html广告代码 
	 * arr 相关参数
	 */
	function get_cont2html($arr) {
		if(isset($arr["pic_url"])) {
			return self::get_pichtml($arr);
		} else if(isset($arr["flash_url"])) {
			return self::get_flashhtml($arr);
		} else if(isset($arr["txt_cont"])) {
			return $arr['txt_cont'];
		} else if(isset($arr["slide1"])) {
			return self::get_slide1html($arr);
		} else {
			return '';
		}
	}
	//生成图片html
	function get_pichtml($arr) {
		$html = "<img src='" . fun_get::html_url($arr["pic_url"]) . "'";
		$arr_style = array();
		if(isset($arr["pic_w"]) && !empty($arr["pic_w"])) {
			$arr_style[] = "width:" . (int)$arr["pic_w"] . "px";
		}
		if(isset($arr["pic_h"]) && !empty($arr["pic_h"])) {
			$arr_style[] = "height:" . (int)$arr["pic_h"] . "px";
		}
		$style = '';
		if(count($arr_style)>0) $style = "style='" . implode(";" , $arr_style) . "'";
		$html .= $style . ">";
		if(isset($arr["pic_link"]) && !empty($arr["pic_link"]) ) {
			$link = fun_get::html_url($arr["pic_link"]);
			$html = "<a href='" . $link . "' target='_blank'>" . $html . "</a>";
		}
		return $html;
	}
	//生成swf html
	function get_flashhtml($arr) {
		$arr_style = array();
		$w = $h = 0;
		if(isset($arr["flash_w"]) && !empty($arr["flash_w"])) {
			$w = (int)$arr["flash_w"];
			$arr_style[] = "width='" . $w . "px'";
		}
		if(isset($arr["flash_h"]) && !empty($arr["flash_h"])) {
			$h = (int)$arr["flash_h"];
			$arr_style[] = "height='" . (int)$arr["flash_h"] . "px'";
		}
		$style = '';
		if(count($arr_style)>0) $style = implode(" " , $arr_style);
		$flash_url = fun_get::html_url($arr["flash_url"]);
		$html = "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0' ".$style."><param name=movie value='" . $flash_url . "'><param name=quality value=high><param name='wmode' value='transparent'><embed src='" . $flash_url . "' wmode='transparent' quality=high pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash'" . $style . "></embed></object>";
		if(!empty($arr["flash_link"])){
			(stristr(KJ_WEBCSS_PATH , "http://")) ? $cssurl = KJ_WEBCSS_PATH : $cssurl = cls_config::get("dirpath") . KJ_WEBCSS_PATH;
			$html = "<div style='float:left;width:" . $w . "px;height:" . $h . "px'>" . $html . "<div style='position:relative;top:-" . $h . "px;left:0px;z-index:3'><a href='" . $arr["flash_link"] . "' target='_blank'><img src='" . $cssurl . "/common/images/blank.gif' width='" . $w . "px' height='" . $h . "px' border=0></a></div></div>";
		}
		return $html;
	}
	//生成幻灯片 
	function get_slide1html($arr) {
		$arr_style = array();
		$w = $h = 0;
		if(isset($arr["slide1_w"]) && !empty($arr["slide1_w"])) $w = (int)$arr["slide1_w"];
		if(isset($arr["slide1_h"]) && !empty($arr["slide1_h"])) $h = (int)$arr["slide1_h"];

		(stristr(KJ_WEBCSS_PATH , "http://")) ? $cssurl = KJ_WEBCSS_PATH : $cssurl = cls_config::get("dirpath") . KJ_WEBCSS_PATH;
		$arrx = array();
		$html = '<script>if(!kj.slide){';
		$html .= 'document.write("<style>.kj_slide{float:left;overflow:hidden}.kj_slide .xpic{float:left}.kj_slide .xpic img{}.kj_slide .xbtn{position:relative;top:-25px;width:100%;text-align:center}.kj_slide .xbtn div{float:left;width:100%}.kj_slide .xbtn span{float:left;width:48px;height:18px;background:#000;FILTER:alpha(opacity=50);opacity:0.6;margin-left:5px;text-decoration:none;cursor:pointer}.kj_slide .xbtn .ysel{FILTER:alpha(opacity=100);opacity:1}</style>");';
		$html .= 'kj.loadjs("' . $cssurl . '/common/js/kj.slide.js",function(){kj.slide.init();});}kj.onload(function(){if(kj.slide){kj.slide.init();}});</script><div class="kj_slide" style="width:' . $w . 'px;height:' . $h . 'px">';
		$html .= '<li class="xpic" style="width:' . $w . 'px;height:' . $h . 'px">';
		$img = '';
		foreach($arr["slide1"] as $item) {
			if(empty($img)) $img .= '<a href="' . fun_get::html_url($item["link"]) . '" target="_blank" title="' . $item["txt"] . '"><img src="' . fun_get::html_url($item["url"]) . '" style="width:' . $w . 'px;height:' . $h . 'px"></a>';
			$arrx[] = '<span mysrc="' . fun_get::html_url($item["url"]) . '" myurl="' . fun_get::html_url($item["link"]) . '">&nbsp;</span>';
		}
		$html .= $img . '</li>';
		$html .= '<li class="xbtn" style="width:' . $w . 'px"><div><table align="center"><tr><td>' . implode("" , $arrx) . '</td></tr></table></div></li>';
		$html .= '</div>';

		return $html;
	}
	/* 删除函数
	 * arr_id : 要删除的 id数组
	 * where : 删除附加条件
	 */
	static function on_delete($arr_id , $where = '') {
		$arr_return = array("code"=>0,"msg"=>"");
		$str_id = fun_format::arr_id($arr_id);
		if( empty($str_id) && empty($where) ){
			$arr_return["code"] = 22;
			$arr_return["msg"]="id".cls_language::get("not_null");
			return $arr_return;
		}
		$obj_db = cls_obj::db_w();
		if( !empty($str_id) ) {
			(is_numeric($str_id)) ? $arr_where[] = "ads_id='".$str_id."'" : $arr_where[] = "ads_id in(".$str_id.")";
		}
		if( !empty($where) ) {
			if(stristr($where , " or ") && substr(trim($where),0,1) != "(") $where = "(" . $where . ")";
			$arr_where[] = $where;
		}
		$where = implode(" and " , $arr_where);
		$arr_return=$obj_db->on_delete(cls_config::DB_PRE."other_ads" , $where);
		return $arr_return;
	}
}
?>