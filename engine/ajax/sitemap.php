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
 File: sitemap.php
-----------------------------------------------------
 Use: Notice search engines about the sitemap
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if(!@file_exists(ROOT_DIR. "/uploads/sitemap.xml")){ 

	die( "error" );

} else {

	if ($config['allow_alt_url']) {

		$map_link = $config['http_home_url']."sitemap.xml";

	} else {

		$map_link = $config['http_home_url']."uploads/sitemap.xml";

	}
}

if( !$user_group[$member_id['user_group']]['admin_googlemap'] ) { die ("error"); }

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
	die ("error");
	
}

$buffer = "";

function send_url($url, $map) {
		
	$data = false;

	$file = $url.urlencode($map);
		
	if( function_exists( 'curl_init' ) ) {
			
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $file );
		curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 5);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
			
		$data = curl_exec( $ch );
		curl_close( $ch );

		return $data;
		
	} else {

		return @file_get_contents( $file );

	}
	
}

if (strpos ( send_url("https://google.com/webmasters/sitemaps/ping?sitemap=", $map_link), "successfully added" ) !== false) {

	$buffer .= $lang['sitemap_send']." Google: ".$lang['nl_finish'];

} else {

	$buffer .= "<br />".$lang['sitemap_send']." Google: ".$lang['nl_error']." URL: <a href=\"https://google.com/webmasters/sitemaps/ping?sitemap=".urlencode($map_link)."\" target=\"_blank\">https://google.com/webmasters/sitemaps/ping?sitemap={$map_link}</a>";

}

send_url("https://www.bing.com/webmaster/ping.aspx?siteMap=", $map_link);
$buffer .= "<br />".$lang['sitemap_send']." Bing: ".$lang['nl_finish'];


echo "<div class=\"findrelated\">".$buffer."</div>";

?>