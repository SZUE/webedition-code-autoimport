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
function we_tag_newsletterSwitch(){
	if(!$GLOBALS["we_editmode"]){
		return '';
	}
//html=false, text=true

	if(($val = we_base_request::_(we_base_request::BOOL, 'we_set_newsletterFormat', -1)) !== -1 && $GLOBALS['we_doc']->InWebEdition){
		$GLOBALS['we_doc']->setEditorPersistent('newsletterFormat', $val);
	}

	$val = (bool) $GLOBALS['we_doc']->getEditorPersistent('newsletterFormat');


	return '
<table style="padding:5px;border:0px;background-color:silver;background-image:none;" class="weEditTable">
	<tr><td><b>' . g_l('modules_newsletter', '[newsletter][preview]') . '</b>&nbsp;</td>
	<td><input id="set_newsletterHtml" type="radio" name="we_set_newsletterFormat" value="0" onclick="top.we_cmd(\'reload_editpage\');"' . (!$val ? ' checked' : '') . ' /></td>
	<td>&nbsp;<label for="set_newsletterHtml">HTML&nbsp;' . g_l('modules_newsletter', '[email]') . '</label>&nbsp;&nbsp;&nbsp;</td>
	<td><input id="set_newsletterText" type="radio" name="we_set_newsletterFormat" value="1" onclick="top.we_cmd(\'reload_editpage\');"' . ($val ? ' checked' : '') . ' /></td>
	<td>&nbsp;<label for="set_newsletterText">' . g_l('modules_newsletter', '[type_text]') . '&nbsp;' . g_l('modules_newsletter', '[email]') . '</label></td>
	</tr>
</table>';
}
