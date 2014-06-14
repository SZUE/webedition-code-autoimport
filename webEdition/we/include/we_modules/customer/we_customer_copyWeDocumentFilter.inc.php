<?php

/**
 * webEdition CMS
 *
 * $Rev: 7330 $
 * $Author: mokraemer $
 * $Date: 2014-03-02 18:34:54 +0100 (So, 02. Mär 2014) $
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
if(we_base_request::_(we_base_request::BOOL,"startCopy")){ // start the fragment
	$_theFrag = new we_customer_copyWeDocumentFilterFrag("copyWeDocumentCustomerFilter", 1, 200);
} else { // print the window
	// if any childs of the folder are open - bring message to close them
	// REQUEST[we_cmd][1] = id of folder
	// REQUEST[we_cmd][2] = table
	$_id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
	$_table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 2);

	// if we_cmd 3 is set, take filters of that folder as parent!!
	$_idForFilter = we_base_request::_(we_base_request::INT, 'we_cmd', $_id, 3);


	$_theFolder = new we_folder();
	$_theFolder->initByID($_id, $_table);

	// now get all childs of this folder
	$_db = new DB_WE();

	$_db->query('SELECT ID,ContentType FROM ' . $_db->escape($_table) . ' WHERE ContentType IN("folder","' . we_base_ContentTypes::WEDOCUMENT . '","objectFile" ) AND PATH LIKE "' . $_theFolder->Path . '/%"');

	$_allChildsJS = 'var _allChilds = new Object();';

	while($_db->next_record()){
		$_allChildsJS .= "_allChilds['id_" . $_db->f("ID") . "'] = '" . $_db->f("ContentType") . "';";
	}
	$_js = 'var _openChilds = Array();
			var _usedEditors = top.opener.top.weEditorFrameController.getEditorsInUse();

			for (frameId in _usedEditors) {

				// table muss FILE_TABLE sein
				if ( _usedEditors[frameId].getEditorEditorTable() == "' . $_table . '" ) {
					if ( _allChilds["id_" + _usedEditors[frameId].getEditorDocumentId()] && _allChilds["id_" + _usedEditors[frameId].getEditorDocumentId()] == _usedEditors[frameId].getEditorContentType() ) {
						_openChilds.push( frameId );
					}
				}
			}';

	$pb = new we_progressBar(0, 0, true);
	$pb->addText("&nbsp;", 0, "copyWeDocumentCustomerFilterText");
	$pb->setStudWidth(10);
	$pb->setStudLen(300);
	$js = $pb->getJS() . $pb->getJSCode();

	// image and progressbar
	$content = $pb->getHTML();

	$buttonBar = we_html_button::create_button("cancel", "javascript:top.close();");

	$_iframeLocation = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . $_REQUEST['we_cmd'][0] . '&we_cmd[1]=' . $_REQUEST['we_cmd'][1] . "&we_cmd[2]=" . $_REQUEST['we_cmd'][2] . (isset($_REQUEST['we_cmd'][3]) ? "&we_cmd[3]=" . $_REQUEST['we_cmd'][3] : "" ) . '&startCopy=1';

	echo we_html_tools::getHtmlTop(g_l('modules_customerFilter', '[apply_filter]')) .
	STYLESHEET .
	we_html_element::jsElement("
		function checkForOpenChilds() {

			$_allChildsJS
			$_js

			if (_openChilds.length) {
				if ( confirm(\"" . g_l('modules_customerFilter', "[apply_filter_cofirm_close]") . "\") ) {
					// close all
					for (i=0;i<_openChilds.length;i++) {
						_usedEditors[_openChilds[i]].setEditorIsHot(false);
						top.opener.top.weEditorFrameController.closeDocument(_openChilds[i]);

					}

				} else {
					window.close();
					return;
				}

			}
			document.getElementById(\"iframeCopyWeDocumentCustomerFilter\").src=\"" . $_iframeLocation . "\";
		}

	");
	echo '</head><body class="weDialogBody" onload="checkForOpenChilds()">' .
	$js . we_html_tools::htmlDialogLayout($content, g_l('modules_customerFilter', "[apply_filter]"), $buttonBar) .
	'<div style="display: none;"> <!-- hidden -->
	<iframe style="position: absolute; top: 150; height: 1px; width: 1px;" name="iframeCopyWeDocumentCustomerFilter" id="iframeCopyWeDocumentCustomerFilter" src="about:blank"></iframe>
</div>
</html>';
}