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
 File: sitelogin.php
-----------------------------------------------------
 Use: authorization of visitors to the site
=====================================================
*/

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$_IP = get_ip();
$_TIME = time();
$dle_login_hash = "";
$allow_login = true;

if( isset( $_REQUEST['action'] ) and $_REQUEST['action'] == "logout" ) {
	
	$dle_user_id = "";
	$dle_password = "";
	set_cookie( "dle_user_id", "", 0 );
	set_cookie( "dle_password", "", 0 );
	set_cookie( "dle_skin", "", 0 );
	set_cookie( "dle_newpm", "", 0 );
	set_cookie( "dle_hash", "", 0 );
	set_cookie( session_name(), "", 0 );
	@session_destroy();
	@session_unset();
	$is_logged = false;
	
	header( "Location: ".str_replace("index.php","",$_SERVER['PHP_SELF']) );
	die();
}

$is_logged = false;
$member_id = array ();
$attempt_login = false;

if( isset( $_POST['login'] ) AND $_POST['login_name'] AND $_POST['login_password'] AND $_POST['login'] == "submit" ) {

	$_POST['login_name'] = (string)$_POST['login_name'];
	$_POST['login_password'] = (string)$_POST['login_password'];
	
	if( strlen($_POST['login_password']) > 72 ) $_POST['login_password'] = substr($_POST['login_password'], 0, 72);

	if ($config['login_log']) $allow_login = check_allow_login ($_IP, $config['login_log']);

	$allow_user = true;

	if ($config['auth_metod']) {

		$_POST['login_name'] = $db->safesql(trim(str_replace( array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " ", "&" ), '', strip_tags( $_POST['login_name'] ) ) ) );
	
		if( !$_POST['login_name'] OR strlen( $_POST['login_name'] ) > 40 OR count(explode("@", $_POST['login_name'])) != 2) $allow_user = false;
		$where_name = "email='{$_POST['login_name']}'";

	} else {

		$_POST['login_name'] = $db->safesql( $_POST['login_name'] );
		
		if ( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $_POST['login_name']) ) $allow_user = false;
		$where_name = "name='{$_POST['login_name']}'";

	}
	
	if( $allow_login AND $allow_user) {
	
		$member_id = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE {$where_name}" );

		if( $member_id['user_id'] AND $member_id['password'] ) {
			
			if( is_md5hash( $member_id['password'] ) ) {
				
				if($member_id['password'] == md5( md5($_POST['login_password']) ) ) {
					$is_logged = true;
				}
				
			} else {
				
				if(password_verify($_POST['login_password'], $member_id['password'] ) ) {
					$is_logged = true;
				}
				
			}
			
		}

		if( $is_logged ) {

			session_regenerate_id();

			if ( password_needs_rehash($member_id['password'], PASSWORD_DEFAULT) ) {

				if ($config['charset'] == "utf-8" AND version_compare($config['version_id'], '11.2', '>=')) {
	
					$member_id['password'] = password_hash($_POST['login_password'], PASSWORD_DEFAULT);
					
					if( !$member_id['password'] ) {
						die("PHP extension Crypt must be loaded for password_hash to function");
					}
					
					$new_pass_hash = "password='".$db->safesql($member_id['password'])."', ";
					
				} else $new_pass_hash = "";
				
			} else $new_pass_hash = "";
			
			if(!$config['twofactor_auth'] OR !$member_id['twofactor_auth']) {
				
				if ( isset($_POST['login_not_save']) AND intval($_POST['login_not_save']) ) {
	
					set_cookie( "dle_user_id", "", 0 );
					set_cookie( "dle_password", "", 0 );
	
				} else {			
	
					set_cookie( "dle_user_id", $member_id['user_id'], 365 );
					set_cookie( "dle_password", md5($member_id['password']), 365 );
	
				}
				
			}
			
			if(!$config['twofactor_auth'] OR !$member_id['twofactor_auth']) {
				$_SESSION['dle_user_id'] = $member_id['user_id'];
				$_SESSION['dle_password'] = md5($member_id['password']);
				$_SESSION['member_lasttime'] = $member_id['lastdate'];
			}
			
			$member_id['lastdate'] = $_TIME;
			
			if($config['twofactor_auth'] AND $member_id['twofactor_auth']) {
				$config['ip_control'] = 2;
				$config['log_hash'] = 1;
			}
			
			if(function_exists('openssl_random_pseudo_bytes')) {
				
				$stronghash = md5(openssl_random_pseudo_bytes(15));
				
			} else $stronghash = md5(uniqid( mt_rand(), TRUE ));
				
			$salt = sha1( str_shuffle("abcdefghjkmnpqrstuvwxyz0123456789") . $stronghash );
			$hash = '';
				
			for($i = 0; $i < 9; $i ++) {
				$hash .= $salt[mt_rand( 0, 39 )];
			}
				
			$hash = md5( $hash );
			$member_id['hash'] = $hash;
			
			if( $config['log_hash'] ) {
				set_cookie( "dle_hash", $hash, 365 );
				$_COOKIE['dle_hash'] = $hash;
			}

			$db->query( "UPDATE LOW_PRIORITY " . USERPREFIX . "_users SET {$new_pass_hash}lastdate='{$_TIME}', hash='{$hash}', logged_ip='{$_IP}' WHERE user_id='{$member_id['user_id']}'" );
			
			if ($user_group[$member_id['user_group']]['allow_admin']) {
		
				$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '101', '')" );	
					
			}
	
			if($config['twofactor_auth'] AND $member_id['twofactor_auth']) {
				
				$is_logged = false;

				if(!$_SESSION['twofactor_auth']) {
					
					$_SESSION['twofactor_auth'] = md5($member_id['password']);
					$_SESSION['twofactor_id'] = $member_id['user_id'];
					
					if ( isset($_POST['login_not_save']) AND intval($_POST['login_not_save']) ) {
						$_SESSION['no_save_cookie'] = 1;
					}
					
					include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/mail.class.php'));
					
					$pin = generate_pin();
					
					$db->query( "DELETE FROM " . USERPREFIX . "_twofactor WHERE user_id='{$member_id['user_id']}'" );
					
					$db->query( "INSERT INTO " . USERPREFIX . "_twofactor (user_id, pin, date) values ('{$member_id['user_id']}', '{$pin}', '{$_TIME}')" );	
					
					$row = $db->super_query( "SELECT * FROM " . PREFIX . "_email WHERE name='twofactor' LIMIT 0,1" );
	
					$mail = new dle_mail( $config, $row['use_html'] );
	
					$row['template'] = stripslashes( $row['template'] );
					$row['template'] = str_replace( "{%username%}", $member_id['name'], $row['template'] );
					$row['template'] = str_replace( "{%pin%}", $pin, $row['template'] );
					$row['template'] = str_replace( "{%ip%}", $_IP, $row['template'] );
					
					$mail->send( $member_id['email'], $lang['twofactor_subj'], $row['template'] );
					
					unset($pin);
					unset($row);
					unset($mail);
					
				}
			
				$member_id = array ();

				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");

			} else {
				
				$attempt_login = true;
				
			}
		
		} else {

			$is_logged = false;
			$attempt_login = true;
			
			if ($member_id['user_id'] AND $user_group[$member_id['user_group']]['allow_admin']) {

				$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '91', '')" );	
			
			}

			$member_id = array ();

		}

	}


} elseif( isset( $_SESSION['dle_user_id'] ) AND  intval( $_SESSION['dle_user_id'] ) > 0 AND $_SESSION['dle_password'] ) {
	
		$attempt_login = true;
		
		$member_id = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE user_id='" . intval( $_SESSION['dle_user_id'] ) . "'" );
		
		if( $member_id['user_id'] AND $member_id['password'] AND md5($member_id['password']) == $_SESSION['dle_password'] ) {
			
			$is_logged = true;
			
			if($config['twofactor_auth'] AND $member_id['twofactor_auth']) {
				$config['ip_control'] = 2;
				$config['log_hash'] = 1;
			}
			
		} else {
			
			$member_id = array ();
			$is_logged = false;
			if ($config['login_log']) $db->query( "INSERT INTO " . PREFIX . "_login_log (ip, count, date) VALUES('{$_IP}', '1', '".time()."') ON DUPLICATE KEY UPDATE count=count+1, date='".time()."'" );
		}

		if( $config['log_hash'] AND (($_COOKIE['dle_hash'] != $member_id['hash']) OR ($member_id['hash'] == "")) ) {
			
			$member_id = array ();
			$is_logged = false;
		
		}
		
} elseif( isset( $_COOKIE['dle_user_id'] ) AND intval( $_COOKIE['dle_user_id'] ) > 0 AND $_COOKIE['dle_password']) {
	
	$attempt_login = true;
		
	if ($config['login_log']) $allow_login = check_allow_login ($_IP, $config['login_log']);

	if ( $allow_login ) {
	
		$member_id = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE user_id='" . intval( $_COOKIE['dle_user_id'] ) . "'" );
		
		if( $member_id['user_id'] AND $member_id['password'] AND md5($member_id['password']) == $_COOKIE['dle_password'] ) {
			
			$is_logged = TRUE;

			session_regenerate_id();			

			$_SESSION['dle_user_id'] = $member_id['user_id'];
			$_SESSION['dle_password'] = md5($member_id['password']);
			$_SESSION['member_lasttime'] = $member_id['lastdate'];
			
			if($config['twofactor_auth'] AND $member_id['twofactor_auth']) {
				$config['ip_control'] = 2;
				$config['log_hash'] = 1;
			}
		
		} else {

			if ($member_id['user_id'] AND $user_group[$member_id['user_group']]['allow_admin']) {

				$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '92', '')" );	
			
			}
			
			$member_id = array ();
			$is_logged = false;
			
			if ($config['login_log']) $db->query( "INSERT INTO " . PREFIX . "_login_log (ip, count, date) VALUES('{$_IP}', '1', '".time()."') ON DUPLICATE KEY UPDATE count=count+1, date='".time()."'" );
		
		}

		if( $config['log_hash'] AND (($_COOKIE['dle_hash'] != $member_id['hash']) OR ($member_id['hash'] == "")) ) {
			
			$member_id = array ();
			$is_logged = false;
		
		}

	}

}

if( isset( $_POST['login'] ) AND !$is_logged AND $allow_login AND !$_SESSION['twofactor_auth'] ) {
	
	if ($config['login_log']) $db->query( "INSERT INTO " . PREFIX . "_login_log (ip, count, date) VALUES('{$_IP}', '1', '".time()."') ON DUPLICATE KEY UPDATE count=count+1, date='".time()."'" );

	if (function_exists('msgbox')) {
		if ($config['auth_metod']) msgbox( $lang['login_err'], $lang['login_err_3'] ); else msgbox( $lang['login_err'], $lang['login_err_1'] );
	}

}

if ( !$allow_login ) {
	if (function_exists('msgbox')) {
		$lang['login_err_2'] = str_replace("{time}", $config['login_ban_timeout'], $lang['login_err_2']);
		msgbox( $lang['login_err'], $lang['login_err_2'] );
	}
}

if( $is_logged ) {

	if($config['online_status']) $stime = 1200; else $stime = 14400;

	if( ($member_id['lastdate'] + $stime) < $_TIME ) {
			
		$db->query( "UPDATE LOW_PRIORITY " . USERPREFIX . "_users SET lastdate='{$_TIME}' WHERE user_id='{$member_id['user_id']}'" );
		
	}
	
	if( !allowed_ip( $member_id['allowed_ip'] ) ) {
		
		$is_logged = false;
		if (function_exists('msgbox')) {		
			msgbox( $lang['login_err'], $lang['ip_block_login'] );
		}	
	}
	
	if( $config['ip_control'] == '2' AND !check_netz( $member_id['logged_ip'], $_IP ) AND !isset( $_POST['login'] ) ) $is_logged = false;
	elseif( $config['ip_control'] == '1' AND $user_group[$member_id['user_group']]['allow_admin'] AND !check_netz( $member_id['logged_ip'], $_IP ) and !isset( $_POST['login'] ) ) $is_logged = false;

}

if ( $is_logged ) {
	
	$dle_login_hash = sha1( SECURE_AUTH_KEY . $member_id['user_id'] . sha1($member_id['password']) . $member_id['hash'] );

	if ( $user_group[$member_id['user_group']]['time_limit'] ) {
		if ($member_id['time_limit'] != "" and (intval ( $member_id['time_limit'] ) < $_TIME)) {
	
			$db->query ( "UPDATE " . USERPREFIX . "_users SET user_group='{$user_group[$member_id['user_group']]['rid']}', time_limit='' WHERE user_id='{$member_id['user_id']}'" );
			$member_id['user_group'] = $user_group[$member_id['user_group']]['rid'];
	
		}
	}
	
	if ( $user_group[$member_id['user_group']]['force_reg'] AND $user_group[$member_id['user_group']]['force_reg_days'] > 0) {
		
		if( $_TIME > ($member_id['reg_date'] + (86400 * $user_group[$member_id['user_group']]['force_reg_days'])) ) {

			$db->query ( "UPDATE " . USERPREFIX . "_users SET user_group='{$user_group[$member_id['user_group']]['force_reg_group']}' WHERE user_id='{$member_id['user_id']}'" );
			$member_id['user_group'] = $user_group[$member_id['user_group']]['force_reg_group'];

		}

	}

	if ( $user_group[$member_id['user_group']]['force_news'] AND $user_group[$member_id['user_group']]['force_news_count'] > 0) {

		if($member_id['news_num']) {
			
			$approved = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE autor='{$member_id['name']}' AND approve = '0'" );

			if( ($member_id['news_num'] - $approved['count']) >= $user_group[$member_id['user_group']]['force_news_count'] ) {
		
				$db->query ( "UPDATE " . USERPREFIX . "_users SET user_group='{$user_group[$member_id['user_group']]['force_news_group']}' WHERE user_id='{$member_id['user_id']}'" );
				$member_id['user_group'] = $user_group[$member_id['user_group']]['force_news_group'];
		
			}
		
		}
	}
	
	if ( $user_group[$member_id['user_group']]['force_comments'] AND $user_group[$member_id['user_group']]['force_comments_count'] > 0) {

		if($member_id['comm_num']) {
			
			if( $config['allow_cmod'] and $user_group[$member_id['user_group']]['allow_modc'] ) {
				
				$approved = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_comments WHERE user_id='{$member_id['user_id']}' AND approve = '0'" );
				
			} else { $approved = array('count' => 0); }

			if( ($member_id['comm_num'] - $approved['count']) >= $user_group[$member_id['user_group']]['force_comments_count'] ) {
		
				$db->query ( "UPDATE " . USERPREFIX . "_users SET user_group='{$user_group[$member_id['user_group']]['force_comments_group']}' WHERE user_id='{$member_id['user_id']}'" );
				$member_id['user_group'] = $user_group[$member_id['user_group']]['force_comments_group'];
		
			}
		
		}
	}
	
	if ( $user_group[$member_id['user_group']]['force_rating'] AND $user_group[$member_id['user_group']]['force_rating_count'] > 0) {
		
		$userrating = $db->super_query( "SELECT SUM(rating) as rating FROM " . PREFIX . "_post_extras WHERE user_id ='{$member_id['user_id']}'" );
		
		if( $userrating['rating'] >= $user_group[$member_id['user_group']]['force_rating_count'] ) {
		
			$db->query ( "UPDATE " . USERPREFIX . "_users SET user_group='{$user_group[$member_id['user_group']]['force_rating_group']}' WHERE user_id='{$member_id['user_id']}'" );
			$member_id['user_group'] = $user_group[$member_id['user_group']]['force_rating_group'];
		
		}
	}
	
} else {
	
	$member_id = array ();
	$dle_login_hash = sha1( SECURE_AUTH_KEY . $_IP );

}

if( !$is_logged AND $attempt_login) {
	
	set_cookie( "dle_user_id", "", 0 );
	set_cookie( "dle_password", "", 0 );
	set_cookie( "dle_hash", "", 0 );
	$_SESSION['dle_user_id'] = 0;
	$_SESSION['dle_password'] = "";

}

?>