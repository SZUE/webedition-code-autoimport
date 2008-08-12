<?php

// +----------------------------------------------------------------------+
// | webEdition                                                           |
// +----------------------------------------------------------------------+
// | PHP version 4.1.0 or greater                                         |
// +----------------------------------------------------------------------+
// | Copyright (c) 2000 - 2007 living-e AG                                |
// +----------------------------------------------------------------------+
//


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_html_tools.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/"."weSuggest.class.inc.php");

$yuiSuggest =& weSuggest::getInstance();

//	send charset, if one is set:
if(isset($we_doc->elements["Charset"]["dat"]) && $we_doc->elements["Charset"]["dat"] && $we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES ){
	header("Content-Type: text/html; charset=" . $we_doc->elements["Charset"]["dat"]);
}

htmlTop();
?>
<script language="JavaScript" type="text/javascript" src="<?php print JS_DIR ?>windows.js"></script>
<?php include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_editors/we_editor_script.inc.php"); ?>
<?php print STYLESHEET; ?>
	</head>
	<body class="weEditorBody" onUnload="doUnload()">
		<form name="we_form" method="post" onsubmit="return false;"><?php $we_doc->pHiddenTrans(); ?>
			<table cellpadding="6" cellspacing="0" border="0">
<?php
	$implementYuiAC = false;
	switch($we_doc->ContentType){
		case "folder":
			include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_templates/we_folder_properties.inc.php");
			$implementYuiAC = true;
			break;
		case "text/webedition":
			include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_templates/we_webedition_properties.inc.php");
			break;
		case "text/xml":
		case "text/css":
		case "text/js":
		case "text/plain":
			include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_templates/we_textfile_properties.inc.php");
			break;
		case "text/html":
			include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_templates/we_htmlfile_properties.inc.php");
			break;
		case "text/weTmpl":
			include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_templates/we_template_properties.inc.php");
			break;
		case "image/*":
			include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_templates/we_image_properties.inc.php");
			break;
		case "application/x-shockwave-flash":
			include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_templates/we_flash_properties.inc.php");
			break;
 		case "video/quicktime":
			include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_templates/we_quicktime_properties.inc.php");
			break;
        case "application/*":
			include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_templates/we_other_properties.inc.php");
			break;
		default:

			$moduleDir = we_getModuleNameByContentType($we_doc->ContentType);

			if($moduleDir != ""){
				$moduleDir .= "/";
			}

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_modules/" . $moduleDir . "we_".$we_doc->ContentType."_properties.inc.php")){
				include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_modules/" . $moduleDir . "we_".$we_doc->ContentType."_properties.inc.php");
			}else{
				exit("Can NOT include property File");
			}
	}

?>
			</table>
		</form>
<?php
echo $yuiSuggest->getYuiCssFiles();
echo $yuiSuggest->getYuiCss();

echo $yuiSuggest->getYuiJsFiles();
echo $yuiSuggest->getYuiJs();
?>
	</body>
</html>