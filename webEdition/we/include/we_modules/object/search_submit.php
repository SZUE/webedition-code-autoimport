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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');


###### init document #########
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
$we_doc = we_document::initDoc($we_dt);

we_html_tools::protect();
$jsCmd = new we_base_jsCmd();

$we_doc->searchclass->objsearch = we_base_request::_(we_base_request::RAW, 'objsearch', '');
$we_doc->searchclass->objsearchField = we_base_request::_(we_base_request::RAW, 'objsearchField', '');
$we_doc->searchclass->objlocation = we_base_request::_(we_base_request::RAW, 'objlocation', 0);

switch(we_base_request::_(we_base_request::STRING, 'todo')){
	case 'add':
		$we_doc->searchclass->height++;
		$we_doc->searchclass->searchname = $we_doc->searchclass->objsearch;
		$we_doc->searchclass->searchfield = $we_doc->searchclass->objsearchField;
		$we_doc->searchclass->searchlocation = $we_doc->searchclass->objlocation;
		$we_doc->searchclass->start = 0;
		$we_doc->searchclass->searchstart = 0;
		$we_doc->searchclass->setLimit();

		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		$jsCmd->addCmd('we_cmd', ['switch_edit_page', (isset($go) ? $go : $we_doc->EditPageNr )]);
		break;
	case 'delete':
		if($we_doc->searchclass->height == 0){
			$we_doc->searchclass->objsearch = '';
			$we_doc->searchclass->objsearchField = '';
			$we_doc->searchclass->objlocation = '';
		} else {
			$we_doc->searchclass->removeFilter(we_base_request::_(we_base_request::INT, 'position'));
		}

		$we_doc->searchclass->searchname = $we_doc->searchclass->objsearch;
		$we_doc->searchclass->searchfield = $we_doc->searchclass->objsearchField;
		$we_doc->searchclass->searchlocation = $we_doc->searchclass->objlocation;

		$we_doc->searchclass->start = 0;
		$we_doc->searchclass->searchstart = 0;
		$we_doc->searchclass->setLimit();

		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

		$jsCmd->addCmd('we_cmd', ['switch_edit_page', (isset($go) ? $go : $we_doc->EditPageNr )]);

		break;
	case 'search':
		$we_doc->searchclass->searchname = $we_doc->searchclass->objsearch;
		$we_doc->searchclass->searchfield = $we_doc->searchclass->objsearchField;
		$we_doc->searchclass->searchlocation = $we_doc->searchclass->objlocation;
		$we_doc->searchclass->start = 0;
		$we_doc->searchclass->searchstart = 0;
		$we_doc->searchclass->setLimit();
		$we_doc->SearchStart = 0;
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		$jsCmd->addCmd('we_cmd', ['switch_edit_page', (isset($go) ? $go : $we_doc->EditPageNr )]);

		break;
	case 'changemeta':
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		$jsCmd->addCmd('we_cmd', ['reload_editpage']);
		break;
	case 'changedate':
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		$jsCmd->addCmd('we_cmd', ['reload_editpage']);
		break;
	case 'changecheckbox':
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		$jsCmd->addCmd('we_cmd', ['reload_editpage']);
		break;
	default:
		if(($objsf = we_base_request::_(we_base_request::STRING, 'obj_searchField')) !== false){
			//echo $obj_searchField."-_".$obj_search;
			$we_doc->searchclass->height = 0;
			$we_doc->searchclass->show = 'AB';
			$go = we_base_constants::WE_EDITPAGE_FIELDS;
			$obj_search = we_base_request::_(we_base_request::STRING, 'obj_search');
			$we_doc->searchclass->searchname = [0 => $obj_search];
			$we_doc->searchclass->searchfield = [0 => $objsf];
			$we_doc->searchclass->searchlocation = ((!empty($objlocation)) ? [0 => $objlocation] : [0 => 'CONTAIN']);
			$we_doc->searchclass->start = 0;
			$we_doc->searchclass->searchstart = 0;
			$we_doc->searchclass->setLimit();

			$we_doc->searchclass->objsearch = [0 => $obj_search];
			$we_doc->searchclass->objsearchField = [0 => $objsf];
			$we_doc->searchclass->objlocation = ((!empty($objlocation)) ? [0 => $objlocation] : [0 => 'CONTAIN']);

			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

			$jsCmd->addCmd('setStart', 0);
			$jsCmd->addCmd('we_cmd', ['switch_edit_page', $go]);
		}
}
echo we_html_tools::getHtmlTop('', '', '', $jsCmd->getCmds(), we_html_element::htmlBody());
