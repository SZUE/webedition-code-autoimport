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
$content = we_html_element::cssElement('
	i.alias{
	color:grey;
	}
button.weBtn{
	padding-bottom:1em;
}') . we_html_button::create_button("fat:create_user,fa-lg fa-user-plus", "javascript:top.opener.top.we_cmd('new_user');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")) .
	we_html_button::create_button("fat:create_group,fa-lg fa-users,fa-plus", "javascript:top.opener.top.we_cmd('new_group');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_GROUP")) .
	we_html_button::create_button("fat:create_alias,alias fa-lg fa-user-plus", "javascript:top.opener.top.we_cmd('new_alias');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_ALIAS"));

$modimage = "user.gif";
