<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "11.3";
$config['max_cache_pages'] = '10';
$config['only_ssl'] = '0';
$config['bbimages_in_wysiwyg'] = '0';
$config['allow_redirects'] = '0';

$tableSchema = array();

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_redirects";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_redirects (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(250) NOT NULL default '',
  `to` varchar(250) NOT NULL default '',
  PRIMARY KEY (`id`)
) ENGINE=" . $storage_engine . " DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_usergroups` ADD `allow_mail_files` TINYINT(1) NOT NULL DEFAULT '0' , ADD `max_mail_files` SMALLINT(6) NOT NULL DEFAULT '0' , ADD `max_mail_allfiles` MEDIUMINT(9) NOT NULL DEFAULT '0' , ADD `mail_files_type` VARCHAR(100) NOT NULL DEFAULT ''";
$tableSchema[] = "UPDATE " . PREFIX . "_usergroups SET `allow_mail_files` = '0', `max_mail_files` = '3', `max_mail_allfiles` = '1000', `mail_files_type` = 'jpg,png,zip,pdf'";
$tableSchema[] = "UPDATE " . PREFIX . "_usergroups SET `allow_mail_files` = '1' WHERE id < '3'";


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
	
unset($video_config['tube_related']);
unset($video_config['tube_dle']);
unset($video_config['height']);
unset($video_config['play']);

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