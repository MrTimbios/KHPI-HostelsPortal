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
 File: complaint.php
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
	die ("error");
	
}

$parse = new ParseFilter();
$parse->safe_mode = true;
$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
$parse->allow_image = $user_group[$member_id['user_group']]['allow_image'];
$parse->allowbbcodes = false;

$id = intval( $_POST['id'] );
$text = strip_tags($_POST['text']);
$text = $parse->BB_Parse( $parse->process( trim( $text ) ), false );
$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " ", "&" );

if (strpos($config['http_home_url'], "//") === 0) $config['http_home_url'] = "https:".$config['http_home_url'];
elseif (strpos($config['http_home_url'], "/") === 0) $config['http_home_url'] = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

if ( $config['allow_complaint_mail'] ) {

	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/mail.class.php'));
	$mail = new dle_mail( $config );
	$lang['mail_complaint_1'] = str_replace( "{site}", $config['http_home_url'], $lang['mail_complaint_1'] );
}

if ($_POST['action'] == "pm") {

	if( !$is_logged ) die( "error" );

	if( !$id OR !$text) die( "error" );

	$row = $db->super_query( "SELECT id, text, user, user_from FROM " . USERPREFIX . "_pm WHERE id='{$id}'" );

	if( $row['user'] != $member_id['user_id'] OR !$row['id']) die("Operation not Allowed");

	if ($row['user_from'] == $member_id['name']) { echo $lang['error_complaint_2']; die(); }

	$db->query( "SELECT id FROM " . PREFIX . "_complaint WHERE p_id='{$id}'" );

	if ($db->num_rows()) { echo $lang['error_complaint_1']; die(); }

	$row['text'] = "<div class=\"quote\">".stripslashes( $row['text'] )."</div>";

	$text = $db->safesql( $row['text'].$text );
	$member_id['name'] = $db->safesql($member_id['name']);
	$row['user_from'] = $db->safesql($row['user_from']);

	$db->query( "INSERT INTO " . PREFIX . "_complaint (`p_id`, `c_id`, `n_id`, `text`, `from`, `to`, `date`) values ('{$row['id']}', '0', '0', '{$text}', '{$member_id['name']}', '{$row['user_from']}', '{$_TIME}')" );

	if ( $config['allow_complaint_mail'] ) {
		$mail->send( $config['admin_mail'], $lang['mail_complaint'], $lang['mail_complaint_1'] );	
	}

} elseif ($_POST['action'] == "comments") {

	if( !$is_logged ) {
		
		$author = $_IP;
		
		$db->query( "SELECT id FROM " . PREFIX . "_complaint WHERE `from`='{$author}'" );
		
		if ($db->num_rows() > 2) { echo $lang['error_complaint_1']; die(); }
		
	} else $author = $db->safesql($member_id['name']);

	if( !$id OR !$text) die( "error" );

	$row = $db->super_query( "SELECT id, autor FROM " . PREFIX . "_comments WHERE id='{$id}'" );

	if(!$row['id']) die("Operation not Allowed");

	if ($row['autor'] == $author) { echo $lang['error_complaint_2']; die(); }

	$db->query( "SELECT id FROM " . PREFIX . "_complaint WHERE c_id='{$id}' AND `from`='{$author}'" );

	if ($db->num_rows()) { echo $lang['error_complaint_1']; die(); }

	$text = $db->safesql( $text );
	
	if( !$is_logged AND $_POST['mail'] ) {
		
		$sender_mail = $db->safesql(trim( str_replace( $not_allow_symbol, '', strip_tags( stripslashes( $_POST['mail'] ) ) ) ) );
		
	} else $sender_mail = "";
	
	$db->query( "INSERT INTO " . PREFIX . "_complaint (`p_id`, `c_id`, `n_id`, `text`, `from`, `to`, `date`, `email`) values ('0', '{$row['id']}', '0', '{$text}', '{$author}', '', '{$_TIME}', '{$sender_mail}')" );

	if ( $config['allow_complaint_mail'] ) {
		$mail->send( $config['admin_mail'], $lang['mail_complaint'], $lang['mail_complaint_1'] );	
	}

} elseif ($_POST['action'] == "news") {

	if( !$is_logged ) {
		
		$author = $_IP;
		
		$db->query( "SELECT id FROM " . PREFIX . "_complaint WHERE `from`='{$author}'" );
		
		if ($db->num_rows() > 2) { echo $lang['error_complaint_1']; die(); }
		
	} else $author = $db->safesql($member_id['name']);

	if( !$id OR !$text) die( "error" );

	$row = $db->super_query( "SELECT id, autor FROM " . PREFIX . "_post WHERE id='{$id}'" );

	if(!$row['id']) die("Operation not Allowed");

	$db->query( "SELECT id FROM " . PREFIX . "_complaint WHERE n_id='{$id}' AND `from`='{$author}'" );

	if ($db->num_rows()) { echo $lang['error_complaint_1']; die(); }

	$text = $db->safesql( $text );

	if( !$is_logged AND $_POST['mail'] ) {
		
		$sender_mail = $db->safesql(trim( str_replace( $not_allow_symbol, '', strip_tags( stripslashes( $_POST['mail'] ) ) ) ) );
		
	} else $sender_mail = "";
	
	$db->query( "INSERT INTO " . PREFIX . "_complaint (`p_id`, `c_id`, `n_id`, `text`, `from`, `to`, `date`, `email`) values ('0', '0', '{$row['id']}', '{$text}', '{$author}', '', '{$_TIME}', '{$sender_mail}')" );

	if ( $config['allow_complaint_mail'] ) {
		$mail->send( $config['admin_mail'], $lang['mail_complaint'], $lang['mail_complaint_1'] );	
	}

} elseif ($_POST['action'] == "orfo") {

	if(!$text) die( "error" );

	$seltext = htmlspecialchars( $parse->process( trim( $_POST['seltext'] ) ), ENT_QUOTES, $config['charset'] );
	$url = $db->safesql( htmlspecialchars( $parse->clear_url( trim( $_POST['url'] ) ), ENT_QUOTES, $config['charset'] ) );

	if(!$seltext) die( "error" );

	if( !$is_logged ) $author = $_IP; else $author = $db->safesql($member_id['name']);
	
	if( !$is_logged AND $_POST['mail'] ) {
		
		$sender_mail = $db->safesql(trim( str_replace( $not_allow_symbol, '', strip_tags( stripslashes( $_POST['mail'] ) ) ) ) );
		
	} else $sender_mail = "";

	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_complaint WHERE p_id='0' AND c_id='0' AND n_id='0' AND `from`='{$author}'" );

	if ($row['count'] > 2 ) { echo $lang['error_complaint_1']; die(); }

	$seltext = "<div class=\"quote\">".stripslashes( $seltext )."</div>";
	$text = $db->safesql( $seltext.$text );
	
	$db->query( "INSERT INTO " . PREFIX . "_complaint (`p_id`, `c_id`, `n_id`, `text`, `from`, `to`, `date`, `email`) values ('0', '0', '0', '{$text}', '{$author}', '{$url}', '{$_TIME}', '{$sender_mail}')" );

	if ( $config['allow_complaint_mail'] ) {
		$mail->send( $config['admin_mail'], $lang['mail_complaint'], $lang['mail_complaint_1'] );	
	}

}

echo "ok";

?>