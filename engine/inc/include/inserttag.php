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
 File: inserttag.php
-----------------------------------------------------
 Use: bbcodes
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}


if( $config['emoji'] ) {


$emoji_script = <<<HTML

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
	
	function display_last_emoji(){
	
		var emoji_array = get_emoji();
		var emoji = '';
		var div = '';

		if( $.isArray( emoji_array ) && emoji_array.length ) {
		
			div += '<div class="emoji_category"><b>'+text_last_emoji+'</b></div>';
			
			div += '<div class="emoji_list">';
		
			for (var i=0, len=emoji_array.length;i<len;i++) {
			
				emoji = emojiFromHex(emoji_array[i]);
				
				if(emoji) {
					div += '<div class="emoji_symbol" data-emoji="'+emoji_array[i]+'"><a onclick="insert_emoji(\''+emoji+'\', \''+emoji_array[i]+'\'); return false;">'+emoji+'</a></div>';
				}
				
			}
			
			div += '</div>';
			
			divs = document.getElementsByClassName( 'last_emoji' );
			
			$('.last_emoji').html(div);

			
		}

	}
	

    function insert_emoji(emoji, code) {
		doInsert(' '+emoji, '', false);

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

		display_last_emoji();
		
	}
	
var emoji_loaded = false;

$(function(){

	$('.emoji-button').on('show.bs.dropdown', function () {
	
		display_last_emoji();
	
		if(!emoji_loaded) {
		
			emoji_loaded = true;
			
			$(".emoji-button div[data-emoji]").each(function(){
				var code = $(this).data('emoji');
				var emoji = emojiFromHex($(this).data('emoji'));
		
				if(emoji) {
					$(this).html('<a onclick="insert_emoji(\''+emoji+'\', \''+code+'\'); return false;">'+emoji+'</a>');
				} else {
					$(this).remove();
				}
		
			});
		
		}
		
	});

});
HTML;


$smiles = <<<HTML
<div class="emoji_box"><div class="last_emoji"></div>
HTML;

	$emoji = json_decode (file_get_contents (ROOT_DIR . "/engine/data/emoticons/emoji.json" ) );
	
	foreach ($emoji as $key => $value ) {
		$i = 0;
		
		$smiles .= "<div class=\"emoji_category\"><b>".$lang['emoji_'.$value->category]."</b></div>
		<div class=\"emoji_list\">";
		

		foreach ($value->emoji as $symbol ) {
			$i++;
			
			$smiles .= "<div class=\"emoji_symbol\" data-emoji=\"{$symbol->code}\"></div>";
			
		}

		$smiles .= "</div>";
		
	}
	
$smiles .= "</div>";
	
} else {

	$i = 0;
	$emoji_script = "";
	$smiles = "<table style=\"width:100%;border: 0px;padding: 0px;\"><tr>";
	
	$smilies = explode(",", $config['smilies']);
	foreach($smilies as $smile) {
	
		$i++;
		$smile = trim($smile);
	
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
	
		$smiles .= "<td style=\"padding:5px;text-align: center;\"><a href=\"#\" onclick=\"dle_smiley(':$smile:'); return false;\">{$sm_image}</a></td>";
	
		if ($i%8 == 0) $smiles .= "</tr><tr>";
	
	}
	
	$smiles .= "</tr></table>";
	
}


if ($user_group[$member_id['user_group']]['allow_image_upload'] OR $user_group[$member_id['user_group']]['allow_file_upload'] ) {

      $image_upload = "<button type=\"button\" rel=\"tooltip\" class=\"btn btn-default btn-sm btn-small\" title=\"{$lang['bb_t_up']}\" onclick=\"image_upload(); return false;\"><span class=\"editoricon-folder-open\"></span></button>";

} else $image_upload = "";

if ($mod != "editnews") {
	$row['autor'] = $member_id['name'];
}

$p_name = urlencode($row['autor']);

$image_align = array ();
$image_align[$config['image_align']] = "selected";

$bb_js = <<<HTML
<script>
<!--
var text_enter_url       = "$lang[bb_url]";
var text_enter_size       = "$lang[bb_flash]";
var text_enter_flash       = "$lang[bb_flash_url]";
var text_enter_page      = "$lang[bb_page]";
var text_enter_url_name  = "$lang[bb_url_name]";
var text_enter_tooltip  = "$lang[bb_url_tooltip]";
var text_enter_page_name = "$lang[bb_page_name]";
var text_enter_image    = "$lang[bb_image]";
var text_enter_email    = "$lang[bb_email]";
var text_enter_list     = "$lang[bb_list_item]";
var text_code           = "$lang[bb_code]";
var text_quote          = "$lang[bb_quote]";
var text_url_video      = "$lang[bb_url_video]";
var text_url_poster     = "$lang[bb_url_poster]";
var text_descr          = "$lang[bb_descr]";
var button_insert       = "$lang[button_insert]";
var button_addplaylist  = "$lang[button_addplaylist]";
var text_url_audio      = "$lang[bb_url_audio]";
var text_alt_image      = "$lang[bb_alt_image]";
var error_no_url        = "$lang[bb_no_url]";
var error_no_title      = "$lang[bb_no_title]";
var error_no_email      = "$lang[bb_no_email]";
var prompt_start        = "$lang[bb_prompt_start]";
var img_title   		= "$lang[bb_img_title]";
var img_align  	        = "{$lang['images_align']}";
var img_align_sel  	    = "<select name='dleimagealign' id='dleimagealign' class='uniform'><option value='' {$image_align[0]}>{$lang['opt_sys_no']}</option><option value='left' {$image_align['left']}>{$lang['images_left']}</option><option value='right' {$image_align['right']}>{$lang['images_right']}</option><option value='center' {$image_align['center']}>{$lang['images_center']}</option></select>";
var email_title  	    = "$lang[bb_email_title]";
var dle_prompt          = "$lang[p_prompt]";
var bb_t_emo  	        = "{$lang['bb_t_emo']}";
var bb_t_col  	        = "{$lang['bb_t_col']}";
var text_last_emoji     = "{$lang['emoji_last']}";

var list_open_tag = '';
var list_close_tag = '';
var listitems = '';
var playlist = '';

var selField  = "short_story";

var bbtags   = new Array();

var fombj    = document.forms[0];

function setFieldName(which)
{

   if (which != selField)
   {
       selField = which;

   }
}

function emoticon(theSmilie)
{
	doInsert(" " + theSmilie + " ", "", false);
}

function pagebreak()
{
	doInsert("{PAGEBREAK}", "", false);
}

function simpletag(thetag)
{
	doInsert("[" + thetag + "]", "[/" + thetag + "]", true);
}

function pagelink()
{
	var thesel = get_sel(eval('fombj.'+ selField))

    if (!thesel) {
        thesel = '$lang[bb_bb_page]';
    }

	DLEprompt(text_enter_page, "1", dle_prompt, function (r) {

		var enterURL = r;

		DLEprompt(text_enter_page_name, thesel, dle_prompt, function (r) {

			doInsert("[page="+enterURL+"]"+r+"[/page]", "", false);
	
		});

	});
}

function DLEurlPrompt( d, callback ){

	var b = {};
    var urlvalue = '';
    var urltitle = '';

	if( d.indexOf("http://") != -1 || d.indexOf("https://") != -1 || d.indexOf("ftp://") != -1) {
		urlvalue = d;
		urltitle = '';
	} else {
		urlvalue = 'http://';
		urltitle = d;	
	}

	urltitle = urltitle.replace(/'/g, "&#039;");
	urlvalue = urlvalue.replace(/'/g, "&#039;");

	b[dle_act_lang[3]] = function() { 
					$(this).dialog("close");						
			    };

	b[button_insert] = function() { 
					if ( $("#dle-promt-url").val().length < 1) {
						 $("#dle-promt-url").addClass('ui-state-error');
					} else if ($("#dle-promt-title").val().length < 1) {
						 $("#dle-promt-title").addClass('ui-state-error');
					} else {
						var dleurl = $("#dle-promt-url").val();
						var dleurltitle = $("#dle-promt-title").val();
						var dleurltooltip = $("#dle-promt-tooltip").val();
						$(this).dialog("close");
						$("#dlepopup").remove();
						if( callback ) callback( dleurl, dleurltitle, dleurltooltip);	
					}				
				};

	$("#dlepopup").remove();

	$("body").append("<div id='dlepopup' title='" + dle_prompt + "' style='display:none'>"+ text_enter_url +"<br /><input type='text' name='dle-promt-url' id='dle-promt-url' class='classic' style='width:100%;' value='" + urlvalue + "'/><br /><br />"+ text_enter_url_name +"<br /><input type='text' name='dle-promt-title' id='dle-promt-title' class='classic' style='width:100%;' value='" + urltitle + "'/><br /><br />"+ text_enter_tooltip +"<br /><input type='text' name='dle-promt-tooltip' id='dle-promt-tooltip' class='classic' style='width:100%;' value=''/></div>");

	$('#dlepopup').dialog({
		autoOpen: true,
		width: 500,
		resizable: false,
		buttons: b
	});


	$("#dle-promt-url").select().focus();

};

function tag_url()
{
	var thesel = get_sel(eval('fombj.'+ selField))

    if (!thesel) {
        thesel ='My Webpage';
    }

	DLEurlPrompt(thesel, function (dleurl, dleurltitle, dleurltooltip) {

		if( dleurltooltip.length > 0 ) {
			dleurl = dleurl + '|' + dleurltooltip;
		}
	
		doInsert("[url="+dleurl+"]"+dleurltitle+"[/url]", "", false);

	});
}


function tag_leech()
{
	var thesel = get_sel(eval('fombj.'+ selField))

    if (!thesel) {
        thesel ='My Webpage';
    }

	DLEurlPrompt(thesel, function (dleurl, dleurltitle, dleurltooltip) {
	
		if( dleurltooltip.length > 0 ) {
			dleurl = dleurl + '|' + dleurltooltip;
		}
		
		doInsert("[leech="+dleurl+"]"+dleurltitle+"[/leech]", "", false);

	});
}

function tag_video()
{
	var thesel = get_sel(eval('fombj.'+ selField))

    if (!thesel) {
        thesel ='http://';
    }

	DLEvideoPrompt(thesel, function (url, poster, descr) {
		var videolink = url;

		if (poster != "" || descr != "" ) { 
			videolink += '|' + poster;
		}
		
		if (descr != "" ) { 
			videolink += '|' + descr;
		}
		
		if(videolink != "" && videolink != "http://") {
			playlist += videolink;
		}else if(playlist != "") {
			playlist = playlist.substring(0, playlist.length - 1)
		}
		
		if (playlist != "" ) {
			doInsert("[video="+playlist+"]", "", false);
		}

		playlist = '';
	
	});
};

function DLEvideoPrompt( d, callback ){

	var b = {};

	if( d.indexOf("http://") != -1 || d.indexOf("https://") != -1) {
		urlvalue = d;
	} else {
		urlvalue = 'http://';
	}
	
	b[dle_act_lang[3]] = function() { 
		$(this).dialog("close");
	};

	b[button_addplaylist] = function() { 
		var videourl = $("#dle-promt-url").val();
		var videoposter = $("#dle-promt-poster").val();
		var videodescr = $("#dle-promt-descr").val();
		
		var videolink = videourl;

		if (videoposter != "" || videodescr != "" ) { 
			videolink += '|' + videoposter;
		}
		
		if (videodescr != "" ) { 
			videolink += '|' + videodescr;
		}
		
		if (videolink != "" && videolink != "http://") {
			playlist +=  videolink + ',';
		}
		
		$("#dle-promt-url").val('http://');
		$("#dle-promt-poster").val('');
		$("#dle-promt-descr").val('');

	};
	
	b[button_insert] = function() { 
		var videourl = $("#dle-promt-url").val();
		var videoposter = $("#dle-promt-poster").val();
		var videodescr = $("#dle-promt-descr").val();
		$(this).dialog("close");
		$("#dlepopup").remove();
		if( callback ) callback( videourl, videoposter, videodescr );	
	};

	$("#dlepopup").remove();

	$("body").append("<div id='dlepopup' title='" + dle_prompt + "' style='display:none'>"+ text_url_video +"<br /><input type='text' name='dle-promt-url' id='dle-promt-url' class='classic' style='width:100%;' value='" + urlvalue + "'/><br /><br />"+ text_descr +"<br /><input type='text' name='dle-promt-descr' id='dle-promt-descr' class='classic' style='width:100%;' value=''/><br /><br />"+ text_url_poster +"<br /><input type='text' name='dle-promt-poster' id='dle-promt-poster' class='classic' style='width:100%;' value=''/>");
	
	$('#dlepopup').dialog({
		autoOpen: true,
		width: 500,
		resizable: false,
		buttons: b
	});

	$("#dle-promt-url").select().focus();
};


function tag_audio()
{
	var thesel = get_sel(eval('fombj.'+ selField))

    if (!thesel) {
        thesel ='http://';
    }

	DLEaudioPrompt(thesel, function (url, descr) {
		var audiolink = url;
		
		if (descr != "" ) { 
			audiolink += '|' + descr;
		}
		
		if(audiolink != "" && audiolink != "http://") {
			playlist += audiolink;
		}else if(playlist != "") {
			playlist = playlist.substring(0, playlist.length - 1)
		}
		
		if (playlist != "" ) {
			doInsert("[audio="+playlist+"]", "", false);
		}

		playlist = '';
	
	});
	
}

function DLEaudioPrompt( d, callback ){

	var b = {};

	if( d.indexOf("http://") != -1 || d.indexOf("https://") != -1) {
		urlvalue = d;
	} else {
		urlvalue = 'http://';
	}
	
	b[dle_act_lang[3]] = function() { 
		$(this).dialog("close");
	};

	b[button_addplaylist] = function() { 
		var videourl = $("#dle-promt-url").val();
		var videodescr = $("#dle-promt-descr").val();
		
		var videolink = videourl;
		
		if (videodescr != "" ) { 
			videolink += '|' + videodescr;
		}
		
		if (videolink != "" && videolink != "http://") {
			playlist +=  videolink + ',';
		}
		
		$("#dle-promt-url").val('http://');
		$("#dle-promt-descr").val('');

	};
	
	b[button_insert] = function() { 
		var videourl = $("#dle-promt-url").val();
		var videodescr = $("#dle-promt-descr").val();
		$(this).dialog("close");
		$("#dlepopup").remove();
		if( callback ) callback( videourl, videodescr );	
	};

	$("#dlepopup").remove();

	$("body").append("<div id='dlepopup' title='" + dle_prompt + "' style='display:none'>"+ text_url_audio +"<br /><input type='text' name='dle-promt-url' id='dle-promt-url' class='classic' style='width:100%;' value='" + urlvalue + "'/><br /><br />"+ text_descr +"<br /><input type='text' name='dle-promt-descr' id='dle-promt-descr' class='classic' style='width:100%;' value=''/>");
	
	$('#dlepopup').dialog({
		autoOpen: true,
		width: 500,
		resizable: false,
		buttons: b
	});

	$("#dle-promt-url").select().focus();
};

function tag_youtube()
{
	var thesel = get_sel(eval('fombj.'+ selField))

    if (!thesel) {
        thesel ='http://';
    }

	DLEprompt(text_enter_url, thesel, dle_prompt, function (r) {

		doInsert("[media="+r+"]", "", false);
	
	});
}

function tag_flash()
{
	var thesel = get_sel(eval('fombj.'+ selField))

    if (!thesel) {
        thesel ='http://';
    }

	DLEprompt(text_enter_flash, thesel, dle_prompt, function (r) {

		var enterURL = r;

		DLEprompt(text_enter_size, "425,264", dle_prompt, function (r) {

			doInsert("[flash="+r+"]"+enterURL+"[/flash]", "", false);
	
		});

	});

}

function tag_list(type)
{

	list_open_tag = type == 'ol' ? '[ol=1]\\n' : '[list]\\n';
	list_close_tag = type == 'ol' ? '[/ol]' : '[/list]';
	listitems = '';

	var thesel = get_sel(eval('fombj.'+ selField))

    if (!thesel) {
        thesel ='';
    }

	insert_list( thesel );

}

function insert_list( thesel )
{
	DLEprompt(text_enter_list, thesel, dle_prompt, function (r) {

		if (r != '') {

			listitems += '[*]' + r + '\\n';
			insert_list('');

		} else {

			if( listitems )
			{
				doInsert(list_open_tag + listitems + list_close_tag, "", false);
			}
		}

	}, true);

}

function tag_image()
{

	var thesel = get_sel(eval('fombj.'+ selField));

    if (!thesel) {
        thesel ='http://';
    }

	DLEimagePrompt(thesel, function (imageurl, imagealt, imagealign) {

		var imgoption = "";

		if (imagealt != "") { 

			imgoption = "|"+imagealt;

		}

		if (imagealign != "" && imagealign != "center") { 

			imgoption = imagealign+imgoption;

		}

		if (imgoption != "" ) {

			imgoption = "="+imgoption;

		}

		if (imagealign == "center") {
			doInsert("[center][img"+imgoption+"]"+imageurl+"[/img][/center]", "", false);
		}
		else {
			doInsert("[img"+imgoption+"]"+imageurl+"[/img]", "", false);
		}

	});
};

function DLEimagePrompt( d, callback ){

	var b = {};
    var urlvalue = '';
    var urltitle = '';

	if( d.indexOf("http://") != -1 || d.indexOf("https://") != -1 ) {
		urlvalue = d;
		urltitle = '';
	} else {
		urlvalue = 'http://';
		urltitle = d;	
	}

	urltitle = urltitle.replace(/'/g, "&#039;");
	urlvalue = urlvalue.replace(/'/g, "&#039;");

	b[dle_act_lang[3]] = function() { 
					$(this).dialog("close");						
			    };

	b[button_insert] = function() { 
					if ( $("#dle-promt-text").val().length < 1) {
						 $("#dle-promt-text").addClass('ui-state-error');
					} else {
						var imageurl = $("#dle-promt-text").val();
						var imagealt = $("#dle-image-alt").val();
						var imagealign = $("#dleimagealign").val();
						$(this).dialog("close");
						$("#dlepopup").remove();
						if( callback ) callback( imageurl, imagealt, imagealign );	
					}				
				};

	$("#dlepopup").remove();

	$("body").append("<div id='dlepopup' title='" + dle_prompt + "' style='display:none'>"+ text_enter_image +"<br /><input type='text' name='dle-promt-text' id='dle-promt-text' class='classic' style='width:100%;' value='" + urlvalue + "'/><br /><br />"+ text_alt_image +"<br /><input type='text' name='dle-image-alt' id='dle-image-alt' class='classic' style='width:100%;' value='" + urltitle + "'><br><br>"+img_align+"&nbsp;"+img_align_sel+"</div>");

	$('#dlepopup').dialog({
		autoOpen: true,
		width: 500,
		resizable: false,
		buttons: b
	});

	if (d.length > 0) {
		$("#dle-promt-text").select().focus();
	} else {
		$("#dle-promt-text").focus();
	}
};

function tag_email()
{
	var thesel = get_sel(eval('fombj.'+ selField))
		
	if (!thesel) {
		   thesel ='';
	}

	DLEprompt(text_enter_email, thesel, dle_prompt, function (r) {

		doInsert("[email="+r+"]"+r+"[/email]", "", false);

	});
}

function doInsert(ibTag, ibClsTag, isSingle) {
	var isClose = false;
	var obj_ta = eval('fombj.'+ selField);
	obj_ta.focus();

 	if ( obj_ta.selectionEnd != null ) { 
		var ss = obj_ta.selectionStart;
		var st = obj_ta.scrollTop;
		var es = obj_ta.selectionEnd;
		
		var start  = (obj_ta.value).substring(0, ss);
		var middle = (obj_ta.value).substring(ss, es);
		var end    = (obj_ta.value).substring(es, obj_ta.textLength);
		var left_indent = 0;

		if(!isSingle) {
			middle = "";
		} else {
			if(ibClsTag != "" && middle == "" ) {
				left_indent = ibClsTag.length;
			}
		}
		
		if (obj_ta.selectionEnd - obj_ta.selectionStart > 0)
		{
			middle = ibTag + middle + ibClsTag;
		}
		else
		{
			middle = ibTag + middle + ibClsTag;
		}
		
		obj_ta.value = start + middle + end;
		
		var cpos = ss + (middle.length) - left_indent;
		
		obj_ta.selectionStart = cpos;
		obj_ta.selectionEnd   = cpos;
		obj_ta.scrollTop      = st;


	} else {
		obj_ta.value += ibTag + ibClsTag;
	}

	return isClose;
}

function setColor(color)
{
	doInsert("[color=" +color+ "]", "[/color]", true );
}

function dle_smiley ( text ){
	doInsert(' ' + text + ' ', '', false);
};
function image_upload()
{

	media_upload ( selField, '{$p_name}', '{$id}', 'no');

}
function insert_font(value, tag)
{
    if (value == 0)
    {
    	return;
	}
	
	doInsert("[" +tag+ "=" +value+ "]", "[/" +tag+ "]", true );


}

function insert_header(value) {
	
	doInsert("[h" +value+ "]", "[/h" +value+ "]", true );


};

function tag_typograf() {

		$('#' + selField).val(dletp.execute(document.getElementById( selField ).value));

}

function get_sel(obj)
{

 if (document.selection) 
 {

   var s = document.selection.createRange(); 
   if (s.text)
   {
	 return s.text;
   }
 }
 else if (typeof(obj.selectionStart)=="number")
 {
   if (obj.selectionStart!=obj.selectionEnd)
   {
     var start = obj.selectionStart;
     var end = obj.selectionEnd;
	 return (obj.value.substr(start,end-start));
   }
 }

 return false;

};

$(function(){
	$( ".color-btn" ).click(function() {
	  setColor( $(this).data('value') );
	});
})
{$emoji_script}
-->
</script>
HTML;

$bb_panel = <<<HTML
<div class="bbcodes-editor">
	<div class="btn-group more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_b']}" onclick="simpletag('b'); return false;"><span class="editoricon-bold"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_i']}" onclick="simpletag('i'); return false;"><span class="editoricon-italic"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_u']}" onclick="simpletag('u'); return false;"><span class="editoricon-underline"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_s']}" onclick="simpletag('s'); return false;"><span class="editoricon-strikethrough"></span></button>
	</div>
	<div class="btn-group more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_sub']}" onclick="simpletag('sub'); return false;"><span class="editoricon-subscript"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_sup']}" onclick="simpletag('sup'); return false;"><span class="editoricon-superscript"></span></button>
	</div>
	<div class="btn-group more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_b_img']}" onclick="tag_image(); return false;"><span class="editoricon-image"></span></button>
		{$image_upload}
	</div>
	<div class="btn-group more-size single emoji-button">
		<button type="button" data-toggle="dropdown" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_emo']}"><span class="editoricon-smile-o"></span></button>
		<ul class="dropdown-menu text-left" style="width:390px;max-height:300px;overflow-y:auto;overflow-x:hidden;">
			<li>{$smiles}</li>
		</ul>
	</div>
	<div class="btn-group more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_url']}" onclick="tag_url(); return false;"><span class="editoricon-chain"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_leech']}" onclick="tag_leech(); return false;"><span class="editoricon-key"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_m']}" onclick="tag_email(); return false;"><span class="editoricon-envelope-o"></span></button>
	</div>
	<div class="btn-group more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_video']}" onclick="tag_video(); return false;"><span class="editoricon-film"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_audio']}" onclick="tag_audio(); return false;"><span class="editoricon-music"></span></button>
	</div>
	<div class="btn-group more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_hide']}" onclick="simpletag('hide'); return false;"><span class="editoricon-eye-blocked"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_quote']}" onclick="simpletag('quote'); return false;"><span class="editoricon-quotes-left"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_code']}" onclick="simpletag('code'); return false;"><span class="editoricon-code"></span></button>
	</div>
	<div style="clear:both;"></div>
	<div class="btn-group dropb more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" data-toggle="dropdown" title="{$lang['bb_t_header']}"><span class="editoricon-header"></span><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a onclick="javascript:insert_header('1'); return(false);" href="#"><h1>{$lang['bb_header']} 1</h1></a></li>
				<li><a onclick="javascript:insert_header('2'); return(false);" href="#"><h2>{$lang['bb_header']} 2</h2></a></li>
				<li><a onclick="javascript:insert_header('3'); return(false);" href="#"><h3>{$lang['bb_header']} 3</h3></a></li>
				<li><a onclick="javascript:insert_header('4'); return(false);" href="#"><h4>{$lang['bb_header']} 4</h4></a></li>
				<li><a onclick="javascript:insert_header('5'); return(false);" href="#"><h5>{$lang['bb_header']} 5</h5></a></li>
				<li><a onclick="javascript:insert_header('6'); return(false);" href="#"><h6>{$lang['bb_header']} 6</h6></a></li>
			</ul>
	</div>
	<div class="btn-group dropb more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" data-toggle="dropdown" title="{$lang['bb_t_font']}"><span class="editoricon-font"></span><span class="caret"></span></button>
			<ul class="dropdown-menu text-left">
				<li><a onclick="javascript:insert_font('Arial', 'font'); return(false);" href="#" style="font-family:Arial">Arial</a></li>
				<li><a onclick="javascript:insert_font('Arial Black', 'font'); return(false);" href="#" style="font-family:Arial Black">Arial Black</a></li>
				<li><a onclick="javascript:insert_font('Century Gothic', 'font'); return(false);" href="#" style="font-family:Century Gothic">Century Gothic</a></li>
				<li><a onclick="javascript:insert_font('Courier New', 'font'); return(false);" href="#" style="font-family:Courier New">Courier New</a></li>
				<li><a onclick="javascript:insert_font('Georgia', 'font'); return(false);" href="#" style="font-family:Georgia">Georgia</a></li>
				<li><a onclick="javascript:insert_font('Impact', 'font'); return(false);" href="#" style="font-family:Impact">Impact</a></li>
				<li><a onclick="javascript:insert_font('System', 'font'); return(false);" href="#" style="font-family:System">System</a></li>
				<li><a onclick="javascript:insert_font('Tahoma', 'font'); return(false);" href="#" style="font-family:Tahoma">Tahoma</a></li>
				<li><a onclick="javascript:insert_font('Times New Roman', 'font'); return(false);" href="#" style="font-family:Times New Roman">Times New Roman</a></li>
				<li><a onclick="javascript:insert_font('Verdana', 'font'); return(false);" href="#" style="font-family:Verdana">Verdana</a></li>
			</ul>
	</div>
	<div class="btn-group dropb more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" data-toggle="dropdown" title="{$lang['bb_t_size']}"><span class="editoricon-text-height"></span><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a onclick="javascript:insert_font('1', 'size'); return(false);" href="#" style="font-size:8pt;">1</a></li>
				<li><a onclick="javascript:insert_font('2', 'size'); return(false);" href="#" style="font-size:10pt;">2</a></li>
				<li><a onclick="javascript:insert_font('3', 'size'); return(false);" href="#" style="font-size:12pt;">3</a></li>
				<li><a onclick="javascript:insert_font('4', 'size'); return(false);" href="#" style="font-size:14pt;">4</a></li>
				<li><a onclick="javascript:insert_font('5', 'size'); return(false);" href="#" style="font-size:18pt;">5</a></li>
				<li><a onclick="javascript:insert_font('6', 'size'); return(false);" href="#" style="font-size:24pt;">6</a></li>
				<li><a onclick="javascript:insert_font('7', 'size'); return(false);" href="#" style="font-size:36pt;">7</a></li>
			</ul>
	</div>
	<div class="btn-group more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_l']}" onclick="simpletag('left'); return false;"><span class="editoricon-align-left"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_c']}" onclick="simpletag('center'); return false;"><span class="editoricon-align-center"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_r']}" onclick="simpletag('right'); return false;"><span class="editoricon-align-right"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_j']}" onclick="simpletag('justify'); return false;"><span class="editoricon-align-justify"></span></button>
	</div>
	<div class="btn-group more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_list1']}" onclick="tag_list('list'); return false;"><span class="editoricon-list-ul"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_list2']}" onclick="tag_list('ol'); return false;"><span class="editoricon-list-ol"></span></button>
	</div>
	<div class="btn-group single">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" data-toggle="dropdown" title="{$lang['bb_t_color']}"><span class="editoricon-brush"></span><span class="caret"></span></button>
			<ul class="dropdown-menu" style="min-width: 150px !important;">
				<li>
					<div class="color-palette"><div><button type="button" class="color-btn" style="background-color:#000000;" data-value="#000000"></button><button type="button" class="color-btn" style="background-color:#424242;" data-value="#424242"></button><button type="button" class="color-btn" style="background-color:#636363;" data-value="#636363"></button><button type="button" class="color-btn" style="background-color:#9C9C94;" data-value="#9C9C94"></button><button type="button" class="color-btn" style="background-color:#CEC6CE;" data-value="#CEC6CE"></button><button type="button" class="color-btn" style="background-color:#EFEFEF;" data-value="#EFEFEF"></button><button type="button" class="color-btn" style="background-color:#F7F7F7;" data-value="#F7F7F7"></button><button type="button" class="color-btn" style="background-color:#FFFFFF;" data-value="#FFFFFF"></button></div><div><button type="button" class="color-btn" style="background-color:#FF0000;" data-value="#FF0000"></button><button type="button" class="color-btn" style="background-color:#FF9C00;" data-value="#FF9C00"></button><button type="button" class="color-btn" style="background-color:#FFFF00;"  data-value="#FFFF00"></button><button type="button" class="color-btn" style="background-color:#00FF00;"  data-value="#00FF00"></button><button type="button" class="color-btn" style="background-color:#00FFFF;"  data-value="#00FFFF" ></button><button type="button" class="color-btn" style="background-color:#0000FF;"  data-value="#0000FF" ></button><button type="button" class="color-btn" style="background-color:#9C00FF;"  data-value="#9C00FF" ></button><button type="button" class="color-btn" style="background-color:#FF00FF;"  data-value="#FF00FF" ></button></div><div><button type="button" class="color-btn" style="background-color:#F7C6CE;"  data-value="#F7C6CE" ></button><button type="button" class="color-btn" style="background-color:#FFE7CE;"  data-value="#FFE7CE" ></button><button type="button" class="color-btn" style="background-color:#FFEFC6;"  data-value="#FFEFC6" ></button><button type="button" class="color-btn" style="background-color:#D6EFD6;"  data-value="#D6EFD6" ></button><button type="button" class="color-btn" style="background-color:#CEDEE7;"  data-value="#CEDEE7" ></button><button type="button" class="color-btn" style="background-color:#CEE7F7;"  data-value="#CEE7F7" ></button><button type="button" class="color-btn" style="background-color:#D6D6E7;"  data-value="#D6D6E7" ></button><button type="button" class="color-btn" style="background-color:#E7D6DE;"  data-value="#E7D6DE" ></button></div><div><button type="button" class="color-btn" style="background-color:#E79C9C;"  data-value="#E79C9C" ></button><button type="button" class="color-btn" style="background-color:#FFC69C;"  data-value="#FFC69C" ></button><button type="button" class="color-btn" style="background-color:#FFE79C;"  data-value="#FFE79C" ></button><button type="button" class="color-btn" style="background-color:#B5D6A5;"  data-value="#B5D6A5" ></button><button type="button" class="color-btn" style="background-color:#A5C6CE;"  data-value="#A5C6CE" ></button><button type="button" class="color-btn" style="background-color:#9CC6EF;"  data-value="#9CC6EF" ></button><button type="button" class="color-btn" style="background-color:#B5A5D6;"  data-value="#B5A5D6" ></button><button type="button" class="color-btn" style="background-color:#D6A5BD;"  data-value="#D6A5BD" ></button></div><div><button type="button" class="color-btn" style="background-color:#E76363;"  data-value="#E76363" ></button><button type="button" class="color-btn" style="background-color:#F7AD6B;"  data-value="#F7AD6B" ></button><button type="button" class="color-btn" style="background-color:#FFD663;"  data-value="#FFD663" ></button><button type="button" class="color-btn" style="background-color:#94BD7B;"  data-value="#94BD7B" ></button><button type="button" class="color-btn" style="background-color:#73A5AD;"  data-value="#73A5AD" ></button><button type="button" class="color-btn" style="background-color:#6BADDE;"  data-value="#6BADDE" ></button><button type="button" class="color-btn" style="background-color:#8C7BC6;"  data-value="#8C7BC6" ></button><button type="button" class="color-btn" style="background-color:#C67BA5;"  data-value="#C67BA5" ></button></div><div><button type="button" class="color-btn" style="background-color:#CE0000;"  data-value="#CE0000" ></button><button type="button" class="color-btn" style="background-color:#E79439;"  data-value="#E79439" ></button><button type="button" class="color-btn" style="background-color:#EFC631;"  data-value="#EFC631" ></button><button type="button" class="color-btn" style="background-color:#6BA54A;"  data-value="#6BA54A" ></button><button type="button" class="color-btn" style="background-color:#4A7B8C;"  data-value="#4A7B8C" ></button><button type="button" class="color-btn" style="background-color:#3984C6;"  data-value="#3984C6" ></button><button type="button" class="color-btn" style="background-color:#634AA5;"  data-value="#634AA5" ></button><button type="button" class="color-btn" style="background-color:#A54A7B;"  data-value="#A54A7B" ></button></div><div><button type="button" class="color-btn" style="background-color:#9C0000;"  data-value="#9C0000" ></button><button type="button" class="color-btn" style="background-color:#B56308;"  data-value="#B56308" ></button><button type="button" class="color-btn" style="background-color:#BD9400;"  data-value="#BD9400" ></button><button type="button" class="color-btn" style="background-color:#397B21;"  data-value="#397B21" ></button><button type="button" class="color-btn" style="background-color:#104A5A;"  data-value="#104A5A" ></button><button type="button" class="color-btn" style="background-color:#085294;"  data-value="#085294" ></button><button type="button" class="color-btn" style="background-color:#311873;"  data-value="#311873" ></button><button type="button" class="color-btn" style="background-color:#731842;"  data-value="#731842" ></button></div><div><button type="button" class="color-btn" style="background-color:#630000;"  data-value="#630000" ></button><button type="button" class="color-btn" style="background-color:#7B3900;"  data-value="#7B3900" ></button><button type="button" class="color-btn" style="background-color:#846300;"  data-value="#846300" ></button><button type="button" class="color-btn" style="background-color:#295218;"  data-value="#295218" ></button><button type="button" class="color-btn" style="background-color:#083139;"  data-value="#083139" ></button><button type="button" class="color-btn" style="background-color:#003163;"  data-value="#003163" ></button><button type="button" class="color-btn" style="background-color:#21104A;"  data-value="#21104A" ></button><button type="button" class="color-btn" style="background-color:#4A1031;"  data-value="#4A1031" ></button></div></div>				
				</li>
			</ul>
	</div>
	<div class="btn-group more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_youtube']}" onclick="tag_youtube(); return false;"><span class="editoricon-youtube-square"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_t']}" onclick="tag_typograf(); return false;"><span class="editoricon-font-size"></span></button>
	</div>
	<div class="btn-group more-size single">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_spoiler']}" onclick="simpletag('spoiler'); return false;"><span class="editoricon-read-more"></span></button>
	</div>
	<div class="btn-group more-size">
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_br']}" onclick="pagebreak(); return false;"><span class="editoricon-page-break"></span></button>
		<button type="button" class="btn btn-default btn-sm btn-small" rel="tooltip" title="{$lang['bb_t_p']}" onclick="pagelink(); return false;"><span class="editoricon-insert-template"></span></button>
	</div>
</div>
HTML;

$bb_code = $bb_js.$bb_panel;
?>