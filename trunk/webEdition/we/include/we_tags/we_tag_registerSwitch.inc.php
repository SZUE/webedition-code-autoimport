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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_registerSwitch(){

	return($GLOBALS["we_editmode"] ? '
<table style="padding:5px;border:0px;background-color:silver;color: black;font-size:' . ((we_base_browserDetect::isMAC()) ? "11px" : ((we_base_browserDetect::isUNIX()) ? "13px" : "12px")) . ';font-family:' . g_l('css', '[font_family]') . ';">
	<tr><td><b>' . g_l('modules_customer', '[view]') . ':</b>&nbsp;</td>
	<td><input id="set_registered" type="radio" name="we_set_registeredUser" value="1" onclick="top.we_cmd(\'reload_editpage\');"' . ((isset($_SESSION['weS']['we_set_registered']) && $_SESSION['weS']['we_set_registered']) ? ' checked' : '') . ' /></td>
	<td>&nbsp;<label for="set_registered">' . g_l('modules_customer', '[registered_user]') . '</label>&nbsp;&nbsp;&nbsp;</td>
	<td><input id="set_unregistered" type="radio" name="we_set_registeredUser" value="0" onclick="top.we_cmd(\'reload_editpage\');"' . ((!isset($_SESSION['weS']['we_set_registered']) || !$_SESSION['weS']['we_set_registered']) ? ' checked' : '') . ' /></td>
	<td>&nbsp;<label for="set_unregistered">' . g_l('modules_customer', '[unregistered_user]') . '</label></td>
	</tr>
</table>' :
			'');
}