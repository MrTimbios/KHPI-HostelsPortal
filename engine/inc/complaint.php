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
 File: complaint.php
-----------------------------------------------------
 Use: complaint manage
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$user_group[$member_id['user_group']]['admin_complaint'] ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if ($_GET['action'] == "delete") {
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$id = intval($_GET['id']);

	$db->query( "DELETE FROM " . PREFIX . "_complaint WHERE id = '{$id}'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '22', '')" );

	header( "Location: ?mod=complaint" ); die();
}

if ($_POST['action'] == "mass_delete") {
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$selected_complaint = $_POST['selected_complaint'];

	if( ! $selected_complaint ) {
		msg( "error", $lang['mass_error'], $lang['opt_complaint_6'], "?mod=complaint" );
	}

	foreach ( $selected_complaint as $complaint ) {

		$complaint = intval($complaint);

		$db->query( "DELETE FROM " . PREFIX . "_complaint WHERE id = '{$complaint}'" );
	}
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '22', '')" );

	header( "Location: ?mod=complaint" ); die();
}

$found = false;
if( !$langformatdatefull ) $langformatdatefull = "d.m.Y H:i";

echoheader("<i class=\"fa fa-bullhorn position-left\"></i><span class=\"text-semibold\">{$lang['opt_complaint']}</span>", $lang['header_compl_1']);

echo <<<HTML
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
//-->
</script>
HTML;

$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_complaint WHERE p_id > '0'" );

if($row['count']) {

echo <<<HTML
<form action="?mod=complaint" method="post" name="optionsbar" id="optionsbar">
<input type="hidden" name="mod" value="complaint">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_complaint_1']}
  </div>
  <div class="table-responsive">

    <table class="table table-xs">
      <thead>
      <tr>
        <th style="width: 180px">{$lang['opt_complaint_3']}</th>
        <th>{$lang['opt_complaint_2']}</th>
		<th style="width: 250px">{$lang['user_action']}</th>
        <th style="width: 40px"><input type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all()" class="icheck"></th>
      </tr>
      </thead>
	  <tbody>
HTML;



$db->query("SELECT `id`, `p_id`, `text`, `from`, `to`, `date`  FROM " . PREFIX . "_complaint WHERE p_id > '0' ORDER BY id DESC");

$entries = "";

while($row = $db->get_row()) {

	$found = true;

	if ( $row['date'] ) $date = date( $langformatdatefull, $row['date'] )."<br /><br />"; else $date = "";

	$row['text'] = stripslashes($row['text']);

	$from = "<a href=\"?mod=editusers&action=edituser&user=".urlencode($row['from'])."\" target=\"_blank\">{$row['from']}</a><br /><br /><a class=\"btn bg-brown-600 btn-sm btn-raised\" href=\"" . $config['http_home_url'] . "index.php?do=pm&doaction=newpm&username=".urlencode($row['from'])."\" target=\"_blank\">{$lang['send_pm']}</a>";
	$to = "<a href=\"?mod=editusers&action=edituser&user=".urlencode($row['to'])."\" target=\"_blank\">{$row['to']}</a>, <a href=\"" . $config['http_home_url'] . "index.php?do=pm&doaction=newpm&username=".urlencode($row['to'])."\" target=\"_blank\">{$lang['send_pm']}</a>";

	$entries .= "<tr>
	<td>{$date}<b>{$from}</b></td>
    <td>{$lang['opt_complaint_4']} <strong>{$to}</strong><br /><br />{$row['text']}<br /><br /></td>
    <td><a uid=\"{$row['id']}\" href=\"?mod=complaint\" class=\"dellink1 btn bg-danger btn-sm btn-raised\"><i class=\"fa fa-trash-o position-left\"></i>{$lang['opt_complaint_11']}</a></td>
    <td><input name=\"selected_complaint[]\" value=\"{$row['id']}\" type=\"checkbox\" class=\"icheck\"></td>
    </tr>";

}


echo <<<HTML
		{$entries}
	  </tbody>
	</table>
	
   </div>
	<div class="panel-footer text-right">
		<select class="uniform" name="action"><option value="">{$lang['edit_selact']}</option><option value="mass_delete">{$lang['edit_seldel']}</option></select>&nbsp;<input class="btn bg-slate-600 btn-sm btn-raised" type="submit" value="{$lang['b_start']}">
	</div>	
</div>
</form>
<script>  
<!-- 

	function ckeck_uncheck_all() {
	    var frm = document.optionsbar;
	    for (var i=0;i<frm.elements.length;i++) {
	        var elmnt = frm.elements[i];
	        if (elmnt.type=='checkbox') {
	            if(frm.master_box.checked == true){ elmnt.checked=false; $(elmnt).parents('tr').removeClass('warning');}
	            else{ elmnt.checked=true; $(elmnt).parents('tr').addClass('warning');}
	        }
	    }
	    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
	    else{ frm.master_box.checked = true; }
		
		$(frm.master_box).parents('tr').removeClass('warning');
		
		$.uniform.update();
		
	}
	
	$(function(){

		var tag_name = '';

		$('.dellink1').click(function(){

			id_comp = $(this).attr('uid');

		    DLEconfirm( '{$lang['opt_complaint_5']}', '{$lang['p_confirm']}', function () {

				document.location='?mod=complaint&user_hash={$dle_login_hash}&action=delete&id=' + id_comp + '';

			} );

			return false;
		});
	});
	
//-->
</script>
HTML;

}

$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_complaint WHERE c_id > '0'" );

if($row['count']) {

echo <<<HTML
<form action="?mod=complaint" method="post" name="optionsbar2" id="optionsbar2">
<input type="hidden" name="mod" value="complaint">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_complaint_15']}
  </div>
  <div class="table-responsive">

    <table class="table table-xs">
      <thead>
      <tr>
        <th style="width: 180px">{$lang['opt_complaint_3']}</th>
        <th>{$lang['opt_complaint_2']}</th>
		<th style="width: 250px">{$lang['user_action']}</th>
        <th style="width: 40px"><input type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all2()" class="icheck"></td>
      </tr>
      </thead>
	  <tbody>
HTML;



$db->query("SELECT " . PREFIX . "_complaint.id, `c_id`, " . PREFIX . "_complaint.text, `from`, `to`, " . PREFIX . "_complaint.date, " . PREFIX . "_complaint.email, " . PREFIX . "_comments.autor, is_register, post_id, " . PREFIX . "_comments.text as c_text, " . PREFIX . "_post.title, " . PREFIX . "_post.date as newsdate, " . PREFIX . "_post.alt_name, " . PREFIX . "_post.category FROM " . PREFIX . "_complaint LEFT JOIN " . PREFIX . "_comments ON " . PREFIX . "_complaint.c_id=" . PREFIX . "_comments.id LEFT JOIN " . PREFIX . "_post ON " . PREFIX . "_comments.post_id=" . PREFIX . "_post.id WHERE c_id > '0' ORDER BY id DESC");

$entries = "";

while($row = $db->get_row()) {

	$found = true;

	$row['text'] = stripslashes($row['text']);
	if ( $row['date'] ) $date = date( $langformatdatefull, $row['date'] )."<br /><br />"; else $date = "";

	if ($row['c_text']) {

		$row['c_text'] = "<div class=\"quote\">" . stripslashes( $row['c_text'] ) . "</div>";
		$edit_link = "<br /><br /><a class=\"btn bg-primary btn-sm btn-raised\" href=\"?mod=comments&action=edit&id=" . $row['post_id'] ."#comment".$row['c_id']."\" target=\"_blank\"><i class=\"fa fa-pencil-square-o position-left\"></i> {$lang['opt_complaint_12']}</a>";
		$del_c_link = "<br /><br /><a class=\"btn bg-danger btn-sm btn-raised\" href=\"javascript:DeleteComments('{$row['c_id']}')\"><i class=\"fa fa-trash-o position-left\"></i>{$lang['opt_complaint_13']}</a>";

	} else {

		$row['c_text'] = "<div class=\"quote\">" .$lang['opt_complaint_10']. "</div>";
		$edit_link = "";
		$del_c_link = "";
	}

	if ( filter_var( $row['from'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) OR filter_var( $row['from'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
		$from = "IP: ".$row['from'];
		
		if( $row['email'] AND filter_var($row['email'], FILTER_VALIDATE_EMAIL) ) {
			
			$email = htmlspecialchars(filter_var($row['email'], FILTER_SANITIZE_EMAIL), ENT_QUOTES, $config['charset']);
			
			$from .= "<br><br><a class=\"btn bg-brown-600 btn-sm btn-raised\" href=\"mailto:{$row['email']}\">{$lang['send_pm']}</a>";
		}
		
	} else $from = "<a href=\"?mod=editusers&action=edituser&user=".urlencode($row['from'])."\" target=\"_blank\">{$row['from']}</a><br /><br /><a class=\"btn bg-brown-600 btn-sm btn-raised\" href=\"" . $config['http_home_url'] . "index.php?do=pm&doaction=newpm&username=".urlencode($row['from'])."\" target=\"_blank\">{$lang['send_pm']}</a>";

	if($row['is_register'])
		$to = "<a href=\"?mod=editusers&action=edituser&user=".urlencode($row['autor'])."\" target=\"_blank\">{$row['autor']}</a>, <a href=\"" . $config['http_home_url'] . "index.php?do=pm&doaction=newpm&username=".urlencode($row['autor'])."\" target=\"_blank\">{$lang['send_pm']}</a>";
	else $to = $row['autor'];

	$row['category'] = intval( $row['category'] );

	if( $config['allow_alt_url'] ) {
					
		if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
						
			if( $row['category'] and $config['seo_type'] == 2 ) {
							
				$full_link = $config['http_home_url'] . get_url( $row['category'] ) . "/" . $row['post_id'] . "-" . $row['alt_name'] . ".html";
						
			} else {
							
				$full_link = $config['http_home_url'] . $row['post_id'] . "-" . $row['alt_name'] . ".html";
						
			}
					
		} else {
						
			$full_link = $config['http_home_url'] . date( 'Y/m/d/', strtotime ($row['newsdate']) ) . $row['alt_name'] . ".html";
		}
				
	} else {
					
		$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['post_id'];
	
	}

	$full_link = "<a class=\"status-info\" href=\"" . $full_link . "\" target=\"_blank\">" . stripslashes( $row['title'] ) . "</a>";

	$entries .= "<tr>
	<td>{$date}<b>{$from}</b></td>
    <td>{$lang['opt_complaint_7']} {$full_link}<br /><br />{$lang['opt_complaint_8']} <b>{$to}</b><br /><br /><b>{$lang['opt_complaint_9']}</b><br />{$row['c_text']}<b>{$lang['opt_complaint_2']}</b><br />{$row['text']}<br /><br /></td>
    <td><a uid=\"{$row['id']}\" class=\"btn bg-danger btn-sm btn-raised dellink2\" href=\"?mod=complaint\"><i class=\"fa fa-trash-o position-left\"></i>{$lang['opt_complaint_11']}</a>{$edit_link}{$del_c_link}</td>
    <td><input name=\"selected_complaint[]\" value=\"{$row['id']}\" type=\"checkbox\" class=\"icheck\"></td>
    </tr>";

}


echo <<<HTML
		{$entries}
	  </tbody>
	</table>
	
   </div>
	<div class="panel-footer text-right">
		<select class="uniform" name="action"><option value="">{$lang['edit_selact']}</option><option value="mass_delete">{$lang['edit_seldel']}</option></select>&nbsp;<input class="btn bg-slate-600 btn-sm btn-raised" type="submit" value="{$lang['b_start']}">
	</div>	
</div>
</form>
<script>  
<!-- 

	function ckeck_uncheck_all2() {
	    var frm = document.optionsbar2;
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

function DeleteComments(id) {

    DLEconfirm( '{$lang['opt_complaint_13']}?', '{$lang['p_confirm']}', function () {

		ShowLoading('');
	
		$.get("engine/ajax/controller.php?mod=deletecomments", { id: id, dle_allow_hash: '{$dle_login_hash}' }, function(r){
	
			HideLoading('');
	
			r = parseInt(r);
		
			if (!isNaN(r)) {
		
				DLEalert('$lang[opt_complaint_14]', '$lang[p_info]');
				
			}
	
		});

	} );

};
$(function(){

		var tag_name = '';

		$('.dellink2').click(function(){

			id_comp = $(this).attr('uid');

		    DLEconfirm( '{$lang['opt_complaint_5']}', '{$lang['p_confirm']}', function () {

				document.location='?mod=complaint&user_hash={$dle_login_hash}&action=delete&id=' + id_comp + '';

			} );

			return false;
		});
});
//-->
</script>
HTML;

}

$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_complaint WHERE n_id > '0'" );

if($row['count']) {

echo <<<HTML
<form action="?mod=complaint" method="post" name="optionsbar3" id="optionsbar3">
<input type="hidden" name="mod" value="complaint">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_complaint_16']}
  </div>
  <div class="table-responsive">

    <table class="table table-xs">
      <thead>
      <tr>
        <th style="width: 180px">{$lang['opt_complaint_3']}</th>
        <th>{$lang['opt_complaint_2']}</th>
		<th style="width: 250px">{$lang['user_action']}</th>
        <th style="width: 40px"><input type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all3()" class="icheck"></th>
      </tr>
      </thead>
	  <tbody>
HTML;



$db->query("SELECT " . PREFIX . "_complaint.id, `n_id`, " . PREFIX . "_complaint.text, `from`, `to`, " . PREFIX . "_complaint.date, " . PREFIX . "_complaint.email, " . PREFIX . "_post.id as post_id, " . PREFIX . "_post.title, " . PREFIX . "_post.date as newsdate, " . PREFIX . "_post.alt_name, " . PREFIX . "_post.category  FROM " . PREFIX . "_complaint LEFT JOIN " . PREFIX . "_post ON " . PREFIX . "_complaint.n_id=" . PREFIX . "_post.id WHERE n_id > '0' ORDER BY id DESC");


$entries = "";

while($row = $db->get_row()) {

	$found = true;

	$row['text'] = stripslashes($row['text']);
	if ( $row['date'] ) $date = date( $langformatdatefull, $row['date'] )."<br /><br />"; else $date = "";

	if ($row['post_id']) {

		$edit_link = "<br /><br /><a class=\"btn bg-primary btn-sm btn-raised\" href=\"?mod=editnews&amp;action=editnews&amp;id=" . $row['n_id'] ."\" target=\"_blank\"><i class=\"fa fa-pencil-square-o position-left\"></i>{$lang['opt_complaint_18']}</a>";

	} else {

		$edit_link = "";
	}
	
	if ( filter_var( $row['from'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) OR filter_var( $row['from'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
		$from = "IP: ".$row['from'];
		
		if( $row['email'] AND filter_var($row['email'], FILTER_VALIDATE_EMAIL) ) {
			
			$email = htmlspecialchars(filter_var($row['email'], FILTER_SANITIZE_EMAIL), ENT_QUOTES, $config['charset']);
			
			$from .= "<br><br><a class=\"btn bg-brown-600 btn-sm btn-raised\" href=\"mailto:{$row['email']}\">{$lang['send_pm']}</a>";
		}
		
	} else $from = "<a href=\"?mod=editusers&action=edituser&user=".urlencode($row['from'])."\" target=\"_blank\">{$row['from']}</a><br /><br /><a class=\"btn bg-brown-600 btn-sm btn-raised\" href=\"" . $config['http_home_url'] . "index.php?do=pm&doaction=newpm&username=".urlencode($row['from'])."\" target=\"_blank\">{$lang['send_pm']}</a>";

	$row['category'] = intval( $row['category'] );

	if( $config['allow_alt_url'] ) {
					
		if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
						
			if( $row['category'] and $config['seo_type'] == 2 ) {
							
				$full_link = $config['http_home_url'] . get_url( $row['category'] ) . "/" . $row['post_id'] . "-" . $row['alt_name'] . ".html";
						
			} else {
							
				$full_link = $config['http_home_url'] . $row['post_id'] . "-" . $row['alt_name'] . ".html";
						
			}
					
		} else {
						
			$full_link = $config['http_home_url'] . date( 'Y/m/d/', strtotime ($row['newsdate']) ) . $row['alt_name'] . ".html";
		}
				
	} else {
					
		$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['post_id'];
	
	}

	$full_link = "<a class=\"status-info\" href=\"" . $full_link . "\" target=\"_blank\">" . stripslashes( $row['title'] ) . "</a>";

	$entries .= "<tr>
	<td>{$date}<strong>{$from}</strong></td>
    <td>{$lang['opt_complaint_17']} {$full_link}<br /><br /><b>{$lang['opt_complaint_2']}</b><br />{$row['text']}<br /><br /></td>
    <td><a uid=\"{$row['id']}\" class=\"btn bg-danger btn-sm btn-raised dellink3\" href=\"?mod=complaint\"><i class=\"fa fa-trash-o position-left\"></i>{$lang['opt_complaint_11']}</a>{$edit_link}</td>
    <td><input name=\"selected_complaint[]\" value=\"{$row['id']}\" type=\"checkbox\" class=\"icheck\"></td>
    </tr>";

}


echo <<<HTML
		{$entries}
	  </tbody>
	</table>
	
   </div>
	<div class="panel-footer text-right">
		<select class="uniform" name="action"><option value="">{$lang['edit_selact']}</option><option value="mass_delete">{$lang['edit_seldel']}</option></select>&nbsp;<input class="btn bg-slate-600 btn-sm btn-raised" type="submit" value="{$lang['b_start']}">
	</div>	
</div>
</form>
<script>  
<!-- 

	function ckeck_uncheck_all3() {
	    var frm = document.optionsbar3;
	    for (var i=0;i<frm.elements.length;i++) {
	        var elmnt = frm.elements[i];
	        if (elmnt.type=='checkbox') {
	            if(frm.master_box.checked == true){ elmnt.checked=false; $(elmnt).parents('tr').removeClass('warning'); }
	            else{ elmnt.checked=true; $(elmnt).parents('tr').addClass('warning'); }
	        }
	    }
	    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
	    else{ frm.master_box.checked = true; }
		
		$(frm.master_box).parents('tr').removeClass('warning');
		
		$.uniform.update();
		
	}
	
	$(function(){

			var tag_name = '';

			$('.dellink3').click(function(){

				id_comp = $(this).attr('uid');

				DLEconfirm( '{$lang['opt_complaint_5']}', '{$lang['p_confirm']}', function () {

					document.location='?mod=complaint&user_hash={$dle_login_hash}&action=delete&id=' + id_comp + '';

				} );

				return false;
			});
	});
//-->
</script>
HTML;

}

$row = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_complaint WHERE p_id = '0' AND c_id = '0' AND n_id = '0'" );

if($row['count']) {

echo <<<HTML
<form action="?mod=complaint" method="post" name="optionsbar4" id="optionsbar4">
<input type="hidden" name="mod" value="complaint">
<input type="hidden" name="user_hash" value="{$dle_login_hash}">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_complaint_21']}
  </div>
  <div class="table-responsive">

    <table class="table table-xs">
      <thead>
      <tr>
        <th style="width: 180px">{$lang['opt_complaint_3']}</th>
        <th>{$lang['opt_complaint_2']}</th>
		<th style="width: 250px">{$lang['user_action']}</th>
        <th style="width: 40px"><input type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all4()" class="icheck"></td>
      </tr>
      </thead>
	  <tbody>
HTML;



$db->query("SELECT * FROM " . PREFIX . "_complaint WHERE p_id = '0' AND c_id = '0' AND n_id = '0' ORDER BY id DESC");

$entries = "";

while($row = $db->get_row()) {

	$found = true;
	if ( $row['date'] ) $date = date( $langformatdatefull, $row['date'] )."<br /><br />"; else $date = "";

	$row['text'] = stripslashes($row['text']);
	
	if ( filter_var( $row['from'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) OR filter_var( $row['from'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
		$from = "IP: ".$row['from'];
		
		if( $row['email'] AND filter_var($row['email'], FILTER_VALIDATE_EMAIL) ) {
			
			$email = htmlspecialchars(filter_var($row['email'], FILTER_SANITIZE_EMAIL), ENT_QUOTES, $config['charset']);
			
			$from .= "<br><br><a class=\"btn bg-brown-600 btn-sm btn-raised\" href=\"mailto:{$row['email']}\">{$lang['send_pm']}</a>";
		}
		
	} else $from = "<a href=\"?mod=editusers&action=edituser&user=".urlencode($row['from'])."\" target=\"_blank\">{$row['from']}</a><br><br><a class=\"btn bg-brown-600 btn-sm btn-raised\" href=\"" . $config['http_home_url'] . "index.php?do=pm&doaction=newpm&username=".urlencode($row['from'])."\" target=\"_blank\">{$lang['send_pm']}</a>";

	$to = "<a href=\"{$row['to']}\" target=\"_blank\">{$row['to']}</a>";

	$entries .= "<tr>
	<td>{$date}<b>{$from}</b></td>
    <td>{$lang['opt_complaint_22']} <b>{$to}</b><br /><br />{$row['text']}<br /><br /></td>
    <td><a uid=\"{$row['id']}\" class=\"dellink4 btn bg-danger btn-sm btn-raised\" href=\"?mod=complaint\"><i class=\"fa fa-trash-o position-left\"></i>{$lang['opt_complaint_11']}</a></td>
    <td><input name=\"selected_complaint[]\" value=\"{$row['id']}\" type=\"checkbox\" class=\"icheck\"></td>
    </tr>";

}


echo <<<HTML
		{$entries}
	  </tbody>
	</table>
	
   </div>
	<div class="panel-footer text-right">
		<select class="uniform" name="action"><option value="">{$lang['edit_selact']}</option><option value="mass_delete">{$lang['edit_seldel']}</option></select>&nbsp;<input class="btn bg-slate-600 btn-sm btn-raised" type="submit" value="{$lang['b_start']}">
	</div>	
</div>
</form>
<script>  
<!-- 

	function ckeck_uncheck_all4() {
	    var frm = document.optionsbar4;
	    for (var i=0;i<frm.elements.length;i++) {
	        var elmnt = frm.elements[i];
	        if (elmnt.type=='checkbox') {
	            if(frm.master_box.checked == true){ elmnt.checked=false; $(elmnt).parents('tr').removeClass('warning'); }
	            else{ elmnt.checked=true; $(elmnt).parents('tr').addClass('warning'); }
	        }
	    }
	    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
	    else{ frm.master_box.checked = true; }
		
		$(frm.master_box).parents('tr').removeClass('warning');
		
		$.uniform.update();
	
	}
	
	$(function(){

			var tag_name = '';

			$('.dellink4').click(function(){

				id_comp = $(this).attr('uid');

				DLEconfirm( '{$lang['opt_complaint_5']}', '{$lang['p_confirm']}', function () {

					document.location='?mod=complaint&user_hash={$dle_login_hash}&action=delete&id=' + id_comp + '';

				} );

				return false;
			});
	});
//-->
</script>
HTML;

}

if (!$found) {


echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_complaint']}
  </div>
  <div class="panel-body">
		<table width="100%">
		    <tr>
		        <td height="100" class="text-center">{$lang['opt_complaint_19']}</td>
		    </tr>
		</table>
  </div>
  <div class="panel-footer"><div class="col-md-12 text-center"><a class="btn bg-teal btn-sm btn-raised" href="javascript:history.go(-1)">{$lang['func_msg']}</a></div></div>
</div>
HTML;


}

echofooter();
?>