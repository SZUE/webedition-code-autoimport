<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * Utility class for string manipulation and creation
 *
 * @category   we
 * @package    none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
abstract class we_util_Strings{

	const PRECISION = 2;

	/**
	 * Returns an unique ID
	 *
	 * @param integer $length  length of generated ID (default: 32)
	 * @return string
	 */
	static function createUniqueId($length = 32){
		return substr('we' . md5(uniqid(__FILE__, true)), 0, $length); // #6590, changed from: uniqid(time())
	}

	/**
	 * Returns CSV of array values.
	 *
	 * @deprecated since version 6.3.8
	 * @param string $arr  The array to be converted
	 * @param boolean $prePostKomma Flag to add leading and tailing separators (default: false)
	 * @param string $sep  The separator to be used (default: ',')
	 * @return string
	 */
	static function makeCSVFromArray($arr, $prePostKomma = false, $sep = ","){
		if(!$arr){
			return '';
		}

		$replaceKomma = (count($arr) > 1) || ($prePostKomma == true);

		if($replaceKomma){
			for($i = 0; $i < count($arr); $i++){
				$arr[$i] = str_replace($sep, "###komma###", $arr[$i]);
			}
		}
		$out = implode($sep, $arr);
		if($prePostKomma){
			$out = $sep . $out . $sep;
		}
		if($replaceKomma){
			$out = str_replace("###komma###", "\\$sep", $out);
		}
		return $out;
	}

	/**
	 * Returns an array of CSV values
	 *
	 * @deprecated since version 6.3.8
	 * @param string $csv  The comma separated string
	 * @return array
	 */
	static function makeArrayFromCSV($csv){
		$csv = str_replace("\\,", "###komma###", $csv);

		if($csv{0} === ','){
			$csv = substr($csv, 1);
		}
		if(substr($csv, -1) === ','){
			$csv = substr($csv, 0, strlen($csv) - 1);
		}
		if($csv == '' && $csv != '0'){
			$foo = array();
		} else {
			$foo = explode(",", $csv);
			for($i = 0; $i < count($foo); $i++){
				$foo[$i] = str_replace("###komma###", ",", $foo[$i]);
			}
		}
		return $foo;
	}

	/**
	 * Returns a quoted string
	 *
	 * @param string $text
	 * @param boolean $quoteForSingle (default: true)
	 * @return string
	 */
	static function quoteForJSString($text, $quoteForSingle = true){
		return ($quoteForSingle ?
				str_replace('\'', '\\\'', str_replace('\\', '\\\\', $text)) :
				str_replace("\"", "\\\"", str_replace("\\", "\\\\", $text)));
	}

	/**
	 * Returns a shortened string representation of a path (e.g. '/path/.../file.php')
	 *
	 * @param string $path  The path to be shortened.
	 * @param integer $len  Length (lower bound), when to start shortening (minimum = 10).
	 * @return string
	 */
	static function shortenPath($path, $len){
		if(strlen($path) <= $len || strlen($path) < 10){
			return $path;
		}
		$l = ($len / 2) - 2;
		return substr($path, 0, $l) . '...' . substr($path, $l * -1);
	}

	/**
	 * Splits up the given path every n-th character and adds a space separator in between
	 *
	 * Example for $len = 10:
	 *   input:  "file(filename)"
	 *   output: "/segment-1 /segment-2 /segment-3 /file"
	 *
	 * @param string $path  The path to be split up.
	 * @param integer $len  Length, when to start the next segment (minimum = 10).
	 * @return string
	 */
	static function shortenPathSpace($path, $len){
		if(strlen($path) <= $len || strlen($path) < 10){
			return $path;
		}
		$l = $len;
		return substr($path, 0, $l) . ' ' . self::shortenPathSpace(substr($path, $l), $len);
	}

	/**
	 * Returns a formatted string representation for the given float.
	 *
	 * @param float   $value     The float to format
	 * @param string  $format    The number format to use (default:english,
	 *                           available: german, deutsch, french, swiss, english)
	 * @param integer $precision The number of decimal points (default: 2)
	 * @return string
	 */
	static function formatNumber($number, $format = '', $precision = self::PRECISION){
		switch($format){
			case 'german':
			case 'deutsch':
				return number_format(floatval($number), $precision, ',', '.');
			case 'french':
				return number_format(floatval($number), $precision, ',', ' ');
			case 'swiss':
				return number_format(floatval($number), $precision, '.', "'");
			case 'english':
			default:
				return number_format(floatval($number), $precision, '.', '');
		}
	}

	/**
	 * Converts a version string to a number.
	 *
	 * Examples:
	 *  - "3.2.4.0" -> 3240
	 *  - "6.4.0.0" -> 6400
	 *  - "7.0.0.0" -> 7000
	 *
	 * @param string $version  The version to convert into a number
	 * @param bool $isApp      Handle minor version (default: false)
	 * @return float
	 */
	static function version2number($version, $isApp = false){
		if($isApp){
			if($version{0} == 0){
				if(strlen($version) == 3){
					$numberStr = '0.0' . $version{2};
					$number = (float) $numberStr;
				} else {
					$numberStr = '0.' . substr($version, 2, 2);
					$number = (float) $numberStr;
				}
			} else {
				$count = 2;
				$numberStr = str_replace('.', '', $version, $count);
				$number = (float) $numberStr;
			}
		} else {
			$count = 3;
			if($version{0} == 6){
				$numberStr = str_replace('.', '', $version, $count);
				$number = (float) $numberStr;
			} else {
				$numberStr = str_replace('.', '', $version, $count);
				$number = (float) $numberStr;
			}
		}
		return $number;
	}

	/**
	 * This function converts a version number (integer/float) to the string
	 * representative. Each number separated with ".". Parameter isApp determines
	 * if the version number might be float to allow for 0.something.
	 *
	 * Examples:
	 *  - 3240 -> 3.2.4.0
	 *  - 6400 -> 6.4.0.0
	 *  - 7000 -> 7.0.0.0
	 *
	 * @param float $number  The version number to convert into a string.
	 * @param bool  $isApp   Handle minor version (default: false)
	 * @return string
	 */
	static function number2version($number, $isApp = false){

		$mynumber = "$number";
		$numbers = array();

		if($isApp){
			if($number < 1){
				if($number < 0.1){
					$mynumber = str_replace('.0', '.', $mynumber);
					$version = $mynumber;
				} else {
					$version = $mynumber;
				}
			} else {
				$intnumber = floor($number);
				$decimal = $number - $intnumber;
				$mynumber = "$intnumber";
				for($i = 0; $i < strlen($mynumber) - 1; $i++){
					if($i = 2 && isset($mynumber[3])){
						$numbers[] = $mynumber[2] . $numbers[] = $mynumber[3];
					} else {
						$numbers[] = $mynumber[$i];
					}
				}
				if($decimal != 0){
					$version = implode('.', $numbers) . $decimal;
				} else {
					$version = implode('.', $numbers);
				}
			}
		} else {
			if($number > 6999){
				$intnumber = floor($number);
				$decimal = $number - $intnumber;
				$mynumber = "$intnumber";
				for($i = 0; $i < strlen($mynumber) - 1; $i++){
					if($i = 2 && isset($mynumber[3])){
						$numbers[] = $mynumber[2] . $numbers[] = $mynumber[3];
					} else {
						$numbers[] = $mynumber[$i];
					}
				}
				if($decimal != 0){
					$version = implode('.', $numbers) . $decimal;
				} else {
					$version = implode('.', $numbers);
				}
			} else {
				for($i = 0; $i < 4; $i++){
					$numbers[] = $number[$i];
				}
				$version = implode('.', $numbers);
			}
		}
		return $version;
	}

	/**
	 * This function prints recursively any array or object.
	 *
	 * @param *       $val   The variable to print
 	 * @param boolean $html  Whether to apply oldHtmlspecialchars (default: true)
	 * @param boolean $useTA Whether output is formated as textarea (dfault: false)
	 * @return void
	 */
	static function p_r($val, $html = true, $useTA = false){
		return p_r($val,$html,$useTA);
	}

}
