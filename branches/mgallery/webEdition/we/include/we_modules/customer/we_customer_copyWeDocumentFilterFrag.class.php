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
class we_customer_copyWeDocumentFilterFrag extends we_fragment_base{

	function init(){

		// init the fragment
		// REQUEST[we_cmd][1] = id of folder
		// REQUEST[we_cmd][2] = table
		$_id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
		$_table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 2);

		// if we_cmd 3 is set, take filters of that folder as parent!!
		$_idForFilter = we_base_request::_(we_base_request::INT, 'we_cmd', $_id, 3);

		if($_id == 0){
			t_e('called function with invalid id');
			die();
		}
		$_theFolder = new we_folder();
		$_theFolder->initByID($_id, $_table);

		$_db = new DB_WE();
		// now get all childs of this folder

		$_db->query('SELECT *,ID,ContentType FROM ' . $_db->escape($_table) . ' WHERE	ContentType IN("folder","' . we_base_ContentTypes::WEDOCUMENT . '","objectFile") AND PATH LIKE "' . $_theFolder->Path . '/%"');

		$this->alldata = array();

		while($_db->next_record()){

			$this->alldata[] = array(
				"folder_id" => $_id,
				"table" => $_table,
				"idForFilter" => $_idForFilter,
				"id" => $_db->f("ID"),
				"contenttype" => $_db->f("ContentType"),
			);
		}
	}

	function doTask(){

		// getFilter of base-folder
		$_theFolder = new we_folder();
		$_theFolder->initByID($this->data["idForFilter"], $this->data["table"]);

		// getTarget-Document
		$_targetDoc = null;
		switch($this->data["contenttype"]){
			case "folder":
				$_targetDoc = new we_folder();
				break;
			case we_base_ContentTypes::WEDOCUMENT:
				$_targetDoc = new we_webEditionDocument();
				break;
			case "objectFile":
				$_targetDoc = new we_objectFile();
				break;
		}
		$_targetDoc->initById($this->data["id"], $this->data["table"]);

		$_targetDoc->documentCustomerFilter = ($_theFolder->documentCustomerFilter ?
				$_theFolder->documentCustomerFilter :
				we_customer_documentFilter::getEmptyDocumentCustomerFilter());

		// write filter to target document
		// save filter
		$_targetDoc->documentCustomerFilter->saveForModel($_targetDoc);
		$_targetDoc->rewriteNavigation();

		echo we_html_element::jsElement("parent.setProgressText('copyWeDocumentCustomerFilterText', '" . we_util_Strings::shortenPath($_targetDoc->Path, 55) . "');
			parent.setProgress(" . number_format(( ( $this->currentTask ) / $this->numberOfTasks) * 100, 0) . ");");
	}

	function finish(){

		echo we_html_element::jsElement("
			parent.setProgressText('copyWeDocumentCustomerFilterText', '" . g_l('modules_customerFilter', '[apply_filter_done]') . "');
			parent.setProgress(100);
			" . we_message_reporting::getShowMessageCall(g_l('modules_customerFilter', '[apply_filter_done]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
			window.setTimeout('parent.top.close()', 2000);
		");
	}

}
