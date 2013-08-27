<?php
/*
 *
 *
 * 2013-03-24
 */
class mod_article extends inc_mod_common {
	/* 浏览指定id文章
	 * id : 文章id
	 * 根据文章或目录或频道指定模板显示文章
	 * 如果频道生成html将按指定模式生成html，并跳转到生成后的html
	 */
	function article_view_byid( $id ) {
		$obj_db = cls_obj::db();
		$obj_article = $obj_db->get_one("select article_id,article_channel_id,article_folder_id,article_tpl from " . cls_config::DB_PRE . "article where article_isdel=0 and article_state>0 and article_id='" . $id . "'");
		if(empty($obj_article)) {
			cls_error::on_show( array("jump_key" => array("back" , "refresh")) , '文章不存在' );
			exit;
		}
		$tpl = $obj_article["article_tpl"];
		$obj_folder = array("pids"=>$obj_article["article_folder_id"] , "folder"=>"");
		$arr_channel = cls_config::get_data("article_channel" , "id_" . $obj_article["article_channel_id"] );
		//目录是否指定了模板，优先于频道指定的模板
		if(!empty($obj_article["article_folder_id"])) {
			$obj_folder = $obj_db->get_one("select folder_article_tpl,folder_id,folder_name,folder_pids from " . cls_config::DB_PRE . "article_folder where folder_id='" . $obj_article["article_folder_id"] . "'");
			if(empty($tpl) && !empty($obj_folder["folder_article_tpl"]) ) $tpl = $obj_folder["folder_article_tpl"];
			if(!empty($obj_folder["folder_pids"])) $obj_folder["pids"] = str_replace("," , "/" , $obj_folder["folder_pids"]) . "/" . $obj_article["article_folder_id"];
			//当以目录名称形式分类，则取所有上级树目录名称
			if($arr_channel["channel_html_dirstyle"]==3 || ( !empty($obj_folder['folder_pids']) && empty($tpl) ) ) {
				if(empty($obj_folder["folder_pids"])) {
					$obj_folder["folder"] = $obj_folder["folder_name"];
				} else {
					$obj_result = $obj_db->select("select folder_name,folder_id,folder_article_tpl from " . cls_config::DB_PRE . "article_folder where folder_id in(" . $obj_folder["folder_pids"] . ")");
					while($obj_rs = $obj_db->fetch_array($obj_result)) {
						$arr_folder["id_".$obj_rs["folder_id"]] = $obj_rs;
					}
					$arr = explode(",",$obj_folder["folder_pids"]);
					$arr = array_reverse($arr);
					$arr_1 = array();
					foreach($arr as $item) {
						if(isset($arr_folder["id_".$item])) $arr_1[] = $arr_folder["id_".$item]["folder_name"];
						if(empty($tpl) && !empty($arr_folder["id_".$item]["folder_article_tpl"]) ) $tpl = $arr_folder["id_".$item]["folder_article_tpl"];
					}
					$obj_folder["folder"] = implode("/" , $arr_1) . "/" . $obj_folder["folder_name"];
				}
			}
		}
		if(empty($tpl) && isset($arr_channel["channel_article_tpl"])) {
			$tpl = $arr_channel["channel_article_tpl"];
		}
		if(empty($tpl)) {
			cls_error::on_show( array("jump_key" => array("back" , "refresh")) , '没有指定文章模板' );
			exit;
		}
		$arr = explode("." , fun_get::basename($tpl));
		$arr_folder = array(
			"dir" => fun_get::basename(dirname($tpl)),
			"app" => $arr[0],
			"act" => $arr[1]
		);
		$str_cont = cls_app::on_display($arr_folder["dir"] , '' , $arr_folder["app"] , $arr_folder["act"]);
		if(isset($arr_channel["channel_html"]) && $arr_channel["channel_html"] == "1") {
			$arr_return = $this->article_tohtml($obj_article["article_id"] , $arr_channel , $obj_folder , $str_cont);
			if(!empty($arr_return["path"])) {
				fun_base::url_jump(fun_get::html_url($arr_return["path"]));
			} else {
				echo $str_cont;
			}
		} else {
			echo $str_cont;
		}
	}
	/* 生成文章html
	 * name : 生成文件名 , arr_channel : 频道相关设置参数 , obj_folder : 目录相关参数　, str_html : html内容
	 */
	function article_tohtml($name , $arr_channel, $obj_folder , $str_html) {
		$arr_return = array("code"=>0 , "path"=>"");
		$path = $arr_channel["channel_html_dir"]; 
		if(substr($path,0,1) != "/" && substr($path,0,1)!="\\") $path = "/" . $path;
		$path = $path;
		switch($arr_channel["channel_html_dirstyle"]) {
			case 0:
				$path .= "/" . date("Y");
				break;
			case 1:
				$path .= "/" . date("Y-m");
				break;
			case 2:
				$path .= "/" . date("Y-m-d");
				break;
			case 3:
				if(!empty($obj_folder["folder"])) {
					(substr($obj_folder["folder"],0,1) == "/" ) ? $path .= $obj_folder["folder"] : $path .= "/" . $obj_folder["folder"];
				}
				break;
			case 4:
				if(!empty($obj_folder["pids"])) {
					(substr($obj_folder["pids"],0,1) == "/" ) ? $path .= $obj_folder["pids"] : $path .= "/" . $obj_folder["pids"];
				}
				break;
		}
		(!isset($arr_channel["channel_html_ext"]) || empty($arr_channel["channel_html_ext"])) ? $ext = "html" : $ext = $arr_channel["channel_html_ext"];
		$path .= "/" . $name . "." . $ext;
		fun_file::file_create( KJ_DIR_ROOT . $path , $str_html , 1);
		$arr_return["path"] = $path;
		return $arr_return;
	}

	/* 浏览指定频道
	 *
	 */
	function channel_view_byid($id) {
		$arr_channel = cls_config::get_data("article_channel" , "id_" . $id );
		if(empty($arr_channel)) {
			cls_error::on_show( array("jump_key" => array("back" , "refresh")) , '频道不存在' );
			exit;
		}
		if(!isset($arr_channel["channel_tpl"]) || empty($arr_channel["channel_tpl"])) {
			cls_error::on_show( array("jump_key" => array("back" , "refresh")) , '没有指定频道模板' );
			exit;
		}
		$tpl = $arr_channel["channel_tpl"];
		$arr = explode("." , fun_get::basename($tpl));
		$arr_folder = array(
			"dir" => fun_get::basename(dirname($tpl)),
			"app" => $arr[0],
			"act" => $arr[1]
		);
		$str_cont = cls_app::on_display($arr_folder["dir"] , '' , $arr_folder["app"] , $arr_folder["act"]);
		if(isset($arr_channel["channel_html"]) && $arr_channel["channel_html"] == "1") {
			$arr_return = $this->article_tohtml($obj_article["article_id"] , $arr_channel , $obj_folder , $str_cont);

			$path = $arr_channel["channel_html_dir"]; 
			if(substr($path,0,1) != "/" && substr($path,0,1)!="\\") $path = "/" . $path;
			(!isset($arr_channel["channel_html_ext"]) || empty($arr_channel["channel_html_ext"])) ? $ext = "html" : $ext = $arr_channel["channel_html_ext"];
			$path .= "/index." . $ext;
			fun_file::file_create( KJ_DIR_ROOT . $path , $str_cont , 1);
			fun_base::url_jump(fun_get::html_url($path)); //跳转到生成的静态文件
		} else {
			echo $str_cont;
		}
	}
	/* 浏览指定id目录
	 * id : 目录id
	 * 根据目录或频道指定模板显示目录
	 * 如果频道生成html将按指定模式生成html，并跳转到生成后的html
	 */
	function folder_view_byid( $id ) {
		$obj_db = cls_obj::db();
		$obj_folder = $obj_db->get_one("select folder_id,folder_channel_id,folder_tpl,folder_pids from " . cls_config::DB_PRE . "article_folder where folder_isdel=0 and folder_id='" . $id . "'");
		if(empty($obj_folder)) {
			cls_error::on_show( array("jump_key" => array("back" , "refresh")) , '目录不存在' );
			exit;
		}
		$tpl = $obj_folder["folder_tpl"];
		$arr_channel = cls_config::get_data("article_channel" , "id_" . $obj_folder["folder_channel_id"] );
		//上级目录是否指定了模板，优先于频道指定的模板
		$obj_folder["pids"] = $obj_folder["folder_id"];
		if(!empty($obj_folder["folder_pids"])) $obj_folder["pids"] = str_replace("," , "/" , $obj_folder["folder_pids"]) . "/" . $obj_folder["folder_id"];
		//当以目录名称形式分类，则取所有上级树目录名称
		if($arr_channel["channel_html_dirstyle"]==3 || ( !empty($obj_folder['folder_pids']) && empty($tpl) ) ) {
			if(empty($obj_folder["folder_pids"])) {
				$obj_folder["folder"] = $obj_folder["folder_name"];
			} else {
				$obj_result = $obj_db->select("select folder_name,folder_id,folder_tpl from " . cls_config::DB_PRE . "article_folder where folder_id in(" . $obj_folder["folder_pids"] . ")");
				while($obj_rs = $obj_db->fetch_array($obj_result)) {
					$arr_folder["id_".$obj_rs["folder_id"]] = $obj_rs;
				}
				$arr = explode(",",$obj_folder["folder_pids"]);
				$arr = array_reverse($arr);
				$arr_1 = array();
				foreach($arr as $item) {
					if(isset($arr_folder["id_".$item])) $arr_1[] = $arr_folder["id_".$item]["folder_name"];
					if(empty($tpl) && !empty($arr_folder["id_".$item]["folder_tpl"]) ) $tpl = $arr_folder["id_".$item]["folder_tpl"];
				}
				$obj_folder["folder"] = implode("/" , $arr_1) . "/" . $obj_folder["folder_name"];
			}
		}

		if(empty($tpl) && isset($arr_channel["channel_folder_tpl"])) {
			$tpl = $arr_channel["channel_folder_tpl"];
		}
		if(empty($tpl)) {
			cls_error::on_show( array("jump_key" => array("back" , "refresh")) , '没有指定目录模板' );
			exit;
		}
		$arr = explode("." , fun_get::basename($tpl));
		$arr_folder = array(
			"dir" => fun_get::basename(dirname($tpl)),
			"app" => $arr[0],
			"act" => $arr[1]
		);
		$str_cont = cls_app::on_display($arr_folder["dir"] , '' , $arr_folder["app"] , $arr_folder["act"]);
		if(isset($arr_channel["channel_html"]) && $arr_channel["channel_html"] == "1") {
			$arr_return = $this->article_tohtml("index" , $arr_channel , $obj_folder , $str_cont);
			if(!empty($arr_return["path"])) {
				fun_base::url_jump(fun_get::html_url($arr_return["path"]));
			} else {
				echo $str_cont;
			}
		} else {
			echo $str_cont;
		}
	}
}