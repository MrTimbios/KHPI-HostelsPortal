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
 File: allvotes.php
-----------------------------------------------------
 Use: votes
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$nick = $db->safesql($member_id['name']);

$sql_result = $db->query( "SELECT * FROM " . PREFIX . "_vote ORDER BY id DESC" );
$content = "";

while ( $row = $db->get_row( $sql_result ) ) {

	$title = stripslashes( $row['title'] );
	$body = stripslashes( $row['body'] );
	$body = str_replace( "<br />", "<br>", $body );
	$body = explode( "<br>", $body );
	$max = $row['vote_num'];

	$db->query( "SELECT answer, count(*) as count FROM " . PREFIX . "_vote_result WHERE vote_id='{$row['id']}' GROUP BY answer" );
	$answer = array ();
	
	while ( $row1 = $db->get_row() ) {
		$answer[$row1['answer']]['count'] = $row1['count'];
	}

	$pn = 0;
	$entry = "";

	$allow_vote = true;
	$disable = $lang['vote_disable'];

	if ($row['start'] AND $_TIME < $row['start'] ) $allow_vote = false;
	if ($row['end'] AND $_TIME > $row['end'] ) $allow_vote = false;

	if ( !$row['approve'] ) $allow_vote = false;

	if ($user_group[$member_id['user_group']]['allow_vote']) {

		if( $is_logged ) $row2 = $db->super_query( "SELECT count(*) as count FROM " . PREFIX . "_vote_result WHERE vote_id='{$row['id']}' AND name='$nick'" );
		else $row2 = $db->super_query( "SELECT count(*) as count FROM " . PREFIX . "_vote_result WHERE vote_id='{$row['id']}' AND ip='$_IP'" );
		
		if( $row2['count'] ) { $disable = $lang['vote_disable_1']; $allow_vote = false; }

	} else { $disable = $lang['vote_not_allow']; $allow_vote = false; }

	for($i = 0; $i < sizeof( $body ); $i ++) {
		
		++ $pn;
		if( $pn > 5 ) $pn = 1;
		
		$num = $answer[$i]['count'];
		if( ! $num ) $num = 0;
		if( $max != 0 ) $proc = (100 * $num) / $max;
		else $proc = 0;
		$proc = round( $proc, 2 );

		if ( $allow_vote )
			$radio = "<input name=\"vote_check\" type=\"radio\" value=\"$i\" />";
		else
			$radio = "&nbsp;";
		
		$entry .= "<tr><td width=\"20\" nowrap>{$radio}</td><td><div class=\"vote\">$body[$i] - $num ($proc%)</div><div class=\"voteprogress\"><span class=\"vote{$pn}\" style=\"width:".intval($proc)."%;\">{$proc}%</span></div></td></tr>";
	}

	$entry = "<table width=\"100%\">{$entry}</table>";

	if ( $allow_vote ) $button = "<br /><input type=\"submit\" onclick=\"fast_vote('{$row['id']}'); return false;\" class=\"dlevotebutton\" value=\"{$lang['vote_set']}\" />";
	else $button = "<span style=\"color:red;\">{$disable}</span>";

	$content .= <<<HTML
<form method="post" name="vote_{$row['id']}" id="vote_{$row['id']}" action=''>
<fieldset>
  <legend>{$title}</legend>
  <div id="dle-vote_list-{$row['id']}">{$entry}{$button}<br /><br />{$lang['max_votes']} {$max}</div>
</fieldset>
</form>
HTML;

}


echo "<div id=\"dlevotespopup\" title=\"{$lang['all_votes']}\" style=\"display:none\"><div id=\"dlevotespopupcontent\" style=\"overflow: auto;\">{$content}</div></div>";

?>