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
 File: templates.php
-----------------------------------------------------
 Use: AJAX template edit
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}


if(($member_id['user_group'] != 1)) {
	die ("error");
}

$allowed_extensions = array ("tpl", "css", "js");

function clear_url_dir($var) {
	if ( is_array($var) ) return "";

	$var = str_replace(chr(0), '', $var);
	$var = str_ireplace( ".php", "", $var );
	$var = str_ireplace( ".php", ".ppp", $var );
	$var = trim( strip_tags( $var ) );
	$var = str_replace( "\\", "/", $var );
	$var = preg_replace( "/[^a-z0-9\/\_\-]+/mi", "", $var );
	return $var;

}

if($_POST['action'] == "create") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die ("error");
	}
	
	if( !check_referer( $config['http_home_url'].$config['admin_path']."?mod=templates") ) {
		echo $lang['no_referer'];
		die ();
	}
	
	$template = trim( totranslit($_POST['template'], false, false) );
	$file = trim( totranslit($_POST['file'], false, false) );
	$root = ROOT_DIR . '/templates/';

	if (!$file OR !$template) die ("error");

	if(!file_exists($root.$template."/") ) die ("error");

	if(!is_writable($root.$template."/")) {
	
		$lang['stat_template'] = str_replace ("{template}", '/templates/'.$template.'/', $lang['stat_template']);
	
		echo $lang['stat_template']; die();
	
	}

	if(file_exists($root.$template."/".$file.".tpl") ) { echo $lang['template_create_err']; die();}

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '69', '{$template}/{$file}.tpl')" );

	$handle = fopen( $root.$template."/".$file.".tpl", "w" );
	fwrite( $handle, "" );
	fclose( $handle );

	@chmod( $root.$template."/".$file.".tpl", 0666 );

	echo "ok"; die();

} elseif($_POST['action'] == "save") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die ("error");
	}
	
	if( !check_referer( $config['http_home_url'].$config['admin_path']."?mod=templates") ) {
		echo $lang['no_referer'];
		die ();
	}

	$_POST['file'] = trim(str_replace( "..", "", urldecode($_POST['file']) ));
	
	if(!$_POST['file']) { die ("error"); }
	
	$url = @parse_url ( $_POST['file'] );

	$root = ROOT_DIR . '/templates/';
	$file_path = dirname (clear_url_dir($url['path']));
	$file_name = pathinfo($url['path']);
	$file_name = totranslit($file_name['basename'], false, true);

	$type = explode( ".", $file_name );
	$type = totranslit( end( $type ) );
	
	if(!in_array( $type, $allowed_extensions ) ) die ("error");

	if(!file_exists($root.$file_path."/".$file_name) ) die ("error");

	if(!is_writable($root.$file_path."/".$file_name)) { echo $lang['template_edit_fail']; die (); }

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '70', '{$file_path}/{$file_name}')" );

	$handle = fopen( $root.$file_path."/".$file_name, "w" );
	fwrite( $handle, $_POST['content'] );
	fclose( $handle );

	if ($type == "css" OR $type == "js") {

		$fdir = opendir( ENGINE_DIR . '/cache/system/' );
		while ( $file = readdir( $fdir ) ) {
			if( $file != '.' and $file != '..' and $file != '.htaccess' and $file != 'cron.php' ) {
				@unlink( ENGINE_DIR . '/cache/system/' . $file );
			
			}
		}

	}

	clear_cache();
	echo "ok"; die();


} elseif($_POST['action'] == "load") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		die ("error");
	}

	$_POST['file'] = trim(str_replace( "..", "", urldecode($_POST['file']) ));
	
	if(!$_POST['file']) { die ("error"); }
	
	$url = @parse_url ( $_POST['file'] );

	$root = ROOT_DIR . '/templates/';
	$file_path = dirname (clear_url_dir($url['path']));
	$file_name = pathinfo($url['path']);
	$file_name = totranslit($file_name['basename'], false, true);

	$type = explode( ".", $file_name );
	$type = totranslit( end( $type ) );
	
	if ( !in_array( $type, $allowed_extensions ) ) die ("error");

	if( !file_exists($root.$file_path."/".$file_name) ) die ("error");

	$content = @htmlspecialchars( file_get_contents( $root.$file_path."/".$file_name ), ENT_QUOTES, $config['charset'] );

	echo $lang['template_edit']." ".$file_path."/".$file_name;

	if(!is_writable($root.$file_path."/".$file_name)) echo " <span style=\"color:red;\">".$lang['template_edit_fail']."</span>";

	echo "<br />".$lang['hot_keys'];

	$script= "";

	if ($type == "tpl") {
		$script= <<<HTML
<script>
  var editor = CodeMirror.fromTextArea(document.getElementById('file_text'), {
    mode: "htmlmixed",
	lineNumbers: true,
	dragDrop: false,
    indentUnit: 4,
    indentWithTabs: false
  });
</script>
HTML;

	}

	if ($type == "css") {
		$script= <<<HTML
<script>
  var editor = CodeMirror.fromTextArea(document.getElementById('file_text'), {
    indentUnit: 4,
	lineNumbers: true,
	dragDrop: false,
    mode: "css"
  });
</script>
HTML;

	}

	if ($type == "js") {
		$script= <<<HTML
<script>
  var editor = CodeMirror.fromTextArea(document.getElementById('file_text'), {
    lineNumbers: true,
    matchBrackets: true,
	indentUnit: 4,
	dragDrop: false,
    mode: "javascript"
  });
</script>
HTML;

	}

	echo <<<HTML
<br /><br /><div style="border: solid 1px #BBB;width:100%;height:460px;"><textarea style="width:100%;height:440px;" name="file_text" id="file_text" wrap="off">{$content}</textarea></div>
<div style="padding:5px;">
<button type="button" class="btn bg-teal btn-sm btn-raised position-left" onclick="savefile('{$file_path}/{$file_name}')"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button></div>
{$script}
HTML;

} else {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}
	
	$root = ROOT_DIR . '/templates/';
	$_POST['dir'] = clear_url_dir(urldecode($_POST['dir']));
	
	if( file_exists($root . $_POST['dir']) ) {
		$files = scandir($root . $_POST['dir']);
		natcasesort($files);
		if( count($files) > 2 ) {
			echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
			// All dirs
			foreach( $files as $file ) {
				if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
					echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . "</a></li>";
				}
			}
			// All files
			foreach( $files as $file ) {
				if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file) ) {
					$serverfile_arr = explode( ".", $file );
					$ext = totranslit( end( $serverfile_arr ) );
	
					if ( in_array( $ext, $allowed_extensions ) )
						echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a></li>";
				}
			}
			echo "</ul>";	
		}
	}
}

?>