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
 File: search.php
-----------------------------------------------------
 Use: search and replace text in database
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if($member_id['user_group'] != 1){ msg("error", $lang['addnews_denied'], $lang['db_denied']); }

if ($_POST['action'] == "replace") {

	if ($_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash) {

		  die("Hacking attempt! User not found");

	}

	$find = $db->safesql(addslashes(trim($_POST['find'])));
	$replace = $db->safesql(addslashes(trim($_POST['replace'])));

	$find_2 = $db->safesql(trim($_POST['find']));
	$replace_2 = $db->safesql(trim($_POST['replace']));
	
	if ($find == "" OR !count($_POST['table'])) {
		msg("error",$lang['addnews_error'],$lang['vote_alert'], "javascript:history.go(-1)");
	}

	if (in_array("news", $_POST['table'])) {
		$db->query("UPDATE `" . PREFIX . "_post` SET `short_story`=REPLACE(`short_story`,'$find','$replace')");
		$db->query("UPDATE `" . PREFIX . "_post` SET `full_story`=REPLACE(`full_story`,'$find','$replace')");
		$db->query("UPDATE `" . PREFIX . "_post` SET `xfields`=REPLACE(`xfields`,'$find','$replace')");
	}

	if (in_array("comments", $_POST['table'])) {
		$db->query("UPDATE `" . PREFIX . "_comments` SET `text`=REPLACE(`text`,'$find','$replace')");
	}

	if (in_array("pm", $_POST['table'])) {
		$db->query("UPDATE `" . USERPREFIX . "_pm` SET `text`=REPLACE(`text`,'$find','$replace')");
	}

	if (in_array("static", $_POST['table'])) {
		$db->query("UPDATE `" . PREFIX . "_static` SET `template`=REPLACE(`template`,'$find','$replace')");

	}

	if (in_array("tags", $_POST['table'])) {
		$db->query("UPDATE `" . PREFIX . "_tags` SET `tag`=REPLACE(`tag`,'$find','$replace')");
		$db->query("UPDATE `" . PREFIX . "_post` SET `tags`=REPLACE(`tags`,'$find','$replace')");
    }
	 
	if (in_array("banners", $_POST['table'])) {
		$db->query("UPDATE `" . PREFIX . "_banners` SET `code`=REPLACE(`code`,'$find_2','$replace_2')");
	}
	
	if (in_array("polls", $_POST['table'])) {
		$db->query("UPDATE `" . USERPREFIX . "_poll` SET `body`=REPLACE(`body`,'$find','$replace')");
		$db->query("UPDATE `" . USERPREFIX . "_vote` SET `body`=REPLACE(`body`,'$find','$replace')");
	}
	
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '58', '".htmlspecialchars("find: ".$find." replace: ".$replace, ENT_QUOTES, $config['charset'])."')" );

	clear_cache ();
	msg("success", $lang['find_done_h'], $lang['find_done'], "?mod=search");

}


echoheader( "<i class=\"fa fa-exchange position-left\"></i><span class=\"text-semibold\">{$lang['opt_sfind']}</span>", $lang['find_main']);

echo <<<HTML
<form action="" method="post" class="form-horizontal">
<input type="hidden" name="action" value="replace">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['find_main']}
  </div>

	<div class="panel-body">
		{$lang['find_info']}
	</div>
	<div class="panel-body">
	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['find_ftable']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="table[]" class="categoryselect" data-placeholder=" " title=" " multiple>
				<option value="news" selected>{$lang['find_rnews']}</option>
				<option value="comments" selected>{$lang['find_rcomms']}</option>
				<option value="pm" selected>{$lang['find_rpm']}</option>
				<option value="static" selected>{$lang['find_rstatic']}</option>
				<option value="polls" selected>{$lang['find_rpolls']}</option>
				<option value="tags" selected>{$lang['find_rtags']}</option>
				<option value="banners" selected>{$lang['find_rbanners']}</option>
		</select>
		   </div>
	    </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['find_ftext']}</label>
		  <div class="col-md-10 col-sm-9">
			<textarea name="find" class="classic width-450" style="height:150px;"></textarea>
		   </div>
	    </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['find_rtext']}</label>
		  <div class="col-md-10 col-sm-9">
			<textarea name="replace" class="classic width-450" style="height:150px;"></textarea>
		   </div>
	    </div>
	
	</div>
	<div class="panel-footer">
		<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-exchange position-left"></i>{$lang['find_rstart']}</button>
	</div>
</div>
</form>
<script>
	$(function(){

		$('.categoryselect').chosen({no_results_text: '{$lang['addnews_cat_fault']}'});

	});
</script>
HTML;


echofooter();
?>