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
 File: main.php
=====================================================
*/

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$home_url = clean_url($config['http_home_url']);

if ($home_url AND clean_url( $_SERVER['HTTP_HOST'] ) != $home_url ) {

	$replace_url = array ();
	$replace_url[0] = $home_url;
	$replace_url[1] = clean_url ( $_SERVER['HTTP_HOST'] );

} else $replace_url = false;

$tpl->load_template ( 'main.tpl' );

$tpl->set ( '{calendar}', $tpl->result['calendar'] );
$tpl->set ( '{archives}', $tpl->result['archive'] );
$tpl->set ( '{tags}', $tpl->result['tags_cloud'] );
$tpl->set ( '{vote}', $tpl->result['vote'] );
$tpl->set ( '{login}', $tpl->result['login_panel'] );
$tpl->set ( '{speedbar}', $tpl->result['speedbar'] );

if ( $dle_module == "showfull" AND $news_found ) {
	
	if( strpos( $tpl->copy_template, "related-news" ) !== false ) {
		$tpl->set( '[related-news]', "" );
		$tpl->set( '[/related-news]', "" );
		$tpl->set( '{related-news}', $related_buffer );
	}
	
	if( strpos( $tpl->copy_template, "[xf" ) !== false OR strpos( $tpl->copy_template, "[ifxf" ) !== false ) {

		$xfieldsdata = xfieldsdataload( $xfieldsdata );
		
		foreach ( $xfields as $value ) {
			$preg_safe_name = preg_quote( $value[0], "'" );
			
			$xfieldsdata[$value[0]] = stripslashes( $xfieldsdata[$value[0]] );
			
			if( $value[20] ) {
				  
				$value[20] = explode( ',', $value[20] );
				  
				if( $value[20][0] AND !in_array( $member_id['user_group'], $value[20] ) ) {
					$xfieldsdata[$value[0]] = "";
				}

			}
	
			if ( $value[3] == "yesorno" ) {
				
			    if( intval($xfieldsdata[$value[0]]) ) {
					$xfgiven = true;
					$xfieldsdata[$value[0]] = $lang['xfield_xyes'];
				} else {
					$xfgiven = false;
					$xfieldsdata[$value[0]] = $lang['xfield_xno'];
				}
				
			} else {
				
				if($xfieldsdata[$value[0]] == "") $xfgiven = false; else $xfgiven = true;
				
			}
			
			if( !$xfgiven ) {
				$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
				$tpl->copy_template = str_replace( "[xfnotgiven_{$value[0]}]", "", $tpl->copy_template );
				$tpl->copy_template = str_replace( "[/xfnotgiven_{$value[0]}]", "", $tpl->copy_template );
			} else {
				$tpl->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
				$tpl->copy_template = str_replace( "[xfgiven_{$value[0]}]", "", $tpl->copy_template );
				$tpl->copy_template = str_replace( "[/xfgiven_{$value[0]}]", "", $tpl->copy_template );
			}
			
			if(strpos( $tpl->copy_template, "[ifxfvalue {$value[0]}" ) !== false ) {
				$tpl->copy_template = preg_replace_callback ( "#\\[ifxfvalue(.+?)\\](.+?)\\[/ifxfvalue\\]#is", "check_xfvalue", $tpl->copy_template );
			}
				
			if ( $value[6] AND !empty( $xfieldsdata[$value[0]] ) ) {
				$temp_array = explode( ",", $xfieldsdata[$value[0]] );
				$value3 = array();

				foreach ($temp_array as $value2) {

					$value2 = trim($value2);
					
					if($value2) {
						$value2 = str_replace(array("&#039;", "&quot;", "&amp;"), array("'", '"', "&"), $value2);
	
						if( $config['allow_alt_url'] ) $value3[] = "<a href=\"" . $config['http_home_url'] . "xfsearch/" .$value[0]."/". rawurlencode( $value2 ) . "/\">" . $value2 . "</a>";
						else $value3[] = "<a href=\"$PHP_SELF?do=xfsearch&amp;xfname=".$value[0]."&amp;xf=" . rawurlencode( $value2 ) . "\">" . $value2 . "</a>";
					}
				}
				
				if( empty($value[21]) ) $value[21] = ", ";
				
				$xfieldsdata[$value[0]] = implode($value[21], $value3);

				unset($temp_array);
				unset($value2);
				unset($value3);

			}
			
			if ($config['allow_links'] AND $value[3] == "textarea" AND function_exists('replace_links')) $xfieldsdata[$value[0]] = replace_links ( $xfieldsdata[$value[0]], $replace_links['news'] );

			if($value[3] == "image" AND $xfieldsdata[$value[0]] ) {
				
				$temp_array = explode('|', $xfieldsdata[$value[0]]);
						
				if (count($temp_array) > 1 ){
					
					$temp_alt = $temp_array[0];
					$temp_value = $temp_array[1];
						
				} else {
					
					$temp_alt = '';
					$temp_value = $temp_array[0];
					
				}

				$path_parts = @pathinfo($temp_value);
		
				if( $value[12] AND file_exists(ROOT_DIR . "/uploads/posts/" .$path_parts['dirname']."/thumbs/".$path_parts['basename']) ) {
					$thumb_url = $config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/thumbs/".$path_parts['basename'];
					$img_url = $config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
				} else {
					$img_url = 	$config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
					$thumb_url = "";
				}
				
				if($thumb_url) {
					$tpl->copy_template = str_replace( "[xfvalue_thumb_url_{$value[0]}]", $thumb_url, $tpl->copy_template );
					$xfieldsdata[$value[0]] = "<a href=\"$img_url\" class=\"highslide\" target=\"_blank\"><img class=\"xfieldimage {$value[0]}\" src=\"$thumb_url\" alt=\"{$temp_alt}\"></a>";
				} else {
					$tpl->copy_template = str_replace( "[xfvalue_thumb_url_{$value[0]}]", $img_url, $tpl->copy_template );
					$xfieldsdata[$value[0]] = "<img class=\"xfieldimage {$value[0]}\" src=\"{$img_url}\" alt=\"{$temp_alt}\">";
				}
				
				$tpl->copy_template = str_replace( "[xfvalue_image_url_{$value[0]}]", $img_url, $tpl->copy_template );
			}
			
			if($value[3] == "image" AND !$xfieldsdata[$value[0]]) {

				$tpl->copy_template = str_replace( "[xfvalue_thumb_url_{$value[0]}]", "", $tpl->copy_template );
				$tpl->copy_template = str_replace( "[xfvalue_image_url_{$value[0]}]", "", $tpl->copy_template );
				
			}
			
			if($value[3] == "imagegalery" AND $xfieldsdata[$value[0]] AND stripos ( $tpl->copy_template, "[xfvalue_{$value[0]}]" ) !== false ) {

				$fieldvalue_arr = explode(',', $xfieldsdata[$value[0]]);
				$gallery_image = array();
				$gallery_single_image = array();
				$xf_image_count = 0;
				$single_need = false;
	
				if(stripos ( $tpl->copy_template, "[xfvalue_{$value[0]} image=" ) !== false) $single_need = true;
					
				foreach ($fieldvalue_arr as $temp_value) {
					$xf_image_count ++;
						
					$temp_value = trim($temp_value);
				
					if($temp_value == "") continue;
					
					$temp_array = explode('|', $temp_value);
						
					if (count($temp_array) > 1 ){
							
						$temp_alt = $temp_array[0];
						$temp_value = $temp_array[1];
							
					} else {
						
						$temp_alt = '';
						$temp_value = $temp_array[0];
							
					}
						
					$path_parts = @pathinfo($temp_value);
					
					if( $value[12] AND file_exists(ROOT_DIR . "/uploads/posts/" .$path_parts['dirname']."/thumbs/".$path_parts['basename']) ) {
						$thumb_url = $config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/thumbs/".$path_parts['basename'];
						$img_url = $config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
					} else {
						$img_url = 	$config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
						$thumb_url = "";
					}
					
					if($thumb_url) {
						$gallery_image[] = "<li><a href=\"$img_url\" onclick=\"return hs.expand(this, { slideshowGroup: 'xf_".NEWS_ID."_{$value[0]}' })\" target=\"_blank\"><img src=\"{$thumb_url}\" alt=\"{$temp_alt}\"></a></li>";
						$gallery_single_image['[xfvalue_'.$value[0].' image="'.$xf_image_count.'"]'] = "<a href=\"{$img_url}\" class=\"highslide\" target=\"_blank\"><img class=\"xfieldimage {$value[0]}\" src=\"{$thumb_url}\" alt=\"{$temp_alt}\"></a>";
					} else {
						$gallery_image[] = "<li><img src=\"{$img_url}\" alt=\"{$temp_alt}\"></li>";
						$gallery_single_image['[xfvalue_'.$value[0].' image="'.$xf_image_count.'"]'] = "<img class=\"xfieldimage {$value[0]}\" src=\"{$img_url}\" alt=\"{$temp_alt}\">";
					}
			  
				}
				
				if($single_need AND count($gallery_single_image) ) {
					foreach($gallery_single_image as $temp_key => $temp_value) $tpl->copy_template = str_replace( $temp_key, $temp_value, $tpl->copy_template );
				}

				$xfieldsdata[$value[0]] = "<ul class=\"xfieldimagegallery {$value[0]}\">".implode($gallery_image)."</ul>";
				
			}
				
			$tpl->copy_template = str_replace( "[xfvalue_{$value[0]}]", $xfieldsdata[$value[0]], $tpl->copy_template );

			if ( preg_match( "#\\[xfvalue_{$preg_safe_name} limit=['\"](.+?)['\"]\\]#i", $tpl->copy_template, $matches ) ) {
				$count= intval($matches[1]);
		
				$xfieldsdata[$value[0]] = str_replace( "</p><p>", " ", $xfieldsdata[$value[0]] );
				$xfieldsdata[$value[0]] = strip_tags( $xfieldsdata[$value[0]], "<br>" );
				$xfieldsdata[$value[0]] = trim(str_replace( "<br>", " ", str_replace( "<br />", " ", str_replace( "\n", " ", str_replace( "\r", "", $xfieldsdata[$value[0]] ) ) ) ));
		
				if( $count AND dle_strlen( $xfieldsdata[$value[0]], $config['charset'] ) > $count ) {
						
					$xfieldsdata[$value[0]] = dle_substr( $xfieldsdata[$value[0]], 0, $count, $config['charset'] );
						
					if( ($temp_dmax = dle_strrpos( $xfieldsdata[$value[0]], ' ', $config['charset'] )) ) $xfieldsdata[$value[0]] = dle_substr( $xfieldsdata[$value[0]], 0, $temp_dmax, $config['charset'] );
					
				}
		
				$tpl->copy_template = str_replace( $matches[0], $xfieldsdata[$value[0]], $tpl->copy_template );
		
			}
			
			if (stripos ( $tpl->copy_template, "[hide" ) !== false ) {
				
				$tpl->copy_template = preg_replace_callback ( "#\[hide(.*?)\](.+?)\[/hide\]#is", 
					function ($matches) use ($member_id, $user_group, $lang) {
						
						$matches[1] = str_replace(array("=", " "), "", $matches[1]);
						$matches[2] = $matches[2];
		
						if( $matches[1] ) {
							
							$groups = explode( ',', $matches[1] );
		
							if( in_array( $member_id['user_group'], $groups ) OR $member_id['user_group'] == "1") {
								return $matches[2];
							} else return "<div class=\"quote dlehidden\">" . $lang['news_regus'] . "</div>";
							
						} else {
							
							if( $user_group[$member_id['user_group']]['allow_hide'] ) return $matches[2]; else return "<div class=\"quote dlehidden\">" . $lang['news_regus'] . "</div>";
							
						}
		
				}, $tpl->copy_template );
			}


			if( $config['files_allow'] ) if( strpos( $tpl->copy_template, "[attachment=" ) !== false ) {
				$tpl->copy_template = show_attach( $tpl->copy_template, NEWS_ID );
			}
	
		}
	}
		
} else {
	
	if( strpos( $tpl->copy_template, "related-news" ) !== false ) {
		$tpl->set( '{related-news}', "" );
		$tpl->set_block( "'\\[related-news\\](.*?)\\[/related-news\\]'si", "" );
	}
	
	if( strpos( $tpl->copy_template, "[xf" ) !== false ) {
		$tpl->copy_template = preg_replace( "'\\[xfnotgiven_(.*?)\\](.*?)\\[/xfnotgiven_(.*?)\\]'is", "", $tpl->copy_template );
		$tpl->copy_template = preg_replace( "'\\[xfgiven_(.*?)\\](.*?)\\[/xfgiven_(.*?)\\]'is", "", $tpl->copy_template );
		$tpl->copy_template = preg_replace( "'\\[xfvalue_(.*?)\\]'i", "", $tpl->copy_template );
	}
	
	if( strpos( $tpl->copy_template, "[ifxfvalue" ) !== false ) {
		$tpl->copy_template = preg_replace( "#\\[ifxfvalue(.+?)\\](.+?)\\[/ifxfvalue\\]#is", "", $tpl->copy_template );
	}

}

if ($config['allow_skin_change']) $tpl->set ( '{changeskin}', ChangeSkin ( ROOT_DIR . '/templates', $config['skin'] ) );

if (count ( $banners ) and $config['allow_banner']) {

	foreach ( $banners as $name => $value ) {
		$tpl->copy_template = str_replace ( "{banner_" . $name . "}", $value, $tpl->copy_template );
		if ( $value ) {
			$tpl->copy_template = str_replace ( "[banner_" . $name . "]", "", $tpl->copy_template );
			$tpl->copy_template = str_replace ( "[/banner_" . $name . "]", "", $tpl->copy_template );
		}
	}

}

$tpl->set_block ( "'{banner_(.*?)}'si", "" );
$tpl->set_block ( "'\\[banner_(.*?)\\](.*?)\\[/banner_(.*?)\\]'si", "" );

if ($config['rss_informer'] AND count ($informers) ) {
	foreach ( $informers as $name => $value ) {
		$tpl->copy_template = str_replace ( "{inform_" . $name . "}", $value, $tpl->copy_template );
	}
}

if (stripos ( $tpl->copy_template, "[category=" ) !== false) {
	$tpl->copy_template = preg_replace_callback ( "#\\[(category)=(.+?)\\](.*?)\\[/category\\]#is", "check_category", $tpl->copy_template );
}

if (stripos ( $tpl->copy_template, "[not-category=" ) !== false) {
	$tpl->copy_template = preg_replace_callback ( "#\\[(not-category)=(.+?)\\](.*?)\\[/not-category\\]#is", "check_category", $tpl->copy_template );
}

if (stripos ( $tpl->copy_template, "[static=" ) !== false) {
	$tpl->copy_template = preg_replace_callback ( "#\\[(static)=(.+?)\\](.*?)\\[/static\\]#is", "check_static", $tpl->copy_template );
}

if (stripos ( $tpl->copy_template, "[not-static=" ) !== false) {
	$tpl->copy_template = preg_replace_callback ( "#\\[(not-static)=(.+?)\\](.*?)\\[/not-static\\]#is", "check_static", $tpl->copy_template );
}

if (stripos ( $tpl->copy_template, "{customcomments" ) !== false) {
	$tpl->copy_template = preg_replace_callback ( "#\\{customcomments(.+?)\\}#i", "custom_comments", $tpl->copy_template );
}

if (stripos ( $tpl->copy_template, "{custom" ) !== false) {
	$tpl->copy_template = preg_replace_callback ( "#\\{custom(.+?)\\}#i", "custom_print", $tpl->copy_template );
}

if ( ($allow_active_news AND $news_found AND $config['allow_change_sort'] AND $dle_module != "userinfo") OR defined('CUSTOMSORT')) {

	$tpl->set ( '[sort]', "" );
	$tpl->set ( '{sort}', news_sort ( $do ) );
	$tpl->set ( '[/sort]', "" );

} else {

	$tpl->set_block ( "'\\[sort\\](.*?)\\[/sort\\]'si", "" );

}


$tpl->copy_template = str_replace ( "{topnews}", $tpl->result['topnews'], $tpl->copy_template );

if( $vk_url ) {
	$tpl->set( '[vk]', "" );
	$tpl->set( '[/vk]', "" );
	$tpl->set( '{vk_url}', $vk_url );	
} else {
	$tpl->set_block( "'\\[vk\\](.*?)\\[/vk\\]'si", "" );
	$tpl->set( '{vk_url}', '' );	
}
if( $odnoklassniki_url ) {
	$tpl->set( '[odnoklassniki]', "" );
	$tpl->set( '[/odnoklassniki]', "" );
	$tpl->set( '{odnoklassniki_url}', $odnoklassniki_url );
} else {
	$tpl->set_block( "'\\[odnoklassniki\\](.*?)\\[/odnoklassniki\\]'si", "" );
	$tpl->set( '{odnoklassniki_url}', '' );	
}
if( $facebook_url ) {
	$tpl->set( '[facebook]', "" );
	$tpl->set( '[/facebook]', "" );
	$tpl->set( '{facebook_url}', $facebook_url );	
} else {
	$tpl->set_block( "'\\[facebook\\](.*?)\\[/facebook\\]'si", "" );
	$tpl->set( '{facebook_url}', '' );	
}
if( $google_url ) {
	$tpl->set( '[google]', "" );
	$tpl->set( '[/google]', "" );
	$tpl->set( '{google_url}', $google_url );
} else {
	$tpl->set_block( "'\\[google\\](.*?)\\[/google\\]'si", "" );
	$tpl->set( '{google_url}', '' );	
}
if( $mailru_url ) {
	$tpl->set( '[mailru]', "" );
	$tpl->set( '[/mailru]', "" );
	$tpl->set( '{mailru_url}', $mailru_url );	
} else {
	$tpl->set_block( "'\\[mailru\\](.*?)\\[/mailru\\]'si", "" );
	$tpl->set( '{mailru_url}', '' );	
}
if( $yandex_url ) {
	$tpl->set( '[yandex]', "" );
	$tpl->set( '[/yandex]', "" );
	$tpl->set( '{yandex_url}', $yandex_url );
} else {
	$tpl->set_block( "'\\[yandex\\](.*?)\\[/yandex\\]'si", "" );
	$tpl->set( '{yandex_url}', '' );
}

$config['http_home_url'] = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
$config['http_home_url'] = reset ( $config['http_home_url'] );

if ( !$user_group[$member_id['user_group']]['allow_admin'] ) $config['admin_path'] = "";

$ajax .= <<<HTML
{$pm_alert}{$twofactor_alert}<script>
<!--
var dle_root       = '{$config['http_home_url']}';
var dle_admin      = '{$config['admin_path']}';
var dle_login_hash = '{$dle_login_hash}';
var dle_group      = {$member_id['user_group']};
var dle_skin       = '{$config['skin']}';
var dle_wysiwyg    = '{$config['allow_comments_wysiwyg']}';
var quick_wysiwyg  = '{$config['allow_quick_wysiwyg']}';
var dle_min_search = '{$config['search_length_min']}';
var dle_act_lang   = ["{$lang['p_yes']}", "{$lang['p_no']}", "{$lang['p_enter']}", "{$lang['p_cancel']}", "{$lang['p_save']}", "{$lang['p_del']}", "{$lang['ajax_info']}"];
var menu_short     = '{$lang['menu_short']}';
var menu_full      = '{$lang['menu_full']}';
var menu_profile   = '{$lang['menu_profile']}';
var menu_send      = '{$lang['menu_send']}';
var menu_uedit     = '{$lang['menu_uedit']}';
var dle_info       = '{$lang['p_info']}';
var dle_confirm    = '{$lang['p_confirm']}';
var dle_prompt     = '{$lang['p_prompt']}';
var dle_req_field  = '{$lang['comm_req_f']}';
var dle_del_agree  = '{$lang['news_delcom']}';
var dle_spam_agree = '{$lang['mark_spam']}';
var dle_c_title    = '{$lang['complaint_title']}';
var dle_complaint  = '{$lang['add_to_complaint']}';
var dle_mail       = '{$lang['reply_mail']}';
var dle_big_text   = '{$lang['big_text']}';
var dle_orfo_title = '{$lang['orfo_title']}';
var dle_p_send     = '{$lang['p_send']}';
var dle_p_send_ok  = '{$lang['p_send_ok']}';
var dle_save_ok    = '{$lang['n_save_ok']}';
var dle_reply_title= '{$lang['reply_comments']}';
var dle_tree_comm  = '{$dle_tree_comments}';
var dle_del_news   = '{$lang['news_delnews']}';
var dle_sub_agree  = '{$lang['subscribe_info_3']}';
var dle_captcha_type  = '{$config['allow_recaptcha']}';
var DLEPlayerLang     = {prev: '{$lang['player_prev']}',next: '{$lang['player_next']}',play: '{$lang['player_play']}',pause: '{$lang['player_pause']}',mute: '{$lang['player_mute']}', unmute: '{$lang['player_unmute']}', settings: '{$lang['player_settings']}', enterFullscreen: '{$lang['player_fullscreen']}', exitFullscreen: '{$lang['player_efullscreen']}', speed: '{$lang['player_speed']}', normal: '{$lang['player_normal']}', quality: '{$lang['player_quality']}', pip: '{$lang['player_pip']}'};\n
HTML;

if ($user_group[$member_id['user_group']]['allow_all_edit']) {

	$ajax .= <<<HTML
var dle_notice     = '{$lang['btn_notice']}';
var dle_p_text     = '{$lang['p_text']}';
var dle_del_msg    = '{$lang['p_message']}';
var allow_dle_delete_news   = true;\n
HTML;

} else {

	$ajax .= <<<HTML
var allow_dle_delete_news   = false;\n
HTML;

}

if ($config['fast_search'] AND $user_group[$member_id['user_group']]['allow_search']) {

	$ajax .= <<<HTML
var dle_search_delay   = false;
var dle_search_value   = '';
HTML;

	$onload_scripts[] = "FastSearch();";

}

if (strpos ( $tpl->result['content'], "<pre" ) !== false) {

	$js_array[] = "engine/classes/highlight/highlight.code.js";
	$onload_scripts[] = "$('pre code').each(function(i, e) {hljs.highlightBlock(e, null)});";

}


if ( (strpos ( $tpl->result['content'], "hs.expand" ) !== false OR strpos ( $tpl->copy_template, "hs.expand" ) !== false OR strpos ( $tpl->result['content'], "highslide" ) !== false OR strpos ( $tpl->copy_template, "highslide" ) !== false) AND $dle_module != "addnews") {

	$js_array[] = "engine/classes/highslide/highslide.js";

	if ($config['thumb_dimming']) $dimming = "hs.dimmingOpacity = 0.60;"; else $dimming = "";

	if ($config['thumb_gallery'] AND ($dle_module == "showfull" OR $dle_module == "static") ) {

	  $gallery = "hs.slideshowGroup='fullnews'; hs.addSlideshow({slideshowGroup: 'fullnews', interval: 4000, repeat: false, useControls: true, fixedControls: 'fit', overlayOptions: { opacity: .75, position: 'bottom center', hideOnMouseOut: true } });";

	} else $gallery = "";

	switch ( $config['outlinetype'] ) {

		case 1 :
			$type = "hs.wrapperClassName = 'wide-border';";
			break;

		case 2 :
			$type = "hs.wrapperClassName = 'borderless';";
			break;

		case 3 :
			$type = "hs.wrapperClassName = 'less';\nhs.outlineType = null;";
			break;

		default :
			$type = "hs.wrapperClassName = 'rounded-white';\nhs.outlineType = 'rounded-white';";
			break;


	}

	$onload_scripts[] = <<<HTML

hs.graphicsDir = '{$config['http_home_url']}engine/classes/highslide/graphics/';
{$type}
hs.numberOfImagesToPreload = 0;
hs.captionEval = 'this.thumb.alt';
hs.showCredits = false;
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
{$dimming}
hs.lang = { loadingText : '{$lang['loading']}', playTitle : '{$lang['thumb_playtitle']}', pauseTitle:'{$lang['thumb_pausetitle']}', previousTitle : '{$lang['thumb_previoustitle']}', nextTitle :'{$lang['thumb_nexttitle']}',moveTitle :'{$lang['thumb_movetitle']}', closeTitle :'{$lang['thumb_closetitle']}',fullExpandTitle:'{$lang['thumb_expandtitle']}',restoreTitle:'{$lang['thumb_restore']}',focusTitle:'{$lang['thumb_focustitle']}',loadingTitle:'{$lang['thumb_cancel']}'
};
{$gallery}

HTML;

	$tpl->result['content'] = preg_replace_callback ( "#slideshowGroup\: '(.+?)'#",
		function ($matches) {
			global $onload_scripts;
			$matches[1] = totranslit(trim($matches[1]));
			$onload_scripts[$matches[1]] = "hs.addSlideshow({slideshowGroup: '{$matches[1]}', interval: 4000, repeat: false, useControls: true, fixedControls: 'fit', overlayOptions: { opacity: .75, position: 'bottom center', hideOnMouseOut: true } });";

			return $matches[0];
		},		
	$tpl->result['content'] );

	$tpl->copy_template = preg_replace_callback ( "#slideshowGroup\: '(.+?)'#",
		function ($matches) {
			global $onload_scripts;
			$matches[1] = totranslit(trim($matches[1]));
			$onload_scripts[$matches[1]] = "hs.addSlideshow({slideshowGroup: '{$matches[1]}', interval: 4000, repeat: false, useControls: true, fixedControls: 'fit', overlayOptions: { opacity: .75, position: 'bottom center', hideOnMouseOut: true } });";

			return $matches[0];
		},		
	$tpl->copy_template );
}

if ($config['image_lazy']) {
	$js_array[] = "engine/classes/js/lazyload.js";
}

if ( $config['allow_share'] AND ($dle_module == "showfull" OR $dle_module == "static") ) {

	if ( preg_match("/(msie)/i", $_SERVER['HTTP_USER_AGENT']) ) {

		$js_array[] = "engine/classes/masha/ierange.js";
		$js_array[] = "engine/classes/masha/masha.js";

	} else $js_array[] = "engine/classes/masha/masha.js";
}

if (strpos ( $tpl->result['content'], "dleplyrplayer" ) !== false OR strpos ( $tpl->copy_template, "dleplyrplayer" ) !== false) {

  $css_array[] = "engine/classes/html5player/plyr.css";
  $js_array[] = "engine/classes/html5player/plyr.js";
  
} elseif (strpos ( $tpl->result['content'], "dleaudioplayer" ) !== false OR strpos ( $tpl->result['content'], "dlevideoplayer" ) !== false OR strpos ( $tpl->copy_template, "dlevideoplayer" ) !== false OR strpos ( $tpl->copy_template, "dleaudioplayer" ) !== false) {
	
  $css_array[] = "engine/classes/html5player/player.css";
  $js_array[] = "engine/classes/html5player/player.js";
  
}

if( $user_group[$member_id['user_group']]['allow_pm'] ) {
	$allow_comments_ajax = true;
}

if ($allow_comments_ajax AND ($config['allow_quick_wysiwyg'] == "2" OR $config['allow_comments_wysiwyg'] == "2") AND $dle_module != "addnews") {

    $js_array[] = "engine/editor/jscripts/tiny_mce/tinymce.min.js";

}

if ($allow_comments_ajax AND ($config['allow_quick_wysiwyg'] == "1" OR $config['allow_comments_wysiwyg'] == "1") AND $dle_module != "addnews" ) {
	
	$js_array[] = "engine/skins/codemirror/js/code.js";
	$js_array[] = "engine/editor/jscripts/froala/editor.js";
	$js_array[] = "engine/editor/jscripts/froala/languages/{$lang['wysiwyg_language']}.js";
	$css_array[] = "engine/editor/jscripts/froala/fonts/font-awesome.css";
	$css_array[] = "engine/editor/jscripts/froala/css/editor.css";

}

if ($config['allow_admin_wysiwyg'] == "1" OR $config['allow_site_wysiwyg'] == "1" OR $config['allow_static_wysiwyg'] == "1" OR $config['allow_quick_wysiwyg'] == "1" ) {
	$css_array[] = "engine/editor/css/default.css";

}

$js_array = build_css($css_array, $config)."\n".build_js($js_array, $config);

if( $_SERVER['QUERY_STRING'] AND !$tpl->result['content'] AND !$tpl->result['info'] AND stripos ( $tpl->copy_template, "{content}" ) !== false ) {

	@header( "HTTP/1.0 404 Not Found" );
	$need_404 = false;
	
	if( $config['own_404'] AND file_exists(ROOT_DIR . '/404.html') ) {
		@header("Content-type: text/html; charset=".$config['charset']);
		echo file_get_contents( ROOT_DIR . '/404.html' );
		die();
		
	} else msgbox( $lang['all_err_1'], $lang['news_err_27'] );

}

if($need_404) {
	@header( "HTTP/1.0 404 Not Found" );
}

if ( count($onload_scripts) ) {
	
	$onload_scripts =implode("\n", $onload_scripts);

	$ajax .= <<<HTML

jQuery(function($){
{$onload_scripts}
});
HTML;

} else $onload_scripts="";

$ajax .= <<<HTML

//-->
</script>
HTML;

if( ($tpl->result['content'] AND isset($tpl->result['navigation']) AND $tpl->result['navigation']) OR defined('CUSTOMNAVIGATION') ) {

	$tpl->set( '[navigation]', "" );
	$tpl->set( '[/navigation]', "" );
	$tpl->set_block( "'\\[not-navigation\\](.*?)\\[/not-navigation\\]'si", "" );
		
	if( stripos ( $tpl->copy_template, "{navigation}" ) !== false )	{

		$tpl->result['content'] = str_replace ( '{newsnavigation}', '', $tpl->result['content'] );
		$tpl->copy_template = str_replace ( '{newsnavigation}', '', $tpl->copy_template );
			
		if( $tpl->result['navigation'] AND stripos ( $tpl->copy_template, "{content}" ) !== false ) {
			
			$tpl->set( '{navigation}', $tpl->result['navigation'] );
			
		} else {
			
			$tpl->set( '{navigation}', $custom_navigation );
			
		}

	} else {
		
		$tpl->result['content'] = str_replace ( '{newsnavigation}', $tpl->result['navigation'], $tpl->result['content'] );
		$tpl->copy_template = str_replace ( '{newsnavigation}', $custom_navigation, $tpl->copy_template );

	}

} else {
	
	$tpl->set( '{navigation}', "" );
	$tpl->set( '[not-navigation]', "" );
	$tpl->set( '[/not-navigation]', "" );
	$tpl->set_block( "'\\[navigation\\](.*?)\\[/navigation\\]'si", "" );
	
}


if (stripos ( $tpl->copy_template, "{jsfiles}" ) !== false) {
	$tpl->set ( '{headers}', $metatags );
	$tpl->set ( '{jsfiles}', $js_array );
} else {
	$tpl->set ( '{headers}', $metatags."\n".$js_array );
}

$tpl->set ( '{AJAX}', $ajax );
$tpl->set ( '{info}',  $tpl->result['info'] );

$tpl->set ( '{content}', "<div id='dle-content'>" . $tpl->result['content'] . "</div>" );

$tpl->compile ( 'main' );

if ($config['allow_links']) $tpl->result['main'] = replace_links ( $tpl->result['main'], $replace_links['all'] );

$tpl->result['main'] = str_ireplace( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $tpl->result['main'] );

if ($replace_url) $tpl->result['main'] = str_replace ( $replace_url[0]."/", $replace_url[1]."/", $tpl->result['main'] );

if($remove_canonical) {
	$tpl->result['main'] = preg_replace( "#<link rel=['\"]canonical['\"](.+?)>#i", "", $tpl->result['main'] );
}

$tpl->result['main'] = str_replace ( 'src="http://'.$_SERVER['HTTP_HOST'].'/', 'src="/', $tpl->result['main'] );
$tpl->result['main'] = str_replace ( 'srcset="http://'.$_SERVER['HTTP_HOST'].'/', 'srcset="/', $tpl->result['main'] );
$tpl->result['main'] = str_replace ( 'src="https://'.$_SERVER['HTTP_HOST'].'/', 'src="/', $tpl->result['main'] );
$tpl->result['main'] = str_replace ( 'srcset="https://'.$_SERVER['HTTP_HOST'].'/', 'srcset="/', $tpl->result['main'] );

echo $tpl->result['main'];

$tpl->global_clear();

$db->close();

echo "\n<!-- DataLife Engine Copyright SoftNews Media Group (http://dle-news.ru) -->\r\n";

GzipOut();

?>