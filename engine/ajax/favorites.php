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
 File: favorites.php
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$is_logged ) die( "error" );

$id = intval( $_REQUEST['fav_id'] );

if( !$id OR $id < 1) die( "error" );

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {

	die ("error");
	
}
	
if( $_REQUEST['action'] == "plus" ) {
	$error = "";
	
	$list = explode( ",", $member_id['favorites'] );
	
	foreach ( $list as $daten ) {

		if( $daten == $id ) $error = "stop";

	}
	
	if( $error != "stop" ) {

		$list[] = $id;
		$favorites = $db->safesql(implode( ",", $list ));
		
		if( $member_id['favorites'] == "" ) $favorites = $id;
		
		$member_id['favorites'] = $favorites;
		
		$db->query( "UPDATE " . USERPREFIX . "_users SET favorites='{$favorites}' WHERE user_id = '{$member_id['user_id']}'" );
	
	}

	if ( $_REQUEST['alert'] ) $buffer = $lang['fav_plus'];
	else $buffer = "<img src=\"" . $config['http_home_url'] . "templates/{$config['skin']}/dleimages/minus_fav.gif\" onclick=\"doFavorites('" . $id . "', 'minus'); return false;\" title=\"" . $lang['news_minfav'] . "\" style=\"vertical-align: middle;border: none;\" />";

} elseif( $_REQUEST['action'] == "minus" ) {
	
	$list = explode( ",", $member_id['favorites'] );
	$i = 0;
	
	foreach ( $list as $daten ) {

		if( $daten == $id ) unset( $list[$i] );
		$i ++;

	}
	
	if( count( $list ) ) $member_id['favorites'] = $db->safesql(implode( ",", $list ));
	else $member_id['favorites'] = "";
	
	$db->query( "UPDATE " . USERPREFIX . "_users SET favorites='{$member_id['favorites']}' WHERE user_id = '{$member_id['user_id']}'" );

	if ( $_REQUEST['alert'] ) $buffer = $lang['fav_minus'];
	else $buffer = "<img src=\"" . $config['http_home_url'] . "templates/{$config['skin']}/dleimages/plus_fav.gif\" onclick=\"doFavorites('" . $id . "', 'plus'); return false;\" title=\"" . $lang['news_addfav'] . "\" style=\"vertical-align: middle;border: none;\" />";

} else die( "error" );

$db->close();

echo $buffer;
?>