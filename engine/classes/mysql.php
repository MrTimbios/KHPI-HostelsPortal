<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004-2020 SoftNews Media Group
=====================================================
 This code is protected by copyright
=====================================================
 File: mysql.php
-----------------------------------------------------
 Use: MySQL class
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

class db
{
	var $db_id = false;
	var $query_num = 0;
	var $query_list = array();
	var $query_errors_list = array();
	var $mysql_error = '';
	var $mysql_version = '';
	var $mysql_error_num = 0;
	var $mysql_extend = "";
	var $MySQL_time_taken = 0;
	var $query_id = false;

	
	function connect($db_user, $db_pass, $db_name, $db_location = 'localhost', $show_error=1) {
		$db_location = explode(":", $db_location);
		
		$time_before = $this->get_real_time();

		if (isset($db_location[1])) {

			$this->db_id = @mysqli_connect($db_location[0], $db_user, $db_pass, $db_name, $db_location[1]);

		} else {

			$this->db_id = @mysqli_connect($db_location[0], $db_user, $db_pass, $db_name);

		}
		
		$this->query_list[] = array('query' => 'Connection with MySQL Server',
									'time'  => ($this->get_real_time() - $time_before), 
									'num'   => 0);
		
		if(!$this->db_id) {
			if($show_error == 1) {
				$this->display_error(mysqli_connect_error(), '1');
			} else {
				$this->query_errors_list[] = array( 'error' => mysqli_connect_error() );
				return false;
			}
		} 

		$this->mysql_version = mysqli_get_server_info($this->db_id);

		if( version_compare($this->mysql_version, '5.5.3', '<') ) {

			die ("Datalife Engine required MySQL version 5.5.3 or greater. You need upgrade MySQL version on your server.");

		}

		mysqli_set_charset ($this->db_id , COLLATE );
		
		mysqli_query($this->db_id, "SET NAMES '" . COLLATE . "'", false );

		$this->sql_mode();

		return true;
	}
	
	function query($query, $show_error=true, $log_query=true) {
		$time_before = $this->get_real_time();

		if(!$this->db_id) $this->connect(DBUSER, DBPASS, DBNAME, DBHOST);

		if(!($this->query_id = mysqli_query($this->db_id, $query) )) {

			$this->mysql_error = mysqli_error($this->db_id);
			$this->mysql_error_num = mysqli_errno($this->db_id);

			if($show_error) {
				
				$this->display_error($this->mysql_error, $this->mysql_error_num, $query);
				
			} else {
				
				$this->query_errors_list[] = array( 'query' => $query, 'error' => $this->mysql_error );
				
			}
		}
			
		$this->MySQL_time_taken += $this->get_real_time() - $time_before;

	    if( $log_query ) {
			
			$this->query_list[] = array('query' => $query,
										'time'  => ($this->get_real_time() - $time_before), 
										'num'   => count($this->query_list));
			$this->query_num ++;
			
		}
		
		return $this->query_id;
	}
	
	function multi_query($query, $show_error=true, $log_query=true) {
		$time_before = $this->get_real_time();

		if(!$this->db_id) $this->connect(DBUSER, DBPASS, DBNAME, DBHOST);
		
		if( mysqli_multi_query($this->db_id, $query) ) {
			while( mysqli_more_results($this->db_id) && mysqli_next_result($this->db_id) ){
				;
			}
		}
		
		if( mysqli_error($this->db_id) ) {
			
			$this->mysql_error = mysqli_error($this->db_id);
			$this->mysql_error_num = mysqli_errno($this->db_id);
			
			if($show_error) {
				
				$this->display_error($this->mysql_error, $this->mysql_error_num, $query);
				
			} else {
				
				$this->query_errors_list[] = array( 'query' => $query, 'error' => $this->mysql_error );
				
			}
		}
		
	    if( $log_query ) {
			
			$this->query_list[] = array('query' => $query,
										'time'  => ($this->get_real_time() - $time_before), 
										'num'   => count($this->query_list));

			$this->MySQL_time_taken += $this->get_real_time() - $time_before;
			
		}
		
		$this->query_num ++;

	}
	
	function get_row($query_id = '') {
		if ($query_id == '') $query_id = $this->query_id;

		return mysqli_fetch_assoc($query_id);
	}

	function get_affected_rows() {
		return mysqli_affected_rows($this->db_id);
	}

	function get_array($query_id = '') {
		if ($query_id == '') $query_id = $this->query_id;

		return mysqli_fetch_array($query_id);
	}
	
	function super_query($query, $multi = false, $show_error=true, $log_query=true) {

		if(!$multi) {

			$this->query($query, $show_error, $log_query);
			$data = $this->get_row();
			$this->free();
			
			return $data;

		} else {
			
			$this->query($query, $show_error, $log_query);
			
			$rows = array();
			
			while($row = $this->get_row()) {
				$rows[] = $row;
			}

			$this->free();			

			return $rows;
		}
	}
	
	function num_rows($query_id = '') {
		if ($query_id == '') $query_id = $this->query_id;

		return mysqli_num_rows($query_id);
	}
	
	function insert_id() {
		return mysqli_insert_id($this->db_id);
	}

	function get_result_fields($query_id = '') {

		if ($query_id == '') $query_id = $this->query_id;

		while ($field = mysqli_fetch_field($query_id))
		{
            $fields[] = $field;
		}
		
		return $fields;
   	}

	function safesql( $source ) {
		if(!$this->db_id) $this->connect(DBUSER, DBPASS, DBNAME, DBHOST);

		if ($this->db_id) return mysqli_real_escape_string ($this->db_id, $source);
		else return addslashes($source);
	}

	function free( $query_id = '' ) {

		if ($query_id == '') $query_id = $this->query_id;

		@mysqli_free_result($query_id);
	}

	function close() {
		@mysqli_close($this->db_id);
		$this->db_id = false;
	}

	function get_real_time() {
		list($seconds, $microSeconds) = explode(' ', microtime());
		return ((float)$seconds + (float)$microSeconds);
	}	

	function sql_mode() {
		$remove_modes = array( 'STRICT_TRANS_TABLES', 'STRICT_ALL_TABLES', 'ONLY_FULL_GROUP_BY', 'NO_ZERO_DATE', 'NO_ZERO_IN_DATE', 'TRADITIONAL' );
		
		$res = $this->query( "SELECT @@SESSION.sql_mode", false, false );

		$row = $this->get_array();
		
		if ( !$row[0] ) {
			return;
		}
		
		$modes_array = explode( ',', $row[0] );
		$modes_array = array_change_key_case( $modes_array, CASE_UPPER );

		foreach ( $modes_array as $key => $value ) {
			if ( in_array( $value, $remove_modes ) ) {
				unset( $modes_array[ $key ] );
			}
		}
		
		$mode_list = implode(',', $modes_array);

		if($row[0] != $mode_list) {
			$this->query( "SET SESSION sql_mode='{$mode_list}'", false, false );
		}
		
	}
	
	function __destruct() {
		
		if( $this->db_id ) @mysqli_close($this->db_id);
		
		$this->db_id = false;
	}
	
	function display_error($error, $error_num, $query = '') {

		$query = htmlspecialchars($query, ENT_QUOTES, 'utf-8');
		$error = htmlspecialchars($error, ENT_QUOTES, 'utf-8');

		$trace = debug_backtrace();

		$level = 0;
		if ($trace[1]['function'] == "query" ) $level = 1;
		if ($trace[2]['function'] == "super_query" ) $level = 2;

		$trace[$level]['file'] = str_replace(ROOT_DIR, "", $trace[$level]['file']);

		echo <<<HTML
<?xml version="1.0" encoding="iso-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>MySQL Fatal Error</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--
body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-style: normal;
	color: #000000;
}
.top {
  color: #ffffff;
  font-size: 15px;
  font-weight: bold;
  padding-left: 20px;
  padding-top: 10px;
  padding-bottom: 10px;
  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.75);
  background-color: #AB2B2D;
  background-image: -moz-linear-gradient(top, #CC3C3F, #982628);
  background-image: -ms-linear-gradient(top, #CC3C3F, #982628);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#CC3C3F), to(#982628));
  background-image: -webkit-linear-gradient(top, #CC3C3F, #982628);
  background-image: -o-linear-gradient(top, #CC3C3F, #982628);
  background-image: linear-gradient(top, #CC3C3F, #982628);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#CC3C3F', endColorstr='#982628',GradientType=0 ); 
  background-repeat: repeat-x;
  border-bottom: 1px solid #ffffff;
}
.box {
	margin: 10px;
	padding: 4px;
	background-color: #EFEDED;
	border: 1px solid #DEDCDC;

}
-->
</style>
</head>
<body>
	<div style="width: 700px;margin: 20px; border: 1px solid #D9D9D9; background-color: #F1EFEF; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px; -moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); -webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);" >
		<div class="top" >MySQL Error!</div>
		<div class="box" ><b>MySQL error</b> in file: <b>{$trace[$level]['file']}</b> at line <b>{$trace[$level]['line']}</b></div>
		<div class="box" >Error Number: <b>{$error_num}</b></div>
		<div class="box" >The Error returned was:<br /> <b>{$error}</b></div>
		<div class="box" ><b>SQL query:</b><br /><br />{$query}</div>
		</div>		
</body>
</html>
HTML;
		
		die();
	}

}

?>