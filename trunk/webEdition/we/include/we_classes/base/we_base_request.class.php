<?php

/**
 * webEdition CMS
 *
 * $Rev: 7705 $
 * $Author: mokraemer $
 * $Date: 2014-06-10 21:46:56 +0200 (Di, 10. Jun 2014) $
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
	const URL = 'url';
	const STRING = 'string';
	const HTML = 'html';
	const EMAIL = 'email';
	const JS = 'js';
	const RAW = 'raw';

	/**
	 * @internal
	 */
	const RAW_CHECKED = 'rawC';

	/** Helper for Filtering variables (callback of array_walk)
	 *
	 * @param mixed $var value
	 * @param string $key key used by array-walk - unused
	 * @param array $data array pair of type & default
	 * @return type
	 */
	private static function _weRequest(&$var, $key, array $data){
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
			case self::CMD:
				$var = strpos($var, 'WECMDENC_') !== false ?
					base64_decode(urldecode(substr($var, 9))) :
					$var;
			case self::UNIT:
				//FIMXE: check for %d[em,ex,pt,...]?
				return;
			case self::INT:
				$var = intval($var);
				return;
			case self::FLOAT:
				$var = floatval($var);
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
				$var = filter_var(str_replace(we_base_link::TYPE_MAIL_PREFIX, '', $var), FILTER_SANITIZE_EMAIL);
				return;
			case self::FILE:
				$var = str_replace(array('../', '//'), '', filter_var($var, FILTER_SANITIZE_URL));
				return;
			case self::URL:
				$var = filter_var($var, FILTER_SANITIZE_URL);
				return;
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
		/* static $requests = array();
		  if(isset($requests[$name][$index])){
		  t_e('rerequest ', $args, $requests[$name][$index]);
		  } else {
		  $requests[$name][$index] = debug_backtrace();
		  } */


		$var = $_REQUEST;
		$args = func_get_args();
		/* fixme temporary until 6.3.9 release */
		if(func_num_args()==4 && $args[3] === null){
			unset($args[3]);
		}
		/* end fix */
		unset($args[0], $args[2]);
		foreach($args as $arg){
			if(!isset($var[$arg])){
				return $default;
			}
			$var = $var[$arg];
		}

		if(is_array($var)){
			$oldVar = $var;
			array_walk($var, 'we_base_request::_weRequest', array($type, $default));
			if($oldVar != $var){
				if(REQUEST_SIMULATION){
					t_e('array changed', $type, $args, $oldVar, $var);
				}
			}
		} else {
			$oldVar = $var;
			self::_weRequest($var, '', array($type, $default));

			switch($type){
				case self::CMD://this must change&is ok!
				case self::RAW_CHECKED:
					//we didn't change anything.
					return $var;
				case self::INTLIST:
					$oldVar = trim($var, ',');
					$cmp = '' . $var;
					break;
				case self::BOOL://bool is transfered as 0/1

					$cmp = '' . intval($var);
					break;
				case self::RAW:
				case self::STRING:
					if($var){
						if($var == ('' . intval($oldVar))){
							t_e('notice', 'variable could be int', $args, $var);
						} elseif($var == ('' . floatval($oldVar))){
							t_e('notice', 'variable could be float', $args, $var);
						} elseif(strpos($var, '@')){
							t_e('notice', 'variable could be mail', $args, $var);
						} elseif(strpos($var, '://')){
							t_e('notice', 'variable could be url', $args, $var);
						} elseif(strpos($var, '/') === 0){
							t_e('notice', 'variable could be file', $args, $var);
						} elseif(count(explode(',', $var)) > 2){
							t_e('notice', 'variable could be list', $args, $var);
						}
					}
				//no break;
				default:
					$cmp = '' . $var;
			}
			if($oldVar != $cmp){
				t_e('changed values', $type, $args, $oldVar, $var);
				//don't break we
				if(REQUEST_SIMULATION){
					return $oldVar;
				}
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
