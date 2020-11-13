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
 File: links.php
-----------------------------------------------------
 Use: the management of cross-references
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( $member_id['user_group'] != 1  ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}


$start_from = intval( $_REQUEST['start_from'] );
$news_per_page = 50;

if( $start_from < 0 ) $start_from = 0;

if ($_REQUEST['searchword']) {
  
  $searchword = htmlspecialchars( strip_tags( stripslashes( trim( urldecode ( $_REQUEST['searchword'] ) ) ) ), ENT_COMPAT, $config['charset'] );
  
} else $searchword = "";

if ($searchword) $urlsearch = "&searchword={$searchword}"; else $urlsearch = "";

if ($_GET['action'] == "delete") {
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$id = intval ( $_GET['id'] );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '95', '')" );
	$db->query( "DELETE FROM " . PREFIX . "_links WHERE id='{$id}'" );

	@unlink( ENGINE_DIR . '/cache/system/links.php' );
	clear_cache();
	header( "Location: ?mod=links&start_from={$start_from}{$urlsearch}" ); die();

}

if ($_POST['action'] == "mass_delete") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( !$_POST['selected_tags'] ) {
		msg( "error", $lang['mass_error'], $lang['mass_links_err'], "?mod=links&start_from={$start_from}" );
	}

	foreach ( $_POST['selected_tags'] as $id ) {
		$id = intval($id);
		$db->query( "DELETE FROM " . PREFIX . "_links WHERE id='{$id}'" );
	}

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '95', '')" );

	@unlink( ENGINE_DIR . '/cache/system/links.php' );
	clear_cache();
	header( "Location: ?mod=links&start_from={$start_from}{$urlsearch}" ); die();

}

if ($_POST['action'] == "mass_r_1" OR $_POST['action'] == "mass_r_2" OR $_POST['action'] == "mass_r_3" OR $_POST['action'] == "mass_r_4" OR $_POST['action'] == "mass_r_9" OR $_POST['action'] == "mass_r_10") {
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( !$_POST['selected_tags'] ) {
		msg( "error", $lang['mass_error'], $lang['mass_links_err'], "?mod=links&start_from={$start_from}" );
	}

	$replacearea = 1;
	
	if( $_POST['action'] == "mass_r_2" ) $replacearea = 2; elseif( $_POST['action'] == "mass_r_3" ) $replacearea = 3; elseif( $_POST['action'] == "mass_r_4" ) $replacearea = 4; elseif( $_POST['action'] == "mass_r_9" ) $replacearea = 5; elseif( $_POST['action'] == "mass_r_10" ) $replacearea = 6;

	foreach ( $_POST['selected_tags'] as $id ) {
		$id = intval($id);
		$db->query( "UPDATE " . PREFIX . "_links SET replacearea='{$replacearea}' WHERE id='{$id}'" );
	}

	@unlink( ENGINE_DIR . '/cache/system/links.php' );
	clear_cache();
	header( "Location: ?mod=links&start_from={$start_from}{$urlsearch}" ); die();
}


if ($_POST['action'] == "mass_r_5" OR $_POST['action'] == "mass_r_6") {
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( !$_POST['selected_tags'] ) {
		msg( "error", $lang['mass_error'], $lang['mass_links_err'], "?mod=links&start_from={$start_from}" );
	}

	$onlyone = 0;

	if( $_POST['action'] == "mass_r_5" ) $onlyone = 1;

	foreach ( $_POST['selected_tags'] as $id ) {
		$id = intval($id);
		$db->query( "UPDATE " . PREFIX . "_links SET only_one='{$onlyone}' WHERE id='{$id}'" );
	}

	@unlink( ENGINE_DIR . '/cache/system/links.php' );
	clear_cache();
	header( "Location: ?mod=links&start_from={$start_from}{$urlsearch}" ); die();
}

if ($_POST['action'] == "mass_r_7" OR $_POST['action'] == "mass_r_8") {
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( !$_POST['selected_tags'] ) {
		msg( "error", $lang['mass_error'], $lang['mass_links_err'], "?mod=links&start_from={$start_from}" );
	}

	$targetblank = 0;

	if( $_POST['action'] == "mass_r_7" ) $targetblank = 1;

	foreach ( $_POST['selected_tags'] as $id ) {
		$id = intval($id);
		$db->query( "UPDATE " . PREFIX . "_links SET targetblank='{$targetblank}' WHERE id='{$id}'" );
	}

	@unlink( ENGINE_DIR . '/cache/system/links.php' );
	clear_cache();
	header( "Location: ?mod=links&start_from={$start_from}{$urlsearch}" ); die();
}

if ($_GET['action'] == "add") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$onlyone = intval ( $_GET['onlyone'] );
	$targetblank = intval ( $_GET['targetblank'] );
	$replacearea = intval ( $_GET['replacearea'] );

	$rcount = intval ( $_GET['rcount'] );

	if($rcount < 1) $rcount = 0;

	$tag = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( urldecode ( $_GET['tag'] ) ) ) ), ENT_COMPAT, $config['charset'] ) );
	$title = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( urldecode ($_GET['title'] ) ) ) ), ENT_QUOTES, $config['charset'] ) );
	$url = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_GET['url'] ) ) ), ENT_QUOTES, $config['charset'] ) );
	$url = str_ireplace( "document.cookie", "d&#111;cument.cookie", $url );
	$url = preg_replace( "/javascript:/i", "j&#1072;vascript:", $url );
	$url = preg_replace( "/data:/i", "d&#1072;ta:", $url );
	
	
	if (!$tag) msg( "error", $lang['opt_error'], $lang['links_err'], "?mod=links" );

	if (is_numeric($tag)) msg( "error", $lang['opt_error'], $lang['links_err'], "?mod=links" );

	$row = $db->super_query( "SELECT word FROM " . PREFIX . "_links WHERE word ='{$tag}'" );

	if( $row['word'] ) {
		msg( "error", $lang['addnews_error'], $lang['links_err_1'], "?mod=links" );
	}
	

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '93', '{$tag}')" );
	$db->query( "INSERT INTO " . PREFIX . "_links (word, link, only_one, replacearea, rcount, targetblank, title) values ('{$tag}', '{$url}', '{$onlyone}', '{$replacearea}', '{$rcount}', '{$targetblank}', '{$title}')" );

	@unlink( ENGINE_DIR . '/cache/system/links.php' );
	clear_cache();
	header( "Location: ?mod=links" ); die();
}

if ($_GET['action'] == "edit") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$onlyone = intval ( $_GET['onlyone'] );
	$targetblank = intval ( $_GET['targetblank'] );
	$replacearea = intval ( $_GET['replacearea'] );
	$rcount = intval ( $_GET['rcount'] );

	if($rcount < 1) $rcount = 0;

	$tag = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( urldecode ( $_GET['tag'] ) ) ) ), ENT_COMPAT, $config['charset'] ) );
	$title = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( urldecode ($_GET['title'] ) ) ) ), ENT_QUOTES, $config['charset'] ) );
	$url = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_GET['url'] ) ) ), ENT_QUOTES, $config['charset'] ) );
	$url = str_ireplace( "document.cookie", "d&#111;cument.cookie", $url );
	$url = preg_replace( "/javascript:/i", "j&#1072;jvascript:", $url );
	$url = preg_replace( "/data:/i", "d&#1072;ta:", $url );
	$id = intval ( $_GET['id'] );
	
	if (!$tag) msg( "error", $lang['index_denied'], $lang['links_err'], "?mod=links&start_from={$start_from}" );

	if (is_numeric($tag)) msg( "error", $lang['index_denied'], $lang['links_err'], "?mod=links" );

	$row = $db->super_query( "SELECT word FROM " . PREFIX . "_links WHERE word = '{$tag}' AND id != '{$id}'" );

	if( $row['word'] ) {
		msg( "error", $lang['opt_error'], $lang['links_err_1'], "?mod=links" );
	}

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '94', '{$tag}')" );
	$db->query( "UPDATE " . PREFIX . "_links SET word='{$tag}', link='{$url}', only_one='{$onlyone}', replacearea='{$replacearea}', rcount='{$rcount}', targetblank='{$targetblank}', title='{$title}' WHERE id='{$id}'" );

	@unlink( ENGINE_DIR . '/cache/system/links.php' );
	clear_cache();
	header( "Location: ?mod=links&start_from={$start_from}{$urlsearch}" ); die();
}

echoheader( "<i class=\"fa fa-link position-left\"></i><span class=\"text-semibold\">{$lang['opt_links']}</span>", $lang['header_l_1'] );

echo <<<HTML
<form action="?mod=links" method="get" name="navi" id="navi">
<input type="hidden" name="mod" value="links">
<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
<input type="hidden" name="searchword" value="{$searchword}">
</form>
<form action="?mod=links" method="post" name="optionsbar" id="optionsbar">
<input type="hidden" name="mod" value="links">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_links']}
	
	<div class="heading-elements">
		<div class="form-group has-feedback" style="width:250px;">
			<input name="searchword" type="search" class="form-control" placeholder="{$lang['search_field']}" onchange="document.optionsbar.start_from.value=0;" value="{$searchword}">
			<div class="form-control-feedback">
			    <a href="#" onclick="$(this).closest('form').submit();"><i class="fa fa-search text-size-base text-muted"></i></a>
			</div>
		</div>
	</div>
	
	
  </div>
HTML;

$i = $start_from+$news_per_page;

if ( $searchword ) {
  
  $searchword = @$db->safesql($searchword);
  $where = "WHERE word like '%$searchword%' OR link like '%$searchword%' ";
  $lang['links_not_found'] = $lang['tags_s_not_found'];
  
} else $where = "";

$result_count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_links {$where}");
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

$i = 0;

if ( $all_count_news ) {

	$entries = "";

	$db->query("SELECT * FROM " . PREFIX . "_links {$where}ORDER BY id DESC LIMIT {$start_from},{$news_per_page}");

	while($row = $db->get_row()) {
	
		$menu_link = <<<HTML
        <div class="btn-group">
          <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
          <ul class="dropdown-menu text-left dropdown-menu-right">
            <li><a uid="{$row['id']}" href="?mod=links" class="editlink"><i class="fa fa-pencil-square-o position-left"></i>{$lang['word_ledit']}</a></li>
			<li class="divider"></li>
            <li><a uid="{$row['id']}" class="dellink" href="?mod=links"><i class="fa fa-trash-o position-left text-danger"></i>{$lang['word_ldel']}</a></li>
          </ul>
        </div>
HTML;

		$entries .= "<tr>
        <td style=\"word-break: break-all;\"><div id=\"content_{$row['id']}\">{$row['word']}</div></td>
        <td style=\"word-break: break-all;\"><div id=\"url_{$row['id']}\">{$row['link']}</div><input type=\"hidden\" name=\"title_{$row['id']}\" id=\"title_{$row['id']}\" value=\"{$row['title']}\" /><input type=\"hidden\" name=\"rcount_{$row['id']}\" id=\"rcount_{$row['id']}\" value=\"{$row['rcount']}\" /><input type=\"hidden\" name=\"only_one_{$row['id']}\" id=\"only_one_{$row['id']}\" value=\"{$row['only_one']}\" /><input type=\"hidden\" name=\"targetblank_{$row['id']}\" id=\"targetblank_{$row['id']}\" value=\"{$row['targetblank']}\" /><input type=\"hidden\" name=\"replacearea_{$row['id']}\" id=\"replacearea_{$row['id']}\" value=\"{$row['replacearea']}\" /></td>
        <td align=\"center\">{$menu_link}</td>
        <td><input name=\"selected_tags[]\" value=\"{$row['id']}\" type=\"checkbox\" class=\"icheck\"></td>
        </tr>";


	}

	$db->free();

echo <<<HTML
<div class="table-responsive">
    <table class="table table-xs table-hover">
      <thead>
      <tr>
        <th>{$lang['links_tag']}</th>
        <th>{$lang['links_url']}</th>
        <th style="width: 70px">&nbsp;</th>
        <th style="width: 40px"><input class="icheck" type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all()"></th>
      </tr>
      </thead>
	  <tbody>
		{$entries}
	  </tbody>
	</table>
</div>
<div class="panel-footer">
	<div class="pull-right">
	<input class="btn bg-teal btn-sm btn-raised position-left" type="button" onclick="addLink()" value="{$lang['add_links']}">
	<select class="uniform position-left" name="action" data-dropdown-align-right="auto">
	<option value="">{$lang['edit_selact']}</option>
	<option value="mass_r_1">{$lang['links_m_act']} {$lang['links_area_2']}</option>
	<option value="mass_r_3">{$lang['links_m_act']} {$lang['links_area_4']}</option>
	<option value="mass_r_4">{$lang['links_m_act']} {$lang['links_area_5']}</option>
	<option value="mass_r_2">{$lang['links_m_act']} {$lang['links_area_3']}</option>
	<option value="mass_r_9">{$lang['links_m_act']} {$lang['links_area_8']}</option>
	<option value="mass_r_10">{$lang['links_m_act']} {$lang['links_area_9']}</option>
	<option value="mass_r_7">{$lang['links_m_act']} {$lang['links_area_6']}</option>
	<option value="mass_r_8">{$lang['links_m_act']} {$lang['links_area_7']}</option>
	<option value="mass_r_5">{$lang['links_m_act_1']} {$lang['links_m_act_2']}</option>
	<option value="mass_r_6">{$lang['links_m_act_1']} {$lang['links_m_act_3']}</option>
	<option value="mass_delete">{$lang['edit_seldel']}</option>
	</select><input class="btn bg-brown-600 btn-sm btn-raised" type="submit" value="{$lang['b_start']}">
	</div>
</div>
HTML;


}  else {

echo <<<HTML
<div class="panel-body">
<table width="100%">
    <tr>
        <td style="height:50px;"><div align="center">{$lang['links_not_found']}</div></td>
    </tr>
</table>
</div>
<div class="panel-footer"><input class="btn bg-teal btn-sm btn-raised position-left" type="button" onclick="addLink()" value="{$lang['add_links']}"></div>
HTML;

}

if (!$config['allow_links']) {

	$module_disabled = "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['module_disabled']}</div>";

} else $module_disabled = "";

echo <<<HTML
</div>
<div class="mb-20">{$npp_nav}</div>
</form>


<div class="alert alert-info alert-styled-left alert-arrow-left alert-component">{$lang['opt_linkshelp']}</div>{$module_disabled}
<script>  
<!--

	$(function() {
		$('.table').find('tr > td:last-child').find('input[type=checkbox]').on('change', function() {
			if($(this).is(':checked')) {
				$(this).parents('tr').addClass('warning');
			}
			else {
				$(this).parents('tr').removeClass('warning');
			}
		});
	});
	
    function search_submit(prm){
      document.navi.start_from.value=prm;
      document.navi.submit();
      return false;
    }

	function ckeck_uncheck_all() {
	    var frm = document.optionsbar;
	    for (var i=0;i<frm.elements.length;i++) {
	        var elmnt = frm.elements[i];
	        if (elmnt.type=='checkbox') {
	            if(frm.master_box.checked == true){ elmnt.checked=false; $(elmnt).parents('tr').removeClass('warning'); }
	            else{ elmnt.checked=true; $(elmnt).parents('tr').addClass('warning');}
	        }
	    }
	    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
	    else{ frm.master_box.checked = true; }
		
		$(frm.master_box).parents('tr').removeClass('warning');
		
		$.uniform.update();
	
	}
	function addLink() {
		var b = {};
	
		b[dle_act_lang[3]] = function() { 
						$(this).dialog("close");						
				    };
	
		b[dle_act_lang[2]] = function() { 
						if ( $("#dle-promt-tag").val().length < 1) {
							 $("#dle-promt-tag").addClass('ui-state-error');
						} else if ( $("#dle-promt-url").val().length < 1 ) {
							 $("#dle-promt-tag").removeClass('ui-state-error');
							 $("#dle-promt-url").addClass('ui-state-error');
						} else {
							var tag = $("#dle-promt-tag").val();
							var url = $("#dle-promt-url").val();
							var title = $("#dle-promt-title").val();
							var rcount = $("#dle-rcount").val();

							if ( $("#only-one").prop( "checked" ) ) { var onlyone = "1"; } else { var onlyone = "0"; }
							if ( $("#targetblank").prop( "checked" ) ) { var targetblank = "1"; } else { var targetblank = "0"; }

							var replacearea = $("#replacearea").val();

							$(this).dialog("close");
							$("#dlepopup").remove();

							document.location='?mod=links&user_hash={$dle_login_hash}&action=add&tag=' + encodeURIComponent(tag) + '&title=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(url)+ '&onlyone=' + onlyone + '&targetblank=' + targetblank + '&rcount=' + rcount +'&replacearea='+replacearea;

						}				
					};

		$("#dlepopup").remove();

		$("body").append("<div id='dlepopup' title='{$lang['add_links_new']}' style='display:none'>{$lang['add_links_tag']}<br /><input type='text' name='dle-promt-tag' id='dle-promt-tag' class='classic' style='width:100%;' value=''><br><br>{$lang['add_links_url']}<br><input type='text' name='dle-promt-url' id='dle-promt-url' class='classic' style='width:100%;' value='http://'><br><br>{$lang['bb_url_tooltip']}<br><input type='text' name='dle-promt-title' id='dle-promt-title' class='classic' style='width:100%;' value=''><br><br>{$lang['links_rcount']} <input type='text' name='dle-rcount' id='dle-rcount' class='classic' style='width:50px;' value='0'/> {$lang['links_rcount_1']}<br /><br />{$lang['links_area_1']} <select name='replacearea' id='replacearea' class='ui-widget-content ui-corner-all'><option value='1'>{$lang['links_area_2']}</option><option value='2'>{$lang['links_area_3']}</option><option value='3'>{$lang['links_area_4']}</option><option value='4'>{$lang['links_area_5']}</option><option value='5'>{$lang['links_area_8']}</option><option value='6'>{$lang['links_area_9']}</option></select><br /><br /><input type='checkbox' name='only-one' id='only-one' value=''><label for='only-one'>&nbsp;{$lang['add_links_one']}</label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='targetblank' id='targetblank' value=''><label for='targetblank'>&nbsp;{$lang['links_target']}</label></div>");
	
		$('#dlepopup').dialog({
			autoOpen: true,
			width: 600,
			resizable: false,
			buttons: b
		});

	}

$(function(){

		var tag_name = '';

		$('.dellink').click(function(){

			tag_name = $('#content_'+$(this).attr('uid')).text();
			var urlid = $(this).attr('uid');

		    DLEconfirm( '{$lang['tagscloud_del']} <b>&laquo;'+tag_name+'&raquo;</b> {$lang['tagscloud_del_2']}', '{$lang['p_confirm']}', function () {

				document.location="?mod=links&start_from={$start_from}&user_hash={$dle_login_hash}{$urlsearch}&action=delete&id=" + urlid;

			} );

			return false;
		});


		$('.editlink').click(function(){

			var tag = $('#content_'+$(this).attr('uid')).text();
			var url = $('#url_'+$(this).attr('uid')).text();
			var onlyone = $('#only_one_'+$(this).attr('uid')).val();
			var targetblank = $('#targetblank_'+$(this).attr('uid')).val();
			var title = $('#title_'+$(this).attr('uid')).val();
			title = title.replace(/'/g, "&#039;");
			
			var rcount = $('#rcount_'+$(this).attr('uid')).val();
			var replacearea = $('#replacearea_'+$(this).attr('uid')).val();
			var urlid = $(this).attr('uid');

			var b = {};
		
			b[dle_act_lang[3]] = function() { 
							$(this).dialog("close");						
					    };
		
			b[dle_act_lang[2]] = function() { 
							if ( $("#dle-promt-tag").val().length < 1) {
								 $("#dle-promt-tag").addClass('ui-state-error');
							} else if ( $("#dle-promt-url").val().length < 1 ) {
								 $("#dle-promt-tag").removeClass('ui-state-error');
								 $("#dle-promt-url").addClass('ui-state-error');
							} else {
								var tag = $("#dle-promt-tag").val();
								var title = $("#dle-promt-title").val();
								var url = $("#dle-promt-url").val();
								var replacearea = $("#replacearea").val();
								var rcount = $("#dle-rcount").val();
	
								if ( $("#only-one").prop( "checked" ) ) { var onlyone = "1"; } else { var onlyone = "0"; }
								if ( $("#targetblank").prop( "checked" ) ) { var targetblank = "1"; } else { var targetblank = "0"; }
							
								$(this).dialog("close");
								$("#dlepopup").remove();
	
								document.location="?mod=links&start_from={$start_from}&user_hash={$dle_login_hash}{$urlsearch}&action=edit&tag=" + encodeURIComponent(tag) + '&title=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(url)+ '&onlyone=' + onlyone + '&targetblank=' + targetblank + '&rcount=' + rcount + '&replacearea='+replacearea+'&id=' + urlid;
	
							}				
						};
	
			$("#dlepopup").remove();

			$("body").append("<div id='dlepopup' title='{$lang['add_links_new']}' style='display:none'><br />{$lang['add_links_tag']}<br /><input type='text' name='dle-promt-tag' id='dle-promt-tag' class='classic' style='width:100%;' value=\""+tag+"\"/><br /><br />{$lang['add_links_url']}<br /><input type='text' name='dle-promt-url' id='dle-promt-url' class='classic' style='width:100%;' value='"+url+"'/><br><br>{$lang['bb_url_tooltip']}<br><input type='text' name='dle-promt-title' id='dle-promt-title' class='classic' style='width:100%;' value='"+title+"'><br><br>{$lang['links_rcount']} <input type='text' name='dle-rcount' id='dle-rcount' class='classic' style='width:50px;' value='"+rcount+"'/> {$lang['links_rcount_1']}<br /><br />{$lang['links_area_1']} <select name='replacearea' id='replacearea' class='ui-widget-content ui-corner-all'><option value='1'>{$lang['links_area_2']}</option><option value='2'>{$lang['links_area_3']}</option><option value='3'>{$lang['links_area_4']}</option><option value='4'>{$lang['links_area_5']}</option><option value='5'>{$lang['links_area_8']}</option><option value='6'>{$lang['links_area_9']}</option></select><br /><br /><input type='checkbox' name='only-one' id='only-one' value=''><label for='only-one'>&nbsp;{$lang['add_links_one']}</label>&nbsp;&nbsp;&nbsp;<input type='checkbox' name='targetblank' id='targetblank' value=''><label for='targetblank'>&nbsp;{$lang['links_target']}</label><input type='hidden' name='url-id' id='url-id' value='"+urlid+"'></div>");
		
			$('#dlepopup').dialog({
				autoOpen: true,
				width: 600,
				resizable: false,
				buttons: b
			});

			if ( onlyone == 1 ) {  $("#only-one").prop( "checked", "checked" ); }
			if ( targetblank == 1 ) {  $("#targetblank").prop( "checked", "checked" ); }

			$('#replacearea').val(replacearea);

			return false;
		});

});
//-->
</script>
HTML;


echofooter();
?>