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
 File: cron.php
-----------------------------------------------------
 Use: Performing automatic operations
=====================================================
*/
if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

set_vars( "cron", $_TIME );

if( $cron == 1 ) {
	$db->query( "DELETE FROM " . PREFIX . "_spam_log WHERE is_spammer = '0'" );
}

if( $config['cache_count'] ) {
	$result = $db->query( "SELECT COUNT(*) as count, news_id FROM " . PREFIX . "_views GROUP BY news_id" );
	
	while ( $row = $db->get_array( $result ) ) {
		
		$db->query( "UPDATE " . PREFIX . "_post_extras SET news_read=news_read+{$row['count']} WHERE news_id='{$row['news_id']}'" );
	
	}
	
	$db->free( $result );
	$db->query( "TRUNCATE TABLE " . PREFIX . "_views" );

	clear_cache( array('news_', 'full_', 'rss') );

}

if( $cron == 2 ) {
	
	$db->query( "TRUNCATE TABLE " . PREFIX . "_login_log" );
	$db->query( "TRUNCATE TABLE " . PREFIX . "_flood" );
	$db->query( "TRUNCATE TABLE " . PREFIX . "_mail_log" );
	$db->query( "TRUNCATE TABLE " . PREFIX . "_read_log" );
	$db->query( "TRUNCATE TABLE " . PREFIX . "_spam_log" );
	$db->query( "TRUNCATE TABLE " . PREFIX . "_banners_logs" );

	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_lostdb" );
	
	if($row['count'] > 3 ) {
		$row['count'] = $row['count'] - 3;
		$db->query( "DELETE FROM " . PREFIX . "_lostdb ORDER BY id LIMIT {$row['count']}" );
	}
	
	$db->query( "DELETE FROM " . USERPREFIX . "_banned WHERE days != '0' AND date < '$_TIME' AND users_id = '0'" );
	@unlink( ENGINE_DIR . '/cache/system/banned.php' );
	
	$sql_cron = $db->query( "SELECT * FROM " . PREFIX . "_post_log WHERE expires <= '" . $_TIME . "'" );
	
	while ( $row = $db->get_row( $sql_cron ) ) {

		if ( $row['action'] == 2 ) {

			$db->query( "UPDATE " . PREFIX . "_post SET approve='0' WHERE id='{$row['news_id']}'" );
	
		} elseif ( $row['action'] == 3 ) {

			$db->query( "UPDATE " . PREFIX . "_post SET allow_main='0' WHERE id='{$row['news_id']}'" );

		} elseif ( $row['action'] == 4 ) {

			$db->query( "UPDATE " . PREFIX . "_post SET fixed='0' WHERE id='{$row['news_id']}'" );
			
		} elseif ( $row['action'] == 5 ) {

			$db->query( "UPDATE " . PREFIX . "_post SET category='{$row['move_cat']}' WHERE id='{$row['news_id']}'" );

			$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$row['news_id']}'" );

			if( $row['move_cat'] ) {

				$cat_ids = array ();

				$cat_ids_arr = explode( ",", $row['move_cat'] );

				foreach ( $cat_ids_arr as $value ) {

					$cat_ids[] = "('" . $row['news_id'] . "', '" . trim( $value ) . "')";
				}

				$cat_ids = implode( ", ", $cat_ids );
				$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );

			}
		
		} elseif ( $row['action'] == 1 ) {

			$row_title = $db->super_query( "SELECT title  FROM " . PREFIX . "_post WHERE id='{$row['news_id']}'" );
			$row_title = $db->safesql( $row_title['title'] );

			$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('dle_cron_auto', '{$_TIME}', 'localhost', '96', '{$row_title}')" );

			deletenewsbyid( $row['news_id'] );

		}
	
	}
	
	$db->query( "DELETE FROM " . PREFIX . "_post_log WHERE expires <= '" . $_TIME . "'" );
	
	$db->free( $sql_cron );
	
	if( intval( $config['max_users_day'] ) ) {
		$thisdate = $_TIME - ($config['max_users_day'] * 3600 * 24);
		
		$sql_result = $db->query( "SELECT name, user_id, foto FROM " . USERPREFIX . "_users WHERE lastdate < '$thisdate' AND user_group = '4'" );
		
		while ( $row = $db->get_row( $sql_result ) ) {

			$db->query( "DELETE FROM " . USERPREFIX . "_pm WHERE user_from = '{$row['name']}' AND folder = 'outbox'" );
			$db->query( "DELETE FROM " . USERPREFIX . "_pm WHERE user='{$row['user_id']}'" );
			$db->query( "DELETE FROM " . USERPREFIX . "_banned WHERE users_id='{$row['user_id']}'" );
			$db->query( "DELETE FROM " . USERPREFIX . "_users WHERE user_id = '{$row['user_id']}'" );
			$db->query( "DELETE FROM " . USERPREFIX . "_social_login WHERE uid='{$row['user_id']}'" );
			$db->query( "DELETE FROM " . USERPREFIX . "_ignore_list WHERE user='{$row['user_id']}' OR user_from='{$row['name']}'" );
			$db->query( "DELETE FROM " . PREFIX . "_logs WHERE `member` = '{$row['name']}'" );
			$db->query( "DELETE FROM " . PREFIX . "_comment_rating_log WHERE `member` = '{$row['name']}'" );
			$db->query( "DELETE FROM " . PREFIX . "_vote_result WHERE name = '{$row['name']}'" );
			$db->query( "DELETE FROM " . PREFIX . "_poll_log WHERE `member` = '{$row['user_id']}'" );
			$db->query( "DELETE FROM " . PREFIX . "_notice WHERE user_id = '{$row['user_id']}'" );
			$db->query( "DELETE FROM " . PREFIX . "_subscribe WHERE user_id='{$row['user_id']}'");

			$url = @parse_url ( $row['foto'] );
			$row['foto'] = basename($url['path']);
			
			@unlink( ROOT_DIR . "/uploads/fotos/" . totranslit($row['foto']) );
		}

		$db->free( $sql_result );
		
	}
	
	if( intval( $config['max_image_days'] ) ) {
		$thisdate = $_TIME - ($config['max_image_days'] * 3600 * 24);
		
		$db->query( "SELECT images  FROM " . PREFIX . "_images where date < '$thisdate' AND news_id = '0'" );
		
		while ( $row = $db->get_row() ) {
			
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
		
		}
		
		$db->free();
		
		$db->query( "DELETE FROM " . PREFIX . "_images where date < '$thisdate' AND news_id = '0'" );

		$db->query( "SELECT id, onserver FROM " . PREFIX . "_files WHERE date < '$thisdate' AND news_id = '0'" );
				
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
				
		$db->query( "DELETE FROM " . PREFIX . "_files WHERE date < '$thisdate' AND news_id = '0'" );
		
		$db->query( "SELECT id, name FROM " . PREFIX . "_comments_files WHERE date < '{$thisdate}' AND c_id = '0'" );
				
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
				
		$db->query( "DELETE FROM " . PREFIX . "_comments_files WHERE date < '{$thisdate}' AND c_id = '0'" );
		
	
	}
	
	clear_cache();

}
?>