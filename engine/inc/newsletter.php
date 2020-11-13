<?php
/*
=====================================================
DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
Copyright (c) 2004-2020
=====================================================
 This code is protected by copyright
=====================================================
 File: newsletter.php
-----------------------------------------------------
 Use: Sending newsletter messages
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['admin_newsletter'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if (isset ($_REQUEST['editor'])) $editor = htmlspecialchars( $_REQUEST['editor'], ENT_QUOTES, $config['charset'] ); else $editor = "";
if (isset ($_REQUEST['type'])) $type = htmlspecialchars( $_REQUEST['type'], ENT_QUOTES, $config['charset'] ); else $type = "";
if (isset ($_REQUEST['action'])) $action = htmlspecialchars( $_REQUEST['action'], ENT_QUOTES, $config['charset'] ); else $action = "";
if (isset ($_REQUEST['a_mail'])) $a_mail = intval($_REQUEST['a_mail']); else $a_mail = "";

if (isset ($_GET['empfanger'])) {

	$empfanger = array ();

	if( !count( $_GET['empfanger'] ) ) {
		$empfanger[] = '0';
	} else {

		foreach ( $_GET['empfanger'] as $value ) {
			$empfanger[] = intval($value);
		}

	}

	if ( $empfanger[0] ) $empfanger = $db->safesql( implode( ',', $empfanger ) ); else $empfanger = "0";

} else $empfanger = "0";

if ($action=="send") {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		msg( "error", $lang['addnews_error'], $lang['sess_error'], "javascript:history.go(-1)" );
	}
	
	include_once (DLEPlugins::Check(ENGINE_DIR.'/classes/parse.class.php'));

	$parse = new ParseFilter();

	$title = strip_tags(stripslashes($parse->process($_POST['title'])));
	$message = stripslashes($parse->process($_POST['message']));
	$start_from = intval($_GET['start_from']);
	$limit = intval($_GET['limit']);
	$interval = intval($_GET['interval']) * 1000;

	if ($limit < 1) {

		$limit = 20;

	}

	if ($editor == "wysiwyg"){

		$message = $parse->BB_Parse($message);

	} else {

		$message = $parse->BB_Parse($message, false);
	}

	
	if( isset($_GET['toregdate']) ) {
		
		$toregdate = intval(strtotime( (string)$_GET['toregdate'] ));
		
	} else $toregdate = 0;

	if( isset($_GET['fromregdate']) ) {
		
		$fromregdate = intval(strtotime( (string)$_GET['fromregdate'] ));
		
	} else $fromregdate = 0;	

	if( isset($_GET['fromentdate']) ) {
		
		$fromentdate = intval(strtotime( (string)$_GET['fromentdate'] ));
		
	} else $fromentdate = 0;	

	if( isset($_GET['toentdate']) ) {
		
		$toentdate = intval(strtotime( (string)$_GET['toentdate'] ));
		
	} else $toentdate = 0;
	
	$where = array();

	$where[] = "banned != 'yes'";

	if ($empfanger) {
	
		$user_list = array(); 
	
		$temp = explode(",", $empfanger); 
	
		foreach ( $temp as $value ) {
			$user_list[] = intval($value);
		}
	
		$user_list = implode( "','", $user_list );
	
		$user_list = "user_group IN ('" . $user_list . "')";
	
	} else $user_list = false;
	
	if( $fromregdate ) {
		$where[] = "reg_date>='" . $fromregdate . "'";
	}
	if( $toregdate ) {
		$where[] = "reg_date<='" . $toregdate . "'";
	}
	if( $fromentdate ) {
		$where[] = "lastdate>='" . $fromentdate . "'";
	}
	if( $toentdate ) {
		$where[] = "lastdate<='" . $toentdate . "'";
	}
	
	if ($user_list) $where[] = $user_list;
	if ($a_mail AND $type == "email") $where[] = "allow_mail = '1'";

	if (count($where)) $where = " WHERE ".implode (" AND ", $where);
	else $where = "";
	
	$row = $db->super_query("SELECT COUNT(*) as count FROM " . USERPREFIX . "_users".$where);

	if ($start_from > $row['count'] OR $start_from < 0) $start_from = 0;

	if ($type == "email")
		$type_send = $lang['bb_b_mail'];
	else
		$type_send = $lang['nl_pm'];

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '47', '{$type_send}')" );
	$css = build_css($css_array);

echo <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="{$config['charset']}">
	<title>DataLife Engine - {$lang['nl_seng']}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="HandheldFriendly" content="true">
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width"> 
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	{$css}
	<script src="engine/classes/js/jquery.js"></script>
</head>
<body class="p-20">
<script>
var total = {$row['count']};

	$(function() {

		$("#status").ajaxError(function(event, request, settings){
		   $(this).html('{$lang['nl_error']}');
			$('#button').attr("disabled", false);

			var startagain = parseInt($('#sendet_ok').val());
			startagain = startagain + {$limit};

			$('#sendet_ok').val( startagain );

		 });

		$('#button').click(function() {
			$('#status').html('{$lang['nl_sinfo']}');
			$('#button').attr("disabled", "disabled");
			$('#button').val("{$lang['send_forw']}");

			senden( $('#sendet_ok').val() );
			return false;
		});
		
		if(total == 0) {
			$('#button').attr("disabled", "disabled");
		}

	});

function senden( startfrom ){

	var title = $('#title').html();
	var message = $('#message').html();

	$.post("engine/ajax/controller.php?mod=newsletter", { startfrom: startfrom, title: title, message: message, user_hash: '{$dle_login_hash}', type: '{$type}', empfanger: '{$empfanger}', a_mail: '{$a_mail}', limit: '{$limit}', fromregdate: '{$fromregdate}', toregdate: '{$toregdate}', fromentdate: '{$fromentdate}', toentdate: '{$toentdate}'  },
		function(data){

			if (data) {

				if (data.status == "ok") {

					$('#gesendet').html(data.count);
					$('#sendet_ok').val(data.count);

					var proc = Math.round( (100 * data.count) / total );

					if ( proc > 100 ) proc = 100;

					$('.progress-bar').css( "width", proc + '%' );

			         if (data.count >= total || data.complete == 1) 
			         {
			              $('#status').html('{$lang['nl_finish']}');
			         }
			         else 
			         { 
			              setTimeout("senden(" + data.count + ")", {$interval} );
			         }


				}

			}
		}, "json");

	return false;
}
</script>
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['nl_seng']}
  </div>
  <div class="panel-body">

<table width="100%">
    <tr>
        <td width="120">{$lang['nl_empf']}</td>
        <td>{$row['count']}</td>
    </tr>
    <tr>
        <td>{$lang['nl_type']}</td>
        <td>{$type_send}</td>
    </tr>
    <tr>
        <td colspan="2">
		<div class="progress">
          <div class="progress-bar progress-blue" style="width:0%;"><span></span></div>
        </div>
		{$lang['nl_sendet']} <span style="color:red;" id='gesendet'>{$start_from}</span> {$lang['mass_i']} <span style="color:blue;">{$row['count']}</span> {$lang['nl_status']} <span id="status"></span>
		</td>
    </tr>
</table>	
	</div>
	<div class="panel-body text-muted text-size-small">
	{$lang['nl_info']}
	</div>	
	<div class="panel-footer">
	<button id="button" type="button" class="btn bg-teal btn-sm btn-raised"><i class="fa fa-paper-plane-o position-left"></i>{$lang['btn_send']}</button>
	<input type="hidden" id="sendet_ok" name="sendet_ok" value="{$start_from}">
	</div>	
</div>
HTML;

$message = stripslashes($message);

echo <<<HTML
<pre style="display:none;" id="title">{$title}</pre>
<pre style="display:none;" id="message">{$message}</pre>
</body>

</html>
HTML;

} elseif ($action=="preview") {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		msg( "error", $lang['addnews_error'], $lang['sess_error'], "javascript:history.go(-1)" );
	}
	
	include_once (DLEPlugins::Check(ENGINE_DIR.'/classes/parse.class.php'));
	
	$parse = new ParseFilter();
	
	$title = strip_tags(stripslashes($parse->process($_POST['title'])));
	$message = stripslashes($parse->process($_POST['message']));
	
	if ($editor == "wysiwyg"){
		$message = $parse->BB_Parse($message);
	} else {
		$message = $parse->BB_Parse($message, false);
	}

echo <<<HTML
<html><title>{$title}</title>
<meta content="text/html; charset={$config['charset']}" http-equiv=Content-Type>
<style type="text/css">
html,body{
height:100%;
margin:0px;
padding: 0px;
font-size: 11px;
font-family: verdana;
}
p {
margin:0px;
padding: 0px;
}
table{
border:0px;
border-collapse:collapse;
}

table td{
padding:0px;
font-size: 11px;
font-family: verdana;
}

a:active,
a:visited,
a:link {
	color: #4b719e;
	text-decoration:none;
	}

a:hover {
	color: #4b719e;
	text-decoration: underline;
	}
</style>
<body>
HTML;

echo "<fieldset style=\"border-style:solid; border-width:1; border-color:black;\"><legend> <span style=\"font-size: 10px; font-family: Verdana\">{$title}</span> </legend>{$message}</fieldset>";


} elseif ($action=="message") {

	if( $_REQUEST['editor'] != "wysiwyg" ) {
		$js_array[] = "engine/classes/js/typograf.min.js";
	}
	
	if( $_REQUEST['editor'] == "wysiwyg") {
		$js_array[] = "engine/editor/jscripts/tiny_mce/tinymce.min.js";
	}
	echoheader( "<i class=\"fa fa-envelope-o position-left\"></i><span class=\"text-semibold\">{$lang['main_newsl']}</span>", $lang['header_ne_1'] );


    echo "
    <script>
    function send(){";

	if ($editor == "wysiwyg"){
	echo "tinyMCE.triggerSave();";
	}

	echo "if(document.addnews.message.value == '' || document.addnews.title.value == ''){ DLEalert('$lang[vote_alert]', '$lang[p_info]'); }
    else{
        dd=window.open('','snd','height=350,width=640,resizable=1,scrollbars=1')
        document.addnews.action.value='send';document.addnews.target='snd'
        document.addnews.submit();dd.focus()
    }
    }
    </script>";

    echo "
    <script>
    function preview(){";

	if ($editor == "wysiwyg"){
	echo "tinyMCE.triggerSave();";
	}

	echo "if(document.addnews.message.value == '' || document.addnews.title.value == ''){ DLEalert('$lang[vote_alert]', '$lang[p_info]'); }
    else{
        dd=window.open('','prv','height=340,width=600,resizable=1,scrollbars=1')
        document.addnews.action.value='preview';document.addnews.target='prv'
        document.addnews.submit();dd.focus()
        setTimeout(\"document.addnews.action.value='send';document.addnews.target='_self'\",500)
    }
    }
    </script>";

	$start_from = intval($_GET['start_from']);

echo <<<HTML
<form method="POST" name="addnews" id="addnews" action="" class="form-horizontal">
<input type="hidden" name="mod" value="newsletter">
<input type="hidden" name="action" value="send">
<input type="hidden" name="type" value="{$type}">
<input type="hidden" name="a_mail" value="{$a_mail}">
<input type="hidden" name="editor" value="{$editor}">
<input type="hidden" name="start_from" value="{$start_from}">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<div class="alert alert-info alert-styled-left alert-arrow-left alert-component text-size-small">{$lang['nl_info_1']} {$lang['nl_info_2']}</div>
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['nl_main']}
	<div class="heading-elements">
	    <ul class="icons-list">
			<li><a href="#" class="panel-fullscreen"><i class="fa fa-expand"></i></a></li>
		</ul>
    </div>
  </div>
  <div class="panel-body">
	
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['edit_title']}</label>
		  <div class="col-md-10">
			<input type="text" class="form-control width-550" name="title" maxlength="160">
		  </div>
		 </div>	
		<div class="form-group editor-group">
		  <label class="control-label col-md-2">{$lang['nl_message']}</label>
		  <div class="col-md-10">
HTML;

	if( $_REQUEST['editor'] == "wysiwyg" ) {
		
		include(DLEPlugins::Check(ENGINE_DIR.'/editor/newsletter.php'));
	
	} else {

		$bb_editor = true;
		include (DLEPlugins::Check(ENGINE_DIR . '/inc/include/inserttag.php'));
		echo "<div class=\"editor-panel\"><div class=\"shadow-depth1\">{$bb_code}<textarea class=\"editor\" style=\"width:100%;height:300px;\" onfocus=\"setFieldName(this.name)\" name=\"message\" id=\"message\" ></textarea></div></div><script>var selField  = \"message\";</script>";
	}

echo <<<HTML
		  </div>
		</div>
	
   </div>
   <div class="panel-footer">
	<button type="button" onclick="send(); return false;" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-paper-plane-o position-left"></i>{$lang['btn_send']}</button>
	<button onclick="preview(); return false;" class="btn bg-slate-600 btn-sm btn-raised"><i class="fa fa-desktop position-left"></i>{$lang['btn_preview']}</button>
   </div>
</div>		
</form>
HTML;

  echofooter();
} else {

	echoheader( "<i class=\"fa fa-envelope-o position-left\"></i><span class=\"text-semibold\">{$lang['main_newsl']}</span>", $lang['header_ne_1'] );
	$group_list = get_groups ();

echo <<<HTML
<form method="GET" action="" class="form-horizontal">
<input type="hidden" name="mod" value="newsletter">
<input type="hidden" name="action" value="message">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['nl_main']}
  </div>
  <div class="panel-body">
	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['nl_type']}</label>
		  <div class="col-md-10 col-sm-9">
			<select class="uniform" name="type">
           <option value="email">{$lang['bb_b_mail']}</option>
          <option value="pm">{$lang['nl_pm']}</option></select>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['nl_empf']}</label>
		  <div class="col-md-10 col-sm-9">
			<select data-placeholder="{$lang['group_select_1']}" name="empfanger[]" class="empfangerselect" multiple>
           <option value="all" selected>{$lang['edit_all']}</option>
           {$group_list}
		   </select>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['edit_regdate']}</label>
		  <div class="col-md-10 col-sm-9">
			{$lang['edit_fdate']}&nbsp;<input data-rel="calendardate" type="text" name="fromregdate" id="fromregdate" class="form-control" style="width:130px;" value="" autocomplete="off">
			{$lang['edit_tdate']}&nbsp;<input data-rel="calendardate" type="text" name="toregdate" id="toregdate" class="form-control" style="width:130px;" value="" autocomplete="off">
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['edit_entedate']}</label>
		  <div class="col-md-10 col-sm-9">
			{$lang['edit_fdate']}&nbsp;<input data-rel="calendardate" type="text" name="fromentdate" id="fromentdate" class="form-control" style="width:130px;" value="" autocomplete="off">
			{$lang['edit_tdate']}&nbsp;<input data-rel="calendardate" type="text" name="toentdate" id="toentdate" class="form-control" style="width:130px;" value="" autocomplete="off">
		   </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['nl_editor']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="editor" class="uniform">
           <option value="bbcodes">BBCODES</option>
          <option value="wysiwyg">WYSIWYG</option></select>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['nl_startfrom']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" class="form-control text-center" style="width:60px;" name="start_from" value="0"> {$lang['nl_user']}
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['nl_n_mail']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" class="form-control text-center" style="width:60px;" name="limit" value="20">
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['nl_interval']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" class="form-control text-center" style="width:60px;" name="interval" value="3">
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"></label>
		  <div class="col-md-10 col-sm-9">
			<div class="checkbox"><label><input type="checkbox" name="a_mail" value="1" class="icheck" checked>{$lang['nl_amail']}</label></div>
		  </div>
		 </div>
	
   </div>
   <div class="panel-footer">
	<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-step-forward position-left"></i>{$lang['edit_next']}</button>
   </div>
</div>
</form>
<script>
$(function(){
$('.empfangerselect').chosen({allow_single_deselect:true, no_results_text: '{$lang['addnews_cat_fault']}'});
});
</script>
HTML;

  echofooter();
}
?>