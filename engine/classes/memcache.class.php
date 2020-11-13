<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004-2020 SoftNews Media Group
=====================================================
 This code is protected by copyright
=====================================================
 File: memcache.class.php
-----------------------------------------------------
 Use: memcache class
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

class dle_memcache
{
	protected $server = null;
	protected $suite_key = null;
	protected $cache_info_key = null;
	protected $server_type = null;
	protected $max_age = null;
	public $connection = null;
	
	public function __construct( $config ) {
		
		$this->suite_key = md5( DBNAME . PREFIX . SECURE_AUTH_KEY );
		$this->cache_info_key = md5( $this->suite_key. '_all_info_tags_' );
		
		$this->server = $this->connect();
		
		if($this->connection !== -1 ) {
			
			$memcache_server = explode(":", $config['memcache_server']);
			$this->connection = 1;
			
			if ($memcache_server[0] == 'unix') {
				
				$memcache_server = array($config['memcache_server'], 0);
				
			}
			
			if ( !$this->server->addServer($memcache_server[0], $memcache_server[1]) ) {
				$this->connection = 0;
			}
			
			if ( $this->server->getStats() === false ) {
				$this->connection = 0;
			}
			
			if($this->connection > 0 AND $this->server_type == "memcached") {
				
				$this->server->setOption(Memcached::OPT_COMPRESSION, false);
				
			}
		}
		
		if ( $config['clear_cache'] ) $this->max_age = $config['clear_cache'] * 60; else $this->max_age = 86400;

	}
	
	protected function connect() {
		
		if( class_exists( 'Memcached' ) ) {
			
			$this->server_type = "memcached";
			
			return new Memcached();
		
		} elseif( class_exists( 'Memcache' ) ) {
			
			$this->server_type = "memcache";
			
			return new Memcache();

		} else {
			
			$this->connection = -1;
			
		}
		
	}
	
	public function get( $key ) {
		
		if($this->connection < 1 ) return false;
		
		$key = md5( $this->suite_key.$key );

		return $this->server->get($key);
		
	}

	public function set($key, $value) {
		
		if($this->connection < 1 ) return false;
		
		$key_name = md5( $this->suite_key.$key );

		$this->_set( $key_name, $value );
		$this->_setstoredkeys( $key_name, $key );
		
		return true;
		
	}
	
	public function clear( $cache_areas = false ) {
		
		if($this->connection < 1 ) return false;
		
		if ( $cache_areas ) {
			if(!is_array($cache_areas)) {
				$cache_areas = array($cache_areas);
			}
		}
		
		if( $cache_areas ) {
			
			$all_list = $this->_getstoredkeys();
			
			if(count($all_list)) {
				
				$temp_list = $all_list;
				
				foreach ( $temp_list as $key => $value) {
					foreach($cache_areas as $cache_area) if( stripos( $value, $cache_area ) === 0 ) {
						$this->_replaceset( $key, false );
						$this->server->delete($key, 0);
						unset($all_list[$key]);
					}
				}
				
				$this->_replaceset( $this->cache_info_key, json_encode($all_list) );
				
			} else {
				
				$this->_clear_all();
				
			}
			
		} else {
			
			$this->_clear_all();
			
		}
		
		return true;
		
	}
	
	protected function _setstoredkeys($key_name, $key) {
		
		if($this->connection < 1 ) return false;
		
		$cache_keys = json_decode($this->server->get($this->cache_info_key), true);

		if( !is_array($cache_keys) ) $cache_keys = array();
		
		$cache_keys[$key_name] = $key;

		$this->_replaceset( $this->cache_info_key, json_encode($cache_keys) );
		
	}
	
	public function _getstoredkeys() {
		
		if($this->connection < 1 ) return false;
		
		$cache_keys = json_decode($this->server->get($this->cache_info_key), true);
		
		if( !is_array($cache_keys) ) $cache_keys = array();
		
		return $cache_keys;
		
	}
	
	protected function _clear_all() {
		
		if($this->connection < 1 ) return false;
		
		$this->server->flush();
		
		return true;
		
	}
	
	protected function _set( $key, $value) {
		
		if($this->connection < 1 ) return false;

		if ( $this->server_type == "memcache" ) {
			
			$this->server->set( $key, $value, null, $this->max_age );
			
		} else {
			
			$this->server->set( $key, $value, $this->max_age);
			
		}
		
		return true;
	}
	
	protected function _replaceset( $key, $value) {
		
		if($this->connection < 1 ) return false;
		
		if ( $this->server_type == "memcache" ) {
			
			$result = $this->server->replace( $key, $value, null, $this->max_age );
			
			if( $result === false ) {
				$this->server->set( $key, $value, null, $this->max_age );
			}
			
		} else {
			
			$result = $this->server->replace( $key, $value, $this->max_age);
			
			if( $result === false ) {
				$this->server->set( $key, $value, $this->max_age);
			}
			
		}
	}
	
	public function __destruct() {
		
		if($this->connection < 1 ) return;
		
		if( $this->server ) {
			if( method_exists( $this->server, 'quit' ) ) {
				$this->server->quit();
			} elseif( method_exists( $this->server, 'close' ) ) {
				$this->server->close();
			}
		}
	}
	
}

?>