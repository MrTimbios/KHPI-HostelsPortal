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
 Files: files.php
-----------------------------------------------------
 Use: manage uploaded pictures
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( $member_id['user_group'] != 1 ) {
	msg( "error", $lang['index_denied'], $lang['index_denied'] );
}

if( $_GET['userdir'] ) $userdir = totranslit( $_GET['userdir'], true, false ) . "/"; else $userdir = "";
if( $_GET['sub_dir'] ) $sub_dir = totranslit( $_GET['sub_dir'], true, false ) . "/"; else $sub_dir = "";

$max_file_size = (int)($config['max_up_size'] * 1024);
$allowed_extensions = array ("gif", "jpg", "png", "webp" );
$simple_ext = implode( "', '", $allowed_extensions );


if ( $userdir == "files/" ) msg( "error", $lang['addnews_denied'], $lang['index_denied'] );

$config_path_image_upload = ROOT_DIR . "/uploads/" . $userdir . $sub_dir;

if( ! @is_dir( $config_path_image_upload ) ) msg( "error", $lang['addnews_denied'], "Directory {$userdir} not found" );

if( $action == "doimagedelete" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( ! isset( $_POST['images'] ) ) {
		msg( "error", $lang['images_delerr'], $lang['images_delerr_1'], "?mod=files" );
	}

	foreach ( $_POST['images'] as $image ) {

		$image = totranslit($image);

		if( stripos ( $image, ".htaccess" ) !== false ) die("Hacking attempt!");

		$img_name_arr = explode( ".", $image );
		$type = totranslit( end( $img_name_arr ) );

		if( !in_array( $type, $allowed_extensions ) ) die("Hacking attempt!");

		@unlink( $config_path_image_upload . $image );
		@unlink( $config_path_image_upload . "thumbs/" . $image );
		@unlink( $config_path_image_upload . "medium/" . $image );

		$db->query( "INSERT INTO " . USERPREFIX . "_admin_logs (name, date, ip, action, extras) values ('".$db->safesql($member_id['name'])."', '{$_TIME}', '{$_IP}', '37', '{$image}')" );

	}
}

	$js_array[] = "engine/classes/uploads/html5/fileuploader.js";

	echoheader( "<i class=\"fa fa-file-image-o position-left\"></i><span class=\"text-semibold\">{$lang['header_f_1']}</span>", $lang['header_f_2'] );


$folder_list = "<select class=\"uniform\" onchange=\"window.open(this.options[this.selectedIndex].value,'_top')\"><option value=\"?mod=files\">--</option>";
	
	$current_dir = opendir( ROOT_DIR . "/uploads" );
	
	while ( $entryname = readdir( $current_dir ) ) {
		
		if( is_dir( ROOT_DIR . "/uploads/$entryname" ) AND ($entryname != "." and $entryname != ".." and $entryname != "files") ) {
			
			if( $userdir == $entryname . "/" ) $sel_dir = "selected";
			else $sel_dir = "";
			
			if( $entryname == "fotos" ) $listname = $lang['images_foto'];
			elseif( $entryname == "thumbs" ) $listname = $lang['images_thumb'];
			elseif( $entryname == "posts" ) $listname = $lang['images_news'];
			else $listname = $entryname;
			
			$folder_list .= "<option value=\"?mod=files&userdir=" . str_replace( ' ', '%20', $entryname ) . "\" {$sel_dir}>{$listname}</option>";

		}
	}

	$current_dir = opendir( ROOT_DIR . "/uploads/posts" );
	
	while ( $entryname = readdir( $current_dir ) ) {
		
		if( is_dir( ROOT_DIR . "/uploads/posts/$entryname" ) and ($entryname != "." and $entryname != ".." and $entryname != "thumbs") ) {
			
			if( $sub_dir == $entryname . "/" ) $sel_dir = "selected";
			else $sel_dir = "";
			
			$folder_list .= "<option value=\"?mod=files&userdir=posts&sub_dir=" . str_replace( ' ', '%20', $entryname ) . "\" {$sel_dir}>{$lang['images_news']} / {$entryname}</option>";
		}
	}

	$folder_list .= "</select>";

	echo <<<HTML
<form action="" method="post">
<div class="panel panel-default">
  <div class="panel-heading">
    {$lang['uploaded_file_list']}
  </div>
  <div class="table-responsive">
  <table class="table table-xs table-hover">
      <thead>
      <tr>
        <th>{$lang['files_head_1']}</th>
        <th style="width: 200px">{$lang['files_head_2']}</th>
		<th style="width: 200px">{$lang['files_head_3']}</th>
        <th style="width: 40px"></th>
      </tr>
      </thead>
	  <tbody>
HTML;


$img_dir = opendir( $config_path_image_upload );
$i = 0;
$total_size = 0;

while ( $file = readdir( $img_dir ) ) {
	$images_in_dir[] = $file;
}

natcasesort( $images_in_dir );
reset( $images_in_dir );

foreach ( $images_in_dir as $file ) {
	
	$img_type = explode( ".", $file );
	$img_type = totranslit( end( $img_type ) );
	
	if( in_array( $img_type, $allowed_extensions ) AND is_file( $config_path_image_upload . $file ) ) {
		
		$i ++;
		$this_size = @filesize( $config_path_image_upload . $file );
		$img_info = @getimagesize( $config_path_image_upload . $file );
		$total_size += $this_size;
		
		echo "
	  <tr>
	  <td><a target=_blank href=\"" . $config['http_home_url'] . "uploads/" . $userdir . $sub_dir . "$file\">$file</a></td>
	  <td>$img_info[0]x$img_info[1]</td>
	  <td>" . formatsize( $this_size ) . "</td>
	  <td><input type=\"checkbox\" class=\"icheck\" name=\"images[{$file}]\" value=\"$file\"></td>
	  </tr>";

	}
}

if( !$total_size ) {
	echo "<tr><td colspan=\"4\" align=\"center\" height=\"40\">" . $lang['files_head_4'] . "</td></tr>";
}


	echo "</tbody></table><div class=\"panel-footer\">
		<div id=\"file-uploader\" style=\"width:210px;float:left;\"></div>
		<div style=\"float:right;\"><input class=\"btn bg-danger btn-sm btn-raised\" type=\"submit\" value=\" {$lang['images_del']} \"><input type=\"hidden\" name=\"action\" value=\"doimagedelete\"><input type=\"hidden\" name=\"user_hash\" value=\"$dle_login_hash\" /></div>
	</div>";




	if( $_GET['userdir'] ) $userdir = totranslit( $_GET['userdir'], true, false ); else $userdir = "";
	if( $_GET['sub_dir'] ) $subdir = totranslit( $_GET['sub_dir'], true, false ); else $subdir = "";

	$total_size = formatsize( $total_size );
	$max_file_size = number_format($max_file_size, 0, '', '');

	echo <<<HTML
   </div>
</div>
<div class="pull-left mb-20"><span class="position-left">{$lang['images_listdir']}</span>{$folder_list}</div>
<div class="pull-right">{$lang['images_size']} {$total_size}</div>
</form>
<script>
jQuery(function($){
	$(function() {
		$('.table').find('tr > td:last-child').find('input[type=checkbox]').on('change', function() {
			if($(this).is(':checked')) {
				$(this).parents('tr').addClass('warning');
			}
			else {
				$(this).parents('tr').removeClass('warning');
			}
		});
	});

	var totaladded = 0;
	var totaluploaded = 0;

	var uploader = new qq.FileUploader({
		element: document.getElementById('file-uploader'),
		action: 'engine/ajax/controller.php?mod=upload',
		maxConnections: 1,
		encoding: 'multipart',
        sizeLimit: {$max_file_size},
		allowedExtensions: ['{$simple_ext}'],
	    params: {"subaction" : "upload", "news_id" : "0", "area" : "adminupload", "userdir" : "{$userdir}", "subdir" : "{$subdir}", "user_hash" : "{$dle_login_hash}"},
        template: '<div class="qq-uploader">' + 
                '<div class="qq-upload-drop-area"><span>{$lang['media_upload_st5']}</span></div>' +
                '<div class="qq-upload-button btn bg-teal btn-sm btn-raised position-left" style="width: auto;">{$lang['media_upload_st14']}</div>' +
                '<ul class="qq-upload-list" style="display:none;"></ul>' + 
             '</div>',
		onSubmit: function(id, fileName) {

					totaladded ++;

					$('<div id="uploadfile-'+id+'" class="file-box"><span class="qq-upload-file">{$lang['media_upload_st6']}&nbsp;'+fileName+'</span><span class="qq-status"><span class="qq-upload-spinner"></span><span class="qq-upload-size"></span></span></div>').appendTo('#file-uploader');

        },
		onProgress: function(id, fileName, loaded, total){
					$('#uploadfile-'+id+' .qq-upload-size').text(uploader._formatSize(loaded)+' {$lang['media_upload_st8']} '+uploader._formatSize(total));
		},
		onComplete: function(id, fileName, response){
						totaluploaded ++;

						if ( response.success ) {

							$('#uploadfile-'+id+' .qq-status').html('{$lang['media_upload_st9']}');

							if (totaluploaded == totaladded ) setTimeout("location.replace( window.location )",2E3);


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
});
</script>
HTML;

	echofooter();

?>