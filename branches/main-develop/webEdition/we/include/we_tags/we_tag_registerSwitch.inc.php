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
function we_tag_registerSwitch(){
	if(!$GLOBALS["we_editmode"]){
		return '';
	}

	if(($val = we_base_request::_(we_base_request::BOOL, 'we_set_registeredUser', -1)) !== -1 && $GLOBALS['WE_MAIN_DOC_REF']->InWebEdition){
		$GLOBALS['WE_MAIN_DOC_REF']->setEditorPersistent('registered', $val);
	}

	$val = (bool) $GLOBALS['WE_MAIN_DOC_REF']->getEditorPersistent('registered');

	return '
<table style="padding:5px;border:0px;background-color:silver;background-image:none;" class="weEditTable">
	<tr><td style="padding: 0px 1em;"><b>' . g_l('modules_customer', '[view]') . ':</b></td>
	<td><input id="set_registered" type="radio" name="we_set_registeredUser" value="1" onclick="top.we_cmd(\'reload_editpage\');"' . ($val ? ' checked' : '') . ' /></td>
	<td style="padding: 0px 1em 0px 0px;"><label for="set_registered">' . g_l('modules_customer', '[registered_user]') . '</label></td>
	<td><input id="set_unregistered" type="radio" name="we_set_registeredUser" value="0" onclick="top.we_cmd(\'reload_editpage\');"' . (!$val ? ' checked' : '') . ' /></td>
	<td style="padding: 0px 1em 0px 0px;"><label for="set_unregistered">' . g_l('modules_customer', '[unregistered_user]') . '</label></td>
	</tr>
</table>';
}
