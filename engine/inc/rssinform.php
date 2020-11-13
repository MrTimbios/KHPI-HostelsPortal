<?php
/*
=====================================================
DataLife Engine - by SoftNews Media Group
-----------------------------------------------------
http://dle-news.ru/
-----------------------------------------------------
Copyright (c) 2004-2020 SoftNews Media Group
=====================================================
File: rssinform.php
-----------------------------------------------------
Use: RSS informers
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['admin_rssinform'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if( isset( $_REQUEST['id'] ) ) $id = intval( $_REQUEST['id'] ); else $id = "";

if( $_REQUEST['action'] == "doadd" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$rss_tag = totranslit( strip_tags( trim( $_POST['rss_tag'] ) ) );
	$rss_descr = $db->safesql( strip_tags( trim( $_POST['rss_descr'] ) ) );
	$rss_template = totranslit( strip_tags( trim( $_POST['rss_template'] ) ) );
	$rss_max = intval( $_POST['rss_max'] );
	$rss_tmax = intval( $_POST['rss_tmax'] );
	$rss_dmax = intval( $_POST['rss_dmax'] );
	$rss_date_format = $db->safesql( strip_tags( trim( $_POST['rss_date_format'] ) ) );

	
	$rss_url = str_replace("\r", "", $_POST['rss_url']);
	$rss_url = str_replace("\n", "", $rss_url);
	$rss_url = htmlspecialchars( $rss_url, ENT_QUOTES, $config['charset'] );
	$rss_url = str_replace ( "&amp;", "&", $rss_url );
	$rss_url = preg_replace( "/javascript:/i", "j&#1072;vascript:", $rss_url );
	
	$rss_url = $db->safesql( trim( $rss_url ) );
	
	$category = $_POST['category'];

	if( !count( $category ) ) {
		$category = array ();
		$category[] = '0';
	}

	$category_list = array();

	foreach ( $category as $value ) {
		$category_list[] = intval($value);
	}

	$category = $db->safesql( implode( ',', $category_list ) );
	
	if( $rss_tag == "" or $rss_descr == "" or $rss_url == "" or $rss_template == "" ) msg( "error", $lang['addnews_error'], $lang['addnews_erstory'], "javascript:history.go(-1)" );
	
	$db->query( "INSERT INTO " . PREFIX . "_rssinform (tag, descr, category, url, template, news_max, tmax, dmax, rss_date_format) values ('$rss_tag', '$rss_descr', '$category', '$rss_url', '$rss_template', '$rss_max', '$rss_tmax', '$rss_dmax', '$rss_date_format')" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '53', '{$rss_tag}')" );

	@unlink( ENGINE_DIR . '/cache/system/informers.php' );
	clear_cache();
	header( "Location: ?mod=rssinform" );

}

if( $_REQUEST['action'] == "doedit" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$rss_tag = totranslit( strip_tags( trim( $_POST['rss_tag'] ) ) );
	$rss_descr = $db->safesql( strip_tags( trim( $_POST['rss_descr'] ) ) );
	$rss_template = totranslit( strip_tags( trim( $_POST['rss_template'] ) ) );
	$rss_max = intval( $_POST['rss_max'] );
	$rss_tmax = intval( $_POST['rss_tmax'] );
	$rss_dmax = intval( $_POST['rss_dmax'] );
	$rss_date_format = $db->safesql( strip_tags( trim( $_POST['rss_date_format'] ) ) );

	$rss_url = str_replace("\r", "", $_POST['rss_url']);
	$rss_url = str_replace("\n", "", $rss_url);
	$rss_url = htmlspecialchars( $rss_url, ENT_QUOTES, $config['charset'] );
	$rss_url = str_replace ( "&amp;", "&", $rss_url );
	$rss_url = preg_replace( "/javascript:/i", "j&#1072;vascript:", $rss_url );
	
	$rss_url = $db->safesql( trim( $rss_url ) );
	
	$category = $_POST['category'];

	if( !count( $category ) ) {
		$category = array ();
		$category[] = '0';
	}

	$category_list = array();

	foreach ( $category as $value ) {
		$category_list[] = intval($value);
	}

	$category = $db->safesql( implode( ',', $category_list ) );
	
	if( $rss_tag == "" or $rss_descr == "" or $rss_url == "" or $rss_template == "" ) msg( "error", $lang['addnews_error'], $lang['addnews_erstory'], "javascript:history.go(-1)" );
	
	$db->query( "UPDATE " . PREFIX . "_rssinform SET tag='$rss_tag', descr='$rss_descr', category='$category', url='$rss_url', template='$rss_template', news_max='$rss_max', tmax='$rss_tmax', dmax='$rss_dmax', rss_date_format='$rss_date_format' WHERE id='$id'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '54', '{$rss_tag}')" );

	@unlink( ENGINE_DIR . '/cache/system/informers.php' );
	clear_cache();
	header( "Location: ?mod=rssinform" );
}

if( $_GET['action'] == "off" AND $id) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$db->query( "UPDATE " . PREFIX . "_rssinform set approve='0' WHERE id='$id'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '55', '{$id}')" );

	@unlink( ENGINE_DIR . '/cache/system/informers.php' );
	clear_cache();
}

if( $_GET['action'] == "on" AND $id) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$db->query( "UPDATE " . PREFIX . "_rssinform set approve='1' WHERE id='$id'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '56', '{$id}')" );

	@unlink( ENGINE_DIR . '/cache/system/informers.php' );
	clear_cache();
}

if( $_GET['action'] == "delete" AND $id) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$db->query( "DELETE FROM " . PREFIX . "_rssinform WHERE id='$id'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '57', '{$id}')" );

	@unlink( ENGINE_DIR . '/cache/system/informers.php' );
	clear_cache();
}

if( $_REQUEST['action'] == "add" or $_REQUEST['action'] == "edit" ) {
	
	if( $_REQUEST['action'] == "add" ) {
		$doaction = "doadd";
		$all_cats = "selected";
		$rss_max = "5";
		$rss_tmax = 0;
		$rss_dmax = 200;
		$rss_template = "informer";
		$rss_date_format = "j F Y H:i";
	
	} else {
		
		$row = $db->super_query( "SELECT * FROM " . PREFIX . "_rssinform WHERE id='$id' LIMIT 0,1" );
		$rss_tag = $row['tag'];
		$rss_descr = htmlspecialchars( stripslashes( $row['descr'] ), ENT_QUOTES, $config['charset'] );
		$rss_url = htmlspecialchars( stripslashes( $row['url'] ), ENT_QUOTES, $config['charset'] );
		$rss_template = htmlspecialchars( stripslashes( $row['template'] ), ENT_QUOTES, $config['charset'] );
		$rss_max = $row['news_max'];
		$rss_tmax = $row['tmax'];
		$rss_dmax = $row['dmax'];
		$rss_date_format = $row['rss_date_format'];
		$doaction = "doedit";
	}
	
	$opt_category = CategoryNewsSelection( explode( ',', $row['category'] ), 0, FALSE );
	if( ! $row['category'] ) $all_cats = "selected";
	else $all_cats = "";
	
	echoheader( "<i class=\"fa fa-rss-square position-left\"></i><span class=\"text-semibold\">{$lang['opt_rssinform']}</span>", $lang['header_rs_2'] );
	
	echo <<<HTML
<form action="" method="post" class="form-horizontal">
<input type="hidden" name="mod" value="rssinform">
<input type="hidden" name="action" value="{$doaction}">
<input type="hidden" name="user_hash" value="$dle_login_hash" />
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_rssinform']}
  </div>
  <div class="panel-body">

		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rssinform_xname']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-200" maxlength="40" type="text" name="rss_tag" value="{$rss_tag}" /><span class="text-muted text-size-small position-right">({$lang['xf_lat']})</span>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rssinform_xdescr']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" maxlength="250" type="text" name="rss_descr" value="{$rss_descr}" />
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['addnews_cat']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="category[]" class="categoryselect" data-placeholder="{$lang['addnews_cat_sel']}" title="{$lang['addnews_cat_sel']}" multiple>
   <option value="0" {$all_cats}>{$lang['edit_all']}</option>
   {$opt_category}
   </select>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rssinform_url']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" maxlength="250" type="text" name="rss_url" value="{$rss_url}" />
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['opt_sys_an']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-200" maxlength="20" type="text" name="rss_date_format" value="{$rss_date_format}" /> <a onclick="javascript:Help('date'); return false;" href="#">{$lang['opt_sys_and']}</a>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rssinform_template']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-200" maxlength="40" type="text" name="rss_template" value="{$rss_template}" /> .tpl
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rssinform_max']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control" style="width: 60px;" type="text" name="rss_max" value="{$rss_max}" /><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_ri_max']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rssinform_tmax']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control" style="width: 60px;" type="text" name="rss_tmax" value="{$rss_tmax}" /><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_ri_tmax']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rssinform_dmax']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control" style="width: 60px;" type="text" name="rss_dmax" value="{$rss_dmax}" /><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_ri_dmax']}" ></i>
		  </div>
		 </div>
	
   </div>
	<div class="panel-footer">
		<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
	</div>
</div>
</form>
<script>
	$(function(){

		$('.categoryselect').chosen({no_results_text: '{$lang['addnews_cat_fault']}'});

	});
</script>
HTML;
	
	echofooter();

} else {
	
	echoheader( "<i class=\"fa fa-rss-square position-left\"></i><span class=\"text-semibold\">{$lang['opt_rssinform']}</span>", $lang['header_rs_2'] );
	
	$db->query( "SELECT * FROM " . PREFIX . "_rssinform ORDER BY id ASC" );
	
	$entries = "";
	
	if( !$config['rss_informer'] ) $offline = "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['module_disabled']}</div>";
	else $offline = "";
	
	while ( $row = $db->get_row() ) {
		
		$row['descr'] = htmlspecialchars( stripslashes( $row['descr'] ), ENT_QUOTES, $config['charset'] );
		$row['tag'] = "{inform_" . $row['tag'] . "}";
		
		if( $row['approve'] ) {
			$status = "<span title=\"{$lang['rssinform_on']}\" class=\"text-success tip\"><b><i class=\"fa fa-check-circle\"></i></b></span>";
			$lang['led_active'] = $lang['banners_aus'];
			$led_action = "off";
		} else {
			$status = "<span title=\"{$lang['rssinform_off']}\" class=\"text-danger tip\"><b><i class=\"fa fa-exclamation-circle\"></i></b></span>";
			$lang['led_active'] = $lang['rssinform_ein'];
			$led_action = "on";
		}

		$menu_link = <<<HTML
        <div class="btn-group">
         <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
          <ul class="dropdown-menu text-left dropdown-menu-right">
            <li><a onclick="document.location='?mod=rssinform&user_hash={$dle_login_hash}&action={$led_action}&id={$row['id']}'; return(false)" href="#"><i class="fa fa-eye position-left"></i>{$lang['led_active']}</a></li>
            <li><a onclick="document.location='?mod=rssinform&user_hash={$dle_login_hash}&action=edit&id={$row['id']}'; return(false)" href="#"><i class="fa fa-pencil-square-o position-left"></i>{$lang['group_sel1']}</a></li>
			<li class="divider"></li>
            <li><a onclick="javascript:confirmdelete('{$row['id']}'); return(false);" href="#"><i class="fa fa-trash-o position-left text-danger"></i> {$lang['cat_del']}</a></li>
          </ul>
        </div>
HTML;

		
		$entries .= "
	   <tr>
		<td class=\"cursor-pointer\" onclick=\"document.location = '?mod=rssinform&user_hash={$dle_login_hash}&action=edit&id={$row['id']}'; return false;\">{$row['tag']}</td>
		<td class=\"cursor-pointer\" onclick=\"document.location = '?mod=rssinform&user_hash={$dle_login_hash}&action=edit&id={$row['id']}'; return false;\">{$row['descr']}</td>
		<td class=\"cursor-pointer\" onclick=\"document.location = '?mod=rssinform&user_hash={$dle_login_hash}&action=edit&id={$row['id']}'; return false;\">{$row['template']}.tpl</td>
		<td class=\"cursor-pointer text-center\" onclick=\"document.location = '?mod=rssinform&user_hash={$dle_login_hash}&action=edit&id={$row['id']}'; return false;\">{$status}</td>
		<td>{$menu_link}</td>
		 </tr>";
	}
	$db->free();
	
	echo <<<HTML
<script>
<!--
function confirmdelete(id){
	    DLEconfirm( '{$lang['rssinform_del']}', '{$lang['p_confirm']}', function () {
			document.location="?mod=rssinform&user_hash={$dle_login_hash}&action=delete&id="+id;
		} );
}
//-->
</script>
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['rssinform_title']}
  </div>
  <div class="table-responsive">

    <table class="table table-xs table-hover">
      <thead>
      <tr>
        <th style="width: 200px">{$lang['banners_tag']}</th>
        <th>{$lang['static_descr']}</th>
        <th>{$lang['rssinform_template']}</th>
		<th style="width: 150px">&nbsp;</th>
        <th style="width: 70px">&nbsp;</th>
      </tr>
      </thead>
	  <tbody>
		{$entries}
	  </tbody>
	</table>
	
   </div>
	<div class="panel-footer">
	<button class="btn bg-teal btn-sm btn-raised" type="button" onclick="document.location='?mod=rssinform&action=add'"><i class="fa fa-plus-circle position-left"></i>{$lang['rssinform_create']}</button>
	</div>	
</div>
{$offline}
HTML;
	
	echofooter();

}
?>