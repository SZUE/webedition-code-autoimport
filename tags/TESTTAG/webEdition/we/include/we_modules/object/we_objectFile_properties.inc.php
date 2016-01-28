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
$wepos = "";
$parts = array();

if($GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_WORKSPACE){
	$parts[] = array(
		"headline" => g_l('weClass', '[path]'),
		"html" => $GLOBALS['we_doc']->formPath(),
		"space" => 140,
		"icon" => "path.gif"
	);

	if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE || !permissionhandler::hasPerm('CAN_SEE_OBJECTS')){ // No link to class in normal mode
		$parts[] = array(
			"headline" => g_l('modules_object', '[class]'),
			"html" => $GLOBALS['we_doc']->formClass(),
			"space" => 140,
			'noline' => true,
			"icon" => "class.gif"
		);
	} elseif($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	Link to class in normal mode
		$_html = '<div class="weMultiIconBoxHeadline" style="margin-bottom:5px;"><a href="javascript:top.weEditorFrameController.openDocument(\'' . OBJECT_TABLE . '\',' . $GLOBALS['we_doc']->TableID . ',\'object\');">' . g_l('modules_object', '[class]') . '</a></div>' .
			'<div style="margin-bottom:12px;">' . $GLOBALS['we_doc']->formClass() . '</div>';
		$_html .= '<div class="weMultiIconBoxHeadline" style="margin-bottom:5px;">' . g_l('modules_object', '[class_id]') . '</div>' .
			'<div style="margin-bottom:12px;">' . $GLOBALS['we_doc']->formClassId() . '</div>';


		$parts[] = array(
			"headline" => "",
			"html" => $_html,
			"space" => 140,
			"forceRightHeadline" => 1,
			"icon" => "class.gif"
		);
	}

	$parts[] = array(
		"headline" => g_l('weClass', '[language]'),
		"html" => $GLOBALS['we_doc']->formLangLinks(),
		"space" => 140,
		"icon" => "lang.gif"
	);


	$parts[] = array(
		"headline" => g_l('global', '[categorys]'),
		"html" => $GLOBALS['we_doc']->formCategory(),
		"space" => 140,
		"icon" => "cat.gif"
	);


	$parts[] = array(
		"headline" => g_l('modules_object', '[copyObject]'),
		"html" => $GLOBALS['we_doc']->formCopyDocument(),
		"space" => 140,
		"icon" => "copy.gif"
	);


	$parts[] = array(
		"headline" => g_l('weClass', '[owners]'),
		"html" => $GLOBALS['we_doc']->formCreatorOwners(),
		"space" => 140,
		"icon" => "user.gif"
	);


	$parts[] = array(
		"headline" => g_l('weClass', '[Charset]'),
		"html" => $GLOBALS['we_doc']->formCharset(),
		"space" => 140,
		"icon" => "charset.gif"
	);
} elseif($GLOBALS['we_doc']->hasWorkspaces()){ //	Show workspaces
	$parts[] = array(
		"headline" => g_l('weClass', '[workspaces]'),
		"html" => $GLOBALS['we_doc']->formWorkspaces(),
		"space" => 140,
		"noline" => 1,
		"icon" => "workspace.gif"
	);
	$parts[] = array(
		"headline" => g_l('weClass', '[extraWorkspaces]'),
		"html" => $GLOBALS['we_doc']->formExtraWorkspaces(),
		"space" => 140,
		"forceRightHeadline" => 1
	);

	$button = we_html_button::create_button('ws_from_class', "javascript:we_cmd('object_ws_from_class');_EditorFrame.setEditorIsHot(true);");

	$parts[] = array(
		"headline" => "",
		"html" => $button,
		"space" => 140
	);
} else { //	No workspaces defined
	$parts[] = array(
		"headline" => "",
		"html" => g_l('modules_object', '[no_workspace_defined]'),
		"space" => 0
	);
}
echo we_html_multiIconBox::getJS() .
	we_html_multiIconBox::getHTML("weOjFileProp", "100%", $parts, 30);

