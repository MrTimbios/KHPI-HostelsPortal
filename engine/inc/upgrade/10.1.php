<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "10.2";
$config['site_offline'] = $config['site_offline']=="yes" ? "1" : "0";
$config['allow_alt_url'] = $config['allow_alt_url']=="yes" ? "1" : "0";
$config['hide_full_link'] = $config['hide_full_link']=="yes" ? "1" : "0";
$config['allow_comments'] = $config['allow_comments']=="yes" ? "1" : "0";
$config['allow_cache'] = $config['allow_cache']=="yes" ? "1" : "0";
$config['allow_gzip'] = $config['allow_gzip']=="yes" ? "1" : "0";
$config['allow_registration'] = $config['allow_registration']=="yes" ? "1" : "0";
$config['allow_votes'] = $config['allow_votes']=="yes" ? "1" : "0";
$config['allow_topnews'] = $config['allow_topnews']=="yes" ? "1" : "0";
$config['allow_calendar'] = $config['allow_calendar']=="yes" ? "1" : "0";
$config['allow_archives'] = $config['allow_archives']=="yes" ? "1" : "0";
$config['files_allow'] = $config['files_allow']=="yes" ? "1" : "0";
$config['files_count'] = $config['files_count']=="yes" ? "1" : "0";
$config['allow_sec_code'] = $config['allow_sec_code']=="yes" ? "1" : "0";
$config['allow_skin_change'] = $config['allow_skin_change']=="yes" ? "1" : "0";
$config['allow_watermark'] = $config['allow_watermark']=="yes" ? "1" : "0";
$config['comments_lazyload'] = '0';

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_banners` ADD `innews` TINYINT(1) NOT NULL DEFAULT '0'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_links` ADD `replacearea` TINYINT(1) NOT NULL DEFAULT '1'";

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