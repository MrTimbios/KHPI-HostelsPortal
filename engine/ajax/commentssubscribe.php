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
 File: commentssubscribe.php
-----------------------------------------------------
 Use: Subscribe to comments
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$is_logged OR  !$user_group[$member_id['user_group']]['allow_subscribe'] OR !$config['allow_subscribe'] OR !$config['allow_comments']) {
	echo "{\"error\":true, \"errorinfo\":\" {$lang['subscribe_err_1']}\"}";
	die();
}

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
	
	echo "{\"error\":true, \"errorinfo\":\" {$lang['subscribe_err_2']}\"}";
	die();
	
}

$news_id = intval($_GET['news_id']);
$perm = true;
$_TIME = time();

if( !$news_id OR $news_id < 1) {
	echo "{\"error\":true, \"errorinfo\":\" {$lang['subscribe_err_3']}\"}";
	die();	
}

$row_news = $db->super_query ( "SELECT id, autor, date, category, allow_comm, approve, access FROM " . PREFIX . "_post LEFT JOIN " . PREFIX . "_post_extras ON (" . PREFIX . "_post.id=" . PREFIX . "_post_extras.news_id) WHERE id ='{$news_id}'" );

if( $row_news['id'] ) {
	$options = news_permission( $row_news['access'] );
	if( $options[$member_id['user_group']] AND $options[$member_id['user_group']] != 3 ) $perm = true;
	if( $options[$member_id['user_group']] == 3 ) $perm = false;
	
	if ($config['no_date'] AND !$config['news_future'] AND !$user_group[$member_id['user_group']]['allow_all_edit']) {
		
		if( strtotime($row_news['date']) > $_TIME ) {
			$perm = false;
		}
		
	}
	
	$cat_list = explode( ',', $row_news['category'] );
	
	if( count($cat_list) ) {
		
		$allow_list = explode( ',', $user_group[$member_id['user_group']]['allow_cats'] );
		$not_allow_cats = explode ( ',', $user_group[$member_id['user_group']]['not_allow_cats'] );
		
		foreach ( $cat_list as $element ) {
				
			if( $allow_list[0] != "all" AND !in_array( $element, $allow_list ) ) $perm = false;
			
			if( $not_allow_cats[0] != "" AND in_array( $element, $not_allow_cats ) ) $perm = false;
			
		}
				
	}
	
	if( !$row_news['allow_comm'] ) $perm = false;
	
	if( !$row_news['approve'] AND $member_id['name'] != $row_news['autor'] AND !$user_group[$member_id['user_group']]['allow_all_edit'] ) $perm = false;
			
} else $perm = false;

if( !$perm ) {
	echo "{\"error\":true, \"errorinfo\":\" {$lang['subscribe_err_3']}\"}";
	die();	
}

$found_subscribe = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_subscribe WHERE news_id='{$news_id}' AND user_id='{$member_id['user_id']}'" );
			
if( !$found_subscribe['count'] ) {
				
	if(function_exists('openssl_random_pseudo_bytes')) {
				
		$stronghash = md5(openssl_random_pseudo_bytes(15));
					
	} else $stronghash = md5(uniqid( mt_rand(), TRUE ));
	
	$salt = str_shuffle($stronghash);
	$s_hash = "";
				
	for($i = 0; $i < 10; $i ++) {
		$s_hash .= $salt[mt_rand( 0, 31 )];
	}
	
	$s_hash = md5($s_hash);
	
	$db->query( "INSERT INTO " . PREFIX . "_subscribe (user_id, name, email, news_id, hash) values ('{$member_id['user_id']}', '{$member_id['name']}', '{$member_id['email']}', '{$news_id}', '{$s_hash}')" );

	echo "{\"success\":true, \"info\":\" {$lang['subscribe_info_1']}\"}";

} else {
	
	echo "{\"success\":true, \"info\":\" {$lang['subscribe_info_2']}\"}";
	
}

?>