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
if(we_base_request::_(we_base_request::BOOL, "startCopy")){ // start the fragment
	$theFrag = new we_customer_copyWeDocumentFilterFrag('copyWeDocumentCustomerFilter', 1, 200);
} else { // print the window
	// if any childs of the folder are open - bring message to close them
	// REQUEST[we_cmd][1] = id of folder
	// REQUEST[we_cmd][2] = table
	$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
	$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 2);

	// if we_cmd 3 is set, take filters of that folder as parent!!
	$idForFilter = we_base_request::_(we_base_request::INT, 'we_cmd', $id, 3);


	$theFolder = new we_folder();
	$theFolder->initByID($id, $table);

	// now get all childs of this folder
	$db = new DB_WE();

	$db->query('SELECT ID,ContentType FROM ' . $db->escape($table) . ' WHERE ContentType IN("folder","' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::OBJECT_FILE . '" ) AND PATH LIKE "' . $theFolder->Path . '/%"');

	$allChildsJS = 'var _allChilds = {};';

	while($db->next_record()){
		$allChildsJS .= "_allChilds['id_" . $db->f("ID") . "'] = '" . $db->f("ContentType") . "';";
	}

	$pb = new we_progressBar(0, true);
	$pb->addText('&nbsp;', 0, 'copyWeDocumentCustomerFilterText');
	$pb->setStudWidth(10);
	$pb->setStudLen(300);
	$js = $pb->getJSCode();

	// image and progressbar
	$content = $pb->getHTML();

	$buttonBar = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");
	$cmd3 = we_base_request::_(we_base_request::INT, 'we_cmd', false, 3);

	$iframeLocation = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . we_base_request::_(we_base_request::RAW, 'we_cmd', '', 0) . '&we_cmd[1]=' . we_base_request::_(we_base_request::INT, 'we_cmd', '', 1) . "&we_cmd[2]=" . we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2) . ($cmd3 !== false ? "&we_cmd[3]=" . $cmd3 : "" ) . '&startCopy=1';

	echo we_html_tools::getHtmlTop(g_l('modules_customerFilter', '[apply_filter]')) .
	STYLESHEET .
	we_html_element::jsElement('
function checkForOpenChilds() {
	' . $allChildsJS . '
	var _openChilds = [];
	var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();

	for (frameId in _usedEditors) {

		// table muss FILE_TABLE sein
		if ( _usedEditors[frameId].getEditorEditorTable() == "' . $table . '" ) {
			if ( _allChilds["id_" + _usedEditors[frameId].getEditorDocumentId()] && _allChilds["id_" + _usedEditors[frameId].getEditorDocumentId()] == _usedEditors[frameId].getEditorContentType() ) {
				_openChilds.push( frameId );
			}
		}
	}

	if (_openChilds.length) {
		if ( confirm("' . g_l('modules_customerFilter', '[apply_filter_cofirm_close]') . '") ) {
			// close all
			for (i=0;i<_openChilds.length;i++) {
				_usedEditors[_openChilds[i]].setEditorIsHot(false);
				WE().layout.weEditorFrameController.closeDocument(_openChilds[i]);
			}
		} else {
			window.close();
			return;
		}

	}
	document.getElementById("iframeCopyWeDocumentCustomerFilter").src="' . $iframeLocation . '";
}');
	echo '</head><body class="weDialogBody" onload="checkForOpenChilds()">' .
	$js . we_html_tools::htmlDialogLayout($content, g_l('modules_customerFilter', '[apply_filter]'), $buttonBar) .
	'<div style="display: none;"> <!-- hidden -->
	<iframe style="position: absolute; top: 150; height: 1px; width: 1px;" name="iframeCopyWeDocumentCustomerFilter" id="iframeCopyWeDocumentCustomerFilter" src="about:blank"></iframe>
</div>
</html>';
}