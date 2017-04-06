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
abstract class we_dialog_addToCollection{

	public static function getDialog(){
		$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2);
		$targetCollection = we_base_request::_(we_base_request::INT, 'we_cmd', '', 3);
		$targetCollectionPath = we_base_request::_(we_base_request::URL, 'we_cmd', '', 4);
		$insertIndex = we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 5);
		$insertPos = ($pos = we_base_request::_(we_base_request::INT, 'we_cmd', -1, 6)) !== -1 ? $pos : (we_base_request::_(we_base_request::INT, 'we_targetInsertPos', -1));

		/* FIXME: adapt when collection perms are implemented
		 *
		  if(($table == TEMPLATES_TABLE && !we_base_permission::hasPerm("MOVE_TEMPLATE")) ||
		  ($table == FILE_TABLE && !we_base_permission::hasPerm("MOVE_DOCUMENT")) ||
		  (defined('OBJECT_TABLE') && $table == OBJECT_TABLE && !we_base_permission::hasPerm("MOVE_OBJECTFILES"))){
		  we_base_permission::noPermDialog(g_l('alert', '[no_perms]'));
		  }
		 *
		 */
		$weSuggest = & we_gui_suggest::getInstance();
		$cmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);

		$ws_Id = get_def_ws($table);
		if($ws_Id){
			$ws_path = id_to_path($ws_Id, $table);
		} else {
			$ws_Id = 0;
			$ws_path = '/';
		}

		$textname = 'we_targetname';
		$idname = 'we_target';
		$weSuggest->setAcId('Dir');
		$weSuggest->setContentType(we_base_ContentTypes::FOLDER . ',' . we_base_ContentTypes::COLLECTION);
		$weSuggest->setInput($textname, $targetCollectionPath);
		$weSuggest->setMaxResults(4);
		$weSuggest->setRequired(true);
		$weSuggest->setResult($idname, $targetCollection);
		$weSuggest->setSelector(we_gui_suggest::DocSelector);
		$weSuggest->setTable(VFILE_TABLE);
		$weSuggest->setWidth(273);
		//$cmd1 = 'top.treeheader.document.we_form.elements.' . $idname . '.value';

		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document', document.we_form.elements." . $idname . ".value,'" . VFILE_TABLE . "','" . $idname . "','" . $textname . "','','',0)"), 6);
		$weSuggest->setAdditionalButton(we_html_button::create_button('fa:btn_add_collection,fa-plus,fa-lg fa-archive', "javascript:we_cmd('edit_new_collection','write_back_to_opener," . $idname . "," . $textname . "','',-1,'" . stripTblPrefix($table) . "');", '', 0, 0, "", "", false, false), 0);

		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'weAddToCollection.js', '', ['id' => 'loadVarWeAddToCollection', 'data-init' => setDynamicVar([
					'table' => $table,
					'targetInsertIndex' => $insertIndex,
					'targetInsertPosition' => $insertPos,
			])]), we_html_element::htmlBody([
				'class' => 'weTreeHeaderAddToCollection'
				], '<form name="we_form" method="post" onsubmit="return false">' .
				we_html_element::htmlHiddens(['we_targetTransaction' => '',
					'we_targetInsertPos' => $insertPos,
					'sel' => '']) . '
<div style="width:440px;">
<h1 class="big" style="padding:0px;margin:0px;">' . g_l('weClass', '[collection][add]') . '</h1>
<p class="small"><span class="middlefont" style="padding-right:5px;padding-bottom:10px;">' . g_l('weClass', '[collection][add_help]') . '</span>
<p style="margin:0px 0px 10px 0px;padding:0px;">' . $weSuggest->getHTML() . we_html_forms::checkboxWithHidden(1, 'InsertRecursive', g_l('weClass', '[collection][insertRecursive]')) . '</p></p>
<div>' . we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:weAddToCollection.press_ok_add();"), "", we_html_button::create_button('quit_addToCollection', "javascript:we_cmd('exit_addToCollection','','" . $table . "')"), 10, "left") . '</div></div>
 </form>')
		);
	}

}
