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
 File: offline.php
-----------------------------------------------------
 Use: The temporary shutdown of the website
=====================================================
*/

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if ($member_id['user_group'] == '1' OR $user_group[$member_id['user_group']]['allow_offline']) {

	$metatags['title'] .= " (Offline)";

} else {

	$tpl->load_template('offline.tpl');

	$tpl->set('{THEME}', $config['http_home_url'].'templates/'.$config['skin']);
	$tpl->set('{charset}', $config['charset']);

	$config['offline_reason'] = str_replace('&quot;', '"', $config['offline_reason']);

	$tpl->set('{reason}', $config['offline_reason']);

	$tpl->compile('main');
	
	@header("Content-type: text/html; charset=".$config['charset']);

	echo $tpl->result['main'];

	die ();

}
?>