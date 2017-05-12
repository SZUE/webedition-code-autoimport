<?php

/**
 * webEdition CMS
 *
 * $Rev: 13703 $
 * $Author: lukasimhof $
 * $Date: 2017-04-06 16:44:34 +0200 (Do, 06. Apr 2017) $
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
class we_exim_ExportXML extends we_exim_Export{
	protected $exportType = we_import_functions::TYPE_XML;
	protected $permittedContentTypes = [
		we_base_ContentTypes::WEDOCUMENT,
		we_base_ContentTypes::OBJECT_FILE
	];

	function __construct(){
		parent::__construct();

	}

	protected function fileCreate(){
		if(parent::fileCreate()){
			$header = '<?xml version="1.0" encoding="' . DEFAULT_CHARSET . "\"?>\n" . we_backup_util::weXmlExImHead . ">\n";
			we_base_file::save($this->exportProperties['file'], $header);

			return true;
		}
		
		return false;
	}

	protected function writeExportItem($doc, $fh, $attribute = [], $isBin = false, $setBackupMarker = false){
		if(!$isBin){
			switch($doc->Table){
				case OBJECT_FILES_TABLE:
					$classname = we_exim_Export::correctTagname(f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($doc->TableID), "", new DB_WE()), 'object');
					fwrite($fh, "\t<" . $classname . ">\n");
					$this->object2nonWE($doc, $fh, $this->options['xml_cdata']);
					fwrite($fh, "\t</" . $classname . ">\n");
					break;
				case FILE_TABLE:
					if($doc->DocType){
						$doctypeText = we_exim_Export::correctTagname(f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' dt WHERE dt.ID=' . intval($doc->DocType), "", new DB_WE()), "document");
					} else {
						$doctypeText = 'document';
					}
					fwrite($fh, "\t<" . $doctypeText . ">\n");
					$this->document2nonWE($doc, $fh, $this->options['xml_cdata']);
					fwrite($fh, "\t</" . $doctypeText . ">\n");
					break;
			}
		}
	}

	protected function formatOutput($content, $tagname, $format = we_import_functions::TYPE_XML, $tabs = 2, $cdata = false, $fix_content = false){
		switch($format){
			case we_import_functions::TYPE_XML:
				// Generate intending tabs
				$tab = '';
				for($i = 0; $i < $tabs; $i++){
					$tab .= "\t";
				}

				// Generate XML output if content is given
				return $tab . "<" . $tagname . ($content ?
						'>' . ($fix_content ? ($cdata ? ('<![CDATA[' . $content . "]]>") : oldHtmlspecialchars($content, ENT_QUOTES)) : $content) . "</" . $tagname . ">\n" :
						"/>\n");
			case "cdata":
				// Generate CDATA XML output if content is given
				return ($content ? '<![CDATA[' . $content . ']]>' : '');
		}
	}

	protected function remove_from_check_array($check_array, $tagname){
		for($i = 0; $i < count($check_array); $i++){
			if(isset($check_array[$i]) && $check_array[$i] == $tagname){
				unset($check_array[$i]);
			}
		}

		return $check_array;
	}

	protected function fileComplete(){
		we_base_file::save($this->exportProperties['file'], we_backup_util::weXmlExImFooter, 'ab');
	}

}
