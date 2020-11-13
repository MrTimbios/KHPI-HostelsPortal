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
 File: deletecomments.php
-----------------------------------------------------
 Use: comments delete
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$is_logged ) die( "error" );

$id = intval( $_REQUEST['id'] );

if( ! $id ) die( "error" );

$row = $db->super_query( "SELECT id, post_id, user_id, date, is_register FROM " . PREFIX . "_comments WHERE id = '{$id}'" );

if ($row['id'])	{

	$have_perm = false;
	$row['date'] = strtotime( $row['date'] );

	if( $_GET['dle_allow_hash'] != "" AND $_GET['dle_allow_hash'] == $dle_login_hash AND (($member_id['user_id'] == $row['user_id'] AND $row['is_register'] AND $user_group[$member_id['user_group']]['allow_delc']) OR $member_id['user_group'] == '1' OR $user_group[$member_id['user_group']]['del_allc']) ) $have_perm = true;

	if ( $user_group[$member_id['user_group']]['edit_limit'] AND (($row['date'] + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) < $_TIME) ) {
		$have_perm = false;
	}

	if( $have_perm ) {
		deletecomments( $row['id'] );

		if ( $config['allow_alt_url'] AND !$config['seo_type'] ) $cprefix = "full_"; else $cprefix = "full_".$row['post_id'];

		clear_cache( array( 'news_', 'rss', 'comm_'.$row['post_id'], $cprefix ) );
		
		echo $row['id'];
	
	} else die( "error" );

} else die( "error" );
?>