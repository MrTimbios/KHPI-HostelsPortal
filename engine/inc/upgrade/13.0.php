<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "13.1";
$config['allow_admin_social'] = "0";

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_category` CHANGE `descr` `descr` VARCHAR(300) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_plugins_files` ADD `searchcount` SMALLINT(6) NOT NULL DEFAULT '0'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_plugins` ADD `filedelete` TINYINT(1) NOT NULL DEFAULT '0', ADD `filelist` TEXT NOT NULL, ADD `upgradeurl` VARCHAR(255) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_flood` CHANGE `ip` `ip` VARCHAR(46) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_logs` CHANGE `ip` `ip` VARCHAR(46) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_vote_result` CHANGE `ip` `ip` VARCHAR(46) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_banners_logs` CHANGE `ip` `ip` VARCHAR(46) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_banned` CHANGE `ip` `ip` VARCHAR(46) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_login_log` CHANGE `ip` `ip` VARCHAR(46) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_admin_logs` CHANGE `ip` `ip` VARCHAR(46) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_read_log` CHANGE `ip` `ip` VARCHAR(46) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_spam_log` CHANGE `ip` `ip` VARCHAR(46) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_comment_rating_log` CHANGE `ip` `ip` VARCHAR(46) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_users` ADD `cat_add` VARCHAR(500) NOT NULL DEFAULT '', ADD `cat_allow_addnews` VARCHAR(500) NOT NULL DEFAULT '', CHANGE `logged_ip` `logged_ip` VARCHAR(46) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_comments` CHANGE `ip` `ip` VARCHAR(46) NOT NULL DEFAULT ''";


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