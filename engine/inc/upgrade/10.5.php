<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "10.6";
$config['search_pages'] = "5";
	
unset($config['yandex_spam_check']);
unset($config['yandex_api_key']);

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_logs` ADD `rating` TINYINT(4) NOT NULL DEFAULT '0'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_comment_rating_log` ADD `rating` TINYINT(4) NOT NULL DEFAULT '0'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_admin_sections` CHANGE `name` `name` VARCHAR(100) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_post` CHANGE `date` `date` DATETIME NOT NULL DEFAULT '2000-01-01 00:00:00'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_comments` CHANGE `date` `date` DATETIME NOT NULL DEFAULT '2000-01-01 00:00:00'";

foreach($tableSchema as $table) {
	$db->query ($table, false);
}


$handler = fopen(ENGINE_DIR.'/data/config.php', "w");
fwrite($handler, "<?PHP \n\n//System Configurations\n\n\$config = array (\n\n");
foreach($config as $name => $value)
{
	fwrite($handler, "'{$name}' => \"{$value}\",\n\n");
}
fwrite($handler, ");\n\n?>");
fclose($handler);


require_once(ENGINE_DIR.'/data/videoconfig.php');
	
$video_config['preload'] = "1";
	
unset($video_config['use_html5']);
unset($video_config['youtube_q']);
unset($video_config['startframe']);
unset($video_config['preview']);
unset($video_config['autohide']);
unset($video_config['fullsizeview']);
unset($video_config['buffer']);
unset($video_config['progressBarColor']);
unset($video_config['play']);

$con_file = fopen(ENGINE_DIR.'/data/videoconfig.php', "w+");
fwrite( $con_file, "<?PHP \n\n//Videoplayers Configurations\n\n\$video_config = array (\n\n" );
foreach ( $video_config as $name => $value ) {
		
	fwrite( $con_file, "'{$name}' => \"{$value}\",\n\n" );
		
}
fwrite( $con_file, ");\n\n?>" );
fclose($con_file);

?>