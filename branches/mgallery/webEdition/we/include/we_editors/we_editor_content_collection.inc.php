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
we_html_element::jsScript(JS_DIR . 'we_editor_collectionContent.js') .
we_html_element::jsElement('var we_name = "' . $GLOBALS['we_doc']->Name . '";');

?>

</head>
<body class="weEditorBody">
	<form name="we_form"><?php echo we_class::hiddenTrans();
		echo '<div style="margin-left:20px;">SELECT: remTable(FILE_TABLE|OBJECT_FILES_TABLE) => actually FILE_TABLE is used by default<br><br></div>';
		
		echo we_html_element::htmlDiv(array('style' => 'margin-left:20px;'), $GLOBALS['we_doc']->formInputField('', 'Collection', 'Collection', 40, 410));
		echo $GLOBALS['we_doc']->formCollection();
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