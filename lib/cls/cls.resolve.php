<?php
/* versionbeta:name
 * versionbeta:number
 * versionbeta:site
 * versionbeta:pubtime
 */
class cls_resolve {
	//获取类配置参数
	public static function get_config($msg_name,$msg_val=""){
		static $arr_perms=array();
		$css = KJ_WEBCSS_PATH;
		if(!stristr($css , "http://")) $css = cls_config::get("dirpath") . $css;
		$arr_config =array(
			"view_root"=>KJ_DIR_APP."/view",//html模板路径
			"css_root"=>$css,//前端样式路径，包括图片,js,css
			"view_ext"=>".html",//html模板文件扩展名，只解析html模板目录下些扩展名的文件
			"cache_view_root"=>KJ_DIR_CACHE."/view",//解析后模板保存路径，缓存目录下面
			"default_dirname"=>"/default"//默认模板名称
		);
		if($msg_val!=""){
			$arr_perms[$msg_name]=$msg_val;
		}
		if(isset($arr_config[$msg_name])){
			return $arr_config[$msg_name];
		}else if(isset($arr_perms[$msg_name])){
			return $arr_perms[$msg_name];
		}else{
			return "";
		}
	}
	//取模板路径
	public static function get_view_path($msg_path)
	{
		$str_path=fun_get::real_path($msg_path,self::get_config("view_root"));
		return $str_path;
	}
	//解析后模板保存路径，缓存目录下面
	public static function get_cache_path($msg_path)
	{
		$str_path=fun_format::path($msg_path);
		$str_path=str_replace(self::get_config("view_root"),"",$str_path);
		$str_path=fun_get::real_path($str_path,self::get_config("cache_view_root"));
		$str_dir=dirname($str_path);	
		$str_name=fun_get::basename($str_path,self::get_config("view_ext"));
		$str_path=$str_dir."/".$str_name.".php";
		return $str_path;
	}
	public static function get_php_folder($msg_path)
	{
		$arr_return=array("dir"=>"","dir_bak"=>"");
		$str_path=substr($msg_path,strlen(self::get_config("cache_view_root")));
		$arr_return["dir"]=dirname($str_path);
		if(self::get_config("tempcur")!=''){
			$arr_x=explode("/",$str_path);
			$str_x="/".$arr_x[1];
			$arr_return["dir_bak"]=$arr_return["dir"];
			$arr_return["dir"]=dirname(self::get_config("tempcur").substr($str_path,strlen($str_x)));
		}
		return $arr_return;
	}
	//取前端样式路径
	public static function get_css_url($msg_path){
		$str_path=substr($msg_path,strlen(self::get_config("view_root")));
		$str_url=self::get_config("css_root").dirname($str_path)."/";
		return $str_url;
	}
	//把html模板解析成php文件
	public static function on_resolve($msgfile)
	{
		if($msgfile=="") return "";
		$str_name=fun_get::basename($msgfile);
		$arr_name=explode(".",$str_name);
		$msgfile.=self::get_config("view_ext");
		$html_path=self::get_view_path($msgfile);
		$html_old_path="";
		$msgfile_old=$msgfile;
		if(!file_exists($html_path)){
			$arr_x=explode("/",$msgfile);
			$str_x="/".$arr_x[1];
			self::get_config("tempcur",$str_x);
			$msgfile=self::get_config("default_dirname").substr($msgfile,-strlen($str_x));
			$html_old_path=$html_path;
			$html_path=self::get_view_path($msgfile);
		}
		//模板不在在
		if( !file_exists($html_path) ) return "";
		$php_path=self::get_cache_path($msgfile_old);
		$arr_html=fun_file::get_file_perms($html_path);
		$arr_php=fun_file::get_file_perms($php_path);
		if(!file_exists($php_path))
		{
			self::on_refresh($html_path,$html_old_path);
		}else if($arr_html["mtime"]>$arr_php["mtime"]){
			self::on_refresh($html_path,$html_old_path);
		}
		return $php_path;
	}
	//重刷新模板
	public static function on_refresh($msghtml,$msghtml2="")
	{
		$html_path=$msghtml;
		if(!file_exists($html_path)) return "";
		$php_path=self::get_cache_path($msghtml);
		$php_cache_path=$php_path;
		if($msghtml2!=""){
			$php_cache_path=self::get_cache_path($msghtml2);
		}
		$arr_php_folder=self::get_php_folder($php_path);
		$php_folder=$arr_php_folder["dir"];
		$php_folder_bak=$arr_php_folder["dir_bak"];
		$str_now_path=self::get_css_url($msghtml);
		$str_source=file_get_contents($html_path);
		$str_source=str_replace('{$temp_url}',$str_now_path,$str_source);
		$str_source=str_replace('{$temp_baseurl}',self::get_config("css_root")."/",$str_source);
		if(defined("DB_CHARSET") && strtolower(DB_CHARSET)=="gbk"){
			$str_utf8_u="";
			$str_chinacode=chr(0xa1)."-".chr(0xff);
		}else{
			$str_utf8_u="u";
			$str_chinacode="\x{4e00}-\x{9fa5}";
		}
		//libs
		//转换{include()} 包函文件代码
		$str_source=preg_replace("/\{include\((\/[_.\/a-z0-9".$str_chinacode."]{1,50})\)\}/".$str_utf8_u."is", "<?php include cls_resolve::on_resolve('\\1')?>", $str_source);
		$str_source=preg_replace("/\{include\(([^\/][_.\/a-z0-9".$str_chinacode."]{1,50})\)\}/".$str_utf8_u."is", "<?php include cls_resolve::on_resolve('".$php_folder."\/\\1')?>", $str_source);
		//转换{fun()} 包函文件代码
		$str_source=preg_replace("/\{fun\(([\/]{0,1}[_.\/a-z0-9".$str_chinacode."]{1,200})\)\}/".$str_utf8_u."is", "<?php include cls_resolve::on_resolve('/sys/fun/\\1.html')?>", $str_source);
		//转换{define()} 定义文件代码
		$str_source=preg_replace("/\{define[(]([\$][a-z0-9_]+),\"([^\"]*)\"[)]\}/is", "<?php \\1=\"\\2\";?>", $str_source);
		//转换{=} 输出代码
		$str_source=preg_replace("/\{([\$]+[a-z0-9_\]\[\"\'->\)\(.$]+)}/is", "<?php echo \\1;?>", $str_source);
		//set 设置值
		$str_source=preg_replace("/\{set\s([\$]+[a-z0-9_\]\[\"\'->\)\(.$|\?&%]+)}/is", "<?php \\1;?>", $str_source);
		//fun公共类调用
		$str_source=preg_replace("/{fun_([a-z0-9_]+)::([a-z0-9_]+)\((.*?)\)\}/is", "<?php echo fun_\\1::\\2(\\3);?>", $str_source);
		$str_source=preg_replace("/{cls_language::([a-z0-9_]+)\((.*?)\)\}/is", "<?php echo cls_language::\\1(\\2);?>", $str_source);
		//config类
		$str_source=preg_replace("/{cfg:\(([a-z0-9_-]+?),([a-z0-9_-]+?)\)\}/is", '<?php echo cls_config::get("\\1","\\2");?>', $str_source);
		//href替换
		$str_source=preg_replace("/(<a\s[^>]*href=[\"|\'])\/(.*?)([\"|\'])/is", "\\1".cls_config::get('url')."/\\2\\3", $str_source);
		//url根目录路径
		$str_source=preg_replace("/(<link\s[^>]*href=[\"|\'])\/(.*?)([\"|\'])/is", "\\1".self::get_config("css_root")."/\\2\\3", $str_source);
		$str_source=preg_replace("/(<img\s[^>]*src=[\"|\'])\/(.*?)([\"|\'])/is", "\\1".self::get_config("css_root")."/\\2\\3", $str_source);
		$str_source=preg_replace("/(<script\s[^>]*src=[\"|\'])\/(.*?)([\"|\'])/is", "\\1".self::get_config("css_root")."/\\2\\3", $str_source);
		$str_source=preg_replace("/(background:url\()\/(.*?)(\))/is", "\\1".self::get_config("css_root")."/\\2\\3", $str_source);
		//url相对路径
		$str_source=preg_replace("/(<link\s[^>]*href=[\"|\'])((?!http)(?!\/)(?!<).*?)([\"|\'])/is", "\\1".$str_now_path."\\2\\3", $str_source);
		$str_source=preg_replace("/(<img\s[^>]*src=[\"|\'])((?!http)(?!\/)(?!<).*?)([\"|\'])/is", "\\1".$str_now_path."\\2\\3", $str_source);
		$str_source=preg_replace("/(background:url\()((?!http)(?!\/)(?!<).*?)(\))/is", "\\1".$str_now_path."\\2\\3", $str_source);
		$str_source=preg_replace("/(<script\s[^>]*src=[\"|\'])((?!http)(?!\/)(?!<).*?)([\"|\'])/is", "\\1".$str_now_path."\\2\\3", $str_source);
		//启用店铺简短域名
		if(cls_config::get('shopshortpath','sys') == 1) {
			$str_source = str_replace('index.php?app_act=shop&id=' , '/' , $str_source);
			$str_source = str_replace('?app_act=shop&id=' , '/' , $str_source);
		}
		$str_source=self::temp_replace_if($str_source);
		$str_source=self::temp_replace_foreach($str_source);
		//缓存为html
		if(strstr(strtolower($str_source),"{cache_to_html")){
			$str_source="<?php ob_start();?>".$str_source;
			$str_source=preg_replace("/{cache_to_html\((.+?)\)\}/is", "<?php cache_to_html(\\1);?>", $str_source);
		}
		//缓存为php
		if(strstr(strtolower($str_source),"{cache_to_php")){
			$str_parms=preg_replace("/.*{cache_to_php\((.+?)\)\}.*/is", "\\1", $str_source);
			$str_source=preg_replace("/{cache_to_php\((.+?)\)\}/is", "", $str_source);
			$arr_parms=explode(",",$str_parms);//0:文件名 , 1:缓存时间
			$str_x='<?php $lng_time=0;$str_file=fun_get::real_path(CACHE_DIR."/to_php/'.$arr_parms[0].'/".get_query_key().".html");if(is_file($str_file)){$arr_file=fun_file::get_file_perms($str_file);$lng_time=strtotime($arr_file["mtime"]);}';
			$str_x.='if(TIME-$lng_time>'.$arr_parms[1].'){ob_start();?>'.$str_source.'<?php $str_content=ob_get_contents();fun_file::file_create($str_file,$str_content,1);}else{include $str_file;}?>';
			$str_source=$str_x;
			unset($str_x);
		}
		$str_dir=dirname($php_cache_path);
		if(!is_dir($str_dir)) fun_file::dir_create($str_dir);
		fun_file::file_create($php_cache_path,$str_source);
		
	}

	public static function temp_replace_if($str_source)
	{
		$str_source = preg_replace("/([\n\r\t]*)\{else if\((.+?)\)\}([\n\r\t]*)/is", "\\1<?php } else if(\\2) { ?>\\3", $str_source);
		$str_source = preg_replace("/([\n\r\t]*)\{else\}([\n\r\t]*)/is", "\\1<?php } else { ?>\\2", $str_source);
		$str_source = preg_replace("/([\n\r\t]*)\{if\((.+?)\)\}([\n\r\t]*)/is", "\\1<?php if(\\2){?>\\3", $str_source);
		$str_source = preg_replace("/([\n\r\t]*)\{\/if\}([\n\r\t]*)/is", "\\1<?php }?>\\2", $str_source);
		return $str_source;
	}
	public static function temp_replace_foreach($msgstr)
	{
		$str_source=$msgstr;
		$str_source=preg_replace("/{foreach\(([\s\$\/\*a-zA-Z0-9+-_=><\'\"]+)\)}/is", "<?php foreach(\\1){ ?>", $str_source);
		$str_source=preg_replace("/{\/foreach}/is", "<?php }?>", $str_source);
		$str_source=preg_replace("/{for\(([\s\$\/\*a-zA-Z0-9+-_=><\'\"\;\,\.\(\)]+)\)}/is", "<?php for(\\1){ ?>", $str_source);
		$str_source=preg_replace("/{\/for}/is", "<?php }?>", $str_source);
		return $str_source;
	}

}