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
 File: zipextract.class.php
-----------------------------------------------------
 Use: ZIP Extract class
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

class dle_zip_extract {
	
	private $root = null;
	
	public $zip;

	private $ftp = null;
	private $ssh = null;
	private $sftp = null;
	private $sftpDir = null;
	
	private $folder_permission = 0755;
	private $file_permission = 0644;
	
	public $errors_list = array();
	public $skip_index = array();
	public $is_plugin = false;

	function __construct( $zip_link = false ) {
		global $lang;
		
		$this->root = ROOT_DIR.'/';
		
		if($zip_link) {
			$this->zip = new ZipArchive();
			if($this->zip->open( $zip_link, ZIPARCHIVE::CHECKCONS ) !== true) {
				throw new RuntimeException($lang['upgr_f_error_16']);
			}
		}
		
	}
	
	public function FtpConnect( $data ) {
		global $lang;
		
		if ( $data['type'] == 'ssh2' ) {
			
			if( !function_exists('ssh2_connect') ) {
				throw new RuntimeException($lang['upgr_f_error_10']);
			}
			
			$this->ssh = ssh2_connect( $data['server'], $data['port'] );	

			if ( $this->ssh === false ) {
				throw new RuntimeException($lang['upgr_f_error_11'].' ' . $data['server'] . ', '.$lang['upgr_ftp_4'].' ' . $data['port']);
			}

			if ( !@ssh2_auth_password( $this->ssh, $data['username'], $data['password'] ) ) {
				throw new RuntimeException($lang['upgr_f_error_12']);
			}

			$this->sftp = @ssh2_sftp( $this->ssh );

			if ( $this->sftp === false ) {
				throw new Exception($lang['upgr_f_error_13']);
			}
			
			if ( $data['path'] and !@ssh2_sftp_stat( $this->sftp, $data['path'] ) ) {
				throw new RuntimeException( $lang['upgr_f_error_14'].' '.$data['path'] );
			}
			
			$this->sftpDir = ssh2_sftp_realpath( $this->sftp, $data['path'] ) . '/';
			
			$stream = @fopen("ssh2.sftp://".intval($this->sftp).$this->sftpDir."index.php", 'r');
			
			if(!$stream OR  @stripos(stream_get_contents($stream), 'DATALIFEENGINE') === false ) {
				throw new RuntimeException($lang['upgr_f_error_15']);
			}

		} else {

			if ( $data['type'] == 'sslftp' ) {
				$this->ftp = @ftp_ssl_connect( $data['server'], $data['port'], 5 );
			} else {
				$this->ftp = @ftp_connect( $data['server'], $data['port'], 5 );
			}

			if ( $this->ftp === false ) {
				throw new RuntimeException($lang['upgr_f_error_11'].' ' . $data['server'] . ', '.$lang['upgr_ftp_4'].' ' . $data['port']);
			}

			if ( !@ftp_login( $this->ftp, $data['username'], $data['password'] ) ) {
				
				$this->DisconnectFTP();
				
				throw new RuntimeException($lang['upgr_f_error_12']);
			}

			@ftp_pasv( $this->ftp, true );
			
			if ( $data['path'] AND !@ftp_chdir( $this->ftp, $data['path'] ) ) {
				
				$this->DisconnectFTP();
				
				throw new RuntimeException( $lang['upgr_f_error_14'].' '.$data['path'] );
			}
			
			$temp = fopen('php://temp', 'r+');
			
			if (@ftp_fget($this->ftp, $temp, 'index.php', FTP_BINARY, 0)) {
				rewind($temp);
				
				if(stripos(stream_get_contents($temp), 'DATALIFEENGINE') === false ) throw new RuntimeException($lang['upgr_f_error_15']);
			
			} else throw new RuntimeException($lang['upgr_f_error_15']);
			
			
		}
	}
	
    public function DisconnectFTP() {
		
        if ($this->ftp) {
			
			if( is_resource($this->ftp) ) {
				@ftp_close($this->ftp);
			}
			
			$this->ftp = null;
		}
    }
	
	public function ExtractZipArchive( $offset = 0, $limit = 0 ) {
		$done = 0;
		
		if( !$limit ) $limit = $this->zip->numFiles;
		
		for ( $i = 0; $i < $limit; $i++ ) {
			$index = $offset + $i;
			
			if ( $this->zip->statIndex($index) ) {
				$this->ExtractFile( $index );
				$done++;
			}
		}
		
		return $done;
	}
	
	public function FixHtaccess() {
		
		$search = array (
			'RewriteRule ^tags/([^/]*)(/?)+$ index.php?do=tags&tag=$1 [L]',
			'RewriteRule ^tags/([^/]*)/page/([0-9]+)(/?)+$ index.php?do=tags&tag=$1&cstart=$2 [L]',
			'RewriteRule ^xfsearch/(.*)/page/([0-9]+)(/?)+$ index.php?do=xfsearch&xf=$1&cstart=$2 [L]',
			'RewriteRule ^xfsearch/(.*)/?$ index.php?do=xfsearch&xf=$1 [L]'
		);
		
		$replace = array (
			'RewriteRule ^tags/([^/]*)(/?)+$ index.php?do=tags&tag=$1 [B,L]',
			'RewriteRule ^tags/([^/]*)/page/([0-9]+)(/?)+$ index.php?do=tags&tag=$1&cstart=$2 [B,L]',
			'RewriteRule ^xfsearch/(.*)/page/([0-9]+)(/?)+$ index.php?do=xfsearch&xf=$1&cstart=$2 [B,L]',
			'RewriteRule ^xfsearch/(.*)/?$ index.php?do=xfsearch&xf=$1 [B,L]'
		);
		
		$data = $this->ReadFile( ".htaccess" );
		
		if($data) {
			$data = str_replace($search, $replace, $data);
			$this->WriteFile( ".htaccess", $data );
		}
		
	}
	
	private function ExtractFile( $index ) {
		global $config, $lang;
		
		$file = $this->zip->statIndex($index);
		
		if ( substr($file['name'], -1) == '/' ) return;
		
		if( count($this->skip_index) AND in_array($index, $this->skip_index) ) return;
		
		if( $this->is_plugin ) {
			
			$file['name'] = str_ireplace( '{THEME}', $config['skin'], $file['name'] );
			
		}
		
		$dir = dirname( $file['name'] );
		
		$directories = array( $dir );
		
		while ( $dir != '.' ) {
			$dir = dirname( $dir );
			if ( $dir != '.' ) {
				$directories[] = $dir;
			}
		}
		
		$directories = array_reverse( $directories );

		foreach ( $directories as $dir ) {
			
			if ( !is_dir( $this->root.$dir ) ) {
				if ( $this->sftp ) {
					if( !@ssh2_sftp_mkdir( $this->sftp, $this->sftpDir . $dir ) ) {
						$this->errors_list[] = array( 'file' => $dir, 'error' => $lang['upgr_f_error_17'] );
					}
				}
				
				if ( $this->ftp ) {
					if( !@ftp_mkdir( $this->ftp, $dir ) ) {
						$this->errors_list[] = array( 'file' => $dir, 'error' => $lang['upgr_f_error_17'] );
					}
				} else {
					if( !@mkdir( $this->root.$dir, $this->folder_permission ) ) {
						$this->errors_list[] = array( 'file' => $dir, 'error' => $lang['upgr_f_error_17'] );
					}
				}
			}
			
		}

		$contents = $this->zip->getFromIndex($index);

		if( $file['name'] == "admin.php" AND $file['name'] != $config['admin_path'] AND $config['admin_path'] ) {
			$file['name'] = $config['admin_path'];
		}
		
		if ( $this->sftp OR $this->ftp ) {
			
			$tmpFile = tempnam( ENGINE_DIR . "/cache/system/", 'DLE' );
			file_put_contents( $tmpFile, $contents );
			
			if ( $this->sftp ) {
				
				if( !@ssh2_scp_send( $this->ssh, $tmpFile, $this->sftpDir . $file['name'] ) ) {
					$this->errors_list[] = array( 'file' => $file['name'], 'error' => $lang['upgr_f_error_18'] );
				}
				
			} else {
				
				if( !@ftp_put( $this->ftp, $file['name'], $tmpFile, FTP_BINARY ) ) {
					
					@ftp_chmod($this->ftp, $this->file_permission, $file['name']);
					
					if( !@ftp_put( $this->ftp, $file['name'], $tmpFile, FTP_BINARY ) ) {
						$this->errors_list[] = array( 'file' => $file['name'], 'error' => $lang['upgr_f_error_18'] );
					}
				}
				
			}
			
			@unlink( $tmpFile );
			
		} else {

			if( @file_exists( $this->root.$file['name'] ) AND !@is_writable( $this->root.$file['name'] ) ) {
				@chmod( $this->root.$file['name'], $this->file_permission );
			}
			
			$fh = @fopen( $this->root.$file['name'], 'w+b' );
			
			if ( $fh !== false ) {

				if ( @fwrite( $fh, $contents ) !== false ) {
					
					@chmod( $this->root.$file['name'], $this->file_permission );
					
				} else $this->errors_list[] = array( 'file' => $file['name'], 'error' => $lang['upgr_f_error_18'] );
				
				@fclose($fh);
				
			} else $this->errors_list[] = array( 'file' => $file['name'], 'error' => $lang['upgr_f_error_18'] );
			
		}
		
	}

	private function ReadFile( $file ) {
		
		$data = '';
		
		if ( $this->sftp ) {
			
			$temp = @fopen("ssh2.sftp://".intval($this->sftp).$this->sftpDir.$file, 'r');
			
			if($temp) {
				$data = stream_get_contents($temp);
				@fclose($temp);
			}
			
		} elseif( $this->ftp ) {
			
			$temp = fopen('php://temp', 'r+');
			
			if (@ftp_fget($this->ftp, $temp, $file, FTP_BINARY, 0)) {
				rewind($temp);
				$data = stream_get_contents($temp);
				@fclose($temp);
			}
			
		} else {
			
			$data = file_get_contents($this->root.$file);
			
		}
		
		return $data;

	}
	
	private function WriteFile( $file, $contents ) {
		global $lang;

		if ( $this->sftp OR $this->ftp ) {
			
			$tmpFile = tempnam( ENGINE_DIR . "/cache/system/", 'DLE' );
			file_put_contents( $tmpFile, $contents );
			
			if ( $this->sftp ) {
				
				if( !@ssh2_scp_send( $this->ssh, $tmpFile, $this->sftpDir . $file ) ) {
					$this->errors_list[] = array( 'file' => $file, 'error' => $lang['upgr_f_error_18'] );
				}
				
			} else {
				
				if( !@ftp_put( $this->ftp, $file, $tmpFile, FTP_BINARY ) ) {
					
					@ftp_chmod($this->ftp, $this->file_permission, $file);
					
					if( !@ftp_put( $this->ftp, $file, $tmpFile, FTP_BINARY ) ) {
						$this->errors_list[] = array( 'file' => $file, 'error' => $lang['upgr_f_error_18'] );
					}
				}
				
			}
			
			@unlink( $tmpFile );
			
		} else {
			
			if( @file_exists( $this->root.$file ) AND !@is_writable( $this->root.$file ) ) {
				@chmod( $this->root.$file, $this->file_permission );
			}
			
			$fh = @fopen( $this->root.$file, 'w+b' );
			
			if ( $fh !== false ) {

				if ( @fwrite( $fh, $contents ) !== false ) {
					
					@chmod( $this->root.$file, $this->file_permission );
					
				} else $this->errors_list[] = array( 'file' => $file, 'error' => $lang['upgr_f_error_18'] );
				
				@fclose($fh);
				
			} else $this->errors_list[] = array( 'file' => $file, 'error' => $lang['upgr_f_error_18'] );
			
		}
		
	}
	
}

?>