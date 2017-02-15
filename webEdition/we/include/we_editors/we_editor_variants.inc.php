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
//	send charset, if one is set:
if(!empty($we_doc->elements["Charset"]["dat"]) && $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES){
	we_html_tools::headerCtCharset('text/html', $we_doc->elements["Charset"]["dat"]);
}
$jsCmd = new we_base_jsCmd();
echo we_html_tools::getHtmlTop();
require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
switch($we_doc->ContentType){
	case we_base_ContentTypes::WEDOCUMENT:
	case we_base_ContentTypes::OBJECT_FILE:
		$inner = we_base_variants::edit($jsCmd, $we_doc->ContentType == we_base_ContentTypes::OBJECT_FILE, we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0), $we_doc);
		break;

	case we_base_ContentTypes::TEMPLATE:
		$inner = we_base_variants::getTemplateCode($we_doc);
		break;
	default:
		$inner = $we_doc->ContentType . ' not available (' . __FILE__ . ' ) ';
		break;
}
echo $jsCmd->getCmds();
?>
</head>
<body class="weEditorBody" onunload="doUnload()">
	<form name="we_form" method="post" onsubmit="return false;"><?php
		echo we_class::hiddenTrans() .
		$inner .
		we_html_element::htmlHidden("we_complete_request", 1);
		?>
	</form>
</body>
</html>