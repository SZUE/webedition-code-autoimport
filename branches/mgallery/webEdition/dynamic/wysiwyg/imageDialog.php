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
		if(!file_exists($thumbObj->getOutputPath(true))){
			$thumbObj->createThumb();
		}
	}

	$attribs = we_base_request::_(we_base_request::BOOL, 'imgChangedCmd') && !we_base_request::_(we_base_request::BOOL, 'wasThumbnailChange') ? we_base_request::_(we_base_request::STRING, 'we_dialog_args') : $args;
	return we_dialog_base::getTinyMceJS() .
		we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/weimage/js/image_insert.js') .
		'<form name="tiny_form">' .
		we_html_element::htmlHiddens(array(
			"src" => $args["src"],
			"width" => $attribs["width"],
			"height" => $attribs["height"],
			"hspace" => $attribs["hspace"],
			"vspace" => $attribs["vspace"],
			"border" => $attribs["border"],
			"alt" => $attribs["alt"],
			"align" => $attribs["align"],
			"name" => $attribs["name"],
			"class" => $attribs["cssclass"],
			"title" => $attribs["title"],
			"longdesc" => (intval($attribs["longdescid"]) ? $attribs["longdescsrc"] . '?id=' . intval($attribs["longdescid"]) : '')
		)) . '</form>';
}
