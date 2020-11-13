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
 File: upload.php
-----------------------------------------------------
 Use: upload files
=====================================================
*/


if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

function xfparamload( $xfname ) {

	$path = ENGINE_DIR . '/data/xfields.txt';
	$filecontents = file( $path );
	
	if( !is_array( $filecontents ) ) {
		return false;
	}
	
	foreach ( $filecontents as $name => $value ) {
		$filecontents[$name] = explode( "|", trim( $value ) );
		if($filecontents[$name][0] == $xfname ) return $filecontents[$name];
	}
	
	return false;
}

$allowed_extensions = array ("gif", "jpg", "png", "jpeg", "webp" );
$allowed_video = array ("avi", "mp4", "wmv", "mpg", "mp3", "m4v", "m4a", "mov", "3gp", "mkv" );
$allowed_files = explode( ',', strtolower( $user_group[$member_id['user_group']]['files_type'] ) );

if( intval( $_REQUEST['news_id'] ) ) $news_id = intval( $_REQUEST['news_id'] ); else $news_id = 0;
if( isset( $_REQUEST['area'] ) ) $area = totranslit( $_REQUEST['area'] ); else $area = "";
if( isset( $_REQUEST['wysiwyg'] ) ) $wysiwyg = totranslit( $_REQUEST['wysiwyg'], true, false ); else $wysiwyg = 0;

if( isset( $_REQUEST['author'] ) ) {
	
	$author = @strip_tags(urldecode($_REQUEST['author']));
	
	if( preg_match( "/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\#|\/|\\\|\&\~\*\{\+]/", $author ) ) {
		die ( "{\"error\":\"{$lang['user_err_6']}\"}" );		
	}
	
	$author = @$db->safesql($author);
	
} else $author = "";

if( !$is_logged ) {
	die ( "{\"error\":\"{$lang['err_notlogged']}\"}" );
}

if( !$user_group[$member_id['user_group']]['allow_image_upload'] AND !$user_group[$member_id['user_group']]['allow_file_upload'] ) {
	if ( $area != "comments" ) {
		die ( "{\"error\":\"{$lang['err_noupload']}\"}" );	
	}
}

if ( !$author ) $author = $db->safesql($member_id['name']);
if ( !$user_group[$member_id['user_group']]['allow_all_edit'] AND $area != "comments" ) $author = $db->safesql($member_id['name']);

if ( $area == "template" ) {

	if ( !$user_group[$member_id['user_group']]['admin_static'] ) die( "Hacking attempt!" );

}

if ( $area == "comments" AND !$user_group[$member_id['user_group']]['allow_up_image'] ) {

	die( "Hacking attempt!" );

}

if ( $area == "adminupload" ) {

	if ( $member_id['user_group'] != 1 ) die( "Hacking attempt!" );

}

if ( $news_id AND $area != "template" AND $area != "comments" ) {

	$row = $db->super_query( "SELECT id, autor, approve FROM " . PREFIX . "_post WHERE id = '{$news_id}'" );

	if ( !$row['id'] OR ($row['approve'] AND !$user_group[$member_id['user_group']]['moderation']) ) die( "Hacking attempt!" );

	if ( !$user_group[$member_id['user_group']]['allow_all_edit'] AND $row['autor'] != $member_id['name'] ) die( "Hacking attempt!" );
}

if ( $news_id AND $area == "comments" ) {

	$row = $db->super_query( "SELECT id, user_id, date, is_register FROM " . PREFIX . "_comments WHERE id = '{$news_id}'" );

	if ( !$row['id'] ) die( "Hacking attempt!" );

	$have_perm = 0;
	$row['date'] = strtotime( $row['date'] );
	
	if( ($member_id['user_id'] == $row['user_id'] AND $row['is_register'] AND $user_group[$member_id['user_group']]['allow_editc']) OR $user_group[$member_id['user_group']]['edit_allc'] ) {
		$have_perm = 1;
	}
	
	if ( $user_group[$member_id['user_group']]['edit_limit'] AND (($row['date'] + ($user_group[$member_id['user_group']]['edit_limit'] * 60)) < $_TIME) ) {
		$have_perm = 0;
	}
	
	if ( !$have_perm ) die( "Hacking attempt!" );
	
}

if( $area == "comments" ) {
	$user_group[$member_id['user_group']]['allow_image_size'] = false;
	$user_group[$member_id['user_group']]['allow_file_upload'] = false;
	$config['max_up_side'] = $user_group[$member_id['user_group']]['up_image_side'];
	$config['max_up_size'] = $user_group[$member_id['user_group']]['up_image_size'];
	
	if ( !$user_group[$member_id['user_group']]['edit_allc'] ) $author = $db->safesql($member_id['name']);
	
}

//////////////////////
// go go upload
//////////////////////
if( $_REQUEST['subaction'] == "upload" ) {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		echo "{\"error\":\"{$lang['sess_error']}\"}";
		die();
	
	}
	
	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/uploads/upload.class.php'));

	if( $_REQUEST['mode'] == "quickload") $user_group[$member_id['user_group']]['allow_image_size'] = false;

	if( $user_group[$member_id['user_group']]['allow_image_size'] ) {

		if ( isset($_REQUEST['t_seite']) ) $t_seite = intval( $_REQUEST['t_seite'] ); else $t_seite = intval($config['t_seite']);
		if ( isset($_REQUEST['m_seite']) ) $m_seite = intval( $_REQUEST['m_seite'] ); else $m_seite = intval($config['t_seite']);
		if ( isset($_REQUEST['make_thumb']) ) $make_thumb = intval( $_REQUEST['make_thumb'] ); else $make_thumb = true;
		if ( isset($_REQUEST['make_medium']) ) $make_medium = intval( $_REQUEST['make_medium'] ); else $make_medium = true;

		$t_size = $_REQUEST['t_size'] ? $_REQUEST['t_size'] : $config['max_image'];
		$m_size = $_REQUEST['m_size'] ? $_REQUEST['m_size'] : $config['medium_image'];
		$make_watermark = $_REQUEST['make_watermark'] ? intval($_REQUEST['make_watermark']) : false;

		if(!$t_size) $make_thumb = false;
		if(!$m_size) $make_medium = false;

		if ( $area == "adminupload" ) {
		
			if ($config['allow_watermark']) $make_watermark = true; else $make_watermark = false;
			$t_seite = intval($config['t_seite']);
			$m_seite = intval($config['t_seite']);
			$t_size = $config['max_image'];
			$m_size = $config['medium_image'];
			$make_thumb = false;
			$make_medium = false;
		
		}

	} else {
		
		$t_seite = intval($config['t_seite']);
		$m_seite = intval($config['t_seite']);
		$t_size = $config['max_image'];
		$m_size = $config['medium_image'];
		$make_thumb = true;
		$make_medium = true;
		if ($config['allow_watermark']) $make_watermark = true; else $make_watermark = false;

		if(!$t_size) $make_thumb = false;
		if(!$m_size) $make_medium = false;
	
	}
	
	if( $area == "xfieldsimage" OR $area == "xfieldsimagegalery") {
		
		$xfparam = xfparamload( $_REQUEST['xfname'] );
		
		if( !is_array( $xfparam ) ) die("xfieldname not found");
		
		$_REQUEST['xfname'] = $xfparam[0];
		$t_seite = intval($config['t_seite']);
		$m_seite = intval($config['t_seite']);
		$t_size = $xfparam[13];
		$m_size = 0;
		$config['max_up_side'] = $xfparam[9];
		$config['max_up_size'] = $xfparam[10];
		$config['min_up_side'] = $xfparam[22];
		$config['files_allow'] = false;
		$user_group[$member_id['user_group']]['allow_file_upload'] = false;
		$make_watermark = $xfparam[11] ? true : false;
		$make_thumb = $xfparam[12] ? true : false;
		$make_medium = false;
		
	}
	
	if( $area == "xfieldsfile" ) {
		$xfparam = xfparamload( $_REQUEST['xfname'] );
		
		if( !is_array( $xfparam ) ) die("xfieldname not found");
		
		$_REQUEST['xfname'] = $xfparam[0];
		$user_group[$member_id['user_group']]['allow_image_upload'] = false;
		$user_group[$member_id['user_group']]['files_type'] = $xfparam[14];
		$user_group[$member_id['user_group']]['max_file_size'] = $xfparam[15];

	}
	
	if( $area == "comments" ) {
		$user_group[$member_id['user_group']]['allow_image_size'] = false;
		$user_group[$member_id['user_group']]['allow_file_upload'] = false;
		$user_group[$member_id['user_group']]['allow_image_upload'] = true;
		$config['max_up_side'] = $user_group[$member_id['user_group']]['up_image_side'];
		$config['max_up_size'] = $user_group[$member_id['user_group']]['up_image_size'];
		$config['min_up_side'] = $user_group[$member_id['user_group']]['min_image_side'];
		$t_seite = intval($config['t_seite']);
		$m_seite = intval($config['t_seite']);
		$t_size = $user_group[$member_id['user_group']]['up_thumb_size'];
		$m_size = 0;
		$make_watermark = $user_group[$member_id['user_group']]['allow_up_watermark'] ? true : false;
		$make_thumb = $user_group[$member_id['user_group']]['allow_up_thumb'] ? true : false;
		$make_medium = false;
	}

	$t_size = explode ("x", $t_size);
	
	if ( count($t_size) == 2) {
	
		$t_size = intval($t_size[0]) . "x" . intval($t_size[1]);
	
	} else {
	
		$t_size = intval( $t_size[0] );
	
	}


	$m_size = explode ("x", $m_size);
	
	if ( count($m_size) == 2) {
	
		$m_size = intval($m_size[0]) . "x" . intval($m_size[1]);
	
	} else {
	
		$m_size = intval( $m_size[0] );
	
	}

	$uploader = new FileUploader($area, $news_id, $author, $t_size, $t_seite, $make_thumb, $make_watermark, $m_size, $m_seite, $make_medium);
	$result = $uploader->FileUpload();
	echo $result;
	die();

}
//////////////////////
// go go delete uploaded files
//////////////////////
check_xss ();

if( $_POST['subaction'] == "deluploads" ) {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( isset( $_POST['images'] ) ) {

		$row = $db->super_query( "SELECT images  FROM " . PREFIX . "_images WHERE author = '$author' AND news_id = '$news_id'" );
		
		$listimages = explode( "|||", $row['images'] );

		$temp_images = $listimages;

		foreach ( $_POST['images'] as $image ) {
			
			$i = 0;
			
			reset( $listimages );
			
			foreach ( $temp_images as $dataimages ) {
				
				if( $dataimages == $image ) {
					
					$url_image = explode( "/", $image );
					
					if( count( $url_image ) == 2 ) {
						
						$folder_prefix = $url_image[0] . "/";
						$image = $url_image[1];
					
					} else {
						
						$folder_prefix = "";
						$image = $url_image[0];
					
					}
					
					unset( $listimages[$i] );
					$image = totranslit($image);
	
					@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $image );
					@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $image );
					@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "medium/" . $image );
				
				}
				
				$i ++;
			}
	
		}

		if( count( $listimages ) ) $row['images'] = implode( "|||", $listimages );
		else $row['images'] = "";

		if( $row['images'] ) $db->query( "UPDATE " . PREFIX . "_images set images='{$row['images']}' WHERE author = '{$author}' AND news_id = '{$news_id}'" );
		else $db->query( "DELETE FROM " . PREFIX . "_images WHERE news_id = '{$news_id}'" );

		if ($user_group[$member_id['user_group']]['allow_admin']) $db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '32', '{$news_id}')" );
	
	}

	if( $user_group[$member_id['user_group']]['allow_file_upload'] AND is_array($_POST['files']) AND count( $_POST['files'] ) ) {

		foreach ( $_POST['files'] as $file ) {
			
			$file = intval( $file );
			
			$row = $db->super_query( "SELECT id, onserver FROM " . PREFIX . "_files WHERE author = '$author' AND news_id = '$news_id' AND id='$file'" );		

			if ( $row['id'] ) {
				$url = explode( "/", $row['onserver'] );

				if( count( $url ) == 2 ) {
						
					$folder_prefix = $url[0] . "/";
					$file = $url[1];
					
				} else {
						
					$folder_prefix = "";
					$file = $url[0];
					
				}

				$file = totranslit( $file, false );
	
				if( trim($file) == ".htaccess") die("Hacking attempt!");	
	
				@unlink( ROOT_DIR . "/uploads/files/" . $folder_prefix . $file );

				$db->query( "DELETE FROM " . PREFIX . "_files WHERE id='{$row['id']}'" );
			}
		
		}

		if ($user_group[$member_id['user_group']]['allow_admin']) $db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '34', '{$news_id}')" );
	
	}


	if( $user_group[$member_id['user_group']]['admin_static'] AND is_array($_POST['static_files']) AND count( $_POST['static_files'] ) ) {

		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '33', '{$news_id}')" );
		
		foreach ( $_POST['static_files'] as $file ) {
			
			$file = intval( $file );
			
			$row = $db->super_query( "SELECT id, name, onserver FROM " . PREFIX . "_static_files WHERE static_id = '$news_id' AND id='$file'" );
			
			if( $row['id'] and $row['onserver'] ) {

				$url = explode( "/", $row['onserver'] );

				if( count( $url ) == 2 ) {
						
					$folder_prefix = $url[0] . "/";
					$file = $url[1];
					
				} else {
						
					$folder_prefix = "";
					$file = $url[0];
					
				}

				$file = totranslit( $file, false );
	
				if( trim($file) == ".htaccess") die("Hacking attempt!");	
	
				@unlink( ROOT_DIR . "/uploads/files/" . $folder_prefix . $file );

				$db->query( "DELETE FROM " . PREFIX . "_static_files WHERE id='{$row['id']}'" );
			
			} else {
				
				if( $row['id'] ) {
					$url_image = explode( "/", $row['name'] );
					
					if( count( $url_image ) == 2 ) {
						
						$folder_prefix = $url_image[0] . "/";
						$image = $url_image[1];
					
					} else {
						
						$folder_prefix = "";
						$image = $url_image[0];
					
					}

					$image = totranslit($image);					

					@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $image );
					@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $image );
					@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "medium/" . $image );
					$db->query( "DELETE FROM " . PREFIX . "_static_files WHERE id='{$row['id']}'" );
				
				}
			
			}
		}
	}


	if( $user_group[$member_id['user_group']]['allow_up_image'] AND is_array($_POST['comments_files']) AND count( $_POST['comments_files'] ) ) {
		
		foreach ( $_POST['comments_files'] as $file ) {
			
			$file = intval( $file );
			
			$row = $db->super_query( "SELECT id, name FROM " . PREFIX . "_comments_files WHERE c_id = '{$news_id}' AND id='{$file}' AND author = '{$author}'" );
				
			if( $row['id'] ) {
				$url_image = explode( "/", $row['name'] );
				
				if( count( $url_image ) == 2 ) {
					
					$folder_prefix = $url_image[0] . "/";
					$image = $url_image[1];
					
				} else {
					
					$folder_prefix = "";
					$image = $url_image[0];
				
				}

				$image = totranslit($image);					

				@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $image );
				@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $image );
				$db->query( "DELETE FROM " . PREFIX . "_comments_files WHERE id='{$row['id']}'" );
			
			}
			
		}
	}


}

//////////////////////
// go go show
//////////////////////

$skin = trim( totranslit($_REQUEST['skin'], false, false) );

if ( $skin ) {

	$css_path = $config['http_home_url']."templates/".$skin."/frame.css";

} else {

	$css_path = $config['http_home_url']."engine/skins/stylesheets/frame.css";

}

include (ENGINE_DIR . '/data/videoconfig.php');

if($_GET['dle_theme']) {
	$theme = htmlspecialchars( totranslit(strip_tags($_GET['dle_theme']), true, false), ENT_QUOTES, $config['charset'] );
} else $theme = "";


echo <<<HTML
<!doctype html>
<html>
<head>
<meta content="text/html; charset={$config['charset']}" http-equiv="content-type">
<title>{$lang['media_upload']}</title>
<link rel="stylesheet" type="text/css" href="{$css_path}">
<script src="{$config['http_home_url']}engine/classes/js/jquery.js"></script>
<script src="{$config['http_home_url']}engine/classes/uploads/html5/fileuploader.js"></script>
</head>
<body class="{$theme}">
HTML;


$uploaded_list = array();
$folder_list = array();

if( $area != "template" AND $area != "comments") {
		
	$row = $db->super_query( "SELECT images  FROM " . PREFIX . "_images WHERE news_id = '{$news_id}' AND author = '{$author}'" );

	if( $row['images'] ) {

		$listimages = explode( "|||", $row['images'] );	

		foreach ( $listimages as $dataimages ) {

			$url_image = explode( "/", $dataimages );
			
			if( count( $url_image ) == 2 ) {
				
				$folder_prefix = $url_image[0] . "/";
				$dataimages = $url_image[1];
			
			} else {
				
				$folder_prefix = "";
				$dataimages = $url_image[0];
			
			}

			if( file_exists( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages ) ) {

				$this_size = @filesize( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages );
				$img_info = @getimagesize( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages );
				$img_url = 	$config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages;

				if( file_exists( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "medium/" . $dataimages ) ) {
					$img_url = 	$config['http_home_url'] . "uploads/posts/" . $folder_prefix . "medium/" . $dataimages;
					$medium_data = "yes";
				} else {
					$medium_data = "no";

				}

				if( file_exists( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $dataimages ) ) {
					$img_url = 	$config['http_home_url'] . "uploads/posts/" . $folder_prefix . "thumbs/" . $dataimages;
					$thumb_data = "yes";
				} else {
					$thumb_data = "no";

				}

				$file_name = explode("_", $dataimages);
				unset($file_name[0]);
				$file_name = implode("_", $file_name);

				$data_url = $config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages;
				$uploaded_list[] = "<div class=\"uploadedfile\"><div class=\"info\">{$file_name}</div><div class=\"uploadimage\"><a class=\"uploadfile\" href=\"{$data_url}\" data-src=\"{$data_url}\" data-thumb=\"{$thumb_data}\" data-medium=\"{$medium_data}\" data-type=\"image\"><img style=\"width:auto;height:auto;max-width:100px;max-height:90px;\" src=\"" . $img_url . "\" /></a></div><div class=\"info\"><input type=\"checkbox\" name=\"images[" . $folder_prefix . $dataimages . "]\" value=\"" . $folder_prefix . $dataimages . "\" data-thumb=\"{$thumb_data}\" data-medium=\"{$medium_data}\" data-type=\"image\" data-src=\"{$data_url}\">&nbsp;{$img_info[0]}x{$img_info[1]}</div></div>";

			}

		}
	}

	$db->query( "SELECT *  FROM " . PREFIX . "_files WHERE author = '$author' AND news_id = '$news_id'" );

	while ( $row = $db->get_row() ) {
		
		if($row['size']) {
			$this_size = formatsize( $row['size'] );
		} else {
			$this_size = formatsize( @filesize( ROOT_DIR . "/uploads/files/" . $row['onserver'] ) );
		}

		$file_type = explode( ".", $row['name'] );
		$file_type = totranslit( end( $file_type ) );


		if( in_array( $file_type, $allowed_video ) ) {
				
			if( $file_type == "mp3" ) {
					
				$file_link = $config['http_home_url'] . "engine/skins/images/mp3_file.png";
				$data_url = $config['http_home_url'] . "uploads/files/" . $row['onserver'];
				$file_play = "audio";
				
			} else {
					
				$file_link = $config['http_home_url'] . "engine/skins/images/video_file.png";
				$data_url = $config['http_home_url'] . "uploads/files/" . $row['onserver'];
				$file_play = "video";
			}
			
		} else { $file_link = $config['http_home_url'] . "engine/skins/images/all_file.png";  $data_url = "#"; $file_play = ""; };

		$uploaded_list[] = "<div class=\"uploadedfile\"><div class=\"info\">{$row['name']}</div><div class=\"uploadimage\"><a class=\"uploadfile\" href=\"{$data_url}\" data-src=\"{$row['id']}:{$row['name']}\" data-type=\"file\" data-play=\"{$file_play}\"><img style=\"width:auto;height:auto;max-width:100px;max-height:90px;\" src=\"" . $file_link . "\" /></a></div><div class=\"info\"><input type=\"checkbox\" id=\"file\" name=\"files[]\" value=\"{$row['id']}\" data-type=\"file\">&nbsp;{$this_size}</div></div>";


	}

}


if( $area == "template" ) {

	$db->query( "SELECT id, name FROM " . PREFIX . "_static_files WHERE static_id = '{$news_id}' AND onserver = ''" );

	while ( $row = $db->get_row() ) {

		$url_image = explode( "/", $row['name'] );
			
		if( count( $url_image ) == 2 ) {
				
			$folder_prefix = $url_image[0] . "/";
			$dataimages = $url_image[1];
			
		} else {
				
			$folder_prefix = "";
			$dataimages = $url_image[0];
			
		}

		if( file_exists( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages ) ) {

			$this_size = @filesize( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages );
			$img_info = @getimagesize( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages );
			$img_url = 	$config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages;

			if( file_exists( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "medium/" . $dataimages ) ) {
				$img_url = 	$config['http_home_url'] . "uploads/posts/" . $folder_prefix . "medium/" . $dataimages;
				$medium_data = "yes";
			} else {
				$medium_data = "no";

			}

			if( file_exists( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $dataimages ) ) {
				$img_url = 	$config['http_home_url'] . "uploads/posts/" . $folder_prefix . "thumbs/" . $dataimages;
				$thumb_data = "yes";
			} else {
				$thumb_data = "no";

			}	

			$file_name = explode("_", $dataimages);
			unset($file_name[0]);
			$file_name = implode("_", $file_name);

			$data_url = $config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages;
			$uploaded_list[] = "<div class=\"uploadedfile\"><div class=\"info\">{$file_name}</div><div class=\"uploadimage\"><a class=\"uploadfile\" href=\"{$data_url}\" data-src=\"{$data_url}\" data-thumb=\"{$thumb_data}\" data-medium=\"{$medium_data}\" data-type=\"image\"><img style=\"width:auto;height:auto;max-width:100px;max-height:90px;\" src=\"" . $img_url . "\" /></a></div><div class=\"info\"><input type=\"checkbox\" name=\"static_files[]\" value=\"" . $row['id'] . "\" data-thumb=\"{$thumb_data}\" data-medium=\"{$medium_data}\" data-src=\"{$data_url}\" data-type=\"image\">&nbsp;{$img_info[0]}x{$img_info[1]}</div></div>";

		}
	
	}

	$db->query( "SELECT id, name, onserver, size FROM " . PREFIX . "_static_files WHERE static_id = '{$news_id}' AND onserver != ''" );
		
	while ( $row = $db->get_row() ) {
		
		if($row['size']) {
			$this_size = formatsize( $row['size'] );
		} else {
			$this_size = formatsize( @filesize( ROOT_DIR . "/uploads/files/" . $row['onserver'] ) );
		}
		
		$file_type = explode( ".", $row['name'] );
		$file_type = totranslit( end( $file_type ) );


		if( in_array( $file_type, $allowed_video ) ) {
				
			if( $file_type == "mp3" ) {
					
				$file_link = $config['http_home_url'] . "engine/skins/images/mp3_file.png";
				$data_url = $config['http_home_url'] . "uploads/files/" . $row['onserver'];
				$file_play = "audio";
				
			} else {
					
				$file_link = $config['http_home_url'] . "engine/skins/images/video_file.png";
				$data_url = $config['http_home_url'] . "uploads/files/" . $row['onserver'];
				$file_play = "video";
			}
			
		} else { $file_link = $config['http_home_url'] . "engine/skins/images/all_file.png";  $data_url = "#"; $file_play = ""; };

		$uploaded_list[] = "<div class=\"uploadedfile\"><div class=\"info\">{$row['name']}</div><div class=\"uploadimage\"><a class=\"uploadfile\" href=\"{$data_url}\" data-src=\"{$row['id']}:{$row['name']}\" data-type=\"file\" data-play=\"{$file_play}\"><img style=\"width:auto;height:auto;max-width:100px;max-height:90px;\" src=\"" . $file_link . "\" /></a></div><div class=\"info\"><input type=\"checkbox\" id=\"file\" name=\"static_files[]\" value=\"{$row['id']}\" data-type=\"file\">&nbsp;{$this_size}</div></div>";


	}

}

if( $area == "comments" ) {

	$db->query( "SELECT id, name FROM " . PREFIX . "_comments_files WHERE c_id = '{$news_id}' AND author = '{$author}'" );

	while ( $row = $db->get_row() ) {

		$url_image = explode( "/", $row['name'] );
			
		if( count( $url_image ) == 2 ) {
				
			$folder_prefix = $url_image[0] . "/";
			$dataimages = $url_image[1];
			
		} else {
				
			$folder_prefix = "";
			$dataimages = $url_image[0];
			
		}

		if( file_exists( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages ) ) {

			$this_size = @filesize( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages );
			$img_info = @getimagesize( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages );
			$img_url = 	$config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages;

			$medium_data = "no";

			if( file_exists( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $dataimages ) ) {
				$img_url = 	$config['http_home_url'] . "uploads/posts/" . $folder_prefix . "thumbs/" . $dataimages;
				$thumb_data = "yes";
			} else {
				$thumb_data = "no";

			}	

			$file_name = explode("_", $dataimages);
			unset($file_name[0]);
			$file_name = implode("_", $file_name);

			$data_url = $config['http_home_url'] . "uploads/posts/" . $folder_prefix . $dataimages;
			$uploaded_list[] = "<div class=\"uploadedfile\"><div class=\"info\">{$file_name}</div><div class=\"uploadimage\"><a class=\"uploadfile\" href=\"{$data_url}\" data-src=\"{$data_url}\" data-thumb=\"{$thumb_data}\" data-medium=\"{$medium_data}\" data-type=\"image\"><img style=\"width:auto;height:auto;max-width:100px;max-height:90px;\" src=\"" . $img_url . "\" /></a></div><div class=\"info\"><input type=\"checkbox\" name=\"comments_files[]\" value=\"" . $row['id'] . "\" data-thumb=\"{$thumb_data}\" data-medium=\"{$medium_data}\" data-src=\"{$data_url}\" data-type=\"image\">&nbsp;{$img_info[0]}x{$img_info[1]}</div></div>";

		}
	
	}

}

$img_dir = opendir(  ROOT_DIR . "/uploads/" );

while ( $file = readdir( $img_dir ) ) {
	$images_in_dir[] = $file;
}

natcasesort( $images_in_dir );
reset( $images_in_dir );

if ( count( $images_in_dir ) ) {
	foreach ( $images_in_dir as $url_image ) {

		$img_type = explode( ".", $url_image );
		$img_type = totranslit( end( $img_type ) );

		if( in_array( $img_type, $allowed_extensions ) AND is_file( ROOT_DIR . "/uploads/" . $url_image ) ) {

			$img_info = @getimagesize( ROOT_DIR . "/uploads/" . $url_image );
			$img_url = 	$config['http_home_url'] . "uploads/" . $url_image;

			if( file_exists( ROOT_DIR . "/uploads/medium/" . $url_image ) ) {
				$img_url = 	$config['http_home_url'] . "uploads/medium/" . $url_image;
				$medium_data = "yes";
			} else {
				$medium_data = "no";

			}	

			if( file_exists( ROOT_DIR . "/uploads/thumbs/" . $url_image ) ) {
				$img_url = 	$config['http_home_url'] . "uploads/thumbs/" . $url_image;
				$thumb_data = "yes";
			} else {
				$thumb_data = "no";

			}		

			$data_url = $config['http_home_url'] . "uploads/" . $url_image;
			$folder_list[] = "<div class=\"uploadedfile\"><div class=\"info\">{$url_image}</div><div class=\"uploadimage\"><a class=\"uploadfile\" href=\"{$data_url}\" data-src=\"{$data_url}\" data-thumb=\"{$thumb_data}\" data-medium=\"{$medium_data}\" data-type=\"image\"><img style=\"width:auto;height:auto;max-width:100px;max-height:90px;\" src=\"" . $img_url . "\" /></a></div><div class=\"info\">{$img_info[0]}x{$img_info[1]}</div></div>";


		}
	
	}
}

if ( count ($uploaded_list) ) $uploaded_list = implode("", $uploaded_list); else $uploaded_list = "";
if ( count ($folder_list) ) $folder_list = implode("", $folder_list); else $folder_list = "";

$image_align = array ();
$image_align[$config['image_align']] = "selected";


if( $user_group[$member_id['user_group']]['allow_file_upload'] ) {
		
	if( $user_group[$member_id['user_group']]['max_file_size'] ) {
			
		$lang['files_max_info'] = $lang['files_max_info'] . " " . formatsize( $user_group[$member_id['user_group']]['max_file_size'] * 1024 );
		
	} else {
			
		$lang['files_max_info'] = $lang['files_max_info_2'];
		
	}
		
	$lang['files_max_info_1'] = $lang['files_max_info'] . "<br />" . $lang['files_max_info_1'] . " " . formatsize( $config['max_up_size'] * 1024 );
	
} else {
		
	$lang['files_max_info_1'] = $lang['files_max_info_1'] . " " . formatsize( $config['max_up_size'] * 1024 );
	
}

if( $user_group[$member_id['user_group']]['allow_image_size'] ) {
	
	$t_seite_selected[$config['t_seite']] = "selected";
	$upload_param = "";

	if ( $config['max_image'] )	{

		$upload_param .= <<<HTML
<hr />
<input type="checkbox" name="make_thumb" value="1" id="make_thumb" checked="checked">&nbsp;<label for="make_thumb">{$lang['images_ath']}</label>
<div>{$lang['upload_t_size']}&nbsp;<input class="edit bk" type="text" name="t_size" id="t_size" size="9" value="{$config['max_image']}">&nbsp;px&nbsp;<select name="t_seite" id="t_seite"><option value="0" {$t_seite_selected[0]}>{$lang['upload_t_seite_1']}</option><option value="1" {$t_seite_selected[1]}>{$lang['upload_t_seite_2']}</option><option value="2" {$t_seite_selected[2]}>{$lang['upload_t_seite_3']}</option></select></div>
HTML;

	}

	if ( $config['medium_image'] )	{

		$upload_param .= <<<HTML
<hr />
<input type="checkbox" name="make_medium" value="1" id="make_medium" checked="checked">&nbsp;<label for="make_medium">{$lang['images_amh']}</label>
<div>{$lang['upload_m_size']}&nbsp;<input class="edit bk" type="text" name="m_size" id="m_size" size="9" value="{$config['medium_image']}">&nbsp;px&nbsp;<select name="m_seite" id="m_seite"><option value="0" {$t_seite_selected[0]}>{$lang['upload_t_seite_1']}</option><option value="1" {$t_seite_selected[1]}>{$lang['upload_t_seite_2']}</option><option value="2" {$t_seite_selected[2]}>{$lang['upload_t_seite_3']}</option></select></div>
HTML;

	}

	if( $config['allow_watermark'] ) $upload_param .= "<hr /><input type=\"checkbox\" name=\"make_watermark\" value=\"yes\" id=\"make_watermark\" checked=\"checked\">&nbsp;<label for=\"make_watermark\">{$lang['images_water']}</label>";

	if( !extension_loaded( "gd" ) ) $upload_param = "<span style=\"color:red;\"><b>{$lang['images_nogd']}</b></span>";

} else $upload_param = "";


if( $member_id['user_group'] == 1 AND $area != "comments" ) {

	$ftp_input = "<div><hr /><b>/uploads/files/</b>&nbsp;<input class=\"edit bk\" type=\"text\" id=\"ftpurl\" name=\"ftpurl\" style=\"width:99%;max-width:350px;\">&nbsp;<button class=\"edit\" onclick=\"upload_from_url('ftp'); return false;\" style=\"width:115px;\">{$lang['db_load_a']}</button><div id=\"upload-viaftp-status\"></div></div>";

} else $ftp_input = "";
	
	if( $user_group[$member_id['user_group']]['allow_file_upload'] ) {
		
		if( ! $user_group[$member_id['user_group']]['max_file_size'] ) $max_file_size = 0;
		elseif( $user_group[$member_id['user_group']]['max_file_size'] > $config['max_up_size'] ) $max_file_size = ( int ) $user_group[$member_id['user_group']]['max_file_size'];
		else $max_file_size = ( int )$config['max_up_size'];
	
	} else {
		
		$max_file_size = ( int )$config['max_up_size'];
	
	}

	$max_file_size = $max_file_size * 1024;
	
	$config['max_file_count'] = intval( $config['max_file_count'] );

	$all_ext = "*." . implode( ";*.", $allowed_extensions );
	$simple_ext = implode( "', '", $allowed_extensions );

	if( $config['files_allow'] and $user_group[$member_id['user_group']]['allow_file_upload'] ) {

		$all_ext .= ";*." . implode( ";*.", $allowed_files );
		$simple_ext .= "', '" . implode( "', '", $allowed_files );
	}

	$author = urlencode($author);
	
	$root = explode ( "controller.php", strtolower ( $_SERVER['PHP_SELF'] ) );
	$root = reset ( $root );
	$root = htmlspecialchars($root, ENT_QUOTES, $config['charset'] );

	if( $area != "comments") {
		$gen_tab = "<li><a href='#' id=\"link3\" onclick=\"tabClick(1); return false;\" title=\"{$lang['images_lgem']}\"><span>{$lang['images_lgem']}</span></a></li>";
		$hidden_params="";
		$auto_close="";
	} else {
		$gen_tab = "";
		$hidden_params=" style=\"display:none;\"";
		$auto_close="parent.$('#mediaupload').dialog('close');";
	}
	
echo <<<HTML
<div class="tabs">
  <ul>
	<li><a href='#' id="link1" onclick="tabClick(2); return false;" title='{$lang['media_upload_st']}' class="current" ><span>{$lang['media_upload_st']}</span></a></li>
	<li><a href='#' id="link2" onclick="tabClick(0); return false;" title='{$lang['images_iln']}'><span>{$lang['images_iln']}</span></a></li>
	{$gen_tab}
  </ul>
</div>
<div style="clear: both;"></div>
<div class="box">
<form action="" method="post" name="form" id="form">
<input type="hidden" name="subaction" value="upload">
<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
<div id="stmode">
	<div id="simpleupload">
		<div id="file-uploader"></div>
	</div>
	<div><hr />{$lang['images_upurl']}&nbsp;<input class="edit bk" type="text" id="copyurl" name="copyurl" style="width:99%;max-width:350px;">&nbsp;<button class="edit" onclick="upload_from_url('url'); return false;" style="width:115px;">{$lang['db_load_a']}</button><div id="upload-viaurl-status"></div></div>
	{$ftp_input}
	<div>{$upload_param}</div>
	<div><hr />{$lang['files_max_info_1']}</div>
</div>
</form>
<form action="" method="post" name="delimages" id="delimages">
<input type="hidden" name="subaction" value="deluploads">
<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
<input type="hidden" name="area" value='{$area}'>
<div id="cont1" style="display:none;">{$uploaded_list}</div>
<div id="cont2" style="display:none;">{$folder_list}</div>
</form>
</div>
<div style="clear: both;"></div>
<div>
<div class="properties">{$lang['images_align']}&nbsp;<select id="imagealign" name="imagealign">
          <option value="none" {$image_align[0]}>{$lang['opt_sys_no']}</option>
          <option value="left" {$image_align['left']}>{$lang['images_left']}</option>
          <option value="right" {$image_align['right']}>{$lang['images_right']}</option>
          <option value="center" {$image_align['center']}>{$lang['images_center']}</option>
        </select></div>
<div style="float: right;"><button class="button" onclick="check_uncheck_all(); return false;">{$lang['edit_selall']}</button><button class="button" onclick="insert_all(); return false;">{$lang['images_all_insert']}</button><button class="button" onclick="delete_file(); return false;">{$lang['images_del']}</button></div>
</div>
<div style="clear: both;"></div>
<div id="linkbox" class="linkbox" style="display:none;">
<div id="linkboximage" style="display:none;">
<table width="100%">
	<tr{$hidden_params}>
		<td width="150">{$lang['media_upload_url']}</td>
		<td><input id="imageurl" name="imageurl" value="" style="width:99%;" class="edit bk" /></td>
	</tr>
	<tr>
		<td width="150">{$lang['media_upload_title']}</td>
		<td><input id="imagetitle" name="imagetitle" value="" style="width:99%;" class="edit bk" /></td>
	</tr>
	<tr{$hidden_params}>
		<td><div id="imgparam"></div></td>
		<td><div id="imgparam1"></div></td>
	</tr>
	<tr{$hidden_params}>
		<td><div id="imgparam6"></div></td>
		<td><div id="imgparam7"></div></td>
	</tr>
</table>
</div>
<div id="linkboxfile" style="display:none;">
<table width="100%">
	<tr>
		<td width="190"><div id="imgparam2"></div></td>
		<td><div id="imgparam3"></div></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td>{$lang['media_upload_link']}</td>
		<td><input id="fileurl" name="fileurl" value="" style="width:99%;" class="edit bk" /></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td><div id="imgparam4"></div></td>
		<td><div id="imgparam5"></div></td>
	</tr>
</table>
</div>
<div style="clear: both;"></div>
<div style="float: right;"><button id="ins_image" class="button" onclick="insert_image(); return false;" style="display:none;">{$lang['media_upload_b1']}</button><button id="ins_file" class="button" onclick="insert_file(); return false;" style="display:none;">{$lang['media_upload_b2']}</button></div>
<div style="clear: both;"></div>
</div>
HTML;

$max_file_size = number_format($max_file_size, 0, '', '');

if ( $uploaded_list ) $im_show = "tabClick(0);"; else $im_show = "";

echo <<<HTML
<script>
jQuery(function($){

	var totaladded = 0;
	var totaluploaded = 0;
	{$im_show}

	var uploader = new qq.FileUploader({
		element: document.getElementById('file-uploader'),
		action: '{$root}controller.php?mod=upload',
		maxConnections: 1,
		encoding: 'multipart',
        sizeLimit: {$max_file_size},
		allowedExtensions: ['{$simple_ext}'],
	    params: {"subaction" : "upload", "news_id" : "{$news_id}", "area" : "{$area}", "author" : "{$author}", "user_hash" : "{$dle_login_hash}"},
        template: '<div class="qq-uploader">' + 
                '<div class="qq-upload-drop-area"><span>{$lang['media_upload_st5']}</span></div>' +
                '<div class="qq-upload-button">{$lang['media_upload_st4']}</div>' +
                '<ul class="qq-upload-list" style="display:none;"></ul>' + 
             '</div>',
		onSubmit: function(id, fileName) {

					uploader._options.params['t_size'] = $('#t_size').val();
					uploader._options.params['t_seite'] = $('#t_seite').val();
					uploader._options.params['make_thumb'] = $("#make_thumb").is(":checked") ? 1 : 0;
					uploader._options.params['m_size'] = $('#m_size').val();
					uploader._options.params['m_seite'] = $('#m_seite').val();
					uploader._options.params['make_medium'] = $("#make_medium").is(":checked") ? 1 : 0;
					uploader._options.params['make_watermark'] = $("#make_watermark").is(":checked") ? 1 : 0;
					totaladded ++;

					$('<div id="uploadfile-'+id+'" class="file-box"><span class="qq-upload-file-status">{$lang['media_upload_st6']}</span><span class="qq-upload-file">&nbsp;'+fileName+'</span><span class="qq-status"><span class="qq-upload-spinner"></span><span class="qq-upload-size"></span></span><div class="progress "><div class="progress-bar progress-blue" style="width: 0%"><span>0%</span></div></div></div>').appendTo('#file-uploader');

        },
		onProgress: function(id, fileName, loaded, total){
					$('#uploadfile-'+id+' .qq-upload-size').text(uploader._formatSize(loaded)+' {$lang['media_upload_st8']} '+uploader._formatSize(total));
					var proc = Math.round(loaded / total * 100);
					$('#uploadfile-'+id+' .progress-bar').css( "width", proc + '%' );
					$('#uploadfile-'+id+' .qq-upload-spinner').css( "display", "inline-block");

		},
		onComplete: function(id, fileName, response){
						totaluploaded ++;

						if ( response.success ) {
							var returnbox = response.returnbox;

							returnbox = returnbox.replace(/&lt;/g, "<");
							returnbox = returnbox.replace(/&gt;/g, ">");
							returnbox = returnbox.replace(/&amp;/g, "&");

							$('#uploadfile-'+id+' .qq-status').html('{$lang['media_upload_st9']}');
							
							if( response.tinypng_error ) {
								var timeout = 10000; 
								$('#uploadfile-'+id+' .qq-status').append( '<br /><span style="color:red;">{$lang['tinyapi_error']} ' + response.tinypng_error + '</span>' );
							} else {
								var timeout = 1000; 
							}
							
							$('#cont1').append( returnbox );

							if (totaluploaded == totaladded ) {
								setTimeout(function() {
									tabClick(0);
								}, timeout);
							
							}

							setTimeout(function() {
								$('#uploadfile-'+id).fadeOut('slow', function() { $(this).remove(); });
							}, timeout);

						} else {
							$('#uploadfile-'+id+' .qq-status').html('{$lang['media_upload_st10']}');

							if( response.error ) $('#uploadfile-'+id+' .qq-status').append( '<br /><span style="color:red;">' + response.error + '</span>' );

							setTimeout(function() {
								$('#uploadfile-'+id).fadeOut('slow');
							}, 10000);
						}
		},
        messages: {
            typeError: "{$lang['media_upload_st11']}",
            sizeError: "{$lang['media_upload_st12']}",
            emptyError: "{$lang['media_upload_st13']}"
        },
		debug: false
    });

	$(document).on("click", ".uploadfile", function() {

		$('#linkbox').show();

		if ( $(this).data('type') == "image" ) {

			var copies = false;
			var chk = 'checked="checked"';

			$("#linkboxfile").hide();
			$('#linkboximage').show();
			$('#ins_image').show();
			$('#ins_file').hide();
			$('#imageurl').val( $(this).data('src') );
			$('#imgparam').html('');
			$('#imgparam1').html('');
			$('#imgparam6').html('');
			$('#imgparam7').html('');

			if ( $(this).data('thumb') == "yes" ) {
				$('#imgparam1').append('<input type="radio" name="thumbimg" id="thumbimg" value="1" '+chk+' /><label for="thumbimg">{$lang['media_upload_ip2']}</label>&nbsp;');
				copies = true;
				chk = '';

			}

			if ( $(this).data('medium') == "yes" ) {
				copies = true;
				$('#imgparam1').append('<input type="radio" name="thumbimg" id="thumbimg1" value="2" '+chk+' /><label for="thumbimg1">{$lang['media_upload_ip6']}</label>&nbsp;');

			}

			if( copies ) {
				$('#imgparam').html('{$lang['media_upload_ip1']}');
				$('#imgparam1').append('<input type="radio" name="thumbimg" id="thumbimg2" value="0" /><label for="thumbimg2">{$lang['media_upload_ip3']}</label>');

				$('#imgparam6').html('{$lang['media_upload_ip7']}');
				$('#imgparam7').html('<input type="radio" name="insertoriginal" id="insertoriginal" value="0" checked="checked" /><label for="insertoriginal">{$lang['media_upload_ip8']}</label>&nbsp;<input type="radio" name="insertoriginal" id="insertoriginal1" value="1" /><label for="insertoriginal1">{$lang['media_upload_ip9']}</label>');
			}


		} else {

			$('#linkboximage').hide();
			$("#linkboxfile").show();
			$('#ins_image').hide();
			$('#ins_file').show();

			$('#fileurl').val( '[attachment='+$(this).data('src') +']' );

			var mode = $(this).data('play');

			if ( mode == "video" || mode == "audio" || mode == "flash") {
				$('#imgparam2').html('{$lang['media_upload_play']}');
				$('#imgparam4').html('{$lang['media_upload_ip1']}');
				$('#imgparam5').html('<input type="radio" name="filemode" value="1" checked="checked" />&nbsp;{$lang['media_upload_ip4']}&nbsp;&nbsp;<input type="radio" name="filemode" value="0" />&nbsp;{$lang['media_upload_ip5']}');

				if ( mode == "video" ) $('#imgparam3').html('<input id="playurl" name="playurl" value="[video={$video_config['width']},'+$(this).attr('href')+']" style="width:420px;" class="edit bk" />');
				if ( mode == "audio" ) $('#imgparam3').html('<input id="playurl" name="playurl" value="[audio={$video_config['audio_width']},'+$(this).attr('href')+']" style="width:420px;" class="edit bk" />');
				if ( mode == "flash" ) $('#imgparam3').html('<input id="playurl" name="playurl" value="[flash=560,315]'+$(this).attr('href')+'[/flash]" style="width:420px;" class="edit bk" />');
			} else {

				$('#imgparam2').html('');
				$('#imgparam3').html('');
				$('#imgparam4').html('');
				$('#imgparam5').html('');
			}

		}

		return false;

	});

});
function tabClick(n) {

	if (n == 0) {
		$("#cont2").hide();
		$("#stmode").hide();
		$("#linkbox").hide();
		$("#cont1").fadeTo('slow', 1);
		$("#link2").addClass("current");
		$("#link1").removeClass("current");
		$("#link3").removeClass("current");

	}

	if (n == 1) {
		$("#stmode").hide();
		$("#cont1").hide();
		$("#linkbox").hide();
		$("#cont2").fadeTo('slow', 1);
		$("#link3").addClass("current");
		$("#link1").removeClass("current");
		$("#link2").removeClass("current");
	}

	if (n == 2) {
		$("#cont2").hide();
		$("#cont1").hide();
		$("#linkbox").hide();
		$("#stmode").fadeTo('slow', 1);
		$("#link1").addClass("current");
		$("#link2").removeClass("current");
		$("#link3").removeClass("current");
	}

};
function check_uncheck_all() {
    var frm = document.delimages;
    for (var i=0;i<frm.elements.length;i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type=='checkbox') {
            if(elmnt.checked == true){ elmnt.checked=false; }
            else{ elmnt.checked=true; }
        }
    }
};

function insert_all() {

    var frm = document.delimages;
    var wysiwyg = '{$wysiwyg}';
	var allways_bbimages = '{$config['bbimages_in_wysiwyg']}';
	var links = new Array();
	var align = $('#imagealign').val();
	var content = '';
	var t = 0;
	var url = '';
	var imagetag = 'thumb';

    for (var i=0;i<frm.elements.length;i++) {
   
     var elmnt = frm.elements[i];
 
       if (elmnt.type=='checkbox') {
            if(elmnt.checked == true){ 
				if ($(elmnt).data('type') == "image" ) {
					if ( $(elmnt).data('thumb') == "yes" || $(elmnt).data('medium') == "yes" ) {
						if( $(elmnt).data('medium') == "yes" ) { imagetag = 'medium'; } else { imagetag = 'thumb'; }
						links[t] = buildthumb ($(elmnt).data('src'), true, imagetag);
					} else {
						links[t] = buildimage ($(elmnt).data('src'), true);
					}
				}
				if ($(elmnt).data('type') == "file" ) {
					links[t] = '[attachment='+elmnt.value+']';
				}
				t++;
			}
		}
	}

	if (wysiwyg != 'no') {
		if ( wysiwyg == '1' ) {
			if (align == 'center') { content = links.join('<br>'); } else { content = links.join(' '); }
			if (align == 'center' && content != "" && allways_bbimages == '1') { content = '<div style="text-align:center;">'+ content +'</div>'; }
		} else {
			if (align == 'center') {
			
				if(allways_bbimages == '1') {
					content = links.join('</p><p style="text-align: center;">');
					content = '<p style="text-align: center;">'+ content +'</p>';
				} else {
					content = links.join('</p><p>&nbsp;</p><p>');
					content = '<p>'+ content +'</p>';
				}
				
			} else {
				content = links.join(' ');
			}
		
		}

	} else {
		content = links.join('\\n');
		if (align == 'center' && content != "" ) { content = '[center]'+ content +'[/center]'; }
	}

	insertcontent( content );

};

function insertcontent( content ) {
    var wysiwyg = '{$wysiwyg}';
	var allways_bbimages = '{$config['bbimages_in_wysiwyg']}';

	if ( wysiwyg == '1' ) {
		parent.active_editor.events.focus();
		parent.active_editor.selection.restore();
		parent.active_editor.undo.saveStep();
		if(allways_bbimages == '1') {
			parent.active_editor.html.insert( content );
		} else {
			parent.active_editor.html.insert( content + parent.$.FE.MARKERS );
		}
		parent.active_editor.undo.saveStep();


	} else if (wysiwyg == '2') {
		if(allways_bbimages == '1') {
			parent.tinyMCE.execCommand( 'mceInsertContent', false, content );
		} else {
			parent.tinyMCE.execCommand( 'mceInsertContent', false, content + '&nbsp;' );
		}

	} else {
		parent.doInsert( content, '', false );
	}
	
	{$auto_close}
};

function escapeHtml( string ) {

	var entityMap = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;',
		'/': '&#x2F;',
		'`': '&#x60;',
		'=': '&#x3D;',
		'?': '&#x3F'
	};
	
	return String(string).replace(/[&<>"'`=\/\?]/g, function (match) {
		return entityMap[match];
	});
	
}

function buildthumb( image, mass, tag ) {

	var align = $('#imagealign').val();
	var content = '';
    var wysiwyg = '{$wysiwyg}';
	var allways_bbimages = '{$config['bbimages_in_wysiwyg']}';

	if( (wysiwyg == '1' || wysiwyg == '2') && allways_bbimages != '1') {
	
		if( tag == 'thumb' ) {
			var folder="thumbs";
		} else {
			var folder="medium";
		}
		
		url = image.split('/');
		var filename = url.pop();
		url.push(folder);
		url.push(filename);
		url = url.join('/');

		content = '<a href="'+image+'" class="highslide" target="_blank">';
		content += buildimage( url, mass );
		content += '</a>';
		
	} else {
	
		var imgoption = "";
		var imagealt = $('#imagetitle').val();
	
		if (imagealt != "") { 
	
			imgoption = "|"+imagealt;
	
		}
	
		if (align != "none" && align != "center") { 

			imgoption = align+imgoption;

		}
	
		if (imgoption != "" ) {
	
			imgoption = "="+imgoption;

		}
	
		content = '['+tag+''+imgoption+']'+ image +'[/'+tag+']';
		
		if ( !mass && align == "center") {
			if(wysiwyg == 'no') {
				content = '[center]'+ content +'[/center]';
			} else {
				content = '<div style="text-align:center;">'+ content +'</div>';
			}
		}
	
	}


	return content;
};

function buildimage( image, mass ) {

    var wysiwyg = '{$wysiwyg}';
	var content = '';
	var align = $('#imagealign').val();
	var imagealt = $('#imagetitle').val();
	var allways_bbimages = '{$config['bbimages_in_wysiwyg']}';
	
	imagealt = escapeHtml(imagealt);
	
	if ( mass ) {
	
		if (wysiwyg != 'no' && allways_bbimages == '1') {
			wysiwyg = 'no';
		}
		
		if (wysiwyg != 'no') {
			
			if ( wysiwyg == '1' ) {
				var img_opt;
				
				if (align == 'center') {
					img_opt = "fr-dib";				
				} else if(align == 'none') {
					img_opt = "fr-dii";
				} else if(align == 'left') {
					img_opt = "fr-dii fr-fil";
				} else {
					img_opt = "fr-dii fr-fir";	
				}
				
				content = '<img src="'+ image +'" alt="'+ imagealt +'" class="'+ img_opt +'">';

			} else {
			
				if (align == 'center' || align == 'none') {
				
					if(align == 'center') {
						img_opt = " style=\"display: block; margin-left: auto; margin-right: auto;\"";
					} else {
						img_opt = "";
					}
					
					content = '<img src="'+ image +'" alt="'+ imagealt +'"'+ img_opt +'>';
					
				} else {
				
					content = '<img src="'+ image +'" style="float:' + align+ ';" alt="'+ imagealt +'">';
					
				}
				
			}

		} else {
	
			var imgoption = "";
			var imagealt = $('#imagetitle').val();
	
			if (imagealt != "") { 
	
				imgoption = "|"+imagealt;
	
			}
	
			if (align != "none" && align != "center") { 
	
				imgoption = align+imgoption;
	
			}
	
			if (imgoption != "" ) {
	
				imgoption = "="+imgoption;
	
			}

			content = '[img'+imgoption+']'+ image +'[/img]';
	
		}
		
	} else {

		if (wysiwyg != 'no' && allways_bbimages != '1') {

			var imagealt = $('#imagetitle').val();
			imagealt = escapeHtml(imagealt);
			
			if ( wysiwyg == '1' ) {
				var img_opt;
				
				if (align == 'center') {
					img_opt = "fr-dib";				
				} else if(align == 'none') {
					img_opt = "fr-dii";
				} else if(align == 'left') {
					img_opt = "fr-dii fr-fil";
				} else {
					img_opt = "fr-dii fr-fir";	
				}
				
				content = '<img src="'+ image +'" alt="'+ imagealt +'" class="'+ img_opt +'">';

				
			} else {
			
				if (align == 'center' || align == 'none') {
				
					if(align == 'center') {
						img_opt = " style=\"display: block; margin-left: auto; margin-right: auto;\"";
					} else {
						img_opt = "";
					}
					
					content = '<img src="'+ image +'" alt="'+ imagealt +'"'+ img_opt +'>';
					
				} else {
				
					content = '<img src="'+ image +'" alt="'+ imagealt +'" style="float:' + align+ ';">';
					
				}
	
			}

		} else {

			var imgoption = "";
			var imagealt = $('#imagetitle').val();
			
			if (imagealt != "") { 
	
				imgoption = "|"+imagealt;
	
			}
	
			if (align != "none" && align != "center") { 
	
				imgoption = align+imgoption;
	
			}
	
			if (imgoption != "" ) {
	
				imgoption = "="+imgoption;
	
			}

			content = '[img'+imgoption+']'+ image +'[/img]';

			if (align == "center" && wysiwyg == 'no') {
				content = '[center]'+ content +'[/center]';
			} else if(align == "center") {
				content = '<div style="text-align:center;">'+ content +'</div>';
			}

		}

	}

	return content;
};

function insert_image() {

	var type = $('#imgparam1 input:radio[name=thumbimg]:checked').val();
	var insertoriginal = $('#imgparam7 input:radio[name=insertoriginal]:checked').val();
	var content = '';
	var url = $('#imageurl').val();

	if ( insertoriginal == 1 || typeof(type) == "undefined" || type == 0 ) {

		if( type && (type == 1 || type == 2) ) {

			if( type == 1 ) {
				var folder="thumbs";
			} else {
				var folder="medium";
			}

			url = url.split('/');
			var filename = url.pop();
			url.push(folder);
			url.push(filename);
			url = url.join('/');
		
		}

		content = buildimage (url, false);

	} else {

		if( type && type == 1 ) {

			content = buildthumb (url, false, 'thumb');

		} else {

			content = buildthumb (url, false, 'medium');

		}


	} 


	insertcontent( content );
};

function insert_file() {

	var type = $('#imgparam5 input:radio[name=filemode]:checked').val()

	if( type ) {

		if( type == 1 ) {
	
			insertcontent( $('#fileurl').val() );
	
		} else {
	
			insertcontent( $('#playurl').val() );
	
		}

	} else {

		insertcontent( $('#fileurl').val() );

	}

};
function upload_from_url( url ) {

	var t_size = $('#t_size').val();
	var t_seite = $('#t_seite').val();
	var m_size = $('#m_size').val();
	var m_seite = $('#m_seite').val();
	var make_thumb = $("#make_thumb").is(":checked") ? 1 : 0;
	var make_medium = $("#make_medium").is(":checked") ? 1 : 0;
	var make_watermark = $("#make_watermark").is(":checked") ? 1 : 0;

	if (url == 'url' ) {

		var copyurl = $('#copyurl').val();
		var ftpurl = '';
		var error_id = 'upload-viaurl-status';		
	} else {

		var ftpurl = $('#ftpurl').val();
		var copyurl = '';
		var error_id = 'upload-viaftp-status';
	}

	$('#'+error_id).html( '<span style="color:green;">{$lang['ajax_info']}</span>' );

	$.post( "{$root}controller.php?mod=upload", { news_id: "{$news_id}", imageurl: copyurl, ftpurl: ftpurl, t_size: t_size, t_seite: t_seite, make_thumb: make_thumb, m_size: m_size, m_seite: m_seite, make_medium: make_medium, make_watermark: make_watermark, area: "{$area}", author: "{$author}", subaction: "upload", user_hash : "{$dle_login_hash}" }, function(data){

		if ( data.success ) {

			var returnbox = data.returnbox;

			returnbox = returnbox.replace(/&lt;/g, "<");
			returnbox = returnbox.replace(/&gt;/g, ">");
			returnbox = returnbox.replace(/&amp;/g, "&");

			$('#cont1').append( returnbox );

			$('#'+error_id).html('');

			if (url == 'url' ) {
				$('#copyurl').val('');
			} else {
				$('#ftpurl').val('');
			}

			tabClick(0);

		} else {

			if( data.error ) $('#'+error_id).html( '<span style="color:red;">' + data.error + '</span>' );

		}

	}, "json");
	return false;

};

function delete_file() {
	parent.DLEconfirm( '{$lang['delete_selected']}', '{$lang['p_info']}', function () {
		document.delimages.submit();
	} );
};

</script>
</body>
</html>
HTML;
?>