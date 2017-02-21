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
switch(we_base_request::_(we_base_request::STRING, 'do')){
	case 'delete':
		$javascript = $we_doc->deleteObjects();
		break;
	case 'unpublish':
		$javascript = $we_doc->publishObjects(false);
		break;
	case 'publish':
		$javascript = $we_doc->publishObjects();
		break;
	case 'unsearchable':
		$javascript = $we_doc->setObjectProperty('IsSearchable', false);
		break;
	case 'searchable':
		$javascript = $we_doc->setObjectProperty('IsSearchable', true);
		break;
	case 'copychar':
		$javascript = $we_doc->setObjectProperty('Charset');
		break;
	case 'copyws':
		$javascript = $we_doc->setObjectProperty('Workspaces');
		break;
	case 'copytid':
		$javascript = $we_doc->setObjectProperty('TriggerID');
		break;
}

we_html_tools::protect();
echo we_html_tools::getHtmlTop('', '', '', $we_doc->getSearchJS() .
		(isset($javascript) ? we_html_element::jsElement($javascript) : '') .
		we_editor_script::get(), '<body class="weEditorBody" onunload="doUnload()">' .
		we_html_multiIconBox::getHTML('', [
			['html' => $we_doc->getSearchDialog()],
			['html' => $we_doc->getSearch()],
				]
				, 30) .
		'</body>');
