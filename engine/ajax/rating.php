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
 File: rating.php
-----------------------------------------------------
 Use: AJAX rating news
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if ( !$config['allow_registration'] ) {
	$dle_login_hash = sha1( SECURE_AUTH_KEY . $_IP );
}

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
	echo "{\"error\":true, \"errorinfo\":\"{$lang['sess_error']}\"}";
	die();
}

if( ! $is_logged ) $member_id['user_group'] = 5;

if( ! $user_group[$member_id['user_group']]['allow_rating'] ) {
		echo "{\"error\":true, \"errorinfo\":\"{$lang['rating_error3']}\"}";
		die();
}

if( $_REQUEST['go_rate'] == "minus" ) $_REQUEST['go_rate'] = -1;
if( $_REQUEST['go_rate'] == "plus" ) $_REQUEST['go_rate'] = 1;

$go_rate = intval( $_REQUEST['go_rate'] );
$news_id = intval( $_REQUEST['news_id'] );

if ( !$config['rating_type'] ) {
	if( $go_rate > 5 or $go_rate < 1 ) $go_rate = false;
}

if ( $config['rating_type'] == "1" ) {
	$go_rate = 1;
}

if ( $config['rating_type'] == "2" OR $config['rating_type'] == "3") {
	if( $go_rate != 1 AND $go_rate != -1 ) $go_rate = false;
}

if( !$go_rate or !$news_id ) {
	echo "{\"error\":true, \"errorinfo\":\"{$lang['rating_error3']}\"}";
	die();
}

$member_id['name'] = $db->safesql($member_id['name']);

if( $is_logged ) $where = "`member` = '{$member_id['name']}'";
else $where = "ip ='{$_IP}'";

$row = $db->super_query( "SELECT news_id, rating FROM " . PREFIX . "_logs WHERE news_id ='{$news_id}' AND {$where}" );

if( !$row['news_id'] ) {

	$allrate = $db->super_query( "SELECT allow_rate, rating, user_id FROM " . PREFIX . "_post_extras WHERE news_id ='{$news_id}'" );
	
	if( $allrate['user_id'] == $member_id['user_id'] ) {
		
		$db->close();
		
		echo "{\"error\":true, \"errorinfo\":\"{$lang['rating_error1']}\"}";
		die();
	}
	
	if( !$allrate['allow_rate'] ) {
		
		$db->close();
		
		echo "{\"error\":true, \"errorinfo\":\"{$lang['rating_error3']}\"}";
		die();
	}
	
	if( $config['rating_type'] == "1" AND $allrate['rating'] < 0 ) {
		
		$db->query( "UPDATE " . PREFIX . "_post_extras SET rating='{$go_rate}', vote_num='1' WHERE news_id ='{$news_id}'" );
		
	} elseif ( !$config['rating_type'] AND $allrate['rating'] < 0 ) {
		
		$db->query( "UPDATE " . PREFIX . "_post_extras SET rating='{$go_rate}', vote_num='1' WHERE news_id ='{$news_id}'" );
		
	} else {
		
		$db->query( "UPDATE " . PREFIX . "_post_extras SET rating=rating+'{$go_rate}', vote_num=vote_num+1 WHERE news_id ='{$news_id}'" );
		
	}	

	if ( $db->get_affected_rows() )	{
		if( $is_logged ) $user_name = $member_id['name'];
		else $user_name = "noname";
		
		$db->query( "INSERT INTO " . PREFIX . "_logs (news_id, ip, `member`, rating) values ('{$news_id}', '{$_IP}', '{$user_name}', '{$go_rate}')" );

		if ( $config['allow_alt_url'] AND !$config['seo_type'] ) $cprefix = "full_"; else $cprefix = "full_".$news_id;	
	
		clear_cache( array( 'news_', $cprefix ) );

	}
	
} elseif( $row['rating'] AND $row['rating'] != $go_rate ) {
	
	$allrate = $db->super_query( "SELECT rating, user_id FROM " . PREFIX . "_post_extras WHERE news_id ='{$news_id}'" );
	
	if( $config['rating_type'] == "1" AND $allrate['rating'] < 0 ) {
		
		$db->query( "UPDATE " . PREFIX . "_post_extras SET rating='{$go_rate}', vote_num='1' WHERE news_id ='{$news_id}'" );
		
	} elseif ( !$config['rating_type'] AND $allrate['rating'] < 0 ) {
		
		$db->query( "UPDATE " . PREFIX . "_post_extras SET rating='{$go_rate}', vote_num='1' WHERE news_id ='{$news_id}'" );
		
	} else {
		
		$db->query( "UPDATE " . PREFIX . "_post_extras SET rating=rating-'{$row['rating']}' WHERE news_id ='{$news_id}'" );
		$db->query( "UPDATE " . PREFIX . "_post_extras SET rating=rating+'{$go_rate}' WHERE news_id ='{$news_id}'" );
		
	}
	
	$db->query( "UPDATE " . PREFIX . "_logs SET rating='{$go_rate}' WHERE news_id ='{$news_id}' AND {$where}" );
	
	if ( $config['allow_alt_url'] AND !$config['seo_type'] ) $cprefix = "full_"; else $cprefix = "full_".$news_id;
	clear_cache( array( 'news_', $cprefix ) );
	
} else {
	
	$db->close();
	
	echo "{\"error\":true, \"errorinfo\":\"{$lang['rating_error2']}\"}";
	die();
}

$row = $db->super_query( "SELECT news_id, rating, vote_num FROM " . PREFIX . "_post_extras WHERE news_id ='{$news_id}'" );

if ( $config['rating_type'] ) {
	$dislikes = ($row['vote_num'] - $row['rating'])/2;
	$likes = $row['vote_num'] - $dislikes;	
} else {
	$dislikes = 0;
	$likes = 0;	
}

$buffer = ShowRating( $row['news_id'], $row['rating'], $row['vote_num'], true );

$buffer = addcslashes($buffer, "\t\n\r\"\\/");

$buffer = htmlspecialchars("{\"success\":true, \"rating\":\"{$buffer}\", \"votenum\":\"{$row['vote_num']}\", \"likes\":\"{$likes}\", \"dislikes\":\"{$dislikes}\"}", ENT_NOQUOTES, $config['charset']);

$db->close();

echo $buffer;
?>