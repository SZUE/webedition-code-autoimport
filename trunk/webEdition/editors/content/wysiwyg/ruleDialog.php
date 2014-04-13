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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
if(!(isset($_REQUEST['we_dialog_args']) &&
	(
	weRequest('bool', 'we_dialog_args', false, 'outsideWE') ||
	weRequest('bool', 'we_dialog_args', false, 'isFrontend')
	))){
	we_html_tools::protect();
}
$dialog = new we_dialog_rule();
$dialog->initByHttp();
$dialog->registerOkJsFN("weDoRuleJS");
print $dialog->getHTML();

function weDoRuleJS(){
	return '
eval("var editorObj = top.opener.weWysiwygObject_"+document.we_form.elements["we_dialog_args[editname]"].value);
var width = document.we_form.elements["we_dialog_args[width]"].value;
var height = document.we_form.elements["we_dialog_args[height]"].value;
var color = document.we_form.elements["we_dialog_args[color]"].value;
var align_sel = document.we_form.elements["we_dialog_args[align]"];
var align = align_sel.options[align_sel.selectedIndex].value;
var noshade = document.we_form.elements["we_dialog_args[noshade]"].checked ? 1 : 0;
editorObj.editrule(width,height,color,align,noshade);
top.close();
';
}