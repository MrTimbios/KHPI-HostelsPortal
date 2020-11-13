<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004,2018 SoftNews Media Group
=====================================================
 This code is protected by copyright
=====================================================
 File: comments.php
-----------------------------------------------------
 Use: WYSIWYG for comments
=====================================================
*/
define('DATALIFEENGINE', true);
define('ROOT_DIR', '../../../../../..');
define('ENGINE_DIR', '../../../../..');

error_reporting(7);
ini_set('display_errors', true);
ini_set('html_errors', false);

include ENGINE_DIR.'/data/config.php';
include ROOT_DIR . '/language/' . $config['langs'] . '/website.lng';

if ($config['http_home_url'] == "") {

	$config['http_home_url'] = explode("engine/editor/jscripts/tiny_mce/plugins/emoticons/emotions.php", $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

}

if( $config['emoji'] ) {

$emoji_script = <<<HTML
	var text_last_emoji = "{$lang['emoji_last']}";

function emojiFromHex(hex) {
	try {
	
		if ( navigator.platform.indexOf('Win') > -1 && hex.match( /^1F1(E[6-9A-F]|F[0-9A-F])/ ) ) {
			return null;
		}
		
		var decimals = [];
		var hexPoints = hex.split('-');
		for ( var p = 0; p < hexPoints.length; p++ ) {
			decimals.push( parseInt( hexPoints[p], 16 ) );
		}

		return String.fromCodePoint.apply( null, decimals );
	} catch ( err ) {
		return null;
	}
}
	
function get_emoji() {
	try {
           return JSON.parse(localStorage.getItem('last_emoji'));
        } catch (e) {
            return null;
        }
}

function set_emoji(value) {
    try {
        localStorage.setItem('last_emoji', JSON.stringify(value));
    } catch (e) {
    }
}
	
function in_array(needle, haystack){
	for (var i=0, len=haystack.length;i<len;i++) {
		if (haystack[i] == needle) return true;
	}
	return false;
}
	
function display_editor_last_emoji(){
	
	var emoji_array = get_emoji();
	var emoji = '';
	var div = '';

	if( $.isArray( emoji_array ) && emoji_array.length ) {
	
		div += '<div class="emoji_category"><b>'+text_last_emoji+'</b></div>';
		
		div += '<div class="emoji_list">';
	
		for (var i=0, len=emoji_array.length;i<len;i++) {
		
			emoji = emojiFromHex(emoji_array[i]);
			
			if(emoji) {
				div += '<div class="emoji_symbol" data-emoji="'+emoji_array[i]+'"><a onclick="insert_editor_emoji(\''+emoji+'\', \''+emoji_array[i]+'\'); return false;">'+emoji+'</a></div>';
			}
			
		}
		
		div += '</div>';
		
		divs = document.getElementsByClassName( 'last_emoji' );
		
		$('.last_emoji').html(div);

		
	}

}
	

function insert_editor_emoji(emoji, code) {

	parent.tinyMCE.execCommand('mceInsertContent',false,' <span class="native-emoji">'+emoji+'</span> ');
		
	var emoji_array = get_emoji();

	if( $.isArray( emoji_array ) ) {

		if( !in_array( code, emoji_array ) ) {

			if(emoji_array.length > 15 ) {
				emoji_array.pop();
			}
			
			emoji_array.unshift(code);
			
		}
		
	} else {
		
		emoji_array = [];
		emoji_array.push(code);
		
	}
	
	set_emoji(emoji_array);

	display_editor_last_emoji();
	
	parent.tinyMCE.activeEditor.windowManager.close();
	
}

$(function(){	
			
	$(".emoji_box div[data-emoji]").each(function(){
		var code = $(this).data('emoji');
		var emoji = emojiFromHex($(this).data('emoji'));
	
		if(emoji) {
			$(this).html('<a onclick="insert_editor_emoji(\''+emoji+'\', \''+code+'\'); return false;">'+emoji+'</a>');
		} else {
			$(this).remove();
		}
	
	});
	
	display_editor_last_emoji();
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
	
	$emoji_script = "";
	$i = 0;
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
		
		$output .= "<td style=\"padding:5px;text-align: center;\"><a href=\"#\" onclick=\"dle_smiley(':$smile:'); return false;\">{$sm_image}</a></td>";
		if ($i%7 == 0 AND $i < $count_smilies) $output .= "</tr><tr>";
	
	}

	$output .= "</tr></table>";
}

echo <<<HTML
<style>
	.emoji_box {
		width:100%;
		max-width: 390px;
	}
	.emoji_category {
		padding:7px;
		clear:both;
	}
	.emoji_list {
		margin-top:5px;
		margin-bottom:5px;
		width:100%;
		font-family:'Apple Color Emoji', 'Segoe UI Emoji', 'NotoColorEmoji', 'Segoe UI Symbol', 'Android Emoji', 'EmojiSymbols';
		font-size:1.5em;
	}
	.emoji_symbol {
		float:left;
		margin-bottom: 10px;
		width:12.5%;
		text-align:center;
	}
	
	.emoji_symbol a,  .emoji_symbol a:hover {
		cursor: pointer;
		text-decoration:none;
	}

	body {
		overflow-x:hidden;
		overflow-y:auto;
   }
</style>
<script src="{$config['http_home_url']}engine/classes/js/jquery.js"></script>
{$output}
<script>
<!--
    function dle_smiley(finalImage) {

		parent.tinyMCE.execCommand('mceInsertContent',false,finalImage);
		parent.tinyMCE.activeEditor.windowManager.close();


	}
{$emoji_script}
-->
</script>
HTML;
?>