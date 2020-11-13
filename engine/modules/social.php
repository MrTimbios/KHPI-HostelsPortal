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
 File: social.php
-----------------------------------------------------
 Use: Authorization through social networks
=====================================================
*/

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( $_SESSION['referrer'] ) {
	$root_href = $_SESSION['referrer'];
} else {
	$root_href = str_replace("index.php","",$_SERVER['PHP_SELF']);
}

$root_href = str_replace("&amp;","&", $root_href );

@header("Content-type: text/html; charset=".$config['charset']);

if (strpos($config['http_home_url'], "//") === 0) $config['http_home_url'] = "https:".$config['http_home_url'];
elseif (strpos($config['http_home_url'], "/") === 0) $config['http_home_url'] = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

$popup = <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>{$config['home_title']}</title>
<meta http-equiv="Content-Type" content="text/html; charset={$config['charset']}" />
<style type="text/css">
<!--
body {
	font-family: -apple-system,BlinkMacSystemFont,"Segoe UI","Roboto","Oxygen","Ubuntu","Cantarell","Fira Sans","Droid Sans","Helvetica Neue",sans-serif;
    font-size: 13px;
    line-height: 1.4285715;
	color: #000000;
	background:#ededed;
}
p {padding:0;margin:0}
.form-wrapper{margin-left:auto;margin-top:3em;margin-right:auto;}
.form-mail{width: 450px;background:#fff;box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);margin:0 auto;}
.form-mail p.register-info{background-color: #1976d2;border-color: #1976d2;box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);color:#fff;padding:8px 15px;margin-bottom: 10px;}
.form-mail p.register-submit{display:inline-block;float:right}
.form-mail p.register-submit > input{display: inline-block;width:auto;margin-bottom: 0;text-align:center;cursor: pointer;background-image: none;border: 1px solid transparent;white-space: nowrap;line-height: 1.6666667;font-size: 12px;padding: 4px 10px;background-color: #009688;border-color: #009688;color: #fff;border-radius: 3px;vertical-align: bottom;box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);margin-bottom:20px}
p input{width: 370px;margin: 10px 35px;font-size: 13px;line-height: 1.5384616;color: #333333;border: 1px solid #cccccc;padding: 3px 5px 3px 5px;box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);}
p input:focus{border:1px solid #b1acac;outline:none}
-->
</style>
</head>
<body>
{text}	
</body>
</html>
HTML;

$js_popup = <<<HTML
<script>
<!--

if(opener)
{
	window.opener.location.reload();
	window.close();

} else {

	window.location = '{$root_href}';
}
//-->
</script>
HTML;


function enter_mail ($info = "") {
	global $popup, $lang;

	$provider = totranslit( $_REQUEST['provider'] );

	if($provider != "od" AND $provider != "vk") {

			echo str_replace("{text}", $lang['reg_err_40'], $popup);
			die();

	}
	
	if($info) $info ="<ul>".$info."</ul>";

$form = <<<HTML
<div class="form-wrapper">
	<form action="?do=auth-social&sub=mail" method="post" class="form-mail">
		<input type="hidden" name="provider" value="{$provider}">
		<p class="register-info">{$lang['reg_err_37']}</p>
		<p><input type="text" name="email"></p>
		{$info}
		<p class="register-submit"><input type="submit" value="{$lang['social_next']}"></p>
	<div style="clear:both;"></div>
	</form>
</div>
HTML;

	echo str_replace("{text}", $form, $popup);
	die();
}

function check_email( $email ) {
	global $lang, $banned_info, $db, $config;
	$stop = "";

	if( empty( $email ) OR strlen( $email ) > 50 OR @count(explode("@", $email)) != 2) $stop .= $lang['reg_err_6'];

	if( count( $banned_info['email'] ) ) foreach ( $banned_info['email'] as $banned ) {
		
		$banned['email'] = str_replace( '\*', '.*', preg_quote( $banned['email'], "#" ) );
		
		if( $banned['email'] and preg_match( "#^{$banned['email']}$#iu", $email ) ) {
			
			if( $banned['descr'] ) {
				$lang['reg_err_23'] = str_replace( "{descr}", $lang['reg_err_22'], $lang['reg_err_23'] );
				$lang['reg_err_23'] = str_replace( "{descr}", $banned['descr'], $lang['reg_err_23'] );
			} else
				$lang['reg_err_23'] = str_replace( "{descr}", "", $lang['reg_err_23'] );

			$stop .= $lang['reg_err_23'];

		}
	}

	$email = $db->safesql($email);

	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_users WHERE email = '{$email}'" );
		
	if( $row['count'] ) {
		$stop .= $lang['reg_err_38'];
	}

	if( $stop ) return $stop; else return true;

}

function check_name( $name ) {
	global $db, $relates_word, $config;

	if( empty($name) ) return false;

	if( function_exists('mb_strtolower') ) {
		$name = mb_strtolower($name, $config['charset']);
	} else {
		$name = strtolower( $name );
	}

	$search_name = strtr( $name, $relates_word );

	$name = $db->safesql($name);
	$search_name = $db->safesql($search_name);

	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_users WHERE LOWER(name) REGEXP '^{$search_name}$' OR name = '{$name}'" );
		
	if( $row['count'] ) return false;
	
	return true;

}

function check_newlogin($name, $user_id) {
	global $lang, $db, $banned_info, $relates_word, $config;
	$stop = "";
	
	if( dle_strlen( $name, $config['charset'] ) > 40 OR dle_strlen(trim($name), $config['charset']) < 3) $stop .= $lang['reg_err_3'];
	if( preg_match( "/[\||\'|\<|\>|\[|\]|\%|\"|\!|\?|\$|\@|\#|\/|\\\|\&\~\*\{\}\+]/", $name ) ) $stop .= $lang['reg_err_4'];

	if (strpos( strtolower($name) , '.php' ) !== false) $stop .= $lang['reg_err_4'];
	
	if( count( $banned_info['name'] ) ) foreach ( $banned_info['name'] as $banned ) {

		$banned['name'] = str_replace( '\*', '.*', preg_quote( $banned['name'], "#" ) );

		if( $banned['name'] and preg_match( "#^{$banned['name']}$#iu", $name ) ) {

			if( $banned['descr'] ) {
				$lang['reg_err_21'] = str_replace( "{descr}", $lang['reg_err_22'], $lang['reg_err_21'] );
				$lang['reg_err_21'] = str_replace( "{descr}", $banned['descr'], $lang['reg_err_21'] );
			} else
				$lang['reg_err_21'] = str_replace( "{descr}", "", $lang['reg_err_21'] );

			$stop .= $lang['reg_err_21'];
		}
	}
	
	if( $stop == "" ) {
		if( function_exists('mb_strtolower') ) {
			$name = trim(mb_strtolower($name, $config['charset']));
		} else {
			$name = trim(strtolower( $name ));
		}
		$search_name = strtr( $name, $relates_word );
		
		$name = $db->safesql($name);
		$search_name = $db->safesql($search_name);
		$user_id = intval($user_id);
		
		$row = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_users WHERE user_id != '{$user_id}' AND (LOWER(name) REGEXP '^{$search_name}$' OR name = '$name')" );

		if( $row['count'] ) $stop .= $lang['reg_err_44'];
	}

	return $stop;

}

function check_registration($name, $email, $social_user) {
	global $lang, $db, $banned_info, $config, $popup;
	
	$stop = "";
	$_IP = get_ip();

	if( empty($name) OR preg_match( "/[\||\'|\<|\>|\[|\]|\%|\"|\!|\?|\$|\@|\#|\/|\\\|\&\~\*\{\}\+]/", $name ) OR dle_strlen( $name, $config['charset'] ) > 40 ) return false;
	if( empty($email) OR strlen($email) > 50 OR @count(explode("@", $email)) != 2) return false;
	if (strpos( strtolower($name) , '.php' ) !== false) return false;

	if( $config['max_users'] > 0 ) {
	
		$row = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_users" );
	
		if ( $row['count'] >= $config['max_users'] ) {
	
				echo str_replace("{text}", $lang['reg_err_10'], $popup);
				die();
		}
	
	}

	if( is_array($banned_info['name']) AND count( $banned_info['name'] ) ) foreach ( $banned_info['name'] as $banned ) {
		
		$banned['name'] = str_replace( '\*', '.*', preg_quote( dle_strtolower($banned['name'], $config['charset']), "#" ) );
		
		if( $banned['name'] and preg_match( "#^{$banned['name']}$#iu", dle_strtolower($name, $config['charset']) ) ) {
			
			if( $banned['descr'] ) {
				$lang['reg_err_21'] = str_replace( "{descr}", $lang['reg_err_22'], $lang['reg_err_21'] );
				$lang['reg_err_21'] = str_replace( "{descr}", $banned['descr'], $lang['reg_err_21'] );
			} else
				$lang['reg_err_21'] = str_replace( "{descr}", "", $lang['reg_err_21'] );

			echo str_replace("{text}", $lang['reg_err_21'], $popup);
			die();

		}
	}
	
	if( is_array($banned_info['email']) AND count( $banned_info['email'] ) ) foreach ( $banned_info['email'] as $banned ) {
		
		$banned['email'] = str_replace( '\*', '.*', preg_quote( dle_strtolower($banned['email'], $config['charset']), "#" ) );
		
		if( $banned['email'] and preg_match( "#^{$banned['email']}$#iu", dle_strtolower($email, $config['charset']) ) ) {
			
			if( $banned['descr'] ) {
				$lang['reg_err_23'] = str_replace( "{descr}", $lang['reg_err_22'], $lang['reg_err_23'] );
				$lang['reg_err_23'] = str_replace( "{descr}", $banned['descr'], $lang['reg_err_23'] );
			} else
				$lang['reg_err_23'] = str_replace( "{descr}", "", $lang['reg_err_23'] );

			echo str_replace("{text}", $lang['reg_err_23'], $popup);
			die();

		}
	}

	$email = $db->safesql($email);

	$row = $db->super_query( "SELECT email, name, user_id, user_group  FROM " . USERPREFIX . "_users WHERE email = '{$email}'" );
		
	if( $row['user_id'] ) {
		
		if( $row['user_group'] == 1 AND !$config['allow_admin_social'] ) {
			
			echo str_replace("{text}", $lang['reg_err_42'], $popup);
			die();
			
		} else register_wait_user($social_user, $row['user_id'], $row['name'], $row['email'], 0, '' );
		
	}

	if( !$config['reg_multi_ip'] ) {
	
		$row = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_users WHERE logged_ip = '{$_IP}'" );
	
		if ( $row['count'] ) {
			echo str_replace("{text}", $lang['reg_err_26'], $popup);
			die();
		}
	
	}
	
	return true;

}

function GetRandInt($max){

	if(function_exists('openssl_random_pseudo_bytes')) {
	     do{
	         $result = floor($max*(hexdec(bin2hex(openssl_random_pseudo_bytes(4)))/0xffffffff));
	     }while($result == $max);
	} else {

		$result = mt_rand( 0, $max );
	}

    return $result;
}

function wait_login( $id, $key ) {
	global $db, $config, $user_group, $popup, $js_popup, $lang;
	
	$js_wait_login = <<<HTML
<script>
<!--

if(opener)
{
	window.opener.location = '{$_SERVER['PHP_SELF']}?do=auth-social&action=waitlogin&id={$id}&key={$key}';
	window.close();

} else {

	window.location = '{$_SERVER['PHP_SELF']}?do=auth-social&action=waitlogin&id={$id}&key={$key}';
}
//-->
</script>
HTML;

	echo str_replace("{text}", $lang['social_login_ok'].$js_wait_login, $popup);
	die();
}

function register_wait_user( $social_user, $user_id, $name, $email, $id, $key ) {
	global $db, $config, $user_group, $popup, $js_popup, $lang;
	
	$id = intval($id);
	
	if ( !$id ) {

		if( function_exists('openssl_random_pseudo_bytes') ) {
					
			$stronghash = openssl_random_pseudo_bytes(15);
				
		} else $stronghash = md5(uniqid( mt_rand(), TRUE ));
		
		$salt = str_shuffle("abchefghjkmnpqrstuvwxyz0123456789".sha1($stronghash. microtime()));
		
		$password = '';

		for($i = 0; $i < 11; $i ++) {
			$password .= $salt[GetRandInt(72)];
		}
	
		$password = md5($password);
		$key = $password;
		
		$db->query( "INSERT INTO " . USERPREFIX . "_social_login (sid, uid, password, provider, wait, waitlogin) VALUES ('{$social_user['sid']}', '{$user_id}', '{$password}', '{$social_user['provider']}', '1', '0')" );
		$id = $db->insert_id();
	
	}
	
	$link = $config['http_home_url'] . "index.php?do=auth-social&action=approve&id=" . $id . "&key=" . $key;
	
	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/mail.class.php'));

	$row = $db->super_query( "SELECT * FROM " . PREFIX . "_email WHERE name='wait_mail' LIMIT 0,1" );
	$mail = new dle_mail( $config, $row['use_html'] );

	$row['template'] = stripslashes( $row['template'] );
	$row['template'] = str_replace( "{%username%}", $name, $row['template'] );
	$row['template'] = str_replace( "{%link%}", $link, $row['template'] );
	$row['template'] = str_replace( "{%ip%}", get_ip(), $row['template'] );
	$row['template'] = str_replace( "{%network%}", $social_user['provider'], $row['template'] );
	
	$mail->send( $email, $lang['wait_subj'], $row['template'] );

	echo str_replace("{text}", $lang['reg_err_36'], $popup);
	die();
}

function register_user( $social_user ) {
	global $db, $config, $user_group, $popup, $js_popup, $lang;

	$add_time = time();
	$_IP = get_ip();
	if( intval( $config['reg_group'] ) < 3 ) $config['reg_group'] = 4;

	if( function_exists('openssl_random_pseudo_bytes') ) {
				
		$stronghash = openssl_random_pseudo_bytes(15);
			
	} else $stronghash = md5(uniqid( mt_rand(), TRUE ));
	
	$salt = str_shuffle("abchefghjkmnpqrstuvwxyz0123456789".sha1($stronghash. microtime()));
	
	$password = '';
	$hash = '';
	
	for($i = 0; $i < 11; $i ++) {
		$password .= $salt[GetRandInt(72)];
	}

	$password = password_hash($password, PASSWORD_DEFAULT);
	$key = md5($password);
	$password = $db->safesql($password);

	if( $config['log_hash'] ) {
		for($i = 0; $i < 9; $i ++) {
			$hash .= $salt[GetRandInt(72)];
		}	
	}

	$social_user['nickname'] = $db->safesql( $social_user['nickname'] );
	$social_user['email'] = $db->safesql( $social_user['email'] );
	$social_user['name'] = $db->safesql( $social_user['name'] );

	$db->query( "INSERT INTO " . USERPREFIX . "_users (name, password, email, reg_date, lastdate, user_group, info, signature, fullname, favorites, xfields, hash, logged_ip) VALUES ('{$social_user['nickname']}', '{$password}', '{$social_user['email']}', '{$add_time}', '{$add_time}', '{$config['reg_group']}', '', '', '{$social_user['name']}', '', '', '{$hash}', '{$_IP}')" );

	$id = $db->insert_id();

	$db->query( "INSERT INTO " . USERPREFIX . "_social_login (sid, uid, password, provider, wait, waitlogin) VALUES ('{$social_user['sid']}', '{$id}', '{$key}', '{$social_user['provider']}', '0', '1')" );

	$id_s_log = $db->insert_id();

	$_SESSION['state'] = 0;

	if( intval( $user_group[$config['reg_group']]['max_foto'] ) > 0 AND $social_user['avatar'] ) {

		include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/thumb.class.php'));

	    if( @copy($social_user['avatar'], ROOT_DIR . "/uploads/fotos/" . $id . ".jpg") ){

			$i_info = @getimagesize(ROOT_DIR . "/uploads/fotos/" . $id . ".jpg");
			
			if( in_array( $i_info[2], array (1, 2, 3 ) ) )	{

				@chmod( ROOT_DIR . "/uploads/fotos/" . $id . ".jpg", 0666 );
				$thumb = new thumbnail( ROOT_DIR . "/uploads/fotos/" . $id . ".jpg" );

				if( !$config['tinypng_avatar'] ) {
					$thumb->img['tinypng'] = false;
				}
				
				$thumb->img['tinypng_resize'] = true;
				$thumb->size_auto( $user_group[$config['reg_group']]['max_foto'] );
				$thumb->jpeg_quality( $config['jpeg_quality'] );
				$thumb->save( ROOT_DIR . "/uploads/fotos/foto_" . $id . ".jpg" );
	
				@unlink( ROOT_DIR . "/uploads/fotos/" . $id . ".jpg" );
				
				if (strpos($config['http_home_url'], "//") === 0) $avatar_url = $config['http_home_url'];
				elseif (strpos($config['http_home_url'], "/") === 0) $avatar_url = "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];
				else $avatar_url = $config['http_home_url'];
				
				$avatar_url = str_ireplace("https:", "", $avatar_url);
				$avatar_url = str_ireplace("http:", "", $avatar_url);
						
				$foto_name = $db->safesql( $avatar_url . "uploads/fotos/" ."foto_" . $id . ".jpg" );
				
				$db->query( "UPDATE " . USERPREFIX . "_users SET foto='{$foto_name}' WHERE user_id='{$id}'" );
				
			} else {
				
				@unlink( ROOT_DIR . "/uploads/fotos/" . $id . ".jpg" );
				
			}
		}
	}

	$js_wait_login = <<<HTML
<script>
<!--

if(opener)
{
	window.opener.location = '{$_SERVER['PHP_SELF']}?do=auth-social&action=waitlogin&id={$id_s_log}&key={$key}';
	window.close();

} else {

	window.location = '{$_SERVER['PHP_SELF']}?do=auth-social&action=waitlogin&id={$id_s_log}&key={$key}';
}
//-->
</script>
HTML;

	echo str_replace("{text}", $lang['social_login_ok'].$js_wait_login, $popup);
	die();
}

if( isset($_GET['code']) AND $_GET['code'] AND !$is_logged AND $config['allow_social'] AND $config['allow_registration']) {

	if(!$_SESSION['state'] OR $_SESSION['state'] != $_GET['state']) {
	
		echo str_replace("{text}", $lang['reg_err_39'], $popup);
		die();
	
	}

	include_once (ENGINE_DIR . '/data/socialconfig.php');
	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/social.class.php'));

	$social = new SocialAuth( $social_config );

	$social_user = $social->getuser();

	if ( is_array($social_user) ) {

		session_regenerate_id();

		$social_user['sid'] = $db->safesql( $social_user['sid'] );

		$row = $db->super_query( "SELECT * FROM " . USERPREFIX . "_social_login WHERE sid='{$social_user['sid']}'" );

		if ( $row['id'] ) {

			if ( $row['uid'] ) {
				$_TIME = time();
				$_IP = get_ip();
				
				$member_id = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE user_id='{$row['uid']}'" );

				if( $member_id['user_id'] ) {
					
					if( $row['wait']  ) {
						register_wait_user($social_user, $member_id['user_id'], $member_id['name'], $member_id['email'], $row['id'], $row['password'] );
					}
					if( $row['waitlogin']  ) {
						wait_login($row['id'], $row['password'] );
					}
					
					if( $member_id['user_group'] == 1 AND !$config['allow_admin_social'] ) {
						echo str_replace("{text}", $lang['reg_err_42'], $popup);
						die();
					}
					
					set_cookie( "dle_user_id", $member_id['user_id'], 365 );
					set_cookie( "dle_password", md5($member_id['password']), 365 );
	
					$_SESSION['dle_user_id'] = $member_id['user_id'];
					$_SESSION['dle_password'] = md5($member_id['password']);
					$_SESSION['member_lasttime'] = $member_id['lastdate'];
					$_SESSION['state'] = 0;
					
					if($config['twofactor_auth'] AND $member_id['twofactor_auth']) {
						$config['log_hash'] = 1;
					}
					
					if( $config['log_hash'] ) {
		
						if(function_exists('openssl_random_pseudo_bytes')) {
						
							$stronghash = md5(openssl_random_pseudo_bytes(15));
						
						} else $stronghash = md5(uniqid( mt_rand(), TRUE ));
						
						$salt = sha1( str_shuffle("abcdefghjkmnpqrstuvwxyz0123456789") . $stronghash );
						$hash = '';
						
						for($i = 0; $i < 9; $i ++) {
							$hash .= $salt[mt_rand( 0, 39 )];
						}
						
						$hash = md5( $hash );
						
						$db->query( "UPDATE LOW_PRIORITY " . USERPREFIX . "_users SET hash='{$hash}', lastdate='{$_TIME}', logged_ip='{$_IP}' WHERE user_id='{$member_id['user_id']}'" );
						
						set_cookie( "dle_hash", $hash, 365 );
						
					
					} else $db->query( "UPDATE LOW_PRIORITY " . USERPREFIX . "_users SET lastdate='{$_TIME}', logged_ip='{$_IP}' WHERE user_id='{$member_id['user_id']}'" );

					echo str_replace("{text}", $lang['social_login_ok'].$js_popup, $popup);
					die();

				} else {
					$member_id = array();
					$is_logged = false;
					$db->query( "DELETE FROM " . USERPREFIX . "_social_login WHERE sid='{$social_user['sid']}'" );

				}

			}

		} else {

			if( empty($social_user['email']) ) enter_mail();

		    $i = 1;
		    $check_name = $social_user['nickname'];
		   
		    while (!check_name($check_name)){
		        $i++;
		        $check_name = $social_user['nickname'].'_'.$i;
		    }
		        
		    $social_user['nickname'] = $check_name;

			if ( check_registration( $social_user['nickname'], $social_user['email'], $social_user ) ) {

				register_user($social_user);

			}

		}

	} else {

		echo str_replace("{text}", $social_user, $popup);
		die();

	}

} elseif( isset($_GET['sub']) AND !$is_logged AND $config['allow_social'] AND $config['allow_registration']) {

	include_once (ENGINE_DIR . '/data/socialconfig.php');
	$url = false;

	$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " ", "&" );
	$_POST['email'] = str_replace( $not_allow_symbol, '',  $_POST['email']);

	$check = check_email( $_POST['email'] );

	if ( $check !== true ) {

		enter_mail($check);

	}

	if ( $_POST['provider'] == "od" AND $_SESSION['od_access_token'] ) {


		$url = $config['http_home_url'] . "index.php?do=auth-social&state={$_SESSION['state']}&provider=od&code={$_SESSION['od_access_code']}&email=".$_POST['email'];

	}

	if ( $_POST['provider'] == "vk" ) {

		$url = $config['http_home_url'] . "index.php?do=auth-social&state={$_SESSION['state']}&provider=vk&code={$_SESSION['vk_access_code']}&email=".$_POST['email'];

	}

	if($url) {

		header( "Location: {$url}" );
		die();

	} else {

		echo str_replace("{text}", $lang['reg_err_40'], $popup);
		die();
	}

} elseif( isset($_GET['action']) AND $_GET['action'] == 'waitlogin' AND $_GET['id'] AND $_GET['key'] AND !$is_logged AND $config['allow_social'] AND $config['allow_registration']) {
	
	$id = intval($_GET['id']);

	$row = $db->super_query( "SELECT * FROM " . USERPREFIX . "_social_login WHERE id='{$id}'" );
	
	if( $row['id'] AND $row['waitlogin'] AND $row['password'] != "" AND $_GET['key'] != "" AND $row['password'] == $_GET['key'] ) {
		
		$userdaten = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE user_id='{$row['uid']}'" );
		
		$login_name = $userdaten['name'];
		
		$lang['enter_login1'] = str_replace("{name}", $userdaten['name'],$lang['enter_login1']);
		
		if( $_POST['newlogin'] ) {
			
			$login_name = strtr(trim($_POST['newlogin']), array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES, $config['charset'])));
			$login_name = trim($login_name,chr(0xC2).chr(0xA0));
			$login_name = preg_replace('#\s+#u', ' ', $login_name);
			
			$login_name = htmlspecialchars($login_name, ENT_QUOTES, $config['charset'] );
	
			$reg_error = check_newlogin($login_name, $userdaten['user_id']);
			
			if($reg_error) {
				
				$lang['enter_login4'] = "<ul>".$reg_error."</ul>";
				
			} else {

				session_regenerate_id();
				
				$login_name = $db->safesql($login_name);
				
				$db->query( "UPDATE " . USERPREFIX . "_users SET name='{$login_name}' WHERE user_id='{$row['uid']}'" );
				$db->query( "UPDATE " . USERPREFIX . "_social_login SET waitlogin='0' WHERE id='{$row['id']}'" );
				
				$member_id = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE user_id='{$row['uid']}'" );
				
				if( $member_id['user_id'] ) {
					set_cookie( "dle_user_id", $member_id['user_id'], 365 );
					set_cookie( "dle_password", md5($member_id['password']), 365 );
	
					$_SESSION['dle_user_id'] = $member_id['user_id'];
					$_SESSION['dle_password'] = md5($member_id['password']);
					$_SESSION['member_lasttime'] = $member_id['lastdate'];
					$_SESSION['state'] = 0;
	
					if( $config['log_hash'] ) {
			
						if(function_exists('openssl_random_pseudo_bytes')) {
						
							$stronghash = md5(openssl_random_pseudo_bytes(15));
						
						} else $stronghash = md5(uniqid( mt_rand(), TRUE ));
							
						$salt = sha1( str_shuffle("abcdefghjkmnpqrstuvwxyz0123456789") . $stronghash );
						$hash = '';
						
						for($i = 0; $i < 9; $i ++) {
							$hash .= $salt[mt_rand( 0, 39 )];
						}
						
						$hash = md5( $hash );
						
						$db->query( "UPDATE " . USERPREFIX . "_users SET hash='{$hash}', lastdate='{$_TIME}', logged_ip='{$_IP}' WHERE user_id='{$member_id['user_id']}'" );
						
						set_cookie( "dle_hash", $hash, 365 );
					
					
					} else $db->query( "UPDATE LOW_PRIORITY " . USERPREFIX . "_users SET lastdate='{$_TIME}', logged_ip='{$_IP}' WHERE user_id='{$member_id['user_id']}'" );
					
				}
				
				header( "Location: {$root_href}" );
				die();
				
			}
			
		} else {
			$lang['enter_login4'] = $lang['enter_login4']."<br /><br />";
		}
		
		$form_login = <<<HTML
<form method="post">
{$lang['enter_login1']}
<br /><br />
{$lang['enter_login2']}
<br />
<input type="text" name="newlogin" id="newlogin" class="textin" style="width:200px" value="{$login_name}">
<br /><br />
{$lang['enter_login4']}
<input type="submit" class="bbcodes"  value="{$lang['enter_login3']}" />
</form>
HTML;

		msgbox( $lang['enter_login'], $form_login );
		
	} else {
		
		@header( "HTTP/1.0 404 Not Found" );
		
		if( $config['own_404'] AND file_exists(ROOT_DIR . '/404.html') ) {
			@header("Content-type: text/html; charset=".$config['charset']);
			echo file_get_contents( ROOT_DIR . '/404.html' );
			die();
			
		} else msgbox( $lang['all_err_1'], $lang['news_err_27'] );

	}

} elseif( isset($_GET['action']) AND $_GET['action'] == 'approve' AND $_GET['id'] AND $_GET['key'] AND !$is_logged AND $config['allow_social'] AND $config['allow_registration']) {

	$id = intval($_GET['id']);
	
	$row = $db->super_query( "SELECT * FROM " . USERPREFIX . "_social_login WHERE id='{$id}'" );
	
	if( $row['id'] AND $row['wait'] ) {
		
		if( $row['password'] != "" AND $_GET['key'] != "" AND $row['password'] == $_GET['key'] ) {
			session_regenerate_id();
			
			$db->query( "UPDATE " . USERPREFIX . "_social_login SET wait='0' WHERE id='{$row['id']}'" );
			
			$member_id = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE user_id='{$row['uid']}'" );
			
			if( $member_id['user_id'] ) {
				set_cookie( "dle_user_id", $member_id['user_id'], 365 );
				set_cookie( "dle_password", md5($member_id['password']), 365 );

				$_SESSION['dle_user_id'] = $member_id['user_id'];
				$_SESSION['dle_password'] = md5($member_id['password']);
				$_SESSION['member_lasttime'] = $member_id['lastdate'];
				$_SESSION['state'] = 0;

				if( $config['log_hash'] ) {
		
					if(function_exists('openssl_random_pseudo_bytes')) {
					
						$stronghash = md5(openssl_random_pseudo_bytes(15));
					
					} else $stronghash = md5(uniqid( mt_rand(), TRUE ));
						
					$salt = sha1( str_shuffle("abcdefghjkmnpqrstuvwxyz0123456789") . $stronghash );
					$hash = '';
					
					for($i = 0; $i < 9; $i ++) {
						$hash .= $salt[mt_rand( 0, 39 )];
					}
					
					$hash = md5( $hash );
					
					$db->query( "UPDATE " . USERPREFIX . "_users SET hash='{$hash}', lastdate='{$_TIME}', logged_ip='{$_IP}' WHERE user_id='{$member_id['user_id']}'" );
					
					set_cookie( "dle_hash", $hash, 365 );
				
				
				} else $db->query( "UPDATE LOW_PRIORITY " . USERPREFIX . "_users SET lastdate='{$_TIME}', logged_ip='{$_IP}' WHERE user_id='{$member_id['user_id']}'" );
			}
			
			msgbox( $lang['all_info'], $lang['auth_social_ok'] . " <a href=\"" . $root_href . "\">" . $lang['auth_next'] . "</a>" );

		} else {
			
			$db->query( "DELETE FROM " . USERPREFIX . "_social_login WHERE id='{$id}'" );
			
			@header( "HTTP/1.0 404 Not Found" );
			
			if( $config['own_404'] AND file_exists(ROOT_DIR . '/404.html') ) {
				@header("Content-type: text/html; charset=".$config['charset']);
				echo file_get_contents( ROOT_DIR . '/404.html' );
				die();
				
			} else msgbox( $lang['all_err_1'], $lang['reg_err_43'] );			
		}
		
	} else {
		
		@header( "HTTP/1.0 404 Not Found" );
		
		if( $config['own_404'] AND file_exists(ROOT_DIR . '/404.html') ) {
			@header("Content-type: text/html; charset=".$config['charset']);
			echo file_get_contents( ROOT_DIR . '/404.html' );
			die();
			
		} else msgbox( $lang['all_err_1'], $lang['reg_err_43'] );

	}
	
} else {

	@header( "HTTP/1.0 404 Not Found" );
	
	if( $config['own_404'] AND file_exists(ROOT_DIR . '/404.html') ) {
		
		@header("Content-type: text/html; charset=".$config['charset']);
		echo file_get_contents( ROOT_DIR . '/404.html' );
		die();
			
	} else msgbox( $lang['all_err_1'], $lang['news_err_27'] );

}

?>