<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$config['version_id'] = "13.2";
$config['image_lazy'] = "0";

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_links` ADD `title` VARCHAR(255) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_static` CHANGE `metadescr` `metadescr` VARCHAR(300) NOT NULL DEFAULT ''";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_plugins` ADD `needplugin` VARCHAR(100) NOT NULL DEFAULT '', ADD `phpinstall` TEXT NOT NULL, ADD `phpupgrade` TEXT NOT NULL, ADD `phpenable` TEXT NOT NULL, ADD `phpdisable` TEXT NOT NULL, ADD `phpdelete` TEXT NOT NULL";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_plugins_files` ADD `replacecount` SMALLINT(6) NOT NULL DEFAULT '0'";

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_post_extras_cats";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_post_extras_cats (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` INT(11) NOT NULL default '0',
  `cat_id` INT(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `news_id` (`news_id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=" . $storage_engine . " DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";

foreach($tableSchema as $table) {
	$db->query ($table, false);
}

$result = $db->query("SELECT id, category FROM " . PREFIX . "_post WHERE approve=1", false);
$cat_ids = array ();

while($row = $db->get_row($result)) {

	if ( $row['category'] ) {
		
		$cat_ids_arr = explode( ",", $row['category'] );
	
		foreach ( $cat_ids_arr as $value ) {
	
			if( intval( $value ) ) $cat_ids[] = "('" . $row['id'] . "', '" . intval( $value ) . "')";
			
		}
		
	}
	
}

if(count($cat_ids)) {
	$cat_ids = implode( ", ", $cat_ids );
	$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids, false );
}

$handler = fopen(ENGINE_DIR.'/data/config.php', "w");
fwrite($handler, "<?PHP \n\n//System Configurations\n\n\$config = array (\n\n");
foreach($config as $name => $value) {
	fwrite($handler, "'{$name}' => \"{$value}\",\n\n");
}
fwrite($handler, ");\n\n?>");
fclose($handler);

?>