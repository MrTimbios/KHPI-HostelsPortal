<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "8.5";
$config['parse_links'] = "0";
$config['t_seite'] = "0";
$config['comments_minlen'] = "0";
$config['js_min'] = "0";
$config['outlinetype'] = "0";

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_usergroups` ADD `allow_image_size` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_usergroups` ADD `cat_allow_addnews` TEXT NOT NULL";
$tableSchema[] = "UPDATE " . PREFIX . "_usergroups SET `allow_image_size` = '1' WHERE id < '4'";
$tableSchema[] = "UPDATE " . PREFIX . "_usergroups SET `cat_allow_addnews` = 'all'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_poll` CHANGE `answer` `answer` TEXT NOT NULL";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_vote` ADD `start` VARCHAR( 15 ) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_vote` ADD `end` VARCHAR( 15 ) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_banners` ADD `start` VARCHAR( 15 ) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_banners` ADD `end` VARCHAR( 15 ) NOT NULL DEFAULT ''";


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

?>