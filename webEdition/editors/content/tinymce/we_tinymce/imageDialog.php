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

$noInternals = false;
if(!(
	we_base_request::_(we_base_request::BOOL, 'we_dialog_args', false, 'outsideWE') ||
	we_base_request::_(we_base_request::BOOL, 'we_dialog_args', false, 'isFrontend')
	)){
	we_html_tools::protect();
} else {
	$noInternals = true;
}
$noInternals = $noInternals || !isset($_SESSION['user']) || !isset($_SESSION['user']['Username']) || $_SESSION['user']['Username'] == '';

$dialog = new we_dialog_image($noInternals);
$dialog->initByHttp();
$dialog->registerCmdFn(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 0) ? '' : "weDoImgCmd");

echo $dialog->getHTML();

function weDoImgCmd($args){
	if($args["thumbnail"] && $args["fileID"]){
		$thumbObj = new we_thumbnail();
		$thumbObj->initByImageIDAndThumbID($args["fileID"], $args["thumbnail"]);
	}

	if(we_base_request::_(we_base_request::STRING, 'we_dialog_args', 'tinyMce', 'editor') != "tinyMce"){

		return we_html_element::jsElement('top.opener.weWysiwygObject_' . $args["editname"] . '.insertImage("' . $args["src"] . '","' . $args["width"] . '","' . $args["height"] . '","' . $args["hspace"] . '","' . $args["vspace"] . '","' . $args["border"] . '","' . addslashes($args["alt"]) . '","' . $args["align"] . '","' . $args["name"] . '","' . $args["class"] . '","' . addslashes($args["title"]) . '","' . $args["longdesc"] . '");
top.close();
');
	} else {
		$attribs = we_base_request::_(we_base_request::BOOL, 'imgChangedCmd') && !we_base_request::_(we_base_request::BOOL, 'wasThumbnailChange') ? we_base_request::_(we_base_request::STRING, 'we_dialog_args') : $args;
		return we_dialog_base::getTinyMceJS() .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'plugins/weimage/js/image_insert.js') .
			'<form name="tiny_form">
				<input type="hidden" name="src" value="' . $args["src"] . '">
				<input type="hidden" name="width" value="' . $attribs["width"] . '">
				<input type="hidden" name="height" value="' . $attribs["height"] . '">
				<input type="hidden" name="hspace" value="' . $attribs["hspace"] . '">
				<input type="hidden" name="vspace" value="' . $attribs["vspace"] . '">
				<input type="hidden" name="border" value="' . $attribs["border"] . '">
				<input type="hidden" name="alt" value="' . addslashes($attribs["alt"]) . '">
				<input type="hidden" name="align" value="' . $attribs["align"] . '">
				<input type="hidden" name="name" value="' . $attribs["name"] . '">
				<input type="hidden" name="class" value="' . $attribs["class"] . '">
				<input type="hidden" name="title" value="' . addslashes($attribs["title"]) . '">
				<input type="hidden" name="longdesc" value="' . (intval($attribs["longdescid"]) ? $attribs["longdescsrc"] . '?id=' . intval($attribs["longdescid"]) : '') . '">
			</form>';
	}
}
