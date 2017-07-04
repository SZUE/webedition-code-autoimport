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

/**
 * Code Snipptes are used in templates inside webEdition
 *
 * @see Parser.php
 * @see dtd:http://docs.oasis-open.org/dita/v1.0.1/dtd/topic.dtd
 *
 */
class we_wizard_codeSnippet{
	/**
	 * Name of the Snippet
	 *
	 * @var string
	 */
	var $Name = "";

	/**
	 * Description of the snippet
	 *
	 * @var string
	 */
	var $Description = "";

	/**
	 * Author of the snippet
	 *
	 * @var string
	 */
	var $Author = "";

	/**
	 * Snippet code
	 *
	 * @var string
	 */
	var $Code = "";

	/**
	 * initialize the snippet from an xml file
	 *
	 * @param string $file
	 */
	function __construct($file){

		$Parser = new we_xml_parser($file);

		// set the title
		if($Parser->execMethod_count("/topic[1]/title[1]", 'g_l') > 0){
			$this->Name = $Parser->getData("/topic[1]/title[1]/g_l[1]");
			$this->Name = g_l('snippet', $this->Name);
		}

		// set the short description
		if($Parser->execMethod_count("/topic[1]/shortdesc[1]", 'g_l') > 0){
			$this->Description = $Parser->getData("/topic[1]/shortdesc[1]/g_l[1]");
			$this->Description = g_l('snippet', $this->Description);
		}

		// set the author
		if($Parser->execMethod_count("/topic[1]/prolog[1]", "author") > 0){
			$this->Author = $Parser->getData("/topic[1]/prolog[1]/author[1]");
			if(isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->getElement('Charset') != "UTF-8"){
				$this->Author = $this->Author;
			}
		}

		// set the code
		if($Parser->execMethod_count("/topic[1]/body[1]", "p") > 0){
			$this->Code = $Parser->getData("/topic[1]/body[1]/p[1]");
			$matches = [];
			if(preg_match_all('|__GL\(([^)]+)\)__|', $this->Code, $matches)){
				foreach($matches[1] as $match){
					$this->Code = str_replace('__GL(' . $match . ')__', g_l('snippet', $match), $this->Code);
				}
			}
		}
	}

	private static function changeCharset($string, $charset = ""){
		if(!$charset){
			$charset = $GLOBALS['we_doc']->getElement('Charset');
			if(!$charset){
				$charset = $GLOBALS['WE_BACKENDCHARSET'];
			}
		}

		if($charset != "UTF-8" && $charset){
			$string = iconv("UTF-8", $charset, $string);
		}

		return $string;
	}

	/**
	 * get the snippet name
	 *
	 * @return string
	 */
	function getName($charset = ""){
		return self::changeCharset($this->Name, $charset);
	}

	/**
	 * get the snippet description
	 *
	 * @return string
	 */
	function getDescription($charset = ""){
		return self::changeCharset($this->Description, $charset);
	}

	/**
	 * get the snippet author
	 *
	 * @return string
	 */
	function getAuthor($charset = ""){
		return self::changeCharset($this->Author, $charset);
	}

	/**
	 * get the snippet code
	 *
	 * @return string
	 */
	function getCode($charset = ""){
		return self::changeCharset($this->Code, $charset);
	}

}
