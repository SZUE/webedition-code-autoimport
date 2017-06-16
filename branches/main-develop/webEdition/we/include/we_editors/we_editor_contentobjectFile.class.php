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
require_once(WE_INCLUDES_PATH . 'we_tag.inc.php');

class we_editor_contentobjectFile extends we_editor_base{
	private $previewMode = false;

	public function setPreview(){
		$this->previewMode = true;
	}

	public function show(){
		$this->charset = (!empty($this->we_doc->Charset) ? //	send charset which might be determined in template
			$this->we_doc->Charset : DEFAULT_CHARSET);

//we_html_tools::headerCtCharset('text/html', $charset);

		$editMode = !$this->previewMode;
		$parts = $this->we_doc->getFieldsHTML($this->jsCmd, $editMode);
		if(is_array($this->we_doc->DefArray)){
			foreach($this->we_doc->DefArray as $n => $v){
				if(is_array($v)){
					if(!empty($v["required"]) && $editMode){
						$parts[] = ["headline" => "",
							"html" => '*' . g_l('global', '[required_fields]'),
							"name" => str_replace('.', '', uniqid('', true)),
						];
						break;
					}
				}
			}
		}
		//$weSuggest = &weSuggest::getInstance();
		$header = we_html_element::jsScript(JS_DIR . '/weOrderContainer.js');

		if($this->we_doc->CSS){
			$cssArr = id_to_path(explode(',', $this->we_doc->CSS), FILE_TABLE, null, true);
			foreach($cssArr as $path){
				$header .= we_html_element::cssLink($path);
			}
		}

		$content = '';
		if($editMode){
			$content .= we_html_multiIconBox::_getBoxStart(g_l('weClass', '[edit]'), md5(uniqid(__FILE__, true)), 30) .
				'<div id="orderContainer"></div>' .
				we_html_multiIconBox::_getBoxEnd();
			foreach($parts as $part){
				$content .= '<div id="' . $part['name'] . '" class="objectFileElement"><div id="f' . $part['name'] . '" class="default defaultfont">' . $part["html"] . '</div></div>';
				$this->jsCmd->addCmd('orderContainerAdd', $part['name']);
			}
		} else {
			$content .= we_gui_SEEM::parseDocument(we_html_multiIconBox::getHTML('', $parts, 30));
		}
		return $this->getPage($content, $header, [
				'onload' => "doScrollTo();"
		]);
	}

}
