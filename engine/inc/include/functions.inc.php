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
 File: functions.inc.php
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

if ( $config['auth_domain'] ) {

	$domain_cookie = explode (".", clean_url( $_SERVER['HTTP_HOST'] ));
	$domain_cookie_count = count($domain_cookie);
	$domain_allow_count = -2;
	
	if ( $domain_cookie_count > 2 ) {
	
		if ( in_array($domain_cookie[$domain_cookie_count-2], array('com', 'net', 'org') )) $domain_allow_count = -3;
		if ( $domain_cookie[$domain_cookie_count-1] == 'ua' ) $domain_allow_count = -3;
		$domain_cookie = array_slice($domain_cookie, $domain_allow_count);
	}
	
	$domain_cookie = "." . implode (".", $domain_cookie);
	
	if( ip2long($_SERVER['HTTP_HOST']) == -1 OR ip2long($_SERVER['HTTP_HOST']) === false) define( 'DOMAIN', $domain_cookie );
	else define( 'DOMAIN', null );

} else define( 'DOMAIN', null );

function dle_session( $sid = false ) {
	global $config;
	
	$params = session_get_cookie_params();

	if ( DOMAIN ) $params['domain'] = DOMAIN;
	
	if ($config['only_ssl']) $params['secure'] = true;

	session_set_cookie_params($params['lifetime'], "/", $params['domain'], $params['secure'], true);

	if ( $sid ) @session_id( $sid );

	@session_start();

}

function set_cookie($name, $value, $expires) {
	global $config;
	
	if( $expires ) {
		
		$expires = time() + ($expires * 86400);
	
	} else {
		
		$expires = FALSE;
	
	}
	
	if ($config['only_ssl']) setcookie( $name, $value, $expires, "/", DOMAIN, TRUE, TRUE );
	else setcookie( $name, $value, $expires, "/", DOMAIN, NULL, TRUE );

}

function check_login($username, $md5_password, $post = true, $check_log = false) {
	global $member_id, $db, $user_group, $lang, $_IP, $_TIME, $config;

	if( $username == "" OR $md5_password == "" ) return false;
	
	$result = false;
	
	if( $post ) {
		
		$username = $db->safesql( $username );
		if( strlen($md5_password) > 72 ) $md5_password = substr($md5_password, 0, 72);

		if ($config['auth_metod']) {

			if ( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\/|\\\|\&\~\*\+]/", $username) ) return false;	
			$where_name = "email='{$username}'";
	
		} else {

			if ( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $username) ) return false;
			$where_name = "name='{$username}'";
	
		}

		$member_id = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE {$where_name}" );
		
		if( $member_id['user_id'] AND $member_id['password'] AND $member_id['banned'] != 'yes' AND $user_group[$member_id['user_group']]['allow_admin'] ) {
			
			if( is_md5hash( $member_id['password'] ) ) {
				
				if($member_id['password'] == md5( md5($md5_password) ) ) {
					$result = true;
				}
				
			} else {
				
				if(password_verify($md5_password, $member_id['password'] ) ) {
					$result = true;
				}
				
			}
			
		}
		
		if( !$result ) {

			$member_id = array ();
	
			$username = $db->safesql(trim( htmlspecialchars( stripslashes($username), ENT_QUOTES, $config['charset'])));
	
			if( version_compare($config['version_id'], "9.3", '>') ) $db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$username."', '{$_TIME}', '{$_IP}', '89', '')" );

		}

	} else {
		
		$username = intval( $username );
		
		$member_id = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE user_id='{$username}'" );
		
		if( $member_id['user_id'] AND $member_id['password'] AND md5($member_id['password']) == $md5_password AND $user_group[$member_id['user_group']]['allow_admin'] AND $member_id['banned'] != 'yes' ) {

			$result = true;

		} else {

			$username = $db->safesql(trim( htmlspecialchars( stripslashes($member_id['name']), ENT_QUOTES, $config['charset'])));

			$member_id = array ();
	
			if( version_compare($config['version_id'], "9.3", '>') ) $db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$username."', '{$_TIME}', '{$_IP}', '90', '')" );

		}
	
	}

	if( $result ) {
		
		if( !allowed_ip( $member_id['allowed_ip'] ) OR !allowed_ip( $config['admin_allowed_ip'] ) ) {
			
			$member_id = array ();
			$result = false;
			set_cookie( "dle_user_id", "", 0 );
			set_cookie( "dle_name", "", 0 );
			set_cookie( "dle_password", "", 0 );
			set_cookie( "dle_hash", "", 0 );
			@session_destroy();
			@session_unset();
			set_cookie( session_name(), "", 0 );
			
			msg( "info", $lang['index_msge'], $lang['ip_block'] );
		
		}
	}

	if ( !$result ) { 

		if ($config['login_log']) $db->query( "INSERT INTO " . PREFIX . "_login_log (ip, count, date) VALUES('{$_IP}', '1', '".time()."') ON DUPLICATE KEY UPDATE count=count+1, date='".time()."'" );

	} else {

		if ( $check_log AND !$_SESSION['check_log']) {

			if( $post ) { $a_id = 82; $extr =""; } else { $a_id = 86; if ($_SERVER['HTTP_REFERER']) $extr = $db->safesql(htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES)); else $extr = "Direct DLE Adminpanel"; }

			if( version_compare($config['version_id'], "9.3", '>') )  $db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '{$a_id}', '{$extr}')" );
			
			$_SESSION['check_log'] = 1;
		}

	}

	return $result;
}


function deletenewsbyid( $id ) {
	global $config, $db;
	
	$id = intval($id);
	
	$row = $db->super_query( "SELECT user_id FROM " . PREFIX . "_post_extras WHERE news_id = '{$id}'" );
	
	$db->query( "UPDATE " . USERPREFIX . "_users SET news_num=news_num-1 WHERE user_id='{$row['user_id']}'" );
	
	$db->query( "DELETE FROM " . PREFIX . "_post WHERE id='{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_post_extras WHERE news_id='{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id='{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_poll WHERE news_id='{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_poll_log WHERE news_id='{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_post_log WHERE news_id='{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_post_pass WHERE news_id='{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_xfsearch WHERE news_id = '{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_logs WHERE news_id = '{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_subscribe WHERE news_id='{$id}'");

	deletecommentsbynewsid( $id );

	$row = $db->super_query( "SELECT images  FROM " . PREFIX . "_images WHERE news_id = '{$id}'" );

	$listimages = explode( "|||", $row['images'] );

	if( $row['images'] != "" ) foreach ( $listimages as $dataimages ) {
		$url_image = explode( "/", $dataimages );

		if( count( $url_image ) == 2 ) {

			$folder_prefix = $url_image[0] . "/";
			$dataimages = $url_image[1];

		} else {

			$folder_prefix = "";
			$dataimages = $url_image[0];

		}

		@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages );
		@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $dataimages );
		@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "medium/" . $dataimages );
	}

	$db->query( "DELETE FROM " . PREFIX . "_images WHERE news_id = '{$id}'" );
	
	$db->query( "SELECT id, onserver FROM " . PREFIX . "_files WHERE news_id = '{$id}'" );

	while ( $row = $db->get_row() ) {

		$url = explode( "/", $row['onserver'] );

		if( count( $url ) == 2 ) {

			$folder_prefix = $url[0] . "/";
			$file = $url[1];

		} else {

			$folder_prefix = "";
			$file = $url[0];

		}
		$file = totranslit( $file, false );

		if( trim($file) == ".htaccess") continue;

		@unlink( ROOT_DIR . "/uploads/files/" . $folder_prefix . $file );

	}

	$db->query( "DELETE FROM " . PREFIX . "_files WHERE news_id = '{$id}'" );
	
	$sql_result = $db->query( "SELECT user_id, favorites FROM " . USERPREFIX . "_users WHERE favorites LIKE '%{$id}%'" );
	
	while ( $row = $db->get_row($sql_result) ) {
		
		$temp_fav = explode( ",", $row['favorites'] );
		$new_fav = array();
		
		foreach ( $temp_fav as $value ) {
			$value = intval($value);
			if($value != $id ) $new_fav[] = $value;
		}
		
		if(count($new_fav)) $new_fav = $db->safesql(implode(",", $new_fav));
		else $new_fav = "";
		
		$db->query( "UPDATE " . USERPREFIX . "_users SET favorites='{$new_fav}' WHERE user_id='{$row['user_id']}'" );

	}
	
}

function deletecomments( $id ) {
	global $config, $db;
	
	$id = intval($id);

	$row = $db->super_query( "SELECT id, post_id, user_id, is_register, approve FROM " . PREFIX . "_comments WHERE id = '{$id}'" );
	
	$db->query( "DELETE FROM " . PREFIX . "_comments WHERE id = '{$id}'" );
	$db->query( "DELETE FROM " . PREFIX . "_comment_rating_log WHERE c_id = '{$id}'" );	

	if( $row['is_register'] ) {
		$db->query( "UPDATE " . USERPREFIX . "_users SET comm_num=comm_num-1 WHERE user_id ='{$row['user_id']}'" );
	}
	
	if($row['approve']) $db->query( "UPDATE " . PREFIX . "_post SET comm_num=comm_num-1 WHERE id='{$row['post_id']}'" );

	$db->query( "SELECT id, name FROM " . PREFIX . "_comments_files WHERE c_id = '{$id}'" );
	
	while ( $row = $db->get_row() ) {
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
			
	}
	
	$db->query( "DELETE FROM " . PREFIX . "_comments_files WHERE c_id = '{$id}'" );
	
	if ( $config['tree_comments'] ) {

		$sql_result = $db->query( "SELECT id FROM " . PREFIX . "_comments WHERE parent = '{$id}'" );
	
		while ( $row = $db->get_row( $sql_result ) ) {
			deletecomments( $row['id'] );
		}

	}

}

function deletecommentsbynewsid( $id ) {
	global $config, $db;
	
	$id = intval($id);

	$result = $db->query( "SELECT id FROM " . PREFIX . "_comments WHERE post_id='{$id}'" );
	
	while ( $row = $db->get_array( $result ) ) {
		
		$db->query( "DELETE FROM " . PREFIX . "_comment_rating_log WHERE c_id = '{$row['id']}'" );

		$db->query( "SELECT id, name FROM " . PREFIX . "_comments_files WHERE c_id = '{$row['id']}'" );
		
		while ( $file = $db->get_row() ) {
			$url_image = explode( "/", $file['name'] );
			
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
				
		}
		
		$db->query( "DELETE FROM " . PREFIX . "_comments_files WHERE c_id = '{$row['id']}'" );
	
	}
	
	$result = $db->query( "SELECT COUNT(*) as count, user_id FROM " . PREFIX . "_comments WHERE post_id='{$id}' AND is_register='1' GROUP BY user_id" );
	
	while ( $row = $db->get_array( $result ) ) {
		
		$db->query( "UPDATE " . USERPREFIX . "_users SET comm_num=comm_num-{$row['count']} WHERE user_id='{$row['user_id']}'" );
	
	}
	
	$db->query( "DELETE FROM " . PREFIX . "_comments WHERE post_id='{$id}'" );


}

function deletecommentsbyuserid( $id, $ip = false ) {
	global $config, $db;
	
	$id = intval($id);
	
	if($ip) {
		$ip = $db->safesql($ip);
		$result = $db->query( "SELECT id, post_id, user_id, is_register, approve FROM " . PREFIX . "_comments WHERE ip='{$ip}' AND is_register='0'" );
	} else {
		$result = $db->query( "SELECT id, post_id, user_id, is_register, approve FROM " . PREFIX . "_comments WHERE user_id='{$id}' AND is_register='1'" );
	}
	
	while ( $row = $db->get_array( $result ) ) {
		
		$db->query( "DELETE FROM " . PREFIX . "_comment_rating_log WHERE c_id = '{$row['id']}'" );

		$db->query( "SELECT id, name FROM " . PREFIX . "_comments_files WHERE c_id = '{$row['id']}'" );
		
		while ( $file = $db->get_row() ) {
			$url_image = explode( "/", $file['name'] );
			
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
				
		}
		
		$db->query( "DELETE FROM " . PREFIX . "_comments_files WHERE c_id = '{$row['id']}'" );
	
	}
	
	if($ip) {
		
		$result = $db->query( "SELECT COUNT(*) as count, post_id FROM " . PREFIX . "_comments WHERE ip='{$ip}' AND is_register='0' AND approve='1' GROUP BY post_id" );
			
		while ( $row = $db->get_array( $result ) ) {
			
			$db->query( "UPDATE " . PREFIX . "_post SET comm_num=comm_num-{$row['count']} WHERE id='{$row['post_id']}'" );
		
		}
		$db->free( $result );
			
		$db->query( "DELETE FROM " . PREFIX . "_comments WHERE ip='{$ip}' AND is_register='0'" );
		
	} else {
		
		$result = $db->query( "SELECT COUNT(*) as count, post_id FROM " . PREFIX . "_comments WHERE user_id='{$id}' AND is_register='1' AND approve='1' GROUP BY post_id" );
			
		while ( $row = $db->get_array( $result ) ) {
	
			$db->query( "UPDATE " . PREFIX . "_post SET comm_num=comm_num-{$row['count']} WHERE id='{$row['post_id']}'" );
			
		}

		$db->free( $result );

		$db->query( "DELETE FROM " . PREFIX . "_comments WHERE user_id='{$id}' AND is_register='1'" );
	}


}

function formatsize($file_size) {
	
	if( !$file_size OR $file_size < 1) return '0 b';
	
    $prefix = array("b", "Kb", "Mb", "Gb", "Tb");
    $exp = floor(log($file_size, 1024)) | 0;
	
    return round($file_size / (pow(1024, $exp)), 2).' '.$prefix[$exp];

}

function CheckCanGzip() {
	
	if( headers_sent() || connection_aborted() || ! function_exists( 'ob_gzhandler' ) || ini_get( 'zlib.output_compression' ) ) return 0;
	
	if( strpos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip' ) !== false ) return "x-gzip";
	if( strpos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) !== false ) return "gzip";
	
	return 0;
}

function GzipOut() {
	
	$ENCODING = CheckCanGzip();
	
	if( $ENCODING ) {
		$Contents = ob_get_contents();
		ob_end_clean();
		
		header( "Content-Encoding: $ENCODING" );
		
		$Contents = gzencode( $Contents, 1, FORCE_GZIP );
		echo $Contents;
		
		exit();
	} else {
		//      ob_end_flush(); 
		exit();
	}
}

function allowed_ip($ip_array) {
	
	$ip_array = trim( $ip_array );

	$_IP = get_ip();

	if( !$ip_array ) {
		return true;
	}
	
	if( strpos($_IP, ":") === false ) {
		$delimiter = ".";
	} else $delimiter = ":";
	
	$db_ip_split = explode( $delimiter, $_IP );
	$ip_lenght = count($db_ip_split);
	
	$ip_array = explode( "|", $ip_array );
	
	foreach ( $ip_array as $ip ) {
		
		$ip = trim( $ip );
		
		if( $ip == $_IP ) {
			
			return true;
		
		} elseif( count(explode ('/', $ip)) == 2 ) {
				
			if( maskmatch($_IP, $ip) ) return true;
				
		} else {
			
			$ip_check_matches = 0;
			$this_ip_split = explode( $delimiter, $ip );
			
			for($i_i = 0; $i_i < $ip_lenght; $i_i ++) {
				if( $this_ip_split[$i_i] == $db_ip_split[$i_i] OR $this_ip_split[$i_i] == '*' ) {
					$ip_check_matches += 1;
				}
			
			}
			
			if( $ip_check_matches == $ip_lenght ) return true;
		}
	
	}
	
	return false;
}


function maskmatch($IP, $CIDR) {
	
    list ($address, $netmask) = explode('/', $CIDR, 2);

	if( strpos($IP, ".") !== false AND strpos($CIDR, ".") !== false ) {
		
		return ( ip2long($IP) & ~((1 << (32 - $netmask)) - 1) ) == ip2long ($address);
	
	} elseif( strpos($IP, ":") !== false AND strpos($CIDR, ":") !== false ) {
		
        if (!((extension_loaded('sockets') && defined('AF_INET6')) || @inet_pton('::1'))) {
          return false;
        }
		
        $bytesAddr = unpack('n*', @inet_pton($address));
        $bytesTest = unpack('n*', @inet_pton($IP));

        if (!$bytesAddr || !$bytesTest) {
            return false;
        }

        for ($i = 1, $ceil = ceil($netmask / 16); $i <= $ceil; ++$i) {
            $left = $netmask - 16 * ($i - 1);
            $left = ($left <= 16) ? $left : 16;
            $mask = ~(0xffff >> $left) & 0xffff;
            if (($bytesAddr[$i] & $mask) != ($bytesTest[$i] & $mask)) {
                return false;
            }
        }
		
		return true;
		
	}
	
	return false;

}

function msg($type, $title, $text, $back = false) {
	global $lang;
	
	$buttons = array();
	
	if(is_array( $back )) {
		$bc = 1;
		
		foreach ($back as $key => $value) {
			
			if($bc == 1) $color="teal";
			elseif($bc == 2) $color="slate-600";
			elseif($bc == 3) $color="brown-600";
			else $color="primary-600";
			
			if( $value == $lang['add_s_5'] ) $target = " target=\"_blank\"";
			else $target="";
			
			$buttons[] = "<a class=\"btn btn-sm bg-{$color} btn-raised position-left\" href=\"{$key}\"{$target}>{$value}</a>";
			
			$bc++;
			
			if($bc > 4) $bc = 1;
		}
	} elseif( $back ) {
		$buttons[] = "<a class=\"btn btn-sm bg-teal btn-raised position-left\" href=\"{$back}\">{$lang['func_msg']}</a>";
	}
	
	if(count($buttons) ) {
		$back = "<div class=\"panel-footer\"><div class=\"text-center\">".implode('', $buttons)."</div></div>";
	} else $back ="";
	
	
	if ($title == "error") $title = $lang['addnews_error'];
	
	echoheader( "<i class=\"fa fa-comment-o position-left\"></i><span class=\"text-semibold\">{$lang['header_box_title']}</span>", $title );

	if($type == "error") {
		$type = "alert-danger";
	} elseif ( $type == "warning" ) {
		$type = "alert-warning";
	} elseif ( $type == "success" ) {
		$type = "alert-success";
	} else $type = "alert-info";
	
	if( is_array( $title ) ) {
		$title = end($title);
	}

	echo <<<HTML
<div class="alert {$type} alert-styled-left alert-arrow-left alert-component message_box">
  <h4>{$title}</h4>
  <div class="panel-body">
		<table width="100%">
		    <tr>
		        <td height="80" class="text-center">{$text}</td>
		    </tr>
		</table>
	</div>
	{$back}
</div>
HTML;
	
	echofooter();
	die();
}

function echoheader($header_title, $header_subtitle) {
	global $skin_header, $skin_footer, $skin_not_logged_header, $member_id, $user_group, $js_array, $css_array, $config, $lang, $is_loged_in, $mod, $action, $langdate, $db, $dle_login_hash;

	if( !is_array( $header_subtitle )) $header_subtitle = array ( '' => $header_subtitle);
	
	$breadcrumb = array( "<li><a href=\"?mod=main\"><i class=\"fa fa-home position-left\"></i>{$lang['skin_main']}</a></li>" );

	foreach ($header_subtitle as $key => $value) {
		
		if($key) {
			$breadcrumb[] = "<li><a href=\"{$key}\">{$value}</a></li>";
		} else {
			$breadcrumb[] = "<li class=\"active\">{$value}</li>";
		}
	}

	$breadcrumb = implode('', $breadcrumb);

	include_once (DLEPlugins::Check(ENGINE_DIR . '/skins/default.skin.php'));
	
	$js = build_js($js_array);
	$css = build_css($css_array);
	
	$skin_header = str_replace( "{js_files}", $js, $skin_header );
	$skin_header = str_replace( "{css_files}", $css, $skin_header );
	$skin_not_logged_header = str_replace( "{js_files}", $js, $skin_not_logged_header );
	$skin_not_logged_header = str_replace( "{css_files}", $css, $skin_not_logged_header );
	
	if( $is_loged_in ) echo $skin_header;
	else echo $skin_not_logged_header;
}

function echofooter() {
	global $is_loged_in, $skin_footer, $skin_not_logged_footer;

	if( $is_loged_in ) echo $skin_footer;
	else echo $skin_not_logged_footer;

}

function listdir($dir) {
	
	$current_dir = @opendir( $dir );
	
	if($current_dir !== false ) {
		while ( $entryname = readdir( $current_dir ) ) {
			if( is_dir( $dir."/".$entryname ) AND ($entryname != "." AND $entryname != "..") ) {
				listdir( $dir."/".$entryname );
			} elseif( $entryname != "." AND $entryname != ".." ) {
				@unlink( $dir."/".$entryname );
			}
		}
		@closedir( $current_dir );
		@rmdir( $dir );
	}

}

function totranslit($var, $lower = true, $punkt = true) {
	global $langtranslit;
	
	if ( is_array($var) ) return "";

	$var = str_replace(chr(0), '', $var);
	
	$var = trim( strip_tags( $var ) );
	$var = preg_replace( "/\s+/u", "-", $var );
	$var = str_replace( "/", "-", $var );

	if (is_array($langtranslit) AND count($langtranslit) ) {
		$var = strtr($var, $langtranslit);
	}
	
	if ( $punkt ) $var = preg_replace( "/[^a-z0-9\_\-.]+/mi", "", $var );
	else $var = preg_replace( "/[^a-z0-9\_\-]+/mi", "", $var );

	$var = preg_replace( '#[\-]+#i', '-', $var );
	$var = preg_replace( '#[.]+#i', '.', $var );

	if ( $lower ) $var = strtolower( $var );

	$var = str_ireplace( ".php", "", $var );
	$var = str_ireplace( ".php", ".ppp", $var );
	
	if( strlen( $var ) > 200 ) {
		
		$var = substr( $var, 0, 200 );
		
		if( ($temp_max = strrpos( $var, '-' )) ) $var = substr( $var, 0, $temp_max );
	
	}
	
	return $var;
}

function langdate($format, $stamp, $servertime = false, $custom = false ) {
	global $langdate, $member_id, $customlangdate;

	$timezones = array('Pacific/Midway','US/Samoa','US/Hawaii','US/Alaska','US/Pacific','America/Tijuana','US/Arizona','US/Mountain','America/Chihuahua','America/Mazatlan','America/Mexico_City','America/Monterrey','US/Central','US/Eastern','US/East-Indiana','America/Lima','America/Caracas','Canada/Atlantic','America/La_Paz','America/Santiago','Canada/Newfoundland','America/Buenos_Aires','America/Godthab','Atlantic/Stanley','Atlantic/Azores','Africa/Casablanca','Europe/Dublin','Europe/Lisbon','Europe/London','Europe/Amsterdam','Europe/Belgrade','Europe/Berlin','Europe/Bratislava','Europe/Brussels','Europe/Budapest','Europe/Copenhagen','Europe/Madrid','Europe/Paris','Europe/Prague','Europe/Rome','Europe/Sarajevo','Europe/Stockholm','Europe/Vienna','Europe/Warsaw','Europe/Zagreb','Europe/Athens','Europe/Bucharest','Europe/Helsinki','Europe/Istanbul','Asia/Jerusalem','Europe/Kiev','Europe/Minsk','Europe/Riga','Europe/Sofia','Europe/Tallinn','Europe/Vilnius','Asia/Baghdad','Asia/Kuwait','Africa/Nairobi','Asia/Tehran','Europe/Kaliningrad','Europe/Moscow','Europe/Volgograd','Europe/Samara','Asia/Baku','Asia/Muscat','Asia/Tbilisi','Asia/Yerevan','Asia/Kabul','Asia/Yekaterinburg','Asia/Tashkent','Asia/Kolkata','Asia/Kathmandu','Asia/Almaty','Asia/Novosibirsk','Asia/Jakarta','Asia/Krasnoyarsk','Asia/Hong_Kong','Asia/Kuala_Lumpur','Asia/Singapore','Asia/Taipei','Asia/Ulaanbaatar','Asia/Urumqi','Asia/Irkutsk','Asia/Seoul','Asia/Tokyo','Australia/Adelaide','Australia/Darwin','Asia/Yakutsk','Australia/Brisbane','Pacific/Port_Moresby','Australia/Sydney','Asia/Vladivostok','Asia/Sakhalin','Asia/Magadan','Pacific/Auckland','Pacific/Fiji');

	if( is_array($custom) ) $locallangdate = $customlangdate; else $locallangdate = $langdate;

	if (!$stamp) { $stamp = time(); }
	
	$local = new DateTime('@'.$stamp);

	if (isset($member_id['timezone']) AND $member_id['timezone'] AND !$servertime) {
		$localzone = $member_id['timezone'];

	} else {

		$localzone = date_default_timezone_get();
	}

	if (!in_array($localzone, $timezones)) $localzone = 'Europe/Moscow';

	$local->setTimeZone(new DateTimeZone($localzone));

	return strtr( $local->format($format), $locallangdate );

}

function CategoryNewsSelection($categoryid = 0, $parentid = 0, $nocat = TRUE, $sublevelmarker = '', $returnstring = '') {
	global $cat_info, $member_id, $user_group, $mod;
	
	if ($mod == "addnews" OR $mod == "editnews") {
		
		if($member_id['cat_allow_addnews']) {
			$allow_list = explode( ',', $member_id['cat_allow_addnews'] );
		} else $allow_list = explode( ',', $user_group[$member_id['user_group']]['cat_allow_addnews'] );
		
	} else {
		
		$allow_list = explode( ',', $user_group[$member_id['user_group']]['allow_cats'] );
		
	}
	
	if( $mod != "usergroup" AND $mod != "editusers") {
		
		$not_allow_list = explode( ',', $user_group[$member_id['user_group']]['not_allow_cats'] );
		
	} else $not_allow_list = array();
	
	if( $parentid == 0 ) {
		if( $nocat ) $returnstring .= '<option value="0"></option>';
	} else {
		$sublevelmarker .= '&nbsp;&nbsp;&nbsp;&nbsp;';
	}
	
	if( count( $cat_info ) ) {
		
		$root_category = array();
		
		foreach ( $cat_info as $cats ) {
			if( $cats['parentid'] == $parentid ) $root_category[] = $cats['id'];
		}

		if( count( $root_category ) ) {
			
			foreach ( $root_category as $id ) {
				
				if( ( $allow_list[0] == "all" OR in_array( $id, $allow_list ) ) OR $mod == "usergroup" OR $mod == "editusers" ) {
					
					if( in_array( $id, $not_allow_list ) ) continue;
					
					$returnstring .= "<option value=\"" . $id . '" ';
					
					if( is_array( $categoryid ) ) {
						foreach ( $categoryid as $element ) {
							if( $element == $id ) $returnstring .= 'selected';
						}
					} elseif( $categoryid == $id ) $returnstring .= 'selected';
					
					$returnstring .= '>' . $sublevelmarker . $cat_info[$id]['name'] . '</option>';
				}
				
				$returnstring = CategoryNewsSelection( $categoryid, $id, $nocat, $sublevelmarker, $returnstring );
			}
		}
	}
	
	return $returnstring;
}


function array_selection($array_list, $selid = 0, $parentid = 0, $sublevelmarker = '', $returnstring = '') {

	$root_category = array ();
	
	if( $parentid == 0 ) {
		$returnstring .= '<option value="0"></option>';
	} else {
		$sublevelmarker .= '&nbsp;&nbsp;&nbsp;';
	}
	
	if( count( $array_list ) ) {
		
		foreach ( $array_list as $list ) {
			if( $list['parentid'] == $parentid ) $root_category[] = $list['id'];
		}
		
		if( count( $root_category ) ) {
			foreach ( $root_category as $id ) {
					
				$returnstring .= "<option value=\"" . $id . '" ';
					
				if( is_array( $selid ) ) {
					foreach ( $selid as $element ) {
						
						$element = intval($element);
						
						if( $element == $id ) $returnstring .= 'selected';
						
					}
				} elseif( intval($selid) == $id ) $returnstring .= 'selected';
					
				$returnstring .= '>' . $sublevelmarker . $array_list[$id]['title'] . '</option>';
				
				$returnstring = array_selection($array_list, $selid, $id, $sublevelmarker, $returnstring );
			}
		}
	}
	
	return $returnstring;
}

$mcache = false;

if ( $config['cache_type'] ) {

	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/memcache.class.php'));
	$mcache = new dle_memcache($config);

}

function clear_cache($cache_areas = false) {
	global $mcache, $config;

	if( $config['cache_type'] ) {
		if( $mcache->connection > 0 ) {
			$mcache->clear( $cache_areas );
			return true;
		}
	}

	if ( $cache_areas ) {
		if(!is_array($cache_areas)) {
			$cache_areas = array($cache_areas);
		}
	}
		
	$fdir = opendir( ENGINE_DIR . '/cache' );
		
	while ( $file = readdir( $fdir ) ) {
		if( $file != '.htaccess' AND !is_dir(ENGINE_DIR . '/cache/' . $file) ) {
			
			if( $cache_areas ) {
				
				foreach($cache_areas as $cache_area) if( stripos( $file, $cache_area ) === 0 ) @unlink( ENGINE_DIR . '/cache/' . $file );
			
			} else {
				
				@unlink( ENGINE_DIR . '/cache/' . $file );
			
			}
		}
	}
}

function clear_all_caches() {
	global $config;
	
	listdir( ENGINE_DIR . '/cache/system/CSS' );
	listdir( ENGINE_DIR . '/cache/system/HTML' );
	listdir( ENGINE_DIR . '/cache/system/URI' );
	listdir( ENGINE_DIR . '/cache/system/plugins' );
	
	$fdir = opendir( ENGINE_DIR . '/cache/system/' );
	while ( $file = readdir( $fdir ) ) {
		if( $file != '.' AND $file != '..' AND $file != '.htaccess' AND $file != 'cron.php' ) {
			@unlink( ENGINE_DIR . '/cache/system/' . $file );
		
		}
	}
	
	if( $config['cache_type'] ) {
		$fdir = opendir( ENGINE_DIR . '/cache' );
		while ( $file = readdir( $fdir ) ) {
			if( $file != '.htaccess' AND !is_dir($file) ) {
					@unlink( ENGINE_DIR . '/cache/' . $file );
			}
		}
	}
	
	clear_cache();
	
	if (function_exists('opcache_reset')) {
		opcache_reset();
	}
	
}

function xfieldsdataload($id) {
	
	if( $id == "" ) return;
	
	$xfieldsdata = explode( "||", $id );
	foreach ( $xfieldsdata as $xfielddata ) {
		list ( $xfielddataname, $xfielddatavalue ) = explode( "|", $xfielddata );
		$xfielddataname = str_replace( "&#124;", "|", $xfielddataname );
		$xfielddataname = str_replace( "__NEWL__", "\r\n", $xfielddataname );
		$xfielddatavalue = str_replace( "&#124;", "|", $xfielddatavalue );
		$xfielddatavalue = str_replace( "__NEWL__", "\r\n", $xfielddatavalue );
		$data[$xfielddataname] = $xfielddatavalue;
	}
	
	return $data;
}

function xfieldsload() {
	global $lang, $config;
	
	$path = ENGINE_DIR . '/data/xfields.txt';
	$filecontents = file( $path );
	$fields = array();
	$tmp_arr = array();

	if( !is_array( $filecontents ) ) {
		
		return array();
	
	} elseif( count($filecontents) ) {
		
		foreach ( $filecontents as $name => $value ) {
			
			if( trim($value) ) {
				
				$tmp_arr = explode( "|", trim($value, "\t\n\r\0\x0B") );
				
				foreach ( $tmp_arr as $name2 => $value2 ) {
					$value2 = str_replace( "&#124;", "|", $value2 );
					$value2 = str_replace( "__NEWL__", "\r\n", $value2 );
					$value2 = html_entity_decode($value2, ENT_QUOTES, $config['charset']);
					$fields[$name][$name2] = $value2;
				}
				
			}
		}

		return $fields;

	}
	
	return array();
}

function create_metatags($story, $ajax = false) {
	global $config, $db;
	
	$keyword_count = 20;
	$newarr = array ();
	$headers = array ();
	$quotes = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", "\\", ",", ".", "/", "#", ";", ":", "@", "~", "[", "]", "{", "}", "=", "-", "+", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"');
	$fastquotes = array ("\x22", "\x60", "\t", "\n", "\r", '"', '\r', '\n', "$", "{", "}", "[", "]", "<", ">", "\\");

	$story = preg_replace( "#\[hide(.*?)\](.+?)\[/hide\]#is", "", $story );
	$story = preg_replace( "'\[attachment=(.*?)\]'si", "", $story );
	$story = preg_replace( "'\[page=(.*?)\](.*?)\[/page\]'si", "", $story );
	$story = preg_replace( "'{banner_(.*?)}'si", "", $story );
	$story = preg_replace( "'\\[banner_(.*?)\\](.*?)\\[/banner_(.*?)\\]'si", "", $story );
	$story = str_replace( "{PAGEBREAK}", "", $story );
	$story = str_replace( "&nbsp;", " ", $story );
	$story = str_replace( "&#1072;", "a", $story );
	$story = str_replace( "&#111;", "o", $story );
	$story = str_replace( '<br />', ' ', $story );
	$story = str_replace( '<br>', ' ', $story );
	$story = preg_replace( "#&(.+?);#", "", $story );
	$story = str_replace( " ,", "", $story );
	$story = trim(preg_replace('/\s+/u', ' ', $story));
	
	$story = strip_tags( $story );
 
	if( trim( $_REQUEST['meta_title'] ) ) {

		$headers['title'] = trim( htmlspecialchars( strip_tags( stripslashes($_REQUEST['meta_title'] ) ), ENT_COMPAT, $config['charset'] ) );
		$headers['title'] = $db->safesql(preg_replace('/\s+/u', ' ', str_replace( $fastquotes, '', $headers['title'] )));

	} else $headers['title'] = "";
	
	if( trim( $_REQUEST['descr'] ) ) {

		$headers['description'] = trim(strip_tags( stripslashes( $_REQUEST['descr'] ) ) );

		if( dle_strlen( $headers['description'], $config['charset'] ) > 300 ) {
			
			$headers['description'] = dle_substr( $headers['description'], 0, 300, $config['charset'] );
			
			if( ($temp_dmax = dle_strrpos( $headers['description'], ' ', $config['charset'] )) ) $headers['description'] = dle_substr( $headers['description'], 0, $temp_dmax, $config['charset'] );

		}
		
		$headers['description'] = $db->safesql( preg_replace('/\s+/u', ' ', str_replace( $fastquotes, '', $headers['description'] )));
	
	} elseif($config['create_metatags'] OR $ajax) {
		
		$story = str_replace( $fastquotes, '', $story );

		$headers['description'] = stripslashes($story);
		
		if( dle_strlen( $headers['description'], $config['charset'] ) > 300 ) {
			
			$headers['description'] = dle_substr( $headers['description'], 0, 300, $config['charset'] );
			
			if( ($temp_dmax = dle_strrpos( $headers['description'], ' ', $config['charset'] )) ) $headers['description'] = dle_substr( $headers['description'], 0, $temp_dmax, $config['charset'] );

		}
		
		$headers['description'] = $db->safesql( $headers['description'] );

	} else {

		$headers['description'] = '';

	}
	
	if( trim( $_REQUEST['keywords'] ) ) {
		
		$arr = explode( ",", $_REQUEST['keywords'] );
		$newarr = array();

		foreach ( $arr as $word ) {
			$newarr[] = trim($word);
		}

		$_REQUEST['keywords'] = implode( ", ", $newarr );
		
		$headers['keywords'] = $db->safesql( preg_replace('/\s+/u', ' ', str_replace( $fastquotes, " ", strip_tags( stripslashes( $_REQUEST['keywords'] ) ) ) ) );

	} elseif( $config['create_metatags'] OR $ajax) {
		
		$story = str_replace( $quotes, ' ', $story );
		
		$arr = explode( " ", $story );
		
		foreach ( $arr as $word ) {
			if( dle_strlen( $word, $config['charset'] ) > 4 ) $newarr[] = $word;
		}
		
		$arr = array_count_values( $newarr );
		arsort( $arr );
		
		$arr = array_keys( $arr );
		
		$total = count( $arr );
		
		$offset = 0;
		
		$arr = array_slice( $arr, $offset, $keyword_count );
		
		$headers['keywords'] = $db->safesql( implode( ", ", $arr ) );
	} else {

		$headers['keywords'] = '';

	}
	
	return $headers;
}

function set_vars($file, $data) {

	$file = totranslit($file, true, false);

	if ( is_array($data) OR is_int($data) ) {
		
		file_put_contents (ENGINE_DIR . '/cache/system/' . $file . '.php', json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), LOCK_EX);
		@chmod( ENGINE_DIR . '/cache/system/' . $file . '.php', 0666 );

	}

}

function get_vars($file) {
	$file = totranslit($file, true, false);

	$data = @file_get_contents( ENGINE_DIR . '/cache/system/' . $file . '.php' );

	if ( $data !== false ) {

		$data = json_decode( $data, true );
		if ( is_array($data) OR is_int($data) ) return $data;

	} 

	return false;

}
function get_groups($id = false) {
	global $user_group;
	
	$returnstring = "";
	
	foreach ( $user_group as $group ) {
		$returnstring .= '<option value="' . $group['id'] . '" ';
		
		if( is_array( $id ) ) {
			foreach ( $id as $element ) {
				if( $element == $group['id'] ) $returnstring .= 'SELECTED';
			}
		} elseif( $id and $id == $group['id'] ) $returnstring .= 'SELECTED';
		
		$returnstring .= ">" . $group['group_name'] . "</option>\n";
	}
	
	return $returnstring;

}
function permload($id) {
	
	if( $id == "" ) return;
	
	$data = array ();
	
	$groups = explode( "|", $id );
	foreach ( $groups as $group ) {
		list ( $groupid, $groupvalue ) = explode( ":", $group );
		$data[$groupid][1] = ($groupvalue == 1) ? "selected" : "";
		$data[$groupid][2] = ($groupvalue == 2) ? "selected" : "";
		$data[$groupid][3] = ($groupvalue == 3) ? "selected" : "";
	}
	return $data;
}

function check_xss() {

	if ($_GET['mod'] == "editnews" AND $_GET['action'] == "list") return;
	if ($_GET['mod'] == "static" AND $_GET['action'] == "list") return;
	if ($_GET['mod'] == "tagscloud" OR $_GET['mod'] == "links" OR $_GET['mod'] == "redirects"  OR $_GET['mod'] == "metatags") return;
	
	$url = html_entity_decode( urldecode( $_SERVER['QUERY_STRING'] ), ENT_QUOTES, 'ISO-8859-1' );

	$url = str_replace( "\\", "/", $url );

	if( $url ) {
		
		if( (strpos( $url, '<' ) !== false) || (strpos( $url, '>' ) !== false) || (strpos( $url, '"' ) !== false) || (strpos( $url, './' ) !== false) || (strpos( $url, '../' ) !== false) || (strpos( $url, '\'' ) !== false) || (strpos( $url, '.php' ) !== false) ) {

			header( "HTTP/1.1 403 Forbidden" );
			die( "Hacking attempt!" );
		
		}
	
	}
	
	$url = html_entity_decode( urldecode( $_SERVER['REQUEST_URI'] ), ENT_QUOTES, 'ISO-8859-1' );
	$url = str_replace( "\\", "/", $url );
	
	if( $url ) {
		
		if( (strpos( $url, '<' ) !== false) || (strpos( $url, '>' ) !== false) || (strpos( $url, '"' ) !== false) || (strpos( $url, '\'' ) !== false) ) {
			header( "HTTP/1.1 403 Forbidden" );
			die( "Hacking attempt!" );
		
		}
	
	}

}

function clean_url($url) {
	
	if( $url == '' ) return;
	
	$url = str_replace( "http://", "", $url );
	$url = str_replace( "https://", "", $url );
	if( strtolower( substr( $url, 0, 4 ) ) == 'www.' ) $url = substr( $url, 4 );
	$url = explode( '/', $url );
	$url = reset( $url );
	$url = explode( ':', $url );
	$url = reset( $url );
	
	return $url;
}

function get_url($id) {
	
	global $cat_info;
	
	if( ! $id ) return;
	
	$parent_id = $cat_info[$id]['parentid'];
	
	$url = $cat_info[$id]['alt_name'];
	
	while ( $parent_id ) {
		
		$url = $cat_info[$parent_id]['alt_name'] . "/" . $url;
		
		$parent_id = $cat_info[$parent_id]['parentid'];
		
		if( $cat_info[$parent_id]['parentid'] == $cat_info[$parent_id]['id'] ) break;
	
	}
	
	return $url;
}

function convert_unicode($t, $to = '') {
// deprecated
	return $t;
}

function check_netz($ip1, $ip2) {
	
	if( strpos($ip1, ":") === false ) {
		$delimiter = ".";
	} else $delimiter = ":";
	
	$ip1 = explode( $delimiter, $ip1 );
	$ip2 = explode( $delimiter, $ip2 );
	
	if( $ip1[0] != $ip2[0] ) return false;
	if( $ip1[1] != $ip2[1] ) return false;
	
	if($delimiter == ":") {
		if( $ip1[2] != $ip2[2] ) return false;
		if( $ip1[3] != $ip2[3] ) return false;
	}
	
	return true;

}

function compare_filter($a, $b) {
	
	$a = explode( "|", $a );
	$b = explode( "|", $b );
	
	if( $a[1] == $b[1] ) return 0;
	
	return strcasecmp( $a[1], $b[1] );

}

function auth() {
	header( 'WWW-Authenticate: Basic realm="Admin Area"' );
	header( 'HTTP/1.0 401 Unauthorized' );
	echo "<H1>Access Denied</H1>";
	exit();
}

function build_js($js) {
	global $config;

	$js_array = array();
	$i=0;
	$defer = "";
	$v = substr(md5(DINITVERSION.SECURE_AUTH_KEY),0,5);
	
	if ($config['js_min']) {

		$js_array[] = "<script src=\"engine/classes/min/index.php?charset={$config['charset']}&amp;g=admin&amp;v={$v}\"></script>";

		if ( count($js) ) $js_array[] = "<script src=\"engine/classes/min/index.php?charset={$config['charset']}&amp;f=".implode(",", $js)."&amp;v={$v}\" defer></script>";

		return implode("\n", $js_array);

	} else {

		$default_array = array (
			'engine/skins/javascripts/application.js',
		);

		if ( count($js) ) $js = array_merge($default_array, $js); else $js = $default_array;

		foreach ($js as $value) {
			
			if($i > 0) $defer =" defer";
			
			$js_array[] = "<script src=\"{$value}?v={$v}\"{$defer}></script>";
			
			$i++;
		
		}

		return implode("\n", $js_array);
	}

}


function build_css($css) {
	global $config;

	$default_array = array (
		'engine/skins/fonts/fontawesome/styles.min.css',
		'engine/skins/stylesheets/application.css'
	);
	
	$css_array = array();
	$v = substr(md5(DINITVERSION.SECURE_AUTH_KEY),0,5);

	if ( count($css) ) $css = array_merge($default_array, $css); else $css = $default_array;

	if ($config['js_min']) {

		return "<link href=\"engine/classes/min/index.php?charset={$config['charset']}&amp;f=".implode(",", $css)."&amp;v={$v}\" rel=\"stylesheet\" type=\"text/css\">";

	} else {

		foreach ($css as $value) {
		
			$css_array[] = "<link href=\"{$value}?v={$v}\" rel=\"stylesheet\" type=\"text/css\">";
		
		}

		return implode("\n", $css_array);
	}

}

function dle_strlen($value, $charset = "utf-8" ) {

	if( function_exists( 'mb_strlen' ) ) {
		return mb_strlen( $value, $charset );
	} elseif( function_exists( 'iconv_strlen' ) ) {
		return iconv_strlen($value, $charset);
	}

	return strlen($value);
}

function dle_substr($str, $start, $length, $charset = "utf-8" ) {

	if( function_exists( 'mb_substr' ) ) {
		return mb_substr( $str, $start, $length, $charset );
	
	} elseif( function_exists( 'iconv_substr' ) ) {
		return iconv_substr($str, $start, $length, $charset);
	}

	return substr($str, $start, $length);

}

function dle_strrpos($str, $needle, $charset = "utf-8" ) {

	if( function_exists( 'mb_strrpos' ) ) {
		return mb_strrpos( $str, $needle, null, $charset );
	
	} elseif( function_exists( 'iconv_strrpos' ) ) {
		return iconv_strrpos($str, $needle, $charset);
	}

	return strrpos($str, $needle);

}

function dle_strpos($str, $needle, $charset = "utf-8" ) {

	if( function_exists( 'mb_strpos' ) ) {
		return mb_strpos( $str, $needle, null, $charset );
	} elseif( function_exists( 'iconv_strrpos' ) ) {
		return iconv_strpos($str, $needle, null, $charset);
	}

	return strpos($str, $needle);

}

function dle_strtolower($str, $charset = "utf-8" ) {

	if( function_exists( 'mb_strtolower' ) ) {
		return mb_strtolower( $str, $charset );
	}

	return strtolower($str);

}

function check_allow_login($ip, $max ) {
	global $db, $config;

	$config['login_ban_timeout'] = intval($config['login_ban_timeout']);
	
	$max = intval($max);
	
	if( $max < 2 ) $max = 2;
	
	$block_date = time()-($config['login_ban_timeout'] * 60);

	$row = $db->super_query( "SELECT * FROM " . PREFIX . "_login_log WHERE ip='{$ip}'" );

	if ( $row['count'] AND $row['date'] < $block_date ) {
		$db->query( "DELETE FROM " . PREFIX . "_login_log WHERE ip = '{$ip}'" );
		return true;
	}

	if ($row['count'] >= $max AND $row['date'] > $block_date ) return false;
	else return true;

}

function detect_encoding($string) {  
  static $list = array('utf-8', 'windows-1251');
   
  foreach ($list as $item) {

	if( function_exists( 'mb_convert_encoding' ) ) {

		$sample = mb_convert_encoding( $string, $item, $item );

	} elseif( function_exists( 'iconv' ) ) {
	
		$sample = iconv($item, $item, $string);
	
	}

	if (md5($sample) == md5($string)) return $item;
   }

   return null;
}

function get_ip() {
	global $config;
	
	if ($config['own_ip']) $ip = $_SERVER[$config['own_ip']]; else $ip = $_SERVER['REMOTE_ADDR'];

	$temp_ip = explode(",", $ip);

	if(count($temp_ip) > 1) $ip = trim($temp_ip[0]);

	if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
		return filter_var( $ip , FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
	}

	if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
		return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	}

	return 'not detected';
}

function http_get_contents( $file, $post_params = false ) {
		
	$data = false;

	if (stripos($file, "http://") !== 0 AND stripos($file, "https://") !== 0) {
		return false;
	}
		
	if( function_exists( 'curl_init' ) ) {
			
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $file );

		if( is_array($post_params) ) {

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_params));

		}
		
		@curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_TIMEOUT, 5 );
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			
		$data = curl_exec( $ch );
		curl_close( $ch );

		if( $data !== false ) return $data;
		
	} 

	if( preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen')) ) {

		if( is_array($post_params) ) {

			$file .= '?'.http_build_query($post_params);
		}

		$data = @file_get_contents( $file );
			
		if( $data !== false ) return $data;

	}

	return false;	
}

function cleanpath($path) {
	$path = trim(str_replace(chr(0), '', (string)$path));
	$path = str_replace(array('/', '\\'), '/', $path);
	$parts = array_filter(explode('/', $path), 'strlen');
	$absolutes = array();
	foreach ($parts as $part) {
		if ('.' == $part) continue;
		if ('..' == $part) {
			array_pop($absolutes);
		} else {
			$absolutes[] = totranslit($part, false, false);
		}
	}

	return implode('/', $absolutes);
}

function is_md5hash( $md5 = '' ) {
  return strlen($md5) == 32 && ctype_xdigit($md5);
}

function generate_pin(){
	
	$pin = "";
	
	for($i = 0; $i < 5; $i ++) {
		$rand = "";
	
		if(function_exists('openssl_random_pseudo_bytes')) {
			 do{
				 $rand = floor(10*(hexdec(bin2hex(openssl_random_pseudo_bytes(4)))/0xffffffff));
			 }while($rand == 10);
		} else {
	
			$rand = mt_rand( 0, 9 );
		}
		
		$pin .= $rand;
	}
	
    return $pin;
}

function normalize_name($var, $punkt = true) {
	
	if ( !is_string($var) ) return;

	$var = str_replace(chr(0), '', $var);
	
	$var = trim( strip_tags( $var ) );
	$var = preg_replace( "/\s+/u", "-", $var );
	$var = str_replace( "/", "-", $var );
	
	if ( $punkt ) $var = preg_replace( "/[^a-z0-9\_\-.]+/mi", "", $var );
	else $var = preg_replace( "/[^a-z0-9\_\-]+/mi", "", $var );

	$var = preg_replace( '#[\-]+#i', '-', $var );
	$var = preg_replace( '#[.]+#i', '.', $var );
	
	return $var;
}

function clearfilepath( $file, $ext=array() ) {

	$file = trim(str_replace(chr(0), '', (string)$file));
	$file = str_replace(array('/', '\\'), '/', $file);
	
	$path_parts = pathinfo( $file );

	if( count($ext) ) {
		if ( !in_array( $path_parts['extension'], $ext ) ) return '';
	}
	
	$filename = normalize_name($path_parts['basename'], true);
	
	if( !$filename) return '';
	
	$parts = array_filter(explode('/', $path_parts['dirname']), 'strlen');
	
	$absolutes = array();
	
	foreach ($parts as $part) {
		if ('.' == $part) continue;
		if ('..' == $part) {
			array_pop($absolutes);
		} else {
			$absolutes[] = normalize_name($part, false);
		}
	}

	$path = implode('/', $absolutes);
	
	if ( $path ) return implode('/', $absolutes).'/'.$filename;
	else return '';

}

function execute_query($id, $query) {
	global $config, $db;

	if(!$query) return;
	
	if( version_compare($db->mysql_version, '5.6.4', '<') ) {
		$storage_engine = "MyISAM";
	} else $storage_engine = "InnoDB";
	
	$query = str_ireplace(array("{prefix}", "{userprefix}", "{charset}", "{engine}"), array(PREFIX, USERPREFIX, COLLATE, $storage_engine), $query);

	$db->query_errors_list = array();
		
	$db->multi_query( trim($query), false );
	
	$id = intval($id);

	if( count($db->query_errors_list) ){

		foreach($db->query_errors_list as $error) {
			$db->query( "INSERT INTO " . PREFIX . "_plugins_logs (plugin_id, area, error, type) values ('{$id}', '".$db->safesql( htmlspecialchars( $error['query'], ENT_QUOTES, $config['charset'] ), false)."', '".$db->safesql( htmlspecialchars( $error['error'], ENT_QUOTES, $config['charset'] ) )."', 'mysql')" );
		}
		
	}
	
	$db->query_errors_list = array();
	
}

function check_referer( $current_path ) {
	
	if( !$_SERVER['HTTP_REFERER'] ) return false;
	
	$ref = parse_url($_SERVER['HTTP_REFERER']);
	$ref['host'] = clean_url($ref['host']);
	$ref['path'] = basename($ref['path']);
	
	$curr = parse_url($current_path);
	$curr['host'] = clean_url($_SERVER['HTTP_HOST']);
	$curr['path'] = basename($curr['path']);
	
	if( $ref['path'] AND $curr['path'] AND $ref['host'] AND $curr['host'] AND $ref['path'] == $curr['path'] AND $ref['host'] == $curr['host'] ) {
		if( strpos($ref['query'], $curr['query']) !== false) {
			return true;
		}
	}
	
	return false;
	
}

function isSSL() {
    if( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == 443)
        || (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https')
		|| (isset($_SERVER['CF_VISITOR']) && $_SERVER['CF_VISITOR'] == '{"scheme":"https"}')
		|| (isset($_SERVER['HTTP_CF_VISITOR']) && $_SERVER['HTTP_CF_VISITOR'] == '{"scheme":"https"}')
    ) return true; else return false;
}

if (!defined('PASSWORD_BCRYPT')) {

    define('PASSWORD_BCRYPT', 1);
    define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);
    define('PASSWORD_BCRYPT_DEFAULT_COST', 10);
	
}

if (!function_exists('password_hash')) {

    function password_hash($password, $algo, array $options = array()) {
        if (!function_exists('crypt')) {
            die("Crypt must be loaded for password_hash to function");
        }
		
        $password = (string) $password;

        if (!is_int($algo)) {
            trigger_error("password_hash() expects parameter 2 to be long, " . gettype($algo) . " given", E_USER_WARNING);
            return null;
        }
        $resultLength = 0;
			
        switch ($algo) {
            case PASSWORD_BCRYPT:
                $cost = PASSWORD_BCRYPT_DEFAULT_COST;

                $raw_salt_len = 16;
                $required_salt_len = 22;
                $hash_format = sprintf("$2y$%02d$", $cost);
                $resultLength = 60;
                break;
             default:
                  trigger_error(sprintf("password_hash(): Unknown password hashing algorithm: %s", $algo), E_USER_WARNING);
                 return null;
        }
			
        $salt_req_encoding = false;

        $buffer = '';
        $buffer_valid = false;
		
        if (function_exists('mcrypt_create_iv')) {
            $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
            if ($buffer) {
                $buffer_valid = true;
            }
        }
		
        if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
            $strong = false;
            $buffer = openssl_random_pseudo_bytes($raw_salt_len, $strong);
            if ($buffer && $strong) {
                $buffer_valid = true;
            }
        }
		
        if (!$buffer_valid && @is_readable('/dev/urandom')) {
            $file = fopen('/dev/urandom', 'r');
            $read = 0;
            $local_buffer = '';
            while ($read < $raw_salt_len) {
                $local_buffer .= fread($file, $raw_salt_len - $read);
                $read = strlen_8bit($local_buffer);
            }
            fclose($file);
            if ($read >= $raw_salt_len) {
                $buffer_valid = true;
            }
            $buffer = str_pad($buffer, $raw_salt_len, "\0") ^ str_pad($local_buffer, $raw_salt_len, "\0");
        }
				
        if (!$buffer_valid || strlen_8bit($buffer) < $raw_salt_len) {
            $buffer_length = strlen_8bit($buffer);
            for ($i = 0; $i < $raw_salt_len; $i++) {
                if ($i < $buffer_length) {
                    $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                } else {
                    $buffer .= chr(mt_rand(0, 255));
                }
            }
        }
		
        $salt = $buffer;
        $salt_req_encoding = true;

			
        if ($salt_req_encoding) {
            $base64_digits = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
            $bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $base64_string = base64_encode($salt);
            $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);
        }
			
        $salt = substr_8bit($salt, 0, $required_salt_len);
        $hash = $hash_format . $salt;
        $ret = crypt($password, $hash);
		
        if (!is_string($ret) || strlen_8bit($ret) != $resultLength) {
            return false;
        }
            return $ret;
    }
    function password_get_info($hash) {
        $return = array(
            'algo' => 0,
            'algoName' => 'unknown',
            'options' => array(),
        );
		
        if (substr_8bit($hash, 0, 4) == '$2y$' && strlen_8bit($hash) == 60) {
            $return['algo'] = PASSWORD_BCRYPT;
            $return['algoName'] = 'bcrypt';
            list($cost) = sscanf($hash, "$2y$%d$");
            $return['options']['cost'] = $cost;
        }
		
        return $return;
    }
		
    function password_needs_rehash($hash, $algo) {
		
        $info = password_get_info($hash);
		
        if ($info['algo'] !== (int) $algo) {
            return true;
        }
		
        switch ($algo) {
            case PASSWORD_BCRYPT:
                $cost = PASSWORD_BCRYPT_DEFAULT_COST;
                if ($cost !== $info['options']['cost']) {
                    return true;
                }
            break;
        }
		
        return false;
		
    }

    function password_verify($password, $hash) {
        if (!function_exists('crypt')) {
            die("Crypt must be loaded for password_hash to function");
        }
		
        $ret = crypt($password, $hash);
		
        if (!is_string($ret) || strlen_8bit($ret) != strlen_8bit($hash) || strlen_8bit($ret) <= 13) {
            return false;
        }
		
        $status = 0;
        for ($i = 0; $i < strlen_8bit($ret); $i++) {
            $status |= (ord($ret[$i]) ^ ord($hash[$i]));
        }
			
        return $status === 0;
    }
	
    function strlen_8bit($binary_string) {
        if (function_exists('mb_strlen')) {
            return mb_strlen($binary_string, '8bit');
        }
        return strlen($binary_string);
    }
	
    function substr_8bit($binary_string, $start, $length) {
        if (function_exists('mb_substr')) {
            return mb_substr($binary_string, $start, $length, '8bit');
        }
        return substr($binary_string, $start, $length);
    }

}

?>