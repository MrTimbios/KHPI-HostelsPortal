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
 File: addnews.php
-----------------------------------------------------
 Use: Add news
=====================================================
*/

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$allow_addnews = true;

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));
$parse = new ParseFilter();

$id = (isset( $_REQUEST['id'] )) ? intval( $_REQUEST['id'] ) : 0;
$found = false;

if( $config['allow_alt_url'] ) $canonical = $config['http_home_url'] . "addnews.html"; else $canonical = $PHP_SELF."?do=addnews";

if( $id AND $is_logged AND $user_group[$member_id['user_group']]['allow_adds'] ) {
	$row = $db->super_query( "SELECT id, autor, category, tags FROM " . PREFIX . "_post WHERE id = '{$id}' AND approve = '0'" );
	if( $id == $row['id'] AND ($member_id['name'] == $row['autor'] OR $user_group[$member_id['user_group']]['allow_all_edit']) ) $found = true;
	else $found = false;
}

if( $id AND !$found){
	$lang['add_err_9'] = $lang['add_err_10'];
	$allow_addnews = false;
}

if( $config['max_moderation'] AND !$user_group[$member_id['user_group']]['moderation'] AND !$found ) {
	
	$stats_approve = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE approve != '1'" );
	$stats_approve = $stats_approve['count'];
	
	if( $stats_approve >= $config['max_moderation'] ) $allow_addnews = false;

}

if ($is_logged AND $config['news_restricted'] AND (($_TIME - $member_id['reg_date']) < ($config['news_restricted'] * 86400)) ) {
	$lang['add_err_9'] = str_replace( '{days}', intval($config['news_restricted']), $lang['news_info_7'] );
	$allow_addnews = false;
}

if( $member_id['restricted'] AND $member_id['restricted_days'] AND $member_id['restricted_date'] < $_TIME ) {
	
	$member_id['restricted'] = 0;
	$db->query( "UPDATE LOW_PRIORITY " . USERPREFIX . "_users SET restricted='0', restricted_days='0', restricted_date='' WHERE user_id='{$member_id['user_id']}'" );

}

if( $member_id['restricted'] == 1 or $member_id['restricted'] == 3 ) {
	
	if( $member_id['restricted_days'] ) {
		
		$lang['news_info_4'] = str_replace( '{date}', langdate( "j F Y H:i", $member_id['restricted_date'] ), $lang['news_info_4'] );
		$lang['add_err_9'] = $lang['news_info_4'];
	
	} else {
		
		$lang['add_err_9'] = $lang['news_info_5'];
	
	}
	
	$allow_addnews = false;

}

if( ! $allow_addnews ) {
	
	msgbox( $lang['all_info'], $lang['add_err_9'] . "<br /><br /><a href=\"javascript:history.go(-1)\">$lang[all_prev]</a>" );

} else {
	
	if( isset( $_REQUEST['mod'] ) AND $_REQUEST['mod'] == "addnews" AND $is_logged AND $user_group[$member_id['user_group']]['allow_adds'] ) {
	
		@header('X-XSS-Protection: 0;');
		
		$stop = "";
		
		if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
			$stop .= "<li>" . $lang['sess_error'] . "</li>";
		}
		
		$allow_comm = intval( $_POST['allow_comm'] );

		if( $user_group[$member_id['user_group']]['allow_main'] ) $allow_main = intval( $_POST['allow_main'] );
		else $allow_main = 0;
		
		$allow_rss_dzen = 1;
		$allow_rss_turbo = 1;
		$disable_rss_dzen = 0;
		$disable_rss_turbo = 0;
		$approve = intval( $_POST['approve'] );
		$allow_rating = intval( $_POST['allow_rating'] );
		
		if( $user_group[$member_id['user_group']]['allow_fixed'] ) $news_fixed = intval( $_POST['news_fixed'] );
		else $news_fixed = 0;
		
		if( !is_array($_POST['catlist']) ) $_POST['catlist'] = array ();
		
		if( !count( $_POST['catlist'] ) ) {
			
			$catlist = array ();
			$catlist[] = '0';
			
		} else $catlist = $_POST['catlist'];

		$category_list = array();
	
		foreach ( $catlist as $value ) {
			$category_list[] = intval($value);
		}
		
		$catlist = $category_list;
		$category_list = $db->safesql( implode( ',', $category_list ) );

		
		foreach ( $catlist as $selected ) {
			
			if($cat_info[$selected]['disable_main']) $allow_main = 0;
			if($cat_info[$selected]['disable_comments']) $allow_comm = 0;
			if($cat_info[$selected]['disable_rating']) $allow_rating = 0;
			
			if($member_id['user_group'] > 2 ) {
				if(!$cat_info[$selected]['enable_dzen']) $disable_rss_dzen ++;
				if(!$cat_info[$selected]['enable_turbo']) $disable_rss_turbo ++;
			}
		
		}
		
		if($member_id['user_group'] > 2 ) {
			if( $disable_rss_dzen AND $disable_rss_dzen = count($catlist) ) $allow_rss_dzen = 0;
			if( $disable_rss_turbo AND $disable_rss_turbo = count($catlist) ) $allow_rss_turbo = 0;
		}
	
		if( ! $config['allow_add_tags'] ) $_POST['tags'] = "";
		elseif( @preg_match( "/[\||\<|\>]/", $_POST['tags'] ) ) $_POST['tags'] = "";
		else $_POST['tags'] = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_POST['tags'] ) ) ), ENT_COMPAT, $config['charset'] ) );

		if ( $_POST['tags'] ) {
	
			$temp_array = array();
			$tags_array = array();
			$temp_array = explode (",", $_POST['tags']);
	
			if (count($temp_array)) {
	
				foreach ( $temp_array as $value ) {
					if( trim($value) ) $tags_array[] = trim( $value );
				}
	
			}
	
			if ( count($tags_array) ) $_POST['tags'] = implode(", ", $tags_array); else $_POST['tags'] = "";
	
		}

		if( trim( $_POST['vote_title'] != "" ) ) {
			
			$add_vote = 1;
			$vote_title =  $db->safesql( trim($parse->process(strip_tags ($_POST['vote_title']))) );
			$frage =  $db->safesql( trim($parse->process(strip_tags ($_POST['frage']))) );
			$vote_body = $db->safesql( $parse->BB_Parse( $parse->process( strip_tags ($_POST['vote_body']) ), false ) );
			$allow_m_vote = intval( $_POST['allow_m_vote'] );
		
		} else $add_vote = 0;
		
		if( ! $user_group[$member_id['user_group']]['moderation'] ) {
			$approve = 0;
			$allow_comm = 1;
			$allow_main = 1;
			$allow_rating = 1;
			$news_fixed = 0;
		}
		
		if( $approve ) $msg = $lang['add_ok_1'];
		else $msg = $lang['add_ok_2'];
		
		if ($member_id['cat_add']) $allow_list = explode( ',', $member_id['cat_add'] );
		else $allow_list = explode( ',', $user_group[$member_id['user_group']]['cat_add'] );
		
		if( $user_group[$member_id['user_group']]['moderation'] ) {
			foreach ( $catlist as $selected ) {
				if( $allow_list[0] != "all" AND !in_array( $selected, $allow_list ) ) {
					$approve = 0;
					$msg = $lang['add_ok_3'];
				}
			}
		}

		if($member_id['cat_allow_addnews']) $allow_list = explode( ',', $member_id['cat_allow_addnews'] );
		else $allow_list = explode( ',', $user_group[$member_id['user_group']]['cat_allow_addnews'] );
		
		if( $allow_list[0] != "all" ) {
			foreach ( $catlist as $selected ) {
				if( !in_array( $selected, $allow_list ) ) {
					$stop .= "<li>" . $lang['news_err_41'] . "</li>";
				}
			}
		}


		if ( !$user_group[$member_id['user_group']]['allow_html'] ) {

			$config['allow_site_wysiwyg'] = 0;
			$_POST['short_story'] = strip_tags ($_POST['short_story']);
			$_POST['full_story'] = strip_tags ($_POST['full_story']);

		}
		
		if( $config['allow_site_wysiwyg'] ) {

			$parse->allow_code = false;			
			$full_story = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['full_story'] ) ) );
			$short_story = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['short_story'] ) ) );
			$allow_br = 0;
		
		} else {
			
			$full_story = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['full_story'] ), false ) );
			$short_story = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['short_story'] ), false ) );
			$allow_br = 1;
		
		}


		if( $parse->not_allowed_text ) {
			$stop .= "<li>" . $lang['news_err_39'] . "</li>";
		}

		$title = $db->safesql( $parse->process( trim( strip_tags ($_POST['title']) ) ) );
		$alt_name = trim( $parse->process( stripslashes( strip_tags($_POST['alt_name']) ) ) );

		$add_module = "yes";
		$xfieldsaction = "init";
		$category = $catlist;
		$xf_existing = array();
		include (DLEPlugins::Check(ENGINE_DIR . '/inc/xfields.php'));
		
		if( $alt_name == "" OR !$alt_name ) $alt_name = totranslit( stripslashes( $title ), true, false );
		else $alt_name = totranslit( $alt_name, true, false );
		
		if( dle_strlen( $alt_name, $config['charset'] ) > 190 ) {
			$alt_name = dle_substr( $alt_name, 0, 190, $config['charset'] );
		}
		
		$alt_name = $db->safesql( $alt_name );
		
		if( !$title ) $stop .= $lang['add_err_1'];
		if( dle_strlen( $title, $config['charset'] ) > 200 ) $stop .= $lang['add_err_2'];
		
		if( $config['allow_alt_url'] AND !$config['seo_type'] ) {
			
			$db->query( "SELECT id, date FROM " . PREFIX . "_post WHERE alt_name ='{$alt_name}'" );
	
			while($found_news = $db->get_row()) {
				if( $found_news['id'] AND date( 'Y-m-d', strtotime( $found_news['date'] ) ) == date( 'Y-m-d', $_TIME ) ) {
					$stop .= "<li>" .$lang['add_err_11'] . "</li>";
					break;
				}	
			}
		
		}
	
		if ($config['create_catalog']) $catalog_url = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( trim( $title ) ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) ); else $catalog_url = "";

		if ( $user_group[$member_id['user_group']]['disable_news_captcha'] AND $member_id['news_num'] >= $user_group[$member_id['user_group']]['disable_news_captcha'] ) {

			$user_group[$member_id['user_group']]['news_question'] = false;
			$user_group[$member_id['user_group']]['news_sec_code'] = false;

		}
		
		if( $user_group[$member_id['user_group']]['news_sec_code']) {
			
			if ($config['allow_recaptcha']) {
	
				require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/recaptcha.php'));
				$sec_code = 1;
				$sec_code_session = false;
	
				if ($_POST['g-recaptcha-response']) {
				
					$reCaptcha = new ReCaptcha($config['recaptcha_private_key']);

					$resp = $reCaptcha->verifyResponse(get_ip(), $_POST['g-recaptcha-response'] );
				
				    if ($resp === null OR !$resp->success) {
	
							$stop .= "<li>" . $lang['recaptcha_fail'] . "</li>";
	
				    }
	
				} else $stop .= "<li>" . $lang['recaptcha_fail'] . "</li>";
	
			} elseif( $_REQUEST['sec_code'] != $_SESSION['sec_code_session'] OR !$_SESSION['sec_code_session'] ) $stop .= "<li>" . $lang['recaptcha_fail'] . "</li>";

		
		}

		if( $user_group[$member_id['user_group']]['news_question'] ) {
	
			if ( intval($_SESSION['question']) ) {
	
				$answer = $db->super_query("SELECT id, answer FROM " . PREFIX . "_question WHERE id='".intval($_SESSION['question'])."'");
	
				$answers = explode( "\n", $answer['answer'] );
	
				$pass_answer = false;
	
				if( function_exists('mb_strtolower') ) {
					$question_answer = trim(mb_strtolower($_POST['question_answer'], $config['charset']));
				} else {
					$question_answer = trim(strtolower($_POST['question_answer']));
				}
	
				if( count($answers) AND $question_answer ) {
					foreach( $answers as $answer ){
	
						if( function_exists('mb_strtolower') ) {
							$answer = trim(mb_strtolower($answer, $config['charset']));
						} else {
							$answer = trim(strtolower($answer));
						}
	
						if( $answer AND $answer == $question_answer ) {
							$pass_answer	= true;
							break;
						}
					}
				}
	
				if( !$pass_answer ) $stop .= $lang['reg_err_24'];
	
			} else $stop .= $lang['reg_err_24'];
		
		}

		if( $user_group[$member_id['user_group']]['flood_news'] ) {
			if( flooder( $member_id['name'],  $user_group[$member_id['user_group']]['flood_news'] )) {
				$stop .= "<li>" .$lang['news_err_4'] . " " . $lang['news_err_43'] . " {$user_group[$member_id['user_group']]['flood_news']} " . $lang['news_err_6']. "</li>";
			}
		}

		$max_detected = false;
		if( $user_group[$member_id['user_group']]['max_day_news'] AND !$found) {
			$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE date >= '".date("Y-m-d", $_TIME)."' AND date < '".date("Y-m-d", $_TIME)."' + INTERVAL 24 HOUR AND autor = '{$member_id['name']}'");
			if ($row['count'] >= $user_group[$member_id['user_group']]['max_day_news'] ) {
				$stop .= "<li>" .$lang['news_err_44'] . "</li>";
				$max_detected = true;
			}
		}

		if( $stop ) {
			$stop = "<ul>" . $stop . "</ul><a href=\"javascript:history.go(-1)\">$lang[all_prev]</a>";
			msgbox( $lang['add_err_6'], $stop  );
		}
		
		if( !$stop ) {
			
			$_SESSION['sec_code_session'] = 0;
			$_SESSION['question'] = false;
			
			if( $found ) {
				
				$db->query( "UPDATE " . PREFIX . "_post set title='$title', short_story='$short_story', full_story='$full_story', xfields='$filecontents', category='$category_list', alt_name='$alt_name', allow_comm='$allow_comm', approve='$approve', allow_main='$allow_main', fixed='$news_fixed', allow_br='$allow_br', tags='" . $_POST['tags'] . "' WHERE id='{$id}'" );
				$db->query( "UPDATE " . PREFIX . "_post_extras SET allow_rate='{$allow_rating}', votes='{$add_vote}' WHERE news_id='{$id}'" );				

				if( $_POST['tags'] != $row['tags'] OR $approve ) {
					$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$row['id']}'" );
					
					if( $_POST['tags'] != "" and $approve ) {
						
						$tags = array ();
						
						$_POST['tags'] = explode( ",", $_POST['tags'] );
						
						foreach ( $_POST['tags'] as $value ) {
							
							$tags[] = "('" . $row['id'] . "', '" . trim( $value ) . "')";
						}
						
						$tags = implode( ", ", $tags );
						$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tags );
					
					}
				}

				if( $category_list != $row['category'] OR $approve ) {
					$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$row['id']}'" );

					if( $category_list AND $approve ) {

						$cat_ids = array ();

						$cat_ids_arr = explode( ",", $category_list );

						foreach ( $cat_ids_arr as $value ) {

							$cat_ids[] = "('" . $row['id'] . "', '" . trim( $value ) . "')";
						}

						$cat_ids = implode( ", ", $cat_ids );
						$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );

					}
				}
				
				$db->query( "DELETE FROM " . PREFIX . "_xfsearch WHERE news_id = '{$row['id']}'" );

				if ( count($xf_search_words) AND $approve ) {
					
					$temp_array = array();
					
					foreach ( $xf_search_words as $value ) {
						
						$temp_array[] = "('" . $row['id'] . "', '" . $value[0] . "', '" . $value[1] . "')";
					}
					
					$xf_search_words = implode( ", ", $temp_array );
					$db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue) VALUES " . $xf_search_words );
				}
				
				
				if( $add_vote ) {
					
					$count = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_poll WHERE news_id = '{$id}'" );
					
					if( $count['count'] ) $db->query( "UPDATE  " . PREFIX . "_poll set title='$vote_title', frage='$frage', body='$vote_body', multiple='$allow_m_vote' WHERE news_id = '{$row['id']}'" );
					else $db->query( "INSERT INTO " . PREFIX . "_poll (news_id, title, frage, body, votes, multiple, answer) VALUES('{$id}', '$vote_title', '$frage', '$vote_body', 0, '$allow_m_vote', '')" );
				
				} else {
					$db->query( "DELETE FROM " . PREFIX . "_poll WHERE news_id='{$row['id']}'" );
					$db->query( "DELETE FROM " . PREFIX . "_poll_log WHERE news_id='{$row['id']}'" );
				}
				
				clear_cache( array('full_'.$row['id'], 'comm_'.$row['id']) );
			
			} else {

				if ( $max_detected ) die( "Hacking attempt!" );
				$added_time = time();
				$thistime = date( "Y-m-d H:i:s", $added_time );
				
				$db->query( "INSERT INTO " . PREFIX . "_post (date, autor, short_story, full_story, xfields, title, keywords, category, alt_name, allow_comm, approve, allow_main, fixed, allow_br, symbol, tags) values ('$thistime', '{$member_id['name']}', '$short_story', '$full_story', '$filecontents', '$title', '', '$category_list', '$alt_name', '$allow_comm', '$approve', '$allow_main', '$news_fixed', '$allow_br', '$catalog_url', '" . $_POST['tags'] . "')" );
				
				$row['id'] = $db->insert_id();

				$db->query( "INSERT INTO " . PREFIX . "_post_extras (news_id, allow_rate, votes, user_id, allow_rss, allow_rss_turbo, allow_rss_dzen) VALUES('{$row['id']}', '{$allow_rating}', '{$add_vote}','{$member_id['user_id']}', '1', '{$allow_rss_turbo}', '{$allow_rss_dzen}')" );

				if ( $approve ) {
					
					$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '1', '{$title}')" );
					
				}
				
				if( $add_vote ) {
					$db->query( "INSERT INTO " . PREFIX . "_poll (news_id, title, frage, body, votes, multiple, answer) VALUES('{$row['id']}', '{$vote_title}', '{$frage}', '{$vote_body}', 0, '{$allow_m_vote}', '')" );
				}

				$member_id['name'] = $db->safesql($member_id['name']);

				$db->query( "UPDATE " . PREFIX . "_images set news_id='{$row['id']}' where author = '{$member_id['name']}' AND news_id = '0'" );
				$db->query( "UPDATE " . PREFIX . "_files set news_id='{$row['id']}' where author = '{$member_id['name']}' AND news_id = '0'" );
				$db->query( "UPDATE " . USERPREFIX . "_users set news_num=news_num+1 where user_id='{$member_id['user_id']}'" );

				if( $user_group[$member_id['user_group']]['flood_news'] ) {
					$db->query( "INSERT INTO " . PREFIX . "_flood (id, ip, flag) values ('$_TIME', '{$member_id['name']}', '1')" );
				}
				
				if( $_POST['tags'] != "" AND $approve ) {
					
					$tags = array ();
					
					$_POST['tags'] = explode( ",", $_POST['tags'] );
					
					foreach ( $_POST['tags'] as $value ) {
						
						$tags[] = "('" . $row['id'] . "', '" . trim( $value ) . "')";
					}
					
					$tags = implode( ", ", $tags );
					$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tags );
				
				}
				
				if( $category_list AND $approve ) {
					
					$cat_ids = array ();
					
					$cat_ids_arr = explode( ",", $category_list );
					
					foreach ( $cat_ids_arr as $value ) {
						
						$cat_ids[] = "('" . $row['id'] . "', '" . trim( $value ) . "')";
					}
					
					$cat_ids = implode( ", ", $cat_ids );
					$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
				
				}
	
				if ( count($xf_search_words) AND $approve ) {
					
					$temp_array = array();
					
					foreach ( $xf_search_words as $value ) {
						
						$temp_array[] = "('" . $row['id'] . "', '" . $value[0] . "', '" . $value[1] . "')";
					}
					
					$xf_search_words = implode( ", ", $temp_array );
					$db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue) VALUES " . $xf_search_words );
				}
	
				if( !$approve and $config['mail_news'] ) {
					
					include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/mail.class.php'));
					
					$row = $db->super_query( "SELECT * FROM " . PREFIX . "_email WHERE name='new_news' LIMIT 0,1" );
					$mail = new dle_mail( $config, $row['use_html'] );
					
					$row['template'] = stripslashes( $row['template'] );
					$row['template'] = str_replace( "{%username%}", $member_id['name'], $row['template'] );
					$row['template'] = str_replace( "{%date%}", langdate( "j F Y H:i", $added_time, true ), $row['template'] );
					$row['template'] = str_replace( "{%title%}", stripslashes( stripslashes( $title ) ), $row['template'] );
					
					$category_list = explode( ",", $category_list );
					$my_cat = array ();
					
					foreach ( $category_list as $element ) {
						
						$my_cat[] = $cat_info[$element]['name'];
					
					}
					
					$my_cat = stripslashes( implode( ', ', $my_cat ) );
					
					$row['template'] = str_replace( "{%category%}", $my_cat, $row['template'] );
					
					$mail->send( $config['admin_mail'], $lang['mail_news'], $row['template'] );
				
				}
			
			}
			
			$categories_default = "";

			if( isset($_POST['categories_default']) ) {
				
				$temp_array = explode( ',', $_POST['categories_default'] );
				$categories_default = array();
				
				foreach ( $temp_array as $element ) {
					$element = intval(trim($element));
					
					if( $element > 0 ) {
						$categories_default[] = $element;
					}
				}
				
				if( count($categories_default) ) $categories_default = htmlspecialchars(implode(',', $categories_default), ENT_QUOTES, $config['charset'] );
				else $categories_default = "";
				
			}
			
			if( $categories_default ) {
				$add_url = "<a href=\"{$PHP_SELF}?do=addnews&amp;category={$categories_default}\">{$lang['add_noch']}</a>";
			} elseif ($config['allow_alt_url']) {
				$add_url = "<a href=\"{$config['http_home_url']}addnews.html\">{$lang['add_noch']}</a>";
			} else $add_url = "<a href=\"{$PHP_SELF}?do=addnews\">{$lang['add_noch']}</a>";

			msgbox( $lang['add_ok'], "{$msg} {$add_url} {$lang['add_or']} <a href=\"{$config['http_home_url']}\">{$lang['all_prev']}</a>" );
			
			if( $approve ) {

				clear_cache( array('news_', 'related_', 'tagscloud_', 'archives_', 'calendar_', 'topnews_', 'rss', 'stats') );

			}
		
		}
	
	} elseif( $is_logged AND $user_group[$member_id['user_group']]['allow_adds'] ) {
		
		$js_array[] = "engine/classes/js/sortable.js";
		$js_array[] = "engine/classes/uploads/html5/fileuploader.js";
		$js_array[] = "engine/classes/calendar/calendar.js";
		
		$css_array[] = "engine/classes/calendar/calendar.css";

		$tpl->load_template( 'addnews.tpl' );
		
		$addtype = "addnews";
		$categories_default = "";

		if ( !$user_group[$member_id['user_group']]['allow_html'] ) {

			$config['allow_site_wysiwyg'] = 0;

		}
		
		if( $found ) {
			
			$row = $db->super_query( "SELECT * FROM " . PREFIX . "_post LEFT JOIN " . PREFIX . "_post_extras ON (" . PREFIX . "_post.id=" . PREFIX . "_post_extras.news_id) WHERE id = '{$id}' AND approve = '0'" );
			if( $id == $row['id'] AND ($member_id['name'] == $row['autor'] OR $user_group[$member_id['user_group']]['allow_all_edit']) ) $found = true;
			else $found = false;
			
		} else { $row = array(); }
		
		if( $found ) {
			
			$cat_list = explode( ',', $row['category'] );
			$categories_list = CategoryNewsSelection( $cat_list, 0 );
			$tpl->set( '{title}', $parse->decodeBBCodes( $row['title'], false ) );
			$tpl->set( '{alt-name}', $row['alt_name'] );
			
			if( $config['allow_site_wysiwyg'] or $row['allow_br'] != '1' ) {
				$row['short_story'] = $parse->decodeBBCodes( $row['short_story'], TRUE, $config['allow_site_wysiwyg'] );
				$row['full_story'] = $parse->decodeBBCodes( $row['full_story'], TRUE, $config['allow_site_wysiwyg'] );
			} else {
				$row['short_story'] = $parse->decodeBBCodes( $row['short_story'], false );
				$row['full_story'] = $parse->decodeBBCodes( $row['full_story'], false );
			}
			
			$tpl->set( '{short-story}', $row['short_story'] );
			$tpl->set( '{full-story}', $row['full_story'] );
			$tpl->set( '{tags}', $row['tags'] );

			if( $row['votes'] ) {
				$poll = $db->super_query( "SELECT * FROM " . PREFIX . "_poll where news_id = '{$row['id']}'" );
				$poll['title'] = $parse->decodeBBCodes( $poll['title'], false );
				$poll['frage'] = $parse->decodeBBCodes( $poll['frage'], false );
				$poll['body'] = $parse->decodeBBCodes( $poll['body'], false );
				$poll['multiple'] = $poll['multiple'] ? "checked" : "";

				$tpl->set( '{votetitle}', $poll['title'] );
				$tpl->set( '{frage}', $poll['frage'] );
				$tpl->set( '{votebody}', $poll['body'] );
				$tpl->set( '{allowmvote}', $poll['multiple'] );

			} else {
				$tpl->set( '{votetitle}', '' );
				$tpl->set( '{frage}', '' );
				$tpl->set( '{votebody}', '' );
				$tpl->set( '{allowmvote}', '' );
			}
		
		} else {
			
			if( isset($_GET['category']) ) {
				
				$categories_list = CategoryNewsSelection( explode( ',', $_GET['category'] ), 0 );
				$temp_array = explode( ',', $_GET['category'] );
				$categories_default = array();
				
				foreach ( $temp_array as $element ) {
					$element = intval(trim($element));
					
					if( $element > 0 ) {
						$categories_default[] = $element;
					}
				}
				
				if( count($categories_default) ) $categories_default = htmlspecialchars(implode(',', $categories_default), ENT_QUOTES, $config['charset'] );
				else $categories_default = "";
				
			} else $categories_list = CategoryNewsSelection( 0, 0 );
			
			$tpl->set( '{title}', '' );
			$tpl->set( '{alt-name}', '' );
			$tpl->set( '{short-story}', '' );
			$tpl->set( '{full-story}', '' );
			$tpl->set( '{tags}', '' );
			$tpl->set( '{votetitle}', '' );
			$tpl->set( '{frage}', '' );
			$tpl->set( '{votebody}', '' );
			$tpl->set( '{allowmvote}', '' );
		
		}
		
		if( $config['allow_site_wysiwyg'] ) {
			
			include_once (DLEPlugins::Check(ENGINE_DIR . '/editor/shortsite.php'));
			include_once (DLEPlugins::Check(ENGINE_DIR . '/editor/fullsite.php'));
			$bb_code = "";
		
		} else {
			$bb_editor = true;
			include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/bbcode.php'));
		}

		if( !$config['allow_site_wysiwyg'] ) {
			
			$tpl->set( '[not-wysywyg]', '' );
			$tpl->set( '[/not-wysywyg]', '' );
		
		} else $tpl->set_block( "'\\[not-wysywyg\\].*?\\[/not-wysywyg\\]'si", '' );
		
		if( $config['allow_site_wysiwyg'] ) {
			
			$tpl->set( '{shortarea}', $shortarea );
			$tpl->set( '{fullarea}', $fullarea );
		
		} else {
			$tpl->set( '{shortarea}', '' );
			$tpl->set( '{fullarea}', '' );
		}
		
		$xfieldsaction = "categoryfilter";
		include_once (DLEPlugins::Check(ENGINE_DIR . '/inc/xfields.php'));
		
		if( $config['allow_multi_category'] ) {
			
			$cats = "<select data-placeholder=\"{$lang['addnews_cat_sel']}\" name=\"catlist[]\" id=\"category\" onchange=\"onCategoryChange(this)\" style=\"width:350px;height:140px;\" multiple=\"multiple\">";
		
		} else {
			
			$cats = "<select data-placeholder=\"{$lang['addnews_cat_sel']}\" name=\"catlist[]\" id=\"category\" onchange=\"onCategoryChange(this)\" style=\"width:350px;\">";
		}
		
		$cats .= $categories_list;
		$cats .= "</select>";
		
		$tpl->set( '{bbcode}', $bb_code );
		$tpl->set( '{category}', $cats );
		
		if( $user_group[$member_id['user_group']]['moderation'] ) {
			
			$admintag = "<div class=\"checkbox\"><label><input type=\"checkbox\" name=\"approve\" id=\"approve\" value=\"1\" checked=\"checked\" />{$lang['add_al_ap']}</label></div>";

			$admintag .= "<div id=\"opt_holder_comments\" class=\"checkbox\"><label><input type=\"checkbox\" name=\"allow_comm\" value=\"1\" checked=\"checked\" />" . $lang['add_al_com'] . "</label></div>";
			
			if( $user_group[$member_id['user_group']]['allow_main'] ) $admintag .= "<div id=\"opt_holder_main\" class=\"checkbox\"><label><input type=\"checkbox\" name=\"allow_main\" id=\"allow_main\" value=\"1\" checked=\"checked\" />" . $lang['add_al_m'] . "</label></div>";
			
			$admintag .= "<div id=\"opt_holder_rating\" class=\"checkbox\"><label><input type=\"checkbox\" name=\"allow_rating\" id=\"allow_rating\" value=\"1\" checked=\"checked\" />{$lang['addnews_allow_rate']}</label></div>";
			
			if( $user_group[$member_id['user_group']]['allow_fixed'] ) $admintag .= "<div class=\"checkbox\"><label><input type=\"checkbox\" name=\"news_fixed\" id=\"news_fixed\" value=\"1\" />{$lang['add_al_fix']}</label></div>";
			
			$tpl->set( '{admintag}', $admintag );
		
		} else $tpl->set( '{admintag}', '' );
		
		if( $is_logged and $member_id['user_group'] < 3 ) {
			
			$tpl->set( '[urltag]', '' );
			$tpl->set( '[/urltag]', '' );
		
		} else
			$tpl->set_block( "'\\[urltag\\].*?\\[/urltag\\]'si", "" );
		
		if( $found ) {
			
			$xfieldsaction = "list";
			$xfieldmode = "site";
			$xfieldsid = $row['xfields'];
			$xfieldscat = $row['category'];
			$author = urlencode($row['autor']);
			$news_id = $row['id'];
			include (DLEPlugins::Check(ENGINE_DIR . '/inc/xfields.php'));
		
		} else {
			
			$xfieldsaction = "list";
			$xfieldmode = "site";
			$xfieldsadd = true;
			$news_id = 0;
	        $author = urlencode($member_id['name']);
			include (DLEPlugins::Check(ENGINE_DIR . '/inc/xfields.php'));
		
		}

		if( !$config['allow_site_wysiwyg'] ) $output = str_replace("<!--panel-->", $bb_code, $output);
		
		$tpl->set( '{xfields}', $output );
		
		if ( count( $xfieldinput ) ) {
			foreach ( $xfieldinput as $key => $value ) {
				if( !$config['allow_site_wysiwyg'] ) $value = str_replace("<!--panel-->", $bb_code, $value);
				$tpl->copy_template = str_replace( "[xfinput_{$key}]", $value, $tpl->copy_template );
			}		
		}

		if ( $user_group[$member_id['user_group']]['disable_news_captcha'] AND $member_id['news_num'] >= $user_group[$member_id['user_group']]['disable_news_captcha'] ) {

			$user_group[$member_id['user_group']]['news_question'] = false;
			$user_group[$member_id['user_group']]['news_sec_code'] = false;

		}

		if( $user_group[$member_id['user_group']]['news_question'] ) {

			$tpl->set( '[question]', "" );
			$tpl->set( '[/question]', "" );

			$question = $db->super_query("SELECT id, question FROM " . PREFIX . "_question ORDER BY RAND() LIMIT 1");
			$tpl->set( '{question}', htmlspecialchars( stripslashes( $question['question'] ), ENT_QUOTES, $config['charset'] ) );

			$_SESSION['question'] = $question['id'];

		} else {

			$tpl->set_block( "'\\[question\\](.*?)\\[/question\\]'si", "" );
			$tpl->set( '{question}', "" );

		}
		
		if( $user_group[$member_id['user_group']]['news_sec_code'] ) {

			if ( $config['allow_recaptcha'] ) {

				$tpl->set( '[recaptcha]', "" );
				$tpl->set( '[/recaptcha]', "" );
				
				if( $config['allow_recaptcha'] == 2) {
					
					$tpl->set( '{recaptcha}', "");
					$tpl->copy_template .= "<input type=\"hidden\" name=\"g-recaptcha-response\" id=\"g-recaptcha-response\" value=\"\"><script src=\"https://www.google.com/recaptcha/api.js?render={$config['recaptcha_public_key']}\"></script>";
					$tpl->copy_template .= "<script>grecaptcha.ready(function() {grecaptcha.execute('{$config['recaptcha_public_key']}', {action: 'addnews'}).then(function(token) {\$('#g-recaptcha-response').val(token);});});</script>";
					
				} else {
					
					$tpl->set( '{recaptcha}', "<div class=\"g-recaptcha\" data-sitekey=\"{$config['recaptcha_public_key']}\" data-theme=\"{$config['recaptcha_theme']}\"></div><script src=\"https://www.google.com/recaptcha/api.js?hl={$lang['wysiwyg_language']}\"></script>" );	
				
				}

				$tpl->set_block( "'\\[sec_code\\](.*?)\\[/sec_code\\]'si", "" );
				$tpl->set( '{sec_code}', "" );

			} else {

				$tpl->set( '[sec_code]', "" );
				$tpl->set( '[/sec_code]', "" );
				$tpl->set( '{sec_code}', "<a onclick=\"reload(); return false;\" href=\"#\" title=\"{$lang['reload_code']}\"><span id=\"dle-captcha\"><img src=\"engine/modules/antibot/antibot.php\" alt=\"{$lang['reload_code']}\" width=\"160\" height=\"80\" /></span></a>" );
				$tpl->set_block( "'\\[recaptcha\\](.*?)\\[/recaptcha\\]'si", "" );
				$tpl->set( '{recaptcha}', "" );
			}

		} else {

			$tpl->set( '{sec_code}', "" );
			$tpl->set( '{recaptcha}', "" );
			$tpl->set_block( "'\\[recaptcha\\](.*?)\\[/recaptcha\\]'si", "" );
			$tpl->set_block( "'\\[sec_code\\](.*?)\\[/sec_code\\]'si", "" );

		}

		if( $config['allow_site_wysiwyg'] == "2" ) $save = "tinyMCE.triggerSave();"; else $save = "";		

		$script = "
<script>
<!--
function preview(){";
		
		$script .= "if(document.entryform.title.value == ''){ DLEalert('$lang[add_err_7]', dle_info); }
    else{
        dd=window.open('','prv','height=400,width=750,resizable=0,scrollbars=1')
        document.entryform.mod.value='preview';document.entryform.action='{$PHP_SELF}?do=preview';document.entryform.target='prv'
        document.entryform.submit();dd.focus()
        setTimeout(\"document.entryform.mod.value='addnews';document.entryform.action='';document.entryform.target='_self'\",500)
    }
}";
		
		$script .= <<<HTML
	function split( val ) {
		return val.split( /,\s*/ );
	}
	
	function extractLast( term ) {
		return split( term ).pop();
	}

	function find_relates ( )
	{
		var title = document.getElementById('title').value;

		ShowLoading('');

		$.post('engine/ajax/controller.php?mod=find_relates', { title: title, mode: 1, user_hash: '{$dle_login_hash}' }, function(data){
	
			HideLoading('');
	
			$('#related_news').html(data);
	
		});

		return false;

	};
	
	function xfimagedelete( xfname, xfvalue )
	{
		
		DLEconfirm( '{$lang['image_delete']}', '{$lang['p_info']}', function () {
		
			ShowLoading('');
			
			$.post('engine/ajax/controller.php?mod=upload', { subaction: 'deluploads', user_hash: '{$dle_login_hash}', news_id: '{$news_id}', author: '{$author}', 'images[]' : xfvalue }, function(data){
	
				HideLoading('');
				
				$('#uploadedfile_'+xfname).html('');
				$('#xf_'+xfname).val('');
				$('#xfupload_' + xfname + ' .qq-upload-button, #xfupload_' + xfname + ' .qq-upload-button input').removeAttr('disabled');
				
			});
			
		} );

		return false;

	};

	function xffiledelete( xfname, xfvalue )
	{
		
		DLEconfirm( '{$lang['file_delete']}', '{$lang['p_info']}', function () {
		
			ShowLoading('');
			
			$.post('engine/ajax/controller.php?mod=upload', { subaction: 'deluploads', user_hash: '{$dle_login_hash}', news_id: '{$news_id}', author: '{$author}', 'files[]' : xfvalue }, function(data){
	
				HideLoading('');
				
				$('#uploadedfile_'+xfname).html('');
				$('#xf_'+xfname).val('');
				$('#xf_'+xfname).hide('');
				$('#xfupload_' + xfname + ' .qq-upload-button, #xfupload_' + xfname + ' .qq-upload-button input').removeAttr('disabled');
				
			});
			
		} );

		return false;

	};
	
	function xfaddalt( id, xfname ) {
	
		var sel_alt = $('#xf_'+id).data('alt').toString().trim();
		sel_alt = sel_alt.replace(/"/g, '&quot;');
		sel_alt = sel_alt.replace(/'/g, '&#039;');
		
		DLEprompt('{$lang['bb_alt_image']}', sel_alt, '{$lang['p_prompt']}', function (r) {
			r = r.replace(/</g, '');
			r = r.replace(/>/g, '');
			r = r.replace(/,/g, '&#44;');
			
			$('#xf_'+id).data('alt', r);
			xfsinc(xfname);
		
		}, true);
		
	};
	
	function xfsinc(xfname) {
	
		var order = [];
		
		$( '#uploadedfile_' + xfname + ' .uploadedfile' ).each(function() {
			var xfurl = $(this).data('id').toString().trim();
			var xfalt = $(this).data('alt').toString().trim();
			
			if(xfalt) {
				order.push(xfalt + '|'+ xfurl);
			} else {
				order.push(xfurl);
			}

		});
	
		$('#xf_' + xfname).val(order.join(','));
	};
	
	function checkxf() {

		var status = '';

		{$save}

		$('[uid=\"essential\"]:visible').each(function(indx) {

			if($.trim($(this).find('[rel=\"essential\"]').val()).length < 1) {
			
				DLEalert('{$lang['addnews_xf_alert']}', dle_info);

				status = 'fail';
			
			}

		});

		if(document.entryform.title.value == ''){

			DLEalert('{$lang['add_err_7']}', dle_info); 

			status = 'fail';

		}

		return status;

	};

	var text_upload = "{$lang['bb_t_up']}";

//-->
</script>
HTML;

			$onload_scripts[] = <<<HTML
$('[data-rel=links]').autocomplete({
	source: function( request, response ) {
		$.getJSON( 'engine/ajax/controller.php?mod=find_tags&user_hash={$dle_login_hash}&mode=xfield', {
			term: extractLast( request.term )
		}, response );
	},
	search: function() {
		var term = extractLast( this.value );
		if ( term.length < 3 ) {
			return false;
		}
	},
	focus: function() {
		return false;
	},
	select: function( event, ui ) {
		var terms = split( this.value );
		terms.pop();
		terms.push( ui.item.value );
		terms.push( '' );
		this.value = terms.join( ', ' );
		return false;
	}
});
HTML;

		if( $config['allow_add_tags'] ) {

			$onload_scripts[] = <<<HTML
$( '#tags' ).autocomplete({
	source: function( request, response ) {
		$.getJSON( 'engine/ajax/controller.php?mod=find_tags&user_hash={$dle_login_hash}', {
			term: extractLast( request.term )
		}, response );
	},
	search: function() {
		var term = extractLast( this.value );
		if ( term.length < 3 ) {
			return false;
		}
	},
	focus: function() {
		return false;
	},
	select: function( event, ui ) {
		var terms = split( this.value );
		terms.pop();
		terms.push( ui.item.value );
		terms.push( '' );
		this.value = terms.join( ', ' );
		return false;
	}
});
HTML;
		}
		
		$script .= "<form method=\"post\" name=\"entryform\" id=\"entryform\" onsubmit=\"if(checkxf()=='fail') return false;\" action=\"\">";
		
		if( $categories_default ) {
			
			$categories_default = "<input type=\"hidden\" name=\"categories_default\" value=\"{$categories_default}\">";
			
		} else $categories_default = "";
		
		$tpl->copy_template = $categoryfilter . $script . $tpl->copy_template . $categories_default."<input type=\"hidden\" name=\"mod\" value=\"addnews\"><input type=\"hidden\" name=\"user_hash\" value=\"{$dle_login_hash}\"></form>";

		if( !$config['allow_site_wysiwyg'] ) $tpl->copy_template .= $bb_js_code;

		$tpl->compile( 'content' );
		$tpl->clear();
	
	} else msgbox( $lang['all_info'], "$lang[add_err_8]<br /><a href=\"javascript:history.go(-1)\">{$lang['all_prev']}</a>" );

}
?>