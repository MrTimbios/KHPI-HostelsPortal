<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2009-2020 IT-Security (Asafov Sergey)
=====================================================
 This code is protected by copyright
=====================================================
 File: api.class.php
-----------------------------------------------------
 Use: API
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	define( 'DATALIFEENGINE', true );
}
if( !defined( 'ROOT_DIR' ) ) {
	define( 'ROOT_DIR', substr( dirname( __FILE__ ), 0, - 11 ) );
}

if( !defined( 'ENGINE_DIR' ) ) {
	define( 'ENGINE_DIR', ROOT_DIR . '/engine' );
}

if(!class_exists('DLE_API'))
{
	class DLE_API
	{
		/**
		 * DB instance
		 * @var object
		 */
     	var $db = false;
    	 	
		/**
		 * API version
		 * @var string
		 */
      	var $version = '0.07';
    	  	
		/**
		 * Copy of DLE config
		 * @var array
		 */
      	var $dle_config = array ();
      	
		/**
		 * Path to directory with cache
		 * @var string
		 */
      	var $cache_dir = false;
      	
		/**
		 * Array with all cache files
		 * @var array
		 */      	
      	var $cache_files = array();
    	  	
		/**
		 * Class constructor
		 * @return boolean
		 */
		function __construct()
		{
			if (!$this->cache_dir)
			{
				$this->cache_dir = ENGINE_DIR."/cache/";
			}
			return true;
		}
			
		/**
		 * Getting information about the user by his ID
		 * @param $id int - ID user
		 * @param $select_list string - List of fields with information or * for all
		 * @return array with data in case of success and false if the user is not found
		 */	
		function take_user_by_id ($id, $select_list = "*")
		{
			$id = intval( $id );
			if( $id == 0 ) return false;
			$row = $this->load_table(USERPREFIX."_users", $select_list, "user_id = '$id'");
			if( count( $row ) == 0 )
				return false;
			else
				return $row;
		}
		
		/**
		 * Getting information about the user by his name
		 * @param $name string - Username
		 * @param $select_list string - List of fields with information or * for all
		 * @return array with data in case of success and false if the user is not found
		 */
		function take_user_by_name($name, $select_list = "*")
		{
			$name = $this->db->safesql( $name );
			if( $name == '' ) return false;
			$row = $this->load_table(USERPREFIX."_users", $select_list, "name = '$name'");
			if( count( $row ) == 0 )
				return false;
			else
				return $row;
		}
			
		/**
		 * Getting information about the user by his email
		 * @param $email string - User email
		 * @param $select_list string - List of fields with information or * for all
		 * @return array with data in case of success and false if the user is not found
		 */	
		function take_user_by_email($email, $select_list = "*")
		{
			$email = $this->db->safesql( $email );
			if( $email == '' ) return false;
			$row = $this->load_table(USERPREFIX."_users", $select_list, "email = '$email'");
			if( count( $row ) == 0 )
				return false;
			else
				return $row;
		}
		
		/**
		 * Receiving user data for a specific group
		 * @param $group int - Group ID
		 * @param $select_list string - List of fields with information or * for all
		 * @param $limit int - Number of users received
		 * @return array with data in case of success and false if the user is not found
		 */
		function take_users_by_group ($group, $select_list = "*", $limit = 0)
		{
			$group = intval( $group );
			$data = array();
			if( $group == 0 ) return false;
			$data = $this->load_table(USERPREFIX."_users", $select_list, "user_group = '$group'", true, 0, $limit);
			if( count( $data ) == 0 )
				return false;
			else
				return $data;
		}
		
		/**
		 * Receiving data from users who have been exposed to a specific IP
		 * @param $ip string - IP of interest to us
		 * @param $like bool - Use the mask when searching
		 * @param $select_list string - List of fields with information or * for all
		 * @param $limit int - Number of users received
		 * @return array with data in case of success and false if the user is not found
		 */
		function take_users_by_ip ($ip, $like = false, $select_list = "*", $limit = 0)
		{
			$ip = $this->db->safesql( $ip );
			$data = array();
			if( $ip == '' ) return false;
			if( $like )
				$condition  = "logged_ip like '$ip%'";
			else
				$condition  = "logged_ip = '$ip'";
			$data = $this->load_table(USERPREFIX."_users", $select_list, $condition, true, 0, $limit);
			if( count( $data ) == 0 )
				return false;
			else
				return $data;
		}
		
		/**
		 * Change Username
		 * @param $user_id int - User ID
		 * @param $new_name string - New username
		 * @return bool - true if successful, and false if the new name is already occupied by another user
		 */
		function change_user_name ($user_id, $new_name)
		{
			$user_id = intval( $user_id );
			$new_name = $this->db->safesql( $new_name );
			$count_arr = $this->load_table(USERPREFIX."_users", "count(user_id) as count", "name = '$new_name'");
			$count = $count_arr['count'];
			
			if( $count > 0 ) return false;

			$old_name_arr = $this->load_table(USERPREFIX."_users", "name", "user_id = '$user_id'");
			$old_name = $old_name_arr['name'];
			$this->db->query( "UPDATE " . PREFIX . "_post SET autor='$new_name' WHERE autor='{$old_name}'" );
			$this->db->query( "UPDATE " . PREFIX . "_comments SET autor='$new_name' WHERE autor='{$old_name}' AND is_register='1'" );
			$this->db->query( "UPDATE " . USERPREFIX . "_pm SET user_from='$new_name' WHERE user_from='{$old_name}'" );
			$this->db->query( "UPDATE " . PREFIX . "_vote_result SET name='$new_name' WHERE name='{$old_name}'" );
			$this->db->query( "UPDATE " . PREFIX . "_images SET author='$new_name' WHERE author='{$old_name}'" );
			$this->db->query( "update " . USERPREFIX . "_users set name = '$new_name' where user_id = '$user_id'" );
			return true;

		}

		/**
		 * Changing the user password
		 * @param $user_id int - User ID
		 * @param $new_password string - New password
		 * @return null
		 */
		function change_user_password($user_id, $new_password)
		{
			$user_id = intval( $user_id );
			
			$new_password = $this->db->safesql( password_hash($new_password, PASSWORD_DEFAULT) );;
			
			if( !$new_password ) {
				die("PHP extension Crypt must be loaded for password_hash to function");
			}
			
			$this->db->query( "update " . USERPREFIX . "_users SET password = '$new_password' WHERE user_id = '$user_id'" );
		}
		
		/**
		 * Change user email
		 * @param $user_id int - User ID
		 * @param $new_email string - new user email
		 * @return int - some code
		 * 		-2: incorrect email
		 * 		-1: a new email is used by another user
		 * 		 1: operation was successfully completed
		 */
		function change_user_email($user_id, $new_email)
		{
			$user_id = intval( $user_id );

			if( (! preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])'.'(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $new_email )) or (empty( $new_email )) )
			{
				return -2;
			}

			$new_email = $this->db->safesql( $new_email );
			$email_exist_arr = $this->load_table(USERPREFIX."_users", "count(user_id) as count", "email = '$new_email'");
			if ($email_exist_arr['count'] > 0) return -1;

			$q = $this->db->query( "update " . USERPREFIX . "_users set email = '$new_email' where user_id = '$user_id'" );
			return 1;			
		}
			
			
		/**
		 * Changing a User Group
		 * @param $user_id int - User ID
		 * @param $new_group int - New user group ID
		 * @return bool - true if successful, and false if the ID of a non-existent group is specified
		 */
		function change_user_group($user_id, $new_group)
		{
			$user_id = intval( $user_id );
			$new_group = intval( $new_group );
			if($this->checkGroup($new_group) === false) return false;
			$this->db->query( "update " . USERPREFIX . "_users set user_group = '$new_group' where user_id = '$user_id'" );
			return true;
		}
		
		/**
		 * User authentication by name and password
		 * @param $login string - Username
		 * @param $password string - user password
		 * @return bool
		 * 		true:	authorize
		 * 		false:	authorization failed
		 */
		function external_auth($login, $password)
		{
			$login = $this->db->safesql( $login );

			$arr = $this->load_table(USERPREFIX."_users", "user_id, password", "name = '$login'");
			
			if( $arr['user_id'] AND $arr['password'] AND $password ) {

				if( $this->is_md5hash( $arr['password'] ) ) {
					
					if($arr['password'] == md5( md5($password) ) ) return true;
					else return false;
					
				} else {	

					if( password_verify($password, $arr['password'] ) ) return true;
					else return false;

				}
			
			} else return false;
			
		}

		function is_md5hash( $md5 = '' ) {
		  return strlen($md5) == 32 && ctype_xdigit($md5);
		}
		
		/**
		 * Adding a new user to the database
		 * @param $login string - Username
		 * @param $password string - user password
		 * @param $email string - user's email
		 * @param $group int - user group
		 * @return int - code
		 * 		-4: given non-existent group
		 * 		-3: incorrect email
		 * 		-2: the email is being used by another user
		 * 		-1: the username is also busy, here's the failure
		 * 		 1: operation was successfully completed
		 */
		function external_register($login, $password, $email, $group)
		{
			$login = $this->db->safesql( $login );

			if( preg_match( "/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\{\+]/", $login ) ) return -1;

			$password = $this->db->safesql( password_hash($password, PASSWORD_DEFAULT) );;
			
			if( !$password ) {
				die("PHP extension Crypt must be loaded for password_hash to function");
			}
			
			$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " " );
			$email = $this->db->safesql(trim( str_replace( $not_allow_symbol, '', strip_tags( stripslashes( $email ) ) ) ) );
			$group = intval( $group );
			
			$login_exist_arr = $this->load_table(USERPREFIX."_users", "count(user_id) as count", "name = '$login'");
			if( $login_exist_arr['count'] > 0 ) return -1;
			
			$email_exist_arr = $this->load_table(USERPREFIX."_users", "count(user_id) as count", "email = '$email'");
			if( $email_exist_arr['count'] > 0 ) return -2;
			
			if (empty( $email ) OR strlen( $email ) > 50 OR @count(explode("@", $email)) != 2)
			{
				return -3;
			}
			
			if($this->checkGroup($group) === false) return -4;
			
			$now = time();
			$q = $this->db->query( "insert into " . USERPREFIX . "_users (email, password, name, user_group, reg_date) VALUES ('$email', '$password', '$login', '$group', '$now')" );
			return 1;
		}		

		/**
		 * Sending a personal message to the user
		 * @param $user_id int - Recipient's ID
		 * @param $subject string - Message subject
		 * @param $text string - Message text
		 * @param $from string - Sender name
		 * @return int - code
		 * 		-1: the recipient does not exist
		 * 		 0: operation failed
		 * 		 1: operation was successfully completed
		 */
		function send_pm_to_user($user_id, $subject, $text, $from)
		{
			$user_id = intval( $user_id );

			$count_arr = $this->load_table(USERPREFIX."_users", "count(user_id) as count", "user_id = '$user_id'");
			if($count_arr['count'] == 0 ) return - 1;			

			$subject = $this->db->safesql( $subject );
			$text = $this->db->safesql( $text );
			$from = $this->db->safesql( $from );
			$now = time();
			$q = $this->db->query( "insert into " . PREFIX . "_pm (subj, text, user, user_from, date, pm_read, folder) VALUES ('$subject', '$text', '$user_id', '$from', '$now', '0', 'inbox')" );
			if( ! $q ) return 0;

			
			$this->db->query( "update " . USERPREFIX . "_users set pm_unread = pm_unread + 1, pm_all = pm_all+1  where user_id = '$user_id'" );
			return 1;

		}
      	
		/**
		 * Service function - take params from table
		 * @param $table string - table name
		 * @param $fields string - required fields via a space or * for all
		 * @param $where string - sampling condition
		 * @param $multirow bool - whether to take one row or several
		 * @param $start int - initial value of the sample
		 * @param $limit int - number of records for selection, 0 - select all
		 * @param $sort string - field for sorting
		 * @param $sort_order - sorting direction
		 * @return array with data or false if mysql returned 0 rows
		 */
		function load_table ($table, $fields = "*", $where = '1', $multirow = false, $start = 0, $limit = 0, $sort = '', $sort_order = 'desc')
		{
			if (!$table) return false;

			if ($sort!='') $where.= ' order by '.$sort.' '.$sort_order;
			if ($limit>0) $where.= ' limit '.$start.','.$limit;
			$q = $this->db->query("Select ".$fields." from ".$table." where ".$where);
			if ($multirow)
			{
				while ($row = $this->db->get_row())
				{
					$values[] = $row;
				}
			}
			else
			{
				$values = $this->db->get_row();
			}
			if (count($values)>0) return $values;
			
			return false;

		}
        
		/**
		 * Writing data to the cache
		 * @param $fname string - file name for the cache without an extension
		 * @param $vars - data for recording
		 * @return unknown_type
		 */
		function save_to_cache ($fname, $vars)
		{
			$filename = $fname.".tmp";
			$f = @fopen($this->cache_dir.$filename, "w+");
			@chmod('0777', $this->cache_dir.$filename);
			if (is_array($vars)) $vars = json_encode($vars, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			@fwrite($f, $vars);
			@fclose($f);
			return $vars;
		}
			
			
		/**
		 * Loading data from the cache
		 * @param $fnamee string - file name for the cache without an extension
		 * @param $timeout int - cache lifetime in seconds
		 * @param $type string - the type of data in the cache. if not text - believe that the array was stored
		 * @return unknown_type
		 */
		function load_from_cache ($fname, $timeout=300, $type = 'text')
		{
			$filename = $fname.".tmp";
			if (!file_exists($this->cache_dir.$filename)) return false;
			if ((filemtime($this->cache_dir.$filename)) < (time()-$timeout)) return false;

			if ($type=='text')
			{
				return file_get_contents($this->cache_dir.$filename);
			}
			else
			{
				return json_decode(file_get_contents($this->cache_dir.$filename), true);
			}
		}			

		/**
		 * Deleting a cache
		 * @param $name string - the name of the file to be deleted. With the GLOBAL value, we delete the entire cache
		 * @return null
		 */				
		function clean_cache($name = "GLOBAL")
		{
			$this->get_cached_files();
			
			if ($name=="GLOBAL")
			{
				foreach ($this->cache_files as $cached_file)
				{
					@unlink($this->cache_dir.$cached_file);
				}
			}
			elseif (in_array($name.".tmp", $this->cache_files))
			{
				@unlink($this->cache_dir.$name.".tmp");
			}
		}

		/**
		 * Getting an array containing the cache file names
		 * @return array
		 */		
		function get_cached_files()
		{
			$handle = opendir($this->cache_dir);
			while (($file = readdir($handle)) !== false)
			{
				if ($file != '.' && $file != '..' && (!is_dir($this->cache_dir.$file) && $file !='.htaccess'))
				{
					$this->cache_files [] = $file;
				}
			}
			closedir($handle);
		}		

		/**
		 * Saving script settings
		 * @param $key string or array
		 * 		string: Parameter name
		 * 		 array: associative array of parameters
		 * @param $new_value - the value of the parameter. Not used if $key array
		 * @return null;
		 */				
		function edit_config ($key, $new_value = '')
		{
			$find[] = "'\r'";
			$replace[] = "";
			$find[] = "'\n'";
			$replace[] = "";
			$config = $this->dle_config;
			if (is_array($key))
			{
				foreach ($key as $ckey=>$cvalue)
				{
					if ($config[$ckey])
					{
						$config[$ckey] = $cvalue;
					}
				}
			}
			else
			{
				if ($config[$key])
				{
					$config[$key] = $new_value;
				}
			}

			$handle = @fopen(ENGINE_DIR.'/data/config.php', 'w');
			fwrite( $handle, "<?PHP \n\n//System Configurations\n\n\$config = array (\n\n" );
			foreach ( $config as $name => $value )
			{
				if( $name != "offline_reason" ) {
					
					$value = trim( strip_tags(stripslashes( $value )) );
					$value = htmlspecialchars( $value, ENT_QUOTES, $config['charset']);
					
					$name = trim( strip_tags(stripslashes( $name )) );
					$name = htmlspecialchars( $name, ENT_QUOTES, $config['charset'] );
			
				} else {

					$value = trim(strip_tags(stripslashes( $value )));
					$value = htmlspecialchars( $value, ENT_QUOTES, $config['charset']);
					$value = str_replace( "\r", '', $value );
					$value = str_replace( "\n", '<br>', $value );
					
				}

				if( $name == "speedbar_separator" OR $name == "category_separator") {
					$value = str_replace( '&amp;', '&', $value );
				}
				
				$value = preg_replace( $find, $replace, $value );
				$value = str_replace( "$", "&#036;", $value );
				$value = str_replace( "{", "&#123;", $value );
				$value = str_replace( "}", "&#125;", $value );
				$value = str_replace( chr(0), "", $value );
				$value = str_replace( chr(92), "", $value );
				$value = str_ireplace( "decode", "dec&#111;de", $value );
				
				$name = preg_replace( $find, $replace, $name );
				$name = str_replace( "$", "&#036;", $name );
				$name = str_replace( "{", "&#123;", $name );
				$name = str_replace( "}", "&#125;", $name );
				$name = str_replace( chr(0), "", $name );
				$name = str_replace( chr(92), "", $name );
				$name = str_replace( '(', "", $name );
				$name = str_replace( ')', "", $name );
				$name = str_ireplace( "decode", "dec&#111;de", $name );

				fwrite( $handle, "'{$name}' => \"{$value}\",\n\n" );
			}
			fwrite( $handle, ");\n\n?>" );
			fclose( $handle );
			$this->clean_cache();
		}
         		
		/**
		 * Receiving news
		 * @param $cat string - news categories, separated by commas
		 * @param $fields string - list of received news fields or * for all
		 * @param $start int - initial value
		 * @param $limit int - number of news for sampling, 0 - select all news
		 * @param $sort string - field for sorting
		 * @param $sort_order - sorting direction
		 * @return array - array with news
		 */
		function take_news ($cat, $fields = "*", $start = 0, $limit = 10, $sort = 'id', $sort_order = 'desc')
		{
			if ($this->dle_config['allow_multi_category'])
			{
				$condition = 'category regexp "[[:<:]]('.str_replace(',', '|', $cat).')[[:>:]]"';
			}
			else
			{
				$condition = 'category IN ('.$cat.')';
			}
			
			return $this->load_table (PREFIX."_post", $fields, $condition, $multirow = true, $start, $limit, $sort, $sort_order);
			 
		}
        	
        	
		/**
		 * Check the existence of a group with the specified ID
		 * @param $group int - Group ID
		 * @return bool - true if exists and false if not
		 */		
		function checkGroup($group)
		{
			$row = $this->db->super_query('SELECT group_name FROM '.USERPREFIX.'_usergroups WHERE id = '.intval($group));
			return isset($row['group_name']);
		}        	
        	

		/**
		 * Installing the administrative part of the module
		 * @param $name string		- the name of the module, namely the .php file located in the engine/inc/ folder,
									but without the file extension
		 * @param $title string		- module header
		 * @param $descr string		- module description
		 * @param $icon string		- icon name for the module, without specifying the path.
		 							The icon must be in the folder engine/skins/images/
		 * @param $perm string		- information about the groups that this module is allowed to display.
		 							This field can have the following values: all or group IDs separated by commas.
									For example: 1,2,3. if all is specified, then the module will be shown to all
									users who have access to adminpanel
		 * @return bool - true if successfully installed and false if not
		 */
		function install_admin_module ($name, $title, $descr, $icon, $perm = '1')
		{
			$name = $this->db->safesql($name);
			$title = $this->db->safesql($title);
			$descr = $this->db->safesql($descr);
			$icon = $this->db->safesql($icon);
			$perm = $this->db->safesql($perm);

			$this->db->query("Select name from `".PREFIX."_admin_sections` where name = '$name'");
			if ($this->db->num_rows()>0)
			{
				$this->db->query("UPDATE `".PREFIX."_admin_sections` set title = '$title', descr = '$descr', icon = '$icon', allow_groups = '$perm' where name = '$name'");
				return true;
			}
			else
			{
				$this->db->query("INSERT INTO `".PREFIX."_admin_sections` (`name`, `title`, `descr`, `icon`, `allow_groups`) VALUES ('$name', '$title', '$descr', '$icon', '$perm')");
				return true;
			}

			return false;
		}

		/**
		 * Removing the Administrative Part of the Module
		 * @param $name string - module name
		 * @return null
		 */
		function uninstall_admin_module ($name)
		{
			$name = $this->db->safesql($name);
			$this->db->query("DELETE FROM `".PREFIX."_admin_sections` where name = '$name'");
		}

		/**
		 * Changing the rights of the administrative part of the module
		 * @param $name string 		- module name
		 * @param $perm string		- information about the groups that this module is allowed to display.
		 							This field can have the following values: all or group IDs separated by commas.
									For example: 1,2,3. if all is specified, then the module will be shown to all
									users who have access to adminpanel
		 * @return null
		 */
		function change_admin_module_perms ($name, $perm)
		{
            $name = $this->db->safesql($name);
            $perm = $this->db->safesql($perm);
			$this->db->query("UPDATE `".PREFIX."_admin_sections` set allow_groups = '$perm' where name = '$name'");
		}
        	

	}
}

	$dle_api = new DLE_API ();
	if( !isset($config['version_id']) ) { include_once (ENGINE_DIR . '/data/config.php'); date_default_timezone_set ( $config['date_adjust'] ); }
	$dle_api->dle_config = $config;
	if( !isset( $db ) ) {
		include_once (ENGINE_DIR . '/classes/mysql.php');
		include_once (ENGINE_DIR . '/data/dbconfig.php');
	}
	$dle_api->db = $db;
?>