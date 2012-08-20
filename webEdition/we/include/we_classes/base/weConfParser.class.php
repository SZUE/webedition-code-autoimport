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

	var $_content = "";
	var $_data = array();

	function __construct($content){
		$this->_content = $content;
		$this->_parse();
	}

	function getConfParserByFile($file){
		$fileContents = implode('', file($file));
		return new weConfParser($fileContents);
	}

	function setGlobalPref($name, $value, $comment = ""){
		$file_name = WE_INCLUDES_PATH . "conf/we_conf_global.inc.php";
		$parser = weConfParser::getConfParserByFile($file_name);
		$settings = $parser->getData();
		$file = weConfParser::changeSourceCode((in_array($name, array_keys($settings)) ? "define" : 'add'), $parser->getContent(), $name, $value, true, $comment);

		return weFile::save($file_name, $file);
	}

	function setGlobalPrefInContent(&$content, $name, $value, $comment = ""){
		$parser = new weConfParser($content);
		$settings = $parser->getData();
		$content = weConfParser::changeSourceCode((in_array($name, array_keys($settings)) ? "define" : 'add'), $content, $name, $value, true, $comment);

		return true;
	}

	function saveToFile($file){
		return weFile::save($file, $this->getFileContent(), 'wb');
	}

	function getValue($key){
		return isset($_data[$key]) ? $_data[$key] : "";
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

	function changeSourceCode($type = "define", $text, $key, $value, $active = true, $comment = ""){
		$_abort = false;

		switch($type){

			case "add":
				return substr(trim($text), 0, -2) .
					weConfParser::makeDefine($key, $value, $active, $comment) . "\n\n";
			case "define":
				$match = array();
				if(preg_match('|/?/?define\(\s*(["\']' . preg_quote($key) . '["\'])\s*,\s*([^\r\n]+)\);[\r\n]|Ui', $text, $match)){
					return str_replace($match[0], weConfParser::makeDefine($key, $value, $active) . "\n", $text);
				}
		}

		return $text;
	}

	function _addSlashes($in){
		$out = str_replace("\\", "\\\\", $in);
		$out = str_replace("\"", "\\\"", $out);
		$out = str_replace("\$", "\\\$", $out);
		return $out;
	}

	function _stripSlashes($in){
		$out = str_replace("\\\\", "\\", $in);
		$out = str_replace("\\\"", "\"", $out);
		$out = str_replace("\\\$", "\$", $out);
		return $out;
	}

	function getFileContent(){
		$out = '<?php

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
			$out .= weConfParser::makeDefine($key, $val) . "\n\n";
		}

		return $out;
	}

	function makeDefine($key, $val, $active = true, $comment = ""){
		$comment = ($comment ? "//$comment\n" : "");
		return $comment . ($active ? '' : "//") . 'define(\'' . $key . '\', ' .
			(!is_numeric($val) ? '"' . weConfParser::_addSlashes($val) . '"' : $val) . ');';
	}

	function _correctMatchValue($value){
		// remove whitespaces at beginning and end
		$value = trim($value);
		if(is_numeric($value)){
			// convert to a real number
			$value = 1 * $value;
		} else if(strlen($value) >= 2){
			// remove starting and ending quotes
			$value = substr($value, 1, strlen($value) - 2);
		} else{
			// something is not right, so  correct it as an empty string
			$value = "";
		}
		return weConfParser::_stripSlashes($value);
	}

	function _parse(){
		// reset data array
		$this->_data = array();
		if($this->_content){
			$pattern = '|define\(\s*"([^"]+)"\s*,\s*([^\r\n]+)\);[\r\n]|Ui';
			if(preg_match_all($pattern, $this->_content, $match, PREG_PATTERN_ORDER)){
				for($i = 0; $i < count($match[1]); $i++){
					$this->_data[$match[1][$i]] = weConfParser::_correctMatchValue($match[2][$i]);
				}
			}
		}
	}

}