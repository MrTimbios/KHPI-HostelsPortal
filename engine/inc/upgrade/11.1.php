<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "11.2";
$config['twofactor_auth'] = '1';
$config['category_newscount'] = '0';

$tableSchema = array();

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_twofactor";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_twofactor (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL default '0',
  `pin` varchar(10) NOT NULL default '',
  `attempt` tinyint(1) NOT NULL DEFAULT '0',
  `date` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pin` (`pin`),
  KEY `date` (`date`)
) ENGINE=" . $storage_engine . " DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";

$tableSchema[] = "INSERT INTO " . PREFIX . "_email values (9, 'twofactor', '{%username%},\r\n\r\nThis letter was sent from the {$config['http_home_url']}\r\n\r\nYou received this email because for your account two-factor authentication enabled. To login on a website you must enter your pin.\r\n\r\n------------------------------------------------\r\nPin:\r\n------------------------------------------------\r\n\r\n{%pin%}\r\n\r\n------------------------------------------------\r\n\r\nThe IP of the user: {%ip%}\r\n\r\nSincerely,\r\n\r\nAdministration {$config['http_home_url']}', 0)";

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_usergroups` DROP `allow_lostpassword`";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_banners` ADD `devicelevel` VARCHAR(10) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_files` ADD `size` BIGINT(20) NOT NULL DEFAULT '0' , ADD `checksum` CHAR(32) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_static_files` ADD `size` BIGINT(20) NOT NULL DEFAULT '0' , ADD `checksum` CHAR(32) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_users` ADD `twofactor_auth` TINYINT(1) NOT NULL DEFAULT '0'";


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