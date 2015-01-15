/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 8967 $
 * $Author: mokraemer $
 * $Date: 2015-01-13 12:56:41 +0100 (Di, 13. Jan 2015) $
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
var ajaxCallback = {
	success: function (o) {
		if (typeof (o.responseText) !== undefined && o.responseText !== '') {
			try {
				eval("var result=" + o.responseText);
			} catch (exp) {
				try {
					//console.log(exp + " " + o.responseText);
				} catch (ex) {
					var result = '';
				}
			}
			if (result && result.Success) {
				var num_users = result.DataArray.num_users;
				weRpcFailedCnt = 0;
				if (top.weEditorFrameController) {
					var _ref = top.weEditorFrameController.getActiveDocumentReference();
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
				}
				if (modules.MESSAGING_SYSTEM) {
					if (top.header_msg_update) {
						var newmsg_count = result.DataArray.newmsg_count;
						var newtodo_count = result.DataArray.newtodo_count;

						top.header_msg_update(newmsg_count, newtodo_count);
					}

				}
			}
		}
	},
	failure: function (o) {
		if (weRpcFailedCnt++ > 5) {
			//in this case, rpc failed 5 times, this is severe, user should be in informed!
			alert(g_l.unable_to_call_ping);
		}
	}
}

function YUIdoAjax() {
	YAHOO.util.Connect.asyncRequest('POST', "/webEdition/rpc/rpc.php", ajaxCallback, 'protocol=json&cmd=Ping');
}

window.setInterval(YUIdoAjax(), constants.PING_TIME);