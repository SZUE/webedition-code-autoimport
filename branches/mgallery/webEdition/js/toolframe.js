/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 9911 $
 * $Author: mokraemer $
 * $Date: 2015-05-28 22:11:55 +0200 (Do, 28. Mai 2015) $
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
top.weToolWindow = true;

function toggleBusy() {
}
var makeNewEntryCheck = 0;
var publishWhenSave = 0;


function we_cmd() {
	var args = [];
	for (var i = 0; i < arguments.length; i++) {
		args.push(arguments[i]);
	}
	top.content.we_cmd.apply(this, args);
}
