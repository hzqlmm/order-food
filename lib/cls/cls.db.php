<?php
/*
 *
 *
 * 2013-03-24
 */
class cls_db {
	protected $perms;
	function __construct($arr_msg) {
		$this->perms["db_pconnect"] = 0;
		$this->perms["db_charset"]  = '';
		foreach($arr_msg as $item => $key) {
			$this->perms[$item] = $key;
		}
	}
	//数据库连接
	function on_connect() {
		$arr_return = array("code"=>0,"connid"=>0);
		$func = $this->perms["db_pconnect"] == 1 ? 'mysql_pconnect' : 'mysql_connect';
		if(!$this->perms["db_connid"] = @$func($this->perms["db_host"], $this->perms["db_user"],$this->perms["db_pwd"])) {
			$arr_return["msg"] = cls_language::get("db_connect_err");
			$arr_return["code"] = 500;
			$this->on_error("db_connect",$arr_return["msg"]);
			return $arr_return;
		}
		if($this->version() > '4.1') {
			$serverset = $this->perms["db_charset"] ? "character_set_connection='".$this->perms["db_charset"]."',character_set_results='".$this->perms["db_charset"]."',character_set_client=binary" : '';
			$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',')." sql_mode='' ") : '';
			$serverset && mysql_query("SET $serverset", $this->perms["db_connid"]);
		}
		if(empty($this->perms["db_name"])) return $arr_return;
		if($this->perms["db_connid"] && !@mysql_select_db($this->perms["db_name"] , $this->perms["db_connid"])) {
			$arr_return["msg"] = cls_language::get("db_use_err")." ".$this->perms["db_name"];
			$arr_return["code"] = 500;
			$this->on_error("db_connect",$arr_return["msg"]);
		} else {
			$arr_return['connid'] = $this->perms["db_connid"];
		}
		return $arr_return;
	}
	function seldb($db) {
		$arr_return = array("code"=>0,"connid"=>$this->perms["db_connid"]);
		$this->perms["db_name"] = $db;
		if(!@mysql_select_db($this->perms["db_name"] , $this->perms["db_connid"])) {
			$arr_return["msg"] = cls_language::get("db_use_err")." ".$this->perms["db_name"];
			$arr_return["code"] = 500;
			$this->on_error("db_connect",$arr_return["msg"]);
		}
		return $arr_return;
	}
	function get_dbs() {
		$result = mysql_query('show databases;');
		$data = array();
		While($row = mysql_fetch_assoc($result)){       
			$data[] = strtolower($row['Database']);
		}
		return $data;
	}
	//查询
	function select($msg_sql , $error_type = 0) {
		$arr = $this->_query($msg_sql);
		if($arr["code"] == 0) {
			return $arr['query'];
		} else {
			//出错
			$this->on_error("db_select",$arr['msg'] , $error_type);
		}
	}
	function query($msg_sql , $error_type = 0) {
		$arr = $this->_query($msg_sql);
		if($arr["code"] == 0) {
			return $arr['query'];
		} else {
			//出错
			$this->on_error("db_select",$arr['msg'] , $error_type);
		}
	}
	/** 执行sql
	 *  error_type : 0表示记录错误，1表示不记录错误
	 */
	protected function _query($msg_sql , $msg_type='' , $error_type = 0 ) {
		$arr_return = array('code'=>0,'msg'=>'','query'=>'');
		if(!isset($this->perms["db_connid"])){
			$arr = $this->on_connect();
			if($arr["code"] != 0) return $arr;
		}
		if(!$this->perms["db_connid"]) {
			$arr_return["msg"] = cls_language::get("db_connect_err");
			$arr_return["code"] = 500;
			return $arr_return;
		}
		$func = $msg_type == 'UNBUFFERED' ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($msg_sql , $this->perms["db_connid"])) && $msg_type != 'SILENT') {
			$arr_return['code'] = 112;//见参数说明文档
			$arr_return['msg'] = "MySQL Query:".$msg_sql." <br /> MySQL Error:".$this->get_error()." <br /> MySQL Errno:".$this->get_errno();
			$this->on_error("db_query",$arr_return['msg'] , $error_type);
		} else {
			$arr_return['query'] = $query;
		}
		$this->_querynum++;
		return $arr_return;
	}
	function get_escape($str) {
		if(!isset($this->perms["db_connid"])){
			$this->on_connect();
		}
		if(!$this->perms["db_connid"]) {
			return $str;
		}
		return mysql_real_escape_string($str);
	}
	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		if(gettype($query) != "resource") return array();
		return mysql_fetch_array($query, $result_type);
	}

	function affected_rows() {
		return mysql_affected_rows($this->perms["db_connid"]);
	}
	function free_result(&$query) {
		return mysql_free_result($query);
	}
	function num_rows($query) {
		return mysql_num_rows($query);
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function insert_id() {
		return mysql_insert_id($this->perms["db_connid"]);
	}

	function version() {
		if(!isset($this->perms["db_connid"])){
			$arr = $this->on_connect();
			if($arr["code"] != 0) return 0;
		}
		return mysql_get_server_info($this->perms["db_connid"]);
	}

	function close(){
		return mysql_close($this->perms["db_connid"]);
	}

	function get_error() {
		return @mysql_error($this->perms["db_connid"]);
	}
	function get_errno() {
		return intval(@mysql_errno($this->perms["db_connid"])) ;
	}
	function get_pageinfo($tab , $where = '' , $page = 1 , $pagesize = 10) {
		$obj_rs = $this->get_one("SELECT count(1) as num FROM " . $tab . $where);
		(!empty($obj_rs))?	$arr_return['total'] = $obj_rs["num"] : $arr_return['total'] = 0 ;
		$arr_return['pages'] = ceil( $arr_return['total'] / $pagesize );
		$arr_return['page'] = max( min($arr_return['pages'] , $page) , 1 );
		$arr_return['offset'] = $pagesize * ($arr_return['page']-1);
		$arr_return['pagesize'] = $pagesize;
		$arr_return['limit'] = " limit " . $arr_return['offset'] . "," . $pagesize;
		return $arr_return;
	}
	function get_one($sql, $type = '' , $error_type = 0)	{
		$arr = $this->_query($sql, $type , $error_type);
		if($arr["code"] == 0) {
			$rs = $this->fetch_array($arr['query']);
			$this->free_result($arr['query']);
			return $rs ;
		} else {
			return array();
		}
	}
	function get_fields($table,$is_arr=false) {
		$fields = array();
		$result = $this->_query("SHOW COLUMNS FROM ".$table);
		if($is_arr) {
			while($r = $this->fetch_array($result['query']))	{
				$fields[$r['Field']] = "";
			}
		} else {
			while($r = $this->fetch_array($result['query']))	{
				$fields[] = $r['Field'];
			}
		}
		$this->free_result($result['query']);
		return $fields;
	}
	//取编辑信息
	function edit($table , $where = '1>2') {
		$obj_rs = $this->get_one("SELECT * FROM ".$table." where " . $where);
		if(empty($obj_rs)) {
			$obj_rs = $this->get_fields($table , true);
		}
		return $obj_rs;
	}
	//设置错误信息
	function on_error($key , $msg ,$error_type = 0) {
		if($error_type == 0) cls_error::on_error($key,$msg); //等于零的时候才调用错误类
	}
	//为兼容不同数据库将，sql中连接字符转换为些函数
	function concat($left , $mid , $right) {
		return "concat('" . $left . "'," . $mid . ",'" . $right . "')";
	}
}