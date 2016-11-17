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
		$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
		$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 2);

		// if we_cmd 3 is set, take filters of that folder as parent!!
		$idForFilter = we_base_request::_(we_base_request::INT, 'we_cmd', $id, 3);

		if($id == 0){
			t_e('called function with invalid id');
			die();
		}
		$theFolder = new we_folder();
		$theFolder->initByID($id, $table);

		$db = new DB_WE();
		// now get all childs of this folder
	$ct = array(
			we_base_ContentTypes::FOLDER,
			we_base_ContentTypes::WEDOCUMENT,
			we_base_ContentTypes::OBJECT_FILE,
			we_base_ContentTypes::APPLICATION,
			we_base_ContentTypes::AUDIO,
			we_base_ContentTypes::VIDEO,
			we_base_ContentTypes::FLASH,
		);

		$db->query('SELECT ID,ContentType FROM ' . $db->escape($table) . ' WHERE ContentType IN("' . implode('","', $ct) . '" ) AND Path LIKE "' . $theFolder->Path . '/%"');

		$this->alldata = array();

		while($db->next_record()){

			$this->alldata[] = array(
				"folder_id" => $id,
				"table" => $table,
				"idForFilter" => $idForFilter,
				"id" => $db->f("ID"),
				"contenttype" => $db->f("ContentType"),
			);
		}
	}

	function doTask(){
		// getFilter of base-folder
		$theFolder = new we_folder();
		$theFolder->initByID($this->data['idForFilter'], $this->data['table']);

		// getTarget-Document
		$targetDoc = null;
		switch($this->data['contenttype']){
			case "folder":
				$targetDoc = new we_folder();
				break;
			case we_base_ContentTypes::WEDOCUMENT:
				$targetDoc = new we_webEditionDocument();
				break;
			case we_base_ContentTypes::OBJECT_FILE:
				$targetDoc = new we_objectFile();
				break;
			case we_base_ContentTypes::APPLICATION:
			case we_base_ContentTypes::AUDIO:
			case we_base_ContentTypes::VIDEO:
			case we_base_ContentTypes::FLASH:
				$targetDoc = new we_binaryDocument();
				break;
		}
		$targetDoc->initById($this->data["id"], $this->data["table"]);

		$targetDoc->documentCustomerFilter = ($theFolder->documentCustomerFilter ?
				$theFolder->documentCustomerFilter :
				we_customer_documentFilter::getEmptyDocumentCustomerFilter());

		// write filter to target document
		// save filter
		we_customer_documentFilter::saveForModel($targetDoc);
		$targetDoc->rewriteNavigation();

		echo we_html_element::jsElement("parent.setProgressText('copyWeDocumentCustomerFilterText', '" . we_base_util::shortenPath($targetDoc->Path, 55) . "');
parent.setProgress(" . number_format(( ( $this->currentTask ) / $this->numberOfTasks) * 100, 0) . ");");
	}

	function finish(){
		echo we_html_element::jsElement("
parent.setProgressText('copyWeDocumentCustomerFilterText', '" . g_l('modules_customerFilter', '[apply_filter_done]') . "');
parent.setProgress(100);
" . we_message_reporting::getShowMessageCall(g_l('modules_customerFilter', '[apply_filter_done]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
window.setTimeout(parent.top.close, 2000);
");
	}

}
