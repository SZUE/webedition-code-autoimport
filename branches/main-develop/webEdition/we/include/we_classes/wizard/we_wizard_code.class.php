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
		$_dir = dir(WE_INCLUDES_PATH . self::SnippetPath . $SnippetDir);
		while(false !== ($_entry = $_dir->read())){

			// ignore files . and ..
			if($_entry === '.' || $_entry === '..'){
				// ignore these
				// get the snippets by file if extension is xml
			} elseif(!is_dir(WE_INCLUDES_PATH . self::SnippetPath . $SnippetDir . '/' . $_entry) && substr_compare($_entry, '.xml', -4, 4, true) == 0){
				// get the snippet
				$_snippet = new we_wizard_codeSnippet(WE_INCLUDES_PATH . self::SnippetPath . $SnippetDir . '/' . $_entry);
				$_item = array(
					'type' => 'option',
					'name' => $_snippet->getName(),
					'value' => $SnippetDir . '/' . $_entry
				);
				$Snippets[] = $_item;

				// enter subdirectory only if depth is smaller than 2
			} elseif(is_dir(WE_INCLUDES_PATH . self::SnippetPath . $SnippetDir . '/' . $_entry) && $Depth < 2){

				$information = array();
				$_infoFile = WE_INCLUDES_PATH . self::SnippetPath . $SnippetDir . '/' . $_entry . '/_information.inc.php';
				if(file_exists($_infoFile) && is_file($_infoFile)){
					include ($_infoFile);
				}

				$_foldername = $_entry;
				if(isset($information['foldername'])){
					$_foldername = $information['foldername'];
				}

				$_folder = array(
					'type' => 'optgroup',
					'name' => $_foldername,
					'value' => self::getSnippetsByDir($SnippetDir . '/' . $_entry, $Depth)
				);
				$Snippets[] = $_folder;
			}
		}
		$_dir->close();

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
		$_options = array();

		switch($type){
			case 'custom' :
				$_options = self::getCustomSnippets();
				break;

			default :
				$_options = self::getStandardSnippets();
				break;
		}

		$_select = "<select id=\"codesnippet_" . $type . "\" name=\"codesnippet_" . $type . "\"  size=\"7\" style=\"width:250px; height: 100px; display: none;\" ondblclick=\"YUIdoAjax(this.value);\" onchange=\"WE().layout.button.enable(document, 'btn_direction_right_applyCode')\">\n";
		foreach($_options as $option){
			if($option['type'] === 'optgroup' && count($option['value']) > 0){
				$_select .= '<optgroup label="' . $option['name'] . '">';

				foreach($option['value'] as $optgroupoption){

					if($optgroupoption['type'] === 'option'){
						$_select .= '<option value="' . $optgroupoption['value'] . '">' . $optgroupoption['name'] . '</option>';
					}
				}
				$_select .= '</optgroup>';
			} elseif($option['type'] === 'option'){
				$_select .= '<option value="' . $option['value'] . '">' . $option['name'] . '</option>';
			}
		}
		$_select .= '</select>';

		return $_select;
	}

	/**
	 * get the needed javascript for the codewizard
	 *
	 * @return string
	 */
	public static function getJavascript(){
		return YAHOO_FILES .
			we_html_element::jsScript(JS_DIR . 'wizard_code.js');
	}

}

/**
 * Code Sample
 *
 *
 * echo $CodeWizard->buildDialog();
 *
 */
