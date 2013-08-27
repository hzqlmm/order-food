<?php
/*
 *
 *
 * 2013-03-24
 */
class cls_db_write extends cls_db {
	/*名称：表插入
	  参数：msg_table=表名，msg_fileds=字段数组
	*/
	private $on_transaction = "";//当前事务名称

	function on_insert($msg_table , $msg_fileds , $error_type = 0) {
		$str_fields = "";
		$str_values = "";
		foreach($msg_fileds as $item=>$key) {
			$str_fields .= $item.",";
			$str_values .= "'" . $this->get_escape($key) . "',";
		}
		$str_fields = substr($str_fields,0,-1);
		$str_values = substr($str_values,0,-1);
		$str_sql = "insert into ".$msg_table."(".$str_fields.") values(".$str_values.")";
		return $this->_query( $str_sql , '' , $error_type );
	}
	//批量插入
	function on_insert_all($msg_table , $msg_fileds , $error_type = 0) {
		$str_fields = "";
		$str_values = "";
		$arr_value = array();
		$str_x = "";
		foreach($msg_fileds as $item) {
			$str_values = "";
			foreach($item as $item_next => $key) {
				$str_x .= $item_next.",";
				$str_values .= "'" . $this->get_escape($key) . "',";
			}
			if(empty($str_fields)) $str_fields = substr($str_x,0,-1);
			$str_values = substr($str_values,0,-1);
			$arr_value[] = "(" . $str_values . ")";
		}
		$str_values = implode("," , $arr_value);
		$str_sql = "insert into ".$msg_table."(".$str_fields.") values".$str_values;
		return $this->_query( $str_sql , '' , $error_type );
	}
	//修改
	function on_update($msg_table, $msg_fields, $msg_where='' , $error_type = 0) {
		if($msg_where){
			$str_sql = '';
			foreach($msg_fields as $key=>$item)
			{
				$str_sql .= ", `".$key."`='" . $this->get_escape($item) . "'";
			}
			$str_sql = substr($str_sql, 1);
			$str_sql = "UPDATE `".$msg_table."` SET ".$str_sql." WHERE ".$msg_where;
		}else{
			$str_sql = "REPLACE INTO `$tablename`(`".implode('`,`', array_keys($msg_fields))."`) VALUES('".implode("','", $msg_fields)."')";
		}
		return $this->_query($str_sql , '' , $error_type);
	}
	//执行
	function on_exe($str_sql , $error_type = 0) {
		return $this->_query($str_sql , '' , $error_type);
	}
	//删除
	function on_delete($msg_table , $msg_where , $error_type = 0) {
		return $this->_query("delete from ".$msg_table." where ".$msg_where , '' , $error_type);
	}
	//事务开始
	function begin($msg_name){
		if($this->on_transaction!="") return;
		if(!isset($this->perms["db_connid"]))	$this->on_connect();
		mysql_query("begin",$this->perms["db_connid"]);
		$this->on_transaction=$msg_name;
	}
	//事务完成
	function commit($msg_name){
		if($this->on_transaction!=$msg_name) return;
		if(!isset($this->perms["db_connid"]))	$this->on_connect();
		mysql_query("commit",$this->perms["db_connid"]);
		$this->on_transaction="";
	}
	//事务回滚
	function rollback($msg_name){
		if($this->on_transaction!=$msg_name) return;
		if(!isset($this->perms["db_connid"]))	$this->on_connect();
		mysql_query("rollback",$this->perms["db_connid"]);
		$this->on_transaction="";
	}

}