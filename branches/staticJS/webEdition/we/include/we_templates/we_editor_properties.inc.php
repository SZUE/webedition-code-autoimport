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
$yuiSuggest = & weSuggest::getInstance();

$charset = ($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES ?
		//	send charset, if one is set:
		$we_doc->getElement('Charset', 'dat', DEFAULT_CHARSET) :
		$GLOBALS['WE_BACKENDCHARSET']);

we_html_tools::headerCtCharset('text/html', $charset);
echo we_html_tools::getHtmlTop('', $charset) .
 we_html_element::jsScript(JS_DIR . 'windows.js');
require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
echo STYLESHEET;
?>
</head>
<body class="weEditorBody" onunload="doUnload()">
	<form name="we_form" method="post" action="" onsubmit="return false;"><?php
echo we_class::hiddenTrans();
$implementYuiAC = false;
switch($we_doc->ContentType){
	case we_base_ContentTypes::FOLDER:
		include(WE_INCLUDES_PATH . 'we_templates/we_folder_properties.inc.php');
		$implementYuiAC = true;
		break;
	case we_base_ContentTypes::WEDOCUMENT:
		include(WE_INCLUDES_PATH . 'we_templates/we_webedition_properties.inc.php');
		break;
	case we_base_ContentTypes::XML:
	case we_base_ContentTypes::CSS:
	case we_base_ContentTypes::JS:
	case we_base_ContentTypes::HTACESS:
	case we_base_ContentTypes::TEXT:
		include(WE_INCLUDES_PATH . 'we_templates/we_textfile_properties.inc.php');
		break;
	case we_base_ContentTypes::HTML:
		include(WE_INCLUDES_PATH . 'we_templates/we_htmlfile_properties.inc.php');
		break;
	case we_base_ContentTypes::TEMPLATE:
		include(WE_INCLUDES_PATH . 'we_templates/we_template_properties.inc.php');
		break;
	case we_base_ContentTypes::IMAGE:
		include(WE_INCLUDES_PATH . 'we_templates/we_image_properties.inc.php');
		break;
	case we_base_ContentTypes::QUICKTIME:
	case we_base_ContentTypes::FLASH:
		include(WE_INCLUDES_PATH . 'we_templates/we_flash_properties.inc.php');
		break;
	case we_base_ContentTypes::VIDEO:
	case we_base_ContentTypes::AUDIO:
	case we_base_ContentTypes::APPLICATION:
		include(WE_INCLUDES_PATH . 'we_templates/we_other_properties.inc.php');
		break;
	default:
		$moduleDir = we_base_moduleInfo::we_getModuleNameByContentType($we_doc->ContentType);
		$moduleDir = $moduleDir ? $moduleDir . '/' : '';

		if(file_exists(WE_MODULES_PATH . $moduleDir . 'we_' . $we_doc->ContentType . '_properties.inc.php')){
			include(WE_MODULES_PATH . $moduleDir . 'we_' . $we_doc->ContentType . '_properties.inc.php');
		} else {
			exit('Can NOT include property File');
		}
}
?>
		<input type="hidden" name="we_complete_request" value="1"/>
	</form>
	<?php
	echo weSuggest::getYuiFiles() .
	$yuiSuggest->getYuiCss() .
	$yuiSuggest->getYuiJs();
	?>
</body>
</html>