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
 Use: cleaning and optimizing the database
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if($member_id['user_group'] != 1){ msg("error", $lang['addnews_denied'], $lang['db_denied']); }

$db->query("SHOW TABLE STATUS FROM `".DBNAME."`");
			$mysql_size = 0;
			while ($r = $db->get_array()) {
			if (strpos($r['Name'], PREFIX."_") !== false)
			$mysql_size += $r['Data_length'] + $r['Index_length'] ;
			}
$db->free();

$lang['clean_all'] = str_replace ('{datenbank}', '<span class="text-danger">'.formatsize($mysql_size).'</span>', $lang['clean_all']);

echoheader( "<i class=\"fa fa-briefcase position-left\"></i><span class=\"text-semibold\">{$lang['header_opt_1']}</span>", $lang['clean_title']);

echo <<<HTML
<script>
<!--
function start_clean ( step, size ){

	$("#status").html('{$lang['ajax_info']}');

	if (document.getElementById( 'f_date_c' )) {
		var date = document.getElementById( 'f_date_c' ).value;
	} else { var date = ''; }
	
	if( document.getElementById( 'category' ) ) {
		var category = $("#category").val();
	} else { var category = []; }
	
	var unreadpm = 0;
	if( document.getElementById( 'unreadpm' ) ) {
		if( document.getElementById('unreadpm').checked ) {
			unreadpm = 1;
		}
	}
	
	if (document.getElementById( 'next_button' )) {
		document.getElementById( 'next_button' ).disabled = true;
	}
	if (document.getElementById( 'skip_button' )) {
		document.getElementById( 'skip_button' ).disabled = true;
	}

	$.get("engine/ajax/controller.php?mod=clean", { step: step, date: date, category: category, unreadpm: unreadpm, size: size, user_hash: "{$dle_login_hash}" }, function(data){

	  $('#main_box').html(data);

	}, 'html');

	return false;
}
//-->
</script>
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['clean_title']}
  </div>
  <div class="panel-body">
	
	<div id="main_box">{$lang['clean_all']}<br /><br /><span class="text-danger"><span id="status"></span></span><br /><br />
		<button type="button" class="btn bg-teal btn-sm btn-raised" onclick="start_clean('1', '{$mysql_size}'); return false;"><i class="fa fa-step-forward position-left"></i>{$lang['edit_next']}</button>
	</div>

	
   </div>
</div>
HTML;


echofooter();
?>