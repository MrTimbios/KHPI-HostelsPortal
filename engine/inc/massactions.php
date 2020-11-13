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
 File: massaction.php
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( ! $_SESSION['admin_referrer'] ) {
	
	$_SESSION['admin_referrer'] = "?mod=editnews&amp;action=list";

}

if( !$user_group[$member_id['user_group']]['admin_editnews'] OR !$user_group[$member_id['user_group']]['allow_all_edit'] ) {
	msg( "error", $lang['mass_error'], $lang['mass_ddenied'], $_SESSION['admin_referrer'] );
}

$allow_list = explode( ',', $user_group[$member_id['user_group']]['cat_add'] );

if( $allow_list[0] != "all" ) {
	msg( "error", $lang['mass_error'], $lang['mass_ddenied'], $_SESSION['admin_referrer'] );
}

$selected_news = $_REQUEST['selected_news'];

if( ! $selected_news ) {
	msg( "error", $lang['mass_error'], $lang['mass_denied'], $_SESSION['admin_referrer'] );
}

if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
	
	die( "Hacking attempt! User not found" );

}

$action = htmlspecialchars( strip_tags( stripslashes( $_POST['action'] ) ) );

$k_mass = false;
$field = false;

if( $action == "mass_approve" ) {
	$field = "approve";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_edit_app_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_app_fr1'];
} elseif( $action == "mass_date" ) {
	$field = "date";
	$value = date( "Y-m-d H:i:s", time() );
	$k_mass = true;
	$title = $lang['mass_edit_date_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_date_fr1'];
} elseif( $action == "mass_not_approve" ) {
	$field = "approve";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_edit_app_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_app_fr2'];
} elseif( $action == "mass_fixed" ) {
	$field = "fixed";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_edit_fix_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_fix_fr1'];
} elseif( $action == "mass_not_fixed" ) {
	$field = "fixed";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_edit_fix_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_fix_fr2'];
} elseif( $action == "mass_comments" ) {
	$field = "allow_comm";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_edit_com_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_comm_fr1'];
	$lang['mass_confirm_1'] = $lang['mass_confirm_2'];
} elseif( $action == "mass_not_comments" ) {
	$field = "allow_comm";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_edit_com_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_comm_fr2'];
	$lang['mass_confirm_1'] = $lang['mass_confirm_2'];
} elseif( $action == "mass_rating" ) {
	$field = "allow_rate";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_edit_rate_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_rate_fr1'];
	$lang['mass_confirm_1'] = $lang['mass_confirm_2'];
} elseif( $action == "mass_not_rating" ) {
	$field = "allow_rate";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_edit_rate_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_rate_fr2'];
	$lang['mass_confirm_1'] = $lang['mass_confirm_2'];
} elseif( $action == "mass_main" ) {
	$field = "allow_main";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_edit_main_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_main_fr1'];
} elseif( $action == "mass_not_main" ) {
	$field = "allow_main";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_edit_main_tl'];
	$lang['mass_confirm'] = $lang['mass_edit_main_fr2'];

} elseif( $action == "mass_clear_count" ) {
	$field = "news_read";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_clear_count_2'];
	$lang['mass_confirm'] = $lang['mass_clear_count_1'];
	$lang['mass_confirm_1'] = $lang['mass_confirm_2'];

} elseif( $action == "mass_clear_rating" ) {
	$field = "rating";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_clear_rating_2'];
	$lang['mass_confirm'] = $lang['mass_clear_rating_1'];
	$lang['mass_confirm_1'] = $lang['mass_confirm_2'];

} elseif( $action == "mass_clear_cloud" ) {
	$field = "tags";
	$value = "";
	$k_mass = true;
	$title = $lang['mass_clear_cloud_2'];
	$lang['mass_confirm'] = $lang['mass_clear_cloud_1'];
	$lang['mass_confirm_1'] = $lang['mass_confirm_2'];
} elseif( $action == "mass_yandex_dzen" ) {
	$field = "allow_rss_dzen";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_dzen_tl'];
	$lang['mass_confirm'] = $lang['mass_dzen_fr1'];
	$lang['mass_confirm_1'] = $lang['mass_confirm_2'];
} elseif( $action == "mass_not_yandex_dzen" ) {
	$field = "allow_rss_dzen";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_dzen_tl'];
	$lang['mass_confirm'] = $lang['mass_dzen_fr2'];
	$lang['mass_confirm_1'] = $lang['mass_confirm_2'];
} elseif( $action == "mass_yandex_turbo" ) {
	$field = "allow_rss_turbo";
	$value = 1;
	$k_mass = true;
	$title = $lang['mass_turbo_tl'];
	$lang['mass_confirm'] = $lang['mass_turbo_fr1'];
	$lang['mass_confirm_1'] = $lang['mass_confirm_2'];
} elseif( $action == "mass_not_yandex_turbo" ) {
	$field = "allow_rss_turbo";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_turbo_tl'];
	$lang['mass_confirm'] = $lang['mass_turbo_fr2'];
	$lang['mass_confirm_1'] = $lang['mass_confirm_2'];
}

if( $_POST['doaction'] == "mass_update" AND $field ) {
	
	foreach ( $selected_news as $id ) {
		$id = intval( $id );

		if (in_array($field, array("news_read", "allow_rate", "rating", "vote_num", "disable_index", "allow_rss_turbo", "allow_rss_dzen" ) )) {
			$db->query( "UPDATE " . PREFIX . "_post_extras SET {$field}='{$value}' WHERE news_id='{$id}'" );
		} else	$db->query( "UPDATE " . PREFIX . "_post SET {$field}='{$value}' WHERE id='{$id}'" );
		
		if( $field == "approve" ) {
			
			if( $value ) {
				
				$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$id}'" );
				$row = $db->super_query( "SELECT tags FROM " . PREFIX . "_post where id = '{$id}'" );
				
				if( $row['tags'] ) {
					
					$tags = array ();
					
					$row['tags'] = explode( ",", $row['tags'] );
					
					foreach ( $row['tags'] as $tags_value ) {
						
						$tags[] = "('" . $id . "', '" . $db->safesql(stripslashes(trim( $tags_value ))) . "')";
					}
					
					$tags = implode( ", ", $tags );
					$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tags );
				
				}

				$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$id}'" );
				$row = $db->super_query( "SELECT category FROM " . PREFIX . "_post where id = '{$id}'" );
				
				if( $row['category'] ) {
					
					$cat_ids = array ();
					
					$row['category'] = explode( ",", $row['category'] );
					
					foreach ( $row['category'] as $cats_value ) {
						
						$cat_ids[] = "('" . $id . "', '" . $db->safesql(stripslashes(trim( $cats_value ))) . "')";
					}
					
					$cat_ids = implode( ", ", $cat_ids );
					$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
				
				}
				
				$row = $db->super_query( "SELECT xfields FROM " . PREFIX . "_post WHERE id = '{$id}'" );

				if ($row['xfields'] != "") {
					
					$xf_search_words = array ();			
					$xfields = xfieldsload();
					$postedxfields = xfieldsdataload($row['xfields']);
					
					if( !empty( $postedxfields ) ) {
						
						foreach ($xfields as $name => $value3) {
							if ( $value3[6] AND !empty( $postedxfields[$value3[0]] ) ) {
								$temp_array = explode( ",", stripslashes($postedxfields[$value3[0]]) );
								
								foreach ($temp_array as $value2) {
									$value2 = trim($value2);
									if($value2) $xf_search_words[] = array( $db->safesql($value3[0]), $db->safesql($value2) );
								}
							
							}
						}
						
						if ( count($xf_search_words) ) {
							
							$temp_array = array();
							
							foreach ( $xf_search_words as $value3 ) {
								
								$temp_array[] = "('" . $id . "', '" . $value3[0] . "', '" . $value3[1] . "')";
							}
							
							$xf_search_words = implode( ", ", $temp_array );
							$db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue) VALUES " . $xf_search_words );
						}
					}
				}
			
			} else {
				
				$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$id}'" );
				$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$id}'" );
				$db->query( "DELETE FROM " . PREFIX . "_xfsearch WHERE news_id = '{$id}'" );
				
			}
		
		}

		if ( $field == "news_read" ) {

			$db->query( "DELETE FROM " . PREFIX . "_views WHERE news_id = '{$id}'" );

		}

		if ( $field == "rating" ) {

			$db->query( "UPDATE " . PREFIX . "_post_extras SET vote_num='0' WHERE news_id='{$id}'" );
			$db->query( "DELETE FROM " . PREFIX . "_logs WHERE news_id = '{$id}'" );

		}

		if ( $field == "tags" ) {

			$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '{$id}'" );

		}
	
	}

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '46', '')" );
	
	clear_cache();
	
	msg( "success", $lang['db_ok'], $lang['db_ok_1'], $_SESSION['admin_referrer'] );
}

if( $k_mass ) {
	
	echoheader( "<i class=\"fa fa-comment-o position-left\"></i><span class=\"text-semibold\">{$lang['header_box_title']}</span>", $title );
	
	echo <<<HTML
<form method="post">
<div class="panel panel-default">
  <div class="panel-heading">
    {$title}
  </div>
  <div class="panel-body">
		<table width="100%">
		    <tr>
		        <td height="100" class="text-center">{$lang['mass_confirm']}
HTML;
	
	echo " (<b>" . count( $selected_news ) . "</b>) $lang[mass_confirm_1]<br><br>
<input class=\"btn bg-teal btn-sm btn-raised position-left\" type=\"submit\" value=\"{$lang['mass_yes']}\" style=\"min-width:100px;\"><input type=button class=\"btn bg-danger btn-sm btn-raised position-left\" value=\"{$lang['mass_no']}\" style=\"min-width:100px;\" onclick=\"javascript:document.location='{$_SESSION['admin_referrer']}'\">
<input type=hidden name=action value=\"{$action}\">
<input type=hidden name=user_hash value=\"{$dle_login_hash}\">
<input type=hidden name=doaction value=\"mass_update\">
<input type=hidden name=mod value=\"massactions\">";
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">\n";
	}
	
	echo <<<HTML
</td>
		    </tr>
		</table>
  </div>
</div></form>
HTML;
	
	echofooter();
	exit();

}

if( $action == "mass_delete" ) {
	
	echoheader( "<i class=\"fa fa-comment-o position-left\"></i><span class=\"text-semibold\">{$lang['header_box_title']}</span>", $lang['mass_head'] );
	
	echo <<<HTML
<form method="post">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['mass_head']}
  </div>
  <div class="panel-body">
		<table width="100%">
		    <tr>
		        <td height="100" class="text-center">{$lang['mass_confirm']}
HTML;
	
	echo "(<b>" . count( $selected_news ) . "</b>) $lang[mass_confirm_1]<br><br>
<input class=\"btn bg-teal btn-sm btn-raised position-left\" type=\"submit\" value=\"{$lang['mass_yes']}\" style=\"min-width:100px;\"><input type=button class=\"btn bg-danger btn-sm btn-raised position-left\" value=\"{$lang['mass_no']}\" style=\"min-width:100px;\" onclick=\"javascript:document.location='{$_SESSION['admin_referrer']}'\">
<input type=hidden name=action value=\"do_mass_delete\">
<input type=hidden name=user_hash value=\"{$dle_login_hash}\">
<input type=hidden name=mod value=\"massactions\">";
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">\n";
	}
	
	echo <<<HTML
</td>
		    </tr>
		</table>
  </div>
</div></form>
HTML;
	
	echofooter();
	exit();

} elseif( $action == "do_mass_delete" ) {
	
	$deleted_articles = 0;
	
	foreach ( $selected_news as $id ) {
		
		$id = intval( $id );
		$row = $db->super_query( "SELECT title FROM " . PREFIX . "_post WHERE id = '{$id}'" );

		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '26', '".$db->safesql($row['title'])."')" );
		deletenewsbyid( $id );
		$deleted_articles ++;

	}
	
	clear_cache();
	
	if( count( $selected_news ) == $deleted_articles ) {
		msg( "success", $lang['mass_head'], $lang['mass_delok'], $_SESSION['admin_referrer'] );
	} else {
		msg( "error", $lang['mass_notok'], "$deleted_articles $lang[mass_i] " . count( $selected_news ) . " $lang[mass_notok_1]", $_SESSION['admin_referrer'] );
	}
} elseif( $action == "mass_add_cat" ) {

	echoheader( "<i class=\"fa fa-comment-o position-left\"></i><span class=\"text-semibold\">{$lang['header_box_title']}</span>", $lang['mass_cat_1'] );
	
	$count = count( $selected_news );
	if( $config['allow_multi_category'] ) $category_multiple = "class=\"categoryselect\" multiple";
	else $category_multiple = "class=\"categoryselect\"";
	
	echo <<<HTML
<form method="post">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['mass_cat_1']}
  </div>
  <div class="panel-body">
		<table width="100%">
		    <tr>
		        <td height="100" class="text-center">{$lang['mass_cat_4']} (<b>{$count}</b>)<br /><br />
<select data-placeholder="{$lang['addnews_cat_sel']}" name="add_to_category[]" {$category_multiple} style="width:350px;">
HTML;
	
	echo CategoryNewsSelection( 0, 0 );
	echo "</select><br /><br />";
	
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">";
	}
	
	echo <<<HTML
<input type=hidden name=user_hash value="{$dle_login_hash}"><input type="hidden" name="action" value="do_mass_add_cat"><input type="hidden" name="mod" value="massactions">&nbsp;<input type="submit" value="{$lang['b_start']}" class="btn bg-teal btn-sm btn-raised"></td>
</td>
		    </tr>
		</table>
  </div>
</div></form>
<script>
$(function(){
	$('.categoryselect').chosen({allow_single_deselect:true, no_results_text: '{$lang['addnews_cat_fault']}'});
});
</script>
HTML;
	
	echofooter();
	exit();

} elseif( $action == "mass_move_to_cat" ) {

	echoheader( "<i class=\"fa fa-comment-o position-left\"></i><span class=\"text-semibold\">{$lang['header_box_title']}</span>", $lang['mass_cat_1'] );
	
	$count = count( $selected_news );
	if( $config['allow_multi_category'] ) $category_multiple = "class=\"categoryselect\" multiple";
	else $category_multiple = "class=\"categoryselect\"";
	
	echo <<<HTML
<form method="post">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['mass_cat_1']}
  </div>
  <div class="panel-body">
		<table width="100%">
		    <tr>
		        <td height="100" class="text-center">{$lang['mass_cat_2']} (<b>{$count}</b>) {$lang['mass_cat_3']}<br /><br />
<select data-placeholder="{$lang['addnews_cat_sel']}" name="move_to_category[]" {$category_multiple} style="width:350px;">
HTML;
	
	echo CategoryNewsSelection( 0, 0 );
	echo "</select><br /><br />";
	
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">";
	}
	
	echo <<<HTML
<input type=hidden name=user_hash value="{$dle_login_hash}"><input type="hidden" name="action" value="do_mass_move_to_cat"><input type="hidden" name="mod" value="massactions">&nbsp;<input type="submit" value="{$lang['b_start']}" class="btn bg-teal btn-sm btn-raised"></td>
</td>
		    </tr>
		</table>
  </div>
</div></form>
<script>
$(function(){
	$('.categoryselect').chosen({allow_single_deselect:true, no_results_text: '{$lang['addnews_cat_fault']}'});
});
</script>
HTML;
	
	echofooter();
	exit();

} elseif( $action == "mass_edit_symbol" ) {
	
	echoheader( "<i class=\"fa fa-comment-o position-left\"></i><span class=\"text-semibold\">{$lang['header_box_title']}</span>", $lang['catalog_url'] );
	
	$count = count( $selected_news );
	
	echo <<<HTML
<form method="post">
<div class="panel panel-default">
  <div class="panel-heading">
	{$lang['catalog_url']}
  </div>
  <div class="panel-body">
		<table width="100%">
		    <tr>
		        <td height="100" class="text-center">{$lang['catalog_url']}<input type="text" name="catalog_url" class="form-control position-left position-right" style="width:60px;" maxlength="3">
HTML;
	
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">";
	}
	
	echo <<<HTML
<input type=hidden name=user_hash value="{$dle_login_hash}"><input type="hidden" name="action" value="do_mass_edit_symbol"><input type="hidden" name="mod" value="massactions"><input type="submit" value="{$lang['b_start']}" class="btn bg-teal btn-sm btn-raised"></td>
</td>
		    </tr>
		</table>
  </div>
</div></form>
HTML;
	
	echofooter();
	exit();
	
} elseif( $action == "mass_edit_cloud" ) {
	
	echoheader( "<i class=\"fa fa-comment-o position-left\"></i><span class=\"text-semibold\">{$lang['header_box_title']}</span>", $lang['addnews_tags'] );
	
	$count = count( $selected_news );
	
	echo <<<HTML
<form method="post">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['addnews_tags']}
  </div>
  <div class="panel-body">
		<table width="100%">
		    <tr>
		        <td height="100" class="text-center">{$lang['addnews_tags']} <input type="text" name="tags" class="form-control position-left position-right" style="width:200px;" value="">
HTML;
	
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">";
	}
	
	echo <<<HTML
<input type=hidden name=user_hash value="{$dle_login_hash}"><input type="hidden" name="action" value="do_mass_edit_cloud"><input type="hidden" name="mod" value="massactions">&nbsp;<input type="submit" value="{$lang['b_start']}" class="btn bg-teal btn-sm btn-raised"></td>
</td>
		    </tr>
		</table>
  </div>
</div></form>
HTML;
	
	echofooter();
	exit();

} elseif( $action == "mass_edit_author" ) {

	if ($member_id['user_group'] != 1) msg( "error", $lang['index_denied'], $lang['index_denied'], $_SESSION['admin_referrer'] );
	
	echoheader( "<i class=\"fa fa-comment-o position-left\"></i><span class=\"text-semibold\">{$lang['header_box_title']}</span>", $lang['edit_selauthor_1'] );
	
	$count = count( $selected_news );
	
	echo <<<HTML
<form method="post">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['edit_selauthor_1']}
  </div>
  <div class="panel-body">
		<table width="100%">
		    <tr>
		        <td height="100" class="text-center">{$lang['edit_selauthor_2']} <input type="text" name="new_author" class="form-control position-left position-right" style="width:200px;" value="">
HTML;
	
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">";
	}
	
	echo <<<HTML
<input type=hidden name=user_hash value="{$dle_login_hash}"><input type="hidden" name="action" value="do_mass_edit_author"><input type="hidden" name="mod" value="massactions">&nbsp;<input type="submit" value="{$lang['b_start']}" class="btn bg-teal btn-sm btn-raised"></td>
</td>
		    </tr>
		</table>
  </div>
</div></form>
HTML;
	
	echofooter();
	exit();

} elseif( $action == "do_mass_add_cat" ) {
	
	$moved_articles = 0;
	
	if( !count($_REQUEST['add_to_category']) ) {
		msg( "error", $lang['mass_cat_notok'], $lang['mass_cat_notok_1'], $_SESSION['admin_referrer'] );
	}

	$category_list = array();

	foreach ( $_REQUEST['add_to_category'] as $value ) {
		$category_list[] = intval($value);
	}
	
	$add_to_category = $db->safesql( implode( ',', $category_list ) );
	
	foreach ( $selected_news as $id ) {
		$moved_articles ++;
		$id = intval( $id );

		$row = $db->super_query("SELECT category, approve FROM " . PREFIX . "_post WHERE id = '{$id}'");

		if( $row['category'] ) {
			$news_cats = explode(',', $row['category']);
			
			foreach ( $category_list as $value ) {
				if( !in_array($value, $news_cats)) $news_cats[] = $value;
			}
			
			$add_to_category = $db->safesql( implode( ',', $news_cats ) );
		}

		$db->query( "UPDATE " . PREFIX . "_post SET category='{$add_to_category}' WHERE id='{$id}'" );
		
		$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$id}'" );

		if( $add_to_category AND $row['approve'] ) {

			$cat_ids = array ();

			$cat_ids_arr = explode( ",", $add_to_category );

			foreach ( $cat_ids_arr as $value ) {

				$cat_ids[] = "('" . $id . "', '" . trim( $value ) . "')";
			}

			$cat_ids = implode( ", ", $cat_ids );
			$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );

		}
		
	}

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '46', '')" );
	
	clear_cache();
	
	if( count( $selected_news ) == $moved_articles ) {
		msg( "success", $lang['cat_addok'], "{$lang['cat_addok']} ({$moved_articles})", $_SESSION['admin_referrer'] );
	} else {
		msg( "error", $lang['mass_cat_notok'], $lang['mass_cat_notok_1'], $_SESSION['admin_referrer'] );
	}

} elseif( $action == "do_mass_move_to_cat" ) {
	
	$moved_articles = 0;
	
	if( !count(  $_REQUEST['move_to_category'] ) ) {
		$_REQUEST['move_to_category'] = array ();
		$_REQUEST['move_to_category'][] = '0';
	}
	$category_list = array();

	foreach ( $_REQUEST['move_to_category'] as $value ) {
		$category_list[] = intval($value);
	}
	
	$move_to_category = $db->safesql( implode( ',', $category_list ) );
	
	foreach ( $selected_news as $id ) {
		$moved_articles ++;
		$id = intval( $id );
		
		$db->query( "UPDATE " . PREFIX . "_post SET category='{$move_to_category}' WHERE id='$id'" );

		$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$id}'" );

		$row = $db->super_query("SELECT approve FROM " . PREFIX . "_post WHERE id = '{$id}'");
		
		if( $move_to_category AND $row['approve'] ) {

			$cat_ids = array ();

			$cat_ids_arr = explode( ",", $move_to_category );

			foreach ( $cat_ids_arr as $value ) {

				$cat_ids[] = "('" . $id . "', '" . intval( $value ) . "')";
			}

			$cat_ids = implode( ", ", $cat_ids );
			$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );

		}
		
	}

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '46', '')" );
	
	clear_cache();
	
	if( count( $selected_news ) == $moved_articles ) {
		msg( "success", $lang['mass_cat_ok'], "$lang[mass_cat_ok] ($moved_articles)", $_SESSION['admin_referrer'] );
	} else {
		msg( "error", $lang['mass_cat_notok'], $lang['mass_cat_notok_1'], $_SESSION['admin_referrer'] );
	}

} elseif( $action == "do_mass_edit_author" ) {

	if ($member_id['user_group'] != 1) msg( "error", $lang['index_denied'], $lang['index_denied'], $_SESSION['admin_referrer'] );
	
	$edit_articles = 0;
	
	$new_author = $db->safesql( $_POST['new_author'] );

	$row = $db->super_query( "SELECT user_id, name  FROM " . USERPREFIX . "_users WHERE name = '{$new_author}'" );

	if( !$row['user_id'] ) {

		msg( "error", $lang['edit_selauthor_1'], $lang['edit_selauthor_3'], $_SESSION['admin_referrer'] );

	}

	foreach ( $selected_news as $id ) {
		$id = intval( $id );

		$old = $db->super_query( "SELECT autor  FROM " . PREFIX . "_post WHERE id = '{$id}'" );

		if ( $old['autor'] != $row['name'] ) {
			$edit_articles ++;

			$db->query( "UPDATE " . PREFIX . "_post SET autor='{$row['name']}' WHERE id='{$id}'" );
			$db->query( "UPDATE " . PREFIX . "_post_extras SET user_id='{$row['user_id']}' WHERE news_id='{$id}'" );
			$db->query( "UPDATE " . PREFIX . "_images SET author='{$row['name']}' WHERE news_id='{$id}'" );
			$db->query( "UPDATE " . PREFIX . "_files SET author='{$row['name']}' WHERE news_id='{$id}'" );
							
			$db->query( "UPDATE " . USERPREFIX . "_users SET news_num=news_num+1 WHERE user_id='{$row['user_id']}'" );
			$db->query( "UPDATE " . USERPREFIX . "_users SET news_num=news_num-1 WHERE name='{$old['autor']}'" );
		}
	}
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '46', '')" );
	
	clear_cache();
	
	msg( "success", $lang['edit_selauthor_4'], $lang['edit_selauthor_4'] . " ($edit_articles)", $_SESSION['admin_referrer'] );

} elseif( $action == "do_mass_edit_symbol" ) {
	
	$edit_articles = 0;
	
	$catalog_url = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( trim( $_POST['catalog_url'] ) ) ), ENT_QUOTES, $config['charset'] ), 0, 3, $config['charset'] ) );
	
	foreach ( $selected_news as $id ) {
		$edit_articles ++;
		$id = intval( $id );
		
		$db->query( "UPDATE " . PREFIX . "_post SET symbol='$catalog_url' WHERE id='$id'" );
	}
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '46', '')" );
	
	clear_cache();
	
	msg( "success", $lang['mass_symbol_ok'], $lang['mass_symbol_ok'] . " ($edit_articles)", $_SESSION['admin_referrer'] );

} elseif( $action == "do_mass_edit_cloud" ) {
	
	$edit_articles = 0;
	
	if( @preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $_POST['tags'] ) ) $_POST['tags'] = "";
	else $_POST['tags'] = @$db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_POST['tags'] ) ) ), ENT_QUOTES, $config['charset'] ) );

	if ( $_POST['tags'] ) {

		$temp_array = array();
		$tags_array = array();
		$temp_array = explode (",", $_POST['tags']);

		if (count($temp_array)) {

			foreach ( $temp_array as $value ) {
				if( trim($value) ) $tags_array[] = trim( $value );
			}

		}

		if ( count($tags_array) ) $_POST['tags'] = implode(", ", $tags_array); else $_POST['tags'] = "";

	}

	if ( $_POST['tags'] ) {
		foreach ( $selected_news as $id ) {
			$edit_articles ++;
			$id = intval( $id );

			$db->query( "DELETE FROM " . PREFIX . "_tags WHERE news_id = '$id'" );
			$db->query( "UPDATE " . PREFIX . "_post SET tags='{$_POST['tags']}' WHERE id='$id'" );

			$tags = array ();
						
			$tags_array = explode( ",", $_POST['tags'] );
						
			foreach ( $tags_array as $value ) {
							
							$tags[] = "('" . $id . "', '" . trim( $value ) . "')";
			}
						
			$tags = implode( ", ", $tags );
			$db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tags );
		}
	}

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '46', '')" );

	clear_cache();
	
	msg( "success", $lang['mass_cloud_ok'], $lang['mass_cloud_ok'] . " ($edit_articles)", $_SESSION['admin_referrer'] );
	
} else {
	
	msg( "info", $lang['mass_noact'], $lang['mass_noact_1'], $_SESSION['admin_referrer'] );

}
?>