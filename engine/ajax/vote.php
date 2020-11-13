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
 File: vote.php
-----------------------------------------------------
 Use: AJAX votes
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}


require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/templates.class.php'));

$rid = intval( $_REQUEST['vote_id'] );
$vote_check = intval( $_REQUEST['vote_check'] );
$nick = $db->safesql($member_id['name']);

$tpl = new dle_template();
$tpl->dir = ROOT_DIR . '/templates/' . $config['skin'];
define( 'TEMPLATE_DIR', $tpl->dir );


	if( $_REQUEST['vote_action'] == "vote" ) {
		
		if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
			
			echo $lang['sess_error']; die();
		
		}
		
		if ($user_group[$member_id['user_group']]['allow_vote']) {
	
			if( $is_logged ) $row = $db->super_query( "SELECT count(*) as count FROM " . PREFIX . "_vote_result WHERE vote_id='$rid' AND name='$nick'" );
			else $row = $db->super_query( "SELECT count(*) as count FROM " . PREFIX . "_vote_result WHERE vote_id='$rid' AND ip='$_IP'" );
			
			if( !$row['count'] ) $is_voted = false;
			else $is_voted = true;
	
		} else $is_voted = true;
		
		if( $is_voted == false ) {
			
			if( ! $is_logged ) $nick = "guest";
			
			$db->query( "INSERT INTO " . PREFIX . "_vote_result (ip, name, vote_id, answer) VALUES ('$_IP', '$nick', '$rid', '$vote_check')" );
			
			$db->query( "UPDATE " . PREFIX . "_vote SET vote_num=vote_num+1 WHERE id='$rid'" );
		}
		
	}

	if( $_REQUEST['vote_mode'] == "fast_vote" ) { echo $lang['vote_ok']; die(); }

	$result = $db->super_query( "SELECT * FROM " . PREFIX . "_vote WHERE id='$rid'" );
	$title = stripslashes( $result['title'] );
	$body = stripslashes( $result['body'] );
	$body = str_replace( "<br />", "<br>", $body );
	$body = explode( "<br>", $body );
	$max = $result['vote_num'];
	
	$db->query( "SELECT answer, count(*) as count FROM " . PREFIX . "_vote_result WHERE vote_id='$rid' GROUP BY answer" );
	$answer = array ();
	
	while ( $row = $db->get_row() ) {
		$answer[$row['answer']]['count'] = $row['count'];
	}
	
	$db->free();
	$pn = 0;
	$entry = "";
	
	for($i = 0; $i < sizeof( $body ); $i ++) {
		
		$num = $answer[$i]['count'];
		
		++ $pn;
		if( $pn > 5 ) $pn = 1;
		
		if( ! $num ) $num = 0;
		
		if( $max != 0 ) $proc = (100 * $num) / $max;
		else $proc = 0;
		
		$proc = round( $proc, 2 );
		
		$entry .= "<div class=\"vote\">$body[$i] - $num ($proc%)</div><div class=\"voteprogress\"><span class=\"vote{$pn}\" style=\"width:".intval($proc)."%;\">{$proc}%</span></div>\n";
	}
	$entry = "<div id=\"dle-vote\">$entry</div>";
	
	$tpl->load_template( 'vote.tpl' );
	
	$tpl->set( '{list}', $entry );
	$tpl->set( '{vote_id}', $rid );
	$tpl->set( '{title}', $title );
	$tpl->set( '{votes}', $max );
	$tpl->set( '[voteresult]', '' );
	$tpl->set( '[/voteresult]', '' );
	$tpl->set_block( "'\\[votelist\\].*?\\[/votelist\\]'si", "" );
	$tpl->compile( 'vote' );

	$db->close();

	$tpl->result['vote'] = str_replace( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $tpl->result['vote'] );

	echo $tpl->result['vote'];
	
?>