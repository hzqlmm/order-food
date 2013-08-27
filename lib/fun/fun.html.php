<?php
/*
 *
 *
 * 2013-03-24
 * 自定义html组件类
 */
class fun_html{
	//生成 select 组件
	static function select($name , $options , $default = '' , $layer_split = "&nbsp;&nbsp;&nbsp;&nbsp;") {
		$str_html = "<select name='".$name."' id='id_".$name."'>";
		foreach($options as $item) {
			$val = $name = $split = $str_sel = "";
			if(is_array($item)) {
				if(isset($item["val"])) {
					$val = $item["val"];
				} else if(isset($item["title"])){
					$val = $title = $item["title"];
				}
				(isset($item["title"])) ? $title = $item["title"] : $title = $val;
			} else {
				$val = $title = $item;
			}
			if(isset($item["layer"]) && $item["layer"]>0) {
				$split = str_pad($layer_split , $item["layer"]*strlen($layer_split) , $layer_split);
			}
			if($val == $default) $str_sel = " selected";
			if(empty($title)) {
				$str_html .= "<option value='" . $val . "'" . $str_sel . ">".$title . "</option>";
			} else {
				$str_html .= "<option value='" . $val . "'" . $str_sel . ">" . $split . "|--".$title . "</option>";
			}
		}
		$str_html .= "</select>";
		return $str_html;
	}
}