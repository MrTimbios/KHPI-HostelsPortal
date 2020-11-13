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
 File: rss.php
-----------------------------------------------------
 Use: RSS import
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['admin_rss'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if( isset( $_REQUEST['id'] ) ) $id = intval( $_REQUEST['id'] ); else $id = "";


if( $_GET['subaction'] == "clear" ) {

	$lastdate = intval( $_GET['lastdate'] );
	if( $id and $lastdate ) $db->query( "UPDATE " . PREFIX . "_rss SET lastdate='$lastdate' WHERE id='$id'" );

}

if( $_REQUEST['action'] == "addnews" ) {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));
	
	$parse = new ParseFilter();
	
	$allow_comm = intval( $_POST['allow_comm'] );
	$allow_main = intval( $_POST['allow_main'] );
	$allow_rating = intval( $_POST['allow_rating'] );
	$news_fixed = 0;
	$allow_br = intval( $_POST['text_type'] );
	$lastdate = intval( $_POST['lastdate'] );
	
	if( count( $_POST['content'] ) ) {
		
		foreach ( $_POST['content'] as $content ) {
			$approve = intval( $content['approve'] );
			
			if( ! count( $content['category'] ) ) {
				$content['category'] = array ();
				$content['category'][] = '0';
			}

			$category_list = array();
		
			foreach ( $content['category'] as $value ) {
				$category_list[] = intval($value);
			}
		
			$category_list = $db->safesql( implode( ',', $category_list ) );
			
			$full_story = $parse->process( $content['full'] );
			$short_story = $parse->process( $content['short'] );
			$title = $parse->process(  trim( strip_tags ($content['title']) ) );
			$_POST['title'] = $title;
			$alt_name = totranslit( stripslashes( $title ) );
			$title = $db->safesql( $title );
			
			if( ! $allow_br ) {
				$full_story = $db->safesql( $parse->BB_Parse( $full_story ) );
				$short_story = $db->safesql( $parse->BB_Parse( $short_story ) );
			} else {
				$full_story = $db->safesql( $parse->BB_Parse( $full_story, false ) );
				$short_story = $db->safesql( $parse->BB_Parse( $short_story, false ) );
			}
			
			$metatags = create_metatags( $short_story . $full_story );
			$thistime = date( "Y-m-d H:i:s", strtotime( $content['date'] ) );
			
			if( trim( $title ) == "" ) {
				msg( "error", $lang['addnews_error'], $lang['addnews_ertitle'], "javascript:history.go(-1)" );
			}
			if( trim( $short_story ) == "" ) {
				msg( "error", $lang['addnews_error'], $lang['addnews_erstory'], "javascript:history.go(-1)" );
			}
			
			$db->query( "INSERT INTO " . PREFIX . "_post (date, autor, short_story, full_story, xfields, title, descr, keywords, category, alt_name, allow_comm, approve, allow_main, allow_br) values ('$thistime', '{$member_id['name']}', '$short_story', '$full_story', '', '$title', '{$metatags['description']}', '{$metatags['keywords']}', '$category_list', '$alt_name', '$allow_comm', '$approve', '$allow_main', '$allow_br')" );

			$row = $db->insert_id();
			$db->query( "INSERT INTO " . PREFIX . "_post_extras (news_id, allow_rate, votes, user_id) VALUES('{$row}', '$allow_rating', '0', '{$member_id['user_id']}')" );


			$db->query( "UPDATE " . USERPREFIX . "_users set news_num=news_num+1 where user_id='{$member_id['user_id']}'" );
			$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '1', '{$title}')" );
		
		}
		
		if( $id and $lastdate ) $db->query( "UPDATE " . PREFIX . "_rss SET lastdate='$lastdate' WHERE id='$id'" );
		
		clear_cache();
		msg( "success", $lang['addnews_ok'], $lang['rss_added'], "?mod=rss" );
	
	}
	
	msg( "error", $lang['addnews_error'], $lang['rss_notadded'], "?mod=rss" );

} elseif( $_REQUEST['action'] == "news" and $id ) {
	
	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/rss.class.php'));
	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));
	
	$parse = new ParseFilter();
	$parse->leech_mode = true;
	
	$rss = $db->super_query( "SELECT * FROM " . PREFIX . "_rss WHERE id='$id'" );
	
	$xml = new xmlParser( stripslashes( $rss['url'] ), $rss['max_news'] );
	
	$xml->pre_lastdate = $rss['lastdate'];
	
	$xml->pre_parse( $rss['date'] );
	
	$i = 0;

	foreach ( $xml->content as $content ) {
		if( $rss['text_type'] ) {
			
			$xml->content[$i]['title'] = $parse->decodeBBCodes( strip_tags($xml->content[$i]['title']), false );
			$xml->content[$i]['description'] = $parse->decodeBBCodes( $xml->content[$i]['description'], false );
			$xml->content[$i]['date'] = date( "Y-m-d H:i:s", $xml->content[$i]['date'] );
			
			if($xml->content[$i]['image']) $xml->content[$i]['description'] = "[img]".$xml->content[$i]['image']."[/img]\n".$xml->content[$i]['description'];

		} else {
			
			if($xml->content[$i]['image']) $xml->content[$i]['description'] = "<img src=\"".$xml->content[$i]['image']."\">\n".$xml->content[$i]['description'];
			
			$xml->content[$i]['title'] = $parse->decodeBBCodes( strip_tags($xml->content[$i]['title']), false );
			$xml->content[$i]['description'] = $parse->decodeBBCodes( $xml->content[$i]['description'], true, "yes" );
			$xml->content[$i]['date'] = date( "Y-m-d H:i:s", $xml->content[$i]['date'] );
		}
		$i ++;
	}
	
	echoheader( "<i class=\"fa fa-rss-square position-left\"></i><span class=\"text-semibold\">{$lang['opt_rss']}</span>", $lang['header_rs_1'] );
	
	echo <<<HTML
<script>

	function doFull( link, news_id, rss_id )
	{

		ShowLoading('');

		$.post('engine/ajax/controller.php?mod=rss', { link: link, news_id: news_id, rss_id: rss_id, user_hash: '{$dle_login_hash}', rss_charset: "{$xml->rss_charset}" }, function(data){
	
			HideLoading('');
	
			$('#cfull'+ news_id).html(data);
	
		});

	return false;
	}

	function RemoveTable( nummer ) {
	    DLEconfirm( '{$lang['edit_cdel']}', '{$lang['p_confirm']}', function () {
			document.getElementById('ContentTable' + nummer).innerHTML = '';
		} );
	}

	function preview( id )
	{
        dd=window.open('','prv','height=400,width=750,resizable=1,scrollbars=1');
        document.addnews.target='prv';
		document.addnews.title.value = document.getElementById('title_' + id).value;
		document.addnews.short_story.value = document.getElementById('short_' + id).value;
		if (document.getElementById('full_' + id)) {
		document.addnews.full_story.value = document.getElementById('full_' + id).value;
		} else {
		document.addnews.full_story.value = "";
		}
        document.addnews.submit();
    }
	$(function(){

		$('.categoryselect').chosen({no_results_text: '{$lang['addnews_cat_fault']}'});

	});
</script>
<form method=post name="addnewsrss" action="?mod=rss&action=addnews">
<div class="panel panel-default">
  <div class="panel-heading">
    {$rss['url']}
  </div>
  <div class="table-responsive">
HTML;
	
	$i = 0;
	$categories_list = CategoryNewsSelection( $rss['category'], 0 );
	
	if( count( $xml->content ) ) {
		foreach ( $xml->content as $content ) {
			
			echo '<span id="ContentTable' . $i . '"><table class="table form-horizontal"><tr><td>
    <b><a onclick="RemoveTable(' . $i . '); return false;" href="#" ><i class="fa fa-trash-o position-left text-danger"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:ShowOrHide(\'cp' . $i . '\',\'cc' . $i . '\')" >' . $content['title'] . '</a></td>
    </tr>
    <tr id=\'cp' . $i . '\' style=\'display:none\'>
    <td><div class="form-group">
	<label class="control-label col-md-2">' .$lang['addnews_title']. '</label>
	<div class="col-md-10"><input type="text"class="form-control width-550" id="title_' . $i . '" name="content[' . $i . '][title]" value="' . $content['title'] . '"></div>
	</div>
	<div class="form-group">
	<label class="control-label col-md-2">' .$lang['addnews_date']. '</label>
	<div class="col-md-10"><input class="form-control" autocomplete="off" style="width:190px;" data-rel="calendar" type="text" name="content[' . $i . '][date]" value="' . $content['date'] . '"></div>
	</div>
	<div class="form-group">
	<label class="control-label col-md-2">' .$lang['addnews_cat']. '</label>
	<div class="col-md-10"><select data-placeholder="' .$lang['addnews_cat_sel']. '" title="' .$lang['addnews_cat_sel']. '" name="content[' . $i . '][category][]" id="category" class="categoryselect" multiple>' . $categories_list . '</select></div>
	</div>
	</td>
    </tr>
    <tr id=\'cc' . $i . '\' style=\'display:none\'>
    <td>
    <textarea class="classic" style="width:100%;max-width:950px;height:200px;" id="short_' . $i . '" name="content[' . $i . '][short]">' . $content['description'] . '</textarea>
	<div id="cfull' . $i . '">' . htmlspecialchars( $content['link'], ENT_QUOTES, $config['charset'] ) . '</div>
	<div class="checkbox"><label><input class="icheck" type="checkbox" name="content[' . $i . '][approve]" id="content[' . $i . '][approve]" value="1" checked>' . $lang['addnews_mod'] . '</label></div>
	<br /><input onclick="doFull(\'' . urlencode( rtrim( $content['link'] ) ) . '\', \'' . $i . '\', \'' . $rss['id'] . '\')" type="button" class="btn bg-teal btn-sm btn-raised position-left" value="' . $lang['rss_dofull'] . '"><input onclick="preview(' . $i . ')" type="button" class="btn bg-slate-600 btn-sm btn-raised position-left" value="' . $lang['btn_preview'] . '"><input onclick="RemoveTable(' . $i . '); return false;" type="button" class="btn bg-danger btn-sm btn-raised" value="' . $lang['edit_dnews'] . '"><br /><br />
  </tr></table></span>';
			
			$i ++;
		}
		
		echo <<<HTML
    <div class="panel-footer"><button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['rss_addnews']}</button>
	<button onclick="document.location='?mod=rss&action=news&subaction=clear&id={$id}&lastdate={$xml->lastdate}'" type="button" class="btn bg-danger btn-sm btn-raised position-left"><i class="fa fa-trash-o"></i>{$lang['rss_clear']}</button>
	<input type="hidden" name="allow_main" value="{$rss['allow_main']}">
	<input type="hidden" name="allow_rating" value="{$rss['allow_rating']}">
	<input type="hidden" name="allow_comm" value="{$rss['allow_comm']}">
	<input type="hidden" name="lastdate" value="{$xml->lastdate}">
	<input type="hidden" name="id" value="{$id}">
	<input type="hidden" name="user_hash" value="$dle_login_hash" />
	<input type="hidden" name="text_type" value="{$rss['text_type']}">
	</div>	
HTML;
	
	} else {
		
		echo "<div style=\"padding:10px;\" align=\"center\">" . $lang['rss_no_rss'] . "<br /><br><a class=\"btn bg-teal btn-sm btn-raised\" href=\"?mod=rss\">{$lang['func_msg']}</a></div>";
	
	}
	
	echo <<<HTML
   </div>
</div></form>

<form method="post" name="addnews" id="addnews">
<input type="hidden" name="mod" value="preview">
<input type="hidden" name="title" value="">
<input type="hidden" name="short_story" value="">
<input type="hidden" name="full_story" value="">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<input type="hidden" name="allow_br" value="{$rss['text_type']}">
</form>
HTML;
	
	echofooter();

} elseif( $_REQUEST['action'] == "doadd" or $_REQUEST['action'] == "doedit" ) {
	
	if( $_POST['user_hash'] == "" or $_POST['user_hash'] != $dle_login_hash ) {

		die( "Hacking attempt! User not found" );
		
	}
	
	$url = str_replace("\r", "", $_POST['rss_url']);
	$url = str_replace("\n", "", $url);
	$url = htmlspecialchars( $url, ENT_QUOTES, $config['charset'] );
	$url = str_replace ( "&amp;", "&", $url );
	$url = preg_replace( "/javascript:/i", "j&#1072;vascript:", $url );
	$url = preg_replace( "/data:/i", "da&#1072;ta:", $url );

	$url = $db->safesql( trim( $url ) );
	$description = $db->safesql( trim( $_POST['rss_descr'] ) );
	
	$max_news = intval( $_POST['rss_maxnews'] );
	$allow_main = intval( $_POST['allow_main'] );
	$allow_rating = intval( $_POST['allow_rating'] );
	$allow_comm = intval( $_POST['allow_comm'] );
	$text_type = intval( $_POST['text_type'] );
	$date = intval( $_POST['rss_date'] );
	$category = intval( $_POST['category'] );
	
	$search = $db->safesql( trim( $_POST['rss_search'] ) );
	$cookies = $db->safesql( trim( $_POST['rss_cookie'] ) );
	
	if( $url == "" ) msg( "error", $lang['addnews_error'], $lang['rss_err1'], "javascript:history.go(-1)" );
	
	if( $_REQUEST['action'] == "doadd" ) {
		$db->query( "INSERT INTO " . PREFIX . "_rss (url, description, allow_main, allow_rating, allow_comm, text_type, date, search, max_news, cookie, category) values ('$url', '$description', '$allow_main', '$allow_rating', '$allow_comm', '$text_type', '$date', '$search', '$max_news', '$cookies', '$category')" );
		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '51', '{$url}')" );
		msg( "success", $lang['all_info'], $lang['rss_ok1'], "?mod=rss" );
	} else {
		$db->query( "UPDATE " . PREFIX . "_rss set url='$url', description='$description', allow_main='$allow_main', allow_rating='$allow_rating', allow_comm='$allow_comm', text_type='$text_type', date='$date', search='$search', max_news='$max_news', cookie='$cookies', category='$category', lastdate='0' WHERE id='{$id}'" );
		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '52', '{$url}')" );
		msg( "success", $lang['all_info'], $lang['rss_ok2'], "?mod=rss" );
	}

} elseif( $_REQUEST['action'] == "add" or $_REQUEST['action'] == "edit" ) {
	
	function makeDropDown($options, $name, $selected) {
		$output = "<select class=\"uniform\" style=\"min-width:100px;\" name=\"$name\">\r\n";
		foreach ( $options as $value => $description ) {
			$output .= "<option value=\"$value\"";
			if( $selected == $value ) {
				$output .= " selected ";
			}
			$output .= ">$description</option>\n";
		}
		$output .= "</select>";
		return $output;
	}
	
	echoheader( "<i class=\"fa fa-rss-square position-left\"></i><span class=\"text-semibold\">{$lang['opt_rss']}</span>", $lang['header_rs_1'] );;
	
	if( $action == "add" ) {
		
		$rss_date = makeDropDown( array ("1" => $lang['rss_date_1'], "0" => $lang['rss_date_2'] ), "rss_date", "1" );
		$text_type = makeDropDown( array ("1" => "BBCODES", "0" => "HTML" ), "text_type", "1" );
		
		$allow_main = makeDropDown( array ("1" => $lang['opt_sys_yes'], "0" => $lang['opt_sys_no'] ), "allow_main", "1" );
		$allow_rating = makeDropDown( array ("1" => $lang['opt_sys_yes'], "0" => $lang['opt_sys_no'] ), "allow_rating", "1" );
		$allow_comm = makeDropDown( array ("1" => $lang['opt_sys_yes'], "0" => $lang['opt_sys_no'] ), "allow_comm", "1" );
		
		$rss_search_value = "<html>{get}</html>";
		$rss_maxnews_value = 5;
		
		$categories_list = CategoryNewsSelection( 0, 0 );
		$rss_info = $lang['rss_new'];
		$submit_value = $lang['rss_new'];
		$form_action = "?mod=rss&amp;action=doadd";
	
	} else {
		
		$row = $db->super_query( "SELECT * FROM " . PREFIX . "_rss WHERE id='$id'" );
		
		$rss_date = makeDropDown( array ("1" => $lang['rss_date_1'], "0" => $lang['rss_date_2'] ), "rss_date", $row['date'] );
		$text_type = makeDropDown( array ("1" => "BBCODES", "0" => "HTML" ), "text_type", $row['text_type'] );
		
		$allow_main = makeDropDown( array ("1" => $lang['opt_sys_yes'], "0" => $lang['opt_sys_no'] ), "allow_main", $row['allow_main'] );
		$allow_rating = makeDropDown( array ("1" => $lang['opt_sys_yes'], "0" => $lang['opt_sys_no'] ), "allow_rating", $row['allow_rating'] );
		$allow_comm = makeDropDown( array ("1" => $lang['opt_sys_yes'], "0" => $lang['opt_sys_no'] ), "allow_comm", $row['allow_comm'] );
		
		$rss_search_value = htmlspecialchars( stripslashes( $row['search'] ), ENT_QUOTES, $config['charset'] );
		$rss_maxnews_value = $row['max_news'];
		
		$categories_list = CategoryNewsSelection( $row['category'], 0 );
		$rss_info = $row['url'];
		$submit_value = $lang['user_save'];
		$rss_url_value = htmlspecialchars( stripslashes( $row['url'] ), ENT_QUOTES, $config['charset'] );
		$rss_descr_value = htmlspecialchars( stripslashes( $row['description'] ), ENT_QUOTES, $config['charset'] );
		$rss_cookie_value = htmlspecialchars( stripslashes( $row['cookie'] ), ENT_QUOTES, $config['charset'] );
		
		$form_action = "?mod=rss&amp;action=doedit&amp;id=" . $id;
	}
	
	echo <<<HTML
<form action="{$form_action}" method="post" class="form-horizontal">
<div class="panel panel-default">
  <div class="panel-heading">
    {$rss_info}
  </div>
  <div class="panel-body">

		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rss_url']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" class="form-control width-400" maxlength="250" name="rss_url" value="{$rss_url_value}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['rss_hurl']}" ></i>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rss_descr']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" type="text" class="form-control width-400" maxlength="250" name="rss_descr" value="{$rss_descr_value}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['rss_hdescr']}" ></i>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rss_maxnews']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" class="form-control text-center" style="width:60px;" name="rss_maxnews" value="{$rss_maxnews_value}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['rss_hmaxnews']}" ></i>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['xfield_xcat']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="category" class="uniform">{$categories_list}</select>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rss_date']}</label>
		  <div class="col-md-10 col-sm-9">
			{$rss_date}
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rss_main']}</label>
		  <div class="col-md-10 col-sm-9">
			{$allow_main}
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rss_rating']}</label>
		  <div class="col-md-10 col-sm-9">
			{$allow_rating}
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rss_comm']}</label>
		  <div class="col-md-10 col-sm-9">
			{$allow_comm}
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rss_text_type']}</label>
		  <div class="col-md-10 col-sm-9">
			{$text_type}
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['rss_search']}</label>
		  <div class="col-md-10 col-sm-9">
			<textarea class="classic" style="width:100%;max-width:350px;" rows="5" name="rss_search">{$rss_search_value}</textarea><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['rss_hsearch']}" ></i>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-lg-2">{$lang['rss_cookie']}</label>
		  <div class="col-md-10 col-sm-9">
			<textarea class="classic" style="width:100%;max-width:350px;" rows="5" name="rss_cookie">{$rss_cookie_value}</textarea><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['rss_hcookie']}" ></i>
		  </div>
		 </div>		 
	
   </div>
	<div class="panel-footer">
		<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$submit_value}</button>
	</div>
</div>
<input type="hidden" name="user_hash" value="$dle_login_hash" />
</form>
HTML;
	
	echofooter();
	
} else {
	
	if( $_REQUEST['action'] == "del" and $id ) {
		
		if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
			
			die( "Hacking attempt! User not found" );
		
		}
		
		$db->query( "DELETE FROM " . PREFIX . "_rss WHERE id = '$id'" );
		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '50', '{$id}')" );

	}
	
	echoheader( "<i class=\"fa fa-rss-square position-left\"></i><span class=\"text-semibold\">{$lang['opt_rss']}</span>", $lang['header_rs_1'] );
	
	$db->query( "SELECT id, url, description FROM " . PREFIX . "_rss ORDER BY id DESC" );
	
	while ( $row = $db->get_row() ) {

		$row['description'] = htmlspecialchars(strip_tags( trim( stripslashes($row['description']) ) ) , ENT_QUOTES, $config['charset']);

		$menu_link = <<<HTML
        <div class="btn-group">
          <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
          <ul class="dropdown-menu text-left dropdown-menu-right">
            <li><a href="?mod=rss&action=news&id={$row['id']}"><i class="fa fa-download position-left"></i>{$lang['rss_news']}</a></li>
            <li><a href="?mod=rss&action=edit&id={$row['id']}"><i class="fa fa-pencil-square-o position-left"></i>{$lang['rss_edit']}</a></li>
			<li class="divider"></li>
            <li><a href="?mod=rss&action=del&user_hash={$dle_login_hash}&id={$row['id']}"><i class="fa fa-trash-o position-left text-danger"></i>{$lang['rss_del']}</a></li>
          </ul>
        </div>
HTML;
		
		$entries .= "
    <tr>
    <td class=\"cursor-pointer\" onclick=\"document.location = '?mod=rss&action=news&id={$row['id']}'; return false;\"><b>{$row['id']}</b></td>
    <td class=\"cursor-pointer\" onclick=\"document.location = '?mod=rss&action=news&id={$row['id']}'; return false;\">{$row['url']}</td>
    <td class=\"cursor-pointer\" onclick=\"document.location = '?mod=rss&action=news&id={$row['id']}'; return false;\">{$row['description']}</td>
    <td>{$menu_link}</td>
     </tr>";
	}
	$db->free();

	echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['rss_list']}
  </div>
  <div class="table-responsive">

    <table class="table table-xs table-hover">
      <thead>
      <tr>
        <th style="width: 60px">ID</th>
        <th>{$lang['rss_url']}</th>
        <th>{$lang['rss_descr']}</th>
        <th style="width: 70px">&nbsp;</th>
      </tr>
      </thead>
	  <tbody>
		{$entries}
	  </tbody>
	</table>
	
   </div>
   	<div class="panel-footer">
	  <button class="btn bg-teal btn-sm btn-raised" type="button" onclick="document.location='?mod=rss&action=add'"><i class="fa fa-plus-circle position-left"></i>{$lang['rss_new']}</button>
	</div>	
</div>	
HTML;
	
	echofooter();
}
?>