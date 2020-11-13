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
 File: upgrade.php
-----------------------------------------------------
 Use: DLE upgrade
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if($member_id['user_group'] != 1){ msg("error", $lang['addnews_denied'], $lang['db_denied']); }

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/zipextract.class.php'));

function convert_file( $file ) {
	
	$string = @file_get_contents($file);
	
	if(!$string) return false;

	if( function_exists( 'mb_convert_encoding' ) ) {
		$string = mb_convert_encoding( $string, "utf-8", "windows-1251" );
	} elseif( function_exists( 'iconv' ) ) {
		$string = iconv("windows-1251", "utf-8", $string);
	}

	if( $string ) {
		file_put_contents ($file, $string, LOCK_EX);
	}

	return true;
}

function need_convert_file( $file ) {
	
	$string = @file_get_contents($file);
	
	if(!$string) return false;

	if( function_exists( 'mb_convert_encoding' ) ) {

		$sample = mb_convert_encoding( $string, "utf-8", "utf-8" );

	} elseif( function_exists( 'iconv' ) ) {
	
		$sample = iconv("utf-8", "utf-8", $string);
	
	}

	if (md5($sample) == md5($string)) return false;

	return true;
}

function table_convert( $table ) {
	global $db;
	
	$table = $db->safesql( $table );
	
	$db->query( "ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci" );

}

function need_table_convert( $table ) {
	global $db;
	
	$table = $db->safesql( $table );
	
	$sql = $db->query( "SHOW FULL COLUMNS FROM `$table`" );
	
	while ( $row = $db->get_row($sql) ) {

		if ( $row['Collation'] ) {
			
			list( $charset ) = explode( '_', $row['Collation'] );
			$charset = strtolower( $charset );

			if ( $charset !== 'utf8mb4' ) {
				return true;
			}
		}
		
	}
	
	$row = $db->super_query( "SHOW TABLE STATUS LIKE '{$table}'" );
	list( $table_charset ) = explode( '_', $row['Collation'] );
	$table_charset = strtolower( $table_charset );
	
	if ( $table_charset != 'utf8mb4') {
		return true;
	}
	
	return false;
	
}

function files_check_chmod( $dir,  $bad_files = array() ) {
	
	if ( $dh = @opendir( $dir ) ) {
		
		while ( false !== ( $file = readdir($dh) ) ) {
			
			if ( $file == '.' or $file == '..' or $file == '.svn' or $file == '.DS_store' ) {
					continue;
			}
		
			if ( is_dir( $dir . "/" . $file ) ) {

				$bad_files = files_check_chmod( $dir . "/" . $file, $bad_files );
				
			} else {
				
				if ( preg_match( "#.*\.(php|txt|tpl)#i", $file ) ) {
					$folder = str_replace(ROOT_DIR, "",$dir);
					
					if(!is_writable($dir . "/" . $file)) {
						$bad_files[] = $folder . "/" . $file;
					}
				}
			}
		}
	}
	
	return $bad_files;
}

function get_files( $dir,  $files = array() ) {
	
	if ( $dh = @opendir( $dir ) ) {
		
		while ( false !== ( $file = readdir($dh) ) ) {
			
			if ( $file == '.' or $file == '..' or $file == '.svn' or $file == '.DS_store' ) {
					continue;
			}
		
			if ( is_dir( $dir . "/" . $file ) ) {

				$files = get_files( $dir . "/" . $file, $files );
				
			} else {
				
				if ( preg_match( "#.*\.(php|txt|tpl)#i", $file ) ) {
					$folder = str_replace(ROOT_DIR, "",$dir);
					$files[] = $folder . "/" . $file;
				}
			}
		}
	}
	
	return $files;
}

if($_REQUEST['action'] == "upgrade_files" ) {
	
		if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
			die( "Hacking attempt! User not found" );
		}
	
		$distr_file = ENGINE_DIR . "/cache/system/" . md5('ugrdistr'.SECURE_AUTH_KEY) . ".zip";
		
		$done = 0;

		try {
			
			$fs = new dle_zip_extract( $distr_file );
			$total = $fs->zip->numFiles;
			
			$offset = intval($_POST['offset']);
			
			if( $_SESSION['distr']['ftp'] )	{
				$fs->FtpConnect( $_SESSION['distr']['ftp'] );
			}
			
			$done = $fs->ExtractZipArchive($offset, 20);
			
		} catch ( Exception $e ) {
	
			$response['error'] = $e->getMessage();
			echo json_encode($response);
			die();
			
		}
		
		if( !isset($_SESSION['files_errors']) ) $_SESSION['files_errors'] = array();
	
		if( $done )	{
			$offset = $offset + $done;
		} else {
			$offset = $total;
		}
		
		if($offset >= $total){
			$fs->FixHtaccess();
			@unlink(ENGINE_DIR.'/data/snap.db');
			clear_all_caches();
		}

		if (function_exists('opcache_reset')) {
			opcache_reset();
		}
		
		if( $_SESSION['distr']['ftp'] )	{
			$fs->DisconnectFTP();
		}
		
		if( count($fs->errors_list) ) $_SESSION['files_errors'] = array_merge($_SESSION['files_errors'], $fs->errors_list);
		
		echo "{\"status\": \"ok\", \"offset\": \"{$offset}\"}";
		die();


} elseif($_REQUEST['action'] == "checkftp" ) {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	try {
		
		$fs = new dle_zip_extract();
		$fs->FtpConnect( $_POST['ftp'] );
		
	} catch ( Exception $e ) {
		
		$response['error'] = $e->getMessage();
		echo json_encode($response);
		die();
	}

	$_SESSION['distr']['ftp'] = $_POST['ftp'];
	
	echo "{\"status\": \"ok\"}";
	die();
	
} elseif($_REQUEST['action'] == "download" ) {
	
	if ( !isset( $_SESSION['distr'] ) )	{
		header( "Location: ?mod=upgrade" );
		die();
	}
	
	$no_access = files_check_chmod(ROOT_DIR."/engine" );


	if($_REQUEST['subaction'] == "manual" OR !class_exists('ZipArchive') ) {
		
		echoheader( "<i class=\"fa fa-database position-left\"></i><span class=\"text-semibold\">{$lang['upgr_dbtitle_2']}</span>", $lang['upgr_dbtitle_3']);
		
		$lang['upgr_ftp_10'] = str_replace("{link}", "<a href=\"{$_SESSION['distr']['link']}\" target=\"_blank\">{$lang['upgr_ftp_11']} {$_SESSION['distr']['version']}</a>", $lang['upgr_ftp_10']);
		
		echo <<<HTML
	<div class="panel panel-default">
	  <div class="panel-heading">
		{$lang['upgr_dbtitle_2']}
	  </div>
		<div class="panel-body">
			{$lang['upgr_ftp_9']}
		</div>
		<div class="panel-body">
			<ol>{$lang['upgr_ftp_10']}</ol>
		</div>
	  <div class="panel-footer">
		 <a href="?mod=upgrade&action=dbupgrade&to={$_SESSION['distr']['version']}" class="btn bg-teal btn-sm btn-raised"><i class="fa fa-forward position-left"></i>{$lang['upgr_next']}</a>
	   </div>
	</div>
HTML;

		if( !class_exists('ZipArchive') ) {
			echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['upgr_f_error_19']}</div>";
		}
		
		echofooter();
		
	} elseif( count($no_access) AND !isset( $_SESSION['distr']['ftp'] ) ) {
		
		echoheader( "<i class=\"fa fa-database position-left\"></i><span class=\"text-semibold\">{$lang['upgr_dbtitle_2']}</span>", $lang['upgr_dbtitle_3']);

		$root = ROOT_DIR;

		echo <<<HTML
	<form class="form-horizontal" id="ftpserver">
	<div class="panel panel-default">
	  <div class="panel-heading">
		{$lang['upgr_dbtitle_2']}
	  </div>
		<div class="panel-body">
			{$lang['upgr_ftp_1']}
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label col-sm-2">{$lang['upgr_ftp_2']}</label>
				<div class="col-sm-10">
					<label class="radio-inline position-left"><input class="icheck" type="radio" name="ftp[type]" value="ftp" checked>FTP</label>
					<label class="radio-inline position-left"><input class="icheck" type="radio" name="ftp[type]" value="sslftp">SSL FTP</label>
					<label class="radio-inline position-left"><input class="icheck" type="radio" name="ftp[type]" value="ssh2">SFTP SSH2</label>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2">{$lang['upgr_ftp_3']}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control width-350 position-left" name="ftp[server]">
					<span class="position-left">{$lang['upgr_ftp_4']}</span>
					<input type="text" class="form-control position-left" name="ftp[port]" style="width:45px" value="21">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2">{$lang['upgr_ftp_5']}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control width-350 position-left" name="ftp[username]">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2">{$lang['upgr_ftp_6']}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control width-350 position-left" name="ftp[password]">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-2">{$lang['upgr_ftp_7']}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control width-450 position-left" name="ftp[path]" value="{$root}">
				</div>
			</div>
		</div>
	  <div class="panel-footer">
		 <button id="button" onclick="check_ftp(); return false;" type="button" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-forward position-left"></i>{$lang['upgr_next']}</button>
		 <a href="?mod=upgrade&action=download&subaction=manual" class="btn bg-brown-600 btn-sm btn-raised"><i class="fa fa-download position-left"></i>{$lang['upgr_ftp_8']}</a>
	   </div>
	<script>
	<!--
	function check_ftp() {
	
		var formData = new FormData($('#ftpserver')[0]);
		
		ShowLoading('');
		$('#button').attr("disabled", "disabled");
		
		$.ajax({
			url: "?mod=upgrade&action=checkftp&user_hash={$dle_login_hash}",
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
			
				HideLoading('');
				
				if (data.status == "ok") {
					setTimeout("window.location = '?mod=upgrade&action=download'", 300 );
				} else {
					$('#button').attr("disabled", false);
					DLEalert(data.error, '{$lang['all_info']}');
						
				}
			}
		});
	
		return false;
	}
	//-->
	</script>
	</div>
	</form>
HTML;

		echofooter();
		
	} else {
		
		$distr_file = ENGINE_DIR . "/cache/system/" . md5('ugrdistr'.SECURE_AUTH_KEY) . ".zip";
		@unlink( $distr_file );
		
		if ( !@copy($_SESSION['distr']['link'], $distr_file ) ) {
			msg( "error", $lang['addnews_error'], $lang['upgr_f_error_8'], array('javascript:location.reload(true);' => $lang['upgr_btn_1'], '?mod=upgrade&action=download&subaction=manual' => $lang['upgr_ftp_8'] ) );
		}
		
		if( md5_file($distr_file) != $_SESSION['distr']['crc']) {
			msg( "error", $lang['addnews_error'], $lang['upgr_f_error_9'], array('javascript:location.reload(true);' => $lang['upgr_btn_1'], '?mod=upgrade&action=download&subaction=manual' => $lang['upgr_ftp_8'] ) );
		}
		
		$fs = new dle_zip_extract( $distr_file );
		$total = $fs->zip->numFiles;

		echoheader( "<i class=\"fa fa-database position-left\"></i><span class=\"text-semibold\">{$lang['upgr_dbtitle_2']}</span>", $lang['upgr_dbtitle_3']);
		
		echo <<<HTML
			<div class="panel panel-default">
			  <div class="panel-heading">
				{$lang['upgr_ftp_12']}
			  </div>
				<div class="panel-body">
					<div class="progress"><div id="progressbar" class="progress-bar progress-blue" style="width:0%;"><span></span></div></div>
					<div class="text-size-small">{$lang['upgr_ftp_14']} <span id="files_ok"></span> <span id="status"></span></div>
				</div>
				<div class="panel-body text-muted text-size-small">
				{$lang['upgr_ftp_13']}
				</div>	
				<div class="panel-footer">
					<button id="button" type="button" class="btn bg-teal btn-sm btn-raised" disabled><i class="fa fa-forward position-left"></i>{$lang['upgr_next']}</button>
				</div>
			</div>
<script>

	var total = {$total};
	var offset = 0;

	function upgrade_files(offset)  {

		$.post("?user_hash={$dle_login_hash}", { mod: 'upgrade', action: 'upgrade_files', offset: offset },
			function(data){
				if (data) {
					if (data.status == "ok") {
					
						offset = data.offset;

						$('#files_ok').text(offset);
						
						var proc = Math.round( (100 * data.offset) / total );
	
						if ( proc > 100 ) proc = 100;
	
						$('#progressbar').css( "width", proc + '%' );

						 if (data.offset >= total)
						 {
							setTimeout("window.location = '?mod=upgrade&action=dbupgrade&to={$_SESSION['distr']['version']}'", 100 );
							
						 } else { setTimeout("upgrade_files(" + data.offset + ")", 300 ); }
	
	
					} else if( data.error ) { $('#status').text(data.error); }
	
				}
			}, "json").fail(function() {
			
			$('#status').html('{$lang['upgr_error']}');
			$('#button').attr("disabled", false);
			
		});
	
		return false;
	
	}

	$(function() {

		$("#status").ajaxError(function(event, request, settings){
		   $(this).html('{$lang['upgr_error']}');
			$('#button').attr("disabled", false);
		});
		
		$('#button').click(function() {
			$('#button').attr("disabled", "disabled");
			upgrade_files(offset);
			return false;
		});
		
		setTimeout("upgrade_files(offset)", 300 );
	});
</script>
HTML;

		echofooter();
	}
	

	
} elseif($_REQUEST['action'] == "checklicense" ) {

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if( strlen(trim((string)$_REQUEST['dle_key'])) != 29 ){
		echo "{\"error\": \"{$lang['upgr_f_error_5']}\"}";
		die();
	}
	
	$params = array(
		'action' => 'info',
		'domain' => $_SERVER['HTTP_HOST'],
		'key' => (string)$_REQUEST['dle_key'],
		'version' => VERSIONID,
		'build' => BUILDID,
		'site_key' => get_domen_hash()
	);

	$data = http_get_contents("https://dle-news.ru/extras/upgrade/index.php?".http_build_query($params, '', '&') );

	if( $data !== false ) {

		$data = json_decode ($data, true);

		if(is_array($data) ) {

			if( $data['error']) {
				
				if( $data['error'] == "-3" ) $data['error'] = $lang['upgr_f_error_2'];
				if( $data['error'] == "-2" ) $data['error'] = $lang['upgr_f_error_3'];
				if( $data['error'] == "-1" ) $data['error'] = $lang['upgr_f_error_4'];
				
				echo "{\"error\": \"{$data['error']}\"}";
				die();
			}
			
			if( $data['distr']['version'] ) {
					
				if( version_compare(VERSIONID, $data['distr']['version'], '>') ) {
					echo "{\"error\": \"{$lang['upgr_f_error_6']}\"}";
					die();
				}
				
				if( version_compare(VERSIONID, $data['distr']['version'], '==') AND version_compare(BUILDID, $data['distr']['build'], '>=') AND !defined('DEMOVERSION') ) {
					echo "{\"error\": \"{$lang['upgr_f_error_6']}\"}";
					die();
				}

				$_SESSION['distr'] = $data['distr'];
				echo "{\"status\": \"ok\"}";
				die();
				
			}
			
		}
		
	}

	echo "{\"error\": \"{$lang['upgr_f_error_1']}\"}";
	die();
	
} elseif($_REQUEST['action'] == "dbupgradecheck" ) {

	if ( !$_SESSION['db_upgrade'] )	{
		header( "Location: ?mod=main" );
		die();
	}
	
	unset($_SESSION['db_upgrade']);
	
	if( is_array( $_SESSION['query_errors'] ) ) $errors_query = count( $_SESSION['query_errors'] ); else $errors_query = 0;	
	if( is_array( $_SESSION['files_errors'] ) ) $errors_files = count( $_SESSION['files_errors'] ); else $errors_files = 0;
	
	$lang['upgr_db_success'] = str_replace("{version}", $config['version_id'], $lang['upgr_db_success']);

	if(COLLATE != "utf8" AND COLLATE != "utf8mb4") {
		
		if( !$errors_query AND !$errors_files ) {
			
			$_SESSION['db_convert'] = 1;
			unset($_SESSION['query_errors']);
			unset($_SESSION['files_errors']);
			header( "Location: ?mod=upgrade&action=dodbconvert&user_hash={$dle_login_hash}" );
			die();
		}
		
		$next_link = '?mod=upgrade&action=dbconvert';
		
	} else $next_link = '?mod=main';
	
	if( !$errors_query AND !$errors_files ) {
		
		unset($_SESSION['query_errors']);
		unset($_SESSION['files_errors']);
		
		msg( "success", $lang['all_info'], $lang['upgr_db_success'], array( $next_link => $lang['upgr_next']) );
	}
	
	echoheader( "<i class=\"fa fa-database position-left\"></i><span class=\"text-semibold\">{$lang['upgr_dbtitle_2']}</span>", $lang['upgr_dbtitle_3']);
		
	$errors = "<div class=\"panel-body\">".$lang['upgr_db_success']."</div>";
	$errors .= "<div class=\"panel-body\">".$lang['upgr_db_errors']."</div>";
	$sql_errors = "";
	$file_errors = "";

	if($errors_files) {
		foreach ($_SESSION['files_errors'] as $value) {
			$file_errors .= "<div class=\"quote\"><b>{$lang['upgr_file']}</b> {$value['file']}<br /><b>{$lang['upgr_db_errt']}</b> {$value['error']}</div>";
		}
		
		$errors .= "<div class=\"panel-body\"><div class=\"text-size-small pre-scrollable\">".$file_errors."</div></div>";
	}
	
	if($errors_query) {
		foreach ($_SESSION['query_errors'] as $value) {
			$sql_errors .= "<div class=\"quote\"><b>{$lang['upgr_db_query']}</b> {$value['query']}<br /><b>{$lang['upgr_db_errt']}</b> {$value['error']}</div>";
		}
		
		$errors .= "<div class=\"panel-body\"><div class=\"text-size-small pre-scrollable\">".$sql_errors."</div></div>";
	}
	
	
		echo <<<HTML
	<div class="panel panel-default">
	  <div class="panel-heading">
		{$lang['upgr_dbtitle_2']}
	  </div>
	  {$errors}
	  <div class="panel-footer">
		 <a href="{$next_link}" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-forward position-left"></i>{$lang['upgr_next']}</a>
	   </div>
	</div>
HTML;

	unset($_SESSION['query_errors']);
	unset($_SESSION['files_errors']);
		
	echofooter();
	die();
	
} elseif($_REQUEST['action'] == "dodbupgrade" ) {
	
	if ( !$_SESSION['db_upgrade'] )	{
		header( "Location: ?mod=upgrade&action=dbupgrade" );
		die();
	}
	
	if( $config['version_id'] == VERSIONID ) {
		echo "{\"status\": \"ok\", \"version\":\"{$config['version_id']}\"}";
		die();
	}
		
	$row = $db->super_query("SHOW TABLE STATUS WHERE Name = '" . PREFIX . "_post'");
	$storage_engine = $row['Engine'];
	
	if ( strtolower($storage_engine) == "innodb" ) {
		$storage_engine = "InnoDB";
	} else $storage_engine = "MyISAM";

	if( isset($_SESSION['distr']) ) {
		$config['key'] = md5( get_domen_hash() . DINITVERSION );
		unset($_SESSION['distr']);
	}

	if( file_exists( ENGINE_DIR . "/inc/upgrade/" . totranslit($config['version_id']) . ".php" ) ) {
		include ( ENGINE_DIR . "/inc/upgrade/" . totranslit($config['version_id']) . ".php" );
	}
	
	if( !isset($_SESSION['query_errors']) ) $_SESSION['query_errors'] = array();
	
	if( count($db->query_errors_list) ) $_SESSION['query_errors'] = array_merge($_SESSION['query_errors'], $db->query_errors_list);

	clear_all_caches();
	@unlink(ENGINE_DIR.'/data/snap.db');
	
	echo "{\"status\": \"ok\", \"version\":\"{$config['version_id']}\"}";
	die();
	
} elseif($_REQUEST['action'] == "dbsettingsconvert" ) {
	
	if ( !$_SESSION['db_convert'] )	{
		header( "Location: ?mod=upgrade&action=dbconvert" );
		die();
	}

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	$config_dbhost = DBHOST;
	$config_dbname = DBNAME;
	$config_dbuser = DBUSER;
	$config_dbpasswd = DBPASS;
	$config_dbprefix = PREFIX;
	$config_userprefix = USERPREFIX;
	$auth_key = SECURE_AUTH_KEY;
	
	$config_dbcollate = "utf8mb4";
	$config_dbpasswd = str_replace ('"', '\"', str_replace ("$", "\\$", $config_dbpasswd) );

	$dbconfig = <<<HTML
<?PHP
	
define ("DBHOST", "{$config_dbhost}"); 
	
define ("DBNAME", "{$config_dbname}");
	
define ("DBUSER", "{$config_dbuser}");
	
define ("DBPASS", "{$config_dbpasswd}");  
	
define ("PREFIX", "{$config_dbprefix}");
	
define ("USERPREFIX", "{$config_userprefix}");
	
define ("COLLATE", "{$config_dbcollate}");
	
define('SECURE_AUTH_KEY', '{$auth_key}');
	
\$db = new db;
	
?>
HTML;
	
	$handler = fopen(ENGINE_DIR.'/data/dbconfig.php', "w");
	fwrite($handler, $dbconfig);
	fclose($handler);
	
	unset($_SESSION['db_convert']);
	clear_all_caches();
	
	echo "{\"status\": \"ok\"}";
	die();	

} elseif($_REQUEST['action'] == "settingsconvert" ) {

	if ( !$_SESSION['db_convert'] )	{
		header( "Location: ?mod=upgrade&action=dbconvert" );
		die();
	}

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	$config['charset'] = "utf-8";
	$handler = fopen(ENGINE_DIR.'/data/config.php', "w");
	fwrite($handler, "<?PHP \n\n//System Configurations\n\n\$config = array (\n\n");
	foreach($config as $name => $value)
	{
		fwrite($handler, "'{$name}' => \"{$value}\",\n\n");
	}
	fwrite($handler, ");\n\n?>");
	fclose($handler);

	$config_dbhost = DBHOST;
	$config_dbname = DBNAME;
	$config_dbuser = DBUSER;
	$config_dbpasswd = DBPASS;
	$config_dbprefix = PREFIX;
	$config_userprefix = USERPREFIX;
	$auth_key = SECURE_AUTH_KEY;
	
	$config_dbcollate = "utf8mb4";
	$config_dbpasswd = str_replace ('"', '\"', str_replace ("$", "\\$", $config_dbpasswd) );

	$dbconfig = <<<HTML
<?PHP
	
define ("DBHOST", "{$config_dbhost}"); 
	
define ("DBNAME", "{$config_dbname}");
	
define ("DBUSER", "{$config_dbuser}");
	
define ("DBPASS", "{$config_dbpasswd}");  
	
define ("PREFIX", "{$config_dbprefix}");
	
define ("USERPREFIX", "{$config_userprefix}");
	
define ("COLLATE", "{$config_dbcollate}");
	
define('SECURE_AUTH_KEY', '{$auth_key}');
	
\$db = new db;
	
?>
HTML;
	
	$handler = fopen(ENGINE_DIR.'/data/dbconfig.php', "w");
	fwrite($handler, $dbconfig);
	fclose($handler);

	$settings_files = get_files(ROOT_DIR."/engine/data");
	
	foreach($settings_files as $file) {
		if( need_convert_file(ROOT_DIR.$file) ) {
			convert_file(ROOT_DIR.$file);
		}
	}

	unset($_SESSION['db_convert']);
	
	clear_all_caches();
	
	echo "{\"status\": \"ok\"}";
	die();

} elseif($_REQUEST['action'] == "templateconvert" ) {

	if ( !$_SESSION['db_convert'] )	{
		header( "Location: ?mod=upgrade&action=dbconvert" );
		die();
	}

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}

	$template_files = get_files(ROOT_DIR."/templates");
	
	foreach($template_files as $file) {
		if( need_convert_file(ROOT_DIR.$file) ) {
			convert_file(ROOT_DIR.$file);
		}
	}
	
	echo "{\"status\": \"ok\"}";
	die();

} elseif($_REQUEST['action'] == "tableconvert" ) {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		die( "Hacking attempt! User not found" );
	}
	
	if ( !$_SESSION['db_convert'] )	{
		header( "Location: ?mod=upgrade&action=dbconvert" );
		die();
	}

	$table = trim(totranslit($_POST['table'], false, false));
	
	if(!$table) die('error');
	
	if( need_table_convert( $table ) ) {
		table_convert( $table );
	}
	
	echo "{\"status\": \"ok\"}";
	die();
	
} elseif($_REQUEST['action'] == "dodbconvert" ) {

	if ( !$_SESSION['db_convert'] )	{
		header( "Location: ?mod=upgrade&action=dbconvert" );
		die();
	}

	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		msg( "error", $lang['addnews_error'], $lang['sess_error'] );
	}
	

	$tables = array();
	$sql = $db->query( "SHOW TABLES" );
	
	while ( $row = $db->get_array($sql) ) {
		if( substr($row[0], 0, strlen( PREFIX ) ) == PREFIX ) {
			if( need_table_convert($row[0]) ) $tables[] = $row[0];
		}
	}
	
	if(!count($tables) ) msg( "warning", $lang['all_info'], $lang['upgr_all_conv'] );
	
	$total = $totaltables = count($tables);
	$tables = "['".implode("','", $tables)."']";

	if($_REQUEST['subaction'] != "onlymb4" ) {
		
		$total = $total+2;
		
		$convert_files = <<<HTML
		function convert_templates()  {
		
			step ++;
			
			$('#wconvert').html('{$lang['upgr_templ_conv']}');
			
			$.post("?user_hash={$dle_login_hash}", { mod: 'upgrade', action: 'templateconvert' },
				function(data){
		
					if (data) {
		
						if (data.status == "ok") {
		
							var proc = Math.round( (100 * step) / total );
		
							if ( proc > 100 ) proc = 100;
		
							$('#progressbar').css( "width", proc + '%' );
							
							setTimeout("convert_settings()", 300 );
		
						}
		
					}
				}, "json").fail(function() {
								$('#status').html('{$lang['upgr_error']}');
							});
		
			return false;
		
		}
		
		function convert_settings()  {
		
			step ++;
			
			$('#wconvert').html('{$lang['upgr_sett_conv']}');
			
			$.post("?user_hash={$dle_login_hash}", { mod: 'upgrade', action: 'settingsconvert' },
				function(data){
		
					if (data) {
		
						if (data.status == "ok") {
							var proc = Math.round( (100 * step) / total );
		
							if ( proc > 100 ) proc = 100;
		
							$('#progressbar').css( "width", proc + '%' );
							
							setTimeout("window.location = '?mod=main'", 300 );
		
						}
		
					}
				}, "json").fail(function() {
								$('#status').html('{$lang['upgr_error']}');
							});
		
			return false;
		
		}
HTML;

	} else { $convert_files = ""; $total = $total+1; }
	
	echoheader( "<i class=\"fa fa-database position-left\"></i><span class=\"text-semibold\">{$lang['upgr_dbtitle']}</span>", $lang['upgr_dbtitle_1']);

	echo <<<HTML
<script>

	var total = $total;
	var totaltables = $totaltables;
	var tables = {$tables};
	var step = 0;
	var table_info = '{$lang['upgr_table_conv']}';

	function convert_tables()  {
	
		var table = tables[step];
		step ++;
		
		$('#wconvert').html(table_info + ' <b>' + table + '</b>');
		
		$.post("?user_hash={$dle_login_hash}", { mod: 'upgrade', action: 'tableconvert', table: table },
			function(data){
	
				if (data) {
	
					if (data.status == "ok") {
	
						var proc = Math.round( (100 * step) / total );
	
						if ( proc > 100 ) proc = 100;
	
						$('#progressbar').css( "width", proc + '%' );

						 if (step >= totaltables)
						 {
							if (typeof convert_templates == 'function') {
							
								setTimeout("convert_templates()", 300 );
								
							} else {
							
								setTimeout("convert_dbsettings()", 300 );
								
							}
							
						 } else { setTimeout("convert_tables()", 300 ); }
	
	
					}
	
				}
			}, "json").fail(function() {
			
			$('#status').html('{$lang['upgr_error']}');
			$('#button').attr("disabled", false);
			
		});
	
		return false;
	
	}
	
	function convert_dbsettings()  {
		
		step ++;
			
		$('#wconvert').html('{$lang['upgr_sett_conv']}');
			
		$.post("?user_hash={$dle_login_hash}", { mod: 'upgrade', action: 'dbsettingsconvert' },
			function(data){
	
				if (data) {
		
					if (data.status == "ok") {
						var proc = Math.round( (100 * step) / total );
		
						if ( proc > 100 ) proc = 100;
		
						$('#progressbar').css( "width", proc + '%' );
						
						setTimeout("window.location = '?mod=main'", 300 );
		
					}
		
				}
				
			}, "json").fail(function() {
			
			$('#status').html('{$lang['upgr_error']}');

		});
		
		return false;
		
	}
	
{$convert_files}

	$(function() {

		$("#status").ajaxError(function(event, request, settings){
			$(this).html('{$lang['upgr_error']}');
			$('#button').attr("disabled", false);
		});
		
		$('#button').click(function() {
			$('#button').attr("disabled", "disabled");
			$('#status').html('');
			convert_tables();
			return false;
		});
		
		setTimeout("convert_tables()", 300 );
	});

</script>
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['upgr_dbtitle_1']}
  </div>
	<div class="panel-body">
		<div class="progress"><div id="progressbar" class="progress-bar progress-blue" style="width:0%;"><span></span></div></div>
		<div class="text-size-small"><span id="wconvert"></span> <span id="status"></span></div>
    </div>
	<div class="panel-body text-muted text-size-small">
	{$lang['upgr_noclose']}
	</div>	
	<div class="panel-footer">
		<button id="button" type="button" class="btn bg-teal btn-sm btn-raised" disabled><i class="fa fa-forward position-left"></i>{$lang['upgr_next']}</button>
	</div>
</div>
HTML;

	echofooter();

} elseif($_REQUEST['action'] == "dbconvert" ) {

	if( COLLATE == "utf8mb4" ) msg( "warning", $lang['all_info'], $lang['upgr_all_conv'] );
	
	echoheader( "<i class=\"fa fa-database position-left\"></i><span class=\"text-semibold\">{$lang['upgr_dbtitle']}</span>", $lang['upgr_dbtitle_1']);
	
	$_SESSION['db_convert'] = 1;
	
	$bad_files = files_check_chmod(ROOT_DIR."/engine/data" );
	$bad_files = array_merge($bad_files, files_check_chmod(ROOT_DIR."/templates" ) );

	$errors = "";

	if( version_compare($db->mysql_version, '5.5.3', '<') ) {
		$lang['upgr_minsql'] = str_replace("{version}", $db->mysql_version, $lang['upgr_minsql']);
		$errors = "<div class=\"panel-body\">".$lang['upgr_minsql']."</div>";
	}

	if($_REQUEST['subaction'] == "onlymb4" ) {
		$subaction = "<input type=\"hidden\" name=\"subaction\" value=\"onlymb4\">";
		$lang['upgr_dbtitle_1'] = str_replace("utf-8", "utf8mb4", $lang['upgr_dbtitle_1']);
		$lang['upgr_dbinfo'] = str_replace("utf-8", "utf8mb4", $lang['upgr_dbinfo']);
	} else $subaction = "";
	
	if(count($bad_files)) {
		
		$list = <<<HTML
		  <div>{$lang['upgr_file_2']}</div>
		  <div class="table-responsive pre-scrollable">
			<table class="table table-striped table-xs table-framed"><thead><tr><th>{$lang['upgr_file']}</th><th style="width:150px;">CHMOD</th></thead><tbody>
HTML;
		foreach($bad_files as $file){
			$list .= "<tr><td>$file</td><td><span class=\"text-danger\">{$lang['upgr_file_1']}</span></td></tr>";
		}
		 
		$list .= <<<HTML
		  </tbody></table></div>
HTML;

		$errors .= "<div class=\"panel-body\">".$list."</div>";
	}
	
	if( $errors ) {
		
		$errors .= "<div class=\"panel-body\">".$lang['upgr_c_err']."</div>";
		$button= "<button onclick=\"location.reload(true); return false;\" class=\"btn bg-danger btn-sm btn-raised position-left\"><i class=\"fa fa-refresh position-left\"></i>{$lang['upgr_btn_1']}</button>";

	} else $button= "<button type=\"submit\" class=\"btn bg-teal btn-sm btn-raised position-left\"><i class=\"fa fa-exchange position-left\"></i>{$lang['upgr_btn_2']}</button>";
	
	echo <<<HTML
<form method="get" class="form-horizontal">
<input type="hidden" name="mod" value="upgrade">
<input type="hidden" name="action" value="dodbconvert">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
{$subaction}
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['upgr_dbtitle_1']}
  </div>
	<div class="panel-body">
		{$lang['upgr_dbinfo']}
	</div>
	{$errors}
	<div class="panel-footer">
		{$button}
	</div>
</div>
</form>
HTML;

	echofooter();
	
} else {
	
	$errors = "";
	$bad_files = files_check_chmod(ROOT_DIR."/engine/data" );

	if( !is_writable( ROOT_DIR."/engine/data" ) ) $bad_files[] = "/engine/data/";
	if( !is_writable( ROOT_DIR."/engine/cache" ) ) $bad_files[] = "/engine/cache/";
	if( !is_writable( ROOT_DIR."/engine/cache/system" ) ) $bad_files[] = "/engine/cache/system/";
	
	if(COLLATE != "utf8" AND COLLATE != "utf8mb4") {
		$bad_files = array_merge($bad_files, files_check_chmod(ROOT_DIR."/templates" ) );
	}

	if( version_compare($db->mysql_version, '5.5.3', '<') ) {
		$lang['upgr_minsql'] = str_replace("{version}", $db->mysql_version, $lang['upgr_minsql']);
		$errors .= "<div class=\"text-danger\">".$lang['upgr_minsql']."</div>";
	}
	
	$lang['upgr_info'] = str_replace("{oldversion}", $config['version_id'], $lang['upgr_info']);
	$lang['upgr_info'] = str_replace("{newversion}", VERSIONID, $lang['upgr_info']);
	
	$phpv = phpversion();
	
	if ( version_compare($phpv, '5.4', '<') ) {
		$lang['upgr_err_1'] = str_replace("{version}", $phpv, $lang['upgr_err_1']);
		$errors .= "<div class=\"text-danger\">".$lang['upgr_err_1']."</div>";
	}

	if($errors) $errors = "<div class=\"panel-body\">".$errors."</div>";

	if(count($bad_files)) {
		
		$list = <<<HTML
		  <div>{$lang['upgr_file_2']}</div>
		  <div class="table-responsive pre-scrollable">
			<table class="table table-striped table-xs table-framed"><thead><tr><th>{$lang['upgr_file']}</th><th style="width:150px;">CHMOD</th></thead><tbody>
HTML;
		foreach($bad_files as $file){
			$list .= "<tr><td>$file</td><td><span class=\"text-danger\">{$lang['upgr_file_1']}</span></td></tr>";
		}
		 
		$list .= <<<HTML
		  </tbody></table></div>
HTML;

		$errors .= "<div class=\"panel-body\">".$list."</div>";
	}

	if($errors) {
		
		echoheader( "<i class=\"fa fa-database position-left\"></i><span class=\"text-semibold\">{$lang['upgr_dbtitle_2']}</span>", $lang['upgr_dbtitle_3']);
		
		$errors .= "<div class=\"panel-body\">".$lang['upgr_c_err']."</div>";

		echo <<<HTML
	<div class="panel panel-default">
	  <div class="panel-heading">
		{$lang['upgr_dbtitle_2']}
	  </div>
	  {$errors}
	  <div class="panel-footer">
		 <button onclick="location.reload(true); return false;" class="btn bg-danger btn-sm btn-raised position-left"><i class="fa fa-refresh position-left"></i>{$lang['upgr_btn_1']}</button>
	   </div>
	</div>
HTML;

		echofooter();
	
	} elseif($_REQUEST['action'] == "dbupgrade" ) {

		$_SESSION['db_upgrade'] = 1;

		if($_REQUEST['to'] AND VERSIONID != $_REQUEST['to']) {
			msg("info", $lang['all_info'], $lang['upgr_f_error_7'], array('javascript:location.reload(true);' => $lang['upgr_btn_1'], 'javascript:history.go(-1)' => $lang['func_msg'] ));
		}
		
		if(!version_compare( $config['version_id'], VERSIONID , '<')) {
			
			if( $_REQUEST['to'] ) {
				header( "Location: ?mod=upgrade&action=dbupgradecheck" );
				die();
			} else {
				msg("info", $lang['all_info'], $lang['upgr_all_upg']);
			}
			
		}

		if( !file_exists( ENGINE_DIR . "/inc/upgrade/" . totranslit($config['version_id']) . ".php" )) {
			
			$lang['upgr_no_upg_files'] = str_replace("{version}", $config['version_id'], $lang['upgr_no_upg_files']);
			msg("error", $lang['addnews_denied'], $lang['upgr_no_upg_files']);
			
		}
		
		if( $_REQUEST['to'] ) {
			$autostart = "setTimeout(\"db_upgrade()\", 100 );";
		} else $autostart = "";
		
		$versions = array();
		$files = glob( ENGINE_DIR . "/inc/upgrade/*.php");
		
		foreach ($files as $file) {
			$version = basename ( $file, ".php" );
			
			if(intval($version) AND version_compare( $version, $config['version_id'] , '>=') ) {
				$versions[] = $version;
			}
			
		}
		
		$total = count($versions);
		
		$versions[] = $actualversion = VERSIONID;
		
		sort($versions, SORT_NUMERIC);
		
		$versions = "['".implode("','", $versions)."']";

		echoheader( "<i class=\"fa fa-database position-left\"></i><span class=\"text-semibold\">{$lang['upgr_dbtitle_2']}</span>", $lang['upgr_dbtitle_3']);
	
		echo <<<HTML
<script>

	var actualversion = '{$actualversion}';
	var total = {$total};
	var versions = {$versions};
	var step = 0;
	var versions_info = '{$lang['upgr_db_ver']}';

	function db_upgrade()  {
	
		var version = versions[step+1];
		step ++;
		
		$('#button').attr("disabled", "disabled");
		$('#wconvert').html(versions_info + ' <b>' + version + '</b>');
		
		$.post("?user_hash={$dle_login_hash}", { mod: 'upgrade', action: 'dodbupgrade' },
			function(data){
	
				if (data) {
	
					if (data.status == "ok") {
	
						var proc = Math.round( (100 * step) / total );
	
						if ( proc > 100 ) proc = 100;
	
						$('#progressbar').css( "width", proc + '%' );

						 if (data.version == actualversion)
						 {

							setTimeout("window.location = '?mod=upgrade&action=dbupgradecheck'", 1000 );

						 } else { setTimeout("db_upgrade()", 1000 ); }
	
	
					}
	
				}
			}, "json").fail(function() {
			
			$('#status').html('{$lang['upgr_error']}');
			$('#button').attr("disabled", false);
			
		});
	
		return false;
	
	}
	
	$(function() {

		$("#status").ajaxError(function(event, request, settings){
		   $(this).html('{$lang['upgr_error']}');
			$('#button').attr("disabled", false);
		});
		
		$('#button').click(function() {
			$('#button').attr("disabled", "disabled");
			db_upgrade();
			return false;
		});
		
		{$autostart}

	});

</script>

	<div class="panel panel-default">
	  <div class="panel-heading">
		{$lang['upgr_dbtitle_2']}
	  </div>
		<div class="panel-body">
			{$lang['upgr_info']}
		</div>
		<div class="panel-body">
			<div class="progress"><div id="progressbar" class="progress-bar progress-blue" style="width:0%;"><span></span></div></div>
			<div class="text-size-small"><span id="wconvert"></span> <span id="status"></span></div>
		</div>
		<div class="panel-body text-muted text-size-small">
		{$lang['upgr_noclose_2']}
		</div>	
		<div class="panel-footer">
			<button id="button" type="button" class="btn bg-teal btn-sm btn-raised"><i class="fa fa-forward position-left"></i>{$lang['upgr_next']}</button>
		</div>
	</div>
HTML;

		echofooter();

	} else {

		echoheader( "<i class=\"fa fa-database position-left\"></i><span class=\"text-semibold\">{$lang['upgr_dbtitle_2']}</span>", $lang['upgr_dbtitle_3']);
		
		echo <<<HTML
	<div class="panel panel-default">
	  <div class="panel-heading">
		{$lang['upgr_dbtitle_2']}
	  </div>
		<div class="panel-body">
			{$lang['upgr_act_info']}<br /><br /><input type="text" name="sitekey" id="sitekey" placeholder="{$lang['trial_key']}" class="classic width-400 mr-10"><button onclick="dle_activation( 'key' ); return false;" class="btn bg-teal btn-raised btn-sm">{$lang['upgr_next']}</button><br /><br /><div id="result_info">{$lang['key_format']} <b>XXXXX-XXXXX-XXXXX-XXXXX-XXXXX</b></div>
		</div>
	</div>
	<script>
	<!--
	function dle_activation ( code ){
	
		var dle_key = document.getElementById('sitekey').value ;
		
		document.getElementById( 'result_info' ).innerHTML = '{$lang['nl_sinfo']}';
		
		$.post("?user_hash={$dle_login_hash}", { mod: 'upgrade', action: 'checklicense', dle_key: dle_key  }, function(data){

			if (data) {
	
				if (data.status == "ok") {
					setTimeout("window.location = '?mod=upgrade&action=download'", 300 );
				} else {
				
					document.getElementById( 'result_info' ).innerHTML = '{$lang['key_format']} <b>XXXXX-XXXXX-XXXXX-XXXXX-XXXXX</b>';
					DLEalert(data.error, '{$lang['all_info']}');
					
				}
	
			}
		
		}, "json");
	
		return false;
	}
	//-->
	</script>
HTML;

		echofooter();
		
	}

}

?>