<?PHP
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
 File: engine.php
=====================================================
*/

if (!defined('DATALIFEENGINE')) {
	die ( "Hacking attempt!" );
}

if ($cstart < 0) $cstart = 0;

$CN_HALT = false;
$allow_add_comment = false;
$allow_active_news = true;
$allow_comments = false;
$allow_userinfo = false;
$active = false;
$disable_index = false;
$social_tags = array();
$canonical = false;
$url_page = false;
$user_query = false;
$news_author = false;
$attachments = array ();
$short_news_cache = false;

switch ( $do ) {
	
	case "search" :
		
		if ($_REQUEST['mode'] == "advanced") $_REQUEST['full_search'] = 1;
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/search.php'));
		break;

	case "changemail" :
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/changemail.php'));
		break;
	
	case "deletenews" :
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/deletenews.php'));
		break;

	case "comments" :
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/comments.php'));
		break;
	
	case "stats" :
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/stats.php'));
		break;
	
	case "addnews" :
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/addnews.php'));
		break;
	
	case "register" :
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/register.php'));
		break;
	
	case "lostpassword" :
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/lostpassword.php'));
		break;
	
	case "rules" :
		$_GET['page'] = "dle-rules-page";
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/static.php'));
		break;
	
	case "static" :
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/static.php'));
		break;
	
	case "alltags" :
		include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/tagscloud.php'));
		break;

	case "auth-social" :
		include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/social.php'));
		break;
	
	case "favorites" :
		
		if ($is_logged) {
			
			$config['allow_cache'] = false;
			
			include (DLEPlugins::Check(ENGINE_DIR . '/modules/favorites.php'));
		
		} else {
			
			@header( "HTTP/1.1 403 Forbidden" );
			msgbox ( $lang['all_err_1'], $lang['fav_error'] );
			
		}
			
		break;
	
	case "feedback" :
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/feedback.php'));
		break;
	
	case "lastcomments" :
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/lastcomments.php'));
		break;
	
	case "pm" :
		include (DLEPlugins::Check(ENGINE_DIR . '/modules/pm.php'));
		break;

	case "unsubscribe" :
		$_GET['post_id'] = intval ($_GET['post_id']);
		$_GET['user_id'] = intval ($_GET['user_id']);

		if ($_GET['post_id'] AND $_GET['user_id'] AND $_GET['hash']) {

			$row = $db->super_query( "SELECT hash FROM " . PREFIX . "_subscribe WHERE news_id='{$_GET['post_id']}' AND user_id='{$_GET['user_id']}'" );

			if ($row['hash'] AND $row['hash'] == $_GET['hash']) {

				$db->query( "DELETE FROM " . PREFIX . "_subscribe WHERE news_id='{$_GET['post_id']}' AND user_id='{$_GET['user_id']}'" );
				msgbox( $lang['all_info'],  $lang['unsubscribe_ok']);

			} else {
				msgbox( $lang['all_info'],  $lang['unsubscribe_err']);
			}

		} else {
			msgbox( $lang['all_info'],  $lang['unsubscribe_err']);
		}

		break;
	
	case "newsletterunsubscribe" :
		
		$_GET['user_id'] = intval ($_GET['user_id']);

		if ($_GET['user_id'] AND $_GET['hash']) {

			$row = $db->super_query( "SELECT password, user_id FROM " . USERPREFIX . "_users WHERE user_id='{$_GET['user_id']}'" );
			
			if ($row['user_id']) {
				
				$unsubscribe_hash = md5( SECURE_AUTH_KEY . $_SERVER['HTTP_HOST'] . $row['user_id'] . sha1( substr($row['password'], 0, 6) ) . $config['key'] );
	
				if ($unsubscribe_hash == $_GET['hash']) {
	
					$db->query( "UPDATE " . USERPREFIX . "_users SET allow_mail='0' WHERE user_id = '{$_GET['user_id']}'" );
					
					msgbox( $lang['all_info'],  $lang['n_unsubscribe_ok']);
	
				} else {
					
					msgbox( $lang['all_info'],  $lang['n_unsubscribe_err']);
					
				}
				
			} else {
				msgbox( $lang['all_info'],  $lang['n_unsubscribe_err']);
			}

		} else {
			msgbox( $lang['all_info'],  $lang['n_unsubscribe_err']);
		}

		break;
	
	default :
		
		$is_main = 0;
		$active = false;
		$user_query = "";
		$url_page = "";
		
		$thisdate = date ( "Y-m-d H:i:s", time () );
		if ($config['no_date'] AND !$config['news_future']) $where_date = " AND date < '" . $thisdate . "'";
		else $where_date = "";
		
		if ($config['allow_fixed']) $fixed = "fixed desc, ";
		else $fixed = "";
		
		$config['news_number'] = intval ( $config['news_number'] );

		if ( $smartphone_detected AND $config['mobile_news'] ) $config['news_number'] = intval ( $config['mobile_news'] );
		
		$news_sort_by = ($config['news_sort']) ? $config['news_sort'] : "date";
		$news_direction_by = ($config['news_msort']) ? $config['news_msort'] : "DESC";

		$allow_list = explode ( ',', $user_group[$member_id['user_group']]['allow_cats'] );
		$stop_list = "";
		$cat_join = "";
		$cat_join_count = "";
		
		if ($allow_list[0] != "all") {
			
			if ($config['allow_multi_category']) {
				
				$cat_join = "INNER JOIN (SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN (" . implode ( ',', $allow_list ) . ")) c ON (p.id=c.news_id) ";
				$cat_join_count = "p ".$cat_join;
			
			} else {
				
				$stop_list = "category IN ('" . implode ( "','", $allow_list ) . "') AND ";
			
			}
		
		}
		
		$not_allow_cats = explode ( ',', $user_group[$member_id['user_group']]['not_allow_cats'] );
		
		if( $not_allow_cats[0] != "" ) {
			
			if ($config['allow_multi_category']) {
				
				$stop_list = "p.id NOT IN ( SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN (" . implode ( ',', $not_allow_cats ) . ") ) AND ";
				$cat_join_count = "p ";
				
			} else {
				
				$stop_list = "category NOT IN ('" . implode ( "','", $not_allow_cats ) . "') AND ";
			
			}
			
		}
		
		if ($user_group[$member_id['user_group']]['allow_short']) { $stop_list = ""; $cat_join = ""; $cat_join_count = ""; }
		
		$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}approve=1 AND allow_main=1" . $where_date . " ORDER BY " . $fixed . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];

		$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post {$cat_join_count}WHERE {$stop_list}approve=1 AND allow_main=1" . $where_date;
		$sql_news = "";
		
		// ################ Show of a category #################
		if ($do == "cat" and $category != '' and $subaction == '') {

			$allow_sub_cats = true;
			
			if( $config['allow_alt_url'] AND $config['seo_control'] AND $category_id AND $view_template != "rss") {

				$re_cat = get_url( $category_id );

				if ($re_cat != $_GET['category'] OR substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' OR $_GET['cstart'] == 1 OR substr ( $_SERVER['REQUEST_URI'], - 2 ) == '//' OR strpos ($_SERVER['REQUEST_URI'], "do=cat" ) !== false ) {
					$re_url = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
					$re_url = reset ( $re_url );

					if( (substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' OR substr ( $_SERVER['REQUEST_URI'], - 2 ) == '//') AND $_GET['cstart'] AND $_GET['cstart'] != 1 ) {
					 $re_cat .= "/page/".intval($_GET['cstart']);
					}
					
					header("HTTP/1.0 301 Moved Permanently");
					header("Location: {$re_url}{$re_cat}/");
					die("Redirect");
				}
			}

			if (!$category_id) $category_id = 'not detected';
			
			if ($allow_list[0] != "all") {
				if (!$user_group[$member_id['user_group']]['allow_short'] AND !in_array( $category_id, $allow_list )) $category_id = 'not detected';
			}

			if ($not_allow_cats[0] != "") {
				if (!$user_group[$member_id['user_group']]['allow_short'] AND in_array( $category_id, $not_allow_cats )) $category_id = 'not detected';
			}

			if( !intval($category_id) ) {
				$allow_active_news = false;
			}
			
			if ( $cat_info[$category_id]['show_sub'] ) {

				if ( $cat_info[$category_id]['show_sub'] == 1 ) $get_cats = get_sub_cats ( $category_id );
				else { $get_cats = $category_id; $allow_sub_cats = false; }

			} else {

				if ( $config['show_sub_cats'] ) $get_cats = get_sub_cats ( $category_id );
				else { $get_cats = $category_id; $allow_sub_cats = false; }

			}

			if ($cat_info[$category_id]['news_sort']) $news_sort_by = $cat_info[$category_id]['news_sort'];
			if ($cat_info[$category_id]['news_msort']) $news_direction_by = $cat_info[$category_id]['news_msort'];
			if ($cat_info[$category_id]['news_number']) $config['news_number'] = $cat_info[$category_id]['news_number'];

			if ($cstart) {
				$cstart = $cstart - 1;
				$cstart = $cstart * $config['news_number'];
			}

			$url_page = $config['http_home_url'] . get_url ( $category_id );
			$user_query = "do=cat&amp;category=" . $cat_info[$category_id]['alt_name'];
			
			if( $config['allow_alt_url'] ) $canonical = $url_page . "/"; else $canonical = $PHP_SELF."?do=cat&category=" . $cat_info[$category_id]['alt_name'];
			
			if ($config['allow_multi_category']) {
		
				$get_cats = str_replace ( "|", "','", $get_cats );
				$join_category = "INNER JOIN (SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN ('" . $get_cats . "')) c ON (p.id=c.news_id) ";
				$where_category = "";
				
			} else {
				
				if ( $allow_sub_cats ) {
					
					$get_cats = str_replace ( "|", "','", $get_cats );
					$where_category = "category IN ('" . $get_cats . "') AND ";
				
				} else {
					
					$where_category = "category = '{$get_cats}' AND ";
				
				}
				
				$join_category = "";
			
			}
			
			if ($view_template == "rss") {
				
				$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, p.full_story, p.xfields, p.title, p.category, p.alt_name, e.allow_rss_turbo, e.allow_rss_dzen FROM " . PREFIX . "_post p {$join_category}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$where_category}approve=1 AND allow_rss=1" . $where_date . " ORDER BY date DESC LIMIT 0," . $config['rss_number'];
			
			} else {
				
				if (isset ( $_SESSION['dle_sort_cat_'.$category_id] )) $news_sort_by = $_SESSION['dle_sort_cat_'.$category_id];
				if (isset ( $_SESSION['dle_direction_cat_'.$category_id] )) $news_direction_by = $_SESSION['dle_direction_cat_'.$category_id];
				
				$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p {$join_category}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$where_category}approve=1" . $where_date . " ORDER BY " . $fixed . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
				$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post p {$join_category}WHERE {$where_category}approve=1" . $where_date;
			}
		
		} elseif ($do == 'lastnews') {
			// ################ Show all news #################			
			if ($cstart) {
				$cstart = $cstart - 1;
				$cstart = $cstart * $config['news_number'];
			}

			if( $config['allow_alt_url'] AND $config['seo_control'] AND $_GET['cstart'] ) {
	
				if (substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' OR $_GET['cstart'] == 1 ) {

					$re_url = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
					$re_url = reset ( $re_url );
						
					$re_url .= "lastnews/";
						
					if(substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' AND $_GET['cstart'] != 1 ) {
						$re_url .= "page/".intval($_GET['cstart'])."/";
					}
					
					header("HTTP/1.0 301 Moved Permanently");
					header("Location: {$re_url}");
					die("Redirect");
				}
			}
				
			$url_page = $config['http_home_url'] . "lastnews";
			$user_query = "do=lastnews";
			
			if( $config['allow_alt_url'] ) $canonical = $url_page . "/"; else $canonical = $PHP_SELF."?do=lastnews";
			
			if (isset ( $_SESSION['dle_sort_lastnews'] )) $news_sort_by = $_SESSION['dle_sort_lastnews'];
			else $news_sort_by = "date";
			if (isset ( $_SESSION['dle_direction_lastnews'] )) $news_direction_by = $_SESSION['dle_direction_lastnews'];
			else $news_direction_by = "DESC";
			
			$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}approve=1" . $where_date . " ORDER BY " . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
			$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post {$cat_join_count}WHERE {$stop_list}approve=1" . $where_date;
		
		} elseif ($do == 'tags') {
			// ################ Seach news by tags #################			
			if ($cstart) {
				$cstart = $cstart - 1;
				$cstart = $cstart * $config['news_number'];
			}

			$tag = rawurldecode( $_GET['tag'] );

			$tag = htmlspecialchars ( strip_tags ( stripslashes ( trim ( $tag ) ) ), ENT_COMPAT, $config['charset'] );
			$encoded_tag = rawurlencode(str_replace(array("&#039;", "&quot;", "&amp;"), array("'", '"', "&"), $tag));
			
			define( 'CLOUDSTAG', $tag );
			
			$url_page = $config['http_home_url'] . "tags/" . $encoded_tag;
			$user_query = "do=tags&amp;tag=" . $encoded_tag;
			
			if( $config['allow_alt_url'] ) $canonical = $url_page . "/"; else $canonical = $PHP_SELF."?do=tags&tag=" . $encoded_tag;

			if( $config['allow_alt_url'] AND $config['seo_control'] ) {

				if ( substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' OR $_GET['cstart'] == 1 OR substr ( $_SERVER['REQUEST_URI'], - 2 ) == '//' OR strpos ($_SERVER['REQUEST_URI'], "do=tags" ) !== false) {
	
					$re_url = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
					$re_url = reset ( $re_url );
						
					$re_url .= "tags/" . $encoded_tag . "/";
						
					if( $_GET['cstart'] > 1 ) {
						$re_url .= "page/".intval($_GET['cstart'])."/";
					}
					
					header("HTTP/1.0 301 Moved Permanently");
					header("Location: {$re_url}");
					die("Redirect");
				}
			}
			
			if (isset ( $_SESSION['dle_sort_tags'] )) $news_sort_by = $_SESSION['dle_sort_tags'];
			if (isset ( $_SESSION['dle_direction_tags'] )) $news_direction_by = $_SESSION['dle_direction_tags'];

			$tag = @$db->safesql ( $tag );

			$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p INNER JOIN " . PREFIX . "_tags t on (t.news_id=p.id) {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}t.tag = '{$tag}' AND p.approve=1" . $where_date . " ORDER BY " . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
			$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post p INNER JOIN " . PREFIX . "_tags t on (t.news_id=p.id) {$cat_join}WHERE {$stop_list}t.tag = '{$tag}' AND approve=1" . $where_date;

		} elseif ($do == 'xfsearch') {
			// ################ Seach news by xfields #################			
			if ($cstart) {
				$cstart = $cstart - 1;
				$cstart = $cstart * $config['news_number'];
			}
			
			if (isset ( $_SESSION['dle_sort_xfsearch'] )) $news_sort_by = $_SESSION['dle_sort_xfsearch'];
			if (isset ( $_SESSION['dle_direction_xfsearch'] )) $news_direction_by = $_SESSION['dle_direction_xfsearch'];
		
			$xf = rawurldecode($_GET['xf']);

			if (dle_substr ( $xf, - 1, 1, $config['charset'] ) == '/') $xf = dle_substr ( $xf, 0, - 1, $config['charset'] );
			
			$xf = explode ( '/', $xf );
			$xfname = "";
			
			if( $_GET['xfname'] AND !$config['allow_alt_url'] ) {
				$xfname =totranslit(trim($_GET['xfname']));
			} elseif(count($xf) > 1 ) {
				$xfname =totranslit(trim($xf[0]));
				unset($xf[0]);
			}

			$xf = implode(' ', $xf);
			$xf = htmlspecialchars ( strip_tags ( stripslashes ( trim ( $xf ) ) ), ENT_QUOTES, $config['charset'] );
			$xf = str_replace( array("{", "[", ":"), array("&#123;", "&#91;", "&#58;"), $xf );

			if($xfname) {
				
				$url_page = $config['http_home_url'] . "xfsearch/{$xfname}/" . rawurlencode ( str_replace(array("&#039;", "&quot;", "&amp;", "&#123;", "&#91;", "&#58;"), array("'", '"', "&", "{", "[", ":"), $xf) );
				$user_query = "do=xfsearch&amp;xfname=".$xfname."&amp;xf=" . rawurlencode ( str_replace(array("&#039;", "&quot;", "&amp;", "&#123;", "&#91;", "&#58;"), array("'", '"', "&", "{", "[", ":"), $xf) );
				
				if( $config['allow_alt_url'] ) $canonical = $url_page . "/"; else $canonical = $PHP_SELF."?do=xfsearch&xfname=".$xfname."&xf=" . urlencode ( str_replace(array("&#039;", "&quot;", "&amp;", "&#123;", "&#91;", "&#58;"), array("'", '"', "&", "{", "[", ":"), $xf) );
				
			} else {
				
				$url_page = $config['http_home_url'] . "xfsearch/" . rawurlencode ( str_replace(array("&#039;", "&quot;", "&amp;", "&#123;", "&#91;", "&#58;"), array("'", '"', "&", "{", "[", ":"), $xf) );
				$user_query = "do=xfsearch&amp;xf=" . rawurlencode ( str_replace(array("&#039;", "&quot;", "&amp;", "&#123;", "&#91;", "&#58;"), array("'", '"', "&", "{", "[", ":"), $xf) );
				
				if( $config['allow_alt_url'] ) $canonical = $url_page . "/"; else $canonical = $PHP_SELF."?do=xfsearch&xf=" . urlencode ( str_replace(array("&#039;", "&quot;", "&amp;", "&#123;", "&#91;", "&#58;"), array("'", '"', "&", "{", "[", ":"), $xf) );
				
				$xfields = xfieldsload();
				$xfields_name = array();
				
				foreach ( $xfields as $value ) {
					$xfields_name[] = $value[0];
				}
				
				if( in_array($xf, $xfields_name) ) {$xf .= "|";}
				
				
			}
			
			$xfname = $db->safesql($xfname);
			$xf = $db->safesql($xf);

			if ( $xfname ) {
				
				if( $config['allow_alt_url'] AND $config['seo_control'] ) {

					if (substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' OR $_GET['cstart'] == 1 OR substr ( $_SERVER['REQUEST_URI'], - 2 ) == '//' OR strpos ($_SERVER['REQUEST_URI'], "do=xfsearch" ) !== false) {
							
						$re_url = $url_page . "/";
							
						if( $_GET['cstart'] > 1 ) {
							$re_url .= "page/".intval($_GET['cstart'])."/";
						}
						
						header("HTTP/1.0 301 Moved Permanently");
						header("Location: {$re_url}");
						die("Redirect");
					}
				}

				$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p INNER JOIN " . PREFIX . "_xfsearch xf on (xf.news_id=p.id) {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}xf.tagname = '{$xfname}' AND xf.tagvalue='{$xf}' AND p.approve=1" . $where_date . " ORDER BY " . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
				$sql_count  = "SELECT COUNT(*) as count FROM " . PREFIX . "_post p INNER JOIN " . PREFIX . "_xfsearch xf on (xf.news_id=p.id) {$cat_join}WHERE {$stop_list}xf.tagname = '{$xfname}' AND xf.tagvalue='{$xf}' AND approve=1" . $where_date;
		
			} else {
				
				if(!$xf) {
					
					$re_url = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
					$re_url = reset ( $re_url );
						
					header("HTTP/1.0 301 Moved Permanently");
					header("Location: {$re_url}");
					die("Redirect");
				}
				
				$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}xfields LIKE '%{$xf}%' AND approve=1" . $where_date . " ORDER BY " . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
				$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post {$cat_join_count}WHERE {$stop_list}xfields LIKE '%{$xf}%' AND approve=1" . $where_date;
		
				$xf = str_replace ( '|', '', $xf );
			}
		
		} elseif ($subaction == 'userinfo') {
			// ################ show user profile #################
			if ($cstart) {
				
				$cstart = $cstart - 1;
				$cstart = $cstart * $config['news_number'];
			
			}
			
			$url_page = $config['http_home_url'] . "user/" . urlencode ( $user );
			$user_query = "subaction=userinfo&amp;user=" . urlencode ( $user );
			
			if ($member_id['name'] == $user OR $user_group[$member_id['user_group']]['allow_all_edit']) {
				if (isset ( $_SESSION['dle_sort_userinfo'] )) $news_sort_by = $_SESSION['dle_sort_userinfo'];
				if (isset ( $_SESSION['dle_direction_userinfo'] )) $news_direction_by = $_SESSION['dle_direction_userinfo'];
				
				$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE autor = '{$user}' AND approve=0 ORDER BY " . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
				$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE autor = '$user' AND approve=0";
				
				if( !$config['profile_news'] ) {
					$allow_active_news = false;
					$news_found = false;		
				}

			} else {
				$allow_active_news = false;
				$news_found = false;
			}
			
			$config['allow_cache'] = false;
		} elseif ($subaction == 'allnews') {
			// ################ show all news by user #################
			if ($cstart) {
				
				$cstart = $cstart - 1;
				$cstart = $cstart * $config['news_number'];
			
			}
			
			$url_page = $config['http_home_url'] . "user/" . urlencode ( $user ) . "/news";
			$user_query = "subaction=allnews&amp;user=" . urlencode ( $user );
			
			if( $config['allow_alt_url'] ) $canonical = $url_page . "/"; else $canonical = $PHP_SELF."?subaction=allnews&user=" . urlencode ( $user );
			
			if ($view_template == "rss") {
				
				$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, p.full_story, p.xfields, p.title, p.category, p.alt_name, e.allow_rss_turbo, e.allow_rss_dzen FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}p.autor = '{$user}' AND p.approve=1 AND e.allow_rss=1" . $where_date . " ORDER BY date DESC LIMIT 0," . $config['rss_number'];
			
			} else {
				
				if (isset ( $_SESSION['dle_sort_allnews'] )) $news_sort_by = $_SESSION['dle_sort_allnews'];
				if (isset ( $_SESSION['dle_direction_allnews'] )) $news_direction_by = $_SESSION['dle_direction_allnews'];
				
				$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}autor = '$user' AND approve=1" . $where_date . " ORDER BY " . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
				$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post {$cat_join_count}WHERE {$stop_list}autor = '$user' AND approve=1" . $where_date;
			}
		
		} elseif ($subaction == 'newposts') {
			// ################ show all unread news #################
			if ($cstart) {
				$cstart = $cstart - 1;
				$cstart = $cstart * $config['news_number'];
			}
			
			$url_page = $config['http_home_url'] . "newposts";
			$user_query = "subaction=newposts";
			
			if( $config['allow_alt_url'] ) $canonical = $url_page . "/"; else $canonical = $PHP_SELF."?subaction=newposts";
			
			$thistime = date ( "Y-m-d H:i:s", $_TIME );
			
			if (isset ( $_SESSION['member_lasttime'] )) {
				$lasttime = date ( "Y-m-d H:i:s", $_SESSION['member_lasttime'] );
			} else {
				$lasttime = date ( "Y-m-d H:i:s", (time () - (3600 * 4)) );
			}
			
			if (isset ( $_SESSION['dle_sort_newposts'] )) $news_sort_by = $_SESSION['dle_sort_newposts'];
			if (isset ( $_SESSION['dle_direction_newposts'] )) $news_direction_by = $_SESSION['dle_direction_newposts'];
			
			$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}approve=1 AND date between '$lasttime' and '$thistime' order by " . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
			$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post {$cat_join_count}WHERE {$stop_list}approve=1 AND date between '$lasttime' and '$thistime'";
			
			$config['allow_cache'] = false;
			
		} elseif ( isset ($_GET['catalog']) ) {
			
			// ################ show by catalog #################
			if ($cstart) {
				$cstart = $cstart - 1;
				$cstart = $cstart * $config['news_number'];
			}

			if( $config['allow_alt_url'] AND $config['seo_control']) {

				if (substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' OR $_GET['cstart'] == 1 OR substr ( $_SERVER['REQUEST_URI'], - 2 ) == '//') {
					
					$re_url = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
					$re_url = reset ( $re_url );
					
					$re_url .= "catalog/".urlencode ( $catalog )."/";
					
					if( $_GET['cstart'] > 1 ) {
						$re_url .= "page/".intval($_GET['cstart'])."/";
					}
					
					
					header("HTTP/1.0 301 Moved Permanently");
					header("Location: {$re_url}");
					die("Redirect");
				}
			}
				
			$url_page = $config['http_home_url'] . "catalog/" . urlencode ( $catalog );
			$user_query = "catalog=" . urlencode ( $catalog );

			if( $config['allow_alt_url'] ) $canonical = $url_page . "/"; else $canonical = $PHP_SELF."?catalog=" . urlencode ( $catalog );

			$news_sort_by = ($config['catalog_sort']) ? $config['catalog_sort'] : "date";
			$news_direction_by = ($config['catalog_msort']) ? $config['catalog_msort'] : "DESC";
			
			if (isset ( $_SESSION['dle_sort_catalog'] )) $news_sort_by = $_SESSION['dle_sort_catalog'];
			if (isset ( $_SESSION['dle_direction_catalog'] )) $news_direction_by = $_SESSION['dle_direction_catalog'];
			
			$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}symbol = '$catalog' AND approve=1" . $where_date . " ORDER BY " . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
			$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post {$cat_join_count}WHERE {$stop_list}symbol = '$catalog' AND approve=1" . $where_date;

		} else {

			// ################ show main page #################
			if ($year == '' AND $month == '' AND $day == '' AND !$newsid) {

				$canonical = $config['http_home_url'];

				if( $config['start_site'] == 2 AND $view_template != "rss") {
					
					break;				
				}
				
				if( $config['allow_alt_url'] AND $config['seo_control'] AND $_GET['cstart'] ) {
	
					if (substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' OR $_GET['cstart'] == 1 ) {
						
						$re_url = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
						$re_url = reset ( $re_url );
						
						if(substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' AND $_GET['cstart'] != 1 ) {
							$re_url .= "page/".intval($_GET['cstart'])."/";
						}
					
						header("HTTP/1.0 301 Moved Permanently");
						header("Location: {$re_url}");
						die("Redirect");
					}
				}
			
				if ($cstart) {
					
					$cstart = $cstart - 1;
					$cstart = $cstart * $config['news_number'];		
				}
			
				$url_page = substr ( $config['http_home_url'], 0, strlen ( $config['http_home_url'] ) - 1 );
				$user_query = "";
				
				if ($view_template == "rss") {
	
					$not_allow_cats = array();
					
					foreach($cat_info as $value) {
						if( !$value['allow_rss'] ) $not_allow_cats[] = $value['id'];
					}
					
					if( count($not_allow_cats) ) {

						if ($config['allow_multi_category']) {
							
							$not_allow_cats = "id NOT IN ( SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN (" . implode ( ',', $not_allow_cats ) . ") ) AND ";
						
						} else {
							
							$not_allow_cats = "category NOT IN ('" . implode ( "','", $not_allow_cats ) . "') AND ";
						
						}
						
					} else $not_allow_cats = "";
					
					$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, p.full_story, p.xfields, p.title, p.category, p.alt_name, e.allow_rss_turbo, e.allow_rss_dzen FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$not_allow_cats}{$stop_list}p.approve=1 AND e.allow_rss=1";
					
					if ($config['rss_mtype']) {
						
						$sql_select .= " AND p.allow_main=1";
					
					}
					
					$sql_select .= $where_date . " ORDER BY date DESC LIMIT 0," . $config['rss_number'];
				
				} else {
					
					if (isset ( $_SESSION['dle_sort_main'] )) $news_sort_by = $_SESSION['dle_sort_main'];
					if (isset ( $_SESSION['dle_direction_main'] )) $news_direction_by = $_SESSION['dle_direction_main'];
					
					$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}approve=1 AND allow_main=1" . $where_date . " ORDER BY " . $fixed . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
					$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post {$cat_join_count}WHERE {$stop_list}approve=1 AND allow_main=1" . $where_date;
				
				}
			}
	
			// ################ Show news by year #################
			if ($year != '' and $month == '' and $day == '') {
				if ($cstart) {
					
					$cstart = $cstart - 1;
					$cstart = $cstart * $config['news_number'];
				}
				
				if( $config['allow_alt_url'] AND $config['seo_control']) {

					if (substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' OR $_GET['cstart'] == 1 OR substr ( $_SERVER['REQUEST_URI'], - 2 ) == '//' OR intval($_GET['year']) < 1970 OR intval($_GET['year']) > 2100) {
						
						$re_url = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
						$re_url = reset ( $re_url );
						
						if (intval($_GET['year']) < 1970 OR intval($_GET['year']) > 2100) {
							$year= date( 'Y', $_TIME );
						}
						
						$re_url .= $year."/";
						
						if( $_GET['cstart'] > 1 ) {
							$re_url .= "page/".intval($_GET['cstart'])."/";
						}
						
						
						header("HTTP/1.0 301 Moved Permanently");
						header("Location: {$re_url}");
						die("Redirect");
					}
				}
				
				$url_page = $config['http_home_url'] . $year;
				$user_query = "year=" . $year;
				
				if( $config['allow_alt_url'] ) $canonical = $url_page . "/"; else $canonical = $PHP_SELF."?year=" . $year;
				
				if (isset ( $_SESSION['dle_sort_date'] )) $news_sort_by = $_SESSION['dle_sort_date'];
				if (isset ( $_SESSION['dle_direction_date'] )) $news_direction_by = $_SESSION['dle_direction_date'];
				
				$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}date >= '{$year}-01-01'AND date < '{$year}-01-01' + INTERVAL 1 YEAR AND approve=1" . $where_date . " ORDER BY " . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
				$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post {$cat_join_count}where {$stop_list}date >= '{$year}-01-01'AND date < '{$year}-01-01' + INTERVAL 1 YEAR AND approve=1" . $where_date;
			}
			
			// ################ Show news by month #################
			if ($year != '' and $month != '' and $day == '') {
				if ($cstart) {
					$cstart = $cstart - 1;
					$cstart = $cstart * $config['news_number'];
				}
				
				if( $config['allow_alt_url'] AND $config['seo_control']) {

					if (substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' OR $_GET['cstart'] == 1 OR substr ( $_SERVER['REQUEST_URI'], - 2 ) == '//' OR intval($_GET['year']) < 1970 OR intval($_GET['year']) > 2100 OR intval($_GET['month']) < 1 OR intval($_GET['month']) > 12) {
						
						$re_url = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
						$re_url = reset ( $re_url );
						
						if (intval($_GET['year']) < 1970 OR intval($_GET['year']) > 2100) {
							$year= date( 'Y', $_TIME );
						}
						
						$re_url .= $year."/";
						
						if (intval($_GET['month']) < 1 OR intval($_GET['month']) > 12) {
							$month= date( 'm', $_TIME );
						}
						
						$re_url .= $month."/";
						
						if( $_GET['cstart'] > 1 ) {
							$re_url .= "page/".intval($_GET['cstart'])."/";
						}

						header("HTTP/1.0 301 Moved Permanently");
						header("Location: {$re_url}");
						die("Redirect");
					}
				}

				$url_page = $config['http_home_url'] . $year . "/" . $month;
				$user_query = "year=" . $year . "&amp;month=" . $month;
				
				if( $config['allow_alt_url'] ) $canonical = $url_page . "/"; else $canonical = $PHP_SELF."?year=" . $year . "&month=" . $month;
				
				if (isset ( $_SESSION['dle_sort_date'] )) $news_sort_by = $_SESSION['dle_sort_date'];
				if (isset ( $_SESSION['dle_direction_date'] )) $news_direction_by = $_SESSION['dle_direction_date'];
				
				$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}date >= '{$year}-{$month}-01'AND date < '{$year}-{$month}-01' + INTERVAL 1 MONTH AND approve=1" . $where_date . " ORDER BY " . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
				$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post {$cat_join_count}where {$stop_list}date >= '{$year}-{$month}-01'AND date < '{$year}-{$month}-01' + INTERVAL 1 MONTH AND approve=1" . $where_date;
			}
		
			// ################ Show news by day #################

			if ($year != '' and $month != '' and $day != '' and $subaction == '') {
				if ($cstart) {
					$cstart = $cstart - 1;
					$cstart = $cstart * $config['news_number'];
				}

				if( $config['allow_alt_url'] AND $config['seo_control']) {

					if (substr ( $_SERVER['REQUEST_URI'], - 1, 1 ) != '/' OR $_GET['cstart'] == 1 OR substr ( $_SERVER['REQUEST_URI'], - 2 ) == '//' OR intval($_GET['year']) < 1970 OR intval($_GET['year']) > 2100 OR intval($_GET['month']) < 1 OR intval($_GET['month']) > 12 OR intval($_GET['day']) < 1 OR intval($_GET['day']) > 31) {
						
						$re_url = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
						$re_url = reset ( $re_url );
						
						if (intval($_GET['year']) < 1970 OR intval($_GET['year']) > 2100) {
							$year= date( 'Y', $_TIME );
						}
						
						$re_url .= $year."/";
						
						if (intval($_GET['month']) < 1 OR intval($_GET['month']) > 12) {
							$month= date( 'm', $_TIME );
						}
						
						$re_url .= $month."/";
						
						if (intval($_GET['day']) < 1 OR intval($_GET['day']) > 31) {
							$day= date( 'd', $_TIME );
						}
						
						$re_url .= $day."/";
						
						if( $_GET['cstart'] > 1 ) {
							$re_url .= "page/".intval($_GET['cstart'])."/";
						}

						header("HTTP/1.0 301 Moved Permanently");
						header("Location: {$re_url}");
						die("Redirect");
					}
				}
				
				$url_page = $config['http_home_url'] . $year . "/" . $month . "/" . $day;
				$user_query = "year=" . $year . "&amp;month=" . $month . "&amp;day=" . $day;
				
				if( $config['allow_alt_url'] ) $canonical = $url_page . "/"; else $canonical = $PHP_SELF."?year=" . $year . "&month=" . $month . "&day=" . $day;
				
				if (isset ( $_SESSION['dle_sort_date'] )) $news_sort_by = $_SESSION['dle_sort_date'];
				if (isset ( $_SESSION['dle_direction_date'] )) $news_direction_by = $_SESSION['dle_direction_date'];
				
				$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p {$cat_join}LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$stop_list}date >= '{$year}-{$month}-{$day}' AND date < '{$year}-{$month}-{$day}' + INTERVAL 24 HOUR AND approve=1" . $where_date . " ORDER BY " . $news_sort_by . " " . $news_direction_by . " LIMIT " . $cstart . "," . $config['news_number'];
				$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post {$cat_join_count}WHERE {$stop_list}date >= '{$year}-{$month}-{$day}' AND date < '{$year}-{$month}-{$day}' + INTERVAL 24 HOUR AND approve=1" . $where_date;
		
			}
			
			// ################ Full News #################
			if ($subaction != '' OR $newsid) {
				if (! $newsid) $sql_news = "SELECT * FROM " . PREFIX . "_post LEFT JOIN " . PREFIX . "_post_extras ON (" . PREFIX . "_post.id=" . PREFIX . "_post_extras.news_id) WHERE alt_name ='$news_name' AND date >= '{$year}-{$month}-{$day}' AND date < '{$year}-{$month}-{$day}' + INTERVAL 24 HOUR LIMIT 1";
				else $sql_news = "SELECT * FROM " . PREFIX . "_post LEFT JOIN " . PREFIX . "_post_extras ON (" . PREFIX . "_post.id=" . PREFIX . "_post_extras.news_id) WHERE  id = '{$newsid}'";
				
				if ($subaction == '') $subaction = "showfull";
			}
		}
		
		if (($subaction == "showfull" or $subaction == "addcomment") AND ( (!isset($category) OR $category == "") )) {
			
			$allow_active_news = false;
			
			//####################################################################################################################
			//          Add a comment to the database
			//####################################################################################################################
			if (isset( $_POST['subaction'] ) AND $_POST['subaction'] == "addcomment") {
				
				$allow_add_comment = true;
				$allow_comments = true;
				$ajax_adds = false;
				
				include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/addcomments.php'));
			}
			//####################################################################################################################
			//         Show the full news
			//####################################################################################################################
			if ($subaction == "showfull") {
				$allow_comments = true;
			
				include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/show.full.php'));
			}
		
		} else {
			
			//####################################################################################################################
			//         Viewing user profile
			//####################################################################################################################
			if ($subaction == 'userinfo') {
				
				$allow_userinfo = true;
				include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/profile.php'));
			
			}
			
			//####################################################################################################################
			//         Viewing short news
			//####################################################################################################################
			
			$cache_prefix = "content_".$dle_module;

			$_SESSION['referrer'] = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, $config['charset'] );
			
			if ($catalog != "") {
				
				$cache_prefix .= "_catalog_" . $catalog;
			
			} elseif ($do == "lastnews") {
				
				$cache_prefix .= "_lastnews";
			
			} elseif ($subaction == 'allnews') {

				$cache_prefix .= "_allnews_". $user;

			} elseif ($do == 'tags') {

				$cache_prefix .= "_tagscl_". $tag;

			} elseif ($do == 'xfsearch') {
				
				if($xfname) $cache_prefix .= "_xfsearch_" . $xfname . "_" . $xf;
				else $cache_prefix .= "_xfsearch_". $xf;

			} else {
				
				$cache_prefix .= "_";
				
				if ($month) $cache_prefix .= "month_" . $month;
				if ($year) $cache_prefix .= "year_" . $year;
				if ($day) $cache_prefix .= "day_" . $day;
				if ($category) $cache_prefix .= "category_" . $category;
			}
			
			$cache_prefix .= "_tempate_" . $config['skin'];

			if ($view_template == "rss") {

				if ($catalog) $active = dle_cache ( "rss", $catalog, false );				
				else $active = dle_cache ( "rss", $category_id, false );
			
			} else {
				
				if ($is_logged and ($user_group[$member_id['user_group']]['allow_edit'] and ! $user_group[$member_id['user_group']]['allow_all_edit'])) $config['allow_cache'] = false;
				if (isset($_SESSION['dle_no_cache']) AND $_SESSION['dle_no_cache']) $config['allow_cache'] = false;
				if ($cstart) $cache_id = ($cstart / $config['news_number']) + 1;
				else $cache_id = 1;
				
				$config['max_cache_pages'] = intval($config['max_cache_pages']);
				if($config['max_cache_pages'] < 3) $config['max_cache_pages'] = 3;

				if ($config['allow_cache'] AND $cache_id <= $config['max_cache_pages']) {
					$active = dle_cache( "news", $cache_id . $cache_prefix, true );
					
				if( $active ) {
					$active = json_decode($active, true);
				}
	
					$short_news_cache = true;
				} else {
					$active = false;
					$short_news_cache = false;
				}
			
			}
			
			if ( is_array($active) ) {

				$tpl->result['content'] .= $active['content'];
				$tpl->result['navigation'] = $active['navigation'];
				
				$active = null;
				$news_found = true;
				if ($config['allow_quick_wysiwyg'] and ($user_group[$member_id['user_group']]['allow_edit'] or $user_group[$member_id['user_group']]['allow_all_edit'])) $allow_comments_ajax = true;
				else $allow_comments_ajax = false;
			
			} else {
				
				include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/show.short.php'));
				
				if (!$config['allow_quick_wysiwyg']) $allow_comments_ajax = false;
				
				if ($config['files_allow']) if (strpos ( $tpl->result['content'], "[attachment=" ) !== false) {
					$tpl->result['content'] = show_attach ( $tpl->result['content'], $attachments );
				}
				
				if ($view_template == "rss" AND $news_found) {
					
					if ($catalog) create_cache ( "rss", $tpl->result['content'], $catalog, false );
					else create_cache ( "rss", $tpl->result['content'], $category_id, false );
				
				} elseif ($news_found AND $cache_id <= $config['max_cache_pages'] ) create_cache ( "news", json_encode( array('content' => $tpl->result['content'], 'navigation' => $tpl->result['navigation'] ) , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), $cache_id . $cache_prefix, true );

			}
			
			if($tpl->result['content'] AND $canonical AND intval($_GET['cstart']) AND intval($_GET['cstart']) != 1 ) {
					
					if( $config['allow_alt_url'] ) {
						
						$canonical .= "page/".intval($_GET['cstart'])."/";
						
					} else {
						
						if ($user_query) {
							
							$canonical = "{$PHP_SELF}?cstart=".intval($_GET['cstart'])."&".str_replace('&amp;', '&', $user_query);
							
						} else $canonical = "{$PHP_SELF}?cstart=".intval($_GET['cstart']);
					}
					
			}
			
		
		}

}

/*
=====================================================
 The output of the page header
=====================================================
*/
$titl_e = '';
$nam_e = '';
$rss_url = '';
$rss_title = '';

if ($do == "cat" and $category != '' and $subaction == '') {
	
	$metatags['description'] = ($cat_info[$category_id]['descr'] != '') ? $cat_info[$category_id]['descr'] : $metatags['description'];
	$metatags['keywords'] = ($cat_info[$category_id]['keywords'] != '') ? $cat_info[$category_id]['keywords'] : $metatags['keywords'];

	if ($cat_info[$category_id]['metatitle'] != '') $metatags['header_title'] = $cat_info[$category_id]['metatitle'];
	else $nam_e = stripslashes ( $cat_info[$category_id]['name'] );
	
	if ($config['allow_alt_url'] ) {
		$rss_url = $url_page . "/" . "rss.xml";
	} else {
		$rss_url = $PHP_SELF . "?mod=rss&do=cat&category=" . $cat_info[$category_id]['alt_name'];
	}

} elseif ($subaction == 'userinfo') {
	$nam_e = $user;
	
	if ($config['allow_alt_url'] ) {
		$rss_url = $url_page . "/" . "rss.xml";
	} else {
		$rss_url = $PHP_SELF . "?mod=rss&subaction=allnews&user=" . urlencode ( $user );
	}

} elseif ($subaction == 'allnews') {
	$nam_e = $lang['show_user_news'] . ' ' . $user;
	
	if ($config['allow_alt_url']) {
		$rss_url = $config['http_home_url'] . "user/" . urlencode ( $user ) . "/" . "rss.xml";
	} else {
		$rss_url = $PHP_SELF . "?mod=rss&subaction=allnews&user=" . urlencode ( $user );
	}

} elseif ($subaction == 'newposts') $nam_e = $lang['title_new'];
elseif ($do == 'stats') $nam_e = $lang['title_stats'];
elseif ($do == 'addnews') $nam_e = $lang['title_addnews'];
elseif ($do == 'register') $nam_e = $lang['title_register'];
elseif ($do == 'favorites') $nam_e = $lang['title_fav'];
elseif ($do == 'pm') $nam_e = $lang['title_pm'];
elseif ($do == 'feedback') $nam_e = $lang['title_feed'];
elseif ($do == 'lastcomments') $nam_e = $lang['title_last'];
elseif ($do == 'lostpassword') $nam_e = $lang['title_lost'];
elseif ($do == 'search') $nam_e = $lang['title_search'];
elseif ($do == 'static') $titl_e = $static_descr;
elseif ($do == 'lastnews') $nam_e = $lang['last_news'];
elseif ($do == 'alltags') $nam_e = $lang['tag_cloud'];
elseif ($do == 'rules') $nam_e = $lang['rules_page'];
elseif ($do == 'tags') $nam_e = stripslashes($tag);
elseif ($do == 'xfsearch') $nam_e = $xf;
elseif ($catalog != "") { 
	$nam_e = $lang['title_catalog'] . ' &raquo; ' . $catalog;

	if ($config['allow_alt_url']) {
		$rss_url = $config['http_home_url'] . "catalog/" . urlencode ( $catalog ) . "/" . "rss.xml";
	} else {
		$rss_url = $PHP_SELF . "?mod=rss&catalog=" . urlencode ( $catalog );
	}

}
else {
	
	if ($year != '' and $month == '' and $day == '') $nam_e = $lang['title_date'] . ' ' . $year . ' ' . $lang['title_year'];
	if ($year != '' and $month != '' and $day == '') $nam_e = $lang['title_date'] . ' ' . $r[$month - 1] . ' ' . $year . ' ' . $lang['title_year1'];
	if ($year != '' and $month != '' and $day != '' and $subaction == '') $nam_e = $lang['title_date'] . ' ' . $day . '.' . $month . '.' . $year;
	if (($subaction != '' or $newsid != '') and $news_found) $titl_e = $metatags['title'];

}

if ( ( isset($_GET['cstart']) AND intval($_GET['cstart']) > 1 ) OR (isset($_GET['news_page']) AND intval($_GET['news_page']) > 1) ){

	if ( isset($_GET['cstart']) AND intval($_GET['cstart']) > 1 ) $page_extra = ' &raquo; '.$lang['news_site'].' '.intval($_GET['cstart']);
	else $page_extra = ' &raquo; '.$lang['news_site'].' '.intval($_GET['news_page']);

} else $page_extra = '';


if ($nam_e) {

	$metatags['title'] = $nam_e . $page_extra . ' &raquo; ' . $metatags['title'];
	$rss_title = $metatags['title'];

} elseif ($titl_e) {

	$metatags['title'] = $titl_e . $page_extra . ' &raquo; ' . $config['home_title'];

} else $metatags['title'] .= $page_extra;

if ( $metatags['header_title'] ) $metatags['title'] = stripslashes($metatags['header_title'].$page_extra);

if (! $rss_url) {
	
	if ($config['allow_alt_url']) {
		$rss_url = $config['http_home_url'] . "rss.xml";
	} else {
		$rss_url = $PHP_SELF . "?mod=rss";
	}
	
	$rss_title = $config['home_title'];
}

$s_meta = "";

if ( count($social_tags) ) {

	foreach ($social_tags as $key => $value) {
		
		$value=str_replace(array("{", "}", "[", "]"),"",$value);

		if( $key == "news_keywords" ) {
			$s_meta .= "<meta name=\"{$key}\" content=\"{$value}\">\n";
		} else {
			$s_meta .= "<meta property=\"og:{$key}\" content=\"{$value}\">\n";
		}

	}
}

if ( $disable_index ) $metatags['robots'] = "noindex,nofollow";

if( $config['allow_own_meta'] ) {
	
	if(is_array($custom_metatags['simple']) AND count($custom_metatags['simple']) AND $custom_metatags['simple'][$r_uri] ) {
		if( $custom_metatags['simple'][$r_uri]['title'] ) $metatags['title'] = $custom_metatags['simple'][$r_uri]['title'];
		if( $custom_metatags['simple'][$r_uri]['description'] ) $metatags['description'] = $custom_metatags['simple'][$r_uri]['description'];
		if( $custom_metatags['simple'][$r_uri]['keywords'] ) $metatags['keywords'] = $custom_metatags['simple'][$r_uri]['keywords'];
		if( $custom_metatags['simple'][$r_uri]['robots'] ) $metatags['robots'] = $custom_metatags['simple'][$r_uri]['robots'];
	}
	
	if(is_array($custom_metatags['regex']) AND count($custom_metatags['regex'])) {	
		foreach ($custom_metatags['regex'] as $key => $value) {
			if(preg_match($key, $r_uri)){
				if( $value['title'] ) $metatags['title'] = $value['title'];
				if( $value['description'] ) $metatags['description'] = $value['description'];
				if( $value['keywords'] ) $metatags['keywords'] = $value['keywords'];
				if( $value['robots'] ) $metatags['robots'] = $value['robots'];
		    }
		}
	}

}

$metatags['title']=str_replace(array("{", "}", "[", "]"), "", $metatags['title']);
$metatags['description']=str_replace(array("{", "}", "[", "]"), "", $metatags['description']);
$metatags['keywords']=str_replace(array("{", "}", "[", "]"), "", $metatags['keywords']);
$metatags['robots']=str_replace(array("{", "}", "[", "]"), "", $metatags['robots']);

if( $metatags['robots'] ) $robots_meta = "\n<meta name=\"robots\" content=\"{$metatags['robots']}\">";
else $robots_meta = "";

$metatags = <<<HTML
<meta charset="{$config['charset']}">
<title>{$metatags['title']}</title>
<meta name="description" content="{$metatags['description']}">
<meta name="keywords" content="{$metatags['keywords']}">{$robots_meta}
<meta name="generator" content="DataLife Engine (http://dle-news.ru)">
{$s_meta}<link rel="search" type="application/opensearchdescription+xml" href="{$PHP_SELF}?do=opensearch" title="{$config['home_title']}">
HTML;

if ($canonical) {
	
	if (strpos($canonical, "//") === 0) $canonical = "https:".$canonical;
	elseif (strpos($canonical, "/") === 0) $canonical = "http://".$_SERVER['HTTP_HOST'].$canonical;

	$metatags .= "\n<link rel=\"canonical\" href=\"{$canonical}\">";

}

if ($config['allow_rss']) $metatags .= "\n<link rel=\"alternate\" type=\"application/rss+xml\" title=\"{$rss_title}\" href=\"{$rss_url}\">";

/*
=====================================================
 speedbar creation
=====================================================
*/

if ($config['speedbar'] AND !$view_template ) {
	
	$s_navigation = "<span itemprop=\"itemListElement\" itemscope itemtype=\"https://schema.org/ListItem\"><meta itemprop=\"position\" content=\"1\"><a href=\"{$config['http_home_url']}\" itemprop=\"item\"><span itemprop=\"name\">" . $config['short_title'] . "</span></a></span>";

	if( $config['start_site'] == 3 AND $_SERVER['QUERY_STRING'] == "" AND !$_POST['do']) $titl_e = "";

	if (intval($category_id)){
		
		if($titl_e OR (isset($_GET['cstart']) AND intval($_GET['cstart']) > 1) ) {
			$last_link = true;
		} else $last_link = false;
		
		$s_navigation .= " {$config['speedbar_separator']} " . get_breadcrumbcategories ( intval($category_id), $config['speedbar_separator'], $last_link );
		
	} elseif ($do == 'tags') {
		
		if ($config['allow_alt_url']) $s_navigation .= " {$config['speedbar_separator']} <span itemprop=\"itemListElement\" itemscope itemtype=\"https://schema.org/ListItem\"><meta itemprop=\"position\" content=\"2\"><a href=\"" . $config['http_home_url'] . "tags/\" itemprop=\"item\"><span itemprop=\"name\">" . $lang['tag_cloud'] . "</span></a></span> {$config['speedbar_separator']} " . $tag;
		else $s_navigation .= " {$config['speedbar_separator']} <span itemprop=\"itemListElement\" itemscope itemtype=\"https://schema.org/ListItem\"><meta itemprop=\"position\" content=\"2\"><a href=\"?do=tags\" itemprop=\"item\"><span itemprop=\"name\">" . $lang['tag_cloud'] . "</span></a></span> {$config['speedbar_separator']} " . $tag;

	} elseif ($nam_e) $s_navigation .= " {$config['speedbar_separator']} " . $nam_e;

	if ($titl_e) {
		
		$s_navigation .= " {$config['speedbar_separator']} " . $titl_e;
		
	} else {

		if ( isset($_GET['cstart']) AND intval($_GET['cstart']) > 1 ){
		
			$page_extra = " {$config['speedbar_separator']} ".$lang['news_site']." ".intval($_GET['cstart']);
		
		} else $page_extra = '';

		$s_navigation .= $page_extra;

	}
	
	$tpl->load_template ( 'speedbar.tpl' );
	$tpl->set ( '{speedbar}', '<span itemscope itemtype="https://schema.org/BreadcrumbList">' . stripslashes ( $s_navigation ) . '</span>' );
	$tpl->compile ( 'speedbar' );
	$tpl->clear ();

}
?>