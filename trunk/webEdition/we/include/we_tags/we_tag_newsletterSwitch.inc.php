<?php

/**
 * webEdition CMS
 *
 * $Rev: 7705 $
 * $Author: Andreas Witt $
 * $Date: 2014-06-10 21:46:56 +0200 (Tue, 10 Jun 2014) $
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
function we_tag_newsletterSwitch(){
	
	(isset($_REQUEST['we_set_newsletterFormat']) && $GLOBALS['we_doc']->InWebEdition) ? 
		$_SESSION['weS']['we_set_newsletterFormat'] = $_REQUEST['we_set_newsletterFormat'] : 
		(!isset($_SESSION['weS']['we_set_newsletterFormat']) ? 
			$_SESSION['weS']['we_set_newsletterFormat'] = 1 : 
			'');

	return($GLOBALS["we_editmode"] ? '
<table style="padding:5px;border:0px;background-color:silver;background-image:none;" class="weEditTable">
	<tr><td><b>'.g_l('modules_newsletter', '[newsletter][preview]').'</b>&nbsp;</td>
	<td><input id="set_newsletterHtml" type="radio" name="we_set_newsletterFormat" value="1" onclick="top.we_cmd(\'reload_editpage\');"' . ((isset($_SESSION['weS']['we_set_newsletterFormat']) && $_SESSION['weS']['we_set_newsletterFormat'] == 1) ? ' checked' : '') . ' /></td>
	<td>&nbsp;<label for="set_newsletterHtml">HTML&nbsp;'.g_l('modules_newsletter', '[email]').'</label>&nbsp;&nbsp;&nbsp;</td>
	<td><input id="set_newsletterText" type="radio" name="we_set_newsletterFormat" value="0" onclick="top.we_cmd(\'reload_editpage\');"' . ((isset($_SESSION['weS']['we_set_newsletterFormat']) && $_SESSION['weS']['we_set_newsletterFormat'] == 0) ? ' checked' : '') . ' /></td>
	<td>&nbsp;<label for="set_newsletterText">'.g_l('modules_newsletter', '[type_text]').'&nbsp;'.g_l('modules_newsletter', '[email]').'</label></td>
	</tr>
</table>' :
			'');
}
