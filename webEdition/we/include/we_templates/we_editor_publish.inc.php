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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
we_html_tools::protect();
echo we_html_element::jsElement('
var _EditorFrame = top.weEditorFrameController.getEditorFrameByTransaction("' . $GLOBALS['we_transaction'] . '");
var _EditorFrameDocumentRef = _EditorFrame.getDocumentReference();' .
		$we_JavaScript . ';top.toggleBusy(0);' .
		($we_responseText ?
				we_message_reporting::getShowMessageCall($we_responseText, $we_responseTextType) :
				'') .
		(isset($_REQUEST['we_cmd'][5]) && $_REQUEST['we_cmd'][5] != '' ?
				$_REQUEST['we_cmd'][5] : ''
		)
);
