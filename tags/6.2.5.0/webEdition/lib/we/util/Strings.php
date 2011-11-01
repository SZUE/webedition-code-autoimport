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
 * @package    we_util
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * Utility class for string manipulation and creation
 * 
 * @category   we
 * @package    we_util
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_util_Strings
{

	/**
	 * Returns an unique ID
	 *
	 * @param integer $length  length of generated ID
	 * @return string
	 */
	static function createUniqueId($length = 32)
	{
		return substr('we'.md5(uniqid(time())), 0, $length);
	}

	/**
	 * Returns cvs of array values
	 *
	 * @param string $arr
	 * @param string $prePostKomma
	 * @param string $sep
	 * @return string
	 */
	static function makeCSVFromArray($arr, $prePostKomma = false, $sep = ",")
	{
		if (!sizeof($arr))
			return "";
		
		$replaceKomma = (count($arr) > 1) || ($prePostKomma == true);
		
		if ($replaceKomma) {
			for ($i = 0; $i < sizeof($arr); $i++) {
				$arr[$i] = str_replace($sep, "###komma###", $arr[$i]);
			}
		}
		$out = implode($sep, $arr);
		if ($prePostKomma) {
			$out = $sep . $out . $sep;
		}
		if ($replaceKomma) {
			$out = str_replace("###komma###", "\\$sep", $out);
		}
		return $out;
	}

	/**
	 * Returns an array of cvs values
	 *
	 * @param string $csv
	 * @return array
	 */
	static function makeArrayFromCSV($csv)
	{
		
		$csv = str_replace("\\,", "###komma###", $csv);
		
		if (substr($csv, 0, 1) == ",") {
			$csv = substr($csv, 1);
		}
		if (substr($csv, -1) == ",") {
			$csv = substr($csv, 0, strlen($csv) - 1);
		}
		if ($csv == "" && $csv != "0") {
			$foo = array();
		} else {
			$foo = explode(",", $csv);
			for ($i = 0; $i < sizeof($foo); $i++) {
				$foo[$i] = str_replace("###komma###", ",", $foo[$i]);
			}
		}
		return $foo;
	}

	/**
	 * Returns a quoted string
	 *
	 * @param string $text
	 * @param boolean $quoteForSingle
	 * @return string
	 */
	static function quoteForJSString($text, $quoteForSingle = true)
	{
		if ($quoteForSingle) {
			return str_replace('\'', '\\\'', str_replace('\\', '\\\\', $text));
		} else {
			return str_replace("\"", "\\\"", str_replace("\\", "\\\\", $text));
		}
	}

	/**
	 * Returns a shortened string
	 *
	 * @param string $path
	 * @param integer $len
	 * @return string
	 */
	static function shortenPath($path, $len)
	{
		if (strlen($path) <= $len || strlen($path) < 10)
			return $path;
		$l = ($len / 2) - 2;
		return substr($path, 0, $l) . "...." . substr($path, $l * -1);
	}
	
	/**
	 * Returns a formatted string
	 *
	 * @param string vale
	 * @param string format 
	 * @return string
	 */
	static function formatnumber($value, $format)
	{ 
		switch ($format) {
			case 'german': return number_format($value, 2, ",", ".");
			break;
			case 'french': return number_format($value, 2, ",", " ");
			break;
			case 'english': return number_format($value, 2, ".", "");
			break;
			case 'swiss' : return number_format($value, 2, ".", "'");
			break;
		
		}
	}

	/**
	 * splits a version (string) to a number. 
	 *
	 * @param string $version
	 * @param bool $isApp
	 * @return float
	 */
	static function version2number($version, $isApp = false) 
	{
		if ($isApp){
			if (substr($version,0,1)=="0"){
				if (strlen($version)==3){
					$numberStr = '0.0'.substr($version,2,1);
					$number = (float) $numberStr;
				} else {
					$numberStr = '0.'.substr($version,2,2);
					$number = (float) $numberStr;
				}
				
			} else {
				$count=2;
				$numberStr = str_replace('.','',$version,$count);
				$number = (float) $numberStr;
			}
		} else {
			$count=3;
			if (substr($version,0,1)=="6"){
				$numberStr = str_replace('.', '', $version,$count);
				$number = (float) $numberStr;
			} else {
				$numberStr = str_replace('.', '', $version,$count);
				$number = (float) $numberStr;
			}
		}	
		return $number;

	}


	/**
	 * this function converts a versionnumber (integer/float) to the number as string
	 * each number separated with ".". Parameter isApp determines if the versionnumber might by float to allow for 0.something.
	 *
	 * @param float $number
	 * @param bool $isApp
	 * @return string
	 */
	static function number2version($number, $isApp=false) {

		$mynumber = "$number";
		$numbers = array();
		
		if ($isApp){
			if ($number < 1){
				if ($number < 0.1){
					$mynumber = str_replace('.0','.',$mynumber);
					$version = $mynumber;
				} else {
					$version = $mynumber;
				}		
			} else {
				$intnumber = floor($number);
				$decimal = $number - $intnumber;
				$mynumber = "$intnumber";
				for ($i=0;$i<strlen($mynumber)-1;$i++) {
					if ($i=2 && isset($mynumber[3])){
						$numbers[] = $mynumber[2].$numbers[] = $mynumber[3];
					} else {
						$numbers[] = $mynumber[$i];
					}
				}
				if ($decimal !=0) {
					$version = implode('.', $numbers).$decimal;
				} else {
					$version = implode('.', $numbers);
				}
			}
		} else {
			if ($number > 6999){
				$intnumber = floor($number);
				$decimal = $number - $intnumber;
				$mynumber = "$intnumber";
				for ($i=0;$i<strlen($mynumber)-1;$i++) {
					if ($i=2 && isset($mynumber[3])){
						$numbers[] = $mynumber[2].$numbers[] = $mynumber[3];
					} else {
						$numbers[] = $mynumber[$i];
					}
				}
				if ($decimal !=0) {
					$version = implode('.', $numbers).$decimal;
				} else {
					$version = implode('.', $numbers);
				}		
			} else {
				for ($i=0;$i<4;$i++) {
					$numbers[] = $number[$i];
				}
				$version = implode('.', $numbers);
			}		
		}
		return $version;
	}

	/**
	 * this function prints recursively any array or object 
	 *
	 * @param  $val
	 * @return void
	 */
	
	static function p_r($val,$where=false)
	{
		if ($where){
			$out = "<pre>";
			$out .= print_r($val,$where);
			$out .= "</pre>";
			return $out;
		} else {
			print "<pre>";
			print_r($val,$where);
			print "</pre>";
		}
	}

}
