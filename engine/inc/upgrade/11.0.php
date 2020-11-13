<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "11.1";
$config['fullcache_days'] = "30";

$tableSchema = array();

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_comments_files";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_comments_files (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `c_id` int(10) NOT NULL default '0',
  `author` varchar(40) NOT NULL default '',
  `date` varchar(50) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY (`id`),
  KEY `c_id` (`c_id`),
  KEY `author` (`author`)
) ENGINE=" . $storage_engine . " DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_usergroups` ADD `allow_up_image` TINYINT(1) NOT NULL DEFAULT '0' , ADD `allow_up_watermark` TINYINT(1) NOT NULL DEFAULT '0' , ADD `allow_up_thumb` TINYINT(1) NOT NULL DEFAULT '0' , ADD `up_count_image` SMALLINT NOT NULL DEFAULT '0' , ADD `up_image_side` VARCHAR(20) NOT NULL DEFAULT '' , ADD `up_image_size` MEDIUMINT(5) NOT NULL DEFAULT '0' , ADD `up_thumb_size` VARCHAR(20) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_static_files` CHANGE `onserver` `onserver` VARCHAR(190) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_static` CHANGE `template` `template` MEDIUMTEXT NOT NULL";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_post_extras` ADD INDEX `rating` (`rating`), ADD INDEX `news_read` (`news_read`)";
$tableSchema[] = "UPDATE " . PREFIX . "_usergroups SET `allow_up_image` = '1', `allow_up_watermark` = '1', `allow_up_thumb` = '1', `up_count_image` = '3', `up_image_side` = '800x600', `up_image_size`='200', `up_thumb_size`='200x150' WHERE id = '1'";
$tableSchema[] = "UPDATE " . PREFIX . "_usergroups SET `allow_up_image` = '0', `allow_up_watermark` = '1', `allow_up_thumb` = '1', `up_count_image` = '3', `up_image_side` = '800x600', `up_image_size`='200', `up_thumb_size`='200x150' WHERE id > '1'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_comments` ADD INDEX `rating` (`rating`)";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_users` CHANGE `password` `password` VARCHAR(255) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_users` ADD `news_subscribe` TINYINT(1) NOT NULL DEFAULT '0', ADD `comments_reply_subscribe` TINYINT(1) NOT NULL DEFAULT '0'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_post` CHANGE `short_story` `short_story` MEDIUMTEXT NOT NULL, CHANGE `full_story` `full_story` MEDIUMTEXT NOT NULL, CHANGE `xfields` `xfields` MEDIUMTEXT NOT NULL, CHANGE `category` `category` VARCHAR(190) NOT NULL DEFAULT '0', CHANGE `alt_name` `alt_name` VARCHAR(190) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_post` DROP INDEX `tags`";

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