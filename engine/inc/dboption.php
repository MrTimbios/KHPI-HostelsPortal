<?PHP
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
 File: dboption.php
-----------------------------------------------------
 Use: DB manage
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( $member_id['user_group'] != 1 ) {
	msg( "error", $lang['addnews_denied'], $lang['db_denied'] );
}

if( isset( $_REQUEST['restore'] ) ) $restore = $_REQUEST['restore']; else $restore = "";

if( $action == "dboption" AND is_array($_REQUEST['ta']) AND count( $_REQUEST['ta'] ) ) {
	$arr = $_REQUEST['ta'];
	reset( $arr );

	$tables = "";
	
	foreach ($arr as $val ) {
		$tables .= ", `" . $db->safesql( $val ) . "`";
	}
	
	$tables = substr( $tables, 1 );
	
	if( $_REQUEST['whattodo'] == "optimize" ) {
		
		$row = $db->super_query("SHOW TABLE STATUS WHERE Name = '" . PREFIX . "_post'");
		$storage_engine = $row['Engine'];
		
		if ( strtolower($storage_engine) == "innodb" ) {
			$query = "ANALYZE TABLE  ";
		} else $query = "OPTIMIZE TABLE  ";

	} else {
		$query = "REPAIR TABLE ";
	}
	$query .= $tables;

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '23', '')" );

	
	if( $db->query( $query ) ) {
		msg( "success", $lang['db_ok'], $lang['db_ok_1'], "?mod=dboption" );
	} else {
		msg( "error", $lang['db_err'], $lang['db_err_1'], "?mod=dboption" );
	}

}

echoheader( "<i class=\"fa fa-hdd-o position-left\"></i><span class=\"text-semibold\">{$lang['opt_db']}</span>", $lang['db_info'] );

$tabellen = "";

$db->query( "SHOW TABLES" );
while ( $row = $db->get_array() ) {
	$titel = $row[0];
	if( substr( $titel, 0, strlen( PREFIX ) ) == PREFIX ) {
		$tabellen .= "<option value=\"{$titel}\" selected>{$titel}</option>\n";
	}
}
$db->free();

echo <<<HTML
<form action="?mod=dboption&action=dboption" method="post">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['db_info']}
  </div>
  <div class="panel-body">
	
	  <div class="col-md-3">
		<select style="width:100%;" size="7" name="ta[]" multiple="multiple">{$tabellen}</select>
		<br /><br /><input type="submit" id="rest" class="btn bg-slate-600 btn-sm btn-raised" value="{$lang['db_action']}" />
	  </div>
	  
	  <div class="col-md-9">
		<table width="100%">
          <tr>
            <td style="width:70px;"><i class="fa fa-retweet" style="font-size:500%"></i></td>
            <td width="5%" nowrap="nowrap"><div align="left">
                <input style="border:0px" type="radio" name="whattodo" checked="checked" value="optimize" class="icheck" />
              </div></td>
            <td class="option"><h5 class="text-semibold">{$lang['db_opt']}</h5><span class="text-muted text-size-small">{$lang['db_opt_i']}</span></td>
          </tr>
          <tr>
            <td><i class="fa fa-magic" style="font-size:400%"></i></td>
            <td width="5%" nowrap="nowrap"><div align="left">
                <input style="border:0px" type="radio" name="whattodo" value="repair" class="icheck" />
              </div></td>
            <td class="option"><h5 class="text-semibold">{$lang['db_re']}</h5><span class="text-muted text-size-small">{$lang['db_re_i']}</span></td>
          </tr>
        </table>
		
	  </div>
	
   </div>
</div>
</form>
HTML;

if( function_exists( "bzopen" ) ) {
	$comp_methods[2] = 'BZip2';
}
if( function_exists( "gzopen" ) ) {
	$comp_methods[1] = 'GZip';
}
$comp_methods[0] = $lang['opt_notcompress'];

function fn_select($items, $selected) {
	$select = '';
	foreach ( $items as $key => $value ) {
		$select .= $key == $selected ? "<OPTION VALUE='{$key}' SELECTED>{$value}" : "<OPTION VALUE='{$key}'>{$value}";
	}
	return $select;
}
$comp_methods = fn_select( $comp_methods, '' );

echo <<<HTML
<script>
    function save(){

		var rndval = new Date().getTime(); 

		$('body').append('<div id="modal-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #666666; opacity: .40;filter:Alpha(Opacity=40); z-index: 999; display:none;"></div>');
		$('#modal-overlay').css({'filter' : 'alpha(opacity=40)'}).fadeIn('slow');
	
		$("#dlepopup").remove();
		$("body").append("<div id='dlepopup' title='{$lang['db_back']}' style='display:none'></div>");
	
		$('#dlepopup').dialog({
			autoOpen: true,
			width: 540,
			height: 345,
			resizable: false,
			dialogClass: "modalfixed",
			buttons: {
				"Ok": function() { 
					$(this).dialog("close");
					$("#dlepopup").remove();							
				} 
			},
			open: function(event, ui) { 
				$("#dlepopup").html("<iframe width='100%' height='220' src='?mod=dumper&user_hash={$dle_login_hash}&action=backup&comp_method=" + $("#comp_method").val() + "&rndval=" + rndval + "' frameborder='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>");
			},
			beforeClose: function(event, ui) { 
				$("#dlepopup").html("");
			},
			close: function(event, ui) {
					$('#modal-overlay').fadeOut('slow', function() {
			        $('#modal-overlay').remove();
			    });
			 }

		});

		if ($(window).width() > 830 && $(window).height() > 530 ) {
			$('.modalfixed.ui-dialog').css({position:"fixed"});
			$( '#dlepopup').dialog( "option", "position", { my: "center", at: "center", of: window } );
		}

		return false;

    }
</script>
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['db_back']}
  </div>
  <div class="panel-body">
	
		{$lang['b_method']}&nbsp;&nbsp;<select class="uniform" name="comp_method" id="comp_method">{$comp_methods}</select>&nbsp;&nbsp;<input type="button" class="btn bg-teal btn-sm btn-raised" onclick="save(); return false;" value="{$lang['b_save']}" />
	
   </div>
</div>
HTML;

define( 'PATH', 'backup/' );

function file_select() {
	$files = array ('' );
	if( is_dir( PATH ) && $handle = opendir( PATH ) ) {
		while ( false !== ($file = readdir( $handle )) ) {
			if( preg_match( "/^.+?\.sql(\.(gz|bz2))?$/", $file ) ) {
				$files[$file] = $file;
			}
		}
		closedir( $handle );
	}
	return $files;
}

$files = fn_select( file_select(), '' );

echo <<<HTML
<script>
    function dbload(){

		var rndval = new Date().getTime(); 

		$('body').append('<div id="modal-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #666666; opacity: .40;filter:Alpha(Opacity=40); z-index: 999; display:none;"></div>');
		$('#modal-overlay').css({'filter' : 'alpha(opacity=40)'}).fadeIn('slow');
	
		$("#dlepopup").remove();
		$("body").append("<div id='dlepopup' title='{$lang['db_load']}' style='display:none'></div>");
	
		$('#dlepopup').dialog({
			autoOpen: true,
			width: 540,
			height: 345,
			resizable: false,
			dialogClass: "modalfixed",
			buttons: {
				"Ok": function() { 
					$(this).dialog("close");
					$("#dlepopup").remove();							
				} 
			},
			open: function(event, ui) { 
				$("#dlepopup").html("<iframe width='100%' height='220' src='?mod=dumper&user_hash={$dle_login_hash}&action=restore&file=" + $("#file").val() + "&rndval=" + rndval + "' frameborder='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>");
			},
			beforeClose: function(event, ui) { 
				$("#dlepopup").html("");
			},
			close: function(event, ui) {
					$('#modal-overlay').fadeOut('slow', function() {
			        $('#modal-overlay').remove();
			    });
			 }
		});

		if ($(window).width() > 830 && $(window).height() > 530 ) {
			$('.modalfixed.ui-dialog').css({position:"fixed"});
			$( '#dlepopup' ).dialog( "option", "position", { my: "center", at: "center", of: window } );
		}

		return false;

    }
</script>
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['db_load']}
  </div>
  <div class="panel-body">

		{$lang['b_restore']}&nbsp;&nbsp;<select class="uniform" name="file" id="file">{$files}</select>&nbsp;&nbsp;<input type="button" class="btn bg-danger btn-sm btn-raised" onclick="dbload(); return false;" value="{$lang['b_load']}" />
	
   </div>
</div>
HTML;

echofooter();
?>