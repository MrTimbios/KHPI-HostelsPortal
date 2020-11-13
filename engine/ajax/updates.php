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
 File: updates.php
-----------------------------------------------------
 Use: Check for new versions
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if(($member_id['user_group'] != 1)) {die ("error");}

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {

	echo $lang['sess_error'];
	die();

}

$_REQUEST['versionid'] = htmlspecialchars( strip_tags($_REQUEST['versionid']), ENT_QUOTES, $config['charset']);
$_REQUEST['build'] = htmlspecialchars( strip_tags($_REQUEST['build']), ENT_QUOTES, $config['charset']);

$data = @file_get_contents("http://dle-news.ru/extras/updates.php?version_id=".$_REQUEST['versionid']."&build=".$_REQUEST['build']."&key=".$config['key']."&lang=".$lang['wysiwyg_language']);

if ( !$data ) echo $lang['no_update']; else {

	if( function_exists( 'mb_convert_encoding' ) ) {
	
		$data = mb_convert_encoding( $data, "utf-8", "windows-1251" );
	
	} elseif( function_exists( 'iconv' ) ) {
		
		$data = iconv("windows-1251", "utf-8", $data);
		
	}
	
	echo $data;

}
?>