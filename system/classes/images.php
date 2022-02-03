<?php
/* 
	Appointment: создание уменьшенных копий
	File: images.php 
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
if(!defined( 'MOZG')){
	die( "Hacking attempt!" );
}

class thumbnail {
	var $img;
	var $watermark_image_light;
	var $watermark_image_dark;
	
	function thumbnail($imgfile) {
		//detect image format

		$info = @getimagesize($imgfile); 

		if( $info[2] == 2 ) {
			$this->img['format'] = "JPEG";
			$this->img['src'] = @imagecreatefromjpeg( $imgfile );
		} elseif( $info[2] == 3 ) {
			$this->img['format'] = "PNG";
			$this->img['src'] = @imagecreatefrompng( $imgfile );
		} elseif( $info[2] == 1 ) {
			$this->img['format'] = "GIF";
			$this->img['src'] = @imagecreatefromgif( $imgfile );
		} else {
			echo "Not Supported File! Thumbnails can only be made from .jpg, gif and .png images!";
			@unlink( $imgfile );
			exit();
		}

		if( !$this->img['src'] ) {
			echo "Not Supported File! Thumbnails can only be made from .jpg, gif and .png images!";
			@unlink( $imgfile );
			exit();
		
		}

		$this->img['lebar'] = @imagesx( $this->img['src'] );
		$this->img['tinggi'] = @imagesy( $this->img['src'] );
		$this->img['lebar_thumb'] = $this->img['lebar'];
		$this->img['tinggi_thumb'] = $this->img['tinggi'];
		//default quality jpeg
		$this->img['quality'] = 90;
		
	}
	
	function size_auto($size = 100, $site = 0, $jqCrop = 0) {

		$size = explode ("x", $size);
		
		if($jqCrop){

			return $this->jqCrop( intval($size[0]), intval($size[1]), $jqCrop);
			
		} else if ( count($size) == 2 ) {
			$size[0] = intval($size[0]);
			$size[1] = intval($size[1]);
			return $this->crop( intval($size[0]), intval($size[1]) );

		} else {
			$size[0] = intval($size[0]);
			return $this->scale( intval($size[0]), $site);

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

		$nw = min($nw, $w);
		$nh = min($nh, $h);

		$size_ratio = max($nw / $w, $nh / $h);

		$src_w = ceil($nw / $size_ratio);
		$src_h = ceil($nh / $size_ratio);

		$sx = floor(($w - $src_w)/2);
		$sy = floor(($h - $src_h)/2);

		$this->img['des'] = imagecreatetruecolor($nw, $nh);

		if ( $this->img['format'] == "PNG" ) {
			imagealphablending( $this->img['des'], false);
			imagesavealpha( $this->img['des'], true);
		}

		imagecopyresampled($this->img['des'],$this->img['src'],0,0,$sx,0,$nw,$nh,$src_w,$src_h);

		$this->img['src'] = $this->img['des'];
		return 1;
	}
	
	function jqCrop($nw, $nh, $cropData) {
		$cropDataExp = explode('|', $cropData);
		$left = $cropDataExp[0];
		$top = $cropDataExp[1];
		
		if(!$left OR $left <= 0) $left = 0;
		if(!$top OR $top <= 0) $top = 0;

		if($nw < 100) $nw = 100;
		if($nh < 100) $nh = 100;
		
		$w = $this->img['lebar'];
		$h = $this->img['tinggi'];

		if( $w <= $nw AND $h <= $nh ) {
			$this->img['lebar_thumb'] = $w;
			$this->img['tinggi_thumb'] = $h;
			return 0;
		}

		$nw = min($nw, $w);
		$nh = min($nh, $h);

		$size_ratio = max($nw / $w, $nh / $h);

		$src_w = ceil($nw / $size_ratio);
		$src_h = ceil($nh / $size_ratio);

		$this->img['des'] = imagecreatetruecolor($nw, $nh);

		if ( $this->img['format'] == "PNG" ) {
			imagealphablending( $this->img['des'], false);
			imagesavealpha( $this->img['des'], true);
		}

		imagecopyresampled($this->img['des'], $this->img['src'], 0, 0, $left, $top, $nw, $nh, $nw, $nh);

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
					$this->img['tinggi_thumb'] = ($this->img['lebar_thumb'] / $this->img['lebar']) * $this->img['tinggi'];
				}
				
				break;
			
			case "2" :
				if( $this->img['tinggi'] <= $size ) {
					$this->img['lebar_thumb'] = $this->img['lebar'];
					$this->img['tinggi_thumb'] = $this->img['tinggi'];
					return 0;
				} else {
					$this->img['tinggi_thumb'] = $size;
					$this->img['lebar_thumb'] = ($this->img['tinggi_thumb'] / $this->img['tinggi']) * $this->img['lebar'];
				}
				
				break;
			
			default :
				
				if( $this->img['lebar'] >= $this->img['tinggi'] ) {
					$this->img['lebar_thumb'] = $size;
					$this->img['tinggi_thumb'] = ($this->img['lebar_thumb'] / $this->img['lebar']) * $this->img['tinggi'];
				
				} else {
					
					$this->img['tinggi_thumb'] = $size;
					$this->img['lebar_thumb'] = ($this->img['tinggi_thumb'] / $this->img['tinggi']) * $this->img['lebar'];
				
				}
				
				break;
		}

		if ($this->img['lebar_thumb'] < 1 ) $this->img['lebar_thumb'] = 1;
		if ($this->img['tinggi_thumb'] < 1 ) $this->img['tinggi_thumb'] = 1;
		
		$this->img['des'] = imagecreatetruecolor( $this->img['lebar_thumb'], $this->img['tinggi_thumb'] );

		if ( $this->img['format'] == "PNG" ) {
			imagealphablending( $this->img['des'], false);
			imagesavealpha( $this->img['des'], true);
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
			//PNG
			imagealphablending( $this->img['src'], false);
			imagesavealpha( $this->img['src'], true);
			imagepng( $this->img['src'], $save );
		} elseif( $this->img['format'] == "GIF" ) {
			//GIF
			imagegif( $this->img['src'], $save );
		}
		
		imagedestroy( $this->img['src'] );
	}
	
	function show() {
		if( $this->img['format'] == "JPG" || $this->img['format'] == "JPEG" ) {
			//JPEG
			imageJPEG( $this->img['src'], "", $this->img['quality'] );
		} elseif( $this->img['format'] == "PNG" ) {
			//PNG
			imagePNG( $this->img['src'] );
		} elseif( $this->img['format'] == "GIF" ) {
			//GIF
			imageGIF( $this->img['src'] );
		}
		
		imagedestroy( $this->img['src'] );
	}

}
?>