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
 File: email.php
-----------------------------------------------------
 Use: E-mail templates
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

if( $action == "save" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$reg_mail_text = $db->safesql($_POST['reg_mail_text'] );
	$reg_mail_html = intval( $_POST['reg_mail_html'] );
	$feed_mail_text = $db->safesql($_POST['feed_mail_text'] );
	$feed_mail_html = intval( $_POST['feed_mail_html'] );
	$lost_mail_text = $db->safesql($_POST['lost_mail_text'] );
	$lost_mail_html = intval( $_POST['lost_mail_html'] );
	$new_news_text = $db->safesql($_POST['new_news_text'] );
	$new_news_html = intval( $_POST['new_news_html'] );
	$new_comments_text = $db->safesql( $_POST['new_comments_text'] );
	$new_comments_html = intval( $_POST['new_comments_html'] );
	$new_pm_text = $db->safesql($_POST['new_pm_text'] );
	$new_pm_html = intval( $_POST['new_pm_html'] );
	$new_newsletter_text = $db->safesql( $_POST['new_newsletter_text'] );
	$wait_mail_html = intval( $_POST['wait_mail_html'] );
	$wait_mail_text = $db->safesql( $_POST['wait_mail_text'] );
	
	$twofactor_html = intval( $_POST['twofactor_html'] );
	$twofactor_text = $db->safesql( $_POST['twofactor_text'] );
	
	$db->query( "UPDATE " . PREFIX . "_email SET template='$reg_mail_text', use_html='$reg_mail_html' WHERE name='reg_mail'" );
	$db->query( "UPDATE " . PREFIX . "_email SET template='$feed_mail_text', use_html='$feed_mail_html' WHERE name='feed_mail'" );
	$db->query( "UPDATE " . PREFIX . "_email SET template='$lost_mail_text', use_html='$lost_mail_html' WHERE name='lost_mail'" );
	$db->query( "UPDATE " . PREFIX . "_email SET template='$new_news_text', use_html='$new_news_html' WHERE name='new_news'" );
	$db->query( "UPDATE " . PREFIX . "_email SET template='$new_comments_text', use_html='$new_comments_html' WHERE name='comments'" );
	$db->query( "UPDATE " . PREFIX . "_email SET template='$new_pm_text', use_html='$new_pm_html' WHERE name='pm'" );
	$db->query( "UPDATE " . PREFIX . "_email SET template='$new_newsletter_text', use_html='1' WHERE name='newsletter'" );
	$db->query( "UPDATE " . PREFIX . "_email SET template='$wait_mail_text', use_html='$wait_mail_html' WHERE name='wait_mail'" );
	$db->query( "UPDATE " . PREFIX . "_email SET template='$twofactor_text', use_html='$twofactor_html' WHERE name='twofactor'" );
	
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '31', '')" );
	
	msg( "success", $lang['mail_addok'], $lang['mail_addok_1'], "?mod=email" );

} else {
	
	echoheader( "<i class=\"fa fa-envelope-o position-left\"></i><span class=\"text-semibold\">{$lang['opt_email']}</span>", $lang['header_m_1'] );
	
	$db->query( "SELECT * FROM " . PREFIX . "_email" );
	$use_html = array();
	$mail_template = array();

	while ( $row = $db->get_row() ) {
		$mail_template[$row['name']] = htmlspecialchars( stripslashes( $row['template'] ), ENT_QUOTES, $config['charset'] );
		
		if( $row['use_html'] ) $use_html[$row['name']] = 'checked'; else $use_html[$row['name']] = '';
	}
	$db->free();

	echo <<<HTML
<form action="?mod=email&action=save" method="post">
<input type="hidden" name="user_hash" value="$dle_login_hash" />
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['opt_email']}
  </div>
  <div class="panel-body">
	

<div class="accordion" id="accordion">

  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
        {$lang['mail_info']}
      </a>
    </div>
    <div id="collapseOne" class="accordion-body collapse">
      <div class="accordion-inner mt-20">
        {$lang['mail_reg_info']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="reg_mail_text">{$mail_template['reg_mail']}</textarea>
		<div class="checkbox"><label><input class="icheck" type="checkbox" id="reg_mail_html" name="reg_mail_html" value="1" {$use_html['reg_mail']}>{$lang['email_use_html']}</label></div>
      </div>
    </div>
  </div>
  
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
        {$lang['mail_info_1']}
      </a>
    </div>
    <div id="collapseTwo" class="accordion-body collapse">
      <div class="accordion-inner mt-20">
        {$lang['mail_feed_info']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="feed_mail_text">{$mail_template['feed_mail']}</textarea>
		<div class="checkbox"><label><input class="icheck" type="checkbox" id="feed_mail_html" name="feed_mail_html" value="1" {$use_html['feed_mail']}>{$lang['email_use_html']}</label></div>
      </div>
    </div>
  </div>

  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
        {$lang['mail_info_2']}
      </a>
    </div>
    <div id="collapseThree" class="accordion-body collapse">
      <div class="accordion-inner mt-20">
        {$lang['mail_lost_info']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="lost_mail_text">{$mail_template['lost_mail']}</textarea>
		<div class="checkbox"><label><input class="icheck" type="checkbox" id="lost_mail_html" name="lost_mail_html" value="1" {$use_html['lost_mail']}>{$lang['email_use_html']}</label></div>
      </div>
    </div>
  </div>

  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse4">
        {$lang['mail_info_3']}
      </a>
    </div>
    <div id="collapse4" class="accordion-body collapse">
      <div class="accordion-inner mt-20">
        {$lang['mail_news_info']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="new_news_text">{$mail_template['new_news']}</textarea>
		<div class="checkbox"><label><input class="icheck" type="checkbox" id="new_news_html" name="new_news_html" value="1" {$use_html['new_news']}>{$lang['email_use_html']}</label></div>
      </div>
    </div>
  </div>
  
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse5">
        {$lang['mail_info_4']}
      </a>
    </div>
    <div id="collapse5" class="accordion-body collapse">
      <div class="accordion-inner mt-20">
        {$lang['mail_comm_info']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="new_comments_text">{$mail_template['comments']}</textarea>
		<div class="checkbox"><label><input class="icheck" type="checkbox" id="new_comments_html" name="new_comments_html" value="1" {$use_html['comments']}>{$lang['email_use_html']}</label></div>
      </div>
    </div>
  </div>
  
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse6">
        {$lang['mail_info_6']}
      </a>
    </div>
    <div id="collapse6" class="accordion-body collapse">
      <div class="accordion-inner mt-20">
        {$lang['mail_pm_info']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="new_pm_text">{$mail_template['pm']}</textarea>
		<div class="checkbox"><label><input class="icheck" type="checkbox" id="new_pm_html" name="new_pm_html" value="1" {$use_html['pm']}>{$lang['email_use_html']}</label></div>
      </div>
    </div>
  </div>

  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse7">
        {$lang['mail_info_8']}
      </a>
    </div>
    <div id="collapse7" class="accordion-body collapse">
      <div class="accordion-inner mt-20">
        {$lang['mail_wait_info']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="wait_mail_text">{$mail_template['wait_mail']}</textarea>
		<div class="checkbox"><label><input class="icheck" type="checkbox" id="wait_mail_html" name="wait_mail_html" value="1" {$use_html['wait_mail']}>{$lang['email_use_html']}</label></div>
      </div>
    </div>
  </div>

  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse8">
        {$lang['mail_info_9']}
      </a>
    </div>
    <div id="collapse8" class="accordion-body collapse">
      <div class="accordion-inner mt-20">
        {$lang['mail_twofactor_info']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="twofactor_text">{$mail_template['twofactor']}</textarea>
		<div class="checkbox"><label><input class="icheck" type="checkbox" id="twofactor_html" name="twofactor_html" value="1" {$use_html['twofactor']}>{$lang['email_use_html']}</label></div>
      </div>
    </div>
  </div>

  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse9">
        {$lang['mail_info_7']}
      </a>
    </div>
    <div id="collapse9" class="accordion-body collapse">
      <div class="accordion-inner mt-20">
        {$lang['mail_newsletter_info']}<br /><br />
		<textarea class="classic" rows="15" style="width:100%;" name="new_newsletter_text">{$mail_template['newsletter']}</textarea>
      </div>
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
}
?>