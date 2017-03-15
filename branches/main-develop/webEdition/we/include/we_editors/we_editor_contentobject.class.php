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

class we_editor_contentobject extends we_editor_base{

	public function show(){

		$uniquename = md5(uniqid(__FILE__, true));
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0);
//	---> Setting the Content-Type

		$this->charset = (!empty($this->we_doc->elements['Charset']['dat']) ? //	send charset which might be determined in template
			$this->we_doc->elements['Charset']['dat'] : DEFAULT_CHARSET);
		$sort = $this->we_doc->getElement('we_sort');

//	---> Loading the Stylesheets
		$head = '';
		if($this->we_doc->CSS){
			$cssArr = makeArrayFromCSV($this->we_doc->CSS);
			foreach($cssArr as $cs){
				$path = id_to_path($cs);
				if($path){
					$head .= we_html_element::cssLink($path);
				}
			}
		}

		$content = '';

		foreach(array_keys($sort) as $identifier){
			$uniqid = "entry_" . $identifier;

			$content .= '<div id="' . $uniqid . '" class="objectFileElement">
<div id="f' . $uniqid . '" class="default">
<table cellpadding="6" style="float:left;">' .
				$this->we_doc->getFieldHTML($this->jsCmd, $this->we_doc->getElement('wholename' . $identifier), $uniqid) .
				'</table>
		<span class="defaultfont clearfix" style="width:180px;">' .
				we_html_button::create_button('fa:btn_add_field,fa-plus,fa-lg fa-square-o', "javascript:we_cmd('object_insert_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
				we_html_button::create_button(we_html_button::DIRUP, "javascript:we_cmd('object_up_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", '', 0, 0, "", "", false, false, "_" . $identifier) .
				we_html_button::create_button(we_html_button::DIRDOWN, "javascript:we_cmd('object_down_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", '', 0, 0, "", "", false, false, "_" . $identifier) .
				we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('object_delete_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
				'</span>
		</div></div>';
			$this->jsCmd->addCmd('orderContainerAdd', $uniqid);
		}

		return $this->getPage($this->we_doc->getEmptyDefaultFields() . we_html_multiIconBox::_getBoxStart($uniquename) .
				'<div id="orderContainer"></div><div id="' . $uniquename . '_div">
 <table style="margin-left:30px;margin-bottom:15px;" class="default">
 <tr>
 <td style="vertical-align:top"></td>
 <td class="defaultfont">' .
				we_html_button::create_button('fa:btn_add_field,fa-plus,fa-lg fa-square-o', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_entry_at_class','" . we_base_request::_(we_base_request::TRANSACTION, 'we_transaction') . "');") .
				'</td>
 </tr>
 </table>
 </div>' .
				we_html_multiIconBox::_getBoxEnd() .
				$content, we_html_element::jsScript(JS_DIR . '/weOrderContainer.js') . $head, [
				'onload' => "doScrollTo();"
		]);
	}

}
