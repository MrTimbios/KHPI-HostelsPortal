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
 File: antivirus.php
-----------------------------------------------------
 Use: Antivirus
=====================================================
*/

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
	echo $lang['sess_error'];
	die();
}
	
if($member_id['user_group'] != 1) {die ("error");}

require_once (DLEPlugins::Check(ENGINE_DIR.'/classes/antivirus.class.php'));

$antivirus = new antivirus();

if ($_REQUEST['folder'] == "lokal"){

	if( $antivirus->snap ) {

		$antivirus->scan_files( ROOT_DIR, false, true );

	} else {

		$antivirus->scan_files( ROOT_DIR."/backup", false );
		$antivirus->scan_files( ROOT_DIR."/engine", false);
		$antivirus->scan_files( ROOT_DIR."/language", false );
		$antivirus->scan_files( ROOT_DIR."/templates", false);
		$antivirus->scan_files( ROOT_DIR."/uploads", false );
		$antivirus->scan_files( ROOT_DIR."/upgrade", false );
		$antivirus->scan_files( ROOT_DIR, false);
	}

} elseif ($_REQUEST['folder'] == "snap") {

	$antivirus->scan_files( ROOT_DIR, true, true );

	$filecontents = "";

    foreach( $antivirus->snap_files as $idx => $data )
    {
		$filecontents .= $data['file_path']."|".$data['file_crc']."\r\n";
    }

    $filehandle = fopen(ENGINE_DIR.'/data/snap.db', "w+");
    fwrite($filehandle, $filecontents);
    fclose($filehandle);
	@chmod(ENGINE_DIR.'/data/snap.db', 0666);

} else {

	$antivirus->snap = false;
	$antivirus->scan_files( ROOT_DIR, false, true );

}

if ($_REQUEST['folder'] != "snap") {
	$con_content = @file_get_contents( ROOT_DIR . "/engine/data/config.php");

	if (strpos ( $con_content, "_SERVER" ) !== false OR strpos ( $con_content, "eval" ) !== false) {
	
		$file_date = date("d.m.Y H:i:s", filectime(ROOT_DIR . "/engine/data/config.php"));
		$file_size = filesize(ROOT_DIR . "/engine/data/config.php");
	
		 $antivirus->bad_files[] = array( 'file_path' => "/engine/data/config.php",
									'file_name' => "config.php",
									'file_date' => $file_date,
									'type' => 2,
									'file_size' => $file_size ); 
	}

	$con_content = @file_get_contents( ROOT_DIR . "/engine/data/dbconfig.php");

	if (strpos ( $con_content, "_SERVER" ) !== false OR strpos ( $con_content, "eval" ) !== false) {
	
		$file_date = date("d.m.Y H:i:s", filectime(ROOT_DIR . "/engine/data/dbconfig.php"));
		$file_size = filesize(ROOT_DIR . "/engine/data/dbconfig.php");
	
		 $antivirus->bad_files[] = array( 'file_path' => "/engine/data/dbconfig.php",
									'file_name' => "dbconfig.php",
									'file_date' => $file_date,
									'type' => 2,
									'file_size' => $file_size ); 
	}

	$con_content = @file_get_contents( ROOT_DIR . "/engine/data/videoconfig.php");

	if (strpos ( $con_content, "_SERVER" ) !== false OR strpos ( $con_content, "eval" ) !== false) {
	
		$file_date = date("d.m.Y H:i:s", filectime(ROOT_DIR . "/engine/data/videoconfig.php"));
		$file_size = filesize(ROOT_DIR . "/engine/data/videoconfig.php");
	
		$antivirus->bad_files[] = array( 'file_path' => "/engine/data/videoconfig.php",
									'file_name' => "videoconfig.php",
									'file_date' => $file_date,
									'type' => 2,
									'file_size' => $file_size ); 
	}

}

if (count($antivirus->bad_files)) {

echo <<<HTML
<div class="panel-body">{$lang['anti_result']}</div>
<div class="table-responsive">
<table class="table table-xs table-hover table-striped">
<thead>
    <tr>
        <th>{$lang['anti_file']}</th>
        <th>{$lang['anti_size']}</th>
        <th>{$lang['addnews_date']}</th>
        <th>&nbsp;</th>
    </tr>
</thead>
<tbody>
HTML;

  foreach( $antivirus->bad_files as $idx => $data )
  { 

	if ($data['file_size'] < 50000) $color = "<span style=\"color:green;\">";
	elseif ($data['file_size'] < 100000) $color = "<span style=\"color:blue;\">";
	else $color = "<span style=\"color:red;\">";

	$data['file_size'] = formatsize ($data['file_size']);

	if ($data['type']) $type = $lang['anti_modified']; else $type = $lang['anti_not'];

	if ($data['type'] == 2 ) $type = $lang['anti_modified_1'];

	$data['file_path'] = preg_replace("/([0-9]){10}_/", "*****_", $data['file_path']);

echo <<<HTML
    <tr>
        <td>{$color}{$data['file_path']}</span></td>
        <td>{$color}{$data['file_size']}</span></td>
        <td>{$color}{$data['file_date']}</span></td>
        <td>{$color}{$type}</span></td>
    </tr>
HTML;
  }

echo <<<HTML
</tbody>
</table>
</div>
HTML;

}
elseif ($_REQUEST['folder'] == "snap") {

echo <<<HTML
<div class="panel-body">{$lang['anti_creates']}</div>
HTML;

}
else {

echo <<<HTML
<div class="panel-body">{$lang['anti_notfound']}</div>
HTML;

}

echo <<<HTML
<div class="panel-body"><button class="btn bg-slate btn-sm btn-raised" onclick="check_files('global'); return false;"><i class="fa fa-search"></i> {$lang['anti_global']}</button> <button class="btn btn-sm btn-raised bg-orange-700" onclick="check_files('snap'); return false;"><i class="fa fa-magic"></i> {$lang['anti_snap']}</button></div>
HTML;
?>