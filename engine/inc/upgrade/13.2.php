<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "13.3";
$config['search_length_min'] = "4";
$config['min_up_side'] = "10x10";
$config['jquery_version'] = "0";

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_usergroups` ADD `min_image_side` VARCHAR(20) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_metatags` ADD `robots` VARCHAR(255) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_tags` CHANGE `tag` `tag` VARCHAR(100) CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_bin NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_xfsearch` CHANGE `tagvalue` `tagvalue` VARCHAR(100) CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_bin NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_complaint` ADD `email` VARCHAR(50) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_plugins` ADD `notice` TEXT NOT NULL, ADD `mnotice` TINYINT(1) NOT NULL DEFAULT '0', ADD `posi` MEDIUMINT(8) NOT NULL DEFAULT '1'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_post_extras` ADD `allow_rss` TINYINT(1) NOT NULL DEFAULT '1', ADD `allow_rss_turbo` TINYINT(1) NOT NULL DEFAULT '1', ADD `allow_rss_dzen` TINYINT(1) NOT NULL DEFAULT '1'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_post_extras` ADD INDEX `allow_rss` (`allow_rss`)";
$tableSchema[] = "UPDATE " . PREFIX . "_usergroups SET `min_image_side` = '10x10'";

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

?>