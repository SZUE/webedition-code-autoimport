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
 * Util Functions
 *
 * all functions in this class are static! Please use it in static form:
 *    we_base_util::function_name();
 *
 *
 * @static
 */
abstract class we_base_util{
	const MIME_BY_HEAD_THEN_EXTENSION = 0;
	const MIME_BY_EXTENSION = 1;
	const MIME_BY_HEAD = 2;
	const MIME_BY_DATA = 3;

	//FIXME: add more extensions
	//FIXME: change this to $finfo = finfo_open(FILEINFO_MIME_TYPE); 		  $mime = finfo_file($finfo, $filepath);		  finfo_close($finfo);
	//NOTICE: WE contenttypes differ strongly from the following mime types!
	private static $mimetypes = [
		'hqx' => 'application/mac-binhex40',
		'cpt' => 'application/mac-compactpro',
		'doc' => 'application/msword',
		'bin' => 'application/macbinary',
		'dms' => 'application/octet-stream',
		'lha' => 'application/octet-stream',
		'lzh' => 'application/octet-stream',
		'exe' => 'application/octet-stream',
		'class' => 'application/octet-stream',
		'psd' => 'application/octet-stream',
		'so' => 'application/octet-stream',
		'sea' => 'application/octet-stream',
		'dll' => 'application/octet-stream',
		'oda' => 'application/oda',
		'pdf' => 'application/pdf',
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',
		'smi' => 'application/smil',
		'smil' => 'application/smil',
		'mif' => 'application/vnd.mif',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',
		'wbxml' => 'application/vnd.wap.wbxml',
		'wmlc' => 'application/vnd.wap.wmlc',
		'dcr' => 'application/x-director',
		'dir' => 'application/x-director',
		'dxr' => 'application/x-director',
		'dvi' => 'application/x-dvi',
		'gtar' => 'application/x-gtar',
		'php' => 'application/x-httpd-php', //in WE this is text/html?
		'php4' => 'application/x-httpd-php',
		'php3' => 'application/x-httpd-php',
		'phtml' => 'application/x-httpd-php',
		'phps' => 'application/x-httpd-php-source',
		'js' => 'application/x-javascript',
		'swf' => 'application/x-shockwave-flash',
		'sit' => 'application/x-stuffit',
		'tar' => 'application/x-tar',
		'tgz' => 'application/x-tar',
		'xhtml' => 'application/xhtml+xml',
		'xht' => 'application/xhtml+xml',
		'zip' => 'application/zip',
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'mpga' => 'audio/mpeg',
		'mp2' => 'audio/mpeg',
		'mp3' => 'audio/mpeg',
		'aif' => 'audio/x-aiff',
		'aiff' => 'audio/x-aiff',
		'aifc' => 'audio/x-aiff',
		'ram' => 'audio/x-pn-realaudio',
		'rm' => 'audio/x-pn-realaudio',
		'rpm' => 'audio/x-pn-realaudio-plugin',
		'ra' => 'audio/x-realaudio',
		'rv' => 'video/vnd.rn-realvideo',
		'wav' => 'audio/x-wav',
		'bmp' => 'image/bmp',
		'gif' => 'image/gif',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'jpe' => 'image/jpeg',
		'png' => 'image/png',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'css' => 'text/css',
		'html' => 'text/html',
		'htm' => 'text/html',
		'shtml' => 'text/html',
		'txt' => 'text/plain',
		'text' => 'text/plain',
		'log' => 'text/plain',
		'rtx' => 'text/richtext',
		'rtf' => 'text/rtf',
		'xml' => 'text/xml',
		'xsl' => 'text/xml',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpe' => 'video/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',
		'avi' => 'video/x-msvideo',
		'movie' => 'video/x-sgi-movie',
		'doc' => 'application/msword',
		'word' => 'application/msword',
		'xl' => 'application/excel',
		'eml' => 'message/rfc822',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'shtm' => 'text/html',
		'ogg' => ['application/ogg', 'video/ogg', 'audio/ogg'],
		'mp4' => 'video/mp4',
		'm4v' => 'video/mp4',
		'mp3' => 'audio/mp3',
		'wav' => 'audio/wav'
	 ];

	/**
	 * Formates a number with a country specific format into computer readable format.
	 * Returns the formated number.
	 *
	 * @static
	 * @access public
	 *
	 * @param mixed number
	 */
	static function std_numberformat($number){
		if(strpos($number, 'E')){ //  when number is too big, it is shown with E+xx
			$number = number_format($number, 2, '.', '');
		}
		$match = [];
		if(preg_match('|([0-9]*\.?[0-9]*),([0-9]*)|', $number, $match)){ // deutsche schreibweise
			return floatval(str_replace('.', '', $match[1]) . '.' . $match[2]);
		}
		if(preg_match('|([0-9]*)\.([0-9]*)|', $number)){ // engl schreibweise
			return floatval($number);
		}
		return floatval(str_replace([',', '.'], '', $number));
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
	static function formatNumber($number, $format = '', $precision = 2){
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
	 * Converts all windows and mac newlines from string to unix newlines
	 * Returns the converted String.
	 *
	 * @static
	 * @access public
	 *
	 * @param mixed number
	 */
	static function cleanNewLine($string){
		return str_replace(["\n\r", "\r\n", "\r"], "\n", $string);
	}

	/**
	 * Removes from string all newlines and converts all <br/> to newlines
	 * Returns the converted String.
	 *
	 * @static
	 * @access public
	 *
	 * @param mixed number
	 */
	static function br2nl($string){
		$string = str_replace(["\n", "\r"], '', $string);
		return preg_replace('|<br ?/?>|i', "\n", $string);
	}

	static function rmPhp($in){
		$out = '';
		$starttag = strpos($in, '<?');
		if($starttag === false){
			return $in;
		}
		$lastStart = 0;
		while(!($starttag === false)){
			$endtag = strpos($in, '?>', $starttag);
			$out .= substr($in, $lastStart, ($starttag - $lastStart));
			$lastStart = $endtag + 2;
			$starttag = strpos($in, '<?', $lastStart);
		}
		if($lastStart < strlen($in)){
			$out .= substr($in, $lastStart, (strlen($in) - $lastStart));
		}
		return $out;
	}

	static function getGlobalPath(){
		return (isset($GLOBALS['WE_MAIN_DOC']) && isset($GLOBALS['WE_MAIN_DOC']->Path) ? $GLOBALS['WE_MAIN_DOC']->Path : '');
	}

	static function html2uml($text){
		return html_entity_decode($text, ENT_COMPAT, (!empty($GLOBALS['CHARSET']) ? $GLOBALS['CHARSET'] : DEFAULT_CHARSET));
	}

	/**
	 * Converts a given number in a via array specified system.
	 * as default a number is converted in the matching chars 0->^,1->a,2->b, ...
	 * other systems can simply set via the parameter $chars for example -> array(0,1)
	 * for bin-system
	 *
	 * @return string
	 * @param int $value
	 * @param array[optional] $chars
	 * @param string[optional] $str
	 */
	public static function number2System($value, $chars = [], $str = ''){

		if(!(is_array($chars) && count($chars) > 1)){ //	in case of error take default-array
			$chars = ['^', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
		}
		$base = count($chars);

//	get some information about the numbers:
		$rest = $value % $base;
		$result = ($value - $rest) / $base;

//	1. Deal with the rest
		$str = $chars[$rest] . $str;

//	2. Deal with remaining result
		return ($result > 0 ? self::number2System($result, $chars, $str) : $str);
	}

	static function getCurlHttp($server, $path = '', $files = [], $header = false, $timeout = 0){
		$response = [
			'data' => '', // data if successful
			'status' => 0, // 0=ok otherwise error
			'error' => '' // error string
			];
		$parsedurl = parse_url($server);
		$protocol = (isset($parsedurl['scheme']) ?
				$parsedurl['scheme'] . '://' :
				'http://');

		$port = (isset($parsedurl['port']) ? ':' . $parsedurl['port'] : '');
		$pathA = explode('?', $path);
		$url = $protocol . $parsedurl['host'] . $port . $pathA[0];
		if(isset($pathA[1]) && strlen($url . $pathA[1]) < 2000){
//it is safe to have uri's lower than 2k chars - so no need to do a post which servers (e.g. twitter) do not accept.
			$url.='?' . $pathA[1];
			unset($pathA[1]);
		}
		$params = [];

		$session = curl_init();
		curl_setopt($session, CURLOPT_URL, $url);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($session, CURLOPT_MAXREDIRS, 5);

		if($timeout){
			curl_setopt($session, CURLOPT_CONNECTTIMEOUT, $timeout);
		}

		/* 	if($username != ''){
		  curl_setopt($session, CURLOPT_USERPWD, $username . ':' . $password);
		  } */

		if(!empty($pathA[1])){
			$url_param = explode('&', $pathA[1]);
			foreach($url_param as $cur){
				$param_split = explode('=', $cur);
				$params[$param_split[0]] = isset($param_split[1]) ? $param_split[1] : '';
			}
		}

		if($files){
			foreach($files as $k => $v){
				$params[$k] = '@' . $v;
			}
		}

		if($params){
			curl_setopt($session, CURLOPT_POST, 1);
			curl_setopt($session, CURLOPT_POSTFIELDS, $params);
		}

		if($header){
			curl_setopt($session, CURLOPT_HEADER, 1);
		}

		if(defined('WE_PROXYHOST') && WE_PROXYHOST != ''){

			$proxyhost = defined('WE_PROXYHOST') ? WE_PROXYHOST : '';
			$proxyport = (defined('WE_PROXYPORT') && WE_PROXYPORT) ? WE_PROXYPORT : '80';
			$proxy_user = defined('WE_PROXYUSER') ? WE_PROXYUSER : '';
			$proxy_pass = defined('WE_PROXYPASSWORD') ? WE_PROXYPASSWORD : '';

			if($proxyhost != ''){
				curl_setopt($session, CURLOPT_PROXY, $proxyhost . ':' . $proxyport);
				if($proxy_user != ''){
					curl_setopt($session, CURLOPT_PROXYUSERPWD, $proxy_user . ':' . $proxy_pass);
				}
				curl_setopt($session, CURLOPT_SSL_VERIFYPEER, FALSE);
			}
		}

		$data = curl_exec($session);

		if(curl_errno($seson)){
			$info = curl_getinfo($session);
			$_response['status'] = empty($info['http_code']) ? 1 : $info['http_code'];
			$_response['error'] = curl_error($session);
			return false;
		}
		$response['status'] = 0;
		$response['data'] = $data;
		curl_close($session);


		return $response;
	}

	public static function convertDateInRequest(array &$req, $asInt = false){
		$dates = $regs = [];

		foreach($req as $key => $value){
			if(preg_match('/^we_date_([a-zA-Z0-9_]+)_(day|month|year|minute|hour)$/', $key, $regs)){
				$dates[$regs[1]][$regs[2]] = $value;
				unset($req[$key]);
			}
		}
		foreach($dates as $k => $vv){
			if($vv['year'] == 0){
				$vv['month'] = $vv['day'] = $vv['hour'] = $vv['minute'] = 0;
			}
			$req[$k] = ($asInt ?
					mktime($vv['hour'], $vv['minute'], 0, $vv['month'], $vv['day'], $vv['year']) :
					sprintf('%04d-%02d-%02d %02d:%02d:00', $vv['year'], $vv['month'], $vv['day'], $vv['hour'], $vv['minute']));
		}
	}

	/**
	 * This function works in very same way as the standard array_splice function
	 * except the second parametar is the array index and not just offset
	 * The functions modifies the array that has been passed by reference as the first function parametar
	 *
	 * @param          array                                  $a
	 * @param          interger                                $start
	 * @param          integer                                 $len
	 *
	 *
	 * @return         none
	  @deprecated
	 *
	 *  */
	public static function new_array_splice(&$a, $start, $len = 1){
		$ks = array_keys($a);
		$k = array_search($start, $ks);
		if($k !== false){
			$ks = array_splice($ks, $k, $len);
			foreach($ks as $k){
				unset($a[$k]);
			}
		}
	}

	/**
	 * get the mime type of a given file
	 * @param string $ext the extension of the file
	 * @param string $filepath path of the file
	 * @param enum $method the method how to determine the type
	 * @param bool $handleCompressed if true compressed data type is not returned as "application/compressed", so the real type should be determined
	 * @return boolean
	 */
	public static function getMimeType($ext, $filepath = '', $method = self::MIME_BY_HEAD_THEN_EXTENSION, $handleCompressed = false){
		$isCompressed = ($filepath && $handleCompressed ? we_base_file::isCompressed($filepath) : false);
		switch($filepath ? $method : self::MIME_BY_EXTENSION){
			case self::MIME_BY_DATA:
				if(function_exists('finfo_open')){
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mime = finfo_buffer($finfo, we_base_file::loadPart($filepath, 0, 8192, $isCompressed));
					finfo_close($finfo);
					if($mime){
						return $mime;
					}
				}
				break;
			case self::MIME_BY_HEAD:
			case self::MIME_BY_HEAD_THEN_EXTENSION:
				if(function_exists('finfo_open')){
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mime = finfo_buffer($finfo, we_base_file::loadPart($filepath, 0, 8192, $isCompressed));
					finfo_close($finfo);
					if($mime || $method == self::MIME_BY_HEAD){
						return $mime ? : false;
					}
				}
				if(!($handleCompressed && $isCompressed) && function_exists('mime_content_type')){
					$mime = mime_content_type($filepath);
					if($mime || $method == self::MIME_BY_HEAD){
						return $mime ? : false;
					}
				}
				if($method == self::MIME_BY_HEAD){
					return false;
				}
				break;
			case self::MIME_BY_EXTENSION:
				break;
			default:
				//false means: no info about MIME type can be determined
				return false;
		}
		//self::MIME_BY_EXTENSION
		return (!isset(self::$mimetypes[strtolower($ext)])) ?
			'application/octet-stream' :
			(is_array(self::$mimetypes[strtolower($ext)]) ?
				current(self::$mimetypes[strtolower($ext)]) :
				self::$mimetypes[strtolower($ext)]);
	}

	public static function extension2mime($ext){
		return isset(self::$mimetypes[strtolower($ext)]) ?
			(is_array(self::$mimetypes[strtolower($ext)]) ?
				current(self::$mimetypes[strtolower($ext)]) :
				self::$mimetypes[strtolower($ext)]) :
			false;
	}

	public static function mime2extensions($mime, $retCsv = false){
		$mime = str_replace('/*', '/', trim($mime));
		$extensions = [];
		foreach(self::$mimetypes as $k => $v){
			if(is_array($v)){
				foreach($v as $cur){
					if(strpos($cur, $mime) === 0){
						$extensions[] = $k;
					}
				}
			} elseif(strpos($v, $mime) === 0){
				$extensions[] = $k;
			}
		}

		return $retCsv ? implode(',', $extensions) : $extensions;
	}

	public static function mimegroup2mimes($mimegroup, $retCsv = false){
		$mimegroup = str_replace('/*', '/', trim($mimegroup));
		$mimes = [];
		foreach(self::$mimetypes as $v){
			if(is_array($v)){
				foreach($v as $cur){
					if(strpos($cur, $mimegroup) === 0 && !in_array($cur, $mimes)){
						$mimes[] = $cur;
					}
				}
			} elseif(strpos($v, $mimegroup) === 0 && !in_array($v, $mimes)){
				$mimes[] = $v;
			}
		}

		return $retCsv ? implode(',', $mimes) : $mimes;
	}

	public static function isExtensionMime($ext, $mime){
		$mime = str_replace('/*', '/', trim($mime));
		$check = self::$mimetypes[$ext];
		if(!is_array($check)){
			return strpos($check, $mime) !== false;
		}
		foreach($check as $cur){
			if(strpos($cur, $mime) !== false){
				return true;
			}
		}
		return false;
	}

	static function convertUnits($string, $base = 16){
		//FIXME: what to do with % ??
		$regs = [];
		if(!preg_match('/(\d+\.?\d*) ?(em|ex|pt|px|in|mm|cm|pc|ch|rem|vw|vh|vmin|vmax|%)?/', $string, $regs)){
			$regs[1] = intval($string);
			$regs[2] = 'px';
		}

		switch(isset($regs[2]) ? $regs[2] : 'px'){
			case 'ch':
				$regs[1]*=1.2;
			case 'ex':
				$regs[1]*=2;
			case 'rem':
			case 'em':
				return $regs[1] * $base;
			case 'pt':
				return round($regs[1] * 96 / 72);
			case 'pc':
				return round($regs[1] * 96 / 6);
			case 'in':
				return round($regs[1] * 96);
			case 'mm':
				return round($regs[1] * 96 / 254);
			case 'cm':
				return round($regs[1] * 96 / 2.54);
			case '%':
				//don't convert %
				return $string;
			default:
			case 'px':
				return $regs[1];
		}
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

	public static function getPercent($total, $value, $precision = 0){
		$result = ($total ? round(($value * 100) / $total, $precision) : 0);
		return self::formatNumber($result, strtolower($GLOBALS['WE_LANGUAGE']));
	}

}
