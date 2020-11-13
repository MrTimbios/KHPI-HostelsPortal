<?php
/*
=====================================================
DataLife Engine - by SoftNews Media Group
-----------------------------------------------------
http://dle-news.ru/
-----------------------------------------------------
Copyright (c) 2004-2020 SoftNews Media Group
=====================================================
File: banners.php
-----------------------------------------------------
Use: banners
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$user_group[$member_id['user_group']]['admin_banners'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if( isset( $_REQUEST['id'] ) ) $id = intval( $_REQUEST['id'] ); else $id = "";

if( isset( $_REQUEST['rubric'] ) ) $rubric = intval( $_REQUEST['rubric'] ); else $rubric = 0;

if ($_REQUEST['searchword']) {
  
  $searchword = htmlspecialchars( strip_tags( stripslashes( trim( urldecode ( $_REQUEST['searchword'] ) ) ) ), ENT_QUOTES, $config['charset'] );
  
} else $searchword = "";
	
if ($searchword) $urlsearch = "&searchword={$searchword}"; else $urlsearch = "";
	
$rubrics = array ();
	
$db->query( "SELECT * FROM " . PREFIX . "_banners_rubrics ORDER BY id ASC" );

while ( $row = $db->get_row() ) {

	$rubrics[$row['id']] = array ();

	foreach ( $row as $key => $value ) {
		$rubrics[$row['id']][$key] = stripslashes( $value );
	}
	
}

function get_bread_crumbs($id) {
	global $rubrics, $lang;
	
	if( !$id ) return;
	
	$parent_id = $rubrics[$id]['parentid'];
	
	$list = array();
	
	while ( $parent_id ) {
		
		$list=array('?mod=banners&rubric='.$parent_id => $rubrics[$parent_id]['title']) + $list;
		
		$parent_id = $rubrics[$parent_id]['parentid'];

		if($parent_id) {		
			if( $rubrics[$parent_id]['parentid'] == $rubrics[$parent_id]['id'] ) break;
		}

	}
	
	$list=array('?mod=banners' => $lang['header_banner']) + $list;
	
	$list[''] = $rubrics[$id]['title'];
	
	return $list;
}

function makeDropDown($options, $name, $selected) {
	$output = "<select class=\"uniform\" name=\"{$name}\">\r\n";
	foreach ( $options as $value => $description ) {
		$output .= "<option value=\"$value\"";
		if( $selected == $value ) {
			$output .= " selected ";
		}
		$output .= ">{$description}</option>\n";
	}
	$output .= "</select>";
	return $output;
}

function get_devicelevel($id = false) {
	global $lang;
	
	$returnstring = "";
	$list = array( $lang['device_desktop'] => 1,  $lang['device_pads'] => 2,  $lang['device_mobile'] => 3  );
	
	foreach ( $list as $key => $value ) {
		$returnstring .= '<option value="' . $value . '" ';
		
		if( is_array( $id ) ) {
			foreach ( $id as $element ) {
				if( $element == $value ) $returnstring .= 'selected';
			}
		} elseif( $id AND $id == $value ) $returnstring .= 'selected';
		
		$returnstring .= ">" . $key . "</option>\n";
	}
	
	return $returnstring;

}

if( $_POST['action'] == "doadd" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=banners") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	$banner_tag = totranslit( strip_tags( trim( $_POST['banner_tag'] ) ) );
	$banner_descr = $db->safesql( strip_tags( trim( $_POST['banner_descr'] ) ) );
	$banner_code = $db->safesql( trim( $_POST['banner_code'] ) );
	$approve = intval( $_REQUEST['approve'] );
	$short_place = intval( $_REQUEST['short_place'] );
	$bstick = intval( $_REQUEST['bstick'] );
	$main = intval( $_REQUEST['main'] );
	$fpage = intval( $_REQUEST['fpage'] );
	$innews = intval( $_REQUEST['innews'] );
	$max_views = intval( $_REQUEST['max_views'] );
	$max_counts = intval( $_REQUEST['max_counts'] );
	$allow_views = intval( $_REQUEST['allow_views'] );
	$allow_counts = intval( $_REQUEST['allow_counts'] );
	$rub_id = intval( $_REQUEST['rub_id'] );
	
	$category = $_POST['category'];

	if( !count( $category ) ) {
		$category = array ();
		$category[] = '0';
	}

	$category_list = array();

	foreach ( $category as $value ) {
		$category_list[] = intval($value);
	}

	$category = $db->safesql( implode( ',', $category_list ) );

	$grouplevel = $_POST['grouplevel'];
	
	if( !count( $grouplevel ) ) {
		$grouplevel = array ();
		$grouplevel[] = 'all';
	}

	$g_list = array();

	foreach ( $grouplevel as $value ) {
		if ($value == "all") $g_list[] = $value; else $g_list[] = intval($value);
	}

	$grouplevel = $db->safesql( implode( ',', $g_list ) );
	
	$devicelevel = $_POST['devicelevel'];
	
	if( !count( $devicelevel ) ) {
		$devicelevel = array ();
		$devicelevel[] = 'all';
	}

	$d_list = array();

	foreach ( $devicelevel as $value ) {
		if ($value == "all") $d_list[] = $value; else $d_list[] = intval($value);
	}

	$devicelevel = $db->safesql( implode( ',', $d_list ) );
	
	if ( trim($_POST['start_date']) ) {

		$start_date = @strtotime( $_POST['start_date'] );

		if ($start_date === - 1 OR !$start_date) $start_date = "";

	} else $start_date = "";

	if ( trim($_POST['end_date']) ) {

		$end_date = @strtotime( $_POST['end_date'] );

		if ($end_date === - 1 OR !$end_date) $end_date = "";

	} else $end_date = "";
	
	if( $banner_tag == "" or $banner_descr == "" ) msg( "error", $lang['addnews_error'], $lang['addnews_erstory'], "javascript:history.go(-1)" );
	
	$db->query( "INSERT INTO " . PREFIX . "_banners (banner_tag, descr, code, approve, short_place, bstick, main, category, grouplevel, start, end, fpage, innews, devicelevel, allow_views, max_views, allow_counts, max_counts, rubric) values ('$banner_tag', '$banner_descr', '$banner_code', '$approve', '$short_place', '$bstick', '$main', '$category', '$grouplevel', '$start_date', '$end_date', '$fpage', '$innews', '$devicelevel', '$allow_views', '$max_views', '$allow_counts', '$max_counts', '$rub_id')" );
	@unlink( ENGINE_DIR . '/cache/system/banners.php' );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '4', '{$banner_tag}')" );

	clear_cache();
	header('X-XSS-Protection: 0;');
	header( "Location: ?mod=banners{$urlsearch}&rubric=".$rub_id );
	die();

}

if( $_POST['action'] == "doedit" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=banners") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	if (!$id) msg( "error", "ID not valid", "ID not valid" );
	
	$banner_tag = totranslit( strip_tags( trim( $_POST['banner_tag'] ) ) );
	$banner_descr = $db->safesql( strip_tags( trim( $_POST['banner_descr'] ) ) );
	$banner_code = $db->safesql( trim( $_POST['banner_code'] ) );
	$approve = intval( $_REQUEST['approve'] );
	$short_place = intval( $_REQUEST['short_place'] );
	$bstick = intval( $_REQUEST['bstick'] );
	$main = intval( $_REQUEST['main'] );
	$fpage = intval( $_REQUEST['fpage'] );
	$innews = intval( $_REQUEST['innews'] );
	$max_views = intval( $_REQUEST['max_views'] );
	$max_counts = intval( $_REQUEST['max_counts'] );
	$allow_views = intval( $_REQUEST['allow_views'] );
	$allow_counts = intval( $_REQUEST['allow_counts'] );
	$rub_id = intval( $_REQUEST['rub_id'] );
	
	$category = $_POST['category'];

	if( !count( $category ) ) {
		$category = array ();
		$category[] = '0';
	}

	$category_list = array();

	foreach ( $category as $value ) {
		$category_list[] = intval($value);
	}

	$category = $db->safesql( implode( ',', $category_list ) );

	$grouplevel = $_POST['grouplevel'];
	
	if( !count( $grouplevel ) ) {
		$grouplevel = array ();
		$grouplevel[] = 'all';
	}

	$g_list = array();

	foreach ( $grouplevel as $value ) {
		if ($value == "all") $g_list[] = $value; else $g_list[] = intval($value);
	}

	$grouplevel = $db->safesql( implode( ',', $g_list ) );

	$devicelevel = $_POST['devicelevel'];
	
	if( !count( $devicelevel ) ) {
		$devicelevel = array ();
		$devicelevel[] = 'all';
	}

	$d_list = array();

	foreach ( $devicelevel as $value ) {
		if ($value == "all") $d_list[] = $value; else $d_list[] = intval($value);
	}

	$devicelevel = $db->safesql( implode( ',', $d_list ) );

	if ( trim($_POST['start_date']) ) {

		$start_date = @strtotime( $_POST['start_date'] );

		if ($start_date === - 1 OR !$start_date) $start_date = "";

	} else $start_date = "";

	if ( trim($_POST['end_date']) ) {

		$end_date = @strtotime( $_POST['end_date'] );

		if ($end_date === - 1 OR !$end_date) $end_date = "";

	} else $end_date = "";
	
	if( $banner_tag == "" or $banner_descr == "" ) msg( "error", $lang['addnews_error'], $lang['addnews_erstory'], "javascript:history.go(-1)" );
	
	$db->query( "UPDATE " . PREFIX . "_banners SET banner_tag='$banner_tag', descr='$banner_descr', code='$banner_code', approve='$approve', short_place='$short_place', bstick='$bstick', main='$main', category='$category', grouplevel='$grouplevel', start='$start_date', end='$end_date', fpage='$fpage', innews='$innews', devicelevel='$devicelevel', allow_views='$allow_views', max_views='$max_views', allow_counts='$allow_counts', max_counts='$max_counts', rubric='$rub_id' WHERE id='$id'" );
	@unlink( ENGINE_DIR . '/cache/system/banners.php' );
	clear_cache();
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '5', '{$banner_tag}')" );

	header('X-XSS-Protection: 0;');
	header( "Location: ?mod=banners{$urlsearch}&rubric=".$rub_id );
	die();
	
}

if( $_POST['action'] == "addrubric" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=banners") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	$title  = $db->safesql(  htmlspecialchars( strip_tags( stripslashes($_POST['title'] ) ), ENT_QUOTES, $config['charset']) );
	$description  = $db->safesql(  htmlspecialchars( strip_tags( stripslashes($_POST['description'] ) ), ENT_QUOTES, $config['charset']) );
	$parent = intval($rubric);
	
	$db->query( "INSERT INTO " . PREFIX . "_banners_rubrics (parentid, title, description) values ('{$parent}', '{$title}', '{$description}')" );
	
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '111', '{$title}')" );
	
	header( "Location: ?mod=banners{$urlsearch}&rubric=".$rubric );
	die();
	
}

if( $_POST['action'] == "editrubric" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=banners") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	$title  = $db->safesql(  htmlspecialchars( strip_tags( stripslashes($_POST['title'] ) ), ENT_QUOTES, $config['charset']) );
	$description  = $db->safesql(  htmlspecialchars( strip_tags( stripslashes($_POST['description'] ) ), ENT_QUOTES, $config['charset']) );
	$editrubricid = intval($_POST['editrubricid']);
	
	$db->query( "UPDATE " . PREFIX . "_banners_rubrics SET title='{$title}', description='{$description}' WHERE id='{$editrubricid}'" );
	
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '112', '{$title}')" );
	
	header( "Location: ?mod=banners{$urlsearch}&rubric=".$rubric );
	die();
}

if( $_GET['action'] == "deleterubric" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=banners") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	function DeleteSubRubrics($parentid) {
		global $db;
		
		$subr = $db->query( "SELECT id FROM " . PREFIX . "_banners_rubrics WHERE parentid = '{$parentid}'" );
		
		while ( $row = $db->get_row( $subr ) ) {
			DeleteSubRubrics( $row['id'] );
			
			$db->query( "DELETE FROM " . PREFIX . "_banners_rubrics WHERE id='{$row['id']}'" );
			$db->query( "DELETE FROM " . PREFIX . "_banners WHERE rubric='{$row['id']}'" );
		}
	}
	
	$rid=intval($_GET['rid']);
	
	$db->query( "DELETE FROM " . PREFIX . "_banners WHERE rubric='{$rid}'" );
	$db->query( "DELETE FROM " . PREFIX . "_banners_rubrics WHERE id='{$rid}'" );

	DeleteSubRubrics($rid);

	@unlink( ENGINE_DIR . '/cache/system/banners.php' );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '113', '{$rid}')" );

	clear_cache();
	
	header( "Location: ?mod=banners{$urlsearch}&rubric=".$rubric );
	die();
}


if( $_GET['action'] == "off" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=banners") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	if (!$id) msg( "error", "ID not valid", "ID not valid" );
	
	$db->query( "UPDATE " . PREFIX . "_banners SET approve='0' WHERE id='$id'" );
	@unlink( ENGINE_DIR . '/cache/system/banners.php' );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '6', '{$id}')" );

	clear_cache();
	
	header( "Location: ?mod=banners{$urlsearch}&rubric=".$rubric );
	die();
	
}

if( $_GET['action'] == "clearviews" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=banners") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	if (!$id) msg( "error", "ID not valid", "ID not valid" );
	
	$db->query( "UPDATE " . PREFIX . "_banners SET views='0' WHERE id='$id'" );
	@unlink( ENGINE_DIR . '/cache/system/banners.php' );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '109', '{$id}')" );

	clear_cache();
	
	header( "Location: ?mod=banners{$urlsearch}&rubric=".$rubric );
	die();
}

if( $_GET['action'] == "clearclicks" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=banners") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	if (!$id) msg( "error", "ID not valid", "ID not valid" );
	
	$db->query( "UPDATE " . PREFIX . "_banners SET clicks='0' WHERE id='$id'" );
	@unlink( ENGINE_DIR . '/cache/system/banners.php' );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '110', '{$id}')" );

	clear_cache();
	
	header( "Location: ?mod=banners{$urlsearch}&rubric=".$rubric );
	die();
}

if( $_GET['action'] == "on" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=banners") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	if (!$id) msg( "error", "ID not valid", "ID not valid" );
	
	$db->query( "UPDATE " . PREFIX . "_banners set approve='1' WHERE id='$id'" );
	@unlink( ENGINE_DIR . '/cache/system/banners.php' );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '7', '{$id}')" );

	clear_cache();
	
	header( "Location: ?mod=banners{$urlsearch}&rubric=".$rubric );
	die();
}

if( $_GET['action'] == "delete" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=banners") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	if (!$id) msg( "error", "ID not valid", "ID not valid" );
	
	$db->query( "DELETE FROM " . PREFIX . "_banners WHERE id='$id'" );
	@unlink( ENGINE_DIR . '/cache/system/banners.php' );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '8', '{$id}')" );

	clear_cache();
	
	header( "Location: ?mod=banners{$urlsearch}&rubric=".$rubric );
	die();
	
}

if( $_GET['action'] == "view" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if (!$id) msg( "error", "ID not valid", "ID not valid" );

	$row = $db->super_query( "SELECT * FROM " . PREFIX . "_banners WHERE id='$id'" );
	
	$row['code'] = str_ireplace( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $row['code'] );

	if (!$row['id']) msg( "error", "ID not valid", "ID not valid" );

echo <<<HTML
<!doctype html>
<html>
<head>
<meta charset="{$config['charset']}">
<title>DataLife Engine - {$lang['skin_title']}</title>
<link href="/templates/{$config['skin']}/preview.css" type="text/css" rel="stylesheet">
</head>
<body>
HTML;

echo "<fieldset style=\"border-style:solid; border-width:1; border-color:black;\">{$row['code']}</fieldset></body></html>";

	die();
}

if( $_REQUEST['action'] == "add" or $_REQUEST['action'] == "edit" ) {
	
	$start_date = "";
	$stop_date  = "";

	if( $_REQUEST['action'] == "add" ) {
		$checked = "checked";
		$doaction = "doadd";
		$all_cats = "selected";
		$check_all = "selected";
		$check_all_1 = "selected";
		$groups = get_groups();
		$devicelevel = get_devicelevel();
		$checked2 = "";
		$checked3 = "";
		$checked4 = "";
		$checked5 = "";
		$checked6 = "";
		$max_views = "";
		$max_counts = "";
		$allow_views = makeDropDown( array ("0" => $lang['opt_sys_r1'], "1" => $lang['opt_sys_r2'], "2" => $lang['opt_sys_r3'] ) , "allow_views", 0 );
		$allow_counts = makeDropDown( array ("0" => $lang['opt_sys_r1'], "1" => $lang['banner_counts_1'], "2" => $lang['banner_counts_2'] ) , "allow_counts", 0 );
		$rubrics_list = array_selection($rubrics, $rubric);

	} else {
		
		$row = $db->super_query( "SELECT * FROM " . PREFIX . "_banners WHERE id='{$id}' LIMIT 1" );
		$banner_tag = $row['banner_tag'];
		$banner_descr = htmlspecialchars( $row['descr'], ENT_QUOTES, $config['charset'] );
		$banner_code = htmlspecialchars(  $row['code'], ENT_QUOTES, $config['charset'] );
		$short_place = $row['short_place'];
		$checked = ($row['approve']) ? "checked" : "";
		$checked2 = ($row['allow_full']) ? "checked" : "";
		$checked3 = ($row['bstick']) ? "checked" : "";
		$checked4 = ($row['main']) ? "checked" : "";
		$checked5 = ($row['fpage']) ? "checked" : "";
		$checked6 = ($row['innews']) ? "checked" : "";
		$max_views = ($row['max_views']) ? intval($row['max_views']) : "";
		$max_counts = ($row['max_counts']) ? intval($row['max_counts']) : "";
		$lang['banners_title'] = $lang['banners_title_1'];
		$doaction = "doedit";
		
		$groups = get_groups( explode( ',', $row['grouplevel'] ) );
		$devicelevel = get_devicelevel( explode( ',', $row['devicelevel'] ) );
		$allow_views = makeDropDown( array ("0" => $lang['opt_sys_r1'], "1" => $lang['opt_sys_r2'], "2" => $lang['opt_sys_r3'] ) , "allow_views", $row['allow_views'] );
		$allow_counts = makeDropDown( array ("0" => $lang['opt_sys_r1'], "1" => $lang['banner_counts_1'], "2" => $lang['banner_counts_2'] ) , "allow_counts", $row['allow_counts'] );
		$rubrics_list = array_selection($rubrics, $row['rubric']);
		
		if( $row['grouplevel'] == "all" ) $check_all = "selected";
		else $check_all = "";
		
		if( $row['devicelevel'] == "all" ) $check_all_1 = "selected";
		else $check_all_1 = "";
		
		if ( $row['start'] ) $start_date = @date( "Y-m-d H:i", $row['start'] );
		if ( $row['end'] )  $end_date  = @date( "Y-m-d H:i", $row['end'] );
	
	}
	
	$opt_category = CategoryNewsSelection( explode( ',', $row['category'] ), 0, FALSE );
	if( ! $row['category'] ) $all_cats = "selected";
	else $all_cats = "";
	
	$js_array[] = "engine/skins/codemirror/js/code.js";
	$css_array[] = "engine/skins/codemirror/css/default.css";
	
	if($rubric) $r_name=$rubrics[$rubric]['title']; else $r_name = $lang['header_banner'];
	
	echoheader( "<i class=\"fa fa-shopping-cart position-left\"></i><span class=\"text-semibold\">{$lang['header_banner']}</span>", array('?mod=banners&rubric='.$rubric => $r_name, '' => $lang['banners_title'] ) );
	
	echo <<<HTML
<style type="text/css">
.CodeMirror {
  height: 300px !important;
}
</style>
<form action="" method="post" name="bannersform" id="addnews" class="form-horizontal">
<input type="hidden" name="mod" value="banners">
<input type="hidden" name="action" value="{$doaction}">
<input type="hidden" name="user_hash" value="$dle_login_hash" />
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['banners_title']}
  </div>
  <div class="panel-body">

		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['banners_xname']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350 position-left" maxlength="40" type="text" name="banner_tag" value="{$banner_tag}" /><span class="text-muted text-size-small">({$lang['xf_lat']})</span>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['banners_xdescr']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" type="text" name="banner_descr" maxlength="200" value="{$banner_descr}" />
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['add_rubric_3']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="rub_id" style="width:100%;max-width:350px;" class="uniform" data-width="350">{$rubrics_list}</select>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['addnews_cat']}</label>
		  <div class="col-md-10 col-sm-9">
			<select data-placeholder="{$lang['addnews_cat_sel']}" style=width:350px;" name="category[]" class="cat_select" multiple><option value="0" {$all_cats}>{$lang['edit_all']}</option>{$opt_category}</select>
		  </div>
		 </div>		 
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['vote_startdate']}</label>
		  <div class="col-md-10 col-sm-9">
			<input data-rel="calendardatetime" type="text" name="start_date" class="form-control" style="width:190px;" value="{$start_date}" autocomplete="off"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_bstart']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['vote_enddate']}</label>
		  <div class="col-md-10 col-sm-9">
			<input data-rel="calendardatetime" type="text" name="end_date" class="form-control" style="width:190px;" value="{$end_date}" autocomplete="off"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_bend']}" ></i>
		  </div>
		 </div>
		<div class="form-group editor-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['banners_code']}</label>
		  <div class="col-md-10 col-sm-9">
			<div style="border: solid 1px #BBB;width:100%;">
				<textarea style="width:100%;" name="banner_code" id="banner_code" rows="16">{$banner_code}</textarea>
			</div>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['stat_allow']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="grouplevel[]" class="cat_select" data-placeholder="{$lang['group_select_1']}" style="width:250px;" multiple><option value="all" {$check_all}>{$lang['edit_all']}</option>{$groups}</select>
		  </div>
		</div>
		
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['banner_dev']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="devicelevel[]" class="cat_select" data-placeholder="{$lang['group_select_2']}" style="width:250px;" multiple><option value="all" {$check_all_1}>{$lang['edit_all']}</option>{$devicelevel}</select>
		  </div>
		</div>
		
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['banner_views']}</label>
		  <div class="col-md-10 col-sm-9">
			{$allow_views}
		  </div>
		</div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['banner_counts']}</label>
		  <div class="col-md-10 col-sm-9">
			{$allow_counts}
		  </div>
		</div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['banner_mviews']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" name="max_views" class="form-control" style="width:220px;" value="{$max_views}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_bviews']}" ></i>
		  </div>
		</div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['banner_mcounts']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" name="max_counts" class="form-control" style="width:220px;" value="{$max_counts}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_bcounts']}" ></i>
		  </div>
		</div>
		
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"></label>
		  <div class="col-md-10 col-sm-9">
			<div class="checkbox"><label><input class="icheck" type="checkbox" name="approve" value="1" {$checked} id="editbact"/>{$lang['banners_approve']}</label></div>
			<div class="checkbox"><label><input class="icheck" type="checkbox" value="1" name="main" {$checked4} id="main" />{$lang['banners_main']}</label></div>
			<div class="checkbox"><label><input class="icheck" type="checkbox" value="1" name="fpage" {$checked5} id="fpage" />{$lang['banners_fpage']}</label></div>
			<div class="checkbox"><label><input class="icheck" type="checkbox" value="1" name="innews" {$checked6} id="innews" />{$lang['banners_innews']}</label></div>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"></label>
		  <div class="col-md-10 col-sm-9">
			<b>{$lang['banners_s_opt']}</b>
		  </div>
		 </div>		 
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"></label>
		  <div class="col-md-10 col-sm-9">
HTML;

	echo makeDropDown( array ("0" => $lang['banners_s_0'], "1" => $lang['banners_s_1'], "2" => $lang['banners_s_2'], "3" => $lang['banners_s_3'], "4" => $lang['banners_s_4'], "5" => $lang['banners_s_5'], "6" => $lang['banners_s_6'], "7" => $lang['banners_s_7'] ), "short_place", $short_place );
	
	echo <<<HTML
		  <label class="position-right text-muted text-size-small">{$lang['banners_s']}</label>
		  <div class="checkbox mt-5"><label><input class="icheck" type="checkbox" value="1" name="bstick" {$checked3} id="bstick" />{$lang['banners_bstick']}</label></div>
		  </div>
		 </div>	
		 
	</div>
	<div class="panel-footer">
		<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
	</div>	
</div>
</form>
<script>
	$(function(){
		  $(".cat_select").chosen({allow_single_deselect:true, no_results_text: '{$lang['addnews_cat_fault']}'});
		  
			var editor = CodeMirror.fromTextArea(document.getElementById('banner_code'), {
			  mode: "htmlmixed",
			  lineNumbers: true,
			  dragDrop: false,
			  indentUnit: 4,
			  indentWithTabs: false
			});

	});
</script>
HTML;
	
	echofooter();

} else {
	
	$js_array[] = "engine/classes/highlight/highlight.code.js";

	$where = array("rubric='{$rubric}'");
	
	if($rubric) {
		
		$bread = get_bread_crumbs($rubric);
		
	} else $bread = $lang['header_banner_1'];

	if ( $searchword ) {
	  
	  $sql_searchword = @$db->safesql($_REQUEST['searchword']);
	  $where[] = "`banner_tag` like '%{$sql_searchword}%' OR `descr` like '%{$sql_searchword}%' OR `code` like '%{$sql_searchword}%'";
	  
	}

	$where = implode( " AND ", $where );
	
	echoheader( "<i class=\"fa fa-shopping-cart position-left\"></i><span class=\"text-semibold\">{$lang['header_banner']}</span>", $bread );

	$db->query( "SELECT * FROM " . PREFIX . "_banners WHERE {$where} ORDER BY id DESC" );

	$entries = "";
	$r_list = "";
	
	while ( $row = $db->get_row() ) {
		
		$row['descr'] = $row['descr'];
		$row['code'] = "<pre><code>".htmlspecialchars ($row['code'], ENT_QUOTES, $config['charset'])."</code></pre>";

		if( !$langformatdatefull ) $langformatdatefull = "d.m.Y H:i";

		if ( $row['start'] ) $start_date = "<br>".date( $langformatdatefull, $row['start'] ); else $start_date = "--";
		if ( $row['end'] ) $end_date = "<br>".date( $langformatdatefull, $row['end'] ); else $end_date = "--";

		if ($row['max_views'] AND $row['views'] >= $row['max_views'] ) $row['approve'] = 0;
		if ($row['max_counts'] AND $row['clicks'] >= $row['max_counts'] ) $row['approve'] = 0;
		
		if( $row['approve'] ) {
			$status = "<span title=\"{$lang['banners_on']}\" class=\"text-success tip\"><b><i class=\"fa fa-check-circle\"></i></b></span>";
			$lang['led_active'] = $lang['banners_aus'];
			$led_action = "off";
		} else {
			$status = "<span title=\"{$lang['banners_off']}\" class=\"text-danger tip\"><b><i class=\"fa fa-exclamation-circle\"></i></b></span>";
			$lang['led_active'] = $lang['banners_ein'];
			$led_action = "on";
		}

		if( $row['end'] AND time() > $row['end']) {
			$status = "<span title=\"{$lang['banners_off']}\" class=\"text-danger tip\"><b><i class=\"fa fa-exclamation-circle\"></i></b></span>";
		}
		
		if( $row['start'] AND time() < $row['start'] ) {
			$status = "<span title=\"{$lang['banners_off']}\" class=\"text-danger tip\"><b><i class=\"fa fa-exclamation-circle\"></i></b></span>";
		}
		
		if ($row['allow_views'] AND $row['max_views'] AND $row['views'] >= $row['max_views'] ) {
			$status = "<span title=\"{$lang['banners_off']}\" class=\"text-danger tip\"><b><i class=\"fa fa-exclamation-circle\"></i></b></span>";
		}
			
		if ($row['allow_views'] AND $row['max_counts'] AND $row['clicks'] >= $row['max_counts'] ) {
			$status = "<span title=\"{$lang['banners_off']}\" class=\"text-danger tip\"><b><i class=\"fa fa-exclamation-circle\"></i></b></span>";
		}	
		
		if($row['allow_views']) $row['views'] = number_format($row['views'], 0, ',', ' '); else $row['views'] = "--";
		if($row['allow_counts']) $row['clicks'] = number_format($row['clicks'], 0, ',', ' '); else $row['clicks'] = "--";
		
		$menu_link = <<<HTML
        <div class="btn-group">
			<a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
			<ul class="dropdown-menu dropdown-menu-right">
			  <li><a onclick="javascript:preview('{$row['id']}'); return false;" href="#"><i class="fa fa-desktop"></i> {$lang['banner_view']}</a></li>
			  <li><a href="?mod=banners&user_hash={$dle_login_hash}&action={$led_action}&rubric={$rubric}&id={$row['id']}"><i class="fa fa-eye"></i> {$lang['led_active']}</a></li>
			  <li><a href="?mod=banners&user_hash={$dle_login_hash}&action=edit&rubric={$rubric}&id={$row['id']}"><i class="fa fa-magic"></i> {$lang['group_sel1']}</a></li>
			  <li class="divider"></li>
			  <li><a href="?mod=banners&user_hash={$dle_login_hash}&action=clearviews&rubric={$rubric}&id={$row['id']}"><i class="fa fa-eraser"></i> {$lang['banner_clear_1']}</a></li>
			  <li><a href="?mod=banners&user_hash={$dle_login_hash}&action=clearclicks&rubric={$rubric}&id={$row['id']}"><i class="fa fa-eraser"></i> {$lang['banner_clear_2']}</a></li>
			  <li class="divider"></li>
			  <li><a onclick="javascript:confirmdelete('{$row['id']}'); return false;" href="#"><i class="fa fa-trash-o text-danger"></i> {$lang['cat_del']}</a></li>
			</ul>
        </div>
HTML;
		
		$entries .= "
		<tr>
		 <td class=\"text-size-small\">
		 {$row['descr']}<br />{$lang['banners_tag']}<br />[banner_{$row['banner_tag']}]<br />{banner_{$row['banner_tag']}}<br />[/banner_{$row['banner_tag']}]<br /><br />{$lang['vote_startinfo']}: {$start_date}<br />{$lang['vote_endinfo']}: {$end_date}</td>
		 <td class=\"hidden-xs\">{$row['code']}</td>
		 <td class=\"text-nowrap text-center\">{$row['views']}</td>
		 <td class=\"text-nowrap text-center\">{$row['clicks']}</td>
		 <td>{$status}</td>
		 <td class=\"text-center\">{$menu_link}</td>
	   </tr>";
	}
	
	if( $entries ) {
		$th_head = <<<HTML
      <tr>
        <td class="no-border" style="width: 170px">{$lang['static_descr']}</td>
        <td id="codelist" class="hidden-xs no-border">&nbsp;</td>
		<td class="no-border text-center" style="width: 60px;"><i class="fa fa-eye tip" data-original-title="{$lang['st_views']}"></i></td>
		<td class="no-border text-center" style="width: 60px;"><i class="fa fa-hand-pointer-o tip" data-original-title="{$lang['banner_counts_3']}"></i></td>
        <td class="no-border" style="width: 30px"></td>
        <td class="no-border" style="width: 70px">&nbsp;</td>
      </tr>
HTML;

		$entries = $th_head.$entries;
	}
	
	$db->free();
	
	$i=0;
	
	foreach($rubrics as $value) {

		if($value['parentid'] == $rubric ) {

			$menu_link = <<<HTML
			<div class="btn-group">
				<a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
				<ul class="dropdown-menu dropdown-menu-right">
				  <li><a uid="{$value['id']}" href="?mod=banners" class="editlink"><i class="fa fa-magic"></i> {$lang['group_sel1']}</a></li>
				  <li class="divider"></li>
				  <li><a onclick="javascript:confirm_rubric_delete('{$value['id']}'); return false;" href="#"><i class="fa fa-trash-o text-danger"></i> {$lang['cat_del']}</a></li>
				</ul>
			</div>
HTML;
			if(!$i) $border = "no-border-top "; else $border = "";
			
			$r_list .= "
			<tr>
			 <td class=\"{$border}cursor-pointer\" onclick=\"document.location = '?mod=banners&rubric={$value['id']}'; return false;\"><h6 id=\"title_{$value['id']}\" class=\"media-heading text-semibold\">{$value['title']}</h6><div class=\"text-muted text-size-small\">{$value['description']}</div><textarea id=\"descr_{$value['id']}\" style=\"display:none;\">{$value['description']}</textarea></td>
			 <td class=\"{$border}text-center\" style=\"width: 70px\">{$menu_link}</td>
		   </tr>";
		   
		   $i++;
	
		}
		
	}

	if($r_list) $r_list = '<table class="table table-xs table-hover">'.$r_list.'</table>';

	if(!$entries AND !$r_list) {
		$entries = "<tr><td class=\"no-border-top\"><div align=\"center\"><br><br>{$lang['banner_not_found']}<br><br><br></div></td></tr>";
	}
	
	echo <<<HTML
<form action="?mod=banners&rubric={$rubric}" method="post" name="optionsbar" id="optionsbar">
<input type="hidden" name="mod" value="banners">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['banners_list']}
	<div class="heading-elements">
		<div class="form-group has-feedback" style="width:250px;">
			<input name="searchword" type="search" class="form-control" placeholder="{$lang['search_field']}" onchange="document.optionsbar.start_from.value=0;" value="{$searchword}">
			<div class="form-control-feedback">
			    <a href="#" onclick="$(this).closest('form').submit();"><i class="fa fa-search text-size-base text-muted"></i></a>
			</div>
		</div>
	</div>
  </div>
  <div class="table-responsive">
	{$r_list}
	<table class="table table-xs" style="table-layout:fixed;">
		{$entries}
	</table>	  
   </div>
	<div class="panel-footer">
		<button type="button" onclick="document.location='?mod=banners&action=add&rubric={$rubric}'" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-plus position-left"></i>{$lang['bb_create']}</button>
		<button type="button" onclick="addRubric(); return false;" class="btn bg-slate-600 btn-sm btn-raised position-left"><i class="fa fa-plus position-left"></i>{$lang['add_rubric']}</button>
		<a class="pull-right" onclick="javascript:Help('banners'); return false;" href="#">{$lang['banners_help']}</a>
	</div>		
</div>
</form>
<script>  
<!--
function confirmdelete(id){
	    DLEconfirm( '{$lang['banners_del']}', '{$lang['p_confirm']}', function () {
			document.location="?mod=banners&action=delete&user_hash={$dle_login_hash}&rubric={$rubric}&id="+id;
		} );
}
function confirm_rubric_delete(id){
	    DLEconfirm( '{$lang['rubric_del']}', '{$lang['p_confirm']}', function () {
			document.location="?mod=banners&action=deleterubric&user_hash={$dle_login_hash}&rubric={$rubric}&rid="+id;
		} );
}


function preview(id){
	window.open('?mod=banners&action=view&user_hash={$dle_login_hash}&id='+id,'prv','height=300,width=650,resizable=1,scrollbars=1');
}

function addRubric() {
	var b = {};
	
	b[dle_act_lang[3]] = function() { 
					$(this).dialog("close");						
			    };
	
	b[dle_act_lang[2]] = function() { 
					if ( $("#dle-promt-title").val().length < 1) {
						 $("#dle-promt-title").addClass('ui-state-error');
					} else {
						$("#addrubric").submit();
					}			
				};

	$("#dlepopup").remove();

	$("body").append("<div id='dlepopup' title='{$lang['add_rubric_1']}' style='display:none'><form id='addrubric' method='post'>{$lang['rubric_title']}<input type='hidden' name='mod' value='banners'><input type='hidden' name='action' value='addrubric'><input type='hidden' name='user_hash' value='{$dle_login_hash}'><input type='hidden' name='rubric' value='{$rubric}'><br /><input type='text' name='title' id='dle-promt-title' class='classic' style='width:100%;' value=''><br /><br />{$lang['rubric_description']}<br /><textarea name='description' id='dle-promt-descr' class='classic' style='width:100%;' rows='3'></textarea></form></div>");
	
	$('#dlepopup').dialog({
		autoOpen: true,
		width: 600,
		resizable: false,
		buttons: b
	});

}
	
$(function(){

	hljs.initHighlightingOnLoad();

	$('.editlink').click(function(){

		var rid = $(this).attr('uid');
		var title = $('#title_'+$(this).attr('uid')).text();
		title = title.replace(/'/g, "&#039;");
		var description = $('#descr_'+rid).val();
			
			var b = {};
		
			b[dle_act_lang[3]] = function() { 
							$(this).dialog("close");						
					    };
		
			b[dle_act_lang[2]] = function() { 
						if ( $("#dle-promt-title").val().length < 1) {
							 $("#dle-promt-title").addClass('ui-state-error');
						} else {
							$("#editrubric").submit();
						}					
					};
	
			$("#dlepopup").remove();

		$("body").append("<div id='dlepopup' title='{$lang['add_rubric_2']}' style='display:none'><form id='editrubric' method='post'>{$lang['rubric_title']}<input type='hidden' name='mod' value='banners'><input type='hidden' name='action' value='editrubric'><input type='hidden' name='user_hash' value='{$dle_login_hash}'><input type='hidden' name='editrubricid' value='"+rid+"'><br /><input type='text' name='title' id='dle-promt-title' class='classic' style='width:100%;' value='"+title+"'><br /><br />{$lang['rubric_description']}<br /><textarea name='description' id='dle-promt-descr' class='classic' style='width:100%;' rows='3'>"+description+"</textarea></form></div>");
		
			$('#dlepopup').dialog({
				autoOpen: true,
				width: 600,
				resizable: false,
				buttons: b
			});

			return false;
	});
});
//-->
</script>
HTML;
	
	echofooter();

}
?>