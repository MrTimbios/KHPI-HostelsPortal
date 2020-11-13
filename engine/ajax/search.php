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
 File: search.php
-----------------------------------------------------
 Use: Fast search
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$config['fast_search'] OR !$user_group[$member_id['user_group']]['allow_search'] ) die( "error" );

if ( !$config['allow_registration'] ) {
	$dle_login_hash = sha1( SECURE_AUTH_KEY . $_IP );
}

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {

	echo $lang['sess_error'];
	die();

}

function strip_data($text) {

	$quotes = array ( "\x60", "\t", "\n", "\r", ".", ",", ";", ":", "&", "(", ")", "[", "]", "{", "}", "=", "*", "^", "%", "$", "<", ">", "+", "-" );
	$goodquotes = array ("#", "'", '"' );
	$repquotes = array ("\#", "\'", '\"' );
	$text = stripslashes( $text );
	$text = trim( strip_tags( $text ) );
	$text = str_replace( $quotes, ' ', $text );
	$text = str_replace( $goodquotes, $repquotes, $text );
	
	return $text;
}

$query = dle_substr( strip_data( $_POST['query'] ), 0, 90, $config['charset'] );

$arr = explode( ' ', $query );
$query = array ();

foreach ( $arr as $word ) {
	if( $word ) $query[] = $word;
}

$query = implode( "%", $query );

$query = $db->safesql( addslashes( $query ) );

if( $query == "" ) die();

$buffer = "";

$_TIME = time ();
$this_date = date( "Y-m-d H:i:s", $_TIME );
if( $config['no_date'] AND !$config['news_future'] ) $this_date = " AND p.date < '" . $this_date . "'"; else $this_date = "";

$disable_search = array();

if( count( $cat_info ) ) {
	foreach ($cat_info as $cats) {
		if($cats['disable_search']) $disable_search[] = $cats['id'];
	}
}

if( $user_group[$member_id['user_group']]['not_allow_cats'] ) {
	$n_c = explode(',', $user_group[$member_id['user_group']]['not_allow_cats'] );
	
	foreach ($n_c as $cats) {
		if(!in_array($cats, $disable_search)) $disable_search[] = $cats;
	}

}

if( count( $disable_search ) ) {

	if( $config['allow_multi_category'] ) {
		
		$where_category = " AND p.id NOT IN ( SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN ('" . implode ("','", $disable_search ) . "') )";
	
	} else {
		
		$where_category = " AND category NOT IN ('" . implode ("','", $disable_search ) . "')";
	}
	
} else $where_category = "";

$db->query("SELECT id, short_story, title, date, alt_name, category FROM " . PREFIX . "_post p LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id)  WHERE p.approve=1 AND e.disable_search=0".$this_date.$where_category." AND (short_story LIKE '%{$query}%' OR full_story LIKE '%{$query}%' OR xfields LIKE '%{$query}%' OR title LIKE '%{$query}%') ORDER by date DESC LIMIT 5");

while($row = $db->get_row()){

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

		$row['title'] = stripslashes($row['title']);

		if( dle_strlen( $row['title'], $config['charset'] ) > 43 ) $title = dle_substr( $row['title'], 0, 43, $config['charset'] ) . " ...";
		else $title = $row['title'];

		$row['short_story'] = trim (htmlspecialchars( strip_tags( stripslashes( str_replace( array("<br>", "<br />", "&nbsp;"), " ", $row['short_story'] ) ) ), ENT_QUOTES, $config['charset'] ) );

		if (stripos ( $row['short_story'], "[hide" ) !== false ) {
			
			$row['short_story'] = preg_replace_callback ( "#\[hide(.*?)\](.+?)\[/hide\]#is", 
				function ($matches) use ($member_id, $user_group, $lang) {
					
					$matches[1] = str_replace(array("=", " "), "", $matches[1]);
					$matches[2] = $matches[2];
	
					if( $matches[1] ) {
						
						$groups = explode( ',', $matches[1] );
	
						if( in_array( $member_id['user_group'], $groups ) OR $member_id['user_group'] == "1") {
							return $matches[2];
						} else return "";
						
					} else {
						
						if( $user_group[$member_id['user_group']]['allow_hide'] ) return $matches[2]; else return "";
						
					}
	
			}, $row['short_story'] );
		}

		if( dle_strlen( $row['short_story'], $config['charset'] ) > 150 ) $description = dle_substr( $row['short_story'], 0, 150, $config['charset'] ) . " ...";
		else $description = $row['short_story'];

		$description = str_replace('&amp;', '&', $description);

		$description = preg_replace( "'\[attachment=(.*?)\]'si", "", $description );

	    $buffer .= "<a href=\"" . $full_link . "\"><span class=\"searchheading\">" . stripslashes( $title ) . "</span>";

		$buffer .= "<span>".$description."</span></a>";

}

if ( !$buffer ) {
	
	$db->query("SELECT id, name, descr FROM " . PREFIX . "_static WHERE disable_search=0 AND template LIKE '%{$query}%' ORDER BY id DESC");

	while($row = $db->get_row()){
		
		if( $config['allow_alt_url'] ) $full_link = $config['http_home_url'] . $row['name'] . ".html";
		else $full_link = "$PHP_SELF?do=static&amp;page=" . $row['name'];
		
	    $buffer .= "<a href=\"" . $full_link . "\"><span class=\"searchheading\">" . stripslashes( $row['descr'] ) . "</span></a>";

	}
	
}

if ( !$buffer ) $buffer .= "<span class=\"notfound\">{$lang['related_not_found']}</span>";

$query = rawurlencode(dle_substr(trim( strip_tags( stripslashes( $_POST['query'] ) ) ), 0, 90, $config['charset'] ));

$buffer .= '<span class="seperator"><a href="'.$config['http_home_url'].'?do=search&amp;mode=advanced&amp;subaction=search&amp;story='.$query.'">'.$lang['s_ffullstart'].'</a></span><br class="break" />';

echo $buffer;

?>