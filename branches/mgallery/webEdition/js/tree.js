/* global top */

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

var startloc = 0;
var treeHTML;
var wasdblclick = false;
var tout = null;
var hot = 0;

function container() {
	this.len = 0;
	this.state = 0;
	this.startloc = 0;
	this.table = "";
	this.selection = "";
	this.selection_table = "";
	return this;
}

container.prototype = {
	getLayout: function () {
		return this.tree_layouts[this.state];
	},
	setTreeState: function () {
		this.state = arguments[0];
		if (this.state == this.tree_states.edit) {
			for (var i = 1; i <= this.len; i++) {
				if (this[i].checked == 1) {
					this[i].checked = 0;
				}
			}
		}
	},
	selectNode: function () {
		if (arguments[0]) {
			var ind;
			if (treeData.selection !== "" && treeData.selection_table == treeData.table) {
				ind = treeData.indexOfEntry(treeData.selection);
				if (ind != -1) {
					var oldnode = get(treeData.selection);
					oldnode.selected = 0;
					oldnode.applylayout();
				}
			}
			ind = treeData.indexOfEntry(arguments[0]);
			if (ind != -1) {
				var newnode = get(arguments[0]);
				newnode.selected = 1;
				newnode.applylayout();
			}
			treeData.selection = arguments[0];
			treeData.selection_table = treeData.table;
		}
	},
	unselectNode: function () {
		if (treeData.selection !== "" && treeData.table == treeData.selection_table) {
			var ind = treeData.indexOfEntry(treeData.selection);
			if (ind != -1) {
				var node = get(treeData.selection);
				node.selected = 0;
				if (node.applylayout)
					node.applylayout();
			}
			treeData.selection = "";
		}
	},
	clear: function () {
		this.len = 0;
	},
	indexOfEntry: function (id) {
		for (var ai = 1; ai <= treeData.len; ai++) {
			if (treeData[ai].id == id) {
				return ai;
			}
		}
		return -1;
	},
	add: function (object) {
		this[++this.len] = object;
	},
	addSort: function (object) {
		this.len++;
		for (var i = this.len; i > 0; i--) {
			if (i > 1 && (this[i - 1].text.toLowerCase() > object.text.toLowerCase() || (this[i - 1].typ > object.typ))) {
				this[i] = this[i - 1];
				continue;
			}
			this[i] = object;
			break;
		}
	}
};

function treeStartDrag(evt, type, table, id, ct) { // TODO: throw out setData
	top.dd.dataTransfer.text = type + ',' + table + ',' + id + ',' + ct;
	evt.dataTransfer.setData('text', type + ',' + table + ',' + id + ',' + ct);
}

function rootEntry(id, text, rootstat, offset) {
	return new node({
		id: id,
		text: text,
		open: 1,
		loaded: 1,
		typ: "root",
		offset: offset,
		rootstat: rootstat,
	});
}

function node(attribs) {
	for (var aname in attribs) {
		var val = attribs[aname];
		this[aname] = val;
	}

	return this;
}

node.prototype = {
	getLayout: function () {
		var layout_key = (this.typ == "group" ? "group" : "item");
		return treeData.node_layouts[layout_key];
	},
	showSegment: function () {
		parentnode = frames.top.get(this.parentid);
		parentnode.clear();
		we_cmd("loadFolder", treeData.table, parentnode.id, "", "", "", this.offset);
	},
	applylayout: function () {
		eval('if(' + treeData.treeFrame + '.document.getElementById("lab_' + this.id + '"))' + treeData.treeFrame + '.document.getElementById("lab_' + this.id + '").className ="' +
						(arguments[0] ? arguments[0] : this.getLayout()) +
						'";');
	},
	clear: function () {
		var deleted = 0;
		for (var ai = 1; ai <= treeData.len; ai++) {
			if (treeData[ai].parentid != this.id) {
				continue;
			}
			if (treeData[ai].contenttype == "group") {
				deleted += treeData[ai].clear();
			} else {
				ind = ai;
				while (ind <= treeData.len - 1) {
					treeData[ind] = treeData[ind + 1];
					ind++;
				}
				ai--;
				treeData.len[treeData.len] = null;
				treeData.len--;
			}
			deleted++;
		}
		drawTree();
		return deleted;
	}
};

function deleteEntry(id) {
	var ind = 0;
	for (var ai = 1; ai <= treeData.len; ai++) {
		if (treeData[ai].id == id) {
			ind = ai;
			break;
		}
	}
	if (ind !== 0) {
		for (ai = ind; ai <= treeData.len - 1; ai++) {
			treeData[ai] = treeData[ai + 1];
		}
		treeData.len[treeData.len] = null;
		treeData.len--;
		drawTree();
	}
}

function makeFoldersOpenString() {
	var op = "";
	for (i = 1; i <= treeData.len; i++) {
		if (treeData[i].typ == "group" && treeData[i].open) {
			op += treeData[i].id + ",";
		}
	}
	op = op.substring(0, op.length - 1);
	return op;
}

function clearTree() {
	treeData.clear();
}

function parentChecked(start) {
	var obj = top.treeData;
	for (var i = 1; i <= obj.len; i++) {
		if (obj[i].id == start) {
			if (obj[i].checked == 1) {
				return true;
			}

			if (obj[i].parentid !== 0) {
				parentChecked(obj[i].parentid);
			}
		}
	}

	return false;
}


function clickHandler(cur) {
	var row = "<span>";
	var href = false;
	var select = false;
	if (treeData.selection_table == treeData.table && cur.id == treeData.selection) {
		cur.selected = 1;
	}
	switch (cur.disabled ? -1 : treeData.state) {
		case -1:
			href = false;
			break;
		case treeData.tree_states.selectitem:
			href = cur.typ != "group";
			select = true;
			break;
		case treeData.tree_states.selectgroup:
			href = cur.typ != "item";
			select = true;
			break;
		case treeData.tree_states.select:
			href = true;
			select = true;
			break;
		default:
			href = true;
			row += "<a ondragstart=\"treeStartDrag(event,'" + (cur.contenttype === 'folder' ? 'dragFolder' : 'dragItem') + "','" + cur.table + "'," + parseInt(cur.id) + ", '" + cur.contenttype + "')\" name=\"_" + cur.id + "\" href=\"javascript://\"  ondblclick=\"" + treeData.topFrame + ".wasdblclick=true;clearTimeout(" + treeData.topFrame + ".tout);" + treeData.topFrame + ".doClick('" + cur.id + "');return true;\" onclick=\"" + treeData.topFrame + ".tout=setTimeout('if(!" + treeData.topFrame + ".wasdblclick){" + treeData.topFrame + ".doClick(\\'" + cur.id + "\\'); }else{ " + treeData.topFrame + ".wasdblclick=false;}',300);return true;\" onmouseover=\"" + treeData.topFrame + ".info('ID:" + (cur.we_id ? cur.we_id : cur.id) + "')\" onmouseout=\"" + treeData.topFrame + ".info(' ');\">";
	}
	row += (select && href ? '<a href="javascript:' + treeData.topFrame + ".checkNode('img_" + cur.id + "')\">" : '') +
					WE().util.getTreeIcon(cur.contenttype, cur.open, cur.text.replace(/^.*\./, ".")) +
					(cur.inschedule > 0 ? '<i class="fa fa-clock-o"></i> ' : '') +
					(select && href ? '<i class="fa fa-' + (cur.checked ? 'check-' : '') + 'square-o wecheckIcon" name="img_' + cur.id + '"></i>' : '') +
					'<label id="lab_' + cur.id + '"' + (cur.tooltip !== "" ? ' title="' + (cur.tooltip ? cur.tooltip : cur.id) + '"' : "") + ' class="' + cur.getLayout() + (cur.class ? ' ' + cur.class : '') + '">' + cur.text + "</label>" +
					(href ? "</a>" : "") +
					"</span><br/>";
	return row;
}

function drawItem(nf, ai) {
	return '<span class="treeKreuz ' + (ai == nf.len ? "kreuzungend" : "kreuzung") + '"></span>' + clickHandler(nf[ai]);
}

function drawThreeDots(nf, ai) {
	return '<span class="treeKreuz kreuzungend"></span>' +
					'<a name="_' + nf[ai].id + '" href="javascript://"  onclick="' + treeData.topFrame + ".setSegment('" + nf[ai].id + '\');return true;">' +
					'<span class="threedots"><i class="fa fa-' + (nf[ai].contenttype == 'arrowup' ? 'caret-up' : 'caret-down') + '"></i></span></a><br/>';
}

function drawGroup(nf, ai, zweigEintrag) {
	var newAst = zweigEintrag;
	row = "<a href=\"javascript:" + treeData.topFrame + ".setScrollY();" + treeData.topFrame + ".openClose('" + nf[ai].id + "')\"><span class='treeKreuz fa-stack " + (ai == nf.len ? "kreuzungend" : "kreuzung") + "'><i class='fa fa-square fa-stack-1x we-color'></i><i class='fa fa-" + (nf[ai].open ? "minus" : "plus") + "-square-o fa-stack-1x'></i></span></a>";
	row += clickHandler(nf[ai]);
	if (nf[ai].open) {
		newAst += (ai == nf.len ?
						"<span class=\"treeKreuz\"></span>" :
						'<span class="strich treeKreuz "></span>'
						);
		row += draw(nf[ai].id, newAst);
	}
	return row;
}


function get(eintrag) {
	var nf = new container();
	for (var ai = 1; ai <= treeData.len; ai++) {
		if (treeData[ai].id == eintrag) {
			return treeData[ai];
		}
	}
	return nf;
}

function search(eintrag) {
	var nf = new container();
	for (var ai = 1; ai <= treeData.len; ai++) {
		if (treeData[ai].parentid == eintrag) {
			nf.add(treeData[ai]);
		}
	}
	return nf;
}

function openClose(id) {
	if (id == "") {
		return;
	}

	var eintragsIndex = treeData.indexOfEntry(id);
	openstatus = (treeData[eintragsIndex].open ? 0 : 1);
	treeData[eintragsIndex].open = openstatus;
	if (openstatus && treeData[eintragsIndex].loaded != 1) {
		top.content.cmd.location = treeData.frameset + "?pnt=cmd&pid=" + id;
	} else {
		drawTree();
	}
	if (openstatus == 1) {
		treeData[eintragsIndex].loaded = 1;
	}
}

function info(text) {
}

function updateEntry(attribs) {
	if (attribs.table && treeData.table != attribs.table) {
		return;
	}
	var updated = false;
	for (var ai = 1; ai <= treeData.len; ai++) {
		if (treeData[ai].id == attribs["id"]) {
			updated = true;
			for (aname in attribs) {
				treeData[ai][aname] = attribs[aname];
			}
			break;
		}
	}
	if (updated) {
		drawTree();
	}
}

function checkNode(imgName) {
	var object_name = imgName.substring(4, imgName.length);
	for (var i = 1; i <= treeData.len; i++) {
		if (treeData[i].id != object_name) {
			continue;
		}
		if (treeData[i].checked) {
			treeData[i].checked = 0;
			treeData[i].applylayout();
			try {
				eval("if(" + treeData.treeFrame + ".document.getElementsByName(imgName)){var tmp=" + treeData.treeFrame + ".document.getElementsByName(imgName)[0];tmp.classList.remove('fa-check-square-o');tmp.classList.add('fa-square-o');}");
			} catch (e) {

			}
			break;
		}
		treeData[i].checked = 1;
		treeData[i].applylayout();
		try {
			eval("if(" + treeData.treeFrame + ".document.getElementsByName(imgName)){var tmp=" + treeData.treeFrame + ".document.getElementsByName(imgName)[0];tmp.classList.remove('fa-square-o');tmp.classList.add('fa-check-square-o');}");
		} catch (e) {

		}
		break;
	}

	if (!document.images) {
		drawTree();
	}
}

function zeichne(startEntry, zweigEintrag) {
	draw(startEntry, zweigEintrag);
}

function setSegment(id) {
	var node = frames.top.get(id);
	node.showsegment();
}

function setScrollY() {
	if (frames.top) {
		if (frames.top.we_scrollY) {
			frames.top.we_scrollY[treeData.table] = pageYOffset;
		}
	}
}
/*	if (attribs.table && treeData.table != attribs.table) {
 return;
 }
 */
function makeNewEntry(attribs) {
	if (table && treeData.table != table) {
		return;
	}
	var pos = treeData.indexOfEntry(attribs.parentid);
	if (treeData[pos] && treeData[pos].loaded) {
		attribs.typ = (attribs.contenttype === "folder" ? "group" : "item");
		attribs.tooltip = attribs.id;
		attribs.disabled = 0;
		attribs.selected = 0;
		if (attribs.typ == "item") {
			attribs.published = 0;
		}

		treeData.addSort(new node(attribs));
		drawTree();
	}
}

function setHot() {
	hot = 1;
}
function usetHot() {
	hot = 0;
}
