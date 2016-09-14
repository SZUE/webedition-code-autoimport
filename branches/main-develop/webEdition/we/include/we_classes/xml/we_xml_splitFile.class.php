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
 * Class XML_SplitFile()
 *
 * This class offers methods to split a XML document using the XPath language.
 * The xml document will be split into self-contained XML files.
 */
class we_xml_splitFile extends we_xml_parser{

	/**
	 * Number of exported XML files.
	 * @var        int
	 */
	var $fileId = 0;

	/**
	 * The path consists of the temporary directory and the uniqueId.
	 * @var        string
	 */
	var $path = "";

	/**
	 * Allow the file locking. This variable will be set to FALSE if the OS does
	 * not support file locking. This will surpress the file locking error for
	 * win 98 users when exportToFile() is called.
	 * @var        bool
	 */
	var $lockFile = TRUE;

	/**
	 * The indentation, i.e. number of whitespaces preceding the XML elements.
	 * @var        int
	 */
	//var $indent = 4;

	/**
	 * Constructor of the class.
	 *
	 * This constructor initializes the class and when a file is given, tries to
	 * read and parse it. Otherwise, call the XML_Parser::getFile() method to
	 * load and parse a file.
	 *
	 * @param      string $file
	 * @see        we_xml_parser::getFile()
	 */
	function __construct($file = ''){
		parent::__construct($file);
	}

	/**
	 * Split the given XML file into self-contained XML files whenever a child
	 * node is found. Make sure the XML document was already loaded by the
	 * parser before calling this method.
	 *
	 * @throws     FALSE on error
	 * @see        XML_Parser::parserHasContent(), XML_Parser::hasChildNodes(),
	 *             XML_Parser::evaluate(), getUniqueId(), exportAsXML()
	 */
	function splitFile($absoluteXPath = "*/descendant::*", $start = false, $end = false, $dpth = 1){
		// Check if a XML file was loaded, either by the constructor or by the
		// getFile method.
		if(!$this->parserHasContent()){
			return false;
		}

		// Save the path consisting of the temporary directory and a unique id.
		$this->path = TEMP_PATH . $this->getUniqueId();

		// Make the current directory.
		we_base_file::createLocalFolderByPath($this->path);

		// Node-set with paths of the descendant nodes.
		$nodeSet = $this->evaluate($absoluteXPath);
		$desc = 0;

		// Run through the descendant nodes.
		foreach($nodeSet as $node){
			// Split the XML data at each node that has children.
			if($this->hasChildNodes($node)){
				$desc++;
				if((!$start || ($desc >= $start)) && (!$end || ($desc <= $end))){
					// Add the XML declaration.
					$xml = '<?xml version="1.0" ' . ($this->mainXmlEncoding ? 'encoding="' . $this->mainXmlEncoding . '"' : 'standalone="yes"') . "?>\n" .
						// Add the XML data containing all nodes till to the given depth.
						$this->exportAsXML($node, $dpth);
					// Write the XML data to a file.
					$this->exportToFile($xml);
				}
			}
		}
	}

	/**
	 * Use the given node to generate and export self-contained XML data.
	 *
	 * @param      string $node
	 * @param      int $dpth
	 * @param      int $lvl
	 * @return     string The returned string contains the XML data
	 */
	function exportAsXML($node, $dpth, $lvl = 1){
		// Calculate the indentation.
		//$indent = str_repeat(' ', ($lvl * $this->indent));

		// Add the start tag of the new root element.
		$root = $this->nodes[$node];
		$xml =  '<' . $root['name'] . $this->getAttributeString($root) . ">\n";

		// Run through the child nodes.
		foreach($this->nodes[$node]["children"] as $tagname => $id){

			// Run through all siblings with the same name.
			for($sibl = 1; $sibl <= $id; $sibl++){

				// Leave out the child nodes which will be processed in the
				// next call of this method.
				$absoluteXPath = $node . '/' . $tagname . '[' . $sibl . ']';

				$sibling = $this->nodes[$absoluteXPath];
				if(!$this->hasChildNodes($absoluteXPath)){

					// Add the additional indentation.
					/*for($i = 0; $i < $this->indent; $i++){
						$xml .= ' ';
					}*/
					// Add the start tag of the element.
					$xml .= '<' . $tagname . $this->getAttributeString($sibling);
					$hasText = $this->hasCdata($absoluteXPath);
					if($hasText){
						$xml .= '>';
						// Add the character data and insert it within a CDATA
						// section if necessary.
						$hasSection = $this->hasCdataSection($absoluteXPath);
						$text = stripslashes($sibling["data"]);
						$xml .= (!$hasSection) ? $this->replaceEntities($text) : '<![CDATA[' . $text . ']]>';

						// Add the end tag of the element.
						if($hasText){
							$xml .= '</' . $tagname . ">\n";
						}
					} else {
						// Auto-close the tag.
						$xml .= "/>\n";
					}
				} else if($dpth > $lvl){
					$xml .= $this->exportAsXML($absoluteXPath, $dpth, $lvl + 1) . "\n";
				}
			}
		}
		// Add the end tag of the new root element.
		$xml .=  '</' . $root['name'] . '>';

		return $xml;
	}

	/**
	 * Generates a XML file with the content of the current node of the XML
	 * document. The given parameter contains the XML node beeing modified by
	 * this class before.
	 *
	 * @param      string $data
	 * @throws     FALSE on error
	 * @see        exportAsXML()
	 */
	function exportToFile($data){
		// The current file.
		$file = 'temp_' . $this->fileId . '.xml';

		// Open the file.
		$hFile = fopen($this->path . '/' . $file, 'wb');

		// Check if the file was opened correctly.
		if(!$hFile){
			return FALSE;
		} else {
			// Acquire an exclusive lock.
			flock($hFile, LOCK_EX);

			// Write the xml data to the file.
			if(!fwrite($hFile, $data)){
				return FALSE;
			}

			// Flush the output to the file.
			fflush($hFile);
			// Release the lock.
			flock($hFile, LOCK_UN);

			// Close the file.
			if(!fclose($hFile)){
				return FALSE;
			}
		}
		// Increase the number of exported xml files.
		$this->fileId++;
	}

	/**
	 * Returns a random hex code consisting of 32 characters.
	 *
	 * @return     string The returned string contains hexadecimal code
	 */
	function getUniqueId(){
		// md5 encrypted hash with the start value microtime(). The function
		// uniqid prevents from simultanious access, within a microsecond.
		return md5(uniqid(__FILE__, true)); // #6590, changed from: uniqid(microtime())
	}

	/**
	 * Replaces the XML entities for not beeing recognized as markup.
	 *
	 * @param      string $text
	 * @return     string
	 */
	function replaceEntities($text){
		return strtr($text, ['<' => '&lt;', '>' => '&gt;', '&nbsp;' => '&amp;nbsp;']);
	}

}
