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
 File: redirects.php
-----------------------------------------------------
 Use: manage the redirects on the website
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


function clear_url_for_redirect ($a) {
	if (!$a) return '';
	
	if (strpos($a, "//") === 0) $a = "http:".$a;
	$a = parse_url($a);
	
	if ($a['query']) $a = $a['path'].'?'.$a['query']; else $a = $a['path'];
	
	$a = preg_replace( '#[/]+#i', '/', $a );
	
	if($a[0] != '/') $a = '/'.$a;
	
	return $a;
}

if ($_GET['action'] == "delete") {
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$id = intval ( $_GET['id'] );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '104', '')" );
	$db->query( "DELETE FROM " . PREFIX . "_redirects WHERE id='{$id}'" );

	@unlink( ENGINE_DIR . '/cache/system/redirects.php' );
	header( "Location: ?mod=redirects&start_from={$start_from}{$urlsearch}" );
	die();

}

if ($_POST['action'] == "mass_delete") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( !$_POST['selected_tags'] ) {
		msg( "error", $lang['mass_error'], $lang['redirects_err_4'], "?mod=redirects&start_from={$start_from}{$urlsearch}" );
	}

	foreach ( $_POST['selected_tags'] as $id ) {
		$id = intval($id);
		$db->query( "DELETE FROM " . PREFIX . "_redirects WHERE id='{$id}'" );
	}

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '104', '')" );

	@unlink( ENGINE_DIR . '/cache/system/redirects.php' );
	header( "Location: ?mod=redirects&start_from={$start_from}{$urlsearch}" );
	die();

}

if ($_GET['action'] == "add") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$oldurl = clear_url_for_redirect(strip_tags( stripslashes( trim( $_GET['oldurl'] ))));
	$newurl = strip_tags( stripslashes( trim( $_GET['newurl'] )));

	$oldurl = str_ireplace( "document.cookie", "d&#111;cument.cookie", $oldurl );
	$oldurl = preg_replace( "/javascript:/i", "j&#1072;vascript:", $oldurl );
	$oldurl = preg_replace( "/data:/i", "d&#1072;ta:", $oldurl );
	$newurl = str_ireplace( "document.cookie", "d&#111;cument.cookie", $newurl );
	$newurl = preg_replace( "/javascript:/i", "j&#1072;vascript:", $newurl );
	$newurl = preg_replace( "/data:/i", "d&#1072;ta:", $newurl );

	if (!$oldurl OR !$newurl ) {
		msg( "error", $lang['opt_error'], $lang['redirects_err'], "?mod=redirects&start_from={$start_from}" );
	}
	
	if ($oldurl == $newurl OR clear_url_for_redirect ($oldurl) == clear_url_for_redirect ($newurl) ) {
		msg( "error", $lang['opt_error'], $lang['redirects_err_2'], "?mod=redirects&start_from={$start_from}" );
	}

	$oldurl = @$db->safesql( $oldurl );
	$newurl = @$db->safesql( $newurl );
	
	$row = $db->super_query( "SELECT `from` FROM " . PREFIX . "_redirects WHERE `from` = '{$oldurl}'" );

	if( $row['from'] ) {
		msg( "error", $lang['opt_error'], $lang['redirects_err_3'], "?mod=redirects&start_from={$start_from}" );
	}
	
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '102', '{$oldurl}')" );
	$db->query( "INSERT INTO " . PREFIX . "_redirects (`from`, `to`) values ('{$oldurl}', '{$newurl}')" );

	@unlink( ENGINE_DIR . '/cache/system/redirects.php' );
	header( "Location: ?mod=redirects" );
	die();
}

if ($_GET['action'] == "edit") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$id = intval ( $_GET['id'] );
	$oldurl = clear_url_for_redirect(strip_tags( stripslashes( trim( $_GET['oldurl'] ))));
	$newurl = strip_tags( stripslashes( trim( $_GET['newurl'] )));

	$oldurl = str_ireplace( "document.cookie", "d&#111;cument.cookie", $oldurl );
	$oldurl = preg_replace( "/javascript:/i", "j&#1072;vascript:", $oldurl );
	$oldurl = preg_replace( "/data:/i", "d&#1072;ta:", $oldurl );
	$newurl = str_ireplace( "document.cookie", "d&#111;cument.cookie", $newurl );
	$newurl = preg_replace( "/javascript:/i", "j&#1072;vascript:", $newurl );
	$newurl = preg_replace( "/data:/i", "d&#1072;ta:", $newurl );

	if (!$oldurl OR !$newurl ) {
		msg( "error", $lang['opt_error'], $lang['redirects_err'], "?mod=redirects&start_from={$start_from}{$urlsearch}" );
	}
	
	if ($oldurl == $newurl OR clear_url_for_redirect ($oldurl) == clear_url_for_redirect ($newurl) ) {
		msg( "error", $lang['opt_error'], $lang['redirects_err_2'], "?mod=redirects&start_from={$start_from}{$urlsearch}" );
	}

	$oldurl = @$db->safesql( $oldurl );
	$newurl = @$db->safesql( $newurl );
	
	$row = $db->super_query( "SELECT `from` FROM " . PREFIX . "_redirects WHERE `from` = '{$oldurl}' AND id != '{$id}'" );

	if( $row['from'] ) {
		msg( "error", $lang['opt_error'], $lang['redirects_err_3'], "?mod=redirects&start_from={$start_from}{$urlsearch}" );
	}	

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '103', '{$oldurl}')" );
	$db->query( "UPDATE " . PREFIX . "_redirects SET `from`='{$oldurl}', `to`='{$newurl}' WHERE id='{$id}'" );

	@unlink( ENGINE_DIR . '/cache/system/redirects.php' );
	header( "Location: ?mod=redirects&start_from={$start_from}{$urlsearch}" );
	die();
}

echoheader( "<i class=\"fa fa-external-link position-left\"></i><span class=\"text-semibold\">{$lang['opt_redirects']}</span>", $lang['header_r_1'] );

echo <<<HTML
<form action="?mod=redirects" method="get" name="navi" id="navi">
<input type="hidden" name="mod" value="redirects">
<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
<input type="hidden" name="searchword" value="{$searchword}">
</form>
<form action="?mod=redirects" method="post" name="optionsbar" id="optionsbar">
<input type="hidden" name="mod" value="redirects">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_redirects']}
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
  $where = "WHERE `from` like '%$searchword%' OR `to` like '%$searchword%' ";
  $lang['links_not_found'] = $lang['tags_s_not_found'];
  
} else $where = "";

$result_count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_redirects {$where}");
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

	$db->query("SELECT * FROM " . PREFIX . "_redirects {$where}ORDER BY id DESC LIMIT {$start_from},{$news_per_page}");

	while($row = $db->get_row()) {
	
		$menu_link = <<<HTML
        <div class="btn-group">
          <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
          <ul class="dropdown-menu text-left dropdown-menu-right">
            <li><a uid="{$row['id']}" href="?mod=redirects" class="editlink"><i class="fa fa-pencil-square-o position-left"></i>{$lang['word_ledit']}</a></li>
			<li class="divider"></li>
            <li><a uid="{$row['id']}" class="dellink" href="?mod=redirects"><i class="fa fa-trash-o position-left text-danger"></i> {$lang['word_ldel']}</a></li>
          </ul>
        </div>
HTML;
		$row['from'] = htmlspecialchars($row['from'], ENT_QUOTES, $config['charset'] );
		$row['to'] = htmlspecialchars($row['to'], ENT_QUOTES, $config['charset'] );
		
		$entries .= "<tr>
        <td style=\"word-break: break-all;\"><div id=\"content_{$row['id']}\">{$row['from']}</div></td>
        <td style=\"word-break: break-all;\"><div id=\"url_{$row['id']}\">{$row['to']}</div></td>
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
        <th>{$lang['header_r_2']}</th>
        <th>{$lang['header_r_3']}</th>
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
	<div class="pull-left"><button class="btn bg-teal btn-sm btn-raised" type="button" onclick="addLink()"><i class="fa fa-plus-circle position-left"></i>{$lang['add_links']}</button></div>
	<div class="pull-right">
	<select class="uniform position-left" name="action">
	<option value="">{$lang['edit_selact']}</option>
	<option value="mass_delete">{$lang['edit_seldel']}</option>
	</select><input class="btn bg-brown-600 btn-sm btn-raised" type="submit" value="{$lang['b_start']}">
	</div>
</div>
HTML;


}  else {

if($where) $lang['redirects_not_found'] = $lang['redirects_not_found_1'];

echo <<<HTML
<div class="panel-body">
<table width="100%">
    <tr>
        <td style="height:50px;"><div align="center"><br /><br />{$lang['redirects_not_found']}<br /><br></a></div></td>
    </tr>
</table>
</div>
<div class="panel-footer">
	<button class="btn bg-teal btn-sm btn-raised" type="button" onclick="addLink()"><i class="fa fa-plus-circle position-left"></i>{$lang['add_links']}</button>
</div>

HTML;

}

if (!$config['allow_redirects']) {

	$module_disabled = "<div class=\"alert alert-warning alert-styled-left alert-arrow-left alert-component\">{$lang['module_disabled']}</div>";

} else $module_disabled = "";

echo <<<HTML
</div>
<div class="mb-20">{$npp_nav}</div>
</form>

<div class="alert alert-info alert-styled-left alert-arrow-left alert-component">{$lang['opt_redirectshelp']}</div>{$module_disabled}

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
	            if(frm.master_box.checked == true){ elmnt.checked=false; $(elmnt).parents('tr').removeClass('warning');}
	            else{ elmnt.checked=true; $(elmnt).parents('tr').addClass('warning'); }
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
						if ( $("#dle-promt-oldurl").val().length < 1) {
							 $("#dle-promt-oldurl").addClass('ui-state-error');
						} else if ( $("#dle-promt-newurl").val().length < 1 ) {
							 $("#dle-promt-oldurl").removeClass('ui-state-error');
							 $("#dle-promt-newurl").addClass('ui-state-error');
						} else {
							var oldurl = $("#dle-promt-oldurl").val();
							var newurl = $("#dle-promt-newurl").val();

							$(this).dialog("close");
							$("#dlepopup").remove();

							document.location='?mod=redirects&user_hash={$dle_login_hash}&action=add&oldurl=' + encodeURIComponent(oldurl) + '&newurl=' + encodeURIComponent(newurl);

						}				
					};

		$("#dlepopup").remove();

		$("body").append("<div id='dlepopup' title='{$lang['add_links_new']}' style='display:none'>{$lang['input_oldurl']}<br><input type='text' name='dle-promt-oldurl' id='dle-promt-oldurl' class='classic' style='width:100%;' value=''/><br><br>{$lang['input_newurl']}<br /><input type='text' name='dle-promt-newurl' id='dle-promt-newurl' class='classic' style='width:100%;' value=''/></div>");
	
		$('#dlepopup').dialog({
			autoOpen: true,
			width: 500,
			resizable: false,
			buttons: b
		});

	}

$(function(){

		var old_link = '';

		$('.dellink').click(function(){

			old_link = $('#content_'+$(this).attr('uid')).text();
			var urlid = $(this).attr('uid');

		    DLEconfirm( '{$lang['redirects_del']} <b>&laquo;'+old_link+'&raquo;</b> {$lang['redirects_del_1']}', '{$lang['p_confirm']}', function () {

				document.location="?mod=redirects&start_from={$start_from}&user_hash={$dle_login_hash}{$urlsearch}&action=delete&id=" + urlid;

			} );

			return false;
		});


		$('.editlink').click(function(){

			var oldurl = $('#content_'+$(this).attr('uid')).text();
			var newurl = $('#url_'+$(this).attr('uid')).text();
			var urlid = $(this).attr('uid');
			
			var b = {};
		
			b[dle_act_lang[3]] = function() { 
							$(this).dialog("close");						
					    };
		
			b[dle_act_lang[2]] = function() { 
						if ( $("#dle-promt-oldurl").val().length < 1) {
							 $("#dle-promt-oldurl").addClass('ui-state-error');
						} else if ( $("#dle-promt-newurl").val().length < 1 ) {
							 $("#dle-promt-oldurl").removeClass('ui-state-error');
							 $("#dle-promt-newurl").addClass('ui-state-error');
						} else {
							var oldurl = $("#dle-promt-oldurl").val();
							var newurl = $("#dle-promt-newurl").val();
							
							$(this).dialog("close");
							$("#dlepopup").remove();
	
							document.location='?mod=redirects&user_hash={$dle_login_hash}{$urlsearch}&start_from={$start_from}&action=edit&id='+urlid+'&oldurl=' + encodeURIComponent(oldurl) + '&newurl=' + encodeURIComponent(newurl);
	
						}				
					};
	
			$("#dlepopup").remove();

			$("body").append("<div id='dlepopup' title='{$lang['add_links_new']}' style='display:none'>{$lang['input_oldurl']}<br><input type='text' name='dle-promt-oldurl' id='dle-promt-oldurl' class='classic' style='width:100%;' value='"+oldurl+"'/><br /><br />{$lang['input_newurl']}<br /><input type='text' name='dle-promt-newurl' id='dle-promt-newurl' class='classic' style='width:100%;' value='"+newurl+"'/></div>");
		
			$('#dlepopup').dialog({
				autoOpen: true,
				width: 500,
				resizable: false,
				buttons: b
			});

			return false;
		});

});
//-->
</script>
HTML;


echofooter();
?>