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
File: links.php
=====================================================
*/

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$replace_links = array ();

$links = get_vars( "links" );

if( !is_array( $links ) ) {

	$links = array ();
	
	$db->query( "SELECT * FROM " . PREFIX . "_links ORDER BY id DESC" );
	
	while ( $row_b = $db->get_row() ) {
		
		$links[$row_b['id']] = array ();
		
		foreach ( $row_b as $key => $value ) {
			$links[$row_b['id']][$key] = stripslashes( $value );
		}
	
	}
	
	usort($links, "cmplinks");
	
	set_vars( "links", $links );
	$db->free();
}

function cmplinks ( $a, $b ) {
	global $config;

	return dle_strlen($b['word'], $config['charset'])-dle_strlen($a['word'], $config['charset']);
}

function comparehosts ( $a, $b ) {

	if (!$a OR !$b) return false;

	if (strpos($a, "//") === 0) $a = "http:".$a;
	$a = parse_url($a);

	if (strpos($b, "//") === 0) $b = "http:".$b;
	$b = parse_url($b);

	if ($a['query']) $a = $a['path'].'?'.$a['query']; else $a = $a['path'];
	if ($b['query']) $b = $b['path'].'?'.$b['query']; else $b = $b['path'];

    $a = preg_replace( '#[/]+#i', '/', $a );
    $b = preg_replace( '#[/]+#i', '/', $b );
	
	if($a[0] != '/') $a = '/'.$a;

	if (!$a OR !$b) return false;
	
	if ($a == $b) return true; else return false;	
	
}

function replace_links ( $source, $links ) {

	if( !is_array($links['find']) ) {
		return $source;
	}
	
	$count = count( $links['find'] );

	if( $count ) {

		$temp_array = array();
		$safe_tags_list = array();
		$i=0;

		if ( preg_match_all('#<title>(.+?)</title>#i', $source, $temp_array) ) {

			$temp_array = array_unique($temp_array[0]);
			foreach($temp_array as $value) {
				$i++;
				$safe_tags_list[$i]=$value;
				$source=str_replace($value, '!#' . $i . '#!', $source);
			}
		
		}
		
		if ( preg_match_all('#<h([1-6]{1})(.*?)>(.+?)</h\\1>#i', $source, $temp_array) ) {

			$temp_array = array_unique($temp_array[0]);
			foreach($temp_array as $value) {
				$i++;
				$safe_tags_list[$i]=$value;
				$source=str_replace($value, '!#' . $i . '#!', $source);
			}
		
		}
		
		if ( preg_match_all('#<a(.+?)>(.+?)</a>#i', $source, $temp_array) ) {

			$temp_array = array_unique($temp_array[0]);
			foreach($temp_array as $value) {
				$i++;
				$safe_tags_list[$i]=$value;
				$source=str_replace($value, '!#' . $i . '#!', $source);
			}
		
		}
		
		if ( preg_match_all('#<[^>]*>#', $source, $temp_array) ) {

			$temp_array = array_unique($temp_array[0]);

			foreach($temp_array as $value) {
				$i++;
				$safe_tags_list[$i]=$value;
				$source=str_replace($value, '!#' . $i . '#!', $source);
			}
		
		}

		for($t = 0; $t < $count; $t++) {
			
			$replaced = false;
			$source = preg_replace( $links['find'][$t], $links['replace'][$t], $source, $links['rcount'][$t], $replaced );

			if ( $replaced ) {
				preg_match_all('#<a(.+?)>(.+?)</a>#i', $source, $temp_array);
				$temp_array = array_unique($temp_array[0]);
				foreach($temp_array as $value) {
					$i++;
					$safe_tags_list[$i]=$value;
					$source=str_replace($value, '!#' . $i . '#!', $source);
				}
			}
			
			if ( $links['rcount'][$t] > 0 ) {
				preg_match_all( $links['find'][$t], $source, $temp_array);
				$temp_array = array_unique($temp_array[0]);
				foreach($temp_array as $value) {
					$i++;
					$safe_tags_list[$i]=$value;
					$source=str_replace($value, '!#' . $i . '#!', $source);
				}
			}

		}

		if( count( $safe_tags_list ) ) {
			$safe_tags_list = array_reverse($safe_tags_list, true);

			foreach($safe_tags_list as $key => $value) {
				$source=str_replace('!#' . $key . '#!', $value, $source);
			}
		}

		return $source;

	} else {

		return $source;

	}
}

if( count( $links ) ) {

	$find = "";
	$replace = "";

	if ( $config['charset'] == "utf-8" ) $register .= "u";

	foreach ( $links as $value ) {

		$register ="";

		if ( comparehosts( urldecode($value['link']), urldecode($_SERVER['REQUEST_URI']) ) ) continue;

		if ( !$value['only_one'] ) $register .="i";
		if ( $config['charset'] == "utf-8" ) $register .= "u";
		if ( $value['targetblank'] ) $targetblank = " target=\"_blank\""; else $targetblank = "";
		if ( $value['title'] ) $title = " title=\"{$value['title']}\""; else $title = "";

		if ($value['rcount'] < 1 ) $rcount = -1; else $rcount = intval($value['rcount']);

		if ( !substr_count ($value['word'], "(") ) { 

			$find = "#(^|\b|\s|\<br \/\>)(" . preg_quote( $value['word'], "#" ) . ")(\b|\s|!|\?|\.|,|$)#".$register;
			$replace = "\\1<a href=\"{$value['link']}\"{$title}{$targetblank}>\\2</a>\\3";

		} else {

			$words = preg_quote( $value['word'], "#" );
			$words = str_replace( '\|', "|", $words);
			$words = str_replace( '\(', ")(", $words);
			$words = str_replace( '\)', ")(", $words);

			if (substr ( $words, - 1, 1 ) == '(') $words = substr ( $words, 0, - 1 );
			if (substr ( $words, - 1, 1 ) != ')') $words .= ')';

			$words = '('.$words;

			$scount = substr_count ($words, "(");
			$rp = "";

			for ($i = 2; $i <= $scount+1; $i++) {
			    $rp .= "\\".$i;
			}

			$find = "#(^|\b|\s|\<br \/\>){$words}(\b|\s|!|\?|\.|,|$)#".$register;
			$replace = "\\1<a href=\"{$value['link']}\"{$title}{$targetblank}>{$rp}</a>\\{$i}";

		}


		if ( $value['replacearea'] == 2 ) {
	
			$replace_links['news']['find'][] = $find;
			$replace_links['news']['replace'][] = $replace;
			$replace_links['news']['rcount'][] = $rcount;
			$replace_links['comments']['find'][] = $find;
			$replace_links['comments']['replace'][] = $replace;
			$replace_links['comments']['rcount'][] = $rcount;
	
		} elseif( $value['replacearea'] == 3){
	
			$replace_links['news']['find'][] = $find;
			$replace_links['news']['replace'][] = $replace;
			$replace_links['news']['rcount'][] = $rcount;
	
		} elseif( $value['replacearea'] == 4){
	
			$replace_links['comments']['find'][] = $find;
			$replace_links['comments']['replace'][] = $replace;
			$replace_links['comments']['rcount'][] = $rcount;
			
		} elseif( $value['replacearea'] == 5){
	
			$replace_links['static']['find'][] = $find;
			$replace_links['static']['replace'][] = $replace;
			$replace_links['static']['rcount'][] = $rcount;
			
		} elseif( $value['replacearea'] == 6){
			
			$replace_links['news']['find'][] = $find;
			$replace_links['news']['replace'][] = $replace;
			$replace_links['news']['rcount'][] = $rcount;
			$replace_links['comments']['find'][] = $find;
			$replace_links['comments']['replace'][] = $replace;
			$replace_links['comments']['rcount'][] = $rcount;
			$replace_links['static']['find'][] = $find;
			$replace_links['static']['replace'][] = $replace;
			$replace_links['static']['rcount'][] = $rcount;
			
		} else {
	
			$replace_links['all']['find'][] = $find;
			$replace_links['all']['replace'][] = $replace;
			$replace_links['all']['rcount'][] = $rcount;
	
		}
	}

	unset ($links);

}

?>