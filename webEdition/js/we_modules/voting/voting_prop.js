/* global WE, top */

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
var owners_label,
				question_edit,
				answers_edit,
				iptable_label;

var table = WE().consts.tables.FILE_TABLE;
var votings = WE().util.getDynamicVar(document, 'loadVarVoting', 'data-voting');

function toggle(id) {
	var elem = document.getElementById(id);
	elem.style.display = (elem.style.display == "none" ? "block" : "none");
}

function setVisible(id, visible) {
	var elem = document.getElementById(id);
	elem.style.display = (visible ? "block" : "none");
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var i;

	switch (args[0]) {
		case "switchPage":
			document.we_form.cmd.value = args[0];
			document.we_form.tabnr.value = args[1];
			submitForm();
			break;
		case "we_voting_dirSelector":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
			args[0] = "we_voting_dirSelector";
			for (i = 0; i < args.length; i++) {
				url += "we_cmd[]=" + encodeURI(args[i]);
				if (i < (args.length - 1)) {
					url += "&";
				}
			}
			new (WE().util.jsWindow)(caller, url, "we_votingSelector", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "browse_server":
			new (WE().util.jsWindow)(caller, url, "browse_server", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, false, true);
			break;
		case "we_users_selector":
			new (WE().util.jsWindow)(caller, url, "browse_users", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, false, true);
			break;
		case "users_add_owner":
			var owners = args[1].allPaths;
			var isfolders = args[2].allIsFolder;

			for (i = 0; i < owners.length; i++) {
				if (owners[i] !== "") {
					owners_label.addItem();
					owners_label.setItem(0, (owners_label.itemCount - 1), WE().util.getTreeIcon(isfolders[i] == 1 ? WE().consts.contentTypes.FOLDER : "we/user") + " " + owners[i]);
					owners_label.showVariant(0);
				}
			}
			break;
		case "export_csv":
			var oldcmd = document.we_form.cmd.value;
			var oldpnt = document.we_form.pnt.value;
			document.we_form.question_name.value = question_edit.name;
			document.we_form.answers_name.value = answers_edit.name;
			document.we_form.variant_count.value = answers_edit.variantCount;
			document.we_form.item_count.value = answers_edit.itemCount;
			document.we_form.cmd.value = args[0];
			document.we_form.pnt.value = args[0];
			new (WE().util.jsWindow)(caller, "", "export_csv", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, false, true);
			submitForm("export_csv");
			document.we_form.cmd.value = oldcmd;
			document.we_form.pnt.value = oldpnt;
			break;
		case "exportGroup_csv":
			oldcmd = document.we_form.cmd.value;
			oldpnt = document.we_form.pnt.value;
			document.we_form.cmd.value = args[0];
			document.we_form.pnt.value = args[0];
			new (WE().util.jsWindow)(caller, "", "exportGroup_csv", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, false, true);
			submitForm("exportGroup_csv");
			document.we_form.cmd.value = oldcmd;
			document.we_form.pnt.value = oldpnt;
			break;

		case  "reset_ipdata":
			WE().util.showConfirm(window, "", WE().consts.g_l.voting.delete_ipdata_question, ["reset_ipdata_do"]);
			break;
		case "reset_ipdata_do":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=reset_ipdata";
			new (WE().util.jsWindow)(caller, url, "reset_ipdata", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, false, true);
			var t = document.getElementById("ip_mem_size");
			setVisible("delete_ip_data", false);
			t.innerHTML = "0";
			break;
		case "delete_log":
			WE().util.showConfirm(window, "", WE().consts.g_l.voting.delete_log_question, ["delete_log_do"]);
			break;
		case "delete_log_do":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=delete_log";
			new (WE().util.jsWindow)(caller, url, "delete_log", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, false, true);
			break;
		case "show_log":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=" + args[0];
			new (WE().util.jsWindow)(caller, url, "show_log", WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, true, true);
			break;
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function submitForm(target, action, method, form) {
	var f = form ? window.document.forms[form] : window.document.we_form;
	f.target = (target ? target : "edbody");
	f.action = (action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting");
	f.method = (method ? method : "post");

	f.submit();
}

function callAnswerLimit() {
	WE().util.showMessage(WE().consts.g_l.voting.answer_limit, WE().consts.message.WE_MESSAGE_ERROR, window);
}

function removeAll() {
	for (var i = 0; i < iptable_label.itemCount + 1; i++) {
		top.content.editor.edbody.iptable_label.delItem(i);
	}
}

function newIp() {
	var ip = window.prompt(WE().consts.g_l.voting.new_ip_add, "");
	var re = new RegExp("[a-zA-Z|,]");
	if (ip.match(re) !== null) {
		WE().util.showMessage(WE().consts.g_l.voting.not_valid_ip, WE().consts.message.WE_MESSAGE_ERROR, window);
		return;
	}

	re = new RegExp("^(([0-2|\*]?[0-9|\*]{1,2}\.){3}[0-2|\*]?[0-9|\*]{1,2})");

	if (ip.match(re) !== null) {

		var p = ip.split(".");
		for (var i = 0; i < p.length; i++) {
			var t = p[i];
			t.replace("*", "");
			if (parseInt(t) > 255) {
				WE().util.showMessage(WE().consts.g_l.voting.not_valid_ip, WE().consts.message.WE_MESSAGE_ERROR, window);
				return false;
			}
		}

		top.content.editor.edbody.iptable_label.addItem();
		top.content.editor.edbody.iptable_label.setItem(0, (top.content.editor.edbody.iptable_label.itemCount - 1), ip);
		top.content.editor.edbody.iptable_label.showVariant(0);
	} else {
		WE().util.showMessage(WE().consts.g_l.voting.not_valid_ip, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}


function refreshTexts() {
	document.getElementById("question_score").innerHTML = document.we_form[question_edit.name + "_item0"].value;
	for (var i = 0; i < answers_edit.itemCount; i++) {
		document.getElementById("answers_score_" + i).innerHTML = document.we_form[answers_edit.name + "_item" + i].value;
	}
}

function checkValue(elem, oldval) {
	var r = parseInt(elem.value);
	if (isNaN(r)) {
		elem.value = oldval;
	} else {
		elem.value = r;
		document.we_form.scores_changed.value = 1;
	}
	refreshTotal();
}

function resetScores(max) {
	if (!window.confirm(WE().consts.g_l.voting.result_delete_alert)) {
		return;
	}
	var elems = document.we_form.elements;
	for (var i = 0; i < elems.length; i++) {
		if (elems[i].name.match(/^scores_/)) {
			elems[i].value = 0;
		}
	}
	document.we_form.scores_changed.value = 1;
	refreshTotal();

}

function refreshTotal() {
	var total = 0, percent,
					i;
	var elems = document.we_form.elements;
	for (i = 0; i < elems.length; i++) {
		if (elems[i].name.match(/^scores_/)) {
			total += parseInt(elems[i].value);
		}
	}

	var t = document.getElementById("total");
	t.innerHTML = total;

	for (i = 0; i < elems.length; i++) {
		if (elems[i].name.match(/^scores_/)) {
			percent = (total ?
							Math.round((parseInt(elems[i].value) / total) * 100) :
							0);
		}
	}
}

function setMultiEdits() {
	var i, value, k, v, akey, aval, aval2, aval3, aval4;
	owners_label = new (WE().util.multi_edit)("owners", window, 0, votings.delBut, 510, false);
	owners_label.addVariant();

	for (i in votings.owners) {
		owners_label.addItem();
		owners_label.setItem(0, (owners_label.itemCount - 1), WE().util.getTreeIcon(votings.owners[i].IsFolder ? WE().consts.contentTypes.FOLDER : 'we/user') + votings.owners[i].Path);
	}
	owners_label.showVariant(0);

	if (votings.isFolder) {
		return;
	}
	question_edit = new (WE().util.multi_edit)("question", window, 1, "", 520, true);
	answers_edit = new (WE().util.multi_editMulti)("answers", document.we_form, 0, votings.delBut1, 500, true);
	answers_edit.SetImageIDText(WE().consts.g_l.voting.imageID_text);
	answers_edit.SetMediaIDText(WE().consts.g_l.voting.mediaID_text);
	answers_edit.SetSuccessorIDText(WE().consts.g_l.voting.successorID_text);
	for (i = 0; i < votings.answerCount; i++) {
		answers_edit.addItem("2");
	}
	for (var variant in votings.QASet) {
		value = votings.QASet[variant];
		question_edit.addVariant();
		answers_edit.addVariant();
		for (k in value) {
			v = value[k];
			switch (k) {
				case 'question':
					question_edit.setItem(variant, 0, v);
					break;
				case 'answers':
					for (akey in v) {
						aval = v[akey];
						if ((votings.QASetAdditions[variant]) && (votings.QASetAdditions[variant].imageID[akey])) {
							aval2 = votings.QASetAdditions[variant].imageID[akey];
							aval3 = votings.QASetAdditions[variant].mediaID[akey];
							aval4 = votings.QASetAdditions[variant].successorID[akey];
						} else {
							aval2 = aval3 = aval4 = '';
						}
						answers_edit.setItem(variant, akey, aval);
						answers_edit.setItemImageID(variant, akey, aval2);
						answers_edit.setItemMediaID(variant, akey, aval3);
						answers_edit.setItemSuccessorID(variant, akey, aval4);
					}
					break;
			}
		}
	}

	answers_edit.delRelatedItems = true;
	question_edit.showVariant(0);
	answers_edit.showVariant(0);
	question_edit.showVariant(votings.showVariant);
	answers_edit.showVariant(votings.showVariant);


	answers_edit.SetMinCount(votings.allow.freeText ? 1 : 2);
	answers_edit.setImages(votings.allow.images);
	answers_edit.setMedia(votings.allow.media);
	answers_edit.setSuccessors(votings.allow.successor);

	iptable_label = new (WE().util.multi_edit)("iptable", window, 0, votings.delBut, 510, false);
	iptable_label.addVariant();

	for (i in votings.blackList) {
		iptable_label.addItem();
		iptable_label.setItem(0, (iptable_label.itemCount - 1), votings.blackList[i]);
	}
	if (votings.blackList) {
		top.content.setHot();
	}

	iptable_label.showVariant(0);
}