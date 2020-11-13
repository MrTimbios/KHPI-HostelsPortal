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
 File: quote.php
-----------------------------------------------------
 Use: comments quote
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));

if ( !$config['allow_registration'] ) {
	$dle_login_hash = sha1( SECURE_AUTH_KEY . $_IP );
}

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
	die ("error");
}

$id = intval( $_GET['id'] );
$area = $_GET['area'];

if(!$id) die( "error" );

if( $config['allow_comments_wysiwyg'] > 0) {
	
	$allowed_tags = array ('div[style|class]', 'span[style|class]', 'p[style|class]', 'br', 'strong', 'em', 'ul', 'li', 'ol', 'b', 'u', 'i', 's' );
	
	if( $user_group[$member_id['user_group']]['allow_url'] ) $allowed_tags[] = 'a[href|target|style|class|title]';
	if( $user_group[$member_id['user_group']]['allow_image'] ) $allowed_tags[] = 'img[style|class|src|alt|width|height]';
	
	$parse = new ParseFilter( $allowed_tags );
	$parse->wysiwyg = true;
	
} else {
	$parse = new ParseFilter();
}

$parse->safe_mode = true;
$parse->remove_html = false;

$row = $db->super_query( "SELECT post_id, autor, text FROM " . PREFIX . "_comments WHERE id = '{$id}'" );

if (!$row['text']) die( "error" );

$row_news = $db->super_query( "SELECT allow_comm, approve, access FROM " . PREFIX . "_post LEFT JOIN " . PREFIX . "_post_extras ON (" . PREFIX . "_post.id=" . PREFIX . "_post_extras.news_id) WHERE id='{$row['post_id']}'" );
$options = news_permission( $row_news['access'] );

if( (!$user_group[$member_id['user_group']]['allow_addc'] and $options[$member_id['user_group']] != 2) or $options[$member_id['user_group']] == 1 ) die( "error" );

if( !$row_news['allow_comm'] OR !$row_news['approve'] ) {
	die( "error" );
}

if( $config['allow_comments_wysiwyg'] < 1 ) {
	
	$text = $parse->decodeBBCodes( $row['text'], false );
	$text = str_replace( "&#58;", ":", $text );
	$text = str_replace( "&#91;", "[", $text );
	$text = str_replace( "&#93;", "]", $text );
	$text = str_replace( "&#123;", "{", $text );
	$text = "[quote={$row['autor']}]{$text}[/quote]";

} else {
	
	$parse->wysiwyg = true;
	$text = $parse->decodeBBCodes( $row['text'], TRUE, $config['allow_comments_wysiwyg'] );
	$text = preg_replace('/<p[^>]*>/', '', $text); 
	$text = str_replace("</p>", "<br>", $text);	
	$text = preg_replace('/<div[^>]*>/', '', $text); 
	$text = str_replace("</div>", "<br>", $text);
	$text = str_replace( "\r", "", $text );
	$text = str_replace( "\n", "", $text );
	$text = trim($text);
	$text = "<div class=\"title_quote\">{$lang['i_quote']} {$row['autor']}</div><div class=\"quote\">{$text}</div>";

}

$text = preg_replace ( "#\[hide(.*?)\](.+?)\[/hide\]#is", "", $text );

echo $text;

?>