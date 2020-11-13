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
 File: replycomments.php
-----------------------------------------------------
 Use: comments reply
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if ( !$config['allow_registration'] ) {
	$dle_login_hash = sha1( SECURE_AUTH_KEY . $_IP );
}

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
	echo $lang['sess_error'];
	die();
}

if( !$user_group[$member_id['user_group']]['allow_addc'] OR !$config['allow_comments'] OR !$config['tree_comments']) {
	echo $lang['reply_error_1'];
	die();
}

$id = intval($_GET['id']);
$indent = intval($_GET['indent']);

if( $id < 1 ) {
	echo $lang['reply_error_2'];
	die();
}

$row = $db->super_query("SELECT id, post_id, autor FROM " . PREFIX . "_comments WHERE id = '{$id}'");

if (!$row['id']) {
	echo $lang['reply_error_2'];
	die();
}

if ( $is_logged AND $user_group[$member_id['user_group']]['disable_comments_captcha'] AND $member_id['comm_num'] >= $user_group[$member_id['user_group']]['disable_comments_captcha'] ) {
		
		$user_group[$member_id['user_group']]['comments_question'] = false;
		$user_group[$member_id['user_group']]['captcha'] = false;
		
}


echo $lang['reply_descr']." <b>".$row['autor']."</b><br />";

echo "<form  method=\"post\" name=\"dle-comments-form-{$id}\" id=\"dle-comments-form-{$id}\">";

if( $is_logged ) echo "<input type=\"hidden\" name=\"name{$id}\" id=\"name{$id}\" value=\"{$member_id['name']}\" /><input type=\"hidden\" name=\"mail{$id}\" id=\"mail{$id}\" value=\"\" />";
else {

	if ( $config['simple_reply'] ) {
		echo <<<HTML
<div style="padding-bottom:5px;">{$lang['reply_name']}&nbsp;&nbsp;<input type="text" name="name{$id}" id="name{$id}" class="commentsreplyname" /></div>
HTML;

	} else {
		
		echo <<<HTML
<div style="float:left;width:50%;padding-right: 10px;box-sizing: border-box;"><input type="text" name="name{$id}" id="name{$id}" class="ui-widget-content ui-corner-all" style="width:100%;" placeholder="{$lang['reply_name']}"></div>
<div style="float:left;width:50%;padding-left: 10px;box-sizing: border-box;"><input type="text" name="mail{$id}" id="mail{$id}" class="ui-widget-content ui-corner-all" style="width:100%;" placeholder="{$lang['reply_mail']}"></div>
<div style="clear:both;padding-bottom:5px;"></div>
HTML;

	}
}

	$p_name = urlencode($member_id['name']);
	$p_id = 0;

	if( $config['allow_comments_wysiwyg'] < 1 OR $config['simple_reply'] ) {
		
		if ( !$config['simple_reply'] ) {
			
			include_once (DLEPlugins::Check(ENGINE_DIR . '/ajax/bbcode.php'));
			
			if ( $config['allow_comments_wysiwyg'] == 0 ) $params = "onfocus=\"setNewField(this.name, document.getElementById( 'dle-comments-form-{$id}' ) )\"";
			else $params = "";
		
		} else $params = "";


	} else {
		
		$params = "class=\"ajaxwysiwygeditor\"";

		if ($config['allow_comments_wysiwyg'] == "1") {	

			if( $user_group[$member_id['user_group']]['allow_url'] ) $link_icon = "'insertLink', 'dleleech',"; else $link_icon = "";
			
			if ($user_group[$member_id['user_group']]['allow_image']) {
				if($config['bbimages_in_wysiwyg']) $link_icon .= "'dleimg',"; else $link_icon .= "'insertImage',";
			}
			
			if ($user_group[$member_id['user_group']]['allow_up_image']) {
				$link_icon .= "'dleupload',";
				$image_upload_params = "imageDefaultWidth: 0,imageUpload: true,imageAllowedTypes: ['jpeg', 'jpg', 'png', 'gif', 'webp'],imageMaxSize: {$user_group[$member_id['user_group']]['up_image_size']} * 1024,imageUploadURL: dle_root + 'engine/ajax/controller.php?mod=upload',imageUploadParam: 'qqfile',imageUploadParams: { 'subaction' : 'upload', 'news_id' : '{$p_id}', 'area' : 'comments', 'author' : '{$p_name}', 'mode' : 'quickload', 'user_hash' : '{$dle_login_hash}' },";
			} else {
				$image_upload_params = "imageUpload: false,";
			}
	
			if ($user_group[$member_id['user_group']]['video_comments']) $link_icon .= "'insertVideo',";
			if ($user_group[$member_id['user_group']]['media_comments']) $link_icon .= "'dlemedia',";
			
		$bb_code = <<<HTML
<script>
	var text_upload = "{$lang['bb_t_up']}";

      $('.ajaxwysiwygeditor').froalaEditor({
        dle_root: dle_root,
        dle_upload_area : "comments",
        dle_upload_user : "{$p_name}",
        dle_upload_news : "{$p_id}",
        width: '100%',
        height: '220',
        zIndex: 9990,
        language: '{$lang['wysiwyg_language']}',

		htmlAllowedTags: ['div', 'span', 'p', 'br', 'strong', 'em', 'ul', 'li', 'ol', 'b', 'u', 'i', 's', 'a', 'img'],
		htmlAllowedAttrs: ['class', 'href', 'alt', 'src', 'style', 'target'],
		pastePlain: true,
        imagePaste: false,
        listAdvancedTypes: false,
        {$image_upload_params}
		videoInsertButtons: ['videoBack', '|', 'videoByURL'],
		
        toolbarButtonsXS: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'align', 'formatOL', 'formatUL', '|', {$link_icon} 'emoticons', '|', 'dlehide', 'dlequote', 'dlespoiler'],

        toolbarButtonsSM: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'align', 'formatOL', 'formatUL', '|', {$link_icon} 'emoticons', '|', 'dlehide', 'dlequote', 'dlespoiler'],

        toolbarButtonsMD: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'align', 'formatOL', 'formatUL', '|', {$link_icon} 'emoticons', '|', 'dlehide', 'dlequote', 'dlespoiler'],

        toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'align', 'formatOL', 'formatUL', '|', {$link_icon} 'emoticons', '|', 'dlehide', 'dlequote', 'dlespoiler']

      }).on('froalaEditor.image.inserted froalaEditor.image.replaced', function (e, editor, \$img, response) {

			if( response ) {
			
			    response = JSON.parse(response);
			  
			    \$img.removeAttr("data-returnbox").removeAttr("data-success").removeAttr("data-xfvalue").removeAttr("data-flink");

				if(response.flink) {
				  if(\$img.parent().hasClass("highslide")) {
		
					\$img.parent().attr('href', response.flink);
		
				  } else {
		
					\$img.wrap( '<a href="'+response.flink+'" class="highslide"></a>' );
					
				  }
				}
			  
			}
			
		});
</script>
HTML;

		} else {

			if( $user_group[$member_id['user_group']]['allow_url'] ) $link_icon = "link dleleech | "; else $link_icon = "";
			
			if ($user_group[$member_id['user_group']]['allow_image']) {
				if($config['bbimages_in_wysiwyg']) $link_icon .= "dleimage "; else $link_icon .= "image ";
			}
			
			if ($user_group[$member_id['user_group']]['allow_up_image']) $link_icon .= "dleupload ";
			if ($user_group[$member_id['user_group']]['video_comments']) $link_icon .= "dlemp ";
			
			if ($user_group[$member_id['user_group']]['media_comments']) $link_icon .= "dletube ";
		$bb_code = <<<HTML

<script>
var text_upload = "{$lang['bb_t_up']}";

setTimeout(function() {

	tinymce.remove('textarea.ajaxwysiwygeditor');

	tinyMCE.baseURL = dle_root + 'engine/editor/jscripts/tiny_mce';
	tinyMCE.suffix = '.min';

	tinymce.init({
		selector: 'textarea.ajaxwysiwygeditor',
		language : "{$lang['wysiwyg_language']}",
		width : "99%",
		height : 180,
		plugins: ["link image paste dlebutton"],
		theme: "modern",
		relative_urls : false,
		convert_urls : false,
		remove_script_host : false,
		branding: false,
		extended_valid_elements : "div[align|class|style|id|title]",
		paste_as_text: true,
		toolbar_items_size: 'small',
		statusbar : false,
		menubar: false,
		dle_root : dle_root,
		image_dimensions: false,
		dle_upload_area : "comments",
		dle_upload_user : "{$p_name}",
		dle_upload_news : "{$p_id}",
		toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | {$link_icon}dleemo | bullist numlist | dlequote dlehide",
		dle_root : "{$config['http_home_url']}",
		content_css : "{$config['http_home_url']}engine/editor/css/content.css"

	});

	$('#dlereplypopup{$id}').dialog( "option", "position", { my: "center", at: "center", of: window } );
}, 100);

</script>
HTML;


		}
	}

echo <<<HTML
<div class="bb-editor">
{$bb_code}
<textarea name="comments{$id}" id="comments{$id}" rows="10" cols="50" {$params}></textarea>
</div>
HTML;

if ($config['allow_subscribe'] AND $user_group[$member_id['user_group']]['allow_subscribe']) {
echo <<<HTML
<div style="padding-top:5px;">
	<label class="comments_subscribe"><input type="checkbox" name="subscribe{$id}" id="subscribe{$id}" value="1">{$lang['c_subscribe']}</label>
</div>
HTML;
}

if( $user_group[$member_id['user_group']]['comments_question'] ) {
	$question = $db->super_query("SELECT id, question FROM " . PREFIX . "_question ORDER BY RAND() LIMIT 1");

	$_SESSION['question'] = $question['id'];

	$question = htmlspecialchars( stripslashes( $question['question'] ), ENT_QUOTES, $config['charset'] );
	
	echo <<<HTML
<div id="dle-question{$id}" style="padding-top:5px;">{$question}</div>
<div><input type="text" name="question_answer{$id}" id="question_answer{$id}" placeholder="{$lang['question_hint']}" class="quick-edit-text"></div>
HTML;

}

if( $user_group[$member_id['user_group']]['captcha'] ) {

	if ( $config['allow_recaptcha'] ) {
		
		if( $config['allow_recaptcha'] == 2) {
			
			echo <<<HTML
	<input type="hidden" name="comments-recaptcha-response{$id}" id="comments-recaptcha-response{$id}" data-key="{$config['recaptcha_public_key']}" value="">
	<script>
	if ( typeof grecaptcha === "undefined"  ) {
	
		$.getScript( "https://www.google.com/recaptcha/api.js?render={$config['recaptcha_public_key']}").done(function () {
		
			grecaptcha.ready(function() {grecaptcha.execute('{$config['recaptcha_public_key']}', {action: 'comments'}).then(function(token) {\$('#comments-recaptcha-response{$id}').val(token);});});
			
		});

    } else {
		grecaptcha.execute('{$config['recaptcha_public_key']}', {action: 'comments'}).then(function(token) {\$('#comments-recaptcha-response{$id}').val(token);});
	}
	</script>
HTML;

		} else {
			
		echo <<<HTML
<div id="dle_recaptcha{$id}" style="padding-top:5px;height:78px;"></div><input type="hidden" name="recaptcha{$id}" id="recaptcha{$id}" value="1" />
<script>
<!--
	var recaptcha_widget;
	
	if ( typeof grecaptcha === "undefined"  ) {
	
		$.getScript( "https://www.google.com/recaptcha/api.js?hl={$lang['wysiwyg_language']}&render=explicit").done(function () {
		
			var setIntervalID = setInterval(function () {
				if (window.grecaptcha) {
					clearInterval(setIntervalID);
					recaptcha_widget = grecaptcha.render('dle_recaptcha{$id}', {'sitekey' : '{$config['recaptcha_public_key']}', 'theme':'{$config['recaptcha_theme']}'});
				};
			}, 300);
		});

    } else {
		recaptcha_widget = grecaptcha.render('dle_recaptcha{$id}', {'sitekey' : '{$config['recaptcha_public_key']}', 'theme':'{$config['recaptcha_theme']}'});
	}
//-->
</script>
HTML;
		}
		
	} else {

		echo <<<HTML
<div style="padding-top:5px;" class="dle-captcha"><a onclick="reload{$id}(); return false;" title="{$lang['reload_code']}" href="#"><span id="dle-captcha{$id}"><img src="{$config['http_home_url']}engine/modules/antibot/antibot.php" alt="{$lang['reload_code']}" width="160" height="80" /></span></a>
<input class="ui-widget-content ui-corner-all sec-code" type="text" name="sec_code{$id}" id="sec_code{$id}" placeholder="{$lang['captcha_hint']}">
</div>
<script>
<!--
function reload{$id} () {

	var rndval = new Date().getTime(); 

	document.getElementById('dle-captcha{$id}').innerHTML = '<img src="{$config['http_home_url']}engine/modules/antibot/antibot.php?rndval=' + rndval + '" width="160" height="80" alt="" />';
	document.getElementById('sec_code{$id}').value = '';
};
//-->
</script>
HTML;

	}
}
	
echo "<input type=\"hidden\" name=\"postid{$id}\" id=\"postid{$id}\" value=\"{$row['post_id']}\" /></form>";

if( $config['simple_reply'] ) {

	echo  <<<HTML
<div class="save-buttons" style="text-align: right;"><input class="bbcodes applychanges" title="{$lang['reply_comments']}" type="button" onclick="ajax_fast_reply('{$id}', '{$indent}'); return false;" value="{$lang['reply_comments_1']}">
<input class="bbcodes cancelchanges" title="$lang[bb_t_cancel]" type="button" onclick="ajax_cancel_reply(); return false;" value="{$lang['bb_b_cancel']}">
</div>
HTML;

	
}

?>