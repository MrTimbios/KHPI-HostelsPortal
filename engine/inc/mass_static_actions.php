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
 File: mass_static_action.php
-----------------------------------------------------
 Use: mass action static pages
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['admin_static'] ) {
	msg( "error", $lang['mass_error'], $lang['mass_ddenied'], $_SESSION['admin_referrer'] );
}

if( ! $_SESSION['admin_referrer'] ) {
	
	$_SESSION['admin_referrer'] = "?mod=static&amp;action=list";

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

if( $action == "mass_date" ) {
	$field = "date";
	$value = time();
	$k_mass = true;
	$title = $lang['mass_static_edit_date_tl'];
	$lang['mass_confirm'] = $lang['mass_static_edit_date_fr1'];
	$lang['mass_confirm_1'] = $lang['mass_static_confirm_2'];
} elseif( $action == "mass_clear_count" ) {
	$field = "views";
	$value = 0;
	$k_mass = true;
	$title = $lang['mass_clear_count_2'];
	$lang['mass_confirm'] = $lang['mass_clear_count_1'];
	$lang['mass_confirm_1'] = $lang['mass_static_confirm_2'];
}

if( $_POST['doaction'] == "mass_update" AND $field ) {
	foreach ( $selected_news as $id ) {
		$id = intval( $id );
		$db->query( "UPDATE " . PREFIX . "_static SET {$field}='{$value}' WHERE id='{$id}'" );
	}
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '39', '')" );
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
<input class=\"btn bg-teal btn-sm btn-raised position-left\" type=submit value=\"{$lang['mass_yes']}\" style=\"min-width:100px;\"><input type=button class=\"btn bg-danger btn-sm btn-raised position-left\" value=\"{$lang['mass_no']}\" style=\"min-width:100px;\" onclick=\"javascript:document.location='?mod=static&action=list'\">
<input type=hidden name=action value=\"{$action}\">
<input type=hidden name=user_hash value=\"{$dle_login_hash}\">
<input type=hidden name=doaction value=\"mass_update\">
<input type=hidden name=mod value=\"mass_static_actions\">";
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">\n";
	}
	
	echo <<<HTML
</td>
		    </tr>
		</table>
  </div>
</div>
</form>
HTML;
	
	echofooter();
	exit();

}

if( $action == "mass_delete" ) {
	
	echoheader( "<i class=\"fa fa-comment-o position-left\"></i><span class=\"text-semibold\">{$lang['header_box_title']}</span>", $lang['mass_static_delete'] );
	
	echo <<<HTML
<form method="post">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['mass_static_delete']}
  </div>
  <div class="panel-body">
		<table width="100%">
		    <tr>
		        <td height="100" class="text-center">
{$lang['mass_confirm']}
HTML;
	
	echo "(<b>" . count( $selected_news ) . "</b>) $lang[mass_static_confirm_3]<br><br>
<input class=\"btn bg-teal btn-sm btn-raised position-left\" type=submit value=\"{$lang['mass_yes']}\" style=\"min-width:100px;\"><input type=button class=\"btn bg-danger btn-sm btn-raised position-left\" value=\"{$lang['mass_no']}\" style=\"min-width:100px;\" onclick=\"javascript:document.location='?mod=static&action=list'\">
<input type=hidden name=action value=\"do_mass_delete\">
<input type=hidden name=user_hash value=\"{$dle_login_hash}\">
<input type=hidden name=mod value=\"mass_static_actions\">";
	foreach ( $selected_news as $newsid ) {
		$newsid = intval($newsid);
		echo "<input type=hidden name=selected_news[] value=\"$newsid\">\n";
	}
	
	echo <<<HTML
</td>
		    </tr>
		</table>
  </div>
</div>
</form>
HTML;
	
	echofooter();
	exit();

} elseif( $action == "do_mass_delete" ) {
	
	$deleted_articles = 0;
	
	foreach ( $selected_news as $id ) {
		
		$id = intval( $id );

		$deleted_articles ++;
		
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

	}

	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '40', '')" );
	
	if( count( $selected_news ) == $deleted_articles ) {
		msg( "success", $lang['mass_static_delete'], $lang['mass_delok'], $_SESSION['admin_referrer'] );
	} else {
		msg( "error", $lang['mass_notok'], "$deleted_articles $lang[mass_i] " . count( $selected_news ) . " $lang[mass_notok_1]", $_SESSION['admin_referrer'] );
	}
	
} else {
	
	msg( "info", $lang['mass_noact'], $lang['mass_noact_1'], $_SESSION['admin_referrer'] );

}
?>