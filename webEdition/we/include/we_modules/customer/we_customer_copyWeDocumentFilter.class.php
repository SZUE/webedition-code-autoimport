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
abstract class we_customer_copyWeDocumentFilter{

	public static function getDialog(){
		if(we_base_request::_(we_base_request::BOOL, "startCopy")){ // start the fragment
			$theFrag = new we_customer_copyWeDocumentFilterFrag('copyWeDocumentCustomerFilter', 5);
			return;
		}

// print the window
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
		$ct = [we_base_ContentTypes::FOLDER,
			we_base_ContentTypes::WEDOCUMENT,
			we_base_ContentTypes::OBJECT_FILE,
			we_base_ContentTypes::APPLICATION,
			we_base_ContentTypes::AUDIO,
			we_base_ContentTypes::VIDEO,
			we_base_ContentTypes::FLASH,
		];

		$db->query('SELECT ID,ContentType FROM ' . $db->escape($table) . ' WHERE ContentType IN("' . implode('","', $ct) . '" ) AND Path LIKE "' . $theFolder->Path . '/%"');

		$allChildsJS = $db->getAllFirst(false);

		$pb = new we_progressBar(0, 300);
		$pb->addText('&nbsp;', we_progressBar::TOP, 'copyWeDocumentCustomerFilterText');

		// image and progressbar
		$content = $pb->getHTML();

		$buttonBar = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");
		$cmd3 = we_base_request::_(we_base_request::INT, 'we_cmd', false, 3);

		$iframeLocation = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . we_base_request::_(we_base_request::RAW, 'we_cmd', '', 0) . '&we_cmd[1]=' . we_base_request::_(we_base_request::INT, 'we_cmd', '', 1) . "&we_cmd[2]=" . we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2) . ($cmd3 !== false ? "&we_cmd[3]=" . $cmd3 : "" ) . '&startCopy=1';

		echo we_html_tools::getHtmlTop(g_l('modules_customerFilter', '[apply_filter]'), '', '', we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/copyWeDocumentFilter.js', '', ['id' => 'loadVarFilter', 'data-filter' => setDynamicVar([
						'allChilds' => $allChildsJS,
						'table' => $table,
						'question' => g_l('modules_customerFilter', '[apply_filter_cofirm_close]'),
						'redirect' => $iframeLocation
			])]) .
				we_progressBar::getJSCode(), we_html_element::htmlBody([
					'class' => "weDialogBody", 'onload' => "checkForOpenChilds()"
						], we_html_tools::htmlDialogLayout($content, g_l('modules_customerFilter', '[apply_filter]'), $buttonBar) .
						'<div style="display: none;">
	<iframe style="position: absolute; top: 150; height: 1px; width: 1px;" name="iframeCopyWeDocumentCustomerFilter" id="iframeCopyWeDocumentCustomerFilter" src="about:blank"></iframe>
</div>'));
	}

}
