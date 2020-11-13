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
 File: newsletter.php
-----------------------------------------------------
 Use: WYSIWYG for newsletter
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if($config['bbimages_in_wysiwyg']) {
	$implugin = 'dleimage';
} else $implugin = 'image';

$ed_root = explode ( pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME), $_SERVER['PHP_SELF'] );
$ed_root = reset ( $ed_root );

echo <<<HTML
<script>
$(function(){

	tinyMCE.baseURL = '{$ed_root}engine/editor/jscripts/tiny_mce';
	tinyMCE.suffix = '.min';

	if(dle_theme === null) dle_theme = '';

	tinymce.init({
		selector: 'textarea.wysiwygeditor',
		language : "{$lang['wysiwyg_language']}",
		width : "100%",
		height : "350",
		theme: "modern",
		body_class: dle_theme,
		plugins: ["advlist autolink lists link image charmap anchor searchreplace visualblocks visualchars fullscreen media nonbreaking table contextmenu emoticons paste textcolor colorpicker codemirror spellchecker dlebutton codesample"],
		relative_urls : false,
		convert_urls : false,
		remove_script_host : false,
		extended_valid_elements : "noindex,div[align|class|style|id|title]",
		custom_elements : 'noindex',
		toolbar_items_size: 'small',
		menubar: false,
		toolbar1: "fontselect fontsizeselect | table | link anchor dleleech unlink | {$implugin} dleemo dlemp dletube dlaudio | dletypo dlequote dlespoiler codesample dlebreak dlepage code",
		toolbar2: "undo redo | copy paste pastetext | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | subscript superscript | bullist numlist forecolor backcolor spellchecker removeformat",

		spellchecker_language : "ru",
		spellchecker_languages : "Russian=ru,Ukrainian=uk,English=en",
		spellchecker_rpc_url : "https://speller.yandex.net/services/tinyspell",
		image_caption: true,
		branding: false,
		dle_root : "",
		dle_upload_area : "short_story",
		dle_upload_user : "{$p_name}",
		dle_upload_news : "{$id}",

		content_css : "engine/editor/css/content.css"

	});

});
</script>
    <div class="editor-panel"><textarea id="message" name="message" class="wysiwygeditor" style="width:100%;height:300px;"></textarea></div>
HTML;

?>