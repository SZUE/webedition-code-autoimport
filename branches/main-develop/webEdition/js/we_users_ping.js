/* global top, WE, YAHOO */

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

var weRpcFailedCnt = 0;

window.setInterval(function () {
	YAHOO.util.Connect.asyncRequest('POST', WE().consts.dirs.WEBEDITION_DIR + "rpc.php", {
		success: function (o) {
			if (o.responseText !== undefined && o.responseText !== '') {
				var result;
				try {
					result = JSON.parse(o.responseText);
				} catch (exp) {

				}
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
						mfdData = result.DataArray.mfd_data;
						if (_ref.setMfdData && mfdData !== undefined) {
							_ref.setMfdData(mfdData);
						}
					}

					if (WE().consts.tables.MESSAGES_TABLE) {
						if (top.header_msg_update) {
							var newmsg_count = result.DataArray.newmsg_count;
							var newtodo_count = result.DataArray.newtodo_count;

							top.header_msg_update(newmsg_count, newtodo_count);
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
		},
		failure: function (o) {
			if (weRpcFailedCnt++ > 5) {
				//in this case, rpc failed 5 times, this is severe, user should be in informed!
				alert(WE().consts.g_l.main.unable_to_call_ping);
			}
		}
	}, 'protocol=json&cmd=Ping');
}, WE().consts.global.PING_TIME);