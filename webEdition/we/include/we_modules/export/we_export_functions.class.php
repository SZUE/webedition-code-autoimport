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
abstract class we_export_functions{
	/*	 * ***********************************************************************
	 * HELPER FUNCTIONS
	 * *********************************************************************** */

	/**
	 * Creates the export file.
	 *
	 * @param      $format                                 string              (optional)
	 * @param      $filename                               string
	 * @param      $path                                   string
	 *
	 * @see        exportDocument
	 * @see        exportObject
	 *
	 * @return     bool
	 */
	static function fileCreate($format = we_import_functions::TYPE_GENERIC_XML, $filename, $path){
		switch($format){
			case we_import_functions::TYPE_GENERIC_XML:
				$_file_name = ($path === "###temp###" ? TEMP_PATH : $_SERVER['DOCUMENT_ROOT'] . $path) . $filename;

				$_continue = true;

				// Check if have to delete an existing file first
				if(file_exists($_file_name)){
					$_continue = unlink($_file_name);
				}

				// Check if can create the file now
				if(!$_continue === false){
					we_base_file::save($_file_name, '<?xml version="1.0" encoding="' . DEFAULT_CHARSET . "\"?>\n" . we_backup_util::weXmlExImHead . ">\n");
				}

				break;
			case "csv":
				$_file_name = ($path === "###temp###" ? TEMP_PATH : $_SERVER['DOCUMENT_ROOT'] . $path) . $filename;

				$_continue = true;

				// Check if have to delete an existing file first
				if(file_exists($_file_name)){
					$_continue = unlink($_file_name);
				}

				// Check if can create the file now
				if($_continue){
					we_base_file::save($_file_name, "");
				}

				break;
		}

		return ((isset($_continue) && $_continue === false) ? false : (isset($_continue) ? true : false));
	}

	/**
	 * Completes the export file.
	 *
	 * @param      $format                                 string              (optional)
	 * @param      $text                                   string
	 * @param      $filename                               string
	 *
	 * @see        exportDocument
	 * @see        exportObject
	 *
	 * @return     void
	 */
	static function fileComplete($format = we_import_functions::TYPE_GENERIC_XML, $filename){
		switch($format){
			case we_import_functions::TYPE_GENERIC_XML:
				we_base_file::save($filename, we_backup_util::weXmlExImFooter, "ab");

				break;
		}
	}

	/**
	 * Inits the export file (resuming supported).
	 *
	 * @param      $format                                 string              (optional)
	 * @param      $filename                               string
	 * @param      $path                                   string
	 * @param      $doctype                                string              (optional)
	 * @param      $tableid                                string              (optional)
	 *
	 * @see        exportDocument
	 * @see        exportObject
	 *
	 * @return     array
	 */
	static function fileInit($format, $filename, $path, $doctype = null, $tableid = null){
		switch($format){
			case we_import_functions::TYPE_GENERIC_XML:
				$_file = "";

				// Get a matching doctype or classname
				if(($doctype != null) && ($doctype != "") && ($doctype != 0)){
					$_doctype = f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' dt WHERE dt.ID=' . intval($doctype), "", new DB_WE());
				} else if(($tableid != null) && ($tableid != "") && ($tableid != 0)){
					$tableid = f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($tableid), "", new DB_WE());
				}

				if($doctype != null){
					$_doctype = self::correctTagname((isset($_doctype) ? $_doctype : $doctype), "document");
				} else if($tableid){
					$tableid = self::correctTagname($tableid, "object");
				}

				// Open document tag
				if($doctype != null){
					$_file .= "\t<" . $_doctype . ">\n";
				} else if($tableid != null){
					$_file .= "\t<" . $tableid . ">\n";
				}

				break;
			case "csv":
				$_file = "";

				// Get a matching classname
				if(intval($tableid) != 0){
					$tableid = f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($tableid), "Text", new DB_WE());
					$tableid = self::correctTagname($tableid, "object");
				}

				break;
		}

		return array("file" => $_file, "filename" => ($_SERVER['DOCUMENT_ROOT'] . ($path === "###temp###" ? TEMP_DIR : $path) . $filename), "doctype" => ((isset($doctype) && $doctype != null) ? $_doctype : ""), "tableid" => ($tableid ? : ""));
	}

	/**
	 * Writes the final output file.
	 *
	 * @param      $format                                 string              (optional)
	 * @param      $text                                   string
	 * @param      $doctype                                string
	 * @param      $filename                               string
	 *
	 * @see        exportDocument
	 * @see        exportObject
	 *
	 * @return     void
	 */
	static function fileFinish($format, $text, $doctype, $filename, $csv_lineend = "\\n"){
		switch($format){
			case we_import_functions::TYPE_GENERIC_XML:
				// Close document tag
				$text .= "\t</" . $doctype . ">\n";
				we_base_file::save($filename, $text, "ab");

				break;
			case "csv":
				// New linebreak
				switch($csv_lineend){
					case 'windows':
						$text .= "\r\n";
						break;
					case 'unix':
						$text .= "\n";
						break;
					case 'mac':
						$text .= "\r";
						break;
				}

				we_base_file::save($filename, $text, 'ab');
				break;
		}
	}

	/**
	 * This function corrects the name of a XML tag.
	 *
	 * @param      $tagname                                string
	 * @param      $alternative_name                       string
	 * @param      $alternative_number                     int                 (optional)
	 *
	 * @see        exportDocument
	 *
	 * @return     string
	 */
	static function correctTagname($tagname, $alternative_name, $alternative_number = -1){
		if($tagname != ''){
			// Remove spaces + special characters
			$tagname = preg_replace(array('/\40+/', '/[^a-zA-Z0-9_]+/'), array("_", ''), $tagname);
		}

		// Set alternative name if no name is now present present
		return ($tagname ? :
				(($alternative_number != -1) ? $alternative_name . $alternative_number : $alternative_name) );
	}

	/**
	 * This function checks for the need of a CSV encloser to be set.
	 *
	 * @param      $content                                string
	 * @param      $alternative_name                       string
	 * @param      $alternative_number                     int                 (optional)
	 *
	 * @see        exportDocument
	 *
	 * @return     string
	 */
	static function checkCompatibility($content, $csv_delimiter = ",", $csv_enclose = "'", $type = "escape"){
		switch($type){
			case 'escape':
				$_check = array("\\");

				break;
			case 'enclose':
				$_check = array($csv_enclose);

				break;
			case 'delimiter':
				$_check = array($csv_delimiter);

				break;
			case 'lineend':
				$_check = array("\r\n", "\n", "\r");

				break;
		}

		foreach($_check as $cur){
			if(strpos($content, $cur) !== false){
				return true;
			}
		}

		return false;
	}

	/**
	 * This function checks for the need of a CSV escape character to be set.
	 *
	 * @param      $content                                string
	 * @param      $alternative_name                       string
	 * @param      $alternative_number                     int                 (optional)
	 *
	 * @see        exportDocument
	 *
	 * @return     string
	 */
	static function correctEscape($content){
		return str_replace("\\", "\\\\", $content);
	}

	/**
	 * This function checks for the need of a CSV escape character to be set.
	 *
	 * @param      $content                                string
	 * @param      $alternative_name                       string
	 * @param      $alternative_number                     int                 (optional)
	 *
	 * @see        exportDocument
	 *
	 * @return     string
	 */
	static function correctEnclose($content, $csv_enclose = "'"){
		return str_replace($csv_enclose, ("\\" . $csv_enclose), $content);
	}

	/**
	 * This function checks for the need of a CSV escape character to be set.
	 *
	 * @param      $content                                string
	 * @param      $alternative_name                       string
	 * @param      $alternative_number                     int                 (optional)
	 *
	 * @see        exportDocument
	 *
	 * @return     string
	 */
	static function correctLineend($content, $csv_lineend = "windows"){
		switch($csv_lineend){
			case "windows":
				return str_replace(array("\n", "\r"), "\\r\\n", $content);
			case "unix":
			default:
				return str_replace(array("\r\n", "\r"), "\\n", $content);
			case "mac":
				return str_replace(array("\r\n", "\n"), "\\r", $content);
		}
	}

	/**
	 * This function sets a CSV encloder if it is needed.
	 *
	 * @param      $content                                string
	 * @param      $alternative_name                       string
	 * @param      $alternative_number                     int                 (optional)
	 *
	 * @see        exportDocument
	 *
	 * @return     string
	 */
	static function correctCSV($content, $csv_delimiter = ",", $csv_enclose = "'", $csv_lineend = "windows"){
		$_encloser_corrected = false;
		$_delimiter_corrected = false;
		$_lineend_corrected = false;

		// Escape
		$_corrected_content = (self::checkCompatibility($content, $csv_delimiter, $csv_enclose, "escape") ?
				self::correctEscape($content) : $content);


		// Enclose
		if(self::checkCompatibility($_corrected_content, $csv_delimiter, $csv_enclose, "enclose")){
			$_encloser_corrected = true;

			$_corrected_content = self::correctEnclose($_corrected_content, $csv_enclose);
		} else {
			$_corrected_content = $content;
		}

		// Delimiter
		if(self::checkCompatibility($_corrected_content, $csv_delimiter, $csv_enclose, "delimiter")){
			$_delimiter_corrected = true;
		}

		// Lineend
		if(self::checkCompatibility($_corrected_content, $csv_delimiter, $csv_enclose, "lineend")){
			$_lineend_corrected = true;

			$_corrected_content = self::correctLineend($_corrected_content, $csv_lineend);
		} else {
			$_corrected_content = $_corrected_content;
		}

		if($_encloser_corrected || $_delimiter_corrected || $_lineend_corrected){
			$_corrected_content = $csv_enclose . $_corrected_content . $csv_enclose;
		}

		return $_corrected_content;
	}

	/**
	 * This functions formats the output of a single element of an export.
	 *
	 * @param      $tagname                                string
	 * @param      $content                                string
	 * @param      $format                                 string              (optional)
	 * @param      $tabs                                   string              (optional)
	 * @param      $fix_content                            bool                (optional)
	 * @param      $csv_delimiter                          string              (optional)
	 * @param      $csv_enclose                            string              (optional)
	 *
	 * @see        exportDocument
	 * @see        correctXMLContent
	 *
	 * @return     string
	 */
	static function formatOutput($tagname, $content, $format = we_import_functions::TYPE_GENERIC_XML, $tabs = 2, $cdata = false, $fix_content = false, $csv_delimiter = ",", $csv_enclose = "'", $csv_lineend = "windows"){
		switch($format){
			case we_import_functions::TYPE_GENERIC_XML:
				// Generate intending tabs
				$_tabs = '';
				for($i = 0; $i < $tabs; $i++){
					$_tabs .= "\t";
				}

				// Generate XML output if content is given
				return $_tabs . "<" . $tagname . ($content ?
						'>' . ($fix_content ? ($cdata ? ('<![CDATA[' . $content . "]]>") : oldHtmlspecialchars($content, ENT_QUOTES)) : $content) . "</" . $tagname . ">\n" :
						"/>\n");

			case "csv":
				// Generate XML output if content is given
				return ($content ?
						self::correctCSV($content, $csv_delimiter, $csv_enclose, $csv_lineend) . $csv_delimiter : $csv_delimiter);
			case "cdata":
				// Generate CDATA XML output if content is given
				return ($content ? '<![CDATA[' . $content . ']]>' : '');
		}
	}

	/**
	 * Helper function to detect empty xml tags to be written.
	 *
	 * @param      $check_array                            array
	 * @param      $tagname                                string
	 *
	 * @see        exportDocument
	 *
	 * @return     array
	 */
	static function remove_from_check_array($check_array, $tagname){
		for($i = 0; $i < count($check_array); $i++){
			if(isset($check_array[$i]) && $check_array[$i] == $tagname){
				unset($check_array[$i]);
			}
		}

		return $check_array;
	}

	/*	 * ***********************************************************************
	 * EXPORT FUNCTIONS
	 * *********************************************************************** */

	/**
	 * Imports a document into webEdition.
	 *
	 * @param      $ID                                     int
	 * @param      $format                                 string              (optional)
	 * @param      $filename                               string
	 * @param      $path                                   string
	 * @param      $file_create                            bool                (optional)
	 * @param      $file_complete                          bool                (optional)
	 *
	 * @see        correctTagname
	 * @see        formatOutput
	 * @see        remove_from_check_array
	 * @see        fileCreate
	 * @see        fileComplete
	 *
	 * @return     bool
	 */
	static function exportDocument($ID, $format, $filename, $path, $file_create, $file_complete, $cdata){
		$_export_success = false;

		// Create a new webEdition document object
		$we_doc = new we_webEditionDocument();

		$we_doc->initByID($ID);

		if($file_create){
			self::fileCreate($format, $filename, $path);
		}
		// Read content
		if($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT){
			$DB_WE = new DB_WE();

			$_template_code = f('SELECT c.Dat FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON l.CID=c.ID WHERE l.DocumentTable="' . stripTblPrefix(TEMPLATES_TABLE) . '" AND l.DID=' . intval($we_doc->TemplateID) . ' AND l.nHash=x\'' . md5("completeData") . '\'', '', $DB_WE);
			$_tag_parser = new we_tag_tagParser($_template_code);
			$_tags = $_tag_parser->getAllTags();
			$_regs = $_records = array();

			foreach($_tags as $_tag){
				if(preg_match('|<we:([^> /]+)|i', $_tag, $_regs)){
					$_tag_name = $_regs[1];
					if(preg_match('|name="([^"]+)"|i', $_tag, $_regs) && ($_tag_name != "var")){
						$_name = $_regs[1];
						switch($_tag_name){
							// tags with text content, links and hrefs
							case "input":
							case "textarea":
							case "href":
							case "link":
								$_records[] = $_name;
								break;
						}
					}
				}
			}

			$hrefs = array();

			$_file_values = self::fileInit($format, $filename, $path, ((isset($we_doc->DocType) && ($we_doc->DocType != "") && ($we_doc->DocType != 0)) ? $we_doc->DocType : "document"));

			$_file = $_file_values["file"];
			$_file_name = $_file_values["filename"];
			$_doctype = $_file_values["doctype"];

			$_tag_counter = 0;
			$regs = array();

			foreach($we_doc->elements as $k => $v){
				$_tag_counter++;

				switch(isset($v["type"]) ? $v["type"] : ''){
					case "date": // is a date field
						$_tag_name = self::correctTagname($k, "date", $_tag_counter);
						$_file .= self::formatOutput($_tag_name, abs($we_doc->elements[$k]["dat"]), $format, 2, $cdata);

						// Remove tagname from array
						if(isset($_records)){
							$_records = self::remove_from_check_array($_records, $_tag_name);
						}

						break;
					case "txt":
						if(preg_match('|(.+)' . we_base_link::MAGIC_INFIX . '(.+)|', $k, $regs)){ // is a we:href field
							if(!in_array($regs[1], $hrefs)){
								$hrefs[] = $regs[1];

								if($we_doc->getElement($regs[1] . we_base_link::MAGIC_INT_LINK, 'dat', 0)){
									$_intID = $we_doc->getElement($regs[1] . we_base_link::MAGIC_INT_LINK_ID, 'bdid');

									$_tag_name = self::correctTagname($k, "link", $_tag_counter);
									$_file .= self::formatOutput($_tag_name, id_to_path($_intID, FILE_TABLE, $DB_WE), $format, 2, $cdata);

									// Remove tagname from array
									if(isset($_records)){
										$_records = self::remove_from_check_array($_records, $_tag_name);
									}
								} else {
									$_tag_name = self::correctTagname($k, "link", $_tag_counter);
									$_file .= self::formatOutput($_tag_name, $we_doc->elements[$regs[1]]["dat"], $format, 2, $cdata);

									// Remove tagname from array
									if(isset($_records)){
										$_records = self::remove_from_check_array($_records, $_tag_name);
									}
								}
							}
						} else if(substr($we_doc->elements[$k]["dat"], 0, 2) === "a:" && is_array(we_unserialize($we_doc->elements[$k]["dat"]))){ // is a we:link field
							$_tag_name = self::correctTagname($k, "link", $_tag_counter);
							$_file .= self::formatOutput($_tag_name, self::formatOutput("", $we_doc->getFieldByVal($we_doc->elements[$k]["dat"], "link"), "cdata"), $format, 2, $cdata);

							// Remove tagname from array
							if(isset($_records)){
								$_records = self::remove_from_check_array($_records, $_tag_name);
							}
						} else { // is a normal text field
							$_tag_name = self::correctTagname($k, 'text', $_tag_counter);
							$_file .= self::formatOutput($_tag_name, we_document::parseInternalLinks($we_doc->elements[$k]["dat"], $we_doc->ParentID, ''), $format, 2, $cdata, $format == we_import_functions::TYPE_GENERIC_XML);

							// Remove tagname from array
							if(isset($_records)){
								$_records = self::remove_from_check_array($_records, $_tag_name);
							}
						}

						break;
				}
			}

			if(isset($_records) && is_array($_records)){
				foreach($_records as $cur){
					$_file .= self::formatOutput($cur, '', $format, 2, $cdata);
				}
			}

			self::fileFinish($format, $_file, $_doctype, $_file_name);
		}
		$_tmp_file_name = $_SERVER['DOCUMENT_ROOT'] . ($path === "###temp###" ? TEMP_DIR : $path) . $filename;

		if($file_complete){
			self::fileComplete($format, $_tmp_file_name);
		}

		// Return success of export
		return $_export_success;
	}

	/**
	 * Imports a document into webEdition.
	 *
	 * @param      $ID                                     int
	 * @param      $format                                 string              (optional)
	 * @param      $filename                               string
	 * @param      $path                                   string
	 * @param      $file_create                            bool                (optional)
	 * @param      $file_complete                          bool                (optional)
	 * @param      $csv_delimiter                          string              (optional)
	 * @param      $csv_enclose                            string              (optional)
	 * @param      $csv_lineend                            string              (optional)
	 * @param      $csv_fieldnames                         string              (optional)
	 *
	 * @see        correctTagname
	 * @see        formatOutput
	 * @see        remove_from_check_array
	 * @see        fileCreate
	 * @see        fileComplete
	 *
	 * @return     bool
	 */
	static function exportObject($ID, $format, $filename, $path, $file_create = false, $file_complete = false, $cdata = false, $csv_delimiter = ",", $csv_enclose = "'", $csv_lineend = "\\n", $csv_fieldnames = false){
		$_export_success = false;

		if($csv_delimiter === '\t'){
			$csv_delimiter = "\t";
		}

		// Create a new webEdition object object
		$we_obj = new we_objectFile();

		$we_obj->initByID($ID, OBJECT_FILES_TABLE);

		$DB_WE = new DB_WE();

		$dv = f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($we_obj->TableID), '', $DB_WE);
		$dv = we_unserialize($dv);
		if(!is_array($dv)){
			$dv = array();
		}

		$tableInfo_sorted = $we_obj->getSortedTableInfo($we_obj->TableID, true, $DB_WE);

		$fields = $regs = array();
		foreach($tableInfo_sorted as $cur){
			// bugfix 8141
			if(preg_match('/(.+?)_(.*)/', $cur["name"], $regs)){
				$fields[] = array("name" => $regs[2], "type" => $regs[1]);
			}
		}

		if($file_create && !$csv_fieldnames){
			self::fileCreate($format, $filename, $path);
		}

		$_file_values = self::fileInit($format, $filename, $path, null, $we_obj->TableID);

		if($csv_fieldnames){
			self::exportObjectFieldNames($fields, $_file_values, $csv_delimiter, $csv_enclose, $csv_lineend);
		}

		$_file = $_file_values["file"];
		$_file_name = $_file_values["filename"];
		$_tableid = $_file_values["tableid"];

		foreach($fields as $i => $field){
			switch($field['type']){
				case 'object':
				case 'img':
				case 'binary':
					continue;
				default:
					$realName = $field['type'] . '_' . $field['name'];

					switch($format){
						case we_import_functions::TYPE_GENERIC_XML:
							$_tag_name = self::correctTagname($field["name"], "value", $i);
							$_content = $we_obj->getElementByType($field["name"], $field["type"], $dv[$realName]);
							$_file .= self::formatOutput($_tag_name, we_document::parseInternalLinks($_content, 0, ''), $format, 2, $cdata, (($format == we_import_functions::TYPE_GENERIC_XML) && ($field["type"] != "date") && ($field["type"] != "int") && ($field["type"] != "float")));

							break;
						case 'csv':
							$_content = $we_obj->getElementByType($field["name"], $field["type"], $dv[$realName]);
							$_file .= self::formatOutput("", we_document::parseInternalLinks($_content, 0, ''), $format, 2, false, (($format == we_import_functions::TYPE_GENERIC_XML) && ($field["type"] != "date") && ($field["type"] != "int") && ($field["type"] != "float")), $csv_delimiter, $csv_enclose, $csv_lineend);

							break;
					}
			}
		}

		self::fileFinish($format, $_file, $_tableid, $_file_name, ($format === "csv" ? $csv_lineend : ""));

		if($file_complete){
			self::fileComplete($format, $_file_name);
		}

		// Return success of export
		return $_export_success;
	}

	/**
	 * Imports a document into webEdition.
	 *
	 * @param      $ID                                     int
	 * @param      $format                                 string              (optional)
	 * @param      $filename                               string
	 * @param      $path                                   string
	 * @param      $file_create                            bool                (optional)
	 * @param      $file_complete                          bool                (optional)
	 * @param      $csv_delimiter                          string              (optional)
	 * @param      $csv_enclose                            string              (optional)
	 * @param      $csv_lineend                            string              (optional)
	 * @param      $csv_fieldnames                         string              (optional)
	 *
	 * @see        correctTagname
	 * @see        formatOutput
	 * @see        remove_from_check_array
	 * @see        fileCreate
	 * @see        fileComplete
	 *
	 * @return     bool
	 */
	private static function exportObjectFieldNames($fields, $_file_values, $csv_delimiter, $csv_enclose, $csv_lineend){
		$_export_success = false;

		$_file = $_file_values["file"];
		$_file_name = $_file_values["filename"];
		$pos = 0;

		foreach($fields as $field){
			switch($field['type']){
				case 'object':
				case 'img':
				case 'binary':
					continue;
				default:
					$realName = $field["type"] . '_' . $field["name"];

					$_tag_name = self::correctTagname($field["name"], "value", ++$pos);
					$_file .= self::formatOutput('', $_tag_name, "csv", 2, false, false, $csv_delimiter, $csv_enclose, $csv_lineend);
			}
		}

		self::fileFinish('csv', $_file, '', $_file_name, $csv_lineend);

		// Return success of export
		return $_export_success;
	}

}
