<?php

/**
 * webEdition CMS
 *
 * $Rev: 7705 $
 * $Author: mokraemer $
 * $Date: 2014-06-10 21:46:56 +0200 (Di, 10 Jun 2014) $
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

$dialog = new we_dialog_fullscreenEdit();
$dialog->initByHttp();
$dialog->registerOkJsFN("weDoFullscreenJS");
print $dialog->getHTML();

function weDoFullscreenJS(){
	return '
if(weWysiwygSetHiddenText){
	weWysiwygSetHiddenText();
}

eval("editorObj = top.opener.weWysiwygObject_"+document.we_form.elements["we_dialog_args[editname]"].value);
var src = document.we_form.elements["we_dialog_args[src]"].value;
editorObj.setText(src);
top.close();
';
}
