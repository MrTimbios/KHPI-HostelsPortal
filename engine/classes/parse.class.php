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
 File: parse.class.phpf
-----------------------------------------------------
 Use: Text Parser
=====================================================
*/

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/htmlpurifier/HTMLPurifier.standalone.php'));

class ParseFilter {

	var $video_config = array ();
	var $code_text = array ();
	var $code_count = 0;
	var $image_count = 0;
	var $codes_param = array ();
	var $wysiwyg = false;
	var $safe_mode = false;
	var $allow_code = true;
	var $leech_mode = false;
	var $disable_leech = false;
	var $filter_mode = true;
	var $allow_url = true;
	var $allow_image = true;
	var $allow_video = true;
	var $allow_media = true;
	var $edit_mode = true;
	var $allowbbcodes = true;
	var $not_allowed_tags = false;
	var $not_allowed_text = false;
	var $remove_html = true;
	var $is_comments = false;
	var $allowed_domains = array("vkontakte.ru", "ok.ru", "vk.com", "youtube.com", "maps.google.ru", "maps.google.com", "player.vimeo.com", "facebook.com", "web.facebook.com", "dailymotion.com", "bing.com", "ustream.tv", "w.soundcloud.com", "coveritlive.com", "video.yandex.ru", "player.rutv.ru", "promodj.com", "rutube.ru", "skydrive.live.com", "docs.google.com", "api.video.mail.ru", "megogo.net", "mapsengine.google.com", "google.com", "videoapi.my.mail.ru", "coub.com", "music.yandex.ru", "rasp.yandex.ru", "mixcloud.com", "yandex.ru", "my.mail.ru", "icloud.com", "codepen.io");	

	var $font_sizes = array (1 => '8', 2 => '10', 3 => '12', 4 => '14', 5 => '18', 6 => '24', 7 => '36' );
	var $allowed_fonts = array ("Arial", "Arial Black", "Century Gothic", "Courier New", "Georgia", "Impact", "System", "Tahoma", "Times New Roman", "Verdana");
	
	var $htmlparser = false;
	
	protected $media_providers = false;
	
	function __construct($tagsArray = array()) {
		global $config;
		
		if (function_exists('mb_internal_encoding')) {
           mb_internal_encoding($config['charset']);
        }

		$parse_config = HTMLPurifier_Config::createDefault();
		$parse_config->set('Core.Encoding', $config['charset']);
		$parse_config->set('Core.AllowParseManyTags', true);
		$parse_config->set('HTML.Doctype', 'HTML 4.01 Transitional');
		$parse_config->set('CSS.MaxImgLength', null);

		$parse_config->set('Cache.SerializerPath', ENGINE_DIR.'/cache/system');

		$parse_config->set('AutoFormat.RemoveEmpty', true);

		$parse_config->set('URI.SafeIframeRegexp', '%^(http:|https:)?//(www.)?('.implode("/|", $this->allowed_domains).')%');
		
		$parse_config->set('HTML.DefinitionID', 'html5-definitions');
		$parse_config->set('HTML.DefinitionRev', 1);

		$parse_config->set('Attr.DefaultImageAlt', '' );
		$parse_config->set('Attr.AllowedFrameTargets', array("_blank") );
		$parse_config->set('Attr.AllowedRel', array("highslide", "external" , "noopener" , "noreferrer", "nofollow", "sponsored", "ugc") );
		$parse_config->set('URI.AllowedSchemes', array('http' => true, 'https' => true, 'mailto' => true, 'ftp' => true, 'nntp' => true, 'news' => true, 'tel' => true,'magnet' => true) );
		$parse_config->set('Attr.EnableID', true);
		$parse_config->set('Attr.ID.HTML5', true);
		$parse_config->set('HTML.FlashAllowFullScreen', true);
		$parse_config->set('HTML.MaxImgLength', null);
		$parse_config->set('HTML.TargetNoreferrer', false);
		$parse_config->set('AutoFormat.RemoveEmpty.Predicate', array ('colgroup' => array(),'th' => array(),'td' => array(),'div' => array(),'p' => array(), 'i' => array() , 'video' => array(), 'audio' => array(), 'iframe' => array(0 => 'src') ));

		if ( count($tagsArray) ) {
			
			for($i = 0; $i < count( $tagsArray ); $i ++) {
				$tagsArray[$i] = strtolower( $tagsArray[$i] );
			}

			$parse_config->set('HTML.Allowed', implode(",",$tagsArray) );
			$parse_config->set('Attr.AllowedClasses', array("quote", "title_quote", "highslide", "fr-dib", "fr-dii", "fr-fir", "fr-draggable", "fr-fil", "fr-rounded", "fr-padded", "fr-bordered", "fr-shadows", "fr-strong", "fr-text-red", "fr-text-blue", "fr-text-green", "native-emoji") );
			$parse_config->set('CSS.AllowTricky', true);
			$parse_config->set('CSS.AllowedProperties', array("text-align", "width", "height", "margin-right", "margin-left", "display", "float") );
			$this->is_comments = true;
			
		} else {
			
			$parse_config->set('CSS.AllowTricky', true);
			$parse_config->set('CSS.Proprietary', true);
			$parse_config->set('HTML.SafeEmbed', true);
			$parse_config->set('HTML.SafeObject', true);
			$parse_config->set('Output.FlashCompat', true);
			$parse_config->set('HTML.SafeIframe', true);
			
		}
		
		if ($def = $parse_config->maybeGetRawHTMLDefinition()) {

			$def->addElement('section', 'Block', 'Flow', 'Common');
			$def->addElement('noindex', 'Block', 'Flow', 'Common');
			$def->addElement('nav',     'Block', 'Flow', 'Common');
			$def->addElement('article', 'Block', 'Flow', 'Common');
			$def->addElement('aside',   'Block', 'Flow', 'Common');
			$def->addElement('header',  'Block', 'Flow', 'Common');
			$def->addElement('footer',  'Block', 'Flow', 'Common');
			$def->addElement('summary',  'Block', 'Flow', 'Common');
			$def->addElement('datalist', 'Block', 'Flow', 'Common' );
			$def->addElement('rp', 'Block', 'Flow', 'Common' );
			$def->addElement('rt', 'Block', 'Flow', 'Common' );
			$def->addElement('ruby', 'Block', 'Flow', 'Common' );
			$def->addElement('address', 'Block', 'Flow', 'Common');
			$def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');

			$def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
			$def->addElement('figcaption', 'Inline', 'Flow', 'Common');

			$def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
			  'src' => 'URI',
			  'type' => 'Text',
			  'id' => 'Text',
			  'width' => 'Length',
			  'height' => 'Length',
			  'poster' => 'URI',
			  'preload' => 'Enum#auto,metadata,none',
			  'controls' => 'Bool',
			  'autoplay' => 'Bool',
			  'loop' => 'Bool',
			  'muted' => 'Bool',
			  'playsinline' => 'Bool',
			));
			
			$def->addElement('audio', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
			  'src' => 'URI',
			  'type' => 'Text',
			  'id' => 'Text',
			  'width' => 'Length',
			  'height' => 'Length',
			  'preload' => 'Enum#auto,metadata,none',
			  'controls' => 'Bool',
			  'autoplay' => 'Bool',
			  'loop' => 'Bool',
			  'muted' => 'Bool',
			));
			
			$def->addElement( 'track', 'Inline', 'Empty', 'Common', array(
			  'kind' => 'Enum#captions,chapters,descriptions,metadata,subtitle',
			  'src' => 'URI',
			  'srclang' => 'Text',
			  'label' => 'Text',
			  'default' => 'Bool',
			) );

			$def->addElement('source', 'Inline', 'Empty', 'Common', array(
			  'src' => 'URI',
			  'type' => 'Text',
			  'srcset' => 'Text',
			  'sizes' => 'Text',
			  'media' => 'Text',
			));
			
			$def->addElement('canvas', 'Block', 'Flow', 'Common', array(
			  'width' => 'Length',
			  'label' => 'Text',
			) );
			
			$def->addElement('details', 'Block', 'Flow', 'Common', array(
			  'open' => 'Bool',
			) );
			
			$def->addElement('picture', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
			  'srcset' => 'Text',
			  'sizes' => 'Text',
			  'media' => 'Text',
			  'type' => 'Text',
			) );
			
			$def->addElement('map', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
			  'id' => 'Text',
			  'name' => 'Text',
			) );
			
			$def->addElement('area', 'Inline', 'Empty', 'Common', array(
			  'alt' => 'Text',
			  'coords' => 'Text',
			  'shape' => 'Enum#default,rect,circle,poly',
			  'href' => 'URI',
			  'target' => 'Enum#_self,_blank,_top,_parent',
			));
			
	        $time = $def->addElement('time', 'Inline', 'Inline', 'Common', array('datetime' => 'Text', 'pubdate' => 'Bool'));
	        $time->excludes = array('time' => true);
		
			$def->addElement('s',    'Inline', 'Inline', 'Common');
			$def->addElement('var',  'Inline', 'Inline', 'Common');
			$def->addElement('sub',  'Inline', 'Inline', 'Common');
			$def->addElement('sup',  'Inline', 'Inline', 'Common');
			$def->addElement('mark', 'Inline', 'Inline', 'Common');
			$def->addElement('wbr',  'Inline', 'Empty', 'Core');
			$def->addElement('a', 'Flow', 'Flow', 'Common', array('href' => 'URI', 'download' => 'Bool','rel' => new HTMLPurifier_AttrDef_HTML_LinkTypes('rel'),'rev' => new HTMLPurifier_AttrDef_HTML_LinkTypes('rev')));

			$def->addElement('ins', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
			$def->addElement('del', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
			$def->addElement('progress', 'Inline', 'Flow', 'Common', array('max' => 'Number', 'value' => 'CDATA'));


			$def->addAttribute('img', 'data-maxwidth', 'Number');
			$def->addAttribute('img', 'contenteditable', 'Enum#true,false');
			$def->addAttribute('img', 'usemap', 'Text');
			$def->addAttribute('span', 'contenteditable', 'Enum#true,false');

			$def->addAttribute('iframe', 'allowfullscreen', 'Bool');
			$def->addAttribute('img', 'srcset', 'Text');
			$def->addAttribute('img', 'sizes', 'Text' );
			$def->addAttribute('table', 'height', 'Text');
			$def->addAttribute('td', 'border', 'Text');
			$def->addAttribute('th', 'border', 'Text');
			$def->addAttribute('tr', 'width', 'Text');
			$def->addAttribute('tr', 'height', 'Text');
			$def->addAttribute('tr', 'border', 'Text');

		}
		
 		$this->htmlparser = new HTMLPurifier($parse_config);
		$this->media_providers = new OEmbed();
		
	}
	function process($source) {

		$source = $this->decode( $source );
		
		$source = preg_replace( "/javascript:/i", "j&#1072;vascript:", $source );
		$source = preg_replace( "/data:/i", "d&#1072;ta:", $source );
		$source = str_replace( "__CODEAMP__", "&", $source );

		$source = $this->htmlparser->purify($source);
	
		$source = str_ireplace( "{include", "&#123;include", $source );
		$source = str_ireplace( "{content", "&#123;content", $source );
		$source = str_ireplace( "{custom", "&#123;custom", $source );
		$source = str_ireplace( "{THEME}", "&#123;THEME}", $source );
		$source = str_ireplace( "{newsnavigation", "&#123;newsnavigation", $source );
		$source = str_replace( "slideshowGroup:", "slideshowGroup&#58;", $source );
		$source = str_replace(array("_&#123;_", "_&#91;_"), array("_{_", "_[_"), $source);

		if ( $this->safe_mode AND !$this->wysiwyg AND $this->edit_mode ) {
			$source = str_replace( '"', '&quot;', $source );
			$source = str_replace( "'", '&#039;', $source );
		}
		
		$source = preg_replace_callback( "#<a(.+?)>(.*?)</a>#is", array( &$this, 'remove_bad_url'), $source );
		$source = str_ireplace( "<p></p>", "<p><br></p>", $source );
		
		if( $this->code_count ) {
			foreach ( $this->code_text as $key_find => $key_replace ) {
				$find[] = $key_find;
				$replace[] = $key_replace;
			}

			$source = str_replace( $find, $replace, $source );
		}

		$this->code_count = 0;
		$this->code_text = array ();

		$source = str_replace( "<?", "&lt;?", $source );
		$source = str_replace( "?>", "?&gt;", $source );

		$source = addslashes( $source );
		return $source;

	}
	
	function decode($source) {
		global $config;

		if( $this->allow_code AND $this->allowbbcodes) {
			$source = preg_replace_callback( "#\[code\](.+?)\[/code\]#is",  array( &$this, 'code_tag'), $source );
		}
		
		$source = str_replace("&#8203;", '', $source);

		if ( $this->safe_mode ) {
			
			if( $this->remove_html ) {
				
				$source = htmlspecialchars( strip_tags($source), ENT_QUOTES, $config['charset'] );
				
			} elseif( !$this->wysiwyg AND $this->edit_mode ) {
				
				$source = htmlspecialchars( $source, ENT_QUOTES, $config['charset'] );
				
			}
			
		} else {
			
			$source = preg_replace_callback( "#<pre class=['\"]language-markup['\"]><code>(.+?)</code></pre>#is",  array( &$this, 'clear_code'), $source );
			
		}

		return $source;
	}


	function BB_Parse($source, $use_html = TRUE) {
		global $config, $lang;

		if( $this->allowbbcodes) $source = preg_replace_callback( "#\[code\](.+?)\[/code\]#is",  array( &$this, 'hide_code_tag'), $source );
			
		$find = array ('/data:/i','/about:/i','/vbscript:/i','/onclick/i','/onload/i','/onunload/i','/onabort/i','/onerror/i','/onblur/i','/onchange/i','/onfocus/i','/onreset/i','/onsubmit/i','/ondblclick/i','/onkeydown/i','/onkeypress/i','/onkeyup/i','/onmousedown/i','/onmouseup/i','/onmouseover/i','/onmouseout/i','/onselect/i','/javascript/i','/onmouseenter/i','/onwheel/i','/onshow/i','/onafterprint/i','/onbeforeprint/i','/onbeforeunload/i','/onhashchange/i','/onmessage/i','/ononline/i','/onoffline/i','/onpagehide/i','/onpageshow/i','/onpopstate/i','/onresize/i','/onstorage/i','/oncontextmenu/i','/oninvalid/i','/oninput/i','/onsearch/i','/ondrag/i','/ondragend/i','/ondragenter/i','/ondragleave/i','/ondragover/i','/ondragstart/i','/ondrop/i','/onmousemove/i','/onmousewheel/i','/onscroll/i','/oncopy/i','/oncut/i','/onpaste/i','/oncanplay/i','/oncanplaythrough/i','/oncuechange/i','/ondurationchange/i','/onemptied/i','/onended/i','/onloadeddata/i','/onloadedmetadata/i','/onloadstart/i','/onpause/i','/onprogress/i',	'/onratechange/i','/onseeked/i','/onseeking/i','/onstalled/i','/onsuspend/i','/ontimeupdate/i','/onvolumechange/i','/onwaiting/i','/ontoggle/i');
		$replace = array ("d&#1072;ta:", "&#1072;bout:", "vbscript<b></b>:", "&#111;nclick", "&#111;nload", "&#111;nunload", "&#111;nabort", "&#111;nerror", "&#111;nblur", "&#111;nchange", "&#111;nfocus", "&#111;nreset", "&#111;nsubmit", "&#111;ndblclick", "&#111;nkeydown", "&#111;nkeypress", "&#111;nkeyup", "&#111;nmousedown", "&#111;nmouseup", "&#111;nmouseover", "&#111;nmouseout", "&#111;nselect", "j&#1072;vascript", '&#111;nmouseenter', '&#111;nwheel', '&#111;nshow', '&#111;nafterprint','&#111;nbeforeprint','&#111;nbeforeunload','&#111;nhashchange','&#111;nmessage','&#111;nonline','&#111;noffline','&#111;npagehide','&#111;npageshow','&#111;npopstate','&#111;nresize','&#111;nstorage','&#111;ncontextmenu','&#111;ninvalid','&#111;ninput','&#111;nsearch','&#111;ndrag','&#111;ndragend','&#111;ndragenter','&#111;ndragleave','&#111;ndragover','&#111;ndragstart','&#111;ndrop','&#111;nmousemove','&#111;nmousewheel','&#111;nscroll','&#111;ncopy','&#111;ncut','&#111;npaste','&#111;ncanplay','&#111;ncanplaythrough','&#111;ncuechange','&#111;ndurationchange','&#111;nemptied','&#111;nended','&#111;nloadeddata','&#111;nloadedmetadata','&#111;nloadstart','&#111;npause','&#111;nprogress',	'&#111;nratechange','&#111;nseeked','&#111;nseeking','&#111;nstalled','&#111;nsuspend','&#111;ntimeupdate','&#111;nvolumechange','&#111;nwaiting','&#111;ntoggle');

		if( $use_html == false ) {
			$find[] = "'\r'";
			$replace[] = "";
			$find[] = "'\n'";
			$replace[] = "<br>";
		} else {
			$source = str_replace( "\r\n\r\n", "\n", $source );
		}

		$smilies_arr = explode( ",", $config['smilies'] );
		
		foreach ( $smilies_arr as $smile ) {
			
			$smile = trim( $smile );
			$sm_image ="";
			
			if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . ".png" ) ) {
				if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . "@2x.png" ) ) {
					$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.png\" srcset=\"{$config['http_home_url']}engine/data/emoticons/{$smile}@2x.png 2x\">";
				} else {
					$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.png\">";	
				}
			} elseif ( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . ".gif" ) ) {
				if( file_exists( ROOT_DIR . "/engine/data/emoticons/" . $smile . "@2x.gif" ) ) {
					$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.gif\" srcset=\"{$config['http_home_url']}engine/data/emoticons/{$smile}@2x.gif 2x\">";
				} else {
					$sm_image = "<img alt=\"{$smile}\" class=\"emoji\" src=\"{$config['http_home_url']}engine/data/emoticons/{$smile}.gif\">";	
				}
			}
			
			if( $sm_image ) {
				
				$find[] = "':$smile:'";
				$replace[] = "<!--smile:{$smile}-->{$sm_image}<!--/smile-->";

			}
		}

		if( $this->filter_mode ) $source = $this->word_filter( $source );

		$source = preg_replace( $find, $replace, $source );

		$source = str_replace( "`", "&#96;", $source );
		$source = str_ireplace( "{comments}", "&#123;comments}", $source );
		$source = str_ireplace( "{addcomments}", "&#123;addcomments}", $source );
		$source = str_ireplace( "{newsnavigation}", "&#123;newsnavigation}", $source );
		$source = str_ireplace( "[declination", "&#91;declination", $source );

		$source = str_replace( "<?", "&lt;?", $source );
		$source = str_replace( "?>", "?&gt;", $source );

		if ($config['parse_links'] AND $this->allowbbcodes) {
			$source = preg_replace("#(^|\s|>)((http|https|ftp)://\w+[^\s\[\]\<]+)#i", '\\1[url]\\2[/url]', $source);
		}

		$count_start = substr_count ($source, "[quote");
		$count_end = substr_count ($source, "[/quote]");

		if ($count_start AND $count_start == $count_end) {
			$source = str_ireplace( "[quote=]", "[quote]", $source );

			if ( !$this->allow_code ) {
				$source = preg_replace_callback( "#\[(quote)\](.+?)\[/quote\]#is", array( &$this, 'clear_div_tag'), $source );
				$source = preg_replace_callback( "#\[(quote)=(.+?)\](.+?)\[/quote\]#is", array( &$this, 'clear_div_tag'), $source );
			}

			while( preg_match( "#\[quote\](.+?)\[/quote\]#is", $source ) ) {
				$source = preg_replace( "#\[quote\](.+?)\[/quote\]#is", "<!--QuoteBegin--><div class=\"quote\"><!--QuoteEBegin-->\\1<!--QuoteEnd--></div><!--QuoteEEnd-->", $source );
			}
			
			while( preg_match( "#\[quote=([^\]|\[|<]+)\](.+?)\[/quote\]#is", $source ) ) {
				$source = preg_replace( "#\[quote=([^\]|\[|<]+)\](.+?)\[/quote\]#is", "<!--QuoteBegin \\1 --><div class=\"title_quote\">{$lang['i_quote']} \\1</div><div class=\"quote\"><!--QuoteEBegin-->\\2<!--QuoteEnd--></div><!--QuoteEEnd-->", $source );
			}
		}
	
		if ( $this->allowbbcodes ) {
			
			$count_start = substr_count ($source, "[spoiler");
			$count_end = substr_count ($source, "[/spoiler]");
	
			if ($count_start AND $count_start == $count_end) {
				$source = str_ireplace( "[spoiler=]", "[spoiler]", $source );
	
				if ( !$this->allow_code ) {
					$source = preg_replace_callback( "#\[(spoiler)\](.+?)\[/spoiler\]#is", array( &$this, 'clear_div_tag'), $source );
					$source = preg_replace_callback( "#\[(spoiler)=(.+?)\](.+?)\[/spoiler\]#is", array( &$this, 'clear_div_tag'), $source );
				}
				while( preg_match( "#\[spoiler\](.+?)\[/spoiler\]#is", $source ) ) {
					$source = preg_replace_callback( "#\[spoiler\](.+?)\[/spoiler\]#is", array( &$this, 'build_spoiler'), $source );
				}
				
				while( preg_match( "#\[spoiler=([^\]|\[|<]+)\](.+?)\[/spoiler\]#is", $source ) ) {
					$source = preg_replace_callback( "#\[spoiler=([^\]|\[|<]+)\](.+?)\[/spoiler\]#is", array( &$this, 'build_spoiler'), $source);
				}
	
			}
	
			$source = preg_replace( "#\[(left|right|center|justify)\](.+?)\[/\\1\]#is", "<div style=\"text-align:\\1;\">\\2</div>", $source );
	
			while( preg_match( "#\[(b|i|s|u|sub|sup)\](.+?)\[/\\1\]#is", $source ) ) {
				$source = preg_replace( "#\[(b|i|s|u|sub|sup)\](.+?)\[/\\1\]#is", "<\\1>\\2</\\1>", $source );
			}
			
			if( $this->allow_url ) {
	
				$source = preg_replace_callback( "#\[(url)\](\S.+?)\[/url\]#i", array( &$this, 'build_url'), $source );
				$source = preg_replace_callback( "#\[(url)\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/url\]#i", array( &$this, 'build_url'), $source );
				$source = preg_replace_callback( "#\[(url)\s*=\s*(\S.+?)\s*\](.*?)\[\/url\]#i", array( &$this, 'build_url'), $source );
	
				$source = preg_replace_callback( "#\[(leech)\](\S.+?)\[/leech\]#i", array( &$this, 'build_url'), $source );
				$source = preg_replace_callback( "#\[(leech)\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/leech\]#i", array( &$this, 'build_url'), $source );
				$source = preg_replace_callback( "#\[(leech)\s*=\s*(\S.+?)\s*\](.*?)\[\/leech\]#i", array( &$this, 'build_url'), $source );
	
			} else {
	
				if( stristr( $source, "[url" ) !== false ) $this->not_allowed_tags = true;
				if( stristr( $source, "[leech" ) !== false ) $this->not_allowed_tags = true;
				if( stristr( $source, "&lt;a" ) !== false ) $this->not_allowed_tags = true;
	
			}
	
			if( $this->allow_image ) {
	
				$source = preg_replace_callback( "#\[img\](.+?)\[/img\]#i", array( &$this, 'build_image'), $source );
				$source = preg_replace_callback( "#\[img=(.+?)\](.+?)\[/img\]#i", array( &$this, 'build_image'), $source );
				$source = preg_replace_callback( "'\[thumb\](.+?)\[/thumb\]'i", array( &$this, 'build_thumb'), $source );
				$source = preg_replace_callback( "'\[thumb=(.+?)\](.+?)\[/thumb\]'i", array( &$this, 'build_thumb'), $source );
	
			} else {
	
				if( stristr( $source, "[img" ) !== false OR stristr( $source, "[thumb" ) !== false ) $this->not_allowed_tags = true;
				if( stristr( $source, "&lt;img" ) !== false ) $this->not_allowed_tags = true;
	
			}
	
			$source = preg_replace_callback( "#\[email\s*=\s*\&quot\;([\.\w\-]+\@[\.\w\-]+\.[\.\w\-]+)\s*\&quot\;\s*\](.*?)\[\/email\]#i", array( &$this, 'build_email'), $source );
			$source = preg_replace_callback( "#\[email\s*=\s*([\.\w\-]+\@[\.\w\-]+\.[\w\-]+)\s*\](.*?)\[\/email\]#i", array( &$this, 'build_email'), $source );
	
			if( !$this->safe_mode ) {
	
				$source = preg_replace_callback( "'\[medium\](.+?)\[/medium\]'i", array( &$this, 'build_medium'), $source );
				$source = preg_replace_callback( "'\[medium=(.+?)\](.+?)\[/medium\]'i", array( &$this, 'build_medium'), $source );
				$source = preg_replace_callback( "#\[audio\s*=\s*(\S.+?)\s*\]#i", array( &$this, 'build_audio'), $source );
				$source = preg_replace_callback( "#\[flash=([^\]]+)\](.+?)\[/flash\]#i", array( &$this, 'build_flash'), $source );
	
				$source = preg_replace_callback( "#\[ol=([^\]]+)\]\[\*\]#is", array( &$this, 'build_list'), $source );
				$source = preg_replace_callback( "#\[ol=([^\]]+)\](.+?)\[\*\]#is", array( &$this, 'build_list'), $source );
				$source = str_ireplace("[list][*]", "<!--dle_list--><ul><li>", $source);
				$source = preg_replace( "#\[list\](.+?)\[\*\]#is", "<!--dle_list--><ul><li>", $source );
				$source = str_replace("[*]", "</li><!--dle_li--><li>", $source);
				$source = str_ireplace("[/list]", "</li></ul><!--dle_list_end-->", $source);
				$source = str_ireplace("[/ol]", "</li></ol><!--dle_list_end-->", $source);
	
				$source = preg_replace_callback( "#\[(size)=([^\]]+)\]#i", array( &$this, 'font_change'), $source );
				$source = preg_replace_callback( "#\[(font)=([^\]]+)\]#i", array( &$this, 'font_change'), $source );
				$source = str_ireplace("[/size]", "<!--sizeend--></span><!--/sizeend-->", $source);
				$source = str_ireplace("[/font]", "<!--fontend--></span><!--/fontend-->", $source);
				
				while( preg_match( "#\[h([1-6]{1})\](.+?)\[/h\\1\]#is", $source ) ) {
					$source = preg_replace( "#\[h([1-6]{1})\](.+?)\[/h\\1\]#is", "<h\\1>\\2</h\\1>", $source );
				}
			
			}
			
			if( $this->allow_media ) {
				
				$source = preg_replace_callback( "#\[media=([^\]]+)\]#i", array( &$this, 'build_media'), $source );
				
			} else {
	
				if( stristr( $source, "[media" ) !== false ) $this->not_allowed_tags = true;
	
			}
			
			if( $this->allow_video ) {
				
				$source = preg_replace_callback( "#\[video\s*=\s*(\S.+?)\s*\]#i", array( &$this, 'build_video'), $source );
				
			} else {
	
				if( stristr( $source, "[video" ) !== false ) $this->not_allowed_tags = true;
	
			}
			
			$source = preg_replace_callback( "#\[(color)=([^\]]+)\]#i", array( &$this, 'font_change'), $source );
	
			$source = str_ireplace("[/color]", "<!--colorend--></span><!--/colorend-->", $source);
			
			if ($this->is_comments) {
				
				if( intval( $config['auto_wrap'] ) ) {
					
					$source = preg_split( '((>)|(<))', $source, - 1, PREG_SPLIT_DELIM_CAPTURE );
					$n = count( $source );
					
					for($i = 0; $i < $n; $i ++) {
						if( $source[$i] == "<" ) {
							$i ++;
							continue;
						}
						
						if( preg_match( "#([^\s\n\r]{" . intval( $config['auto_wrap'] ) . "})#ui", $source[$i] ) ) {
			
							$source[$i] = preg_replace( "#([^\s\n\r]{" . intval( $config['auto_wrap']-1 ) . "})#ui", "\\1<br />", $source[$i] );
			
						}
			
					}
					
					$source = join( "", $source );
				
				}
				
			}
			
			$source = preg_replace_callback( "#<a(.+?)>(.*?)</a>#is", array( &$this, 'add_rel'), $source );
			$source = preg_replace_callback( "#<img(.+?)>#is", array( &$this, 'clear_img'), $source );

			if( $this->code_count ) {
				
				$find=array();$replace=array();
				foreach ( $this->code_text as $key_find => $key_replace ) {
					$find[] = $key_find;
					$replace[] = $key_replace;
				}
	
				$source = str_replace( $find, $replace, $source );

				$this->code_count = 0;
				$this->code_text = array ();
			
				$source = preg_replace( "#\[code\](.+?)\[/code\]#is", "<pre><code>\\1</code></pre>", $source );
		
				if ( !$this->allow_code AND $this->edit_mode) {
					$source = preg_replace_callback( "#<pre><code>(.+?)</code></pre>#is", array( &$this, 'clear_p_tag'), $source );
				}
				
				$source = str_replace( "__CODENR__", "\r", $source );
				$source = str_replace( "__CODENN__", "\n", $source );

			}
			
			$this->image_count = 0;
		}
		
		return trim( $source );

	}

	function decodeBBCodes($txt, $use_html = TRUE, $wysiwig = false) {

		global $config;

		$txt = stripslashes( $txt );
		if( $this->filter_mode ) $txt = $this->word_filter( $txt, false );

		$txt = str_ireplace( "&#123;THEME}", "{THEME}", $txt );
		$txt = str_ireplace( "&#123;comments}", "{comments}", $txt );
		$txt = str_ireplace( "&#123;addcomments}", "{addcomments}", $txt );
		$txt = str_ireplace( "&#123;newsnavigation}", "{newsnavigation}", $txt );
		$txt = str_ireplace( "&#91;declination", "[declination", $txt );
		$txt = str_ireplace( "&#123;include", "{include", $txt );
		$txt = str_ireplace( "&#123;content", "{content", $txt );
		$txt = str_ireplace( "&#123;custom", "{custom", $txt );
		
		$txt = preg_replace_callback( "#<!--(TBegin|MBegin):(.+?)-->(.+?)<!--(TEnd|MEnd)-->#i", array( &$this, 'decode_thumb'), $txt );
		$txt = preg_replace_callback( "#<!--TBegin-->(.+?)<!--TEnd-->#i", array( &$this, 'decode_oldthumb'), $txt );
		$txt = preg_replace( "#<!--QuoteBegin-->(.+?)<!--QuoteEBegin-->#", '[quote]', $txt );
		$txt = preg_replace( "#<!--QuoteBegin ([^>]+?) -->(.+?)<!--QuoteEBegin-->#", "[quote=\\1]", $txt );
		$txt = preg_replace( "#<!--QuoteEnd-->(.+?)<!--QuoteEEnd-->#", '[/quote]', $txt );
		$txt = preg_replace( "#<!--code1-->(.+?)<!--ecode1-->#", '[code]', $txt );
		$txt = preg_replace( "#<!--code2-->(.+?)<!--ecode2-->#", '[/code]', $txt );
		$txt = preg_replace_callback( "#<!--dle_leech_begin--><a href=\"(.+?)\"(.*?)>(.+?)</a><!--dle_leech_end-->#i", array( &$this, 'decode_leech'), $txt );
		$txt = preg_replace( "#<!--dle_video_begin-->(.+?)src=\"(.+?)\"(.+?)<!--dle_video_end-->#is", '[video=\\2]', $txt );
		$txt = preg_replace_callback( "#<!--dle_video_begin:(.+?)-->(.+?)<!--dle_video_end-->#is", array( &$this, 'decode_video'), $txt );
		$txt = preg_replace_callback( "#<!--dle_audio_begin:(.+?)-->(.+?)<!--dle_audio_end-->#is", array( &$this, 'decode_audio'), $txt );
		$txt = preg_replace_callback( "#<!--dle_image_begin:(.+?)-->(.+?)<!--dle_image_end-->#is", array( &$this, 'decode_dle_img'), $txt );
		$txt = preg_replace( "#<!--dle_youtube_begin:(.+?)-->(.+?)<!--dle_youtube_end-->#is", '[media=\\1]', $txt );
		$txt = preg_replace( "#<!--dle_media_begin:(.+?)-->(.+?)<!--dle_media_end-->#is", '[media=\\1]', $txt );
		$txt = preg_replace_callback( "#<!--dle_flash_begin:(.+?)-->(.+?)<!--dle_flash_end-->#is", array( &$this, 'decode_flash'), $txt );
		$txt = preg_replace( "#<!--dle_spoiler-->(.+?)<!--spoiler_text-->#is", '[spoiler]', $txt );
		$txt = preg_replace_callback( "#<!--dle_spoiler (.+?) -->(.+?)<!--spoiler_text-->#is", array( &$this, 'decode_spoiler'), $txt );
		$txt = str_replace( "<!--spoiler_text_end--></div><!--/dle_spoiler-->", '[/spoiler]', $txt );
		$txt = str_replace( "<!--dle_list--><ul><li>", "[list]\n[*]", $txt );
		$txt = str_replace( "</li></ul><!--dle_list_end-->", '[/list]', $txt );
		$txt = str_replace( "</li></ol><!--dle_list_end-->", '[/ol]', $txt );
		$txt = str_replace( "</li><!--dle_li--><li>", '[*]', $txt );
		$txt = preg_replace('/<pre[^>]*><code>/', '[code]', $txt);
		$txt = str_replace( "</code></pre>", '[/code]', $txt );
		$txt = preg_replace( "#<!--dle_ol_(.+?)-->(.+?)<!--/dle_ol-->#i", "[ol=\\1]\n[*]", $txt );

		if( !$wysiwig ) {

			while( preg_match( "#\<(b|i|s|u|sub|sup)\>(.+?)\</\\1\>#is", $txt ) ) {
				$txt = preg_replace( "#\<(b|i|s|u|sub|sup)\>(.+?)\</\\1\>#is", "[\\1]\\2[/\\1]", $txt );
			}

			$txt = preg_replace( "#<a href=[\"']mailto:(.+?)['\"]>(.+?)</a>#i", "[email=\\1]\\2[/email]", $txt );
			$txt = preg_replace_callback( "#<a href=\"(.+?)\"(.*?)>(.+?)</a>#i", array( &$this, 'decode_url'), $txt );

			$txt = preg_replace( "#<!--sizestart:(.+?)-->(.+?)<!--/sizestart-->#", "[size=\\1]", $txt );
			$txt = preg_replace( "#<!--colorstart:(.+?)-->(.+?)<!--/colorstart-->#", "[color=\\1]", $txt );
			$txt = preg_replace( "#<!--fontstart:(.+?)-->(.+?)<!--/fontstart-->#", "[font=\\1]", $txt );

			$txt = str_replace( "<!--sizeend--></span><!--/sizeend-->", "[/size]", $txt );
			$txt = str_replace( "<!--colorend--></span><!--/colorend-->", "[/color]", $txt );
			$txt = str_replace( "<!--fontend--></span><!--/fontend-->", "[/font]", $txt );
			
			$txt = preg_replace( "#<h([1-6]{1})>(.+?)</h\\1>#is", "[h\\1]\\2[/h\\1]", $txt );

			$txt = preg_replace( "#<div align=['\"](left|right|center|justify)['\"]>(.+?)</div>#is", "[\\1]\\2[/\\1]", $txt );
			$txt = preg_replace( "#<div style=['\"]text-align:(left|right|center|justify);['\"]>(.+?)</div>#is", "[\\1]\\2[/\\1]", $txt );



		} else {

			$txt = str_replace( "<!--sizeend--></span><!--/sizeend-->", "</span>", $txt );
			$txt = str_replace( "<!--colorend--></span><!--/colorend-->", "</span>", $txt );
			$txt = str_replace( "<!--fontend--></span><!--/fontend-->", "</span>", $txt );
			$txt = str_replace( "<!--/sizestart-->", "", $txt );
			$txt = str_replace( "<!--/colorstart-->", "", $txt );
			$txt = str_replace( "<!--/fontstart-->", "", $txt );
			$txt = preg_replace( "#<!--sizestart:(.+?)-->#", "", $txt );
			$txt = preg_replace( "#<!--colorstart:(.+?)-->#", "", $txt );
			$txt = preg_replace( "#<!--fontstart:(.+?)-->#", "", $txt );

		}

		$txt = preg_replace( "#<!--smile:(.+?)-->(.+?)<!--/smile-->#is", ':\\1:', $txt );
		$txt = preg_replace_callback( "#<a(.+?)>(.*?)</a>#is", array( &$this, 'remove_rel'), $txt );

		if( !$use_html ) {
			$txt = str_ireplace( "<br>", "\n", $txt );
			$txt = str_ireplace( "<br />", "\n", $txt );
		}

		if ((!$this->safe_mode OR $wysiwig) AND $this->edit_mode) {
			$txt = htmlspecialchars( $txt, ENT_QUOTES, $config['charset'] );
		}
		
		$this->codes_param['html'] = $use_html;
		$this->codes_param['wysiwig'] = $wysiwig;
		$txt = preg_replace_callback( "#\[code\](.+?)\[/code\]#is", array( &$this, 'decode_code'), $txt );

		if(!$this->safe_mode AND $this->edit_mode AND !$this->codes_param['wysiwig'] ) {
			$txt = str_replace( "&amp;amp;", "&amp;", $txt );
			$txt = str_replace( "__CODEAMP__", "&", $txt );
		}
		
		if ( $this->safe_mode AND $this->edit_mode AND !$this->codes_param['wysiwig'] )	{
			$txt = str_replace( "__CODEAMP__", "&amp;", $txt );
		}
		
		return trim( $txt );

	}
	
	function build_list( $matches=array() ) {
		$type = $matches[1];

		$allowed_types = array ("A", "a", "I", "i", "1");

		if (in_array($type, $allowed_types))
			return "<!--dle_ol_{$type}--><ol type=\"{$type}\"><li><!--/dle_ol-->";
		else
			return "<!--dle_ol_1--><ol type=\"1\"><li><!--/dle_ol-->";

	}

	function font_change( $matches=array() ) {

		$style = $matches[2];
		$type = $matches[1];

		$style = str_replace( '&quot;', '', $style );
		$style = preg_replace( "/[&\(\)\.\%\{\}\[\]<>\'\"]/", "", preg_replace( "#^(.+?)(?:;|$)#", "\\1", $style ) );

		if( $type == 'size' ) {
			$style = intval( $style );

			if( $this->font_sizes[$style] ) {
				$real = $this->font_sizes[$style];
			} else {
				$real = 12;
			}

			return "<!--sizestart:{$style}--><span style=\"font-size:" . $real . "pt;\"><!--/sizestart-->";
		}

		if( $type == 'font' ) {
			
			$style = preg_replace( "/[^\d\w\#\-\_\s]/s", "", $style );
			
			if (!in_array($style, $this->allowed_fonts)) $style = "Verdana";
			
			return "<!--fontstart:{$style}--><span style=\"font-family:" . $style . "\"><!--/fontstart-->";
		}

		$style = preg_replace( "/[^\d\w\#]/s", "", $style );
		
		if( preg_match("/#([a-f0-9]{3}){1,2}\b/i", $style) ) return "<!--colorstart:{$style}--><span style=\"color:" . $style . "\"><!--/colorstart-->";
		else return "<!--colorstart:#000000--><span style=\"color:#000000\"><!--/colorstart-->";;
		
	}

	function build_email( $matches=array() ) {

		$matches[1] = $this->clear_url( $matches[1] );

		return $this->htmlparser->purify("<a href=\"mailto:{$matches[1]}\">{$matches[2]}</a>");

	}

	function build_flash( $matches=array() ) {

		$size = $matches[1];
		$url = $matches[2];
		$size = explode(",", $size);

		$width = trim(intval($size[0]));
		$height = trim(intval($size[1]));

		if (!$width OR !$height) return $matches[0];

		$url = $this->clear_url( urldecode( $url ) );

		if( $url == "" ) return $matches[0];

		if( preg_match( "/[?&;%<\[\]]/", $url ) ) {

			return $matches[0];

		}

		return "<!--dle_flash_begin:{$width}||{$height}||{$url}-->".$this->htmlparser->purify("<object type=\"application/x-shockwave-flash\" width=\"$width\" height=\"$height\" data=\"$url\"><param name=\"movie\" value=\"$url\"><param name=\"wmode\" value=\"transparent\" /><param name=\"play\" value=\"true\"><param name=\"loop\" value=\"true\"><param name=\"quality\" value=\"high\"><param name=\"allowScriptAccess\" value=\"never\"><param name=\"allowNetworking\" value=\"internal\"><embed allowscriptaccess=\"never\" allownetworking=\"internal\" src=\"$url\" width=\"$width\" height=\"$height\" play=\"true\" loop=\"true\" quality=\"high\" wmode=\"transparent\"></embed></object>")."<!--dle_flash_end-->";

	}

	function decode_flash( $matches=array() )
	{
		$url = explode( "||", $matches[1] );

		return '[flash='.$url[0].','.$url[1].']'.$url[2].'[/flash]';
	}

	function build_media( $matches=array() ) {
		global $config;

		$url = $matches[1];

		$get_size = explode( ",", trim( $url ) );
		$sizes = array();
		$params = array();
		
		if (!count($this->video_config)) {

			include (ENGINE_DIR . '/data/videoconfig.php');
			$this->video_config = $video_config;

		}
		
		if (count($get_size) == 2)  {

			$url = $get_size[1];
			$sizes = explode( "x", trim( $get_size[0] ) );
			
			if( intval($sizes[0]) > 0 ) {
				$params['width'] = intval($sizes[0]);
			}
			
			if( intval($sizes[1]) > 0 ) {
				$params['height'] = intval($sizes[1]);
			}

		}

		$url = $this->clear_url( urldecode( $url ) );
		$url = str_replace("&amp;","&", $url );

		if( !$url ) {
			
			return $matches[0];
		
		}
		
		$decode_url = "";
		
		if( $params['width'] ) {
			$decode_url = $params['width'];
			
			if ( $params['height'] ) $decode_url .= "x".$params['height'];
			
			$decode_url .= ",".$url;
			
		} else {
			
			if (substr( trim($this->video_config['width']), - 1, 1 ) != '%') {
				$params['width'] = intval($this->video_config['width']);
			}
			
			$decode_url = $url;
		}
		
		$html = $this->media_providers->getHTML($url, $params);

		if(!$html) {
			return $matches[0];
		}

		return '<!--dle_media_begin:'.$decode_url.'-->'.$html.'<!--dle_media_end-->';

	}

	function build_url( $matches=array() ) {
		global $config, $member_id, $user_group;

		$url = array();

		if ($matches[1] == "leech" ) $url['leech'] = 1;

		$option=explode("|", $matches[2]);
		
		$url['html'] = $option[0];
		$url['tooltip'] = $option[1];
		$url['show'] = $matches[3];
		
		if ( !$url['show'] ) $url['show'] = $url['html'];

		if ( $user_group[$member_id['user_group']]['force_leech'] ) $url['leech'] = 1;

		if( preg_match( "/([\.,\?]|&#33;)$/", $url['show'], $match ) ) {
			$url['end'] = $match[1];
			$url['show'] = preg_replace( "/([\.,\?]|&#33;)$/", "", $url['show'] );
		}

		$url['html'] = $this->clear_url( $url['html'] );
		$url['show'] = stripslashes( $url['show'] );

		if( $this->safe_mode ) {

			$url['show'] = str_replace( "&nbsp;", " ", $url['show'] );

			if (strlen(trim($url['show'])) < 3 )
				return $matches[0];

		}

		if( stripos( $url['html'], $config['admin_path'] ) !== false ) {

			return $matches[0];

		}
		
		if( stripos( $url['html'], "engine/go.php" ) !== false OR ($this->check_home( $url['html'] ) AND stripos( $url['html'], "do=go" ) !== false) ) {
			return $matches[0];
		}

		if( !preg_match( "#^(http|https|ftp|nntp|news)://#", $url['html'] ) AND !preg_match( "#^(tel):#", $url['html'] )  AND !preg_match( "#^(magnet):#", $url['html'] ) AND $url['html'][0] != "/" AND $url['html'][0] != "#") {
			$url['html'] = 'http://' . $url['html'];
		}

		if ($url['html'] == 'http://' ) {
			return $matches[0];
		}

		$url['show'] = str_replace( "&amp;amp;", "&amp;", $url['show'] );

		if( $this->check_home( $url['html'] ) OR $url['html'][0] == "/" OR $url['html'][0] == "#") $target = "";
		else $target = " target=\"_blank\"";

		if( $url['tooltip'] ) {
			$url['tooltip'] = htmlspecialchars( strip_tags( stripslashes( $url['tooltip'] ) ), ENT_QUOTES, $config['charset'] );
			$url['tooltip'] = str_replace( "&amp;amp;", "&amp;", $url['tooltip'] );
			$target = "title=\"".$url['tooltip']."\"".$target;
		}
		
		if( $url['leech'] AND !$this->disable_leech) {

			$url['html'] = $config['http_home_url'] . "index.php?do=go&url=" . rawurlencode( base64_encode( $url['html'] ) );

			return "<!--dle_leech_begin-->".$this->htmlparser->purify("<a href=\"" . $url['html'] . "\" " . $target . ">" . $url['show'] . "</a>")."<!--dle_leech_end-->" . $url['end'];

		} else {

			if ($this->safe_mode AND !$config['allow_search_link'] AND $target)
				return $this->htmlparser->purify("<a href=\"" . $url['html'] . "\" " . $target . " rel=\"nofollow\">" . $url['show'] . "</a>") . $url['end'];
			else
				return $this->htmlparser->purify("<a href=\"" . $url['html'] . "\" " . $target . ">" . $url['show'] . "</a>") . $url['end'];

		}

	}

	function code_tag( $matches=array() ) {

		$txt = $matches[1];

		if( $txt == "" ) {
			return;
		}

		$this->code_count ++;
		
		if ( $this->is_comments AND $this->wysiwyg AND $this->edit_mode) {
			$txt = str_replace( "<br>", "\n", $txt );
			$txt = preg_replace('/<p[^>]*>/', '', $txt);
			$txt = str_replace("</p>", "", $txt);
			$txt = str_replace( "&lt;", "<", $txt );
			$txt = str_replace( "&gt;", ">", $txt );
			$txt = str_replace( "&amp;", "&", $txt );
		}
		
		if ( $this->edit_mode )	{
			$txt = str_replace( "&", "&amp;", $txt );
			$txt = str_replace( "'", "&#39;", $txt );
			$txt = str_replace( "<", "&lt;", $txt );
			$txt = str_replace( ">", "&gt;", $txt );
			$txt = str_replace( "&quot;", "&#34;", $txt );
			$txt = str_replace( '"', "&#34;", $txt );
			$txt = str_replace( ":", "&#58;", $txt );
			$txt = str_replace( "[", "&#91;", $txt );
			$txt = str_replace( "]", "&#93;", $txt );
			$txt = str_replace( "&amp;#123;include", "&#123;include", $txt );
			$txt = str_replace( "&amp;#123;content", "&#123;content", $txt );
			$txt = str_replace( "&amp;#123;custom", "&#123;custom", $txt );
			$txt = str_replace( "{", "&#123;", $txt );
			$txt = str_replace( "\r", "__CODENR__", $txt );
			$txt = str_replace( "\n", "__CODENN__", $txt );

		}
		
		$txt = str_ireplace( "{include", "&#123;include", $txt );
		$txt = str_ireplace( "{content", "&#123;content", $txt );
		$txt = str_ireplace( "{custom", "&#123;custom", $txt );
		$txt = str_ireplace( "{newsnavigation", "&#123;newsnavigation", $txt );
		$txt = str_ireplace( "{THEME}", "&#123;THEME}", $txt );
		$txt = str_replace( "slideshowGroup:", "slideshowGroup&#58;", $txt );
		
		$p = "[code]{" . $this->code_count . "}[/code]";

		$this->code_text[$p] = "[code]{$txt}[/code]";

		return $p;
	}
	
	function clear_code( $matches=array() ) {
		$txt = $matches[1];

		if( $txt == "" ) {
			return;
		}
		
		$txt = str_replace( "</code>", "\n", $txt );
		$txt = str_replace("<code>", "", $txt);
		
		return "<pre class=\"language-markup\"><code>".$txt."</code></pre>";
	}
	
	function hide_code_tag( $matches=array() ) {
		$txt = $matches[1];

		if( $txt == "" ) {
			return;
		}

		$this->code_count ++;
		
		$p = "[code]{" . $this->code_count . "}[/code]";

		$this->code_text[$p] = "[code]{$txt}[/code]";

		return $p;
	}

	function decode_code( $matches=array() ) {

		$txt = $matches[1];

		if ( !$this->codes_param['wysiwig'] AND $this->edit_mode )	{

			$txt = str_replace( "&amp;", "__CODEAMP__", $txt );
		}

		if( !$this->codes_param['wysiwig'] AND $this->codes_param['html']) {
			$txt = str_replace( "&lt;br /&gt;", "\n", $txt );
			$txt = str_replace( "&lt;br&gt;", "\n", $txt );
		}
		
		if ( $this->safe_mode AND $this->codes_param['wysiwig'] AND $this->edit_mode) {
			$txt = str_replace( "\n", "<br>", $txt );
		}
		
		if ( $this->codes_param['wysiwig'] AND $this->edit_mode AND !$this->is_comments) {

			return "&lt;pre class=\"language-markup\">&lt;code&gt;".$txt."&lt;/code>&lt;/pre&gt;";
		}

		return "[code]".$txt."[/code]";
	}


	function build_video( $matches=array() ) {
		global $config;

		$url = $matches[1];
		
		if (!count($this->video_config)) {

			include (ENGINE_DIR . '/data/videoconfig.php');
			$this->video_config = $video_config;

		}
		
		$get_videos = array();
		$sizes = array();
		$decode_url = array();
		$video_url = array();
		$video_option = array();
		$i = 0;
		
		$width = $this->video_config['width'];
		
		if( $this->video_config['preload'] ) $preload = "metadata"; else $preload = "none";

		$get_videos = explode( ",", trim( $url ) );

		foreach ($get_videos as $video) {
			$i++;
			
			if( $i == 1 AND count($get_videos) > 1 AND stripos ( $video, "http" ) === false AND intval($video) ) {
				
				$sizes = explode( "x", trim( $video ) );
				$width = intval($sizes[0]) > 0 ? intval($sizes[0]) : $this->video_config['width'];
				
				if (substr( $sizes[0], - 1, 1 ) == '%') $width = $width."%";
				
				$decode_url[] = $width;
				continue;
			
			}
		
			$video = str_replace( "%20", " ", trim( $video ) );
			
			$video_option = explode( "|", trim( $video ) );
		
			$video_option[0] = $this->clear_url( trim($video_option[0]) );
			
			if( !$video_option[0] ) continue;
			
			if($video_option[1]) {
				$video_option[1] = $this->clear_url( trim($video_option[1]) );
				$preview = " poster=\"{$video_option[1]}\" ";
			} else { $preview = ""; }
			
			if($video_option[2]) {
				$video_option[2] = htmlspecialchars( strip_tags( stripslashes( trim($video_option[2]) ) ), ENT_QUOTES, $config['charset'] );
				$video_option[2] = str_replace("&amp;amp;","&amp;", $video_option[2]);
			}
			
			
			$decode_url[] = implode("|", $video_option);
			if( !$video_option[2] ) $video_option[2] = str_replace( "%20", " ", pathinfo( $video_option[0], PATHINFO_FILENAME ) );
			
			$type="type=\"video/mp4\"";
			
			if (strpos ( $video_option[0], "youtube.com" ) !== false) { $type="provider=\"youtube\""; $preload = "metadata"; }

			$video_url[] = "<video title=\"{$video_option[2]}\" preload=\"{$preload}\" controls{$preview}><source {$type} src=\"{$video_option[0]}\"></video>";
			
		}
		
		if( count($video_url) ){
			$video_url = implode($video_url);
			$decode_url = implode(",",$decode_url);
		} else {
			return $matches[0];
		}
		
		if (substr( $width, - 1, 1 ) != '%') $width = $width."px";

		$width = "style=\"width:100%;max-width:{$width};\"";
		
		return "<!--dle_video_begin:{$decode_url}--><div class=\"dleplyrplayer\" {$width} theme=\"{$this->video_config['theme']}\">{$video_url}</div><!--dle_video_end-->";

	}
	
	function build_audio( $matches=array() ) {
		global $config;

		$url = $matches[1];

		if( $url == "" ) return;

		if (!count($this->video_config)) {

			include (ENGINE_DIR . '/data/videoconfig.php');
			$this->video_config = $video_config;

		}

		$get_audios = array();
		$sizes = array();
		$decode_url = array();
		$audio_url = array();
		$audio_option = array();
		$i = 0;
		
		$width = $this->video_config['audio_width'];
		
		if( $this->video_config['preload'] ) $preload = "metadata"; else $preload = "none";

		$get_audios = explode( ",", trim( $url ) );

		foreach ($get_audios as $audio) {
			$i++;
			
			if( $i == 1 AND count($get_audios) > 1 AND stripos ( $audio, "http" ) === false AND intval($audio)) {
				
				$sizes = explode( "x", trim( $audio ) );
				$width = intval($sizes[0]) > 0 ? intval($sizes[0]) : $this->video_config['audio_width'];
				
				if (substr( $sizes[0], - 1, 1 ) == '%') $width = $width."%";
				
				$decode_url[] = $width;
				continue;
			
			}
			
			$audio = str_replace( "%20", " ", trim( $audio ) );
			
			$audio_option = explode( "|", trim( $audio ) );
			
			$audio_option[0] = $this->clear_url( trim($audio_option[0]) );
			
			if( !$audio_option[0] ) continue;
			
			if($audio_option[1]) $audio_option[1] = htmlspecialchars( strip_tags( stripslashes( trim($audio_option[1]) ) ), ENT_QUOTES, $config['charset'] );
			
			$decode_url[] = implode("|", $audio_option);
			if( !$audio_option[1] ) $audio_option[1] = str_replace( "%20", " ", pathinfo( $audio_option[0], PATHINFO_FILENAME ));
			
			$audio_url[] = "<audio title=\"{$audio_option[1]}\" preload=\"{$preload}\" controls><source type=\"audio/mp3\" src=\"{$audio_option[0]}\"></audio>";
			
		}
		
		if( count($audio_url) ){
			$audio_url = implode($audio_url);
			$decode_url = implode(",",$decode_url);
		} else {
			return $matches[0];
		}
		
		if (substr( $width, - 1, 1 ) != '%') $width = $width."px";

		if( $width ) $width = "style=\"width:100%;max-width:{$width};\""; 

		return "<!--dle_audio_begin:{$decode_url}--><div class=\"dleplyrplayer\" {$width} theme=\"{$this->video_config['theme']}\">{$audio_url}</div><!--dle_audio_end-->";		


	}
	
	function decode_video( $matches=array() ) {
		$url = 	$matches[1];
		
		$url = str_replace("&amp;","&", $url );
		$url = str_replace("&quot;",'"', $url );
		$url = str_replace("&#039;","'", $url );
		
		return '[video='.$url.']';
	}
	

	function decode_audio( $matches=array() ) {
		$url = 	$matches[1];
		
		$url = str_replace("&amp;","&", $url );
		$url = str_replace("&quot;",'"', $url );
		$url = str_replace("&#039;","'", $url );
		
		return '[audio='.$url.']';
	}
	
	function build_image( $matches=array() ) {
		global $config;

		if(count($matches) == 2 ) {

			$align = "";
			$url = $matches[1];

		} else {
			$align = $matches[1];
			$url = $matches[2];
		}

		$url = trim( $url );
		$option = explode( "|", trim( $align ) );
		$align = $option[0];

		if( $align != "left" and $align != "right" ) $align = '';

		$url = $this->clear_url( urldecode( $url ) );
		
		if( preg_match( "/[?&;<\[\]]/", $url ) ) {

			return $matches[0];

		}

		$info = $url;

		$info = $info."|".$align;

		if( $url == "" ) return $matches[0];

		$this->image_count ++;

		if( $option[1] != "" ) {

			$alt = htmlspecialchars( strip_tags( stripslashes( $option[1] ) ), ENT_QUOTES, $config['charset'] );
			$alt = str_replace("&amp;amp;","&amp;",$alt);
			
			$info = $info."|".$alt;
			$alt = "alt=\"" . $alt . "\"";

		} else {
			
			if($this->image_count == 1) {
				
				$alt = htmlspecialchars( strip_tags( stripslashes( $_POST['title'] ) ), ENT_QUOTES, $config['charset'] );
				$alt = str_replace("&amp;amp;","&amp;",$alt);
				
			} else { $alt = ""; }
			
			$alt = "alt=\"" . $alt . "\"";

		}

		if ( $align ) {
			
			$style="style=\"float:{$align};max-width:100%;\"";
			
		} else $style="style=\"max-width:100%;\"";
		
		if( intval( $config['tag_img_width'] ) ) {

			if (clean_url( $config['http_home_url'] ) != clean_url ( $url ) ) {
				
				$style .= " data-maxwidth=\"".intval($config['tag_img_width'])."\"";
				
			}
			
		}

		return "<!--dle_image_begin:{$info}-->".$this->htmlparser->purify("<img src=\"{$url}\" {$style} {$alt}>")."<!--dle_image_end-->";

	}

	function decode_dle_img( $matches=array() ) {

		$txt = $matches[1];
		$txt = explode("|", $txt );
		$url = $txt[0];
		$align = $txt[1];
		$alt = $txt[2];
		$extra = "";

		if( ! $align and ! $alt ) return "[img]" . $url . "[/img]";

		if( $align ) $extra = $align;

		if( $alt ) {

			$alt = str_replace("&#039;", "'", $alt);
			$alt = str_replace("&quot;", '"', $alt);
			$alt = str_replace("&amp;", '&', $alt);
			$extra .= "|" . $alt;

		}

		return "[img=" . $extra . "]" . $url . "[/img]";

	}

	function clear_p_tag( $matches=array() ) {

		$txt = $matches[1];

		$txt = str_replace("\r", "", $txt);
		$txt = str_replace("\n", "", $txt);

		$txt = preg_replace('/<p[^>]*>/', '', $txt);
		$txt = str_replace("</p>", "\n", $txt);
		$txt = preg_replace('/<div[^>]*>/', '', $txt);
		$txt = str_replace("</div>", "\n", $txt);
		$txt = preg_replace('/<br[^>]*>/', "\n", $txt);

		return "<pre><code>".$txt."</code></pre>";

	}

	function clear_div_tag( $matches=array() ) {

		$spoiler = array();

		if ( count($matches) == 3 ) {
			$spoiler['title'] = '';
			$spoiler['txt'] = $matches[2];
		} else {
			$spoiler['title'] = $matches[2];
			$spoiler['txt'] = $matches[3];
		}

		$tag = $matches[1];

		$spoiler['txt'] = preg_replace('/<div[^>]*>/', '', $spoiler['txt']);
		$spoiler['txt'] = str_replace("</div>", "<br />", $spoiler['txt']);

		if ($spoiler['title'])
			return "[{$tag}={$spoiler['title']}]".$spoiler['txt']."[/{$tag}]";
		else
			return "[{$tag}]".$spoiler['txt']."[/{$tag}]";

	}

	function build_thumb( $matches=array() ) {
		global $config;

		if (count($matches) == 2 ) {
			$align = "";
			$gurl = $matches[1];
		} else {
			$align = $matches[1];
			$gurl = $matches[2];
		}

		$gurl = $this->clear_url( urldecode( $gurl ) );
		
		if( preg_match( "/[?&;%<\[\]]/", $gurl ) ) {

			return $matches[0];

		}
		
		$url = preg_replace( "'([^\[]*)([/\\\\])(.*?)'i", "\\1\\2thumbs\\2\\3", $gurl );

		$url = trim( $url );
		$gurl = trim( $gurl );
		$option = explode( "|", trim( $align ) );

		$align = $option[0];

		if( $align != "left" and $align != "right" ) $align = '';

		$url = $this->clear_url( urldecode( $url ) );

		$info = $gurl;
		$info = $info."|".$align;

		if( $gurl == "" or $url == "" ) return $matches[0];

		if( $option[1] != "" ) {

			$alt = htmlspecialchars( strip_tags( stripslashes( $option[1] ) ), ENT_QUOTES, $config['charset'] );

			$alt = str_replace("&amp;amp;","&amp;",$alt);

			$info = $info."|".$alt;
			$alt = "alt=\"" . $alt . "\"";

		} else {

			$alt = "alt=''";

		}

		if( $align == '' ) return "<!--TBegin:{$info}-->".$this->htmlparser->purify("<a href=\"$gurl\" class=\"highslide\" target=\"_blank\"><img src=\"$url\" style=\"max-width:100%;\" {$alt}></a>")."<!--TEnd-->";
		else return "<!--TBegin:{$info}-->".$this->htmlparser->purify("<a href=\"$gurl\" class=\"highslide\" target=\"_blank\"><img src=\"$url\" style=\"float:{$align};max-width:100%;\" {$alt}></a>")."<!--TEnd-->";

	}


	function build_medium( $matches=array() ) {
		global $config;

		if (count($matches) == 2 ) {
			$align = "";
			$gurl = $matches[1];
		} else {
			$align = $matches[1];
			$gurl = $matches[2];
		}

		$gurl = $this->clear_url( urldecode( $gurl ) );
		
		if( preg_match( "/[?&;%<\[\]]/", $gurl ) ) {

			return $matches[0];

		}
		
		$url = preg_replace( "'([^\[]*)([/\\\\])(.*?)'i", "\\1\\2medium\\2\\3", $gurl );

		$url = trim( $url );
		$gurl = trim( $gurl );
		$option = explode( "|", trim( $align ) );

		$align = $option[0];

		if( $align != "left" and $align != "right" ) $align = '';

		$url = $this->clear_url( urldecode( $url ) );

		$info = $gurl;
		$info = $info."|".$align;

		if( $gurl == "" or $url == "" ) return $matches[0];

		if( $option[1] != "" ) {

			$alt = htmlspecialchars( strip_tags( stripslashes( $option[1] ) ), ENT_QUOTES, $config['charset'] );

			$alt = str_replace("&amp;amp;","&amp;",$alt);

			$info = $info."|".$alt;
			$alt = "alt=\"" . $alt . "\"";

		} else {

			$alt = "alt=''";

		}

		if( $align == '' ) return "<!--MBegin:{$info}-->".$this->htmlparser->purify("<a href=\"$gurl\" class=\"highslide\"><img src=\"$url\" style=\"max-width:100%;\" {$alt}></a>")."<!--MEnd-->";
		else return "<!--MBegin:{$info}-->".$this->htmlparser->purify("<a href=\"$gurl\" class=\"highslide\"><img src=\"$url\" style=\"float:{$align};max-width:100%;\" {$alt}></a>")."<!--MEnd-->";
		
	}

	function build_spoiler( $matches=array() ) {
		global $lang, $config;

		
		if (count($matches) == 3 ) {
			
			$title = $matches[1];

			$title = htmlspecialchars( strip_tags( stripslashes( trim($title) ) ), ENT_QUOTES, $config['charset'] );
	
			$title = str_replace( "&amp;amp;", "&amp;", $title );
			$title = preg_replace( "/javascript:/i", "j&#1072;vascript&#58; ", $title );
			
		} else $title = false;
		
		$id_spoiler = "sp".md5( microtime().uniqid( mt_rand(), TRUE ) );
		
		if( !$title ) {

			return "<!--dle_spoiler--><div class=\"title_spoiler\"><a href=\"javascript:ShowOrHide('" . $id_spoiler . "')\"><img id=\"image-" . $id_spoiler . "\" style=\"vertical-align: middle;border: none;\" alt=\"\" src=\"{THEME}/dleimages/spoiler-plus.gif\" /></a>&nbsp;<a href=\"javascript:ShowOrHide('" . $id_spoiler . "')\"><!--spoiler_title-->" . $lang['spoiler_title'] . "<!--spoiler_title_end--></a></div><div id=\"" . $id_spoiler . "\" class=\"text_spoiler\" style=\"display:none;\"><!--spoiler_text-->{$matches[1]}<!--spoiler_text_end--></div><!--/dle_spoiler-->";

		} else {

			return "<!--dle_spoiler $title --><div class=\"title_spoiler\"><a href=\"javascript:ShowOrHide('" . $id_spoiler . "')\"><img id=\"image-" . $id_spoiler . "\" style=\"vertical-align: middle;border: none;\" alt=\"\" src=\"{THEME}/dleimages/spoiler-plus.gif\" /></a>&nbsp;<a href=\"javascript:ShowOrHide('" . $id_spoiler . "')\"><!--spoiler_title-->" . $title . "<!--spoiler_title_end--></a></div><div id=\"" . $id_spoiler . "\" class=\"text_spoiler\" style=\"display:none;\"><!--spoiler_text-->{$matches[2]}<!--spoiler_text_end--></div><!--/dle_spoiler-->";

		}

	}
	
	function decode_spoiler( $matches=array() ) {
		$url = 	$matches[1];
		
		$url = str_replace("&amp;","&", $url );
		$url = str_replace("&quot;",'"', $url );
		$url = str_replace("&#039;","'", $url );
		
		return '[spoiler='.$url.']';
	}
	
	function clear_url($url) {
		global $config;

		$url = strip_tags( trim( stripslashes( html_entity_decode($url, ENT_QUOTES, $config['charset']) ) ) );

		$url = str_replace( '\"', '"', $url );
		$url = str_replace( "'", "", $url );
		$url = str_replace( '"', "", $url );
		$url = str_replace( "&#111;", "o", $url );
		$url = preg_replace( "/j&#1072;vascript(.*?):/i", "javascript:", $url );
		$url = preg_replace( "/d&#1072;ta(.*?):/i", "data:", $url );
		$url = htmlspecialchars( $url, ENT_QUOTES, $config['charset'] );
		
		$url_array = parse_url($url);

		if ( $url_array['scheme'] AND !in_array( $url_array['scheme'], array("http","https","mailto","ftp","nntp","news","tel","magnet")) ) {

			return '';
		}
		
		if( stripos( $url, "engine/go.php" ) !== false OR ($this->check_home( $url ) AND stripos( $url, "do=go" ) !== false) ) {
			return '';
		}
		
		$url = str_replace( "&amp;amp;", "&amp;", $url );
		
		$url = str_ireplace( "document.cookie", "d&#111;cument.cookie", $url );
		$url = str_replace( " ", "%20", $url );
		$url = str_replace( "<", "&#60;", $url );
		$url = str_replace( ">", "&#62;", $url );
		$url = str_replace(array("{", "}", "[", "]"),array("%7B", "%7D", "%5B", "%5D"), $url);
		$url = preg_replace( "/javascript:/i", "j&#1072;vascript:", $url );
		$url = preg_replace( "/data:/i", "d&#1072;ta:", $url );
		
		return $url;

	}

	function decode_leech( $matches=array() ) {
		global $config;
		
		$url = 	$matches[1];
		$show = $matches[3];

		if( $this->leech_mode ) return "[url=" . $url . "]" . $show . "[/url]";

		$url = explode( "url=", $url );
		$url = end( $url );
		$url = rawurldecode( $url );
		$url = base64_decode( $url );
		
		$charset = $this->detect_encoding($url);
		
		if($charset AND $charset != strtolower($config['charset']) ) {
			
			if( function_exists( 'mb_convert_encoding' ) ) {
		
				$url = mb_convert_encoding( $url, $config['charset'], $charset );
		
			} elseif( function_exists( 'iconv' ) ) {
			
				$url = iconv($charset, $config['charset'], $url);
			
			}
			
		}

		$url = strip_tags( $url );
		$url = str_replace("&amp;","&", $url );

		if( preg_match( "#title=['\"](.+?)['\"]#i", $matches[2], $match ) ) {
			$match[1] = str_replace("&quot;", '"', $match[1]);
			$match[1] = str_replace("&#039;", "'", $match[1]);
			$match[1] = str_replace("&amp;", "&", $match[1]);
			$match[1] = strip_tags( $match[1] );
			$url = $url."|".$match[1];
		}
		
		return "[leech=" . $url . "]" . $show . "[/leech]";
	}
	
	function detect_encoding($string) {  
	  static $list = array('utf-8', 'windows-1251');
	   
	  foreach ($list as $item) {
	
		if( function_exists( 'mb_convert_encoding' ) ) {
	
			$sample = mb_convert_encoding( $string, $item, $item );
	
		} elseif( function_exists( 'iconv' ) ) {
		
			$sample = iconv($item, $item, $string);
		
		}
	
		if (md5($sample) == md5($string)) return $item;
	
	   }
	
	   return null;
	}
	
	function decode_url( $matches=array() ) {

		$show =  $matches[3];
		$url = $matches[1];
		$params = trim($matches[2]);

		if( preg_match( "#title=[\"](.+?)[\"]#i", $params, $match ) ) {
			$match[1] = str_replace("&quot;", '"', $match[1]);
			$match[1] = str_replace("&#039;", "'", $match[1]);
			$match[1] = str_replace("&amp;", "&", $match[1]);
			$url = $url."|".$match[1];
			$params = trim(str_replace($match[0], "", $params));
		}
		
		if( preg_match( "#rel=[\"](.+?)[\"]#i", $params, $match ) ) {
			$params = trim(str_replace($match[0], "", $params));
		}

		if (!$params OR $params == 'target="_blank"') {

			$url = str_replace("&amp;","&", $url );

			return "[url=" . $url . "]" . $show . "[/url]";

 		} else {

			return $matches[0];

		}
	}
	
	function clear_img( $matches=array() ) {
		
		$params = trim( stripslashes($matches[1]) );
		
		if( preg_match( "#src=['\"](.+?)['\"]#i", $params, $match ) ) {
			if( preg_match( "/[?&;<]/", $match[1]) ) return "";
		}
		
		return $matches[0];
	}
	
	function remove_bad_url( $matches=array() ) {
		global $config;
		
		$params = trim( stripslashes($matches[1]) );
		
		if( preg_match( "#href=['\"](.+?)['\"]#i", $params, $match ) ) {
			
			if( stripos( $match[1], "engine/go.php" ) !== false OR ($this->check_home( $match[1] ) AND stripos( $match[1], "do=go" ) !== false) ) {
				return '';
			}
			
			if( stripos( $match[1], $config['admin_path'] ) !== false ) {
	
				return '';
	
			}
			
		}
		
		return $matches[0];
	}
	
	function add_rel( $matches=array() ) {

		$params = trim( stripslashes($matches[1]) );
		
		if( preg_match( "#href=['\"](.+?)['\"]#i", $params, $match ) ) {
			
			if( $this->check_home($match[1]) ) {

				if( preg_match( "#rel=['\"](.+?)['\"]#i", $params, $match ) ) {
					
					$remove_params = array("external", "noopener", "noreferrer");
					$new_params = array();
					
					$exist_params = explode(" ", trim($match[1]) );
					
					foreach ($exist_params as $value) {
						if(!in_array( $value, $remove_params ) ) $new_params[] = $value;
					}
					
					if( count($new_params) ) {
						
						$new_params = implode(" ", $new_params);
						$params = str_ireplace($match[0], "rel=\"{$new_params}\"", $params);
						
					} else $params = str_ireplace($match[0], "", $params);
					
					$params = addslashes(trim($params));
					
					return "<a {$params}>{$matches[2]}</a>";
				
				} else {
					
					return $matches[0];
					
				}

			}
			
		} else return $matches[0];
		
		if( preg_match( "#rel=['\"](.+?)['\"]#i", $params, $match ) ) {
			
			$new_params = array("external", "noopener", "noreferrer");

			$exist_params = trim(preg_replace('/\s+/', ' ', $match[1]));
			
			$exist_params = explode(" ", $exist_params);
			
			foreach ($new_params as $value) {
				if(!in_array( $value, $exist_params ) ) $exist_params[] = $value;
			}
			
			$exist_params = implode(" ", $exist_params);

			$params = str_ireplace($match[0], "rel=\"{$exist_params}\"", $params);

		} else {
			
			$params .= " rel=\"external noopener noreferrer\"";
			
		}
		
		$params = addslashes( $params );

		return "<a {$params}>{$matches[2]}</a>";
		
	}
	
	function remove_rel( $matches=array() ) {
		
		$params = trim( $matches[1] );
		
		if( preg_match( "#rel=['\"](.+?)['\"]#i", $params, $match ) ) {
			
			$remove_params = array("external", "noopener", "noreferrer");
			$new_params = array();
			
			$exist_params = explode(" ", trim($match[1]) );
			
			foreach ($exist_params as $value) {
				if(!in_array( $value, $remove_params ) ) $new_params[] = $value;
			}
			
			if( count($new_params) ) {
				
				$new_params = implode(" ", $new_params);
				$params = str_ireplace($match[0], "rel=\"{$new_params}\"", $params);
				
			} else $params = str_ireplace($match[0], "", $params);
			
			$params = trim($params);
			
			return "<a {$params}>{$matches[2]}</a>";
		
		} else {
			
			return $matches[0];
			
		}
		
	}
	
	function decode_thumb( $matches=array() ) {

		if ($matches[1] == "TBegin") $tag="thumb"; else $tag="medium";
		$txt = $matches[2];

		$txt = stripslashes( $txt );
		$txt = explode("|", $txt );
		$url = $txt[0];
		$align = $txt[1];
		$alt = $txt[2];
		$extra = "";

		if( ! $align and ! $alt ) return "[{$tag}]{$url}[/{$tag}]";

		if( $align ) $extra = $align;
		if( $alt ) {

			$alt = str_replace("&#039;", "'", $alt);
			$alt = str_replace("&quot;", '"', $alt);
			$alt = str_replace("&amp;", '&', $alt);
			$extra .= "|" . $alt;

		}

		return "[{$tag}={$extra}]{$url}[/{$tag}]";

	}

	function decode_oldthumb( $matches=array() ) {

		$txt = $matches[1];

		$align = false;
		$alt = false;
		$extra = "";
		$txt = stripslashes( $txt );

		$url = str_replace( "<a href=\"", "", $txt );
		$url = explode( "\"", $url );
		$url = reset( $url );

		if( strpos( $txt, "align=\"" ) !== false ) {

			$align = preg_replace( "#(.+?)align=\"(.+?)\"(.*)#is", "\\2", $txt );
		}

		if( strpos( $txt, "alt=\"" ) !== false ) {

			$alt = preg_replace( "#(.+?)alt=\"(.+?)\"(.*)#is", "\\2", $txt );
		}

		if( $align != "left" and $align != "right" ) $align = false;

		if( ! $align and ! $alt ) return "[thumb]" . $url . "[/thumb]";

		if( $align ) $extra = $align;
		if( $alt ) {
			$alt = str_replace("&#039;", "'", $alt);
			$alt = str_replace("&quot;", '"', $alt);
			$alt = str_replace("&amp;", '&', $alt);
			$extra .= "|" . $alt;

		}

		return "[thumb=" . $extra . "]" . $url . "[/thumb]";

	}

	function decode_img( $matches=array() ) {

		$img = $matches[1];
		$txt = $matches[2];
		$align = false;
		$alt = false;
		$extra = "";

		if( strpos( $txt, "align=\"" ) !== false ) {

			$align = preg_replace( "#(.+?)align=\"(.+?)\"(.*)#is", "\\2", $txt );
		}

		if( strpos( $txt, "alt=\"\"" ) !== false ) {

			$alt = false;

		} elseif( strpos( $txt, "alt=\"" ) !== false ) {

			$alt = preg_replace( "#(.+?)alt=\"(.+?)\"(.*)#is", "\\2", $txt );
		}

		if( $align != "left" and $align != "right" ) $align = false;

		if( ! $align and ! $alt ) return "[img]" . $img . "[/img]";

		if( $align ) $extra = $align;
		if( $alt ) $extra .= "|" . $alt;

		return "[img=" . $extra . "]" . $img . "[/img]";

	}

	function check_home($url) {
		global $config;

		$url = strtolower(@parse_url($url, PHP_URL_HOST));
		$value = strtolower(@parse_url($config['http_home_url'], PHP_URL_HOST));

		if( !$value ) $value = $_SERVER['HTTP_HOST'];

		if( !$url ) return true;
		
		if( $url != $value ) return false;
		else return true;
	}

	function word_filter($source, $encode = true) {
		global $config;

		if( $encode ) {

			$all_words = @file( ENGINE_DIR . '/data/wordfilter.db.php' );
			$find = array ();
			$replace = array ();

			if( ! $all_words or ! count( $all_words ) ) return $source;

			foreach ( $all_words as $word_line ) {
				$word_arr = explode( "|", $word_line );
				
				$word_arr[1] = str_replace( "&#036;", "$", $word_arr[1] );
				$word_arr[1] = str_replace( "&#123;", "{", $word_arr[1] );
				$word_arr[1] = str_replace( "&#125;", "}", $word_arr[1] );
			
				if( $word_arr[4] ) {

					$register ="";

				} else $register ="i";

				$register .= "u";

				$allow_find = true;

				if ( $word_arr[5] == 1 AND $this->safe_mode ) $allow_find = false;
				if ( $word_arr[5] == 2 AND !$this->safe_mode ) $allow_find = false;

				if ( $allow_find ) {

					if( $word_arr[3] ) {

						$find_text = "#(^|\b|\s|\<br \/\>)" . preg_quote( $word_arr[1], "#" ) . "(\b|\s|!|\?|\.|,|$)#".$register;

						if( $word_arr[2] == "" ) $replace_text = "\\1";
						else $replace_text = "\\1<!--filter:" . $word_arr[1] . "-->" . $word_arr[2] . "<!--/filter-->\\2";

					} else {

						$find_text = "#(" . preg_quote( $word_arr[1], "#" ) . ")#".$register;

						if( $word_arr[2] == "" ) $replace_text = "";
						else $replace_text = "<!--filter:" . $word_arr[1] . "-->" . $word_arr[2] . "<!--/filter-->";

					}

					if ( $word_arr[6] ) {

						if ( preg_match($find_text, $source) ) {

							$this->not_allowed_text = true;
							return $source;

						}

					} else {

						$find[] = $find_text;
						$replace[] = $replace_text;
					}

				}

			}

			if( !count( $find ) ) return $source;

			$source = preg_split( '((>)|(<))', $source, - 1, PREG_SPLIT_DELIM_CAPTURE );
			$count = count( $source );

			for($i = 0; $i < $count; $i ++) {
				if( $source[$i] == "<" or $source[$i] == "[" ) {
					$i ++;
					continue;
				}

				if( $source[$i] != "" ) $source[$i] = preg_replace( $find, $replace, $source[$i] );
			}

			$source = join( "", $source );

		} else {

			$source = preg_replace( "#<!--filter:(.+?)-->(.+?)<!--/filter-->#", "\\1", $source );

		}

		return $source;
	}
	
}

class OEmbed {

	protected $providers = array();

	function __construct(){

		$this->providers['%^(http:|https:)?//(www.)?(youtube.com/watch)%i'] = "https://www.youtube.com/oembed";
		$this->providers['%^(http:|https:)?//(www.)?(youtube.com/playlist)%i'] = "https://www.youtube.com/oembed";
		$this->providers['%^(http:|https:)?//(www.)?(youtu.be/)%i'] = "https://www.youtube.com/oembed";
		$this->providers['%^(http:|https:)?//(www.)?(flickr.com/)%i'] = "https://www.flickr.com/services/oembed/";
		$this->providers['%^(http:|https:)?//(www.)?(flic.kr/)%i'] = "https://www.flickr.com/services/oembed/";
		$this->providers['%^(http:|https:)?//(www.)?(vimeo.com/)%i'] = "https://vimeo.com/api/oembed.json";
		$this->providers['%^(http:|https:)?//(www.)?(player.vimeo.com/)%i'] = "https://vimeo.com/api/oembed.json";
		$this->providers['%^(http:|https:)?//(www.)?(twitter.com/)%i'] = "https://publish.twitter.com/oembed";
		$this->providers['%^(http:|https:)?//(www.)?(docs.com/)%i'] = "https://docs.com/api/oembed";
		$this->providers['%^(http:|https:)?//(www.)?(facebook.com/(.*)/videos/)%i'] = "https://www.facebook.com/plugins/video/oembed.json/";
		$this->providers['%^(http:|https:)?//(www.)?(facebook.com/(.*)/posts/)%i'] = "https://www.facebook.com/plugins/post/oembed.json";
		$this->providers['%^(http:|https:)?//(www.)?(vine.co/)%i'] = "https://vine.co/oembed.json";
		$this->providers['%^(http:|https:)?//(www.)?(gty.im/)%i'] = "http://embed.gettyimages.com/oembed";
		$this->providers['%^(http:|https:)?//(www.)?(codepen.io/)%i'] = "https://codepen.io/api/oembed";
		$this->providers['%^(http:|https:)?//(www.)?(dailymotion.com/video/)%i'] = "https://www.dailymotion.com/services/oembed";
		$this->providers['%^(http:|https:)?//(www.)?(instagram.com/)%i'] = "https://api.instagram.com/oembed/";
		$this->providers['%^(http:|https:)?//(www.)?(instagr.am/)%i'] = "https://api.instagram.com/oembed/";
		$this->providers['%^(http:|https:)?//(www.)?(mixcloud.com/)%i'] = "https://www.mixcloud.com/oembed/";
		$this->providers['%^(http:|https:)?//(www.)?(soundcloud.com/)%i'] = "https://soundcloud.com/oembed";
		$this->providers['%^(http:|https:)?//(www.)?(coub.com/)%i'] = "https://coub.com/api/oembed.json";
		$this->providers['%^(http:|https:)?//(www.)?(ifixit.com/)%i'] = "https://www.ifixit.com/Embed";
		$this->providers['%^(http:|https:)?//(www.)?(icloud.com/keynote/)%i'] = "https://iwmb.icloud.com/iwmb/oembed";
		
    }
    
	
	function fetch($provider_url, $content_url, $args = ""){
	
		$query_string = http_build_query(array('url' => $content_url,'maxwidth' => $args["width"],'maxheight' => $args["height"],'format' => 'json'));	
		
		$result_json = $this->queryProvider($provider_url."?".$query_string);
		
		if($result_json['success']){
		
			$result = json_decode(trim($result_json['data']), false);
			
			if(is_object($result)){
					
				return $result;
				
			}else{
			
				return false;
			}
				
			
		}
		
		return false;
	
	}
	
	function getHtml($url, $args = ""){
	
		$url = trim($url);
		
		foreach ($this->providers as $regex => $provider_url) {
			if(preg_match($regex,$url)){
		    	$provider = $provider_url;
		    	break;
		    }
		}

		if($provider){
		
			if($data = $this->fetch($provider, $url, $args)){

				return $this->toHtml($data, $args);
				
			}else{
			
				return false;
				
			}
		
		}else{
		
			return false;
		}	
	
	}
	
	function toHtml($data, $args){
		global $config;
		
 		if(is_object($data) || !empty($data->type)){
 			
			switch($data->type){
				case 'photo':
					
					if( empty($data->url) ){
						
						return false;
					
					} else {
					
						$title = (!empty($data->title)) ? $data->title : '';
						
						$style = "";
						
						if( $args['width'] ) {
							$style = "style=\"width:100%;max-width:".intval($args['width'])."px;";
							
							if($args['height']) {
								$style .= intval($args['height'])."px;";
							}
							
							$style .= "\"";
							
						}
					
						$html = '<img src="' . $this->escapeHTML($this->safeUrl($data->url)) . '" alt="' . $this->escapeHTML($title) . '" ' . $style . ' />';
					}
					
					break;
					
				case 'video':
				case 'rich':
					$html = ( !empty($data->html) ) ? $data->html : false;
					break;
					
				case 'link':
					$html = ( !empty($data->title) ) ? '<a href="' . $this->safeUrl($data->url) . '">' . $this->escapeHTML($data->title) . '</a>' : false;
					break;
				
				default:
					return false;
			}
			
			return $html;
		
		}else{
		
			return false;	
		
		}

	
	}
	
	function queryProvider($url){
		
		$result = array();
		
		if (stripos($url, "http://") !== 0 AND stripos($url, "https://") !== 0) {
			return false;
		}
	
		$ch = curl_init($url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		
		if($data = curl_exec($ch)){
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
			if($http_code >= 200 && $http_code < 300){
				$result['success'] = true;
				$result['data'] = $data;
				$result['http_code'] = $http_code;
			}else{
				$result['success'] = false;
				$result['http_code'] = $http_code;
				$result['url'] = $url;
			}
			
		}else{
			$result['success'] = false;
			$result['curl_error_code'] = curl_errno($ch);
		};
		
		curl_close($ch);

		return $result;
	}
	
	function safeUrl($url){
		return (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)) ? $url : "";
	}
	
	function escapeHTML($html){
		global $config;
		
		return htmlspecialchars( strip_tags($html), ENT_QUOTES, $config['charset'] );
	}	
}

?>