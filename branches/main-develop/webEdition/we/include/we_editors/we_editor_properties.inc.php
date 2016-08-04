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
echo we_html_tools::getHtmlTop('', $charset);
require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
echo weSuggest::getYuiFiles();
?>
</head>
<body class="weEditorBody" onload="doScrollTo()" onunload="doUnload()">
	<form name="we_form" method="post" action="" onsubmit="return false;"><?php
		echo we_class::hiddenTrans() .
		we_html_multiIconBox::getJS() .
		$GLOBALS['we_doc']->getPropertyPage() .
		we_html_element::htmlHidden("we_complete_request", 1);
		?>
	</form>
	<?php
	echo $yuiSuggest->getYuiJs();
	?>
</body>
</html>