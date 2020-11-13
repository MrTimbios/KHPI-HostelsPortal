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
 File: templates.class.php
-----------------------------------------------------
 Use: Templates class
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

require_once (DLEPlugins::Check(ROOT_DIR . '/engine/classes/mobiledetect.class.php'));
require_once (DLEPlugins::Check(ROOT_DIR . '/engine/classes/antivirus.class.php'));

class dle_template {
	
	var $dir = '';
	var $template = null;
	var $copy_template = null;
	var $desktop = true;
	var $smartphone = false;
	var $tablet = false;
	var $android = false;
	var $ios = false;
	var $data = array ();
	var $block_data = array ();
	var $user_data = array ();
	var $user_block_data = array ();
	var $user_loaded = false;
	var $result = array ('info' => '', 'vote' => '', 'speedbar' => '', 'content' => '' );
	var $allow_php_include = true;
	var $include_mode = 'tpl';
	var $category_tree = false;
	var $is_custom = false;
	
	var $template_parse_time = 0;

    function __construct(){

		$this->dir = ROOT_DIR . '/templates/';

		$mobile_detect = new Mobile_Detect;

		if ( $mobile_detect->isMobile() ) {
			$this->smartphone = true;
			$this->desktop = false;
		}

		if ( $mobile_detect->isTablet() ) {
			$this->smartphone = false;
			$this->desktop = false;
			$this->tablet = true;
		}
		
		if( $mobile_detect->isiOS() ){
			$this->ios = true;
		}

		if( $mobile_detect->isAndroidOS() ){
			$this->android = true;
		}
		
	}
	
	function set($name, $var) {
		
		if( is_array( $var ) ) {
			if( count( $var ) ) {
				foreach ( $var as $key => $key_var ) {
					$this->set( $key, $key_var );
				}
			}
			return;
		}
		
		$var = str_replace(array("{", "["),array("_&#123;_", "_&#91;_"), $var);
			
		$this->data[$name] = $var;
		
	}
	
	function set_block($name, $var) {
		
		if( is_array( $var ) ) {
			if( count( $var ) ) {
				foreach ( $var as $key => $key_var ) {
					$this->set_block( $key, $key_var );
				}
			}
			return;
		}
		
		$var = str_replace(array("{", "["),array("_&#123;_", "_&#91;_"), $var);
			
		$this->block_data[$name] = $var;
	}
	
	function load_template($tpl_name) {
		global $category_id, $cat_info, $page_header_info, $config;
		
		$time_before = $this->get_real_time();

		if( !$this->user_loaded ) {
			$this->buld_user_data();
		}

		$tpl_name = str_replace(chr(0), '', (string)$tpl_name);

		$file_path = cleanpath(dirname($tpl_name));
		
		$url = parse_url ( $tpl_name );
		$tpl_name = pathinfo($url['path']);
		$tpl_name = totranslit($tpl_name['basename']);
		$type = explode( ".", $tpl_name );
		$type = strtolower( end( $type ) );

		if ($type != "tpl") {
			$this->template = "Not Allowed Template Name: " .str_replace(ROOT_DIR, '', $this->dir)."/".$tpl_name ;
			$this->copy_template = $this->template;
			return "";

		}

		if ($file_path AND $file_path != ".") $tpl_name = $file_path."/".$tpl_name;

		if( stripos ( $tpl_name, ".php" ) !== false ) {
			$this->template = "Not Allowed Template Name: " .str_replace(ROOT_DIR, '', $this->dir)."/".$tpl_name ;
			$this->copy_template = $this->template;
			return "";
		}

		if( $tpl_name == '' || !file_exists( $this->dir . "/" . $tpl_name ) ) {
			$this->template = "Template not found: " .str_replace(ROOT_DIR, '', $this->dir)."/".$tpl_name ;
			$this->copy_template = $this->template;
			return "";
		}

		$this->template = file_get_contents( $this->dir . "/" . $tpl_name );
		
		if (strpos ( $this->template, "{*" ) !== false) {
			$this->template = preg_replace("'\\{\\*(.*?)\\*\\}'si", '', $this->template);
		}

		if (stripos ( $this->template, "page-title" ) !== false OR stripos( $this->template, "page-description" ) !== false) {
			
			$this->template = str_ireplace( array('{page-title}', '{page-description}'), array($page_header_info['title'], $page_header_info['description']), $this->template );
		
			if( $page_header_info['title'] ) {
				$this->template = preg_replace( "'\\[not-page-title\\](.*?)\\[/not-page-title\\]'is", "", $this->template );
				$this->template = str_ireplace( "[page-title]", "", $this->template );
				$this->template = str_ireplace( "[/page-title]", "", $this->template );
			} else {
				$this->template = preg_replace( "'\\[page-title\\](.*?)\\[/page-title\\]'is", "", $this->template );
				$this->template = str_ireplace( "[not-page-title]", "", $this->template );
				$this->template = str_ireplace( "[/not-page-title]", "", $this->template );
			}
			if( $page_header_info['description'] ) {
				$this->template = preg_replace( "'\\[not-page-description\\](.*?)\\[/not-page-description\\]'is", "", $this->template );
				$this->template = str_ireplace( "[page-description]", "", $this->template );
				$this->template = str_ireplace( "[/page-description]", "", $this->template );
			} else {
				$this->template = preg_replace( "'\\[page-description\\](.*?)\\[/page-description\\]'is", "", $this->template );
				$this->template = str_ireplace( "[not-page-description]", "", $this->template );
				$this->template = str_ireplace( "[/not-page-description]", "", $this->template );
			}
		}
		
		$this->template = $this->check_module($this->template);
		
		if (strpos ( $this->template, "[group=" ) !== false OR strpos ( $this->template, "[not-group=" ) !== false) {
			$this->template = $this->check_group($this->template);
		}
		
		if( defined( 'NEWS_ID' ) AND !$this->is_custom) $this->template = str_ireplace( "{news-id}", NEWS_ID, $this->template );
		
		if (strpos ( $this->template, "[page-count=" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(page-count)=(.+?)\\](.*?)\\[/page-count\\]#is", array( &$this, 'check_page'), $this->template );
		}

		if (strpos ( $this->template, "[not-page-count=" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(not-page-count)=(.+?)\\](.*?)\\[/not-page-count\\]#is", array( &$this, 'check_page'), $this->template );
		}

		if (strpos ( $this->template, "[tags=" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(tags)=(.+?)\\](.*?)\\[/tags\\]#is", array( &$this, 'check_tag'), $this->template );
		}


		if (strpos ( $this->template, "[not-tags=" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(not-tags)=(.+?)\\](.*?)\\[/not-tags\\]#is", array( &$this, 'check_tag'), $this->template );
		}

		if (strpos ( $this->template, "[news=" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(news)=(.+?)\\](.*?)\\[/news\\]#is", array( &$this, 'check_tag'), $this->template );
		}

		if (strpos ( $this->template, "[not-news=" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(not-news)=(.+?)\\](.*?)\\[/not-news\\]#is", array( &$this, 'check_tag'), $this->template );
		}

		if (strpos ( $this->template, "[smartphone]" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(smartphone)\\](.*?)\\[/smartphone\\]#is", array( &$this, 'check_device'), $this->template );
		}

		if (strpos ( $this->template, "[not-smartphone]" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(not-smartphone)\\](.*?)\\[/not-smartphone\\]#is", array( &$this, 'check_device'), $this->template );
		}

		if (strpos ( $this->template, "[tablet]" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(tablet)\\](.*?)\\[/tablet\\]#is", array( &$this, 'check_device'), $this->template );
		}

		if (strpos ( $this->template, "[not-tablet]" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(not-tablet)\\](.*?)\\[/not-tablet\\]#is", array( &$this, 'check_device'), $this->template );
		}

		if (strpos ( $this->template, "[desktop]" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(desktop)\\](.*?)\\[/desktop\\]#is", array( &$this, 'check_device'), $this->template );
		}

		if (strpos ( $this->template, "[not-desktop]" ) !== false) {
			$this->template = preg_replace_callback ( "#\\[(not-desktop)\\](.*?)\\[/not-desktop\\]#is", array( &$this, 'check_device'), $this->template );
		}
		
		if (strpos ( $this->template, "[android]" ) !== false) {
			
			if($this->android) {
				
				$this->template = str_replace( '[android]', "", $this->template );
				$this->template = str_replace( '[/android]', "", $this->template );
				$this->template = preg_replace( "#\[not-android\](.+?)\[/not-android\]#is", "", $this->template );
				
			} else {
				
				$this->template = str_replace( '[not-android]', "", $this->template );
				$this->template = str_replace( '[/not-android]', "", $this->template );
				$this->template = preg_replace( "#\[android\](.+?)\[/android\]#is", "", $this->template );
				
			}
			
		}
		
		if (strpos ( $this->template, "[ios]" ) !== false) {
			
			if($this->ios) {
				
				$this->template = str_replace( '[ios]', "", $this->template );
				$this->template = str_replace( '[/ios]', "", $this->template );
				$this->template = preg_replace( "#\[not-ios\](.+?)\[/not-ios\]#is", "", $this->template );
				
			} else {
				
				$this->template = str_replace( '[not-ios]', "", $this->template );
				$this->template = str_replace( '[/not-ios]', "", $this->template );
				$this->template = preg_replace( "#\[ios\](.+?)\[/ios\]#is", "", $this->template );
				
			}
			
		}
	
		if (strpos ( $this->template, "{category-" ) !== false) {
			
			$cat_id = intval($category_id);
			
			if( $cat_id ) {
				
				$this->template = str_ireplace( "{category-id}", $cat_id, $this->template );
				$this->template = str_ireplace( "{category-title}", $cat_info[$cat_id]['name'], $this->template );
				$this->template = str_ireplace( "{category-description}", $cat_info[$cat_id]['fulldescr'], $this->template );
				
				if ( !$this->is_custom AND $tpl_name != "shortstory.tpl" AND $tpl_name != "fullstory.tpl" ) {
					if( $config['allow_alt_url'] ) $this->template = str_ireplace( "{category-url}", $config['http_home_url'] . get_url( $cat_id ) . "/", $this->template );
					else $this->template = str_ireplace( "{category-url}", "$PHP_SELF?do=cat&category={$cat_info[$cat_id]['alt_name']}", $this->template );
	
					if( $cat_info[$cat_id]['icon'] ) $this->template = str_ireplace( "{category-icon}", $cat_info[$cat_id]['icon'], $this->template );
				}


			} else {
				
				$this->template = str_ireplace( "{category-id}", '', $this->template );
				$this->template = str_ireplace( "{category-title}", '', $this->template );
				$this->template = str_ireplace( "{category-description}", '', $this->template );

				
			}

		}
		
		if (strpos ( $this->template, "{catmenu" ) !== false) {
			$this->template = preg_replace_callback ( "#\\{catmenu(.*?)\\}#is", array( &$this, 'build_cat_menu'), $this->template );
		}
		
		if (strpos ( $this->template, "{catnewscount" ) !== false) {
			$this->template = preg_replace_callback ( "#\\{catnewscount id=['\"](.+?)['\"]\\}#i", array( &$this, 'catnewscount'), $this->template );
		}
		
		if( strpos( $this->template, "{include file=" ) !== false ) {
			$this->include_mode = 'tpl';			
			$this->template = preg_replace_callback( "#\\{include file=['\"](.+?)['\"]\\}#i", array( &$this, 'load_file'), $this->template );
		
		}

		$this->copy_template = $this->template;
		
		$this->template_parse_time += $this->get_real_time() - $time_before;
		return true;
	}

	function load_file( $matches=array() ) {
		global $db, $is_logged, $member_id, $cat_info, $config, $user_group, $category_id, $_TIME, $lang, $smartphone_detected, $dle_module;

		$name = $matches[1];

		$name = str_replace( chr(0), "", $name );
		$name = str_replace( '..', '', $name );
		$name = str_replace(array('/', '\\'), '/', $name);

		$url = @parse_url ($name);
		$type = explode( ".", $url['path'] );
		$type = strtolower( end( $type ) );

		if ($type == "tpl") {

			return $this->sub_load_template( $name );

		}

		if ($this->include_mode == "php") {

			if ( !$this->allow_php_include ) return;

			if ($type != "php") return "To connect permitted only files with the extension: .tpl or .php";

			$file_path = ROOT_DIR."/".cleanpath(dirname($url['path']));	
			$url['path'] = clearfilepath( trim($url['path']) , array ("php") );
			
			if (substr ( $file_path, - 1, 1 ) == '/') $file_path = substr ( $file_path, 0, - 1 );

			$file_name = pathinfo($url['path']);
			$file_name = $file_name['basename'];
			$antivirus = new antivirus();

			if ( stristr ( php_uname( "s" ) , "windows" ) === false )
				$chmod_value = @decoct(@fileperms($file_path)) % 1000;
				
			if(!$file_name)
				return "Include files from root directory is denied";
			
			if(in_array("./" . $url['path'], $antivirus->good_files))
				return "Include standart DLE files is denied";
			
			if ( stristr ( dirname ($url['path']) , "uploads" ) !== false )
				return "Include files from directory /uploads/ is denied";

			if ( stristr ( dirname ($url['path']) , "templates" ) !== false )
				return "Include files from directory /templates/ is denied";

			if ( stristr ( dirname ($url['path']) , "engine/data" ) !== false )
				return "Include files from directory /engine/data/ is denied";

			if ( stristr ( dirname ($url['path']) , "engine/cache" ) !== false )
				return "Include files from directory /engine/cache/ is denied";

			if ( stristr ( dirname ($url['path']) , "engine/inc" ) !== false )
				return "Include files from directory /engine/inc/ is denied";
		
			if ($chmod_value == 777 ) return "File {$url['path']} is in the folder, which is available to write (CHMOD 777). For security purposes the connection files from these folders is impossible. Change the permissions on the folder that it had no rights to the write.";

			if ( !file_exists(DLEPlugins::Check($file_path."/".$file_name)) ) return "File {$url['path']} not found.";

			$url['query'] = str_ireplace(array("file_path","file_name", "dle_login_hash", "_GET","_FILES","_POST","_REQUEST","_SERVER","_COOKIE","_SESSION") ,"Filtered", $url['query'] );

			if( substr_count ($this->template, "{include file=") < substr_count ($this->copy_template, "{include file=")) return "Filtered";

			if ( isset($url['query']) AND $url['query'] ) {

				$module_params = array();

				parse_str( $url['query'], $module_params );

				extract($module_params, EXTR_SKIP);

				unset($module_params);
				

			}

			ob_start();
			$tpl = new dle_template();
			$tpl->dir = TEMPLATE_DIR;
			
			include (DLEPlugins::Check($file_path."/".$file_name));
			return ob_get_clean();

		}

		return $matches[0];
	}
	
	function sub_load_template( $tpl_name ) {
		global $category_id, $cat_info, $page_header_info, $config;
		
		$tpl_name = str_replace(chr(0), '', (string)$tpl_name);

		$file_path = cleanpath(dirname($tpl_name));
		
		if (strpos($tpl_name, '/templates/') === 0) $file_path = '/'.$file_path;
		
		$url = @parse_url($tpl_name);
		$tpl_name = pathinfo($url['path']);
		$tpl_name = totranslit($tpl_name['basename']);
		$type = explode( ".", $tpl_name );
		$type = strtolower( end( $type ) );
		
		if ($file_path AND $file_path != ".") $tpl_name = $file_path."/".$tpl_name;

		if ($type != "tpl") {

			return "Not Allowed Template Name: ". $tpl_name;

		}

		if (strpos($tpl_name, '/templates/') === 0) {

			$tpl_name = str_replace('/templates/','',$tpl_name);
			$templatefile = ROOT_DIR . '/templates/'.$tpl_name;

		} else $templatefile = $this->dir . "/" . $tpl_name;

		if( $tpl_name == '' || !file_exists( $templatefile ) ) {

			$templatefile = str_replace(ROOT_DIR,'',$templatefile);

			return "Template not found: " . $templatefile;

		}

		if( stripos ( $templatefile, ".php" ) !== false ) return "Not Allowed Template Name: ". $tpl_name;

		$template = file_get_contents( $templatefile );

		if (strpos ( $template, "{*" ) !== false) {
			$template = preg_replace("'\\{\\*(.*?)\\*\\}'si", '', $template);
		}
		
		if (stripos ( $template, "page-title" ) !== false OR stripos( $template, "page-description" ) !== false) {
			
			$template = str_ireplace( array('{page-title}', '{page-description}'), array($page_header_info['title'], $page_header_info['description']), $template );
		
			if( $page_header_info['title'] ) {
				$template = preg_replace( "'\\[not-page-title\\](.*?)\\[/not-page-title\\]'is", "", $template );
				$template = str_ireplace( "[page-title]", "", $template );
				$template = str_ireplace( "[/page-title]", "", $template );
			} else {
				$template = preg_replace( "'\\[page-title\\](.*?)\\[/page-title\\]'is", "", $template );
				$template = str_ireplace( "[not-page-title]", "", $template );
				$template = str_ireplace( "[/not-page-title]", "", $template );
			}
			if( $page_header_info['description'] ) {
				$template = preg_replace( "'\\[not-page-description\\](.*?)\\[/not-page-description\\]'is", "", $template );
				$template = str_ireplace( "[page-description]", "", $template );
				$template = str_ireplace( "[/page-description]", "", $template );
			} else {
				$template = preg_replace( "'\\[page-description\\](.*?)\\[/page-description\\]'is", "", $template );
				$template = str_ireplace( "[not-page-description]", "", $template );
				$template = str_ireplace( "[/not-page-description]", "", $template );
			}
		}
		
		$template = $this->check_module($template);
		
		if (strpos ( $template, "[group=" ) !== false OR strpos ( $template, "[not-group=" ) !== false ) {
			$template = $this->check_group($template);
		}
		
		if( defined( 'NEWS_ID' ) AND !$this->is_custom) $template = str_ireplace( "{news-id}", NEWS_ID, $template );

		if (strpos ( $template, "[page-count=" ) !== false) {
			$template = preg_replace_callback ( "#\\[(page-count)=(.+?)\\](.*?)\\[/page-count\\]#is", array( &$this, 'check_page'), $template );
		}

		if (strpos ( $template, "[not-page-count=" ) !== false) {
			$template = preg_replace_callback ( "#\\[(not-page-count)=(.+?)\\](.*?)\\[/not-page-count\\]#is", array( &$this, 'check_page'), $template );
		}

		if (strpos ( $template, "[tags=" ) !== false) {
			$template = preg_replace_callback ( "#\\[(tags)=(.+?)\\](.*?)\\[/tags\\]#is", array( &$this, 'check_tag'), $template );
		}


		if (strpos ( $template, "[not-tags=" ) !== false) {
			$template = preg_replace_callback ( "#\\[(not-tags)=(.+?)\\](.*?)\\[/not-tags\\]#is", array( &$this, 'check_tag'), $template );
		}

		if (strpos ( $template, "[news=" ) !== false) {
			$template = preg_replace_callback ( "#\\[(news)=(.+?)\\](.*?)\\[/news\\]#is", array( &$this, 'check_tag'), $template );
		}


		if (strpos ( $template, "[not-news=" ) !== false) {
			$template = preg_replace_callback ( "#\\[(not-news)=(.+?)\\](.*?)\\[/not-news\\]#is", array( &$this, 'check_tag'), $template );
		}

		if (strpos ( $template, "[smartphone]" ) !== false) {
			$template = preg_replace_callback ( "#\\[(smartphone)\\](.*?)\\[/smartphone\\]#is", array( &$this, 'check_device'), $template );
		}

		if (strpos ( $template, "[not-smartphone]" ) !== false) {
			$template = preg_replace_callback ( "#\\[(not-smartphone)\\](.*?)\\[/not-smartphone\\]#is", array( &$this, 'check_device'), $template );
		}

		if (strpos ( $template, "[tablet]" ) !== false) {
			$template = preg_replace_callback ( "#\\[(tablet)\\](.*?)\\[/tablet\\]#is", array( &$this, 'check_device'), $template );
		}

		if (strpos ( $template, "[not-tablet]" ) !== false) {
			$template = preg_replace_callback ( "#\\[(not-tablet)\\](.*?)\\[/not-tablet\\]#is", array( &$this, 'check_device'), $template );
		}

		if (strpos ( $template, "[desktop]" ) !== false) {
			$template = preg_replace_callback ( "#\\[(desktop)\\](.*?)\\[/desktop\\]#is", array( &$this, 'check_device'), $template );
		}

		if (strpos ( $template, "[not-desktop]" ) !== false) {
			$template = preg_replace_callback ( "#\\[(not-desktop)\\](.*?)\\[/not-desktop\\]#is", array( &$this, 'check_device'), $template );
		}
		
		if (strpos ( $template, "[android]" ) !== false) {
			
			if($this->android) {
				
				$template = str_replace( '[android]', "", $template );
				$template = str_replace( '[/android]', "", $template );
				$template = preg_replace( "#\[not-android\](.+?)\[/not-android\]#is", "", $template );
				
			} else {
				
				$template = str_replace( '[not-android]', "", $template );
				$template = str_replace( '[/not-android]', "", $template );
				$template = preg_replace( "#\[android\](.+?)\[/android\]#is", "", $template );
				
			}
			
		}
		
		if (strpos ( $template, "[ios]" ) !== false) {
			
			if($this->ios) {
				
				$template = str_replace( '[ios]', "", $template );
				$template = str_replace( '[/ios]', "", $template );
				$template = preg_replace( "#\[not-ios\](.+?)\[/not-ios\]#is", "", $template );
				
			} else {
				
				$template = str_replace( '[not-ios]', "", $template );
				$template = str_replace( '[/not-ios]', "", $template );
				$template = preg_replace( "#\[ios\](.+?)\[/ios\]#is", "", $template );
				
			}
			
		}
		
		if (strpos ( $template, "{category-" ) !== false) {
			$cat_id = intval($category_id);
			
			if( $cat_id ) {
				
				$template = str_ireplace( "{category-id}", $cat_id, $template );
				$template = str_ireplace( "{category-title}", $cat_info[$cat_id]['name'], $template );
				$template = str_ireplace( "{category-description}", $cat_info[$cat_id]['fulldescr'], $template );
				
				if( $config['allow_alt_url'] ) $template = str_ireplace( "{category-url}", $config['http_home_url'] . get_url( $cat_id ) . "/", $template );
				else $template = str_ireplace( "{category-url}", "$PHP_SELF?do=cat&category={$cat_info[$cat_id]['alt_name']}", $template );
				
				if( $cat_info[$cat_id]['icon'] ) {
					$template = str_ireplace( "{category-icon}", $cat_info[$cat_id]['icon'], $template );
				}
			
			} else {
				
				$template = str_ireplace( "{category-id}", '', $template );
				$template = str_ireplace( "{category-title}", '', $template );
				$template = str_ireplace( "{category-description}", '', $template );
				
			}
			
		}
		
		if (strpos ( $template, "{catnewscount" ) !== false) {
			$template = preg_replace_callback ( "#\\{catnewscount id=['\"](.+?)['\"]\\}#i", array( &$this, 'catnewscount'), $template );
		}
		
		if (strpos ( $template, "{catmenu" ) !== false) {
			$template = preg_replace_callback ( "#\\{catmenu(.*?)\\}#is", array( &$this, 'build_cat_menu'), $template );
		}
		
		return $template;
	}

	function check_module($matches) {
		global $dle_module;
		
		$regex = '/\[(aviable|available|not-aviable|not-available)=(.*?)\]((?>(?R)|.)*?)\[\/\1\]/is';

		if (is_array($matches)) {
			
			$aviable = $matches[2];
			$block = $matches[3];
			
			if ($matches[1] == "aviable" OR $matches[1] == "available") $action = true; else $action = false;
			
			$aviable = explode( '|', $aviable );
			
			if( $action ) {
				
				if( ! (in_array( $dle_module, $aviable )) and ($aviable[0] != "global") ) $matches = '';
				else $matches = $block;
			
			} else {
				
				if( (in_array( $dle_module, $aviable )) ) $matches = '';
				else $matches = $block;
			}		
	
		}
	
		return preg_replace_callback($regex, array( &$this, 'check_module'), $matches);
	}

	function check_group( $matches ) {
		global $member_id;

		$regex = '/\[(group|not-group)=(.*?)\]((?>(?R)|.)*?)\[\/\1\]/is';

		if (is_array($matches)) {

			$groups = $matches[2];
			$block = $matches[3];
	
			if ($matches[1] == "group") $action = true; else $action = false;
			
			$groups = explode( ',', $groups );
			
			if( $action ) {
				
				if( ! in_array( $member_id['user_group'], $groups ) ) $matches = ''; else $matches = $block;
			
			} else {
				
				if( in_array( $member_id['user_group'], $groups ) ) $matches = ''; else $matches = $block;
			
			}
		}
		
		return preg_replace_callback($regex, array( &$this, 'check_group'), $matches);
	
	}

	function check_device( $matches=array() ) {

		$block = $matches[2];
		$device = $this->desktop;

		if ($matches[1] == "smartphone" OR $matches[1] == "tablet" OR $matches[1] == "desktop") $action = true; else $action = false;
		if ($matches[1] == "smartphone" OR $matches[1] == "not-smartphone") $device = $this->smartphone;
		if ($matches[1] == "tablet" OR $matches[1] == "not-tablet") $device = $this->tablet;

		if( $action ) {
			
			if( !$device ) return "";
		
		} else {
			
			if( $device ) return "";
		
		}

		return $block;
	}

	function declination( $matches=array() ) {

		$matches[1] = strip_tags($matches[1] );
	    $matches[1] = str_replace(' ', '', $matches[1] );

		$matches[1] = intval($matches[1]);
		$words = explode('|', trim($matches[2]));
		$parts_word = array();

		switch ( count($words) ) {
			case 1:
				$parts_word[0] = $words[0];
				$parts_word[1] = $words[0];
				$parts_word[2] = $words[0];
				break;
			case 2:
				$parts_word[0] = $words[0];
				$parts_word[1] = $words[0].$words[1];
				$parts_word[2] = $words[0].$words[1];
				break;
			case 3: 
				$parts_word[0] = $words[0];
				$parts_word[1] = $words[0].$words[1];
				$parts_word[2] = $words[0].$words[2];
				break;
			case 4: 
				$parts_word[0] = $words[0].$words[1];
				$parts_word[1] = $words[0].$words[2];
				$parts_word[2] = $words[0].$words[3];
				break;
		}
	
		$word = $matches[1]%10==1&&$matches[1]%100!=11?$parts_word[0]:($matches[1]%10>=2&&$matches[1]%10<=4&&($matches[1]%100<10||$matches[1]%100>=20)?$parts_word[1]:$parts_word[2]);
	
		return $word;
	}

	function check_page( $matches=array() ) {

		$pages = $matches[2];
		$block = $matches[3];

		if ($matches[1] == "page-count") $action = true; else $action = false;
	
		$pages = explode( ',', $pages );
		$page = intval($_GET['cstart']);

		if ( $page < 1 ) $page = 1;
		
		if( $action ) {
			
			if( !$this->_in_rangearray( $page, $pages ) ) return "";
		
		} else {
			
			if( $this->_in_rangearray( $page, $pages ) ) return "";
		
		}
		
		return $block;
	
	}

	function check_tag( $matches=array() ) {
		global $config;

		$params = $matches[2];
		$block = $matches[3];

		if ($matches[1] == "tags" OR $matches[1] == "news") $action = true; else $action = false;
		if ($matches[1] == "tags" OR $matches[1] == "not-tags") $tag = "tags";
		if ($matches[1] == "news" OR $matches[1] == "not-news") $tag = "news";
	
		$props = "";
		$params = trim($params);

		if ( $tag == "news" ) {

			if( defined( 'NEWS_ID' ) ) $props = NEWS_ID;
			$params = explode( ',', $params);
			
			if( $action ) {
				
				if( !$this->_in_rangearray( $props, $params ) ) return "";
			
			} else {
				
				if( $this->_in_rangearray( $props, $params ) ) return "";
			
			}
			
			return $block;
		
		} elseif ( $tag == "tags" ) {
		
			if( defined( 'CLOUDSTAG' ) ) {

				if( function_exists('mb_strtolower') ) {

					$params = mb_strtolower($params, $config['charset']);
					$props = trim(mb_strtolower(CLOUDSTAG, $config['charset']));

				} else {

					$params = strtolower($params);
					$props = trim(strtolower(CLOUDSTAG));

				}

			}

			$params = explode( ',', $params);

			if( $action ) {
				
				if( !in_array( $props, $params ) ) return "";
			
			} else {
				
				if( in_array( $props, $params ) ) return "";
			
			}
			
			return $block;
	
		} else return "";
	
	}
	
	function _in_rangearray($findvalue, $findarray) {
	
		$findvalue = trim($findvalue);
	
		foreach ($findarray as $value) {
			
			$value = trim($value);
			
			if( $value == $findvalue ) {
				
				return true;
			
			} elseif( count(explode('-', $value)) == 2 ) {
				
				list($min, $max) = explode('-', $value);
				
				$findvalue = intval($findvalue);
				$min = intval($min);
				$max = intval($max);
				
				if( $findvalue >= $min && $findvalue <= $max ) {
					return true;
				}
				
			}
		}
		
		return false;
	
	}
	
	function catnewscount( $matches=array() ) {
		global $cat_info;
		
		$id = intval($matches[1]);
		
		return intval($cat_info[$id]['newscount']);
	}

	function build_tree( $data ) {

		$tree = array();
		foreach ($data as $id=>&$node) {
			if ($node['parentid'] == 0) {
				$tree[$id] = &$node;
			} else {
				if (!isset($data[$node['parentid']]['children'])) $data[$node['parentid']]['children'] = array();
				$data[$node['parentid']]['children'][$id] = &$node;
			}
		}
		
		return $tree;

	}
	
	function recursive_array_search($needle, $haystack, $subcat = true, &$item = false) {
		
		if(!$item) $item = array();

		foreach($haystack as $key => $value) {

			if(in_array($key, $needle)) {
			
				if( $subcat === "only" ) {

					if(is_array( $value['children'] )) {
						
						foreach($value['children'] as $value2) {
							$item[$value2['id']] = $value2;
						}
						
					}
					
				} else $item[$key] = $value;
				
				if(!$subcat AND is_array( $value['children'] ) ) {
					unset($item[$key]['children']);
					$this->recursive_array_search($needle, $value['children'], $subcat, $item);
				}

			} elseif (is_array( $value['children'] ) ) {
				$this->recursive_array_search($needle, $value['children'], $subcat, $item);
			}
		}
		
		return $item;
	}

	function build_cat_menu( $matches=array() ) {
		global $cat_info, $config;

		if(!count($cat_info)) return "";

		if( !is_array($this->category_tree) ) {
			
			$this->category_tree = $this->build_tree($cat_info);
			
		}
		
		if(!count($this->category_tree)) return "";
		
		$param_str = trim($matches[1]);
		$allow_cache = $config['allow_cache'];
		$config['allow_cache'] = false;
		$catlist = $this->category_tree;
		$cache_id = md5($param_str);
		
		if( $config['category_newscount'] ) $cache_prefix = "news"; else $cache_prefix = "catmenu";
		
		if( preg_match( "#cache=['\"](.+?)['\"]#i", $param_str, $match ) ) {
			if( $match[1] == "yes" ) $config['allow_cache'] = 1;
		}
		
		$content = dle_cache( $cache_prefix, $cache_id );
		
		if( $content !== false ) {
			
			$config['allow_cache'] = $allow_cache;
			return $content;
		
		} else {
			
			if( preg_match( "#subcat=['\"](.+?)['\"]#i", $param_str, $match ) ) {
				
				$match[1] = trim($match[1]);
				
				if($match[1] == "yes") $subcat = true; else $subcat = false;
				
				if($match[1] == "only") $subcat = "only";
	
			} else $subcat = true;
			
			if( preg_match( "#id=['\"](.+?)['\"]#i", $param_str, $match ) ) {
	
				$temp_array = array();
		
				$match[1] = explode (',', $match[1]);
		
				foreach ($match[1] as $value) {
		
					if( count(explode('-', $value)) == 2 ) $temp_array[] = get_mass_cats($value);
					else $temp_array[] = intval($value);
		
				}
		
				$temp_array = implode(',', $temp_array);
			
				$catlist= $this->recursive_array_search( explode(',', $temp_array), $catlist, $subcat);
				
				if(!count($catlist)) return "";
				
			}
			
			if( preg_match( "#template=['\"](.+?)['\"]#i", $param_str, $match ) ) {
				$template_name = trim($match[1]);
			} else $template_name = "categorymenu";
	
			$template = $this->sub_load_template( $template_name . '.tpl' );
	
			$template = str_replace( "[root]", "", $template );
			$template = str_replace( "[/root]", "", $template );
			
			if( preg_match( "'\\[sub-prefix\\](.+?)\\[/sub-prefix\\]'si", $template, $match ) ) {
				$prefix = trim($match[1]);
				$template = str_replace( $match[0], "", $template );
			}
			
			if( preg_match( "'\\[sub-suffix\\](.+?)\\[/sub-suffix\\]'si", $template, $match ) ) {
				$suffix = trim($match[1]);
				$template = str_replace( $match[0], "", $template );
			}
			
			if($config['allow_cache']) {
				$template = preg_replace( "'\\[active\\](.+?)\\[/active\\]'si", "", $template );
				$template = str_replace( "[not-active]", "", $template );
				$template = str_replace( "[/not-active]", "", $template );
			}
		
			if( preg_match( "'\\[item\\](.+?)\\[/item\\]'si", $template, $match ) ) {
				$item = trim($match[1]);
				$template = str_replace( $match[0], "{items}", $template );
				
				$template = str_replace( "{items}", $this->compile_menu($catlist, $prefix, $item, $suffix, false, 0), $template );
				
			}
			
			create_cache( $cache_prefix, $template, $cache_id);
			
			$config['allow_cache'] = $allow_cache;
			
			return $template;
		
		}

	}

	function compile_menu( $nodes, $prefix, $item_template, $suffix, $sublevelmarker = false, $indent = 0 ) {
		
		$item = "";
		
		foreach ($nodes as $node) {
			
			if( !$node['id'] ) continue;
			
			$item .= $this->compile_item($node, $item_template);
			
			if (isset($node['children'])) {
				if ( stripos ( $item_template, "{sub-item}" ) !== false ) {
					$item = str_replace( "{sub-item}", $this->compile_menu($node['children'], $prefix, $item_template, $suffix, true, $indent+1), $item );
				} else {
					$item .= $this->compile_menu($node['children'], $prefix, $item_template, $suffix, true, $indent+1);
				}
			}
			
		}
		
		if( $sublevelmarker ) {
			
			$item =  $prefix.$item.$suffix;
			
		}
			
		
		return $item;
	}
	
	function compile_item( $row,  $template) {
		global $config, $category_id;
		
		$category = intval($category_id);
		
		$template = str_replace( "{id}", $row['id'], $template );
		$template = str_replace( "{name}", $row['name'], $template );
		$template = str_replace( "{icon}", $row['icon'], $template );
		
		if( $config['allow_alt_url'] ) {
			$template = str_replace( "{url}", $config['http_home_url'] . get_url( $row['id'] ) . "/" , $template );
		} else {
			$template = str_replace( "{url}", $config['http_home_url'] . "index.php?do=cat&amp;category=".$row['alt_name'] , $template );
		}
		
		$template = str_replace( "{news-count}", intval($row['newscount']), $template );
		
		if($category == $row['id']) {
			$template = str_replace( "[active]", "", $template );
			$template = str_replace( "[/active]", "", $template );
			$template = preg_replace( "'\\[not-active\\](.+?)\\[/not-active\\]'si", "", $template );
		} else {
			$template = str_replace( "[not-active]", "", $template );
			$template = str_replace( "[/not-active]", "", $template );
			$template = preg_replace( "'\\[active\\](.+?)\\[/active\\]'si", "", $template );
		}
		
	    if(!isset($row['children'])) {
			$template = str_replace( "{sub-item}", "", $template );
			$template = preg_replace( "'\\[isparent\\](.+?)\\[/isparent\\]'si", "", $template );
		} else {
			$template = str_replace( "[isparent]", "", $template );
			$template = str_replace( "[/isparent]", "", $template );
		}
		
		return $template;
		
	}
	
	function _clear() {
		
		$this->data = array ();
		$this->block_data = array ();
		$this->copy_template = $this->template;
	
	}
	
	function clear() {
		
		$this->data = array ();
		$this->block_data = array ();
		$this->copy_template = null;
		$this->template = null;
	
	}
	
	function global_clear() {
		
		$this->data = array ();
		$this->block_data = array ();
		$this->result = array ();
		$this->copy_template = null;
		$this->template = null;
	
	}
	
	function compile($tpl) {
		
		$time_before = $this->get_real_time();
		
		$find = $find_preg = $replace = $replace_preg = array();
		
		if( count( $this->block_data ) ) {
			
			foreach ( $this->block_data as $key_find => $key_replace ) {
				$find_preg[] = $key_find;
				$replace_preg[] = $key_replace;
			}
			
			$this->copy_template = preg_replace( $find_preg, $replace_preg, $this->copy_template );
		}

		foreach ( $this->data as $key_find => $key_replace ) {
			$find[] = $key_find;
			$replace[] = $key_replace;
		}
		
		$find[] = "{category-icon}";
		$replace[] = '{THEME}/dleimages/no_icon.gif';
		$find[] = "{category-url}";
		$replace[] = '';
		
		$this->copy_template = str_ireplace( $find, $replace, $this->copy_template );

		$find = $find_preg = $replace = $replace_preg = array();
		
		foreach ( $this->user_block_data as $key_find => $key_replace ) {
			$find_preg[] = $key_find;
			$replace_preg[] = $key_replace;
		}
			
		$this->copy_template = preg_replace( $find_preg, $replace_preg, $this->copy_template );
		
		foreach ( $this->user_data as $key_find => $key_replace ) {
			$find[] = $key_find;
			$replace[] = $key_replace;
		}	

		$this->copy_template = str_ireplace( $find, $replace, $this->copy_template );

		if (strpos ( $this->copy_template, "[declination=" ) !== false) {
			$this->copy_template = preg_replace_callback ( "#\\[declination=(.+?)\\](.+?)\\[/declination\\]#is", array( &$this, 'declination'), $this->copy_template );
		}
		
		if( strpos( $this->copy_template, "{customcomments" ) !== false ) {		
			$this->copy_template = preg_replace_callback( "#\\{customcomments(.+?)\\}#i", "custom_comments", $this->copy_template );
		
		}
		
		if( strpos( $this->copy_template, "{custom" ) !== false ) {		
			$this->copy_template = preg_replace_callback( "#\\{custom(.+?)\\}#i", "custom_print", $this->copy_template );
		
		}
		
		if( strpos( $this->template, "{include file=" ) !== false ) {
			$this->include_mode = 'php';			
			$this->copy_template = preg_replace_callback( "#\\{include file=['\"](.+?)['\"]\\}#i", array( &$this, 'load_file'), $this->copy_template );
		
		}
		
		$this->copy_template = str_replace(array("_&#123;_", "_&#91;_"), array("{", "["), $this->copy_template);
		
		if( isset( $this->result[$tpl] ) ) $this->result[$tpl] .= $this->copy_template;
		else $this->result[$tpl] = $this->copy_template;
		
		$this->_clear();
		
		$this->template_parse_time += $this->get_real_time() - $time_before;
	}

	function buld_user_data() {
		global $PHP_SELF, $member_id, $config, $user_group, $lang, $_IP;

		$this->user_data['{ip}'] = $_IP;

		if( $member_id['user_group'] != 5 ) {
			
			if ( count(explode("@", $member_id['foto'])) == 2 ) {
		
				$this->user_data['{foto}'] = 'https://www.gravatar.com/avatar/' . md5(trim($member_id['foto'])) . '?s=' . intval($user_group[$member_id['user_group']]['max_foto']);
			
			} else {
			
				if( $member_id['foto'] ) {
					
					if (strpos($member_id['foto'], "//") === 0) $avatar = "http:".$member_id['foto']; else $avatar = $member_id['foto'];
		
					$avatar = @parse_url ( $avatar );
		
					if( $avatar['host'] ) {
						
						$this->user_data['{foto}'] = $member_id['foto'];
						
					} else $this->user_data['{foto}'] = $config['http_home_url'] . "uploads/fotos/" . $member_id['foto'] ;
					
				} else $this->user_data['{foto}'] = "{THEME}/dleimages/noavatar.png" ;
		
			}
			
			$this->user_data['{profile-login}'] = stripslashes( $member_id['name'] );
			
			if( $member_id['fullname'] ) {
				
				$this->user_data['[fullname]'] = "";
				$this->user_data['[/fullname]'] = "";
				$this->user_data['{fullname}'] = stripslashes( $member_id['fullname'] );
				$this->user_block_data["'\\[not-fullname\\](.*?)\\[/not-fullname\\]'si"] = "";
			
			} else {
				
				$this->user_block_data["'\\[fullname\\](.*?)\\[/fullname\\]'si"] = "";
				$this->user_data['{fullname}'] = "";
				$this->user_data['[not-fullname]'] = "";
				$this->user_data['[/not-fullname]'] = "";
		
			}
			
			if( $member_id['land'] ) {
				
				$this->user_data['[land]'] =  "";
				$this->user_data['[/land]'] =  "";
				$this->user_data['{land}'] =  stripslashes( $member_id['land'] );
				$this->user_block_data["'\\[not-land\\](.*?)\\[/not-land\\]'si"] = "";
			
			} else {
				
				$this->user_block_data["'\\[land\\](.*?)\\[/land\\]'si"] = "";
				$this->user_data['{land}'] =  "";
				$this->user_data['[not-land]'] =  "";
				$this->user_data['[/not-land]'] =  "";
		
			}
			
			$this->user_data['{mail}'] =  stripslashes( $member_id['email'] );
			$this->user_data['{group}'] =  $user_group[$member_id['user_group']]['group_prefix'].$user_group[$member_id['user_group']]['group_name'].$user_group[$member_id['user_group']]['group_suffix'];
			$this->user_data['{registration}'] =  langdate( "j F Y H:i", $member_id['reg_date'] );
			$this->user_data['{lastdate}'] = langdate( "j F Y H:i", $member_id['lastdate'] );

			if( $user_group[$member_id['user_group']]['icon'] ) $this->user_data['{group-icon}'] = "<img src=\"" . $user_group[$member_id['user_group']]['icon'] . "\" alt=\"\">";
			else $this->user_data['{group-icon}'] =  "";

			if( $user_group[$member_id['user_group']]['time_limit'] ) {
				
				$this->user_block_data["'\\[time_limit\\](.*?)\\[/time_limit\\]'si"] = "\\1";
				
				if( $member_id['time_limit'] ) {
					
					$this->user_data['{time_limit}'] = langdate( "j F Y H:i", $member_id['time_limit'] );
				
				} else {
					
					$this->user_data['{time_limit}'] = $lang['no_limit'];
				
				}
			
			} else {
				
				$this->user_block_data["'\\[time_limit\\](.*?)\\[/time_limit\\]'si"] = "";
				$this->user_data['{time_limit}'] = "";
			
			}
			
			if( $member_id['comm_num'] ) {
				$this->user_data['[comm-num]'] = "";
				$this->user_data['[/comm-num]'] = "";
				$this->user_data['{comm-num}'] = number_format($member_id['comm_num'], 0, ',', ' ');
				$this->user_data['{comments}'] = "{$PHP_SELF}?do=lastcomments&amp;userid=" . $member_id['user_id'];
				$this->user_block_data["'\\[not-comm-num\\](.*?)\\[/not-comm-num\\]'si"] = "";

			} else {
				$this->user_data['{comments}'] = "";
				$this->user_data['{comm-num}'] = 0;
				$this->user_data['[not-comm-num]'] = "";
				$this->user_data['[/not-comm-num]'] = "";
				$this->user_block_data["'\\[comm-num\\](.*?)\\[/comm-num\\]'si"] = "";
				
			}
			
			if( $member_id['news_num'] ) {
				
				if( $config['allow_alt_url'] ) {
					$this->user_data['{news}'] = $config['http_home_url'] . "user/" . urlencode( $member_id['name'] ) . "/news/";
					$this->user_data['{rss}'] = $config['http_home_url'] . "user/" . urlencode( $member_id['name'] ) . "/rss.xml";
				} else {
					$this->user_data['{news}'] = $PHP_SELF . "?subaction=allnews&amp;user=" . urlencode( $member_id['name'] );
					$this->user_data['{rss}'] = $PHP_SELF . "?mod=rss&amp;subaction=allnews&amp;user=" . urlencode( $member_id['name'] );
				}
		
				$this->user_data['{news-num}'] = number_format($member_id['news_num'], 0, ',', ' ');
				$this->user_data['[news-num]'] = "";
				$this->user_data['[/news-num]'] = "";
				$this->user_block_data["'\\[not-news-num\\](.*?)\\[/not-news-num\\]'si"] = "";
		
			} else {
				
				$this->user_data['{news}'] = "";
				$this->user_data['{rss}'] = "";
				$this->user_data['{news-num}'] = 0;
				$this->user_data['[not-news-num]'] = "";
				$this->user_data['[/not-news-num]'] = "";
				$this->user_block_data["'\\[news-num\\](.*?)\\[/news-num\\]'si"] = "";
		
			}

			if ( $member_id['xfields'] ) {
			
				$xfields = xfieldsload( true );
				$xfieldsdata = xfieldsdataload( $member_id['xfields'] );
			
				foreach ( $xfields as $value ) {
					$preg_safe_name = preg_quote( $value[0], "'" );
			
					if( empty( $xfieldsdata[$value[0]] ) ) {
			
						$this->user_block_data["'\\[profile_xfgiven_{$preg_safe_name}\\](.*?)\\[/profile_xfgiven_{$preg_safe_name}\\]'is"] = "";
						$this->user_data["[profile_xfnotgiven_{$value[0]}]"] = "";
						$this->user_data["[/profile_xfnotgiven_{$value[0]}]"] = "";
			
					} else {
						
						$this->user_block_data["'\\[profile_xfnotgiven_{$preg_safe_name}\\](.*?)\\[/profile_xfnotgiven_{$preg_safe_name}\\]'is"] = "";
						$this->user_data["[profile_xfgiven_{$value[0]}]"] = "";
						$this->user_data["[/profile_xfgiven_{$value[0]}]"] = "";
					}
					
					$this->user_data["[profile_xfvalue_{$value[0]}]"] = stripslashes( $xfieldsdata[$value[0]] );
			
				}
			
			} else {
				
				$this->user_block_data["'\\[profile_xfgiven_(.*?)\\](.*?)\\[/profile_xfgiven_(.*?)\\]'is"] = "";
				$this->user_block_data["'\\[profile_xfvalue_(.*?)\\]'i"] = "";
				$this->user_block_data["'\\[profile_xfnotgiven_(.*?)\\](.*?)\\[/profile_xfnotgiven_(.*?)\\]'is"] = "";
			
			}

			$this->user_data['{new-pm}'] = $member_id['pm_unread'];
			$this->user_data['{all-pm}'] = $member_id['pm_all'];
			
			if( $member_id['pm_unread'] ) {
				$this->user_data['[new-pm]'] = "";
				$this->user_data['[/new-pm]'] = "";
			} else {
				$this->user_block_data["'\\[new-pm\\](.*?)\\[/new-pm\\]'si"] = "";
			}
			
			if ($member_id['favorites']) {
				$this->user_data['{favorite-count}'] = count(explode("," ,$member_id['favorites']));
			} else $this->user_data['{favorite-count}'] = 0;
	
			if ( $user_group[$member_id['user_group']]['allow_admin'] ) {
				$this->user_data['[admin-link]'] = "";
				$this->user_data['[/admin-link]'] = "";
				$this->user_data['{admin-link}'] = $config['http_home_url'] . $config['admin_path'] . "?mod=main";
			} else {
				$this->user_data['{admin-link}'] = "";
				$this->user_block_data["'\\[admin-link\\](.*?)\\[/admin-link\\]'si"] = "";
			}

			if ($config['allow_alt_url']) {
				$this->user_data['{profile-link}'] = $config['http_home_url'] . "user/" . urlencode ( $member_id['name'] ) . "/";
			} else {
				$this->user_data['{profile-link}'] = $PHP_SELF . "?subaction=userinfo&user=" . urlencode ( $member_id['name'] );
			}

		} else {
			
			$this->user_block_data["'\\[new-pm\\](.*?)\\[/new-pm\\]'si"] = "";
			$this->user_data['{profile-link}'] = "";
			$this->user_data['{admin-link}'] = "";
			$this->user_block_data["'\\[admin-link\\](.*?)\\[/admin-link\\]'si"] = "";
			$this->user_data['{favorite-count}'] = 0;
			$this->user_data['{new-pm}'] = '';
			$this->user_data['{all-pm}'] = '';	
			$this->user_block_data["'\\[profile_xfgiven_(.*?)\\](.*?)\\[/profile_xfgiven_(.*?)\\]'is"] = "";
			$this->user_block_data["'\\[profile_xfvalue_(.*?)\\]'i"] = "";
			$this->user_block_data["'\\[profile_xfnotgiven_(.*?)\\](.*?)\\[/profile_xfnotgiven_(.*?)\\]'is"] = "";
			$this->user_data['{news}'] = "";
			$this->user_data['{rss}'] = "";
			$this->user_data['{news-num}'] = 0;
			$this->user_data['[not-news-num]'] = "";
			$this->user_data['[/not-news-num]'] = "";
			$this->user_block_data["'\\[not-news-num\\](.*?)\\[/not-news-num\\]'si"] = "";
			$this->user_block_data["'\\[comm-num\\](.*?)\\[/comm-num\\]'si"] = "";
			$this->user_data['[not-comm-num]'] = "";
			$this->user_data['[/not-comm-num]'] = "";
			$this->user_data['{comments}'] = "";
			$this->user_data['{comm-num}'] = 0;	
			$this->user_block_data["'\\[time_limit\\](.*?)\\[/time_limit\\]'si"] = "";
			$this->user_data['{time_limit}'] = "";
			$this->user_data['{group-icon}'] =  "";
			$this->user_data['{registration}'] =  "";
			$this->user_data['{registration}'] =  "";
			$this->user_data['{group}'] =  "";
			$this->user_data['{mail}'] =  "";
			$this->user_data['{land}'] =  "";
			$this->user_data['[not-land]'] =  "";
			$this->user_data['[/not-land]'] =  "";
			$this->user_block_data["'\\[land\\](.*?)\\[/land\\]'si"] = "";
			$this->user_data['{profile-login}'] = '';
			$this->user_block_data["'\\[fullname\\](.*?)\\[/fullname\\]'si"] = "";
			$this->user_data['{fullname}'] = "";
			$this->user_data['[not-fullname]'] = "";
			$this->user_data['[/not-fullname]'] = "";
				
			$this->user_data['{foto}'] = "{THEME}/dleimages/noavatar.png";
			$this->user_data['{mail}'] =  "";
			
		}

		$this->user_loaded = true;
	}
	
	
	function get_real_time() {
		list ( $seconds, $microSeconds ) = explode( ' ', microtime() );
		return (( float ) $seconds + ( float ) $microSeconds);
	}
}
?>