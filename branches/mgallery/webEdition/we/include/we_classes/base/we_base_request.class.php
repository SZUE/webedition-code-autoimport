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
class we_base_request{

	private static $allTables = array();

	const NOT_VALID = '__NOT_VALID__'; // to be used as default just for indicating that is IS an default

	/* converts an csv of ints to an array */
	const INTLISTA = 'intListA';
	const INT = 'int';
	const FLOAT = 'float';
	const BOOL = 'bool';
	const RAW = 'raw';
	const URL = 'url';
	const EMAIL = 'email'; //add email_list
	const STRING = 'string';
	const HTML = 'html';

	/**
	 * @internal
	 */
	const TRANSACTION = 'transaction';
	const INTLIST = 'intList';
	const CMD = 'cmd';
	const UNIT = 'unit';
	const TABLE = 'table';
	const FILE = 'file';
	const FILELIST = 'filelist';
	const FILELISTA = 'filelista';
	const STRING_LIST = 'stringL';
	const EMAILLIST = 'emailL';
	const EMAILLISTA = 'emailLA';
//only temporary
	const RAW_CHECKED = 'rawC';
	// the following types do not sanitize, so they will allow spaces,...
	const WEFILE = 'wefile';
	const WEFILELIST = 'wefilelist';
	const WEFILELISTA = 'wefilelista';
//remove these types!
	const JS = 'js';
	const SERIALIZED = 'serial';
	const SERIALIZED_KEEP = 'serialK';

	/** Helper for Filtering variables (callback of array_walk)
	 *
	 * @param mixed $var value
	 * @param string $key key used by array-walk - unused
	 * @param array $data array pair of type & default
	 * @return type
	 */
	private static function _weRequest(&$var, $key, array $data){
		if(is_array($var)){
			array_walk($var, 'we_base_request::_weRequest', $data);
			return;
		}

		list($type, $default) = $data;
		switch($type){
			case self::TRANSACTION:
				$var = (preg_match('|^([a-f0-9]){32}$|i', $var) ? $var : $default);
				return;
			case self::INTLISTA:
				$var = trim($var, ',');
				$var = $var || $var === '0' ? array_map('intval', explode(',', $var)) : $default;
				return;
			case self::INTLIST:
				$var = trim($var, ',');
				$var = $var || $var === '0' ? implode(',', array_map('intval', explode(',', $var))) : $default;
				return;
			case self::SERIALIZED:
				$var = we_unserialize($var);
				return;
			case self::SERIALIZED_KEEP:
				//$var = serialize(we_unserialize($var));
				return;
			case self::CMD:
				$var = strpos($var, 'WECMDENC_') !== false ?
						base64_decode(urldecode(substr($var, 9))) :
						$var;
				return;
			case self::UNIT:
				$regs = array(); //FIMXE: check for %d[em,ex,pt,%...]?
				$var = (preg_match('/(\d+\.?\d*) ?(em|ex|pt|px|in|mm|cm|pc|ch|rem|vw|vh|vmin|vmax|%)?/', $var, $regs) ? $regs[1] . (isset($regs[2]) ? $regs[2] : '') : '' );
				return;
			case self::INT:
				$var = ($var === '' ? $default : intval($var));
				return;
			case self::FLOAT:
				//FIXME: check for country dependencies (eg. 1.3333,22)
				$var = ($var === '' ? $default : floatval(str_replace(',', '.', $var)));
				return;
			case self::BOOL:
				if(is_bool($var)){
					return $var;
				}

				switch(is_string($var) ? strtolower(trim($var)) : $var){
					case '0':
					case 'off':
					case 'false':
						$var = false;
						return;
					case 'true':
					case 'on':
					case '1':
						$var = true;
						return;
					default:
						$var = (bool) $var;
						return;
				}

			case self::TABLE: //FIXME: this doesn't hold for OBJECT_X_TABLE - make sure we don't use them in requests
				$var = $var && in_array($var, self::$allTables) ? $var : $default;
				return;
			case self::EMAILLISTA:
			case self::EMAILLIST:
				//FIXME we need to improve this for "test, x" <a@b.de>, "test2, x" <b@d.de>
				//preg_match_all('/("[\S\s]+"\s*|[^,"<>@]*\s+|)<?([^@<>,]*)@([a-zA-Z\d.-]+)>?/', $mail, $regs,PREG_SET_ORDER);
				$mails = array_map('trim', explode(',', str_replace(we_base_link::TYPE_MAIL_PREFIX, '', $var)));
				$regs = array();
				foreach($mails as &$mail){
					if(!preg_match('-(["\'][\S\s]+["\']\s*|\S+\s*)<(\S+)@(\S+)>-', $mail, $regs)){ //mail formats "yy" <...@...>, =..... <...@...>
						//if format didn't match, filter the whole var as one address
						$regs = array_merge(array('', ''), explode('@', $mail, 2));
						if(!isset($regs[3])){
							$mail = '';
							continue;
						}
					}
					$host = (function_exists('idn_to_ascii') ? idn_to_ascii($regs[3]) : $regs[3]);
					$mail = (filter_var($regs[2] . '@' . $host, FILTER_VALIDATE_EMAIL) !== false ?
							$regs[1] . ($regs[1] ? '<' : '') . $regs[2] . '@' . $regs[3] . ($regs[1] ? '>' : '') :
							'');
				}//if format didn't match, filter the whole var as one address

				$mails = array_filter($mails);
				$var = ($type == self::EMAILLISTA ? $mails : implode(',', $mails));
				return;
			case self::EMAIL://removes mailto:
				$regs = array();
				$mail = trim(str_replace(we_base_link::TYPE_MAIL_PREFIX, '', $var));
				if(!preg_match('-(["\'][\S\s]+["\']\s*|\S+\s*)<(\S+)@(\S+)>-', $mail, $regs)){ //mail formats "yy" <...@...>, =..... <...@...>
					//if format didn't match, filter the whole var as one address
					$regs = array_merge(array('', ''), explode('@', $mail, 2));
					if(!isset($regs[3])){
						$mail = '';
						continue;
					}
				}
				t_e($regs);
				$host = (function_exists('idn_to_ascii') ? idn_to_ascii($regs[3]) : $regs[3]);

				$var = (filter_var($regs[2] . '@' . $host, FILTER_VALIDATE_EMAIL) !== false ?
						$regs[1] . ($regs[1] ? '<' : '') . $regs[2] . '@' . $regs[3] . ($regs[1] ? '>' : '') :
						'');
				return;
			case self::WEFILELIST:
			case self::WEFILELISTA:
			case self::FILELISTA:
			case self::FILELIST:
				$var = explode(',', trim(strtr($var, array(
					'../' => '',
					'//' => ''
								)), ','));
				foreach($var as &$cur){
					$cur = ($type == self::WEFILELIST || $type == self::WEFILELISTA ? $cur : filter_var($cur, FILTER_SANITIZE_URL));
					if($cur === rtrim(WEBEDITION_DIR, '/') || strpos($cur, WEBEDITION_DIR) === 0){//file-selector has propably access
						if(!(strstr($cur, SITE_DIR) || strstr($cur, TEMP_DIR))){//allow site/tmp dir
							$cur = isset($GLOBALS['supportDebugging']) ? $cur : '-1';
						}
					}
				}
				$var = ($type == self::FILELIST || $type == self::WEFILELIST ? implode(',', $var) : $var);
				return;
			case self::WEFILE:
			case self::FILE:
				$var = strtr(($type == self::FILE ? filter_var($var, FILTER_SANITIZE_URL) : $var), array(
					'../' => '',
					'//' => '/'
				));
				if(strpos($var, rtrim(WEBEDITION_DIR, '/')) === 0){//file-selector has propably access
					if(!(strstr($var, SITE_DIR) || strstr($var, TEMP_DIR))){//allow site/tmp dir
						$var = isset($GLOBALS['supportDebugging']) ? $var : '-1';
					}
				}
				return;
			case self::URL:
				if(preg_match('-(' . we_base_link::TYPE_INT_PREFIX . '|' . we_base_link::TYPE_MAIL_PREFIX . '|' . we_base_link::TYPE_OBJ_PREFIX . '|' . we_base_link::TYPE_THUMB_PREFIX . ')-', $var)){
					return;
				}
				$urls = parse_url(urldecode($var));
				if(!empty($urls['host'])){
					$urls['host'] = (function_exists('idn_to_ascii') ? idn_to_ascii($urls['host']) : $urls['host']);
				}
				$url = filter_var(self::unparse_url($urls), FILTER_SANITIZE_URL);
				$urls = parse_url($url);
				if(!empty($urls['host'])){
					$urls['host'] = (function_exists('idn_to_utf8') ? idn_to_utf8($urls['host']) : $urls['host']);
				}
				$var = self::unparse_url($urls);
				return;
			case self::STRING:
			case self::STRING://strips tags
				$var = filter_var($var, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
				return;
			case self::STRING_LIST:
				//in lists, we don't accept quotes
				$var = array_filter(array_map('trim', explode(',', filter_var($var, FILTER_SANITIZE_STRING))));
				return;
			case self::HTML:
				$var = filter_var(htmlspecialchars_decode($var), FILTER_SANITIZE_SPECIAL_CHARS);
				return;
			case self::JS://for information!
			case self::RAW:
			case self::RAW_CHECKED:
				//do nothing - used as placeholder for all types not yet known
				return;
			default:
				t_e('unknown filter type ' . $type);
		}
		$var = $default;
	}

	public static function filterVar($var, $varType, $default = ''){
		//FIXME: remove checker at release
		//$preVar = $var;
		self::_weRequest($var, '', array($varType, $default));
		/* 		if($varType != self::INTLIST && !is_bool($var) && !is_array($var) && $preVar != $var && $var != $default){
		  t_e('changed var/tag attribute', $preVar, $var);
		  } */
		return $var;
	}

	/**
	 * Filter an Requested variable
	 * Note: every parameter after default is an optional index
	 * @param string $type type to filter, see list in _weGetVar
	 * @param string $name name of variable in Request array
	 * @param mixed $default default value

	 * @return mixed default, if value not set, the filtered value else
	 */
	public static function _($type, $name, $default = false){
		if(!isset($_REQUEST)){
			return $default;
		}
		$var = $_REQUEST;
		$args = func_get_args();
		unset($args[0], $args[2]);
		if(false && !empty($_SESSION['user']['isWeSession']) && WE_VERSION_SUPP){
			$argname = implode('.', $args);
			//reduce duplicate requests on the same global scope
			static $requests = array();
			$requests[$name][$argname][] = getBacktrace(array('error_showDevice', 'error_handler', 'getBacktrace', 'display_error_message'));
			if(count($requests[$name][$argname]) > 1){
				t_e('rerequest ', $name, $args, $requests[$name][$argname]);
			}
		}
		foreach($args as $arg){
			if(is_string($var) || !is_array($var) || !isset($var[$arg])){
				return $default;
			}
			$var = $var[$arg];
		}

		if(is_array($var)){
			$oldVar = $var;
			array_walk($var, 'we_base_request::_weRequest', array($type, $default));
			if($oldVar != $var){

			}
		} else {
			$oldVar = $var;
			self::_weRequest($var, '', array($type, $default));
			/*
			  switch($type){
			  case self::URL:
			  $oldVar = urldecode($var);
			  $cmp = '' . $var;
			  break;
			  case self::CMD://this must change&is ok!
			  case self::RAW_CHECKED:
			  case self::STRING:
			  case self::STRING_LIST:
			  case self::INTLISTA:
			  //we didn't change anything.
			  return $var;
			  case self::INTLIST:
			  $oldVar = trim($var, ',');
			  $cmp = '' . $var;
			  break;
			  case self::INT:
			  if($oldVar === ''){//treat empty as 0
			  return $var;
			  }
			  $cmp = '' . $var;
			  break;
			  case self::FILELIST:
			  $cmp = '' . $var;
			  $oldVar = trim($oldVar, ',');
			  break;
			  case self::BOOL://bool is transfered as 0/1
			  if($oldVar === ''){//treat empty as 0
			  $oldVar = 0;
			  }
			  if(is_string($var)){
			  switch($var){
			  case 'off':
			  case 'false':
			  $cmp = 0;
			  break 2;
			  case 'on':
			  case 'true':
			  $cmp = 1;
			  break 2;
			  }
			  } elseif(is_bool($var)){
			  $cmp = $var;
			  break;
			  }
			  $cmp = '' . intval($var);
			  break;
			  case self::RAW:
			  case self::STRING:
			  case self::JS:
			  if(defined('WE_VERSION_SUPP') && WE_VERSION_SUPP && $var){//show this only during development
			  if($var == ('' . intval($oldVar))){
			  t_e('notice', 'variable could be int/bool?', $args, $var);
			  } elseif(str_replace(',', '.', $var) == ('' . floatval($oldVar))){
			  t_e('notice', 'variable could be float', $args, $var);
			  } elseif(strpos($var, '@')){
			  t_e('notice', 'variable could be mail', $args, $var);
			  } elseif(strpos($var, '://')){
			  t_e('notice', 'variable could be url', $args, $var);
			  } elseif(strpos($var, '/') === 0){
			  t_e('notice', 'variable could be file', $args, $var);
			  } elseif($type != self::JS && count(explode(',', $var)) > 2){
			  t_e('notice', 'variable could be list', $args, $var);
			  } elseif(strpos($var, 'a:') === 0 || strpos($var, 's:') === 0){
			  t_e('notice', 'variable could be serial', $args, $var);
			  } elseif(strpos($var, 'tbl') === 0){
			  t_e('notice', 'variable could be table', $args, $var);
			  }
			  }
			  //no break;
			  default:
			  $cmp = '' . $var;
			  }
			  if($oldVar != $cmp){

			  t_e('changed values', $type, $args, $oldVar, $var);
			  //don't break we
			  } */
		}
		return $var;
	}

	/**
	 * @internal
	 * @param array $tables
	 */
	public static function registerTables(array $tables){
		self::$allTables = array_merge(self::$allTables, $tables);
	}

	public static function getAllTables(){
		return self::$allTables;
	}

	/**
	 * @internal
	 * @param type $str
	 * @return type
	 */
	public static function encCmd($str){
		return ($str ? 'WECMDENC_' . urlencode(base64_encode($str)) : '');
	}

	private static function unparse_url($parsed_url){
		$scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
		$pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
		$pass = ($user || $pass) ? "$pass@" : '';
		$path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}

}
