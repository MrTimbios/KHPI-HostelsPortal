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
 File: static.php
-----------------------------------------------------
 Use: edit static pages
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$user_group[$member_id['user_group']]['admin_static'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));

$parse = new ParseFilter();

function SelectSkin($skin) {
	global $lang;
	
	$templates_list = array ();
	
	$handle = opendir( './templates' );
	
	while ( false !== ($file = readdir( $handle )) ) {
		if( is_dir( "./templates/$file" ) and ($file != "." and $file != "..") ) {
			$templates_list[] = $file;
		}
	}
	closedir( $handle );
	
	$skin_list = "<select class=\"uniform\" name=\"skin_name\">";
	$skin_list .= "<option value=\"\">" . $lang['cat_skin_sel'] . "</option>";
	
	foreach ( $templates_list as $single_template ) {
		if( $single_template == $skin ) $selected = " selected";
		else $selected = "";
		$skin_list .= "<option value=\"$single_template\"" . $selected . ">$single_template</option>";
	}
	$skin_list .= '</select>';
	
	return $skin_list;
}

if( !$action ) $action = "list";

if( $action == "list" ) {
	
	$_SESSION['admin_referrer'] = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, $config['charset'] );

	echoheader( "<i class=\"fa fa-file-text-o position-left\"></i><span class=\"text-semibold\">{$lang['opt_sm_static']}</span>", $lang['header_st_1'] );
	
	$search_field = $db->safesql( trim( htmlspecialchars( stripslashes( @urldecode( $_GET['search_field'] ) ), ENT_QUOTES, $config['charset'] ) ) );
	$search_field2 = $db->safesql( addslashes(addslashes(trim( urldecode( $_REQUEST['search_field'] ) ) ) ) );
	
	if ($_GET['fromnewsdate']) $fromnewsdate = strtotime( $_GET['fromnewsdate'] ); else $fromnewsdate = "";
	if ($_GET['tonewsdate']) $tonewsdate = strtotime( $_GET['tonewsdate'] ); else $tonewsdate = "";


	if ($fromnewsdate === -1 OR !$fromnewsdate) $fromnewsdate = "";
	if ($tonewsdate === -1 OR !$tonewsdate)   $tonewsdate = "";
	
	$start_from = intval( $_GET['start_from'] );
	$news_per_page = intval( $_GET['news_per_page'] );
	$gopage = intval( $_REQUEST['gopage'] );

	if( ! $news_per_page or $news_per_page < 1 ) {
		$news_per_page = 50;
	}
	if( $gopage ) $start_from = ($gopage - 1) * $news_per_page;
	
	if( $start_from < 0 ) $start_from = 0;

	$where = array ();
	$where[] = "name != 'dle-rules-page'";
	
	if( $search_field ) {
		
		$search_field = preg_replace('/\s+/u', '%', $search_field);
		
		if(!$_REQUEST['search_area']) {
			$where[] = "(name like '%$search_field%' OR template like '%$search_field2%' OR descr like '%$search_field%')";
		} elseif($_REQUEST['search_area'] == 1) {
			$where[] = "name like '%{$search_field}%'";
		} elseif($_REQUEST['search_area'] == 2) {
			$where[] = "descr like '%{$search_field}%'";
		} elseif($_REQUEST['search_area'] == 3) {
			$where[] = "template like '%{$search_field2}%'";
		}
	
	}
	
	if( $fromnewsdate != "" ) {
		
		$where[] = "date >= '$fromnewsdate'";
	
	}
	
	if( $tonewsdate != "" ) {
		
		$where[] = "date <= '$tonewsdate'";
	
	}
	
	if( count( $where ) ) {
		
		$where = implode( " AND ", $where );
		$where = " WHERE " . $where;
	
	} else {
		$where = "";
	}
	
	$order_by = array ();
	
	if( $_REQUEST['search_order_t'] == "asc" or $_REQUEST['search_order_t'] == "desc" ) $search_order_t = $_REQUEST['search_order_t'];
	else $search_order_t = "";
	if( $_REQUEST['search_order_d'] == "asc" or $_REQUEST['search_order_d'] == "desc" ) $search_order_d = $_REQUEST['search_order_d'];
	else $search_order_d = "";
	if( $_REQUEST['search_order_v'] == "asc" or $_REQUEST['search_order_v'] == "desc" ) $search_order_v = $_REQUEST['search_order_v'];
	else $search_order_v = "";
	
	if( ! empty( $search_order_t ) ) {
		$order_by[] = "name $search_order_t";
	}
	if( ! empty( $search_order_d ) ) {
		$order_by[] = "date $search_order_d";
	}
	if( ! empty( $search_order_v ) ) {
		$order_by[] = "views $search_order_v";
	}
	
	$order_by = implode( ", ", $order_by );
	if( ! $order_by ) $order_by = "date desc";
	
	$search_order_date = array ('----' => '', 'asc' => '', 'desc' => '' );
	if( isset( $_REQUEST['search_order_d'] ) ) {
		$search_order_date[$search_order_d] = 'selected';
	} else {
		$search_order_date['desc'] = 'selected';
	}
	
	$search_order_title = array ('----' => '', 'asc' => '', 'desc' => '' );
	if( ! empty( $search_order_t ) ) {
		$search_order_title[$search_order_t] = 'selected';
	} else {
		$search_order_title['----'] = 'selected';
	}
	
	$search_order_view = array ('----' => '', 'asc' => '', 'desc' => '' );
	if( ! empty( $search_order_v ) ) {
		$search_order_view[$search_order_v] = 'selected';
	} else {
		$search_order_view['----'] = 'selected';
	}
	
	$search_area = array();
	
	if( isset( $_REQUEST['search_area'] ) ) {
		$_REQUEST['search_area'] = intval($_REQUEST['search_area']);
		$search_area[$_REQUEST['search_area']] = 'selected';
	} else {
		$search_area[0] = 'selected';
	}
	
	$db->query( "SELECT id, name, descr, template, views, date, password FROM " . PREFIX . "_static" . $where . " ORDER BY " . $order_by . " LIMIT $start_from,$news_per_page" );

	// Prelist Entries

	if( $start_from == "0" ) {
		$start_from = "";
	}
	$i = $start_from;
	$entries_showed = 0;
	
	$entries = "";
	
	while ( $row = $db->get_array() ) {

		$i ++;
		
		if( !$langformatdate ) $langformatdate = "d.m.Y";
		
		$itemdate = @date( $langformatdate, $row['date'] );
		
		$title = htmlspecialchars( stripslashes( $row['name'] ), ENT_QUOTES, $config['charset'] );
		$descr = stripslashes($row['descr']);
		if( $config['allow_alt_url'] ) $vlink = $config['http_home_url'] . $row['name'] . ".html";
		else $vlink = $config['http_home_url'] . "index.php?do=static&page=" . $row['name'];

		if( $row['password'] ) $lock = "<i class=\"fa fa-lock position-left text-muted\"></i>"; else $lock = "";

		
		$row['views'] = number_format( $row['views'], 0, ',', ' ');
		
		$entries .= "<tr>
        <td class=\"hidden-xs\">$itemdate - {$lock}<a title=\"{$lang['static_view']}\" class=\"tip\" href=\"{$vlink}\" target=\"_blank\">$title</a></td>
        <td class=\"cursor-pointer\" onclick=\"document.location = '?mod=static&action=doedit&id={$row['id']}'; return false;\"><a title=\"{$lang['edit_static_act']}\" class=\"tip\" href=\"?mod=static&action=doedit&id={$row['id']}\">$descr</a></td>
        <td class=\"hidden-xs text-center text-nowrap cursor-pointer\" onclick=\"document.location = '?mod=static&action=doedit&id={$row['id']}'; return false;\">{$row['views']}</td>
        <td><input name=\"selected_news[]\" value=\"{$row['id']}\" type='checkbox' class=\"icheck\" /></td>
        </tr>";

		$entries_showed ++;
		
	}
	
	// End prelisting
	$result_count = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_static" . $where );
	
	$all_count_news = $result_count['count'];
	if ( $fromnewsdate ) $fromnewsdate = date("Y-m-d", $fromnewsdate );
	if ( $tonewsdate ) $tonewsdate = date("Y-m-d", $tonewsdate );

	
	///////////////////////////////////////////
	// Options Bar
	echo <<<HTML
<script language="javascript">
    function search_submit(prm){
      document.optionsbar.start_from.value=prm;
      document.optionsbar.submit();
      return false;
    }
    function gopage_submit(prm){
      document.optionsbar.start_from.value= (prm - 1) * {$news_per_page};
      document.optionsbar.submit();
      return false;
    }
    </script>
<div class="modal fade" id="advancedsearch" name="advancedsearch" role="dialog" aria-labelledby="advancedsearchLabel">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
	<form action="?mod=static&amp;action=list" method="GET" name="optionsbar" id="optionsbar" class="form-horizontal">
	<input type="hidden" name="mod" value="static">
	<input type="hidden" name="action" value="list">
	<input type="hidden" name="start_from" id="start_from" value="{$start_from}">
  <div class="modal-header ui-dialog-titlebar">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <span class="ui-dialog-title" id="newcatsLabel">{$lang['edit_stat']}&nbsp;{$entries_showed}&nbsp;&nbsp;&nbsp;{$lang['edit_stat_1']}&nbsp;{$all_count_news}</span>
  </div>

  <div class="modal-body">

	<div class="row">
	  <div class="col-sm-12">
		 
		<div class="form-group">
				<div class="col-sm-12">
					<label>{$lang['edit_search_static']}</label>
					<div class="input-group">
						<input name="search_field" value="{$search_field}" type="text" class="form-control">
						<span class="input-group-btn">
							<select name="search_area" class="uniform form-control"><option value="0" {$search_area[0]}>{$lang['filter_search_0']}</option><option value="1" {$search_area[1]}>{$lang['filter_search_7']}</option><option value="2" {$search_area[2]}>{$lang['filter_search_8']}</option><option value="3" {$search_area[3]}>{$lang['filter_search_9']}</option></select>
						</span>
					</div>
				</div>
		</div>
		
	  </div>
	</div>
	
	<div class="row">	  
	  <div class="col-sm-6">

	  	<div class="form-group">
		  <label class="control-label col-sm-12">{$lang['search_by_date']}</label>
		  <div class="col-sm-12">
			{$lang['edit_fdate']} <input data-rel="calendardate" type="text" name="fromnewsdate" id="fromnewsdate" class="form-control" style="width:160px;" value="{$fromnewsdate}" autocomplete="off">
			{$lang['edit_tdate']} <input data-rel="calendardate" type="text" name="tonewsdate" id="tonewsdate" class="form-control" style="width:160px;" value="{$tonewsdate}" autocomplete="off">
		  </div>
		 </div>

	  </div>
	  
	  <div class="col-sm-6">
		<div class="form-group">
		  <label class="control-label col-sm-12">{$lang['static_per_page']}</label>
		  <div class="col-sm-12">
			<input class="form-control text-center" name="news_per_page" value="{$news_per_page}" type="text">
		  </div>
		 </div>
	  </div>
	</div>
	
	<div class="pb-10">{$lang['static_order']}</div>

	<div class="form-group">
			<div class="col-sm-4">
				<label>{$lang['edit_et']}</label>
				<select class="uniform" data-width="100%" name="search_order_t" id="search_order_t">
					<option {$search_order_title['----']} value="">{$lang['user_order_no']}</option>
					<option {$search_order_title['asc']} value="asc">{$lang['user_order_plus']}</option>
					<option {$search_order_title['desc']} value="desc">{$lang['user_order_minus']}</option>
				</select>
			</div>
			<div class="col-sm-4">
				<label>{$lang['search_by_date']}</label>
				<select class="uniform" data-width="100%" name="search_order_d" id="search_order_d">
				   <option {$search_order_date['----']} value="">{$lang['user_order_no']}</option>
				   <option {$search_order_date['asc']} value="asc">{$lang['user_order_plus']}</option>
				   <option {$search_order_date['desc']} value="desc">{$lang['user_order_minus']}</option>
				</select>
			</div>
			
			<div class="col-sm-4">
				<label>{$lang['search_by_view']}</label>
				<select class="uniform" data-width="100%" name="search_order_v" id="search_order_v">
					<option {$search_order_view['----']} value="">{$lang['user_order_no']}</option>
					<option {$search_order_view['asc']} value="asc">{$lang['user_order_plus']}</option>
					<option {$search_order_view['desc']} value="desc">{$lang['user_order_minus']}</option>
				</select>
			</div>
	</div>


		<button onclick="search_submit(0); return(false);" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-search position-left"></i>{$lang['edit_act_1']}</button>
		<button onclick="document.location='?mod=static'; return(false);" class="btn bg-danger btn-sm btn-raised"><i class="fa fa-eraser position-left"></i>{$lang['drop_search']}</button>

	</div>
	</form>
   </div>
</div>
</div>
HTML;
	// End Options Bar
	

	echo <<<JSCRIPT
<script>
<!--
function ckeck_uncheck_all() {
    var frm = document.static;
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

-->
</script>
JSCRIPT;
	
	if( $entries_showed == 0 ) {
		
		echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['static_head']}
	<div class="heading-elements not-collapsible">
		<ul class="icons-list">
			<li><a data-toggle="modal" data-target="#advancedsearch" href="#"><i class="fa fa-search position-left"></i><span class="visible-lg-inline visible-md-inline visible-sm-inline">{$lang['static_advanced_search']}</span></a></li>
		</ul>
	</div>
  </div>
	<div class="panel-body">
		<div style="display: table;min-height:100px;width:100%;">
		  <div class="text-center" style="display: table-cell;vertical-align:middle;">{$lang['edit_nostatic']}</div>
		</div>
	</div>
	<div class="panel-footer">
	  <button class="btn bg-teal btn-sm btn-raised" type="button" onclick="document.location='?mod=static&action=addnew'"><i class="fa fa-plus-circle position-left"></i>{$lang['static_new']}</button>
	</div>
</div>
HTML;
	
	} else {

		// pagination
		$npp_nav = "";
			
		if( $all_count_news > $news_per_page ) {
			
			if( $start_from > 0 ) {
				$previous = $start_from - $news_per_page;
				$npp_nav .= "<li><a onclick=\"javascript:search_submit($previous); return(false);\" href=\"#\" title=\"{$lang['edit_prev']}\"><i class=\"fa fa-backward\"></i></a></li>";
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
				$npp_nav .= "<li><a onclick=\"javascript:search_submit($i); return(false);\" href=\"#\" title=\"{$lang['edit_next']}\"><i class=\"fa fa-forward\"></i></a></li>";
			}
			
			$npp_nav = "<ul class=\"pagination pagination-sm\">".$npp_nav."</ul>";
		
		}
		
		// pagination
	
		echo <<<HTML
<form action="" method="post" name="static">
<input type="hidden" name="mod" value="mass_static_actions">
<input type="hidden" name="user_hash" value="$dle_login_hash" />
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['static_head']}
	
	<div class="heading-elements not-collapsible">
		<ul class="icons-list">
			<li><a data-toggle="modal" data-target="#advancedsearch" href="#"><i class="fa fa-search position-left"></i><span class="visible-lg-inline visible-md-inline visible-sm-inline">{$lang['static_advanced_search']}</span></a></li>
		</ul>
	</div>

  </div>
  
    <table class="table table-striped table-xs table-hover">
      <thead>
      <tr>
        <th class="hidden-xs" style="width: 400px">{$lang['static_title']}</th>
        <th>{$lang['static_descr']}</th>
        <th class="hidden-xs" style="width: 60px;text-align:center;"><i class="fa fa-eye tip" data-original-title="{$lang['st_views']}"></i></th>
        <th style="width: 40px"><input class="icheck" type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all()"></th>
      </tr>
      </thead>
	  <tbody>
		{$entries}
	  </tbody>
	</table>
		  
  
<div class="panel-footer">
 <div class="pull-left"><button class="btn bg-teal btn-sm btn-raised" type="button" onclick="document.location='?mod=static&action=addnew'"><i class="fa fa-plus-circle position-left"></i>{$lang['static_new']}</button></div>
 <div class="pull-right"><select name="action" class="uniform">
  <option value="">{$lang['edit_selact']}</option>
  <option value="mass_date">{$lang['mass_edit_date']}</option>
  <option value="mass_clear_count">{$lang['mass_clear_count']}</option>
  <option value="mass_delete">{$lang['edit_seldel']}</option>
  </select>
  <input class="btn bg-brown-600 btn-sm btn-raised" type="submit" value="{$lang['b_start']}"></div>
</div>  
  
</div>
<div class="mb-20">{$npp_nav}</div>
</form>
HTML;
	
	}
	
	echofooter();

} elseif( $action == "addnew" ) {

	if( $config['allow_static_wysiwyg'] == 1 ) {
		$js_array[] = "engine/skins/codemirror/js/code.js";
		$js_array[] = "engine/editor/jscripts/froala/editor.js";
		$js_array[] = "engine/editor/jscripts/froala/languages/{$lang['wysiwyg_language']}.js";
		$css_array[] = "engine/editor/jscripts/froala/css/editor.css";
	}
	
	if( $config['allow_static_wysiwyg'] == 2 ) {
		$js_array[] = "engine/editor/jscripts/tiny_mce/tinymce.min.js";
	}
	
	if( !$config['allow_static_wysiwyg'] ) {
		$js_array[] = "engine/classes/js/typograf.min.js";
	}
	
	echoheader( "<i class=\"fa fa-file-text-o position-left\"></i><span class=\"text-semibold\">{$lang['opt_sm_static']}</span>", array($_SESSION['admin_referrer'] => $lang['opt_sm_static'], '' => $lang['static_a'] ) );
	
	echo "
    <script>
    function preview(){";

	if( $config['allow_static_wysiwyg'] == 2 ) {
		echo "tinyMCE.triggerSave();";
	}
	
	echo "if(document.static.template.value == '' || document.static.description.value == '' || document.static.name.value == ''){ Growl.error({ title: '{$lang['p_info']}', text: '{$lang['static_err_1']}'}); }
    else{
        dd=window.open('','prv','height=400,width=750,resizable=1,scrollbars=1')
        document.static.mod.value='preview';document.static.target='prv'
        document.static.submit(); dd.focus()
        setTimeout(\"document.static.mod.value='static';document.static.target='_self'\",500)
    }
    }
    onload=focus;function focus(){document.forms[0].name.focus();}

	function auto_keywords ( key )
	{

		var wysiwyg = '{$config['allow_static_wysiwyg']}';

		if (wysiwyg == \"2\") {
			tinyMCE.triggerSave();
		}

		var short_txt = document.getElementById('template').value;

		ShowLoading('');

		$.post(\"engine/ajax/controller.php?mod=keywords\", { short_txt: short_txt, key: key, user_hash: '{$dle_login_hash}' }, function(data){
	
			HideLoading('');
	
			if (key == 1) { $('#autodescr').val(data); }
			else { $('#keywords').tokenfield('setTokens', data); }
	
		});

		return false;
	}
	$(function(){
		  $('.cat_select').chosen({allow_single_deselect:true, no_results_text: '{$lang['addnews_cat_fault']}'});

	});
	
	function onPassChange(obj) {
  
	  var value = obj.checked;
	  
	  if (value == true) {
		$('#passlist').show();
	  } else {
		$('#passlist').hide();
	  }
	  
	}
	
    </script>";

	if( !$config['allow_static_wysiwyg'] ) $fix_br = "<div class=\"radio\"><label><input class=\"icheck\" type=\"radio\" name=\"allow_br\" id=\"allow_br\" value=\"1\" checked=\"checked\" />{$lang['static_br_html']}</label></div><div class=\"radio\"><label><input class=\"icheck\" type=\"radio\" name=\"allow_br\" id=\"allow_br1\" value=\"0\" />{$lang['static_br_html_1']}</label></div>";
	else $fix_br = "<div class=\"radio\"><label><input class=\"icheck\" type=\"radio\" name=\"allow_br\" name=\"allow_br\" value=\"0\" />{$lang['static_br_html_1']}</label></div>";

	if ($member_id['user_group'] == 1 ) $fix_br .= "<div class=\"radio\"><label><input class=\"icheck\" type=\"radio\" name=\"allow_br\" id=\"allow_br2\" value=\"2\" />{$lang['static_br_html_2']}</label></div>";

	$groups = get_groups();
	$skinlist = SelectSkin('');
	
	if( $config['allow_static_wysiwyg'] == "2" ) echo "<form class=\"form-horizontal\" method=post name=\"static\" id=\"addnews\" onsubmit=\"if(document.static.name.value == '' || document.static.description.value == ''){Growl.error({ title: '{$lang['p_info']}', text: '{$lang['static_err_1']}'}); return false}\" action=\"\" autocomplete=\"off\">";
	else echo "<form class=\"form-horizontal\" method=post name=\"static\" id=\"addnews\" onsubmit=\"if(document.static.name.value == '' || document.static.description.value == ''){Growl.error({ title: '{$lang['p_info']}', text: '{$lang['static_err_1']}'}); return false}\" action=\"\" autocomplete=\"off\">";	

	echo <<<HTML
<input type="hidden" name="action" value="dosavenew">
<input type="hidden" name="mod" value="static">
<input type="hidden" name="preview_mode" value="static" >
<input type="hidden" name="user_hash" value="$dle_login_hash" />
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['static_a']}
    <div class="heading-elements">
	    <ul class="icons-list">
		<li><a href="#" class="panel-fullscreen"><i class="fa fa-expand"></i></a></li>
		</ul>
    </div>
  </div>
  <div class="panel-body">
	
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['static_title']}</label>
		  <div class="col-md-10">
			<input type="text" name="name" class="form-control width-550" maxlength="100"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_stitle']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['static_descr']}</label>
		  <div class="col-md-10">
			<input type="text" name="description" class="form-control width-550" maxlength="250"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_sdesc']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['edit_edate']}</label>
		  <div class="col-md-10">
			<input data-rel="calendar" type="text" name="newdate" class="form-control position-left" style="width:190px;" value="" autocomplete="off"><label class="checkbox-inline"><input class="icheck" type="checkbox" name="allow_now" id="allow_now" value="yes" checked>{$lang['edit_jdate']}</label>
		  </div>
		 </div>
		<div class="form-group editor-group">
		  <label class="control-label col-md-2">{$lang['static_templ']}</label>
		  <div class="col-md-10">
HTML;
	
	if( $config['allow_static_wysiwyg'] ) {
		
		include (DLEPlugins::Check(ENGINE_DIR . '/editor/static.php'));
	
	} else {
		
		include (DLEPlugins::Check(ENGINE_DIR . '/inc/include/inserttag.php'));
		
		echo <<<HTML
		<div class="editor-panel"><div class="shadow-depth1">{$bb_code}<textarea class="editor" style="width:100%;height:350px;" name="template" id="template" onfocus="setFieldName(this.name)"></textarea></div></div><script>var selField  = "template";</script>
HTML;
	
	}
	
	
	echo <<<HTML
		  </div>
		 </div>
		 
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['static_type']}</label>
		  <div class="col-md-10">
			{$fix_br}
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2"></label>
		  <div class="col-md-10">
			{$lang['add_metatags']}<i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_metas']}" ></i>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['meta_title']}</label>
		  <div class="col-md-10">
			<input type="text" name="meta_title" class="form-control width-500" maxlength="140">
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['meta_descr']}</label>
		  <div class="col-md-10">
			<input type="text" name="descr" id="autodescr" class="form-control width-500" maxlength="300">
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['meta_keys']}</label>
		  <div class="col-md-10">
			<textarea class="tags" name="keywords" id='keywords'></textarea><br /><br />
			<button onclick="auto_keywords(1); return false;" class="btn bg-primary-600 btn-sm btn-raised position-left"><i class="fa fa-exchange position-left"></i>{$lang['btn_descr']}</button>
			<button onclick="auto_keywords(2); return false;" class="btn bg-primary-600 btn-sm btn-raised"><i class="fa fa-exchange position-left"></i>{$lang['btn_keyword']}</button>
		  </div>
		 </div>		 
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['static_tpl']}</label>
		  <div class="col-md-10">
			<input type="text" name="static_tpl" class="form-control position-left" style="width:220px;">.tpl<i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-html="true" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_stpl']}" ></i>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['static_skin']}</label>
		  <div class="col-md-10">
			{$skinlist}<i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-html="true" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_static_skin']}" ></i>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['stat_allow']}</label>
		  <div class="col-md-10">
			<select name="grouplevel[]" class="cat_select" data-placeholder="{$lang['group_select_1']}" style="width:250px;" multiple><option value="all" selected>{$lang['edit_all']}</option>{$groups}</select>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"></label>
		  <div class="col-md-10 col-sm-9">
			<div class="checkbox"><label><input class="icheck" type="checkbox" id="need_pass" name="need_pass" value="1" onchange="onPassChange(this)">{$lang['pass_list_1']}</label></div>
		  </div>
		 </div>
		<div class="form-group" id="passlist" style="display:none;">
		  <label class="control-label col-md-2 col-sm-3">{$lang['pass_list_2']}<div class="text-muted text-size-small">{$lang['pass_list_3']}</div></label>
		  <div class="col-md-10 col-sm-9">
			<textarea rows="5" class="classic width-500" name="password"></textarea>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2"></label>
		  <div class="col-md-10">
		    <div class="checkbox"><label><input class="icheck" type="checkbox" name="allow_template" id="allow_template" value="1" checked>{$lang['st_al_templ']}</label></div>
			<div class="checkbox"><label><input class="icheck" type="checkbox" name="allow_count" id="allow_count" value="1" checked>{$lang['allow_count']}</label></div>
			<div class="checkbox"><label><input class="icheck" type="checkbox" name="allow_sitemap" id="allow_sitemap" value="1" checked>{$lang['allow_sitemap']}</label></div>
			<div class="checkbox"><label><input class="icheck" type="checkbox" name="disable_index" id="disable_index" value="1">{$lang['add_disable_index']}</label></div>
			<div class="checkbox"><label><input class="icheck" type="checkbox" id="disable_search" name="disable_search" value="1">{$lang['cat_d_search']}</label></div>
		  </div>
		 </div>	
	
   </div>
<div class="panel-footer">
	<button class="btn bg-teal btn-sm btn-raised position-left" type="submit"><i class="fa fa fa-floppy-o position-left"></i>{$lang['news_add']}</button>
	<button onclick="preview(); return false;" class="btn bg-slate-600 btn-sm btn-raised position-left"><i class="fa fa-desktop position-left"></i>{$lang['btn_preview']}</button>
</div>
</div>
</form>
HTML;
	
	echofooter();
	
} elseif( $action == "dosavenew" ) {
	@header('X-XSS-Protection: 0;');
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( ! $_SESSION['admin_referrer'] ) {

		$_SESSION['admin_referrer'] = "?mod=static&amp;action=list";

	}
	
	$allow_br = intval( $_POST['allow_br'] );
	if ($member_id['user_group'] != 1 AND $allow_br > 1 ) $allow_br = 1;

	if ($allow_br == 2) {

		$template = trim( addslashes( $_POST['template'] ) );

	} else {

		if ( $config['allow_static_wysiwyg'] ) $parse->allow_code = false;

		$template = $parse->process( $_POST['template'] );
	
		if( $config['allow_static_wysiwyg'] or $allow_br != '1' ) {
			$template = $parse->BB_Parse( $template );
		} else {
			$template = $parse->BB_Parse( $template, false );
		}

	}

	$disable_index = isset( $_POST['disable_index'] ) ? intval( $_POST['disable_index'] ) : 0;
	$disable_search = isset( $_POST['disable_search'] ) ? intval( $_POST['disable_search'] ) : 0;
	
	$metatags = create_metatags( $template );
	$name = trim( totranslit( $_POST['name'], true, false ) );
	$descr = $db->safesql( htmlspecialchars( strip_tags(trim($_POST['description'])), ENT_QUOTES, $config['charset'] ) );
	$template = $db->safesql( $template );
	$password = $db->safesql(trim($_POST['password']));

	$tpl = $db->safesql(cleanpath( $_POST['static_tpl'] ));

	$skin_name =  trim( totranslit( $_POST['skin_name'], false, false ) );
	$newdate = $_POST['newdate'];
    if( isset( $_POST['allow_now'] ) ) $allow_now = $_POST['allow_now']; else $allow_now = "";
	
	if( ! count( $_POST['grouplevel'] ) ) $_POST['grouplevel'] = array ("all" );
	$grouplevel = $db->safesql( implode( ',', $_POST['grouplevel'] ) );
	
	$allow_template = intval( $_POST['allow_template'] );
	$allow_count = intval( $_POST['allow_count'] );
	$allow_sitemap = intval( $_POST['allow_sitemap'] );

	$added_time = time();
	$newsdate = strtotime( $newdate );

	if( ($allow_now == "yes") OR ($newsdate === - 1) OR !$newsdate) {
		$thistime = $added_time;
	} else {
		$thistime = $newsdate;
		if( ! intval( $config['no_date'] ) and $newsdate > $added_time ) $thistime = $added_time;
	}
					
	if( $name == "" or $descr == "" or $template == "" ) msg( "error", $lang['static_err'], $lang['static_err_1'], $_SESSION['admin_referrer'] );
	
	$static_count = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_static WHERE name='{$name}'" );

	if ($static_count['count']) msg( "error", $lang['static_err'], $lang['static_err_2'], $_SESSION['admin_referrer'] );
	
	$db->query( "INSERT INTO " . PREFIX . "_static (name, descr, template, allow_br, allow_template, grouplevel, tpl, metadescr, metakeys, template_folder, date, metatitle, allow_count, sitemap, disable_index, disable_search, password) values ('$name', '$descr', '$template', '$allow_br', '$allow_template', '$grouplevel', '$tpl', '{$metatags['description']}', '{$metatags['keywords']}', '{$skin_name}', '{$thistime}', '{$metatags['title']}', '$allow_count', '$allow_sitemap', '$disable_index', '$disable_search', '$password')" );
	$row = $db->insert_id();
	$db->query( "UPDATE " . PREFIX . "_static_files SET static_id='{$row}' WHERE author = '{$member_id['name']}' AND static_id = '0'" );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '59', '{$name}')" );
	
	msg( "success", $lang['static_addok'], $lang['static_addok_1'], array('?mod=static&action=addnew' => $lang['add_s_1'], '?mod=static&action=doedit&id='.$row => $lang['add_s_2'], $_SESSION['admin_referrer'] => $lang['add_s_3'] ) );

} elseif( $action == "doedit" ) {

	
	$id = intval( $_GET['id'] );
	
	if( $_GET['page'] == "rules" ) {
		
		$row = $db->super_query( "SELECT * FROM " . PREFIX . "_static where name='dle-rules-page'" );
		$lang['static_edit'] = $lang['rules_edit'];

		if( !$row['id'] ) {
			$id = "";
			$row['allow_template'] = "1";
		} else $id = $row['id'];
		
		if( ! $config['registration_rules'] ) $lang['rules_descr'] = $lang['rules_descr'] . " <span class=\"text-danger\">" . $lang['rules_check'] . "</span>";

		$_SESSION['admin_referrer'] = "?mod=static&amp;action=list";
	
	} else {
		
		$row = $db->super_query( "SELECT * FROM " . PREFIX . "_static where id='$id'" );

		if($row['name'] == "dle-rules-page") {
			header( "Location: ?mod=static&action=doedit&page=rules" ); 
			die();
		}
	}

	if ($row['allow_br'] == 2) {

		if ($member_id['user_group'] != 1) msg( "error", $lang['index_denied'], $lang['static_not_allowed'], $_SESSION['admin_referrer'] );

		$row['template'] = htmlspecialchars( stripslashes( $row['template'] ), ENT_QUOTES, $config['charset'] );


	} else {
	
		if( $row['allow_br'] != '1' or $config['allow_static_wysiwyg'] ) {
			
			$row['template'] = $parse->decodeBBCodes( $row['template'], true, $config['allow_static_wysiwyg'] );
		
		} else {
			
			$row['template'] = $parse->decodeBBCodes( $row['template'], false );
		
		}
	}
	
	$skinlist = SelectSkin( $row['template_folder'] );
	$row['descr'] = stripslashes($row['descr']);
	$row['metatitle'] = stripslashes( $row['metatitle'] );
	$itemdate = @date( "Y-m-d H:i:s", $row['date'] );

	if( $config['allow_static_wysiwyg'] == 1 ) {
		$js_array[] = "engine/skins/codemirror/js/code.js";
		$js_array[] = "engine/editor/jscripts/froala/editor.js";
		$js_array[] = "engine/editor/jscripts/froala/languages/{$lang['wysiwyg_language']}.js";
		$css_array[] = "engine/editor/jscripts/froala/css/editor.css";
	}
	
	if( $config['allow_static_wysiwyg'] == 2 ) {
		$js_array[] = "engine/editor/jscripts/tiny_mce/tinymce.min.js";
	}
	
	if( !$config['allow_static_wysiwyg'] ) {
		$js_array[] = "engine/classes/js/typograf.min.js";
	}
	
	echoheader( "<i class=\"fa fa-file-text-o position-left\"></i><span class=\"text-semibold\">{$lang['opt_sm_static']}</span>", array($_SESSION['admin_referrer'] => $lang['opt_sm_static'], '' => $lang['static_edit'] ) );
	
	echo <<<HTML
<script language="javascript">

function confirmdelete(id) {
	    DLEconfirm( '{$lang['static_confirm']}', '{$lang['p_confirm']}', function () {
			document.location="?mod=static&action=dodelete&user_hash={$dle_login_hash}&id="+id;
		} );
}

function onPassChange(obj) {
  
	var value = obj.checked;
  
  if (value == true) {
	$('#passlist').show();
  } else {
	$('#passlist').hide();
  }
  
}
	
$(function(){

	if( document.getElementById('need_pass') ) {
		onPassChange(document.getElementById('need_pass'));
	}
	
	$('.cat_select').chosen({allow_single_deselect:true, no_results_text: '{$lang['addnews_cat_fault']}'});
	
});
</script>
HTML;

	echo "
    <script>
    function preview(){";

	if( $config['allow_static_wysiwyg'] == 2 ) {
		echo "tinyMCE.triggerSave();";
	}
	
	echo "if(document.static.template.value == ''){ Growl.error({ title: '{$lang['p_info']}', text: '{$lang['static_err_1']}'}); }
    else{
        dd=window.open('','prv','height=400,width=750,resizable=1,scrollbars=1')
        document.static.mod.value='preview';document.static.target='prv'
        document.static.submit(); dd.focus()
        setTimeout(\"document.static.mod.value='static';document.static.target='_self'\",500)
    }
    }

	function auto_keywords ( key )
	{

		var wysiwyg = '{$config['allow_static_wysiwyg']}';

		if (wysiwyg == \"2\") {
			tinyMCE.triggerSave();
		}

		var short_txt = document.getElementById('template').value;

		ShowLoading('');

		$.post(\"engine/ajax/controller.php?mod=keywords\", { short_txt: short_txt, key: key, user_hash: '{$dle_login_hash}' }, function(data){
	
			HideLoading('');
	
			if (key == 1) { $('#autodescr').val(data); }
			else { $('#keywords').tokenfield('setTokens', data); }
	
		});

		return false;
	}
    </script>";
	$check = array();

	$check[$row['allow_br']] = "checked=\"checked\"";

	if( !$config['allow_static_wysiwyg'] ) $fix_br = "<div class=\"radio\"><label><input class=\"icheck\" type=\"radio\" name=\"allow_br\" id=\"allow_br\" value=\"1\" {$check[1]} />{$lang['static_br_html']}</label></div><div class=\"radio\"><label><input class=\"icheck\" type=\"radio\" name=\"allow_br\" id=\"allow_br1\" value=\"0\" {$check[0]} />{$lang['static_br_html_1']}</label></div>";
	else $fix_br = "<div class=\"radio\"><label><input class=\"icheck\" type=\"radio\" name=\"allow_br\" id=\"allow_br\" value=\"0\" {$check[0]} />{$lang['static_br_html_1']}</label></div>";

	if ($member_id['user_group'] == 1 ) $fix_br .= "<div class=\"radio\"><label><input class=\"icheck\" type=\"radio\" name=\"allow_br\" id=\"allow_br2\" value=\"2\" {$check[2]} />{$lang['static_br_html_2']}</label></div>";

	if( $row['allow_template'] ) $check_t = "checked";
	else $check_t = "";

	if( $row['allow_count'] ) $check_c = "checked";
	else $check_c = "";
	
	if( $row['disable_search'] ) $check_ds = "checked";
	else $check_ds = "";
	
	if( $row['password'] ) $check_pass = "checked";
	else $check_pass = "";
	
	$password  = htmlspecialchars( $row['password'], ENT_QUOTES, $config['charset'] );

	if( $_GET['page'] != "rules" ) {

		if( $row['sitemap'] ) $allow_sitemap = "<div class=\"checkbox\"><label><input class=\"icheck\" type=\"checkbox\" name=\"allow_sitemap\" id=\"allow_sitemap\" value=\"1\" checked>{$lang['allow_sitemap']}</label></div>";
		else $allow_sitemap = "<div class=\"checkbox\"><label><input class=\"icheck\" type=\"checkbox\" name=\"allow_sitemap\" id=\"allow_sitemap\" value=\"1\">{$lang['allow_sitemap']}</label></div>";

		if( $row['disable_index'] ) $disable_index = "<div class=\"checkbox\"><label><input class=\"icheck\" type=\"checkbox\" name=\"disable_index\" id=\"disable_index\" value=\"1\" checked>{$lang['add_disable_index']}</label></div>";
		else $disable_index = "<div class=\"checkbox\"><label><input class=\"icheck\" type=\"checkbox\" name=\"disable_index\" id=\"disable_index\" value=\"1\">{$lang['add_disable_index']}</label></div>";
	
	} else $allow_sitemap = "";


	$groups = get_groups( explode( ',', $row['grouplevel'] ) );
	if( $row['grouplevel'] == "all" ) $check_all = "selected";
	else $check_all = "";
	
	if( $_GET['page'] == "rules" ) {
		
		echo "<form class=\"form-horizontal\" method=post name=\"static\" id=\"addnews\" action=\"\" autocomplete=\"off\">";
	
	} else {
		
		if( $config['allow_static_wysiwyg'] == 2 ) echo "<form class=\"form-horizontal\" method=post name=\"static\" id=\"addnews\" onsubmit=\"if(document.static.name.value == '' || document.static.description.value == '' ){Growl.error({ title: '{$lang['p_info']}', text: '{$lang['static_err_1']}'}); return false}\" action=\"\" autocomplete=\"off\">";
		else echo "<form class=\"form-horizontal\" method=post name=\"static\" id=\"addnews\" onsubmit=\"if(document.static.name.value == '' || document.static.description.value == ''){Growl.error({ title: '{$lang['p_info']}', text: '{$lang['static_err_1']}'}); return false}\" action=\"\" autocomplete=\"off\">";
	
	}
	
	echo <<<HTML
<input type="hidden" name="action" value="dosaveedit">
<input type="hidden" name="mod" value="static">
<input type="hidden" name="preview_mode" value="static" >
<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
<input type="hidden" name="id" value="{$id}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['static_edit']}
    <div class="heading-elements">
	    <ul class="icons-list">
		<li><a href="#" class="panel-fullscreen"><i class="fa fa-expand"></i></a></li>
		</ul>
    </div>
  </div>
  <div class="panel-body">

HTML;
	
	if( $_GET['page'] == "rules" ) {
		
		echo <<<HTML
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['static_descr']}</label>
		  <div class="col-md-10">
			<input type="text" name="description" class="form-control width-550" maxlength="250" value="{$row['descr']}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_sdesc']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2"></label>
		  <div class="col-md-10">
			{$lang['rules_descr']}
		  </div>
		 </div>
HTML;
	
	} else {
		
		echo <<<HTML
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['static_title']}</label>
		  <div class="col-md-10">
			<input type="text" name="name" class="form-control width-550" maxlength="100" value="{$row['name']}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_stitle']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['static_descr']}</label>
		  <div class="col-md-10">
			<input type="text" name="description" class="form-control width-550" maxlength="250" value="{$row['descr']}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_sdesc']}" ></i>
		  </div>
		 </div>
HTML;
	
	}
	
	echo <<<HTML
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['edit_edate']}</label>
		  <div class="col-md-10">
			<input data-rel="calendar" type="text" name="newdate" value="{$itemdate}" class="form-control position-left" style="width:190px;" autocomplete="off"><label class="checkbox-inline"><input class="icheck" type="checkbox" name="allow_now" id="allow_now" value="yes">{$lang['edit_jdate']}</label>
		  </div>
		 </div>
		<div class="form-group editor-group">
		  <label class="control-label col-md-2">{$lang['static_templ']}</label>
		  <div class="col-md-10">
HTML;
	
	if( $config['allow_static_wysiwyg'] ) {
		
		include (DLEPlugins::Check(ENGINE_DIR . '/editor/static.php'));
	
	} else {
		
		include (DLEPlugins::Check(ENGINE_DIR . '/inc/include/inserttag.php'));
		
		echo <<<HTML
		<div class="editor-panel"><div class="shadow-depth1">{$bb_code}<textarea class="editor" style="width:100%;height:350px;" name="template" id="template" onfocus="setFieldName(this.name)">{$row['template']}</textarea></div></div><script>var selField  = "template";</script>
HTML;
	
	}
	
	
	echo <<<HTML
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['static_type']}</label>
		  <div class="col-md-10">
			{$fix_br}
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2"></label>
		  <div class="col-md-10">
			{$lang['add_metatags']}<i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_metas']}" ></i>
		  </div>
		 </div>			 
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['meta_title']}</label>
		  <div class="col-md-10">
			<input type="text" name="meta_title" class="form-control width-500" maxlength="140" value="{$row['metatitle']}">
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['meta_descr']}</label>
		  <div class="col-md-10">
			<input type="text" name="descr" id="autodescr" class="form-control width-500" maxlength="300" value="{$row['metadescr']}">
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['meta_keys']}</label>
		  <div class="col-md-10">
			<textarea class="tags" name="keywords" id='keywords'>{$row['metakeys']}</textarea><br /><br />
			<button onclick="auto_keywords(1); return false;" class="btn bg-primary-600 btn-sm btn-raised position-left"><i class="fa fa-exchange position-left"></i>{$lang['btn_descr']}</button>&nbsp;
			<button onclick="auto_keywords(2); return false;" class="btn bg-primary-600 btn-sm btn-raised"><i class="fa fa-exchange position-left"></i>{$lang['btn_keyword']}</button>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['static_tpl']}</label>
		  <div class="col-md-10">
			<input type="text" name="static_tpl" class="form-control position-left" style="width:220px;" value="{$row['tpl']}">.tpl<i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-html="true" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_stpl']}" ></i>
		  </div>
		 </div>
HTML;
	
	
	if( $_GET['page'] != "rules" ) echo <<<HTML
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['static_skin']}</label>
		  <div class="col-md-10">
			{$skinlist}<i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-html="true" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_static_skin']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2">{$lang['stat_allow']}</label>
		  <div class="col-md-10">
			<select name="grouplevel[]" class="cat_select" data-placeholder="{$lang['group_select_1']}" multiple><option value="all" {$check_all}>{$lang['edit_all']}</option>{$groups}</select>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"></label>
		  <div class="col-md-10 col-sm-9">
			<div class="checkbox"><label><input class="icheck" type="checkbox" id="need_pass" name="need_pass" value="1" onchange="onPassChange(this)" {$check_pass}>{$lang['pass_list_1']}</label></div>
		  </div>
		 </div>
		<div class="form-group" id="passlist" style="display:none;">
		  <label class="control-label col-md-2 col-sm-3">{$lang['pass_list_2']}<div class="text-muted text-size-small">{$lang['pass_list_3']}</div></label>
		  <div class="col-md-10 col-sm-9">
			<textarea rows="5" class="classic width-500" name="password">{$password}</textarea>
		  </div>
		 </div>
HTML;


	
	echo <<<HTML
		<div class="form-group">
		  <label class="control-label col-md-2"></label>
		  <div class="col-md-10">
		    <div class="checkbox"><label><input class="icheck" type="checkbox" name="allow_template" id="allow_template" value="1" {$check_t}>{$lang['st_al_templ']}</label></div>
			<div class="checkbox"><label><input class="icheck" type="checkbox" name="allow_count" id="allow_count" value="1" {$check_c}>{$lang['allow_count']}</label></div>
			{$allow_sitemap}
			{$disable_index}
			<div class="checkbox"><label><input class="icheck" type="checkbox" id="disable_search" name="disable_search" value="1" {$check_ds}>{$lang['cat_d_search']}</label></div>
		  </div>
		 </div>

   </div>
	<div class="panel-footer">
		<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
		<button type="button" onclick="preview(); return false;" class="btn bg-slate-600 btn-sm btn-raised position-left"><i class="fa fa-desktop position-left"></i>{$lang['btn_preview']}</button>
		<button type="button" onclick="confirmdelete('{$row['id']}'); return false;" class="btn bg-danger btn-sm btn-raised"><i class="fa fa-desktop position-left"></i>{$lang['edit_dnews']}</button>
	</div>
</div>
</form>
HTML;

	echofooter();
	
} elseif( $action == "dosaveedit" ) {
	
	@header('X-XSS-Protection: 0;');
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( ! $_SESSION['admin_referrer'] ) {

		$_SESSION['admin_referrer'] = "?mod=static&amp;action=list";

	}
	
	$allow_br = intval( $_POST['allow_br'] );
	if ($member_id['user_group'] != 1 AND $allow_br > 1 ) $allow_br = 1;

	if ($allow_br == 2) {

		$template = trim( addslashes( $_POST['template'] ) );
		
	} else {

		if ( $config['allow_static_wysiwyg'] ) $parse->allow_code = false;

		$template = $parse->process( $_POST['template'] );
	
		if( $config['allow_static_wysiwyg'] or $allow_br != '1' ) {
			$template = $parse->BB_Parse( $template );
		} else {
			$template = $parse->BB_Parse( $template, false );
		}

	}
	
	$metatags = create_metatags( $template );
	
	if( $_GET['page'] == "rules" ) {
		
		$name = "dle-rules-page";
	
	} else {
		
		$name = trim( totranslit( $_POST['name'], true, false ) );
		
		if( ! count( $_POST['grouplevel'] ) ) $_POST['grouplevel'] = array ("all" );
		$grouplevel = $db->safesql( implode( ',', $_POST['grouplevel'] ) );
	
	}

	$descr = trim( $db->safesql( htmlspecialchars( $_POST['description'], ENT_QUOTES, $config['charset'] ) ) );
	$disable_index = isset( $_POST['disable_index'] ) ? intval( $_POST['disable_index'] ) : 0;
	$disable_search = isset( $_POST['disable_search'] ) ? intval( $_POST['disable_search'] ) : 0;
	$need_pass = isset( $_POST['need_pass'] ) ? intval( $_POST['need_pass'] ) : 0;
	$template = $db->safesql( $template );
	$allow_template = intval( $_POST['allow_template'] );
	$allow_count = intval( $_POST['allow_count'] );
	$allow_sitemap = intval( $_POST['allow_sitemap'] );
	$tpl = $db->safesql(cleanpath( $_POST['static_tpl'] ));
	$skin_name =  trim( totranslit( $_POST['skin_name'], false, false ) );

	if($need_pass AND trim($_POST['password'])) {
		
		$password = $db->safesql(trim($_POST['password']));
		
	} else $password = "";

	$added_time = time();
	$newdate = trim($_POST['newdate']);
	if( isset( $_POST['allow_now'] ) )  $allow_now = $_POST['allow_now']; else $allow_now = "";
	
	if( $newdate ) {
		
        $newsdate = strtotime( $newdate );
		
		if( $allow_now == "yes" ) {
			
			$thistime = $added_time;
			
		} elseif( ($newsdate === - 1) OR !$newsdate ) {
			
				$thistime = $added_time;
				
		} else {

			$thistime = $newsdate;

			if( ! intval( $config['no_date'] ) and $newsdate > $added_time ) {
				$thistime = $added_time;
			}

		}

	} else {
		$thistime = $added_time;
	}
	
	if( $_GET['page'] == "rules" ) {
		
		if( $_POST['id'] ) {
			
			$db->query( "UPDATE " . PREFIX . "_static SET descr='$descr', template='$template', allow_br='$allow_br', allow_template='$allow_template', grouplevel='all', tpl='$tpl', metadescr='{$metatags['description']}', metakeys='{$metatags['keywords']}', template_folder='{$skin_name}', date='{$thistime}', metatitle='{$metatags['title']}', allow_count='{$allow_count}', sitemap='0', disable_index='0', disable_search='{$disable_search}', password='' WHERE name='dle-rules-page'" );

			$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '60', 'dle-rules-page')" );
		
		} else {
			
			$db->query( "INSERT INTO " . PREFIX . "_static (name, descr, template, allow_br, allow_template, grouplevel, tpl, metadescr, metakeys, template_folder, date, metatitle, allow_count, sitemap, disable_index, disable_search, password) values ('$name', '$descr', '$template', '$allow_br', '$allow_template', 'all', '$tpl', '{$metatags['description']}', '{$metatags['keywords']}', '{$skin_name}', '{$thistime}', '{$metatags['title']}', '{$allow_count}', '0', '0', '{$disable_search}', '')" );
			$row = $db->insert_id();
			$db->query( "UPDATE " . PREFIX . "_static_files SET static_id='{$row}' WHERE author = '{$member_id['name']}' AND static_id = '0'" );
		
		}
		
		msg( "success", $lang['rules_ok'], $lang['rules_ok'], "?mod=static&action=doedit&page=rules" );
	
	} else {
		
		$id = intval( $_GET['id'] );

		if( $name == "" or $descr == "" or $template == "" ) msg( "error", $lang['static_err'], $lang['static_err_1'], $_SESSION['admin_referrer'] );

		$static_count = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_static WHERE name='$name' AND id != '$id'" );
	
		if ($static_count['count']) msg( "error", $lang['static_err'], $lang['static_err_2'], $_SESSION['admin_referrer'] );

		$db->query( "UPDATE " . PREFIX . "_static SET name='$name', descr='$descr', template='$template', allow_br='$allow_br', allow_template='$allow_template', grouplevel='$grouplevel', tpl='$tpl', metadescr='{$metatags['description']}', metakeys='{$metatags['keywords']}', template_folder='{$skin_name}', date='{$thistime}', metatitle='{$metatags['title']}', allow_count='{$allow_count}', sitemap='{$allow_sitemap}', disable_index='$disable_index', disable_search='{$disable_search}', password='{$password}' WHERE id='$id'" );

		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '60', '{$name}')" );
		
		msg( "success", $lang['static_addok'], $lang['static_addok_1'], array( $_SESSION['admin_referrer'] => $lang['add_s_3'], '?mod=static&action=doedit&id='.$id => $lang['add_s_4'] ) );
	
	}
	

} elseif( $action == "dodelete" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( ! $_SESSION['admin_referrer'] ) {

		$_SESSION['admin_referrer'] = "?mod=static&amp;action=list";

	}
	
	$id = intval( $_GET['id'] );
	
	$db->query( "DELETE FROM " . PREFIX . "_static WHERE id='$id'" );
	
	$db->query( "SELECT name, onserver FROM " . PREFIX . "_static_files WHERE static_id = '$id'" );
	
	while ( $row = $db->get_row() ) {
		
		if( $row['onserver'] ) {
			
			$url = explode( "/", $row['onserver'] );

			if( count( $url ) == 2 ) {
					
				$folder_prefix = $url[0] . "/";
				$file = $url[1];
				
			} else {
					
				$folder_prefix = "";
				$file = $url[0];
				
			}
			$file = totranslit( $file, false );
	
			if( trim($file) == ".htaccess") die("Hacking attempt!");

			@unlink( ROOT_DIR . "/uploads/files/" . $folder_prefix . $file );
		
		} else {
			
			$url_image = explode( "/", $row['name'] );
			
			if( count( $url_image ) == 2 ) {
				
				$folder_prefix = $url_image[0] . "/";
				$dataimages = $url_image[1];
			
			} else {
				
				$folder_prefix = "";
				$dataimages = $url_image[0];
			
			}
			
			@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages );
			@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $dataimages );
			@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "medium/" . $dataimages );
		}
	
	}
	
	$db->query( "DELETE FROM " . PREFIX . "_static_files WHERE static_id = '$id'" );
	
	msg( "success", $lang['static_del'], $lang['static_del_1'], $_SESSION['admin_referrer'] );

}
?>