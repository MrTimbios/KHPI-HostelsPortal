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
 File: download.php
-----------------------------------------------------
 Use: Files download
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../' );
	die( "Hacking attempt!" );
}

function reset_url($url) {
	$url = (string)$url;
	
	$value = str_replace ( "http://", "", $url );
	$value = str_replace ( "https://", "", $value );
	$value = str_replace ( "www.", "", $value );
	$value = explode ( "/", $value );
	$value = reset ( $value );
	return $value;
}

function clear_url_dir($var) {
	$var = (string)$var;
	
	$var = str_ireplace( ".php", "", $var );
	$var = str_ireplace( ".php", ".ppp", $var );
	$var = trim( strip_tags( $var ) );
	$var = str_replace( "\\", "/", $var );
	$var = preg_replace( "/[^a-z0-9\/\_\-]+/mi", "", $var );
	return $var;
	
}

if ( $config['allow_registration'] ) {
	include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/sitelogin.php'));
}

if ( !$is_logged ) {
	$member_id['user_group'] = 5;
}

require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/download.class.php'));

$id = intval( $_REQUEST['id'] );
$viewonline = intval( $_REQUEST['viewonline'] );
$perm = true;
$onlineview_ext = array('doc', 'docx','odt','pdf','xls','xlsx');
$full_link = $config['http_home_url'];

if ($_REQUEST['area'] == "static") {
	
	$row = $db->super_query ( "SELECT static_id, name, onserver FROM " . PREFIX . "_static_files WHERE id ='{$id}'" );

	$row_news = $db->super_query ( "SELECT id, name FROM " . PREFIX . "_static WHERE id ='{$row['static_id']}'" );
	
	if ( $row_news['id'] ) {
		
		if( $config['allow_alt_url'] ) $full_link = $config['http_home_url'] . $row_news['name'] . ".html";
		else $full_link = $config['http_home_url'] . "index.php?do=static&page=" . $row_news['name'];
		
	} else {
		
		$perm = false;
		
	}
	
} else {
	
	$row = $db->super_query ( "SELECT news_id, name, onserver FROM " . PREFIX . "_files WHERE id ='{$id}'" );

	if ( $row['news_id'] AND !$viewonline ) {
		
		$row_news = $db->super_query ( "SELECT id, autor, date, category, alt_name, approve, access FROM " . PREFIX . "_post LEFT JOIN " . PREFIX . "_post_extras ON (" . PREFIX . "_post.id=" . PREFIX . "_post_extras.news_id) WHERE id ='{$row['news_id']}'" );
		
		if( $row_news['id'] ) {
			
			$row_news['date'] = strtotime( $row_news['date'] );
			
			if( $config['allow_alt_url'] ) {
				
				if( $config['seo_type'] == 1 OR $config['seo_type'] == 2  ) {
					
					if( $row_news['category'] and $config['seo_type'] == 2 ) {
						
						$full_link = $config['http_home_url'] . get_url( $row_news['category'] ) . "/" . $row_news['id'] . "-" . $row_news['alt_name'] . ".html";
					
					} else {
						
						$full_link = $config['http_home_url'] . $row_news['id'] . "-" . $row_news['alt_name'] . ".html";
					
					}
				
				} else {
					
					$full_link = $config['http_home_url'] . date( 'Y/m/d/', $row_news['date'] ) . $row_news['alt_name'] . ".html";
				}
			
			} else {
				
				$full_link = $config['http_home_url'] . "index.php?newsid=" . $row_news['id'];
			
			}
			
			$options = news_permission( $row_news['access'] );
			if( $options[$member_id['user_group']] AND $options[$member_id['user_group']] != 3 ) $perm = true;
			if( $options[$member_id['user_group']] == 3 ) $perm = false;
	
			if ($config['no_date'] AND !$config['news_future'] AND !$user_group[$member_id['user_group']]['allow_all_edit']) {
		
				if( $row_news['date'] > $_TIME ) {
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
			
			if( !$row_news['approve'] AND $member_id['name'] != $row_news['autor'] AND !$user_group[$member_id['user_group']]['allow_all_edit'] ) $perm = false;
			
		} else $perm = false;

	} elseif ( !$row['news_id'] ) {
		$perm = false;
	}

}

if ( !$row['name'] ) {
	header( "HTTP/1.1 403 Forbidden" );
	die ( "Access denied" );
}

if ( !$perm ) {
	header( "HTTP/1.1 403 Forbidden" );
	die ( "You don't have access to download this file" );
}

$url = @parse_url ( $row['onserver'] );

$file_path = dirname (clear_url_dir($url['path']));
$file_name = pathinfo($url['path']);
$file_ext  = totranslit($file_name['extension'], true, false);
$file_name = totranslit($file_name['basename'], false);

if ($file_path AND $file_path != ".") $file_name = $file_path."/".$file_name;

if( stripos ( $file_name, ".php" ) !== false ) {
	header( "HTTP/1.1 403 Forbidden" );
	die ( "Access denied" );
}

if ($viewonline == 1 AND in_array($file_ext, $onlineview_ext) ) {
	
    $config['files_antileech'] = false;
	$user_group[$member_id['user_group']]['files_max_speed'] = 0;
	$user_group[$member_id['user_group']]['allow_files'] = true;
	
}

if ( !$user_group[$member_id['user_group']]['allow_files'] ) {
	header( "HTTP/1.1 403 Forbidden" );
	die ( "Access denied" );
}

if ($config['files_antileech']) {
	
	$_SERVER['HTTP_REFERER'] = reset_url ( $_SERVER['HTTP_REFERER'] );
	$_SERVER['HTTP_HOST'] = reset_url ( $_SERVER['HTTP_HOST'] );

	if ($_SERVER['HTTP_HOST'] != $_SERVER['HTTP_REFERER']) {
		header( "HTTP/1.1 403 Forbidden" );
		header( "Location: " . $full_link );
		die ( "Access denied!!!<br /><br />Please visit <a href=\"{$config['http_home_url']}\">{$config['http_home_url']}</a>" );
	}

}

$file = new download ( ROOT_DIR . '/uploads/files/' . $file_name, $row['name'], $config['files_force'], intval($user_group[$member_id['user_group']]['files_max_speed']) );

if ($_REQUEST['area'] == "static") {
	
	if ($config['files_count'] AND !$file->range) {
		$db->query ( "UPDATE " . PREFIX . "_static_files SET dcount=dcount+1 WHERE id ='$id'" );
	}

} else {
	
	if ($config['files_count'] AND !$file->range) {
		$db->query ( "UPDATE " . PREFIX . "_files SET dcount=dcount+1 WHERE id ='$id'" );
	}

}

$db->close();
session_write_close();

$file->download_file();

die();
?>