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
 File: clean.php
-----------------------------------------------------
 Use: DB optimization
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if(($member_id['user_group'] != 1)) {die ("error");}

if ($_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash) {

	  die ("error");

}

if ($_REQUEST['step'] == 10) {
	$_REQUEST['step'] = 11;
	$db->query("TRUNCATE TABLE " . PREFIX . "_logs");
	$db->query("TRUNCATE TABLE " . PREFIX . "_comment_rating_log");
	$db->query("TRUNCATE TABLE " . USERPREFIX . "_lostdb");
	$db->query("TRUNCATE TABLE " . PREFIX . "_flood");
	$db->query("TRUNCATE TABLE " . PREFIX . "_poll_log");
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '18', '')" );


}

if ($_REQUEST['step'] == 8) {
	$_REQUEST['step'] = 9;
	
	$_REQUEST['unreadpm'] = intval($_REQUEST['unreadpm']);
	
	if( $_REQUEST['unreadpm'] ) {
		
		$sql_result = $db->query( "SELECT id, user FROM " . USERPREFIX . "_pm WHERE pm_read='1'" );
		
		while ( $row = $db->get_row( $sql_result ) ) {
			$db->query( "DELETE FROM " . USERPREFIX . "_pm WHERE id='{$row['id']}'" );
			$db->query( "UPDATE " . USERPREFIX . "_users SET pm_all=pm_all-1 WHERE user_id='{$row['user']}'" );
			
		}
		
	} else {
		
		$db->query("TRUNCATE TABLE " . USERPREFIX . "_pm");
		$db->query("UPDATE " . USERPREFIX . "_users set pm_all='0', pm_unread='0'");
		
	}
	
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '17', '')" );

}

if ($_REQUEST['step'] == 6) {
		$_REQUEST['step'] = 7;

		$db->query("UPDATE " . USERPREFIX . "_users, " . PREFIX . "_post SET " . USERPREFIX . "_users.news_num = (SELECT COUNT(*) FROM " . PREFIX . "_post WHERE " . PREFIX . "_post.autor = " . USERPREFIX . "_users.name ) WHERE " . USERPREFIX . "_users.name = " . PREFIX . "_post.autor");
		$db->query("UPDATE " . USERPREFIX . "_users, " . PREFIX . "_comments SET " . USERPREFIX . "_users.comm_num = (SELECT COUNT(*) FROM " . PREFIX . "_comments WHERE " . PREFIX . "_comments.user_id = " . USERPREFIX . "_users.user_id ) WHERE " . USERPREFIX . "_users.user_id = " . PREFIX . "_comments.user_id");

}


if ($_REQUEST['step'] == 4) {
	if ((@strtotime($_REQUEST['date']) === -1) OR (@strtotime($_REQUEST['date']) === false) OR (trim($_REQUEST['date']) == ""))
		$_REQUEST['step'] = 3;
	else {

		$_REQUEST['step'] = 5;
		$_REQUEST['date'] = $db->safesql( $_REQUEST['date'] );
		$thisdate = strtotime($_REQUEST['date']);

		$sql = $db->query("SELECT COUNT(*) as count, post_id FROM " . PREFIX . "_comments WHERE date < '{$_REQUEST['date']}' GROUP BY post_id");

		while($row = $db->get_row($sql)){

			$db->query("UPDATE " . PREFIX . "_post SET comm_num=comm_num-{$row['count']} WHERE id='{$row['post_id']}'");

		}

		$db->free ($sql);

	    $db->query("DELETE FROM " . PREFIX . "_comments WHERE date < '{$_REQUEST['date']}'");

		$db->query( "SELECT id, name FROM " . PREFIX . "_comments_files WHERE date < '{$thisdate}'" );
		
		while ( $row = $db->get_row() ) {
			$url_image = explode( "/", $row['name'] );
			
			if( count( $url_image ) == 2 ) {
				
				$folder_prefix = $url_image[0] . "/";
				$image = $url_image[1];
						
			} else {
				
				$folder_prefix = "";
				$image = $url_image[0];
			
			}
	
			$image = totranslit($image);					
	
			@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $image );
			@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $image );
				
		}
		
		$db->query( "DELETE FROM " . PREFIX . "_comments_files WHERE date < '{$thisdate}'" );
	
	    $db->query("UPDATE " . PREFIX . "_post SET comm_num=0 WHERE comm_num='65535'");
	    $db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '16', '{$_REQUEST['date']}')" );

	    clear_cache();
	}
}


if ($_REQUEST['step'] == 2) {
	
	if ((@strtotime($_REQUEST['date']) === -1) OR (@strtotime($_REQUEST['date']) === false) OR (trim($_REQUEST['date']) == ""))
		$_REQUEST['step'] = 1;
	else {

			
		$category = array ();
		$where_cats = "";
		$cat_join = "";
		$group_by = "";
		
		if(is_array($_REQUEST['category']) AND count( $_REQUEST['category'] ) ) {
			
			$cat_join = "INNER JOIN " . PREFIX . "_post_extras_cats c ON (p.id=c.news_id) ";
			$group_by = " GROUP BY p.id";
		
			foreach ( $_REQUEST['category'] as $value ) {
				if( intval($value) ) $category[] = intval($value);
			}
			
			if ( count($category) ) {
				$where_cats = " AND c.cat_id IN (".implode(",", $category).")";
			}
		
		}
		
		$_REQUEST['step'] = 3;
		$_REQUEST['date'] = $db->safesql( $_REQUEST['date'] );

		$sql = $db->query("SELECT p.id FROM " . PREFIX . "_post p {$cat_join}WHERE date < '{$_REQUEST['date']}'{$where_cats}{$group_by}");

		while($row = $db->get_row($sql)){
			deletenewsbyid( $row['id'] );
		}

		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '15', '{$_REQUEST['date']}')" );


	   $db->free ($sql);
	   clear_cache();
	}
}

if ($_REQUEST['step'] == 11) {

$rs = $db->query("SHOW TABLE STATUS FROM `".DBNAME."`");
			while ($r = $db->get_array($rs)) {
			$db->query("OPTIMIZE TABLE  ". $r['Name']);
			}
$db->free ($rs);

$db->query("SHOW TABLE STATUS FROM `".DBNAME."`");
			$mysql_size = 0;
			while ($r = $db->get_array()) {
			if (strpos($r['Name'], PREFIX."_") !== false)
			$mysql_size += $r['Data_length'] + $r['Index_length'] ;
			}

$lang['clean_finish'] = str_replace ('{db-alt}', '<span style="color:red;">'.formatsize($_REQUEST['size']).'</span>', $lang['clean_finish']);
$lang['clean_finish'] = str_replace ('{db-new}', '<span style="color:red;">'.formatsize($mysql_size).'</span>', $lang['clean_finish']);
$lang['clean_finish'] = str_replace ('{db-compare}', '<span style="color:red;">'.formatsize($_REQUEST['size'] - $mysql_size).'</span>', $lang['clean_finish']);

$buffer = <<<HTML
{$lang['clean_finish']}
<br /><br />
HTML;

}

if ($_REQUEST['step'] == 9) {
$buffer = <<<HTML
{$lang['clean_logs']}
<br /><br /><span style="color:red;"><span id="status"></span></span><br /><br />
		<input id = "next_button" onclick="start_clean('10', '{$_REQUEST['size']}'); return false;" class="btn bg-teal btn-sm btn-raised position-left" type="button" value="{$lang['edit_next']}">
		<input id = "skip_button" onclick="start_clean('11', '{$_REQUEST['size']}'); return false;" class="btn bg-slate-600 btn-sm btn-raised" type="button" value="{$lang['clean_skip']}">
HTML;
}

if ($_REQUEST['step'] == 7) {
$buffer = <<<HTML
<script>
    $(".icheck").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-teal-600 text-teal-800',
        fileDefaultHtml: filedefaulttext,
        fileButtonHtml: filebtntext,
        fileButtonClass: 'btn bg-teal btn-sm btn-raised'
    });
</script>
{$lang['clean_pm']}
<div class="checkbox"><label><input class="icheck" type="checkbox" id="unreadpm" name="unreadpm" value="1">{$lang['only_unread_pm']}</label></div>
<span style="color:red;"><span id="status"></span></span><br /><br />
		<input id = "next_button" onclick="start_clean('8', '{$_REQUEST['size']}'); return false;" class="btn bg-teal btn-sm btn-raised position-left" type="button" value="{$lang['edit_next']}">
		<input id = "skip_button" onclick="start_clean('9', '{$_REQUEST['size']}'); return false;" class="btn bg-slate-600 btn-sm btn-raised" type="button" value="{$lang['clean_skip']}">
HTML;
}

if ($_REQUEST['step'] == 5) {
$buffer = <<<HTML
{$lang['clean_users']}
<br /><br /><span style="color:red;"><span id="status"></span></span><br /><br />
		<input id = "next_button" onclick="start_clean('6', '{$_REQUEST['size']}'); return false;" class="btn bg-teal btn-sm btn-raised position-left" type="button" value="{$lang['edit_next']}">
		<input id = "skip_button" onclick="start_clean('7', '{$_REQUEST['size']}'); return false;" class="btn bg-slate-600 btn-sm btn-raised" type="button" value="{$lang['clean_skip']}">
HTML;
}

if ($_REQUEST['step'] == 3) {
$buffer = <<<HTML
{$lang['clean_comments']}<br /><br />{$lang['addnews_date']}&nbsp;<input data-rel="calendardate" type="text" name="date" id="f_date_c" class="form-control" style="width:190px;" autocomplete="off">
<script>
	$('[data-rel=calendardate]').datetimepicker({
	  format:'Y-m-d',
	  closeOnDateSelect:true,
	  dayOfWeekStart: 1,
	  timepicker:false,
	  scrollMonth:false,
	  scrollInput:false,
	  i18n: cal_language
	});
</script>
<br /><br /><span style="color:red;"><span id="status"></span></span><br /><br />
		<input id = "next_button" onclick="start_clean('4', '{$_REQUEST['size']}'); return false;" class="btn bg-teal btn-sm btn-raised position-left" type="button" value="{$lang['edit_next']}">&nbsp;
		<input id = "skip_button" onclick="start_clean('5', '{$_REQUEST['size']}'); return false;" class="btn bg-slate-600 btn-sm btn-raised" type="button" value="{$lang['clean_skip']}">
HTML;
}

if ($_REQUEST['step'] == 1) {

	$categories_list = CategoryNewsSelection( 0, 0 );
	
	
$buffer = <<<HTML
{$lang['clean_news']}<br /><br />{$lang['addnews_date']}&nbsp;<input data-rel="calendardate" type="text" name="date" id="f_date_c" class="form-control position-left" style="width:190px;" autocomplete="off"> <span class="position-left">{$lang['addnews_cat']}</span> <select data-placeholder="{$lang['addnews_cat_sel']}" title="{$lang['addnews_cat_sel']}" name="category[]" id="category" class="categoryselect" multiple style="width:100%;max-width:350px;">{$categories_list}</select>
<script>
	$('[data-rel=calendardate]').datetimepicker({
	  format:'Y-m-d',
	  closeOnDateSelect:true,
	  dayOfWeekStart: 1,
	  timepicker:false,
	  scrollMonth:false,
	  scrollInput:false,
	  i18n: cal_language
	});
	
	$('.categoryselect').chosen({no_results_text: '{$lang['addnews_cat_fault']}'});
	
</script>
<br /><br /><span style="color:red;"><span id="status"></span></span><br /><br />
		<input id = "next_button" onclick="start_clean('2', '{$_REQUEST['size']}'); return false;" class="btn bg-teal btn-sm btn-raised position-left" type="button" value="{$lang['edit_next']}">
		<input id = "skip_button" onclick="start_clean('3', '{$_REQUEST['size']}'); return false;" class="btn bg-slate-600 btn-sm btn-raised" type="button" value="{$lang['clean_skip']}">
HTML;
}


echo $buffer;

?>