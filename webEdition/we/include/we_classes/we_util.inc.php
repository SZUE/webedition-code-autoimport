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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * Util Functions
 *
 * all functions in this class are static! Please use it in static form:
 *    we_util::function_name();
 *
 *
 * @static
 */
abstract class we_util{

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
		$match = array();
		if(preg_match('|([0-9]*\.?[0-9]*),([0-9]*)|', $number, $match)){ // deutsche schreibweise
			return floatval(str_replace('.', '', $match[1]) . '.' . $match[2]);
		} else if(preg_match('|([0-9]*)\.([0-9]*)|', $number)){ // engl schreibweise
			return floatval($number);
		} else {
			return floatval(str_replace(array(',', '.'), '', $number));
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
		return str_replace(array("\n\r", "\r\n", "\r"), "\n", $string);
	}

	/**
	 * Removes from string all newlines and converts all <br> to newlines
	 * Returns the converted String.
	 *
	 * @static
	 * @access public
	 *
	 * @param mixed number
	 */
	static function br2nl($string){
		$string = str_replace(array("\n", "\r"), '', $string);
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
		return html_entity_decode($text, ENT_COMPAT, (isset($GLOBALS['CHARSET']) && $GLOBALS['CHARSET'] ? $GLOBALS['CHARSET'] : DEFAULT_CHARSET));
	}

	/**
	 * returns array of directory IDs of all directories which are located inside $folderID (recursive)
	 *
	 * @return array
	 * @param int $folderID
	 */
	static function getFoldersInFolder($folderID, $table = FILE_TABLE, we_database_base $db = null){
		$outArray = array(
			$folderID
		);
		$db = ($db ? $db : new DB_WE());
		$db->query('SELECT ID FROM ' . $table . ' WHERE ParentID=' . intval($folderID) . ' AND IsFolder=1');
		$new = array();
		while($db->next_record()){
			$new[] = $db->f('ID');
		}
		foreach($new as $cur){
			$tmpArray = self::getFoldersInFolder($cur, $table, $db);
			$outArray = array_merge($outArray, $tmpArray);
		}
		return $outArray;
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
	public static function number2System($value, $chars = array(), $str = ''){

		if(!(is_array($chars) && count($chars) > 1)){ //	in case of error take default-array
			$chars = array('^', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		}
		$base = count($chars);

//	get some information about the numbers:
		$_rest = $value % $base;
		$_result = ($value - $_rest) / $base;

//	1. Deal with the rest
		$str = $chars[$_rest] . $str;

//	2. Deal with remaining result
		return ($_result > 0 ? self::number2System($_result, $chars, $str) : $str);
	}

	static function getCurlHttp($server, $path, $files = array(), $header = false, $timeout = 0){
		$_response = array(
			'data' => '', // data if successful
			'status' => 0, // 0=ok otherwise error
			'error' => '' // error string
		);
		$parsedurl = parse_url($server);
		$protocol = (isset($parsedurl['scheme']) ?
				$parsedurl['scheme'] . '://' :
				'http://');

		$port = (isset($parsedurl['port']) ? ':' . $parsedurl['port'] : '');
		$_pathA = explode('?', $path);
		$_url = $protocol . $parsedurl['host'] . $port . $_pathA[0];
		if(isset($_pathA[1]) && strlen($_url . $_pathA[1]) < 2000){
//it is safe to have uri's lower than 2k chars - so no need to do a post which servers (e.g. twitter) do not accept.
			$_url.='?' . $_pathA[1];
			unset($_pathA[1]);
		}
		$_params = array();

		$_session = curl_init();
		curl_setopt($_session, CURLOPT_URL, $_url);
		curl_setopt($_session, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($_session, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($_session, CURLOPT_MAXREDIRS, 5);

		if($timeout){
			curl_setopt($_session, CURLOPT_CONNECTTIMEOUT, $timeout);
		}

		/* 	if($username != ''){
		  curl_setopt($_session, CURLOPT_USERPWD, $username . ':' . $password);
		  } */

		if(isset($_pathA[1]) && $_pathA[1] != ''){
			$_url_param = explode('&', $_pathA[1]);
			foreach($_url_param as $cur){
				$_param_split = explode('=', $cur);
				$_params[$_param_split[0]] = isset($_param_split[1]) ? $_param_split[1] : '';
			}
		}

		if(!empty($files)){
			foreach($files as $k => $v){
				$_params[$k] = '@' . $v;
			}
		}

		if(!empty($_params)){
			curl_setopt($_session, CURLOPT_POST, 1);
			curl_setopt($_session, CURLOPT_POSTFIELDS, $_params);
		}

		if($header){
			curl_setopt($_session, CURLOPT_HEADER, 1);
		}

		if(defined('WE_PROXYHOST') && WE_PROXYHOST != ''){

			$_proxyhost = defined('WE_PROXYHOST') ? WE_PROXYHOST : '';
			$_proxyport = (defined('WE_PROXYPORT') && WE_PROXYPORT) ? WE_PROXYPORT : '80';
			$_proxy_user = defined('WE_PROXYUSER') ? WE_PROXYUSER : '';
			$_proxy_pass = defined('WE_PROXYPASSWORD') ? WE_PROXYPASSWORD : '';

			if($_proxyhost != ''){
				curl_setopt($_session, CURLOPT_PROXY, $_proxyhost . ':' . $_proxyport);
				if($_proxy_user != ''){
					curl_setopt($_session, CURLOPT_PROXYUSERPWD, $_proxy_user . ':' . $_proxy_pass);
				}
				curl_setopt($_session, CURLOPT_SSL_VERIFYPEER, FALSE);
			}
		}

		$_data = curl_exec($_session);

		if(curl_errno($_session)){
			$_response['status'] = 1;
			$_response['error'] = curl_error($_session);
			return false;
		} else {
			$_response['status'] = 0;
			$_response['data'] = $_data;
			curl_close($_session);
		}

		return $_response;
	}

}
