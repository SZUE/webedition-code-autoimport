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
class weConfParser{

	private $_content;
	private $_data;

	function __construct($content){
		$this->_content = $content;
		$this->_parse();
	}

	function getConfParserByFile($file){
		$fileContents = implode('', file($file));
		return new self($fileContents);
	}

	static function updateGlobalPrefByFile($filename, array $ignore = array()){
		$parser = self::getConfParserByFile($filename);
		$newglobals = $parser->getData();
		foreach($ignore as $cur){
			if(isset($newglobals[$cur])){
				unset($newglobals[$cur]);
			}
		}
		self::updateGlobalPref($newglobals);
	}

	static function updateGlobalPref(array $settings){
		$file_name = WE_INCLUDES_PATH . 'conf/we_conf_global.inc.php';
		$parser = self::getConfParserByFile($file_name);
		$settings = $parser->getData();
		$backup = $content = $parser->getContent();

		foreach($settings as $name => $value){
			if($value != ''){
				$content = self::changeSourceCode((in_array($name, array_keys($settings)) ? 'define' : 'add'), $content, $name, $value, true, '');
			}
		}
		if($content != $backup){
			weFile::save($file_name . '.bak', $backup);
			weFile::save($file_name, $content);
		}
	}

	function setGlobalPrefInContent(&$content, $name, $value, $comment = ""){
		$parser = new self($content);
		$settings = $parser->getData();
		$content = self::changeSourceCode((in_array($name, array_keys($settings)) ? "define" : 'add'), $content, $name, $value, true, $comment);

		return true;
	}

	function saveToFile($file){
		return weFile::save($file, $this->getFileContent(), 'wb');
	}

	function getValue($key){
		return isset($_data[$key]) ? $_data[$key] : '';
	}

	function setValue($key, $value){
		$_data[$key] = $value;
	}

	function getData(){
		return $this->_data;
	}

	function getContent(){
		return $this->_content;
	}

	static function changeSourceCode($type = 'define', $text, $key, $value, $active = true, $comment = ''){
		switch($type){
			case 'add':
				return trim($text, "\n\t ") . "\n\n" .
					self::makeDefine($key, $value, $active, $comment);
			case 'define':
				$match = array();
				if(preg_match('|/?/?define\(\s*(["\']' . preg_quote($key) . '["\'])\s*,\s*([^\r\n]+)\);[\r\n]|Ui', $text, $match)){
					return str_replace($match[0], self::makeDefine($key, $value, $active) . "\n", $text);
				}
		}

		return $text;
	}

	function _addSlashes($in){
		return str_replace(array("\\", '"', "\$"), array("\\\\", '\"', "\\\$"), $in);
	}

	function _stripSlashes($in){
		return str_replace(array("\\\\", "\\\"", "\\\$"), array("\\", '"', "\$"), $in);
	}

	function getFileContent(){
		$out = '<?php

/**
 * webEdition CMS
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
 * Configuration file for webEdition
 * =================================
 *
 * Holds the globals settings of webEdition.
 *
 * NOTE:
 * =====
 * Edit this file ONLY if you know exactly what you are doing!
 */

';
		foreach($this->_data as $key => $val){
			$out .= self::makeDefine($key, $val) . "\n\n";
		}

		return $out;
	}

	static function makeDefine($key, $val, $active = true, $comment = ''){
		return ($comment ? "//$comment\n" : '') . ($active ? '' : "//") . 'define(\'' . $key . '\', ' .
			(is_bool($val) || $val == 'true' || $val == 'false' ? ($val ? 'true' : 'false') :
				(!is_numeric($val) ? '"' . self::_addSlashes($val) . '"' : intval($val))) . ');';
	}

	function _correctMatchValue($value){
		// remove whitespaces at beginning and end
		$value = trim($value);
		if(is_numeric($value)){
			// convert to a real number
			$value = 1 * $value;
		} else if(strlen($value) >= 2){
			// remove starting and ending quotes
			$value = trim($value, '"\'');
		} else{
			// something is not right, so  correct it as an empty string
			$value = "";
		}
		return self::_stripSlashes($value);
	}

	//FIXME: parse & add comments!
	function _parse(){
		// reset data array
		$this->_data = array();
		$match = array();
		if($this->_content){
			$pattern = '|define\(\s*["\']([^"]+)["\']\s*,\s*([^\r\n]+)\);[\r\n]?|Ui';
			if(preg_match_all($pattern, $this->_content, $match, PREG_PATTERN_ORDER)){
				for($i = 0; $i < count($match[1]); $i++){
					$this->_data[$match[1][$i]] = self::_correctMatchValue($match[2][$i]);
				}
			}
		}
	}

}