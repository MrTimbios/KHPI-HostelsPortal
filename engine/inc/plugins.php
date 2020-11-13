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
 Use: Plugins Manager
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if($member_id['user_group'] != 1){ msg("error", $lang['addnews_denied'], $lang['db_denied']); }

if( isset( $_REQUEST['id'] ) ) $id = intval( $_REQUEST['id'] ); else $id = 0;

function makeDropDown($options, $name, $selected) {
	global $config;

	$output = "<select class=\"uniform\" name=\"{$name}\" style=\"min-width:100px;\">\r\n";
	foreach ( $options as $value => $description ) {
		$output .= "<option value=\"".htmlspecialchars( $value, ENT_QUOTES, $config['charset'] )."\"";
		
		if( $selected == $value ) {
			$output .= " selected ";
		}
		
		$output .= ">{$description}</option>\n";
	}
	$output .= "</select>";
	return $output;
}

if( $_GET['action'] == "on" OR $_GET['action'] == "off" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( !$config['allow_plugins'] ) {
		msg( "error", $lang['addnews_error'], $lang['module_disabled'], "?mod=plugins" );
	} elseif( DLEPlugins::$read_only ) {
		msg( "error", $lang['addnews_error'], $lang['plugins_errors_6'], "?mod=plugins" );
	}

	if( !check_referer($_SERVER['PHP_SELF']."?mod=plugins") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	$row = $db->super_query( "SELECT id, dleversion, versioncompare, mysqlenable, mysqldisable, needplugin, phpenable, phpdisable FROM " . PREFIX . "_plugins WHERE id='{$id}'" );
	
	if (!$row['id']) msg( "error", "ID not valid", "ID not valid" );
	
	if( $_GET['action'] == "on" ) {
		
		$active = 1;
		
		if( $row['dleversion'] AND $row['versioncompare'] ) {
			if( !version_compare($config['version_id'], $row['dleversion'], $row['versioncompare']) ) {
				
				$row['versioncompare'] = str_replace(array("==", ">=", "<="), array($lang['plugins_vc_1'], $lang['plugins_vc_2'], $lang['plugins_vc_3']), $row['versioncompare']);
				$lang['plugins_nerror_2'] = str_replace(array("{version}", "{versioncompare}", "{dleversion}"), array($row['dleversion'],$row['versioncompare'],$config['version_id']), $lang['plugins_nerror_2']);
				
				msg( "error", $lang['addnews_error'], $lang['plugins_nerror_2'], "javascript:history.go(-1)" );
			}
		}
		
		if ($row['needplugin']) {
			
			$find_need = $db->super_query( "SELECT id FROM " . PREFIX . "_plugins WHERE name='{$row['needplugin']}' AND id!='{$id}'" );
			
			if(!$find_need['id']) {
				$lang['plugins_nerror_3'] = str_replace("{plugin}", $row['needplugin'], $lang['plugins_nerror_3']);
				msg( "error", $lang['addnews_error'], $lang['plugins_nerror_3'], "javascript:history.go(-1)" );
			}
			
		}
		
		if( $row['mysqlenable'] ) {
			execute_query($id, $row['mysqlenable']);	
		}
		
		if($row['phpenable']) {
			eval($row['phpenable']);
		}
		
	} else {
		
		$active = 0;
		
		if( $row['mysqldisable'] ) {
			execute_query($id, $row['mysqldisable']);
		}
		
		if( $row['phpdisable'] ) {
			eval($row['phpdisable']);
		}
	}
	
	$db->query( "UPDATE " . PREFIX . "_plugins SET active='{$active}' WHERE id='{$id}'" );
	$db->query( "UPDATE " . PREFIX . "_plugins_files SET active='{$active}' WHERE plugin_id='{$id}'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '114{$active}', '{$id}')" );

	clear_all_caches();
	
	header( "Location: ?mod=plugins" );
	die();
}

if( $_GET['action'] == "clearerrors" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( !$config['allow_plugins'] ) {
		msg( "error", $lang['addnews_error'], $lang['module_disabled'], "?mod=plugins" );
	} elseif( DLEPlugins::$read_only ) {
		msg( "error", $lang['addnews_error'], $lang['plugins_errors_6'], "?mod=plugins" );
	}
	
	if($id) $db->query( "DELETE FROM " . PREFIX . "_plugins_logs WHERE plugin_id = '{$id}'" );
	else $db->query( "DELETE FROM " . PREFIX . "_plugins_logs" );

	header( "Location: ?mod=plugins" );
	die();
	
}

if( $_GET['action'] == "delete" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=plugins") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	if( !$config['allow_plugins'] ) {
		msg( "error", $lang['addnews_error'], $lang['module_disabled'], "?mod=plugins" );
	} elseif( DLEPlugins::$read_only ) {
		msg( "error", $lang['addnews_error'], $lang['plugins_errors_6'], "?mod=plugins" );
	}
	
	$row = $db->super_query( "SELECT id, mysqldelete, filedelete, filelist, phpdelete FROM " . PREFIX . "_plugins WHERE id='{$id}'" );
	
	if (!$row['id']) msg( "error", "ID not valid", "ID not valid" );
	
	if($row['mysqldelete']) {
		execute_query($id, $row['mysqldelete']);
	}
	
	if($row['filedelete'] AND $row['filelist']) {
		$filelist = explode(",", $row['filelist']);
		if(count($filelist)) {
			foreach($filelist as $file){
				$file = trim($file);
				if($file) @unlink( ROOT_DIR."/".$file );
			}
		}
	}
	
	$db->query( "DELETE FROM " . PREFIX . "_plugins WHERE id='{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_plugins_files WHERE plugin_id='{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_plugins_logs WHERE plugin_id = '{$id}'" );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '115', '{$id}')" );

	clear_all_caches();
	
	if($row['phpdelete']) {
		
		eval($row['phpdelete']);
		
	}
	
	header( "Location: ?mod=plugins" );
	die();
	
}

if( $_REQUEST['action'] == "download" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt!" );
	
	}
	
	$row = $db->super_query( "SELECT * FROM " . PREFIX . "_plugins WHERE id='{$id}'" );
	
	if (!$row['id']) msg( "error", "ID not valid", "ID not valid" );	

	if($row['versioncompare'] == ">=" ) $row['versioncompare'] = 'greater';
	elseif ($row['versioncompare'] == "<=") $row['versioncompare'] = 'less';
	
	$plugin_f_name = totranslit($row['name'], true, false );
	$files = array();

	$db->query( "SELECT * FROM " . PREFIX . "_plugins_files WHERE plugin_id='{$id}' ORDER BY id ASC" );
		
	while ( $filerow = $db->get_row() ) {
		$filerow['file'] = htmlspecialchars( $filerow['file'], ENT_QUOTES, $config['charset'] );
		$files[$filerow['file']][] = array('action' => $filerow['action'], 'searchcode' => $filerow['searchcode'], 'replacecode' => $filerow['replacecode'], 'searchcount' => intval($filerow['searchcount']), 'replacecount' => intval($filerow['replacecount']) );
	}
	
	$x='';
	
	if(count($files)) {
		
		foreach( $files as $key => $value ) {

			$x .=  "\n\t<file name=\"$key\">";
			
			foreach ($value as $value2) {
				$x .=  "\n\t\t<operation action=\"{$value2['action']}\">";
				
				if($value2['searchcode']) {
					$x .=  "\n\t\t\t<searchcode><![CDATA[{$value2['searchcode']}]]></searchcode>";
				}
				
				if($value2['replacecode']) {
					$x .=  "\n\t\t\t<replacecode><![CDATA[{$value2['replacecode']}]]></replacecode>";
				}
				
				if($value2['searchcount']) {
					$x .=  "\n\t\t\t<searchcount>{$value2['searchcount']}</searchcount>";
				}
				
				if($value2['replacecount']) {
					$x .=  "\n\t\t\t<replacecount>{$value2['replacecount']}</replacecount>";
				}
				
				$x .=  "\n\t\t</operation>";
			}
			
			$x .= "\n\t</file>";
		}
	}
	
	$plugin_data = <<<HTML
<?xml version="1.0" encoding="{$config['charset']}"?>
<dleplugin>
	<name>{$row['name']}</name>
	<description>{$row['description']}</description>
	<icon>{$row['icon']}</icon>
	<version>{$row['version']}</version>
	<dleversion>{$row['dleversion']}</dleversion>
	<versioncompare>{$row['versioncompare']}</versioncompare>
	<upgradeurl>{$row['upgradeurl']}</upgradeurl>
	<filedelete>{$row['filedelete']}</filedelete>
	<needplugin>{$row['needplugin']}</needplugin>
	<mnotice>{$row['mnotice']}</mnotice>
	<mysqlinstall><![CDATA[{$row['mysqlinstall']}]]></mysqlinstall>
	<mysqlupgrade><![CDATA[{$row['mysqlupgrade']}]]></mysqlupgrade>
	<mysqlenable><![CDATA[{$row['mysqlenable']}]]></mysqlenable>
	<mysqldisable><![CDATA[{$row['mysqldisable']}]]></mysqldisable>
	<mysqldelete><![CDATA[{$row['mysqldelete']}]]></mysqldelete>
	<phpinstall><![CDATA[{$row['phpinstall']}]]></phpinstall>
	<phpupgrade><![CDATA[{$row['phpupgrade']}]]></phpupgrade>
	<phpenable><![CDATA[{$row['phpenable']}]]></phpenable>
	<phpdisable><![CDATA[{$row['phpdisable']}]]></phpdisable>
	<phpdelete><![CDATA[{$row['phpdelete']}]]></phpdelete>
	<notice><![CDATA[{$row['notice']}]]></notice>{$x}
</dleplugin>
HTML;

	header( "Pragma: public" );
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header( "Cache-Control: private", false);
	header( "Content-Type: text/xml; charset={$config['charset']}" );
	header( 'Content-Disposition: attachment; filename="'.$plugin_f_name.'.xml"' );
	header( "Content-Transfer-Encoding: binary" );
	header( "Connection: close");
	
	echo $plugin_data;

	die();
}

if( $_REQUEST['action'] == "doadd" OR $_REQUEST['action'] == "doedit" ) {
	
	@header('X-XSS-Protection: 0;');

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		msg( "error", $lang['addnews_error'], "Hacking attempt! User not found", "?mod=plugins" );
	}
	
	if( !$config['allow_plugins'] ) {
		msg( "error", $lang['addnews_error'], $lang['module_disabled'], "?mod=plugins" );
	} elseif( DLEPlugins::$read_only ) {
		msg( "error", $lang['addnews_error'], $lang['plugins_errors_6'], "?mod=plugins" );
	}

	if( defined('DEMOVERSION') ) {
		msg( "error", $lang['addnews_error'], $lang['upgr_demo_1'], "?mod=plugins" );
	}
	
	if( !check_referer($_SERVER['PHP_SELF']."?mod=plugins") ) {
		msg( "error", $lang['index_denied'], $lang['no_referer'], "javascript:history.go(-1)" );
	}
	
	$name = $db->safesql(htmlspecialchars( trim($_POST['name']), ENT_QUOTES, $config['charset'] ));
	$description = $db->safesql(htmlspecialchars( trim($_POST['description']), ENT_QUOTES, $config['charset'] ));
	$icon = $db->safesql( clearfilepath( htmlspecialchars( trim($_POST['icon']), ENT_QUOTES, $config['charset'] ), array ("gif", "jpg", "jpeg", "png", "webp" ) ) );
	$upgradeurl = $db->safesql( htmlspecialchars( trim($_POST['upgradeurl']), ENT_QUOTES, $config['charset'] ) );
	$needplugin = $db->safesql( htmlspecialchars( trim($_POST['needplugin']), ENT_QUOTES, $config['charset'] ) );
	$version = $db->safesql(htmlspecialchars( trim($_POST['version']), ENT_QUOTES, $config['charset'] ));
	$dleversion = $db->safesql(htmlspecialchars( trim($_POST['dleversion']), ENT_QUOTES, $config['charset'] ));
	
	$mysqlinstall = $db->safesql(trim($_POST['mysqlinstall']));
	$mysqlupgrade = $db->safesql(trim($_POST['mysqlupgrade']));
	$mysqlenable = $db->safesql(trim($_POST['mysqlenable']));
	$mysqldisable = $db->safesql(trim($_POST['mysqldisable']));
	$mysqldelete = $db->safesql(trim($_POST['mysqldelete']));
	
	$phpinstall = $db->safesql(trim($_POST['phpinstall']));
	$phpupgrade = $db->safesql(trim($_POST['phpupgrade']));
	$phpenable = $db->safesql(trim($_POST['phpenable']));
	$phpdisable = $db->safesql(trim($_POST['phpdisable']));
	$phpdelete = $db->safesql(trim($_POST['phpdelete']));
	
	$notice = $db->safesql(trim($_POST['notice']));
	$mnotice = intval($_POST['mnotice']);
	
	$filedelete = intval($_POST['filedelete']);
	
	$plugin_active = 1;
	
	if ( in_array( $_POST['versioncompare'], array("==", ">=", "<=") ) ) $versioncompare = $db->safesql($_POST['versioncompare']); else $versioncompare = '';
	
	if( $dleversion AND $versioncompare) {
		if( !version_compare($config['version_id'], $dleversion, $versioncompare) ) $plugin_active = 0;
	}
	
	if( !$name ) msg( "error", $lang['addnews_error'], $lang['plugins_nerror'], "javascript:history.go(-1)" );
	
	
	$files = array();
	$allowed_action =array("replace", "before", "after", "replaceall", "create");
	
	if(is_array($_POST['file']) AND count($_POST['file']) ) {
		
		foreach($_POST['file'] as $key => $value) {
			$file_name = clearfilepath( trim($value) , array ("php", "lng" ) );
			
			if(!$file_name) continue;
			
			if( in_array( $file_name, DLEPlugins::$protected_files ) ) {
				
				$lang['plugins_errors_7'] = str_replace ("{file}", $file_name, $lang['plugins_errors_7']);
				msg( "error", $lang['addnews_error'], $lang['plugins_errors_7'], "javascript:history.go(-1)" );
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
	
	if( $_REQUEST['action'] == "doadd" ) {
		
		$row = $db->super_query( "SELECT id FROM " . PREFIX . "_plugins WHERE name='{$name}'" );
		
		if( $row['id'] ) {
			msg( "error", $lang['addnews_error'], $lang['plugins_nerror_1'], "javascript:history.go(-1)" );
		}

		if ($needplugin) {
			$row = $db->super_query( "SELECT id FROM " . PREFIX . "_plugins WHERE name='{$needplugin}'" );
			
			if(!$row['id']) $plugin_active = 0;
		}
	
		$db->query( "INSERT INTO " . PREFIX . "_plugins (name, description, icon, version, dleversion, versioncompare, active, mysqlinstall, mysqlupgrade, mysqlenable, mysqldisable, mysqldelete, filedelete, upgradeurl, needplugin, phpinstall, phpupgrade, phpenable, phpdisable, phpdelete, notice, mnotice) values ('{$name}', '{$description}','{$icon}','{$version}','{$dleversion}','{$versioncompare}', '{$plugin_active}', '{$mysqlinstall}', '{$mysqlupgrade}','{$mysqlenable}','{$mysqldisable}','{$mysqldelete}', '{$filedelete}', '{$upgradeurl}', '{$needplugin}','{$phpinstall}', '{$phpupgrade}','{$phpenable}','{$phpdisable}','{$phpdelete}', '{$notice}', '{$mnotice}')" );
		$id = $db->insert_id();
		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '116', '{$name}')" );
		
		execute_query($id, trim($_POST['mysqlinstall']) );
		
		if ($plugin_active) {
			execute_query($id, trim($_POST['mysqlenable']) );
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
		
		if (!$row['id']) msg( "error", "ID not valid", "ID not valid" );
		
		$row = $db->super_query( "SELECT id FROM " . PREFIX . "_plugins WHERE name='{$name}'" );
	
		if( $row['id'] AND $row['id'] != $id ) {
			msg( "error", $lang['cat_error'], $lang['plugins_nerror_1'], "javascript:history.go(-1)" );
		}
	
		$db->query( "DELETE FROM " . PREFIX . "_plugins_logs WHERE plugin_id = '{$id}'" );
		
		$db->query( "UPDATE " . PREFIX . "_plugins SET name='{$name}', description='{$description}', icon='{$icon}', version='{$version}', dleversion='{$dleversion}', versioncompare='{$versioncompare}', mysqlinstall='{$mysqlinstall}', mysqlupgrade='{$mysqlupgrade}', mysqlenable='{$mysqlenable}', mysqldisable='{$mysqldisable}', mysqldelete='{$mysqldelete}', filedelete='{$filedelete}', upgradeurl='{$upgradeurl}', needplugin='{$needplugin}', phpinstall='{$phpinstall}', phpupgrade='{$phpupgrade}', phpenable='{$phpenable}', phpdisable='{$phpdisable}', phpdelete='{$phpdelete}', notice='{$notice}', mnotice='{$mnotice}' WHERE id='{$id}'" );
		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '117', '{$name}')" );
		
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
	
	clear_all_caches();
	
	header( "Location: ?mod=plugins" );
	die();
}

if( $_REQUEST['action'] == "add" OR $_REQUEST['action'] == "edit" ) {

	$files = array();

	if( $_REQUEST['action'] == "add" ) {
		
		$form_action = "?mod=plugins&amp;action=doadd";
		$row=array();
		$versioncompare = makeDropDown( array ("==" => $lang['plugins_vc_1'], ">=" => $lang['plugins_vc_2'], "<=" => $lang['plugins_vc_3'] ) , "versioncompare", 0 );
		$ifch1 = "";
		$ifch2 = "";

	} else {
		
		$form_action = "?mod=plugins&amp;action=doedit&amp;id=" . $id;
		$lang['plugins_bread'] = $lang['plugins_bread_1'];
		$lang['news_add'] = $lang['news_save'];

		$errors = "";
		
		$db->query( "SELECT * FROM " . PREFIX . "_plugins_logs WHERE plugin_id = '{$id}'" );
		
		while ( $row = $db->get_row() ) {
	
			if( $row['plugin_id'] AND $row['type'] == "mysql") {
				$area = $lang['upgr_db_query'];
				$row['area'] = "<pre><code>".$row['area']."</code></pre>";
			} elseif( $row['plugin_id'] AND $row['type'] == "file" ) {
				$area = $lang['plugins_filename'];
				$row['error'] = $lang['plugins_errors_4']."<pre><code>".$row['error']."</code></pre>";
			} elseif( $row['plugin_id'] AND $row['type'] == "upload" ) {
				$area = $lang['plugins_filename'];
				$row['error'] = "<pre><code>".$row['error']."</code></pre>";
			} else {
				$area = $lang['plugins_errors_5'];
			}
			
			$errors .= "<div class=\"quote\"><b>{$area}</b> {$row['area']}<div class=\"mt-10\"><b>{$lang['upgr_db_errt']}</b> {$row['error']}</div></div>";
			
		}

		$row = $db->super_query( "SELECT * FROM " . PREFIX . "_plugins WHERE id='{$id}'" );
		
		$versioncompare = makeDropDown( array ("==" => $lang['plugins_vc_1'], ">=" => $lang['plugins_vc_2'], "<=" => $lang['plugins_vc_3'] ) , "versioncompare", $row['versioncompare'] );
		$row['mysqlinstall'] = htmlspecialchars( $row['mysqlinstall'], ENT_QUOTES, $config['charset'] );
		$row['mysqlupgrade'] = htmlspecialchars( $row['mysqlupgrade'], ENT_QUOTES, $config['charset'] );
		$row['mysqlenable'] = htmlspecialchars( $row['mysqlenable'], ENT_QUOTES, $config['charset'] );
		$row['mysqldisable'] = htmlspecialchars( $row['mysqldisable'], ENT_QUOTES, $config['charset'] );
		$row['mysqldelete'] = htmlspecialchars( $row['mysqldelete'], ENT_QUOTES, $config['charset'] );

		
		$row['phpinstall'] = htmlspecialchars( $row['phpinstall'], ENT_QUOTES, $config['charset'] );
		$row['phpupgrade'] = htmlspecialchars( $row['phpupgrade'], ENT_QUOTES, $config['charset'] );
		$row['phpenable'] = htmlspecialchars( $row['phpenable'], ENT_QUOTES, $config['charset'] );
		$row['phpdisable'] = htmlspecialchars( $row['phpdisable'], ENT_QUOTES, $config['charset'] );
		$row['phpdelete'] = htmlspecialchars( $row['phpdelete'], ENT_QUOTES, $config['charset'] );
		
		$row['notice'] = htmlspecialchars( $row['notice'], ENT_QUOTES, $config['charset'] );

		if( $row['filedelete'] ) $ifch1 = " checked"; else $ifch1 = "";
		if( $row['mnotice'] ) $ifch2 = " checked"; else $ifch2 = "";

		$db->query( "SELECT * FROM " . PREFIX . "_plugins_files WHERE plugin_id='{$id}' ORDER BY id ASC" );
		
		while ( $filerow = $db->get_row() ) {
			
			$filerow['file'] = htmlspecialchars( $filerow['file'], ENT_QUOTES, $config['charset'] );
			
			if($filerow['replacecode'][0] == "\n" OR $filerow['replacecode'][0] == "\r") $filerow['replacecode'] = "\n".$filerow['replacecode'];
			
			
			$files[$filerow['file']][] = array('action' => htmlspecialchars($filerow['action'], ENT_QUOTES, $config['charset'] ), 'searchcode' => htmlspecialchars($filerow['searchcode'], ENT_QUOTES, $config['charset'] ), 'replacecode' => htmlspecialchars($filerow['replacecode'], ENT_QUOTES, $config['charset'] ), 'searchcount' => intval($filerow['searchcount']), 'replacecount' => intval($filerow['replacecount']) );
		}
	
	}
	
	$x = "";
	$total_files = 0;
	$total_action = 0;
	
	if(count($files)) {
		
		foreach( $files as $key => $value ) {
			$total_files ++;

			$x .=  "<div class=\"well\" id=\"filefieldset_{$total_files}\"><fieldset>";
			$x .= "<legend>{$lang['plugins_filename']}<input name=\"file[{$total_files}]\" type=\"text\" class=\"form-control width-500 position-right\" value=\"{$key}\"><i class=\"help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right\" data-rel=\"popover\" data-trigger=\"hover\" data-placement=\"right\" data-content=\"{$lang['plugins_file_h']}\"></i><div class=\"pull-right\"><a onclick=\"javascript:fdel('{$total_files}'); return(false);\"><i class=\"fa fa-trash-o text-danger\"></i></a></div></legend>";
			$x .= "<div id=\"actions_{$total_files}\">";
			
			foreach ($value as $value2) {
				$total_action ++;
				
				$f_action = array();

				$f_action[$value2['action']] = " selected";
		
				$x .=  "<fieldset id=\"actionset_{$total_action}\">";
				$x .= "<legend><span class=\"position-left\">{$lang['vote_action']}</span>";
				$x .= "<select class=\"uniform position-right\" name=\"fileaction[{$total_files}][{$total_action}]\" onchange=\"onActionChange(this, {$total_files}, {$total_action})\">";
				$x .= "<option value=\"\">{$lang['xfield_xact']}</option>";
				$x .= "<option value=\"replace\"{$f_action['replace']}>{$lang['plugins_aсt_l1']}</option>";
				$x .= "<option value=\"before\"{$f_action['before']}>{$lang['plugins_aсt_l2']}</option>";
				$x .= "<option value=\"after\"{$f_action['after']}>{$lang['plugins_aсt_l3']}</option>";
				$x .= "<option value=\"replaceall\"{$f_action['replaceall']}>{$lang['plugins_aсt_l4']}</option>";
				$x .= "<option value=\"create\"{$f_action['create']}>{$lang['plugins_aсt_l5']}</option>";
				$x .= "</select>";
				$x .= "<div class=\"pull-right\"><a onclick=\"javascript:adel('{$total_action}'); return(false);\"><i class=\"fa fa-trash-o text-danger\"></i></a></div></legend>";
				$x .= "<div id=\"searchs_{$total_action}\">";

				if($value2['searchcode']) {
					$x .=  "<div class=\"form-group\">";
					$x .= "<label class=\"control-label col-sm-2\">{$lang['plugins_aсt_l6']}</label>";
					$x .= "<div class=\"col-sm-10\">";
					$x .= "<textarea name=\"filesearch[{$total_files}][{$total_action}]\" id=\"text_search_{$total_files}_{$total_action}\" class=\"classic width-500 php_editor\" rows=\"3\">{$value2['searchcode']}</textarea>";
					$x .= "</div>";
					$x .= "</div>";
				}
				
				if($value2['replacecode'] OR $value2['action'] == "replace" ) {
					$rep_lang = $lang['plugins_aсt_l7'];
					
					if($value2['action'] == 'before' ) {
						$rep_lang="{$lang['plugins_aсt_l8']}";
					} elseif($value2['action'] == 'after') {
						$rep_lang="{$lang['plugins_aсt_l9']}";
					} elseif($value2['action'] == 'create') {
						$rep_lang="{$lang['plugins_aсt_l10']}";
					}
					
					$x .= "<div class=\"form-group\">";
					$x .= "<label class=\"control-label col-sm-2\">{$rep_lang}</label>";
					$x .= "<div class=\"col-sm-10\">";
					$x .= "<textarea name=\"filereplace[{$total_files}][{$total_action}]\" id=\"text_replace_{$total_files}_{$total_action}\" class=\"classic width-500 php_editor\" rows=\"5\">{$value2['replacecode']}</textarea>";
					$x .= "</div></div>";	
				}

				if($value2['searchcode']) {
				
					if( !$value2['searchcount'] ) $value2['searchcount'] = "";
					if( !$value2['replacecount'] ) $value2['replacecount'] = "";
					
					$x .= "<div class=\"form-group\">";
					$x .= "<label class=\"control-label col-sm-2\">{$lang['plugins_aсt_l11']}</label>";
					$x .= "<div class=\"col-sm-10\">";
					$x .= "<input type=\"text\" name=\"filefindcount[{$total_files}][{$total_action}]\" id=\"find_count_{$total_files}_{$total_action}\" class=\"form-control\" maxlength=\"3\" style=\"width:55px;\" value=\"{$value2['searchcount']}\"><i class=\"help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left\" data-rel=\"popover\" data-trigger=\"hover\" data-placement=\"right\" data-content=\"{$lang['plugins_aсt_hint']}\"></i>";
					$x .= "</div></div>";			

					$x .= "<div class=\"form-group\">";
					$x .= "<label class=\"control-label col-sm-2\">{$lang['plugins_aсt_l12']}</label>";
					$x .= "<div class=\"col-sm-10\">";
					$x .= "<input type=\"text\" name=\"filereplacecount[{$total_files}][{$total_action}]\" id=\"replace_count_{$total_files}_{$total_action}\" class=\"form-control\" maxlength=\"3\" style=\"width:55px;\" value=\"{$value2['replacecount']}\"><i class=\"help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left\" data-rel=\"popover\" data-trigger=\"hover\" data-placement=\"right\" data-content=\"{$lang['plugins_rep_hint']}\"></i>";
					$x .= "</div></div>";	
				}
	
				$x .="</div></fieldset>";
				
			}
			
			$x .= "</div>";
			$x .= "<button type=\"button\" onclick=\"AddAction('{$total_files}'); return false;\" class=\"btn bg-brown-600 btn-sm btn-raised position-left\"><i class=\"fa fa-plus position-left\"></i>{$lang['plugins_addact']}</button>";
			$x .= "</fieldset></div>";
		}
	}
		
	$js_array[] = "engine/skins/codemirror/js/code.js";
	$css_array[] = "engine/skins/codemirror/css/default.css";

	echoheader( "<i class=\"fa fa-puzzle-piece position-left\"></i><span class=\"text-semibold\">{$lang['opt_plugins']}</span>", array('?mod=plugins' => $lang['plugins_list'], '' => $lang['plugins_bread'] ) );
	
	if ($errors) {
		$error_tab = "<li><a href=\"#taberror\" data-toggle=\"tab\" class=\"text-danger\"><i class=\"fa fa-exclamation-triangle position-left\"></i> {$lang['plugins_bread_2']}</a></li>";
		
	$errors = <<<HTML
                <div class="tab-pane" id="taberror">
					<div class="panel-body">
						{$errors}
						<a href="?mod=plugins&action=clearerrors&user_hash={$dle_login_hash}&id={$id}" class="btn bg-brown-600 btn-sm btn-raised position-left"><i class="fa fa-trash position-left"></i>{$lang['plugins_errors_2']}</a>
					</div>
				</div>
HTML;

	} else {
		$error_tab = "";
	}
	
	echo <<<HTML
<style>
.CodeMirror {
	height: auto;
	border: 1px solid #cccccc;
    box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
}
.CodeMirror-scroll { 
	max-height: 300px; 
}
</style>
		<form action="{$form_action}" method="post" class="form-horizontal" id="addnews">
		<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
		<div class="panel panel-default">
		
		    <div class="panel-heading">
				<ul class="nav nav-tabs nav-tabs-solid">
					<li class="active"><a href="#tabhome" data-toggle="tab"><i class="fa fa-home position-left"></i> {$lang['plugins_tab_1']}</a></li>
					<li><a href="#tabfiles" data-toggle="tab"><i class="fa fa-file-code-o position-left"></i> {$lang['plugins_tab_2']}</a></li>
					<li><a href="#tabmysql" data-toggle="tab"><i class="fa fa-database position-left"></i> {$lang['plugins_tab_3']}</a></li>
					<li><a href="#tabphp" data-toggle="tab"><i class="fa fa-code position-left"></i> PHP</a></li>
					<li><a href="#notinfo" data-toggle="tab"><i class="fa fa-pencil-square-o position-left"></i> {$lang['main_p_notice']}</a></li>
					{$error_tab}
				</ul>
                <div class="heading-elements">
	                <ul class="icons-list">
						<li><a href="#" class="panel-fullscreen"><i class="fa fa-expand"></i></a></li>
					</ul>
                </div>
			</div>

            <div class="panel-tab-content tab-content">
			 
                <div class="tab-pane active" id="tabhome">
					<div class="panel-body">
					
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_name']}</label>
							<div class="col-sm-10">
							    <input type="text" class="form-control width-500 position-left" name="name" maxlength="100" value="{$row['name']}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['plugins_name_h']}"></i>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_descr']}</label>
							<div class="col-sm-10">
							    <textarea name="description" class="classic width-500" rows="3">{$row['description']}</textarea>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['cat_addicon']}</label>
							<div class="col-sm-10">
							    <input type="text" class="form-control width-500 position-left" name="icon" maxlength="255" value="{$row['icon']}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['plugins_icon_h']}"></i>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_ver']}</label>
							<div class="col-sm-10">
							    <input type="text" class="form-control" name="version" maxlength="10" value="{$row['version']}" style="width:100px;">
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_dlever']}</label>
							<div class="col-sm-10">
							    <input type="text" class="form-control position-left" name="dleversion" maxlength="10" value="{$row['dleversion']}" style="width:100px;">{$versioncompare}<i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['plugins_dleverh']}"></i>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_needp']}</label>
							<div class="col-sm-10">
							    <input type="text" class="form-control width-500 position-left" name="needplugin" maxlength="100" value="{$row['needplugin']}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['plugins_needp_h']}"></i>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_upgr']}</label>
							<div class="col-sm-10">
							    <input type="text" class="form-control width-500 position-left" name="upgradeurl" maxlength="255" value="{$row['upgradeurl']}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['plugins_upgr_h']}"></i>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">&nbsp;</label>
							<div class="col-sm-10">
							    <div class="checkbox"><label><input class="icheck" type="checkbox" name="filedelete" value="1"{$ifch1}>{$lang['plugins_filedel']}</label><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['plugins_filedelh']}"></i></div>
							</div>	
						</div>
						
					</div>
				</div>

                <div class="tab-pane" id="tabfiles">
					<div class="panel-body">
						<div id="container">{$x}</div>
						<button type="button" onclick="AddFile(); return false;" class="btn bg-slate-600 btn-sm btn-raised position-left"><i class="fa fa-plus position-left"></i>{$lang['plugins_addfile']}</button>
					</div>
				</div>
				
                <div class="tab-pane" id="tabmysql">
					<div class="panel-body">
					
						<div class="form-group">
							<label class="control-label col-sm-12">{$lang['plugins_myinfo_1']}</label>
						</div>
						
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_myinstall']}</label>
							<div class="col-sm-10">
							    <textarea name="mysqlinstall" class="classic width-500 sql_editor" rows="5">{$row['mysqlinstall']}</textarea>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_myupgrade']}</label>
							<div class="col-sm-10">
							    <textarea name="mysqlupgrade" class="classic width-500 sql_editor" rows="5">{$row['mysqlupgrade']}</textarea>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_myenable']}</label>
							<div class="col-sm-10">
							    <textarea name="mysqlenable" class="classic width-500 sql_editor" rows="5">{$row['mysqlenable']}</textarea>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_mydisable']}</label>
							<div class="col-sm-10">
							    <textarea name="mysqldisable" class="classic width-500 sql_editor" rows="5">{$row['mysqldisable']}</textarea>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_mydelete']}</label>
							<div class="col-sm-10">
							    <textarea name="mysqldelete" class="classic width-500 sql_editor" rows="5">{$row['mysqldelete']}</textarea>
							</div>	
						</div>
						
						<div class="alert alert-success">{$lang['plugins_myinfo']}</div>
						
					</div>
				</div>

                <div class="tab-pane" id="tabphp">
					<div class="panel-body">
					
						<div class="form-group">
							<label class="control-label col-sm-12">{$lang['plugins_pinfo_1']}</label>
						</div>
						
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_myinstall']}</label>
							<div class="col-sm-10">
							    <textarea name="phpinstall" class="classic width-500 php_editor" rows="5">{$row['phpinstall']}</textarea>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_myupgrade']}</label>
							<div class="col-sm-10">
							    <textarea name="phpupgrade" class="classic width-500 php_editor" rows="5">{$row['phpupgrade']}</textarea>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_myenable']}</label>
							<div class="col-sm-10">
							    <textarea name="phpenable" class="classic width-500 php_editor" rows="5">{$row['phpenable']}</textarea>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_mydisable']}</label>
							<div class="col-sm-10">
							    <textarea name="phpdisable" class="classic width-500 php_editor" rows="5">{$row['phpdisable']}</textarea>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">{$lang['plugins_mydelete']}</label>
							<div class="col-sm-10">
							    <textarea name="phpdelete" class="classic width-500 php_editor" rows="5">{$row['phpdelete']}</textarea>
							</div>	
						</div>
						
					</div>
				</div>

                <div class="tab-pane" id="notinfo">
					<div class="panel-body">
						<textarea name="notice" class="classic" style="width:100%;height:200px;" placeholder="{$lang['main_no_p_notice']}">{$row['notice']}</textarea>
						<div class="checkbox mt-10"><label><input class="icheck" type="checkbox" name="mnotice" value="1"{$ifch2}>{$lang['main_p_notice_1']}</label></div>
					</div>
				</div>
				
				{$errors}
				
			</div>


			<div class="panel-footer">
				<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['news_add']}</button>
			</div>
		</div>
		
		</form>
<script>  
<!--

$(function(){

	$(".sql_editor").each(function() {
		var editor = CodeMirror.fromTextArea( this, {
			mode: "text/x-mysql",
			dragDrop: false,
			autoRefresh: true,
			viewportMargin: Infinity
		  });
	
		function updateTextArea() {
			editor.save();
		}
	
		editor.on('change', updateTextArea);
	});
	
	$(".php_editor").each(function() {
		var editor = CodeMirror.fromTextArea( this, {
			mode: "text/x-php",
			dragDrop: false,
			autoRefresh: true,
			viewportMargin: Infinity
		  });
	
		function updateTextArea() {
			editor.save();
		}
	
		editor.on('change', updateTextArea);
	});

});

var files = {$total_files};
var actions = {$total_action};

function fdel(id){
	DLEconfirm( '{$lang['plugins_f_del']}', '{$lang['p_confirm']}', function () {
		$("#filefieldset_"+id).remove();
	} );
}

function adel(id){
	DLEconfirm( '{$lang['plugins_a_del']}', '{$lang['p_confirm']}', function () {
		$("#actionset_"+id).remove();
	} );
}

function AddFile(){
	files ++;
	
	var x =  "<div class=\"well\" id=\"filefieldset_" + files + "\"><fieldset>";
	x += "<legend>{$lang['plugins_filename']}<input name=\"file[" + files + "]\" type=\"text\" class=\"form-control width-500 position-right\"><i class=\"help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right\" data-rel=\"popover\" data-trigger=\"hover\" data-placement=\"right\" data-content=\"{$lang['plugins_file_h']}\"></i><div class=\"pull-right\"><a onclick=\"javascript:fdel('" + files + "'); return(false);\"><i class=\"fa fa-trash-o text-danger\"></i></a></div></legend>";
	x += "<div id=\"actions_" + files + "\"></div>";
	x += "<button type=\"button\" onclick=\"AddAction('" + files + "'); return false;\" class=\"btn bg-brown-600 btn-sm btn-raised position-left\"><i class=\"fa fa-plus position-left\"></i>{$lang['plugins_addact']}</button>";
	x += "</fieldset></div>";
	
	$("#container").append(x);
	$("#filefieldset_" + files).find(".help-button").popover();

}

function AddAction(fileid){
	actions ++;

	var x =  "<fieldset id=\"actionset_" + actions + "\">";
	x += "<legend><span class=\"position-left\">{$lang['vote_action']}</span>";
	x += "<select class=\"uniform position-right\" name=\"fileaction[" + fileid + "][" + actions + "]\" onchange=\"onActionChange(this, " + fileid + ", " + actions + ")\">";
	x += "<option value=\"\">{$lang['xfield_xact']}</option>";
	x += "<option value=\"replace\">{$lang['plugins_aсt_l1']}</option>";
	x += "<option value=\"before\">{$lang['plugins_aсt_l2']}</option>";
	x += "<option value=\"after\">{$lang['plugins_aсt_l3']}</option>";
	x += "<option value=\"replaceall\">{$lang['plugins_aсt_l4']}</option>";
	x += "<option value=\"create\">{$lang['plugins_aсt_l5']}</option>";
	x += "</select>";
	x += "<div class=\"pull-right\"><a onclick=\"javascript:adel('" + actions + "'); return(false);\"><i class=\"fa fa-trash-o text-danger\"></i></a></div></legend>";
	x += "<div id=\"searchs_" + actions + "\"></div>";
	x += "</fieldset>";
	
	$("#actions_" + fileid ).append(x);
	$("#actionset_" + actions ).find("select.uniform").selectpicker();
	
}

function onActionChange(obj, fileid, actionid) {
	var value = $(obj).val();
	
	if (value == '') {
	
		$("#searchs_" + actionid ).html('');
		return false;
		
	}

	var x =  "";
	
	if( $("#text_search_" + fileid + "_" + actionid ).val() ) {
		var prev_val_search  = $("#text_search_" + fileid + "_" + actionid ).val();
	} else {
		var prev_val_search  = '';
	}
	
	if( $("#text_replace_" + fileid + "_" + actionid ).val() ) {
		var prev_val_replace = $("#text_replace_" + fileid + "_" + actionid ).val();
	} else {
		var prev_val_replace  = '';
	}

	if( $("#replace_count_" + fileid + "_" + actionid ).val() ) {
		var prev_val_replace_count = $("#replace_count_" + fileid + "_" + actionid ).val();
	} else {
		var prev_val_replace_count  = '';
	}

	if( $("#find_count_" + fileid + "_" + actionid ).val() ) {
		var prev_val_find_count = $("#find_count_" + fileid + "_" + actionid ).val();
	} else {
		var prev_val_find_count  = '';
	}	

	if(value != 'replaceall' && value !='create') {
		x +=  "<div class=\"form-group\">";
		x += "<label class=\"control-label col-sm-2\">{$lang['plugins_aсt_l6']}</label>";
		x += "<div class=\"col-sm-10\">";
		x += "<textarea name=\"filesearch[" + fileid + "][" + actionid + "]\" id=\"text_search_" + fileid + "_" + actionid + "\" class=\"classic width-500 php_editor\" rows=\"3\">"+prev_val_search+"</textarea>";
		x += "</div>";
		x += "</div>";

	}
	
	var lang="{$lang['plugins_aсt_l7']}";
	
	if(value == 'before' ) {
		lang="{$lang['plugins_aсt_l8']}";
	} else if(value == 'after') {
		lang="{$lang['plugins_aсt_l9']}";
	} else if(value == 'create') {
		lang="{$lang['plugins_aсt_l10']}";
	}
	
	x +=  "<div class=\"form-group\">";
	x += "<label class=\"control-label col-sm-2\">"+lang+"</label>";
	x += "<div class=\"col-sm-10\">";
	x += "<textarea name=\"filereplace[" + fileid + "][" + actionid + "]\" id=\"text_replace_" + fileid + "_" + actionid + "\" class=\"classic width-500 php_editor\" rows=\"5\">"+prev_val_replace+"</textarea>";
	x += "</div>";	
	x += "</div>";

	if(value != 'replaceall' && value !='create') {
	
		x +=  "<div class=\"form-group\">";
		x += "<label class=\"control-label col-sm-2\">{$lang['plugins_aсt_l11']}</label>";
		x += "<div class=\"col-sm-10\">";
		x += "<input type=\"text\" name=\"filefindcount[" + fileid + "][" + actionid + "]\" id=\"find_count_" + fileid + "_" + actionid + "\" class=\"form-control\" maxlength=\"3\" style=\"width:55px;\"><i class=\"help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left\" data-rel=\"popover\" data-trigger=\"hover\" data-placement=\"right\" data-content=\"{$lang['plugins_aсt_hint']}\"></i>";
		x += "</div>";	
		x += "</div>";

		x +=  "<div class=\"form-group\">";
		x += "<label class=\"control-label col-sm-2\">{$lang['plugins_aсt_l12']}</label>";
		x += "<div class=\"col-sm-10\">";
		x += "<input type=\"text\" name=\"filereplacecount[" + fileid + "][" + actionid + "]\" id=\"replace_count_" + fileid + "_" + actionid + "\" class=\"form-control\" maxlength=\"3\" style=\"width:55px;\"><i class=\"help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left\" data-rel=\"popover\" data-trigger=\"hover\" data-placement=\"right\" data-content=\"{$lang['plugins_rep_hint']}\"></i>";
		x += "</div>";	
		x += "</div>";

	}
	
	$("#searchs_" + actionid ).html(x);
	$("#text_search_" + fileid + "_" + actionid ).val(prev_val_search);
	$("#text_replace_" + fileid + "_" + actionid ).val(prev_val_replace);
	$("#find_count_" + fileid + "_" + actionid ).val(prev_val_find_count);
	$("#replace_count_" + fileid + "_" + actionid ).val(prev_val_replace_count);
	
	$("#searchs_" + actionid ).find(".php_editor").each(function() {
		var editor = CodeMirror.fromTextArea( this, {
			mode: "text/x-php",
			dragDrop: false,
			autoRefresh: true,
			viewportMargin: Infinity
		  });
	
		function updateTextArea() {
			editor.save();
		}
	
		editor.on('change', updateTextArea);
	});
	
	$("#actionset_" + actionid ).find("[data-rel=popover]").popover();
}

//-->
</script>
HTML;

	echofooter();
	
} elseif($_REQUEST['action'] == "errors") {
	
	$errors = "";

	$db->query( "SELECT id, name FROM " . PREFIX . "_plugins" );
	
	while ( $row = $db->get_row() ) {
		$plugins_name[$row['id']] = $row['name'];
	}
	
	if($id) $db->query( "SELECT * FROM " . PREFIX . "_plugins_logs WHERE plugin_id = '{$id}'" );
	else $db->query( "SELECT * FROM " . PREFIX . "_plugins_logs" );
	
	while ( $row = $db->get_row() ) {

		if( $row['plugin_id'] AND $row['type'] == "mysql") {
			$area = $lang['upgr_db_query'];
			$row['area'] = "<pre><code>".$row['area']."</code></pre>";
		} elseif( $row['plugin_id'] AND $row['type'] == "file" ) {
			$area = $lang['plugins_filename'];
			$row['error'] = $lang['plugins_errors_4']."<pre><code>".$row['error']."</code></pre>";
		} elseif( $row['plugin_id'] AND $row['type'] == "upload" ) {
			$area = $lang['plugins_filename'];
			$row['error'] = "<pre><code>".$row['error']."</code></pre>";
		} else {
			$plugins_name[0] = $lang['plugins_errors_5'];
			$area = "";
		}
		
		$errors .= "<div class=\"quote\"><b>{$lang['plugins_name']}</b> {$plugins_name[$row['plugin_id']]}<div class=\"mt-10\"><b>{$area}</b> {$row['area']}</div><div class=\"mt-10\"><b>{$lang['upgr_db_errt']}</b> {$row['error']}</div></div>";
		
	}
	
	if(!$errors) msg( "success", $lang['all_info'], $lang['plugins_errors_3'], "javascript:history.go(-1)" );

	$js_array[] = "engine/classes/highlight/highlight.code.js";

	echoheader( "<i class=\"fa fa-puzzle-piece position-left\"></i><span class=\"text-semibold\">{$lang['opt_plugins']}</span>", array('?mod=plugins' => $lang['plugins_list'], '' => $lang['plugins_bread_2'] ) );

	$errors = "<div class=\"panel-body\"><div class=\"text-size-small\">".$errors."</div></div>";
	
	echo <<<HTML
	<div class="panel panel-default">
	  <div class="panel-heading">
		{$lang['plugins_bread_2']}
	  </div>
	  {$errors}
	  <div class="panel-footer">
		 <a href="?mod=plugins" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-forward position-left"></i>{$lang['upgr_next']}</a>
		 <a href="?mod=plugins&action=clearerrors&user_hash={$dle_login_hash}&id={$id}" class="btn bg-brown-600 btn-sm btn-raised position-left"><i class="fa fa-trash position-left"></i>{$lang['plugins_errors_2']}</a>
	   </div>
	</div>
	<script>  
	<!--
	$(function(){
	
		hljs.initHighlightingOnLoad();
	});
	//-->
	</script>
HTML;

	echofooter();
	
} else {
	
	include_once (DLEPlugins::Check(ENGINE_DIR.'/classes/parse.class.php'));
	$parse = new ParseFilter();

	echoheader( "<i class=\"fa fa-puzzle-piece position-left\"></i><span class=\"text-semibold\">{$lang['opt_plugins']}</span>", $lang['plugins_list']);

	$plugins_errors = array();
	
	$db->query( "SELECT plugin_id, COUNT(id) AS count FROM " . PREFIX . "_plugins_logs GROUP BY plugin_id" );
	
	while ( $row = $db->get_row() ) {
		$plugins_errors[$row['plugin_id']] = $row['count'];
	}
	
	if( count($plugins_errors) ) {
		
		$error_button = "<button type=\"button\" onclick=\"document.location='?mod=plugins&action=errors'\" class=\"btn bg-brown-600 btn-sm btn-raised position-left\"><i class=\"fa fa-exclamation-triangle position-left\"></i>{$lang['plugins_errors_1']}</button>";
		
	} else $error_button = "";
	
	$db->query( "SELECT * FROM " . PREFIX . "_plugins ORDER BY posi ASC, id DESC" );
	
	$entries = "";
	$i=0;
	
	while ( $row = $db->get_row() ) {
	
		if(!$i) $border = "no-border-top "; else $border = "";
	
		if ( !$row['icon'] OR !@file_exists( $row['icon'] )) $row['icon'] = "engine/skins/images/default_icon.png";
	
		if( $row['version'] ) $row['version'] = " v.<span id=\"version{$row['id']}\">". $row['version']."</span>";
		
		if( $row['mnotice'] AND $row['notice']) {
			
			$row['notice'] = $parse->BB_Parse($parse->process($row['notice']), false);
			$row['notice'] = "<div class=\"alert alert-info p-5\" style=\"max-height:200px;overflow:auto;cursor:auto;\">".stripslashes($row['notice'])."</div>";
			
		} else $row['notice'] = "";
		
		if( $row['active'] ) {
			$status = "<span title=\"{$lang['plugins_on_1']}\" class=\"text-success position-left position-right tip\"><b><i class=\"fa fa-check-circle\"></i></b></span>";
			$lang['led_active'] = $lang['plugins_off'];
			$led_action = "off";
		} else {
			$status = "<span title=\"{$lang['plugins_off_1']}\" class=\"text-danger position-left position-right tip\"><b><i class=\"fa fa-exclamation-circle\"></i></b></span>";
			$lang['led_active'] = $lang['plugins_on'];
			$led_action = "on";
		}
		
		if ($plugins_errors[$row['id']]) {
			$plugin_error = "<span class=\"label label-danger\">{$lang['plugins_errors']}</span>";
			$menu_error ="<li><a href=\"?mod=plugins&action=errors&id={$row['id']}\"><i class=\"fa fa-exclamation-triangle\"></i> {$lang['plugins_errors_1']}</a></li>";

		} else { $plugin_error = ""; $menu_error =""; }
		
		$menu_link = <<<HTML
		<div class="btn-group">
			<a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
			<ul class="dropdown-menu dropdown-menu-right">
			  {$menu_error}
			  <li><a href="?mod=plugins&user_hash={$dle_login_hash}&action={$led_action}&id={$row['id']}"><i class="fa fa-eye"></i> {$lang['led_active']}</a></li>
			  <li><a href="?mod=plugins&user_hash={$dle_login_hash}&action=download&id={$row['id']}"><i class="fa fa-download"></i> {$lang['plugins_download']}</a></li>
			  <li class="divider"></li>
			  <li><a onclick="CheckUpdate('{$row['id']}'); return false;" href="#"><i class="fa fa-search"></i> {$lang['plugins_upgr_c']}</a></li>
			  <li><a onclick="PluginUpdate('{$row['id']}'); return false;" href="#"><i class="fa fa-upload"></i> {$lang['plugins_update']}</a></li>
			  <li class="divider"></li>
			  <li><a onclick="confirmdelete('{$row['id']}'); return false;" href="#"><i class="fa fa-trash-o text-danger"></i> {$lang['cat_del']}</a></li>
			</ul>
		</div>
HTML;
	
		$entries .= "
			<tr class=\"dd-item\" data-id=\"{$row['id']}\">
			<td class=\"{$border}dd-handles\"></td>
			 <td class=\"{$border}cursor-pointer\" onclick=\"document.location = '?mod=plugins&action=edit&id={$row['id']}'; return false;\"><div class=\"media-list\"><div class=\"media-left\"><img src=\"{$row['icon']}\" class=\"img-lg section_icon\"></div><div class=\"media-body\"><h6 class=\"media-heading text-semibold\">{$row['name']}{$row['version']}{$status}{$plugin_error}</h6><span class=\"text-muted text-size-small\">{$row['description']}</span><span id=\"upgrade{$row['id']}\" class=\"label label-success position-right text-size-small\"></span>{$row['notice']}</div></div></td>
			 <td class=\"{$border}text-center\" style=\"width: 70px\">{$menu_link}</td>
		   </tr>";
			   
			 $i++;
		
	}
	
	if(!$entries) {
		$entries = "<tr><td class=\"no-border-top\"><div align=\"center\"><br><br>{$lang['plugins_not_found']}<br><br><br></div></td></tr>";
	} else {
		$entries = "<tbody class=\"dd-list\">".$entries."</tbody>";
	}
	
	if( !$config['allow_plugins'] ) {
		
		$alert = "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['module_disabled']}</div>";
		
	} elseif( !DLEPlugins::$read_only AND !defined('DEMOVERSION') ) {
		
		$alert = "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['stat_secfault_5']}</div>";
		
	} else $alert = "";
	
	if( !$lic_tr AND defined('DEMOVERSION') ) {
		$alert .= "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['upgr_demo']}</div>";
	} elseif( defined('DEMOVERSION') ) {
		$alert .= "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['upgr_demo_1']}</div>";
	}
	
	$root = ROOT_DIR;
	
		echo <<<HTML
	<div class="panel panel-default">
	  <div class="panel-heading">
		{$lang['plugins_list']}
	  </div>
	  
	  <div class="dd" id="nestable">
		<table class="table table-xs table-hover">
			{$entries}
		</table>
	  </div>
		<div class="panel-footer">
			<button type="button" onclick="document.location='?mod=plugins&action=add'" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-plus position-left"></i>{$lang['plugins_b_add']}</button>
			<button type="button" onclick="PluginUpload(); return false;" class="btn bg-slate-600 btn-sm btn-raised position-left"><i class="fa fa-upload position-left"></i>{$lang['plugins_uploads']}</button>
			{$error_button}
			<button type="button" onclick="CheckUpdate(0); return false;" class="btn bg-primary-600 btn-sm btn-raised position-left"><i class="fa fa-search position-left"></i>{$lang['plugins_upgr_c']}</button>
		</div>		
	</div>
	{$alert}
	<div class="modal fade" name="moduleupload" id="moduleupload">
		<div class="modal-dialog" role="document">	
			<div class="modal-content">
				<form method="post" autocomplete="off" class="form-horizontal" id="pluginupload" enctype="multipart/form-data">
				<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
				<input type="hidden" name="id" id="plugin_id" value="0">
				<div class="modal-header ui-dialog-titlebar">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<span class="ui-dialog-title">{$lang['plugins_upload']}</span>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<div class="col-sm-12">
						  <input type="file" name="pluginfile" accept="text/xml,application/zip" style="width:304px;" class="icheck">
						</div>
		
					</div>
				</div>
				<div class="modal-footer" style="margin-top:-20px;">
					<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-upload position-left"></i>{$lang['plugins_uploads']}</button>
					<button type="button" class="btn bg-slate-600 btn-sm btn-raised" data-dismiss="modal">{$lang['p_cancel']}</button>
				</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" name="needftp" id="needftp">
		<div class="modal-dialog" role="document">	
			<div class="modal-content">
				<form method="post" autocomplete="off" class="form-horizontal" id="ftpserver">
				<input type="hidden" name="action" value="checkftp">
				<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
				<div class="modal-header ui-dialog-titlebar">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<span class="ui-dialog-title">{$lang['plugins_upload']}</span>
				</div>
				<div class="modal-body">
				
					<div class="form-group">
						<label class="control-label col-sm-3">{$lang['upgr_ftp_2']}</label>
						<div class="col-sm-9">
							<label class="radio-inline position-left"><input class="icheck" type="radio" name="ftp[type]" value="ftp" checked>FTP</label>
							<label class="radio-inline position-left"><input class="icheck" type="radio" name="ftp[type]" value="sslftp">SSL FTP</label>
							<label class="radio-inline position-left"><input class="icheck" type="radio" name="ftp[type]" value="ssh2">SFTP SSH2</label>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">{$lang['upgr_ftp_3']}</label>
						<div class="col-sm-9">
							<input type="text" class="form-control width-300 position-left" name="ftp[server]">
							<span class="position-left">{$lang['upgr_ftp_4']}</span>
							<input type="text" class="form-control position-left" name="ftp[port]" style="width:45px" value="21">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">{$lang['upgr_ftp_5']}</label>
						<div class="col-sm-9">
							<input type="text" class="form-control width-350 position-left" name="ftp[username]">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">{$lang['upgr_ftp_6']}</label>
						<div class="col-sm-9">
							<input type="text" class="form-control width-350 position-left" name="ftp[password]">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">{$lang['upgr_ftp_7']}</label>
						<div class="col-sm-9">
							<input type="text" class="form-control width-450 position-left" name="ftp[path]" value="{$root}">
						</div>
					</div>
					<div class="text-muted text-size-small">{$lang['upgr_ftp_15']}</div>
				

				</div>
				<div class="modal-footer" style="margin-top:-20px;">
					<button id="checkftpbutton" type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-forward position-left"></i>{$lang['upgr_next']}</button>
					<button type="button" class="btn bg-slate-600 btn-sm btn-raised" data-dismiss="modal">{$lang['p_cancel']}</button>
				</div>
				</form>
			</div>
		</div>
	</div>	
	
	<script>  
	<!--
	function confirmdelete(id){
			DLEconfirm( '{$lang['plugins_del']}', '{$lang['p_confirm']}', function () {
				document.location="?mod=plugins&action=delete&user_hash={$dle_login_hash}&id="+id;
			} );
	}
	
	function PluginUpload(){
		$('#plugin_id').val('0');
		$('#moduleupload').modal();
		
	}
	
	function PluginUpdate(id){
	
		$('#plugin_id').val(id);
		$('#moduleupload').modal();
		
	}
	
	function PluginUpdateFromURL(id, url, version){

		ShowLoading('');
		
		$.ajax({
			url: "engine/ajax/controller.php?mod=plugins",
			data: { id: id, url: url, user_hash: "{$dle_login_hash}", action: "updatefromurl" },
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				HideLoading('');
				
				if (data) {
				
					if (data.status == "error") {
					
						DLEalert(data.text, '{$lang['p_info']}');
		
					} else if (data.status == "needftp") {
					
						$('#needftp').modal();
						
					} else if (data.status == "succes") {
					
						if(version) {
							$("#upgrade"+id).html('');
							$("#version"+id).text(version);
						} else {
							setTimeout("window.location = '?mod=plugins'", 300 );
						}
					}
			
				}
			},
			error: function(data) {
				HideLoading('');
					
				DLEalert(data.responseText, '{$lang['p_info']}');
			
			}
		});
		
	}
	
	function CheckUpdate(id){
	
		ShowLoading('');
		
		$.ajax({
			url: "engine/ajax/controller.php?mod=plugins",
			data: { id: id, user_hash: "{$dle_login_hash}", action: "checkupdate" },
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				HideLoading('');
				
				if (data) {
				
					if (data.status == "error") {
					
						DLEalert(data.text, '{$lang['p_info']}');
		
					} else if (data.status == "succes") {

						if(id) {
							DLEconfirm( '{$lang['plugins_upgr_s1']} '+data.version+'<br><br>{$lang['plugins_upgr_s2']}', '{$lang['p_info']}', function () {
							
								PluginUpdateFromURL(id, data.url, false)
							
							} );
						} else {
							$.each( data.versions, function( i, val ) {
								$("#upgrade"+val.id).html('{$lang['plugins_upgr_s1']} '+val.version+' <a class="text-danger" onclick="event.stopPropagation(); PluginUpdateFromURL(\''+val.id+'\', \''+val.url+'\', \''+val.version+'\'); return false;" href="#">{$lang['plugins_upgr_s3']}</a>');
							});
						}

					}
			
				}
			},
			error: function(data) {
				HideLoading('');
					
				DLEalert(data.responseText, '{$lang['p_info']}');
			
			}
		});
		
	}
	
	$('#pluginupload').submit(function() {
	
		var formData = new FormData($('#pluginupload')[0]);
		
		$('#moduleupload').modal('hide');
		$("#pluginupload")[0].reset();
		$(".filename").html('{$lang['file_def_1']}');
		ShowLoading('');
		
		$.ajax({
			url: "engine/ajax/controller.php?mod=plugins",
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				HideLoading('');
				
				if (data) {
				
					if (data.status == "error") {
					
						DLEalert(data.text, '{$lang['p_info']}');
		
					} else if (data.status == "needftp") {
					
						$('#needftp').modal();
						
					} else if (data.status == "succes") {
						setTimeout("window.location = '?mod=plugins'", 300 );
					}
			
				}
			},
			error: function(data) {
				HideLoading('');
					
				DLEalert(data.responseText, '{$lang['p_info']}');
			
			}
		});

		return false;
	});
	
	$('#ftpserver').submit(function() {
	
		var formData = new FormData($('#ftpserver')[0]);
		
		ShowLoading('');
		$('#checkftpbutton').attr("disabled", "disabled");
		
		$.ajax({
			url: "engine/ajax/controller.php?mod=plugins",
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				HideLoading('');
				$('#checkftpbutton').attr("disabled", false);
				
				if (data.status == "succes") {
					setTimeout("window.location = '?mod=plugins'", 300 );
				} else if (data.status == "error") {
					DLEalert(data.text, '{$lang['all_info']}');
				}
			},
			error: function(data) {
				HideLoading('');
				$('#checkftpbutton').attr("disabled", false);
				DLEalert(data.responseText, '{$lang['p_info']}');
			
			}
		});
	
		return false;
	
	});
	
	jQuery(function($){

		$('.dd').nestable({
			listNodeName: 'tbody',
			itemNodeName: 'tr.dd-item',
			handleClass: 'dd-handles',
			emptyClass: 'dd-emptys',
			placeClass: 'dd-placeholders',
			placeElementDefault: '<tr class="dd-placeholders"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>',
			maxDepth: 1
		});
		
		$('#nestable').nestable().on('change',function(){
		
			var url = "action=pluginsort&user_hash={$dle_login_hash}&list="+window.JSON.stringify($(this).nestable('serialize'));
				
			ShowLoading('');
			$.post('engine/ajax/controller.php?mod=adminfunction', url, function(data){
	
				HideLoading('');
	
				if (data != 'ok') {

					DLEalert('{$lang['cat_sort_fail']}', '{$lang['p_info']}');

				}
	
			});
			
		});
		
	$(".alert-info").click(function(e){
	    e.stopPropagation();
	});
  
	});
	</script>
HTML;
	
	echofooter();

}
?>