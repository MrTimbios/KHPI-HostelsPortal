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
 File: find_relates.php
-----------------------------------------------------
 Use: Search for relates news
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$is_logged ) die( "error" );

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
	die( "error" );
}

$title = $db->safesql( trim( $_POST['title'] ) );

if( $title == "" ) die();

$buffer = "";

$id = intval( $_POST['id'] );
$mode = intval( $_POST['mode'] );

if ( $mode ) {
	if( !$user_group[$member_id['user_group']]['allow_adds'] ) die( "error" );
} else {
	if( !$user_group[$member_id['user_group']]['allow_admin'] ) die( "error" );
}

if( $id ) $where = " AND id != '" . $id . "'";
else $where = "";

$db->query( "SELECT id, title, date, category, alt_name, MATCH (title, short_story, full_story, xfields) AGAINST ('$title') as score FROM " . PREFIX . "_post WHERE MATCH (title, short_story, full_story, xfields) AGAINST ('$title') AND approve='1'" . $where . " ORDER BY score DESC, date DESC LIMIT 5" );

while ( $related = $db->get_row() ) {
	
	$related['date'] = strtotime( $related['date'] );
	$related['category'] = intval( $related['category'] );
	$news_date = date( 'd-m-Y', $related['date'] );
	
	if( $config['allow_alt_url'] ) {
		
		if( $config['seo_type'] == 1 OR  $config['seo_type'] == 2 ) {
			
			if( $related['category'] and $config['seo_type'] == 2 ) {
				
				$full_link = $config['http_home_url'] . get_url( $related['category'] ) . "/" . $related['id'] . "-" . $related['alt_name'] . ".html";
			
			} else {
				
				$full_link = $config['http_home_url'] . $related['id'] . "-" . $related['alt_name'] . ".html";
			
			}
		
		} else {
			
			$full_link = $config['http_home_url'] . date( 'Y/m/d/', $related['date'] ) . $related['alt_name'] . ".html";
		}
	
	} else {
		
		$full_link = $config['http_home_url'] . "index.php?newsid=" . $related['id'];
	
	}

	if ( dle_strlen($related['title'], $config['charset']) > 65 ) $related['title'] = dle_substr ($related['title'], 0, 65, $config['charset'])." ...";

	if ( $user_group[$member_id['user_group']]['allow_all_edit'] ) {

		$d_link = "<a title=\"{$lang['edit_rel']}\" href=\"?mod=editnews&action=editnews&id={$related['id']}\" target=\"_blank\"><i class=\"fa fa-pencil-square-o position-left\"></i></a><a title=\"{$lang['edit_seldel']}\" onclick=\"confirmDelete('?mod=editnews&action=doeditnews&ifdelete=yes&id={$related['id']}&user_hash={$dle_login_hash}', '{$related['id']}'); return false;\" href=\"?mod=editnews&action=doeditnews&ifdelete=yes&id={$related['id']}&user_hash={$dle_login_hash}\" target=\"_blank\"><i class=\"fa fa-trash-o position-left text-danger\"></i></a>";

	} else $d_link = "";

	if ( $mode ) $d_link = "";
	
	$buffer .= "<div style=\"padding:2px;\">{$d_link}{$news_date} - <a href=\"" . $full_link . "\" target=\"_blank\">" . stripslashes( $related['title'] ) . "</a></div>";

}

$db->close();

if( $buffer ) echo "<div class=\"findrelated\">" . $buffer . "</div>";
else echo "<div class=\"findrelated\">" . $lang['related_not_found'] . "</div>";

?>