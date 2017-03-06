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
$jsCmd = new we_base_jsCmd();
switch(we_base_request::_(we_base_request::STRING, 'do')){
	case 'delete':
		$we_doc->deleteObjects($jsCmd);
		break;
	case 'unpublish':
		$we_doc->publishObjects($jsCmd, false);
		break;
	case 'publish':
		$we_doc->publishObjects($jsCmd);
		break;
	case 'unsearchable':
		$we_doc->setObjectProperty('IsSearchable', false);
		break;
	case 'searchable':
		$we_doc->setObjectProperty('IsSearchable', true);
		break;
	case 'copychar':
		$we_doc->setObjectProperty('Charset');
		break;
	case 'copyws':
		$we_doc->setObjectProperty('Workspaces');
		break;
	case 'copytid':
		$we_doc->setObjectProperty('TriggerID');
		break;
}

we_html_tools::protect();
echo we_html_tools::getHtmlTop('', '', '', $we_doc->getSearchJS() .
		we_editor_script::get() .
		$jsCmd->getCmds(), '<body class="weEditorBody" onunload="doUnload()">' .
		we_html_multiIconBox::getHTML('', [
			['html' => $we_doc->getSearchDialog()],
			['html' => $we_doc->getSearch()],
				]
				, 30) .
		'</body>');
