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
 File: categories.php
-----------------------------------------------------
 Use: category management
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['admin_categories'] ) {
	msg( "error", $lang['index_denied'], $lang['cat_perm'] );
}

$cat_info = array ();
	
$db->query( "SELECT * FROM " . PREFIX . "_category ORDER BY posi ASC" );
	
while ( $row = $db->get_row() ) {
		
	$cat_info[$row['id']] = array ();
		
	foreach ( $row as $key => $value ) {
		$cat_info[$row['id']][$key] = stripslashes( $value );
	}
	
}

$result = "";
$catid = intval( $_REQUEST['catid'] );

function get_sub_cats($id, $subcategory = false) {
	global $cat_info;
	$subfound = array ();
	
	if( ! $subcategory ) {
		$subcategory = array ();
		$subcategory[] = $id;
	}
	
	foreach ( $cat_info as $cats ) {
		if( $cats['parentid'] == $id ) {
			$subfound[] = $cats['id'];
		}
	}
	
	foreach ( $subfound as $parentid ) {
		$subcategory[] = $parentid;
		$subcategory = get_sub_cats( $parentid, $subcategory );
	}
	
	return $subcategory;

}

function makeDropDown($options, $name, $selected) {
	$output = "<select class=\"uniform\" name=\"{$name}\" style=\"min-width:100px;\">\r\n";
	foreach ( $options as $value => $description ) {
		$output .= "<option value=\"{$value}\"";
		if( $selected == $value ) {
			$output .= " selected ";
		}
		$output .= ">{$description}</option>\n";
	}
	$output .= "</select>";
	return $output;
}

function SelectSkin($skin) {
	global $lang;
	
	$templates_list = array ();
	
	$handle = opendir( ROOT_DIR . '/templates' );
	
	while ( false !== ($file = readdir( $handle )) ) {
		if( is_dir( ROOT_DIR . "/templates/$file" ) and ($file != "." and $file != ".." and $file != "smartphone") ) {
			$templates_list[] = $file;
		}
	}
	closedir( $handle );
	
	$skin_list = "<select class=\"uniform\" name=\"skin_name\" data-width=\"100%\">";
	$skin_list .= "<option value=\"\">" . $lang['cat_skin_sel'] . "</option>";
	
	foreach ( $templates_list as $single_template ) {
		if( $single_template == $skin ) $selected = " selected";
		else $selected = "";
		$skin_list .= "<option value=\"{$single_template}\"" . $selected . ">{$single_template}</option>";
	}
	$skin_list .= '</select>';
	
	return $skin_list;
}

function clear_url_dir( $var ) {
	if ( is_array($var) ) return "";

	$var = str_ireplace( ".php", "", $var );
	$var = str_ireplace( ".php", ".ppp", $var );
	$var = trim( strip_tags( $var ) );
	$var = str_replace( "\\", "/", $var );
	$var = preg_replace( "/[^a-z0-9\/\_\-]+/mi", "", $var );
	return $var;

}


if( $action == "add" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));
	
	$parse = new ParseFilter();
	
	$quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", '"' );

	if( $_POST['cat_icon'] == $lang['cat_icon'] ) {
		$_POST['cat_icon'] = "";
	}
	
	$cat_name  = $db->safesql(  htmlspecialchars( strip_tags( stripslashes($_POST['cat_name'] ) ), ENT_QUOTES, $config['charset']) );
	$skin_name = trim( totranslit($_POST['skin_name'], false, false) );
	$cat_icon  = $db->safesql(  htmlspecialchars( strip_tags( stripslashes($_POST['cat_icon']) ), ENT_QUOTES, $config['charset']) );
	$show_sub = intval($_POST['show_sub']);
	$allow_rss = intval($_POST['allow_rss']);
	$disable_search = intval($_POST['disable_search']);
	$disable_main = intval($_POST['disable_main']);
	$disable_rating = intval($_POST['disable_rating']);
	$disable_comments = intval($_POST['disable_comments']);
	$enable_dzen = intval($_POST['enable_dzen']);
	$enable_turbo = intval($_POST['enable_turbo']);

	$fulldescr = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['fulldescr'] ), false ) );

	if( !$cat_name ) {
		msg( "error", $lang['cat_error'], $lang['cat_ername'], "javascript:history.go(-1)" );
	}
	
	if (trim($_POST['alt_cat_name'])) {

		$alt_cat_name = totranslit( stripslashes( $_POST['alt_cat_name'] ), true, false );

	} else {

		$alt_cat_name = totranslit( stripslashes( $cat_name ), true, false );

	}

	if( !$alt_cat_name ) {
		msg( "error", $lang['cat_error'], $lang['cat_erurl'], "javascript:history.go(-1)" );
	}
	
	if ( in_array($_POST['news_sort'], array("date", "rating", "news_read", "title", "comm_num")) )	{

		$news_sort = $db->safesql( $_POST['news_sort'] );

	} else $news_sort = "";

	if ( in_array($_POST['news_msort'], array("ASC", "DESC")) )	{

		$news_msort = $db->safesql( $_POST['news_msort'] );

	} else $news_msort = "";

	if ( $_POST['news_number'] > 0) $news_number = intval( $_POST['news_number'] );
	else $news_number = 0;

	if ( $_POST['category'] > 0) $category = intval( $_POST['category'] );
	else $category = 0;

	$reserved_name = array('tags','xfsearch','user','lastnews','catalog','newposts','favorites');

	if (in_array($alt_cat_name, $reserved_name) AND !$category)	{
	
		msg( "error", $lang['cat_error'], $lang['cat_resname'], "javascript:history.go(-1)" );	
	}

	if ( $_POST['short_tpl'] ) {

		$url = @parse_url ( $_POST['short_tpl'] );
		$file_path = dirname (clear_url_dir($url['path']));
		$tpl_name = pathinfo($url['path']);
		$tpl_name = totranslit($tpl_name['basename']);

		if ($file_path AND $file_path != ".") $tpl_name = $file_path."/".$tpl_name;

		$short_tpl = $tpl_name;

	} else $short_tpl = "";
	
	if ( $_POST['full_tpl'] ) {

		$url = @parse_url ( $_POST['full_tpl'] );
		$file_path = dirname (clear_url_dir($url['path']));
		$tpl_name = pathinfo($url['path']);
		$tpl_name = totranslit($tpl_name['basename']);

		if ($file_path AND $file_path != ".") $tpl_name = $file_path."/".$tpl_name;

		$full_tpl = $tpl_name;

	} else $full_tpl = "";
	
	$meta_title = $db->safesql( htmlspecialchars ( strip_tags( stripslashes( $_POST['meta_title'] ) ), ENT_QUOTES, $config['charset'] ) );
	$description = $db->safesql( dle_substr( strip_tags( stripslashes( $_POST['descr'] ) ), 0, 300, $config['charset'] ) );
	$keywords = $db->safesql( str_replace( $quotes, " ", strip_tags( stripslashes( $_POST['keywords'] ) ) ) );
	
	$row = $db->super_query( "SELECT alt_name FROM " . PREFIX . "_category WHERE alt_name ='{$alt_cat_name}'" );
	
	if( $row['alt_name'] ) {
		msg( "error", $lang['cat_error'], $lang['cat_eradd'], "?mod=categories" );
	}
	
	$db->query( "INSERT INTO " . PREFIX . "_category (parentid, name, alt_name, icon, skin, descr, keywords, news_sort, news_msort, news_number, short_tpl, full_tpl, metatitle, show_sub, allow_rss, fulldescr, disable_search, disable_main, disable_rating, disable_comments, enable_dzen, enable_turbo) values ('$category', '$cat_name', '$alt_cat_name', '$cat_icon', '$skin_name', '$description', '$keywords', '$news_sort', '$news_msort', '$news_number', '$short_tpl', '$full_tpl', '$meta_title', '$show_sub', '$allow_rss', '$fulldescr', '$disable_search', '$disable_main', '$disable_rating', '$disable_comments', '$enable_dzen', '$enable_turbo')" );

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '12', '{$cat_name}')" );

	
	@unlink( ENGINE_DIR . '/cache/system/category.php' );
	clear_cache();
	
	msg( "success", $lang['cat_addok'], $lang['cat_addok_1'], "?mod=categories" );

} elseif( $action == "remove" ) {
	

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( ! $catid ) {
		msg( "error", $lang['cat_error'], $lang['cat_noid'], "?mod=categories" );
	}
	
	function DeleteSubcategories($parentid) {
		global $db;
		
		$parentid = (int)$parentid;
		
		if(!$parentid) return;
		
		$subcategories = $db->query( "SELECT id FROM " . PREFIX . "_category WHERE parentid = '{$parentid}'" );
		
		while ( $subcategory = $db->get_row( $subcategories ) ) {
			
			DeleteSubcategories( $subcategory['id'] );
			
			$row = $db->super_query( "SELECT count(*) as count FROM " . PREFIX . "_post_extras_cats WHERE cat_id='{$subcategory['id']}'" );
			
			if( $row['count'] ) {
				if( $_POST['subaction'] == 2 AND is_array( $_REQUEST['new_category'] ) ) {
					if( !in_array( $subcategory['id'], $_REQUEST['new_category'] ) ) {
						
						$category_list = array();
					
						foreach ( $_REQUEST['new_category'] as $value ) {
							$category_list[] = intval($value);
						}
						
						$result = $db->query( "SELECT news_id FROM " . PREFIX . "_post_extras_cats WHERE cat_id='{$subcategory['id']}'" );
		
						while ( $news_id_row = $db->get_array($result) ) {
							
							$row = $db->super_query( "SELECT id, category, approve FROM " . PREFIX . "_post WHERE id='{$news_id_row['news_id']}'" );
							
							$temp_cat = array_merge(explode(',', $row['category']), $category_list);
							$temp_cat = array_unique($temp_cat);
							$r_key = array_keys($temp_cat, $subcategory['id']);
							unset($temp_cat[$r_key[0]]);
		
							$temp_cat = $db->safesql(implode(',', $temp_cat));
							
							$db->query( "UPDATE " . PREFIX . "_post SET category='{$temp_cat}' WHERE id='{$row['id']}'" );
							
							$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$row['id']}'" );

							if( $temp_cat AND $row['approve'] ) {
		
								$cat_ids = array ();
		
								$cat_ids_arr = explode( ",", $temp_cat );
		
								foreach ( $cat_ids_arr as $value ) {
		
									$cat_ids[] = "('" . $row['id'] . "', '" . trim( $value ) . "')";
								}
		
								$cat_ids = implode( ", ", $cat_ids );
								$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
		
							}
					
						}
					}
				}
				
				if( $_POST['subaction'] == 1) {
					
						$result = $db->query( "SELECT news_id FROM " . PREFIX . "_post_extras_cats WHERE cat_id='{$subcategory['id']}'" );
		
						while ( $news_id_row = $db->get_array($result) ) {
							
							$row = $db->super_query( "SELECT id, category, approve FROM " . PREFIX . "_post WHERE id='{$news_id_row['news_id']}'" );
							
							$temp_cat = explode(',', $row['category']);
							$temp_cat = array_unique($temp_cat);
							$r_key = array_keys($temp_cat, $subcategory['id']);
							unset($temp_cat[$r_key[0]]);
							
							if(!count($temp_cat)) $temp_cat[] = 0;
		
							$temp_cat = $db->safesql(implode(',', $temp_cat));
							
							$db->query( "UPDATE " . PREFIX . "_post SET category='{$temp_cat}' WHERE id='{$row['id']}'" );
							
							$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$row['id']}'" );

							if( $temp_cat AND $row['approve'] ) {
		
								$cat_ids = array ();
		
								$cat_ids_arr = explode( ",", $temp_cat );
		
								foreach ( $cat_ids_arr as $value ) {
		
									$cat_ids[] = "('" . $row['id'] . "', '" . trim( $value ) . "')";
								}
		
								$cat_ids = implode( ", ", $cat_ids );
								$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
		
							}
							
						}
				}
				
				if( $_POST['subaction'] == 3) {
					
					$result = $db->query( "SELECT news_id FROM " . PREFIX . "_post_extras_cats WHERE cat_id='{$subcategory['id']}'" );
		
					while ( $row = $db->get_array($result) ) {
						
						deletenewsbyid( $row['news_id'] );
						
					}

				}
				
			}
			
			$db->query( "DELETE FROM " . PREFIX . "_category WHERE id = '" . $subcategory['id'] . "'" );
		}
		
	}

	$row = $db->super_query( "SELECT count(*) as count FROM " . PREFIX . "_post_extras_cats WHERE cat_id IN (".implode(",",get_sub_cats($catid)).")" );
	
	if( $row['count'] ) {
		
		$delete_allowed = false;
		
		if( $_POST['subaction'] == 2 AND is_array( $_REQUEST['new_category'] ) ) {
			if( !in_array( $catid, $_REQUEST['new_category'] ) ) {
				
				$category_list = array();
			
				foreach ( $_REQUEST['new_category'] as $value ) {
					$category_list[] = intval($value);
				}
				
				$result = $db->query( "SELECT news_id FROM " . PREFIX . "_post_extras_cats WHERE cat_id='{$catid}'" );

				while ( $news_id_row = $db->get_array($result) ) {
					
					$row = $db->super_query( "SELECT id, category, approve FROM " . PREFIX . "_post WHERE id='{$news_id_row['news_id']}'" );
					
					$temp_cat = array_merge(explode(',', $row['category']), $category_list);
					$temp_cat = array_unique($temp_cat);
					$r_key = array_keys($temp_cat, $catid);
					unset($temp_cat[$r_key[0]]);

					$temp_cat = $db->safesql(implode(',', $temp_cat));
					
					$db->query( "UPDATE " . PREFIX . "_post SET category='{$temp_cat}' WHERE id='{$row['id']}'" );
					
					$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$row['id']}'" );

					if( $temp_cat AND $row['approve'] ) {
		
						$cat_ids = array ();
		
						$cat_ids_arr = explode( ",", $temp_cat );
		
						foreach ( $cat_ids_arr as $value ) {
		
							$cat_ids[] = "('" . $row['id'] . "', '" . trim( $value ) . "')";
						}
		
						$cat_ids = implode( ", ", $cat_ids );
						$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
		
					}
				
				}
				
				$delete_allowed = true;
			}
		}
		
		if( $_POST['subaction'] == 1) {
			
				$result = $db->query( "SELECT news_id FROM " . PREFIX . "_post_extras_cats WHERE cat_id='{$catid}'" );

				while ( $news_id_row = $db->get_array($result) ) {
					
					$row = $db->super_query( "SELECT id, category, approve FROM " . PREFIX . "_post WHERE id='{$news_id_row['news_id']}'" );
					
					$temp_cat = explode(',', $row['category']);
					$temp_cat = array_unique($temp_cat);
					$r_key = array_keys($temp_cat, $catid);
					unset($temp_cat[$r_key[0]]);
					
					if(!count($temp_cat)) $temp_cat[] = 0;

					$temp_cat = $db->safesql(implode(',', $temp_cat));
					
					$db->query( "UPDATE " . PREFIX . "_post SET category='{$temp_cat}' WHERE id='{$row['id']}'" );
					
					$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$row['id']}'" );

					if( $temp_cat AND $row['approve'] ) {
		
						$cat_ids = array ();
		
						$cat_ids_arr = explode( ",", $temp_cat );
		
						foreach ( $cat_ids_arr as $value ) {
		
							$cat_ids[] = "('" . $row['id'] . "', '" . trim( $value ) . "')";
						}
		
						$cat_ids = implode( ", ", $cat_ids );
						$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
		
					}
					
				}
				
				$delete_allowed = true;
		}

		if( $_POST['subaction'] == 3) {
			
				$result = $db->query( "SELECT news_id FROM " . PREFIX . "_post_extras_cats WHERE cat_id='{$catid}'" );

				while ( $row = $db->get_array($result) ) {
					
					deletenewsbyid( $row['news_id'] );
					
				}
				
				$delete_allowed = true;
		}
		
		if( $delete_allowed ) {
			
			$db->query( "DELETE FROM " . PREFIX . "_category WHERE id='{$catid}'" );
				
			DeleteSubcategories( $catid );
				
			@unlink( ENGINE_DIR . '/cache/system/category.php' );
				
			clear_cache();

			$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '13', '{$catid}')" );
	
			msg( "success", $lang['cat_delok'], $lang['cat_delok_1'], "?mod=categories" );
		}
		
		if( $_POST['subaction'] == 2 ) {
			
			msg( "warning", $lang['all_info'], "<form action=\"\" method=\"post\">{$lang['comm_move']} <select name=\"new_category[]\" class=\"categoryselect\" data-placeholder=\"{$lang['addnews_cat_sel']}\" style=\"width:350px;\" multiple>" . CategoryNewsSelection( 0, 0 ) . "</select> <input class=\"btn bg-primary btn-sm btn-raised\" type=\"submit\" value=\"{$lang['b_start']}\"><script>
$(function(){
	$('.categoryselect').chosen({allow_single_deselect:true, no_results_text: '{$lang['addnews_cat_fault']}'});
});
</script><input type=\"hidden\" name=\"subaction\" value=\"2\"></form>", "?mod=categories" );
		
		}
		
		msg( "warning", $lang['all_info'], "<form action=\"\" method=\"post\">{$lang['cat_rem_1']} <select class=\"uniform\" name=\"subaction\"><option value=\"1\">{$lang['cat_rem_2']}</option><option value=\"2\">{$lang['cat_rem_3']}</option><option value=\"3\">{$lang['cat_rem_4']}</option></select> <input class=\"btn bg-primary btn-sm btn-raised\" type=\"submit\" value=\"{$lang['b_start']}\"></form>", "?mod=categories" );
	
	} else {
		
		$db->query( "DELETE FROM " . PREFIX . "_category WHERE id='{$catid}'" );
		
		DeleteSubcategories( $catid );
		
		@unlink( ENGINE_DIR . '/cache/system/category.php' );

		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '13', '{$catid}')" );
		
		clear_cache();
		
		msg( "success", $lang['cat_delok'], $lang['cat_delok_1'], "?mod=categories" );
	}

} elseif( $action == "edit" ) {
	
	$catid = intval( $_GET['catid'] );
	
	if( ! $catid ) {
		msg( "error", $lang['cat_error'], $lang['cat_noid'], "?mod=categories" );
	}
	
	$row = $db->super_query( "SELECT * FROM " . PREFIX . "_category WHERE id = '{$catid}'" );
	
	if( ! $row['id'] ) msg( "error", $lang['cat_error'], $lang['cat_noid'], "?mod=categories" );

	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));
	
	$parse = new ParseFilter();

	echoheader( "<i class=\"fa fa-folder-open-o position-left\"></i><span class=\"text-semibold\">{$lang['cat_head']}</span>", array("?mod=categories" => $lang['cat_head'], '' => $lang['cat_edit'] ) );
	
	$categorylist = CategoryNewsSelection( $row['parentid'], 0 );
	$skinlist = SelectSkin( $row['skin'] );
	
	$row['name'] = stripslashes( preg_replace( array ("'\"'", "'\''" ), array ("&quot;", "&#039;" ), $row['name'] ) );
	$row['metatitle'] = stripslashes( preg_replace( array ("'\"'", "'\''" ), array ("&quot;", "&#039;" ), $row['metatitle'] ) );
	$row['descr'] = stripslashes( preg_replace( array ("'\"'", "'\''" ), array ("&quot;", "&#039;" ), $row['descr'] ) );
	$row['keywords'] = stripslashes( preg_replace( array ("'\"'", "'\''" ), array ("&quot;", "&#039;" ), $row['keywords'] ) );
	
	$row['news_sort'] = makeDropDown( array ("" => $lang['sys_global'], "date" => $lang['opt_sys_sdate'], "rating" => $lang['opt_sys_srate'], "news_read" => $lang['opt_sys_sview'], "title" => $lang['opt_sys_salph'], "comm_num" => $lang['opt_sys_scnum'] ), "news_sort", $row['news_sort'] );
	$row['news_msort'] = makeDropDown( array ("" => $lang['sys_global'], "DESC" => $lang['opt_sys_mminus'], "ASC" => $lang['opt_sys_mplus'] ), "news_msort", $row['news_msort'] );
	$row['show_sub'] = makeDropDown( array ("0" => $lang['sys_global'], "1" => $lang['opt_sys_yes'], "2" => $lang['opt_sys_no'] ), "show_sub", $row['show_sub'] );
	$row['allow_rss'] = makeDropDown( array ("1" => $lang['opt_sys_yes'], "0" => $lang['opt_sys_no'] ), "allow_rss", $row['allow_rss'] );

	if( $row['disable_search'] ) $ifch = "checked";	else $ifch = "";
	if( $row['disable_main'] ) $ifch2 = "checked"; else $ifch2 = "";
	if( $row['disable_comments'] ) $ifch3 = "checked"; else $ifch3 = "";
	if( $row['disable_rating'] ) $ifch4 = "checked"; else $ifch4 = "";
	if( $row['enable_dzen'] ) $ifch5 = "checked"; else $ifch5 = "";
	if( $row['enable_turbo'] ) $ifch6 = "checked"; else $ifch6 = "";

	$row['fulldescr'] = $parse->decodeBBCodes( $row['fulldescr'], false );
	
	echo <<<HTML
<form method="post" action="" class="form-horizontal" autocomplete="off">
  <input type="hidden" name="action" value="doedit">
  <input type="hidden" name="user_hash" value="{$dle_login_hash}" />
  <input type="hidden" name="catid" value="{$row['id']}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['cat_edit']}
  </div>
  <div class="panel-body">

		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['cat_name']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" value="{$row['name']}" maxlength="50" type="text" name="cat_name"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_catname']}"></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['cat_url']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" value="{$row['alt_name']}" maxlength="50" type="text" name="alt_cat_name"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_cataltname']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['cat_fulldescr']}</label>
		  <div class="col-md-10 col-sm-9">
			<textarea name="fulldescr" class="classic" style="width:100%;max-width:550px;" rows="5">{$row['fulldescr']}</textarea>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['cat_addicon']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-550" value="{$row['icon']}" maxlength="200" type="text" name="cat_icon"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_caticon']}"></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['meta_title']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" name="meta_title" class="form-control width-550" maxlength="200" value="{$row['metatitle']}">
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['meta_descr_cat']}</label>
		  <div class="col-md-10 col-sm-9">
			<input type="text" name="descr" class="form-control width-550" maxlength="300" value="{$row['descr']}">
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['meta_keys']}</label>
		  <div class="col-md-10 col-sm-9">
			<textarea name="keywords" class="classic" style="width:100%;max-width:550px;" rows="3">{$row['keywords']}</textarea>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['cat_parent']}</label>
		  <div class="col-md-10 col-sm-9">
			<select class="uniform" name="parentid">{$categorylist}</select>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['cat_skin']}</label>
		  <div class="col-md-10 col-sm-9">
			<div style="width:220px;display:inline-block;">{$skinlist}</div><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_cattempl']}"></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['opt_sys_sort']}</label>
		  <div class="col-md-10 col-sm-9">
			{$row['news_sort']}
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['opt_sys_msort']}</label>
		  <div class="col-md-10 col-sm-9">
			{$row['news_msort']}
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['opt_sys_sub']}</label>
		  <div class="col-md-10 col-sm-9">
			{$row['show_sub']}
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['cat_allow_rss']}</label>
		  <div class="col-md-10 col-sm-9">
			{$row['allow_rss']}
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['opt_sys_newc']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" type="text" name="news_number" value="{$row['news_number']}"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_news_number']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['cat_s_tpl']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" type="text" name="short_tpl" value="{$row['short_tpl']}">.tpl<i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['cat_s_tpl_hit']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['cat_f_tpl']}</label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" type="text" name="full_tpl" value="{$row['full_tpl']}">.tpl<i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['cat_f_tpl_hit']}" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"></label>
		  <div class="col-md-10 col-sm-9">
			<div class="row">
				<div class="col-sm-6" style="max-width:300px;"><div class="checkbox"><label><input class="icheck" type="checkbox" name="enable_dzen" value="1" {$ifch5}>{$lang['cat_dzen']}</label></div></div>
				<div class="col-sm-6"><div class="checkbox"><label><input class="icheck" type="checkbox" name="enable_turbo" value="1" {$ifch6}>{$lang['cat_turbo']}</label></div></div>
			</div>
			<div class="row">
				<div class="col-sm-6" style="max-width:300px;"><div class="checkbox"><label><input class="icheck" type="checkbox" name="disable_main" value="1" {$ifch2}>{$lang['cat_d_main']}</label></div></div>
				<div class="col-sm-6"><div class="checkbox"><label><input class="icheck" type="checkbox" name="disable_comments" value="1" {$ifch3}>{$lang['cat_d_comments']}</label></div></div>
			</div>
			<div class="row">
				<div class="col-sm-6" style="max-width:300px;"><div class="checkbox"><label><input class="icheck" type="checkbox" name="disable_rating" value="1" {$ifch4}>{$lang['cat_d_rating']}</label></div></div>
				<div class="col-sm-6"><div class="checkbox"><label><input class="icheck" type="checkbox" name="disable_search" value="1" {$ifch}>{$lang['cat_d_search']}</label></div></div>
			</div>
		  </div>
		 </div>

   </div>
	<div class="panel-footer">
		<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
	</div>
</div>
</form>
HTML;
	
	echofooter();
	die();

} elseif( $action == "doedit" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", '"' );
	
	include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));
	$parse = new ParseFilter();
	
	$fulldescr = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['fulldescr'] ), false ) );
	$disable_search = intval($_POST['disable_search']);
	$disable_main = intval($_POST['disable_main']);
	$disable_rating = intval($_POST['disable_rating']);
	$disable_comments = intval($_POST['disable_comments']);
	$enable_dzen = intval($_POST['enable_dzen']);
	$enable_turbo = intval($_POST['enable_turbo']);
	
	$cat_name  = $db->safesql(  htmlspecialchars( strip_tags( stripslashes($_POST['cat_name'] ) ), ENT_QUOTES, $config['charset']) );
	$skin_name = trim( totranslit($_POST['skin_name'], false, false) );
	$cat_icon  = $db->safesql(  htmlspecialchars( strip_tags( stripslashes($_POST['cat_icon']) ), ENT_QUOTES, $config['charset']) );

	if (trim($_POST['alt_cat_name'])) {

		$alt_cat_name = totranslit( stripslashes( $_POST['alt_cat_name'] ), true, false );

	} else {

		$alt_cat_name = totranslit( stripslashes( $cat_name ), true, false );

	}

	$show_sub = intval($_POST['show_sub']);
	$allow_rss = intval($_POST['allow_rss']);
		
	$catid = intval( $_POST['catid'] );
	$parentid = intval( $_POST['parentid'] );

	$meta_title = $db->safesql( htmlspecialchars ( strip_tags( stripslashes( $_POST['meta_title'] ) ), ENT_QUOTES, $config['charset'] ) );
	$description = $db->safesql( dle_substr( strip_tags( stripslashes( $_POST['descr'] ) ), 0, 300, $config['charset'] ) );
	$keywords = $db->safesql( str_replace( $quotes, " ", strip_tags( stripslashes( $_POST['keywords'] ) ) ) );

	$reserved_name = array('tags','xfsearch','user','lastnews','catalog','newposts','favorites');

	if (in_array($alt_cat_name, $reserved_name) AND !$parentid)	{
	
		msg( "error", $lang['cat_error'], $lang['cat_resname'], "javascript:history.go(-1)" );	
	}

	if ( $_POST['short_tpl'] ) {

		$url = @parse_url ( $_POST['short_tpl'] );
		$file_path = dirname (clear_url_dir($url['path']));
		$tpl_name = pathinfo($url['path']);
		$tpl_name = totranslit($tpl_name['basename']);

		if ($file_path AND $file_path != ".") $tpl_name = $file_path."/".$tpl_name;

		$short_tpl = $tpl_name;

	} else $short_tpl = "";
	
	if ( $_POST['full_tpl'] ) {

		$url = @parse_url ( $_POST['full_tpl'] );
		$file_path = dirname (clear_url_dir($url['path']));
		$tpl_name = pathinfo($url['path']);
		$tpl_name = totranslit($tpl_name['basename']);

		if ($file_path AND $file_path != ".") $tpl_name = $file_path."/".$tpl_name;

		$full_tpl = $tpl_name;

	} else $full_tpl = "";

	if ( in_array($_POST['news_sort'], array("date", "rating", "news_read", "title", "comm_num")) )	{

		$news_sort = $db->safesql( $_POST['news_sort'] );

	} else $news_sort = "";

	if ( in_array($_POST['news_msort'], array("ASC", "DESC")) )	{

		$news_msort = $db->safesql( $_POST['news_msort'] );

	} else $news_msort = "";

	if ( $_POST['news_number'] > 0)
		$news_number = intval( $_POST['news_number'] );
	else $news_number = 0;
	
	if( ! $catid ) {
		msg( "error", $lang['cat_error'], $lang['cat_noid'], "?mod=categories" );
	}
	if( $cat_name == "" ) {
		msg( "error", $lang['cat_error'], $lang['cat_noname'], "javascript:history.go(-1)" );
	}
	
	$row = $db->super_query( "SELECT id, alt_name FROM " . PREFIX . "_category WHERE alt_name = '$alt_cat_name'" );
	
	if( $row['id'] and $row['id'] != $catid ) {
		msg( "error", $lang['cat_error'], $lang['cat_eradd'], "javascript:history.go(-1)" );
	}
	
	if( in_array( $parentid, get_sub_cats( $catid ) ) ) {
		msg( "error", $lang['cat_error'], $lang['cat_noparentid'], "?mod=categories" );
	}

	$db->query( "UPDATE " . PREFIX . "_category SET parentid='$parentid', name='$cat_name', alt_name='$alt_cat_name', icon='$cat_icon', skin='$skin_name', descr='$description', keywords='$keywords', news_sort='$news_sort', news_msort='$news_msort', news_number='$news_number', short_tpl='$short_tpl', full_tpl='$full_tpl', metatitle='$meta_title', show_sub='$show_sub', allow_rss='$allow_rss', fulldescr='$fulldescr', disable_search='$disable_search', disable_main='$disable_main', disable_rating='$disable_rating', disable_comments='$disable_comments', enable_dzen='$enable_dzen', enable_turbo='$enable_turbo' WHERE id='{$catid}'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '14', '{$cat_name}')" );

	@unlink( ENGINE_DIR . '/cache/system/category.php' );
	clear_cache();
	
	msg( "success", $lang['cat_editok'], $lang['cat_editok_1'], "?mod=categories" );
}
// ********************************************************************************
// List all Categories
// ********************************************************************************

echoheader( "<i class=\"fa fa-folder-open-o position-left\"></i><span class=\"text-semibold\">{$lang['cat_head']}</span>", $lang['opt_catc_1'] );

$categorylist = CategoryNewsSelection( 0, 0 );
$skinlist = SelectSkin( '' );

echo <<<HTML

<div class="modal fade" id="newcats" tabindex="-1" role="dialog" aria-labelledby="newcatsLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
	<form method="post" action="" autocomplete="off">
	<input type="hidden" name="mod" value="categories">
	<input type="hidden" name="user_hash" value="{$dle_login_hash}" />
	<input type="hidden" name="action" value="add">
      <div class="modal-header ui-dialog-titlebar">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<span class="ui-dialog-title" id="newcatsLabel">{$lang['cat_add']}</span>
      </div>
      <div class="modal-body">
	  
		<div class="form-group">
			<div class="row">
				<div class="col-sm-6">
					<label>{$lang['cat_name']}</label>
					<div class="input-group">
						<input name="cat_name" type="text" class="form-control" maxlength="50" required>
						<span class="input-group-addon"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_catname']}" ></i></span>
					</div>
				</div>
				<div class="col-sm-6">
					<label>{$lang['cat_url']}</label>
					<div class="input-group">
						<input name="alt_cat_name" type="text" class="form-control" maxlength="50">
						<span class="input-group-addon"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_cataltname']}" ></i></span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-12">
					<label>{$lang['cat_fulldescr']}</label>
					<textarea name="fulldescr" class="classic" style="width:100%;" rows="5"></textarea>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-6">
					<label>{$lang['cat_addicon']}</label>
					<div class="input-group">
						<input name="cat_icon" type="text" class="form-control" maxlength="200">
						<span class="input-group-addon"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_caticon']}" ></i></span>
					</div>
				</div>
				<div class="col-sm-6">
					<label>{$lang['cat_parent']}</label>
					<select class="uniform" name="category" data-width="100%">{$categorylist}</select>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-6">
					<label>{$lang['meta_title']}</label>
					<input name="meta_title" type="text" class="form-control" maxlength="200">
				</div>
				<div class="col-sm-6">
					<label>{$lang['meta_descr_cat']}</label>
					<input name="descr" type="text" class="form-control" maxlength="300">
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-12">
					<label>{$lang['meta_keys']}</label>
					<textarea name="keywords" class="classic" style="width:100%;" rows="3"></textarea>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-6">
					<label>{$lang['opt_sys_sort']}</label>
					<select class="uniform" name="news_sort" data-width="100%"><option value="" selected >{$lang['sys_global']}</option><option value="date">{$lang['opt_sys_sdate']}</option><option value="rating">{$lang['opt_sys_srate']}</option><option value="news_read">{$lang['opt_sys_sview']}</option><option value="title">{$lang['opt_sys_salph']}</option><option value="comm_num">{$lang['opt_sys_scnum']}</option></select>
				</div>
				<div class="col-sm-6">
					<label>{$lang['opt_sys_msort']}</label>
					<select class="uniform" name="news_msort" data-width="100%"><option value="" selected >{$lang['sys_global']}</option><option value="DESC">{$lang['opt_sys_mminus']}</option><option value="ASC">{$lang['opt_sys_mplus']}</option></select>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-6">
					<label>{$lang['opt_sys_sub']}</label>
					<select class="uniform" name="show_sub" data-width="100%"><option value="0" selected >{$lang['sys_global']}</option><option value="1">{$lang['opt_sys_yes']}</option><option value="2">{$lang['opt_sys_no']}</option></select>
				</div>
				<div class="col-sm-6">
					<label>{$lang['cat_allow_rss']}</label>
					<select class="uniform" name="allow_rss" data-width="100%"><option value="1" selected>{$lang['opt_sys_yes']}</option><option value="0">{$lang['opt_sys_no']}</option></select>
				</div>
			</div>
		</div>	
		<div class="form-group">
			<div class="row">
				<div class="col-sm-6">
					<label>{$lang['cat_skin']}</label>
					<div class="input-group">
						{$skinlist}
						<span class="input-group-addon"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_cattempl']}" ></i></span>
					</div>
				</div>
				<div class="col-sm-6">
					<label>{$lang['opt_sys_newc']}</label>
					<div class="input-group">
						<input name="news_number" type="text" class="form-control">
						<span class="input-group-addon"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['hint_news_number']}" ></i></span>
					</div>
				</div>
			</div>
		</div>	
		<div class="form-group">
			<div class="row">
				<div class="col-sm-6">
					<label>{$lang['cat_s_tpl']}</label>
					<div class="input-group">
						<input name="short_tpl" type="text" class="form-control">
						<span class="input-group-addon"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['cat_s_tpl_hit']}" ></i></span>
					</div>
				</div>
				<div class="col-sm-6">
					<label>{$lang['cat_f_tpl']}</label>
					<div class="input-group">
						<input name="full_tpl" type="text" class="form-control">
						<span class="input-group-addon"><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="{$lang['cat_f_tpl_hit']}" ></i></span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-6">
					<div class="checkbox"><label><input class="icheck" type="checkbox" name="enable_dzen" value="1" checked>{$lang['cat_dzen']}</label></div>
					<div class="checkbox"><label><input class="icheck" type="checkbox" name="disable_main" value="1">{$lang['cat_d_main']}</label></div>
					<div class="checkbox"><label><input class="icheck" type="checkbox" name="disable_rating" value="1">{$lang['cat_d_rating']}</label></div>
				</div>
				<div class="col-sm-6">
					<div class="checkbox"><label><input class="icheck" type="checkbox" name="enable_turbo" value="1" checked>{$lang['cat_turbo']}</label></div>
					<div class="checkbox"><label><input class="icheck" type="checkbox" name="disable_comments" value="1">{$lang['cat_d_comments']}</label></div>
					<div class="checkbox"><label><input class="icheck" type="checkbox" name="disable_search" value="1">{$lang['cat_d_search']}</label></div>
				</div>
			</div>
		</div>


      </div>
      <div class="modal-footer" style="margin-top:-20px;">
	    <button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
        <button type="button" class="btn bg-slate-600 btn-sm btn-raised" data-dismiss="modal">{$lang['p_cancel']}</button>
      </div>
	  </form>
    </div>
  </div>
</div>
HTML;


if( !count( $cat_info ) ) {
	
	echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['cat_list']}
	<div class="heading-elements">
		<ul class="icons-list">
			<li><a href="#" data-toggle="modal" data-target="#newcats"><i class="fa fa-plus-circle"></i> {$lang['b_cats_1']}</a></li>
		</ul>
	</div>
  </div>
  <div class="panel-body">
	{$lang['cat_nocat']}
  </div>
  <div class="panel-footer">
	<button class="btn bg-teal btn-sm btn-raised position-left" onclick="$('#newcats').modal(); return false;"><i class="fa fa-plus-circle position-left"></i>{$lang['b_cats_1']}</button>
  </div>
</div>
HTML;

} else {

	function DisplayCategories($parentid = 0, $sublevelmarker = false) {
		global $lang, $cat_info, $config, $dle_login_hash;

		$cat_item = "";
		$root_category = array();
		
		if( count( $cat_info ) ) {
			
			foreach ( $cat_info as $cats ) {
				if( $cats['parentid'] == $parentid ) $root_category[] = $cats['id'];
			}
			
			if( count( $root_category ) ) {
				
				foreach ( $root_category as $id ) {
					
					if($cat_info[$id]['active']) {
						$status = "<span id=\"status-{$cat_info[$id]['id']}\"><a onclick=\"javascript:changestatus('{$cat_info[$id]['id']}', 'off'); return(false);\" href=\"#\"><span title=\"{$lang['cat_on']}\" class=\"text-success position-left tip\"><b><i class=\"fa fa-check-circle\"></i></b></span></a></span>";
					} else {
						$status = "<span id=\"status-{$cat_info[$id]['id']}\"><a onclick=\"javascript:changestatus('{$cat_info[$id]['id']}', 'on'); return(false);\" href=\"#\"><span title=\"{$lang['cat_off']}\" class=\"text-danger position-left tip\"><b><i class=\"fa fa-exclamation-circle\"></i></b></span></a></span>";
					}
					
					if( $config['allow_alt_url'] ) $link = "<a href=\"" . $config['http_home_url'] . get_url( $id ) . "/\" target=\"_blank\">" . stripslashes( $cat_info[$id]['name'] ) . "</a>";
					else $link = "<a href=\"{$config['http_home_url']}index.php?do=cat&category=" . $cat_info[$id]['alt_name'] . "\" target=\"_blank\">" . stripslashes( $cat_info[$id]['name'] ) . "</a>";

					$cat_item .= "<li class=\"dd-item\" data-id=\"{$cat_info[$id]['id']}\"><div class=\"dd-handle\"></div><div class=\"dd-content\">{$status}<b>ID:{$cat_info[$id]['id']}</b> {$link} <div class=\"pull-right\"><a href=\"?mod=categories&action=edit&catid=" . $cat_info[$id]['id'] . "\"><i title=\"{$lang['cat_ed']}\" alt=\"{$lang['cat_ed']}\" class=\"fa fa-pencil-square-o\"></i></a>&nbsp;&nbsp;<a onclick=\"javascript:cdelete('{$cat_info[$id]['id']}'); return(false);\" href=\"?mod=categories&user_hash=" . $dle_login_hash . "&action=remove&catid=" . $cat_info[$id]['id'] . "\"><i title=\"{$lang['cat_del']}\" alt=\"{$lang['cat_del']}\" class=\"fa fa-trash-o text-danger\"></i></a></div></div>";
					
					$cat_item .= DisplayCategories( $id, true );
					
				}

				if( $sublevelmarker ) return "<ol class=\"dd-list\">".$cat_item."</ol>"; else return $cat_item;

			}
		}
		
	}


	echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['cat_list']}
	<div class="heading-elements">
		<ul class="icons-list">
			<li><a href="#" data-toggle="modal" data-target="#newcats"><i class="fa fa-plus-circle position-left"></i>{$lang['b_cats_1']}</a></li>
		</ul>
	</div>
  </div>
  <div class="panel-body">
	
		<div class="dd" id="nestable"><ol class="dd-list">
HTML;

	echo DisplayCategories();

	echo <<<HTML
		</ol></div>
  </div>
	<div class="panel-footer">
		<button class="btn bg-primary-600 btn-sm btn-raised position-left nestable-action" data-action="expand-all">{$lang['cat_expand']}</button><button class="btn bg-primary-600 btn-sm btn-raised position-left nestable-action" data-action="collapse-all">{$lang['cat_collapse']}</button><button class="btn bg-teal btn-sm btn-raised position-left" onclick="$('#newcats').modal(); return false;"><i class="fa fa-plus-circle position-left"></i>{$lang['b_cats_1']}</button>
	</div>
</div>
<script>
	jQuery(function($){

		$('.dd').nestable({
			maxDepth: 500
		});

		$('.dd').nestable('collapseAll');
		
		$('.dd-handle a').on('mousedown', function(e){
			e.stopPropagation();
		});

		$('.dd-handle a').on('touchstart', function(e){
			e.stopPropagation();
		});

		$('.nestable-action').on('click', function(e)
		{
			var target = $(e.target),
				action = target.data('action');
			if (action === 'expand-all') {
				$('.dd').nestable('expandAll');
			}
			if (action === 'collapse-all') {
				$('.dd').nestable('collapseAll');
			}
		});
		
		$('#nestable').nestable().on('change',function(){
		
			var url = "action=catsort&user_hash={$dle_login_hash}&list="+window.JSON.stringify($('.dd').nestable('serialize'));
			ShowLoading('');
			$.post('engine/ajax/controller.php?mod=adminfunction', url, function(data){
	
				HideLoading('');
	
				if (data != 'ok') {

					DLEalert('{$lang['cat_sort_fail']}', '{$lang['p_info']}');

				}
	
			});

		});


	});

	function changestatus(id, status){
		
		if(status == 'on' ) {
			var promt_text = '{$lang['cat_on_1']}';
		} else {
			var promt_text = '{$lang['cat_off_1']}';
		}
		
	    DLEconfirm( promt_text, '{$lang['p_confirm']}', function () {
		
			ShowLoading('');
			
			var url = "action=catchangestatus&user_hash={$dle_login_hash}&status="+status+"&id="+id;
			
			$.post('engine/ajax/controller.php?mod=adminfunction', url, function(data){
	
				HideLoading('');
	
				if (data != 'ok') {

					DLEalert('{$lang['cat_status_fail']}', '{$lang['p_info']}');

				} else {
					
					if(status == 'on' ) {
						$("#status-"+id).html('<a onclick="javascript:changestatus('+id+', \'off\'); return(false);" href="#"><span title="{$lang['cat_on']}" class="text-success position-left tip"><b><i class="fa fa-check-circle"></i></b></span></a>');
					} else {
						$("#status-"+id).html('<a onclick="javascript:changestatus('+id+', \'on\'); return(false);" href="#"><span title="{$lang['cat_off']}" class="text-danger position-left tip"><b><i class="fa fa-exclamation-circle"></i></b></span></a>');
					}
					
				}
	
			});
			
			
		} );
	}
	
	function cdelete(id){
		
	    DLEconfirm( '{$lang['cat_delete']}', '{$lang['p_confirm']}', function () {
			document.location='?mod=categories&user_hash={$dle_login_hash}&action=remove&catid=' + id + '';
		} );
	}
</script>
HTML;
}


echofooter();
?>