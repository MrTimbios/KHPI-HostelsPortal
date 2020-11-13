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
 File: ratingcomments.php
-----------------------------------------------------
 Use: AJAX rating for comments
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

if( ! $user_group[$member_id['user_group']]['allow_comments_rating'] ) {
		echo "{\"error\":true, \"errorinfo\":\"{$lang['rating_error3']}\"}";
		die();
}

if( $_REQUEST['go_rate'] == "minus" ) $_REQUEST['go_rate'] = -1;
if( $_REQUEST['go_rate'] == "plus" ) $_REQUEST['go_rate'] = 1;

$go_rate = intval( $_REQUEST['go_rate'] );
$c_id = intval( $_REQUEST['c_id'] );

if ( !$config['comments_rating_type'] ) {
	if( $go_rate > 5 or $go_rate < 1 ) $go_rate = false;
}

if ( $config['comments_rating_type'] == "1" ) {
	$go_rate = 1;
}

if ( $config['comments_rating_type'] == "2" OR $config['comments_rating_type'] == "3") {
	if( $go_rate != 1 AND $go_rate != -1 ) $go_rate = false;
}

if( !$go_rate or !$c_id ) {
	echo "{\"error\":true, \"errorinfo\":\"{$lang['rating_error3']}\"}";
	die();
}


$member_id['name'] = $db->safesql($member_id['name']);

if( $is_logged ) $where = "`member` = '{$member_id['name']}'";
else $where = "ip ='{$_IP}'";

$row = $db->super_query( "SELECT c_id, rating FROM " . PREFIX . "_comment_rating_log WHERE c_id ='{$c_id}' AND {$where}" );

if( !$row['c_id'] ) {

	$allrate = $db->super_query( "SELECT user_id, ip, rating FROM " . PREFIX . "_comments WHERE id ='{$c_id}'" );
	
	if( $is_logged AND $allrate['user_id'] == $member_id['user_id'] ) {
		
		$db->close();
		
		echo "{\"error\":true, \"errorinfo\":\"{$lang['rating_error4']}\"}";
		die();
	
	} elseif( !$is_logged AND $_IP == $allrate['ip'] ) {
		
		$db->close();
		
		echo "{\"error\":true, \"errorinfo\":\"{$lang['rating_error4']}\"}";
		die();
		
	}
	
	if( $config['comments_rating_type'] == "1" AND $allrate['rating'] < 0 ) {
		
		$db->query( "UPDATE " . PREFIX . "_comments SET rating='{$go_rate}', vote_num='1' WHERE id ='{$c_id}'" );
		
	} elseif ( !$config['comments_rating_type'] AND $allrate['rating'] < 0 ) {
		
		$db->query( "UPDATE " . PREFIX . "_comments SET rating='{$go_rate}', vote_num='1' WHERE id ='{$c_id}'" );
		
	} else {
		
		$db->query( "UPDATE " . PREFIX . "_comments SET rating=rating+'{$go_rate}', vote_num=vote_num+1 WHERE id ='{$c_id}'" );
		
	}
	
	if ( $db->get_affected_rows() )	{
		if( $is_logged ) $user_name = $member_id['name'];
		else $user_name = "noname";
		
		$db->query( "INSERT INTO " . PREFIX . "_comment_rating_log (`c_id`, `ip`, `member`, `rating`) values ('{$c_id}', '{$_IP}', '{$user_name}', '{$go_rate}')" );
	
		clear_cache( array( "comm_" ) );

	}
	
} elseif ( $row['rating'] AND $row['rating'] != $go_rate ) {
	
	$allrate = $db->super_query( "SELECT user_id, rating FROM " . PREFIX . "_comments WHERE id ='{$c_id}'" );

	if( $config['comments_rating_type'] == "1" AND $allrate['rating'] < 0 ) {
		
		$db->query( "UPDATE " . PREFIX . "_comments SET rating='{$go_rate}', vote_num='1' WHERE id ='{$c_id}'" );
		
	} elseif ( !$config['comments_rating_type'] AND $allrate['rating'] < 0 ) {
		
		$db->query( "UPDATE " . PREFIX . "_comments SET rating='{$go_rate}', vote_num='1' WHERE id ='{$c_id}'" );
		
	} else {
		
		$db->query( "UPDATE " . PREFIX . "_comments SET rating=rating-'{$row['rating']}' WHERE id ='{$c_id}'" );
		$db->query( "UPDATE " . PREFIX . "_comments SET rating=rating+'{$go_rate}' WHERE id ='{$c_id}'" );
		
	}
	
	$db->query( "UPDATE " . PREFIX . "_comment_rating_log SET rating='{$go_rate}' WHERE c_id ='{$c_id}' AND {$where}" );
	
} else {
	$db->close();
	
	echo "{\"error\":true, \"errorinfo\":\"{$lang['rating_error5']}\"}";
	die();	
}

$row = $db->super_query( "SELECT id, rating, vote_num FROM " . PREFIX . "_comments WHERE id ='$c_id'" );

if ( $config['comments_rating_type'] ) {
	$dislikes = ($row['vote_num'] - $row['rating'])/2;
	$likes = $row['vote_num'] - $dislikes;	
} else {
	$dislikes = 0;
	$likes = 0;	
}

$buffer = ShowCommentsRating( $row['id'], $row['rating'], $row['vote_num'], true );

$buffer = addcslashes($buffer, "\t\n\r\"\\/");

$buffer = htmlspecialchars("{\"success\":true, \"rating\":\"{$buffer}\", \"votenum\":\"{$row['vote_num']}\", \"likes\":\"{$likes}\", \"dislikes\":\"{$dislikes}\"}", ENT_NOQUOTES, $config['charset']);

$db->close();

echo $buffer;
?>