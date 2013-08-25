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
abstract class we_glossary_replace{

	const configFile = 'we_conf_glossary_settings.inc.php';

	public static function useAutomatic(){
		$configFile = WE_GLOSSARY_MODULE_PATH . self::configFile;
		if(!file_exists($configFile) || !is_file($configFile)){
			we_glossary_settingControl::saveSettings(true);
		}
		include_once($configFile);

		return (isset($GLOBALS['weGlossaryAutomaticReplacement']) && $GLOBALS['weGlossaryAutomaticReplacement']);

	}

	/**
	 * replace the content
	 *
	 * @param unknown_type $content
	 * @param unknown_type $language
	 */
	public static function replace($content, $language){
		$configFile = WE_GLOSSARY_MODULE_PATH . self::configFile;
		if(!file_exists($configFile) || !is_file($configFile)){
			we_glossary_settingControl::saveSettings(true);
		}
		include_once($configFile);

		if(isset($GLOBALS['weGlossaryAutomaticReplacement']) && $GLOBALS['weGlossaryAutomaticReplacement']){
			return self::doReplace($content, $language);
		}
		return $content;
	}

	/**
	 * replace all glossary items for the requested language in the
	 * given source code
	 *
	 * @param string $src
	 * @param string $language
	 * @return string
	 */
	public static function doReplace($src, $language){
		if($language == ''){
			we_loadLanguageConfig();
			$language = $GLOBALS['weDefaultFrontendLanguage'];
		}
		$matches = array();
		// get the words to replace
		$cache = new we_glossary_cache($language);
		$replace = array(
			'<span ' => $cache->get(we_glossary_glossary::TYPE_FOREIGNWORD),
			'<abbr ' => $cache->get(we_glossary_glossary::TYPE_ABBREVATION),
			'<acronym ' => $cache->get(we_glossary_glossary::TYPE_ACRONYM),
			'<a ' => $cache->get(we_glossary_glossary::TYPE_LINK),
			'' => $cache->get(we_glossary_glossary::TYPE_TEXTREPLACE)
		);
		unset($cache);

		//forbid self-reference links
		foreach($replace['<a '] as $k => $rep){
			if(stripos($rep, $GLOBALS['we_doc']->Path) !== FALSE){
				unset($replace['<a '][$k]);
			}
		}
		//remove empty elements
		foreach($replace as $tag => $words){
			if(empty($words)){
				unset($replace[$tag]);
			}
		}

		// first check if there is a body tag inside the sourcecode
		preg_match('|<body[^>]*>(.*)</body>|si', $src, $matches);

		$srcBody = $replBody = (isset($matches[1]) ? $matches[1] : $src);

		/*
		  This is the fastest variant
		 */
		// split the source into tag and non-tag pieces
		$pieces = preg_split('|(<[^>]*>)|', $replBody, -1, PREG_SPLIT_DELIM_CAPTURE);
		// replace words in non-tag pieces
		$replBody = '';
		$before = '';
		foreach($pieces as $piece){
			if(strpos($piece, '<') === FALSE && stripos($before, '<script') === FALSE){
				//this will generate invalid code: $piece = str_replace('&quot;', '"', $piece);
				foreach($replace as $tag => $words){
					if($tag == '' || stripos($before, $tag) === FALSE){
						$piece = self::doReplaceWords($piece, $words);
					}
				}
			}
			$replBody .= $piece;
			$before = $piece;
		}

		$replBody = str_replace('@@@we@@@', '\'', $replBody);
		if(isset($matches[1])){
			return str_replace($srcBody, $replBody, $src);
		} else {
			return $replBody;
		}
	}

	/**
	 * replace just the given replacements in the given source
	 *
	 * @param string $src
	 * @param array $replacements
	 * @return string
	 */
	private static function doReplaceWords($src, $replacements){
		if($src === '' || count($replacements) == 0){
			return $src;
		}
		update_time_limit(0);
		$src2 = preg_replace(array_keys($replacements), $replacements, ' ' . $src . ' ', 1);

		if(trim($src, ' ') != trim($src2, ' ') && trim($src2, ' ') != ''){
			$len = strlen($src);
			$spaceStr = '';
			for($i = $len - 1; $i >= 0; $i--){
				if($src{$i} == ' '){
					$spaceStr .=' ';
				} else {
					break;
				}
			}

			// add spaces before and after and replace the words
			$src = preg_replace(array_keys($replacements), $replacements, ' ' . $src . ' ', 1);
			// remove added spaces
			//$return = (preg_replace('/^ (.+) $/', '$1', $src));
			$return = substr($src, 1, -1);
			// remove added slashes
			return stripslashes($return);
		}

		return $src;
	}

}