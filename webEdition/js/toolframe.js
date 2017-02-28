/* global top, WE */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
'use strict';
top.weToolWindow = true;

var makeNewEntryCheck = 0;
var publishWhenSave = 0;

function setTab(tool) {
	if (top.content.hot) {
		if (window.confirm(WE().consts.g_l.alert.discard_changed_data)) {
			top.content.hot = false;
			current = tool;
			top.content.location.replace(WE().consts.dirs.WE_INCLUDES_DIR + "we_tools/tools_content.php?tool=" + tool);
		} else {
			top.navi.weTabs.setActiveTab(current);
		}
	} else {
		top.content.hot = false;
		current = tool;
		top.content.location.replace(WE().consts.dirs.WE_INCLUDES_DIR + "we_tools/tools_content.php?tool=" + tool);
	}
}