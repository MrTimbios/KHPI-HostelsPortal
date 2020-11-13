<?php
/*
=====================================================
DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004-2020 SoftNews Media Group
=====================================================
 File: editvote.php
-----------------------------------------------------
 Use: Votes manage
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['admin_editvote'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if( isset( $_REQUEST['id'] ) ) $id = intval( $_REQUEST['id'] ); else $id = "";

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));

$parse = new ParseFilter();
$parse->safe_mode = true;
$parse->filter_mode = false;

$stop = false;

if( $_GET['action'] == "delete" ) {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$db->query( "DELETE FROM " . PREFIX . "_vote WHERE id='$id'" );
	$db->query( "DELETE FROM " . PREFIX . "_vote_result WHERE vote_id='$id'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '27', '{$id}')" );

	@unlink( ENGINE_DIR . '/cache/system/vote.php' );
	msg( "success", $lang['vote_str_2'], $lang['vote_str_2'], "?mod=editvote" );

}
if( $_GET['action'] == "clear" ) {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$db->query( "UPDATE " . PREFIX . "_vote set vote_num='0' WHERE id='$id'" );
	$db->query( "DELETE FROM " . PREFIX . "_vote_result WHERE vote_id='$id'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '28', '{$id}')" );

	@unlink( ENGINE_DIR . '/cache/system/vote.php' );
	msg( "success", $lang['vote_clear3'], $lang['vote_clear3'], "?mod=editvote" );

}

if( $_GET['action'] == "off" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$db->query( "UPDATE " . PREFIX . "_vote set approve='0' WHERE id='$id'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '29', '{$id}')" );

	@unlink( ENGINE_DIR . '/cache/system/vote.php' );
}

if( $_GET['action'] == "on" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$db->query( "UPDATE " . PREFIX . "_vote set approve='1' WHERE id='$id'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '30', '{$id}')" );

	@unlink( ENGINE_DIR . '/cache/system/vote.php' );
}

if( $_GET['action'] == "doadd" ) {

	if( $_POST['user_hash'] == "" or $_POST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if ( trim($_POST['start_date']) ) {

		$start_date = @strtotime( $_POST['start_date'] );

		if ($start_date === - 1 OR !$start_date) $start_date = "";

	} else $start_date = "";

	if ( trim($_POST['end_date']) ) {

		$end_date = @strtotime( $_POST['end_date'] );

		if ($end_date === - 1 OR !$end_date) $end_date = "";

	} else $end_date = "";
	
	$category = $_POST['category'];

	if( !is_array($category) ) $category = array ();

	if( !count( $category ) ) {
		$category[] = 'all';
	}

	$category_list = array();

	foreach ( $category as $value ) {
		if ($value == "all") $category_list[] = $value; else $category_list[] = intval($value);
	}

	$category = $db->safesql( implode( ',', $category_list ) );

	$grouplevel = $_POST['grouplevel'];
	
	if( !count( $grouplevel ) ) {
		$grouplevel = array ();
		$grouplevel[] = 'all';
	}

	$g_list = array();

	foreach ( $grouplevel as $value ) {
		if ($value == "all") $g_list[] = $value; else $g_list[] = intval($value);
	}

	$grouplevel = $db->safesql( implode( ',', $g_list ) );
	
	$title = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['title'] ), false ) );
	$body = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['body'] ), false ) );
	
	$db->query( "INSERT INTO " . PREFIX . "_vote (category, vote_num, date, title, body, approve, start, end, grouplevel) VALUES ('$category', 0, CURRENT_DATE(), '$title', '$body', '1', '$start_date', '$end_date', '$grouplevel')" );
	@unlink( ENGINE_DIR . '/cache/system/vote.php' );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '2', '{$title}')" );

	msg( "success", $lang['vote_str_3'], $lang['vote_str_3'], "?mod=editvote" );

} elseif( $_GET['action'] == "update" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if ( trim($_POST['start_date']) ) {

		$start_date = @strtotime( $_POST['start_date'] );

		if ($start_date === - 1 OR !$start_date) $start_date = "";

	} else $start_date = "";

	if ( trim($_POST['end_date']) ) {

		$end_date = @strtotime( $_POST['end_date'] );

		if ($end_date === - 1 OR !$end_date) $end_date = "";

	} else $end_date = "";
	
	$category = $_POST['category'];
	
	if( !is_array($category) ) $category = array ();
	
	if( ! count( $category ) ) {
		$category[] = 'all';
	}

	$category_list = array();

	foreach ( $category as $value ) {
		if ($value == "all") $category_list[] = $value; else $category_list[] = intval($value);
	}

	$category = $db->safesql( implode( ',', $category_list ) );

	$grouplevel = $_POST['grouplevel'];
	
	if( !count( $grouplevel ) ) {
		$grouplevel = array ();
		$grouplevel[] = 'all';
	}

	$g_list = array();

	foreach ( $grouplevel as $value ) {
		if ($value == "all") $g_list[] = $value; else $g_list[] = intval($value);
	}

	$grouplevel = $db->safesql( implode( ',', $g_list ) );
	
	$title = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['title'] ), false ) );
	$body = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['body'] ), false ) );
	$id = intval( $_REQUEST['id'] );
	
	$db->query( "UPDATE " . PREFIX . "_vote SET category='$category', title='$title', body='$body', start='$start_date', end='$end_date', grouplevel='$grouplevel' WHERE id=$id" );
	@unlink( ENGINE_DIR . '/cache/system/vote.php' );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '3', '{$title}')" );

	msg( "success", $lang['vote_str_4'], $lang['vote_str_4'], "?mod=editvote" );

}

if( $_GET['action'] == "views" AND $_GET['id']) {

	$id = intval ($_GET['id']);

	$row = $db->super_query( "SELECT id, title, category, body, vote_num FROM " . PREFIX . "_vote WHERE id='$id'" );
		
	$title = stripslashes( $row['title'] );
	$body = stripslashes( $row['body'] );
	$body = str_replace( "<br />", "<br>", $body );
	$body = explode( "<br>", $body );
	$max = $row['vote_num'];


	$db->query( "SELECT answer, count(*) as count FROM " . PREFIX . "_vote_result WHERE vote_id='$id' GROUP BY answer" );
	
	$pn = 0;
	$entry = "";
	$answer = array ();
	
	while ( $row = $db->get_row() ) {
		$answer[$row['answer']]['count'] = $row['count'];
	}
	
	$db->free();

	for($i = 0; $i < sizeof( $body ); $i ++) {
			
		++ $pn;
		if( $pn > 5 ) $pn = 1;
			
		$num = $answer[$i]['count'];
		if( ! $num ) $num = 0;
		if( $max != 0 ) $proc = (100 * $num) / $max;
		else $proc = 0;
		$proc = round( $proc, 2 );
			
		$entry .= "<div align=\"left\">$body[$i] - $num ($proc%)</div><div class=\"voteprogress\" align=\"left\"><span class=\"vote{$pn}\" style=\"width:".intval($proc)."%;\">{$proc}%</span></div>\n";

	}

	if ( !$title ) $entry = $lang['vote_notfound'];

	$entry = "<div style=\"width:100%; max-width:500px;\">$entry</div>";

	echoheader( "<i class=\"fa fa-bar-chart position-left\"></i><span class=\"text-semibold\">{$lang['header_votes']}</span>", $lang['editvote'] );

echo <<<HTML
<style type="text/css">
.voteprogress {
  overflow: hidden;
  height: 15px;
  margin-bottom: 5px;
  background-color: #f7f7f7;
  background-image: -moz-linear-gradient(top, #f5f5f5, #f9f9f9);
  background-image: -ms-linear-gradient(top, #f5f5f5, #f9f9f9);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#f5f5f5), to(#f9f9f9));
  background-image: -webkit-linear-gradient(top, #f5f5f5, #f9f9f9);
  background-image: -o-linear-gradient(top, #f5f5f5, #f9f9f9);
  background-image: linear-gradient(top, #f5f5f5, #f9f9f9);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f5f5f5', endColorstr='#f9f9f9', GradientType=0);
  -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
  -moz-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
}

.voteprogress span {
  color: #ffffff;
  text-align: center;
  text-indent: -2000em;
  height: 15px;
  display: block;
  overflow: hidden;
  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
  background: #0e90d2;
  background-image: -moz-linear-gradient(top, #149bdf, #0480be);
  background-image: -ms-linear-gradient(top, #149bdf, #0480be);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#149bdf), to(#0480be));
  background-image: -webkit-linear-gradient(top, #149bdf, #0480be);
  background-image: -o-linear-gradient(top, #149bdf, #0480be);
  background-image: linear-gradient(top, #149bdf, #0480be);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#149bdf', endColorstr='#0480be', GradientType=0);
}

.voteprogress .vote2 {
  background-color: #dd514c;
  background-image: -moz-linear-gradient(top, #ee5f5b, #c43c35);
  background-image: -ms-linear-gradient(top, #ee5f5b, #c43c35);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee5f5b), to(#c43c35));
  background-image: -webkit-linear-gradient(top, #ee5f5b, #c43c35);
  background-image: -o-linear-gradient(top, #ee5f5b, #c43c35);
  background-image: linear-gradient(top, #ee5f5b, #c43c35);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ee5f5b', endColorstr='#c43c35', GradientType=0);
}

.voteprogress .vote3 {
  background-color: #5eb95e;
  background-image: -moz-linear-gradient(top, #62c462, #57a957);
  background-image: -ms-linear-gradient(top, #62c462, #57a957);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#62c462), to(#57a957));
  background-image: -webkit-linear-gradient(top, #62c462, #57a957);
  background-image: -o-linear-gradient(top, #62c462, #57a957);
  background-image: linear-gradient(top, #62c462, #57a957);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#62c462', endColorstr='#57a957', GradientType=0);
}

.voteprogress .vote4 {
  background-color: #4bb1cf;
  background-image: -moz-linear-gradient(top, #5bc0de, #339bb9);
  background-image: -ms-linear-gradient(top, #5bc0de, #339bb9);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#5bc0de), to(#339bb9));
  background-image: -webkit-linear-gradient(top, #5bc0de, #339bb9);
  background-image: -o-linear-gradient(top, #5bc0de, #339bb9);
  background-image: linear-gradient(top, #5bc0de, #339bb9);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#5bc0de', endColorstr='#339bb9', GradientType=0);
}

.voteprogress .vote5 {
  background-color: #faa732;
  background-image: -moz-linear-gradient(top, #fbb450, #f89406);
  background-image: -ms-linear-gradient(top, #fbb450, #f89406);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#fbb450), to(#f89406));
  background-image: -webkit-linear-gradient(top, #fbb450, #f89406);
  background-image: -o-linear-gradient(top, #fbb450, #f89406);
  background-image: linear-gradient(top, #fbb450, #f89406);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fbb450', endColorstr='#f89406', GradientType=0);
}
</style>
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['vote_result']}&nbsp;{$title}
  </div>
  <div class="panel-body">
	
		<div id="main_box" align="center"><br />{$entry}<br /><br />{$lang['vote_count']}&nbsp;{$max}<br /><br /> 
		<input id = "next_button" onclick="history.go(-1); return false;" class="btn bg-teal btn-sm btn-raised" type="button" value="{$lang['func_msg']}">
		</div>
	
   </div>
</div>
HTML;

	echofooter();

} elseif( $_GET['action'] == "edit" OR $_GET['action'] == "add" ) {

	echoheader( "<i class=\"fa fa-bar-chart position-left\"></i><span class=\"text-semibold\">{$lang['header_votes']}</span>", $lang['editvote'] );
	
	$canedit = false;
	$start_date = "";
	$stop_date  = "";
	

	if( ($_GET['action'] == "edit") && $id != '' ) {
		$canedit = true;
		$row = $db->super_query( "SELECT * FROM " . PREFIX . "_vote WHERE id='$id' LIMIT 0,1" );
		
		$title = $parse->decodeBBCodes( $row['title'], false );
		$body = $parse->decodeBBCodes( $row['body'], false );
		$icategory = explode( ',', $row['category'] );
		if( $row['category'] == "all" ) $all_cats = "selected";
		else $all_cats = "";

		if ( $row['start'] ) $start_date = @date( "Y-m-d H:i", $row['start'] );
		if ( $row['end'] )  $end_date  = @date( "Y-m-d H:i", $row['end'] );
		$groups = get_groups( explode( ',', $row['grouplevel'] ) );

		if( $row['grouplevel'] == "all" ) $check_all = "selected";
		else $check_all = "";
	
	} else {
		$canedit = false;
		$groups = get_groups();
		$check_all = "selected";
		$icategory = 0;
		$title = "";
		$body = "";
	}
	
	$opt_category = CategoryNewsSelection( $icategory, 0, FALSE );
	
	if( $canedit == false ) {
		echo "<form class=\"form-horizontal\" method=\"post\" action=\"?mod=editvote&action=doadd\" name=\"addvote\" onsubmit=\"if(document.addvote.title.value == '' || document.addvote.body.value == ''){DLEalert('{$lang['vote_alert']}', '{$lang['p_info']}');return false}\">";
		$button = "<input type=\"submit\" class=\"btn bg-teal btn-sm btn-raised\" value=\"{$lang['vote_new']}\">";
	} else {
		echo "<form class=\"form-horizontal\" method=\"post\" action=\"?mod=editvote&action=update&id={$id}\" name=\"addvote\" onsubmit=\"if(document.addvote.title.value == '' || document.addvote.body.value == ''){DLEalert('{$lang['vote_alert']}', '{$lang['p_info']}');return false}\">";
		$button = "<input type=\"submit\" class=\"btn bg-teal btn-sm btn-raised\" value=\"{$lang['vote_edit']}\">";
	
	}
	$user_group[$member_id['user_group']]['allow_image_upload'] =false;
	$user_group[$member_id['user_group']]['allow_file_upload'] =false;
	
	echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_votec']}
  </div>
  <div class="panel-body">
	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['vote_title']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" name="title" class="form-control width-500" value="{$title}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_vtitle']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['addnews_cat']}</label>
		  <div class="col-md-10 col-sm-9">
			<select data-placeholder="{$lang['addnews_cat_sel']}" name="category[]" class="cat_select" multiple>
				<option value="all" {$all_cats}>{$lang['edit_all']}</option>
				{$opt_category}
			</select><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_vcat']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['vote_startdate']}</label>
		  <div class="col-md-10 col-sm-9">
			<input data-rel="calendar" type="text" name="start_date" class="form-control" style="width:190px;" value="{$start_date}" autocomplete="off"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_vstart']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['vote_enddate']}</label>
		  <div class="col-md-10 col-sm-9">
			<input data-rel="calendar" type="text" name="end_date" class="form-control" style="width:190px;" value="{$end_date}" autocomplete="off"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_vend']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['vote_body']}<br /><span class="note large">{$lang['vote_str_1']}</span></label>
		  <div class="col-md-10 col-sm-9">
			<textarea class="classic width-500" style="height:200px;" name="body" id="body">{$body}</textarea>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['stat_allow']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="grouplevel[]" class="cat_select" data-placeholder=" " multiple><option value="all" {$check_all}>{$lang['edit_all']}</option>{$groups}</select>
		  </div>
		 </div>
	
   </div>
	<div class="panel-footer">
		{$button}
	</div>
</div>
<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
</form>
<script>
	$(function(){
		  $(".cat_select").chosen({allow_single_deselect:true, no_results_text: '{$lang['addnews_cat_fault']}'});
	});
</script>
HTML;
	
	echofooter();

} else {

echoheader( "<i class=\"fa fa-bar-chart position-left\"></i><span class=\"text-semibold\">{$lang['header_votes']}</span>", $lang['editvote'] );


echo "
 <script language=\"javascript\">
 <!-- begin
    function confirmdelete(id){
	    DLEconfirm( '{$lang['vote_confirm']}', '{$lang['p_confirm']}', function () {
			document.location=\"?mod=editvote&action=delete&user_hash={$dle_login_hash}&id=\"+id;
		} );
    }
    function confirmclear(id){
	    DLEconfirm( '{$lang['vote_clear']}', '{$lang['p_confirm']}', function () {
			document.location=\"?mod=editvote&action=clear&user_hash={$dle_login_hash}&id=\"+id;
		} );
    }
 // end -->
 </script>";

$db->query( "SELECT * FROM " . PREFIX . "_vote ORDER BY id DESC" );

$entries = "";
if( !$langformatdate ) $langformatdate = "d.m.Y";
if( !$langformatdatefull ) $langformatdatefull = "d.m.Y H:i";

while ( $row = $db->get_row() ) {
	
	$item_id = $row['id'];
	$item_date = date( $langformatdate, strtotime( $row['date'] ) );
	$title = stripslashes( $row['title'] );

	if ( $row['start'] ) $start_date = date( $langformatdatefull, $row['start'] ); else $start_date = "--";
	if ( $row['end'] ) $end_date = date( $langformatdatefull, $row['end'] ); else $end_date = "--";
	
	if( dle_strlen( $title, $config['charset'] ) > 74 ) {
		$title = dle_substr( $title, 0, 70, $config['charset'] ) . " ...";
	}
	
	$item_num = $row['vote_num'];
	if( empty( $row['category'] ) ) {
		$my_cat = "---";
	} elseif( $row['category'] == "all" ) {
		$my_cat = $lang['edit_all'];
	} else {
		
		$my_cat = array ();
		$cat_list = explode( ',', $row['category'] );
		
		foreach ( $cat_list as $element ) {
			if( $element AND $cat_info[$element]['name'] ) $my_cat[] = $cat_info[$element]['name'];
		}
		
		if( count($my_cat) ) $my_cat = implode( ',<br />', $my_cat );
		else $my_cat = "---";
	}
	
	if( $row['approve'] ) {
		$status = "<span title=\"{$lang['led_on_title']}\" class=\"text-success tip\"><b><i class=\"fa fa-check-circle\"></i></b></span>";
		$led_action = "off";
		$lang['led_title'] = $lang['vote_aus'];		
	} else {
		$status = "<span title=\"{$lang['led_off_title']}\" class=\"text-danger tip\"><b><i class=\"fa fa-exclamation-circle\"></i></b></span>";
		$lang['led_title'] = $lang['vote_ein'];
		$led_action = "on";
	}

		$menu_link = <<<HTML
        <div class="btn-group">
          <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
          <ul class="dropdown-menu text-left pull-right">
            <li><a href="?mod=editvote&action=views&id={$item_id}"><i class="fa fa-eye position-left"></i>{$lang['vote_view']}</a></li>
            <li><a href="?mod=editvote&action=edit&id={$item_id}"><i class="fa fa-pencil-square-o position-left"></i>{$lang['word_ledit']}</a></li>
            <li><a href="?mod=editvote&action={$led_action}&user_hash={$dle_login_hash}&id={$item_id}"><i class="fa fa-magic position-left"></i>{$lang['led_title']}</a></li>
			<li><a onclick="javascript:confirmclear('{$item_id}'); return(false);" href="#"><i class="fa fa-retweet position-left"></i>{$lang['vote_clear2']}</a></li>
			<li class="divider"></li>
            <li><a onclick="javascript:confirmdelete('{$item_id}'); return(false);" href="#"><i class="fa fa-trash-o position-left text-danger"></i>{$lang['cat_del']}</a></li>
          </ul>
        </div>
HTML;
	
	$entries .= "
   <tr>
    <td class=\"cursor-pointer\" onclick=\"document.location = '?mod=editvote&action=edit&id={$item_id}'; return false;\">{$item_date}&nbsp;-&nbsp;<a class=\"tip\" title='{$lang['word_ledit']}' href=\"?mod=editvote&action=edit&id={$item_id}\">{$title}</a></td>
    <td class=\"cursor-pointer text-center hidden-xs\" onclick=\"document.location = '?mod=editvote&action=edit&id={$item_id}'; return false;\">{$start_date}</td>
    <td class=\"cursor-pointer text-center hidden-xs\" onclick=\"document.location = '?mod=editvote&action=edit&id={$item_id}'; return false;\">{$end_date}</td>
    <td class=\"cursor-pointer text-center\" onclick=\"document.location = '?mod=editvote&action=edit&id={$item_id}'; return false;\">{$status}</td>
    <td class=\"cursor-pointer text-center hidden-xs\" onclick=\"document.location = '?mod=editvote&action=edit&id={$item_id}'; return false;\">{$row['vote_num']}</td>
    <td class=\"cursor-pointer text-center hidden-xs\" onclick=\"document.location = '?mod=editvote&action=edit&id={$item_id}'; return false;\">{$my_cat}</td>
    <td align=\"center\">{$menu_link}</td>
     </tr>";
}
$db->free();

if( empty( $entries ) ) {
	$entries = "<tr><td colspan=\"7\" align=\"center\" height=\"40\">" . $lang['vote_nodata'] . "</td></tr>";
}

echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_votec']}
  </div>
  <div class="table-responsive">

    <table class="table table-xs table-hover">
      <thead>
      <tr>
        <th>{$lang['edit_title']}</th>
        <th class="text-center hidden-xs">{$lang['vote_startinfo']}</th>
        <th class="text-center hidden-xs">{$lang['vote_endinfo']}</th>
        <th class="text-center">{$lang['led_status']}</th>
        <th class="text-center hidden-xs">{$lang['vote_count']}</th>
		<th class="text-center hidden-xs">{$lang['edit_cl']}</th>
        <th style="width: 80px">&nbsp;</th>
      </tr>
      </thead>
	  <tbody>
		{$entries}
	  </tbody>
	</table>
	
   </div>
	<div class="panel-footer">
		<input onclick="document.location='?mod=editvote&action=add'" type="button" class="btn bg-teal btn-sm btn-raised" value="{$lang['poll_new']}">
	</div>	
</div>
HTML;

echofooter();

}
?>