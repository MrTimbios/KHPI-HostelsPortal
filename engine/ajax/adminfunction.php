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
 File: adminfunction.php
-----------------------------------------------------
 Use: Adminpanel AJAX functions
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}


if ($_REQUEST['action'] == "bannersviews") {

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}
	
	$ids = $uniq_ids_ip = $max_views = array();
	
	foreach ($_REQUEST['ids'] as $id) {

		$id = intval($id);

		if($id < 1 ) die ("error");

		$ids[$id] = $id;
		
	}
	
	if( !count($ids) ) die ("error");

	$db->query( "SELECT id, allow_views, max_views, views FROM " . PREFIX . "_banners WHERE id='".implode("' OR id='", $ids)."'" );
	
	while ( $row = $db->get_row() ) {
		if($row['allow_views'] == 2 ) $uniq_ids_ip[$row['id']] = $row['id'];
		if( $row['max_views'] ) $max_views[$row['id']] = array('max_views' => $row['max_views'], 'views' => $row['views']);
	}
	
	if( count($uniq_ids_ip) ) {
		$db->query( "SELECT bid FROM " . PREFIX . "_banners_logs WHERE (bid='".implode("' OR bid='", $uniq_ids_ip)."') AND ip='{$_IP}'" );
		while ( $row = $db->get_row() ) {
			unset($ids[$row['bid']]);
			unset($uniq_ids_ip[$row['bid']]);
		}
	}
	
	if( count($ids) ) {
		$db->query( "UPDATE " . PREFIX . "_banners SET views=views+1 WHERE id='".implode("' OR id='", $ids)."'" );
		foreach ($ids as $id) {
			if($max_views[$id]['max_views'] AND ($max_views[$id]['views']+1) >= $max_views[$id]['max_views'] ) {
				@unlink( ENGINE_DIR . '/cache/system/banners.php' );
			}
		}
	}
	
	if( count($uniq_ids_ip) ) {
		foreach ($uniq_ids_ip as $id) {
			$db->query( "INSERT INTO " . PREFIX . "_banners_logs (bid, ip) VALUES ('{$id}', '{$_IP}')" );
		}
	}
	
	die( "ok" );
}

if ($_REQUEST['action'] == "bannersclick") {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		die ("error");
	}
	
	$id = intval($_REQUEST['id']);
	
	if($id < 1 ) die ("error");
	
	$row = $db->super_query( "SELECT allow_counts, max_counts, clicks FROM " . PREFIX . "_banners WHERE id='{$id}'" );
	$max_counts = $row['max_counts'];
	$clicks = $row['clicks'];
	
	if( !$row['allow_counts'] ) die ("error");
	
	if( $row['allow_counts'] == 2 ) {
		
		$row = $db->super_query( "SELECT id, click FROM " . PREFIX . "_banners_logs WHERE bid='{$id}' AND ip='{$_IP}'" );
		
		if(!$row['click']) {
			$db->query( "UPDATE " . PREFIX . "_banners SET clicks=clicks+1 WHERE id='{$id}'" );
			
			if($max_counts AND ($clicks+1) >= $max_counts ) {
				@unlink( ENGINE_DIR . '/cache/system/banners.php' );
			}
		}
		
		if($row['id']) $db->query( "UPDATE " . PREFIX . "_banners_logs SET click='1' WHERE id='{$row['id']}'" );
		else $db->query( "INSERT INTO " . PREFIX . "_banners_logs (bid, click, ip) VALUES ('{$id}', '1', '{$_IP}')" );
		
	} else {
		
		$db->query( "UPDATE " . PREFIX . "_banners SET clicks=clicks+1 WHERE id='{$id}'" );
		
		if($max_counts AND ($clicks+1) >= $max_counts ) {
			@unlink( ENGINE_DIR . '/cache/system/banners.php' );
		}

	}
	
	die( "ok" );	
}

if( !$is_logged OR !$user_group[$member_id['user_group']]['allow_admin'] ) { die ("error"); }

$buffer = "";

function parseJsonArray($jsonArray, $parentID = 0)
{
  $return = array();
  foreach ($jsonArray as $subArray) {
     $returnSubSubArray = array();
     if (isset($subArray['children'])) {
       $returnSubSubArray = parseJsonArray($subArray['children'], $subArray['id']);
     }
     $return[] = array('id' => $subArray['id'], 'parentid' => $parentID);
     $return = array_merge($return, $returnSubSubArray);
  }

  return $return;
}

if ($_REQUEST['action'] == "relatedids") {

	if ( !$user_group[$member_id['user_group']]['admin_addnews'] ) die ("error");

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}

	$id = intval( $_REQUEST['id'] );
	
	if ($config['no_date'] AND !$config['news_future']) $where_date = " AND date < '" . date ( "Y-m-d H:i:s", time () ) . "'";
	else $where_date = "";
		
	$related_ids = array();
	
	if( strlen( $_REQUEST['full_txt'] ) < strlen( $_REQUEST['short_txt'] ) ) $body = $_REQUEST['short_txt'];
	else $body = $_REQUEST['full_txt'];
					
	$body = trim(strip_tags( stripslashes( $_REQUEST['title'] . " " . $body ) ));

	if( dle_strlen( $body, $config['charset'] ) > 1000 ) {
		$body = dle_substr( $body, 0, 1000, $config['charset'] );
	}
					
	$body = $db->safesql( $body );
					
	$config['related_number'] = intval( $config['related_number'] );
	if( $config['related_number'] < 1 ) $config['related_number'] = 5;
	
	$allowed_cats = array();
	
	foreach ($user_group as $value) {
		if ($value['allow_cats'] != "all" AND !$value['allow_short'] ) $allowed_cats[] = $db->safesql($value['allow_cats']);
	}

	$join_category = "";
	
	if (count($allowed_cats)) {

		$allowed_cats = implode(",", $allowed_cats);
		$allowed_cats = explode(",", $allowed_cats);
		$allowed_cats = array_unique($allowed_cats);
		sort($allowed_cats);
	
		if ($config['allow_multi_category']) {
		
			$join_category = "p INNER JOIN (SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN ('" . implode ( ',', $allowed_cats ) . "')) c ON (p.id=c.news_id) ";
			$allowed_cats = "";
		
		} else {
		
			$allowed_cats = "category IN ('" . implode ( "','", $allowed_cats ) . "') AND ";
			
		}

	} else $allowed_cats="";

	$not_allowed_cats = array();
	
	foreach ($user_group as $value) {
		if ($value['not_allow_cats'] != "" AND !$value['allow_short'] ) $not_allowed_cats[] = $db->safesql($value['not_allow_cats']);
	}
	
	if (count($not_allowed_cats)) {

		$not_allowed_cats = implode(",", $not_allowed_cats);
		$not_allowed_cats = explode(",", $not_allowed_cats);
		$not_allowed_cats = array_unique($not_allowed_cats);
		sort($not_allowed_cats);
	
		if ($config['allow_multi_category']) {
		
			$not_allowed_cats = "p.id NOT IN ( SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN (" . implode ( ',', $not_allowed_cats ) . ") ) AND ";
			$join_category = "p ";
		
		} else {
	
			$not_allowed_cats = "category NOT IN ('" . implode ( "','", $not_allowed_cats ) . "') AND ";
	
		}

	} else $not_allowed_cats="";
					
	
	if ( $id ) {
		$id = " AND id != {$id}";
	} else $id = '';
	
	$db->query( "SELECT id, MATCH (title, short_story, full_story, xfields) AGAINST ('{$body}') as score FROM " . PREFIX . "_post {$join_category}WHERE {$allowed_cats}{$not_allowed_cats}MATCH (title, short_story, full_story, xfields) AGAINST ('{$body}'){$id} AND approve=1" . $where_date . " ORDER BY score DESC LIMIT " . $config['related_number'] );

	while ( $related = $db->get_row() ) {
		$related_ids[] = $related['id'];
	}
	
	if ( count($related_ids) ) {
		$related_ids = implode(",",$related_ids);
	} else $related_ids = '';
	
	$buffer = $related_ids;

}

if ($_REQUEST['action'] == "newsspam") {

	if ( !$user_group[$member_id['user_group']]['allow_all_edit']) die ("error");

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}

	$id = intval( $_REQUEST['id'] );
	
	if( $id < 1 ) die( "error" );

	$row = $db->super_query( "SELECT id, autor, approve FROM " . PREFIX . "_post WHERE id = '{$id}'" );

	if ($row['id'])	{

		$author = $db->safesql($row['autor']);

		if( $row['approve'] ) die ("error");

		$row = $db->super_query( "SELECT user_id, user_group FROM " . USERPREFIX . "_users WHERE name = '{$author}'" );

		$user_id = intval($row['user_id']);

		if ($user_group[$row['user_group']]['allow_admin']) die ($lang['mark_spam_error']);

		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '87', '{$author}')" );

		$result = $db->query( "SELECT id FROM " . PREFIX . "_post WHERE autor='{$author}' AND approve='0'" );
			
		while ( $row = $db->get_array( $result ) ) {
			deletenewsbyid( $row['id'] );
		}

		$db->free( $result );
		$db->query( "UPDATE " . USERPREFIX . "_users SET restricted='3', restricted_days='0' WHERE user_id ='{$user_id}'" );
		clear_cache();
		$buffer = $lang['mark_spam_ok_2'];

	} else die ("error");

}


if ($_REQUEST['action'] == "clearpoll") {

	if ( !$user_group[$member_id['user_group']]['allow_all_edit']) die ("error");

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}

	$id = intval( $_REQUEST['id'] );
	
	if( $id < 1 ) die( "error" );
	
	$db->query( "UPDATE  " . PREFIX . "_poll SET  votes='0', answer='' WHERE news_id = '{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_poll_log WHERE news_id='{$id}'" );
	
	$buffer = $lang['clear_poll_2'];

}

if ($_REQUEST['action'] == "commentspublic") {

	if ( !$user_group[$member_id['user_group']]['admin_comments']) die ("error");

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}
	
	$c_id = intval( $_REQUEST['id'] );
	$post_id = intval( $_REQUEST['post_id'] );
	
	$db->query( "UPDATE " . PREFIX . "_comments SET approve='1' WHERE id='{$c_id}'" );
	$db->query( "UPDATE " . PREFIX . "_post SET comm_num=comm_num+1 WHERE id='{$post_id}'" );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '19', '')" );
	
	clear_cache();

	if ( $config['allow_subscribe'] ) {

		$row = $db->super_query( "SELECT autor, text, parent FROM " . PREFIX . "_comments WHERE id = '{$c_id}'" );

		$name = $row['autor'];
		$body = $row['text'];
		$parent = $row['parent'];
		
		include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/mail.class.php'));

		$row = $db->super_query( "SELECT id, short_story, title, date, alt_name, category FROM ".PREFIX."_post WHERE id = '{$post_id}'" );

		$row['date'] = strtotime( $row['date'] );
		$row['category'] = intval( $row['category'] );

		if( $config['allow_alt_url'] ) {
				
			if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
			
				if( $row['category'] and $config['seo_type'] == 2 ) {
					
					$full_link = $config['http_home_url'] . get_url( $row['category'] ) . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
					
				} else {
					
					$full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
					
				}
				
			} else {
				
				$full_link = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] ) . $row['alt_name'] . ".html";
			}
			
		} else {
				
			$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
			
		}
	
		$title = stripslashes($row['title']);
		
		$row = $db->super_query( "SELECT * FROM " . PREFIX . "_email WHERE name='comments' LIMIT 0,1" );
		$mail = new dle_mail( $config, $row['use_html'] );

		if (strpos($full_link, "//") === 0) $full_link = "http:".$full_link;
		elseif (strpos($full_link, "/") === 0) $full_link = "http://".$_SERVER['HTTP_HOST'].$full_link;

		$row['template'] = stripslashes( $row['template'] );
		$row['template'] = str_replace( "{%username%}", $name, $row['template'] );
		$row['template'] = str_replace( "{%date%}", langdate( "j F Y H:i", $_TIME, true ), $row['template'] );
		$row['template'] = str_replace( "{%link%}", $full_link, $row['template'] );
		$row['template'] = str_replace( "{%title%}", $title, $row['template'] );

		$body = str_replace( '\n', "", $body );
		$body = str_replace( '\r', "", $body );
			
		$body = stripslashes( stripslashes( $body ) );
		$body = str_replace( "<br />", "\n", $body );
		$body = strip_tags( $body );
			
		if( $row['use_html'] ) {
			$body = str_replace("\n", "<br />", $body );
		}
					
		$row['template'] = str_replace( "{%text%}", $body, $row['template'] );
		$row['template'] = str_replace( "{%ip%}", "--", $row['template'] );
		
		$found_news_author_subscribe = false;
		$found_reply_author_subscribe = false;
		
		$news_author_subscribe = $db->super_query( "SELECT " . USERPREFIX . "_users.user_id, " . USERPREFIX . "_users.name, " . USERPREFIX . "_users.email, " . USERPREFIX . "_users.news_subscribe FROM " . PREFIX . "_post_extras LEFT JOIN " . USERPREFIX . "_users ON " . PREFIX . "_post_extras.user_id=" . USERPREFIX . "_users.user_id WHERE " . PREFIX . "_post_extras.news_id='{$post_id}'" );
		
		if( $parent ) {
			$reply_author_subscribe = $db->super_query( "SELECT " . USERPREFIX . "_users.user_id, " . USERPREFIX . "_users.name, " . USERPREFIX . "_users.email, " . USERPREFIX . "_users.comments_reply_subscribe FROM " . PREFIX . "_comments LEFT JOIN " . USERPREFIX . "_users ON " . PREFIX . "_comments.user_id=" . USERPREFIX . "_users.user_id WHERE " . PREFIX . "_comments.id='{$parent}'" );
		} else $reply_author_subscribe = array();	

		if (strpos($config['http_home_url'], "//") === 0) $slink = "https:".$config['http_home_url'];
		elseif (strpos($config['http_home_url'], "/") === 0) $slink = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'];
		else $slink = $config['http_home_url'];
				
		$db->query( "SELECT user_id, name, email, hash FROM " . PREFIX . "_subscribe WHERE news_id='{$post_id}'" );

		while($rec = $db->get_row())
		{
			if( $rec['user_id'] == $news_author_subscribe['user_id'] ) {
				$found_news_author_subscribe = true;
			}
				
			if( $parent AND $rec['user_id'] == $reply_author_subscribe['user_id'] ) {
				$found_reply_author_subscribe = true;
			}
				
			if ($rec['user_id'] != $member_id['user_id'] ) {
		
				$body = str_replace( "{%username_to%}", $rec['name'], $row['template'] );
				$body = str_replace( "{%unsubscribe%}", $slink . "index.php?do=unsubscribe&post_id=" . $post_id . "&user_id=" . $rec['user_id'] . "&hash=" . $rec['hash'], $body );
				$mail->send( $rec['email'], $lang['mail_comments'], $body );

			}

		}

		if($news_author_subscribe['news_subscribe'] AND !$found_news_author_subscribe) {
			
			$body = str_replace( "{%username_to%}", $news_author_subscribe['name'], $row['template'] );
			
			if ($config['allow_alt_url']) {
				$body = str_replace( "{%unsubscribe%}", $slink . "user/" . urlencode ( $news_author_subscribe['name'] ) . "/", $body );
			} else {
				$body = str_replace( "{%unsubscribe%}", $slink . "index.php??subaction=userinfo&user=" . urlencode ( $news_author_subscribe['name'] ), $body );
			}
			
			$mail->send( $news_author_subscribe['email'], $lang['mail_comments'], $body );
			
			$last_send = $news_author_subscribe['user_id'];
			
		} else $last_send = false;
		
		if($parent AND $reply_author_subscribe['comments_reply_subscribe'] AND !$found_reply_author_subscribe AND $reply_author_subscribe['user_id'] != $last_send) {
			
			$body = str_replace( "{%username_to%}", $reply_author_subscribe['name'], $row['template'] );
			
			if ($config['allow_alt_url']) {
				$body = str_replace( "{%unsubscribe%}", $slink . "user/" . urlencode ( $reply_author_subscribe['name'] ) . "/", $body );
			} else {
				$body = str_replace( "{%unsubscribe%}", $slink . "index.php??subaction=userinfo&user=" . urlencode ( $reply_author_subscribe['name'] ), $body );
			}
			
			$mail->send( $reply_author_subscribe['email'], $lang['mail_comments'], $body );
		}

		$db->free();
	}
	
	$buffer = 'ok';	
}

if ($_REQUEST['action'] == "commentsspam") {

	if ( !$user_group[$member_id['user_group']]['del_allc']) die ("error");

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}

	$id = intval( $_REQUEST['id'] );
	
	if( $id < 1 ) die( "error" );

	$row = $db->super_query( "SELECT id, user_id, autor, email, ip, is_register FROM " . PREFIX . "_comments WHERE id = '{$id}'" );

	if ($row['id'])	{

		$user_id = intval($row['user_id']);
		$author = $db->safesql($row['autor']);
		$email = $db->safesql($row['email']);
		$is_register = $row['is_register'];
		$ip = $db->safesql($row['ip']);

		if ( $is_register ) {

			$row = $db->super_query( "SELECT user_group FROM " . USERPREFIX . "_users WHERE user_id = '{$user_id}'" );

			if ($user_group[$row['user_group']]['allow_admin']) die ($lang['mark_spam_error']);

			$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '87', '{$author}')" );

			$db->query( "UPDATE " . USERPREFIX . "_users SET comm_num='0', restricted='3', restricted_days='0' WHERE user_id ='{$user_id}'" );
			
			deletecommentsbyuserid($user_id);


		} else {

			$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '88', '{$author}')" );

			deletecommentsbyuserid(0, $ip);

			$db->query( "INSERT INTO " . USERPREFIX . "_banned (descr, date, days, ip) values ('{$lang['mark_spam_ok_1']}', '0', '0', '{$ip}')" );
			@unlink( ENGINE_DIR . '/cache/system/banned.php' );

		}

		clear_cache();

		if ( $email AND strlen($config['spam_api_key']) > 3 ) {
		
			include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/stopspam.class.php'));
			$sfs = new StopSpam($config['spam_api_key'], $config['sec_addnews']);
			$args = array('ip_addr' => $ip, 'username' => $author, 'email' => $email );
			$sfs->add( $args );
		
		}

		$buffer = $lang['mark_spam_ok'];		

	} else die ("error");
}

if ($_REQUEST['action'] == "clearcache") {

	if ( $member_id['user_group'] != 1 ) die ("error");
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}
	
	clear_all_caches();

	$buffer = $lang['clear_cache'];

}


if ($_REQUEST['action'] == "clearsubscribe") {

	if ( $member_id['user_group'] != 1 ) die ("error");
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		die ("error");
	}

	$db->query("TRUNCATE TABLE " . PREFIX . "_subscribe");

	$buffer = $lang['clear_subscribe'];

}

if ($_REQUEST['action'] == "clearsubscribenews") {

	if ( $member_id['user_group'] != 1 ) die ("error");
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		die ("error");
	}
	
	$id = intval( $_REQUEST['id'] );
	
	if( $id < 1 ) die( "error" );
	
	$db->query( "DELETE FROM " . PREFIX . "_subscribe WHERE news_id='{$id}'" );

	$buffer = $lang['clear_subscribe'];

}

if ($_REQUEST['action'] == "sendnotice") {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}
	
	$row = $db->super_query( "SELECT id FROM " . PREFIX . "_notice WHERE user_id = '{$member_id['user_id']}'" );
	
	$notice = $_POST['notice'];
	
	$notice = $db->safesql( $notice );
	
	if( dle_strlen( $notice, $config['charset'] ) > 65000 ) {
		die( "error" );
	}
	
	if( $row['id'] ) {
		
		$db->query( "UPDATE " . PREFIX . "_notice SET notice='{$notice}' WHERE user_id = '{$member_id['user_id']}'" );
	
	} else {
		
		$db->query( "INSERT INTO " . PREFIX . "_notice (user_id, notice) values ('{$member_id['user_id']}', '{$notice}')" );
	
	}

	$buffer = $lang['saved'];

}

if ($_REQUEST['action'] == "savetheme") {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}
	
	$file = md5(SECURE_AUTH_KEY.$member_id['user_id']);
	
	if( !is_dir( ENGINE_DIR . "/cache/system/adminpanel" ) ) {
			
		@mkdir( ENGINE_DIR . "/cache/system/adminpanel", 0777 );
		@chmod( ENGINE_DIR . "/cache/system/adminpanel", 0777 );

	}
	
	if( !is_dir( ENGINE_DIR . "/cache/system/adminpanel") ) {

		echo "{\"error\":\"{$lang['stat_cache']}\"}";
		die();
	}
	
	if( !is_writable( ENGINE_DIR . "/cache/system/adminpanel" ) ) {

		echo "{\"error\":\"{$lang['upload_error_1']} /engine/cache/system/adminpanel/ {$lang['upload_error_2']}\"}";
		die();
		
	}

	if( file_exists( ENGINE_DIR . "/cache/system/adminpanel/" . $file ) AND !is_writable( ENGINE_DIR . "/cache/system/adminpanel/" . $file ) ) {
		
		$lang['stat_system'] = str_replace ("{file}", "/engine/cache/system/adminpanel/" . $file, $lang['stat_system']);
		
		echo "{\"error\":\"{$lang['stat_system']}\"}";
		die();
		
	}
	
	$allowed_themes = array('dle_theme_a', 'dle_theme_b','dle_theme_c','dle_theme_d','dle_theme_e','dle_theme_f','dle_theme_g','dle_theme_h','dle_theme_i','dle_theme_dark','sidebar-xs','layout-boxed','input-classic', 'auto_dark_theme');
	$theme = array();
	
	if( strpos ( $_REQUEST['theme'], "saved_dle_theme_" ) OR strpos ( $_REQUEST['theme'], "auto_night_mode" ) ) {
		$_REQUEST['theme'] = str_replace('saved_dle_theme_', 'dle_theme_', $_REQUEST['theme']);
		$_REQUEST['theme'] = str_replace('dle_theme_dark', '', $_REQUEST['theme']);
		$_REQUEST['theme'] = trim($_REQUEST['theme']);
	}
	
	$themes = explode(" ", $_REQUEST['theme']);
	
	foreach($themes as $temp) {
		if( trim($temp) AND in_array($temp, $allowed_themes) ) {
			$theme[] = trim($temp);
		}
	}
	
	if( count($theme) ) {
		
		$theme = implode(" ", $theme);
		
		file_put_contents (ENGINE_DIR . "/cache/system/adminpanel/" . $file, $theme, LOCK_EX);
		@chmod( ENGINE_DIR . "/cache/system/adminpanel/" . $file, 0666 );
	
	} else {
		
		@unlink( ENGINE_DIR . "/cache/system/adminpanel/" . $file );
		
	}
	
	echo "{\"ok\":\"ok\"}";
	die();
	
}

if ($_REQUEST['action'] == "deletemodules") {

	if ( $member_id['user_group'] != 1 ) die ("error");
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}
	
	$id = intval($_REQUEST['id']);

	if ( $id ) {
		$db->query( "DELETE FROM " . PREFIX . "_admin_sections WHERE id = '{$id}'" );
	
		$buffer = 'ok';
	}

}

if ($_REQUEST['action'] == "catsort") {

	if( !$user_group[$member_id['user_group']]['admin_categories'] ) die ("error");
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}

	$_POST['list'] = json_decode(stripslashes($_POST['list']), true);

	if ( !is_array($_POST['list']) ) die ("error");
	
	$_POST['list'] = parseJsonArray($_POST['list']);
	
	$i= 0;

	foreach ( $_POST['list'] as $value ) {
		$i++;

		$id = intval($value['id']);
		$parentid = intval($value['parentid']);
		
		if ( $id ) {

			$db->query( "UPDATE " . PREFIX . "_category SET parentid='{$parentid}', posi='{$i}' WHERE id = '{$id}'" );

		}
	}

	clear_all_caches();
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '11', '')" );

	$buffer = 'ok';

}

if ($_REQUEST['action'] == "catchangestatus") {

	if( !$user_group[$member_id['user_group']]['admin_categories'] ) die ("error");
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}

	$id = intval($_POST['id']);

	if( !$id OR $id < 1) {
		
		die ("error");
	
	}
	
	if( $_POST['status'] == 'off' ) {
		$db->query( "UPDATE " . PREFIX . "_category SET active='0' WHERE id = '{$id}'" );
		$logs = 120;
	} else {
		$db->query( "UPDATE " . PREFIX . "_category SET active='1' WHERE id = '{$id}'" );
		$logs = 121;
	}

	clear_all_caches();
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '{$logs}', '{$id}')" );

	$buffer = 'ok';

}

if ($_REQUEST['action'] == "pluginsort") {

	if( $member_id['user_group'] != 1 ) die ("error");
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}

	$_POST['list'] = json_decode(stripslashes($_POST['list']), true);

	if ( !is_array($_POST['list']) ) die ("error");
	
	$_POST['list'] = parseJsonArray($_POST['list']);
	
	$i= 0;

	foreach ( $_POST['list'] as $value ) {
		$i++;

		$id = intval($value['id']);
		
		if ( $id ) {

			$db->query( "UPDATE " . PREFIX . "_plugins SET posi='{$i}' WHERE id = '{$id}'" );

		}
	}

	clear_all_caches();
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '119', '')" );

	$buffer = 'ok';

}


if ($_REQUEST['action'] == "xfsort") {

	if( !$user_group[$member_id['user_group']]['admin_xfields'] ) die ("error");

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}

	$_POST['list'] = json_decode(stripslashes($_POST['list']), true);

	if ( !is_array($_POST['list']) ) die ("error");
	
	$_POST['list'] = parseJsonArray($_POST['list']);

	function xfieldssave($data) {
		global $config;
	
	    $data = array_values($data);
		$filecontents = "";
	
	    foreach ($data as $index => $value) {
	      $value = array_values($value);
	      foreach ($value as $index2 => $value2) {
	        $value2 = stripslashes($value2);
	        $value2 = str_replace("|", "&#124;", $value2);
	        $value2 = str_replace("\r\n", "__NEWL__", $value2);
	        $filecontents .= $value2 . ($index2 < count($value) - 1 ? "|" : "");
	      }
	      $filecontents .= ($index < count($data) - 1 ? "\r\n" : "");
	    }
	
	    $filehandle = fopen(ENGINE_DIR.'/data/xfields.txt', "w+");
		
	    if (!$filehandle) die ("error");
	
		$filecontents = htmlspecialchars($filecontents, ENT_QUOTES, $config['charset'] );
		$filecontents = str_replace("&amp;#124;", "&#124;", $filecontents);

	    fwrite($filehandle, $filecontents);
	    fclose($filehandle);

	}

	$xfields = xfieldsload();
	$temp_array = array();

	foreach ( $_POST['list'] as $value ) {

		$id = intval($value['id']);
		$temp_array[] = $xfields[$id];		

	}

	$xfields = $temp_array;

	xfieldssave($xfields);

	$buffer = 'ok';

}

if ($_REQUEST['action'] == "userxfsort") {

	if( !$user_group[$member_id['user_group']]['admin_userfields'] ) die ("error");

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}

	$_POST['list'] = json_decode(stripslashes($_POST['list']), true);

	if ( !is_array($_POST['list']) ) die ("error");
	
	$_POST['list'] = parseJsonArray($_POST['list']);

	function profileload() {

	  $path = ENGINE_DIR.'/data/xprofile.txt';
	  $filecontents = file($path);
	
	    if (!is_array($filecontents)) die ("error");
	  
	    foreach ($filecontents as $name => $value) {
	      $filecontents[$name] = explode("|", trim($value));
	      foreach ($filecontents[$name] as $name2 => $value2) {
	        $value2 = str_replace("&#124;", "|", $value2); 
	        $value2 = str_replace("__NEWL__", "\r\n", $value2);
	        $filecontents[$name][$name2] = $value2;
	      }
	    }
	    return $filecontents;
	}


	function profilesave($data) {
	
	    $data = array_values($data);
		$filecontents = "";
	
	    foreach ($data as $index => $value) {
	      $value = array_values($value);
	      foreach ($value as $index2 => $value2) {
	        $value2 = stripslashes($value2);
	        $value2 = str_replace("|", "&#124;", $value2);
	        $value2 = str_replace("\r\n", "__NEWL__", $value2);
	        $filecontents .= $value2 . ($index2 < count($value) - 1 ? "|" : "");
	      }
	      $filecontents .= ($index < count($data) - 1 ? "\r\n" : "");
	    }
	  
	    $filehandle = fopen(ENGINE_DIR.'/data/xprofile.txt', "w+");
	    if (!$filehandle) die ("error");
	
		$find = array ('/data:/i','/about:/i','/vbscript:/i','/onclick/i','/onload/i','/onunload/i','/onabort/i','/onerror/i','/onblur/i','/onchange/i','/onfocus/i','/onreset/i','/onsubmit/i','/ondblclick/i','/onkeydown/i','/onkeypress/i','/onkeyup/i','/onmousedown/i','/onmouseup/i','/onmouseover/i','/onmouseout/i','/onselect/i','/javascript/i','/onmouseenter/i','/onwheel/i','/onshow/i','/onafterprint/i','/onbeforeprint/i','/onbeforeunload/i','/onhashchange/i','/onmessage/i','/ononline/i','/onoffline/i','/onpagehide/i','/onpageshow/i','/onpopstate/i','/onresize/i','/onstorage/i','/oncontextmenu/i','/oninvalid/i','/oninput/i','/onsearch/i','/ondrag/i','/ondragend/i','/ondragenter/i','/ondragleave/i','/ondragover/i','/ondragstart/i','/ondrop/i','/onmousemove/i','/onmousewheel/i','/onscroll/i','/oncopy/i','/oncut/i','/onpaste/i','/oncanplay/i','/oncanplaythrough/i','/oncuechange/i','/ondurationchange/i','/onemptied/i','/onended/i','/onloadeddata/i','/onloadedmetadata/i','/onloadstart/i','/onpause/i','/onprogress/i',	'/onratechange/i','/onseeked/i','/onseeking/i','/onstalled/i','/onsuspend/i','/ontimeupdate/i','/onvolumechange/i','/onwaiting/i','/ontoggle/i');
		$replace = array ("d&#1072;ta:", "&#1072;bout:", "vbscript<b></b>:", "&#111;nclick", "&#111;nload", "&#111;nunload", "&#111;nabort", "&#111;nerror", "&#111;nblur", "&#111;nchange", "&#111;nfocus", "&#111;nreset", "&#111;nsubmit", "&#111;ndblclick", "&#111;nkeydown", "&#111;nkeypress", "&#111;nkeyup", "&#111;nmousedown", "&#111;nmouseup", "&#111;nmouseover", "&#111;nmouseout", "&#111;nselect", "j&#1072;vascript", '&#111;nmouseenter', '&#111;nwheel', '&#111;nshow', '&#111;nafterprint','&#111;nbeforeprint','&#111;nbeforeunload','&#111;nhashchange','&#111;nmessage','&#111;nonline','&#111;noffline','&#111;npagehide','&#111;npageshow','&#111;npopstate','&#111;nresize','&#111;nstorage','&#111;ncontextmenu','&#111;ninvalid','&#111;ninput','&#111;nsearch','&#111;ndrag','&#111;ndragend','&#111;ndragenter','&#111;ndragleave','&#111;ndragover','&#111;ndragstart','&#111;ndrop','&#111;nmousemove','&#111;nmousewheel','&#111;nscroll','&#111;ncopy','&#111;ncut','&#111;npaste','&#111;ncanplay','&#111;ncanplaythrough','&#111;ncuechange','&#111;ndurationchange','&#111;nemptied','&#111;nended','&#111;nloadeddata','&#111;nloadedmetadata','&#111;nloadstart','&#111;npause','&#111;nprogress',	'&#111;nratechange','&#111;nseeked','&#111;nseeking','&#111;nstalled','&#111;nsuspend','&#111;ntimeupdate','&#111;nvolumechange','&#111;nwaiting','&#111;ntoggle');
		
		$filecontents = preg_replace( $find, $replace, $filecontents );
		$filecontents = preg_replace( "#<iframe#i", "&lt;iframe", $filecontents );
		$filecontents = preg_replace( "#<script#i", "&lt;script", $filecontents );
		$filecontents = str_replace( "<?", "&lt;?", $filecontents );
		$filecontents = str_replace( "?>", "?&gt;", $filecontents );
		$filecontents = str_replace( "$", "&#036;", $filecontents );
	
	    fwrite($filehandle, $filecontents);
	    fclose($filehandle);
	}

	$xfields = profileload();

	$temp_array = array();

	foreach ( $_POST['list'] as $value ) {

		$id = intval($value['id']);
		$temp_array[] = $xfields[$id];		

	}

	$xfields = $temp_array;
	profilesave($xfields);

	$buffer = 'ok';
}

echo $buffer;

?>