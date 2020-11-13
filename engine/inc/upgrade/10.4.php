<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "10.5";
$config['tree_comments'] = "0";
$config['tree_comments_level'] = "5";
$config['simple_reply'] = "0";
$config['recaptcha_theme'] = "light";
$config['yandex_spam_check'] = "0";
$config['yandex_api_key'] = "";
$config['smtp_secure'] = "";
	
unset($config['mail_additional']);
unset($config['smtp_helo']);
unset($config['use_admin_mail']);

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_usergroups` ADD `max_edit_days` TINYINT(1) NOT NULL DEFAULT '0'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_usergroups` ADD `spampmfilter` TINYINT(1) NOT NULL DEFAULT '2'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_comments` ADD `parent` INT(11) NOT NULL DEFAULT '0', ADD INDEX `parent` (`parent`)";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_users` CHANGE `foto` `foto` VARCHAR(255) NOT NULL DEFAULT ''";

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