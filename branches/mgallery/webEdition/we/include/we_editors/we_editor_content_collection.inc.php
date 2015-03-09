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
echo STYLESHEET .
we_html_element::jsScript(JS_DIR . 'windows.js') .
we_html_element::jsScript(JS_DIR . 'utils/we_cmd_encode.js') .
we_html_element::jsScript(JS_DIR . 'we_editor_collectionContent.js') .
we_html_element::jsElement('
weCollectionEdit.we_doc = {
	ID: "' . $GLOBALS['we_doc']->ID . '",
	name: "' . $GLOBALS['we_doc']->Name. '",
	remTable: "' . $GLOBALS['we_doc']->getRemTable() . '",
	remCT: "' . $GLOBALS['we_doc']->getRemCT() . '",
	remClass: "' . $GLOBALS['we_doc']->getRemClass() . '"
};

weCollectionEdit.we_const = {
	TBL_PREFIX: "' . TBL_PREFIX . '",
	FILE_TABLE: "' . FILE_TABLE . '",
	OBJECT_FILES_TABLE: "' . OBJECT_FILES_TABLE . '",
};

weCollectionEdit.csv = ",' . implode(',', $GLOBALS['we_doc']->getCollection()) . ',";
');
// FIXME: set weCollectionEdit.csv when first used in addItems()...

?>

</head>
<body class="weEditorBody" style="height:100%;overflow:hidden;">
	<form name="we_form"><?php
		echo we_class::hiddenTrans() . 
		$GLOBALS['we_doc']->formCollection();
		?>
		<input type="hidden" name="we_complete_request" value="1"/>
	</form>
	<?php
	echo weSuggest::getYuiFiles() .
	$yuiSuggest->getYuiJs();
	?>
</body>
</html>