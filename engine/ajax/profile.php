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
 File: profile.php
-----------------------------------------------------
 Use: User profile
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/templates.class.php'));

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
	echo "<div id='dleprofilepopup' title='{$lang['all_err_1']}' style='display:none'><br />{$lang['sess_error']}</div>";
	die();

}

$tpl = new dle_template( );
$tpl->dir = ROOT_DIR . '/templates/' . $config['skin'];
define( 'TEMPLATE_DIR', $tpl->dir );
$PHP_SELF = $config['http_home_url'] . "index.php";

if (isset ( $_GET['name'] )) $name = @$db->safesql ( strip_tags ( urldecode ( $_GET['name'] ) ) ); else $name = '';

if (!$name ) die("Hacking attempt!");

if( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $name ) ) die("Not allowed user name!");

$row = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE name = '{$name}'" );

if (!$row['user_id']) {

echo "<div id='dleprofilepopup' title='{$lang['all_err_1']}' style='display:none'><br />{$lang['news_err_26']}</div>";

} else {

$tpl->load_template( 'profile_popup.tpl' );

if( strpos( $tpl->copy_template, "[xfvalue_" ) !== false ) {

	$xfields = xfieldsload( true );
	$xfieldsdata = xfieldsdataload( $row['xfields'] );
				
	foreach ( $xfields as $value ) {
		$preg_safe_name = preg_quote( $value[0], "'" );
					
		if( $value[5] != 1 or $member_id['user_group'] == 1 or ($is_logged and $row['is_register'] and $member_id['name'] == $row['name']) ) {

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

		} else {

			$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
			$tpl->copy_template = preg_replace( "'\\[xfvalue_{$preg_safe_name}\\]'i", "", $tpl->copy_template );
			$tpl->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );

		}
	}
}

if ( count(explode("@", $row['foto'])) == 2 ) {

	$tpl->set( '{foto}', 'https://www.gravatar.com/avatar/' . md5(trim($row['foto'])) . '?s=' . intval($user_group[$row['user_group']]['max_foto']) );	

} else {
	
	if( $row['foto'] ) {
			
		if (strpos($row['foto'], "//") === 0) $avatar = "http:".$row['foto']; else $avatar = $row['foto'];

		$avatar = @parse_url ( $avatar );

		if( $avatar['host'] ) {
				
			$tpl->set( '{foto}', $row['foto'] );
				
		} else $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $row['foto'] );
			
	} else $tpl->set( '{foto}', "{THEME}/dleimages/noavatar.png" );

}

if (stripos ( $tpl->copy_template, "[profile-user-group=" ) !== false) {
	$tpl->copy_template = preg_replace_callback ( '#\\[profile-user-group=(.+?)\\](.*?)\\[/profile-user-group\\]#is',
		function ($matches) {
			global $row;

			$groups = $matches[1];
			$block = $matches[2];
			
			$groups = explode( ',', $groups );
			
			if( !in_array( $row['user_group'], $groups ) ) return "";
		
			return $block;
		},		
	$tpl->copy_template );
}

if (stripos ( $tpl->copy_template, "[not-profile-user-group=" ) !== false) {
	$tpl->copy_template = preg_replace_callback ( '#\\[not-profile-user-group=(.+?)\\](.*?)\\[/not-profile-user-group\\]#is',
		function ($matches) {
			global $row;
			
			$groups = $matches[1];
			$block = $matches[2];
			
			$groups = explode( ',', $groups );
			
			if( in_array( $row['user_group'], $groups ) ) return "";
	
			return $block;
		},		
	$tpl->copy_template );
}

if( $row['banned'] == 'yes' ) $user_group[$row['user_group']]['group_name'] = $lang['user_ban'];

$tpl->set( '{status}',  $user_group[$row['user_group']]['group_prefix'].$user_group[$row['user_group']]['group_name'].$user_group[$row['user_group']]['group_suffix'] );
$tpl->set( '{registration}', langdate( "j F Y H:i", $row['reg_date'] ) );
$tpl->set( '{lastdate}', langdate( "j F Y H:i", $row['lastdate'] ) );

if ( ($row['lastdate'] + 1200) > $_TIME ) {

	$tpl->set( '[online]', "" );
	$tpl->set( '[/online]', "" );
	$tpl->set_block( "'\\[offline\\](.*?)\\[/offline\\]'si", "" );

} else {
	$tpl->set( '[offline]', "" );
	$tpl->set( '[/offline]', "" );
	$tpl->set_block( "'\\[online\\](.*?)\\[/online\\]'si", "" );
}

if( $row['fullname'] ) {
	$tpl->set( '[fullname]', "" );
	$tpl->set( '[/fullname]', "" );
	$tpl->set( '{fullname}', stripslashes( $row['fullname'] ) );
	$tpl->set_block( "'\\[not-fullname\\](.*?)\\[/not-fullname\\]'si", "" );
} else {
	$tpl->set_block( "'\\[fullname\\](.*?)\\[/fullname\\]'si", "" );
	$tpl->set( '{fullname}', "" );
	$tpl->set( '[not-fullname]', "" );
	$tpl->set( '[/not-fullname]', "" );
}

if( $row['land'] ) {
	$tpl->set( '[land]', "" );
	$tpl->set( '[/land]', "" );
	$tpl->set( '{land}', stripslashes( $row['land'] ) );
	$tpl->set_block( "'\\[not-land\\](.*?)\\[/not-land\\]'si", "" );
} else {
	$tpl->set_block( "'\\[land\\](.*?)\\[/land\\]'si", "" );
	$tpl->set( '{land}', "" );
	$tpl->set( '[not-land]', "" );
	$tpl->set( '[/not-land]', "" );
}

if( $row['info'] ) {
	$tpl->set( '[info]', "" );
	$tpl->set( '[/info]', "" );
	$tpl->set( '{info}', stripslashes( $row['info'] ) );
	$tpl->set_block( "'\\[not-info\\](.*?)\\[/not-info\\]'si", "" );	
} else {
	$tpl->set_block( "'\\[info\\](.*?)\\[/info\\]'si", "" );
	$tpl->set( '{info}', "" );
	$tpl->set( '[not-info]', "" );
	$tpl->set( '[/not-info]', "" );
}

if ( $config['rating_type'] == "1" ) {
		$tpl->set( '[rating-type-2]', "" );
		$tpl->set( '[/rating-type-2]', "" );
		$tpl->set_block( "'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", "" );
		$tpl->set_block( "'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", "" );
		$tpl->set_block( "'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", "" );
} elseif ( $config['rating_type'] == "2" ) {
		$tpl->set( '[rating-type-3]', "" );
		$tpl->set( '[/rating-type-3]', "" );
		$tpl->set_block( "'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", "" );
		$tpl->set_block( "'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", "" );
		$tpl->set_block( "'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", "" );
} elseif ( $config['rating_type'] == "3" ) {
		$tpl->set( '[rating-type-4]', "" );
		$tpl->set( '[/rating-type-4]', "" );
		$tpl->set_block( "'\\[rating-type-1\\](.*?)\\[/rating-type-1\\]'si", "" );
		$tpl->set_block( "'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", "" );
		$tpl->set_block( "'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", "" );
} else {
		$tpl->set( '[rating-type-1]', "" );
		$tpl->set( '[/rating-type-1]', "" );
		$tpl->set_block( "'\\[rating-type-4\\](.*?)\\[/rating-type-4\\]'si", "" );
		$tpl->set_block( "'\\[rating-type-3\\](.*?)\\[/rating-type-3\\]'si", "" );
		$tpl->set_block( "'\\[rating-type-2\\](.*?)\\[/rating-type-2\\]'si", "" );	
}

if ( $config['comments_rating_type'] == "1" ) {
		$tpl->set( '[comments-rating-type-2]', "" );
		$tpl->set( '[/comments-rating-type-2]', "" );
		$tpl->set_block( "'\\[comments-rating-type-1\\](.*?)\\[/comments-rating-type-1\\]'si", "" );
		$tpl->set_block( "'\\[comments-rating-type-3\\](.*?)\\[/comments-rating-type-3\\]'si", "" );
		$tpl->set_block( "'\\[comments-rating-type-4\\](.*?)\\[/comments-rating-type-4\\]'si", "" );
} elseif ( $config['comments_rating_type'] == "2" ) {
		$tpl->set( '[comments-rating-type-3]', "" );
		$tpl->set( '[/comments-rating-type-3]', "" );
		$tpl->set_block( "'\\[comments-rating-type-1\\](.*?)\\[/comments-rating-type-1\\]'si", "" );
		$tpl->set_block( "'\\[comments-rating-type-2\\](.*?)\\[/comments-rating-type-2\\]'si", "" );
		$tpl->set_block( "'\\[comments-rating-type-4\\](.*?)\\[/comments-rating-type-4\\]'si", "" );
} elseif ( $config['comments_rating_type'] == "3" ) {
		$tpl->set( '[comments-rating-type-4]', "" );
		$tpl->set( '[/comments-rating-type-4]', "" );
		$tpl->set_block( "'\\[comments-rating-type-1\\](.*?)\\[/comments-rating-type-1\\]'si", "" );
		$tpl->set_block( "'\\[comments-rating-type-2\\](.*?)\\[/comments-rating-type-2\\]'si", "" );
		$tpl->set_block( "'\\[comments-rating-type-3\\](.*?)\\[/comments-rating-type-3\\]'si", "" );
} else {
		$tpl->set( '[comments-rating-type-1]', "" );
		$tpl->set( '[/comments-rating-type-1]', "" );
		$tpl->set_block( "'\\[comments-rating-type-4\\](.*?)\\[/comments-rating-type-4\\]'si", "" );
		$tpl->set_block( "'\\[comments-rating-type-3\\](.*?)\\[/comments-rating-type-3\\]'si", "" );
		$tpl->set_block( "'\\[comments-rating-type-2\\](.*?)\\[/comments-rating-type-2\\]'si", "" );	
}

$tpl->set( '{rate}', userrating( $row['user_id'] ) );
$tpl->set( '{commentsrate}', commentsuserrating( $row['user_id'] ) );

if( $row['signature'] and $user_group[$row['user_group']]['allow_signature'] ) {
		
	$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "\\1" );
	$tpl->set( '{signature}', stripslashes( $row['signature'] ) );
	
} else {
		
	$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "" );
	
}

if( $user_group[$row['user_group']]['icon'] ) $tpl->set( '{group-icon}', "<img src=\"" . $user_group[$row['user_group']]['icon'] . "\" border=\"0\" />" );
else $tpl->set( '{group-icon}', "" );

if( $row['news_num'] ) {
		
	if( $config['allow_alt_url'] ) {
			
		$tpl->set( '{news}', "<a href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/news/" . "\">" . $lang['all_user_news'] . "</a>" );
		$tpl->set( '[rss]', "<a href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/rss.xml" . "\" title=\"" . $lang['rss_user'] . "\">" );
		$tpl->set( '[/rss]', "</a>" );		

	} else {
			
		$tpl->set( '{news}', "<a href=\"" . $PHP_SELF . "?subaction=allnews&amp;user=" . urlencode( $row['name'] ) . "\">" . $lang['all_user_news'] . "</a>" );
		$tpl->set( '[rss]', "<a href=\"" . $PHP_SELF . "?mod=rss&amp;subaction=allnews&amp;user=" . urlencode( $row['name'] ) . "\" title=\"" . $lang['rss_user'] . "\">" );
		$tpl->set( '[/rss]', "</a>" );

	}

	$tpl->set( '{news-num}', number_format($row['news_num'], 0, ',', ' ') );
	$tpl->set( '[news-num]', "" );
	$tpl->set( '[/news-num]', "" );
	$tpl->set_block( "'\\[not-news-num\\](.*?)\\[/not-news-num\\]'si", "" );

} else {
		
	$tpl->set( '{news}', $lang['all_user_news'] );
	$tpl->set_block( "'\\[rss\\](.*?)\\[/rss\\]'si", "" );
	$tpl->set( '{news-num}', 0 );
	$tpl->set_block( "'\\[news-num\\](.*?)\\[/news-num\\]'si", "" );
	$tpl->set( '[not-news-num]', "" );
	$tpl->set( '[/not-news-num]', "" );
}

if( $row['comm_num'] ) {
		
	$tpl->set( '{comments}', "<a href=\"$PHP_SELF?do=lastcomments&amp;userid=" . $row['user_id'] . "\">" . $lang['last_comm'] . "</a>" );

	$tpl->set( '[comm-num]', "" );
	$tpl->set( '[/comm-num]', "" );
	$tpl->set( '{comm-num}', number_format($row['comm_num'], 0, ',', ' ') );
	$tpl->set_block( "'\\[not-comm-num\\](.*?)\\[/not-comm-num\\]'si", "" );
	
} else {
		
	$tpl->set( '{comments}', $lang['last_comm'] );
	$tpl->set( '{comm-num}', 0 );
	$tpl->set_block( "'\\[comm-num\\](.*?)\\[/comm-num\\]'si", "" );
	$tpl->set( '[not-comm-num]', "" );
	$tpl->set( '[/not-comm-num]', "" );	
}

$tpl->compile( 'content' );

$tpl->result['content'] = str_replace( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $tpl->result['content'] );

echo "<div id='dleprofilepopup' title='{$lang['p_user']} {$row['name']}' style='display:none'>{$tpl->result['content']}</div>";

}
?>