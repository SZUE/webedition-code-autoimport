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

echo we_html_tools::getHtmlTop();
require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
echo we_html_element::jsScript(JS_DIR . 'utils/we_cmd_encode.js') .
 we_html_element::jsScript(JS_DIR . 'we_editor_collectionContent.js') .
 we_html_element::jsElement('
weCollectionEdit.we_doc = {
	ID: "' . $GLOBALS['we_doc']->ID . '",
	Path: "' . $GLOBALS['we_doc']->Path . '",
	name: "' . $GLOBALS['we_doc']->Name . '",
	remTable: "' . $GLOBALS['we_doc']->getRemTable() . '",
	remCT: "' . $GLOBALS['we_doc']->getRemCT() . '",
	realRemCT: "' . $GLOBALS['we_doc']->getRealRemCT() . '",
	remClass: "' . $GLOBALS['we_doc']->getRemClass() . '",
	defaultDir: ' . $GLOBALS['we_doc']->DefaultDir . '
};

weCollectionEdit.g_l = {
	element_not_set: "' . g_l('weClass', '[collection][notSet]') . '",
	info_insertion: "' . g_l('weClass', '[collection][infoAddFiles]') . '"

};

// since these props are defined on Properties we can write them here
weCollectionEdit.dd.IsDuplicates = ' . intval($GLOBALS['we_doc']->IsDuplicates) . ';
weCollectionEdit.dd.fillEmptyRows = 1;
weCollectionEdit.view = "grid";
');
?>

</head>
<body class="weEditorBody" style="height:100%;overflow:hidden;" onload="weCollectionEdit.init();">
	<form name="we_form"><?=
		we_class::hiddenTrans() .
		$GLOBALS['we_doc']->formCollection() .
		we_html_element::htmlHidden("we_complete_request", 1);
		?>
	</form>
	<?=
	weSuggest::getYuiFiles() .
	$yuiSuggest->getYuiJs();
	?>
</body>
</html>