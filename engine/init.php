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
 File: init.php
-----------------------------------------------------
 Use: Initialization
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../' );
	die( "Hacking attempt!" );
}

date_default_timezone_set ( $config['date_adjust'] );

require_once (DLEPlugins::Check(ENGINE_DIR . '/modules/functions.php'));

dle_session();
check_xss ();

$Timer = new microTimer();
$cron = false;
$_TIME = time();
$PHP_SELF = $config['http_home_url'] . "index.php";
$pm_alert = "";
$twofactor_alert = "";
$ajax = "";
$allow_comments_ajax = false;
$_DOCUMENT_DATE = false;
$user_query = "";
$static_result = array ();
$is_logged = false;
$member_id = array ();
$related_buffer = false;
$banners = array ();
$banner_in_news = array ();
$js_array = array ();
$css_array = array ();
$replace_links = array ();
$custom_news = false;
$dle_tree_comments = 0;
$attachments = array ();
$view_template = false;
$short_news_cache = false;
$onload_scripts = array();
$remove_canonical = false;
$smartphone_detected = false;
$vk_url = false;
$odnoklassniki_url = false;
$facebook_url = false;
$google_url = false;
$mailru_url = false;
$yandex_url = false;
$need_404 = false;
$xfieldsdata = "";
$xfields = array();
$custom_navigation = false;
$metatags = array ( 'title' => $config['home_title'], 'description' => $config['description'], 'keywords' => $config['keywords'], 'header_title' => "" );
$config['charset'] = strtolower(trim($config['charset']));
$_SERVER['PHP_SELF'] = htmlspecialchars( $_SERVER['PHP_SELF'], ENT_QUOTES, $config['charset'] );


if ( !$config['http_home_url'] ) {

	$config['http_home_url'] = explode ( "index.php", $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset ( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];

}

if( isSSL() AND stripos( $config['http_home_url'], 'http://' ) !== false ) {
	$config['http_home_url'] = str_replace( "http://", "https://", $config['http_home_url'] );
}

if (substr ( $config['http_home_url'], - 1, 1 ) != '/') $config['http_home_url'] .= '/';

if( $config['start_site'] == 3 AND $_SERVER['QUERY_STRING'] == "" AND !$_POST['do']) {

	$_GET['do'] = "static";
	$_REQUEST['do'] = "static";
	$_GET['page'] = "main";
	$_REQUEST['page'] = "main";

}

if (isset ( $_GET['year'] )) {
	
	$year = intval($_GET['year']);
		
	if($year < 1970 ) $year = 1970;
	if($year > 2100 ) $year = 2100;
	
} else $year = '';

if (isset ( $_GET['month'] )) {
	
	$month = intval($_GET['month']);
	
	if($month < 1 OR $_GET['month'] > 12 ) $month = 1;
	
	$month = @$db->safesql ( sprintf("%02d", $month ) );
	
} else $month = '';

if (isset ( $_GET['day'] )) {
	$day = intval($_GET['day']);
	
	if($day < 1 OR $day > 31 ) $day = 1;
	
	$day = @$db->safesql ( sprintf("%02d", $day ) );
	
} else $day = '';

if (isset ( $_GET['catalog'] )) {

	$catalog = strip_tags ( str_replace ( '/', '', urldecode ( (string)$_GET['catalog'] ) ) );
	$catalog = $db->safesql ( dle_substr ( $catalog, 0, 3, $config['charset'] ) );

} else $catalog = '';

if (isset ( $_GET['user'] )) {

	$user = strip_tags ( str_replace ( '/', '', urldecode ( (string)$_GET['user'] ) ) );
	$user = $db->safesql ( $user );

	if( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\#|\/|\\\|\&\~\*\+]/", $user ) ) $user="";

} else $user = '';

if (isset ( $_GET['category'] )) {
	$_GET['category'] = (string)$_GET['category'];
	if (substr ( $_GET['category'], - 1, 1 ) == '/') $_GET['category'] = substr ( $_GET['category'], 0, - 1 );
	$category = explode ( '/', $_GET['category'] );
	$category = end ( $category );
	$category = $db->safesql ( strip_tags ( $category ) );
} else $category = '';

if (isset ( $_GET['cstart'] ) AND $_GET['cstart'] < 1 ) $_GET['cstart'] = 0;
if (isset ( $_GET['news_name'] )) $news_name = @$db->safesql ( strip_tags ( str_replace ( '/', '', (string)$_GET['news_name'] ) ) ); else $news_name = '';
if (isset ( $_GET['newsid'] )) $newsid = intval ( $_GET['newsid'] ); else $newsid = 0;
if (isset ( $_GET['cstart'] )) $cstart = intval ( $_GET['cstart'] ); else $cstart = 0;
if (isset ( $_GET['news_page'] )) $news_page = intval ( $_GET['news_page'] ); else $news_page = 0;
if ($cstart > 9000000) $cstart = 0;

if( isset( $_REQUEST['action'] ) and $_REQUEST['action'] == "mobiledisable" ) { $_SESSION['mobile_disable'] = 1; $_SESSION['mobile_enable'] = 0; }
if( isset( $_REQUEST['action'] ) and $_REQUEST['action'] == "mobile" ) { $_SESSION['mobile_enable'] = 1; $_SESSION['mobile_disable'] = 0;}
if( !isset( $_SESSION['mobile_disable'] ) ) $_SESSION['mobile_disable'] = 0;
if( !isset( $_SESSION['mobile_enable'] ) ) $_SESSION['mobile_enable'] = 0;
if( !isset ( $do ) AND isset ($_REQUEST['do']) ) $do = totranslit ( $_REQUEST['do'] ); elseif(isset ( $do )) $do = totranslit ( $do ); else $do = '';
if( !isset ( $subaction ) AND isset ($_REQUEST['subaction']) ) $subaction = totranslit ($_REQUEST['subaction']); elseif(isset($subaction)) $subaction = totranslit($subaction); else $subaction = '';
if( isset ($_REQUEST['doaction']) ) $doaction = totranslit ($_REQUEST['doaction']); else $doaction = "";
if( $do == "tags" AND !$_GET['tag'] ) $do = "alltags";

$dle_module = $do;
if (!$do AND !$subaction AND $year) $dle_module = "date";
elseif (!$do AND isset($_GET['catalog'])) $dle_module = "catalog";
elseif (!$do) $dle_module = $subaction;
if (!$subaction AND $newsid) $dle_module = "showfull";
$dle_module = $dle_module ? $dle_module : "main";

if( $config['start_site'] == 3 AND !$_SERVER['QUERY_STRING'] AND !$_POST['do']) {
	$dle_module = "main";
}

//################# Definition of user groups
$user_group = get_vars ( "usergroup" );

if (!is_array( $user_group )) {
	$user_group = array ();

	$db->query ( "SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC" );

	while ( $row = $db->get_row () ) {

		$user_group[$row['id']] = array ();

		foreach ( $row as $key => $value ) {
			$user_group[$row['id']][$key] = stripslashes($value);
		}

	}
	set_vars ( "usergroup", $user_group );
	$db->free ();
}

//####################################################################################################################
//     Definition of categories
//####################################################################################################################
$cat_info = get_vars ( "category" );

if (!is_array ( $cat_info )) {
	$cat_info = array ();

	$db->query ( "SELECT * FROM " . PREFIX . "_category ORDER BY posi ASC" );
	
	while ( $row = $db->get_row () ) {

		if( !$row['active'] ) continue;
	
		$cat_info[$row['id']] = array ();

		foreach ( $row as $key => $value ) {
			$cat_info[$row['id']][$key] = stripslashes ( $value );
		}
		
		$cat_info[$row['id']]['newscount'] = 0;

	}
	set_vars ( "category", $cat_info );
	$db->free ();
}

if( $do == "download" ) {
	include_once (DLEPlugins::Check(ENGINE_DIR . '/download.php'));
	die();
} elseif($do == "go") {
	include_once (DLEPlugins::Check(ENGINE_DIR . '/go.php'));
	die();
} elseif($do == "opensearch") {
	include_once (DLEPlugins::Check(ENGINE_DIR . '/opensearch.php'));
	die();
} elseif($_GET['mod'] == "rss") {
	include_once (DLEPlugins::Check(ENGINE_DIR . '/rss.php'));
	die();
}

if( $config['allow_redirects'] ) {
	$redirects = get_vars( "redirects" );
	
	if( !is_array( $redirects ) ) {
		$redirects = array ();

		$db->query( "SELECT * FROM " . PREFIX . "_redirects ORDER BY id DESC" );
		
		while ( $row = $db->get_row() ) {
			
			if( strpos ( $row['from'], "*" ) !== false ) {
				
				$row['from'] = preg_quote(urldecode($row['from']), '%');
				$row['from'] = '%^'.str_replace('\*', '(.*)', $row['from']).'%i';
				$redirects['regex'][$row['from']] = $row['to'];
			
			} else {
				$row['from'] = urldecode($row['from']);
				$redirects['simple'][$row['from']] = urldecode($row['to']);
			}
		
		}
		
		set_vars( "redirects", $redirects );
		$db->free();
	}
	
	$uri = preg_replace( '#[/]+#i', '/', urldecode($_SERVER['REQUEST_URI']) );

	if(is_array($redirects['simple']) AND count($redirects['simple']) AND $redirects['simple'][$uri] AND !isset($_SESSION['is_redirect'])  ) {
		$_SESSION['is_redirect'] = true;
		header("HTTP/1.0 301 Moved Permanently");
		header("Location: ". $redirects['simple'][$uri] );
		die("301 Redirect");
	}
	
	if(is_array($redirects['regex']) AND count($redirects['regex']) AND !isset($_SESSION['is_redirect'])  ) {	
		foreach ($redirects['regex'] as $key => $value) {
			if(preg_match($key, $uri)){
				$_SESSION['is_redirect'] = true;
				header("HTTP/1.0 301 Moved Permanently");
				header("Location: ". $value );
				die("301 Redirect");
		    }
		}
	}
	
	unset($_SESSION['is_redirect']);

}

if( $config['only_ssl'] AND !isSSL() AND !isset($_SESSION['is_redirect']) ) {
	$_SESSION['is_redirect'] = true;
	
	$_SERVER['REQUEST_URI'] = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, $config['charset'] );
	header("HTTP/1.0 301 Moved Permanently");
	header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	die("Redirect");

} elseif( isset($_SESSION['is_redirect']) ) { unset($_SESSION['is_redirect']); }

$cron_time = get_vars ( "cron" );

if (date ( "Y-m-d", $cron_time ) != date ( "Y-m-d", $_TIME )) $cron = 2;
elseif (($cron_time + (3600 * 2)) < $_TIME) $cron = 1;

if ($cron) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/cron.php'));

//####################################################################################################################
//    meta tags and titles for pages
//####################################################################################################################
$custom_metatags = array ();
$page_header_info = array();

if( $config['allow_own_meta'] ) {
	$custom_metatags = get_vars( "metatags" );
	
	if( !is_array( $custom_metatags ) ) {
		$custom_metatags = array ();

		$db->query( "SELECT * FROM " . PREFIX . "_metatags ORDER BY id DESC" );
		
		while ( $row = $db->get_row() ) {
			
			if( strpos ( $row['url'], "*" ) !== false ) {

				$row['url'] = preg_quote(urldecode($row['url']), '%');
				$row['url'] = '%^'.str_replace('\*', '(.*)', $row['url']).'%i';
				
				$custom_metatags['regex'][$row['url']] = array('title' => $row['title'], 'description' => $row['description'], 'keywords' => $row['keywords'], 'page_title' => $row['page_title'], 'robots' => $row['robots'], 'page_description' => stripslashes($row['page_description']));

			} else {

				$row['url'] = urldecode($row['url']);
				$custom_metatags['simple'][$row['url']] = array('title' => $row['title'], 'description' => $row['description'], 'keywords' => $row['keywords'], 'page_title' => $row['page_title'], 'robots' => $row['robots'], 'page_description' => stripslashes($row['page_description']));

			}
		
		}
		
		set_vars( "metatags", $custom_metatags );
		$db->free();
	}

	$r_uri = preg_replace( '#[/]+#i', '/', urldecode($_SERVER['REQUEST_URI']) );

	$url_charset = detect_encoding($r_uri);

	if ( $url_charset AND $url_charset != $config['charset'] ) {

		if( function_exists( 'mb_convert_encoding' ) ) {
	
			$r_uri = mb_convert_encoding( $r_uri, $config['charset'], $url_charset );
	
		} elseif( function_exists( 'iconv' ) ) {
		
			$r_uri = iconv($url_charset, $config['charset'], $r_uri);
		
		}

	}

	if(is_array($custom_metatags['simple']) AND count($custom_metatags['simple']) AND $custom_metatags['simple'][$r_uri] ) {
		if( $custom_metatags['simple'][$r_uri]['page_title'] ) $page_header_info['title'] = $custom_metatags['simple'][$r_uri]['page_title'];
		if( $custom_metatags['simple'][$r_uri]['page_description'] ) $page_header_info['description'] = $custom_metatags['simple'][$r_uri]['page_description'];
	}
	
	if(is_array($custom_metatags['regex']) AND count($custom_metatags['regex'])) {	
		foreach ($custom_metatags['regex'] as $key => $value) {
			if(preg_match($key, $r_uri)){
				if( $value['page_title'] ) $page_header_info['title'] = $value['page_title'];
				if( $value['page_description'] ) $page_header_info['description'] = $value['page_description'];
		    }
		}
	}
	
}

//####################################################################################################################
//     Counting the number of news categories
//####################################################################################################################
if( $config['category_newscount'] ) {

	$news_count_in_array = dle_cache ( "news", "newscountcacheincats" );
	
	if( $news_count_in_array ) {
	
			$news_count_in_array = json_decode($news_count_in_array, true);
	
			if ( !is_array($news_count_in_array) ) $news_count_in_array = array();
	
	} else {
	
		$news_count_in_array = array();
		
		if( $config['no_date'] AND !$config['news_future'] ) {
			$thisdate = date( "Y-m-d H:i:s", $_TIME );
			$where_date = " AND date < '" . $thisdate . "'";
		} else $where_date = "";
		
		$db->query( "SELECT category, COUNT(*) AS count FROM " . PREFIX . "_post WHERE approve=1" . $where_date . " GROUP BY category" );
		
		while ( $row = $db->get_row() ) {
			
			if(!$row['category']) continue;
		
			$cat_array = $temp_cat_array = explode(",", $row['category']);
			
			foreach ( $temp_cat_array as $value ) {
				
				if(!isset($news_count_in_array[$value])) $news_count_in_array[$value] = $row['count'];
				else $news_count_in_array[$value] = $news_count_in_array[$value] + $row['count'];
		
				$sub_count = $config['show_sub_cats'];
				
				if ( $cat_info[$cat_info[$value]['parentid']]['show_sub'] ) {
	
					if (  $cat_info[$cat_info[$value]['parentid']]['show_sub'] == 1 ) $sub_count = 1; else $sub_count = 0;
	
				}
	
				if( $sub_count ) {

					$temp_parent = $cat_info[$value]['parentid'];

					while ( $temp_parent ) {

						if( !in_array($temp_parent, $cat_array) ) {
					
							if(!isset($news_count_in_array[$temp_parent])) $news_count_in_array[$temp_parent] = $row['count'];
							else $news_count_in_array[$temp_parent] = $news_count_in_array[$temp_parent] + $row['count'];
							
							$cat_array[] = $temp_parent;
				
						}

						$temp_parent = $cat_info[$temp_parent]['parentid'];
					}
				}

			}
			
		}
		
		create_cache ( "news", json_encode($news_count_in_array), "newscountcacheincats" );
		unset($temp_parent, $temp_cat_array, $cat_array);
	}

	foreach ( $news_count_in_array as $key => $value ) {
		if($cat_info[$key]['id']) $cat_info[$key]['newscount'] = $value;
	}
	
	unset($news_count_in_array);
}

//####################################################################################################################
//    The definition of banned users and IP
//####################################################################################################################
$banned_info = get_vars ( "banned" );

if (!is_array ( $banned_info )) {

	$banned_info = array ();

	$db->query ( "SELECT * FROM " . USERPREFIX . "_banned" );
	while ( $row = $db->get_row () ) {

		if ($row['users_id']) {

			$banned_info['users_id'][$row['users_id']] = array (
																'users_id' => $row['users_id'],
																'descr' => stripslashes ( $row['descr'] ),
																'date' => $row['date'] );

		} else {

			if (count ( explode ( ".", $row['ip'] ) ) == 4 OR filter_var( $row['ip'] , FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) OR strpos($row['ip'], ":") !== false )
				$banned_info['ip'][$row['ip']] = array (
														'ip' => $row['ip'],
														'descr' => stripslashes ( $row['descr'] ),
														'date' => $row['date']
														);
			elseif (strpos ( $row['ip'], "@" ) !== false)
				$banned_info['email'][$row['ip']] = array (
															'email' => $row['ip'],
															'descr' => stripslashes ( $row['descr'] ),
															'date' => $row['date'] );
			else $banned_info['name'][$row['ip']] = array (
															'name' => $row['ip'],
															'descr' => stripslashes ( $row['descr'] ),
															'date' => $row['date'] );

		}

	}
	set_vars ( "banned", $banned_info );
	$db->free ();
}

$category_skin = "";

if ($category != '') $category_id = get_ID( $cat_info, $category );
else $category_id = false;

if ($category_id) $category_skin = $cat_info[$category_id]['skin'];

// #################################
if ($news_name != '' OR $newsid) {

	$allow_sql_skin = false;

	foreach ( $cat_info as $cats ) {
		if ($cats['skin'] != '') $allow_sql_skin = true;
	}

	if ($allow_sql_skin) {

		if (!$newsid) $sql_skin = $db->super_query ( "SELECT category FROM " . PREFIX . "_post where month(date) = '$month' AND year(date) = '$year' AND dayofmonth(date) = '$day' AND alt_name ='$news_name'" );
		else $sql_skin = $db->super_query ( "SELECT category FROM " . PREFIX . "_post where  id = '$newsid' AND approve" );

		$base_skin = explode ( ',', $sql_skin['category'] );

		$category_skin = $cat_info[$base_skin[0]]['skin'];

		unset ( $sql_skin );
		unset ( $base_skin );

	}

}

if (isset($_GET['do']) AND $_GET['do'] == "static") {

	$name = @$db->safesql( trim( totranslit( $_GET['page'], true, false ) ) );
	$static_result = $db->super_query ( "SELECT * FROM " . PREFIX . "_static WHERE name='{$name}'" );
	$category_skin = $static_result['template_folder'];

}

if ($category_skin != "") {

	$category_skin = trim( totranslit($category_skin, false, false) );

	if ($category_skin AND @is_dir ( ROOT_DIR . '/templates/' . $category_skin )) {
		$config['skin'] = $category_skin;
	}

} elseif (isset ( $_REQUEST['action_skin_change'] )) {

	$_REQUEST['skin_name'] = trim( totranslit($_REQUEST['skin_name'], false, false) );

	if ($_REQUEST['skin_name'] AND @is_dir ( ROOT_DIR . '/templates/' . $_REQUEST['skin_name'] ) ) {
		$config['skin'] = $_REQUEST['skin_name'];
		set_cookie ( "dle_skin", $_REQUEST['skin_name'], 365 );
	}

} elseif (isset ( $_COOKIE['dle_skin'] ) ) {

	$_COOKIE['dle_skin'] = trim( totranslit($_COOKIE['dle_skin'], false, false) );

	if ($_COOKIE['dle_skin'] != '' AND @is_dir ( ROOT_DIR . '/templates/' . $_COOKIE['dle_skin'] )) {
		$config['skin'] = $_COOKIE['dle_skin'];
	}
}

if (isset ( $config["lang_" . $config['skin']] ) AND $config["lang_" . $config['skin']] != '' AND file_exists( DLEPlugins::Check(ROOT_DIR . '/language/' . $config["lang_" . $config['skin']] . '/website.lng') ) ) {

	include_once (DLEPlugins::Check(ROOT_DIR . '/language/' . $config["lang_" . $config['skin']] . '/website.lng'));
	
} else {

	include_once (DLEPlugins::Check(ROOT_DIR . '/language/' . $config['langs'] . '/website.lng'));

}

if (isset ( $_POST['set_new_sort'] ) AND $config['allow_change_sort']) {

	$allowed_sort = array (
							'date',
							'rating',
							'news_read',
							'comm_num',
							'title' );

	if( !$config['allow_comments'] ) unset($allowed_sort[3]);

	$find_sort = str_replace ( ".", "", totranslit ( $_POST['set_new_sort'] ) );
	$direction_sort = str_replace ( ".", "", totranslit ( $_POST['set_direction_sort'] ) );

	if (in_array($_POST['dlenewssortby'], $allowed_sort) AND stripos($find_sort, "dle_sort_") === 0) {

		if ($_POST['dledirection'] == "desc" or $_POST['dledirection'] == "asc") {

			$_SESSION[$find_sort] = $_POST['dlenewssortby'];
			$_SESSION[$direction_sort] = $_POST['dledirection'];
			$_SESSION['dle_sort_global'] = $_POST['dlenewssortby'];
			$_SESSION['dle_direction_global'] = $_POST['dledirection'];
			$_SESSION['dle_no_cache'] = "1";

		}

	}

}

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/templates.class.php'));

$tpl = new dle_template();

if ( ($config['allow_smartphone'] AND !$_SESSION['mobile_disable'] AND $tpl->smartphone) OR $_SESSION['mobile_enable'] ) {

	if ( @is_dir ( ROOT_DIR . '/templates/smartphone' ) ) {

		$config['skin'] = "smartphone";
		$smartphone_detected = true;
		
		if( $config['allow_comments_wysiwyg'] > 0 ) $config['allow_comments_wysiwyg'] = 0;

	}

}

$tpl->dir = ROOT_DIR . '/templates/' . totranslit($config['skin'], false, false);

define ( 'TEMPLATE_DIR', $tpl->dir );

if ( $config['allow_registration'] ) {

	include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/sitelogin.php'));
	
	if ( $_SESSION['twofactor_auth'] ){
		
		$twofactor_alert = <<<HTML
<div id="twofactor" title="{$lang['twofactor_title']}" style="display:none;" >{$lang['twofactor_alert']}
<br /><input type="text" name="dle-promt-text" id="dle-promt-text" style="width:100%;" class="ui-widget-content ui-corner-all" value="" />
<div id="twofactor_response" style="color:red"></div>
</div>
HTML;

		$onload_scripts[] = <<<HTML
$('#twofactor').dialog({
	autoOpen: true,
	show: 'fade',
	hide: 'fade',
	width: 450,
	resizable: false,
	dialogClass: "dle-popup-twofactor",
	buttons: {
		"{$lang['p_cancel']}" : function() { 
			$(this).dialog("close");						
		}, 
		"{$lang['p_enter']}": function() {
			if ( $("#dle-promt-text").val().length < 1) {
				 $("#dle-promt-text").addClass('ui-state-error');
			} else {
				var pin = $("#dle-promt-text").val();
				$.post(dle_root + "engine/ajax/controller.php?mod=twofactor", { pin: pin, skin: dle_skin }, function(data){
				
					if ( data.success ) {
					
						window.location = window.location.pathname + window.location.search;
						
					} else if (data.error) {
						
						$("#twofactor_response").html(data.errorinfo);
						$(".dle-popup-twofactor").css('max-height', '');
						$("#twofactor").css('height', 'auto');
						
					}
					
				}, "json");

			}		
		}
	}
});
HTML;

	} else {
		
		if ($is_logged) {
	
			set_cookie ( "dle_newpm", $member_id['pm_unread'], 365 );
			
			if( !isset($_COOKIE['dle_newpm']) ) $_COOKIE['dle_newpm'] = 0;
			
			if ( $member_id['pm_unread'] > intval ( $_COOKIE['dle_newpm'] ) AND !$smartphone_detected) {
	
				include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/pm_alert.php'));
	
			}
	
		}
		
	}

} else {

	$_IP = get_ip();
	$dle_login_hash = sha1( SECURE_AUTH_KEY . $_IP );
	
}

if (!$is_logged) $member_id['user_group'] = 5;

if ( isset( $banned_info['ip'] ) ) $blockip = check_ip ( $banned_info['ip'] );  else $blockip = false;

if ( ($is_logged AND $member_id['banned'] == "yes") OR $blockip) {
	
	include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/banned.php'));
	
}

if( $do == "preview" ) {
	
	include_once (DLEPlugins::Check(ENGINE_DIR . '/preview.php'));
	die();
	
} elseif($_GET['mod'] == "print") {
	
	include_once (DLEPlugins::Check(ENGINE_DIR . '/print.php'));
	die();
}


$tpl->load_template( 'login.tpl' );

$tpl->set( '{login-method}', $config['auth_metod'] ? "E-Mail:" : $lang['login_metod'] );
$tpl->set( '{registration-link}', $PHP_SELF . "?do=register" );
$tpl->set( '{lostpassword-link}', $PHP_SELF . "?do=lostpassword" );
$tpl->set( '{logout-link}', $PHP_SELF . "?action=logout" );
$tpl->set( '{pm-link}', $PHP_SELF . "?do=pm" );
$tpl->set( '{group}', $user_group[$member_id['user_group']]['group_prefix'].$user_group[$member_id['user_group']]['group_name'].$user_group[$member_id['user_group']]['group_suffix'] );

if ($is_logged) {

	$tpl->set( '{login}', $member_id['name'] );
	$tpl->set( '{new-pm}', $member_id['pm_unread'] );
	$tpl->set( '{all-pm}', $member_id['pm_all'] );

	if ($member_id['favorites']) {
	    $tpl->set( '{favorite-count}', count(explode("," ,$member_id['favorites'])) );
	} else $tpl->set( '{favorite-count}', '0' );

	if ( count(explode("@", $member_id['foto'])) == 2 ) {
		
		$tpl->set( '{foto}', 'https://www.gravatar.com/avatar/' . md5(trim($member_id['foto'])) . '?s=' . intval($user_group[$member_id['user_group']]['max_foto']) );
		
	} else {
		
		if( $member_id['foto'] ) {
			
			if (strpos($member_id['foto'], "//") === 0) $avatar = "http:".$member_id['foto']; else $avatar = $member_id['foto'];

			$avatar = @parse_url ( $avatar );

			if( $avatar['host'] ) {
				
				$tpl->set( '{foto}', $member_id['foto'] );
				
			} else $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $member_id['foto'] );
			
			unset($avatar);
			
		} else $tpl->set( '{foto}', "{THEME}/dleimages/noavatar.png" );
	}

} else {
	
	$member_id['name'] ='';
	$tpl->set( '{login}', '' );
	$tpl->set( '{new-pm}', '0' );
	$tpl->set( '{all-pm}', '0' );
	$tpl->set( '{favorite-count}', '0' );
	$tpl->set( '{foto}', "{THEME}/dleimages/noavatar.png" );

}

if($config['allow_social'] AND $config['allow_registration'] AND !$is_logged) {

	include_once (ENGINE_DIR . '/data/socialconfig.php');

	if( !$_SESSION['state'] ) $_SESSION['state'] = md5(uniqid(rand(), TRUE));

	if (strpos($config['http_home_url'], "//") === 0) $return_domain = "https:".$config['http_home_url'];
	elseif (strpos($config['http_home_url'], "/") === 0) $return_domain = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'];
	else  $return_domain = $config['http_home_url'];
	
	if ( $social_config['vk'] ) {

		$social_params = array(
			'client_id'     => $social_config['vkid'],
			'redirect_uri'  => $return_domain . "index.php?do=auth-social&provider=vk",
			'scope' => 'offline,email',
			'state' => $_SESSION['state'],
			'response_type' => 'code',
			'v'  => '5.90'
		);
		
		$vk_url = 'https://oauth.vk.com/authorize'.'?' . http_build_query($social_params, '', '&amp;');
		
		$tpl->set( '[vk]', "" );
		$tpl->set( '[/vk]', "" );
		$tpl->set( '{vk_url}', $vk_url );

	} else {

		$tpl->set_block( "'\\[vk\\](.*?)\\[/vk\\]'si", "" );
		$tpl->set( '{vk_url}', '' );
	}

	if ( $social_config['od'] ) {

		$social_params = array(
			'client_id'     => $social_config['odid'],
			'redirect_uri'  => $return_domain . "index.php?do=auth-social&provider=od",
			'scope' => 'VALUABLE_ACCESS;GET_EMAIL',
			'state' => $_SESSION['state'],
			'response_type' => 'code'
		);

		$odnoklassniki_url = 'https://connect.ok.ru/oauth/authorize'.'?' . http_build_query($social_params, '', '&amp;');
		
		$tpl->set( '[odnoklassniki]', "" );
		$tpl->set( '[/odnoklassniki]', "" );
		$tpl->set( '{odnoklassniki_url}', $odnoklassniki_url );

	} else {

		$tpl->set_block( "'\\[odnoklassniki\\](.*?)\\[/odnoklassniki\\]'si", "" );
		$tpl->set( '{odnoklassniki_url}', '' );
	}

	if ( $social_config['fc'] ) {

		$social_params = array(
			'client_id'     => $social_config['fcid'],
			'redirect_uri'  => $return_domain . "index.php?do=auth-social&provider=fc",
			'scope' => 'public_profile,email',
			'display' => 'popup',
			'state' => $_SESSION['state'],
			'response_type' => 'code'
		);

		$facebook_url = 'https://www.facebook.com/dialog/oauth'.'?' . http_build_query($social_params, '', '&amp;');
		$tpl->set( '[facebook]', "" );
		$tpl->set( '[/facebook]', "" );
		$tpl->set( '{facebook_url}', $facebook_url );

	} else {

		$tpl->set_block( "'\\[facebook\\](.*?)\\[/facebook\\]'si", "" );
		$tpl->set( '{facebook_url}', '' );
	}


	if ( $social_config['google'] ) {

		$social_params = array(
			'client_id'     => $social_config['googleid'],
			'redirect_uri'  => $return_domain . "index.php?do=auth-social&provider=google",
			'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
			'state' => $_SESSION['state'],
			'response_type' => 'code'
		);

		$google_url = 'https://accounts.google.com/o/oauth2/auth'.'?' . http_build_query($social_params, '', '&amp;');
		$tpl->set( '[google]', "" );
		$tpl->set( '[/google]', "" );
		$tpl->set( '{google_url}', $google_url );

	} else {

		$tpl->set_block( "'\\[google\\](.*?)\\[/google\\]'si", "" );
		$tpl->set( '{google_url}', '' );
	}

	if ( $social_config['mailru'] ) {

		$social_params = array(
			'client_id'     => $social_config['mailruid'],
			'redirect_uri'  => $return_domain . "index.php?do=auth-social&provider=mailru",
			'scope'         => 'userinfo',
			'state' => $_SESSION['state'],
			'response_type' => 'code'
		);

		$mailru_url = 'https://oauth.mail.ru/login'.'?' . http_build_query($social_params, '', '&amp;');
		$tpl->set( '[mailru]', "" );
		$tpl->set( '[/mailru]', "" );
		$tpl->set( '{mailru_url}', $mailru_url );

	} else {

		$tpl->set_block( "'\\[mailru\\](.*?)\\[/mailru\\]'si", "" );
		$tpl->set( '{mailru_url}', '' );
	}

	if ( $social_config['yandex'] ) {

		$social_params = array(
			'client_id'     => $social_config['yandexid'],
			'redirect_uri'  => $return_domain . "index.php?do=auth-social&provider=yandex",
			'state' => $_SESSION['state'],
			'response_type' => 'code'
		);

		$yandex_url = 'https://oauth.yandex.ru/authorize'.'?' . http_build_query($social_params, '', '&amp;');
		$tpl->set( '[yandex]', "" );
		$tpl->set( '[/yandex]', "" );
		$tpl->set( '{yandex_url}', $yandex_url );

	} else {

		$tpl->set_block( "'\\[yandex\\](.*?)\\[/yandex\\]'si", "" );
		$tpl->set( '{yandex_url}', '' );
	}

} else {

	$_SESSION['state'] = false;

	$tpl->set_block( "'\\[vk\\](.*?)\\[/vk\\]'si", "" );
	$tpl->set( '{vk_url}', '' );
	$tpl->set_block( "'\\[odnoklassniki\\](.*?)\\[/odnoklassniki\\]'si", "" );
	$tpl->set( '{odnoklassniki_url}', '' );
	$tpl->set_block( "'\\[facebook\\](.*?)\\[/facebook\\]'si", "" );
	$tpl->set( '{facebook_url}', '' );
	$tpl->set_block( "'\\[google\\](.*?)\\[/google\\]'si", "" );
	$tpl->set( '{google_url}', '' );
	$tpl->set_block( "'\\[mailru\\](.*?)\\[/mailru\\]'si", "" );
	$tpl->set( '{mailru_url}', '' );
	$tpl->set_block( "'\\[yandex\\](.*?)\\[/yandex\\]'si", "" );
	$tpl->set( '{yandex_url}', '' );
}

if( $user_group[$member_id['user_group']]['icon'] ) $tpl->set( '{group-icon}', "<img src=\"" . $user_group[$member_id['user_group']]['icon'] . "\" alt=\"\" />" );
else $tpl->set( '{group-icon}', "" );

if ( $user_group[$member_id['user_group']]['allow_admin'] ) {
	$tpl->set( '[admin-link]', "" );
	$tpl->set( '[/admin-link]', "" );
	$tpl->set( '{admin-link}', $config['http_home_url'] . $config['admin_path'] . "?mod=main" );
} else {
	$tpl->set( '{admin-link}', "" );
	$tpl->set_block( "'\\[admin-link\\](.*?)\\[/admin-link\\]'si", "" );
}

if ($config['allow_alt_url']) {
	
	$tpl->set( '{profile-link}', $config['http_home_url'] . "user/" . urlencode ( $member_id['name'] ) . "/" );
	$tpl->set( '{stats-link}', $config['http_home_url'] . "statistics.html" );
	$tpl->set( '{addnews-link}', $config['http_home_url'] . "addnews.html" );
	$tpl->set( '{favorites-link}', $config['http_home_url'] . "favorites/" );
	$tpl->set( '{newposts-link}', $config['http_home_url'] . "newposts/" );

} else {
	$tpl->set( '{profile-link}', $PHP_SELF . "?subaction=userinfo&user=" . urlencode ( $member_id['name'] ) );
	$tpl->set( '{stats-link}', $PHP_SELF . "?do=stats" );
	$tpl->set( '{addnews-link}', $PHP_SELF . "?do=addnews" );
	$tpl->set( '{favorites-link}', $PHP_SELF . "?do=favorites" );
	$tpl->set( '{newposts-link}', $PHP_SELF . "?subaction=newposts" );

}

if ($is_logged AND strpos( $tpl->copy_template, "[xfvalue_" ) !== false) {

	$xfields = xfieldsload( true );
	$xfieldsdata = xfieldsdataload( $member_id['xfields'] );

	foreach ( $xfields as $value ) {
		$preg_safe_name = preg_quote( $value[0], "'" );

		if( empty( $xfieldsdata[$value[0]] ) ) {

			$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
			$tpl->copy_template = str_replace( "[xfnotgiven_{$value[0]}]", "", $tpl->copy_template );
			$tpl->copy_template = str_replace( "[/xfnotgiven_{$value[0]}]", "", $tpl->copy_template );

		} else {
			$tpl->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
			$tpl->copy_template = str_replace( "[xfgiven_{$value[0]}]", "", $tpl->copy_template );
			$tpl->copy_template = str_replace( "[/xfgiven_{$value[0]}]", "", $tpl->copy_template );
		}

		$tpl->set( "[xfvalue_{$value[0]}]", stripslashes( $xfieldsdata[$value[0]] ));

	}

} else {

	$tpl->copy_template = preg_replace( "'\\[xfgiven_(.*?)\\](.*?)\\[/xfgiven_(.*?)\\]'is", "", $tpl->copy_template );
	$tpl->copy_template = preg_replace( "'\\[xfvalue_(.*?)\\]'i", "", $tpl->copy_template );
	$tpl->copy_template = preg_replace( "'\\[xfnotgiven_(.*?)\\](.*?)\\[/xfnotgiven_(.*?)\\]'is", "", $tpl->copy_template );

}

$tpl->compile( 'login_panel' );
$tpl->clear();

if ($config['site_offline']) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/offline.php'));

if ($config['allow_calendar'] OR $config['allow_archives']) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/calendar.php'));

if ($config['rss_informer']) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/rssinform.php'));

if ($config['allow_links']) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/links.php'));

include_once (DLEPlugins::Check(ROOT_DIR . '/engine/engine.php'));

if ($config['allow_topnews']) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/topnews.php'));

if ($config['allow_votes'] ) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/vote.php'));

if ( !defined('BANNERS') ) {
	if ($config['allow_banner']) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/banners.php'));
}

if ($config['allow_tags']) include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/tagscloud.php'));

include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/main.php'));

?>