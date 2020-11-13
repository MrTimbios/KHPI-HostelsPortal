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
 File: tagscloud.php
-----------------------------------------------------
 Use: tags cloud
=====================================================
*/

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$is_change = false;

if (!$config['allow_cache']) { $config['allow_cache'] = 1; $is_change = true;}

$tpl->result['tags_cloud'] = dle_cache("tagscloud", $config['skin']);

if ($tpl->result['tags_cloud'] === false) {

	$counts = array();
	$tags = array();
	$list = array();
	$sizes = array( "clouds_xsmall", "clouds_small", "clouds_medium", "clouds_large", "clouds_xlarge" );
	$min   = 1;
	$max   = 1;
	$range = 1;

	$config['tags_number'] = intval($config['tags_number']);
	if ($config['tags_number'] < 1 ) $config['tags_number'] = 10;

	$db->query("SELECT tag, COUNT(*) AS count FROM " . PREFIX . "_tags GROUP BY tag ORDER BY count DESC LIMIT 0,{$config['tags_number']}");

	while($row = $db->get_row()){

		$tags[$row['tag']] = $row['count'];
		$counts[] = $row['count'];

	}
	$db->free();

	if (count($counts)) {
		$min   = min($counts);
		$max   = max($counts);
		$range = ($max-$min);
	}

	if (!$range) $range = 1;

	foreach ($tags as $tag => $value) {

		$list[$tag]['tag']   = $tag;
		$list[$tag]['size']  = $sizes[sprintf("%d", ($value-$min)/$range*4 )];
		$list[$tag]['count']  = $value;
	}

	usort ($list, "compare_tags");
	$tags = array();	

	foreach ($list as $value) {

		if (trim($value['tag']) != "" ) {

			$url_tag = str_replace(array("&#039;", "&quot;", "&amp;"), array("'", '"', "&"), $value['tag']);
		
			if ($config['allow_alt_url'] )
	        	$tags[] = "<span class=\"{$value['size']}\"><a href=\"".$config['http_home_url']."tags/".rawurlencode($url_tag)."/\" title=\"".$lang['tags_count']." ".$value['count']."\">".$value['tag']."</a></span>";
			else
				$tags[] = "<span class=\"{$value['size']}\"><a href=\"$PHP_SELF?do=tags&amp;tag=".rawurlencode($url_tag)."\" title=\"".$lang['tags_count']." ".$value['count']."\">".$value['tag']."</a></span>";

		}

	}

	$tpl->result['tags_cloud'] = implode(" ", $tags);

	$row = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_tags");

	if ($row['count'] >= $config['tags_number']) {
		
		if ($config['allow_alt_url'] )
        	$tpl->result['tags_cloud'] .= "<div class=\"tags_more\"><a href=\"".$config['http_home_url']."tags/\">".$lang['all_tags']."</a></div>";
		else
			$tpl->result['tags_cloud'] .= "<div class=\"tags_more\"><a href=\"$PHP_SELF?do=tags\">".$lang['all_tags']."</a></div>";


	}

	create_cache ("tagscloud", $tpl->result['tags_cloud'], $config['skin']);
}


if ($do == "alltags") {

	if( $config['allow_alt_url'] ) $canonical = $config['http_home_url'] . "tags/"; else $canonical = $PHP_SELF."?do=tags";

	$tpl->result['content'] = dle_cache("alltagscloud", $config['skin']);

	if (!$tpl->result['content']) {

		$tpl->load_template('tagscloud.tpl');

		$counts = array();
		$tags = array();
		$list = array();
		$sizes = array( "clouds_xsmall", "clouds_small", "clouds_medium", "clouds_large", "clouds_xlarge" );
		$min   = 1;
		$max   = 1;
		$range = 1;
		$limit = false;

		if ( preg_match( "#\\{tags limit=['\"](.+?)['\"]\\}#i", $tpl->copy_template, $matches ) ) {
			$limit= true;
			$sql_select = "SELECT tag, COUNT(*) AS count FROM " . PREFIX . "_tags GROUP BY tag ORDER BY count DESC LIMIT 0,".intval($matches[1]);

		} else $sql_select = "SELECT tag, COUNT(*) AS count FROM " . PREFIX . "_tags GROUP BY tag";

		$db->query($sql_select);

		while($row = $db->get_row()){

			$tags[$row['tag']] = $row['count'];
			$counts[] = $row['count'];

		}
		$db->free();

		if (count($counts)) {
			$min   = min($counts);
			$max   = max($counts);
			$range = ($max-$min);
		}

		if (!$range) $range = 1;

		foreach ($tags as $tag => $value) {

			$list[$tag]['tag']   = $tag;
			$list[$tag]['size']  = $sizes[sprintf("%d", ($value-$min)/$range*4 )];
			$list[$tag]['count']  = $value;

		}

		usort ($list, "compare_tags");
		$tags = array();	

		foreach ($list as $value) {

			if (trim($value['tag']) != "" ) {

				$url_tag = str_replace(array("&#039;", "&quot;", "&amp;"), array("'", '"', "&"), $value['tag']);
				
				if ($config['allow_alt_url'] )
	        		$tags[] = "<span class=\"{$value['size']}\"><a href=\"".$config['http_home_url']."tags/".rawurlencode($url_tag)."/\" title=\"".$lang['tags_count']." ".$value['count']."\">".$value['tag']."</a></span>";
				else
					$tags[] = "<span class=\"{$value['size']}\"><a href=\"$PHP_SELF?do=tags&amp;tag=".rawurlencode($url_tag)."\" title=\"".$lang['tags_count']." ".$value['count']."\">".$value['tag']."</a></span>";
			}

		}

		$tags = implode($tags);

		if ( $limit ) $tpl->set( $matches[0], $tags);
		else $tpl->set('{tags}', $tags);

		$tpl->compile('content');
		$tpl->clear();

		create_cache ("alltagscloud", $tpl->result['content'], $config['skin']);

	}

}

if ($is_change) $config['allow_cache'] = false;

?>