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
 File: wordfilter.php
-----------------------------------------------------
 Use: words filter
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['admin_wordfilter'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

$result = "";
$word_id = intval( $_REQUEST['word_id'] );

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));

$parse = new ParseFilter();
$parse->filter_mode = false;

if( $action == "add" ) {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$word_find = trim( strip_tags( stripslashes( $_POST['word_find'] ) ) );
	
	if( $word_find == "" ) {
		msg( "error", $lang['word_error'], $lang['word_word'], "?mod=wordfilter" );
	}
	
	$word_replace = trim(stripslashes( $parse->BB_Parse( $parse->process( $_POST['word_replace'] ), false ) ));
	
	$word_id = time();
	
	$all_items = file( ENGINE_DIR . '/data/wordfilter.db.php' );
	foreach ( $all_items as $item_line ) {
		$item_arr = explode( "|", $item_line );
		if( $item_arr[0] == $word_id ) {
			$word_id ++;
		}
	}
	
	foreach ( $all_items as $word_line ) {
		$word_arr = explode( "|", $word_line );
		if( $word_arr[1] == $word_find ) {
			msg( "error", $lang['word_error'], $lang['word_ar'], "?mod=wordfilter" );
		}
	}
	
	$new_words = fopen( ENGINE_DIR . '/data/wordfilter.db.php', "a" );
	$word_find = str_replace( "|", "&#124", $word_find );
	$word_replace = str_replace( "|", "&#124", $word_replace );

	$word_find = str_replace( "$", "&#036;", $word_find );
	$word_find = str_replace( "{", "&#123;", $word_find );
	$word_find = str_replace( "}", "&#125;", $word_find );
	$word_find = str_replace( "<", "&lt;", $word_find );
	$word_find = str_replace( ">", "&gt;", $word_find );
		
	$word_replace = str_replace( "$", "&#036;", $word_replace );
	$word_replace = str_replace( "{", "&#123;", $word_replace );
	$word_replace = str_replace( "}", "&#125;", $word_replace );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '79', '".htmlspecialchars($word_find, ENT_QUOTES, $config['charset'])."')" );

	fwrite( $new_words, "$word_id|$word_find|$word_replace|" . intval( $_POST['type'] ) . "|". intval( $_POST['register'] ) ."|". intval( $_POST['filter_search'] ) ."|". intval( $_POST['filter_action'] ) ."||\n" );
	fclose( $new_words );

} elseif( $action == "remove" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( ! $word_id ) {
		msg( "error", $lang['word_error'], $lang['word_nof'], "?mod=wordfilter" );
	}
	
	$old_words = file( ENGINE_DIR . '/data/wordfilter.db.php' );
	$new_words = fopen( ENGINE_DIR . '/data/wordfilter.db.php', "w" );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '80', '')" );
	
	foreach ( $old_words as $old_words_line ) {
		$word_arr = explode( "|", $old_words_line );
		if( $word_arr[0] != $word_id ) {
			fwrite( $new_words, $old_words_line );
		}
	}
	fclose( $new_words );

} elseif( $action == "edit" ) {
	
	// Check if Filter was specified
	if( ! $word_id ) {
		msg( "error", $lang['word_error'], $lang['word_nof'], "?mod=wordfilter" );
	}
	echoheader( "<i class=\"fa fa-filter position-left\"></i><span class=\"text-semibold\">{$lang['word_head']}</span>", $lang['header_fi_1'] );

	$all_words = file( ENGINE_DIR . '/data/wordfilter.db.php' );
	foreach ( $all_words as $word_line ) {
		$word_arr = explode( "|", $word_line );
		if( $word_arr[0] == $word_id ) {

			$word_arr[1] = str_replace( "&#036;", "$", $word_arr[1] );
			$word_arr[1] = str_replace( "&#123;", "{", $word_arr[1] );
			$word_arr[1] = str_replace( "&#125;", "}", $word_arr[1] );
			
			$word_arr[2] = str_replace( "&#036;", "$", $word_arr[2] );
			$word_arr[2] = str_replace( "&#123;", "{", $word_arr[2] );
			$word_arr[2] = str_replace( "&#125;", "}", $word_arr[2] );
			
			$word_arr[1] = $parse->decodeBBCodes( $word_arr[1], false );
			$word_arr[2] = $parse->decodeBBCodes( $word_arr[2], false );
	
			if( $word_arr[3] ) $selected = "selected";
			else $selected = "";

			if( $word_arr[4] ) $selected_1 = "selected";
			else $selected_1 = "";

			$selected_2[$word_arr[5]] = "selected";
			$selected_3[$word_arr[6]] = "selected";
		
			$msg = <<<HTML
<form method="post" class="form-horizontal">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['word_edit_head']}
  </div>
  <div class="panel-body">
  
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['word_word']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" value="$word_arr[1]" type="text" name="word_find">
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['word_rep']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" type="text" name="word_replace" value="$word_arr[2]"  title="{$lang['word_help_1']}">
			<div class="text-muted text-size-small hidden-sm hidden-xs">{$lang['word_help_2']}</div>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['filter_type']}</label>
		  <div class="col-md-10 col-sm-9">
			<select class="uniform" name="type"><option value="0">{$lang['filter_type_1']}</option><option value="1" {$selected}>{$lang['filter_type_2']}</option></select>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['filter_register']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="register" class="uniform" style="min-width:100px;"><option value="0">{$lang['opt_sys_no']}</option><option value="1" {$selected_1}>{$lang['opt_sys_yes']}</option></select>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['filter_search']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="filter_search" class="uniform"><option value="0" {$selected_2[0]}>{$lang['filter_search_0']}</option><option value="1" {$selected_2[1]}>{$lang['filter_search_1']}</option><option value="2" {$selected_2[2]}>{$lang['filter_search_2']}</option></select>
		  </div>
		 </div>		 
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['filter_action']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="filter_action" class="uniform"><option value="0" {$selected_3[0]}>{$lang['filter_action_0']}</option><option value="1" {$selected_3[1]}>{$lang['filter_action_1']}</option></select>
		  </div>
		 </div>	

	</div>
<div class="panel-footer">
	<button class="btn bg-teal btn-sm btn-raised" type="submit"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
</div>
</div>
<input type="hidden" name="action" value="doedit">
<input type="hidden" name="word_id" value="$word_arr[0]">
<input type="hidden" name="mod" value="wordfilter">
<input type="hidden" name="user_hash" value="$dle_login_hash">
</form>
HTML;
			

			echo $msg;
	
		
		}
		
	}
	
	echofooter();
	die();

} elseif( $action == "doedit" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$word_find = trim( strip_tags( stripslashes( $_POST['word_find'] ) ) );
	
	if( $word_find == "" ) {
		msg( "error", $lang['word_error'], $lang['word_word'], "javascript:history.go(-1)" );
	}
	
	$word_replace = stripslashes( $parse->BB_Parse( $parse->process( $_POST['word_replace'] ), false ) );
	
	$word_find = str_replace( "|", "&#124", $word_find );
	$word_replace = str_replace( "|", "&#124", $word_replace );

	$word_find = str_replace( "$", "&#036;", $word_find );
	$word_find = str_replace( "{", "&#123;", $word_find );
	$word_find = str_replace( "}", "&#125;", $word_find );
	$word_find = str_replace( "<", "&lt;", $word_find );
	$word_find = str_replace( ">", "&gt;", $word_find );
	
	$word_replace = str_replace( "$", "&#036;", $word_replace );
	$word_replace = str_replace( "{", "&#123;", $word_replace );
	$word_replace = str_replace( "}", "&#125;", $word_replace );
	
	$old_words = file( ENGINE_DIR . '/data/wordfilter.db.php' );
	$new_words = fopen( ENGINE_DIR . '/data/wordfilter.db.php', "w" );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '81', '".htmlspecialchars($word_find, ENT_QUOTES, $config['charset'])."')" );
	
	foreach ( $old_words as $word_line ) {
		$word_arr = explode( "|", $word_line );
		if( $word_arr[0] == $word_id ) {
			fwrite( $new_words, "$word_id|$word_find|$word_replace|" . intval( $_POST['type'] ) . "|". intval( $_POST['register'] ) ."|". intval( $_POST['filter_search'] ) ."|". intval( $_POST['filter_action'] ) ."||\n" );
		} else {
			fwrite( $new_words, $word_line );
		}
	}
	
	fclose( $new_words );
}


echoheader( "<i class=\"fa fa-filter position-left\"></i><span class=\"text-semibold\">{$lang['word_head']}</span>", $lang['header_fi_1'] );

echo <<<HTML
<form method="post" class="form-horizontal">
<input type="hidden" name="action" value="add">
<input type="hidden" name="mod" value="wordfilter">
<input type="hidden" name="user_hash" value="$dle_login_hash" />
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['word_new']}
  </div>
  <div class="panel-body">

		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['word_word']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" class="form-control width-350" name="word_find" title="{$lang['word_help']}" >
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['word_rep']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" type="text" name="word_replace" title="{$lang['word_help_1']}">
			<div class="text-muted text-size-small hidden-sm hidden-xs">{$lang['word_help_2']}</div>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3col-md-2 col-sm-3">{$lang['filter_type']}</label>
		  <div class="col-md-10 col-sm-9">
			<select class="uniform" name="type"><option value="0">{$lang['filter_type_1']}</option><option value="1">{$lang['filter_type_2']}</option></select>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['filter_register']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="register" class="uniform" style="min-width:100px;"><option value="0">{$lang['opt_sys_no']}</option><option value="1">{$lang['opt_sys_yes']}</option></select>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['filter_search']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="filter_search" class="uniform"><option value="0">{$lang['filter_search_0']}</option><option value="1">{$lang['filter_search_1']}</option><option value="2">{$lang['filter_search_2']}</option></select>
		  </div>
		 </div>		 
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['filter_action']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="filter_action" class="uniform"><option value="0">{$lang['filter_action_0']}</option><option value="1">{$lang['filter_action_1']}</option></select>
		  </div>
		 </div>	
	
   </div>
<div class="panel-footer">
	<button class="btn bg-teal btn-sm btn-raised" type="submit"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
</div>
</div>
</form>


<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['word_worte']}
  </div>
  <div class="table-responsive">
<table class="table table-xs table-hover">
HTML;

$all_words = file( ENGINE_DIR . '/data/wordfilter.db.php' );
$count_words = 0;

usort( $all_words, "compare_filter" );

foreach ( $all_words as $word_line ) {
	$word_arr = explode( "|", $word_line );
	
	$result .= "
    <tr>
     <td>
       $word_arr[1]
     </td><td>";
	
	if( $word_arr[2] == "" ) {
		$result .= "<span class=\"text-danger\">{$lang['word_del']}</span>";
	} else {
		$result .= "$word_arr[2]";
	}
	
	$type = ($word_arr[3]) ? $lang['filter_type_2'] : $lang['filter_type_1'];
	$register = ($word_arr[4]) ? $lang['opt_sys_yes'] : $lang['opt_sys_no'];

	
	$result .= "</td><td>{$register}</td><td>{$type}</td><td>{$lang['filter_search_'.$word_arr[5]]}</td><td>{$lang['filter_action_'.$word_arr[6]]}</td>
	<td>
       <a href=\"?mod=wordfilter&action=edit&word_id=$word_arr[0]\"><i title=\"{$lang['cat_ed']}\" alt=\"{$lang['cat_ed']}\" class=\"fa fa-pencil-square-o position-left\"></i></a>&nbsp;&nbsp;
	   <a onclick=\"confirmDelete('{$word_arr[0]}'); return false;\" href=\"#\"><i title=\"{$lang['cat_del']}\" alt=\"{$lang['cat_del']}\" class=\"fa fa-trash-o text-danger\"></i></a>
    </td>
    </tr>";
	$count_words ++;
}

if( $count_words == 0 ) {

	echo "<thead>
    <tr>
     <th height=50><p align=center><br><b>{$lang['word_empty']}</b></th>
    </tr></thead>";

} else {

echo <<<HTML
      <thead>
      <tr>
        <td>{$lang['word_worte']}</td>
        <td>{$lang['word_lred']}</td>
        <td style="width: 150px">{$lang['filter_register']}</td>
        <td style="width: 200px">{$lang['filter_type']}</td>
        <td style="width: 200px">{$lang['filter_search']}</td>
		<td style="width: 200px">{$lang['filter_action']}</td>
        <td style="width: 100px"></td>
      </tr>
      </thead>
	  <tbody>
		{$result}
	  </tbody>
HTML;

}


echo <<<HTML
</table>
   </div>
</div>
<script>
<!--

function confirmDelete(id){

	DLEconfirm( '{$lang['del_filter']}', '{$lang['p_confirm']}', function () {

		document.location='?mod=wordfilter&action=remove&user_hash={$dle_login_hash}&word_id='+id;

	} );

}
//-->
</script>
HTML;

echofooter();
?>