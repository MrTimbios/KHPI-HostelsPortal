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
 File: logs.php
-----------------------------------------------------
 Use: The list of actions in the admin panel
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( $member_id['user_group'] != 1 ) {
	msg( "error", $lang['addnews_denied'], $lang['db_denied'] );
}


if ( file_exists( DLEPlugins::Check(ROOT_DIR . '/language/' . $selected_language . '/adminlogs.lng') ) ) {
	require_once (DLEPlugins::Check(ROOT_DIR . '/language/' . $selected_language . '/adminlogs.lng'));
}

if ($_REQUEST['searchword']) {
	  
	$searchword = urldecode ( $_REQUEST['searchword'] );
	  
	if( @preg_match( "/[\||\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $searchword ) ) $searchword = "";
	else $searchword = htmlspecialchars( strip_tags( stripslashes( trim( $searchword ) ) ), ENT_COMPAT, $config['charset'] );
	  
} else $searchword = "";
	
$start_from = intval( $_REQUEST['start_from'] );
$config['adminlog_maxdays'] = intval($config['adminlog_maxdays']);
$news_per_page = 50;

if( $start_from < 0 ) $start_from = 0;
if($config['adminlog_maxdays'] < 30 ) $config['adminlog_maxdays'] = 30;

$thisdate = $_TIME - ($config['adminlog_maxdays'] * 3600 * 24);

$db->query( "DELETE FROM " . USERPREFIX . "_admin_logs WHERE date < '{$thisdate}'" );

echoheader( "<i class=\"fa fa-globe position-left\"></i><span class=\"text-semibold\">{$lang['opt_logs']}</span>", $lang['header_log_1']  );

$menu_active = " class=\"active\"";
$menu_active_auth = "";

if( $action == "auth") {
	$lang['opt_logsc'] = $lang['admin_logs_auth'];
	$menu_active_auth = " class=\"active\"";
	$menu_active = "";
}

	echo <<<HTML
<script>
<!--

function search_submit(prm){
	document.navi.start_from.value=prm;
	document.navi.submit();
	return false;
}

//-->
</script>
<div class="navbar navbar-default navbar-component navbar-xs" style="z-index: inherit;">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="fa fa-bars"></i></a></li>
	</ul>
	<div class="navbar-collapse collapse" id="navbar-filter">
		<ul class="nav navbar-nav">
			<li{$menu_active}><a href="?mod=logs" class="tip" title="{$lang['admin_logs_all']}"><i class="fa fa-globe position-left"></i>{$lang['admin_logs_all']}</a></li>
			<li{$menu_active_auth}><a href="?mod=logs&action=auth" class="tip" title="{$lang['admin_logs_auth']}"><i class="fa fa-lock position-left"></i>{$lang['admin_sh_auth']}</a></li>
		</ul>
	</div>
</div>

<form action="?mod=logs" method="get" name="navi" id="navi">
<input type="hidden" name="mod" value="logs">
<input type="hidden" name="action" value="{$action}">
<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_logsc']}
	<div class="heading-elements">
		<div class="form-group has-feedback" style="width:250px;">
			<input name="searchword" type="search" class="form-control" placeholder="{$lang['search_field']}" onchange="document.navi.start_from.value=0;" value="{$searchword}">
			<div class="form-control-feedback">
			    <a href="#" onclick="$(this).closest('form').submit();"><i class="fa fa-search text-size-base text-muted"></i></a>
			</div>
		</div>
	</div>
  </div>
  <div class="table-responsive">
    <table class="table table-xs table-striped table-hover">
      <thead>
      <tr>
        <th>{$lang['addnews_date']}</th>
        <th>{$lang['user_name']}</th>
        <th>IP:</td>
        <th>{$lang['user_action']}</th>
      </tr>
      </thead>
	  <tbody>
HTML;

	if( !$langformatdatefull ) $langformatdatefull = "d.m.Y H:i:s";
	if( !$langformatdate ) $langformatdate = "d.m.Y";

	if ( $searchword ) {

		$searchword = @$db->safesql($searchword);
		$date = date_create_from_format($langformatdate, $searchword);
		
		if( $date ) {
			$date = date_time_set($date, 0, 0, 0);
			$startdate = date_timestamp_get ( $date );
			$enddate = date_timestamp_get ( date_modify($date, '+1 day') );
			$where[] = "date >= '{$startdate}' AND date <= '{$enddate}'";

		} else {
			
			$action_count=array();
			
			if(count($lang_logs)) {
				foreach($lang_logs as $key => $value) {
					if (mb_stripos($value, $searchword, 0, $config['charset']) !== false) {
						$act = intval(str_ireplace("admin_logs_action_","", $key));
						if($act) $action_count[] = $act;
					}
				}
			}
			
			if(count($action_count)) {
				
				$action_lists = " OR action ='".implode("' OR action ='", $action_count)."'";
				
			} else $action_lists="";

			$where[] = "(name like '%$searchword%' OR ip like '%$searchword%' OR extras like '%$searchword%'{$date}$action_lists)";
		}
	}

	if( $action == "auth") {

		$where[] = "(action ='89' OR action ='90' OR action ='91' OR action ='92' OR action ='99')";

	} else {

		$where[] = "(action !='89' AND action !='90' AND action !='91' AND action !='92' AND action !='99')";
	}
	
	$where = implode(" AND ", $where);

	$db->query( "SELECT * FROM " . USERPREFIX . "_admin_logs WHERE {$where} ORDER BY date DESC LIMIT {$start_from},{$news_per_page}" );

	$entries = "";
	
	$i = $start_from;
	while ( $row = $db->get_array() ) {
		$i ++;

		$row['date'] = date( $langformatdatefull, $row['date'] );
		$status = $lang_logs["admin_logs_action_".$row['action']];

		$entries .= "
        <tr>
        <td class=\"text-nowrap\">{$row['date']}</td>
        <td class=\"text-nowrap\"><a href=\"?mod=editusers&action=edituser&user=".urlencode($row['name'])."\" target=\"_blank\">{$row['name']}</a></td>
        <td>{$row['ip']}</td>
        <td style=\"word-break: break-all;\">{$status} <b>".stripslashes($row['extras'])."</b></td>
        </tr>";
	}

	if( !$entries ) {
		echo "<tr><td colspan=\"4\" align=\"center\"><br /><br />" . $lang['logs_not_found'] . "<br /><br /><br /></td></tr>";
	} else {
		echo $entries;
	}


	$db->free();

	$result_count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_admin_logs WHERE {$where}");
	$all_count_news = $result_count['count'];

		// pagination

		$npp_nav = "";
		
		if( $all_count_news > $news_per_page ) {

			if( $start_from > 0 ) {
				$previous = $start_from - $news_per_page;
				$npp_nav .= "<li><a onclick=\"javascript:search_submit($previous); return(false);\" href=\"#\" title=\"{$lang['edit_prev']}\">&lt;&lt;</a></li>";
			}
		
			$enpages_count = @ceil( $all_count_news / $news_per_page );
			$enpages_start_from = 0;
			$enpages = "";
			
			if( $enpages_count <= 10 ) {
				
				for($j = 1; $j <= $enpages_count; $j ++) {
					
					if( $enpages_start_from != $start_from ) {
						
						$enpages .= "<li><a onclick=\"javascript:search_submit($enpages_start_from); return(false);\" href=\"#\">$j</a></li>";
					
					} else {
						
						$enpages .= "<li class=\"active\"><span>$j</span></li>";
					}
					
					$enpages_start_from += $news_per_page;
				}
				
				$npp_nav .= $enpages;
			
			} else {
				
				$start = 1;
				$end = 10;
				
				if( $start_from > 0 ) {
					
					if( ($start_from / $news_per_page) > 4 ) {
						
						$start = @ceil( $start_from / $news_per_page ) - 3;
						$end = $start + 9;
						
						if( $end > $enpages_count ) {
							$start = $enpages_count - 10;
							$end = $enpages_count - 1;
						}
						
						$enpages_start_from = ($start - 1) * $news_per_page;
					
					}
				
				}
				
				if( $start > 2 ) {
					
					$enpages .= "<li><a onclick=\"javascript:search_submit(0); return(false);\" href=\"#\">1</a></li> <li><span>...</span></li>";
				
				}
				
				for($j = $start; $j <= $end; $j ++) {
					
					if( $enpages_start_from != $start_from ) {
						
						$enpages .= "<li><a onclick=\"javascript:search_submit($enpages_start_from); return(false);\" href=\"#\">$j</a></li>";
					
					} else {
						
						$enpages .= "<li class=\"active\"><span>$j</span></li>";
					}
					
					$enpages_start_from += $news_per_page;
				}
				
				$enpages_start_from = ($enpages_count - 1) * $news_per_page;
				$enpages .= "<li><span>...</span></li><li><a onclick=\"javascript:search_submit($enpages_start_from); return(false);\" href=\"#\">$enpages_count</a></li>";
				
				$npp_nav .= $enpages;
			
			}

			if( $all_count_news > $i ) {
				$how_next = $all_count_news - $i;
				if( $how_next > $news_per_page ) {
					$how_next = $news_per_page;
				}
				$npp_nav .= "<li><a onclick=\"javascript:search_submit($i); return(false);\" href=\"#\" title=\"{$lang['edit_next']}\">&gt;&gt;</a></li>";
			}
			
			$npp_nav = "<ul class=\"pagination pagination-sm\">".$npp_nav."</ul>";
		
		}
		
		// pagination
	
	echo <<<HTML
</tbody></table>

	</div>
</div>
<div class="mb-20">
	{$npp_nav}
</div>
</form>
HTML;

echofooter();
?>