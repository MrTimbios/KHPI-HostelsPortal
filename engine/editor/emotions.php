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
 File: emotions.php
-----------------------------------------------------
 Use: Smiles for WYSIWYG
=====================================================
*/
define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR', '..');

error_reporting(7);
ini_set('display_errors', true);
ini_set('html_errors', false);

include ENGINE_DIR.'/data/config.php';
include ROOT_DIR . '/language/' . $config['langs'] . '/website.lng';

date_default_timezone_set ( $config['date_adjust'] );

if ($config['http_home_url'] == "") {

	$config['http_home_url'] = explode("engine/editor/emotions.php", $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

}

if( $config['emoji'] ) {

$emoji_script = <<<HTML
	var text_last_emoji = "{$lang['emoji_last']}";
	
	display_editor_last_emoji();
			
	$(".dle-emoticon div[data-emoji]").each(function(){
		var code = $(this).data('emoji');
		var emoji = emojiFromHex($(this).data('emoji'));
	
		if(emoji) {
			$(this).html('<a onclick="insert_editor_emoji(\''+emoji+'\', \''+code+'\'); return false;">'+emoji+'</a>');
		} else {
			$(this).remove();
		}
	
	});
HTML;


$output = <<<HTML
<div class="emoji_box"><div class="last_emoji"></div>
HTML;

	$emoji = json_decode (file_get_contents (ROOT_DIR . "/engine/data/emoticons/emoji.json" ) );
	
	foreach ($emoji as $key => $value ) {
		$i = 0;
		
		$output .= "<div class=\"emoji_category\"><b>".$lang['emoji_'.$value->category]."</b></div>
		<div class=\"emoji_list\">";
		

		foreach ($value->emoji as $symbol ) {
			$i++;
			
			$output .= "<div class=\"emoji_symbol\" data-emoji=\"{$symbol->code}\"></div>";
			
		}

		$output .= "</div>";
		
	}
	
$output .= "</div>";
	
} else {
	
	$i = 0;
	$emoji_script = "";
	$output = "<table style=\"width:100%;border: 0px;padding: 0px;\"><tr>";

	$smilies = explode(",", $config['smilies']);
	$count_smilies = count($smilies);
	
	foreach($smilies as $smile)
	{
		$i++;
		$smile = trim($smile);
		$sm_image ="";
		if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . ".png" ) ) {
			if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . "@2x.png" ) ) {
				$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.png\" srcset=\"{$config['http_home_url']}engine/data/emoticons/{$smile}@2x.png 2x\" />";
			} else {
				$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.png\" />";	
			}
		} elseif ( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . ".gif" ) ) {
			if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . "@2x.gif" ) ) {
				$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.gif\" srcset=\"{$config['http_home_url']}engine/data/emoticons/{$smile}@2x.gif 2x\" />";
			} else {
				$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.gif\" />";	
			}
		}
		
		$output .= "<td style=\"padding:5px;text-align: center;\" align=\"center\"><a href=\"#\" onclick=\"dle_smiley(':$smile:'); return false;\" ontouchstart=\"dle_smiley(':$smile:'); return false;\">{$sm_image}</a></td>";
		if ($i%7 == 0 AND $i < $count_smilies) $output .= "</tr><tr>";
	
	}

	$output .= "</tr></table>";

}

echo <<<HTML
{$output}
<script>
<!--
    function dle_smiley(finalImage) {
		active_editor.emoticons.insert(finalImage);
	}
{$emoji_script}
-->
</script>
HTML;
?>