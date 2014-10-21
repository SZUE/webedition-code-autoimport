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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
if(!(
	we_base_request::_(we_base_request::BOOL, 'we_dialog_args', false, 'outsideWE') ||
	we_base_request::_(we_base_request::BOOL, 'we_dialog_args', false, 'isFrontend')
	)){
	we_html_tools::protect();
}
$dialog = new we_dialog_table();
// MS-Fix

$dialog->initByHttp();
$dialog->registerOkJsFN("weDoTblJS");

if(!we_base_request::_(we_base_request::BOOL, "we_dialog_args", false, "edit")){
	$dialog->dialogTitle = g_l('wysiwyg', "[insert_table]");
}
echo $dialog->getHTML();

function weDoTblJS(){
	return '
eval("var editorObj = top.opener.weWysiwygObject_"+document.we_form.elements["we_dialog_args[editname]"].value);
var edit_table = (document.we_form.elements["we_dialog_args[edit]"].value==1) ? true : false;
var rows = document.we_form.elements["we_dialog_args[rows]"].value;rows = rows ? rows : 3;
var cols = document.we_form.elements["we_dialog_args[cols]"].value;cols = cols ? cols : 3;
var border = document.we_form.elements["we_dialog_args[border]"].value;
var classname = document.we_form.elements["we_dialog_args[class]"].value;
var cellpadding = document.we_form.elements["we_dialog_args[cellpadding]"].value;
var cellspacing = document.we_form.elements["we_dialog_args[cellspacing]"].value;
var bgcolor = document.we_form.elements["we_dialog_args[bgcolor]"].value;
var background = "";
var summary = document.we_form.elements["we_dialog_args[summary]"].value;
var width = document.we_form.elements["we_dialog_args[width]"].value;
var height = document.we_form.elements["we_dialog_args[height]"].value;
var align_sel = document.we_form.elements["we_dialog_args[align]"];
var align = align_sel.options[align_sel.selectedIndex].value;
editorObj.edittable(edit_table,rows,cols,border,cellpadding,cellspacing,bgcolor,background,width,height,align,classname,summary);
top.close();
';
}

?>