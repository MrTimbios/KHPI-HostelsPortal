<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

function GetRandInt($max){

	if(function_exists('openssl_random_pseudo_bytes') && (version_compare(PHP_VERSION, '5.3.4') >= 0 || strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) {
	     do{
	         $result = floor($max*(hexdec(bin2hex(openssl_random_pseudo_bytes(4)))/0xffffffff));
	     }while($result == $max);
	} else {

		$result = mt_rand( 0, $max );
	}

    return $result;
}

function generate_auth_key() {

    $arr = array('a','b','c','d','e','f',
                 'g','h','i','j','k','l',
                 'm','n','o','p','r','s',
                 't','u','v','x','y','z',
                 'A','B','C','D','E','F',
                 'G','H','I','J','K','L',
                 'M','N','O','P','R','S',
                 'T','U','V','X','Y','Z',
                 '1','2','3','4','5','6',
                 '7','8','9','0','.',',',
                 '(',')','[',']','!','?',
                 '&','^','%','@','*',' ',
                 '<','>','/','|','+','-',
                 '{','}','`','~','#',';',
                 '/','|','=',':','`');

    $key = "";
    for($i = 0; $i < 64; $i++)
    {
      $index = GetRandInt(count($arr))-1;
      $key .= $arr[$index];
    }
    return $key;
}

function xfieldssave($data) {
	global $config;
	
	$data = array_values($data);
	$filecontents = "";
	
    foreach ($data as $index => $value) {
      $value = array_values($value);
      foreach ($value as $index2 => $value2) {
        $value2 = stripslashes($value2);
        $value2 = str_replace("|", "&#124;", $value2);
        $value2 = str_replace("\r\n", "__NEWL__", $value2);
        $filecontents .= $value2 . ($index2 < count($value) - 1 ? "|" : "");
      }
      $filecontents .= ($index < count($data) - 1 ? "\r\n" : "");
    }
	
    $filehandle = fopen(ENGINE_DIR.'/data/xfields.txt', "w+");
	
	$filecontents = htmlspecialchars($filecontents, ENT_QUOTES, $config['charset'] );
	$filecontents = str_replace("&amp;#124;", "&#124;", $filecontents);

    fwrite($filehandle, $filecontents);
    fclose($filehandle);
	
}

$config['version_id'] = "10.0";
$config['start_site'] = "1";
$config['clear_cache'] = "0";
$config['use_admin_mail'] = "0";
$config['allow_complaint_mail'] = "0";
$config['spam_api_key'] = "";
$config['sec_addnews'] = "2";

if ( $config['allow_read_count'] == "yes" ) $config['allow_read_count'] = "1";
if ( $config['allow_read_count'] == "no" ) $config['allow_read_count'] = "0";

if ( $config['safe_xfield'] ) $xfsafe_xfield = 1; else $xfsafe_xfield = 0;

unset($config['safe_xfield']);

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_static` CHANGE `date` `date` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_pm` CHANGE `date` `date` INT(11) UNSIGNED NOT NULL DEFAULT '0'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_rss` CHANGE `lastdate` `lastdate` INT(11) UNSIGNED NOT NULL DEFAULT '0'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_sendlog` CHANGE `date` `date` INT(11) UNSIGNED NOT NULL DEFAULT '0'";

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_read_log";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_read_log (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `news_id` (`news_id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

$tableSchema[] = "DROP TABLE IF EXISTS " . PREFIX . "_spam_log";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_spam_log (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(16) NOT NULL DEFAULT '',
  `is_spammer` tinyint(1) NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL DEFAULT '',
  `date` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `is_spammer` (`is_spammer`)
) ENGINE=" . $storage_engine . " DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";

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


$config_dbhost = DBHOST;
$config_dbname = DBNAME;
$config_dbuser = DBUSER;
$config_dbpasswd = DBPASS;
$config_dbprefix = PREFIX;
$config_userprefix = USERPREFIX;
$config_dbcollate = COLLATE;
$auth_key = generate_auth_key();
$config_dbpasswd = str_replace ('"', '\"', str_replace ("$", "\\$", $config_dbpasswd) );

$dbconfig = <<<HTML
<?PHP

define ("DBHOST", "{$config_dbhost}"); 

define ("DBNAME", "{$config_dbname}");

define ("DBUSER", "{$config_dbuser}");

define ("DBPASS", "{$config_dbpasswd}");  

define ("PREFIX", "{$config_dbprefix}");

define ("USERPREFIX", "{$config_userprefix}");

define ("COLLATE", "{$config_dbcollate}");

define('SECURE_AUTH_KEY', '{$auth_key}');

\$db = new db;

?>
HTML;

$con_file = fopen(ENGINE_DIR.'/data/dbconfig.php', "w");
fwrite($con_file, $dbconfig);
fclose($con_file);


$xfields = xfieldsload();

$i=0;

if (count($xfields)) {

	foreach ( $xfields as $value ) {	
	
		if( $value[3] == "textarea" ) { $xfields[$i][7] = 1; $xfields[$i][8] = $xfsafe_xfield; }
		else { $xfields[$i][7] = 0; $xfields[$i][8] = 1; }
	
		$i++;
	}
	
	xfieldssave($xfields);
}

?>