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
 * @package    webEdition_wysiwyg
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/weSpecialCharDialog.class.inc.php");
//make sure we know which browser is used
include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_browser_check.inc.php');
we_html_tools::protect();
$dialog = new weSpecialCharDialog();
$dialog->initByHttp();
$dialog->registerOkJsFN("weDoRuleJS");
print $dialog->getHTML();

function weDoRuleJS(){
	return '
eval("var editorObj = top.opener.weWysiwygObject_"+document.we_form.elements["we_dialog_args[editname]"].value);
var ch = document.we_form.elements["we_dialog_args[char]"].value;
var isSafari = '.($GLOBALS['BROWSER']=='SAFARI'?'true':'false').';

if (isSafari) {
	ch = ch.replace(/^&/,"_xx_WE_AMP_xx_");
}

editorObj.replaceText(ch);

if (isSafari) {
	editorObj.editContainer.innerHTML = editorObj.editContainer.innerHTML.replace(/_xx_WE_AMP_xx_/,"&");
}

top.close();
';
}
?>