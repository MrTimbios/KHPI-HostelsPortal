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
 File: tagscloud.php
-----------------------------------------------------
 Use: manageme tags cloud
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$user_group[$member_id['user_group']]['admin_tagscloud'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

function compare_tags($a, $b) {
	
	if( $a['tag'] == $b['tag'] ) return 0;
	
	return strcasecmp( $a['tag'], $b['tag'] );

}

$start_from = intval( $_REQUEST['start_from'] );
$news_per_page = 50;

if( $start_from < 0 ) $start_from = 0;

if ($_REQUEST['searchword']) {
  
  $searchword = urldecode ( $_REQUEST['searchword'] );
  
  if( @preg_match( "/[\||\<|\>]/", $searchword ) ) $searchword = "";
  else $searchword = htmlspecialchars( strip_tags( stripslashes( trim( $searchword ) ) ), ENT_COMPAT, $config['charset'] );
  
} else $searchword = "";

if ($searchword) $urlsearch = "&searchword={$searchword}"; else $urlsearch = "";

if ($_POST['action'] == "mass_delete") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$selected_tags = $_POST['selected_tags'];

	if( ! $selected_tags ) {
		msg( "error", $lang['mass_error'], $lang['mass_tags_err'], "?mod=tagscloud&start_from={$start_from}" );
	}

	foreach ( $selected_tags as $name ) {

		if( @preg_match( "/[\||\<|\>]/", $name ) ) $name = "";
		else $name = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $name ) ) ), ENT_COMPAT, $config['charset'] ) );

		if (!$name) { msg( "error", $lang['mass_error'], $lang['mass_tags_err_1'], "?mod=tagscloud&start_from={$start_from}" ); die(); }

		$db->query ( "SELECT news_id FROM " . PREFIX . "_tags WHERE tag = '{$name}'" );

		$tag_array = array ();
				
		while ( $row = $db->get_row () ) {
					
			$tag_array[] = $row['news_id'];
				
		}
		$db->free ();

		if (count ( $tag_array )) {
					
			$tag_array = "(" . implode ( ",", $tag_array ) . ")";
	
			$sql_result = $db->query( "SELECT id, tags FROM " . PREFIX . "_post WHERE id IN {$tag_array}" );
	
			while ( $row = $db->get_row( $sql_result ) ) {
	
				$row['tags'] = explode( ",", $row['tags'] );
	
				$tags = array ();
				
				foreach ( $row['tags'] as $value ) {
					
					$value = trim( $value );
					if ( stripslashes($value) == stripslashes($name) ) continue;
					$tags[] = $value;
				}
	
				$tags = array_unique($tags);
	
				if ( count($tags) ) $post_tags = @$db->safesql( implode( ", ", $tags ) ); else $post_tags = "";
	
				$db->query( "UPDATE " . PREFIX . "_post SET tags='{$post_tags}' WHERE id='{$row['id']}'" );
	
				$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$row['id']}'" );
	
				if ( count($tags) ) {
	
					$tagcloud = array ();
		
					foreach ( $tags as $value ) {
									
						$tagcloud[] = "('" . $row['id'] . "', '" . $db->safesql(trim( $value )) . "')";
					}
		
					$tagcloud = implode( ", ", $tagcloud );
					$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tagcloud );
				}
			}
	
			$db->query( "DELETE FROM " . PREFIX . "_tags WHERE tag = '{$name}'" );
			$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '61', '{$name}')" );

		}

	}

	clear_cache();
	header( "Location: ?mod=tagscloud&start_from={$start_from}{$urlsearch}" ); die();

}


if ($_GET['action'] == "delete") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( @preg_match( "/[\||\<|\>]/", $_GET['name'] ) ) $_GET['name'] = "";
	else $_GET['name'] = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_GET['name'] ) ) ), ENT_COMPAT, $config['charset'] ) );

	if (!$_GET['name']) { header( "Location: ?mod=tagscloud" ); die(); }

	$db->query ( "SELECT news_id FROM " . PREFIX . "_tags WHERE tag = '{$_GET['name']}'" );
			
	$tag_array = array ();
			
	while ( $row = $db->get_row () ) {
				
		$tag_array[] = $row['news_id'];
			
	}
	$db->free ();

	if (count ( $tag_array )) {
				
		$tag_array = "(" . implode ( ",", $tag_array ) . ")";

		$sql_result = $db->query( "SELECT id, tags FROM " . PREFIX . "_post WHERE id IN {$tag_array}" );

		while ( $row = $db->get_row( $sql_result ) ) {

			$row['tags'] = explode( ",", $row['tags'] );

			$tags = array ();
			
			foreach ( $row['tags'] as $value ) {
				
				$value = trim( $value );
				if ( stripslashes($value) == stripslashes($_GET['name']) ) continue;
				$tags[] = $value;
			}

			$tags = array_unique($tags);

			if ( count($tags) ) $post_tags = $db->safesql( implode( ", ", $tags ) ); else $post_tags = "";

			$db->query( "UPDATE " . PREFIX . "_post SET tags='{$post_tags}' WHERE id='{$row['id']}'" );

			$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$row['id']}'" );

			if ( count($tags) ) {

				$tagcloud = array ();
	
				foreach ( $tags as $value ) {
								
					$tagcloud[] = "('" . $row['id'] . "', '" . $db->safesql(trim( $value )) . "')";
				}
	
				$tagcloud = implode( ", ", $tagcloud );
				$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tagcloud );
			}
		}

		$db->query( "DELETE FROM " . PREFIX . "_tags WHERE tag = '{$_GET['name']}'" );
		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '61', '{$_GET['name']}')" );

	}

	clear_cache();
	header( "Location: ?mod=tagscloud&start_from={$start_from}{$urlsearch}" ); die();
}

if ($_GET['action'] == "edit") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( @preg_match( "/[\||\<|\>]/", $_GET['oldname'] ) ) $_GET['oldname'] = "";
	else $_GET['oldname'] = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_GET['oldname'] ) ) ), ENT_COMPAT, $config['charset'] ) );

	if( @preg_match( "/[\||\<|\>]/", $_GET['newname'] ) ) $_GET['newname'] = "";
	else $_GET['newname'] = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_GET['newname'] ) ) ), ENT_COMPAT, $config['charset'] ) );

	$_GET['newname'] = str_replace (",", " ", $_GET['newname']);

	if (!$_GET['oldname'] OR !$_GET['newname']) { header( "Location: ?mod=tagscloud" ); die(); }

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '62', '{$_GET['oldname']} to: {$_GET['newname']}')" );

	$db->query ( "SELECT news_id FROM " . PREFIX . "_tags WHERE tag = '{$_GET['oldname']}'" );
			
	$tag_array = array ();
			
	while ( $row = $db->get_row () ) {
				
		$tag_array[] = $row['news_id'];
			
	}
	$db->free ();

	if (count ( $tag_array )) {
				
		$tag_array = "(" . implode ( ",", $tag_array ) . ")";

		$sql_result = $db->query( "SELECT id, tags FROM " . PREFIX . "_post WHERE id IN {$tag_array}" );

		while ( $row = $db->get_row( $sql_result ) ) {

			$row['tags'] = explode( ",", $row['tags'] );

			$tags = array ();
			
			foreach ( $row['tags'] as $value ) {
				
				$value = trim( $value );
				if ( stripslashes($value) == stripslashes($_GET['oldname']) ) $value = stripslashes($_GET['newname']);
				$tags[] = $value;
			}

			if ( count($tags) ) { 

				$tags = array_unique($tags);
				$post_tags = $db->safesql(implode( ", ", $tags ));

			} else $post_tags = "";

			$db->query( "UPDATE " . PREFIX . "_post SET tags='{$post_tags}' WHERE id='{$row['id']}'" );

			$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$row['id']}'" );

			if ( count($tags) ) {

				$tagcloud = array ();
	
				foreach ( $tags as $value ) {
								
					$tagcloud[] = "('" . $row['id'] . "', '" . $db->safesql(trim( $value )) . "')";
				}

				$tagcloud = implode( ", ", $tagcloud );
				$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tagcloud );

			}
		}
	}

	clear_cache();
	header( "Location: ?mod=tagscloud&start_from={$start_from}{$urlsearch}" ); die();

}

echoheader( "<i class=\"fa fa-tags position-left\"></i><span class=\"text-semibold\">{$lang['header_tc_1']}</span>", $lang['header_tc_2'] );

echo <<<HTML
<form action="?mod=tagscloud" method="get" name="navi" id="navi">
<input type="hidden" name="mod" value="tagscloud">
<input type="hidden" name="searchword" value="{$searchword}">
<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
</form>

<form action="?mod=tagscloud" method="post" name="optionsbar" id="optionsbar">
<input type="hidden" name="mod" value="tagscloud">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_tagscloud']}
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


$tags = array();
$list = array();

$i = $start_from;

if ( $searchword ) {
  
  $searchword = @$db->safesql($searchword);
  $where = "WHERE tag like '%{$searchword}%' ";
  $lang['tagscloud_not_found'] = $lang['tags_s_not_found'];
  
} else $where = "";

$db->query("SELECT tag, COUNT(*) AS count FROM " . PREFIX . "_tags {$where}GROUP BY tag LIMIT {$start_from},{$news_per_page}");

while($row = $db->get_row()){
  
  if( $row['tag'] ) {
	  $tags[$row['tag']] = $row['count'];
	  $i ++;
  }
  
}
$db->free();

$result_count = $db->super_query("SELECT COUNT(DISTINCT tag) as count FROM " . PREFIX . "_tags {$where}");
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

if ( count($tags) ) {

	foreach ($tags as $tag => $value) {
	
		$list[$tag]['tag']   = $tag;
		$list[$tag]['count']  = $value;
	
	}
	usort ($list, "compare_tags");

	$i = 0;
	$entries = "";

	foreach ($list as $value) {

		if ( trim($value['tag']) ) {

		$i ++;
		
		$url_tag = str_replace(array("&#039;", "&quot;", "&amp;"), array("'", '"', "&"), $value['tag']);
		
		if( $config['allow_alt_url'] ) $link = "<a href=\"" . $config['http_home_url'] . "tags/" . rawurlencode( $url_tag ) . "/\" target=\"_blank\"><i class=\"fa fa-eye position-left\"></i>" . $lang['comm_view'] . "</a>";
		else $link = "<a href=\"{$config['http_home_url']}index.php?do=tags&amp;tag=" . rawurlencode( $url_tag ) . "\" target=\"_blank\"><i class=\"fa fa-eye position-left\"></i>" . $lang['comm_view'] . "</a>";

		$menu_link = <<<HTML
        <div class="btn-group">
          <a href="#" class="dropdown-toggle nocolor" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
          <ul class="dropdown-menu text-left dropdown-menu-right">
            <li>{$link}</li>
            <li><a uid="{$i}" class="editlink" href="?mod=tagscloud"><i class="fa fa-pencil-square-o position-left"></i>{$lang['word_ledit']}</a></li>
			<li class="divider"></li>
            <li><a uid="{$i}" class="dellink" href="?mod=tagscloud"><i class="fa fa-trash-o position-left text-danger"></i>{$lang['word_ldel']}</a></li>
          </ul>
        </div>
HTML;

		$value['count'] = number_format( $value['count'], 0, ',', ' ');

		$entries .= "<tr>
        <td><div id=\"content_{$i}\">{$value['tag']}</div></td>
        <td class=\"text-center text-nowrap\"><b>{$value['count']}</b></td>
        <td>{$menu_link}</td>
        <td><input name=\"selected_tags[]\" value=\"{$value['tag']}\" type=\"checkbox\" class=\"icheck\"></td>
        </tr>";

		}

	}

echo <<<HTML
<div class="box-content table-responsive">
    <table class="table table-xs table-hover">
      <thead>
      <tr>
        <th>{$lang['tagscloud_name']}</th>
        <th class="text-center" style="width: 200px;">{$lang['tagscloud_count']}</th>
        <th style="width: 70px">&nbsp;</th>
        <th style="width: 40px"><input type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all()" class="icheck"></th>
      </tr>
      </thead>
	  <tbody>
		{$entries}
	  </tbody>
	</table>
</div>
<div class="panel-footer">
	<div class="pull-right">
	<select name="action" class="uniform">
<option value="">{$lang['edit_selact']}</option>
<option value="mass_delete">{$lang['edit_seldel']}</option>
</select><input class="btn bg-brown-600 btn-sm btn-raised position-right" type="submit" value="{$lang['b_start']}">
	</div>
</div>
HTML;


}  else {

echo <<<HTML
<div class="panel-body">
<table width="100%">
    <tr>
        <td style="height:50px;"><div align="center">{$lang['tagscloud_not_found']}</div></td>
    </tr>
</table>
</div>
HTML;

}

echo <<<HTML
</div>
<div class="mb-20">{$npp_nav}</div>
</form>
<script>  
<!-- 
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

	$(function(){
	
		$('.table').find('tr > td:last-child').find('input[type=checkbox]').on('change', function() {
			if($(this).is(':checked')) {
				$(this).parents('tr').addClass('warning');
			}
			else {
				$(this).parents('tr').removeClass('warning');
			}
		});
		
		var tag_name = '';
		var safe_name = '';

		$('.dellink').click(function(){

			tag_name = $('#content_'+$(this).attr('uid')).text();

		    DLEconfirm( '{$lang['tagscloud_del']} <b>&laquo;'+tag_name+'&raquo;</b> {$lang['tagscloud_del_1']}', '{$lang['p_confirm']}', function () {

				document.location="?mod=tagscloud&start_from={$start_from}&user_hash={$dle_login_hash}{$urlsearch}&action=delete&name=" + encodeURIComponent(tag_name) + '';

			} );

			return false;
		});


		$('.editlink').click(function(){

			tag_name = $('#content_'+$(this).attr('uid')).text();
			safe_name = tag_name.replace(/"/g, "&quot;");
			
			DLEprompt('{$lang['tagscloud_edit_1']}', safe_name, '{$lang['tagscloud_edit']}', function (r) {
				if (tag_name != r) {	
					document.location="?mod=tagscloud&start_from={$start_from}&user_hash={$dle_login_hash}{$urlsearch}&action=edit&oldname=" + encodeURIComponent(tag_name) + '&newname=' + encodeURIComponent(r);
				}		
			});

			return false;
		});

	});
//-->
</script>
HTML;


echofooter();
?>