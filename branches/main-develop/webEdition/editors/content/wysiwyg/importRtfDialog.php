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

//FIXME: 6.4: remove this file
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
if(!(isset($_REQUEST['we_dialog_args']) && isset($_REQUEST['we_dialog_args']['outsideWE']) && $_REQUEST['we_dialog_args']['outsideWE']==1) ){
	we_html_tools::protect();
}
$dialog = new we_dialog_importRtf();
$dialog->initByHttp();
if(isset($dialog->args["ntxt"]) && $dialog->args["ntxt"]){
	$dialog->registerOkJsFN("weDoRtfJSTxt");
} else{
	$dialog->registerOkJsFN("weDoRtfJS");
}
print $dialog->getHTML();

function weDoRtfJS(){
	return '
eval("var editorObj = top.opener.weWysiwygObject_"+document.we_form.elements["we_dialog_args[editname]"].value);
editorObj.replaceText(document.we_form.elements["we_dialog_args[htmltxt]"].value);
top.close();
';
}

function weDoRtfJSTxt(){
	return '
eval("var taObj = top.opener."+document.we_form.elements["we_dialog_args[taname]"].value+"Object");
taObj.appendText(document.we_form.elements["we_dialog_args[htmltxt]"].value);
top.close();
';
}