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

	switch(we_base_request::_(we_base_request::STRING,'do')){
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
			$javascript = $we_doc->searchableObjects(false);
			break;
		case 'searchable':
			$javascript = $we_doc->searchableObjects();
			break;
		case 'copychar':
			$javascript = $we_doc->copyCharsetfromClass();
			break;
		case 'copyws':
			$javascript = $we_doc->copyWSfromClass();
			break;
		case 'copytid':
			$javascript = $we_doc->copyTIDfromClass();
			break;
	}

we_html_tools::protect();

echo we_html_tools::getHtmlTop() .
 we_html_element::jsScript(JS_DIR . 'windows.js') .
 $we_doc->getSearchJS() .
 (isset($javascript) ? we_html_element::jsElement($javascript) : '');

require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

echo STYLESHEET .
 '</head>
<body class="weEditorBody" onunload="doUnload()">';


$_parts = array(
	array('html' => $we_doc->getSearchDialog()),
	array('html' => $we_doc->searchProperties())
);

echo we_html_multiIconBox::getHTML('', $_parts, 30, '', -1, '', '', false) . '
</body>
</html>';
