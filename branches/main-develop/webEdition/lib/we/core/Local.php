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
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
/**
 * Base class for localisation
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
if(!isset($_SESSION)){
	session_name(SESSION_NAME);
}
//on frontend this is not set, since no session is started where prefs are read. We assume UTF-8.
if(!isset($GLOBALS['WE_BACKENDCHARSET'])){
	$GLOBALS['WE_BACKENDCHARSET'] = 'UTF-8';
}

class we_core_Local{
	/**
	 * lang attribute
	 *
	 * @var string
	 */
	private static $_lang = '';

	/**
	 * charset attribute
	 *
	 * @var string
	 */
	private static $_charset = '';

	/**
	 * translate attribute
	 *
	 * @var NULL
	 */
	public static $_translate = NULL;

	/**
	 * translationSources attribute
	 *
	 * @var array
	 */
	public static $_translationSources = [];

	/**
	 * return localisation string
	 *
	 * @param string $lang
	 * @return string
	 */
	public static function weLangToLocale($lang){
		$locales = array_flip(getWELangs());

		$lang = str_replace('_UTF-8', '', $lang);

		if(isset($locales[$lang])){
			return $locales[$lang];
		}
		return $lang;
	}

	/**
	 * return language string
	 *
	 * @param string $locale
	 * @return string
	 */
	public static function localeToWeLang($locale){
		$langs = getWELangs();

		$locale = substr($locale, 0, 2);

		if(isset($langs[$locale])){
			$charset = self::getComputedUICharset();
			if($charset === 'UTF-8'){
				return $langs[$locale] . '_UTF-8';
			}
			return $langs[$locale];
		}
		return $locale;
	}

	/**
	 * return localisation string
	 *
	 * @return string
	 */
	public static function getLocale(){
		return self::weLangToLocale(self::getComputedUILang());
	}

	/**
	 * return language string
	 *
	 * @return string
	 */
	public static function getComputedUILang(){
		// get from cache if there
		if(self::$_lang !== ''){
			return self::$_lang;
		}

		if(defined('WE_WEBUSER_LANGUAGE')){
			self::$_lang = WE_WEBUSER_LANGUAGE;
		} else {
			if(!isset($_SESSION)){
				/*if(!isset($_SERVER['TMP'])){
					$_SERVER['TMP'] = WEBEDITION_PATH . 'we/cache';
				}*/
			}

			if(!empty($_SESSION['prefs']['Language'])){
				if(is_dir(WE_INCLUDES_PATH . 'we_language/' . $_SESSION['prefs']['Language'])){
					self::$_lang = $_SESSION['prefs']['Language'];
				} else if(defined('WE_LANGUAGE')){ //  bugfix #4229
					$_SESSION['prefs']['Language'] = WE_LANGUAGE;
					self::$_lang = WE_LANGUAGE;
				}
			} else {
				if(defined('WE_LANGUAGE')){
					self::$_lang = WE_LANGUAGE;
				}
			}
		}
		if(self::$_lang === ''){
			self::$_lang = 'English';
		}
		return self::$_lang;
	}

	/**
	 * return charset
	 *
	 * @return string
	 */
	public static function getComputedUICharset(){
		// get from cache if there
		if(self::$_charset !== ''){
			return self::$_charset;
		}
		$lang = self::getComputedUILang();
		if($GLOBALS['WE_BACKENDCHARSET'] === false){
			//we_util_Log::errorlog('Error: No charset language file found, using UTF-8 now!');
			self::$_charset = 'UTF-8';
			return self::$_charset;
		}
		self::$_charset = $GLOBALS['WE_BACKENDCHARSET'];
		return self::$_charset;
	}

	/**
	 * add translation to application
	 *
	 * @param string $file
	 * @param string $appName
	 * @return object
	 */
	public static function addTranslation($file, $appName = ''){
		$locale = self::getLocale();
		$path = ($appName === '') ? (WEBEDITION_PATH . '/lang/' . $locale . '/' . $file) : (WE_APPS_PATH . '/' . $appName . '/lang/' . $locale . '/' . $file);
		if(!file_exists($path)){
			if(defined('WE_LANGUAGE')){
				$locale = self::weLangToLocale(WE_LANGUAGE);
				$path = ($appName === '') ? (WEBEDITION_PATH . '/lang/' . $locale . '/' . $file) : (WE_APPS_PATH . '/' . $appName . '/lang/' . $locale . '/' . $file);
			}
		}

		if(file_exists($path)){
			if(!in_array($path, self::$_translationSources)){

				if(is_null(self::$_translate)){
					self::$_translate = new we_core_Translate('tmx', $path, $locale);
					self::$_translate->setLocale($locale);
				} else {
					self::$_translate->addTranslation($path, $locale);
				}
				self::$_translationSources[] = $path;
			}
		}
		return self::$_translate;
	}

}
