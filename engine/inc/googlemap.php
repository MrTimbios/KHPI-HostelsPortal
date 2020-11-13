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
 File: googlemap.php
-----------------------------------------------------
 Use: Create sitemap
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$user_group[$member_id['user_group']]['admin_googlemap'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

$user_group = get_vars ( "usergroup" );

if (!is_array( $user_group )) {
	$user_group = array ();

	$db->query ( "SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC" );

	while ( $row = $db->get_row () ) {

		$user_group[$row['id']] = array ();

		foreach ( $row as $key => $value ) {
			$user_group[$row['id']][$key] = stripslashes($value);
		}

	}
	set_vars ( "usergroup", $user_group );
	$db->free ();
}

function send_url($url, $map) {
					
	$data = false;
			
	$file = $url.urlencode($map);
					
	if( function_exists( 'curl_init' ) ) {
						
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $file );
		curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
						
		$data = curl_exec( $ch );
		curl_close( $ch );
			
		return $data;
					
	} else {
			
		return @file_get_contents( $file );
			
	}
			
}

if ($_POST['action'] == "create") {
	
	if( !defined('AUTOMODE') ) {
		if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
			msg( "error", $lang['addnews_error'], $lang['sess_error'], "javascript:history.go(-1)" );
		}
	}
	
	include_once (DLEPlugins::Check(ENGINE_DIR.'/classes/google.class.php'));
	$map = new googlemap($config);

	$config['charset'] = strtolower($config['charset']);

	$map->limit = intval($_POST['limit']);
	$map->news_priority = strip_tags(stripslashes($_POST['priority']));
	$map->stat_priority = strip_tags(stripslashes($_POST['stat_priority']));
	$map->cat_priority = strip_tags(stripslashes($_POST['cat_priority']));

	$allow_list = explode ( ',', $user_group[5]['allow_cats'] );
	$not_allow_cats = explode ( ',', $user_group[5]['not_allow_cats'] );
	$stop_list = "";
	$cat_join = "";

	if ($allow_list[0] != "all") {
		
		if ($config['allow_multi_category']) {
			
			$cat_join = "INNER JOIN (SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN (" . implode ( ',', $allow_list ) . ")) c ON (p.id=c.news_id) ";
		
		} else {
			
			$stop_list = "category IN ('" . implode ( "','", $allow_list ) . "') AND ";
		
		}
		
	}

	if( $not_allow_cats[0] != "" ) {
		
		if ($config['allow_multi_category']) {
			
			$stop_list = "p.id NOT IN ( SELECT DISTINCT(" . PREFIX . "_post_extras_cats.news_id) FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN (" . implode ( ',', $not_allow_cats ) . ") ) AND ";

			
		} else {
			
			$stop_list = "category NOT IN ('" . implode ( "','", $not_allow_cats ) . "') AND ";
		
		}
		
	}
	
	$thisdate = date( "Y-m-d H:i:s", time() );
	if( $config['no_date'] AND !$config['news_future'] ) $where_date = " AND date < '" . $thisdate . "'";
	else $where_date = "";

	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post p {$cat_join}WHERE {$stop_list}approve=1{$where_date}" );

	if ( !$map->limit ) $map->limit = $row['count'];

	if ( $map->limit > 40000 ) {

		$pages_count = @ceil( $row['count'] / 40000 );

		$sitemap = $map->build_index( $pages_count );

	    $handler = fopen(ROOT_DIR. "/uploads/sitemap.xml", "wb+");
	    fwrite($handler, $sitemap);
	    fclose($handler);
	
		@chmod(ROOT_DIR. "/uploads/sitemap.xml", 0666);

		$sitemap = $map->build_stat();

	    $handler = fopen(ROOT_DIR. "/uploads/sitemap1.xml", "wb+");
	    fwrite($handler, $sitemap);
	    fclose($handler);
	
		@chmod(ROOT_DIR. "/uploads/sitemap1.xml", 0666);
		
		$n = 0;

		for ($i =0; $i < $pages_count; $i++) {

			$t = $i+2;
			$n = $n+1;

			$sitemap = $map->build_map_news( $n );

		    $handler = fopen(ROOT_DIR. "/uploads/sitemap{$t}.xml", "wb+");
		    fwrite($handler, $sitemap);
		    fclose($handler);
		
			@chmod(ROOT_DIR. "/uploads/sitemap{$t}.xml", 0666);

		}


	} else {

		$sitemap = $map->build_map();
	
	    $handler = fopen(ROOT_DIR. "/uploads/sitemap.xml", "wb+");
	    fwrite($handler, $sitemap);
	    fclose($handler);
	
		@chmod(ROOT_DIR. "/uploads/sitemap.xml", 0666);
	}

	if(defined('AUTOMODE')) {
		
		if (strpos($config['http_home_url'], "//") === 0) $config['http_home_url'] = "https:".$config['http_home_url'];
		elseif (strpos($config['http_home_url'], "/") === 0) $config['http_home_url'] = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'];
			
		if ($config['allow_alt_url']) {
	
			$map_link = $config['http_home_url']."sitemap.xml";
		
		} else {
		
			$map_link = $config['http_home_url']."uploads/sitemap.xml";
		
		}

		send_url("https://google.com/webmasters/sitemaps/ping?sitemap=", $map_link);
//		send_url("https://ping.blogs.yandex.ru/ping?sitemap=", $map_link);
		send_url("https://www.bing.com/webmaster/ping.aspx?siteMap=", $map_link);

		die("done"); 

	} else { $db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '38', '')" ); }

}

echoheader( "<i class=\"fa fa-google position-left\"></i><span class=\"text-semibold\">{$lang['opt_google']}</span>", $lang['header_g_1'] );

if (strpos($config['http_home_url'], "//") === 0) $config['http_home_url'] = "https:".$config['http_home_url'];
elseif (strpos($config['http_home_url'], "/") === 0) $config['http_home_url'] = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

echo <<<HTML
<div class="row">
<div class="col-md-7">
<form action="" method="post" class="form-horizontal">
<input type="hidden" name="action" value="create">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['google_map']}
  </div>
  <div class="panel-body">

HTML;

	if(!@file_exists(ROOT_DIR. "/uploads/sitemap.xml")){ 

		echo $lang['no_google_map'];

	} else {
		
		if( !$langformatdatefull ) $langformatdatefull = "d.m.Y H:i";

		$file_date = date($langformatdatefull, filectime(ROOT_DIR. "/uploads/sitemap.xml") );

		echo "<b>".$file_date."</b> ".$lang['google_map_info'];

		if ($config['allow_alt_url']) {

			$map_link = $config['http_home_url']."sitemap.xml";

			echo " <a href=\"".$map_link."\" target=\"_blank\">".$config['http_home_url']."sitemap.xml</a>";

		} else {

			$map_link = $config['http_home_url']."uploads/sitemap.xml";

			echo " <a href=\"".$map_link."\" target=\"_blank\">".$config['http_home_url']."uploads/sitemap.xml</a>";

		}

		$map_link = base64_encode(urlencode($map_link));

		echo "<br /><br /><input id=\"sendbutton\" name=\"sendbutton\" type=\"button\" class=\"btn bg-slate-600 btn-sm btn-raised mb-10\" value=\"{$lang['google_map_send']}\" /><div id=\"send_result\"></div>";

	}


echo <<<HTML
<script>
$(function(){
	$('#sendbutton').click(function() {
		$('#send_result').html('{$lang['dle_updatebox']}');
		$.post("engine/ajax/controller.php?mod=sitemap", { url: "{$map_link}", user_hash: "{$dle_login_hash}" } , function( data ){
					$('#send_result').html(data);
		});
	});
});
</script>
		<div class="form-group">
		  <label class="control-label col-sm-4 col-xs-6">{$lang['google_nnum']}</label>
		  <div class="col-sm-8 col-xs-6">
			<input type="text" class="form-control" style="width:60px;" name="limit"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_g_num']}" ></i>
		   </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-sm-4 col-xs-6">{$lang['google_stat_priority']}</label>
		  <div class="col-sm-8 col-xs-6">
			<input type="text" class="form-control" style="width:60px;" name="stat_priority" value="0.5"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_g_priority']}" ></i>
		   </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-sm-4 col-xs-6">{$lang['google_priority']}</label>
		  <div class="col-sm-8 col-xs-6">
			<input type="text" class="form-control" style="width:60px;" name="priority" value="0.6">
		   </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-sm-4 col-xs-6">{$lang['google_cat_priority']}</label>
		  <div class="col-sm-8 col-xs-6">
			<input type="text" class="form-control" style="width:60px;" name="cat_priority" value="0.7">
		   </div>
		 </div>	
   </div>
   <div class="panel-footer"><input type="submit" class="btn bg-teal btn-sm btn-raised" value="{$lang['google_create']}"></div>	
</div>
</form>
</div>
HTML;

echo <<<HTML
<div class="col-md-5">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['google_main']}
  </div>
  <div class="panel-body">
	
	  {$lang['google_info']}
	  
	
   </div>
</div>
</div>
</div>
HTML;


echofooter();
?>