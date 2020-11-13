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
 File: rebuild.php
-----------------------------------------------------
 Use: News rebuild
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));

if(($member_id['user_group'] != 1)) {die ("error");}

if ($_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash) {

	  die ("error");

}

if ($_POST['area'] == "related" ) {
	$db->query( "UPDATE " . PREFIX . "_post_extras SET related_ids=''" );
    echo "{\"status\": \"ok\"}";
	die();
}

$startfrom = intval($_POST['startfrom']);
$buffer = "";
$step = 0;
$count_per_step = 100;

if ($_POST['area'] == "comments" ) {
	$count_per_step = 500;
}

if ($_POST['area'] == "static" ) {

	$parse = new ParseFilter();
	$parse->edit_mode = false;

	if ( $config['allow_static_wysiwyg'] ) $parse->allow_code = false;

	$result = $db->query("SELECT id, template, allow_br FROM " . PREFIX . "_static WHERE allow_br !='2' LIMIT ".$startfrom.", ".$count_per_step);

	while($row = $db->get_row($result))
	{

		if( $row['allow_br'] != '1' OR $config['allow_static_wysiwyg'] ) {
			
			$row['template'] = $parse->decodeBBCodes( $row['template'], true, $config['allow_static_wysiwyg'] );
		
		} else {
			
			$row['template'] = $parse->decodeBBCodes( $row['template'], false );
		
		}

		$template = $parse->process( $row['template'] );

		if( $config['allow_static_wysiwyg'] OR $row['allow_br'] != '1' ) {
			$template = $db->safesql($parse->BB_Parse( $template ));
		} else {
			$template = $db->safesql($parse->BB_Parse( $template, false ));
		}

		$db->query( "UPDATE " . PREFIX . "_static SET template='$template' WHERE id='{$row['id']}'" );

		$step++;
	}

	$rebuildcount = $startfrom + $step;
	$buffer = "{\"status\": \"ok\",\"rebuildcount\": {$rebuildcount}}";
	echo $buffer;
	
} elseif ($_POST['area'] == "comments" ) {
	
	if( $config['allow_comments_wysiwyg'] > 0 ) {
		
		$allowed_tags = array ('div[style|class]', 'span[style|class]', 'p[style|class]', 'br', 'strong', 'em', 'ul', 'li', 'ol', 'b', 'u', 'i', 's' );
		
		if( $user_group[$member_id['user_group']]['allow_url'] ) $allowed_tags[] = 'a[href|target|style|class|title]';
		if( $user_group[$member_id['user_group']]['allow_image'] ) $allowed_tags[] = 'img[style|class|src|alt|width|height]';
		
		$parse = new ParseFilter( $allowed_tags );
		$parse->wysiwyg = true;
		$parse->allow_code = false;
		$use_html = true;
	
	} else {
		
		$parse = new ParseFilter();
		$use_html = false;
		
		if ($config['allow_comments_wysiwyg'] == "-1") $parse->allowbbcodes = false;
		
	}
	
	$parse->safe_mode = true;
	$parse->remove_html = false;
	$parse->edit_mode = false;
	$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
	$parse->allow_image = $user_group[$member_id['user_group']]['allow_image'];

	$result = $db->query("SELECT id, text FROM " . PREFIX . "_comments LIMIT ".$startfrom.", ".$count_per_step);
	
	while($row = $db->get_row($result)) {
		
		if( $config['allow_comments_wysiwyg'] < 1 ) {
			
			$row['text'] = $parse->decodeBBCodes( $row['text'], false );
			
		} else {
			$row['text'] = $parse->decodeBBCodes( $row['text'], true, $config['allow_comments_wysiwyg'] );
		}

		$row['text'] = $db->safesql( $parse->BB_Parse($parse->process( $row['text'] ), $use_html) );
		
		$db->query( "UPDATE " . PREFIX . "_comments SET text='{$row['text']}' WHERE id='{$row['id']}'" );
		
		$step++;
	}
	
	clear_cache();
	$rebuildcount = $startfrom + $step;
	$buffer = "{\"status\": \"ok\",\"rebuildcount\": {$rebuildcount}}";
	echo $buffer;
	
} else {


	$parse = new ParseFilter();
	$parse->edit_mode = false;
	if ( $config['allow_admin_wysiwyg'] ) $parse->allow_code = false;
	
	$parsexf = new ParseFilter();
	$parsexf->edit_mode = false;
	if ( $config['allow_admin_wysiwyg'] ) $parsexf->allow_code = false;
	
	$result = $db->query("SELECT p.id, p.short_story, p.full_story, p.xfields, p.title, p.category, p.approve, p.allow_br, e.news_id FROM " . PREFIX . "_post p LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) LIMIT ".$startfrom.", ".$count_per_step);
	
	while($row = $db->get_row($result))
	{
	
		if( $row['allow_br'] != '1' OR $config['allow_admin_wysiwyg'] ) {
			$row['short_story'] = $parse->decodeBBCodes( $row['short_story'], true, $config['allow_admin_wysiwyg'] );
			$row['full_story'] = $parse->decodeBBCodes( $row['full_story'], true, $config['allow_admin_wysiwyg'] );
		} else {
			$row['short_story'] = $parse->decodeBBCodes( $row['short_story'], false );
			$row['full_story'] = $parse->decodeBBCodes( $row['full_story'], false );
		}
	
		$short_story = $parse->process( $row['short_story'] );
		$full_story = $parse->process( $row['full_story'] );
		$_POST['title'] = $row['title'];
	
		if( $config['allow_admin_wysiwyg'] OR $row['allow_br'] != '1' ) {
			
			$full_story = $db->safesql( $parse->BB_Parse( $full_story ) );
			$short_story = $db->safesql( $parse->BB_Parse( $short_story ) );
		
		} else {
			
			$full_story = $db->safesql( $parse->BB_Parse( $full_story, false ) );
			$short_story = $db->safesql( $parse->BB_Parse( $short_story, false ) );
		
		}

		$xf_search_words = array ();
		
		if ($row['xfields']) {
	
			$xfields = xfieldsload();
			$postedxfields = xfieldsdataload($row['xfields']);
			$filecontents = array ();
			$newpostedxfields = array ();
	
			if( !empty( $postedxfields ) ) {
	
				foreach ($xfields as $name => $value) {
				
					if ($value[3] != "select" AND $value[3] != "image" AND $value[3] != "file" AND $value[3] != "htmljs" AND $value[3] != "datetime" AND $value[8] == 0 AND $value[6] == 0 AND $postedxfields[$value[0]] != "" ) {
				
						if( $config['allow_admin_wysiwyg'] OR $row['allow_br'] != '1' ) {
							$postedxfields[$value[0]] = $parsexf->decodeBBCodes($postedxfields[$value[0]], true, true);					
							$newpostedxfields[$value[0]] = $parsexf->BB_Parse($parsexf->process($postedxfields[$value[0]]));
								
						} else {
							$postedxfields[$value[0]] = $parsexf->decodeBBCodes($postedxfields[$value[0]], false);
							$newpostedxfields[$value[0]] = $parsexf->BB_Parse($parsexf->process($postedxfields[$value[0]]), false);
								
						}
				
					} elseif ( $postedxfields[$value[0]] != "" ) {
						
						if($value[3] == "htmljs") {
							
							$newpostedxfields[$value[0]] = $postedxfields[$value[0]];
							
						} else {
							
							$postedxfields[$value[0]] = html_entity_decode($postedxfields[$value[0]], ENT_QUOTES, $config['charset']);
							$newpostedxfields[$value[0]] = trim( htmlspecialchars(strip_tags( stripslashes($postedxfields[$value[0]]) ), ENT_QUOTES, $config['charset'] ));
							$newpostedxfields[$value[0]] = preg_replace( "/javascript:/i", "j&#1072;vascript&#58;", $newpostedxfields[$value[0]] );
							$newpostedxfields[$value[0]] = preg_replace( "/data:/i", "d&#1072;ta&#58;", $newpostedxfields[$value[0]] );
							$newpostedxfields[$value[0]] = str_replace( array("{", "[", ":"), array("&#123;", "&#91;", "&#58;"), $newpostedxfields[$value[0]] );
				
						}
				
					}
					
					if ( $value[6] AND !empty( $newpostedxfields[$value[0]] ) ) {
						$temp_array = explode( ",", $newpostedxfields[$value[0]] );
						
						foreach ($temp_array as $value2) {
							$value2 = trim($value2);
							if($value2) $xf_search_words[] = array( $db->safesql($value[0]), $db->safesql($value2) );
						}
					
					}
				
				}
	
				if (count ($newpostedxfields) ) {
		
					foreach ( $newpostedxfields as $xfielddataname => $xfielddatavalue ) {
						if( $xfielddatavalue === "" ) {
							continue;
						}
		
						$xfielddatavalue = str_replace( "|", "&#124;", $xfielddatavalue );
						$filecontents[] = $db->safesql("{$xfielddataname}|{$xfielddatavalue}");
					}
					
					$filecontents = implode( "||", $filecontents );
		
				} else	$filecontents = '';
			
			} else	$filecontents = '';
	
		} else	$filecontents = '';
	
		$db->query( "UPDATE " . PREFIX . "_post SET short_story='{$short_story}', full_story='{$full_story}', xfields='{$filecontents}' WHERE id='{$row['id']}'" );

		if ( !$row['news_id'] ) $db->query( "INSERT INTO " . PREFIX . "_post_extras (news_id, allow_rate) VALUES('{$row['id']}', '1')" );

		$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$row['id']}'" );

		if( $row['category'] AND $row['approve'] ) {

			$cat_ids = array ();

			$cat_ids_arr = explode( ",", $row['category'] );

			foreach ( $cat_ids_arr as $value ) {

				$cat_ids[] = "('" . $row['id'] . "', '" . intval( $value ) . "')";
				
			}

			$cat_ids = implode( ", ", $cat_ids );
			$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );

		}
		
		$db->query( "DELETE FROM " . PREFIX . "_xfsearch WHERE news_id = '{$row['id']}'" );

		if ( count($xf_search_words) ) {
			
			$temp_array = array();
			
			foreach ( $xf_search_words as $value ) {
				
				$temp_array[] = "('" . $row['id'] . "', '" . $value[0] . "', '" . $value[1] . "')";
			}
			
			$xf_search_words = implode( ", ", $temp_array );
			$db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue) VALUES " . $xf_search_words );
		}
	
		$step++;
	}
	
	clear_cache();
	$rebuildcount = $startfrom + $step;
	$buffer = "{\"status\": \"ok\",\"rebuildcount\": {$rebuildcount}}";
	echo $buffer;
}
?>