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
 File: iptools.php
-----------------------------------------------------
 Use: Search by IP
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['admin_iptools'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if( isset( $_REQUEST['ip'] ) ) $ip = $db->safesql( htmlspecialchars( strip_tags( trim( $_REQUEST['ip'] ) ) ) ); else $ip = "";
if( isset( $_REQUEST['name'] ) ) $name = $db->safesql( htmlspecialchars( strip_tags( trim( $_REQUEST['name'] ) ), ENT_QUOTES, $config['charset'] ) ); else $name = "";

if( !$langformatdatefull ) $langformatdatefull = "d.m.Y H:i";

if( $_REQUEST['doaction'] == "dodelcomments" AND $_REQUEST['id']) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$id = intval( $_REQUEST['id'] );
	
	$db->query( "UPDATE " . USERPREFIX . "_users set comm_num='0' WHERE user_id ='{$id}'" );
	deletecommentsbyuserid($id);
}
	
echoheader( "<i class=\"fa fa-search position-left\"></i><span class=\"text-semibold\">{$lang['opt_iptools']}</span>", $lang['header_ip_1'] );

echo <<<HTML
<form action="?mod=iptools" method="post" class="form-horizontal">
<input type="hidden" name="action" value="find">
<input type="hidden" name="mod" value="iptools">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_iptoolsc']}
  </div>
  <div class="panel-body">
	{$lang['opt_iptoolsc']}<br /><input class="form-control width-350 position-left" type="text" name="ip" value="{$ip}"><input type="submit" value="{$lang['b_find']}" class="btn bg-primary-600 btn-sm btn-raised">
	 <div class="text-muted text-size-small mb-20"><i class="fa fa-exclamation-circle"></i> {$lang['opt_ipfe']}</div>
	 {$lang['opt_iptoolsname']}<br /><input class="form-control width-350 position-left" type="text" name="name" value="{$name}"><input type="submit" value="{$lang['b_find']}" class="btn bg-primary-600 btn-sm btn-raised">
  </div>
</div>

</form>
HTML;

if( $_REQUEST['action'] == "find" and $ip != "" ) {
	
	echo <<<HTML
<script>
<!--
function cdelete(id){
	    DLEconfirm( '{$lang['comm_alldelconfirm']}', '{$lang['p_confirm']}', function () {
			document.location='?mod=iptools&action=find&ip={$ip}&doaction=dodelcomments&user_hash={$dle_login_hash}&id=' + id + '';
		} );
}
//-->
</script>
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['ip_found_users']}
  </div>
  <div class="table-responsive">
    <table class="table table-xs table-hover">
      <thead>
      <tr>
        <th>{$lang['user_name']}</th>
		<th class="text-center">IP</td>
        <th class="text-center">{$lang['user_reg']}</th>
        <th class="text-center">{$lang['user_last']}</th>
        <th class="text-center">{$lang['user_news']}</th>
        <th class="text-center">{$lang['user_coms']}</th>
		<th class="text-center">{$lang['user_acc']}</th>
      </tr>
      </thead>
	  <tbody>
HTML;
	
	$db->query( "SELECT * FROM " . USERPREFIX . "_users WHERE logged_ip LIKE '{$ip}%'" );
	
	$i = 0;
	while ( $row = $db->get_array() ) {
		$i ++;
		
		if( $row[news_num] == 0 ) {
			$news_link = "$row[news_num]";
		} else {
			$news_link = "[<a href=\"{$config['http_home_url']}index.php?subaction=allnews&user=" . urlencode( $row['name'] ) . "\" target=\"_blank\">" . $row[news_num] . "</a>]";
		}
		if( $row[comm_num] == 0 ) {
			$comms_link = $row['comm_num'];
		} else {
			$comms_link = <<<HTML
				<div class="btn-group">
				<a href="#" target="_blank" data-toggle="dropdown" data-original-title="{$lang['edit_com']}" class="status-info tip"><b>{$row['comm_num']}</b></a>
				  <ul class="dropdown-menu text-left">
				   <li><a href="{$config['http_home_url']}index.php?do=lastcomments&userid={$row['user_id']}" target="_blank"><i class="fa fa-eye position-left"></i>{$lang['comm_view']}</a></li>
				   <li class="divider"></li>
				   <li><a onclick="javascript:cdelete('{$row['user_id']}'); return(false)" href=""?mod=iptools&action=find&ip={$ip}&doaction=dodelcomments&user_hash={$dle_login_hash}&id={$row['id']}"><i class="fa fa-trash-o position-left text-danger"></i>{$lang['comm_del']}</a></li>
				  </ul>
				</div>
HTML;
		}
		
		if( $row['banned'] == 'yes' ) $group = "<span class=\"text-danger\">" . $lang['user_ban'] . "</span>";
		else $group = $user_group[$row['user_group']]['group_name'];
		
		echo "
        <tr>
        <td><a href=\"?mod=editusers&action=edituser&id={$row['user_id']}\" target=\"_blank\">{$row['name']}</a></td>
        <td class=\"text-center\">" . $row['logged_ip'] . "</td>
        <td class=\"text-center\"> " . langdate( $langformatdatefull, $row['reg_date'] ) . "</td>
        <td class=\"text-center\">" . langdate( $langformatdatefull, $row['lastdate'] ) . "</td>
        <td class=\"text-center\">" . $news_link . "</td>
        <td class=\"text-center\">" . $comms_link . "</td>
        <td class=\"text-center\">" . $group . "</td>
        </tr>";
	}
	
	if( $i == 0 ) {
		echo "<tr><td height=18 colspan=7><p align=center>{$lang['ip_empty']}</p></td></tr>";
	}
	
	echo <<<HTML
	  </tbody>
	</table>
  </div>
</div>


<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['ip_found_comments']}
  </div>
  <div class="table-responsive">

    <table class="table table-xs table-hover">
      <thead>
      <tr>
        <th>{$lang['user_name']}</th>
		<th class="text-center">IP</td>
        <th class="text-center">{$lang['user_reg']}</th>
        <th class="text-center">{$lang['user_last']}</th>
        <th class="text-center">{$lang['user_news']}</th>
        <th class="text-center">{$lang['user_coms']}</th>
		<th class="text-center">{$lang['user_acc']}</th>
      </tr>
      </thead>
	  <tbody>
HTML;
	
	$db->query( "SELECT " . PREFIX . "_comments.user_id, " . PREFIX . "_comments.ip, " . USERPREFIX . "_users.comm_num, banned, user_group, reg_date, lastdate, " . USERPREFIX . "_users.name, " . USERPREFIX . "_users.news_num FROM " . PREFIX . "_comments LEFT JOIN " . USERPREFIX . "_users ON " . PREFIX . "_comments.user_id=" . USERPREFIX . "_users.user_id WHERE " . PREFIX . "_comments.ip LIKE '{$ip}%' AND " . PREFIX . "_comments.is_register = '1' AND " . USERPREFIX . "_users.name != '' GROUP BY " . PREFIX . "_comments.user_id" );
	
	$i = 0;
	while ( $row = $db->get_array() ) {
		$i ++;
		
		if( $row[news_num] == 0 ) {
			$news_link = "$row[news_num]";
		} else {
			$news_link = "[<a href=\"{$config['http_home_url']}index.php?subaction=allnews&user=" . urlencode( $row['name'] ) . "\" target=\"_blank\">" . $row[news_num] . "</a>]";
		}
		if( $row[comm_num] == 0 ) {
			$comms_link = $row['comm_num'];
		} else {
			$comms_link = <<<HTML
				<div class="btn-group">
				<a href="#" target="_blank" data-toggle="dropdown" data-original-title="{$lang['edit_com']}" class="status-info tip"><b>{$row['comm_num']}</b></a>
				  <ul class="dropdown-menu text-left">
				   <li><a href="{$config['http_home_url']}index.php?do=lastcomments&userid={$row['user_id']}" target="_blank"><i class="fa fa-eye position-left"></i>{$lang['comm_view']}</a></li>
				   <li class="divider"></li>
				   <li><a onclick="javascript:cdelete('{$row['user_id']}'); return(false)" href=""?mod=iptools&action=find&ip={$ip}&doaction=dodelcomments&user_hash={$dle_login_hash}&id={$row['id']}"><i class="fa fa-trash-o position-left text-danger"></i>{$lang['comm_del']}</a></li>
				  </ul>
				</div>
HTML;
		}
		
		if( $row['banned'] == 'yes' ) $group = "<span class=\"text-danger\">" . $lang['user_ban'] . "</span>";
		else $group = $user_group[$row['user_group']]['group_name'];
		
		echo "
        <tr>
        <td><a href=\"?mod=editusers&action=edituser&id={$row['user_id']}\" target=\"_blank\">{$row['name']}</a></td>
        <td class=\"text-center\">" . $row['ip'] . "</td>
        <td class=\"text-center\">" . langdate( $langformatdatefull, $row['reg_date'] ) . "</td>
        <td class=\"text-center\">" . langdate( $langformatdatefull, $row['lastdate'] ) . "</td>
        <td class=\"text-center\">" . $news_link . "</td>
        <td class=\"text-center\">" . $comms_link . "</td>
        <td class=\"text-center\">" . $group . "</td>
        </tr>";
	}
	
	if( $i == 0 ) {
		echo "<tr><td height=18 colspan=7><p align=center>{$lang['ip_empty']}</p></td></tr>";
	}
	
	echo <<<HTML
	  </tbody>
	</table>
  </div>
</div>
HTML;

}

if( $name != "" ) {
	
	echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_iptoolsname']}
  </div>
  <div class="panel-body">

HTML;
	
	$row = $db->super_query( "SELECT user_id, name, logged_ip FROM " . USERPREFIX . "_users WHERE name='" . $name . "'" );
	
	if( !$row['user_id'] ) {
		
		echo "<div class=\"text-center\"><b>" . $lang['user_nouser'] . "</b></div>";
	
	} else {
			$ip_link = <<<HTML
				<div class="btn-group">
				<a href="#" target="_blank" data-toggle="dropdown" class="status-info">{$row['logged_ip']}</a>
				  <ul class="dropdown-menu text-left">
				   <li><a href="https://www.nic.ru/whois/?searchWord={$row['logged_ip']}" target="_blank"><i class="fa fa-eye position-left"></i> {$lang['ip_info']}</a></li>
				   <li class="divider"></li>
				   <li><a href="?mod=blockip&ip={$row['logged_ip']}"><i class="fa fa-trash-o position-left text-danger"></i>{$lang['ip_ban']}</a></li>
				  </ul>
				</div>
HTML;
		
		echo $lang['user_name'] . " <b>" . $row['name'] . "</b><br /><br />" . $lang['opt_iptoollast'] . $ip_link."<br /><br />" . $lang['opt_iptoolcall'];
		
		$db->query( "SELECT ip FROM " . PREFIX . "_comments WHERE user_id = '{$row['user_id']}' GROUP BY ip" );
		
		$ip_list = array ();
		
		while ( $row = $db->get_array() ) {
		
			$ip_list[] = <<<HTML
				<div class="btn-group">
				<a href="#" target="_blank" data-toggle="dropdown" class="status-info">{$row['ip']}</a>
				  <ul class="dropdown-menu text-left">
				   <li><a href="https://www.nic.ru/whois/?searchWord={$row['ip']}" target="_blank"><i class="fa fa-eye position-left"></i> {$lang['ip_info']}</a></li>
				   <li class="divider"></li>
				   <li><a href="?mod=blockip&ip={$row['ip']}"><i class="fa fa-trash-o position-left text-danger"></i>{$lang['ip_ban']}</a></li>
				  </ul>
				</div>
HTML;
		}
		
		echo implode( ", ", $ip_list );
	}
	
	echo <<<HTML
   </div>
</div>
HTML;

}

echofooter();
?>