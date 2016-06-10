<?php
/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * Class we_image_resize
 *
 * Provides functions for creating webEdition buttons.
 */
abstract class we_base_imageEdit{
	const IMAGE_EXTENSIONS = 'svgz';

	public static $GDIMAGE_TYPE = array('.gif' => 'gif', '.jpg' => 'jpg', '.jpeg' => 'jpg', '.png' => 'png');

	/*	 * ***********************************************************************
	 * FUNCTIONS
	 * *********************************************************************** */

	/**
	 * Returns values of the function phpinfo().
	 *
	 * @see        gd_info()
	 *
	 * @return     array
	 */
	private static function php_info(){
		static $phpinfo = array();

		// Check if need to get the requested information
		if(empty($phpinfo)){
			// Start output buffering
			ob_start();

			phpinfo();

			// Read output of the function phpinfo()
			$returned_phpinfo = ob_get_clean();

			// Fill informations of PHP
			$phpinfo = explode("\n", $returned_phpinfo);
		}

		return $phpinfo;
	}

	public static function supported_image_types(){
		$output_formats = array();

		if(function_exists('ImageTypes')){
			$imagetypes = ImageTypes();

			// Check output formats
			if($imagetypes & IMG_GIF){
				$output_formats[] = 'gif';
			}

			if($imagetypes & IMG_PNG){
				$output_formats[] = 'png';
			}

			if($imagetypes & IMG_JPG){
				$output_formats[] = 'jpg';
			}
		}
		return $output_formats;
	}

	public static function detect_image_type($filename, $imagedata = ''){
		// Check if we need to read the beginning of the image
		$imagedata = (file_exists($filename) ? we_base_file::loadPart($filename, 0, 3) : substr($imagedata, 0, 3));

		switch($imagedata){
			case 'GIF':
				return 'gif';
			case "\xFF\xD8\xFF":
				return 'jpg';
			case "\x89" . 'PN':
				return 'png';
			default:
				if(substr($imagedata, 0, 2) === 'BM'){
					return 'bmp';
				}
				return '';
		}
	}

	private static function gd_info(){
		// Check if we need to emulate this function since it is built into PHP v4.3.0+ (with bundled GD2 library)
		if(!function_exists('gd_info')){
			static $gdinfo = array();

			// Check if need to get the requested information
			if(empty($gdinfo)){
				// Initialize array with default values
				$gdinfo = array('GD Version' => '', 'FreeType Support' => false, 'FreeType Linkage' => '', 'T1Lib Support' => false, 'GIF Read Support' => false, 'GIF Create Support' => false, 'JPG Support' => false, 'PNG Support' => false, 'WBMP Support' => false, 'XBM Support' => false);

				// Now we need to read the phpinfo() to detect the GD library support
				$phpinfo = self::php_info();

				foreach($phpinfo as $value){
					$value = trim(strip_tags($value));

					foreach(array_keys($gdinfo) as $key){
						if(strpos($value, $key) === 0){
							$new_value = trim(str_replace($key, '', $value));
							$gdinfo[$key] = $new_value;
						}
					}
				}

				// Check if GD version information is present now
				if(empty($gdinfo['GD Version'])){
					// Check if we can detect GD library by bypassing the function: phpinfo()
					if(function_exists('ImageTypes')){
						$imagetypes = ImageTypes();

						// Check JPG support
						if($imagetypes & IMG_JPG){
							$gdinfo['JPG Support'] = true;
						}

						// Check PNG support
						if($imagetypes & IMG_PNG){
							$gdinfo['PNG Support'] = true;
						}

						// Check GIF support
						if($imagetypes & IMG_GIF){
							$gdinfo['GIF Create Support'] = true;
						}
					}

					// Detect capabilities of GIF support
					if(function_exists('ImageCreateFromGIF')){
						if(($tempfilename = we_base_file::saveTemp(base64_decode('R0lGODlhAQABAIAAAH//AP///ywAAAAAAQABAAACAUQAOw==')))){

							// GIF create support must be enabled if we're able to create a image
							$gif_test = @imagecreatefromgif($tempfilename);

							if($gif_test){
								$gdinfo['GIF Read Support'] = true;
							}
							unlink($tempfilename);
						}
					}

					// Detect version of GD library
					if(function_exists('ImageCreateTrueColor') && @imagecreatetruecolor(1, 1)){
						$gdinfo['GD Version'] = '2.0.1 or higher (assumed)';
					} else if(function_exists('ImageCreate') && @imagecreate(1, 1)){
						$gdinfo['GD Version'] = '1.6.0 or higher (assumed)';
					}
				}
			}

			return $gdinfo;
		} else {
			return gd_info();
		}
	}

	public static function gd_version(){

		static $gdversion = 0;

		// Check if need to get the requested information
		if(empty($gdversion)){
			// Request information about GD libary
			$gdinfo = self::gd_info();

			// Define string to be searched
			$searchstring = 'bundled (';

			// Detect information string now
			$gdversion = (substr($gdinfo['GD Version'], 0, strlen($searchstring)) == $searchstring ?
					substr($gdinfo['GD Version'], strlen($searchstring), 3) :
					substr($gdinfo['GD Version'], 0, 3));
		}

		return $gdversion;
	}

	private static function ImageCreateFromStringReplacement(&$imagedata){
		// Serious bugs in the non-bundled versions of GD library cause PHP to segfault when calling ImageCreateFromString() - avoid if possible
		$gdimg = false;

		switch(self::detect_image_type('', $imagedata)){
			case 'gif':
				$image_create_from_string_replacement_function = 'imagecreatefromgif';
				break;
			case 'jpg':
				$image_create_from_string_replacement_function = 'ImageCreateFromJPEG';
				break;
			case 'png':
				$image_create_from_string_replacement_function = 'ImageCreateFromPNG';
				break;
			default:
				return '';
		}

		if(($tempfilename = we_base_file::saveTemp($imagedata))){
			$imagedata = '';
			unset($imagedata);
			if(function_exists($image_create_from_string_replacement_function)){
				$gdimg = $image_create_from_string_replacement_function($tempfilename);
			}
			unlink($tempfilename);
		}

		return $gdimg;
	}

	private static function ImageCreateFromFileReplacement($filename){
		switch(self::detect_image_type($filename)){
			case 'gif':
				$image_create_from_string_replacement_function = 'imagecreatefromgif';
				break;
			case 'jpg':
				$image_create_from_string_replacement_function = 'ImageCreateFromJPEG';
				break;
			case 'png':
				$image_create_from_string_replacement_function = 'ImageCreateFromPNG';
				break;
			default:
				return false;
		}

		if(function_exists($image_create_from_string_replacement_function)){
			return $image_create_from_string_replacement_function($filename);
		}
	}

	private static function calculate_image_size($origwidth, $origheight, $newwidth, $newheight, $keep_aspect_ratio = true, $maxsize = true, $fitinside = false){
		if(self::should_not_resize($origwidth, $origheight, $newwidth, $newheight, $maxsize, $fitinside)){
			return array('width' => $origwidth, 'height' => $origheight, 'useorig' => 1);
		}

		// If width has been specified set it and compute new height based on source area aspect ratio
		if($newwidth){
			$outsize['width'] = $newwidth;
			$outsize['height'] = round($origheight * $newwidth / $origwidth);
		} else {
			// bugfix #2482: preserve aspect ratio for thumbnails with width=0 and height != 0
			$outsize['width'] = round(($origwidth / $origheight) * $newheight);
			$outsize['height'] = ($newheight ? : round($origheight * $newwidth / $origwidth));
		}

		// If height has been specified set it.
		// If width has already been set and the new image is too tall, compute a new width based
		// on aspect ratio - otherwise, use height and compute new width
		if($newheight){
			if($outsize['height'] > $newheight){
				$outsize['width'] = round($origwidth * $newheight / $origheight);
				$outsize['height'] = $newheight;
			}
		}

		// Check, if we must discard aspect ratio
		if(!$keep_aspect_ratio && ($newwidth) && ($newheight)){
			$outsize['width'] = $newwidth;
			$outsize['height'] = $newheight;
		}

		// Check, if it is supposed to fit inside
		if($fitinside && ($newwidth) && ($newheight)){
			$outsize['width'] = $newwidth;
			$outsize['height'] = $newheight;
		}

		return array('width' => $outsize['width'], 'height' => $outsize['height'], 'useorig' => 0);
	}

	static function calculate_image_sizeFit($origwidth, $origheight, $newwidth, $newheight, $maxsize = true){
		if(self::should_not_resize($origwidth, $origheight, $newwidth, $newheight, $maxsize, true)){
			return array('width' => $origwidth, 'height' => $origheight, 'useorig' => 1);
		}

		// If width has been specified set it and compute new height based on source area aspect ratio
		// here it is set
		$outsize['width'] = $newwidth;
		$outsize['height'] = round($origheight * $newwidth / $origwidth);



		// If width has already been set and the new image is too tall, compute a new width based
		// on aspect ratio - otherwise, use height and compute new width
		if($newheight){
			if($outsize['height'] > $newheight){
				$outsize['width'] = round($origwidth * $newheight / $origheight);
				$outsize['height'] = $newheight;
			}
		}


		return array('width' => $outsize['width'], 'height' => $outsize['height'], 'useorig' => 0);
	}

	private static function should_not_resize($origwidth, $origheight, $newwidth, $newheight, $maxsize = false, $fitinside = false){
		return (!$maxsize) && (!$fitinside) && ($origwidth <= $newwidth) && ($origheight <= $newheight);
	}

	public static function getimagesize($filename){
		$type = self::detect_image_type($filename);
		if(self::is_imagetype_supported($type)){
			$gdimg = self::ImageCreateFromFileReplacement($filename);
			$ct = 0;
			switch($type){
				case 'gif':
					$ct = 1;
					break;
				case 'jpg':
					$ct = 2;
					break;
				case 'png':
					$ct = 3;
					break;
			}
			$w = ImageSX($gdimg);
			$h = ImageSY($gdimg);
			return array($w, $h, $ct, 'width="' . $w . '" height="' . $h . '"');
		}
		return array();
	}

	public static function is_imagetype_supported($type){
		return in_array($type, self::supported_image_types());
	}

	public static function is_imagetype_read_supported($type){
		$t = array('gif', 'jpg', 'png');

		$sit = self::supported_image_types();
		$fn = '';

		foreach($t as $cur){
			if(!in_array($cur, $sit)){
				switch($t[$i]){
					case 'gif':
					case 'png':
						$fn = 'ImageCreateFrom' . strtoupper($cur);
						break;
					case 'jpg':
						$fn = 'ImageCreateFromJPEG';
						break;
				}
				if(function_exists($fn)){
					if(@$fn($_SERVER['DOCUMENT_ROOT'] . IMAGE_DIR . 'foo.' . $cur)){
						$sit[] = $cur;
					}
				}
			}
		}
		return in_array($type, $sit);
	}

	private static function UnsharpMask($img, $amount = 80, $radius = .5, $threshold = 3){

////////////////////////////////////////////////////////////////////////////////////////////////
////
////                  Unsharp Mask for PHP - version 2.1.1
////
////    Unsharp mask algorithm by Torstein Hønsi 2003-07.
////             thoensi_at_netcom_dot_no.
////               Please leave this notice.
////
///////////////////////////////////////////////////////////////////////////////////////////////
		// $img is an image that is already created within php using
		// imgcreatetruecolor. No url! $img must be a truecolor image.
		// Attempt to calibrate the parameters to Photoshop:
		$amount = min($amount, 500) * 0.016;
		$radius = round(min(abs($radius), 50) * 2);
		$threshold = min($threshold, 255);
		if($radius == 0){
			return $img;
		}
		$w = imagesx($img);
		$h = imagesy($img);
		$imgCanvas = imagecreatetruecolor($w, $h);
		$imgBlur = imagecreatetruecolor($w, $h);


		// Gaussian blur matrix:
		//
		//    1    2    1
		//    2    4    2
		//    1    2    1
		//
		//////////////////////////////////////////////////


		$matrix = array(
			array(1, 2, 1),
			array(2, 4, 2),
			array(1, 2, 1)
		);
		imagecopy($imgBlur, $img, 0, 0, 0, 0, $w, $h);
		imageconvolution($imgBlur, $matrix, 16, 0);

		if($threshold > 0){
			// Calculate the difference between the blurred pixels and the original
			// and set the pixels
			for($x = 0; $x < $w - 1; $x++){ // each row
				for($y = 0; $y < $h; $y++){ // each pixel
					$rgbOrig = ImageColorAt($img, $x, $y);
					$rOrig = (($rgbOrig >> 16) & 0xFF);
					$gOrig = (($rgbOrig >> 8) & 0xFF);
					$bOrig = ($rgbOrig & 0xFF);

					$rgbBlur = ImageColorAt($imgBlur, $x, $y);

					$rBlur = (($rgbBlur >> 16) & 0xFF);
					$gBlur = (($rgbBlur >> 8) & 0xFF);
					$bBlur = ($rgbBlur & 0xFF);

					// When the masked pixels differ less from the original
					// than the threshold specifies, they are set to their original value.
					$rNew = (abs($rOrig - $rBlur) >= $threshold) ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig)) : $rOrig;
					$gNew = (abs($gOrig - $gBlur) >= $threshold) ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig)) : $gOrig;
					$bNew = (abs($bOrig - $bBlur) >= $threshold) ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig)) : $bOrig;

					if(($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)){
						$pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
						ImageSetPixel($img, $x, $y, $pixCol);
					}
				}
			}
		} else {
			for($x = 0; $x < $w; $x++){ // each row
				for($y = 0; $y < $h; $y++){ // each pixel
					$rgbOrig = ImageColorAt($img, $x, $y);
					$rOrig = (($rgbOrig >> 16) & 0xFF);
					$gOrig = (($rgbOrig >> 8) & 0xFF);
					$bOrig = ($rgbOrig & 0xFF);

					$rgbBlur = ImageColorAt($imgBlur, $x, $y);

					$rBlur = (($rgbBlur >> 16) & 0xFF);
					$gBlur = (($rgbBlur >> 8) & 0xFF);
					$bBlur = ($rgbBlur & 0xFF);

					$rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
					if($rNew > 255){
						$rNew = 255;
					} elseif($rNew < 0){
						$rNew = 0;
					}
					$gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
					if($gNew > 255){
						$gNew = 255;
					} elseif($gNew < 0){
						$gNew = 0;
					}
					$bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
					if($bNew > 255){
						$bNew = 255;
					} elseif($bNew < 0){
						$bNew = 0;
					}
					$rgbNew = ($rNew << 16) + ($gNew << 8) + $bNew;
					ImageSetPixel($img, $x, $y, $rgbNew);
				}
			}
		}
		imagedestroy($imgCanvas);
		imagedestroy($imgBlur);

		return $img;
	}

	public static function edit_image($imagedata, $output_format = 'jpg', $output_filename = '', $output_quality = 75, $width = '', $height = '', array $options = array(we_thumbnail::OPTION_RATIO, we_thumbnail::OPTION_INTERLACE), array $crop = array(0, 0), $rotate_angle = 0){
		$output_format = strtolower($output_format);
		if($output_format === 'jpeg'){
			$output_format = 'jpg';
		}
		$options = array_filter($options);
		$fromFile = (strlen($imagedata) < 255 && @file_exists($imagedata));

		// Output format is available
		if(!in_array($output_format, self::supported_image_types())){
			return array(false, -1, -1);
		}
		// Set quality for JPG images
		if($output_format === 'jpg'){
			// Keep quality between 1 and 99
			$output_quality = max(1, min(99, (is_int($output_quality) ? $output_quality : 75)));
		}

		$gdimg = ($fromFile ? self::ImageCreateFromFileReplacement($imagedata) : self::ImageCreateFromStringReplacement($imagedata));

		// Now we need to ensure that we could read the file
		if($gdimg){
			// Detect dimension of image
			$width = ImageSX($gdimg);
			$height = ImageSY($gdimg);

			if(($rotate_angle != 0) && function_exists('ImageRotate')){
				$rotate_angle = floatval($rotate_angle);
				if($rotate_angle < 0){
					$rotate_angle = $rotate_angle + ((abs(intval($rotate_angle / 360)) + 1) * 360);
				} elseif($rotate_angle > 360){
					$rotate_angle = $rotate_angle % 360;
				}

				if($rotate_angle > 0){
					$gdimg = ImageRotate($gdimg, $rotate_angle, 0);
					$width = ImageSX($gdimg);
					$height = ImageSY($gdimg);
				}
			}

			$outsize = self::calculate_image_size($width, $height, $width, $height, in_array(we_thumbnail::OPTION_RATIO, $options), true, in_array(we_thumbnail::OPTION_FITINSIDE, $options));

			// Decide, which functions to use (depends on version of GD library)
			$image_create_function = (self::gd_version() >= 2.0 ? 'imagecreatetruecolor' : 'imagecreate');
			$image_resize_function = (function_exists('imagecopyresampled') ? 'imagecopyresampled' : 'imagecopyresized');

			$outsize['width'] = max(1, $outsize['width']);
			$outsize['height'] = max(1, $outsize['height']);


			// Now create the image
			$output_gdimg = $image_create_function($outsize['width'], $outsize['height']); // this image is always black
			// preserve transparency of png and gif images:
			switch($output_format){
				case 'gif':
					$colorTransparent = imagecolortransparent($gdimg);
					imagepalettecopy($gdimg, $output_gdimg);
					imagefill($output_gdimg, 0, 0, $colorTransparent);
					imagecolortransparent($output_gdimg, $colorTransparent);
					imagetruecolortopalette($output_gdimg, true, 256);
					break;
				case 'png':
					imagealphablending($output_gdimg, false);
					//$transparent = imagecolorallocatealpha($_output_gdimg, 0, 0, 0, 127);
					$transparent = imagecolorallocatealpha($output_gdimg, 255, 255, 255, 127);
					imagefill($output_gdimg, 0, 0, $transparent);
					imagesavealpha($output_gdimg, true);
					break;
				default:
			}

			if((in_array(we_thumbnail::OPTION_FITINSIDE, $options) || in_array(we_thumbnail::OPTION_CROP, $options)) && $width && $height){
				if(in_array(we_thumbnail::OPTION_FITINSIDE, $options)){
					$wratio = $width / $width;
					$hratio = $height / $height;
					$ratio = max($width / $width, $height / $height);
					$h = $height / $ratio;
					$w = $width / $ratio;

					if($wratio < $hratio){
						$x = ($width - $w) / 2;
						$y = 0;
					} else {
						$x = 0;
						$y = ($height - $h) / 2;
					}
				} else {
					$h = $height;
					$w = $width;
					$x = ($width - $w) / 2;
					$y = ($height - $h) / 2;
				}

				if(array_filter($crop)){
					$xNew = $x + ($w / 2); // x + origthumbwidth/2 => x-Mittelpunkt
					$x = $xNew + ($xNew * $crop[0]) - ($w / 2); // Mittelpunkt + Bildfokus - origthumbwidth/2 => Neuer x-Punkt
					if($x + $w > $width){
						$x = $width - $w;
					}
					$x = max(0, $x);
					$yNew = $y + ($h / 2); // y + origthumbheight/2 => y-Mittelpunkt
					$y = $yNew + ($yNew * $crop[1]) - ($h / 2); // Mittelpunkt + Bildfokus - origthumbheight/2 => Neuer y-Punkt
					if($y + $h > $height){
						$y = $height - $h;
					}
					$y = max(0, $y);
				}

				$image_resize_function($output_gdimg, $gdimg, 0, 0, $x, $y, $width, $height, $w, $h);
			} else {
				$image_resize_function($output_gdimg, $gdimg, 0, 0, 0, 0, $outsize['width'], $outsize['height'], $width, $height);
			}

			// PHP 4.4.1 GDLIB-Bug/Safemode - Workarround
			if($output_filename != '' && file_exists($output_filename)){
				touch($output_filename);
			}
			if(in_array(we_thumbnail::OPTION_GAUSSBLUR, $options)){
				imagefilter($output_gdimg, IMG_FILTER_GAUSSIAN_BLUR);
			}
			if(in_array(we_thumbnail::OPTION_GRAY, $options) || in_array(we_thumbnail::OPTION_SEPIA, $options)){
				imagefilter($output_gdimg, IMG_FILTER_GRAYSCALE);
			}
			if(in_array(we_thumbnail::OPTION_NEGATE, $options)){
				imagefilter($output_gdimg, IMG_FILTER_NEGATE);
			}
			if(in_array(we_thumbnail::OPTION_SEPIA, $options)){
				imagefilter($output_gdimg, IMG_FILTER_COLORIZE, 90, 60, 40);
			}


			if(in_array(we_thumbnail::OPTION_UNSHARP, $options)){
				$output_gdimg = self::UnsharpMask($output_gdimg);
			}
			ImageInterlace($output_gdimg, (in_array(we_thumbnail::OPTION_INTERLACE, $options) ? 1 : 0));

			switch($output_format){
				case 'jpg':
					// Output to a filename or directly
					if($output_filename){
						$gdimg = imagejpeg($output_gdimg, $output_filename, $output_quality) ?
							basename($output_filename) :
							'';
					} elseif(($tempfilename = tempnam(TEMP_PATH, ''))){
						imagejpeg($output_gdimg, $tempfilename, $output_quality);
						$gdimg = we_base_file::load($tempfilename);

						// As we read the temporary file we no longer need it
						unlink($tempfilename);
					}

					break;

				case 'png':
				case 'gif':
					// Set output function
					$image_out_function = 'image' . $output_format;
					// Output to a filename or directly
					if($output_filename){
						$gdimg = $image_out_function($output_gdimg, $output_filename);
						if($gdimg){
							$gdimg = basename($output_filename);
						}
					} elseif(($tempfilename = tempnam(TEMP_PATH, ''))){
						$image_out_function($output_gdimg, $tempfilename);
						$gdimg = we_base_file::load($tempfilename);

						// As we read the temporary file we no longer need it
						unlink($tempfilename);
					}

					break;
			}

			ImageDestroy($output_gdimg);
		}

			return !empty($gdimg) ? array($gdimg, $outsize['width'], $outsize['height']) : array(false, -1, -1);
	}

	/* static function ImageTrueColorToPalette2($image, $dither, $ncolors){
	  $width = @imagesx($image);
	  $height = @imagesy($image);
	  $colors_handle = @imagecreatetruecolor($width, $height);
	  @imagecopymerge($colors_handle, $image, 0, 0, 0, 0, $width, $height, 100);
	  @imagetruecolortopalette($image, $dither, $ncolors);
	  if(is_callable("imagecolormatch")){
	  @imagecolormatch($colors_handle, $image);
	  }
	  @imagedestroy($colors_handle);
	  return $image;
	  } */

	public static function createPreviewThumb($imgSrc, $imgID, $width, $height, &$outputFormat = 'jpg', $outputQuality = 75, $tmpName = ''){
		if(self::gd_version() == 0){
			$outputFormat = 'gif';
			return $_SERVER['DOCUMENT_ROOT'] . ICON_DIR . 'image.gif';
		}
		if(substr($imgSrc, 0, strlen($_SERVER ['DOCUMENT_ROOT'])) == $_SERVER['DOCUMENT_ROOT']){ // it is no src, it is a server path
			$imgSrc = substr($imgSrc, strlen($_SERVER['DOCUMENT_ROOT']));
		}
		$imgSrc = '/' . ltrim($imgSrc, '/');

		$imgPath = $_SERVER ['DOCUMENT_ROOT'] . WEBEDITION_DIR . '..' . $imgSrc;
		$path_parts = pathinfo($imgPath);
		if(isset($path_parts['extension']) && ( $path_parts ['extension'] === 'svg' || $path_parts['extension'] === 'svgz')){
			if(file_exists($imgPath)){
				$outputFormat = 'svg-xml';
				return $imgPath;
			}
			$outputFormat = 'gif';
			return $_SERVER['DOCUMENT_ROOT'] . ICON_DIR . 'image.gif';
		}
		if(!file_exists($imgPath) || !($imagesize = getimagesize($imgPath))){
			return $imgPath;
		}

		if($imagesize[0] > $width || $imagesize[1] > $height){
			$thumbSrc = ($imgID ?
					WE_THUMBNAIL_DIRECTORY . '/' . $imgID . '_' . $width . '_' . $height . '.' . strtolower($outputFormat) :
					TEMP_DIR . ($tmpName ? : we_base_file::getUniqueId()) . '.' . strtolower($outputFormat));
			$thumbPath = WEBEDITION_PATH . '../' . $thumbSrc;

			$thumbExists = file_exists($thumbPath);

			$imageCreationDate = filemtime($imgPath);
			$thumbCreationDate = $thumbExists ? filemtime($thumbPath) : 0;

			if(!$thumbExists || ($imageCreationDate > $thumbCreationDate)){
				self::edit_image($imgPath, $outputFormat, $thumbPath, $outputQuality, $width, $height);
			}
			$thumbSrc = $_SERVER['DOCUMENT_ROOT'] . $thumbSrc;
			if(!$imgID){
				//keep the file for 1h
				we_base_file::insertIntoCleanUp($thumbSrc, 3600);
			}
			return $thumbSrc;
		}

		return $imgPath;
	}

	/**
	 * returns the HTML for a quality output select box
	 *
	 * @return string
	 * @param string $name
	 * @param string[optional] $sel
	 */
	public static function qualitySelect($name = 'quality', $sel = 8){
		return '<select name="' . $name . '" class="weSelect">
<option value="0"' . (($sel == 0) ? ' selected' : '') . '>0 - ' . g_l('weClass', '[quality_low]') . '</option>
<option value="1"' . (($sel == 1) ? ' selected' : '') . '>1</option>
<option value="2"' . (($sel == 2) ? ' selected' : '') . '>2</option>
<option value="3"' . (($sel == 3) ? ' selected' : '') . '>3</option>
<option value="4"' . (($sel == 4) ? ' selected' : '' ) . '>4 - ' . g_l('weClass', '[quality_medium]') . '</option>
<option value="5"' . (($sel == 5) ? ' selected' : '') . '>5</option>
<option value="6"' . (($sel == 6) ? ' selected' : '') . '>6</option>
<option value="7"' . (($sel == 7) ? ' selected' : '') . '>7</option>
<option value="8"' . (($sel == 8) ? ' selected' : '') . '>8 - ' . g_l('weClass', '[quality_high]') . '</option>
<option value="9"' . (($sel == 9) ? ' selected' : '') . '>9</option>
<option value="10"' . (($sel == 10) ? ' selected' : '' ) . '>10 - ' . g_l('weClass', '[quality_maximum]') . '</option>
</select>';
	}

}
