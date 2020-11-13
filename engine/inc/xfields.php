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
 File: xfields.php
-----------------------------------------------------
 Use: manage extra fields
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if (!isset($xfieldsaction)) $xfieldsaction = $_REQUEST['xfieldsaction'];
if (isset ( $_REQUEST['xfieldssubactionadd'] )) $xfieldssubactionadd = $_REQUEST['xfieldssubactionadd'];
if (isset ( $_REQUEST['xfieldssubaction'] )) $xfieldssubaction = $_REQUEST['xfieldssubaction'];
if (isset ( $_REQUEST['xfieldsindex'] )) $xfieldsindex = intval($_REQUEST['xfieldsindex']);
if (isset ( $_REQUEST['editedxfield'] )) $editedxfield = $_REQUEST['editedxfield'];

if (isset ($xfieldssubactionadd))
if ($xfieldssubactionadd == "add") {
  $xfieldssubaction = $xfieldssubactionadd;
}

if (!isset($xf_inited)) $xf_inited = "";

if ($xf_inited !== true) { // Prevent "Cannot redeclare" error

	function xfieldssave($data) {
		global $lang, $dle_login_hash, $config;
	
		if ($_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash) {
	
			  die("Hacking attempt! User not found");
	
		}
	
	    $data = array_values($data);
		$filecontents = "";
	
	    foreach ($data as $index => $value) {
	      $value = array_values($value);
	      foreach ($value as $index2 => $value2) {
	        $value2 = stripslashes($value2);
	        $value2 = str_replace("|", "&#124;", $value2);
	        $value2 = str_replace("\r\n", "__NEWL__", $value2);
	        $filecontents .= $value2 . ($index2 < count($value) - 1 ? "|" : "");
	      }
	      $filecontents .= ($index < count($data) - 1 ? "\r\n" : "");
	    }
	
	    $filehandle = fopen(ENGINE_DIR.'/data/xfields.txt', "w+");
		
	    if (!$filehandle) msg("error", $lang['xfield_error'], "$lang[xfield_err_1] \"engine/data/xfields.txt\", $lang[xfield_err_2]");
	
		$filecontents = htmlspecialchars($filecontents, ENT_QUOTES, $config['charset'] );
		$filecontents = str_replace("&amp;#124;", "&#124;", $filecontents);

	    fwrite($filehandle, $filecontents);
	    fclose($filehandle);
	
	    header("Location: ?mod=xfields&xfieldsaction=configure");
	    die();
	}

	function clear_js( $txt ) {
	
		if(!$txt) return;
	
		$find = array ('/data:/i','/about:/i','/vbscript:/i','/onclick/i','/onload/i','/onunload/i','/onabort/i','/onerror/i','/onblur/i','/onchange/i','/onfocus/i','/onreset/i','/onsubmit/i','/ondblclick/i','/onkeydown/i','/onkeypress/i','/onkeyup/i','/onmousedown/i','/onmouseup/i','/onmouseover/i','/onmouseout/i','/onselect/i','/javascript/i','/onmouseenter/i','/onwheel/i','/onshow/i','/onafterprint/i','/onbeforeprint/i','/onbeforeunload/i','/onhashchange/i','/onmessage/i','/ononline/i','/onoffline/i','/onpagehide/i','/onpageshow/i','/onpopstate/i','/onresize/i','/onstorage/i','/oncontextmenu/i','/oninvalid/i','/oninput/i','/onsearch/i','/ondrag/i','/ondragend/i','/ondragenter/i','/ondragleave/i','/ondragover/i','/ondragstart/i','/ondrop/i','/onmousemove/i','/onmousewheel/i','/onscroll/i','/oncopy/i','/oncut/i','/onpaste/i','/oncanplay/i','/oncanplaythrough/i','/oncuechange/i','/ondurationchange/i','/onemptied/i','/onended/i','/onloadeddata/i','/onloadedmetadata/i','/onloadstart/i','/onpause/i','/onprogress/i',	'/onratechange/i','/onseeked/i','/onseeking/i','/onstalled/i','/onsuspend/i','/ontimeupdate/i','/onvolumechange/i','/onwaiting/i','/ontoggle/i');
		$replace = array ("d&#1072;ta:", "&#1072;bout:", "vbscript<b></b>:", "&#111;nclick", "&#111;nload", "&#111;nunload", "&#111;nabort", "&#111;nerror", "&#111;nblur", "&#111;nchange", "&#111;nfocus", "&#111;nreset", "&#111;nsubmit", "&#111;ndblclick", "&#111;nkeydown", "&#111;nkeypress", "&#111;nkeyup", "&#111;nmousedown", "&#111;nmouseup", "&#111;nmouseover", "&#111;nmouseout", "&#111;nselect", "j&#1072;vascript", '&#111;nmouseenter', '&#111;nwheel', '&#111;nshow', '&#111;nafterprint','&#111;nbeforeprint','&#111;nbeforeunload','&#111;nhashchange','&#111;nmessage','&#111;nonline','&#111;noffline','&#111;npagehide','&#111;npageshow','&#111;npopstate','&#111;nresize','&#111;nstorage','&#111;ncontextmenu','&#111;ninvalid','&#111;ninput','&#111;nsearch','&#111;ndrag','&#111;ndragend','&#111;ndragenter','&#111;ndragleave','&#111;ndragover','&#111;ndragstart','&#111;ndrop','&#111;nmousemove','&#111;nmousewheel','&#111;nscroll','&#111;ncopy','&#111;ncut','&#111;npaste','&#111;ncanplay','&#111;ncanplaythrough','&#111;ncuechange','&#111;ndurationchange','&#111;nemptied','&#111;nended','&#111;nloadeddata','&#111;nloadedmetadata','&#111;nloadstart','&#111;npause','&#111;nprogress',	'&#111;nratechange','&#111;nseeked','&#111;nseeking','&#111;nstalled','&#111;nsuspend','&#111;ntimeupdate','&#111;nvolumechange','&#111;nwaiting','&#111;ntoggle');
	
		$txt = preg_replace( $find, $replace, $txt );
		$txt = preg_replace( "#<iframe#i", "&lt;iframe", $txt );
		$txt = preg_replace( "#<script#i", "&lt;script", $txt );
		$txt = str_replace( "<?", "&lt;?", $txt );
		$txt = str_replace( "?>", "?&gt;", $txt );
	
		return $txt;
	
	}

	$xf_inited = true;
}

$xfields = xfieldsload();

switch ($xfieldsaction) {
  case "configure":

	if( ! $user_group[$member_id['user_group']]['admin_xfields'] ) {
		msg( "error", $lang['index_denied'], $lang['index_denied'] );
		die();
	}

    switch ($xfieldssubaction) {
      case "delete":
        if (!isset($xfieldsindex)) {
          msg("error", $lang['xfield_error'], $lang['xfield_err_5'],"javascript:history.go(-1)");
        }
		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '73', '{$xfields[$xfieldsindex][0]}')" );

        unset($xfields[$xfieldsindex]);
        @xfieldssave($xfields);
        break;
      case "add":
        $xfieldsindex = count($xfields);

      case "edit":
		
        if (!isset($xfieldsindex)) {
          msg("error", $lang['xfield_error'], $lang['xfield_err_8'],"javascript:history.go(-1)");
        }
    
        if (!$editedxfield) {
			
          $editedxfield = $xfields[$xfieldsindex];
		  
        } elseif (strlen(trim($editedxfield[0])) > 0 AND strlen(trim($editedxfield[1])) > 0) {
			
          foreach ($xfields as $name => $value) {
            if ($name != $xfieldsindex AND  $value[0] == $editedxfield[0]) {
              msg("error", $lang['xfield_error'], $lang['xfield_err_9'],"javascript:history.go(-1)");
            }
          }
		  
          $editedxfield[0] = totranslit(trim($editedxfield[0]));

		  $db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '74', '{$editedxfield[0]}')" );

		  $editedxfield[1] = strip_tags( stripslashes( trim( $editedxfield[1] ) ) );
		  $editedxfield[18] = strip_tags( stripslashes( trim( $editedxfield[18] ) ) );
		  $editedxfield[21] = clear_js($editedxfield[21]);
		  
		  if (!count($editedxfield[2])) $editedxfield[2][0] ="";
		  elseif (count($editedxfield[2]) > 1 AND $editedxfield[2][0] == "") unset($editedxfield[2][0]);

			$category_list = array();
		
			foreach ( $editedxfield[2] as $catval ) {
				if($catval) $category_list[] = intval($catval);
			}

		  $editedxfield[2] 	= implode(',', $category_list);

		  $editedxfield[3] = totranslit(trim($editedxfield[3]));

          if ($editedxfield[3] == "select") {
			
            $options = array();
            foreach (explode("\r\n", $editedxfield["4_select"]) as $name => $value) {
              $value = trim($value);
              if (!in_array($value, $options)) {
                $options[] = $value;
              }
            }
            if (count($options) < 2) {
            msg("error", $lang['xfield_error'], $lang['xfield_err_10'],"javascript:history.go(-1)");
            }
			
            $editedxfield[4] = implode("\r\n", $options);
			
          } else {
			
			if( $editedxfield[3] == "htmljs") {
				$editedxfield[4] = $editedxfield["4_textarea"];
			} else {
				 $editedxfield[4] = $editedxfield["4_{$editedxfield[3]}"];
			}
			
          }

          unset($editedxfield["4_text"], $editedxfield["4_textarea"], $editedxfield["4_select"]);

          if ($editedxfield[3] == "select") {
            $editedxfield[5] = 0;
          } else {
            $editedxfield[5] = ($editedxfield[5] == "on" ? 1 : 0);
          }

          if ($editedxfield[3] == "text" OR $editedxfield[3] == "select" OR $editedxfield[3] == "datetime" ) {
			$editedxfield[6] = ($editedxfield[6] == "on" ? 1 : 0);
          } else $editedxfield[6] = 0;

          if ($editedxfield[3] == "textarea") {
			$editedxfield[7] = ($editedxfield[7] == "on" ? 1 : 0);
          } else $editedxfield[7] = 0;

          if ($editedxfield[3] == "text" OR $editedxfield[3] == "textarea") {
			$editedxfield[8] = ($editedxfield[8] == "on" ? 1 : 0);
          } else $editedxfield[8] = 0;

          if ($editedxfield[3] == "image" OR $editedxfield[3] == "imagegalery") {
			
			$size = explode ("x", $editedxfield[9]);
			
			if ( count($size) == 2 ) {
				$editedxfield[9] = intval($size[0])."x".intval($size[1]);
		    } elseif ( intval($size[0]) > 0 ) {
				$editedxfield[9] = intval($size[0]);
			} else $editedxfield[9] = '';
			
			if( intval($editedxfield[10]) > 0 ) {
				$editedxfield[10] = intval($editedxfield[10]);
			} else $editedxfield[10] = '';
			
			$editedxfield[11] = ($editedxfield[11] == "on" ? 1 : 0);
			$editedxfield[12] = ($editedxfield[12] == "on" ? 1 : 0);

			$size = explode ("x", $editedxfield[13]);
			
			if ( count($size) == 2 ) {
				$editedxfield[13] = intval($size[0])."x".intval($size[1]);
		    } elseif ( intval($size[0]) > 0 ) {
				$editedxfield[13] = intval($size[0]);
			} else $editedxfield[13] = '';

			$size = explode ("x", $editedxfield[22]);
			
			if ( count($size) == 2 ) {
				$editedxfield[22] = intval($size[0])."x".intval($size[1]);
		    } elseif ( intval($size[0]) > 0 ) {
				$editedxfield[22] = intval($size[0]);
			} else $editedxfield[22] = '';

          } else { $editedxfield[11] = 0; $editedxfield[12] = 0; $editedxfield[9] = '';$editedxfield[10] = ''; $editedxfield[13] = ''; $editedxfield[22] = '';}
		  
		  if($editedxfield[3] == "imagegalery") {
			if( intval($editedxfield[16]) > 0 ) {
				$editedxfield[16] = intval($editedxfield[16]);
			} else $editedxfield[16] = 0;
		  } else $editedxfield[16] = '';
		  
          if ($editedxfield[3] == "file" ) {
			
			if ($editedxfield[14]) {
				
				$files_type = explode (",", $editedxfield[14]);
				$items = array();
				
				foreach ($files_type as $item) {
					$items[] = totranslit(trim($item), true, false);
				}
				
				$editedxfield[14] = implode(",", $items);
		    }

			if( intval($editedxfield[15]) > 0 ) {
				$editedxfield[15] = intval($editedxfield[15]);
			} else $editedxfield[15] = '';
			
		  } else { $editedxfield[14] = ''; $editedxfield[15] = '';}
		  
		  if($editedxfield[3] == "yesorno") {
			if( intval($editedxfield[17]) > 0 ) {
				$editedxfield[17] = 1;
			} else $editedxfield[17] = 0;
		  } else $editedxfield[17] = '';

		  if (!count($editedxfield[19])) $editedxfield[19][0] ="";
		  elseif (count($editedxfield[19]) > 1 AND $editedxfield[19][0] == "") unset($editedxfield[19][0]);

		  $list = array();
		  
		  if(count($editedxfield[19])) {		
			foreach ( $editedxfield[19] as $val ) {
			   if($val) $list[] = intval($val);
			}
		  }
		  
		  $editedxfield[19] = implode(',', $list);

		  if (!count($editedxfield[20])) $editedxfield[20][0] ="";
		  elseif (count($editedxfield[20]) > 1 AND $editedxfield[20][0] == "") unset($editedxfield[20][0]);

		  $list = array();
		  
		  if(count($editedxfield[20])) {
			  foreach ( $editedxfield[20] as $val ) {
				 if($val) $list[] = intval($val);
			  }
		  }
		  
		  $editedxfield[20] = implode(',', $list);

		  if($editedxfield[3] == "datetime") {
			$editedxfield[23] = intval($editedxfield[23]);
			$editedxfield[24] = strip_tags( stripslashes( trim( $editedxfield[24] ) ) );
			$editedxfield[25] = ($editedxfield[25] == "on" ? 1 : 0);
			$editedxfield[26] = ($editedxfield[26] == "on" ? 1 : 0);
			
		  } else { $editedxfield[23] = ''; $editedxfield[24] = ''; $editedxfield[25] = ''; $editedxfield[26] = ''; }
 
          ksort($editedxfield);
          
          $xfields[$xfieldsindex] = $editedxfield;
          ksort($xfields);
		  
          @xfieldssave($xfields);
          break;
        } else {
          msg("error", $lang['xfield_error'], $lang['xfield_err_11'],"javascript:history.go(-1)");
        }

        echoheader( "<i class=\"fa fa-list position-left\"></i><span class=\"text-semibold\">{$lang['header_nf_1']}</span>", $lang['header_nf_2'] );
        $checked = ($editedxfield[5] ? " checked" : "");
        $checked2 = ($editedxfield[6] ? " checked" : "");
        $checked3 = ($editedxfield[7] ? " checked" : "");
        $checked4 = ($editedxfield[8] ? " checked" : "");
		$checked11 = ($editedxfield[11] ? " checked" : "");
		$checked12 = ($editedxfield[12] ? " checked" : "");
		$checked13 = ($editedxfield[25] ? " checked" : "");
		$checked14 = ($editedxfield[26] ? " checked" : "");
?>
    <form method="post" name="xfieldsform" class="form-horizontal">
      <script language="javascript">
      function ShowOrHideEx(id, show) {
        var item = null;
        if (document.getElementById) {
          item = document.getElementById(id);
        } else if (document.all) {
          item = document.all[id];
        } else if (document.layers){
          item = document.layers[id];
        }
        if (item && item.style) {
          item.style.display = show ? "" : "none";
        }
      }
      function onTypeChange(value) {
        ShowOrHideEx("default_text", value == "text");
        ShowOrHideEx("optional2", value == "text" || value == "select" || value == "datetime");
        ShowOrHideEx("optional7", value == "text" || value == "select");
        ShowOrHideEx("default_textarea", value == "textarea" || value == "htmljs");
        ShowOrHideEx("optional3", value == "textarea");
        ShowOrHideEx("optional4", value == "text" || value == "textarea");
        ShowOrHideEx("select_options", value == "select");
        ShowOrHideEx("optional", value != "select" && value != "yesorno");
        ShowOrHideEx("default_image", value == "image" || value == "imagegalery");
		ShowOrHideEx("optional5", value == "imagegalery");
		ShowOrHideEx("optional6", value == "yesorno");
		ShowOrHideEx("optional8", value == "datetime");
		ShowOrHideEx("optional9", value == "datetime");
		ShowOrHideEx("default_file", value == "file");
		ShowOrHideEx("default_htmljs", value == "htmljs");
      }
      function onCategoryChange(value) {
        ShowOrHideEx("category_custom", value == "custom");
      }
      </script>
      <input type="hidden" name="mod" value="xfields">
	  <input type="hidden" name="user_hash" value="<?php echo $dle_login_hash; ?>">
      <input type="hidden" name="xfieldsaction" value="configure">
      <input type="hidden" name="xfieldssubaction" value="edit">
      <input type="hidden" name="xfieldsindex" value="<?php echo $xfieldsindex; ?>">
<div class="panel panel-default">
  <div class="panel-heading">
    <?php echo $lang['xfield_title']; ?>
  </div>
  <div class="panel-body">

		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xname']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-200" maxlength="30" type="text" name="editedxfield[0]" value="<?php echo htmlspecialchars($editedxfield[0], ENT_QUOTES, $config['charset'] );?>" /><span class="text-muted text-size-small"><i class="fa fa-exclamation-circle position-left position-right"></i><?php echo $lang['xf_lat']; ?></span>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xdescr']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-400" maxlength="100" type="text" name="editedxfield[1]" value="<?php echo htmlspecialchars($editedxfield[1], ENT_QUOTES, $config['charset'] );?>" />
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_hint']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-400" maxlength="200" type="text" name="editedxfield[18]" value="<?php echo htmlspecialchars($editedxfield[18], ENT_QUOTES, $config['charset'] );?>" placeholder="<?php echo $lang['xfield_hint_1']; ?>" />
		  </div>
		 </div>
		  
<?php
        $cat_options = CategoryNewsSelection(explode (',', $editedxfield[2]), 0, FALSE);
		if ($editedxfield[2] == "") $cats_value = "selected"; else $cats_value = "";
		
		$groups_add = get_groups( explode( ',', $editedxfield[19] ) );
		if ($editedxfield[19] == "") $groups_add_value = "selected"; else $groups_add_value = "";
		
		$groups_view = get_groups( explode( ',', $editedxfield[20] ) );
		if ($editedxfield[20] == "") $groups_view_value = "selected"; else $groups_view_value = "";

echo <<<HTML
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['xfield_xcat']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="editedxfield[2][]" id="category" class="categoryselect" data-placeholder="{$lang['addnews_cat_sel']}" style="width:350px;;height:100px;" multiple><option value="" {$cats_value}>{$lang['xfield_xall']}</option>{$cat_options}</select>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['xf_group_add']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="editedxfield[19][]" id="groups_add" class="categoryselect" data-placeholder="{$lang['group_select_1']}" style="width:350px;;height:100px;" multiple><option value="" {$groups_add_value}>{$lang['xfield_xall']}</option>{$groups_add}</select>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3">{$lang['xf_group_view']}</label>
		  <div class="col-md-10 col-sm-9">
			<select name="editedxfield[20][]" id="groups_view" class="categoryselect" data-placeholder="{$lang['group_select_1']}" style="width:350px;;height:100px;" multiple><option value="" {$groups_view_value}>{$lang['xfield_xall']}</option>{$groups_view}</select>
		  </div>
		 </div>	
HTML;

?>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xtype']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<select class="uniform" name="editedxfield[3]" id="type" onchange="onTypeChange(this.value);">
          <option value="text"<?php if($editedxfield[3] != "textarea") echo " selected"; else echo "";?>><?php echo $lang['xfield_xstr']; ?></option>
          <option value="textarea"<?php echo ($editedxfield[3] == "textarea") ? " selected" : "";?>><?php echo $lang['xfield_xarea']; ?></option>
		  <option value="htmljs"<?php echo ($editedxfield[3] == "htmljs") ? " selected" : "";?>><?php echo $lang['xfield_xhtmljs']; ?></option>
          <option value="select"<?php echo ($editedxfield[3] == "select") ? " selected" : "";?>><?php echo $lang['xfield_xsel']; ?></option>
          <option value="image"<?php echo ($editedxfield[3] == "image") ? " selected" : "";?>><?php echo $lang['xfield_ximage']; ?></option>
          <option value="imagegalery"<?php echo ($editedxfield[3] == "imagegalery") ? " selected" : "";?>><?php echo $lang['xfield_ximagegalery']; ?></option>
          <option value="file"<?php echo ($editedxfield[3] == "file") ? " selected" : "";?>><?php echo $lang['xfield_xfile']; ?></option>
          <option value="yesorno"<?php echo ($editedxfield[3] == "yesorno") ? " selected" : "";?>><?php echo $lang['xfield_xyesorno']; ?></option>
		  <option value="datetime"<?php echo ($editedxfield[3] == "datetime") ? " selected" : "";?>><?php echo $lang['xfield_xdatetime']; ?></option>
        </select>
		  </div>
		 </div>		 
		<div class="form-group" id="default_text">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xfaul']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-400" type="text" name="editedxfield[4_text]" value="<?php if ($editedxfield[3] == "text") echo htmlspecialchars($editedxfield[4], ENT_QUOTES, $config['charset'] ); else echo ""; ?>" />
		  </div>
		 </div>	
		<div class="form-group" id="default_textarea">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xfaul']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<textarea class="classic" style="width:100%;max-width: 400px;height: 100px;" name="editedxfield[4_textarea]"><?php echo ($editedxfield[3] == "textarea" OR $editedxfield[3] == "htmljs") ? htmlspecialchars($editedxfield[4], ENT_QUOTES, $config['charset'] ) : "";?></textarea><div id="default_htmljs" class="text-muted text-size-small"><?php echo $lang['xfield_xhtmljs_1']; ?></div>
		  </div>
		 </div>	
		<div class="form-group" id="select_options">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xfaul']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<textarea class="classic" style="width:100%;max-width: 400px; height: 100px;" name="editedxfield[4_select]"><?php if ($editedxfield[4]{0} == "\r") $editedxfield[4] = "\n".$editedxfield[4]; echo ($editedxfield[3] == "select") ? htmlspecialchars($editedxfield[4], ENT_QUOTES, $config['charset'] ) : "";?></textarea><div class="text-muted text-size-small"><?php echo $lang['xfield_xfsel']; ?></div>
		  </div>
		 </div>
		
		<div id="default_image">
			<div class="form-group">
			  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['opt_sys_minside']; ?></label>
			  <div class="col-md-10 col-sm-9">
				<input class="form-control text-center" style="width:100%;max-width: 100px;" type="text" name="editedxfield[22]" value="<?php echo htmlspecialchars($editedxfield[22], ENT_QUOTES, $config['charset']); ?>" /><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xfield_xi22']; ?>" ></i>
			  </div>
			</div>
			<div class="form-group">
			  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xi1']; ?></label>
			  <div class="col-md-10 col-sm-9">
				<input class="form-control text-center" style="width:100%;max-width: 100px;" type="text" name="editedxfield[9]" value="<?php echo htmlspecialchars($editedxfield[9], ENT_QUOTES, $config['charset']); ?>" /><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xfield_xi2']; ?>" ></i>
			  </div>
			</div>
			<div class="form-group">
			  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xi3']; ?></label>
			  <div class="col-md-10 col-sm-9">
				<input class="form-control text-center" style="width:100%;max-width: 100px;" type="text" name="editedxfield[10]" value="<?php echo htmlspecialchars($editedxfield[10], ENT_QUOTES, $config['charset']); ?>" /><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xfield_xi4']; ?>" ></i>
			  </div>
			</div>
			
			<div id="optional5" class="form-group">
			  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xi9']; ?></label>
			  <div class="col-md-10 col-sm-9">
				<input class="form-control text-center" style="width:100%;max-width: 100px;" type="text" name="editedxfield[16]" value="<?php echo htmlspecialchars($editedxfield[16], ENT_QUOTES, $config['charset']); ?>" /><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xfield_xi10']; ?>" ></i>
			  </div>
			</div>
			
			<div class="form-group">
			  <label class="control-label col-md-2 col-sm-3"></label>
			  <div class="col-md-10 col-sm-9">
				 <div class="checkbox"><label><input  class="icheck" type="checkbox" name="editedxfield[11]"<?php echo $checked11; ?> id="editx11" /><?php echo $lang['xfield_xi5']; ?></label></div>
			  </div>
			</div>
			<div class="form-group">
			  <label class="control-label col-md-2 col-sm-3"></label>
			  <div class="col-md-10 col-sm-9">
				 <div class="checkbox"><label><input  class="icheck" type="checkbox" name="editedxfield[12]"<?php echo $checked12; ?> id="editx12" /><?php echo $lang['xfield_xi6']; ?></label></div>
			  </div>
			</div>
			<div class="form-group">
			  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xi7']; ?></label>
			  <div class="col-md-10 col-sm-9">
				<input class="form-control text-center" style="width:100%;max-width: 100px;" type="text" name="editedxfield[13]" value="<?php echo htmlspecialchars($editedxfield[13], ENT_QUOTES, $config['charset']); ?>" /><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xfield_xi8']; ?>" ></i>
			  </div>
			</div>
		</div>
	
		<div id="default_file">
			<div class="form-group">
			  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xf1']; ?></label>
			  <div class="col-md-10 col-sm-9">
				<input class="form-control width-350" type="text" name="editedxfield[14]" value="<?php echo htmlspecialchars($editedxfield[14], ENT_QUOTES, $config['charset']); ?>" /><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xfield_xf2']; ?>" ></i>
			  </div>
			</div>
			<div class="form-group">
			  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['opt_sys_maxfile']; ?></label>
			  <div class="col-md-10 col-sm-9">
				<input class="form-control text-center" style="width:100%;max-width: 100px;" type="text" name="editedxfield[15]" value="<?php echo htmlspecialchars($editedxfield[15], ENT_QUOTES, $config['charset']); ?>" /><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['opt_sys_maxfiled']; ?>" ></i>
			  </div>
			</div>
		</div>

		<div id="optional6" class="form-group">
			<label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xfaul']; ?></label>
			<div class="col-md-10 col-sm-9">
				<select class="uniform" name="editedxfield[17]">
					 <option value="0"<?php if(!$editedxfield[17]) echo " selected"; else echo "";?>><?php echo $lang['xfsel_off']; ?></option>
					 <option value="1"<?php if($editedxfield[17]) echo " selected"; else echo "";?>><?php echo $lang['xfsel_on']; ?></option>
				</select>
			</div>
		</div>

		<div id="optional8">
			<div class="form-group">
				<label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xinput']; ?></label>
				<div class="col-md-10 col-sm-9">
					<select class="uniform" name="editedxfield[23]">
						 <option value="0"<?php if(!$editedxfield[23]) echo " selected"; else echo "";?>><?php echo $lang['xfield_xdatetime']; ?></option>
						 <option value="1"<?php if($editedxfield[23] == 1) echo " selected"; else echo "";?>><?php echo $lang['xfsel_date']; ?></option>
						 <option value="2"<?php if($editedxfield[23] == 2) echo " selected"; else echo "";?>><?php echo $lang['xfsel_time']; ?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
			  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xoutput']; ?></label>
			  <div class="col-md-10 col-sm-9">
				<input class="form-control" style="width:100%;max-width: 200px;" type="text" name="editedxfield[24]" value="<?php echo htmlspecialchars($editedxfield[24], ENT_QUOTES, $config['charset']); ?>" /> <a onclick="javascript:Help('date'); return false;" href="#"><?php echo $lang['opt_sys_and']; ?></a>
			  </div>
			</div>
		</div>
	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"></label>
		  <div class="col-md-10 col-sm-9">
			<div id="optional">
				<div class="checkbox"><label><input class="icheck" type="checkbox" name="editedxfield[5]"<?php echo $checked; ?> id="editxfive" /><?php echo $lang['xfield_xw']; ?></label></div>
			</div>
			
			<div id="optional9">
				<div class="checkbox"><label><input class="icheck" type="checkbox" name="editedxfield[25]"<?php echo $checked13; ?> /><?php echo $lang['xfield_xlocaldate']; ?><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xfield_xhelplocal']; ?>" ></i></label></div>
				<div class="checkbox"><label><input class="icheck" type="checkbox" name="editedxfield[26]"<?php echo $checked14; ?> /><?php echo $lang['xfield_xdecldate']; ?><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xfield_xhelpdec']; ?>" ></i></label></div>
			</div>
			
			<div id="optional4">
				<div class="checkbox display-inline-block"><label><input  class="icheck" type="checkbox" name="editedxfield[8]"<?php echo $checked4; ?> id="editx8" /><?php echo $lang['opt_sys_sxfield']; ?></label></div><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['opt_sys_sxfieldd']; ?>" ></i>
			</div>
			<div id="optional3">
				  <div class="checkbox"><label><input  class="icheck" type="checkbox" name="editedxfield[7]"<?php echo $checked3; ?> id="editx7" /><?php echo $lang['xfield_xw4']; ?></label></div>
			</div>
			<div id="optional2">
				<div class="checkbox display-inline-block"><label><input class="icheck" type="checkbox" name="editedxfield[6]"<?php echo $checked2; ?> id="editxsixt" /><?php echo $lang['xfield_xw2']; ?></label></div><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xfield_xw3']; ?>" ></i>
			</div>
		  </div>
		 </div>			 

		<div id="optional7" class="form-group">
			<label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_separator']; ?></label>
			<div class="col-md-10 col-sm-9">
				<input class="form-control width-300" type="text" name="editedxfield[21]" value="<?php echo htmlspecialchars($editedxfield[21], ENT_QUOTES, $config['charset']); ?>" /><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xfield_separator_1']; ?>" ></i>
			</div>
		</div>

   </div>
<div class="panel-footer">
	<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i><?php echo $lang['user_save']; ?></button>
</div>
</div>
<script>
$(function(){
	$('.categoryselect').chosen({allow_single_deselect:true, no_results_text: '<?php echo $lang['addnews_cat_fault'] ?>'});
});
</script>
</form>
    <script>
    <!--
      var item_type = document.getElementById("type");
      var item_category = document.getElementById("category");

      if (item_type) {
        onTypeChange(item_type.value);
        onCategoryChange(item_category.value);
      }
    // -->
    </script>
<?php
        echofooter();
        break;

      default:

        echoheader( "<i class=\"fa fa-list position-left\"></i><span class=\"text-semibold\">{$lang['header_nf_1']}</span>", $lang['header_nf_2'] );
?>
<form  method="get" name="xfieldsform">
<input type="hidden" name="mod" value="xfields">
<input type="hidden" name="xfieldsaction" value="configure">
<input type="hidden" name="xfieldssubactionadd" value="">
<input type="hidden" name="user_hash" value="<?php echo $dle_login_hash; ?>">
<div class="panel panel-default">
  <div class="panel-heading">
    <?php echo $lang['xfield_xlist']; ?>
  </div>
  <div class="panel-body">

<?php
        if (count($xfields) == 0) {

          echo "<center><br />{$lang['xfield_xnof']}<br /><br /></center>";

        } else {

			$x_list = "<ol class=\"dd-list\">";
	
			foreach ($xfields as $name => $value) {
	
				$cats_v = trim($value[2]) ? $value[2] : $lang['xfield_xall'];
	
				if ( $value[3] == "text" ) $type=$lang['xfield_xstr'];
				elseif($value[3] == "textarea") $type=$lang['xfield_xarea'];
				elseif($value[3] == "select") $type=$lang['xfield_xsel'];
				elseif($value[3] == "image") $type=$lang['xfield_ximage'];
				elseif($value[3] == "imagegalery") $type=$lang['xfield_ximagegalery'];
				elseif($value[3] == "file") $type=$lang['xfield_xfile'];
				elseif($value[3] == "yesorno") $type=$lang['xfield_xyesorno'];
				elseif($value[3] == "htmljs") $type=$lang['xfield_xhtmljs'];
				elseif($value[3] == "datetime") $type=$lang['xfield_xdatetime'];
				
				$req = $value[5] != 0 ? $lang['opt_sys_yes'] : $lang['opt_sys_no'];
	
				$x_list .= "<li class=\"dd-item\" data-id=\"{$name}\"><div class=\"dd-handle\"></div><div class=\"dd-content\"><b id=\"x_name\" class=\"s-el\">{$value[0]}</b><b id=\"x_cats\" class=\"s-el\">{$lang['xfield_xcat']}: {$cats_v}</b><b id=\"x_type\" class=\"s-el\">{$type}</b><b class=\"s-el\">{$lang['xfield_xwt']}: {$req}</b><div style=\"float:right;\"><a href=\"?mod=xfields&xfieldsaction=configure&xfieldssubaction=edit&xfieldsindex={$name}&user_hash={$dle_login_hash}\"><i title=\"{$lang['cat_ed']}\" alt=\"{$lang['cat_ed']}\" class=\"fa fa-pencil-square-o position-left\"></i></a><a href=\"javascript:xfdelete('{$name}');\"><i title=\"{$lang['cat_del']}\" alt=\"{$lang['cat_del']}\" class=\"fa fa-trash-o position-right text-danger\"></i></a></div></div></li>";
	
			}

			$x_list .= "</ol>";
			echo "<div class=\"dd\" id=\"nestable\">{$x_list}</div>";


        }
?>
	
   </div>
	<div class="panel-footer">
		<div class="pull-left">
		<input type="submit" class="btn bg-teal btn-sm btn-raised" value=" <?php echo $lang['b_create']; ?> " onclick="document.forms['xfieldsform'].xfieldssubactionadd.value = 'add';">
		</div>
		<div class="pull-right">
		<a onclick="javascript:Help('xfields'); return false;" href="#"><?php echo $lang['xfield_xhelp']; ?></a>
		</div>
	</div>
</div>
  </form>
<script>
	jQuery(function($){

		$('.dd').nestable({
			maxDepth: 1
		});
		
		$('.dd-handle a').on('mousedown', function(e){
			e.stopPropagation();
		});
		
		$('.dd-handle a').on('touchstart', function(e){
			e.stopPropagation();
		});

		$('#nestable').nestable().on('change',function(){
			var xfsort =  window.JSON.stringify($('.dd').nestable('serialize'));
			var url = "action=xfsort&user_hash=<?php echo $dle_login_hash; ?>&list="+xfsort;

			ShowLoading('');
			$.post('engine/ajax/controller.php?mod=adminfunction', url, function(data){
	
				HideLoading('');
	
				if (data != 'ok') {

					DLEalert('<?php echo $lang['cat_sort_fail']; ?>', '<?php echo $lang['p_info']; ?>');

				}
	
			});

			return false;

		});

	});
	function xfdelete(id){
		
	    DLEconfirm( '<?php echo $lang['xfield_err_6']; ?>', '<?php echo $lang['p_confirm']; ?>', function () {
			document.location='?mod=xfields&xfieldsaction=configure&xfieldsindex=' + id +'&xfieldssubaction=delete&user_hash=<?php echo $dle_login_hash; ?>';
		} );
	}
</script>
<?php
      echofooter();
    }
    break;

case "list":
    $output = "";
	$xfieldinput = array();
	
    if (!isset($xfieldsid)) $xfieldsid = "";
    $xfieldsdata = xfieldsdataload ($xfieldsid);
	
    foreach ($xfields as $name => $value) {
		
	  $value[0]  = totranslit(trim($value[0]));
	  $fieldname = $value[0];
	  
	  if( $value[19] ) {
		
		$value[19] = explode( ',', $value[19] );
		
		if( $value[19][0] AND !in_array( $member_id['user_group'], $value[19] ) ) {
			continue;
		}
		
	  }

	  $fieldcount = md5($fieldname);
	  
	  $value[1] = htmlspecialchars($value[1], ENT_QUOTES, $config['charset'] );
	  $value[18] = htmlspecialchars($value[18], ENT_QUOTES, $config['charset'] );
  
      if ( !$xfieldsadd ) {
	
        $fieldvalue = $xfieldsdata[$value[0]];

		if ( $xfieldmode == "site" ) $ed_mode = $config['allow_site_wysiwyg']; else $ed_mode = $config['allow_admin_wysiwyg'];
		
		$fieldvalue = str_ireplace( "&#123;title", "{title", $fieldvalue );
		$fieldvalue = str_ireplace( "&#123;short-story", "{short-story", $fieldvalue );
		$fieldvalue = str_ireplace( "&#123;full-story", "{full-story", $fieldvalue );
		
		if ($value[8] OR $value[6] OR $value[3] == "image" OR $value[3] == "imagegalery" OR $value[3] == "file") {
			
			$fieldvalue = str_replace( "&#44;", "&amp;#44;", $fieldvalue );
			$fieldvalue = html_entity_decode(stripslashes($fieldvalue), ENT_QUOTES, $config['charset']);
			$fieldvalue = htmlspecialchars($fieldvalue, ENT_QUOTES, $config['charset'] );
			
		} elseif($value[3] == "htmljs") {
			
			 $fieldvalue = htmlspecialchars($fieldvalue, ENT_QUOTES, $config['charset'] );
			 
		} elseif($value[3] == "datetime") {

			if ($fieldvalue) {
				
				$fieldvalue = str_replace( "&#58;", ":", $fieldvalue );
				$fieldvalue = @strtotime( $fieldvalue );
				
				if( $fieldvalue !== - 1 AND $fieldvalue ) {
					
					if( $value[23] == 1 ) $fieldvalue = date( "Y-m-d", $fieldvalue );
					elseif( $value[23] == 2 ) $fieldvalue = date( "H:i", $fieldvalue );
					else $fieldvalue = date( "Y-m-d H:i", $fieldvalue );
					
				} else $fieldvalue = "";
				
			}
			 
		} else {
			
			if ($row['allow_br'] AND !$ed_mode ) $fieldvalue = $parse->decodeBBCodes($fieldvalue, false);
			else $fieldvalue = $parse->decodeBBCodes($fieldvalue, true, $ed_mode);

		}


      } elseif ($value[3] != "select" AND $value[3] != "image" AND $value[3] != "imagegalery" AND $value[3] != "file" AND $value[3] != "yesorno" ) {
			
        $fieldvalue = htmlspecialchars($value[4], ENT_QUOTES, $config['charset'] );
		
      } else $fieldvalue = '';

      $holderid = "xfield_holder_$fieldname";

	  if ($xfieldmode == "site") {
		
		if ($value[18]) $value[18] = "<div class=\"xfieldsnote\">{$value[18]}</div>";
		
	  } else {

		if ($value[18]) {
			
			$help_text = $value[18];
			$value[18] = "<i class=\"help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left\" data-rel=\"popover\" data-trigger=\"hover\" data-placement=\"right\" data-content=\"{$value[18]}\" ></i>";
			
		} 
	  }
	  
      if ($value[3] == "textarea") {      

		$params = "";
		$panel = "<!--panel-->";
		$bb_pref = "";
		$bb_suff = "";
		$noborder="";
		
		if ( $value[7] ) {
			
			if ($bb_editor) {
				$params = "onfocus=\"setFieldName(this.id)\" class=\"editor\" ";
				$bb_pref = "<div class=\"shadow-depth1\">";
				$bb_suff = "</div>";
			} else $params = "class=\"wysiwygeditor\" ";
			
		} else {
			
			$panel = "";
			$params = "class=\"classic\" ";
			$noborder=" no-border";
			
		}

		if (!$value[5]) { 
			$uid = "uid=\"essential\" ";
			$params .= "rel=\"essential\" ";
		} else { 
			$uid = "";
		}
			
		$fid = preg_replace( '#[\-]+#i', '_', $fieldname );
		
		if ($xfieldmode == "site") {
			
			if ( $value[7] ) {
				
				if ( $bb_editor ) $class_name = "bb-editor"; else $class_name = "wseditor";

	        $output .= <<<HTML
<tr id="$holderid" {$uid}>
<td class="xfields" colspan="2">{$value[1]}: [not-optional]<span style="color:red;">*</span>[/not-optional]<div class="{$class_name}">{$panel}<textarea name="xfield[$fieldname]" id="xf_$fid" {$params}>$fieldvalue</textarea>{$value[18]}</div></td></tr>
HTML;

			$xfieldinput[$fieldname] = "<div class=\"{$class_name}\">{$panel}<textarea name=\"xfield[$fieldname]\" id=\"xf_$fid\" {$params}>$fieldvalue</textarea></div>";

			} else {
			
	        $output .= <<<HTML
<tr id="$holderid" {$uid}>
<td class="xfieldsdescr">$value[1]: [not-optional]<span style="color:red;">*</span>[/not-optional]</td>
<td class="xfields"><textarea name="xfield[$fieldname]" id="xf_$fid" {$params}>{$fieldvalue}</textarea>{$value[18]}</td></tr>
HTML;

			$xfieldinput[$fieldname] = "<textarea name=\"xfield[$fieldname]\" id=\"xf_$fid\" {$params}>{$fieldvalue}</textarea>";

			}


		} else {

	        $output .= <<<HTML
<div id="$holderid" class="form-group editor-group" {$uid}>
  <label class="control-label col-md-2">{$value[1]}: [not-optional]<span style="color:red;">*</span>[/not-optional]{$value[18]}</label>
  <div class="col-md-10">
     <div class="editor-panel{$noborder}">{$bb_pref}{$panel}<textarea style="width:100%;height:200px;" name="xfield[$fieldname]" id="xf_$fid" {$params}>{$fieldvalue}</textarea>{$bb_suff}</div>
  </div>
</div>
HTML;

		}
		
      } elseif ($value[3] == "htmljs") {

		$params = "";

		if (!$value[5]) { 
			$uid = "uid=\"essential\" ";
			$params .= "rel=\"essential\" ";
		} else { 
			$uid = "";
		}

		if ($xfieldmode == "site") {
			
	        $output .= <<<HTML
<tr id="$holderid" {$uid}>
<td class="xfieldsdescr">$value[1]: [not-optional]<span style="color:red;">*</span>[/not-optional]</td>
<td class="xfields"><textarea name="xfield[$fieldname]" id="xf_$fieldname" {$params}>{$fieldvalue}</textarea>{$value[18]}</td></tr>
HTML;

			$xfieldinput[$fieldname] = "<textarea name=\"xfield[$fieldname]\" id=\"xf_$fieldname\" {$params}>{$fieldvalue}</textarea>";


		} else {

	        $output .= <<<HTML
<div id="$holderid" class="form-group editor-group" {$uid}>
  <label class="control-label col-md-2">{$value[1]}: [not-optional]<span style="color:red;">*</span>[/not-optional]{$value[18]}</label>
  <div class="col-md-10">
     <textarea class="classic" style="width:100%;height:200px;max-width: 950px;" name="xfield[$fieldname]" id="xf_$fieldname" {$params}>{$fieldvalue}</textarea>
  </div>
</div>
HTML;

		}
		
      } elseif ($value[3] == "text") {

		if (!$value[5]) { 
			$params = "rel=\"essential\" "; 
			$uid = "uid=\"essential\" "; 

		} else { 

			$params = ""; 
			$uid = "";

		}

		if ($value[6]) {
			$params .= "data-rel=\"links\" "; 
		}

		if ($xfieldmode == "site") {
		
$output .= <<<HTML
<tr id="$holderid" {$uid}>
<td class="addnews">$value[1]: [not-optional]<span style="color:red;">*</span>[/not-optional]</td>
<td class="xfields"><input type="text" name="xfield[$fieldname]" id="xf_$fieldname" value="$fieldvalue" {$params}/>{$value[18]}</td>
</tr>
HTML;

			$xfieldinput[$fieldname] = "<input type=\"text\" name=\"xfield[$fieldname]\" id=\"xf_$fieldname\" value=\"$fieldvalue\" {$params}/>";


		} else {
		
$output .= <<<HTML
<div id="$holderid" class="form-group" {$uid}>
  <label class="control-label col-sm-2">{$value[1]}: [not-optional]<span style="color:red;">*</span>[/not-optional]</label>
  <div class="col-sm-10">
     <input type="text" class="form-control width-500" name="xfield[$fieldname]" id="xf_$fieldname" value="$fieldvalue" {$params}/> {$value[18]}
  </div>
</div>
HTML;

		}
		
      } elseif ($value[3] == "datetime") {
		
		if (!$value[5]) { 
			$params = "rel=\"essential\" "; 
			$uid = "uid=\"essential\" "; 

		} else { 

			$params = ""; 
			$uid = "";

		}

		
		if ($value[23] == 1 ) {
			$params .= "data-rel=\"calendardate\" "; 
		} elseif($value[23] == 2) {
			$params .= "data-rel=\"calendartime\" "; 
		} else {
			$params .= "data-rel=\"calendardatetime\" ";
		}
		
		if ($xfieldmode == "site") {
		
$output .= <<<HTML
<tr id="$holderid" {$uid}>
<td class="addnews">$value[1]: [not-optional]<span style="color:red;">*</span>[/not-optional]</td>
<td class="xfields"><input type="text" name="xfield[$fieldname]" id="xf_$fieldname" value="$fieldvalue" {$params}/>{$value[18]}</td>
</tr>
HTML;

			$xfieldinput[$fieldname] = "<input type=\"text\" name=\"xfield[$fieldname]\" id=\"xf_$fieldname\" value=\"$fieldvalue\" {$params}/>";


		} else {
		
$output .= <<<HTML
<div id="$holderid" class="form-group" {$uid}>
  <label class="control-label col-sm-2">{$value[1]}: [not-optional]<span style="color:red;">*</span>[/not-optional]</label>
  <div class="col-sm-10">
     <input type="text" class="form-control" style="width:200px;" name="xfield[$fieldname]" id="xf_$fieldname" value="$fieldvalue" {$params}/> {$value[18]}
  </div>
</div>
HTML;

		}
		
		
      } elseif ($value[3] == "select") {
		
		if ($xfieldmode == "site") {
			$select = "<select name=\"xfield[$fieldname]\">";
		} else {
			$select = "<select class=\"uniform\" name=\"xfield[$fieldname]\">";
		}
		
		if ( !isset($fieldvalue) ) $fieldvalue = "";

		$fieldvalue = str_replace('&amp;', '&', $fieldvalue);

        foreach (explode("\r\n", htmlspecialchars($value[4], ENT_QUOTES, $config['charset'] )) as $index1 => $value1) {

		  $value1 = explode("|", $value1);
		  if( count($value1) < 2) $value1[1] = $value1[0];
          $select .= "<option value=\"$index1\"" . ($fieldvalue == $value1[0] ? " selected" : "") . ">{$value1[1]}</option>\r\n";
        }

		$select .= "</select>";
	  
		if ($xfieldmode == "site") {

			$output .= <<<HTML
<tr id="$holderid">
<td class="addnews">$value[1]:</td>
<td class="xfields">{$select} {$value[18]}</td>
</tr>
HTML;

		$xfieldinput[$fieldname] = $select;

		} else {

			$output .= <<<HTML
<div id="$holderid" class="form-group">
  <label class="control-label col-sm-2">{$value[1]}:</label>
  <div class="col-sm-10">{$select} {$value[18]}
  </div>
</div>
HTML;
		}
		
	  } elseif( $value[3] == "yesorno" ) {

		if ( !isset($fieldvalue) OR $fieldvalue === '') $fieldvalue = $value[17];

		$fieldvalue = intval($fieldvalue);
		
		if ($xfieldmode == "site") {

			$select = "<select name=\"xfield[$fieldname]\">";
            $select .= "<option value=\"1\"" . ($fieldvalue == 1 ? " selected" : "") . ">{$lang['xfield_xyes']}</option>\r\n";
            $select .= "<option value=\"0\"" . ($fieldvalue == 0 ? " selected" : "") . ">{$lang['xfield_xno']}</option>\r\n";
			$select .= "</select>";
			
			$output .= <<<HTML
<tr id="$holderid">
<td class="addnews">$value[1]:</td>
<td class="xfields">{$select} {$value[18]}</td>
</tr>
HTML;

		$xfieldinput[$fieldname] = $select;

		} else {

			$selected = $fieldvalue ? "checked" : "";
			
			if ($value[18]) $value[18] = "<i class=\"help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left\" style=\"position: relative;top: -8px;\" data-rel=\"popover\" data-trigger=\"hover\" data-placement=\"right\" data-content=\"{$help_text}\" ></i>";
	
			$output .= <<<HTML
<div id="$holderid" class="form-group">
  <label class="control-label col-sm-2">{$value[1]}:</label>
  <div class="col-sm-10"><input class="switch" type="checkbox" name="xfield[$fieldname]" value="1" {$selected}>{$value[18]}
  </div>
</div>
HTML;
		}		
		
      } elseif( $value[3] == "image" ) {

	    $max_file_size = (int)$value[10] * 1024;

		if( $fieldvalue ) {
			
			$temp_array = explode('|', $fieldvalue);
				
			if (count($temp_array) > 1 ){
					
				$temp_alt = $temp_array[0];
				$temp_value = $temp_array[1];
					
			} else {
					
				$temp_alt = '';
				$temp_value = $temp_array[0];
					
			}
			
			$path_parts = pathinfo($temp_value);

			if( $value[12] AND file_exists(ROOT_DIR . "/uploads/posts/" .$path_parts['dirname']."/thumbs/".$path_parts['basename']) ) {
				$img_url = 	$config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/thumbs/".$path_parts['basename'];
			} else {
				$img_url = 	$config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
			}
			
			$filename = explode("_", $path_parts['basename']);
			unset($filename[0]);
			$filename = implode("_", $filename);
			
			$xf_id = md5($temp_value);
			$up_image = "<div id=\"xf_{$xf_id}\" class=\"uploadedfile\" data-id=\"{$temp_value}\" data-alt=\"{$temp_alt}\"><div class=\"info\">{$filename}</div><div class=\"uploadimage\"><img style=\"width:auto;height:auto;max-width:100px;max-height:90px;\" src=\"" . $img_url . "\" /></div><div class=\"info\"><a href=\"#\" onclick=\"xfaddalt(\\'".$xf_id."\\', \\'".$fieldname."\\');return false;\">{$lang['xf_img_descr']}</a><br><a href=\"#\" onclick=\"xfimagedelete(\\'".$fieldname."\\',\\'".$temp_value."\\');return false;\">{$lang['xfield_xfid']}</a></div></div>";
			
		} else $up_image = "";
		
		if (!$value[5]) { 
			$params = "rel=\"essential\" "; 
			$uid = "uid=\"essential\" "; 

		} else { 

			$params = ""; 
			$uid = "";

		}
		
		$max_file_size = number_format($max_file_size, 0, '', '');

$uploadscript = <<<HTML
	new qq.FileUploader({
		element: document.getElementById('xfupload_{$fieldname}'),
		action: 'engine/ajax/controller.php?mod=upload',
		maxConnections: 1,
		multiple: false,
		allowdrop: false,
		encoding: 'multipart',
        sizeLimit: {$max_file_size},
		allowedExtensions: ['gif', 'jpg', 'jpeg', 'png', 'webp'],
	    params: {"subaction" : "upload", "news_id" : "{$news_id}", "area" : "xfieldsimage", "author" : "{$author}", "xfname" : "{$fieldname}", "user_hash" : "{$dle_login_hash}"},
        template: '<div class="qq-uploader">' + 
                '<div id="uploadedfile_{$fieldname}" style="min-height: 2px;">{$up_image}</div>' +
                '<div class="qq-upload-button btn btn-green bg-teal btn-sm btn-raised" style="width: auto;">{$lang['xfield_xfim']}</div>' +
                '<ul class="qq-upload-list" style="display:none;"></ul>' + 
             '</div>',
		onSubmit: function(id, fileName) {

					$('<div id="uploadfile-'+id+'" class="file-box"><span class="qq-upload-file-status">{$lang['media_upload_st6']}</span><span class="qq-upload-file">&nbsp;'+fileName+'</span>&nbsp;<span class="qq-status"><span class="qq-upload-spinner"></span><span class="qq-upload-size"></span></span><div class="progress "><div class="progress-bar progress-blue" style="width: 0%"><span>0%</span></div></div></div>').appendTo('#xfupload_{$fieldname}');

        },
		onProgress: function(id, fileName, loaded, total){
					$('#uploadfile-'+id+' .qq-upload-size').text(DLEformatSize(loaded)+' {$lang['media_upload_st8']} '+DLEformatSize(total));
					var proc = Math.round(loaded / total * 100);
					$('#uploadfile-'+id+' .progress-bar').css( "width", proc + '%' );
					$('#uploadfile-'+id+' .qq-upload-spinner').css( "display", "inline-block");

		},
		onComplete: function(id, fileName, response){

						if ( response.success ) {
							var returnbox = response.returnbox;
							var returnval = response.xfvalue;

							returnbox = returnbox.replace(/&lt;/g, "<");
							returnbox = returnbox.replace(/&gt;/g, ">");
							returnbox = returnbox.replace(/&amp;/g, "&");

							$('#uploadfile-'+id+' .qq-status').html('{$lang['media_upload_st9']}');
							$('#uploadedfile_{$fieldname}').html( returnbox );
							$('#xf_{$fieldname}').val(returnval);

							$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').attr("disabled","disabled");
							
							setTimeout(function() {
								$('#uploadfile-'+id).fadeOut('slow', function() { $(this).remove(); });
							}, 1000);

						} else {
							$('#uploadfile-'+id+' .qq-status').html('{$lang['media_upload_st10']}');

							if( response.error ) $('#uploadfile-'+id+' .qq-status').append( '<br /><span class="text-danger">' + response.error + '</span>' );

							setTimeout(function() {
								$('#uploadfile-'+id).fadeOut('slow');
							}, 4000);
						}
		},
        messages: {
            typeError: "{$lang['media_upload_st11']}",
            sizeError: "{$lang['media_upload_st12']}",
            emptyError: "{$lang['media_upload_st13']}"
        },
		debug: false
    });
	
	if($('#xf_{$fieldname}').val() != "" ) {
		$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').attr("disabled","disabled");
	}
	
	if ( typeof Sortable != "undefined"  ) {
	
		var sortable_{$fieldcount} = Sortable.create(document.getElementById('uploadedfile_{$fieldname}'), {
		  group: {
			name: 'xfuploadedimages',
			put: function (to) {
				return to.el.children.length < 1;
			}
		  },
		  handle: '.uploadimage',
		  draggable: '.uploadedfile',
		  onSort: function (evt) {
				
				if( sortable_{$fieldcount}.el.children.length ) {
					$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').attr("disabled","disabled");
				} else {
					$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').removeAttr('disabled');
				}
				
				xfsinc('{$fieldname}');
		  },
		  animation: 150
		});
		
	}
	
HTML;

		if ($xfieldmode == "site") {
			
$onload_scripts[] = <<<HTML
if ($('#xfupload_{$fieldname}').length){
	{$uploadscript}
}
HTML;
			
$output .= <<<HTML
<tr id="$holderid" {$uid}>
<td class="addnews">$value[1]: [not-optional]<span style="color:red;">*</span>[/not-optional]</td>
<td class="xfields"><div id="xfupload_{$fieldname}"></div><input type="hidden" name="xfield[$fieldname]" id="xf_$fieldname" value="{$fieldvalue}" {$params}/>{$value[18]}</td>
</tr>
HTML;

			$xfieldinput[$fieldname] = "<div id=\"xfupload_{$fieldname}\"></div><input type=\"hidden\" name=\"xfield[$fieldname]\" id=\"xf_$fieldname\" value=\"{$fieldvalue}\" {$params}/>";
			
		} else {
				
$output .= <<<HTML
<div id="$holderid" class="form-group" {$uid}>
  <label class="control-label col-sm-2">{$value[1]}: [not-optional]<span style="color:red;">*</span>[/not-optional] {$value[18]}</label>
  <div class="col-sm-10"><div id="xfupload_{$fieldname}"></div><input type="hidden" name="xfield[$fieldname]" id="xf_$fieldname" value="{$fieldvalue}" {$params}/>
<script>
jQuery(function($){
{$uploadscript}
});
</script>
  </div>
</div>
HTML;

		}

      } elseif( $value[3] == "imagegalery" ) {

	    $max_file_size = (int)$value[10] * 1024;

		if( $fieldvalue ) {
			$fieldvalue_arr = explode(',', $fieldvalue);
			$up_image = array();
			
			foreach ($fieldvalue_arr as $temp_value) {
				$temp_value = trim($temp_value);
				
				if($temp_value == "") continue;
				
				$temp_array = explode('|', $temp_value);
				
				if (count($temp_array) > 1 ){
					
					$temp_alt = $temp_array[0];
					$temp_value = $temp_array[1];
					
				} else {
					
					$temp_alt = '';
					$temp_value = $temp_array[0];
					
				}
				
				$path_parts = pathinfo($temp_value);
				
				if( $value[12] AND file_exists(ROOT_DIR . "/uploads/posts/" .$path_parts['dirname']."/thumbs/".$path_parts['basename']) ) {
					$img_url = 	$config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/thumbs/".$path_parts['basename'];
				} else {
					$img_url = 	$config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
				}
				
				$filename = explode("_", $path_parts['basename']);
				unset($filename[0]);
				$filename = implode("_", $filename);
				
				$xf_id = md5($temp_value);
				$up_image[] = "<div id=\"xf_{$xf_id}\" data-id=\"{$temp_value}\" data-alt=\"{$temp_alt}\" class=\"uploadedfile\"><div class=\"info\">{$filename}</div><div class=\"uploadimage\"><img style=\"width:auto;height:auto;max-width:100px;max-height:90px;\" src=\"" . $img_url . "\" /></div><div class=\"info\"><a href=\"#\" onclick=\"xfaddalt(\\'".$xf_id."\\', \\'".$fieldname."\\');return false;\">{$lang['xf_img_descr']}</a><br><a href=\"#\" onclick=\"xfimagegalerydelete_{$fieldcount}(\\'".$fieldname."\\',\\'".$temp_value."\\', \\'".$xf_id."\\');return false;\">{$lang['xfield_xfid']}</a></div></div>";

			}
			
			$totaluploadedfiles = count($up_image);
			$up_image = implode($up_image);

			
		} else { $up_image = ""; $totaluploadedfiles = 0; }
		
		if (!$value[5]) { 
			$params = "rel=\"essential\" "; 
			$uid = "uid=\"essential\" "; 

		} else { 

			$params = ""; 
			$uid = "";

		}

$del_function = <<<HTML
	var maxallowfiles_{$fieldcount} = {$value[16]};
	var totaluploaded_{$fieldcount} = {$totaluploadedfiles};
	var totalqueue_{$fieldcount} = 0;
	
	function xfimagegalerydelete_{$fieldcount} ( xfname, xfvalue, id )
	{
		DLEconfirm( '{$lang['image_delete']}', '{$lang['p_info']}', function () {
		
			ShowLoading('');
	
			$.post('engine/ajax/controller.php?mod=upload', { subaction: 'deluploads', user_hash: '{$dle_login_hash}', news_id: '{$news_id}', author: '{$author}', 'images[]' : xfvalue }, function(data){
	
				HideLoading('');

				$('#xf_'+id).remove();
				totaluploaded_{$fieldcount} --;
				xfsinc('{$fieldname}');
				
				$('#xfupload_' + xfname + ' .qq-upload-button, #xfupload_' + xfname + ' .qq-upload-button input').removeAttr('disabled');
			});
			
		} );
		
		return false;

	};
HTML;

	$max_file_size = number_format($max_file_size, 0, '', '');

$uploadscript = <<<HTML
	var uploader_{$fieldcount} = new qq.FileUploader({
		element: document.getElementById('xfupload_{$fieldname}'),
		action: 'engine/ajax/controller.php?mod=upload',
		maxConnections: 1,
		multiple: true,
		allowdrop: false,
		encoding: 'multipart',
        sizeLimit: {$max_file_size},
		allowedExtensions: ['gif', 'jpg', 'jpeg', 'png', 'webp'],
	    params: {"subaction" : "upload", "news_id" : "{$news_id}", "area" : "xfieldsimagegalery", "author" : "{$author}", "xfname" : "{$fieldname}", "user_hash" : "{$dle_login_hash}"},
        template: '<div class="qq-uploader">' + 
                '<div id="uploadedfile_{$fieldname}" style="min-height: 2px;">{$up_image}</div>' +
                '<div class="qq-upload-button btn btn-green bg-teal btn-sm btn-raised" style="width: auto;">{$lang['xfield_xfimg']}</div>' +
                '<ul class="qq-upload-list" style="display:none;"></ul>' + 
             '</div>',
		onSubmit: function(id, fileName) {
		
					totalqueue_{$fieldcount} ++;
					
					if(maxallowfiles_{$fieldcount} && (totaluploaded_{$fieldcount} + totalqueue_{$fieldcount} ) > maxallowfiles_{$fieldcount} ) {
						totalqueue_{$fieldcount} --;
					
					    $('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').attr("disabled","disabled");
						return false;
					}
							
					$('<div id="uploadfile-'+id+'" class="file-box"><span class="qq-upload-file-status">{$lang['media_upload_st6']}</span><span class="qq-upload-file">&nbsp;'+fileName+'</span>&nbsp;<span class="qq-status"><span class="qq-upload-spinner"></span><span class="qq-upload-size"></span></span><div class="progress "><div class="progress-bar progress-blue" style="width: 0%"><span>0%</span></div></div></div>').appendTo('#xfupload_{$fieldname}');

        },
		onProgress: function(id, fileName, loaded, total){
					$('#uploadfile-'+id+' .qq-upload-size').text(DLEformatSize(loaded)+' {$lang['media_upload_st8']} '+DLEformatSize(total));
					var proc = Math.round(loaded / total * 100);
					$('#uploadfile-'+id+' .progress-bar').css( "width", proc + '%' );
					$('#uploadfile-'+id+' .qq-upload-spinner').css( "display", "inline-block");

		},
		onComplete: function(id, fileName, response){

						totalqueue_{$fieldcount} --;

						if ( response.success ) {
							totaluploaded_{$fieldcount} ++;

							var fieldvalue = $('#xf_{$fieldname}').val();
						
							var returnbox = response.returnbox;
							var returnval = response.xfvalue;

							returnbox = returnbox.replace(/&lt;/g, "<");
							returnbox = returnbox.replace(/&gt;/g, ">");
							returnbox = returnbox.replace(/&amp;/g, "&");

							$('#uploadfile-'+id+' .qq-status').html('{$lang['media_upload_st9']}');
							$('#uploadedfile_{$fieldname}').append( returnbox );
							
							if (fieldvalue == "") {
								$('#xf_{$fieldname}').val(returnval);
							} else {
								fieldvalue += ',' +returnval;
								$('#xf_{$fieldname}').val(fieldvalue);
							}

							if(maxallowfiles_{$fieldcount} && totaluploaded_{$fieldcount} == maxallowfiles_{$fieldcount} ) {
									$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').attr("disabled","disabled");
							}

							setTimeout(function() {
								$('#uploadfile-'+id).fadeOut('slow', function() { $(this).remove(); });
							}, 1000);

						} else {
							$('#uploadfile-'+id+' .qq-status').html('{$lang['media_upload_st10']}');

							if( response.error ) $('#uploadfile-'+id+' .qq-status').append( '<br /><span class="text-danger">' + response.error + '</span>' );

							setTimeout(function() {
								$('#uploadfile-'+id).fadeOut('slow');
							}, 4000);
						}
		},
        messages: {
            typeError: "{$lang['media_upload_st11']}",
            sizeError: "{$lang['media_upload_st12']}",
            emptyError: "{$lang['media_upload_st13']}"
        },
		debug: false
    });
	
	if(maxallowfiles_{$fieldcount} && totaluploaded_{$fieldcount} >=  maxallowfiles_{$fieldcount} ) {
		$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').attr("disabled","disabled");
	}
	
	if ( typeof Sortable != "undefined"  ) {
	
		var sortable_{$fieldcount} = Sortable.create(document.getElementById('uploadedfile_{$fieldname}'), {
		  group: {
			name: 'xfuploadedimages',
			put: function (to) {
				if(maxallowfiles_{$fieldcount} && totaluploaded_{$fieldcount} >= maxallowfiles_{$fieldcount} ) {
					return false;
				} else {return true;}
			}
		  },
		  handle: '.uploadimage',
		  draggable: '.uploadedfile',
		  onSort: function (evt) {
				totaluploaded_{$fieldcount} = sortable_{$fieldcount}.el.children.length;
				
				if(maxallowfiles_{$fieldcount} && totaluploaded_{$fieldcount} >= maxallowfiles_{$fieldcount} ) {
					$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').attr("disabled","disabled");
				} else {
					$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').removeAttr('disabled');
				}
				
				xfsinc('{$fieldname}');
		  },
		  animation: 150
		});
		
	}
HTML;

		if ($xfieldmode == "site") {
			
$onload_scripts[] = <<<HTML
if ($('#xfupload_{$fieldname}').length){
	{$uploadscript}
}
HTML;
			
$output .= <<<HTML
<tr id="$holderid" {$uid}>
<td class="addnews">$value[1]: [not-optional]<span style="color:red;">*</span>[/not-optional]</td>
<td class="xfields"><div id="xfupload_{$fieldname}"></div><input type="hidden" name="xfield[$fieldname]" id="xf_$fieldname" value="{$fieldvalue}" {$params}/>{$value[18]}
<script>
{$del_function}
</script>
</td>
</tr>
HTML;

			$xfieldinput[$fieldname] = "<div id=\"xfupload_{$fieldname}\"></div><input type=\"hidden\" name=\"xfield[$fieldname]\" id=\"xf_$fieldname\" value=\"{$fieldvalue}\" {$params}/><script>{$del_function}</script>";
			
		} else {
					
$output .= <<<HTML
<div id="$holderid" class="form-group" {$uid}>
  <label class="control-label col-sm-2">{$value[1]}: [not-optional]<span style="color:red;">*</span>[/not-optional] {$value[18]}</label>
  <div class="col-sm-10"><div id="xfupload_{$fieldname}"></div><input type="hidden" name="xfield[$fieldname]" id="xf_$fieldname" value="{$fieldvalue}" {$params}/>
<script>
{$del_function}
jQuery(function($){
{$uploadscript}
});
</script>
  </div>
</div>
HTML;

		}

		
	  } elseif( $value[3] == "file" ) {
		
	    $max_file_size = (int)$value[15] * 1024;
		$allowed_files = explode( ',', strtolower( $value[14] ) );
		$allowed_files = implode( "', '", $allowed_files );

		$fieldvalue = str_replace('&amp;', '&', $fieldvalue);

		if (!$value[5]) { 
			$params = "rel=\"essential\" "; 
			$uid = "uid=\"essential\" "; 

		} else { 

			$params = ""; 
			$uid = "";

		}

		if( $fieldvalue ) {
			
			$fileid = intval(preg_replace( "'\[attachment=(.*?):(.*?)\]'si", "\\1", $fieldvalue ));
			
			$fileid = "&nbsp;<button class=\"qq-upload-button btn btn-sm btn-red bg-danger btn-raised\" onclick=\"xffiledelete('".$fieldname."','".$fileid."');return false;\">{$lang['xfield_xfid']}</button>";

			$show="display:inline-block;";
			
		} else { $show="display:none;"; $fileid="";}

		$max_file_size = number_format($max_file_size, 0, '', '');

$uploadscript = <<<HTML
	new qq.FileUploader({
		element: document.getElementById('xfupload_{$fieldname}'),
		action: 'engine/ajax/controller.php?mod=upload',
		maxConnections: 1,
		multiple: false,
		allowdrop: false,
		encoding: 'multipart',
        sizeLimit: {$max_file_size},
		allowedExtensions: ['{$allowed_files}'],
	    params: {"subaction" : "upload", "news_id" : "{$news_id}", "area" : "xfieldsfile", "author" : "{$author}", "xfname" : "{$fieldname}", "user_hash" : "{$dle_login_hash}"},
        template: '<div class="qq-uploader">' + 
                '<div class="qq-upload-button btn btn-green bg-teal btn-sm btn-raised" style="width: auto;">{$lang['xfield_xfif']}</div>' +
                '<ul class="qq-upload-list" style="display:none;"></ul>' + 
             '</div>',
		onSubmit: function(id, fileName) {

					$('<div id="uploadfile-'+id+'" class="file-box"><span class="qq-upload-file-status">{$lang['media_upload_st6']}</span><span class="qq-upload-file">&nbsp;'+fileName+'</span>&nbsp;<span class="qq-status"><span class="qq-upload-spinner"></span><span class="qq-upload-size"></span></span><div class="progress"><div class="progress-bar progress-blue" style="width: 0%"><span>0%</span></div></div></div>').appendTo('#xfupload_{$fieldname}');

        },
		onProgress: function(id, fileName, loaded, total){
					$('#uploadfile-'+id+' .qq-upload-size').text(DLEformatSize(loaded)+' {$lang['media_upload_st8']} '+DLEformatSize(total));
					var proc = Math.round(loaded / total * 100);
					$('#uploadfile-'+id+' .progress-bar').css( "width", proc + '%' );
					$('#uploadfile-'+id+' .qq-upload-spinner').css( "display", "inline-block");

		},
		onComplete: function(id, fileName, response){

						if ( response.success ) {
							var returnbox = response.returnbox;
							var returnval = response.xfvalue;

							returnbox = returnbox.replace(/&lt;/g, "<");
							returnbox = returnbox.replace(/&gt;/g, ">");
							returnbox = returnbox.replace(/&amp;/g, "&");

							$('#uploadfile-'+id+' .qq-status').html('{$lang['media_upload_st9']}');
							$('#xf_{$fieldname}').show();
							$('#uploadedfile_{$fieldname}').html( returnbox );
							$('#xf_{$fieldname}').val(returnval);
							$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').attr("disabled","disabled");
							
							setTimeout(function() {
								$('#uploadfile-'+id).fadeOut('slow', function() { $(this).remove(); });
							}, 1000);

						} else {
							$('#uploadfile-'+id+' .qq-status').html('{$lang['media_upload_st10']}');

							if( response.error ) $('#uploadfile-'+id+' .qq-status').append( '<br /><span class="text-danger">' + response.error + '</span>' );

							setTimeout(function() {
								$('#uploadfile-'+id).fadeOut('slow');
							}, 4000);
						}
		},
        messages: {
            typeError: "{$lang['media_upload_st11']}",
            sizeError: "{$lang['media_upload_st12']}",
            emptyError: "{$lang['media_upload_st13']}"
        },
		debug: false
    });
	
	if($('#xf_{$fieldname}').val() != "" ) {
		$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').attr("disabled","disabled");
	}
	
HTML;

		if ($xfieldmode == "site") {
			
$onload_scripts[] = <<<HTML
if ($('#xfupload_{$fieldname}').length){
	{$uploadscript}
}
HTML;
			
$output .= <<<HTML
<tr id="$holderid" {$uid}>
<td class="addnews">$value[1]: [not-optional]<span style="color:red;">*</span>[/not-optional]</td>
<td class="xfields"><input style="{$show}" type="text" name="xfield[$fieldname]" id="xf_$fieldname" value="{$fieldvalue}" {$params}/><span id="uploadedfile_{$fieldname}">{$fileid}</span><div id="xfupload_{$fieldname}"></div>{$value[18]}</td>
</tr>
HTML;

			$xfieldinput[$fieldname] = "<input style=\"{$show}\" type=\"text\" name=\"xfield[$fieldname]\" id=\"xf_$fieldname\" value=\"{$fieldvalue}\" {$params}/><span id=\"uploadedfile_{$fieldname}\">{$fileid}</span><div id=\"xfupload_{$fieldname}\"></div>";
			
		} else {
		
$output .= <<<HTML
<div id="$holderid" class="form-group" {$uid}>
  <label class="control-label col-sm-2">{$value[1]}: [not-optional]<span style="color:red;">*</span>[/not-optional] {$value[18]}</label>
  <div class="col-sm-10"><input class="form-control width-350 position-left" style="margin-bottom:5px;{$show}" type="text" name="xfield[$fieldname]" id="xf_$fieldname" value="{$fieldvalue}" {$params}/><span id="uploadedfile_{$fieldname}">{$fileid}</span><div id="xfupload_{$fieldname}"></div>
<script>
jQuery(function($){
{$uploadscript}
});
</script>
  </div>
</div>
HTML;

		}		
	  }
	  
      $output = preg_replace("'\\[not-optional\\](.*?)\\[/not-optional\\]'s", $value[5] ? "" : "\\1", $output);

    }
	
	if ($xfieldmode == "site") {
    
	$onload_scripts[] = <<<HTML
	
	onCategoryChange($('#category'));
	
	cal_language   = {en:{months:['{$lang['January']}','{$lang['February']}','{$lang['March']}','{$lang['April']}','{$lang['May']}','{$lang['June']}','{$lang['July']}','{$lang['August']}','{$lang['September']}','{$lang['October']}','{$lang['November']}','{$lang['December']}'],dayOfWeek:["{$langdate['Sun']}", "{$langdate['Mon']}", "{$langdate['Tue']}", "{$langdate['Wed']}", "{$langdate['Thu']}", "{$langdate['Fri']}", "{$langdate['Sat']}"]}};

HTML;

		
	} else {

    $output .= <<<HTML

<script>
<!--
jQuery(function($){
    onCategoryChange($('#category'));
});
// -->
</script>
HTML;
		
	}
	

    break;
  case "init":

    $postedxfields = $_POST['xfield'];
    $newpostedxfields = array();
	$filecontents = array ();
	$xf_search_words = array ();
	$xf_complete_fields = array();
	$xf_not_allowed = array();

	
	foreach ($category as $cats_explode) {
		foreach ($xfields as $name => $value) {
			
			if ($value[2] != "" AND !in_array($cats_explode, explode(",", $value[2]))) {
				continue;
			}
			
			if( $value[19] ) {
			  
			  $value[19] = explode( ',', $value[19] );
			  
			  if( $value[19][0] AND !in_array( $member_id['user_group'], $value[19] ) ) {
				  $xf_not_allowed[] = $value[0];
				  continue;
			  }
			  
			}
  
			if( in_array($value[0], $xf_complete_fields) ) continue;
			
			if( $value[3] == "yesorno" ) {
				
				$postedxfields[$value[0]] = intval($postedxfields[$value[0]]);
				
			}
			
			if( $value[3] == "datetime" AND $postedxfields[$value[0]] ) {
				
				$postedxfields[$value[0]] = @strtotime( $postedxfields[$value[0]] );
				
				if( $postedxfields[$value[0]] !== - 1 AND $postedxfields[$value[0]] ) {
					
					if( $value[23] == 1 ) $postedxfields[$value[0]] = date( "Y-m-d", $postedxfields[$value[0]] );
					elseif( $value[23] == 2 ) $postedxfields[$value[0]] = date( "H:i", $postedxfields[$value[0]] );
					else $postedxfields[$value[0]] = date( "Y-m-d H:i", $postedxfields[$value[0]] );
					

				} else $postedxfields[$value[0]] = "";

			}

			if ($value[5] == 0 AND $postedxfields[$value[0]] === "" AND $value[3] != "select") {

				if ($add_module == "yes")
					$stop .= $lang['xfield_xerr1'];
				else
					msg("error", "error", $lang['xfield_xerr1'], "javascript:history.go(-1)");
		
			}

			if ($value[3] == "select") {

				$options = explode("\r\n", $value[4]);
				$options = explode("|", $options[$_POST['xfield'][$value[0]]] );
		        $postedxfields[$value[0]] = $options[0];
			}
			
			if ($value[3] == "datetime" AND $postedxfields[$value[0]] != "") {
				
				$newpostedxfields[$value[0]] = str_replace( ":", "&#58;", $postedxfields[$value[0]] );
				
			} elseif($value[3] == "yesorno") {
				
				$newpostedxfields[$value[0]] = $postedxfields[$value[0]];
				
			} elseif($value[3] == "htmljs" AND $postedxfields[$value[0]] != "" ) {
				
				$newpostedxfields[$value[0]] = $postedxfields[$value[0]];
				
			} elseif (($value[8] == 1 OR $value[6] == 1 OR $value[3] == "select" OR $value[3] == "image" OR $value[3] == "imagegalery" OR $value[3] == "file") AND $postedxfields[$value[0]] != "" ) {
				
				$newpostedxfields[$value[0]] = str_replace( "&#44;", "&amp;#44;", $postedxfields[$value[0]] );

				$newpostedxfields[$value[0]] = html_entity_decode($newpostedxfields[$value[0]], ENT_QUOTES, $config['charset']);
				$newpostedxfields[$value[0]] = trim( htmlspecialchars(strip_tags( stripslashes($newpostedxfields[$value[0]]) ), ENT_QUOTES, $config['charset'] ));
				$newpostedxfields[$value[0]] = preg_replace( "/javascript:/i", "j&#1072;vascript&#58;", $newpostedxfields[$value[0]] );
				$newpostedxfields[$value[0]] = preg_replace( "/data:/i", "d&#1072;ta&#58;", $newpostedxfields[$value[0]] );
				$newpostedxfields[$value[0]] = str_replace( array("{", "[", ":"), array("&#123;", "&#91;", "&#58;"), $newpostedxfields[$value[0]] );

				if($value[3] == "image" OR $value[3] == "imagegalery") {

					$f_arr = explode(',', $newpostedxfields[$value[0]]);
					
					foreach($f_arr as $t_val) {
						
						$t_a = explode('|', $t_val);
						
						if (count($t_a) > 1 ){
							$t_v = $t_a[1];
						} else {
							$t_v = $t_a[0];
						}
		
						if( preg_match( "/[?&;<]/", $t_v) OR stripos( $t_v, ".php" ) !== false ) $newpostedxfields[$value[0]] = "";
					}

				}
				
				if($value[3] == "file") {
					
					$newpostedxfields[$value[0]] = str_replace( array("&#91;", "&#58;"), array("[", ":"), $newpostedxfields[$value[0]] );
					
					if (strpos ( $newpostedxfields[$value[0]], "[attachment=" ) === false) $newpostedxfields[$value[0]] = "";
					
				}

			} elseif ( $postedxfields[$value[0]] != "" ) {

				if ($add_module == "yes") {

					if( $config['allow_site_wysiwyg'] OR $allow_br != '1' ) {
						
						$newpostedxfields[$value[0]] = $parse->BB_Parse($parse->process($postedxfields[$value[0]]));
					
					} else {
						
						$newpostedxfields[$value[0]] = $parse->BB_Parse($parse->process($postedxfields[$value[0]]), false);
					
					}

				} else {

					if( $config['allow_admin_wysiwyg'] OR $allow_br != '1' ) {
						
						$newpostedxfields[$value[0]] = $parse->BB_Parse($parse->process($postedxfields[$value[0]]));
					
					} else {
						
						$newpostedxfields[$value[0]] = $parse->BB_Parse($parse->process($postedxfields[$value[0]]), false);
					
					}

				}

			}
			
			$newpostedxfields[$value[0]] = str_ireplace( "{title", "&#123;title", $newpostedxfields[$value[0]] );
			$newpostedxfields[$value[0]] = str_ireplace( "{short-story", "&#123;short-story", $newpostedxfields[$value[0]] );
			$newpostedxfields[$value[0]] = str_ireplace( "{full-story", "&#123;full-story", $newpostedxfields[$value[0]] );

			if ( $value[6] AND !empty($newpostedxfields[$value[0]]) ) {
				$temp_array = explode( ",", $newpostedxfields[$value[0]] );
				
				foreach ($temp_array as $value2) {
					$value2 = trim($value2);
					if($value2) {
						$xf_search_words[] = array( $db->safesql($value[0]), $db->safesql($value2) );
					}
				}
			
			}
			
			$xf_complete_fields[] = $value[0];

		}
	}
	
    $postedxfields = $newpostedxfields;
	
	if(count($xf_not_allowed) AND isset($xf_existing) and count($xf_existing) ) {
		foreach( $xf_not_allowed as $defxf) {
			if ($xf_existing[$defxf]) $postedxfields[$defxf] = $xf_existing[$defxf];
		}
	}
	
	if( !empty( $postedxfields ) ) {
		foreach ( $postedxfields as $xfielddataname => $xfielddatavalue ) {

			if( $xfielddatavalue === "" ) {
				continue;
			}
				
			$xfielddataname = str_replace( "|", "&#124;", $xfielddataname );
			$xfielddataname = str_replace( "\r\n", "__NEWL__", $xfielddataname );
			$xfielddatavalue = str_replace( "|", "&#124;", $xfielddatavalue );
			$xfielddatavalue = str_replace( "\r\n", "__NEWL__", $xfielddatavalue );
			$filecontents[] = "$xfielddataname|$xfielddatavalue";
		}
		
		if ( count($filecontents) ) $filecontents = $db->safesql(implode( "||", $filecontents )); else $filecontents = '';

	} else $filecontents = '';

    break;
  case "delete":
    break;
  case "templatereplacepreview":
	
	if (isset ($_POST["xfield"])) $xfield = $_POST["xfield"];
    $xfieldsoutput = $xfieldsinput;

    foreach ($xfields as $value) {
		
		$preg_safe_name = preg_quote($value[0], "'");

		if ($value[3] == "select") {
			$options = explode("\r\n", $value[4]);
			$xfield[$value[0]] = $options[$xfield[$value[0]]];
		}

		$parse->allow_code = true;
	  
		if( $value[19] ) {
		  
		  $value[19] = explode( ',', $value[19] );
		  
		  if( $value[19][0] AND !in_array( $member_id['user_group'], $value[19] ) ) {
			continue;
		  }
			
		}
		
		if( $value[3] == "htmljs" ) {
			
			$xfield[$value[0]] = $lang['xfield_xhtmljs_2'];
			
		} elseif (($value[8] == 1 OR $value[3] == "select" OR $value[3] == "image" OR $value[3] == "imagegalery" OR $value[3] == "file" ) AND $xfield[$value[0]] != "" ) {

			$xfield[$value[0]] = str_replace( "&#44;", "&amp;#44;", $xfield[$value[0]] );

			$xfield[$value[0]] = html_entity_decode($xfield[$value[0]], ENT_QUOTES, $config['charset']);
			$xfield[$value[0]] = trim( htmlspecialchars(strip_tags( stripslashes($xfield[$value[0]]) ), ENT_QUOTES, $config['charset'] ));
			$xfield[$value[0]] = preg_replace( "/javascript:/i", "j&#1072;vascript&#58;", $xfield[$value[0]] );
			$xfield[$value[0]] = preg_replace( "/data:/i", "d&#1072;ta&#58;", $xfield[$value[0]] );
			$xfield[$value[0]] = str_replace( array("{", "[", ":"), array("&#123;", "&#91;", "&#58;"), $xfield[$value[0]] );
				
			if($value[3] == "image" OR $value[3] == "imagegalery") {

				$f_arr = explode(',', $xfield[$value[0]]);
				
				foreach($f_arr as $t_val) {
					
					$t_a = explode('|', $t_val);
					
					if (count($t_a) > 1 ){
						$t_v = $t_a[1];
					} else {
						$t_v = $t_a[0];
					}
		
					if( preg_match( "/[?&;<]/", $t_v) OR stripos( $t_v, ".php" ) !== false ) $xfield[$value[0]] = "";
				}

			}
				
				
		} elseif ( $xfield[$value[0]] != "" ) {

			if ($add_module == "yes") {
				
				if( $config['allow_site_wysiwyg'] OR $allow_br != '1' ) {
						
					$xfield[$value[0]] = $parse->BB_Parse($parse->process($xfield[$value[0]]));
					
				} else {
						
					$xfield[$value[0]] = $parse->BB_Parse($parse->process($xfield[$value[0]]), false);
					
				}
				
			} else {
				
				if( $config['allow_admin_wysiwyg'] OR $allow_br != '1' ) {
						
					$xfield[$value[0]] = $parse->BB_Parse($parse->process($xfield[$value[0]]));
					
				} else {
						
					$xfield[$value[0]] = $parse->BB_Parse($parse->process($xfield[$value[0]]), false);
					
				}
			}

		}
		
		$xfield[$value[0]] = stripslashes($xfield[$value[0]]);
	  
		if($value[3] == "image" AND $xfield[$value[0]] ) {
			
			$temp_array = explode('|', $xfield[$value[0]]);
				
			if (count($temp_array) > 1 ){
				
				$temp_alt = $temp_array[0];
				$temp_value = $temp_array[1];
				
			} else {
				
				$temp_alt = '';
				$temp_value = $temp_array[0];
				
			}
				
			$path_parts = @pathinfo($temp_value);

			if( $value[12] AND file_exists(ROOT_DIR . "/uploads/posts/" .$path_parts['dirname']."/thumbs/".$path_parts['basename']) ) {
				$thumb_url = $config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/thumbs/".$path_parts['basename'];
				$img_url = $config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
			} else {
				$img_url = 	$config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
				$thumb_url = "";
			}
			
			if($thumb_url) {
				$xfield[$value[0]] = "<a href=\"$img_url\" class=\"highslide\"><img class=\"xfieldimage {$value[0]}\" src=\"$thumb_url\" alt=\"{$temp_alt}\" /></a>";
			} else $xfield[$value[0]] = "<img class=\"xfieldimage {$value[0]}\" src=\"{$img_url}\" alt=\"{$temp_alt}\" />";
		}

		if($value[3] == "imagegalery" AND $xfield[$value[0]] ) {
					
			$fieldvalue_arr = explode(',', $xfield[$value[0]] );
			$gallery_image = array();
					
			foreach ($fieldvalue_arr as $temp_value) {
						
				$temp_value = trim($temp_value);
				
				if($temp_value == "") continue;
				
				$temp_array = explode('|', $temp_value);
				
				if (count($temp_array) > 1 ){
					
					$temp_alt = $temp_array[0];
					$temp_value = $temp_array[1];
					
				} else {
					
					$temp_alt = '';
					$temp_value = $temp_array[0];
					
				}
				
				$path_parts = @pathinfo($temp_value);
						
				if( $value[12] AND file_exists(ROOT_DIR . "/uploads/posts/" .$path_parts['dirname']."/thumbs/".$path_parts['basename']) ) {
					$thumb_url = $config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/thumbs/".$path_parts['basename'];
					$img_url = $config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
				} else {
					$img_url = 	$config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
					$thumb_url = "";
				}
			
				if($thumb_url) {
					$gallery_image[] = "<li><a href=\"$img_url\" onclick=\"return hs.expand(this, { slideshowGroup: 'xf_{$value[0]}' })\" target=\"_blank\"><img src=\"{$thumb_url}\" alt=\"{$temp_alt}\" /></a></li>";
				} else $gallery_image[] = "<li><img src=\"{$img_url}\" alt=\"{$temp_alt}\" /></li>";
			
			}
			
			$xfield[$value[0]] = "<ul class=\"xfieldimagegallery {$value[0]}\">".implode($gallery_image)."</ul><script>hs.addSlideshow({slideshowGroup: 'xf_{$value[0]}', interval: 4000, repeat: false, useControls: true, fixedControls: 'fit', overlayOptions: { opacity: .75, position: 'bottom center', hideOnMouseOut: true } });</script>";
			
		}

		if ( $value[3] == "datetime" AND !empty($xfield[$value[0]]) ) {

			$xfield[$value[0]] = strtotime( str_replace("&#58;", ":", $xfield[$value[0]]) );

			if( !trim($value[24]) ) $value[24] = $config['timestamp_active'];

			if( $value[25] ) {
					
				if($value[26]) $xfield[$value[0]] = langdate($value[24], $xfield[$value[0]]);
				else $xfield[$value[0]] = langdate($value[24], $xfield[$value[0]], false, $customlangdate);

			} else $xfield[$value[0]] = date( $value[24], $xfield[$value[0]] );
			
			
		}		
		
		if ( $value[3] == "yesorno" ) {
			
		    if( intval($xfield[$value[0]]) ) {
				$xfgiven = true;
				$xfield[$value[0]] = $lang['xfield_xyes'];
			} else {
				$xfgiven = false;
				$xfield[$value[0]] = $lang['xfield_xno'];
			}
			
		} else {
			if($xfield[$value[0]] == "") $xfgiven = false; else $xfgiven = true;
		}

       if ( !$xfgiven ) {
          $xfieldsoutput = preg_replace("'\\[xfgiven_{$preg_safe_name}\\].*?\\[/xfgiven_{$preg_safe_name}\\]'is", "", $xfieldsoutput);
          $xfieldsoutput = str_replace( "[xfnotgiven_{$value[0]}]", "", $xfieldsoutput );
          $xfieldsoutput = str_replace( "[/xfnotgiven_{$value[0]}]", "", $xfieldsoutput );
       } else {
          $xfieldsoutput = preg_replace( "'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $xfieldsoutput );
          $xfieldsoutput = str_replace( "[xfgiven_{$value[0]}]", "", $xfieldsoutput );
          $xfieldsoutput = str_replace( "[/xfgiven_{$value[0]}]", "", $xfieldsoutput );
       }

	  $xfieldsoutput = preg_replace("'\\[xfvalue_{$preg_safe_name}\\]'i", $xfield[$value[0]], $xfieldsoutput);
	  
      if ( preg_match( "#\\[xfvalue_{$preg_safe_name} limit=['\"](.+?)['\"]\\]#i", $xfieldsoutput, $matches ) ) {
			$count= intval($matches[1]);

			$xfield[$value[0]] = str_replace( "</p><p>", " ", $xfield[$value[0]] );
			$xfield[$value[0]] = strip_tags( $xfield[$value[0]], "<br>" );
			$xfield[$value[0]] = trim(str_replace( "<br>", " ", str_replace( "<br />", " ", str_replace( "\n", " ", str_replace( "\r", "", $xfield[$value[0]] ) ) ) ));

			if( $count AND dle_strlen( $xfield[$value[0]], $config['charset'] ) > $count ) {
							
				$xfield[$value[0]] = dle_substr( $xfield[$value[0]], 0, $count, $config['charset'] );
							
				if( ($temp_dmax = dle_strrpos( $xfield[$value[0]], ' ', $config['charset'] )) ) $xfield[$value[0]] = dle_substr( $xfield[$value[0]], 0, $temp_dmax, $config['charset'] );
						
			}

			$xfieldsoutput = str_replace($matches[0], $xfield[$value[0]], $xfieldsoutput);

      }

    }
    break;
  case "categoryfilter":
    $categoryfilter = <<<HTML
<script>
	function ShowOrHideEx(id, show) {
	  
		if($('#' + id).length) {
			if (show) {
			  $( '#' + id ).show();
			} else {
				$( '#' + id ).hide();
			}
		}
		
	}

  function onCategoryChange(obj) {

	var value = $(obj).val();
	var totaldzendisabled = 0;
	var totalturbodisabled = 0;
	var founddzencount = 0;
	var foundturbocount = 0;
	
	valuecount = 0;

	if ($.isArray(value)) {

		valuecount = value.length
		
HTML;


    foreach ($xfields as $value) {

      if ( $value[2] ) {

		$categories = explode(",", $value[2]);
		$temp_array = array();

		foreach ($categories as $temp_value) {

			$temp_array[] = "jQuery.inArray('{$temp_value}', value) != -1";

		}

		$categories = implode(" || ", $temp_array);

        $categoryfilter .= "ShowOrHideEx(\"xfield_holder_{$value[0]}\", {$categories} );\r\n";
      }
    }
	
	foreach ($cat_info as $value) {
		if ( $value['disable_main'] ) {
			$categoryfilter .= "ShowOrHideEx(\"opt_holder_main\", jQuery.inArray('{$value['id']}', value) == -1 );\r\n";
		}
		if ( $value['disable_comments'] ) {
			$categoryfilter .= "ShowOrHideEx(\"opt_holder_comments\", jQuery.inArray('{$value['id']}', value) == -1 );\r\n";
		}
		if ( $value['disable_rating'] ) {
			$categoryfilter .= "ShowOrHideEx(\"opt_holder_rating\", jQuery.inArray('{$value['id']}', value) == -1 );\r\n";
		}

		if ( !$value['enable_dzen'] ) {
			$categoryfilter .= "totaldzendisabled ++; if( jQuery.inArray('{$value['id']}', value) != -1 ) { founddzencount ++; } \r\n";	
		}
		
		if ( !$value['enable_turbo'] ) {
			$categoryfilter .= "totalturbodisabled ++; if( jQuery.inArray('{$value['id']}', value) != -1 ) { foundturbocount ++; } \r\n";	
		}
		
	}


$categoryfilter .= <<<HTML

	} else {

		valuecount = 1;
HTML;

    foreach ($xfields as $value) {
      $categories = str_replace(",", "||value==", $value[2]);
      if ($categories) {
        $categoryfilter .= "ShowOrHideEx(\"xfield_holder_{$value[0]}\", value == $categories);\r\n";
      }
    }

	foreach ($cat_info as $value) {
		if ( $value['disable_main'] ) {
			$categoryfilter .= "ShowOrHideEx(\"opt_holder_main\", value != {$value['id']} );\r\n";
		}
		if ( $value['disable_comments'] ) {
			$categoryfilter .= "ShowOrHideEx(\"opt_holder_comments\", value != {$value['id']} );\r\n";
		}
		if ( $value['disable_rating'] ) {
			$categoryfilter .= "ShowOrHideEx(\"opt_holder_rating\", value != {$value['id']} );\r\n";
		}
		
		if ( !$value['enable_dzen'] ) {
			$categoryfilter .= "totaldzendisabled ++; if( value == {$value['id']} ) { founddzencount ++; } \r\n";	
		}
		
		if ( !$value['enable_turbo'] ) {
			$categoryfilter .= "totalturbodisabled ++; if( value == {$value['id']} ) { foundturbocount ++; } \r\n";	
		}
		
	}
	
$categoryfilter .= <<<HTML
	}

	if( totaldzendisabled && $('#allow_rss_dzen').length ) {
		$('#allow_rss_dzen').prop('checked', valuecount != founddzencount);
		$.uniform.update();
	}
	
	if( totalturbodisabled && $('#allow_rss_turbo').length ) {
		$('#allow_rss_turbo').prop('checked', valuecount != foundturbocount);
		$.uniform.update();
	}
	
  }
</script>
HTML;

    break;
  default:
  if (function_exists('msg'))
    msg("error", $lang['xfield_error'], $lang['xfield_xerr2']);
}
?>