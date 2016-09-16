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

$properties = [
	'docRemTable' => $GLOBALS['we_doc']->getRemTable(),
	'docRemCT' => $GLOBALS['we_doc']->getRemCT(),
	'docRealRemCT' => $GLOBALS['we_doc']->getRealRemCT(),
	'docRemClass' => $GLOBALS['we_doc']->getRemClass(),
	'docDefaultDir' => $GLOBALS['we_doc']->DefaultDir,
	'docIsDuplicates' => intval($GLOBALS['we_doc']->IsDuplicates),
	'docLastView' => "grid", // save last view to session
];
echo we_html_element::jsScript(JS_DIR . 'collection.js', '', ['id' => 'loadVarCollection', 'data-we_doc' => setDynamicVar($properties)]);
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