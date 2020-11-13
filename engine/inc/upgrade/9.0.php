<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "9.2";
$config['allow_recaptcha'] = "0";
$config['recaptcha_public_key'] = "6LfoOroSAAAAAEg7PViyas0nRqCN9nIztKxWcDp_";
$config['recaptcha_private_key'] = "6LfoOroSAAAAAMgMr_BTRMZy20PFir0iGT2OQYZJ";
$config['recaptcha_theme'] = "clean";
unset($config['allow_upload']);
unset($config['news_captcha']);

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_usergroups` ADD `admin_tagscloud` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$tableSchema[] = "UPDATE " . PREFIX . "_usergroups SET `admin_tagscloud` = '1' WHERE id = '1'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_comments` ADD INDEX `post_id` ( `post_id` ), ADD INDEX `approve` ( `approve` )";

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