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
 File: twofactor.php
-----------------------------------------------------
 Use: Two-factor authentication
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !isset($_SESSION['twofactor_id']) OR !isset($_SESSION['twofactor_auth'])) {
	echo "{\"error\":true, \"errorinfo\":\" {$lang['twofactor_err_1']}\"}";
	die();
}

$_POST['pin'] = (string)$_POST['pin'];

if(!$_POST['pin']) {
	echo "{\"error\":true, \"errorinfo\":\" {$lang['twofactor_err_2']}\"}";
	die();
}

$user_id = intval($_SESSION['twofactor_id']);

if( !$user_id OR $user_id < 1 ) {
	echo "{\"error\":true, \"errorinfo\":\" {$lang['twofactor_err_1']}\"}";
	die();
}

$_IP = get_ip();
$_TIME = time ();
$thisdate = $_TIME-900;

$db->query( "DELETE FROM " . USERPREFIX . "_twofactor WHERE date < '$thisdate'" );

$member_id = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE user_id='{$user_id}'" );

if( $member_id['user_id'] AND $member_id['password'] AND $_SESSION['twofactor_auth'] AND md5($member_id['password']) == $_SESSION['twofactor_auth'] ) {

	$row = $db->super_query( "SELECT * FROM " . USERPREFIX . "_twofactor WHERE user_id='{$user_id}'" );
	
	if(!$row['id']) {
		
		$_SESSION['twofactor_id'] = 0;
		$_SESSION['twofactor_auth'] = "";
		
		unset($_SESSION['twofactor_id']);
		unset($_SESSION['twofactor_auth']);
		echo "{\"error\":true, \"errorinfo\":\" {$lang['twofactor_err_4']}\"}";
		die();
	}
	
	if( $row['pin'] !== $_POST['pin'] ) {
		
		$db->query( "UPDATE " . USERPREFIX . "_twofactor SET attempt=attempt+1 WHERE id='{$row['id']}'" );
		
		if ($user_group[$member_id['user_group']]['allow_admin']) {

			$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '99', '')" );	
			
		}
			
		$attempt = 2-$row['attempt'];
		
		if ($attempt < 1) {
			
			$db->query( "DELETE FROM " . USERPREFIX . "_twofactor WHERE id='{$row['id']}'" );
			
			$_SESSION['twofactor_id'] = 0;
			$_SESSION['twofactor_auth'] = "";
			unset($_SESSION['twofactor_id']);
			unset($_SESSION['twofactor_auth']);
			echo "{\"success\":true}";
			die();
		}
		
		$lang['twofactor_err_5'] = str_replace("{attempt}", $attempt, $lang['twofactor_err_5']);
		echo "{\"error\":true, \"errorinfo\":\" {$lang['twofactor_err_5']}\"}";
		die();
	}

	session_regenerate_id();

	$db->query( "DELETE FROM " . USERPREFIX . "_twofactor WHERE id='{$row['id']}'" );

	if ($user_group[$member_id['user_group']]['allow_admin']) {

		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '100', '')" );	
			
	}
		
	if ( $_SESSION['no_save_cookie'] ) {
	
		set_cookie( "dle_user_id", "", 0 );
		set_cookie( "dle_password", "", 0 );
	
	} else {			
	
		set_cookie( "dle_user_id", $member_id['user_id'], 365 );
		set_cookie( "dle_password", md5($member_id['password']), 365 );
	
	}

	$_SESSION['dle_user_id'] = $member_id['user_id'];
	$_SESSION['dle_password'] = md5($member_id['password']);
	$_SESSION['member_lasttime'] = $member_id['lastdate'];
	
	$_SESSION['twofactor_id'] = 0;
	$_SESSION['no_save_cookie'] = 0;
	$_SESSION['twofactor_auth'] = "";
	unset($_SESSION['twofactor_id']);
	unset($_SESSION['twofactor_auth']);
	unset($_SESSION['no_save_cookie']);
	echo "{\"success\":true}";
	die();
	
} else {
	
	$_SESSION['twofactor_id'] = 0;
	$_SESSION['twofactor_auth'] = "";
	
	unset($_SESSION['twofactor_id']);
	unset($_SESSION['twofactor_auth']);
	echo "{\"error\":true, \"errorinfo\":\" {$lang['twofactor_err_3']}\"}";
	die();
	
}


?>