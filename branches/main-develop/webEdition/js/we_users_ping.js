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

			var _ref = WE().layout.cockpitFrame;
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
			var i, obj, msg, id;
			//requests handling
			if (releases.requests.length) {
				msg = "";
				for (i = 0; i < releases.requests.length; i++) {
					id = parseInt(releases.requests[i].ID);
					obj = {};
					obj[releases.requests[i].tbl] = [id];
					if (releases.requests[i].forceTime) {
						top.window.setTimeout(forceReleaseDocuments, (releases.requests[i].forceTime > 0 ? releases.requests[i].forceTime - 5 : 30) * 1000, obj);
					}
					obj = getDocumentNames(obj);
					msg += releases.requests[i].User + ": " + releases.requests[i].text + obj[releases.requests[i].tbl].names[id] +
						(releases.requests[i].forceTime ? " " + WE().consts.g_l.main.unlockRequestForceTo + " " + releases.requests[i].forceDate : "") +
						"\n";
				}
				WE().util.showMessage(WE().consts.g_l.main.unlockRequest + "\n" + msg, WE().consts.message.WE_MESSAGE_INFO, window);
			}
			//reply handling
			for (i = 0; i < releases.reply.count; i++) {
				console.log(releases.reply[i]);
			}
			//reload documents handling
			if (releases.unlock) {
				WE().layout.reloadUsedEditors(releases.unlock);
			}
		}
	}
	).fail(function (jqxhr, textStatus, error) {
		if (weRpcFailedCnt++ > 5) {
			//in this case, rpc failed 5 times, this is severe, user should be in informed!
			top.we_showMessage(WE().consts.g_l.main.unable_to_call_ping, WE().consts.message.WE_MESSAGE_ERROR, window, 20000);
		}
	});
}, WE().consts.global.PING_TIME);

function getDocumentNames(docs) {
	var usedEditors = WE().layout.weEditorFrameController.getEditorsInUse(),
		curDoc;
	for (var frameId in usedEditors) {
		curDoc = docs[usedEditors[frameId].getEditorEditorTable()];
		if (curDoc && curDoc.indexOf(usedEditors[frameId].getEditorDocumentId()) !== -1) {
			if (!docs[usedEditors[frameId].getEditorEditorTable()].names) {
				docs[usedEditors[frameId].getEditorEditorTable()].names = {};
			}
			docs[usedEditors[frameId].getEditorEditorTable()].names[usedEditors[frameId].getEditorDocumentId()] = usedEditors[frameId].getEditorDocumentPath();
		}
	}
	return docs;
}

function forceReleaseDocuments(docs) {
	var usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
	var trans,
		url = WE().util.getWe_cmdArgsUrl(['save_document']),
		paths = "",
		curDoc;
	for (var frameId in usedEditors) {
		curDoc = docs[usedEditors[frameId].getEditorEditorTable()];
		if (curDoc && curDoc.indexOf(usedEditors[frameId].getEditorDocumentId()) !== -1) {
			trans = usedEditors[frameId].getEditorTransaction();
			top.doSave(url, trans);
			paths += usedEditors[frameId].getEditorDocumentPath() + "\n";
			WE().layout.weEditorFrameController.closeDocument(frameId);
		}
	}
	if (paths) {
		WE().util.showMessage(WE().consts.g_l.main.unlockReleaseDone + "\n" + paths, WE().consts.message.WE_MESSAGE_INFO, window);
	}
}