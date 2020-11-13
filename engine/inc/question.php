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
 File: question.php
-----------------------------------------------------
 Use: Configuring questions and answers
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( $member_id['user_group'] != 1 ) {
	msg( "error", $lang['addnews_denied'], $lang['db_denied'] );
}

if ($_POST['action'] == "addquestion") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$question = $db->safesql( strip_tags($_POST['question']) );
	$answer = $db->safesql( strip_tags(str_replace( "\r", "", $_POST['answer'] )) );
	
	$db->query( "INSERT INTO " . PREFIX . "_question (question, answer) VALUES ('{$question}', '{$answer}')" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '84', '".htmlspecialchars($question, ENT_QUOTES, $config['charset'])."')" );

	header( "Location: ?mod=question" ); die();
}

if ($_POST['action'] == "editquestion") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$id = intval($_POST['id']);
	$question = $db->safesql( strip_tags($_POST['question']) );
	$answer = $db->safesql( strip_tags(str_replace( "\r", "", $_POST['answer'] )) );
	
	$db->query( "UPDATE " . PREFIX . "_question SET question='{$question}', answer='{$answer}' WHERE id='{$id}'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '83', '".htmlspecialchars($question, ENT_QUOTES, $config['charset'])."')" );


	header( "Location: ?mod=question" ); die();
}


if ($_GET['action'] == "delete") {

	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	$id = intval($_GET['id']);

	$db->query( "DELETE FROM " . PREFIX . "_question WHERE id = '{$id}'" );
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '85', '{$id}')" );

	header( "Location: ?mod=question" ); die();
}

echoheader("<i class=\"fa fa-lightbulb-o position-left\"></i><span class=\"text-semibold\">{$lang['header_q_1']}</span>", $lang['header_q_2']);

echo <<<HTML
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_question']}
  </div>
HTML;

$db->query("SELECT * FROM " . PREFIX . "_question ORDER BY id DESC");

$entries = "";

while($row = $db->get_row()){

		$row['question'] = htmlspecialchars( stripslashes($row['question']), ENT_QUOTES, $config['charset'] );
		$row['answer'] = htmlspecialchars( stripslashes($row['answer']), ENT_QUOTES, $config['charset'] );

		$entries .= "<tr>
        <td align=\"center\" style=\"width:100px;\"><a uid=\"{$row['id']}\" class=\"editlink\" href=\"?mod=question\"><i title=\"{$lang['word_ledit']}\" alt=\"{$lang['word_ledit']}\" class=\"fa fa-pencil-square-o position-left\"></i></a><a uid=\"{$row['id']}\" class=\"dellink\" href=\"?mod=question\"><i title=\"{$lang['word_ldel']}\" alt=\"{$lang['word_ldel']}\" class=\"fa fa-trash-o position-right text-danger\"></i></a></td>
        <td><div id=\"question_{$row['id']}\">{$row['question']}</div><div id=\"answer_{$row['id']}\" style=\"display:none\">{$row['answer']}</div></td>
        </tr>";

}

$result_count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_question");

if ($result_count['count']) {

echo <<<HTML
<div class="table-responsive">
<table class="table table-xs table-hover">
      <thead>
      <tr>
        <th style="width: 100px;">&nbsp;</th>
        <th>{$lang['q_list']}</th>
      </tr>
      </thead>
	{$entries}
</table>
HTML;


} else {

echo <<<HTML
<div class="panel-body">
<table style="width:100%">
    <tr>
        <td><div align="center"><br /><br />{$lang['opt_question_1']}<br /><br /><br /></div></td>
    </tr>
</table>
</div>
HTML;

}

echo <<<HTML
	<div class="panel-footer">
	<button name="btn-new" id="btn-new" type="submit" class="btn bg-teal btn-sm btn-raised"><i class="fa fa-plus-circle position-left"></i>{$lang['btn_question']}</button>
	</div>
</div>
<script>  
<!--
$(function(){

		$('#btn-new').click(function(){

			var b = {};
			b[dle_act_lang[3]] = function() { 
				$(this).dialog('close');						
			};

			b['{$lang['user_save']}'] = function() { 
				if ( $('#question').val().length < 1 || $('#answer').val().length < 1) {
					if ( $('#question').val().length < 1) { $('#question').addClass('ui-state-error'); }
					if ( $('#answer').val().length < 1) { $('#answer').addClass('ui-state-error'); }
				} else {
					document.saveform.submit();
		
				}				
			};

			$('#dlepopup').remove();
							
			$('body').append("<div id='dlepopup' title='{$lang['opt_question_2']}' style='display:none'><form action=\"?mod=question\" method=\"POST\" name=\"saveform\" id=\"saveform\">{$lang['opt_question_3']}<br><input type='text' name='question' id='question' class='classic' style='width:100%' value=''/><br><br>{$lang['opt_question_4']}<br /><textarea name='answer' id='answer' class='classic' style='width:100%;height:100px;'></textarea><input type=\"hidden\" name=\"mod\" value=\"question\"><input type=\"hidden\" name=\"user_hash\" value=\"{$dle_login_hash}\"><input type=\"hidden\" name=\"action\" value=\"addquestion\"></form></div>");
							
			$('#dlepopup').dialog({
				autoOpen: true,
				width: 500,
				resizable: false,
				buttons: b
			});

		});

		$('.editlink').click(function(){

			var id = $(this).attr('uid');
			var qa = $('#question_'+id).html();
			var ans = $('#answer_'+id).html();

			var b = {};
			b[dle_act_lang[3]] = function() { 
				$(this).dialog('close');						
			};
	
			b['{$lang['user_save']}'] = function() { 
				if ( $('#question').val().length < 1 || $('#answer').val().length < 1) {
					if ( $('#question').val().length < 1) { $('#question').addClass('ui-state-error'); }
					if ( $('#answer').val().length < 1) { $('#answer').addClass('ui-state-error'); }
				} else {
					document.saveform.submit();
		
				}				
			};
	
			$('#dlepopup').remove();
							
			$('body').append("<div id='dlepopup' title='{$lang['opt_question_2']}' style='display:none'><form action=\"?mod=question\" method=\"POST\" name=\"saveform\" id=\"saveform\">{$lang['opt_question_3']}<br><input type='text' name='question' id='question' class='classic' style='width:100%' value=\""+qa+"\"/><br><br>{$lang['opt_question_4']}<br /><textarea name='answer' id='answer' class='classic' style='width:100%;height:100px;'>"+ans+"</textarea><input type=\"hidden\" name=\"mod\" value=\"question\"><input type=\"hidden\" name=\"user_hash\" value=\"{$dle_login_hash}\"><input type=\"hidden\" name=\"action\" value=\"editquestion\"><input type=\"hidden\" name=\"id\" value=\""+id+"\"></form></div>");
							
			$('#dlepopup').dialog({
				autoOpen: true,
				width: 500,
				buttons: b,
				resizable: false,
				open: function(event, ui) { 
					$("#question").val(qa);
				}
			});

			return false;
		});

		$('.dellink').click(function(){

			var id = $(this).attr('uid');
			var qa = $('#question_'+id).html();

		    DLEconfirm( '{$lang['opt_question_5']} <b>&laquo;'+qa+'&raquo;</b>', '{$lang['p_confirm']}', function () {

				document.location='?mod=question&user_hash={$dle_login_hash}&action=delete&id=' + id + '';

			} );

			return false;
		});
});
//-->
</script>
HTML;
echofooter();
?>