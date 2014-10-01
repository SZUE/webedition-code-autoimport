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

	const TRANSACTION = 'transaction';
	const INTLIST = 'intList';
	/* converts an csv of ints to an array */
	const INTLISTA = 'intListA';
	const CMD = 'cmd';
	const UNIT = 'unit';
	const INT = 'int';
	const FLOAT = 'float';
	const BOOL = 'bool';

	/**
	 * @internal
	 */
	const TOGGLE = 'toggle';
	const TABLE = 'table';
	const FILE = 'file';
	const FILELIST = 'filelist';
	const FILELISTA = 'filelista';
	const URL = 'url';
	const STRING = 'string';
	const HTML = 'html';
	const EMAIL = 'email';
//only temporary
	const STRINGC = 'stringC';
	const RAW_CHECKED = 'rawC';
//remove these types!!!
	const JS = 'js';
	const RAW = 'raw';
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
				$var = array_map('intval', explode(',', trim($var, ',')));
				return;
			case self::INTLIST:
				$var = implode(',', array_map('intval', explode(',', trim($var, ','))));
				return;
			case self::SERIALIZED:
				$var = unserialize($var);
				return;
			case self::SERIALIZED_KEEP:
				$var = serialize(unserialize($var));
				return;
			case self::CMD:
				$var = strpos($var, 'WECMDENC_') !== false ?
					base64_decode(urldecode(substr($var, 9))) :
					$var;
				return;
			case self::UNIT:
				$regs = array(); //FIMXE: check for %d[em,ex,pt,%...]?
				$var = (preg_match('/(\d+) ?(em|ex|pt|px|%)?/', $var, $regs) ? $regs[1] . (isset($regs[2]) ? $regs[2] : '') : '' );
				return;
			case self::INT:
				$var = ($var === '' ? $default : intval($var));
				return;
			case self::FLOAT:
				//FIXME: check for country dependencies (eg. 1.3333,22)
				$var = floatval(str_replace(',', '.', $var));
				return;
			case self::BOOL:
				if(is_bool($var)){
					return $var;
				}
				switch($var){
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

			case self::TOGGLE: //FIXME: temporary type => whenever possible use 'bool'
				$var = $var == 'on' || $var == 'off' || $var == '1' || $var == '0' ? $var : (bool) $var;
				return;
			case self::TABLE: //FIXME: this doesn't hold for OBJECT_X_TABLE - make sure we don't use them in requests
				$var = $var && in_array($var, self::$allTables) ? $var : $default;
				return;
			case self::EMAIL://removes mailto:
				$regs = array();
				if(preg_match('-("[\S ]+"|\S+) <(\S+@\S+)>-', $var, $regs)){ //mail formats "yy" <...@...>, =..... <...@...>
					if(filter_var($regs[2], FILTER_VALIDATE_EMAIL) !== false){
						return;
					}
				}//if format didn't match, filter the whole var as one address

				$var = filter_var(str_replace(we_base_link::TYPE_MAIL_PREFIX, '', $var), FILTER_SANITIZE_EMAIL);
				return;
			case self::FILELISTA:
			case self::FILELIST:
				$var = explode(',', trim(strtr($var, array(
					'../' => '',
					'//' => ''
						)), ','));
				foreach($var as &$cur){
					$cur = filter_var($cur, FILTER_SANITIZE_URL);
					if(strpos($cur, rtrim(WEBEDITION_DIR, '/')) === 0){//file-selector has propably access
						if(!(strstr($cur, SITE_DIR) || strstr($cur, TEMP_DIR))){//allow site/tmp dir
							$cur = isset($GLOBALS['supportDebugging']) ? $cur : '-1';
						}
					}
				}
				$var = $type == self::FILELIST ? implode(',', $var) : $var;
				return;
			case self::FILE:
				$var = strtr(filter_var($var, FILTER_SANITIZE_URL), array(
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
				$var = filter_var(urldecode($var), FILTER_SANITIZE_URL);
				return;
			case self::STRINGC:
			case self::STRING://strips tags
				$var = filter_var($var, FILTER_SANITIZE_STRING);
				return;
			case self::HTML:
				$var = filter_var($var, FILTER_SANITIZE_SPECIAL_CHARS);
				return;
			default:
				t_e('unknown filter type ' . $type);
			case self::JS://for information!
			case self::RAW:
			case self::RAW_CHECKED:
				//do nothing - used as placeholder for all types not yet known
				return;
		}
		$var = $default;
	}

	function we_defineTables(array $tables){
		if(!isset($GLOBALS['we']['allTables'])){
			$GLOBALS['we']['allTables'] = array();
		}
		foreach($tables as $tab => $name){
			define($tab, TBL_PREFIX . $name);
			$GLOBALS['we']['allTables'][$tab] = TBL_PREFIX . $name;
		}
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

		$var = $_REQUEST;
		$args = func_get_args();
		/* fixme temporary until 6.3.9 release */
		if(func_num_args() == 4 && $args[3] === null){
			unset($args[3]);
		}
		/* end fix */
		unset($args[0], $args[2]);
		if(false && isset($_SESSION['user']['isWeSession']) && $_SESSION['user']['isWeSession'] && WE_VERSION_SUPP){
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

			switch($type){
				case self::URL:
					$oldVar = urldecode($var);
					$cmp = '' . $var;
					break;
				case self::CMD://this must change&is ok!
				case self::RAW_CHECKED:
				case self::STRINGC:
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
							case 'false':
								$cmp = 0;
								break 2;
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
					if(WE_VERSION_SUPP && $var){//show this only during development
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
			}
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

	/**
	 * @internal
	 * @param type $str
	 * @return type
	 */
	public static function encCmd($str){
		return ($str ? 'WECMDENC_' . urlencode(base64_encode($str)) : '');
	}

}
