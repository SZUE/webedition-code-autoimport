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
class we_browserDetect {

	///Browser
	protected static $br = 'unknown';
	/// String of useragent
	protected static $ua = '';
	///Version
	protected static $v = 0;
	///Operating System
	protected static $sys = 'unknown';
	///determines, if browser already detected
	private static $detected = false;

 	function we_browserDetect($ua = '') {
		//prevent from redetecting the same strings
		if (self::$detected && $ua=='') {
			return;
		}
		if($ua!=''){
			self::$br = 'unknown';
			self::$v = 0;
			self::$sys = 'unknown';
		}
		self::$detected = true;
		self::$ua = $ua ? $ua : $_SERVER['HTTP_USER_AGENT'];
		$regs = array();
		if (preg_match('/^([^ ]+) ([^(]*)(\([^)]+\))(.*)$/', self::$ua, $regs)) {
			$pre = $regs[1];
			$mid = $regs[2];
			$bracket = $regs[3];
			$bracket = str_replace(array('(', ')'), '', $bracket);
			$brArr = explode(';', $bracket);
			$post = $regs[4];

			list($bez, $prever) = explode('/', $pre);
			$bez = strtolower($bez);

			switch ($bez) {
				case 'lynx':
					self::$br = 'lynx';
					break;
				case 'mozilla': {
						$java = explode('/', trim($post));
						if($java[0]=='Java'){
							self::$br = 'java';
							self::$v = $java[1];
						}else
						if (preg_match('/msie (.*)$/i', trim($brArr[1]), $regs) && (trim($post) == '' || preg_match('/\.net/i', $post))) { //if last condition matches this will produce a notice. $regs[1] won't be defined...
							self::$br = 'ie';
							self::$v = $regs[1];
						} else
						if (preg_match('/konqueror\/(.*)$/i', trim($brArr[1]), $regs)) {
							self::$br = 'konqueror';
							self::$v = $regs[1];
						} else
						if (preg_match('/galeon\/(.*)$/i', trim($brArr[1]), $regs)) {
							self::$br = 'unknown';
							self::$v = $regs[1];
						} else {
							if (stristr($post, 'netscape6')) {
								self::$br = 'nn';
								if (preg_match('/netscape6\/(.+)/i', $post, $regs)) {
									self::$v = trim($regs[1]);
								} else {
									self::$v = 6;
								}
							} else
							if (stristr($post, 'netscape/7')) {
								self::$br = 'nn';
								if (preg_match('/netscape\/(7.+)/i', $post, $regs)) {
									self::$v = trim($regs[1]);
								} else {
									self::$v = 7;
								}
							} else
							if (preg_match('/AppleWebKit\/([0-9.]+)/i', $post, $regs)) {
								self::$v = $regs[1];
								self::$br = 'appleWebKit';

							if (stristr($post, 'chrome')) {
								if (preg_match('/chrome\/([0-9]+\.[0-9]+)/i', $post, $regs)) {
									self::$v = $regs[1];
								} else {
									self::$v = '1';
								}

								self::$br = 'chrome';
							}else
							if (stristr($post, 'safari')) {
								if (preg_match('/version\/([0-9]+\.[0-9]+)/i', $post, $regs)) {
									self::$v = $regs[1];
								} else {
									self::$v = '1';
								}
								self::$br = 'safari';

							}} else
							if (preg_match('/firefox\/([0-9]+.[0-9]+)/i', $post, $regs)) {
								self::$v = $regs[1];
								self::$br = 'firefox';
							} else
							if (stristr($post, 'gecko')) {
								self::$br = 'mozilla';
								if (preg_match('/rv:([0-9.]*)/i', $bracket, $regs)) {
									self::$v = $regs[1];
								}
							} else
							if (preg_match('/opera ([^ ]+)/i', $post, $regs)) {
								self::$br = 'opera';
								if (preg_match('/version\/([^ ]+)/i', $post, $reg)) {
									self::$v = $reg[1];
								} else {
									self::$v = $regs[1];
								}
							} else
							if ($brArr[0] == 'compatible') {
								self::$br = 'unknown';
								break;
							} else
							if (!stristr($bracket, 'msie')) {
								self::$br = 'nn';
								self::$v = preg_replace('/[^0-9.]/', '', $prever);
							}
						}

						$this->_getSys($bracket);
						break;
					}
				case 'opera':
					self::$br = 'opera';
					if (preg_match('/version\/([^ ]+)/i', $post, $reg)) {
						self::$v = $reg[1];
					} else {
						self::$v = $prever;
					}
					$this->_getSys($bracket);
					break;
				case 'googlebot':
					self::$br = 'unknown';
					#self::$v=$prever;
					break;
				case 'nokia-communicator-www-Browser':
					self::$br = 'unknown';
					break;
			}
			if (self::$sys == 'unknown') {
				if (stristr(self::$ua, 'webtv')) {
					self::$sys = 'webtv';
				}
			}
		} else
		if (preg_match('/^lynx([^a-z]+)[a-z].*/i', $ua, $regs)) {
			self::$br = 'lynx';
			self::$v = str_replace('/', '', $regs[1]);
		} else {
			self::$br = 'unknown';
		}
	}

	private function _getSys($bracket) {
		if (stristr($bracket, 'mac')) {
			self::$sys = 'mac';
		} else
		if (stristr($bracket, 'win')) {
			self::$sys = 'win';
		} else
		if (stristr($bracket, 'linux') || stristr($bracket, 'x11') || stristr($bracket, 'sun')) {
			self::$sys = 'unix';
		}
	}

	function getBrowser() {
		return self::$br;
	}

	function getBrowserVersion() {
		return trim(self::$v);
	}

	function getSystem() {
		return self::$sys;
	}

	function getUserAgent() {
		return self::$ua;
	}

	function getWebKitVersion(){
		if (preg_match('#AppleWebKit/([^ ]+)#i', self::$ua, $regs)) {
			return abs($regs[1]);
		}
		return 0;
	}

	function isGecko(){
		return stristr(self::$ua, 'gecko');
	}

	//todo: implement from we_browser_check
	function isSupported(){
		return false;
	}

}