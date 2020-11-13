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
 File: print.php
-----------------------------------------------------
 Use: Version for print
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../' );
	die( "Hacking attempt!" );
}

if($dle_module != "static" AND $dle_module != "showfull" ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: /' );
	die("Hacking attempt!");
}

@header("Content-type: text/html; charset=".$config['charset']);

if ($config['rss_informer']) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/rssinform.php'));

if($dle_module == "static" AND $_GET['page'] == "rules") {
	$_GET['page'] = "dle-rules-page";
}

$config['allow_cache'] = false;
$view_template = "print";

require_once (DLEPlugins::Check(ENGINE_DIR . '/engine.php'));

$tpl->result['content'] = str_replace ( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $tpl->result['content'] );
$tpl->result['content'] = str_replace ( '{charset}', $config['charset'], $tpl->result['content'] );

echo $tpl->result['content'];

die();

?>