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
 File: comments.php
-----------------------------------------------------
 Use: comments edit
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['admin_comments'] ) {
	msg( "error", $lang['addnews_denied'], $lang['addnews_denied'], $_SESSION['admin_referrer'] );
}

$id = intval( $_REQUEST['id'] );

$_SESSION['admin_referrer'] = "?mod=comments&amp;action=edit";

if( $action == "dodelete" AND $id) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	deletecommentsbynewsid($id);
	$db->query( "UPDATE " . PREFIX . "_post SET comm_num='0' WHERE id ='{$id}'" );
	
	clear_cache();
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '20', '$id')" );
	
	msg( "success", $lang['mass_head'], $lang['mass_delokc'], $_SESSION['admin_referrer'] );

} elseif( $action == "mass_delete" ) {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( ! $_POST['selected_comments'] ) {
		msg( "error", $lang['mass_error'], $lang['mass_dcomm'], $_SESSION['admin_referrer'] );
	}
	
	foreach ( $_POST['selected_comments'] as $c_id ) {

		$c_id = intval( $c_id );
		
		deletecomments( $c_id );

	}
	
	clear_cache( array('news_', 'full_', 'comm_', 'rss') );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '21', '')" );
	
	msg( "success", $lang['mass_head'], $lang['mass_delokc'], "?mod=comments&action=edit&id={$id}" );

} elseif( $action == "edit" ) {

	$where = array ( PREFIX . "_comments.approve = '1'");
	
	if ( $id ) $where[] = "post_id = '{$id}'";
	
	if(isset($_REQUEST['search_field']) AND $_REQUEST['search_field']) {
		
		$search_field = $db->safesql( addslashes(addslashes(trim( urldecode( $_REQUEST['search_field'] ) ) ) ) );
		$search_field = preg_replace('/\s+/u', '%', $search_field);
		
		$search_field2 = $db->safesql(trim( htmlspecialchars( urldecode( $_REQUEST['search_field'] ), ENT_QUOTES, $config['charset']  ) ) );
		$search_field2 = preg_replace('/\s+/u', '%', $search_field2);
		
		$where[] = "(".PREFIX ."_comments.text like '%{$search_field}%' OR ".PREFIX."_comments.text like '%{$search_field2}%')";
		
		$search_field = trim( htmlspecialchars( urldecode( $_REQUEST['search_field'] ), ENT_QUOTES, $config['charset']  ) );
		
	} else $search_field = "";

	$where = implode( " AND ", $where );

	$start_from = intval( $_GET['start_from'] );
	if( $start_from < 0 ) $start_from = 0;
	$news_per_page = 20;
	$i = $start_from;

	$gopage = intval( $_GET['gopage'] );
	if( $gopage > 0 ) $start_from = ($gopage - 1) * $news_per_page;

	if ($config['allow_comments_wysiwyg'] == "2") {
	
		$js_array[] = "engine/editor/jscripts/tiny_mce/tinymce.min.js";
	
	}
	
	if ($config['allow_comments_wysiwyg'] == "1") {
		
		$js_array[] = "engine/skins/codemirror/js/code.js";
		$js_array[] = "engine/editor/jscripts/froala/editor.js";
		$js_array[] = "engine/editor/jscripts/froala/languages/{$lang['wysiwyg_language']}.js";
		$css_array[] = "engine/editor/jscripts/froala/css/editor.css";
	
	}
	
	echoheader( "<i class=\"fa fa-file-text-o position-left\"></i><span class=\"text-semibold\">{$lang['header_c_1']}</span>", $lang['header_c_3'] );
	
	$entries = "";

	$result_count = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_comments WHERE {$where}" );

	$db->query( "SELECT " . PREFIX . "_comments.id, post_id, " . PREFIX . "_comments.date, " . PREFIX . "_comments.autor, text, ip, " . PREFIX . "_post.title, " . PREFIX . "_post.date as newsdate, " . PREFIX . "_post.alt_name, " . PREFIX . "_post.category FROM " . PREFIX . "_comments LEFT JOIN " . PREFIX . "_post ON " . PREFIX . "_comments.post_id=" . PREFIX . "_post.id WHERE {$where} ORDER BY " . PREFIX . "_comments.date DESC LIMIT $start_from,$news_per_page" );
	
	while ( $row = $db->get_array() ) {
		$i ++;
		
		$row['text'] = str_ireplace( '{THEME}', 'templates/' . $config['skin'], $row['text'] );
		$row['text'] = "<div id='comm-id-" . $row['id'] . "'>" . stripslashes( $row['text'] ) . "</div>";
		$row['newsdate'] = strtotime( $row['newsdate'] );
		$row['date'] = strtotime( $row['date'] );
		if( !$langformatdatefull ) $langformatdatefull = "d.m.Y H:i:s";
		$date = date( $langformatdatefull, $row['date'] );
		
		if( $config['allow_alt_url'] ) {
			
			if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
				
				if( intval( $row['category'] ) and $config['seo_type'] == 2 ) {
					
					$full_link = $config['http_home_url'] . get_url( intval( $row['category'] ) ) . "/" . $row['post_id'] . "-" . $row['alt_name'] . ".html";
				
				} else {
					
					$full_link = $config['http_home_url'] . $row['post_id'] . "-" . $row['alt_name'] . ".html";
				
				}
			
			} else {
				
				$full_link = $config['http_home_url'] . date( 'Y/m/d/', $row['newsdate'] ) . $row['alt_name'] . ".html";
			}
		
		} else {
			
			$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['post_id'];
		
		}
		
		$news_title = "<a href=\"" . $full_link . "\"  target=\"_blank\">" . stripslashes( $row['title'] ) . "</a>";
		$row['autor'] = "<a href=\"?mod=editusers&action=edituser&user=".urlencode($row['autor'])."\" target=\"_blank\">{$row['autor']}</a>";
		$row['ip'] = "<a href=\"?mod=blockip&ip=".urlencode($row['ip'])."\" target=\"_blank\">{$row['ip']}</a>";

	
	$entries .= <<<HTML
<a name="comment{$row['id']}"></a>
<div id='table-comm-{$row['id']}' class="panel panel-default">
  <div class="panel-heading">
    <span class="label label-info position-left">{$lang['edit_autor']}</span><strong class="position-left">{$row['autor']}</strong>IP: {$row['ip']} {$lang['cmod_n_title']} {$news_title}
	<div class="heading-elements">
		<div class="checkbox checkbox-right"><label><input name="selected_comments[]" value="{$row['id']}" type="checkbox" class="icheck"></label></div>
	</div>
  </div>
  <div class="panel-body">
  {$row['text']}
  </div>
  <div class="panel-footer">
	<button onclick="ajax_comm_edit('{$row['id']}'); return false;" type="button" class="btn bg-primary-600 btn-sm btn-raised position-left"><i class="fa fa-pencil-square-o position-left"></i>{$lang['group_sel1']}</button>
	<button onclick="MarkSpam('{$row['id']}'); return false;" type="button" class="btn bg-brown-600 btn-sm btn-raised position-left"><i class="fa fa-minus-circle position-left"></i>{$lang['btn_spam']}</button>
	<button onclick="DeleteComments('{$row['id']}'); return false;" type="button" class="btn bg-danger btn-sm btn-raised"><i class="fa fa-trash-o position-left"></i>{$lang['edit_dnews']}</button>
	<span class="pull-right" style="margin-top: 4px;"><i class="fa fa-clock-o position-left"></i>{$date}</span>
  </div>
</div>
<input type="hidden" name="post_id[{$row['id']}]" value="{$row['post_id']}">
HTML;
	
	}
	
	$db->free();

		// pagination

		$npp_nav = "";
		
		if( $start_from > 0 ) {
			$previous = $start_from - $news_per_page;
			$npp_nav .= "<li><a href=\"?mod=comments&action=edit&id={$id}&start_from={$previous}\" title=\"{$lang['edit_prev']}\">&lt;&lt;</a></li>";
		}
		
		if( $result_count['count'] > $news_per_page ) {
			
			$enpages_count = @ceil( $result_count['count'] / $news_per_page );
			$enpages_start_from = 0;
			$enpages = "";
			
			if( $enpages_count <= 10 ) {
				
				for($j = 1; $j <= $enpages_count; $j ++) {
					
					if( $enpages_start_from != $start_from ) {
						
						$enpages .= "<li><a href=\"?mod=comments&action=edit&id={$id}&start_from={$enpages_start_from}\">$j</a></li>";
					
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
					
					$enpages .= "<li><a href=\"?mod=comments&action=edit&id={$id}&start_from=0\">1</a></li> <li><span>...</span></li>";
				
				}
				
				for($j = $start; $j <= $end; $j ++) {
					
					if( $enpages_start_from != $start_from ) {
						
						$enpages .= "<li><a href=\"?mod=comments&action=edit&id={$id}&start_from={$enpages_start_from}\">$j</a></li>";
					
					} else {
						
						$enpages .= "<li class=\"active\"><span>$j</span></li>";
					}
					
					$enpages_start_from += $news_per_page;
				}
				
				$enpages_start_from = ($enpages_count - 1) * $news_per_page;
				$enpages .= "<li><span>...</span></li><li><a href=\"?mod=comments&action=edit&id={$id}&start_from={$enpages_start_from}\">$enpages_count</a></li>";
				
				$npp_nav .= $enpages;
			
			}
		
			if( $result_count['count'] > $i ) {
				$how_next = $result_count['count'] - $i;
				if( $how_next > $news_per_page ) {
					$how_next = $news_per_page;
				}
				$npp_nav .= "<li><a href=\"?mod=comments&action=edit&id={$id}&start_from={$i}\" title=\"{$lang['edit_next']}\">&gt;&gt;</a></li>";
			}
			
			$npp_nav = "<div class=\"pull-left\"><ul class=\"pagination pagination-sm\">".$npp_nav."</ul></div>";
		}		
		// pagination

	echo <<<HTML
<style type="text/css">
.bb-pane {
  height: 1%; overflow: hidden;
  padding-bottom: 5px;
  padding-left: 5px;
  margin: 0;
  height: auto !important;
  text-decoration:none;
  border-bottom-left-radius: 0px;
  border-top:1px solid #cccccc;
  border-left:1px solid #cccccc;
  border-right:1px solid #cccccc;
  box-shadow: none !important;
  margin: 0;
  text-decoration: none;
  box-shadow: none !important;
  background-color: #f6f6f6;
}
.dle_theme_dark .bb-pane {
    color: #fefefe;
    background-color: #363636;
    border-color: #363636;
}
.bb-pane>b {
    margin-top: 5px;
    margin-left: 0;
	vertical-align: middle;
}
.bb-pane .bb-btn + .bb-btn, .bb-pane .bb-btn + .bb-pane,.bb-pane .bb-pane + .bb-btn,.bb-pane .bb-pane + .bb-pane {
    margin-left:-1px;
}
.bb-btn {
	display: inline-block; overflow: hidden; float: left;
	padding: 4px 10px;
    border: 1px solid transparent;
}
 

.bb-btn:hover {
	background-color: #e6e6e6;
    border: 1px solid rgba(0, 0, 0, 0.23);
}

.dle_theme_dark .bb-btn:hover {
	background-color: transparent;
    border: 1px solid rgba(0, 0, 0, 0.23);
}

.bb-editor textarea { 
	font-size: 12px;
	font-family: verdana;
	-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	-webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
	transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
	-webkit-border-radius: 0;
	border-radius: 0;
	color: #000;
	padding: 3px 5px 3px 5px;
	border:1px solid #cccccc;
	background: #ffffff;
	resize: vertical;
	outline: none;
	height: 300px;
	width: 100%;
}
.dle_theme_dark .bb-editor textarea {
    color: #ddd;
    background-color: #262626;
	border:1px solid #363636;
}
.ui-dialog input[type="text"], input[type="password"], textarea {
  -webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);
  box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);
  -webkit-transition:border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
  transition:border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
  -webkit-border-radius: 0;
  border-radius: 0;
  color: #000;
  padding: 3px 5px 3px 5px;
  border: 1px solid #cccccc;
  display: inline-block;
  background: #ffffff;
  font-size: 13px;
}

.ui-dialog input[type="text"]:focus, input[type="password"]:focus, .ui-dialog textarea:focus {
    border: 1px solid #009688; 
}

.dle_theme_dark .ui-dialog input[type="text"], .dle_theme_dark .ui-dialog input[type="password"], .dle_theme_dark .ui-dialog textarea {
  color: #fefefe;
  background-color: #555;
  border-color: #cbcbcb;
}

	.bb-pane-dropdown {
		position: absolute;
		top: 100%; left: 0;
		z-index: 1000;
		display: none;
		min-width: 180px;
		padding: 5px 0 !important;
		margin: 2px 0 0;
		list-style: none;
		font-size: 11px;
		border-radius: 2px;
		background: #fff;
		background-clip: padding-box;
		-webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
		max-height: 300px;
    	overflow: auto;
	}
	.bb-pane-dropdown > li > a {
		display: block;
		padding: 3px 10px;
		clear: both;
		font-weight: normal;
		line-height: 1.42857;
		color: #353535;
		white-space: nowrap;
	}
	.bb-pane-dropdown > li > a:hover { text-decoration:none; color: #262626; background-color:whitesmoke; }
	.bb-pane-dropdown .color-palette div .color-btn {
		width: 17px; height: 17px;
		padding: 0; margin: 0;
		border: 1px solid #fff;
		cursor: pointer;
	}
	.bb-pane-dropdown .color-palette { padding: 0px 5px; }

	.bb-pane-dropdown table { margin: 0px; }
	
	.dle_theme_dark .bb-pane-dropdown {
		color: #fefefe;
		background-color: #363636!important;
	}
	
	.bb-sel { float: left; padding: 2px 2px 0 2px; }
	.bb-sel select { font-size: 11px; }
	.bb-sep { display: inline-block; float: left; width: 1px; padding: 2px; }
	.bb-btn { cursor: pointer;  outline: 0; }

	#b_font select, #b_size select { padding: 0;}

	.bb-pane h1, .bb-pane h2, .bb-pane h3, .bb-pane h4, .bb-pane h5, .bb-pane h6 { margin-top: 5px; margin-bottom: 5px; }
	.bb-pane h1 { font-size: 36px; }
	.bb-pane h2 { font-size: 30px; }
	.bb-pane h3 { font-size: 24px; }
	.bb-pane h4 { font-size:18px; }
	.bb-pane h5 { font-size:14px; }
	.bb-pane h6 { font-size:12px; }

	[class^="bb-btn"], [class*=" bb-btn"] {
	    font-family: 'bb-editor-font';
	    speak: none;
	    font-style: normal;
	    font-weight: normal;
	    font-variant: normal;
	    text-transform: none;
	    line-height: 1;
	    font-size: 14px;
	    -webkit-font-smoothing: antialiased;
	    -moz-osx-font-smoothing: grayscale;
	}

	.bb-sel { float: left; padding: 2px 2px 0 2px; }
	.bb-sel select { font-size: 11px; }
	.bb-sep { display: inline-block; float: left; width: 1px; padding: 2px; }
	.bb-btn { cursor: pointer;  outline: 0; }

	#b_font select, #b_size select { padding: 0;}

	#b_b:before {content: "\\f032";}
	#b_i:before {content: "\\f033";}
	#b_u:before {content: "\\f0cd";}
	#b_s:before {content: "\\f0cc";}
	#b_img:before { content: "\\f03e"; }
	#b_up:before { content: "\\e930"; }
	#b_emo:before { content: "\\f118"; }
	#b_url:before { content: "\\f0c1"; }
	#b_leech:before { content: "\\e98d"; }
	#b_mail:before { content: "\\f003"; }
	#b_video:before { content: "\\e913"; }
	#b_audio:before { content: "\\e911"; }
	#b_hide:before { content: "\\e9d1"; }
	#b_quote:before { content: "\\e977"; }
	#b_code:before { content: "\\f121"; }
	#b_left:before { content: "\\f036"; }
	#b_center:before { content: "\\f037"; }
	#b_right:before { content: "\\f038"; }
	#b_color:before { content: "\\e601"; }
	#b_spoiler:before { content: "\\e600"; }
	#b_fla:before { content: "\\ea8d"; }
	#b_yt:before { content: "\\f16a"; }
	#b_tf:before { content: "\\ea61"; }
	#b_list:before { content: "\\f0ca"; }
	#b_ol:before { content: "\\f0cb"; }
	#b_tnl:before { content: "\\ea61"; }
	#b_br:before { content: "\\ea68"; }
	#b_pl:before { content: "\\ea72"; }
	#b_size:before { content: "\\f034"; }
	#b_font:before { content: "\\f031"; }
	#b_header:before { content: "\\f1dc"; }
	#b_sub:before { content: "\\f12c"; }
	#b_sup:before { content: "\\f12b"; }
	#b_justify:before { content: "\\f039"; }
	.bbcodes {
		display:inline-block;
		padding: 4px 10px;
		margin-bottom:0;
		line-height: 1.5;
		cursor:pointer;
		border-width: 0;
        background-color: #1e88e5;
        border-color: #1e88e5;
        color: #fff;
		border-radius: 3px;
		white-space:nowrap;
		outline:0;
        -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        -webkit-transition: all ease-in-out 0.15s;
        transition: all ease-in-out 0.15s;

	}
	.bbcodes:hover {
      -webkit-box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
	}
	 .btn:focus {
		outline:0;
	}
	.emoji_box {
		width:100%;
		max-width: 390px;
	}
	.emoji_category {
		padding:7px;
		clear:both;
	}
	.emoji_list {
		margin-top:5px;
		margin-bottom:5px;
		width:100%;
		font-family:'Apple Color Emoji', 'Segoe UI Emoji', 'NotoColorEmoji', 'Segoe UI Symbol', 'Android Emoji', 'EmojiSymbols';
		font-size:2em;
	}
	.emoji_symbol {
		float:left;
		margin-bottom: 10px;
		width:12.5%;
		text-align:center;
	}
	
	.emoji_symbol a,  .emoji_symbol a:hover {
		cursor: pointer;
		text-decoration:none;
	}
</style>
<script>
<!--

var c_cache = [];
var dle_root = '';
var dle_prompt = '{$lang['p_prompt']}';
var dle_wysiwyg    = '{$config['allow_comments_wysiwyg']}';

function setNewField(which, formname)
{
	if (which != selField)
	{
		fombj    = formname;
		selField = which;

	}
};

function ajax_comm_edit( c_id )
{

	for (var i = 0, length = c_cache.length; i < length; i++) {
	    if (i in c_cache) {
			if ( c_cache[ i ] !== '' )
			{
				ajax_cancel_comm_edit( i );
			}
	    }
	}

	if ( ! c_cache[ c_id ] || c_cache[ c_id ] === '' )
	{
		c_cache[ c_id ] = $('#comm-id-'+c_id).html();
	}

	ShowLoading('');

	$.get("engine/ajax/controller.php?mod=editcomments", { id: c_id, area: 'news', action: "edit" }, function(data){

		HideLoading('');

		$('#comm-id-'+c_id).html(data);

		setTimeout(function() {
           $("html,body").stop().animate({scrollTop: $("#comm-id-" + c_id).offset().top - 70}, 700);
        }, 100);

	}, 'html');
	return false;
};

function ajax_cancel_comm_edit( c_id ) {
	if ( c_cache[ c_id ] != "" )
	{
		$("#comm-id-"+c_id).html(c_cache[ c_id ]);
	}

	c_cache[ c_id ] = '';

	return false;
};

function ajax_save_comm_edit( c_id, area )
{

	if (dle_wysiwyg == "2") {

		tinyMCE.triggerSave();

	}

	var comm_txt = $('#dleeditcomments'+c_id).val();


	ShowLoading('');

	$.post("engine/ajax/controller.php?mod=editcomments", { id: c_id, comm_txt: comm_txt, area: area, action: "save", user_hash: "{$dle_login_hash}" }, function(data){

		HideLoading('');
		c_cache[ c_id ] = '';
		$("#comm-id-"+c_id).html(data);

	});
	return false;
	
};

function DeleteComments(id) {

    DLEconfirm( '{$lang['d_c_confirm']}', '{$lang['p_confirm']}', function () {

		ShowLoading('');
	
		$.get("engine/ajax/controller.php?mod=deletecomments", { id: id, dle_allow_hash: '{$dle_login_hash}' }, function(r){
	
			HideLoading('');
	
			ShowOrHide('table-comm-'+id);
	
		});

	} );

};
function MarkSpam(id) {

    DLEconfirm( '{$lang['mark_spam_c']}', '{$lang['p_confirm']}', function () {

		ShowLoading('');
	
		$.get("engine/ajax/controller.php?mod=adminfunction", { id: id, action: 'commentsspam', user_hash: '{$dle_login_hash}' }, function(data){
	
			HideLoading('');
	
			if (data != "error") {
	
			    DLEconfirm( data, '{$lang['p_confirm']}', function () {
					location.reload(true);
				} );
	
			}
	
		});

	} );

};

function ckeck_uncheck_all() {
    var frm = document.dlemasscomments;
    for (var i=0;i<frm.elements.length;i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type=='checkbox') {
            if(frm.master_box.checked == true){ elmnt.checked=false; $(elmnt).parents('.panel').find('.panel-body').removeClass('warning'); }
            else{ elmnt.checked=true; $(elmnt).parents('.panel').find('.panel-body').addClass('warning'); }
        }
    }
    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
    else{ frm.master_box.checked = true; }
	
	$.uniform.update();
	
	return false;
};
$(function() {
    $('.heading-elements input[type=checkbox]').on('change', function() {
        if($(this).is(':checked')) {
            $(this).parents('.panel').find('.panel-body').addClass('warning');
        }
        else {
            $(this).parents('.panel').find('.panel-body').removeClass('warning');
        }
    });
});
//-->
</script>
<form action="" method="post" name="dlemasscomments" id="dlemasscomments">
<input type=hidden name="mod" value="comments">
<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
<div class="panel panel-flat">
	<div class="panel-heading">
		<div class="has-feedback width-350">
			<input name="search_field" type="search" class="form-control" placeholder="{$lang['search_field']}" value="{$search_field}">
			<div class="form-control-feedback">
			    <a href="#" onclick="$(this).closest('form').submit(); return false;"><i class="fa fa-search text-size-base text-muted"></i></a>
			</div>
		</div>
		
		<div class="heading-elements">
			<div class="checkbox checkbox-right"><label><input name="master_box" id="master_box" type="checkbox" class="icheck" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all();">{$lang['edit_selall']}</label></div>
		</div>
	</div>
</div>
{$entries}
{$npp_nav}
<div class="pull-right">
	<select class="uniform" name="action"><option value="edit">---</option><option value="mass_delete">{$lang['edit_seldel']}</option></select>
	<input class="btn bg-slate-600 btn-sm btn-raised" type="submit" value="{$lang['b_start']}" />
</div>
</form>
HTML;

	if( strpos ( $entries, "dleplyrplayer" ) !== false ) {
		echo <<<HTML
		<link href="{$config['http_home_url']}engine/classes/html5player/plyr.css" rel="stylesheet" type="text/css">
		<script src="{$config['http_home_url']}engine/classes/html5player/plyr.js"></script>
HTML;

	}
	
	echofooter();
} else {
	msg( "error", $lang['addnews_denied'], $lang['addnews_denied'], $_SESSION['admin_referrer'] );
}
?>