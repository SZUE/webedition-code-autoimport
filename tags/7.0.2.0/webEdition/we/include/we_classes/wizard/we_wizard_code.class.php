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
abstract class we_wizard_code{
	/**
	 * Directory where the snippets are located
	 *
	 * @var string
	 */
	const SnippetPath = 'weCodeWizard/data/';

	/**
	 * get all custom specific snippets
	 *
	 * @return array
	 */
	private static function getCustomSnippets(){
		$SnippetDir = WE_INCLUDES_PATH . self::SnippetPath . 'custom';
		return (!is_dir($SnippetDir) ?
				array() :
				self::getSnippetsByDir('custom'));
	}

	/**
	 * get all standard snippets
	 *
	 * @return array
	 */
	private static function getStandardSnippets(){
		$SnippetDir = WE_INCLUDES_PATH . self::SnippetPath . 'default';

		return (!is_dir($SnippetDir) ?
				array() :
				self::getSnippetsByDir('default'));
	}

	/**
	 * get snippets by directory name
	 *
	 * @return array
	 */
	private static function getSnippetsByDir($SnippetDir, $Depth = 0){

		$Snippets = array();

		$Depth++;
		$dir = dir(WE_INCLUDES_PATH . self::SnippetPath . $SnippetDir);
		while(false !== ($entry = $dir->read())){

			// ignore files . and ..
			if($entry === '.' || $entry === '..'){
				// ignore these
				// get the snippets by file if extension is xml
			} elseif(!is_dir(WE_INCLUDES_PATH . self::SnippetPath . $SnippetDir . '/' . $entry) && substr_compare($entry, '.xml', -4, 4, true) == 0){
				// get the snippet
				$snippet = new we_wizard_codeSnippet(WE_INCLUDES_PATH . self::SnippetPath . $SnippetDir . '/' . $entry);
				$item = array(
					'type' => 'option',
					'name' => $snippet->getName(),
					'value' => $SnippetDir . '/' . $entry
				);
				$Snippets[] = $item;

				// enter subdirectory only if depth is smaller than 2
			} elseif(is_dir(WE_INCLUDES_PATH . self::SnippetPath . $SnippetDir . '/' . $entry) && $Depth < 2){

				$information = array();
				$infoFile = WE_INCLUDES_PATH . self::SnippetPath . $SnippetDir . '/' . $entry . '/_information.inc.php';
				if(file_exists($infoFile) && is_file($infoFile)){
					include ($infoFile);
				}

				$foldername = $entry;
				if(isset($information['foldername'])){
					$foldername = $information['foldername'];
				}

				$folder = array(
					'type' => 'optgroup',
					'name' => $foldername,
					'value' => self::getSnippetsByDir($SnippetDir . '/' . $entry, $Depth)
				);
				$Snippets[] = $folder;
			}
		}
		$dir->close();

		$Depth--;

		return $Snippets;
	}

	/**
	 * create the select box to select a snippet
	 *
	 * @param string $type
	 * @return string
	 */
	public static function getSelect($type = 'standard'){
		$options = array();

		switch($type){
			case 'custom' :
				$options = self::getCustomSnippets();
				break;

			default :
				$options = self::getStandardSnippets();
				break;
		}

		$select = "<select id=\"codesnippet_" . $type . "\" name=\"codesnippet_" . $type . "\"  size=\"7\" style=\"width:250px; height: 100px; display: none;\" ondblclick=\"YUIdoAjax(this.value);\" onchange=\"WE().layout.button.enable(document, 'btn_direction_right_applyCode')\">\n";
		foreach($options as $option){
			if($option['type'] === 'optgroup' && count($option['value']) > 0){
				$select .= '<optgroup label="' . $option['name'] . '">';

				foreach($option['value'] as $optgroupoption){

					if($optgroupoption['type'] === 'option'){
						$select .= '<option value="' . $optgroupoption['value'] . '">' . $optgroupoption['name'] . '</option>';
					}
				}
				$select .= '</optgroup>';
			} elseif($option['type'] === 'option'){
				$select .= '<option value="' . $option['value'] . '">' . $option['name'] . '</option>';
			}
		}
		$select .= '</select>';

		return $select;
	}

	/**
	 * get the needed javascript for the codewizard
	 *
	 * @return string
	 */
	public static function getJavascript(){
		return YAHOO_FILES .
			we_html_element::jsScript(JS_DIR . 'we_srcTmpl.js');
	}

}

/**
 * Code Sample
 *
 *
 * echo $CodeWizard->buildDialog();
 *
 */
