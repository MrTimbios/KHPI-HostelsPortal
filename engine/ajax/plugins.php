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
 File: plugins.php
-----------------------------------------------------
 Use: AJAX plugins manage
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if($member_id['user_group'] != 1) {
	echo_error ($lang['sess_error']);
}

if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
	echo_error ($lang['sess_error']);
}

if( !check_referer( $config['http_home_url'].$config['admin_path']."?mod=plugins") ) {
	echo_error ($lang['no_referer']);
}

if( !$config['allow_plugins'] ) {
	echo_error ($lang['module_disabled']);
} elseif( DLEPlugins::$read_only ) {
	echo_error ($lang['plugins_errors_6']);
}

if(!function_exists('simplexml_load_string')) {
	echo_error ("You need the PHP 'SimpleXML' extension installed");
}

if( !class_exists('ZipArchive') ) {
	echo_error ("You need the PHP 'ZipArchive' extension installed");
}

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/zipextract.class.php'));

if($_POST['id']) {
	
	$id = intval($_POST['id']);
	unset($_SESSION['upload_plugins']['id']);
	
} else $id = 0;

if( !isset($_SESSION['upload_plugins']['id']) ) $_SESSION['upload_plugins']['id'] = $id;


if($_POST['action'] == "checkftp") {
	
	try {
		
		$fs = new dle_zip_extract();
		$fs->FtpConnect( $_POST['ftp'] );
		$fs->DisconnectFTP();
		
	} catch ( Exception $e ) {
		
		echo_error ($e->getMessage(), false);

	}

	$_SESSION['upload_plugins']['ftp'] = $_POST['ftp'];
	
}

if($_POST['action'] == "checkupdate") {
	
	$new_versions = array();
	
	if(!$id) $db->query( "SELECT id, version, upgradeurl FROM " . PREFIX . "_plugins" );
	else $db->query( "SELECT id, version, upgradeurl FROM " . PREFIX . "_plugins WHERE id='{$id}'" );
	
	while ( $row = $db->get_row() ) {
		
		if($row['upgradeurl']) {
			
			$row['upgradeurl'] = str_replace("&amp;", "&", $row['upgradeurl'] );
			
			$data = http_get_contents( $row['upgradeurl'], array( "version" => $config['version_id'] ));
			
			if($data) {
				$data = json_decode($data, true);
				
				if($data AND $data['version']) {
					
					if( version_compare($data['version'], $row['version'], '>') ) {

						if($data['url']) {
							if( $id ) {
								$new_versions = array('id' => $row['id'], 'version' => htmlspecialchars($data['version'], ENT_QUOTES, $config['charset'] ), 'url' => htmlspecialchars($data['url'], ENT_QUOTES, $config['charset'] ) );
							} else {
								$new_versions['versions'][] = array('id' => $row['id'], 'version' => htmlspecialchars($data['version'], ENT_QUOTES, $config['charset'] ), 'url' => htmlspecialchars($data['url'], ENT_QUOTES, $config['charset'] ) );
							}
						}

					}
					
				}
			}
			
		}
		
	}
	
	if( count($new_versions) ) {

		$new_versions['status'] = "succes";
		echo json_encode($new_versions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		die();

	}
	
	echo_error ($lang['plugins_upgr_e1']);

}

if($_POST['action'] == "updatefromurl") {
	
	if(!$id) echo_error ($lang['plugins_upgr_e1']);

	$pluginurl = trim( strip_tags( $_POST['url'] ) );
	$pluginurl = str_replace(chr(0), '', $pluginurl);
	$pluginurl = str_replace( "\\", "/", $pluginurl );

	$url = @parse_url ( $pluginurl );

    if (!array_key_exists('host', $url)) {
        echo_error ($lang['plugins_upgr_e2']);
    }

	if($url['scheme'] != 'http' AND $url['scheme'] != 'https') {
        echo_error ($lang['plugins_upgr_e2']);
	}
	
	$filename_arr = explode( ".", basename($pluginurl) );
	$type = strtolower(end( $filename_arr ));
	
	if($type != "xml" AND $type != "zip") {
		echo_error ($lang['plugins_errors_8']);
	}
	
	if( $type == "xml" ){
		
		$_FILES['pluginfile']['tmp_name'] = $pluginurl;
		$_FILES['pluginfile']['name'] = basename($pluginurl);
		
	} else {
		
		if(@copy($pluginurl, ENGINE_DIR . "/cache/system/" . md5('uploads_plugin'.SECURE_AUTH_KEY) . ".zip")) {
			
			$_FILES['pluginfile']['tmp_name'] = ENGINE_DIR . "/cache/system/" . md5('uploads_plugin'.SECURE_AUTH_KEY) . ".zip";
			$_FILES['pluginfile']['name'] = md5('uploads_plugin'.SECURE_AUTH_KEY) . ".zip";
			
		} else echo_error ($lang['plugins_upgr_e2']);
		
	}


}

if ( isset($_SESSION['upload_plugins']['file']) AND isset($_SESSION['upload_plugins']['ftp']) ) {
	
	if ( file_exists( ENGINE_DIR . "/cache/system/" . md5('uploads_plugin'.SECURE_AUTH_KEY) . ".zip" ) ) {
		
		$_FILES['pluginfile']['tmp_name'] = ENGINE_DIR . "/cache/system/" . md5('uploads_plugin'.SECURE_AUTH_KEY) . ".zip";
		$_FILES['pluginfile']['name'] = md5('uploads_plugin'.SECURE_AUTH_KEY) . ".zip";
		
	} else {
		echo_error ($lang['upload_error_3']);
	}
	
} elseif( $_POST['action'] != "updatefromurl" ) {
	
	if( !$_FILES['pluginfile']['tmp_name'] OR !is_uploaded_file( $_FILES['pluginfile']['tmp_name'] ) ) {
		echo_error ($lang['upload_error_3']);
	}
	
}

function echo_error ($text, $unset = true) {
	
	if($unset AND isset( $_SESSION['upload_plugins']['file'] ) ) {
		unset($_SESSION['upload_plugins']['file']);
		@unlink(ENGINE_DIR . "/cache/system/" . md5('uploads_plugin'.SECURE_AUTH_KEY) . ".zip");
	}
	
	if($unset AND isset( $_SESSION['upload_plugins']['id'] ) ) {
		unset( $_SESSION['upload_plugins']['id'] );
	}
	
	echo json_encode(array('status' => 'error', 'text' => $text));
	die();

}

function install_xml_plugin ($plugin, $id, $file_list) {
	global $config, $db, $member_id, $_TIME, $_IP, $lang;

	$id = intval($id);
	libxml_use_internal_errors(true);
	
	$xml = simplexml_load_string($plugin);
	
	if (!$xml) {
		
		$errors = libxml_get_errors();
		echo_error(sprintf( "XML error: %s at line %d", $errors[0]->message, $errors[0]->line ));
		
	} else {
		
		if(is_array($file_list) AND count($file_list)){
			$file_list = $db->safesql(implode(",", $file_list));
		} else $file_list = "";
		
		if ( $xml->name ) $name = (string)$xml->name;
		if ( $xml->description ) $description = (string)$xml->description;
		if ( $xml->icon ) $icon = (string)$xml->icon;
		if ( $xml->version ) $version = (string)$xml->version;
		if ( $xml->dleversion ) $dleversion = (string)$xml->dleversion;
		if ( $xml->versioncompare ) $versioncompare = (string)$xml->versioncompare;
		if ( $xml->upgradeurl ) $upgradeurl = (string)$xml->upgradeurl;
		if ( $xml->needplugin ) $needplugin = (string)$xml->needplugin;
		if ( $xml->filedelete ) $filedelete = trim((string)$xml->filedelete);
		if ( $xml->mnotice ) $mnotice = trim((string)$xml->mnotice);
		
		if( $versioncompare == "greater" ) $versioncompare = '>=';
		elseif ( $versioncompare == "less") $versioncompare = '<=';
		
		if ( $xml->mysqlinstall ) $_POST['mysqlinstall'] = trim((string)$xml->mysqlinstall);
		if ( $xml->mysqlupgrade ) $_POST['mysqlupgrade'] = trim((string)$xml->mysqlupgrade);
		if ( $xml->mysqlenable )  $_POST['mysqlenable'] = trim((string)$xml->mysqlenable);
		if ( $xml->mysqldisable ) $_POST['mysqldisable'] = trim((string)$xml->mysqldisable);
		if ( $xml->mysqldelete )  $_POST['mysqldelete'] = trim((string)$xml->mysqldelete);

		if ( $xml->phpinstall ) $_POST['phpinstall'] = trim((string)$xml->phpinstall);
		if ( $xml->phpupgrade ) $_POST['phpupgrade'] = trim((string)$xml->phpupgrade);
		if ( $xml->phpenable )  $_POST['phpenable'] = trim((string)$xml->phpenable);
		if ( $xml->phpdisable ) $_POST['phpdisable'] = trim((string)$xml->phpdisable);
		if ( $xml->phpdelete )  $_POST['phpdelete'] = trim((string)$xml->phpdelete);
		
		if ( $xml->notice )  $_POST['notice'] = trim((string)$xml->notice);
		
		$i=0;
		$t=0;
		
		if ( $xml->file ) {
			foreach ($xml->file as $file) {
				$i++;
				$_POST['file'][$i] = (string)$file->attributes()->name;
				
				if ( $file->operation ) {
					foreach ($file->operation as $operation) {
						$t++;
						$_POST['fileaction'][$i][$t] = (string)$operation->attributes()->action;
						
						if($operation->searchcode) $_POST['filesearch'][$i][$t] = (string)$operation->searchcode;
						if($operation->replacecode) $_POST['filereplace'][$i][$t] = (string)$operation->replacecode;
						if($operation->searchcount) $_POST['filefindcount'][$i][$t] = (string)$operation->searchcount;
						if($operation->replacecount) $_POST['filereplacecount'][$i][$t] = (string)$operation->replacecount;
						
					}
					
					
				}
				
			}
		}
		
		$name = $db->safesql(htmlspecialchars( trim($name), ENT_QUOTES, $config['charset'] ));
		$description = $db->safesql(htmlspecialchars( trim($description), ENT_QUOTES, $config['charset'] ));
		$icon = $db->safesql( clearfilepath( htmlspecialchars( trim($icon), ENT_QUOTES, $config['charset'] ), array ("gif", "jpg", "jpeg", "png", "webp" ) ) );
		$version = $db->safesql(htmlspecialchars( trim($version), ENT_QUOTES, $config['charset'] ));
		$dleversion = $db->safesql(htmlspecialchars( trim($dleversion), ENT_QUOTES, $config['charset'] ));
		$upgradeurl = $db->safesql( htmlspecialchars( trim($upgradeurl), ENT_QUOTES, $config['charset'] ) );
		$needplugin = $db->safesql( htmlspecialchars( trim($needplugin), ENT_QUOTES, $config['charset'] ) );
		$filedelete = intval($filedelete);
		$mnotice = intval($mnotice);
		$plugin_active = 1;
		
		if ( in_array( $versioncompare, array("==", ">=", "<=") ) ) $versioncompare = $db->safesql($versioncompare); else $versioncompare = '';
		
		$mysqlinstall = $db->safesql($_POST['mysqlinstall']);
		$mysqlupgrade = $db->safesql($_POST['mysqlupgrade']);
		$mysqlenable = $db->safesql($_POST['mysqlenable']);
		$mysqldisable = $db->safesql($_POST['mysqldisable']);
		$mysqldelete = $db->safesql($_POST['mysqldelete']);

		$phpinstall = $db->safesql($_POST['phpinstall']);
		$phpupgrade = $db->safesql($_POST['phpupgrade']);
		$phpenable = $db->safesql($_POST['phpenable']);
		$phpdisable = $db->safesql($_POST['phpdisable']);
		$phpdelete = $db->safesql($_POST['phpdelete']);
		
		$notice = $db->safesql(trim($_POST['notice']));
		
		if( $dleversion AND $versioncompare) {
			if( !version_compare($config['version_id'], $dleversion, $versioncompare) ) $plugin_active = 0;
		}
		
		if( !$name ) echo_error ($lang['plugins_nerror']);
		
		$files = array();
		$allowed_action =array("replace", "before", "after", "replaceall", "create");
		
		if(is_array($_POST['file']) AND count($_POST['file']) ) {
			
			foreach($_POST['file'] as $key => $value) {
				$file_name = clearfilepath( trim($value) , array ("php", "lng" ) );
				
				if(!$file_name) continue;
				
				if( in_array( $file_name, DLEPlugins::$protected_files ) ) {
					
					$lang['plugins_errors_7'] = str_replace ("{file}", $file_name, $lang['plugins_errors_7']);
					echo_error ($lang['plugins_errors_7']);

				}
		
				if(is_array($_POST['fileaction'][$key]) AND count($_POST['fileaction'][$key]) ) {
					
					foreach($_POST['fileaction'][$key] as $key2 => $value2) {
						
						if( !in_array($value2, $allowed_action) ) continue;
						
						$file_action = $value2;
						$file_search = $_POST['filesearch'][$key][$key2];
						$file_replace = $_POST['filereplace'][$key][$key2];
						$searchcount = intval($_POST['filefindcount'][$key][$key2]);
						$replacecount = intval($_POST['filereplacecount'][$key][$key2]);
						
						if( !trim($file_search) ) $file_search ='';
						if( !trim($file_replace) ) $file_replace ='';
	
						if( ($file_action == "replace" OR $file_action == "before" OR $file_action == "after") AND !$file_search ) continue;
						
						if( ($file_action == "before" OR $file_action == "after" OR $file_action == "replaceall" OR $file_action == "create") AND !$file_replace) continue;
						
						$files[$file_name][] = array('action' => $file_action, 'searchcode' => $file_search, 'replacecode' => $file_replace, 'searchcount' => $searchcount, 'replacecount' => $replacecount );
	
					}
				}
				
			}
		}
		
		if (!$id) {
			
			$row = $db->super_query( "SELECT id FROM " . PREFIX . "_plugins WHERE name='{$name}'" );
			
			if( $row['id'] ) {
				echo_error ($lang['plugins_nerror_1']);
			}
			
			if ($needplugin) {
				$row = $db->super_query( "SELECT id FROM " . PREFIX . "_plugins WHERE name='{$needplugin}'" );
				
				if(!$row['id']) $plugin_active = 0;
			}
		
			$db->query( "INSERT INTO " . PREFIX . "_plugins (name, description, icon, version, dleversion, versioncompare, active, mysqlinstall, mysqlupgrade, mysqlenable, mysqldisable, mysqldelete, filedelete, filelist, upgradeurl, needplugin, phpinstall, phpupgrade, phpenable, phpdisable, phpdelete, notice, mnotice) values ('{$name}', '{$description}','{$icon}','{$version}','{$dleversion}','{$versioncompare}', '{$plugin_active}', '{$mysqlinstall}', '{$mysqlupgrade}','{$mysqlenable}','{$mysqldisable}','{$mysqldelete}','{$filedelete}','{$file_list}', '{$upgradeurl}', '{$needplugin}', '{$phpinstall}', '{$phpupgrade}','{$phpenable}','{$phpdisable}','{$phpdelete}', '{$notice}', '{$mnotice}')" );
			$id = $_SESSION['upload_plugins']['id'] = $db->insert_id();
			$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '116', '{$name}')" );
	
			execute_query($id, $_POST['mysqlinstall'] );
			
			if ($plugin_active) {
				execute_query($id, $_POST['mysqlenable'] );
			}
			
			$row = $db->super_query( "SELECT phpinstall, phpenable FROM " . PREFIX . "_plugins WHERE id='{$id}'" );
	
			if($row['phpinstall']) {
				eval($row['phpinstall']);
			}
			
			if($row['phpenable'] AND $plugin_active) {
				eval($row['phpenable']);
			}
		
		} else {
			
			$row = $db->super_query( "SELECT id FROM " . PREFIX . "_plugins WHERE id='{$id}'" );
			
			if (!$row['id']) echo_error ("ID not valid", "ID not valid");
			
			$row = $db->super_query( "SELECT id FROM " . PREFIX . "_plugins WHERE name='{$name}'" );
		
			if( $row['id'] AND $row['id'] != $id ) {
				echo_error ($lang['plugins_nerror_1']);
			}
		
			$db->query( "DELETE FROM " . PREFIX . "_plugins_logs WHERE plugin_id = '{$id}'" );
			$db->query( "UPDATE " . PREFIX . "_plugins SET name='{$name}', description='{$description}', icon='{$icon}', version='{$version}', dleversion='{$dleversion}', versioncompare='{$versioncompare}', mysqlinstall='{$mysqlinstall}', mysqlupgrade='{$mysqlupgrade}', mysqlenable='{$mysqlenable}', mysqldisable='{$mysqldisable}', mysqldelete='{$mysqldelete}', filedelete='{$filedelete}', filelist='{$file_list}', upgradeurl='{$upgradeurl}', phpinstall='{$phpinstall}', phpupgrade='{$phpupgrade}', phpenable='{$phpenable}', phpdisable='{$phpdisable}', phpdelete='{$phpdelete}', notice='{$notice}', mnotice='{$mnotice}' WHERE id='{$id}'" );
			$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '117', '{$name}')" );
	
			execute_query($id, $_POST['mysqlupgrade'] );
			
			$row = $db->super_query( "SELECT phpupgrade FROM " . PREFIX . "_plugins WHERE id='{$id}'" );
	
			if($row['phpupgrade']) {
				eval($row['phpupgrade']);
			}
			
		}
		
		$db->query( "DELETE FROM " . PREFIX . "_plugins_files WHERE plugin_id='{$id}'" );
		
		if(count($files)) {
			
			$row = $db->super_query( "SELECT active FROM " . PREFIX . "_plugins WHERE id='{$id}'" );
			
			foreach( $files as $key => $value ) {
				foreach ($value as $value2) {
					$key = $db->safesql($key);
					$value2['action'] = $db->safesql($value2['action']);
					$value2['searchcode'] = $db->safesql($value2['searchcode']);
					$value2['replacecode'] = $db->safesql($value2['replacecode']);
					$value2['searchcount'] = intval($value2['searchcount']);
					$value2['replacecount'] = intval($value2['replacecount']);
					
					$db->query( "INSERT INTO " . PREFIX . "_plugins_files (plugin_id, file, action, searchcode, replacecode, searchcount, active, replacecount) values ('{$id}', '{$key}', '{$value2['action']}', '{$value2['searchcode']}', '{$value2['replacecode']}', '{$value2['searchcount']}', '{$row['active']}', '{$value2['replacecount']}')" );
				}
	
			}
	
		}
		
	}

}

function folders_check_chmod( $dir,  $bad_folders = array() ) {

	$folder = str_replace(ROOT_DIR, "", $dir);

	if(!is_writable($dir)) {
		$bad_folders[] = $folder;
	}

	if ( $dh = @opendir( $dir ) ) {
		
		while ( false !== ( $file = readdir($dh) ) ) {
			
			if ( $file == '.' or $file == '..' or $file == '.svn' or $file == '.DS_store' ) {
					continue;
			}
		
			if ( is_dir( $dir . "/" . $file ) ) {

				$bad_folders = folders_check_chmod( $dir . "/" . $file, $bad_folders );
				
			}
		}
	}
	
	return $bad_folders;
}


$filename_arr = explode( ".", $_FILES['pluginfile']['name'] );
$type = strtolower(end( $filename_arr ));
$file_list = array();

if($type != "xml" AND $type != "zip") {
	echo_error ($lang['plugins_errors_8']);
}

if( $type == "xml" ){
	$plugin_file = trim( @file_get_contents($_FILES['pluginfile']['tmp_name']) );
	
	if(!$plugin_file) {
		echo_error ($lang['plugins_upgr_e2']);
	}
	
	install_xml_plugin($plugin_file, $_SESSION['upload_plugins']['id'], $file_list);
	
	
} else {
	
	include_once (DLEPlugins::Check(ENGINE_DIR.'/classes/antivirus.class.php'));
	$zip = new ZipArchive();
	$antivirus = new antivirus();
	
	if(@$zip->open( $_FILES['pluginfile']['tmp_name'], ZIPARCHIVE::CHECKCONS ) !== true) {
		echo_error ($lang['upgr_f_error_16']);
	}
	
	$plugin_file = false;
	$plugin_file_index = false;
	
	for ( $i = 0; $i < $zip->numFiles; $i++ ) {

		if ( $zip->statIndex($i) ) {
			$file = $zip->statIndex($i);
			
			if ( substr($file['name'], -1) == '/' ) continue;
			
			$filename_arr = explode( ".", $file['name'] );
			$type = strtolower(end( $filename_arr ));
			
			if( $type == "xml" AND strpos($file['name'], "/") == false ) {
				$plugin_file = $zip->getFromIndex($i);
				$plugin_file_index = $i;
				continue;
			}
			
			if(in_array("./" . $file['name'], $antivirus->good_files)) {
				echo_error ($lang['plugins_errors_10']);
				
			} else $file_list[] = $file['name'];

		}

	}
	
	if( !$plugin_file ) {
		echo_error ($lang['plugins_errors_9']);
	}
	
	$no_access = folders_check_chmod(ROOT_DIR."/engine" );
	$no_access = array_merge($no_access, folders_check_chmod(ROOT_DIR."/language" ) );
	
	if(count($no_access) AND !isset( $_SESSION['upload_plugins']['ftp'] )) {

		if($_POST['action'] == "updatefromurl") $uploaded=true;
		else $uploaded=@move_uploaded_file($_FILES['pluginfile']['tmp_name'], ENGINE_DIR . "/cache/system/" . md5('uploads_plugin'.SECURE_AUTH_KEY) . ".zip");

        if( $uploaded ) {
			$_SESSION['upload_plugins']['file'] = true;
			echo "{\"status\": \"needftp\"}";
			die();
        } else {
			echo_error ("{$lang['media_upload_st6']} {$_FILES['pluginfile']['name']} {$lang['media_upload_st10']}");
		}
		
	}
	
	install_xml_plugin($plugin_file, $_SESSION['upload_plugins']['id'], $file_list);
	
	try {
		
		$fs = new dle_zip_extract( $_FILES['pluginfile']['tmp_name'] );
		$fs->skip_index[] = $plugin_file_index;
		$fs->is_plugin = true;
		
		if( $_SESSION['upload_plugins']['ftp'] ) {
			$fs->FtpConnect( $_SESSION['upload_plugins']['ftp'] );
		}
		
		$fs->ExtractZipArchive();
		
		if( $_SESSION['upload_plugins']['ftp'] ) {
			$fs->DisconnectFTP();
		}
		
		if( isset( $_SESSION['upload_plugins']['file'] ) ) {
			unset($_SESSION['upload_plugins']['file']);
			@unlink(ENGINE_DIR . "/cache/system/" . md5('uploads_plugin'.SECURE_AUTH_KEY) . ".zip");
		}
		
		if( count($fs->errors_list) ) {
			foreach($fs->errors_list as $error) {
				$db->query( "INSERT INTO " . PREFIX . "_plugins_logs (plugin_id, area, error, type) values ('{$_SESSION['upload_plugins']['id']}', '".$db->safesql( htmlspecialchars( $error['file'], ENT_QUOTES, $config['charset'] ), false)."', '".$db->safesql( htmlspecialchars( $error['error'], ENT_QUOTES, $config['charset'] ) )."', 'upload')" );
			}
		}
		
	} catch ( Exception $e ) {

		echo_error ($e->getMessage());
		
	}

}

unset($_SESSION['upload_plugins']['id']);
clear_all_caches();
echo "{\"status\": \"succes\"}";

?>