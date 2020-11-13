<?PHP
/*
=====================================================
 ESPO - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004-2020 SoftNews Media Group
=====================================================
 This code is protected by copyright
=====================================================
 File: default.skin.php
-----------------------------------------------------
 Use: Adminpanel template
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$year = date('Y', time());

if ($is_loged_in) {

	$db->query( "SELECT id, text, user_from, date, foto FROM " . USERPREFIX . "_pm LEFT JOIN " . USERPREFIX . "_users ON " . USERPREFIX . "_pm.user_from=" . USERPREFIX . "_users.name WHERE " . USERPREFIX . "_pm.user= '{$member_id['user_id']}' AND folder = 'inbox' ORDER BY pm_read ASC, date DESC LIMIT 5" );

	$pm_list = array();

	while ( $row = $db->get_array() ) {
		
		$row['text'] = preg_replace ( "#\[hide(.*?)\]#i", "", $row['text'] );
		$row['text'] = str_ireplace( "[/hide]", "", $row['text']);
		$row['text'] = str_replace ("<br>", " ", $row['text']);
		$row['text'] = str_replace ("<br />", " ", $row['text']);
		$row['text'] = str_replace ("{", "&#123;", $row['text']);
		$row['text'] = htmlspecialchars(dle_substr(strip_tags (stripslashes($row['text']) ),0,100, $config['charset']), ENT_QUOTES, $config['charset'] )." ...";

		if( date( 'Ymd', $row['date'] ) == date( 'Ymd', time() ) ) {
				
			$date = date( "H:i", $row['date'] );
			
		} else {
				
			$date = langdate( "D H:i", $row['date'] );
			
		}

		if ( count(explode("@", $row['foto'])) == 2 ) {
		
			$pm_avatar = 'https://www.gravatar.com/avatar/' . md5(trim($row['foto'])) . '?s=' . intval($user_group[$row['user_group']]['max_foto']);	
		
		} else {
			
			if( $row['foto'] ) {
				
				if (strpos($row['foto'], "//") === 0) $pm_avatar = "http:".$row['foto']; else $pm_avatar = $row['foto'];
	
				$pm_avatar = @parse_url ( $pm_avatar );
	
				if( $pm_avatar['host'] ) {
					
					$pm_avatar = $row['foto'];
					
				} else $pm_avatar = $config['http_home_url'] . "uploads/fotos/" . $row['foto'];
			
			} else $pm_avatar = "engine/skins/images/noavatar.png";
		
		}

		$pm_list[] = <<<HTML
<li class="media">
	<div class="media-left">
		<img src="{$pm_avatar}" class="img-circle img-sm" alt="">
	</div>

	<div class="media-body">
	  <div class="media-title">
		<a href="{$config['http_home_url']}?do=pm&amp;doaction=readpm&amp;pmid={$row['id']}" target="_blank" class="media-heading">
			<span class="text-semibold">{$row['user_from']}</span>
			<span class="media-annotation pull-right">{$date}</span>
		</a>
	  </div>
		<span class="text-muted">{$row['text']}</span>
	</div>
</li>
HTML;

	}
	
	if( count($pm_list) ) {
		$pm_list = "<ul class=\"media-list dropdown-content-body\">".implode("", $pm_list)."</ul>";
	} else {
		$pm_list = "<div class=\"panel-body\">{$lang['no_message']}</div>";
	}
	
	if ( count(explode("@", $member_id['foto'])) == 2 ) {
		
		$avatar = 'https://www.gravatar.com/avatar/' . md5(trim($member_id['foto'])) . '?s=' . intval($user_group[$member_id['user_group']]['max_foto']);
		
	} else {
		
		if( $member_id['foto'] ) {
			
			if (strpos($member_id['foto'], "//") === 0) $avatar = "http:".$member_id['foto']; else $avatar = $member_id['foto'];

			$avatar = @parse_url ( $avatar );

			if( $avatar['host'] ) {
				
				$avatar = $member_id['foto'];
				
			} else $avatar = $config['http_home_url'] . "uploads/fotos/" . $member_id['foto'];

		} else $avatar = "engine/skins/images/noavatar.png";
	}
	if ( $member_id['pm_unread'] ) {
		$pop_notice = "<span class=\"badge bg-warning-800\">{$member_id['pm_unread']}</span>";
	} else $pop_notice = "";

	$message_notice = "<span class=\"badge bg-teal pull-right\">{$member_id['pm_all']}</span>";
	
	$profile_link = $config['http_home_url'] . "user/" . urlencode ( $member_id['name'] ) . "/";
	
	$options = array ();
	
	$options['config'] = array (
								
								array (
											'name' => $lang['opt_all'], 
											'url' => "?mod=options&action=syscon", 
											'mod' => "options", 
											'access' => "admin" 
								), 
								
								array (
											'name' => $lang['opt_cat'], 
											'url' => "?mod=categories", 
											'mod' => "categories", 
											'access' => $user_group[$member_id['user_group']]['admin_categories'] 
								), 
								
								array (
											'name' => $lang['opt_db'], 
											'url' => "?mod=dboption", 
											'mod' => "dboption", 
											'access' => "admin" 
								), 

								array (
											'name' => $lang['opt_vconf'], 
											'url' => "?mod=videoconfig", 
											'mod' => "videoconfig", 
											'access' => "admin" 
								),
								
								array (
											'name' => $lang['opt_xfil'], 
											'url' => "?mod=xfields&xfieldsaction=configure", 
											'mod' => "xfields", 
											'access' => $user_group[$member_id['user_group']]['admin_xfields'] 
								),

								array (
											'name' => $lang['opt_question'], 
											'url' => "?mod=question", 
											'mod' => "question", 
											'access' => "admin" 
								)
	);
	
	$options['user'] = array (
							
							array (
										'name' => $lang['opt_user'], 
										'url' => "?mod=editusers&action=list", 
										'mod' => "editusers", 
										'access' => $user_group[$member_id['user_group']]['admin_editusers'] 
							), 
							
							array (
										'name' => $lang['opt_xprof'], 
										'url' => "?mod=userfields&xfieldsaction=configure", 
										'mod' => "userfields", 
										'access' => $user_group[$member_id['user_group']]['admin_userfields'] 
							), 
							
							array (
										'name' => $lang['opt_group'], 
										'url' => "?mod=usergroup", 
										'mod' => "usergroup", 
										'access' => "admin" 
							),
							array (
										'name' => $lang['opt_social'], 
										'url' => "?mod=social", 
										'mod' => "social", 
										'access' => "admin"
							),
							array (
										'name' => $lang['opt_ipban'], 
										'url' => "?mod=blockip", 
										'mod' => "blockip", 
										'access' => $user_group[$member_id['user_group']]['admin_blockip'] 
							)
	);
	
	$options['templates'] = array (
									
									array (
											'name' => $lang['opt_t'], 
											'url' => "?mod=templates&user_hash=" . $dle_login_hash, 
											'mod' => "templates", 
											'access' => "admin" 
									), 
									
									array (
											'name' => $lang['opt_email'], 
											'url' => "?mod=email", 
											'mod' => "email", 
											'access' => "admin" 
									) 
	);

	
	
	$options['filter'] = array (
								array (
											'name' => $lang['opt_plugins'], 
											'url' => "?mod=plugins", 
											'mod' => "plugins", 
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_srebuild'], 
											'url' => "?mod=rebuild", 
											'mod' => "rebuild", 
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_fil'], 
											'url' => "?mod=wordfilter", 
											'mod' => "wordfilter", 
											'access' => $user_group[$member_id['user_group']]['admin_wordfilter'] 
								), 
								array (
											'name' => $lang['opt_iptools'], 
											'url' => "?mod=iptools", 
											'mod' => "iptools", 
											'access' => $user_group[$member_id['user_group']]['admin_iptools'] 
								), 
								array (
											'name' => $lang['opt_sfind'], 
											'url' => "?mod=search", 
											'mod' => "search", 
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_complaint'], 
											'url' => "?mod=complaint", 
											'mod' => "complaint",  
											'access' => $user_group[$member_id['user_group']]['admin_complaint'] 
								),
								array (
											'name' => $lang['opt_check'], 
											'url' => "?mod=check", 
											'mod' => "check",
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_links'], 
											'url' => "?mod=links", 
											'mod' => "links",
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_redirects'], 
											'url' => "?mod=redirects", 
											'mod' => "redirects",
											'access' => "admin" 
								),
								array (
											'name' => $lang['opt_metatags'], 
											'url' => "?mod=metatags", 
											'mod' => "metatags",
											'access' => "admin" 
								)
	);

	
	
	$options['others'] = array (
								array (
											'name' => $lang['opt_rules'], 
											'url' => "?mod=static&action=doedit&page=rules", 
											'mod' => "rules",
											'access' => $user_group[$member_id['user_group']]['admin_static'] 
								), 
								
								array (
											'name' => $lang['opt_static'], 
											'url' => "?mod=static", 
											'mod' => "static",
											'access' => $user_group[$member_id['user_group']]['admin_static'] 
								), 
								
								array (
											'name' => $lang['opt_clean'], 
											'url' => "?mod=clean", 
											'mod' => "clean",
											'access' => "admin" 
								), 								
								
								array (
											'name' => $lang['main_newsl'], 
											'url' => "?mod=newsletter", 
											'mod' => "newsletter",
											'access' => $user_group[$member_id['user_group']]['admin_newsletter'] 
								), 
								array (
											'name' => $lang['opt_vote'], 
											'url' => "?mod=editvote", 
											'mod' => "editvote",
											'access' => $user_group[$member_id['user_group']]['admin_editvote'] 
								), 
								
								array (
											'name' => $lang['opt_img'], 
											'url' => "?mod=files", 
											'mod' => "files",
											'access' => "admin" 
								), 
								
								array (
											'name' => $lang['opt_banner'], 
											'url' => "?mod=banners&action=list", 
											'mod' => "banners",
											'access' => $user_group[$member_id['user_group']]['admin_banners'] 
								), 
								array (
											'name' => $lang['opt_google'], 
											'url' => "?mod=googlemap", 
											'mod' => "googlemap",
											'access' => $user_group[$member_id['user_group']]['admin_googlemap'] 
								),
								array (
											'name' => $lang['opt_rss'], 
											'url' => "?mod=rss", 
											'mod' => "rss",
											'access' => $user_group[$member_id['user_group']]['admin_rss'] 
								), 
								array (
											'name' => $lang['opt_rssinform'], 
											'url' => "?mod=rssinform", 
											'mod' => "rssinform",
											'access' => $user_group[$member_id['user_group']]['admin_rssinform'] 
								),
								array (
											'name' => $lang['opt_tagscloud'], 
											'url' => "?mod=tagscloud", 
											'mod' => "tagscloud",
											'access' => $user_group[$member_id['user_group']]['admin_tagscloud'] 
								),

								array (
											'name' => $lang['opt_logs'], 
											'url' => "?mod=logs", 
											'mod' => "logs",
											'access' => "admin" 
								),
	);


	$db->query( "SELECT * FROM " . PREFIX . "_admin_sections" );

	while ( $row = $db->get_array() ) {

		if ($row['allow_groups'] != "all") {

			$groups = explode(",", $row['allow_groups']);

			if ( !in_array($member_id['user_group'], $groups) AND $member_id['user_group'] !=1 ) continue;

		}

		$row['name'] = totranslit($row['name'], true, false);
		$row['title'] = strip_tags(stripslashes($row['title']));

		$options['admin_sections'][] = array (
											'name' => $row['title'], 
											'url' => "?mod={$row['name']}", 
											'mod' => "{$row['name']}",
											'access' => 1
										);

	}


	foreach ( $options as $sub_options => $value ) {
		$count_options = count( $value );
		
		for($i = 0; $i < $count_options; $i ++) {

			if ($member_id['user_group'] == 1 ) continue;

			if ($member_id['user_group'] != 1 AND  $value[$i]['access'] == "admin") unset( $options[$sub_options][$i] );

			if ( !$value[$i]['access'] ) unset( $options[$sub_options][$i] );
		}
	}
	
	$subs = 0;
	$sidebar= "";
	$menu_item = array();
	
	$menu_item[] = "<li><a href=\"?mod=options&action=options\"><i class=\"fa fa-globe\"></i> <span>{$lang['header_all']}</span></a></li>";

	foreach ( $options as $sub_options ) {
	
		$menu_item_header = $lang['opt_hopt'];
		$icon= "wrench";
		if( $subs == 1 ) { $menu_item_header = $lang['opt_s_acc']; $icon= "user";}
		if( $subs == 2 ) { $menu_item_header = $lang['opt_s_tem']; $icon= "laptop";}
		if( $subs == 3 ) { $menu_item_header = $lang['opt_s_fil']; $icon= "leaf";}
		if( $subs == 4 ) { $menu_item_header = $lang['opt_s_oth']; $icon= "link";}
		if( $subs == 5 ) { $menu_item_header = $lang['admin_other_section']; $icon= "list-alt";}
		
		$subs ++;
		
		if( !count( $sub_options ) ) continue;
		
		$submenu_item = array();

		foreach ( $sub_options as $option ) {
		
			if ($mod == $option['mod']) {
				$active_submenu = " class=\"active\"";
			} else $active_submenu = "";
		
			if ($mod == "options" AND $action != "syscon") {
				$active_submenu = "";
			}
			if ($mod == "static" AND $_GET['page'] == "rules") {
				$active_submenu = "";
			}			
			$submenu_item[] = "<li{$active_submenu}><a href=\"{$option['url']}\">{$option['name']}</a></li>";
		}

		$submenu_item = implode("", $submenu_item);
	
		$menu_item[] = "<li><a href=\"#\" class=\"has-ul\"><i class=\"fa fa-{$icon}\"></i> <span>{$menu_item_header}</span></a><ul class=\"hidden-ul\">{$submenu_item}</ul></li>";	
	}
	
	if( count( $menu_item ) ) $sidebar= implode("", $menu_item);
	else $sidebar= "";
	
} else $sidebar= "";

if( @file_exists( ROOT_DIR . '/templates/'. $config['skin'].'/adminpanel.css' ) ) {
	
		$custom_css = "<link href=\"templates/{$config['skin']}/adminpanel.css\" rel=\"stylesheet\" type=\"text/css\" />";
		
} else $custom_css = "";


if( $member_id['user_group'] == 1 ) {
	$settings_bar = "<li><a href=\"?mod=videoconfig\"><i class=\"fa fa-play\"></i>{$lang['settings_video']}</a></li><li class=\"divider\"></li><li><a href=\"?mod=options&action=syscon\"><i class=\"fa fa-cogs\"></i>{$lang['opt_hopt']}</a></li>";
} else {
	$settings_bar = "";
}

$body_class = "";
$sidebardefault = "sidebar-default";

if( file_exists( ENGINE_DIR . "/cache/system/adminpanel/" . md5(SECURE_AUTH_KEY.$member_id['user_id']) ) ) {
	$body_class = trim(@file_get_contents( ENGINE_DIR . "/cache/system/adminpanel/" . md5(SECURE_AUTH_KEY.$member_id['user_id']) ));
}

if($body_class) {
	
	$dark_theme = strpos ( $body_class, "dle_theme_dark" );
	$auto_dark = strpos ( $body_class, "auto_dark_theme" );

	if( $auto_dark !== false AND $dark_theme === false ) {

		$night_time = array (
							 '01' => array('from' => '16:20',
										   'to'   => '08:40'),
							 '02' => array('from' => '17:30',
										   'to'   => '07:50'),
							 '03' => array('from' => '18:30',
										   'to'   => '06:40'),
							 '04' => array('from' => '19:30',
										   'to'   => '05:30'),
							 '05' => array('from' => '20:30',
										   'to'   => '04:20'),
							 '06' => array('from' => '21:20',
										   'to'   => '03:50'),
							 '07' => array('from' => '21:00',
										   'to'   => '04:00'),
							 '08' => array('from' => '20:00',
										   'to'   => '05:00'),
							 '09' => array('from' => '18:50',
										   'to'   => '06:00'),
							 '10' => array('from' => '17:30',
										   'to'   => '07:00'),
							 '11' => array('from' => '16:20',
										   'to'   => '08:00'),
							 '12' => array('from' => '16:00',
										   'to'   => '08:50')
							 );

		$timezones = array('Pacific/Midway','US/Samoa','US/Hawaii','US/Alaska','US/Pacific','America/Tijuana','US/Arizona','US/Mountain','America/Chihuahua','America/Mazatlan','America/Mexico_City','America/Monterrey','US/Central','US/Eastern','US/East-Indiana','America/Lima','America/Caracas','Canada/Atlantic','America/La_Paz','America/Santiago','Canada/Newfoundland','America/Buenos_Aires','America/Godthab','Atlantic/Stanley','Atlantic/Azores','Africa/Casablanca','Europe/Dublin','Europe/Lisbon','Europe/London','Europe/Amsterdam','Europe/Belgrade','Europe/Berlin','Europe/Bratislava','Europe/Brussels','Europe/Budapest','Europe/Copenhagen','Europe/Madrid','Europe/Paris','Europe/Prague','Europe/Rome','Europe/Sarajevo','Europe/Stockholm','Europe/Vienna','Europe/Warsaw','Europe/Zagreb','Europe/Athens','Europe/Bucharest','Europe/Helsinki','Europe/Istanbul','Asia/Jerusalem','Europe/Kiev','Europe/Minsk','Europe/Riga','Europe/Sofia','Europe/Tallinn','Europe/Vilnius','Asia/Baghdad','Asia/Kuwait','Africa/Nairobi','Asia/Tehran','Europe/Kaliningrad','Europe/Moscow','Europe/Volgograd','Europe/Samara','Asia/Baku','Asia/Muscat','Asia/Tbilisi','Asia/Yerevan','Asia/Kabul','Asia/Yekaterinburg','Asia/Tashkent','Asia/Kolkata','Asia/Kathmandu','Asia/Almaty','Asia/Novosibirsk','Asia/Jakarta','Asia/Krasnoyarsk','Asia/Hong_Kong','Asia/Kuala_Lumpur','Asia/Singapore','Asia/Taipei','Asia/Ulaanbaatar','Asia/Urumqi','Asia/Irkutsk','Asia/Seoul','Asia/Tokyo','Australia/Adelaide','Australia/Darwin','Asia/Yakutsk','Australia/Brisbane','Pacific/Port_Moresby','Australia/Sydney','Asia/Vladivostok','Asia/Sakhalin','Asia/Magadan','Pacific/Auckland','Pacific/Fiji');

		$detect_time = new DateTime('@'.time() );
		
		if (isset($member_id['timezone']) AND $member_id['timezone'] ) {
			$timezone = $member_id['timezone'];
		} else {
			$timezone = date_default_timezone_get();
		}
		
		if (!in_array($timezone, $timezones)) $timezone = 'Europe/Moscow';
		
		$detect_time->setTimeZone(new DateTimeZone($timezone));
	
		$c_m = $detect_time->format('m');
		$c_t = $detect_time->format('H:i');
		
		if(is_array($night_time[$c_m])) {
			
			if( $c_t >= $night_time[$c_m]['from'] OR $c_t <= $night_time[$c_m]['to'] ) {
				$body_class = str_replace ('dle_theme_', 'saved_dle_theme_', $body_class);
				$body_class .= " dle_theme_dark auto_night_mode";
				$dark_theme = true;
			}
			
		}
	
	}
	
	if ( $dark_theme !== false ) {
		$sidebardefault = "";
	}
	
	$body_class = " class=\"".htmlspecialchars ( trim($body_class), ENT_QUOTES, $config['charset'] )."\"";
}

$skin_header = <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>ESPO - {$lang['skin_title']}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="HandheldFriendly" content="true">
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width"> 
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">

	{css_files}
	{$custom_css}
	{js_files}
</head>
<body{$body_class}>
<script>
<!--
var dle_act_lang   = ["{$lang['p_yes']}", "{$lang['p_no']}", "{$lang['p_enter']}", "{$lang['p_cancel']}", "{$lang['media_upload']}"];
var cal_language   = {en:{months:['{$lang['January']}','{$lang['February']}','{$lang['March']}','{$lang['April']}','{$lang['May']}','{$lang['June']}','{$lang['July']}','{$lang['August']}','{$lang['September']}','{$lang['October']}','{$lang['November']}','{$lang['December']}'],dayOfWeek:["{$langdate['Sun']}", "{$langdate['Mon']}", "{$langdate['Tue']}", "{$langdate['Wed']}", "{$langdate['Thu']}", "{$langdate['Fri']}", "{$langdate['Sat']}"]}};
var filedefaulttext= '{$lang['file_def_1']}';
var filebtntext    = '{$lang['file_def_2']}';
var dle_login_hash = '{$dle_login_hash}';
//-->
</script>
<div id="loading-layer" class="shadow-depth3"><i class="fa fa-spinner fa-spin"></i></div>
	<div class="navbar navbar-inverse bg-primary-700">
		<div class="navbar-header">
			<a class="navbar-brand" href="?mod=main">ESPO</a>
			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="fa fa-angle-double-down"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="fa fa-bars"></i></a></li>
			</ul>
		</div>
		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="fa fa-bars"></i></a></li>
			</ul>
			<div class="navbar-right">	
				<ul class="nav navbar-nav">
					<li><a href="{$config['http_home_url']}" title="{$lang['skin_view']}" target="_blank"><i class="fa fa-globe"></i><span class="visible-xs-inline-block position-right">{$lang['skin_view']}</span></a>
					<li><a class="sidebar-control hidden-xs" id="full_screen_toggle" title="{$lang['fullscreen_toggle']}"><i class="fa fa-expand"></i></a></li>
					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" title="{$lang['opt_pm_h']}"><i class="fa fa-envelope"></i><span class="visible-xs-inline-block position-right">{$lang['opt_pm_h']}</span>{$pop_notice}</a>
						<div class="dropdown-menu dropdown-content" style="min-width:350px;">
							<div class="dropdown-content-heading">
								{$lang['opt_pm_h']}
								<ul class="icons-list">
									<li><a href="{$config['http_home_url']}index.php?do=pm" target="_blank"><i class="fa fa-envelope-o"></i></a></li>
								</ul>
							</div>
							{$pm_list}
							<div class="dropdown-content-footer">
								<a href="{$config['http_home_url']}index.php?do=pm" data-popup="tooltip" title="{$lang['all_messages']}" target="_blank"><i class="fa fa-ellipsis-h"></i></a>
							</div>
						</div>
					</li>					
				</ul>
			</div>
		</div>
	</div>
	<div class="page-container">
		<div class="page-content">
			<div class="sidebar sidebar-main {$sidebardefault}">
				<div class="sidebar-content">
					<div class="sidebar-user-material">
						<div class="category-content">
							<div class="sidebar-user-material-content">
								<img src="{$avatar}" class="img-circle img-responsive" alt="">
								<h6>{$member_id['name']}</h6>
								<span class="text-size-small">{$user_group[$member_id['user_group']]['group_name']}</span>
							</div>
							<div class="sidebar-user-material-menu">
								<a href="#user-nav" data-toggle="collapse"><span>{$lang['account']}</span> <i class="caret"></i></a>
							</div>
						</div>
						<div class="navigation-wrapper collapse" id="user-nav">
							<ul class="navigation">
								<li><a href="{$profile_link}" target="_blank"><i class="fa fa-user"></i> <span>{$lang['header_profile']}</span></a></li>
								<li><a href="{$config['http_home_url']}index.php?do=pm" target="_blank"><i class="fa fa-envelope"></i> <span>{$message_notice} {$lang['header_messages']}</span></a></li>
								<li class="divider"></li>
								<li><a href="?mod=editusers&action=edituser&id={$member_id['user_id']}"><i class="fa fa-cog"></i> <span>{$lang['header_settings']}</span></a></li>
								<li><a href="?action=logout"><i class="fa fa-power-off text-danger-800"></i> <span>{$lang['skin_logout']}</span></a></li>
							</ul>
						</div>
					</div>
					<div class="sidebar-category sidebar-category-visible">
						<div class="category-content no-padding">
							<ul class="navigation navigation-main navigation-accordion">
								<li class="navigation-header"><span>{$lang['modules_list']}</span> <i class="fa fa-ellipsis-h" title="{$lang['modules_list']}"></i></li>
								{$sidebar}
							</ul>
						</div>
					</div>

				</div>
			</div>
			<div class="content-wrapper">
				<div class="page-header page-header-default">
					<div class="page-header-content">
						<div class="page-title">
							<h4>{$header_title}</h4>
						</div>
						<div class="heading-elements">
							<div class="heading-btn-group">
								<a href="?mod=addnews&amp;action=addnews" class="btn btn-link btn-float text-size-small has-text"><i class="fa fa-file-text-o text-primary"></i> <span>{$lang['add_news']}</span></a>
								<a href="?mod=editnews&amp;action=list" class="btn btn-link btn-float text-size-small has-text"><i class="fa fa-edit text-primary"></i> <span>{$lang['edit_news']}</span></a>
							</div>
						</div>
					</div>
					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							{$breadcrumb}
						</ul>
						<ul class="breadcrumb-elements">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog position-left"></i>{$lang['settings']}<span class="caret"></span></a>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a id="style_switcher_toggle"><i class="fa fa-paint-brush"></i>{$lang['settings_panel']}</a></li>
									<li><a href="?mod=editusers&action=edituser&id={$member_id['user_id']}"><i class="fa fa-user"></i>{$lang['header_settings']}</a></li>
									{$settings_bar}
								</ul>
							</li>
						</ul>
					</div>
				</div>
				<div class="content">
	<!-- maincontent beginn -->
HTML;

$skin_footer = <<<HTML
					<div class="footer text-muted text-size-small">
						ESPO&reg;  Copyright 2020-{$year} &copy; <a href="https://digitalacademy.co.ua/" target="_blank">Digital Academy</a> All rights reserved.
					</div>
				</div>
			</div>
		</div>
	</div>
    <div id="style_switcher" title="{$lang['settings_panel_1']}" style="display: none;">
        <div>
            <h5>{$lang['settings_panel_9']}</h5>
            <ul class="switcher_app_themes" id="theme_switcher">
                <li class="app_style_default" data-app-theme="">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_a" data-app-theme="dle_theme_a">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_b" data-app-theme="dle_theme_b">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_c" data-app-theme="dle_theme_c">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_d" data-app-theme="dle_theme_d">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_e" data-app-theme="dle_theme_e">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_f" data-app-theme="dle_theme_f">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_g" data-app-theme="dle_theme_g">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_h" data-app-theme="dle_theme_h">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_i" data-app-theme="dle_theme_i">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_dark" data-app-theme="dle_theme_dark">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
            </ul>
			<div class="checkbox">
				<label><input type="checkbox" name="auto_dark_theme" id="auto_dark_theme" class="icheck">{$lang['settings_panel_10']}</label>
			</div>
        </div>
        <div>
            <h5>{$lang['settings_panel_2']}</h5>
			<div class="checkbox">
				<label><input type="checkbox" name="style_sidebar_mini" id="style_sidebar_mini" class="icheck">{$lang['settings_panel_3']}</label>
			</div>
        </div>
        <div>
            <h5>{$lang['settings_panel_4']}</h5>
			<div class="checkbox">
				<label><input type="checkbox" name="style_layout_boxed" id="style_layout_boxed" class="icheck">{$lang['settings_panel_5']}</label>
			</div>
        </div>
        <div>
            <h5>{$lang['settings_panel_6']}</h5>
			<label class="radio-inline"><input class="icheck" type="radio" name="style_input" value="0">{$lang['settings_panel_7']}</label>
			<label class="radio-inline"><input class="icheck" type="radio" name="style_input" value="1">{$lang['settings_panel_8']}</label>
        </div>
    </div>
</body>
</html>
HTML;

$skin_login = <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>ESPO - {$lang['skin_title']}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="HandheldFriendly" content="true">
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width"> 
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<meta name="robots" content="noindex, nofollow">
	{css_files}
    {js_files}
</head>
<body class="no-theme">
<script>
<!--
var dle_act_lang   = [];
var cal_language   = {en:{months:[],dayOfWeek:[]}};
var filedefaulttext= '';
var filebtntext    = '';
//-->
</script>

<div class="container">
  <div class="col-md-4 col-md-offset-4">
    <div class="page-container">
<!--MAIN area-->


	<div class="panel panel-default" style="margin-top: 100px;">

      <div class="panel-heading">
        {$lang['skin_title']} espo
      </div>
	  
      <div class="panel-body">
        <form  name="login" action="" method="post" class="separate-sections"><input type="hidden" name="subaction" value="dologin">
			{result}
			<div class="form-group has-feedback has-feedback-left">
				<input class="form-control" type="text" name="username" placeholder="{mauth}">
				<div class="form-control-feedback">
					<i class="fa fa-{mauth2} text-muted"></i>
				</div>
			</div>
			<div class="form-group has-feedback has-feedback-left">
				<input class="form-control" type="password" name="password" placeholder="{$lang['login_box_3']}">
				<div class="form-control-feedback">
					<i class="fa fa-lock text-muted"></i>
				</div>
			</div>
			<div class="form-group">
				{select}
			</div>
			<div class="form-group">
				<div class="checkbox">
					<label><input type="checkbox" class="icheck" name="login_not_save" id="login_not_save" value="1">{$lang['m_not_save']}</label>
				</div>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-raised btn-block">{$lang['login_button']} <i class="fa fa-sign-in"></i></button>
			</div>

        </form>
		<div class="text-right">
			<a href="?mod=lostpassword" class="status-info text-right">{$lang['lost_pass']}</a>
		</div>
      </div>

    </div>
	<div class="text-muted text-size-small text-center">ESPO&reg;  Copyright 2020-{$year}<br>&copy; All rights reserved.</div>



	 <!--MAIN area-->
  </div>
</div>
</div>

</body>
</html>
HTML;


$skin_not_logged_header = <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>ESPO - {$lang['skin_title']}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="HandheldFriendly" content="true">
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width"> 
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<meta name="robots" content="noindex, nofollow">
	{css_files}
    {js_files}
</head>
<body class="no-theme">
<script>
<!--
var dle_act_lang   = [];
var cal_language   = {en:{months:[],dayOfWeek:[]}};
var filedefaulttext= '';
var filebtntext    = '';
//-->
</script>

<div class="container">
  <div class="col-md-8 col-md-offset-2">
    <div class="page-container mt-20">
<!--MAIN area-->
HTML;


$skin_not_logged_footer = <<<HTML
	 <!--MAIN area-->
  </div>
</div>
</div>

</body>
</html>
HTML;

?>