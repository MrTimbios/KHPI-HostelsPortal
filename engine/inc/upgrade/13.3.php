<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "14.0";
$config['allow_yandex_dzen'] = "1";
$config['allow_yandex_turbo'] = "1";
$config['emoji'] = "0";
$config['last_viewed'] = "0";
$config['image_tinypng'] = "0";
$config['tinypng_key'] = "";
$config['tinypng_avatar'] = "0";
$config['tinypng_resize'] = "0";

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_category` ADD `enable_dzen` TINYINT(1) NOT NULL DEFAULT '1', ADD `enable_turbo` TINYINT(1) NOT NULL DEFAULT '1', ADD `active` TINYINT(1) NOT NULL DEFAULT '1'";

foreach($tableSchema as $table) {
	$db->query ($table, false);
}

$handler = fopen(ENGINE_DIR.'/data/config.php', "w");
fwrite($handler, "<?PHP \n\n//System Configurations\n\n\$config = array (\n\n");
foreach($config as $name => $value) {
	fwrite($handler, "'{$name}' => \"{$value}\",\n\n");
}
fwrite($handler, ");\n\n?>");
fclose($handler);

require_once(ENGINE_DIR.'/data/videoconfig.php');
	
$video_config['theme'] = "light";

$con_file = fopen(ENGINE_DIR.'/data/videoconfig.php', "w+");
if($con_file) {
	fwrite( $con_file, "<?PHP \n\n//Videoplayers Configurations\n\n\$video_config = array (\n\n" );
	foreach ( $video_config as $name => $value ) {
		
		fwrite( $con_file, "'{$name}' => \"{$value}\",\n\n" );
		
	}
	fwrite($con_file, ");\n\n?>" );
	fclose($con_file);
}

?>