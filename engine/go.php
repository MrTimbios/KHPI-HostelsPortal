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
 File: go.php
-----------------------------------------------------
 Use: Forwarding links
=====================================================
*/

function reset_url($url) {
	$value = str_replace ( "http://", "", $url );
	$value = str_replace ( "https://", "", $value );
	$value = str_replace ( "www.", "", $value );
	$value = explode ( "/", $value );
	$value = reset ( $value );
	return $value;
}

$url = rawurldecode ( (string)$_GET['url'] );
$url = base64_decode ( $url );
$url = html_entity_decode($url, ENT_QUOTES, "ISO-8859-1");
$url = str_replace("\r", "", $url);
$url = str_replace("\n", "", $url);
$url = htmlspecialchars( strip_tags($url), ENT_QUOTES, "ISO-8859-1" );
$url = str_replace ( "&amp;", "&", $url );
$url = preg_replace( "/javascript:/i", "j&#1072;vascript:", $url );
$url = preg_replace( "/data:/i", "d&#1072;t&#1072;:", $url );

if( !preg_match( "#^(http|https)://#", $url ) ) {
	$url = 'http://' . $url;
}

if( stripos( $url, "go.php" ) !== false OR stripos( $url, "do=go" ) !== false) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: /' );
	die ( "Access denied!!!" );
}

$_SERVER['HTTP_REFERER'] = reset_url ( $_SERVER['HTTP_REFERER'] );
$_SERVER['HTTP_HOST'] = reset_url ( $_SERVER['HTTP_HOST'] );

if (($_SERVER['HTTP_HOST'] != $_SERVER['HTTP_REFERER']) OR !$url) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: /' );
	die ( "Access denied!!!" );
}

@header('X-XSS-Protection: 1; mode=block');
@header('Referrer-Policy: no-referrer');
@header('Location: ' . $url );

die ( "Link Redirect" );
?>