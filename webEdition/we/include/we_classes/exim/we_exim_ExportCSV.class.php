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
class we_exim_ExportCSV extends we_exim_Export{
	protected $exportType = we_import_functions::TYPE_CSV;
	protected $permittedContentTypes = [
		we_base_ContentTypes::WEDOCUMENT,
	];
	private $delimiters = ['semicolon' => ";",
		'comma' => ",",
		'colon' => ":",
		'tab' => "\t",
		'space' => " "
	];
	private $encloses = [
		'singlequote' => "'",
		'doublequote' => '"'
	];

	private $lineends = [
		'unix' => "\n",
		'mac' => "\r",
		'windows' => "\r\n"
	];

	function __construct(){
		parent::__construct();
	}

	protected function fileCreate(){
		if(parent::fileCreate()){
			$header = '';
			we_base_file::save($this->exportProperties['file'], $header);

			return true;
		}

		return false;
	}

	protected function writeExportItem($doc, $fh, $attribute = [], $isBin = false, $setBackupMarker = false){
		if(!$isBin && isset($doc->Table) && $doc->Table === OBJECT_FILES_TABLE){
			$this->object2nonWE($doc, $fh);

			switch($this->options['csv_lineend']){
				case 'unix':
					$content .= "\n";
					break;
				case 'mac':
					$content .= "\r";
					break;
				case 'windows':
				default:
					$content .= "\r\n";
				break;
			}

			fwrite($fh, $content);
		}
	}

	protected function formatOutput($content){
		$delimiter = $this->delimiters[$this->options['csv_delimiter']];

		return ($content ? self::correctCSV($content) . $delimiter : $delimiter);
	}

	protected static function checkCompatibility($content, $csv_delimiter = ",", $csv_enclose = "'", $type = "escape"){
		switch($type){
			case 'escape':
				$check = ["\\"];
				break;
			case 'enclose':
				$check = [$csv_enclose];
				break;
			case 'delimiter':
				$check = [$csv_delimiter];
				break;
			case 'lineend':
				$check = ["\r\n", "\n", "\r"];
				break;
		}

		foreach($check as $cur){
			if(strpos($content, $cur) !== false){
				return true;
			}
		}

		return false;
	}

	protected function correctCSV($content){
		$encloser_corrected = false;
		$delimiter_corrected = false;
		$lineend_corrected = false;

		$encloser = isset($this->encloses[$this->options['csv_enclose']]) ? $this->encloses[$this->options['csv_enclose']] : "'";
		$delimiter = isset($this->delimiters[$this->options['csv_delimiter']]) ? $this->delimiters[$this->options['csv_delimiter']] : ',';
		$lineend = $this->options[csv_lineend] ?: 'windows';

		// Escape
		$corrected_content = (self::checkCompatibility($content, $delimiter, $encloser, "escape") ?
				self::correctEscape($content) : $content);

		// Enclose
		if(self::checkCompatibility($corrected_content, $delimiter, $encloser, "enclose")){
			$encloser_corrected = true;

			$corrected_content = self::correctEnclose($corrected_content, $encloser);
		} else {
			$corrected_content = $content;
		}

		// Delimiter
		if(self::checkCompatibility($corrected_content, $delimiter, $encloser, "delimiter")){
			$delimiter_corrected = true;
		}

		// Lineend
		if(self::checkCompatibility($corrected_content, $delimiter, $encloser, "lineend")){
			$lineend_corrected = true;

			$corrected_content = self::correctLineend($corrected_content, $lineend);
		} else {
			$corrected_content = $corrected_content;
		}

		if($encloser_corrected || $delimiter_corrected || $lineend_corrected){
			$corrected_content = $encloser . $corrected_content . $encloser;
		}

		return $corrected_content;
	}

	protected static function correctEscape($content){
		return str_replace("\\", "\\\\", $content);
	}

	protected static function correctEnclose($content, $csv_enclose = "'"){
		return str_replace($csv_enclose, ("\\" . $csv_enclose), $content);
	}

	protected static function correctLineend($content, $csv_lineend = "windows"){
		switch($csv_lineend){
			case "windows":
				return str_replace(["\n", "\r"], "\\r\\n", $content);
			case "unix":
			default:
				return str_replace(["\r\n", "\r"], "\\n", $content);
			case "mac":
				return str_replace(["\r\n", "\n"], "\\r", $content);
		}
	}

	protected function checkWriteFieldNames($fields, $fh){
		if($this->options['csv_fieldnames'] && $this->RefTable->current === 1){
			$pos = 0;
			$content = '';

			foreach($fields as $field){
				$content .= $this->formatOutput(we_exim_Export::correctTagname($field, "value", ++$pos));
			}

			$content .= isset($this->lineends[$this->options['csv_lineend']]) ? $this->lineends[$this->options['csv_lineend']] :
				$this->lineends['windows'];

			fwrite($fh, $content);
		}
	}

}
