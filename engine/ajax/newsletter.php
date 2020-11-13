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
 File: newsletter.php
-----------------------------------------------------
 Use: AJAX to send messages
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if (!$is_logged OR !$user_group[$member_id['user_group']]['admin_newsletter']) die ("error");

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {

	die ("error");
	
}

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));
$parse = new ParseFilter();
$parse->allow_code = false;

$startfrom = intval($_POST['startfrom']);

if ($_POST['empfanger']) {

	$empfanger = array(); 

	$temp = explode(",", $_POST['empfanger']); 

	foreach ( $temp as $value ) {
		$empfanger[] = intval($value);
	}

	$empfanger = implode( "','", $empfanger );

	$empfanger = "user_group IN ('" . $empfanger . "')";

} else $empfanger = false;

$type = $_POST['type'];
$a_mail = intval($_POST['a_mail']);
$limit = intval($_POST['limit']);
$fromregdate = intval($_POST['fromregdate']);
$toregdate = intval($_POST['toregdate']);
$fromentdate = intval($_POST['fromentdate']);
$toentdate = intval($_POST['toentdate']);
$step = 0;

$title = htmlspecialchars(strip_tags( trim( $_POST['title'] ) ), ENT_QUOTES, $config['charset'] );
$title = str_replace( "&amp;amp;", "&amp;", $title );

$message = stripslashes($parse->process($_POST['message']));

if (!$title OR !$message OR !$limit) die ("error");

$where = array();

$where[] = "banned != 'yes'";

if ($empfanger) $where[] = $empfanger;
if ($a_mail AND $type == "email") $where[] = "allow_mail = '1'";

if( $fromregdate ) {
	$where[] = "reg_date>='" . $fromregdate . "'";
}

if( $toregdate ) {
	$where[] = "reg_date<='" . $toregdate . "'";
}

if( $fromentdate ) {
	$where[] = "lastdate>='" . $fromentdate . "'";
}

if( $toentdate ) {
	$where[] = "lastdate<='" . $toentdate . "'";
}

$where = " WHERE ".implode (" AND ", $where);

if ($type == "pm") {

	$time = time();
	$title = $db->safesql($title);

	$result = $db->query("SELECT user_id, name, fullname FROM " . USERPREFIX . "_users".$where." LIMIT ".$startfrom.",".$limit);



	while($row = $db->get_row($result)) {
	
		if ( $row['fullname'] ) $message_send = str_replace("{%user%}", $row['fullname'], $message);
		else $message_send = str_replace("{%user%}", $row['name'], $message);
			
		$message_send = $db->safesql($message_send);
	
		$db->query("INSERT INTO " . USERPREFIX . "_pm (subj, text, user, user_from, date, pm_read, folder) values ('$title', '$message_send', '$row[user_id]', '$member_id[name]', '$time', 'no', 'inbox')");
		$db->query("UPDATE " . USERPREFIX . "_users set pm_all=pm_all+1, pm_unread=pm_unread+1  where user_id='$row[user_id]'");
	    $step++;
	}

	$db->free($result);

} elseif ($type == "email") {

	$row = $db->super_query( "SELECT template FROM " . PREFIX . "_email WHERE name='newsletter' LIMIT 0,1" );

	$row['template'] = str_replace( "{%charset%}", $config['charset'], $row['template'] );
	$row['template'] = str_replace( "{%title%}", $title, $row['template'] );
	$row['template'] = str_replace( "{%content%}", $message, $row['template'] );
			
	$title = str_replace( array('&amp;', '&quot;', '&#039;'), array('&', '"', "'"), $title );
	$message = stripslashes( $row['template'] );


	include_once (DLEPlugins::Check(ENGINE_DIR.'/classes/mail.class.php'));
	$mail = new dle_mail ($config, true);
	$mail->keepalive = true;

	if ($config['mail_bcc']) {
		$limit = $limit * 6;
		$i = 0;
		$t = 0;
		$h_mail = array();
		$bcc = array();

		$db->query("SELECT email FROM " . USERPREFIX . "_users".$where." ORDER BY user_id DESC LIMIT ".$startfrom.",".$limit);

		$db->close();

		while($row = $db->get_row()) {
			
			if ($i == 0) { $h_mail[$t] = $row['email'];}
			else {$bcc[$t][] = $row['email'];}

			$i++;

			if ($i == 6) {
				$i=0;
				$t++;
			}

			$step++;
        }

		$db->free();

		foreach ($h_mail as $key => $email) {
			$mail->bcc = $bcc[$key];
			$message_send = str_replace("{%user%}", $lang['nl_info_2'], $message);
			
			$message_send = str_replace("{%unsubscribe%}", "--", $message_send);

			$mail->send ($email, $title, $message_send);
		}

	} else {

		$db->query("SELECT email, password, name, user_id, fullname FROM " . USERPREFIX . "_users".$where." ORDER BY user_id DESC LIMIT ".$startfrom.",".$limit);

		$db->close();
		
		if (strpos($config['http_home_url'], "//") === 0) $slink = "https:".$config['http_home_url'];
		elseif (strpos($config['http_home_url'], "/") === 0) $slink = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'];
		else $slink = $config['http_home_url'];
			
		while( $row = $db->get_row() ) {

			if ( $row['fullname'] ) {
				$message_send = str_replace("{%user%}", $row['fullname'], $message);
			} else {
				$message_send = str_replace("{%user%}", $row['name'], $message);
			}

			$hash = md5( SECURE_AUTH_KEY . $_SERVER['HTTP_HOST'] . $row['user_id'] . sha1( substr($row['password'], 0, 6) ) . $config['key'] );
			
			$unsubscribe_link = $slink . "index.php?do=newsletterunsubscribe&user_id=" . $row['user_id'] . "&hash=" . $hash;
			
			if( stripos( $row['email'], "@mail.ru" ) === false AND stripos( $row['email'], "@inbox.ru" ) === false AND stripos( $row['email'], "@list.ru" ) === false AND stripos( $row['email'], "@bk.ru" ) === false ) {
				$mail->addCustomHeader('List-Unsubscribe', "<{$unsubscribe_link}>");
			}
			
			$mail->addCustomHeader('Precedence', "bulk");
			
		    $message_send = str_replace("{%unsubscribe%}", $unsubscribe_link, $message_send);
		    
		    $mail->send ($row['email'], $title, $message_send);
	
		    $step++;
		}

		$db->free();
	}

} else die ("error");

$count = $startfrom + $step;

if($step < $limit) $complete = 1; else $complete = 0;

$buffer = "{\"status\": \"ok\",\"count\": {$count},\"complete\": {$complete}}";

echo $buffer;
?>