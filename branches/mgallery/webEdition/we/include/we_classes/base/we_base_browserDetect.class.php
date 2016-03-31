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
class we_base_browserDetect{
	const UNKNOWN = 'unknown';
	const OPERA = 'opera';
	const IE = 'ie';
	const EDGE = 'edge';
	const FF = 'firefox';
	const LYNX = 'lynx';
	const JAVA = 'java';
	const KONQUEROR = 'konqueror';
	const NETSCAPE = 'nn';
	const MOZILLA = 'mozilla';
	const APPLE = 'appleWebKit';
	const SAFARI = 'safari';
	const CHROME = 'chrome';
	const SYS_MAC = 'mac';
	const SYS_WIN = 'win';
	const SYS_UNIX = 'unix';
	const SYS_ANDROID = 'android';
	const SYS_IPHONE = 'iphone';

	///Browser
	protected static $br = self::UNKNOWN;
	/// String of useragent
	protected static $ua = '';
	///Version
	protected static $v = 0;
	///Operating System
	protected static $sys = self::UNKNOWN;
	///determines, if browser already detected
	private static $detected = false;

	function __construct($ua = ''){
		//prevent from redetecting the same strings
		if(self::$detected && $ua === ''){
			return;
		}
		if($ua != ''){
			self::$br = self::UNKNOWN;
			self::$v = 0;
			self::$sys = self::UNKNOWN;
		}
		self::$detected = true;
		self::$ua = $ua ? : (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		$regs = array();
		if(preg_match('|^([^ ]+) ([^(]*)(\([^)]+\))(.*)$|', self::$ua, $regs)){
			$pre = $regs[1];
			//$mid = $regs[2];
			$bracket = str_replace(array('(', ')'), '', $regs[3]);
			$post = $regs[4];

			$tmp = explode('/', $pre);
			$bez = strtolower($tmp[0]);
			$prever = isset($tmp[1]) ? $tmp[1] : '';

			switch($bez){
				case 'lynx':
					self::$br = self::LYNX;
					break;
				case 'mozilla':
					$java = explode('/', trim($post));
					if($java[0] === 'Java'){
						self::$br = self::JAVA;
						self::$v = $java[1];
					} elseif(preg_match('|msie (.*)$|i', $bracket, $regs) && (trim($post) === '' || preg_match('|\.net|i', $post)) || preg_match('|Trident.*rv:(\d+\.?\d?)|i', $bracket, $regs)){
						self::$br = self::IE;
						self::$v = isset($regs[1]) ? $regs[1] : 0;
					} elseif(preg_match('|konqueror/(.*)$|i', $bracket, $regs)){
						self::$br = self::KONQUEROR;
						self::$v = $regs[1];
					} elseif(preg_match('|galeon/(.*)$|i', $bracket, $regs)){
						self::$br = self::UNKNOWN;
						self::$v = $regs[1];
					} else {
						if(stristr($post, 'netscape6')){
							self::$br = self::NETSCAPE;
							if(preg_match('|netscape6/(.+)|i', $post, $regs)){
								self::$v = trim($regs[1]);
							} else {
								self::$v = 6;
							}
						} elseif(stristr($post, 'netscape/7')){
							self::$br = self::NETSCAPE;
							self::$v = (preg_match('|netscape/(7.+)|i', $post, $regs) ? trim($regs[1]) : 7);
						} elseif(stristr($post, 'edge')){
							self::$br = self::EDGE;
							self::$v = (preg_match('|edge/(.+)|i', $post, $regs) ? trim($regs[1]) : 12);
						} elseif(preg_match('|AppleWebKit/([0-9.]+)|i', $post, $regs)){
							if(stristr($post, 'chrome')){
								self::$v = (preg_match('|chrome/([0-9]+\.[0-9]+)|i', $post, $regs) ? $regs[1] : '1');
								self::$br = self::CHROME;
							} elseif(stristr($post, 'safari')){
								self::$v = (preg_match('|version/([0-9]+\.[0-9]+)|i', $post, $regs) ? $regs[1] : '1');
								self::$br = self::SAFARI;
							} else {
								self::$v = $regs[1];
								self::$br = self::APPLE;
							}
						} elseif(preg_match('|firefox/([0-9]+.[0-9]+)|i', $post, $regs)){
							self::$v = $regs[1];
							self::$br = self::FF;
						} elseif(stristr($post, 'gecko')){
							self::$br = self::MOZILLA;
							if(preg_match('|rv:([0-9.]*)|i', $bracket, $regs)){
								self::$v = $regs[1];
							}
						} elseif(preg_match('|opera ([^ ]+)|i', $post, $regs)){
							$reg = array();
							if(stristr($post, 'chrome')){
								self::$v = (preg_match('|chrome/([0-9]+\.[0-9]+)|i', $post, $regs) ? $regs[1] : '1');
								self::$br = self::CHROME;
							} else {
								self::$br = self::OPERA;
								self::$v = (preg_match('|version/([^ ]+)|i', $post, $reg) ?
										$reg[1] : $regs[1]);
							}
						} elseif(stristr($bracket, 'compatible')){
							self::$br = self::UNKNOWN;
							break;
						} elseif(!stristr($bracket, 'msie')){
							self::$br = self::NETSCAPE;
							self::$v = preg_replace('|[^0-9.]|', '', $prever);
						}
					}

					$this->_getSys($bracket);
					break;
				case 'opera':
					if(stristr($post, 'chrome')){
						self::$v = (preg_match('|chrome/([0-9]+\.[0-9]+)|i', $post, $regs) ? $regs[1] : '1');
						self::$br = self::CHROME;
					} else {
						self::$br = self::OPERA;
						self::$v = (preg_match('|version/([^ ]+)|i', $post, $reg) ? $reg[1] : $prever);
					}
					$this->_getSys($bracket);
					break;
				case 'googlebot':
					self::$br = self::UNKNOWN;
					#self::$v=$prever;
					break;
				case 'nokia-communicator-www-Browser':
					self::$br = self::UNKNOWN;
					break;
			}
			if(self::$sys == self::UNKNOWN){
				if(stristr(self::$ua, 'webtv')){
					self::$sys = 'webtv';
				}
			}
		} elseif(preg_match('|^lynx([^a-z]+)[a-z].*|i', $ua, $regs)){
			self::$br = self::LYNX;
			self::$v = str_replace('/', '', $regs[1]);
		} else {
			self::$br = self::UNKNOWN;
		}
	}

	private function _getSys($bracket){
		if(stristr($bracket, 'mac')){
			self::$sys = self::SYS_MAC;
		} elseif(stristr($bracket, 'win')){
			self::$sys = self::SYS_WIN;
		} elseif(stristr($bracket, 'android')){
			self::$sys = self::SYS_ANDROID;
		} elseif(stristr($bracket, 'iPhone')){
			self::$sys = self::SYS_IPHONE;
		} elseif(stristr($bracket, 'linux') || stristr($bracket, 'x11') || stristr($bracket, 'sun')){
			self::$sys = self::SYS_UNIX;
		}
	}

	public static function inst(){
		static $ref = 0;
		if(!is_object($ref)){
			$ref = new self();
		}
		return $ref;
	}

	function getBrowser(){
		return self::$br;
	}

	public static function isIE(){
		return self::inst()->getBrowser() == self::IE;
	}

	public static function isEdge(){
		return self::inst()->getBrowser() == self::EDGE;
	}

	public static function isOpera(){
		return self::inst()->getBrowser() == self::OPERA;
	}

	public static function isSafari(){
		return self::inst()->getBrowser() == self::SAFARI;
	}

	public static function isNN(){
		switch(self::inst()->getBrowser()){
			case self::NETSCAPE:
			case self::MOZILLA:
			case self::FF:
				return true;
		}
		return false;
	}

	public static function isFF(){
		return self::inst()->getBrowser() == self::FF;
	}

	public static function isChrome(){
		return self::inst()->getBrowser() == self::CHROME;
	}

	public static function isMAC(){
		return self::inst()->getSystem() == self::SYS_MAC;
	}

	public static function isUNIX(){
		return self::inst()->getSystem() == self::SYS_UNIX;
	}

	public static function isWin(){
		return self::inst()->getSystem() == self::SYS_WIN;
	}

	public static function getIEVersion(){
		return self::isIE() ? intval(trim(self::$v)) : -1;
	}

	public function getBrowserVersion(){
		return trim(self::$v);
	}

	public function getSystem(){
		return self::$sys;
	}

	public function getUserAgent(){
		return self::$ua;
	}

	public function getWebKitVersion(){
		$regs = array();
		if(preg_match('|AppleWebKit/([^ ]+)|i', self::$ua, $regs)){
			return intval($regs[1]);
		}
		return 0;
	}

	public static function isGecko(){
		return stristr(self::inst()->getUserAgent(), 'gecko');
	}

}
