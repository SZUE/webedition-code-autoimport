/* global WE, top */

/**
 * webEdition CMS
 *
 * $Rev: 13692 $
 * $Author: mokraemer $
 * $Date: 2017-04-05 18:27:52 +0200 (Mi, 05. Apr 2017) $
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
'use strict';

function checkForm(f) {
	if (f.tagname.value == "") {
		WE().util.showMessage(WE().consts.g_l.banner.error_tagname_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		f.tagname.focus();
		f.tagname.select();
		return false;
	}
	if (f.page.value == "") {
		WE().util.showMessage(WE().consts.g_l.banner.error_page_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		f.page.focus();
		f.page.select();
		return false;
	}
	if (f.width.value == "") {
		WE().util.showMessage(WE().consts.g_l.banner.error_width_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		f.width.focus();
		f.width.select();
		return false;
	}
	if (f.height.value == "") {
		WE().util.showMessage(WE().consts.g_l.banner.error_height_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		f.height.focus();
		f.height.select();
		return false;
	}
	if (f.getscript.value == "") {
		WE().util.showMessage(WE().consts.g_l.banner.error_getscript_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		f.getscript.focus();
		f.getscript.select();
		return false;
	}
	if (f.clickscript.value == "") {
		WE().util.showMessage(WE().consts.g_l.banner.error_clickscript_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
		f.clickscript.focus();
		f.clickscript.select();
		return false;
	}
	return true;
}
