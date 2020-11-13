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
 File: thumb.class.php
-----------------------------------------------------
 Use: Thumbnail class
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}


class thumbnail {
	var $img;
	var $watermark_image_light;
	var $watermark_image_dark;
	var $re_save = false;
	var $tinypng_error = false;
	
	function __construct($imgfile) {
		global $lang, $config;

		$info = @getimagesize($imgfile); 
		$img_name_arr = explode( ".", $imgfile );
		$type =  strtolower( end( $img_name_arr ) );

		if( $info[2] == 2 ) {
			$this->img['format'] = "JPEG";
			$this->img['src'] = @imagecreatefromjpeg( $imgfile );

			if($this->img['src'] AND function_exists('exif_read_data') ) {
				$exif = exif_read_data( $imgfile, 0, true);
				
				if( !empty( $exif['IFD0']['Orientation'] ) ) {
					
					switch( $exif['IFD0']['Orientation'] ) {
						
						case 8:
							$this->img['src'] = imagerotate( $this->img['src'], 90, 0 );
							$this->re_save = true;
						break;
					
						case 3:
							$this->img['src'] = imagerotate( $this->img['src'], 180, 0);
							$this->re_save = true;
						break;
					
						case 6:
							$this->img['src'] = imagerotate( $this->img['src'], -90, 0);
							$this->re_save = true;
						break;
					}
				} 
				
			}
			
		} elseif( $info[2] == 3 ) {
			
			$this->img['format'] = "PNG";
			$this->img['src'] = @imagecreatefrompng( $imgfile );
		} elseif( $info[2] == 1 ) {
			
			$this->img['format'] = "GIF";
			$this->img['src'] = @imagecreatefromgif( $imgfile );
			
		} elseif( $type == "webp" ) {
			
			$this->img['format'] = "WEBP";
			$this->img['src'] = @imagecreatefromwebp( $imgfile );
			
		} else {
			
			if (!@unlink( $imgfile ) ) {
				@chmod( $imgfile, 0666 );
				@unlink( $imgfile );
			}
			
			echo "{\"error\":\"{$lang['upload_error_6']}\"}";
			exit();
		}

		if( !$this->img['src'] ) {
			
			if (!@unlink( $imgfile ) ) {
				@chmod( $imgfile, 0666 );
				@unlink( $imgfile );
			}
			
			echo "{\"error\":\"{$lang['upload_error_6']}\"}";
			exit();
		
		}

		$this->img['lebar'] = @imagesx( $this->img['src'] );
		$this->img['tinggi'] = @imagesy( $this->img['src'] );
		$this->img['lebar_thumb'] = $this->img['lebar'];
		$this->img['tinggi_thumb'] = $this->img['tinggi'];
		//default quality jpeg
		$this->img['quality'] = 90;
		
		if( intval( $config['min_up_side'] ) ) {

			$min_size = explode ("x", $config['min_up_side']);
			
			$allowed = true;
			
			if ( count($min_size) == 2 ) {
				
				$min_size[0] = intval($min_size[0]);
				$min_size[1] = intval($min_size[1]);
	
				if( $this->img['lebar'] < $min_size[0] OR $this->img['tinggi'] < $min_size[1] ) {

					$allowed = false;
				
				}
				
			} else {
				
				$min_size[0] = intval($min_size[0]);
				
				if( $this->img['lebar'] < $min_size[0] OR  $this->img['tinggi'] < $min_size[0] ) {
					
					$allowed = false;
				
				}
				
			}
			
			if( !$allowed ) {
				
				if (!@unlink( $imgfile ) ) {
					@chmod( $imgfile, 0666 );
					@unlink( $imgfile );
				}
				
				$lang['upload_error_7'] = str_ireplace("{minsize}", $config['min_up_side'], $lang['upload_error_7']);
				
				echo "{\"error\":\"{$lang['upload_error_7']}\"}";
				exit();
			}
		
		}
		
		if( $config['image_tinypng'] AND $config['tinypng_key'] AND ($this->img['format'] == "PNG" OR $this->img['format'] == "JPEG") ) {
			
			include_once (DLEPlugins::Check(ENGINE_DIR . '/classes/tinify/tinify.php'));
			
			try {
				
				\Tinify\setKey( $config['tinypng_key'] );
				
				$this->img['tinypng'] = true;
				$this->img['tinypng_method'] = false;
				$this->img['tinypng_resize'] = $config['tinypng_resize'];
				$this->img['tinypng_source_file'] = $imgfile;
				$this->re_save = true;
				
			} catch(\Tinify\Exception $e) {
			
				$this->img['tinypng'] = false;
			
				$this->tinypng_error = $e->getMessage();
			}
			
		} else $this->img['tinypng'] = false;

		
	}
	
	function size_auto($size = 100, $site = 0) {

		$size = explode ("x", $size);

		if ( count($size) == 2 ) {
			$size[0] = intval($size[0]);
			$size[1] = intval($size[1]);

			if ( $size[0] < 10 ) $size[0] = 10;
			if ( $size[1] < 10 ) $size[1] = 10;

			return $this->crop( $size[0], $size[1] );

		} else {
			$size[0] = intval($size[0]);

			if ( $size[0] < 10 ) $size[0] = 10;

			return $this->scale( $size[0], $site);

		}

	}

	function crop($nw, $nh) {

		$w = $this->img['lebar'];
		$h = $this->img['tinggi'];

		if( $w <= $nw AND $h <= $nh ) {
			$this->img['lebar_thumb'] = $w;
			$this->img['tinggi_thumb'] = $h;
			return 0;
		}

		if( $this->img['tinypng'] AND $this->img['tinypng_resize'] ) {
			
			$this->img['tinypng_method'] = "cover";
			$this->img['tinypng_width'] = $nw;
			$this->img['tinypng_height'] = $nh;
			
		}
		
		$nw = min($nw, $w);
		$nh = min($nh, $h);

		$size_ratio = max($nw / $w, $nh / $h);

		$src_w = ceil($nw / $size_ratio);
		$src_h = ceil($nh / $size_ratio);

		$sx = floor(($w - $src_w)/2);
		$sy = floor(($h - $src_h)/2);

		$this->img['des'] = imagecreatetruecolor($nw, $nh);

		if ( $this->img['format'] == "PNG" OR $this->img['format'] == "WEBP") {
			imagealphablending( $this->img['des'], false);
			imagesavealpha( $this->img['des'], true);
		}

		if ( $this->img['format'] == "GIF" ) {

			$transparent_index=imagecolortransparent($this->img['src']);

			if($transparent_index!==-1){
				$transparent_color=imagecolorsforindex($this->img['src'], $transparent_index);
			 
				$transparent_destination_index=imagecolorallocate($this->img['des'], $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
				imagecolortransparent($this->img['des'], $transparent_destination_index);
			 
				imagefill($this->img['des'], 0, 0, $transparent_destination_index);
			}


		}

		imagecopyresampled($this->img['des'],$this->img['src'],0,0,$sx,$sy,$nw,$nh,$src_w,$src_h);

		$this->img['src'] = $this->img['des'];
		return 1;
	}

	function scale($size = 100, $site = 0) {

		$site = intval( $site );
		
		if( $this->img['lebar'] <= $size and $this->img['tinggi'] <= $size ) {
			
			$this->img['lebar_thumb'] = $this->img['lebar'];
			$this->img['tinggi_thumb'] = $this->img['tinggi'];
			return 0;
		
		}
		
		switch ($site) {
			
			case "1" :
				
				if( $this->img['lebar'] <= $size ) {
					
					$this->img['lebar_thumb'] = $this->img['lebar'];
					$this->img['tinggi_thumb'] = $this->img['tinggi'];
					return 0;
				
				} else {
					
					$this->img['lebar_thumb'] = $size;
					$this->img['tinggi_thumb'] = ceil(($this->img['lebar_thumb'] / $this->img['lebar']) * $this->img['tinggi']);
		
				}
				
				break;
			
			case "2" :
				
				if( $this->img['tinggi'] <= $size ) {
					
					$this->img['lebar_thumb'] = $this->img['lebar'];
					$this->img['tinggi_thumb'] = $this->img['tinggi'];
					return 0;
				
				} else {
					
					$this->img['tinggi_thumb'] = $size;
					$this->img['lebar_thumb'] = ceil(($this->img['tinggi_thumb'] / $this->img['tinggi']) * $this->img['lebar']);

					
				}
				
				break;
			
			default :
				
				if( $this->img['lebar'] >= $this->img['tinggi'] ) {
					
					$this->img['lebar_thumb'] = $size;
					$this->img['tinggi_thumb'] = ceil(($this->img['lebar_thumb'] / $this->img['lebar']) * $this->img['tinggi']);
					
				} else {
					
					$this->img['tinggi_thumb'] = $size;
					$this->img['lebar_thumb'] = ceil(($this->img['tinggi_thumb'] / $this->img['tinggi']) * $this->img['lebar']);
				
				}
				
				break;
		}

		if ($this->img['lebar_thumb'] < 1 ) $this->img['lebar_thumb'] = 1;
		if ($this->img['tinggi_thumb'] < 1 ) $this->img['tinggi_thumb'] = 1;
		
		$this->img['des'] = imagecreatetruecolor( $this->img['lebar_thumb'], $this->img['tinggi_thumb'] );

		if ( $this->img['format'] == "PNG" OR $this->img['format'] == "WEBP" ) {
			imagealphablending( $this->img['des'], false);
			imagesavealpha( $this->img['des'], true);
		}


		if ( $this->img['format'] == "GIF" ) {

			$transparent_index=imagecolortransparent($this->img['src']);

			if($transparent_index!==-1){
				$transparent_color=imagecolorsforindex($this->img['src'], $transparent_index);
			 
				$transparent_destination_index=imagecolorallocate($this->img['des'], $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
				imagecolortransparent($this->img['des'], $transparent_destination_index);
			 
				imagefill($this->img['des'], 0, 0, $transparent_destination_index);
			}


		}

		@imagecopyresampled( $this->img['des'], $this->img['src'], 0, 0, 0, 0, $this->img['lebar_thumb'], $this->img['tinggi_thumb'], $this->img['lebar'], $this->img['tinggi'] );
		
		$this->img['src'] = $this->img['des'];
		return 1;

	}
	
	function jpeg_quality($quality = 90) {
		//jpeg quality
		$this->img['quality'] = $quality;
	}
	
	function save($save = "") {
		
		if( $this->img['format'] == "JPG" || $this->img['format'] == "JPEG" ) {
			//JPEG
			imagejpeg( $this->img['src'], $save, $this->img['quality'] );
			
		} elseif( $this->img['format'] == "PNG" ) {
				
			imagealphablending( $this->img['src'], false);
			imagesavealpha( $this->img['src'], true);
			imagepng( $this->img['src'], $save, 8 );
			
		} elseif( $this->img['format'] == "GIF" ) {
			//GIF
			imagegif( $this->img['src'], $save );
			
		} elseif( $this->img['format'] == "WEBP" ) {
			//WEBP
			imagealphablending( $this->img['src'], false);
			imagesavealpha( $this->img['src'], true);
			imagewebp( $this->img['src'], $save, $this->img['quality'] );
		}
		
		imagedestroy( $this->img['src'] );

		if( $this->img['tinypng'] ) {

			$this->tinypng_compress($save);
			
			
		}

	}
	
	function show() {
		if( $this->img['format'] == "JPG" || $this->img['format'] == "JPEG" ) {
			//JPEG
			imagejpeg( $this->img['src'], "", $this->img['quality'] );
		} elseif( $this->img['format'] == "PNG" ) {
			//PNG
			imagepng( $this->img['src'] );
		} elseif( $this->img['format'] == "GIF" ) {
			//GIF
			imagegif( $this->img['src'] );
		}
		
		imagedestroy( $this->img['src'] );
	}

	function hasAlpha($imgdata) {
	    $w = imagesx($imgdata);
	    $h = imagesy($imgdata);

	    if($w>50 || $h>50){ //resize the image to save processing if larger than 50px:
	        $thumb = imagecreatetruecolor(10, 10);
	        imagealphablending($thumb, FALSE);
	        imagecopyresized( $thumb, $imgdata, 0, 0, 0, 0, 10, 10, $w, $h );
	        $imgdata = $thumb;
	        $w = imagesx($imgdata);
	        $h = imagesy($imgdata);
	    }
	    //run through pixels until transparent pixel is found:
	    for($i = 0; $i<$w; $i++) {
	        for($j = 0; $j < $h; $j++) {
	            $rgba = imagecolorat($imgdata, $i, $j);
	            if(($rgba & 0x7F000000) >> 24) return true;
	        }
	    }
	    return false;
	}

	// *************************************************************************
	function insert_watermark($min_image) {
		global $config;
		$margin = 7;
		
		$this->watermark_image_light = ROOT_DIR . '/templates/' . $config['skin'] . '/dleimages/watermark_light.png';
		$this->watermark_image_dark = ROOT_DIR . '/templates/' . $config['skin'] . '/dleimages/watermark_dark.png';
		
		$image_width = imagesx( $this->img['src'] );
		$image_height = imagesy( $this->img['src'] );
		
		list ( $watermark_width, $watermark_height ) = getimagesize( $this->watermark_image_light );
		
		if($config['watermark_seite'] == 1) {
			
			$watermark_x = $margin;
			$watermark_y = $margin;
			
		} elseif($config['watermark_seite'] == 2) {
			
			$watermark_x = $image_width - $margin - $watermark_width;
			$watermark_y = $margin;
			
		} elseif($config['watermark_seite'] == 3) {
			
			$watermark_x = $margin;
			$watermark_y = $image_height - $margin - $watermark_height;
			
		} else {
	
			$watermark_x = $image_width - $margin - $watermark_width;
			$watermark_y = $image_height - $margin - $watermark_height;
		}
		
		$watermark_x2 = $watermark_x + $watermark_width;
		$watermark_y2 = $watermark_y + $watermark_height;
		
		if( $watermark_x < 0 OR $watermark_y < 0 OR $watermark_x2 > $image_width OR $watermark_y2 > $image_height OR $image_width < $min_image OR $image_height < $min_image ) {
			return;
		}
		
		if( $this->img['tinypng'] AND $this->img['tinypng_method'] ) {
			
			$this->tinypng_compress();
			
			$this->img['src'] = imagecreatefromstring( $this->img['tinypng_buffer'] );
			
			unset($this->img['tinypng_buffer']);
			
			$this->img['tinypng_method'] = false;
			
		}
		
		$test = imagecreatetruecolor( 1, 1 );
		imagecopyresampled( $test, $this->img['src'], 0, 0, $watermark_x, $watermark_y, 1, 1, $watermark_width, $watermark_height );
		$rgb = imagecolorat( $test, 0, 0 );
		
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		
		$max = min( $r, $g, $b );
		$min = max( $r, $g, $b );
		$lightness = ( double ) (($max + $min) / 510.0);
		imagedestroy( $test );
		
		$watermark_image = ($lightness < 0.5) ? $this->watermark_image_light : $this->watermark_image_dark;
		
		$watermark = imagecreatefrompng( $watermark_image );

		imagealphablending( $watermark, true );

		if( $this->img['format'] == "PNG" OR $this->img['format'] == "WEBP") {
			
			imagealphablending( $this->img['src'], true );
			$temp_img = imagecreatetruecolor( $image_width, $image_height );

			if( $this->hasAlpha($this->img['src']) ) {

				imagealphablending ( $temp_img , false );

			} else imagealphablending ( $temp_img , true );

			imagesavealpha ( $temp_img , true );

			$transparent_index=imagecolortransparent($this->img['src']);

			if($transparent_index!==-1){
				$transparent_color=imagecolorsforindex($this->img['src'], $transparent_index);
			 
				$transparent_destination_index=imagecolorallocate($temp_img, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
				imagecolortransparent($temp_img, $transparent_destination_index);
			 
				imagefill($temp_img, 0, 0, $transparent_destination_index);
			}	
			
			imagecopy( $temp_img, $this->img['src'], 0, 0, 0, 0, $image_width, $image_height );
			imagecopy( $temp_img, $watermark, $watermark_x, $watermark_y, 0, 0, $watermark_width, $watermark_height );
			imagecopy( $this->img['src'], $temp_img, 0, 0, 0, 0, $image_width, $image_height );
			imagedestroy( $temp_img );
		
		} elseif($this->img['format'] == "GIF") { 

			$temp_img = imagecreatetruecolor( $image_width, $image_height );

			$transparent_index=imagecolortransparent($this->img['src']);

			if($transparent_index!==-1){
				$transparent_color=imagecolorsforindex($this->img['src'], $transparent_index);
			 
				$transparent_destination_index=imagecolorallocate($temp_img, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
				imagecolortransparent($temp_img, $transparent_destination_index);
			 
				imagefill($temp_img, 0, 0, $transparent_destination_index);
			}

			imagecopy( $temp_img, $this->img['src'], 0, 0, 0, 0, $image_width, $image_height );
			imagecopy( $temp_img, $watermark, $watermark_x, $watermark_y, 0, 0, $watermark_width, $watermark_height );
			imagecopy( $this->img['src'], $temp_img, 0, 0, 0, 0, $image_width, $image_height );
			imagedestroy( $temp_img );

		} else {

			imagecopy( $this->img['src'], $watermark, $watermark_x, $watermark_y, 0, 0, $watermark_width, $watermark_height );

		}
	
		imagedestroy( $watermark );
	
	}
	
	function tinypng_compress( $file = false ) {
	
		try {
			
			if( $this->img['tinypng_method'] ) {
				
				$source = \Tinify\fromFile( $this->img['tinypng_source_file'] );
				
				$options = array("method" => $this->img['tinypng_method']);
				
				if( $this->img['tinypng_width'] ) $options['width'] = $this->img['tinypng_width'];
				if( $this->img['tinypng_height'] ) $options['height'] = $this->img['tinypng_height'];
			
				$resized = $source->resize($options);
				
				if( $file ) $resized->toFile($file);
				else $this->img['tinypng_buffer'] = $resized->toBuffer();

			} else {
				
				$source = \Tinify\fromFile( $file );
				
				if( $file ) $source->toFile($file);
				else $this->img['tinypng_buffer'] = $source->toBuffer();
				
			}
			
			return true;
			
		} catch(\Tinify\Exception $e) {
			
			$this->img['tinypng'] = false;
		
			$this->tinypng_error = $e->getMessage();
			
			return false;
			
		}

	}

	
}
?>