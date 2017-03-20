/* global top, WE,console */

/**
 * webEdition CMS
 *
 * webEdition CMS
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
'use strict';

var weRpcFailedCnt = 0;

window.setInterval(function () {
	WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=Ping", '', function (result) {
		if (result && result.Success) {
			var num_users = result.DataArray.num_users;
			weRpcFailedCnt = 0;

			var _ref = WE().layout.weEditorFrameController.getActiveDocumentReference();
			if (_ref) {
				if (_ref.setUsersOnline && _ref.setUsersListOnline) {
					_ref.setUsersOnline(num_users);
					var usersHTML = result.DataArray.users;
					if (usersHTML) {
						_ref.setUsersListOnline(usersHTML);
					}
				}
				var mfdData = result.DataArray.mfd_data;
				if (_ref.setMfdData && mfdData !== undefined) {
					_ref.setMfdData(mfdData);
				}
			}

			var releases = result.DataArray.release;
			//FIXME: add support for release requests
			var i;
			//requests handling
			for (i = 0; i < releases.requests; i++) {
				console.log(releases.requests[i]);
			}
			//reply handling
			for (i = 0; i < releases.reply; i++) {
				console.log(releases.reply[i]);
			}
			//reload documents handling
			for (i = 0; i < releases.unlock; i++) {
				console.log(releases.unlock[i]);
			}
		}
	}
	).fail(function (jqxhr, textStatus, error) {
		if (weRpcFailedCnt++ > 5) {
			//in this case, rpc failed 5 times, this is severe, user should be in informed!
			top.we_showMessage(WE().consts.g_l.main.unable_to_call_ping, WE().consts.message.WE_MESSAGE_ERROR, 20000);
		}
	});
}, WE().consts.global.PING_TIME);