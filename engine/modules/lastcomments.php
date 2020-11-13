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
 File: lastcomments.php
-----------------------------------------------------
 Use: lastcomments
=====================================================
*/

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$userid = intval( $_REQUEST['userid'] );
$_SESSION['referrer'] = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, $config['charset'] );

$allow_list = explode( ',', $user_group[$member_id['user_group']]['allow_cats'] );
$not_allow_cats = explode ( ',', $user_group[$member_id['user_group']]['not_allow_cats'] );
$join = "";
$where = array ();

if( $userid ) {
	
	$where[] = "cm.user_id='{$userid}'";
	$user_query = "do=lastcomments&amp;userid=" . $userid;
	$canonical = $PHP_SELF."?do=lastcomments&userid=" . $userid;
	
} else {
	
	$user_query = "do=lastcomments";
	$canonical = $PHP_SELF."?do=lastcomments";
	
}

if( $allow_list[0] != "all" ) {
	
	$join = "LEFT JOIN " . PREFIX . "_post p ON cm.post_id=p.id ";
	
	if( $config['allow_multi_category'] ) {
		
		$join .= "INNER JOIN (SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN ('" . implode( "','", $allow_list ) . "')) c ON (p.id=c.news_id) ";
	
	} else {
		
		$where[] = "p.category IN ('" . implode( "','", $allow_list ) . "')";
	
	}

}

if( $not_allow_cats[0] != "" ) {
	
	$join = "LEFT JOIN " . PREFIX . "_post p ON cm.post_id=p.id ";
	
	if( $config['allow_multi_category'] ) {
		
		$where[] = "p.id NOT IN ( SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN ('" . implode( "','", $not_allow_cats ) . "') )";
	
	} else {
		
		$where[] = "p.category NOT IN ('" . implode( "','", $not_allow_cats ) . "')";
	
	}

}

if( $config['allow_cmod'] ) {
	
	$where[] = "cm.approve=1";

}

if( count( $where ) ) {
	
	$where = implode( " AND ", $where );
	$where = "WHERE " . $where;

} else $where = "";

if ( !defined('BANNERS') ) {
	if ($config['allow_banner']) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/banners.php'));
}

$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_comments cm " . $join . $where;
$row_count = $db->super_query( $sql_count );

if( $row_count['count'] ) {
		
	if ( isset( $_GET['cstart'] ) ) $fromcstart = intval( $_GET['cstart'] ); else $fromcstart = 0;

	if( $config['comm_nummers'] ) {
		
		$pages_count = @ceil( $row_count['count'] / intval($config['comm_nummers']) );
		
	} else $pages_count = 1;

	if($fromcstart AND $fromcstart > $pages_count) {
		
		@header( "HTTP/1.0 404 Not Found" );
		
		if( $config['own_404'] AND file_exists(ROOT_DIR . '/404.html') ) {
			@header("Content-type: text/html; charset=".$config['charset']);
			echo file_get_contents( ROOT_DIR . '/404.html' );
			die();
			
		} else msgbox( $lang['all_err_1'], $lang['news_err_27'] );
	
	} else {
		
		if( $fromcstart > 0) {
			$fromcstart = $fromcstart - 1;
			$fromcstart = $fromcstart * intval($config['comm_nummers']);
		} else $fromcstart = 0;
	
		include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/comments.class.php'));
	
		$comments = new DLE_Comments( $db, $row_count['count'], intval($config['comm_nummers']) );
	
		$comments->query = "SELECT cm.id, post_id, cm.user_id, cm.date, cm.autor as gast_name, cm.email as gast_email, text, ip, is_register, cm.rating, cm.vote_num, name, u.email, news_num, u.comm_num, user_group, lastdate, reg_date, signature, foto, fullname, land, u.xfields, p.title, p.date as newsdate, p.alt_name, p.category FROM " . PREFIX . "_comments cm LEFT JOIN " . PREFIX . "_post p ON cm.post_id=p.id LEFT JOIN " . USERPREFIX . "_users u ON cm.user_id=u.user_id INNER JOIN (SELECT cm.id FROM " . PREFIX . "_comments cm " . $join . $where . " ORDER BY id desc LIMIT ".$fromcstart.", ".intval($config['comm_nummers'])." ) as sub ON sub.id = cm.id ORDER BY id desc";

		$comments->build_comments('comments.tpl', 'lastcomments' );
	
		$comments->build_navigation('navigation.tpl', false, $user_query);
		
	}
	
	$category_id = false;
	
} else {

	msgbox( $lang['all_info'], $lang['err_last'] );

}

?>