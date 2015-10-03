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
var ajaxCallback = {
	success: function (o) {
		if (o.responseText !== undefined && o.responseText !== '') {
			try {
				eval("var result=" + o.responseText);
			} catch (exp) {
				var result = '';
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
			alert(WE().consts.g_l.main.unable_to_call_ping);
		}
	}
};

window.setInterval(function(){
	YAHOO.util.Connect.asyncRequest('POST', WE().consts.dirs.WEBEDITION_DIR + "rpc/rpc.php", ajaxCallback, 'protocol=json&cmd=Ping');
}, constants.PING_TIME);