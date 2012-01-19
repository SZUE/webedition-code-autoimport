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


include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_inc_min.inc.php');

$yuiSuggest =& weSuggest::getInstance();

//	send charset, if one is set:
if(isset($we_doc->elements["Charset"]["dat"]) && $we_doc->elements["Charset"]["dat"] && $we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES ){
	we_html_tools::headerCtCharset('text/html',$we_doc->elements["Charset"]["dat"]);
}

we_html_tools::htmlTop('',isset($we_doc->elements["Charset"]["dat"])?'':'');
echo we_html_element::jsScript(JS_DIR.'windows.js');
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_editors/we_editor_script.inc.php");
print STYLESHEET; ?>
	</head>
	<body class="weEditorBody" onUnload="doUnload()">
		<form name="we_form" method="post" onSubmit="return false;"><?php $we_doc->pHiddenTrans(); ?>
		<!-- <table cellpadding="6" cellspacing="0" border="0"><tr><td> -->
<?php
	$implementYuiAC = false;
	switch($we_doc->ContentType){
		case "folder":
			include($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_templates/we_folder_properties.inc.php");
			$implementYuiAC = true;
			break;
		case "text/webedition":
			include($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_templates/we_webedition_properties.inc.php");
			break;
		case "text/xml":
		case "text/css":
		case "text/js":
		case "text/htaccess":
		case "text/plain":
			include($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_templates/we_textfile_properties.inc.php");
			break;
		case "text/html":
			include($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_templates/we_htmlfile_properties.inc.php");
			break;
		case "text/weTmpl":
			include($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_templates/we_template_properties.inc.php");
			break;
		case "image/*":
			include($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_templates/we_image_properties.inc.php");
			break;
		case "application/x-shockwave-flash":
			include($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_templates/we_flash_properties.inc.php");
			break;
 		case "video/quicktime":
			include($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_templates/we_quicktime_properties.inc.php");
			break;
        case "application/*":
			include($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_templates/we_other_properties.inc.php");
			break;
		default:

			$moduleDir = we_getModuleNameByContentType($we_doc->ContentType);

			if($moduleDir != ""){
				$moduleDir .= "/";
			}

			if(file_exists($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_modules/" . $moduleDir . "we_".$we_doc->ContentType."_properties.inc.php")){
				include($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_modules/" . $moduleDir . "we_".$we_doc->ContentType."_properties.inc.php");
			}else{
				exit("Can NOT include property File");
			}
	}

?>
		<!--</td></tr></table> -->
		</form>
<?php
echo $yuiSuggest->getYuiCssFiles();
echo $yuiSuggest->getYuiCss();

echo $yuiSuggest->getYuiJsFiles();
echo $yuiSuggest->getYuiJs();
?>
	</body>
</html>