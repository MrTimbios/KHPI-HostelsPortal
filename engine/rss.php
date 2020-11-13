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
 File: rss.php
-----------------------------------------------------
 Use: the news feeds
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../' );
	die( "Hacking attempt!" );
}

if($dle_module != "main" AND $dle_module != "allnews" AND $dle_module != "catalog" AND $dle_module != "cat") {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: /' );
	die("Hacking attempt!");
}

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/templates.class.php'));
include_once (DLEPlugins::Check(ROOT_DIR . '/language/' . $config['langs'] . '/website.lng'));

if (strpos($config['http_home_url'], "//") === 0) $config['http_home_url'] = "https:".$config['http_home_url'];
elseif (strpos($config['http_home_url'], "/") === 0) $config['http_home_url'] = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

$tpl = new dle_template( );
$tpl->dir = ROOT_DIR . '/templates';
define( 'TEMPLATE_DIR', $tpl->dir );

$member_id['user_group'] = 5;

if( $category != '' ) $category_id = get_ID( $cat_info, $category );
else $category_id = false;

$view_template = "rss";

$config['allow_cache'] = true;
$config['allow_banner'] = false;
$config['rss_number'] = intval( $config['rss_number'] );
$config['rss_format'] = intval( $config['rss_format'] );
$cstart = 0;

if ( $user ) $config['allow_cache'] = false;

if( $_GET['subaction'] == 'allnews' ) $config['home_title'] = $lang['show_user_news'] . ' ' . htmlspecialchars( $user, ENT_QUOTES, $config['charset'] ) . " - " . $config['home_title'];
elseif( $_GET['do'] == 'cat' ) $config['home_title'] = stripslashes( $cat_info[$category_id]['name'] ) . " - " . $config['home_title'];

$rss_content = <<<XML
<?xml version="1.0" encoding="{$config['charset']}"?>
<rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">
<channel>
<title>{$config['home_title']}</title>
<link>{$config['http_home_url']}</link>
<language>{$lang['wysiwyg_language']}</language>
<description>{$config['home_title']}</description>
<generator>DataLife Engine</generator>
XML;

if( !file_exists( $tpl->dir . "/rss.tpl" ) ) {

	$tpl->template = <<<HTML
[shortrss]<item turbo="true">
<title>{title}</title>
<guid isPermaLink="true">{rsslink}</guid>
<link>{rsslink}</link>
<description>{short-story}</description>
<turbo:content><![CDATA[{full-story}]]></turbo:content>
<category>{category}</category>
<dc:creator>{rssauthor}</dc:creator>
<pubDate>{rssdate}</pubDate>
</item>[/shortrss]
[fullrss]<item turbo="true">
<title>{title}</title>
<guid isPermaLink="true">{rsslink}</guid>
<link>{rsslink}</link>
<description><![CDATA[{short-story}]]></description>
<turbo:content><![CDATA[{full-story}]]></turbo:content>
<category><![CDATA[{category}]]></category>
<dc:creator>{rssauthor}</dc:creator>
<pubDate>{rssdate}</pubDate>
</item>[/fullrss]
[yandexrss]<item turbo="true">
<title>{title}</title>
<link>{rsslink}</link>
<description>{short-story}</description>
<category>{category}</category>{images}
<pubDate>{rssdate}</pubDate>
<yandex:full-text>{full-story}</yandex:full-text>
<turbo:content><![CDATA[{full-story}]]></turbo:content>
</item>[/yandexrss]
HTML;

	$tpl->copy_template = $tpl->template;

} else {
	
	$tpl->load_template( 'rss.tpl' );
	
}


if( $config['site_offline'] OR ! $config['allow_rss'] ) {
	
	$rss_content .= <<<XML
<item>
<title>RSS in offline mode</title>
<guid isPermaLink="true"></guid>
<link></link>
<description>RSS in offline mode</description>
<category>undefined</category>
<dc:creator>DataLife Engine</dc:creator>
<pubDate>DataLife Engine</pubDate>
</item>
XML;

} else {
	
	if( $config['rss_format'] == 1 ) {
		
		$tpl->template = str_replace( '[fullrss]', '', $tpl->template );
		$tpl->template = str_replace( '[/fullrss]', '', $tpl->template );
		$tpl->template = preg_replace( "'\\[yandexrss\\](.*?)\\[/yandexrss\\]'si", "", $tpl->template );
		$tpl->template = preg_replace( "'\\[shortrss\\](.*?)\\[/shortrss\\]'si", "", $tpl->template );
		$tpl->template = trim($tpl->template);
		
	} elseif( $config['rss_format'] == 2 ) {
		
		$rss_content = <<<XML
<?xml version="1.0" encoding="{$config['charset']}"?>
<rss xmlns:yandex="http://news.yandex.ru" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">
<channel>
<title>{$config['home_title']}</title>
<link>{$config['http_home_url']}</link>
<language>{$lang['wysiwyg_language']}</language>
<description>{$config['home_title']}</description>
<yandex:logo>{$config['http_home_url']}yandexlogo.png</yandex:logo>
<yandex:logo type="square">{$config['http_home_url']}yandexsquarelogo.png</yandex:logo>
<generator>DataLife Engine</generator>
XML;
		
		$tpl->template = str_replace( '[yandexrss]', '', $tpl->template );
		$tpl->template = str_replace( '[/yandexrss]', '', $tpl->template );
		$tpl->template = preg_replace( "'\\[fullrss\\](.*?)\\[/fullrss\\]'si", "", $tpl->template );
		$tpl->template = preg_replace( "'\\[shortrss\\](.*?)\\[/shortrss\\]'si", "", $tpl->template );
		$tpl->template = trim($tpl->template);		
	} else {
		
		$tpl->template = str_replace( '[shortrss]', '', $tpl->template );
		$tpl->template = str_replace( '[/shortrss]', '', $tpl->template );
		$tpl->template = preg_replace( "'\\[fullrss\\](.*?)\\[/fullrss\\]'si", "", $tpl->template );
		$tpl->template = preg_replace( "'\\[yandexrss\\](.*?)\\[/yandexrss\\]'si", "", $tpl->template );
		$tpl->template = trim($tpl->template);	
	}
	
	$tpl->copy_template = $tpl->template;
	
	include_once (DLEPlugins::Check(ENGINE_DIR . '/engine.php'));
	
	$rss_content .= $tpl->result['content'];
}

$rss_content .= '</channel></rss>';

$rss_content = str_ireplace( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $rss_content );

header( "Content-type: application/xml; charset=".$config['charset'] );
echo $rss_content;

die();

?>