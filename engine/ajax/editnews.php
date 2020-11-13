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
 File: editnews.php
-----------------------------------------------------
 Use: AJAX news edit
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

require_once (DLEPlugins::Check(ENGINE_DIR . '/classes/parse.class.php'));

$parse = new ParseFilter();

if( !$is_logged ) die( "error" );

$id = intval( $_REQUEST['id'] );

if( !$id ) die( "error" );

if( $_REQUEST['action'] == "edit" ) {
	$row = $db->super_query( "SELECT p.id, p.autor, p.date, p.short_story, p.full_story, p.xfields, p.title, p.category, p.approve, p.allow_br, e.reason FROM " . PREFIX . "_post p LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE p.id = '$id'" );
	
	if( $id != $row['id'] ) die( "error" );
	
	$cat_list = explode( ',', $row['category'] );
	
	$have_perm = 0;

	if( $user_group[$member_id['user_group']]['allow_edit'] and $row['autor'] == $member_id['name'] ) {
		$have_perm = 1;
	}
	
	if( $user_group[$member_id['user_group']]['allow_all_edit'] ) {
		$have_perm = 1;
		
		$allow_list = explode( ',', $user_group[$member_id['user_group']]['cat_add'] );
		
		foreach ( $cat_list as $selected ) {
			if( $allow_list[0] != "all" and ! in_array( $selected, $allow_list ) ) $have_perm = 0;
		}
	}
	
	if( $user_group[$member_id['user_group']]['max_edit_days'] ) {
		$newstime = strtotime( $row['date'] );
		$maxedittime = $_TIME - ($user_group[$member_id['user_group']]['max_edit_days'] * 3600 * 24);
		if( $maxedittime > $newstime ) $have_perm = 0;
	}
	
	if( ($member_id['user_group'] == 1) ) {
		$have_perm = 1;
	}

	
	if( !$have_perm ) die( $lang['editnews_error'] );

	if( !$user_group[$member_id['user_group']]['allow_html'] ) $config['allow_quick_wysiwyg'] = false;
	
	$news_txt = $row['short_story'];
	$full_txt = $row['full_story'];
	$author = urlencode($row['autor']);

	if( $row['allow_br'] AND !$config['allow_quick_wysiwyg'] ) {
		
		$news_txt = $parse->decodeBBCodes( $news_txt, false );
		$full_txt = $parse->decodeBBCodes( $full_txt, false );
		$fix_br = "checked";
	
	} else {
		
		if( $config['allow_quick_wysiwyg'] ) {
			$news_txt = $parse->decodeBBCodes( $news_txt, true, $config['allow_quick_wysiwyg'] );
			$full_txt = $parse->decodeBBCodes( $full_txt, true, $config['allow_quick_wysiwyg'] );
		} else { 
			$news_txt = $parse->decodeBBCodes( $news_txt, true, false );
			$full_txt = $parse->decodeBBCodes( $full_txt, true, false );

		}
		
		$fix_br = "";
	
	}

	if( $row['approve'] ) {
		$fix_approve = "checked";
	} else $fix_approve = "";
	
	$row['title'] = $parse->decodeBBCodes( $row['title'], false );

	$xfields = xfieldsload();
	$xfieldsdata = xfieldsdataload ($row['xfields']);
	$xfbuffer = "";

	foreach ($xfields as $name => $value) {
		$fieldname = $value[0];
		$fieldcount = md5($fieldname);

		if ( isset($xfieldsdata[$value[0]]) ) $fieldvalue = $xfieldsdata[$value[0]]; else continue;

		if( $value[19] ) {
			
			$value[19] = explode( ',', $value[19] );
			
			if( $value[19][0] AND !in_array( $member_id['user_group'], $value[19] ) ) {
				continue;
			}
			
		}
		
		$value[1] = htmlspecialchars($value[1], ENT_QUOTES, $config['charset'] );
		 
		$fieldvalue = str_ireplace( "&#123;title", "{title", $fieldvalue );
		$fieldvalue = str_ireplace( "&#123;short-story", "{short-story", $fieldvalue );
		$fieldvalue = str_ireplace( "&#123;full-story", "{full-story", $fieldvalue );

		if ($value[8] OR $value[6] OR $value[3] == "image" OR $value[3] == "imagegalery" OR $value[3] == "file") {
			
			$fieldvalue = html_entity_decode(stripslashes($fieldvalue), ENT_QUOTES, $config['charset']);
			$fieldvalue = htmlspecialchars($fieldvalue, ENT_QUOTES, $config['charset'] );
			
		} elseif($value[3] == "htmljs") {
			
			 $fieldvalue = htmlspecialchars($fieldvalue, ENT_QUOTES, $config['charset'] );
			 
		} else {
			
			if( $row['allow_br'] AND !$config['allow_quick_wysiwyg'] ) {
				
				$fieldvalue = $parse->decodeBBCodes( $fieldvalue, false );
			
			} else {
				
				if( $config['allow_quick_wysiwyg'] ) $fieldvalue = $parse->decodeBBCodes( $fieldvalue, true, $config['allow_quick_wysiwyg'] );
				else $fieldvalue = $parse->decodeBBCodes( $fieldvalue, true, false );
			
			}

		}
		

		if ($value[3] == "textarea") {
			
			if ( $value[7] ) {
				
				if ( !$config['allow_quick_wysiwyg'] ) {
	
					$params = "onfocus=\"setNewField(this.id, document.ajaxnews{$id})\" class=\"quick-edit-textarea\" "; 
					$class_name = "bb-editor";
					$panel="<!--panel-->";
					
				} else {
	
					$params = "class=\"wysiwygeditor\" ";
					$class_name = "";
					$panel="";
				}
				
			} else {
				$params = "class=\"quick-edit-textarea\" ";
				$class_name = "";
				$panel="";
			}
		
			 $xfbuffer .= "<div class=\"xfieldsrow\">{$value[1]}:<br /><div class=\"{$class_name}\">{$panel}<textarea name=\"xfield[{$fieldname}]\" id=\"xf_$fieldname\" {$params}>{$fieldvalue}</textarea></div></div>";

		} elseif ($value[3] == "htmljs") {
			
			 $xfbuffer .= "<div class=\"xfieldsrow\">{$value[1]}:<br /><textarea name=\"xfield[{$fieldname}]\" id=\"xf_$fieldname\" class=\"quick-edit-textarea\">{$fieldvalue}</textarea></div>";

		} elseif ($value[3] == "text") {

			$fieldvalue = str_replace('&amp;', '&', $fieldvalue);

			$xfbuffer .= "<div class=\"xfieldsrow\"><div class=\"xfieldscolleft\">{$value[1]}:</div><div class=\"xfieldscolright\"><input type=\"text\" name=\"xfield[{$fieldname}]\" id=\"xfield[{$fieldname}]\" value=\"{$fieldvalue}\" class=\"quick-edit-text\" /></div></div>";

		} elseif ($value[3] == "select") { 

			$fieldvalue = str_replace('&amp;', '&', $fieldvalue);

			$xfbuffer .= "<div class=\"xfieldsrow\"><div class=\"xfieldscolleft\">{$value[1]}:</div><div class=\"xfieldscolright\"><select name=\"xfield[{$fieldname}]\" class=\"quick-edit-select\">";

	        foreach (explode("\r\n", htmlspecialchars($value[4], ENT_QUOTES, $config['charset'] )) as $index => $value) {
			  
			  $value = explode("|", $value);
			  if( count($value) < 2) $value[1] = $value[0];
			  
	          $xfbuffer .= "<option value=\"$index\"" . ($fieldvalue == $value[0] ? " selected" : "") . ">$value[1]</option>\r\n";
	        }

			$xfbuffer .= "</select></div></div>";

		} elseif ($value[3] == "yesorno") {
			
			$fieldvalue = intval($fieldvalue);
			
			$xfbuffer .= "<div class=\"xfieldsrow\"><div class=\"xfieldscolleft\">{$value[1]}:</div><div class=\"xfieldscolright\"><select name=\"xfield[{$fieldname}]\" class=\"quick-edit-select\">";
			$xfbuffer .= "<option value=\"1\"" . ($fieldvalue == 1 ? " selected" : "") . ">{$lang['xfield_xyes']}</option>\r\n";
            $xfbuffer .= "<option value=\"0\"" . ($fieldvalue == 0 ? " selected" : "") . ">{$lang['xfield_xno']}</option>\r\n";
			$xfbuffer .= "</select></div></div>";

		} elseif( $value[3] == "image" ) {
			
			$max_file_size = (int)($value[10] * 1024);
			
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

$max_file_size = number_format($max_file_size, 0, '', '');

$uploadscript = <<<HTML
	new qq.FileUploader({
		element: document.getElementById('xfupload_{$fieldname}'),
		action:  dle_root + 'engine/ajax/controller.php?mod=upload',
		maxConnections: 1,
		multiple: false,
		allowdrop: false,
		encoding: 'multipart',
        sizeLimit: {$max_file_size},
		allowedExtensions: ['gif', 'jpg', 'jpeg', 'png', 'webp'],
	    params: {"subaction" : "upload", "news_id" : "{$row['id']}", "area" : "xfieldsimage", "author" : "{$author}", "xfname" : "{$fieldname}", "user_hash" : "{$dle_login_hash}"},
        template: '<div class="qq-uploader">' + 
                '<div id="uploadedfile_{$fieldname}" style="min-height: 2px;">{$up_image}</div>' +
                '<div class="qq-upload-button btn btn-green" style="width: auto;">{$lang['xfield_xfim']}</div>' +
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

							if( response.error ) $('#uploadfile-'+id+' .qq-status').append( '<br /><span style="color:red;">' + response.error + '</span>' );

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
	
	$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').attr("disabled","disabled");
	
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
			
			$xfbuffer .= "<div class=\"xfieldsrow\"><div class=\"xfieldscolleft\">{$value[1]}:</div><div class=\"xfieldscolright\"><div id=\"xfupload_{$fieldname}\"></div><input type=\"hidden\" name=\"xfield[$fieldname]\" id=\"xf_$fieldname\" value=\"{$fieldvalue}\" /><script>{$uploadscript}</script></div></div>";

		} elseif( $value[3] == "imagegalery" ) {

	    $max_file_size = (int)($value[10] * 1024);

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
		
$max_file_size = number_format($max_file_size, 0, '', '');

$uploadscript = <<<HTML
	var maxallowfiles_{$fieldcount} = {$value[16]};
	var totaluploaded_{$fieldcount} = {$totaluploadedfiles};
	var totalqueue_{$fieldcount} = 0;
	
	function xfimagegalerydelete_{$fieldcount} ( xfname, xfvalue, id )
	{
		DLEconfirm( '{$lang['image_delete']}', '{$lang['p_info']}', function () {
		
			ShowLoading('');
	
			$.post(dle_root + 'engine/ajax/controller.php?mod=upload', { subaction: 'deluploads', user_hash: '{$dle_login_hash}', news_id: '{$row['id']}', author: '{$author}', 'images[]' : xfvalue }, function(data){
	
				HideLoading('');

				$('#xf_'+id).remove();
				totaluploaded_{$fieldcount} --;
				xfsinc('{$fieldname}');
				
				$('#xfupload_' + xfname + ' .qq-upload-button, #xfupload_' + xfname + ' .qq-upload-button input').removeAttr('disabled');
			});
			
		} );
		
		return false;

	};
	
	var uploader_{$fieldcount} = new qq.FileUploader({
		element: document.getElementById('xfupload_{$fieldname}'),
		action: dle_root + 'engine/ajax/controller.php?mod=upload',
		maxConnections: 1,
		multiple: true,
		allowdrop: false,
		encoding: 'multipart',
        sizeLimit: {$max_file_size},
		allowedExtensions: ['gif', 'jpg', 'jpeg', 'png', 'webp'],
	    params: {"subaction" : "upload", "news_id" : "{$row['id']}", "area" : "xfieldsimagegalery", "author" : "{$author}", "xfname" : "{$fieldname}", "user_hash" : "{$dle_login_hash}"},
        template: '<div class="qq-uploader">' + 
                '<div id="uploadedfile_{$fieldname}" style="min-height: 2px;">{$up_image}</div>' +
                '<div class="qq-upload-button btn btn-green" style="width: auto;">{$lang['xfield_xfimg']}</div>' +
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

							if( response.error ) $('#uploadfile-'+id+' .qq-status').append( '<br /><span style="color:red;">' + response.error + '</span>' );

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

			$xfbuffer .= "<div class=\"xfieldsrow\"><div class=\"xfieldscolleft\">{$value[1]}:</div><div class=\"xfieldscolright\"><div id=\"xfupload_{$fieldname}\"></div><input type=\"hidden\" name=\"xfield[$fieldname]\" id=\"xf_$fieldname\" value=\"{$fieldvalue}\" /><script>{$uploadscript}</script></div></div>";

		} elseif( $value[3] == "file" ) {
			
			$max_file_size = (int)($value[15] * 1024);
			$allowed_files = explode( ',', strtolower( $value[14] ) );
			$allowed_files = implode( "', '", $allowed_files );
	
			$fieldvalue = str_replace('&amp;', '&', $fieldvalue);
			
			if( $fieldvalue ) {
				
				$fileid = intval(preg_replace( "'\[attachment=(.*?):(.*?)\]'si", "\\1", $fieldvalue ));
				
				$fileid = "&nbsp;<button class=\"qq-upload-button btn btn-sm btn-red\" onclick=\"xffiledelete('".$fieldname."','".$fileid."');return false;\">{$lang['xfield_xfid']}</button>";
	
				$show="display:inline-block;";
				
			} else { $show="display:none;"; $fileid="";}

			$max_file_size = number_format($max_file_size, 0, '', '');
			
$uploadscript = <<<HTML
	new qq.FileUploader({
		element: document.getElementById('xfupload_{$fieldname}'),
		action: dle_root + 'engine/ajax/controller.php?mod=upload',
		maxConnections: 1,
		multiple: false,
		allowdrop: false,
		encoding: 'multipart',
        sizeLimit: {$max_file_size},
		allowedExtensions: ['{$allowed_files}'],
	    params: {"subaction" : "upload", "news_id" : "{$row['id']}", "area" : "xfieldsfile", "author" : "{$author}", "xfname" : "{$fieldname}", "user_hash" : "{$dle_login_hash}"},
        template: '<div class="qq-uploader">' + 
                '<div class="qq-upload-button btn btn-green" style="width: auto;">{$lang['xfield_xfif']}</div>' +
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

							if( response.error ) $('#uploadfile-'+id+' .qq-status').append( '<br /><span style="color:red;">' + response.error + '</span>' );

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
	
	$('#xfupload_{$fieldname} .qq-upload-button, #xfupload_{$fieldname} .qq-upload-button input').attr("disabled","disabled");
HTML;

			$xfbuffer .= "<div class=\"xfieldsrow\"><div class=\"xfieldscolleft\">{$value[1]}:</div><div class=\"xfieldscolright\"><input style=\"{$show}\" class=\"quick-edit-text\" type=\"text\" name=\"xfield[$fieldname]\" id=\"xf_$fieldname\" value=\"{$fieldvalue}\" /><span id=\"uploadedfile_{$fieldname}\">{$fileid}</span><div id=\"xfupload_{$fieldname}\"></div><script>{$uploadscript}</script></div></div>";
		
		}
	
	}
	
	$addtype = "addnews";
	
	if( !$config['allow_quick_wysiwyg'] ) {
		
		include_once (DLEPlugins::Check(ENGINE_DIR . '/ajax/bbcode.php'));
		$xfbuffer = str_replace ("<!--panel-->", $code, $xfbuffer);
	
	} else {

		$p_name = urlencode($row['autor']);

		if ( $config['allow_quick_wysiwyg'] == "2") {

			if ( $user_group[$member_id['user_group']]['allow_image_upload'] OR $user_group[$member_id['user_group']]['allow_file_upload'] ) $image_upload = "dleupload "; else $image_upload = "";

			if($config['bbimages_in_wysiwyg']) {
				$implugin = 'dleimage';
			} else $implugin = 'image';
			
			$js_code = <<<HTML
<script>
var text_upload = "$lang[bb_t_up]";

setTimeout(function() {

	tinymce.remove('textarea.wysiwygeditor');
	
	tinyMCE.baseURL = dle_root + 'engine/editor/jscripts/tiny_mce';
	tinyMCE.suffix = '.min';
	
	tinymce.init({
		selector: 'textarea.wysiwygeditor',
		language : "{$lang['wysiwyg_language']}",
		element_format : 'html',
		width : "100%",
		height : "280",
		theme: "modern",
		plugins: ["advlist autolink lists link image charmap anchor searchreplace visualblocks visualchars fullscreen media nonbreaking table contextmenu emoticons paste textcolor codemirror spellchecker dlebutton codesample"],
		relative_urls : false,
		convert_urls : false,
		remove_script_host : false,
		verify_html: false,
		branding: false,
	    formats: {
	      bold: {inline: 'b'},  
	      italic: {inline: 'i'},
	      underline: {inline: 'u', exact : true},  
	      strikethrough: {inline: 's', exact : true}
	    },
		codesample_languages: [ {text: 'HTML/JS/CSS', value: 'markup'}],
		image_caption: true,
		image_advtab: true,
		image_dimensions: false,
		toolbar_items_size: 'small',
		menubar: false,
		toolbar1: "fontselect fontsizeselect table link anchor dleleech unlink {$image_upload}{$implugin} dleemo dlemp dletube dlaudio dlequote dlespoiler codesample dlebreak dlepage code",
		toolbar2: "undo redo copy paste pastetext bold italic underline strikethrough alignleft aligncenter alignright alignjustify subscript superscript bullist numlist forecolor backcolor spellchecker removeformat",

		dle_root : dle_root,
		dle_upload_area : "short_story",
		dle_upload_user : "{$p_name}",
		dle_upload_news : "{$row['id']}",
		
		spellchecker_language : "ru",
		spellchecker_languages : "Russian=ru,Ukrainian=uk,English=en",
		spellchecker_rpc_url : "https://speller.yandex.net/services/tinyspell",
		content_css : "{$config['http_home_url']}engine/editor/css/content.css"
	});

}, 100);

</script>
HTML;

		
		} else {


			if ( $user_group[$member_id['user_group']]['allow_image_upload'] OR $user_group[$member_id['user_group']]['allow_file_upload'] ) {
				
				$image_upload = "'dleupload',";
				$image_q_upload = ", 'imageUpload'";
				
			} else { $image_upload = ""; $image_q_upload = ""; }
			
			if($config['bbimages_in_wysiwyg']) {
				$implugin = 'dleimg';
			} else $implugin = 'insertImage';
	
			$js_code = <<<HTML
<script>
var text_upload = "$lang[bb_t_up]";

      $('.wysiwygeditor').froalaEditor({
        dle_root: dle_root,
        dle_upload_area : "short_story",
        dle_upload_user : "{$p_name}",
        dle_upload_news : "{$row['id']}",
        width: '100%',
        height: '280',
        zIndex: 9990,
        language: '{$lang['wysiwyg_language']}',

        imageAllowedTypes: ['jpeg', 'jpg', 'png', 'gif', 'webp'],
        imageDefaultWidth: 0,
        imageInsertButtons: ['imageBack', '|', 'imageByURL'{$image_q_upload}],
		imageUploadURL: dle_root + 'engine/ajax/controller.php?mod=upload',
		imageUploadParam: 'qqfile',
		imageUploadParams: { "subaction" : "upload", "news_id" : "{$row['id']}", "area" : "short_story", "author" : "{$p_name}", "mode" : "quickload", "user_hash" : "{$dle_login_hash}"},
        imageMaxSize: {$config['max_up_size']} * 1024,
        imagePaste: false,
		
        toolbarButtonsXS: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'align', 'indent', 'outdent', '|', 'subscript', 'superscript', '|', 'insertTable', 'formatOL', 'formatUL', 'insertHR', '|', 'clearFormatting', 'dlecode', '|', 'html', '-', 
                         'fontFamily', 'fontSize', '|', 'color', 'paragraphFormat', 'paragraphStyle', '|', 'insertLink', 'dleleech', '|', 'emoticons', '{$implugin}',{$image_upload}'|', 'insertVideo', 'dleaudio', 'dlemedia','|', 'dlehide', 'dlequote', 'dlespoiler'],

						 
        toolbarButtonsSM: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'align', 'indent', 'outdent', '|', 'subscript', 'superscript', '|', 'insertTable', 'formatOL', 'formatUL', 'insertHR', '|', 'clearFormatting', 'dlecode', '|', 'html', '-', 
                         'fontFamily', 'fontSize', '|', 'color', 'paragraphFormat', 'paragraphStyle', '|', 'insertLink', 'dleleech', '|', 'emoticons', '{$implugin}',{$image_upload}'|', 'insertVideo', 'dleaudio', 'dlemedia','|', 'dlehide', 'dlequote', 'dlespoiler'],

        toolbarButtonsMD: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'align', 'indent', 'outdent', '|', 'subscript', 'superscript', '|', 'insertTable', 'formatOL', 'formatUL', 'insertHR', '|', 'clearFormatting', 'dlecode', '|', 'html', '-', 
                         'fontFamily', 'fontSize', '|', 'color', 'paragraphFormat', 'paragraphStyle', '|', 'insertLink', 'dleleech', '|', 'emoticons', '{$implugin}',{$image_upload}'|', 'insertVideo', 'dleaudio', 'dlemedia','|', 'dlehide', 'dlequote', 'dlespoiler'],

        toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'align', 'indent', 'outdent', '|', 'subscript', 'superscript', '|', 'insertTable', 'formatOL', 'formatUL', 'insertHR', '|', 'clearFormatting', 'dlecode', '|', 'html', '-', 
                         'fontFamily', 'fontSize', '|', 'color', 'paragraphFormat', 'paragraphStyle', '|', 'insertLink', 'dleleech', '|', 'emoticons', '{$implugin}',{$image_upload}'|', 'insertVideo', 'dleaudio', 'dlemedia','|', 'dlehide', 'dlequote', 'dlespoiler']

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
		}

		$code = "";	
	}

	if ( !$config['allow_quick_wysiwyg'] ) $params = "onfocus=\"setNewField(this.name, document.ajaxnews{$id})\" class=\"quick-edit-textarea\""; else $params = "class=\"wysiwygeditor\"";

	if($news_txt) {
	
		$short_area = <<<HTML
<div class="xfieldsrow"><b>{$lang['s_fshort']}</b>
<div class="bb-editor">
{$code}
<textarea id="news_txt" name="news_txt" {$params}>{$news_txt}</textarea>
</div>
</div>
HTML;

	}

	if($full_txt) {
	
		$full_area = <<<HTML
<div class="xfieldsrow"><b>{$lang['s_ffull']}</b>
<div class="bb-editor">
{$code}
<textarea id="full_txt" name="full_txt" {$params}>{$full_txt}</textarea>
</div>
</div>
HTML;

	}

if( !$config['allow_quick_wysiwyg'] ) {
	
	$fix_br = "&nbsp;&nbsp;<label><input type=\"checkbox\" name=\"allow_br\" value=\"1\" {$fix_br}>{$lang['aj_allowbr']}</label>";
	
} else $fix_br ="";

	$buffer = <<<HTML
	<script src="{$config['http_home_url']}engine/classes/js/sortable.js"></script>
<script src="{$config['http_home_url']}engine/classes/uploads/html5/fileuploader.js"></script>
<form name="ajaxnews{$id}" id="ajaxnews{$id}" metod="post" action="">
<div><input type="text" name="title" class="quick-edit-text" value="{$row['title']}" /></div>
{$short_area}
{$full_area}
{$xfbuffer}
<div class="xfieldsrow"><div class="xfieldscolleft">{$lang['reason']}</div><div class="xfieldscolright"><input type="text" name="reason" class="quick-edit-text" value="{$row['reason']}"></div></div>
<div class="xfieldsrow"><label><input type="checkbox" name="approve" value="1" {$fix_approve}>{$lang['add_al_ap']}</label>{$fix_br}</div>
</form>
{$js_code}
<script>
	function xfimagedelete( xfname, xfvalue ) {
		
		DLEconfirm( '{$lang['image_delete']}', '{$lang['p_info']}', function () {
		
			ShowLoading('');
			
			$.post(dle_root + 'engine/ajax/controller.php?mod=upload', { subaction: 'deluploads', user_hash: '{$dle_login_hash}', news_id: '{$id}', author: '{$author}', 'images[]' : xfvalue }, function(data){
	
				HideLoading('');
				
				$('#uploadedfile_'+xfname).html('');
				$('#xf_'+xfname).val('');
				$('#xfupload_' + xfname + ' .qq-upload-button, #xfupload_' + xfname + ' .qq-upload-button input').removeAttr('disabled');
			});
			
		} );

		return false;

	};
	function xffiledelete( xfname, xfvalue ) {
		
		DLEconfirm( '{$lang['file_delete']}', '{$lang['p_info']}', function () {
		
			ShowLoading('');
			
			$.post(dle_root + 'engine/ajax/controller.php?mod=upload', { subaction: 'deluploads', user_hash: '{$dle_login_hash}', news_id: '{$id}', author: '{$author}', 'files[]' : xfvalue }, function(data){
	
				HideLoading('');
				
				$('#uploadedfile_'+xfname).html('');
				$('#xf_'+xfname).val('');
				$('#xf_'+xfname).hide('');
				$('#xfupload_' + xfname + ' .qq-upload-button, #xfupload_' + xfname + ' .qq-upload-button input').removeAttr('disabled');
			});
			
		} );

		return false;

	};
	
	function xfaddalt( id, xfname ) {
	
		var sel_alt = $('#xf_'+id).data('alt').toString().trim();
		sel_alt = sel_alt.replace(/"/g, '&quot;');
		sel_alt = sel_alt.replace(/'/g, '&#039;');

		DLEprompt('{$lang['bb_alt_image']}', sel_alt, '{$lang['p_prompt']}', function (r) {
			r = r.replace(/</g, '');
			r = r.replace(/>/g, '');
			r = r.replace(/,/g, '&#44;');
			
			$('#xf_'+id).data('alt', r);
			xfsinc(xfname);
		
		}, true);
		
	};
	
	function xfsinc(xfname) {
	
		var order = [];
		
		$( '#uploadedfile_' + xfname + ' .uploadedfile' ).each(function() {
			var xfurl = $(this).data('id').toString().trim();
			var xfalt = $(this).data('alt').toString().trim();
			
			if(xfalt) {
				order.push(xfalt + '|'+ xfurl);
			} else {
				order.push(xfurl);
			}

		});
	
		$('#xf_' + xfname).val(order.join(','));
	};
</script>	
HTML;

} elseif( $_REQUEST['action'] == "save" ) {
	
	if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die ("error");
	
	}
	
	$row = $db->super_query( "SELECT id, date, xfields, title, category, approve, short_story, full_story, autor FROM " . PREFIX . "_post where id = '$id'" );
	
	if( $id != $row['id'] ) die( "News Not Found" );
	
	$cat_list = explode( ',', $row['category'] );
	
	$have_perm = 0;
	
	if( $user_group[$member_id['user_group']]['allow_all_edit'] ) {
		$have_perm = 1;
		
		$allow_list = explode( ',', $user_group[$member_id['user_group']]['cat_add'] );
		
		foreach ( $cat_list as $selected ) {
			if( $allow_list[0] != "all" and ! in_array( $selected, $allow_list ) ) $have_perm = 0;
		}
	}
	
	if( $user_group[$member_id['user_group']]['allow_edit'] and $row['autor'] == $member_id['name'] ) {
		$have_perm = 1;
	}
	
	if( $user_group[$member_id['user_group']]['max_edit_days'] ) {
		$newstime = strtotime( $row['date'] );
		$maxedittime = $_TIME - ($user_group[$member_id['user_group']]['max_edit_days'] * 3600 * 24);
		if( $maxedittime > $newstime ) $have_perm = 0;
	}
	
	if( ($member_id['user_group'] == 1) ) {
		$have_perm = 1;
	}
	
	if( ! $have_perm ) die( "Access it is refused" );
	
	$allow_br = isset( $_REQUEST['allow_br'] ) ? intval( $_REQUEST['allow_br'] ) : 0;
	$approve = isset(  $_REQUEST['approve'] ) ? intval(  $_REQUEST['approve'] ) : 0;

	if( !$user_group[$member_id['user_group']]['moderation'] ) $approve = 0;
	
	if( !$config['allow_quick_wysiwyg'] AND $allow_br ) $use_html = false;
	else $use_html = true;

	$_POST['title'] = $db->safesql( $parse->process( trim( strip_tags ($_POST['title'] ) ) ) );

	if ( $config['allow_quick_wysiwyg'] ) $parse->allow_code = false;

	if ( !$user_group[$member_id['user_group']]['allow_html'] ) {

		$_POST['news_txt'] = strip_tags ($_POST['news_txt']);
		$_POST['full_txt'] = strip_tags ($_POST['full_txt']);

	}

	$news_txt = $db->safesql($parse->BB_Parse( $parse->process( $_POST['news_txt'] ), $use_html ));
	$full_txt = $db->safesql($parse->BB_Parse( $parse->process( $_POST['full_txt'] ), $use_html ));

	$add_module = "yes";
	$ajax_edit = "yes";
	$stop = "";
	$category = $cat_list;
	$xf_existing = xfieldsdataload($row['xfields']);
	$xfieldsaction = "init";
	include (DLEPlugins::Check(ENGINE_DIR . '/inc/xfields.php'));

	$editreason = $db->safesql( htmlspecialchars( strip_tags( stripslashes( trim( $_POST['reason'] ) ) ), ENT_QUOTES, $config['charset'] ) );
	
	if( $editreason != "" ) $view_edit = 1;
	else $view_edit = 0;
	$added_time = time();
	
	if( !trim($_POST['title']) ) die( $lang['add_err_7'] );

	if ($parse->not_allowed_text ) die( $lang['news_err_39'] );

	$db->query( "UPDATE " . PREFIX . "_post SET title='{$_POST['title']}', short_story='$news_txt', full_story='$full_txt', xfields='$filecontents', approve='$approve', allow_br='$allow_br' WHERE id = '$id'" );
	$db->query( "UPDATE " . PREFIX . "_post_extras SET editdate='$added_time', editor='{$member_id['name']}', reason='$editreason', view_edit='$view_edit' WHERE news_id = '$id'" );

	$db->query( "DELETE FROM " . PREFIX . "_xfsearch WHERE news_id = '{$id}'" );

	if ( count($xf_search_words) AND $approve ) {
					
		$temp_array = array();
					
		foreach ( $xf_search_words as $value ) {
						
			$temp_array[] = "('" . $id . "', '" . $value[0] . "', '" . $value[1] . "')";
		}
					
		$xf_search_words = implode( ", ", $temp_array );
		$db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue) VALUES " . $xf_search_words );
	}

	if( $row['category'] AND $approve != $row['approve'] ) {
		
		$db->query( "DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id = '{$id}'" );

		if($approve) {

			$cat_ids = array ();
	
			$cat_ids_arr = explode( ",", $row['category'] );
	
			foreach ( $cat_ids_arr as $value ) {
	
				$cat_ids[] = "('" . $id . "', '" . trim( $value ) . "')";
	
			}
	
			$cat_ids = implode( ", ", $cat_ids );
			$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );

		}

	}
	
	$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '25', '{$_POST['title']}')" );

	if ( $config['allow_alt_url'] AND !$config['seo_type'] ) $cprefix = "full_"; else $cprefix = "full_".$id;	

	clear_cache( array( 'news_', 'rss', $cprefix ) );
	
	$buffer = "ok";

} else die( "error" );

$db->close();

echo $buffer;
?>