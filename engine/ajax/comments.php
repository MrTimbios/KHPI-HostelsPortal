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
 File: comments.php
-----------------------------------------------------
 Use: Show comments
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/templates.class.php'));

$tpl = new dle_template( );
$tpl->dir = ROOT_DIR . '/templates/' . $config['skin'];
define( 'TEMPLATE_DIR', $tpl->dir );

require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/comments.class.php'));

$news_id = intval($_GET['news_id']);
$user_query = "newsid=" . $news_id;

if ($news_id < 1) die( "Hacking attempt!" );

$row = $db->super_query("SELECT id, date, category, alt_name, comm_num FROM " . PREFIX . "_post WHERE  id = '{$news_id}'");

if (!$row['id']) die( "Hacking attempt!" );

$row['date'] = strtotime( $row['date'] );
$category_id = intval( $row['category'] );

if( $row['date'] >= ($_TIME - 2592000) ) {

	$allow_full_cache = $row['id'];

} else $allow_full_cache = false;

if( $config['allow_alt_url'] ) {

	if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {

		if( $category_id AND $config['seo_type'] == 2 ) {

            $c_url = get_url( $category_id );
            $full_link = $config['http_home_url'] . $c_url . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
			$row['alt_name'] = $row['id'] . "-" . $row['alt_name'];
			$link_page = $config['http_home_url'] . $c_url . "/" . 'page,1,';
			$news_name = $row['alt_name'];

		} else {

			$full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
			$row['alt_name'] = $row['id'] . "-" . $row['alt_name'];
			$link_page = $config['http_home_url'] . 'page,1,';
			$news_name = $row['alt_name'];
		}

	} else {

		$link_page = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] ) . 'page,1,';
		$news_name = $row['alt_name'];
		$full_link = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] ) . $row['alt_name'] . ".html";
	}

} else {

	$link_page = "";
	$news_name = "";
	$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
}

$comments = new DLE_Comments( $db, $row['comm_num'], intval($config['comm_nummers']) );

if( $config['comm_msort'] == "" OR $config['comm_msort'] == "ASC" ) $comm_msort = "ASC"; else $comm_msort = "DESC";

if( $config['tree_comments'] ) $comm_msort = "ASC";

if( $config['allow_cmod'] ) $where_approve = " AND " . PREFIX . "_comments.approve='1'";
else $where_approve = "";

$comments->query = "SELECT " . PREFIX . "_comments.id, post_id, " . PREFIX . "_comments.user_id, date, autor as gast_name, " . PREFIX . "_comments.email as gast_email, text, ip, is_register, " . PREFIX . "_comments.rating, " . PREFIX . "_comments.vote_num, " . PREFIX . "_comments.parent, name, " . USERPREFIX . "_users.email, news_num, comm_num, user_group, lastdate, reg_date, signature, foto, fullname, land, xfields FROM " . PREFIX . "_comments LEFT JOIN " . USERPREFIX . "_users ON " . PREFIX . "_comments.user_id=" . USERPREFIX . "_users.user_id WHERE " . PREFIX . "_comments.post_id = '$news_id'" . $where_approve . " ORDER BY " . PREFIX . "_comments.id " . $comm_msort;

$comments->build_comments('comments.tpl', 'ajax', $allow_full_cache );

$comments->build_navigation('navigation.tpl', $link_page . "{page}," . $news_name . ".html#comment", $user_query, $full_link);

if ($_GET['massact'] != "disable" ) {

	if ($config['comm_msort'] == "DESC" )
		$tpl->result['comments'] = "<div id=\"dle-ajax-comments\"></div>" . $tpl->result['comments'];
	else
		$tpl->result['comments'] = $tpl->result['comments']."<div id=\"dle-ajax-comments\"></div>";

	if ($user_group[$member_id['user_group']]['del_allc'] AND !$user_group[$member_id['user_group']]['edit_limit'])
		$tpl->result['comments'] .= "\n<div class=\"mass_comments_action\">{$lang['mass_comments']}&nbsp;<select name=\"mass_action\"><option value=\"\">{$lang['edit_selact']}</option><option value=\"mass_combine\">{$lang['edit_selcomb']}</option><option value=\"mass_delete\">{$lang['edit_seldel']}</option></select>&nbsp;&nbsp;<input type=\"submit\" class=\"bbcodes\" value=\"{$lang['b_start']}\" /></div>\n<input type=\"hidden\" name=\"do\" value=\"comments\" /><input type=\"hidden\" name=\"dle_allow_hash\" value=\"{$dle_login_hash}\" /><input type=\"hidden\" name=\"area\" value=\"news\" />";

}

if( strpos ( $tpl->result['comments'], "dleplyrplayer" ) !== false ) {
	$tpl->result['comments'] .= <<<HTML
		<script>
			if (typeof DLEPlayer == "undefined") {
			
                $('<link>').appendTo('head').attr({type: 'text/css', rel: 'stylesheet',href: dle_root + 'engine/classes/html5player/plyr.css'});
				  
				$.getScript( dle_root + 'engine/classes/html5player/plyr.js', function() {
					var containers = document.querySelectorAll(".dleplyrplayer");Array.from(containers).forEach(function (container) {new DLEPlayer(container);});
				});
				
			} else {
			
				var containers = document.querySelectorAll(".dleplyrplayer");Array.from(containers).forEach(function (container) {new DLEPlayer(container);});
				
			}
		</script>
HTML;

}

$tpl->result['comments'] = str_replace( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $tpl->result['comments'] );
$tpl->result['commentsnavigation'] = str_replace( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $tpl->result['commentsnavigation'] );

echo json_encode(array("navigation" => $tpl->result['commentsnavigation'], "comments" => $tpl->result['comments'] ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

?>