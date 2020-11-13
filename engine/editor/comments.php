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
 File: comments.php
-----------------------------------------------------
 Use: WYSIWYG for comments
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( $config['allow_comments_wysiwyg'] == 1 ) {

	if ($user_group[$member_id['user_group']]['allow_url']) $link_icon = "'insertLink', 'dleleech',"; else $link_icon = "";
	
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
		
	$onload_scripts[] = <<<HTML
	
      $('#comments').froalaEditor({
        dle_root: dle_root,
        dle_upload_area : "comments",
        dle_upload_user : "{$p_name}",
        dle_upload_news : "{$p_id}",
        width: '100%',
        height: '220',
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

HTML;

$wysiwyg = <<<HTML
<script>
	var text_upload = "$lang[bb_t_up]";
</script>
<div class="wseditor"><textarea id="comments" name="comments" rows="10" cols="50" class="ajaxwysiwygeditor">{$text}</textarea></div>
HTML;

} else {

	if ($user_group[$member_id['user_group']]['allow_url']) $link_icon = "link dleleech | "; else $link_icon = "";
	if ($user_group[$member_id['user_group']]['allow_image']) {
		if($config['bbimages_in_wysiwyg']) $link_icon .= "dleimage "; else $link_icon .= "image ";
	}
	if ($user_group[$member_id['user_group']]['allow_up_image']) $link_icon .= "dleupload ";
	
	if ($user_group[$member_id['user_group']]['video_comments']) $link_icon .= "dlemp ";
	
	if ($user_group[$member_id['user_group']]['media_comments']) $link_icon .= "dletube ";
	
	$onload_scripts[] = <<<HTML
	
	tinyMCE.baseURL = dle_root + 'engine/editor/jscripts/tiny_mce';
	tinyMCE.suffix = '.min';
	
	tinymce.init({
		selector: 'textarea#comments',
		language : "{$lang['wysiwyg_language']}",
		element_format : 'html',
		width : "100%",
		height : 220,
		plugins: ["link image paste dlebutton"],
		theme: "modern",
		relative_urls : false,
		convert_urls : false,
		remove_script_host : false,
		extended_valid_elements : "div[align|class|style|id|title],b/strong,i/em,u,s",
	    formats: {
	      bold: {inline: 'b'},  
	      italic: {inline: 'i'},
	      underline: {inline: 'u', exact : true},  
	      strikethrough: {inline: 's', exact : true}
	    },
		paste_as_text: true,
		toolbar_items_size: 'small',
		statusbar : false,
		branding: false,
		dle_root : dle_root,
		dle_upload_area : "comments",
		dle_upload_user : "{$p_name}",
		dle_upload_news : "{$p_id}",	
		menubar: false,
		image_dimensions: false,
		toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | {$link_icon}dleemo | bullist numlist | dlequote dlespoiler dlehide",
		content_css : dle_root + "engine/editor/css/content.css"

	});
HTML;

$wysiwyg = <<<HTML
<script>
	var text_upload = "$lang[bb_t_up]";
</script>
    <textarea id="comments" name="comments" style="width:100%;" rows="10">{$text}</textarea>
HTML;


}


if ( $allow_subscribe ) $wysiwyg .= "<br /><label class=\"comments_subscribe\"><input type=\"checkbox\" name=\"allow_subscribe\" id=\"allow_subscribe\" value=\"1\" />" . $lang['c_subscribe'] . "</label><br />";


?>