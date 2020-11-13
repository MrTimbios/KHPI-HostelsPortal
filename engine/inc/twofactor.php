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
 File: twofactor.php
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( $is_loged_in ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if( !$_SESSION['twofactor_auth'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

$year = date('Y', time());

$skin_login = <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>DataLife Engine - {$lang['skin_title']}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="HandheldFriendly" content="true">
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width"> 
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
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
        {$lang['skin_title']} DataLife Engine
      </div>
	  
      <div class="panel-body">
		{$lang['twofactor_alert']}
		<br /><br /><input type="text" name="pin" id="pin" class="classic" style="width:100%" placeholder="{$lang['twofactor_title']}">
		<div id="twofactor_response" style="color:red"></div>
		<br /><div class="form-group">
			<button id="send" type="submit" class="btn btn-primary btn-raised btn-block">{$lang['login_button']} <i class="fa fa-sign-in"></i></button>
          </div>
      </div>
    </div>
	
<script>  
<!--
$(function(){

	$('#send').click(function(){
	
		if ( $("#pin").val().length < 1) {
			 $("#pin").addClass('ui-state-error');
		} else {
			var pin = $("#pin").val();
			$.post("engine/ajax/controller.php?mod=twofactor", { pin: pin }, function(data){
			
				if ( data.success ) {
				
					window.location = window.location;
					
				} else if (data.error) {
					
					$("#twofactor_response").html(data.errorinfo);
					
				}
				
			}, "json");
		
		}
		
		return false;
	
	});
});
//-->
</script>
	<div class="text-muted text-size-small text-center">DataLife Engine&reg;  Copyright 2004-{$year}<br>&copy; <a href="https://dle-news.ru/" target="_blank">SoftNews Media Group</a> All rights reserved.</div>
  </div>
</div>
</div>

</body>
</html>
HTML;

	$skin_login = str_replace( "{js_files}", build_js($js_array), $skin_login );
	$skin_login = str_replace( "{css_files}", build_css($css_array), $skin_login );
	
	echo $skin_login;

?>