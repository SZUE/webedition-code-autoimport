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
'use strict';

window.addEventListener('load', function () {
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
					WE().util.showMessage(cmdData.msg, cmdData.prio ? cmdData.prio : WE().consts.message.WE_MESSAGE_NOTICE, window);
					break;
				case 'close':
					top.close();
					break;
				case 'close_delayed':
					window.setTimeout(function() {top.close();}, 1200);
					break;
				case 'history.back':
					history.back();
					break;
				case 'we_cmd':
					if (window.we_cmd) {
						window.we_cmd.apply(window, cmdData);
					} else if (window.parent.we_cmd) {
						window.parent.we_cmd.apply(window, cmdData);
					} else {
						top.we_cmd.apply(window, cmdData);
					}
					break;
				case 'location':
					switch (cmdData.doc) {
						case 'document':
							document.location = cmdData.loc;
							break;
						case 'body':
							top.body.document.location = cmdData.loc;
							break;
					}
					break;
				default:
					//if nothing matched, we set arg[0]=cmd & pass the whole argument to we_cmd
					if (window.we_cmd) {//direct match
						window.we_cmd.apply(window, [cmds[i], cmdData]);
					} else if (window.parent.we_cmd) {//match content frame
						window.parent.we_cmd.apply(window, [cmds[i], cmdData]);
					} else if (window.parent.parent.we_cmd) {//in view component (on modules)
						window.parent.parent.we_cmd.apply(window, [cmds[i], cmdData]);
					} else {//at last try the main we_cmd
						top.we_cmd.apply(window, [cmds[i], cmdData]);
					}
			}
		}
	}
});