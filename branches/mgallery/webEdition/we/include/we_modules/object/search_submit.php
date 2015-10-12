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
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

we_html_tools::protect();

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

		echo we_html_element::jsElement('top.we_cmd("switch_edit_page","' . (isset($go) ? $go : $we_doc->EditPageNr ) . '");');
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

		echo we_html_element::jsElement('top.we_cmd("switch_edit_page","' . (isset($go) ? $go : $we_doc->EditPageNr ) . '");');
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
		echo we_html_element::jsElement('top.we_cmd("switch_edit_page","' . (isset($go) ? $go : $we_doc->EditPageNr ) . '");');
		break;
	case 'changemeta':
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		echo we_html_element::jsElement('top.we_cmd("reload_editpage");');
		break;
	case 'changedate':
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		echo we_html_element::jsElement('top.we_cmd("reload_editpage");');
		break;
	case 'changecheckbox':
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		echo we_html_element::jsElement('top.we_cmd("reload_editpage");');
		break;
	case 'quickchangemeta':
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

		echo we_html_element::jsElement('top.weEditorFrameController.getDocumentReferenceByTransaction("' . $_SESSION['weS']['we_data'][$we_transaction] . '").frames[3].location.replace(WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?we_cmd[0]=load_edit_footer&we_transaction=' . $we_transaction . '&we_cmd[7]=' . we_base_request::_(we_base_request::STRING, "obj_searchField") . '&we_cmd[6]=' . $obj_search . '");');
		break;
	case 'quickchangedate':
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

		echo we_html_element::jsElement('top.weEditorFrameController.getDocumentReferenceByTransaction("' . $_SESSION['weS']['we_data'][$we_transaction] . '").frames[3].location.replace(WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?we_cmd[0]=load_edit_footer&we_transaction=' . $we_transaction . '&we_cmd[7]=' . we_base_request::_(we_base_request::STRING, "obj_searchField") . '&we_cmd[6]=' . $obj_search . '");');
		break;
	case 'quickcheckbox':
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

		echo we_html_element::jsElement('top.weEditorFrameController.getDocumentReferenceByTransaction("' . $_SESSION['weS']['we_data'][$we_transaction] . '").frames[3].location.replace(WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?we_cmd[0]=load_edit_footer&we_transaction=' . $we_transaction . '&we_cmd[7]=' . we_base_request::_(we_base_request::STRING, "obj_searchField") . '&we_cmd[6]=' . $obj_search . '");');
		break;
	default:
		if(($objsf = we_base_request::_(we_base_request::STRING, 'obj_searchField')) !== false){
			//echo $obj_searchField."-_".$obj_search;
			$we_doc->searchclass->height = 0;
			$we_doc->searchclass->show = 'AB';
			$go = we_base_constants::WE_EDITPAGE_CFWORKSPACE;
			$obj_search = we_base_request::_(we_base_request::STRING, 'obj_search');
			$we_doc->searchclass->searchname = array(0 => $obj_search);
			$we_doc->searchclass->searchfield = array(0 => $objsf);
			$we_doc->searchclass->searchlocation = ((!empty($objlocation)) ? array(0 => $objlocation) : array(0 => 'CONTAIN'));
			$we_doc->searchclass->start = 0;
			$we_doc->searchclass->searchstart = 0;
			$we_doc->searchclass->setLimit();

			$we_doc->searchclass->objsearch = array(0 => $obj_search);
			$we_doc->searchclass->objsearchField = array(0 => $objsf);
			$we_doc->searchclass->objlocation = ((!empty($objlocation)) ? array(0 => $objlocation) : array(0 => 'CONTAIN'));

			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

			echo we_html_element::jsElement('
					if (top.weEditorFrameController.getDocumentReferenceByTransaction("' . $_SESSION['weS']['we_data'][$we_transaction] . '").frames[1].document.we_form && top.weEditorFrameController.getDocumentReferenceByTransaction("' . $_SESSION['weS']['we_data'][$we_transaction] . '").frames[1].document.we_form.elements.SearchStart) {
						top.weEditorFrameController.getDocumentReferenceByTransaction("' . $_SESSION['weS']['we_data'][$we_transaction] . '").frames[1].document.we_form.elements.SearchStart.value = 0;
					}
					top.we_cmd("switch_edit_page","' . $go . '");
			');
		}
}
