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
 File: static.php
-----------------------------------------------------
 Use: view static pages
=====================================================
*/

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$name = @$db->safesql( trim( totranslit( $_GET['page'], true, false ) ) );

if(!isset($static_result['id']) OR !$static_result['id'] ) $static_result = $db->super_query( "SELECT * FROM " . PREFIX . "_static WHERE name='{$name}'" ); else $static_result['id'] = intval($static_result['id']);

if( $static_result['id'] ) {
	
	if ($static_result['allow_count']) $db->query( "UPDATE " . PREFIX . "_static SET views=views+1 WHERE id='{$static_result['id']}'" );
	
	$static_result['grouplevel'] = explode( ',', $static_result['grouplevel'] );
	
	if( $static_result['date'] ) $_DOCUMENT_DATE = $static_result['date'];

	$disable_index = $static_result['disable_index'];

	if ($static_result['password'] AND $member_id['user_group'] != 1 ) {

		if( trim($_POST['static_password']) ) {
			
			$pass = explode("\n", str_replace("\r", "", $static_result['password']));
			$n_passwords = array();
			
			foreach ($pass as $value) {
				$value = trim( $value );
				if($value) $n_passwords[] = $value;
			}
			
			unset($value);unset($pass);
			
			if (in_array(trim($_POST['static_password']), $n_passwords)) {
				$_SESSION['static_pass_'.$static_result['id'].''] = 1;
			}

			unset($n_passwords);
		}
	
		if( $_SESSION['static_pass_'.$static_result['id'].''] ) $static_result['password'] = false;

	} else $static_result['password'] = false;


	if( $static_result['password'] ) {
		
		$form_n_pass = <<<HTML
<form method="post" action="">
{$lang['enter_n_pass_1']}
<br>{status}<br>
{$lang['enter_n_pass_2']}&nbsp;&nbsp;<input type="password" name="static_password" style="width:200px">
<br><br>
<button type="submit" class="bbcodes">{$lang['enter_n_pass_3']}</button>
</form>
HTML;

		if( trim($_POST['static_password']) ) {
			$form_n_pass = str_replace("{status}", "<br>".$lang['enter_n_pass_4']."<br>", $form_n_pass);
		} else $form_n_pass = str_replace("{status}","", $form_n_pass);
		
		@header( "HTTP/1.1 403 Forbidden" );
		msgbox( $lang['enter_n_pass'], $form_n_pass );
		
	} elseif( $static_result['grouplevel'][0] != "all" AND !in_array( $member_id['user_group'], $static_result['grouplevel'] ) ) {
		@header( "HTTP/1.1 403 Forbidden" );
		msgbox( $lang['all_err_1'], $lang['static_denied'] );

	} else {

		if ($config['allow_alt_url'] AND $config['seo_control'] AND $static_result['name'] != "dle-rules-page" AND ( isset($_GET['seourl']) OR strpos ( $_SERVER['REQUEST_URI'], "?" ) !== false ) ) {


			if ($_GET['seourl'] != $static_result['name'] OR strpos ( $_SERVER['REQUEST_URI'], "?" ) !== false ) {

				$re_url = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
				$re_url = reset ( $re_url );

				header("HTTP/1.0 301 Moved Permanently");
				header("Location: {$re_url}{$static_result['name']}.html");
				die("Redirect");

			}	
		}

		$template = stripslashes( $static_result['template'] );
		$static_descr = stripslashes( strip_tags( $static_result['descr'] ) );
		
		if( $static_result['metakeys'] == '' AND $static_result['metadescr'] == '' ) create_keywords( $template );
		else {
			$metatags['keywords'] = $static_result['metakeys'];
			$metatags['description'] = $static_result['metadescr'];
		}

		if ($static_result['metatitle']) $metatags['header_title'] = $static_result['metatitle'];

		if( $config['allow_alt_url'] ) {

			if( $static_result['name'] == "dle-rules-page" ) $static_result['name'] = "rules";

			$full_link = $config['http_home_url'] . $static_result['name'] . ".html";
			
		} else {
			
			$full_link = $config['http_home_url'] . "index.php?do=static&page=" . $static_result['name'];
			
		}
		
		if( $static_result['allow_template'] OR $view_template == "print" ) {
			
			if( $view_template == "print" ) $tpl->load_template( 'static_print.tpl' );
			elseif( $static_result['tpl'] != '' ) $tpl->load_template( $static_result['tpl'] . '.tpl' );
			else $tpl->load_template( 'static.tpl' );
			
			if( ! $news_page ) $news_page = 1;
			
			if( $view_template == "print" ) {
				
				$template = str_replace( "{PAGEBREAK}", "", $template );
				$template = str_replace( "{pages}", "", $template );
				$template = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", "", $template );
			
			} else {
				
				$news_seiten = explode( "{PAGEBREAK}", $template );
				$anzahl_seiten = count( $news_seiten );
				
				if( $news_page <= 0 or $news_page > $anzahl_seiten ) {
					$news_page = 1;
				}
				
				$template = $news_seiten[$news_page - 1];
				
				$template = preg_replace( '#(\A[\s]*<br[^>]*>[\s]*|<br[^>]*>[\s]*\Z)#is', '', $template ); // remove <br/> at end of string

				if ($config['seo_control'] AND isset($_GET['news_page']) AND ($_GET['news_page'] < 2 OR $_GET['news_page'] > $anzahl_seiten )) {
			
						$re_url = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
						$re_url = reset ( $re_url );
		
							
						header("HTTP/1.0 301 Moved Permanently");
						header("Location: {$re_url}{$static_result['name']}.html");
						die("Redirect");
					
				}

				$news_seiten = "";
				unset( $news_seiten );
				
				if( $anzahl_seiten > 1 ) {
					
					if( $news_page < $anzahl_seiten ) {
						$pages = $news_page + 1;
						if( $config['allow_alt_url'] ) {
							$nextpage = " | <a href=\"" . $config['http_home_url'] . "page," . $pages . "," . $static_result['name'] . ".html\">" . $lang['news_next'] . "</a>";
						} else {
							$nextpage = " | <a href=\"$PHP_SELF?do=static&page=" . $static_result['name'] . "&news_page=" . $pages . "\">" . $lang['news_next'] . "</a>";
						}
					}
					
					if( $news_page > 1 ) {
						$pages = $news_page - 1;
						
						if( $config['allow_alt_url'] ) {
							$full_link = $config['http_home_url'] . "page," . $news_page . "," . $static_result['name'] . ".html";
						} else {
							$full_link = "$PHP_SELF?do=static&page=" . $static_result['name'] . "&news_page=" . $news_page;
						}
						
						if ($pages == 1 ) {
							if( $config['allow_alt_url'] ) {
								$prevpage = "<a href=\"" . $config['http_home_url'] . $static_result['name'] . ".html\">" . $lang['news_prev'] . "</a> | ";
							} else {
								$prevpage = "<a href=\"$PHP_SELF?do=static&page=" . $static_result['name'] . "\">" . $lang['news_prev'] . "</a> | ";
							}
						} else {
							if( $config['allow_alt_url'] ) {
								$prevpage = "<a href=\"" . $config['http_home_url'] . "page," . $pages . "," . $static_result['name'] . ".html\">" . $lang['news_prev'] . "</a> | ";
							} else {
								$prevpage = "<a href=\"$PHP_SELF?do=static&page=" . $static_result['name'] . "&news_page=" . $pages . "\">" . $lang['news_prev'] . "</a> | ";
							}
						}

					}
					
					$tpl->set( '{pages}', $prevpage . $lang['news_site'] . " " . $news_page . $lang['news_iz'] . $anzahl_seiten . $nextpage );
					
					if( $config['allow_alt_url'] ) {
						$replacepage = "<a href=\"" . $config['http_home_url'] . "page," . "\\1" . "," . $static_result['name'] . ".html\">\\2</a>";
					} else {
						$replacepage = "<a href=\"$PHP_SELF?do=static&page=" . $static_result['name'] . "&news_page=\\1\">\\2</a>";
					}
					
					$template = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", $replacepage, $template );
				
				} else {
					
					$tpl->set( '{pages}', '' );
					$template = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", "", $template );
				
				}
			
			}
			
			if( $config['allow_alt_url'] ) {
				$print_link = $config['http_home_url'] . "print:" . $static_result['name'] . ".html";
				
			} else {
				$print_link = $config['http_home_url'] . "index.php?mod=print&do=static&amp;page=" . $static_result['name'];
			}
			
			if( @date( "Ymd", $static_result['date'] ) == date( "Ymd", $_TIME ) ) {
				
				$tpl->set( '{date}', $lang['time_heute'] . langdate( ", H:i", $static_result['date'] ) );
			
			} elseif( @date( "Ymd", $static_result['date'] ) == date( "Ymd", ($_TIME - 86400) ) ) {
				
				$tpl->set( '{date}', $lang['time_gestern'] . langdate( ", H:i", $static_result['date'] ) );
			
			} else {
				
				$tpl->set( '{date}', langdate( $config['timestamp_active'], $static_result['date'] ) );
			
			}

			$news_date = $static_result['date'];	
			$tpl->copy_template = preg_replace_callback ( "#\{date=(.+?)\}#i", "formdate", $tpl->copy_template );
			
			if ($config['allow_links'] AND function_exists('replace_links') AND isset($replace_links['static'])) $template = replace_links ( $template, $replace_links['static'] );

			if ($config['image_lazy'] AND $view_template != "print" ) $template = preg_replace_callback ( "#<img(.+?)>#i", "enable_lazyload", $template );

			$tpl->set( '{description}', $static_descr );
			$tpl->set( '{static}', $template );
			$tpl->set( '{views}', number_format($static_result['views'], 0, ',', ' ') );

			if( $user_group[$member_id['user_group']]['admin_static'] ) {
				
				$tpl->set( '[edit]', "<a href=\"" . $config['http_home_url'] . $config['admin_path']."?mod=static&action=doedit&id=" . $static_result['id'] . "\"  target=\"_blank\">" );
				$tpl->set( '[/edit]', "</a>" );
			
			} else $tpl->set_block( "'\\[edit\\](.*?)\\[/edit\\]'si", "" );

			if ($config['allow_search_print']) {

				$tpl->set( '[print-link]', "<a href=\"" . $print_link . "\">" );
				$tpl->set( '[/print-link]', "</a>" );

			} else {

				$tpl->set( '[print-link]', "<a href=\"" . $print_link . "\" rel=\"nofollow\">" );
				$tpl->set( '[/print-link]', "</a>" );

			}

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

			if ( preg_match( "#\\{text limit=['\"](.+?)['\"]\\}#i", $tpl->copy_template, $matches ) ) {
				$count= intval($matches[1]);
				
				$stext = preg_replace( "#<!--TBegin(.+?)<!--TEnd-->#is", "", $template );
				$stext = preg_replace( "#<!--MBegin(.+?)<!--MEnd-->#is", "", $stext );
				$stext = preg_replace( "'\[attachment=(.*?)\]'si", "", $stext );
				$stext = preg_replace ( "#\[hide(.*?)\](.+?)\[/hide\]#is", "", $stext );
					
				$stext = str_replace( "</p><p>", " ", $stext );
				$stext = strip_tags( $stext, "<br>" );
				$stext = trim(str_replace( "<br>", " ", str_replace( "<br />", " ", str_replace( "\n", " ", str_replace( "\r", "", $stext ) ) ) ));
	
				if( $count AND dle_strlen( $stext, $config['charset'] ) > $count ) {
						
					$stext = dle_substr( $stext, 0, $count, $config['charset'] );
						
					if( ($temp_dmax = dle_strrpos( $stext, ' ', $config['charset'] )) ) $stext = dle_substr( $stext, 0, $temp_dmax, $config['charset'] );
					
				}
	
				$tpl->set( $matches[0], $stext );
	
			}

			if (stripos ( $tpl->copy_template, "{image-" ) !== false) {
	
				$images = array();
				preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $template, $media);
				$data=preg_replace('/(img|src)("|\'|="|=\')(.*)/i',"$3",$media[0]);
		
				foreach($data as $url) {
					$info = pathinfo($url);
					if (isset($info['extension'])) {
						if ($info['filename'] == "spoiler-plus" OR $info['filename'] == "spoiler-minus" OR strpos($info['dirname'], 'engine/data/emoticons') !== false) continue;
						$info['extension'] = strtolower($info['extension']);
						if (($info['extension'] == 'jpg') || ($info['extension'] == 'jpeg') || ($info['extension'] == 'gif') || ($info['extension'] == 'png') || ($info['extension'] == 'webp')) array_push($images, $url);
					}
				}
		
				if ( count($images) ) {
					$i=0;
					foreach($images as $url) {
						$i++;
						$tpl->copy_template = str_replace( '{image-'.$i.'}', $url, $tpl->copy_template );
						$tpl->copy_template = str_replace( '[image-'.$i.']', "", $tpl->copy_template );
						$tpl->copy_template = str_replace( '[/image-'.$i.']', "", $tpl->copy_template );
					}
		
				}
		
				$tpl->copy_template = preg_replace( "#\[image-(.+?)\](.+?)\[/image-(.+?)\]#is", "", $tpl->copy_template );
				$tpl->copy_template = preg_replace( "#\\{image-(.+?)\\}#i", "{THEME}/dleimages/no_image.jpg", $tpl->copy_template );
		
			}
		
			
			$tpl->compile( 'content' );
			
			if( $_GET['page'] == "dle-rules-page" ) if( $do != "register" ) {
				
				$tpl->result['content'] = str_ireplace( '{ACCEPT-DECLINE}', "", $tpl->result['content'] );
			
			} else {
				
				$tpl->result['content'] = str_ireplace( '{ACCEPT-DECLINE}', "<form  method=\"post\" name=\"registration\" id=\"registration\" action=\"\"><input type=\"submit\" class=\"bbcodes\" value=\"{$lang['rules_accept']}\" />&nbsp;&nbsp;&nbsp;<input type=\"button\" class=\"bbcodes\" value=\"{$lang['rules_decline']}\" onclick=\"history.go(-1); return false;\" /><input name=\"do\" type=\"hidden\" id=\"do\" value=\"register\" /><input name=\"dle_rules_accept\" type=\"hidden\" id=\"dle_rules_accept\" value=\"yes\" /></form>", $tpl->result['content'] );
			
			}
			
			$tpl->clear();
		
		} else {
			
			if ($config['allow_links'] AND function_exists('replace_links') AND isset($replace_links['static'])) $template = replace_links ( $template, $replace_links['static'] );
			
			if ($config['image_lazy'] AND $view_template != "print" ) $template = preg_replace_callback ( "#<img(.+?)>#i", "enable_lazyload", $template );
			
			$tpl->result['content'] = $template;
		}

			
		$social_tags['site_name'] = $config['home_title'];
		$social_tags['type'] = 'article';
		$social_tags['title'] = htmlspecialchars( $static_descr, ENT_QUOTES, $config['charset'] );
		
		if( $config['start_site'] == 3 AND $static_result['name'] == "main" ) {
			$social_tags['url'] = $canonical = $config['http_home_url'];
		} else {
			$social_tags['url'] = $canonical = $full_link;
		}
		
		if( $_GET['page'] == "dle-rules-page" AND $do == "register" ) {
			$social_tags['url'] = $canonical = $config['http_home_url'] . "index.php?do=register";
		}
			
		$images = array();
		preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $template, $media);
		$data=preg_replace('/(img|src)("|\'|="|=\')(.*)/i',"$3",$media[0]);
		
		foreach($data as $url) {
			$info = pathinfo($url);
			if (isset($info['extension'])) {
				if ($info['filename'] == "spoiler-plus" OR $info['filename'] == "spoiler-minus" OR strpos($info['dirname'], 'engine/data/emoticons') !== false) continue;
				$info['extension'] = strtolower($info['extension']);
				if (($info['extension'] == 'jpg' || $info['extension'] == 'jpeg' || $info['extension'] == 'gif' || $info['extension'] == 'png' || $info['extension'] == 'webp') AND !in_array($url, $images) ) array_push($images, $url);
			}
		}
			
		if ( count($images) ) $social_tags['image'] = $images[0];

		if ( preg_match("#<!--dle_video_begin:(.+?)-->#is", $template, $media) ){
			$media[1] = explode( ",", trim( $media[1] ) );

			if (count($media[1]) > 1 )  $media[1] = $media[1][1]; else $media[1] = $media[1][0];

			$media[1] = explode( "|", $media[1] );
			$social_tags['video'] = $media[1][0];

		}

		if ( preg_match("#<!--dle_audio_begin:(.+?)-->#is", $template, $media) ){
			$media[1] = explode( ",", trim( $media[1] ) );

			if (count($media[1]) > 1 )  $media[1] = $media[1][1]; else $media[1] = $media[1][0];

			$social_tags['audio'] = $media[1];

		}

		$allcontent = str_replace("&amp;amp;", "&amp;", htmlspecialchars(strip_tags( $template ), ENT_COMPAT, $config['charset'] ));
		$allcontent = preg_replace( "'{banner_(.*?)}'si", "", $allcontent );
		$allcontent = preg_replace( "'\\[banner_(.*?)\\](.*?)\\[/banner_(.*?)\\]'si", "", $allcontent );
		$allcontent = preg_replace( "#\[hide(.*?)\](.+?)\[/hide\]#is", "", $allcontent );
		$allcontent = preg_replace( "'\[attachment=(.*?)\]'si", "", $allcontent );
		$allcontent = preg_replace( "'\[page=(.*?)\](.*?)\[/page\]'si", "", $allcontent );
		$allcontent = str_replace( "{PAGEBREAK}", "", $allcontent );
	
		if(dle_strlen( $allcontent, $config['charset'] ) > 300 ) {
	
			$allcontent = dle_substr( $allcontent, 0, 300, $config['charset'] );
				
			if( ($temp_dmax = dle_strrpos( $allcontent, ' ', $config['charset'] )) ) $allcontent = dle_substr( $allcontent, 0, $temp_dmax, $config['charset'] );
				
		}
		
		if( $allcontent ) $social_tags['description'] = trim(preg_replace('/\s+/u', ' ', $allcontent));

		unset($allcontent);

		if (stripos ( $tpl->result['content'], "[hide" ) !== false ) {
			
			$tpl->result['content'] = preg_replace_callback ( "#\[hide(.*?)\](.+?)\[/hide\]#is", 
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
	
			}, $tpl->result['content'] );
		}

		if( $config['files_allow'] ) if( strpos( $tpl->result['content'], "[attachment=" ) !== false ) {
			
			$tpl->result['content'] = show_attach( $tpl->result['content'], $static_result['id'], true );
		
		}

		if ($config['rss_informer'] AND count ($informers) ) {
			foreach ( $informers as $name => $value ) {
				$tpl->result['content'] = str_replace ( "{inform_" . $name . "}", $value, $tpl->result['content'] );
			}
		}

		if (stripos ( $tpl->result['content'], "[static=" ) !== false) {
			$tpl->result['content'] = preg_replace_callback ( "#\\[(static)=(.+?)\\](.*?)\\[/static\\]#is", "check_static", $tpl->result['content'] );
		}

		if (stripos ( $tpl->result['content'], "[not-static=" ) !== false) {
			$tpl->result['content'] = preg_replace_callback ( "#\\[(not-static)=(.+?)\\](.*?)\\[/not-static\\]#is", "check_static", $tpl->result['content'] );
		}

		if( $config['allow_banner'] ) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/banners.php'));
		
		if( $config['allow_banner'] AND count( $banners ) ) {
			
			foreach ( $banners as $name => $value ) {
				$tpl->result['content'] = str_replace( "{banner_" . $name . "}", $value, $tpl->result['content'] );
				$tpl->result['content'] = str_replace ( "[banner_" . $name . "]", "", $tpl->result['content'] );
				$tpl->result['content'] = str_replace ( "[/banner_" . $name . "]", "", $tpl->result['content'] );
			}
		}
		
		$tpl->result['content'] = preg_replace( "'{banner_(.*?)}'i", "", $tpl->result['content'] );
		$tpl->result['content'] = preg_replace( "'\\[banner_(.*?)\\](.*?)\\[/banner_(.*?)\\]'si", "", $tpl->result['content'] );

	}
	
} else {
	
	@header( "HTTP/1.0 404 Not Found" );
	
	if( $config['own_404'] AND file_exists(ROOT_DIR . '/404.html') ) {
			@header("Content-type: text/html; charset=".$config['charset']);
			echo file_get_contents( ROOT_DIR . '/404.html' );
			die();
			
	} else {
		$lang['static_page_err'] = str_replace ("{page}", $name.".html", $lang['static_page_err']);
		msgbox( $lang['all_err_1'], $lang['static_page_err'] );
	}


}
?>