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
 File: main.php
-----------------------------------------------------
 Use: Statistics and AutoCheck
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

echoheader( "<i class=\"fa fa-home position-left\"></i><span class=\"text-semibold\">{$lang['header_m_title']}</span>", $lang['header_m_subtitle'] );

$config['max_users_day'] = intval( $config['max_users_day'] );

$maxmemory = (@ini_get( 'memory_limit' ) != '') ? @ini_get( 'memory_limit' ) : $lang['undefined'];
$disabledfunctions = (strlen( ini_get( 'disable_functions' ) ) > 1) ? @ini_get( 'disable_functions' ) : $lang['undefined'];
$disabledfunctions = str_replace( ",", ", ", $disabledfunctions );
$safemode = (@ini_get( 'safe_mode' ) == 1) ? $lang['safe_mode_on'] : $lang['safe_mode_off'];
$licence = ($lic_tr) ? $lang['licence_trial'] : $lang['licence_full'];
$offline = (!$config['site_offline']) ? $lang['safe_mode_on'] : "<span class=\"text-danger\">" . $lang['safe_mode_off'] . "</span>";

if( function_exists( 'apache_get_modules' ) ) {
	if( array_search( 'mod_rewrite', apache_get_modules() ) !== false) {
		$mod_rewrite = $lang['safe_mode_on'];
	} else {
		$mod_rewrite = "<span class=\"text-danger\">" . $lang['safe_mode_off'] . "</span>";
	}
} else {
	$mod_rewrite = $lang['undefined'];
}

$os_version = @php_uname( "s" ) . " " . @php_uname( "r" );
$phpv = phpversion();

if ( function_exists( 'gd_info' ) ) {

	$array=gd_info ();
	$gdversion = "";

	foreach ($array as $key=>$val) {
	  
	  if ($val===true) {
	    $val="Enabled";
	  }
	
	  if ($val===false) {
	    $val="Disabled";
	  }
	
	  $gdversion .= $key.":&nbsp;{$val}, ";
	
	}

} else $gdversion = $lang['undefined'];


$maxupload = str_replace( array ('M', 'm' ), '', @ini_get( 'upload_max_filesize' ) );
$maxupload = formatsize( $maxupload * 1024 * 1024 );
$stats_arr = array();

if ( $config['allow_cache'] AND !$config['cache_type'] ) {

	$stats_cache = @file_get_contents( ENGINE_DIR . "/cache/news_adminstats.tmp" );
	if ( $stats_cache !== false ) $stats_arr = json_decode($stats_cache, true);
	
	if( !is_array($stats_arr) ) $stats_arr = array();
}

if ( !count($stats_arr) ) {

	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post" );
	$stats_arr['stats_news'] = number_format( $row['count'], 0, ',', ' ');
	
	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_subscribe" );
	$stats_arr['count_subscribe'] = number_format( $row['count'], 0, ',', ' ');
	
	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_comments" );
	$stats_arr['count_comments'] = number_format( $row['count'], 0, ',', ' ');
	
	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_comments WHERE approve ='0'" );
	$stats_arr['count_c_app'] = number_format( $row['count'], 0, ',', ' ');
	
	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_users" );
	$stats_arr['stats_users'] = number_format( $row['count'], 0, ',', ' ');
	
	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_users WHERE banned='yes'" );
	$stats_arr['stats_banned'] = number_format( $row['count'], 0, ',', ' ');
	
	$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post where approve = '0'" );
	$stats_arr['approve']  = number_format( $row['count'], 0, ',', ' ');
	
	
	$db->query( "SHOW TABLE STATUS FROM `" . DBNAME . "`" );
	$mysql_size = 0;
	while ( $r = $db->get_array() ) {
		if( strpos( $r['Name'], PREFIX . "_" ) !== false ) $mysql_size += $r['Data_length'] + $r['Index_length'];
	}
	$db->free();
	
	$stats_arr['mysql_size'] = formatsize( $mysql_size );

	if ( $config['allow_cache'] AND !$config['cache_type'] ) {
		file_put_contents (ENGINE_DIR . "/cache/news_adminstats.tmp", json_encode( $stats_arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), LOCK_EX);
		@chmod( ENGINE_DIR . "/cache/news_adminstats.tmp", 0666 );
	}

}

if( $stats_arr['count_c_app'] ) {
	
	$stats_arr['count_c_app'] = $stats_arr['count_c_app'] . " [ <a class=\"status-info\" href=\"?mod=cmoderation\">{$lang['stat_cmod_link']}</a> ]";

}

if( $stats_arr['approve'] and $user_group[$member_id['user_group']]['allow_all_edit'] ) {
	
	$stats_arr['approve'] = $stats_arr['approve'] . " [ <a class=\"status-info\" href=\"?mod=editnews&action=list&news_status=2\">{$lang['stat_medit_link']}</a> ]";

}

$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_complaint" );
$c_complaint = $row['count'];
set_cookie ( "dle_compl", $row['count'], 365 );

if( $c_complaint AND $user_group[$member_id['user_group']]['admin_complaint'] ) {

	$c_complaint = $row['count'] . " [ <a class=\"status-info\" href=\"?mod=complaint\">{$lang['stat_complaint_1']}</a> ]";

	if ($row['count'] > intval ( $_COOKIE['dle_compl'] )) {

		$c_complaint .= <<<HTML
<script>
<!--

$(function(){
	setTimeout(function() {
		Growl.info({
			title: '{$lang['p_info']}',
			text: '{$lang['opt_complaint_20']}'
		  });
	}, 1000);
});

//-->
</script>
HTML;

	}


}

function dirsize($directory) {
	
	if( ! is_dir( $directory ) ) return - 1;
	
	$size = 0;
	
	if( $DIR = opendir( $directory ) ) {
		
		while ( ($dirfile = readdir( $DIR )) !== false ) {
			
			if( @is_link( $directory . '/' . $dirfile ) || $dirfile == '.' || $dirfile == '..' ) continue;
			
			if( @is_file( $directory . '/' . $dirfile ) ) $size += filesize( $directory . '/' . $dirfile );
			
			else if( @is_dir( $directory . '/' . $dirfile ) ) {
				
				$dirSize = dirsize( $directory . '/' . $dirfile );
				if( $dirSize >= 0 ) $size += $dirSize;
				else return - 1;
			
			}
		
		}
		
		closedir( $DIR );
	
	}
	
	return $size;

}

$cache_size = formatsize( dirsize( "engine/cache" ) );

$dfs = @disk_free_space( "." );
$freespace = formatsize( $dfs );

if( $user_group[$member_id['user_group']]['admin_comments'] ) {
	$edit_comments = "&nbsp;[ <a class=\"status-info\" href=\"?mod=comments&action=edit\">{$lang['edit_comm']}</a> ]";
} else $edit_comments = "";

if( $member_id['user_group'] == 1 ) {

	if( $lic_tr ) {
		
		echo $activation_field;

	}
	
	$currect_version = VERSIONID;
	$currect_build = BUILDID;
	
	echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['main_quick']}
  </div>
  <div class="list-bordered">

	<div class="row box-section">	
	  <div class="col-sm-6 media-list media-list-linked">
		<a class="media-link" href="?mod=editusers&action=list">
			<div class="media-left"><img src="engine/skins/images/uset.png" class="img-lg section_icon"></div>
			<div class="media-body">
				<h6 class="media-heading  text-semibold">{$lang['opt_user']}</h6>
				<span class="text-muted text-size-small">{$lang['opt_userc']}</span>
			</div>
		</a>
	  </div>
	  <div class="col-sm-6 media-list media-list-linked">
		<a class="media-link" href="?mod=banners">
			<div class="media-left"><img src="engine/skins/images/rkl.png" class="img-lg section_icon"></div>
			<div class="media-body">
				<h6 class="media-heading  text-semibold">{$lang['opt_banner']}</h6>
				<span class="text-muted text-size-small">{$lang['opt_bannerc']}</span>
			</div>
		</a>
	  </div>
	</div>

	<div class="row box-section">	
	  <div class="col-sm-6 media-list media-list-linked">
		<a class="media-link" href="?mod=options&action=syscon">
			<div class="media-left"><img src="engine/skins/images/tools.png" class="img-lg section_icon"></div>
			<div class="media-body">
				<h6 class="media-heading  text-semibold">{$lang['opt_all']}</h6>
				<span class="text-muted text-size-small">{$lang['opt_allc']}</span>
			</div>
		</a>
	  </div>
	  <div class="col-sm-6 media-list media-list-linked">
		<a class="media-link" href="?mod=newsletter">
			<div class="media-left"><img src="engine/skins/images/nset.png" class="img-lg section_icon"></div>
			<div class="media-body">
				<h6 class="media-heading  text-semibold">{$lang['main_newsl']}</h6>
				<span class="text-muted text-size-small">{$lang['main_newslc']}</span>
			</div>
		</a>
	  </div>
	</div>	

	<div class="row box-section">	
	  <div class="col-sm-6 media-list media-list-linked">
		<a class="media-link" href="?mod=static">
			<div class="media-left"><img src="engine/skins/images/spset.png" class="img-lg section_icon"></div>
			<div class="media-body">
				<h6 class="media-heading  text-semibold">{$lang['opt_static']}</h6>
				<span class="text-muted text-size-small">{$lang['opt_staticd']}</span>
			</div>
		</a>
	  </div>
	  <div class="col-sm-6 media-list media-list-linked">
		<a class="media-link" href="?mod=clean">
			<div class="media-left"><img src="engine/skins/images/clean.png" class="img-lg section_icon"></div>
			<div class="media-body">
				<h6 class="media-heading  text-semibold">{$lang['opt_clean']}</h6>
				<span class="text-muted text-size-small">{$lang['opt_cleanc']}</span>
			</div>
		</a>
	  </div>
	</div>	

	<div class="row box-section">	
	  <div class="col-sm-6 media-list media-list-linked">
		<a class="media-link" onclick="check_files('lokal'); return false;" href="#">
			<div class="media-left"><img src="engine/skins/images/shield.png" class="img-lg section_icon"></div>
			<div class="media-body">
				<h6 class="media-heading  text-semibold">{$lang['mod_anti']}</h6>
				<span class="text-muted text-size-small">{$lang['anti_descr']}</span>
			</div>
		</a>
	  </div>
	  <div class="col-sm-6 media-list media-list-linked">
		<a class="media-link" href="?mod=options&action=options">
			<div class="media-left"><img src="engine/skins/images/next.png" class="img-lg section_icon"></div>
			<div class="media-body">
				<h6 class="media-heading  text-semibold">{$lang['opt_all_rublik']}</h6>
				<span class="text-muted text-size-small">{$lang['opt_all_rublikc']}</span>
			</div>
		</a>
	  </div>
	</div>

  </div>
</div>
<script>
<!--
		function check_files ( folder ){

			if (folder == "snap") {

				DLEconfirm( '{$lang['anti_snapalert']}', '{$lang['p_confirm']}', function () {

					$('#antivirus').html('<div class="panel-body">{$lang['anti_box']}</div>');

					ShowLoading('');		
					$.post('engine/ajax/controller.php?mod=antivirus', { folder: folder, user_hash: '{$dle_login_hash}' }, function(data){
				
						HideLoading('');
				
						$('#antivirus').html(data);
				
					});

				} );

			} else {

				$('#antivirusbox').show();
				$('#antivirus').html('<div class="panel-body">{$lang['anti_box']}</div>');
				
				ShowLoading('');		
				$.post('engine/ajax/controller.php?mod=antivirus', { folder: folder, user_hash: '{$dle_login_hash}' }, function(data){
				
					HideLoading('');
				
					$('#antivirus').html(data);
				
				});

			}

			return false;
		}
		
		$(function(){

			$.ajaxSetup({
				cache: false
			});

			$('#clearbutton').click(function() {

				$.get("engine/ajax/controller.php?mod=adminfunction&action=clearcache&user_hash={$dle_login_hash}", function( data ){

					$('#cachesize').html('0 b');
					Growl.info({
						title: '{$lang['p_info']}',
						text: data
					});

				});
				return false;
			});

			$('#clearsubscribe').click(function() {

			    DLEconfirm( '{$lang['confirm_action']}', '{$lang['p_confirm']}', function () {

					$.get("engine/ajax/controller.php?mod=adminfunction&action=clearsubscribe&user_hash={$dle_login_hash}", function( data ){
						Growl.info({
							title: '{$lang['p_info']}',
							text: data
						});
					});
				} );
				return false;
			});

			$('#check_updates').click(function() {
			
				ShowLoading('');
				
				$.get("engine/ajax/controller.php?mod=updates&versionid={$currect_version}&user_hash={$dle_login_hash}&build={$currect_build}", function( data ){
					HideLoading('');
					DLEalert(data, '{$lang['all_info']}');
				});
				return false;
			});

			$('#send_notice').click(function() {

				ShowLoading('');
				var notice = $('#notice').val();
				$.post("engine/ajax/controller.php?mod=adminfunction&action=sendnotice&user_hash={$dle_login_hash}", { notice: notice } , function( data ){
					HideLoading('');
					DLEalert(data, '{$lang['all_info']}');
				});
				return false;
			});

		});
//-->
</script>
<div id="antivirusbox" class="panel panel-default" style="display:none;">
  <div class="panel-heading">
    <div class="title">{$lang['anti_title']}</div>
  </div>
  <div id="antivirus">
  {$lang['anti_box']}
  </div>
</div>

		
		<div class="panel panel-default">
		
		    <div class="panel-heading">
				<ul class="nav nav-tabs nav-tabs-solid">
					<li class="active"><a href="#statall" data-toggle="tab"><i class="fa fa-bar-chart position-left"></i> {$lang['stat_all']}</a></li>
					<li><a href="#notinfo" data-toggle="tab"><i class="fa fa-pencil-square-o position-left"></i> {$lang['main_notice']}</a></li>
					<li id="dlestats"><a href="#statauto" data-toggle="tab"><i class="fa fa-cog position-left"></i> {$lang['stat_auto']}</a></li>
				</ul>
			</div>
		
                 <div class="panel-tab-content tab-content">
                     <div class="tab-pane active" id="statall">
					 
						<table class="table table-sm">
							<tr>
								<td class="col-md-3">{$lang['site_status']}</td>
								<td class="col-md-9">{$offline}</td>
							</tr>
							<tr>
								<td>{$lang['stat_allnews']}</td>
								<td>{$stats_arr['stats_news']}</td>
							</tr>
							<tr>
								<td>{$lang['stat_mod']}</td>
								<td>{$stats_arr['approve']}</td>
							</tr>
							<tr>
								<td>{$lang['stat_complaint']}</td>
								<td>{$c_complaint}</td>
							</tr>
							<tr>
								<td>{$lang['stat_comments']}</td>
								<td>{$stats_arr['count_comments']} [ <a href="{$config['http_home_url']}index.php?do=lastcomments" target="_blank">{$lang['last_comm']}</a> ]{$edit_comments}</td>
							</tr>
							<tr>
								<td>{$lang['stat_cmod']}</td>
								<td>{$stats_arr['count_c_app']}</td>
							</tr>
							<tr>
								<td>{$lang['stat_users']}</td>
								<td>{$stats_arr['stats_users']}</td>
							</tr>
							<tr>
								<td>{$lang['stat_banned']}</td>
								<td><span class="text-danger">{$stats_arr['stats_banned']}</span></td>
							</tr>
							<tr>
								<td>{$lang['stat_bd']}</td>
								<td>{$stats_arr['mysql_size']}</td>
							</tr>
							<tr>
								<td>{$lang['cache_size']}</td>
								<td><span id="cachesize">{$cache_size}</span></td>
							</tr>
							<tr>
								<td>{$lang['stat_maxfile']}</td>
								<td>{$maxupload}</td>
							</tr>
							<tr>
								<td>{$lang['free_size']}</td>
								<td>{$freespace}</td>
							</tr>
						</table>

						<div class="panel-footer">
HTML;

	echo "<button id=\"check_updates\" name=\"check_updates\" class=\"btn bg-slate-600 btn-sm btn-raised\"><i class=\"fa fa-exclamation-circle\"></i> {$lang['dle_udate']}</button>&nbsp;<button id=\"clearbutton\" name=\"clearbutton\" class=\"btn bg-danger-600 btn-sm btn-raised\"><i class=\"fa fa-trash\"></i> {$lang['btn_clearcache']}</button>";

	if ($stats_arr['count_subscribe']) echo "&nbsp;<button id=\"clearsubscribe\" name=\"clearsubscribe\" class=\"btn bg-brown-600 btn-sm btn-raised\"><i class=\"fa fa-user\"></i> {$lang['btn_clearsubscribe']}</button>";

	$row = $db->super_query( "SELECT notice FROM " . PREFIX . "_notice WHERE user_id = '{$member_id['user_id']}'" );
	
	$row['notice'] = htmlspecialchars( $row['notice'], ENT_QUOTES, $config['charset'] );


echo <<<HTML
						</div>
					</div>
                     <div class="tab-pane has-padding" id="notinfo">
							<textarea id="notice" name="notice" class="classic" style="width:100%;height:200px;" placeholder="{$lang['main_no_notice']}">{$row['notice']}</textarea>
							<button id="send_notice" name="send_notice" class="btn bg-teal btn-sm btn-raised"><i class="fa fa-floppy-o"></i> {$lang['news_save']}</button>
                     </div>
                     <div class="tab-pane" id="statauto" >
						<table class="table table-sm">
							<tr>
								<td class="col-md-3">{$lang['dle_version']}</td>
								<td class="col-md-9">{$config['version_id']}</td>
							</tr>
							<tr>
								<td>{$lang['licence_info']}</td>
								<td>{$licence}</td>
							</tr>
							<tr>
								<td>{$lang['stat_os']}</td>
								<td>{$os_version}</td>
							</tr>
							<tr>
								<td>{$lang['stat_php']}</td>
								<td>{$phpv}</td>
							</tr>
							<tr>
								<td>{$lang['stat_mysql']}</td>
								<td>{$db->mysql_version}</td>
							</tr>
							<tr>
								<td>{$lang['stat_gd']}</td>
								<td>{$gdversion}</td>
							</tr>
							<tr>
								<td>Module mod_rewrite</td>
								<td>{$mod_rewrite}</td>
							</tr>
							<tr>
								<td>{$lang['stat_safemode']}</td>
								<td>{$safemode}</td>
							</tr>
							<tr>
								<td>{$lang['stat_maxmem']}</td>
								<td>{$maxmemory}</td>
							</tr>
							<tr>
								<td>{$lang['stat_func']}</td>
								<td>{$disabledfunctions}</td>
							</tr>
							<tr>
								<td>{$lang['stat_maxfile']}</td>
								<td>{$maxupload}</td>
							</tr>
							<tr>
								<td>{$lang['free_size']}</td>
								<td>{$freespace}</td>
							</tr>
						</table>      
                     </div>
                 </div>
             </div>
HTML;

	if( !is_writable( ENGINE_DIR . "/cache/" ) OR !is_writable( ENGINE_DIR . "/cache/system/" ) ) {
		echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['stat_cache']}</div>";
	
	}
	
	if( @file_exists( "install.php" ) ) {
		echo "<div class=\"alert alert-danger alert-styled-left alert-arrow-left alert-component\">{$lang['stat_install']}</div>";
	}
	if( $dfs AND $dfs < 20240 ) {
		echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['stat_nofree']}</div>";
	}
	
	if (!defined( 'SECURE_AUTH_KEY' ) OR strlen(SECURE_AUTH_KEY) < 20 ) {
		echo "<div class=\"alert alert-danger alert-styled-left alert-arrow-left alert-component\">{$lang['stat_sec_auth']}</div>";
	}
	
	if (get_ip() == "not detected" ) {
		echo "<div class=\"alert alert-danger alert-styled-left alert-arrow-left alert-component\">{$lang['stat_sec_ip']}</div>";
	}
	
	if( !function_exists( 'simplexml_load_string' ) ) {
		echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['stat_not_min']} SimpleXML</div>";
	}

	if( !@extension_loaded('zlib') ) {
		echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['stat_not_min']} Zlib</div>";
	}
	
	if( !@extension_loaded('curl') ) {
		echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['stat_not_min']} CURL</div>";
	}
	
	if( preg_match('/1|yes|on|true/i', ini_get('register_globals')) ) {
		echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['stat_secfault']}</div>";
	}
	
	if( version_compare($phpv, '5.5', '<') ) {
		echo "<div class=\"alert alert-danger alert-styled-left alert-arrow-left alert-component\">{$lang['stat_phperror']}</div>";
	}


	$plugins_errors = array();
	
	$db->query( "SELECT plugin_id, COUNT(id) AS count FROM " . PREFIX . "_plugins_logs GROUP BY plugin_id" );
	
	while ( $row = $db->get_row() ) {
		$plugins_errors[$row['plugin_id']] = $row['count'];
	}
	
	if( count($plugins_errors) ) {
		
		echo "<div class=\"alert alert-danger alert-styled-left alert-arrow-left alert-component\">{$lang['plugins_errors_11']}<br><br><a class=\"btn bg-brown-600 btn-sm btn-raised position-left legitRipple\" href=\"?mod=plugins&action=errors\"><i class=\"fa fa-exclamation-triangle position-left\"></i>{$lang['plugins_errors_12']}</a></div>";
		
	}
	

	if( $config['cache_type'] ) {
		if ($mcache->connection < 1) {
			if (!$mcache->connection) {
				echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['stat_m_fail']}</div>";
			} else echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['stat_m_fail_1']}</div>";
		}
	}

	$check_files       = array(
		"/templates/.htaccess",
		"/uploads/.htaccess",
		"/uploads/files/.htaccess",
		"/engine/data/.htaccess",
		"/engine/cache/.htaccess",
		"/engine/cache/system/.htaccess",
	);

	foreach ($check_files as $file) {

		if( !file_exists( ROOT_DIR .$file ) ) {
			echo "<div class=\"alert alert-danger alert-styled-left alert-arrow-left alert-component\">".str_replace("{folder}", $file, $lang['stat_secfault_2'])."</div>";
		}

	}

	if( COLLATE == "utf8" ) {
		echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['upgr_utf8']}</div>";
	}
	
	if( !$lic_tr AND defined('DEMOVERSION') ) {
		echo "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['upgr_demo']}</div>";
	}

} else {

	$row = $db->super_query( "SELECT notice FROM " . PREFIX . "_notice WHERE user_id = '{$member_id['user_id']}'" );
		
	$row['notice'] = htmlspecialchars( stripslashes( $row['notice'] ), ENT_QUOTES, $config['charset'] );

echo <<<HTML
<div class="panel panel-default">
	<div class="panel-heading">
		<ul class="nav nav-tabs nav-tabs-solid">
			<li class="active"><a href="#statall" data-toggle="tab"><i class="fa fa-bar-chart position-left"></i> {$lang['stat_all']}</a></li>
			<li><a href="#notinfo" data-toggle="tab"><i class="fa fa-pencil-square-o position-left"></i> {$lang['main_notice']}</a></li>
		</ul>
	</div>
                 <div class="panel-tab-content tab-content">
                     <div class="tab-pane active" id="statall">
						<table class="table table-sm">
							<tr>
								<td class="col-md-3">{$lang['site_status']}</td>
								<td class="col-md-9">{$offline}</td>
							</tr>
							<tr>
								<td>{$lang['stat_allnews']}</td>
								<td>{$stats_arr['stats_news']}</td>
							</tr>
							<tr>
								<td>{$lang['stat_mod']}</td>
								<td>{$stats_arr['approve']}</td>
							</tr>
							<tr>
								<td>{$lang['stat_complaint']}</td>
								<td>{$c_complaint}</td>
							</tr>
							<tr>
								<td>{$lang['stat_comments']}</td>
								<td>{$stats_arr['count_comments']} [ <a href="{$config['http_home_url']}index.php?do=lastcomments" target="_blank">{$lang['last_comm']}</a> ]{$edit_comments}</td>
							</tr>
							<tr>
								<td>{$lang['stat_cmod']}</td>
								<td>{$stats_arr['count_c_app']}</td>
							</tr>
							<tr>
								<td>{$lang['stat_users']}</td>
								<td>{$stats_arr['stats_users']}</td>
							</tr>
							<tr>
								<td>{$lang['stat_banned']}</td>
								<td><span class="text-danger">{$stats_arr['stats_banned']}</span></td>
							</tr>
						</table>
					</div>
					
                     <div class="tab-pane" id="notinfo" >
						<div class="panel-body">
							<textarea id="notice" name="notice" class="classic" style="width:100%;height:200px;" placeholder="{$lang['main_no_notice']}">{$row['notice']}</textarea>
							<button id="send_notice" name="send_notice" class="btn bg-teal btn-sm btn-raised"><i class="fa fa-floppy-o"></i> {$lang['news_save']}</button>
						</div>
                     </div>
				</div>
</div>
<script>
		$(function(){

			$('#send_notice').click(function() {

				ShowLoading('');
				var notice = $('#notice').val();
				$.post("engine/ajax/controller.php?mod=adminfunction&action=sendnotice&user_hash={$dle_login_hash}", { notice: notice } , function( data ){
					HideLoading('');
					DLEalert(data, '{$lang['all_info']}');
				});
				return false;
			});

		});
</script>
HTML;

}

echofooter();
?>