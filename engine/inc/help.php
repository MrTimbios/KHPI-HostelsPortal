<?PHP
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
 File: help.php
-----------------------------------------------------
 Use: Help
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$help_sections = array();
$section = totranslit($_REQUEST['section']);

$selected_language = $config['langs'];

if (isset( $_COOKIE['selected_language'] )) { 

	$_COOKIE['selected_language'] = totranslit( $_COOKIE['selected_language'], false, false );

	if ($_COOKIE['selected_language'] != "" AND @is_dir ( ROOT_DIR . '/language/' . $_COOKIE['selected_language'] )) {
		$selected_language = $_COOKIE['selected_language'];
	}

}

if ( file_exists( DLEPlugins::Check(ROOT_DIR . '/language/' . $selected_language . '/help.lng' )) ) {
	require_once (DLEPlugins::Check(ROOT_DIR . '/language/' . $selected_language . '/help.lng'));
} else die("Language file not found");

if($section){

	if(!isset($help_sections['title'][$section])){ die("Help section <b>$section</b> not found"); }

	echo"<div id=\"panel-help-section\" title=\"".$help_sections['title'][$section]."\" class=\"text-size-small\">".$help_sections['body'][$section]."</div>";
}
else{

	msg( "error", $lang['index_denied'], $lang['index_denied'] );
	
}
?>