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
 File: blockip.php
-----------------------------------------------------
 Use: Blocking visitors by IP
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['admin_blockip'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if( isset( $_REQUEST['ip_add'] ) ) $ip_add = htmlspecialchars( strip_tags( trim( $_REQUEST['ip_add'] ) ), ENT_QUOTES, $config['charset'] ); else $ip_add = "";
if( isset( $_REQUEST['ip'] ) ) $ip = htmlspecialchars( strip_tags( trim( $_REQUEST['ip'] ) ), ENT_QUOTES, $config['charset'] ); else $ip = "";
if( isset( $_REQUEST['id'] ) ) $id = intval( $_REQUEST['id'] ); else $id = 0;

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));
	
$parse = new ParseFilter();
$parse->safe_mode = true;
	
if ($_REQUEST['action'] == "mass_delete") {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}

	if( !$_POST['selected_ips'] ) {
		msg( "error", $lang['mass_error'], $lang['ip_sel_error'], "?mod=blockip" );
	}
	
	foreach ( $_POST['selected_ips'] as $id ) {
		$id = intval($id);
		$db->query( "DELETE FROM " . USERPREFIX . "_banned WHERE id = '{$id}'" );
	}
	
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '10', '')" );
	
	@unlink( ENGINE_DIR . '/cache/system/banned.php' );
	
	header( "Location: ?mod=blockip" );
	die();
	
	
}

if ($_REQUEST['action'] == "edit") {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}

	$id = intval ( $_POST['id'] );
	$ip_add = $db->safesql(trim($ip_add));
	$banned_descr = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['descr'] ), false ) );

	if( !trim( $_POST['date'] ) OR (($_POST['date'] = strtotime( $_POST['date'] )) === - 1) OR !$_POST['date']) {
		$this_time = 0;
		$days = 0;
	} else {
		$this_time = $db->safesql($_POST['date']);
		$days = 1;
	}
	
	if( !$ip_add ) {
		msg( "error", $lang['ip_error'], $lang['ip_error'], "?mod=blockip" );
	}
	
	$db->query( "UPDATE " . USERPREFIX . "_banned SET `descr`='{$banned_descr}', `date`='{$this_time}', `days`='{$days}', `ip`='{$ip_add}' WHERE id='{$id}'" );
	
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '118', '{$ip_add}')" );
	
	@unlink( ENGINE_DIR . '/cache/system/banned.php' );
	
	header( "Location: ?mod=blockip" );
	die();
	
}

if( $_REQUEST['action'] == "add" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	$banned_descr = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['descr'] ), false ) );
	
	if( !trim( $_POST['date'] ) OR (($_POST['date'] = strtotime( $_POST['date'] )) === - 1) OR !$_POST['date']) {
		$this_time = 0;
		$days = 0;
	} else {
		$this_time = $db->safesql($_POST['date']);
		$days = 1;
	}
	
	if( !$ip_add ) {
		msg( "error", $lang['ip_error'], $lang['ip_error'], "?mod=blockip" );
	}

	$ips = explode("\n", $ip_add);
	
	foreach ($ips as $ip_add) {
		$ip_add = $db->safesql(trim($ip_add));
		
		if($ip_add) {
			$row = $db->super_query( "SELECT id FROM " . PREFIX . "_banned WHERE ip ='{$ip_add}'" );
			
			if ( !$row['id'] ) {
				$db->query( "INSERT INTO " . USERPREFIX . "_banned (descr, date, days, ip) values ('$banned_descr', '$this_time', '$days', '$ip_add')" );
			}
		}
		
	}
	
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '9', '')" );
	
	@unlink( ENGINE_DIR . '/cache/system/banned.php' );
	
	header( "Location: ?mod=blockip" );
	die();
	
} elseif( $_REQUEST['action'] == "delete" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( !$id ) {
		msg( "error", $lang['ip_error'], $lang['ip_error'], "?mod=blockip" );
	}
	
	$db->query( "DELETE FROM " . USERPREFIX . "_banned WHERE id = '{$id}'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '10', '')" );

	@unlink( ENGINE_DIR . '/cache/system/banned.php' );
	
	header( "Location: ?mod=blockip" );
	die();

}

echoheader( "<i class=\"fa fa-lock position-left\"></i><span class=\"text-semibold\">{$lang['opt_ipban']}</span>", $lang['header_filter_1'] );

echo <<<HTML
<div class="modal fade" id="newblock" tabindex="-1" role="dialog" aria-labelledby="newblockLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
	<form method="post" action="" class="form-horizontal">
	<input type="hidden" name="mod" value="blockip">
	<input type="hidden" name="action" value="add">
	<input type="hidden" name="user_hash" value="{$dle_login_hash}">
      <div class="modal-header ui-dialog-titlebar">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<span class="ui-dialog-title" id="newcatsLabel">{$lang['ip_add']}</span>
      </div>
      <div class="modal-body">

		<div class="form-group">
		  <label class="control-label col-sm-4">{$lang['ip_type']}</label>
		  <div class="col-sm-8">
		    <textarea  class="classic width-350" rows="5" name="ip_add">{$ip}</textarea>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-sm-4">{$lang['ban_date']}</label>
		  <div class="col-sm-8">
			<input  class="form-control" style="width:190px;" data-rel="calendar" type="text" name="date" autocomplete="off">
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-sm-4">{$lang['ban_descr']}</label>
		  <div class="col-sm-8">
			<textarea  class="classic width-350" rows="5" name="descr"></textarea>
		  </div>
		 </div>
	  
		<div class="text-muted text-size-small">{$lang['ip_example']}</div>
	  
      </div>
      <div class="modal-footer" style="margin-top:-20px;">
	    <button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
        <button type="button" class="btn bg-slate-600 btn-sm btn-raised" data-dismiss="modal">{$lang['p_cancel']}</button>
      </div>
	  </form>
    </div>
  </div>
</div>
HTML;

echo <<<HTML
<form action="?mod=blockip" method="post" name="optionsbar" id="optionsbar">
<input type="hidden" name="mod" value="blockip">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['ip_list']}
	<div class="heading-elements">
		<ul class="icons-list">
			<li><a href="#" data-toggle="modal" data-target="#newblock"><i class="fa fa-plus-circle position-left"></i>{$lang['news_add']}</a></li>
		</ul>
	</div>
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-xs table-hover">
      <thead>
      <tr>
        <th style="width: 200px">{$lang['title_filter']}</th>
        <th style="width: 190px">{$lang['ban_date']}</th>
        <th>{$lang['ban_descr']}</th>
        <th style="width: 70px">&nbsp;</th>
		<th style="width: 40px"><input class="icheck" type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all()"></th>
      </tr>
      </thead>
	  <tbody>
HTML;

$db->query( "SELECT * FROM " . USERPREFIX . "_banned WHERE users_id = '0' ORDER BY id DESC" );

$i = 0;
if( !$langformatdatefull ) $langformatdatefull = "d.m.Y H:i";
while ( $row = $db->get_row() ) {
	$i ++;
	
	if( $row['date'] ) {
		$endban = langdate( $langformatdatefull, $row['date'] );
		$editendban = date( "Y-m-d H:i:s", $row['date'] );
	} else {
		$endban = $lang['banned_info'];
		$editendban = "";
	}
	
	$row['edit_descr'] = $parse->decodeBBCodes( $row['descr'], false );
	
	$menu_link = <<<HTML
        <div class="btn-group">
          <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
          <ul class="dropdown-menu text-left dropdown-menu-right">
            <li><a uid="{$row['id']}" href="?mod=blockip" class="editlink"><i class="fa fa-pencil-square-o position-left"></i>{$lang['word_ledit']}</a></li>
			<li class="divider"></li>
            <li><a href="?mod=blockip&action=delete&id={$row['id']}&user_hash={$dle_login_hash}"><i class="fa fa-trash-o position-left text-danger"></i>{$lang['ip_unblock']}</a></li>
          </ul>
        </div>
HTML;

	echo "<tr>
			<td id=\"content_{$row['id']}\" data-date=\"{$editendban}\">{$row['ip']}</td>
			<td>{$endban}</td>
			<td>" . stripslashes( $row['descr'] ) . "<textarea id=\"descr_{$row['id']}\" style=\"display:none;\">{$row['edit_descr']}</textarea></td>
			<td>{$menu_link}</td>
			<td><input name=\"selected_ips[]\" value=\"{$row['id']}\" type=\"checkbox\" class=\"icheck\"></td>
		 </tr>";
}

if( $i == 0 ) {
	echo "<tr>
     <td height=\"18\" colspan=\"5\"><p align=\"center\"><br><b>{$lang['ip_empty']}<br><br></b></td>
    </tr>";
}

echo <<<HTML
	  </tbody>
	</table>
  </div>
<div class="panel-footer">
	<button class="btn bg-teal btn-sm btn-raised position-left" onclick="$('#newblock').modal(); return false;"><i class="fa fa-plus-circle position-left"></i>{$lang['news_add']}</button>
	<div class="pull-right">
	<select class="uniform position-left" name="action">
	<option value="">{$lang['edit_selact']}</option>
	<option value="mass_delete">{$lang['ip_unblock']}</option>
	</select><input class="btn bg-brown-600 btn-sm btn-raised" type="submit" value="{$lang['b_start']}">
	</div>
</div>
</div>
</form>
<script>  
<!--
	$(function() {
		$('.table').find('tr > td:last-child').find('input[type=checkbox]').on('change', function() {
			if($(this).is(':checked')) {
				$(this).parents('tr').addClass('warning');
			}
			else {
				$(this).parents('tr').removeClass('warning');
			}
		});
		
		$('.editlink').click(function(){

			var ip = $('#content_'+$(this).attr('uid')).text();
			ip = ip.replace(/'/g, "&#039;");
			var ipid = $(this).attr('uid');
			var description = $('#descr_'+$(this).attr('uid')).val();
			var date = $('#content_'+$(this).attr('uid')).data('date');
			
			var b = {};
		
			b[dle_act_lang[3]] = function() { 
							$(this).dialog("close");						
					    };
		
			b[dle_act_lang[2]] = function() { 
						if ( $("#dle-promt-ip").val().length < 1) {
							 $("#dle-promt-ip").addClass('ui-state-error');
						} else {
							$("#editip").submit();
						}				
					};
	
			$("#dlepopup").remove();

			$("body").append("<div id='dlepopup' title='{$lang['ip_add']}' style='display:none'><form id='editip' method='post'><input type='hidden' name='id' value='"+ipid+"'><input type='hidden' name='mod' value='blockip'><input type='hidden' name='action' value='edit'><input type='hidden' name='user_hash' value='{$dle_login_hash}'>{$lang['title_filter']}<br><input type='text' name='ip_add' id='dle-promt-ip' class='classic' style='width:100%;' value='"+ip+"'/><br><br>{$lang['ban_date']}<br /><input type='text' name='date' class='form-control' data-rel='calendar' style='width:190px;' value='"+date+"' autocomplete='off'><br><br>{$lang['ban_descr']}<br><textarea name='descr' class='classic' style='width:100%;' rows='5'>"+description+"</textarea></form></div>");
		
			$('#dlepopup').dialog({
				autoOpen: true,
				width: 600,
				resizable: false,
				buttons: b,
				open: function( event, ui ) {
					$('#dlepopup [data-rel=calendar]').datetimepicker({
					  format:'Y-m-d H:i:s',
					  step: 30,
					  closeOnDateSelect:true,
					  dayOfWeekStart: 1,
					  scrollMonth:false,
					  scrollInput:false,
					  i18n: cal_language
					});
				}
			});

			return false;
		});
		
	});
	
	function ckeck_uncheck_all() {
	    var frm = document.optionsbar;
	    for (var i=0;i<frm.elements.length;i++) {
	        var elmnt = frm.elements[i];
	        if (elmnt.type=='checkbox') {
	            if(frm.master_box.checked == true){ elmnt.checked=false; $(elmnt).parents('tr').removeClass('warning'); }
	            else{ elmnt.checked=true; $(elmnt).parents('tr').addClass('warning');}
	        }
	    }
	    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
	    else{ frm.master_box.checked = true; }
		
		$(frm.master_box).parents('tr').removeClass('warning');
		
		$.uniform.update();
	
	}
	
//-->
</script>
HTML;

echofooter();
?>