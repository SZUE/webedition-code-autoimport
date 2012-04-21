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
if(!(isset($_REQUEST['we_dialog_args']) && isset($_REQUEST['we_dialog_args']['outsideWE']) && $_REQUEST['we_dialog_args']['outsideWE'] == 1)){
	we_html_tools::protect();
}
$dialog = new weHyperlinkDialog();
$dialog->initByHttp();
$dialog->registerCmdFn("weDoLinkCmd");
print $dialog->getHTML();

function weDoLinkCmd($args){

	if((!isset($args["href"])) || $args["href"] == "http://")
		$args["href"] = "";

	$param = ($args["param"] ? "?" . str_replace("?", "", $args["param"]) : "");
	$param = trim($param, '&');
	$href = $args["href"] . $param . ($args["anchor"] ? "#" . $args["anchor"] : "");

	return we_html_element::jsElement(
			'top.opener.weWysiwygObject_' . $args["editname"] . '.createLink("' . $href . '","' . $args["target"] . '","' . $args["class"] . '","' . $args["lang"] . '","' . $args["hreflang"] . '","' . $args["title"] . '","' . $args["accesskey"] . '","' . $args["tabindex"] . '","' . $args["rel"] . '","' . $args["rev"] . '");
top.close();
');
}
