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
 File: userfields.php
-----------------------------------------------------
 Use: profile xfields
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

	function profilesave($data) {
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
	  
	    $filehandle = fopen(ENGINE_DIR.'/data/xprofile.txt', "w+");
	    if (!$filehandle) msg("error", $lang['xfield_error'], "$lang[xfield_err_1] \"engine/data/xprofile.txt\", $lang[xfield_err_2]");
	
		$filecontents = htmlspecialchars($filecontents, ENT_QUOTES, $config['charset'] );
		$filecontents = str_replace("&amp;#124;", "&#124;", $filecontents);
		
	    fwrite($filehandle, $filecontents);
	    fclose($filehandle);
	    header("Location: ?mod=userfields&xfieldsaction=configure");
	    exit;
	}

	function profileload() {
	  global $lang, $config;
	  $path = ENGINE_DIR.'/data/xprofile.txt';
	  $filecontents = file($path);
	
	    if (!is_array($filecontents)) $filecontents = array();
	  
	    foreach ($filecontents as $name => $value) {
	      $filecontents[$name] = explode("|", trim($value));
	      foreach ($filecontents[$name] as $name2 => $value2) {
	        $value2 = str_replace("&#124;", "|", $value2); 
	        $value2 = str_replace("__NEWL__", "\r\n", $value2);
			$value2 = html_entity_decode($value2, ENT_QUOTES, $config['charset']);
	        $filecontents[$name][$name2] = $value2;
	      }
	    }
	    return $filecontents;
	}


	$xf_inited = true;
}

$xfields = profileload();

switch ($xfieldsaction) {
  case "configure":

	if( ! $user_group[$member_id['user_group']]['admin_userfields'] ) {
		msg( "error", $lang['index_denied'], $lang['index_denied'] );
	}

    switch ($xfieldssubaction) {
		
      case "delete":
		
        if (!isset($xfieldsindex)) {
          msg("error", $lang['xfield_error'], $lang['xfield_err_5'],"javascript:history.go(-1)");
        }
		
		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '71', '{$xfields[$xfieldsindex][0]}')" );

        unset($xfields[$xfieldsindex]);
		
        @profilesave($xfields);
		
        break;
	
      case "add":
        $xfieldsindex = count($xfields);
        // Fall trough to edit
      case "edit":

        if (!isset($xfieldsindex)) {
          msg("error", $lang['xfield_error'], $lang['xfield_err_8'],"javascript:history.go(-1)");
        }
  
        if (!$editedxfield) {
			
          $editedxfield = $xfields[$xfieldsindex];
		  
        } elseif (strlen(trim($editedxfield[0])) > 0 AND strlen(trim($editedxfield[1])) > 0) {
			
          foreach ($xfields as $name => $value) {
            if ($name != $xfieldsindex AND $value[0] == $editedxfield[0]) {
              msg("error", $lang['xfield_error'], $lang['xfield_err_9'],"javascript:history.go(-1)");
            }
          }
		  
          $editedxfield[0] = totranslit(trim($editedxfield[0]));
          $editedxfield[1] = strip_tags( stripslashes( trim($editedxfield[1]) ) );
          $editedxfield[2] = intval($editedxfield[2]);
          $editedxfield[4] = intval($editedxfield[4]);
          $editedxfield[5] = intval($editedxfield[5]);

		  $db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '72', '{$editedxfield[0]}')" );

          if ($editedxfield[3] == "select") {
            $options = array();
			
            foreach (explode("\r\n", $editedxfield["6_select"]) as $name => $value) {
              $value = trim($value);
              if (!in_array($value, $options)) {
                $options[] = $value;
              }
            }
			
            if (count($options) < 2) {
				msg("error", $lang['xfield_error'], $lang['xfield_err_10'],"javascript:history.go(-1)");
            }
			
            $editedxfield[6] = implode("\r\n", $options);
			
          } else { $editedxfield[6] = ""; }

          unset($editedxfield['6_select']);

          ksort($editedxfield);
          
          $xfields[$xfieldsindex] = $editedxfield;
          ksort($xfields);

          @profilesave($xfields);
          break;
		
        } else {
			
          msg("error", $lang['xfield_error'], $lang['xfield_err_11'],"javascript:history.go(-1)");
		  
        }

        echoheader( "<i class=\"fa fa-user-circle-o position-left\"></i><span class=\"text-semibold\">{$lang['header_uf_1']}</span>", $lang['header_uf_2'] );

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
        ShowOrHideEx("select_options", value == "select");
      }
      </script>
      <input type="hidden" name="mod" value="userfields">
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
			<input class="form-control width-250" maxlength="30" type="text" name="editedxfield[0]" value="<?php echo htmlspecialchars($editedxfield[0], ENT_QUOTES, $config['charset'] ); ?>" /><span class="text-muted text-size-small"><i class="fa fa-exclamation-circle position-left position-right"></i> <?php echo $lang['xf_lat']; ?></span>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xdescr']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<input class="form-control width-350" maxlength="100" type="text" name="editedxfield[1]" value="<?php echo htmlspecialchars($editedxfield[1], ENT_QUOTES, $config['charset'] ); ?>" />
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xtype']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<select class="uniform" name="editedxfield[3]" id="type" onchange="onTypeChange(this.value);">
          <option value="text"<?php echo ($editedxfield[3] != "text") ? " selected" : ""; ?>><?php echo $lang['xfield_xstr']; ?></option>
          <option value="textarea"<?php echo ($editedxfield[3] == "textarea") ? " selected" : ""; ?>><?php echo $lang['xfield_xarea']; ?></option>
          <option value="select"<?php echo ($editedxfield[3] == "select") ? " selected" : ""; ?>><?php echo $lang['xfield_xsel']; ?></option>
        </select>
		  </div>
		 </div>	
		<div class="form-group" id="select_options">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xfield_xfaul']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<textarea class="classic width-350" style="height: 100px;" name="editedxfield[6_select]"><?php echo ($editedxfield[3] == "select") ? $editedxfield[6] : ""; ?></textarea><div class="text-muted text-size-small"><?php echo $lang['xfield_xfsel']; ?></div>
		  </div>
		 </div>	
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xp_reg']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<label class="radio-inline"><input class="icheck" type="radio" id="yes1" name="editedxfield[2]" <?php echo ($editedxfield[2]) ? "checked" : ""; ?> value="1"><?php echo $lang['opt_sys_yes']; ?></label>&nbsp;&nbsp;&nbsp;<label class="radio-inline"><input class="icheck" type="radio" id="no1" name="editedxfield[2]" <?php echo (!$editedxfield[2]) ? "checked" : ""; ?> value="0"><?php echo $lang['opt_sys_no']; ?></label><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xp_reg_hint']; ?>" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xp_edit']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<label class="radio-inline"><input class="icheck" type="radio" id="yes2" name="editedxfield[4]" <?php echo ($editedxfield[4]) ? "checked" : ""; ?> value="1"><?php echo $lang['opt_sys_yes']; ?></label>&nbsp;&nbsp;&nbsp;<label class="radio-inline"><input class="icheck" type="radio" id="no2" name="editedxfield[4]" <?php echo (!$editedxfield[4]) ? "checked" : ""; ?> value="0"><?php echo $lang['opt_sys_no']; ?></label><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xp_edit_hint']; ?>" ></i>
		  </div>
		 </div>
		<div class="form-group">
		  <label class="control-label col-md-2 col-sm-3"><?php echo $lang['xp_privat']; ?></label>
		  <div class="col-md-10 col-sm-9">
			<label class="radio-inline"><input class="icheck" type="radio" id="yes3" name="editedxfield[5]" <?php echo ($editedxfield[5]) ? "checked" : ""; ?> value="1"><?php echo $lang['opt_sys_yes']; ?></label>&nbsp;&nbsp;&nbsp;<label class="radio-inline"><input class="icheck" type="radio" id="no3" name="editedxfield[5]" <?php echo (!$editedxfield[5]) ? "checked" : ""; ?> value="0"><?php echo $lang['opt_sys_no']; ?></label><i class="help-button visible-lg-inline-block text-primary-600 fa fa-question-circle position-right position-left" data-rel="popover" data-trigger="hover" data-placement="auto right" data-content="<?php echo $lang['xp_privat_hint']; ?>" ></i>
		  </div>
		 </div>
	
   </div>
<div class="panel-footer">
	<button type="submit" class="btn bg-teal btn-sm btn-raised position-left"><i class="fa fa-floppy-o position-left"></i><?php echo $lang['user_save']; ?></button>
</div>
</div>
</form>
    <script>
    <!--
      var item_type = null;
      if (document.getElementById) {
        item_type = document.getElementById("type");
      } else if (document.all) {
        item_type = document.all.type;
      } else if (document.layers) {
        item_type = document.layers.type;
      }
      if (item_type) {
        onTypeChange(item_type.value);
      }
    // -->
    </script>
<?php
        echofooter();
        break;

      default:

	echoheader( "<i class=\"fa fa-user-circle-o position-left\"></i><span class=\"text-semibold\">{$lang['header_uf_1']}</span>", $lang['header_uf_2'] );
?>
<form method="get" name="xfieldsform">
<input type="hidden" name="mod" value="userfields">
<input type="hidden" name="user_hash" value="<?php echo $dle_login_hash; ?>">
<input type="hidden" name="xfieldsaction" value="configure">
<input type="hidden" name="xfieldssubactionadd" value="">
<div class="panel panel-default">
  <div class="panel-heading">
    <?php echo $lang['xp_xlist']; ?>
  </div>
  <div class="panel-body">

<?php
        if (count($xfields) == 0) {
		
          echo "<center><br />{$lang['xfield_xnof']}<br /><br /></center>";

		  } else {

			$x_list = "<ol class=\"dd-list\">";
	
			foreach ($xfields as $name => $value) {
		
				if ( $value[3] == "text" ) $type=$lang['xfield_xstr'];
				elseif($value[3] == "textarea") $type=$lang['xfield_xarea'];
				elseif($value[3] == "select") $type=$lang['xfield_xsel'];
	
				$p1 = $value[2] != 0 ? $lang['opt_sys_yes'] : $lang['opt_sys_no'];
				$p2 = $value[4] != 0 ? $lang['opt_sys_yes'] : $lang['opt_sys_no'];
				$p3 = $value[5] != 0 ? $lang['opt_sys_yes'] : $lang['opt_sys_no'];
	
				$x_list .= "<li class=\"dd-item\" data-id=\"{$name}\"><div class=\"dd-handle\"></div><div class=\"dd-content\"><b id=\"x_uname\" class=\"s-el\">{$value[0]}</b><b id=\"x_cats\" class=\"s-el\">{$lang['xp_descr']}: {$value[1]}</b><b id=\"x_utype\" class=\"s-el\">{$lang['xfield_xtype']}: {$type}</b><b id=\"x_par\" class=\"s-el\">{$lang['xp_regh']}: {$p1}</b><b id=\"x_par\" class=\"s-el\">{$lang['xp_edith']}: {$p2}</b><b id=\"x_l\" class=\"s-el\">{$lang['xp_privath']}: {$p3}</b><span><a href=\"?mod=userfields&xfieldsaction=configure&xfieldssubaction=edit&xfieldsindex={$name}&user_hash={$dle_login_hash}\"><i title=\"{$lang['cat_ed']}\" alt=\"{$lang['cat_ed']}\" class=\"fa fa-pencil-square-o\"></i></a>&nbsp;&nbsp;<a href=\"javascript:xfdelete('{$name}');\"><i title=\"{$lang['cat_del']}\" alt=\"{$lang['cat_del']}\" class=\"fa fa-trash-o text-danger\"></i></a></span></div></li>";		
	
			}

			$x_list .= "</ol>";
			echo "<div class=\"dd\">{$x_list}</div>";

        }
?>
	</div>
	<div class="panel-footer">
		<div class="pull-left">
	<?php if (count($xfields) > 0) { ?>
		<button id="xfsort" class="btn bg-primary-600 btn-sm btn-raised position-left"><?php echo $lang['xf_posi']; ?></button>
	<?php } ?>
		<input type="submit" class="btn bg-teal btn-sm btn-raised" value=" <?php echo $lang['b_create']; ?> " onclick="document.forms['xfieldsform'].xfieldssubactionadd.value = 'add';">
		</div>
		<div class="pull-right">
		<a onclick="javascript:Help('xprofile')" href="#"><?php echo $lang['xfield_xhelp']; ?></a>
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
		
		$('#xfsort').click(function(){
			var xfsort = window.JSON.stringify($('.dd').nestable('serialize'));
			var url = "action=userxfsort&user_hash=<?php echo $dle_login_hash; ?>&list="+xfsort;

			ShowLoading('');
			$.post('engine/ajax/controller.php?mod=adminfunction', url, function(data){
	
				HideLoading('');
	
				if (data == 'ok') {

					document.location.reload(false);

				} else {

					DLEalert('<?php echo $lang['cat_sort_fail']; ?>', '<?php echo $lang['p_info']; ?>');

				}
	
			});

			return false;

		});

	});

	function xfdelete(id){
		
	    DLEconfirm( '<?php echo $lang['xfield_err_6']; ?>', '<?php echo $lang['p_confirm']; ?>', function () {
			document.location='?mod=userfields&xfieldsaction=configure&xfieldsindex=' + id +'&xfieldssubaction=delete&user_hash=<?php echo $dle_login_hash; ?>';
		} );
	}
</script>
<?php
      echofooter();
    }
    break;

case "list":
    $output = "";
    if (!isset($xfieldsid)) $xfieldsid = "";
    $xfieldsdata = xfieldsdataload ($xfieldsid);
	$xfieldinput = array();

    foreach ($xfields as $name => $value) {
		
      $fieldname = totranslit(trim($value[0]));
	  $value[1] = htmlspecialchars($value[1], ENT_QUOTES, $config['charset'] );
	  
      if (!$xfieldsadd) {
        $fieldvalue = $xfieldsdata[$value[0]];
        $fieldvalue = $parse->decodeBBCodes($fieldvalue, false);
		if (!$xfieldsadd AND !intval($value[4]) AND !$user_group[$member_id['user_group']]['admin_editusers']) continue;
      }

if (intval($value[2]) OR (!$xfieldsadd)) {

     if ($value[3] == "textarea") {

		if ( isset($adminmode) ) {
		
		      $output .= <<<HTML
				<div class="form-group">
				  <label class="control-label col-md-3 col-sm-3">{$value[1]}:</label>
				  <div class="col-md-9 col-sm-9">
					<textarea name="xfield[$fieldname]" id="xf_$fieldname" class="classic" style="width:100%;height:100px;">{$fieldvalue}</textarea>
				  </div>
				 </div>
HTML;
		
		} else {

      $output .= <<<HTML
<tr>
<td>$value[1]:</td>
<td class="xprofile"><textarea name="xfield[$fieldname]" id="xf_$fieldname">$fieldvalue</textarea></td></tr>
HTML;

			$xfieldinput[$fieldname] = "<textarea name=\"xfield[$fieldname]\" id=\"xf_$fieldname\">$fieldvalue</textarea>";
		}

      } elseif ($value[3] == "text") {

		if ( isset($adminmode) ) {
		
		      $output .= <<<HTML
				<div class="form-group">
				  <label class="control-label col-md-3 col-sm-3">{$value[1]}:</label>
				  <div class="col-md-9 col-sm-9">
					<input class="form-control" type="text" name="xfield[$fieldname]" id="xfield[$fieldname]" value="$fieldvalue" />
				  </div>
				 </div>
HTML;
		
		} else {

        	$output .= <<<HTML
<tr>
<td>$value[1]:</td>
<td class="xprofile"><input type="text" name="xfield[$fieldname]" id="xfield[$fieldname]" value="{$fieldvalue}" /></td>
</tr>
HTML;

			$xfieldinput[$fieldname] = "<input type=\"text\" name=\"xfield[$fieldname]\" id=\"xfield[$fieldname]\" value=\"{$fieldvalue}\" />";
		}

      } elseif ($value[3] == "select") {

		if (isset($adminmode)) {
			$select = "<select name=\"xfield[$fieldname]\" id=\"xf_$fieldname\" class=\"uniform\">";
		} else {
			$select = "<select name=\"xfield[$fieldname]\" id=\"xf_$fieldname\">";
		}

		$fieldvalue = str_replace('&amp;', '&', $fieldvalue);
		
        foreach (explode("\r\n", htmlspecialchars($value[6], ENT_QUOTES, $config['charset']) ) as $index1 => $value1) {

		  $value1 = explode("|", $value1);
		  if( count($value1) < 2) $value1[1] = $value1[0];
          $select .= "<option value=\"$index1\"" . ($fieldvalue == $value1[0] ? " selected" : "") . ">{$value1[1]}</option>\r\n";

        }

		$select .= "</select>";

		if ( isset($adminmode) ) {
		
		      $output .= <<<HTML
				<div class="form-group">
				  <label class="control-label col-md-3 col-sm-3">{$value[1]}:</label>
				  <div class="col-md-9 col-sm-9">
					{$select}
				  </div>
				 </div>
HTML;
		
		} else {

        $output .= <<<HTML

<tr>
<td>$value[1]:</td>
<td class="xprofile">{$select}</td>
</tr>
HTML;

		$xfieldinput[$fieldname] = $select;

		}
      }
}

    }
    break;

case "admin":
    $output = "";
    if (!isset($xfieldsid)) $xfieldsid = "";
    $xfieldsdata = xfieldsdataload ($xfieldsid);
	
    foreach ($xfields as $name => $value) {
		$fieldname = totranslit(trim($value[0]));
		$value[1] = htmlspecialchars($value[1], ENT_QUOTES, $config['charset'] );

        $fieldvalue = $xfieldsdata[$value[0]];
        $fieldvalue = $parse->decodeBBCodes($fieldvalue, false);


     if ($value[3] == "textarea") {      
      $output .= <<<HTML
<tr>
<td style="padding:4px;">$value[1]:</td>
<td class="xprofile" colspan="2"><textarea name="xfield[$fieldname]" id="xf_$fieldname">$fieldvalue</textarea></td></tr>
HTML;
      } elseif ($value[3] == "text") {
        $output .= <<<HTML
<tr>
<td style="padding:4px;">$value[1]:</td>
<td class="xprofile" colspan="2"><input type="text" name="xfield[$fieldname]" id="xfield[$fieldname]" value="$fieldvalue" /></td>
</tr>
HTML;
      } elseif ($value[3] == "select") {

        $output .= <<<HTML

<tr>
<td style="padding:4px;">$value[1]:</td>
<td class="xprofile" colspan="2"><select name="xfield[$fieldname]" id="xf_$fieldname">
HTML;
		
        foreach (explode("\r\n", htmlspecialchars($value[6], ENT_QUOTES, $config['charset']) ) as $index => $value) {

		  $value = explode("|", $value);
		  if( count($value) < 2) $value[1] = $value[0];
		  
          $output .= "<option value=\"$index\"" . ($fieldvalue == $value[0] ? " selected" : "") . ">{$value[1]}</option>\r\n";
        }

$output .= <<<HTML
</select></td>
</tr>
HTML;
      }

    }
    break;
  case "init":
    $postedxfields = $_POST['xfield'];
    $newpostedxfields = array();
    if (!isset($xfieldsid)) $xfieldsid = "";
    $xfieldsdata = xfieldsdataload ($xfieldsid);

    foreach ($xfields as $name => $value) {
		if ((!$value[2] AND $xfieldsadd)) {
			continue;
		}

		if (intval($value[4]) OR $member_id['user_group'] == 1 OR ($value[2] AND $xfieldsadd)) {
			
	      $newpostedxfields[$value[0]] = substr($postedxfields[$value[0]], 0, 10000);
		  
		} else $newpostedxfields[$value[0]] = $xfieldsdata[$value[0]];

	    if ($value[3] == "select") {
	        $options = explode("\r\n", $value[6]);

			if (intval($value[4]) OR $user_group[$member_id['user_group']]['admin_editusers'] OR ($value[2] AND $xfieldsadd)) {
				$options = explode("|", $options[$postedxfields[$value[0]]] );
		        $newpostedxfields[$value[0]] = $options[0];
			} else {
				$newpostedxfields[$value[0]] = $xfieldsdata[$value[0]];
			}
	    }

	}

    $postedxfields = $newpostedxfields;
    break;
  default:
  if (function_exists('msg'))
    msg("error", $lang['xfield_error'], $lang['xfield_xerr2']);
}
?>