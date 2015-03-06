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
	name: "' .$GLOBALS['we_doc']->Name. '",
	remTable: "' . $GLOBALS['we_doc']->remTable . '",
	remCT: "' . $GLOBALS['we_doc']->remCT . '",
	remCT: "' . $GLOBALS['we_doc']->remClass . '",
};

weCollectionEdit.we_const = {
	TBL_PREFIX: "' . TBL_PREFIX . '",
	FILE_TABLE: "' . FILE_TABLE . '",
	OBJECT_FILES_TABLE: "' . OBJECT_FILES_TABLE . '",
}')
?>

</head>
<body class="weEditorBody" style="height:100%;overflow:hidden;">
	<form name="we_form"><?php
		echo we_class::hiddenTrans() .
		'remTable: ' . $GLOBALS['we_doc']->remTable . '<br/>
		remCT: ' . $GLOBALS['we_doc']->remCT . '<br/>
		remClass: ' . $GLOBALS['we_doc']->remClass . '<br/>' .
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