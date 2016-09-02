/* global top,WE, self */

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

var el = document.getElementById('loadVarCmd');
var cmd = el.getAttribute('data-cmds');
if (cmd) {
	var cmds = cmd.split(',');
	var cmdData = null;
	for (var i = 0; i < cmds.length; i++) {
		cmdData = WE().util.decodeDynamicVar(el, 'data-cmd' + i);

		//Keep switch clean, only few statements should be here, everthing else goes to specific js file
		switch (cmds[i]) {
			case 'msg':
				top.we_showMessage(cmdData.msg, cmdData.prio ? cmdData.prio : WE().consts.message.WE_MESSAGE_NOTICE, window);
				break;
			case 'close':
				top.close();
				break;
			case 'history.back':
				history.back();
				break;
			case 'we_cmd':
				top.we_cmd.apply(this, cmdData);
				break;
			case 'location':
				switch (cmdData['doc']) {
					case 'document':
						document.location = cmdData['loc'];
						break;
					case 'body':
						top.body.document.location = cmdData['loc'];
						break;
				}
		}
	}
}
