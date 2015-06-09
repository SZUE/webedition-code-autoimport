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

function treeStartDrag(evt, type, table, id, ct) {
	evt.dataTransfer.setData('text', type + ',' + table + ',' + id + ',' + ct);
}

function getTreeLayout() {
	return this.tree_layouts[this.state];
}

function setTreeState() {
	this.state = arguments[0];
	if (this.state == this.tree_states.edit) {
		for (var i = 1; i <= this.len; i++) {
			if (this[i].checked == 1) {
				this[i].checked = 0;
			}
		}
	}
}

function applyLayout() {
	eval("if(" + treeData.treeFrame + ".document.getElementById(\"lab_" + this.id + "\"))" + treeData.treeFrame + ".document.getElementById(\"lab_" + this.id + "\").className =\"" +
					(arguments[0] ? arguments[0] : this.getlayout()) +
					"\";");
}

function rootEntry(id, text, rootstat, offset) {
	this.id = id;
	this.text = text;
	this.open = 1;
	this.loaded = 1;
	this.typ = "root";
	this.offset = offset;
	this.rootstat = rootstat;
	this.showsegment = showSegment;
	this.clear = clearItems;

	return this;
}

function node(attribs) {
	for (var aname in attribs) {
		var val = attribs[aname];
		this[aname] = val;
	}

	this.getlayout = getLayout;
	this.applylayout = applyLayout;
	this.showsegment = showSegment;
	this.clear = clearItems;
	return this;
}

function selectNode() {
	if (arguments[0]) {
		var ind;
		if (treeData.selection !== "" && treeData.selection_table == treeData.table) {
			ind = indexOfEntry(treeData.selection);
			if (ind != -1) {
				var oldnode = get(treeData.selection);
				oldnode.selected = 0;
				oldnode.applylayout();
			}
		}
		ind = indexOfEntry(arguments[0]);
		if (ind != -1) {
			var newnode = get(arguments[0]);
			newnode.selected = 1;
			newnode.applylayout();
		}
		treeData.selection = arguments[0];
		treeData.selection_table = treeData.table;
	}
}

function unselectNode() {
	if (treeData.selection !== "" && treeData.table == treeData.selection_table) {
		var ind = indexOfEntry(treeData.selection);
		if (ind != -1) {
			var node = get(treeData.selection);
			node.selected = 0;
			if (node.applylayout)
				node.applylayout();
		}
		treeData.selection = "";
	}
}

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
		if (treeData[i].typ == "group" && treeData[i].open == 1) {
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

function setCheckNode(imgName) {
	if (document.images[imgName]) {
		document.images[imgName].src = "/webEdition/images/tree/check0.gif";
	}
}

function setUnCheckNode(imgName) {
	if (document.images[imgName]) {
		document.images[imgName].src = "/webEdition/images/tree/check1.gif";
	}
}

function clearItems() {
	var ai = 1;
	var deleted = 0;

	while (ai <= treeData.len) {
		if (treeData[ai].parentid == this.id) {
			if (treeData[ai].contenttype == "group") {
				deleted += treeData[ai].clear();
			} else {
				ind = ai;
				while (ind <= treeData.len - 1) {
					treeData[ind] = treeData[ind + 1];
					ind++;
				}
				treeData.len[treeData.len] = null;
				treeData.len--;
			}
			deleted++;
		} else {
			ai++;
		}
	}
	drawTree();
	return deleted;
}


function clickHandler(cur) {
	var row = "";
	if (treeData.selection_table == treeData.table && cur.id == treeData.selection) {
		cur.selected = 1;
	}
	if (cur.disabled != 1) {
		if (treeData.state == treeData.tree_states.select) {
			row += "<a href=\"javascript:" + treeData.topFrame + ".checkNode('img_" + cur.id + "')\">";
		} else if (treeData.state == treeData.tree_states.selectitem && cur.typ == "item") {
			row += "<a href=\"javascript:" + treeData.topFrame + ".checkNode('img_" + cur.id + "')\">";
		} else if (treeData.state == treeData.tree_states.selectgroup && cur.typ == "group") {
			row += "<a href=\"javascript:" + treeData.topFrame + ".checkNode('img_" + cur.id + "')\">";
		} else {
			row += "<a name=\"_" + cur.id + "\" href=\"javascript://\"  ondblclick=\"" + treeData.topFrame + ".wasdblclick=true;clearTimeout(" + treeData.topFrame + ".tout);" + treeData.topFrame + ".doClick('" + cur.id + "');return true;\" onclick=\"" + treeData.topFrame + ".tout=setTimeout('if(!" + treeData.topFrame + ".wasdblclick){ " + treeData.topFrame + ".doClick(\\'" + cur.id + "\\'); }else{ " + treeData.topFrame + ".wasdblclick=false;}',300);return true;\" onmouseover=\"" + treeData.topFrame + ".info('ID:" + (cur.we_id ? cur.we_id : cur.id) + "')\" onmouseout=\"" + treeData.topFrame + ".info(' ');\">";
		}
	}
	row += getTreeIcon(cur.contenttype, cur.open, cur.text.replace(/^.*\./, ".")) +
					(cur.disabled != 1 ?
									"</a>" :
									""
									);
	if (cur.disabled != 1) {
		switch (treeData.state) {
			case treeData.tree_states.selectitem:
				row += (cur.typ == "group" ?
								"<label id=\"lab_" + cur.id + "\"" + (cur.tooltip !== "" ? " title=\"" + (cur.tooltip ? cur.tooltip : cur.id) + "\"" : "") + " class=\"" + cur.getlayout() + (cur.class ? ' ' + cur.class : '') + "\">" + cur.text + "</label>" :
								"<a href=\"javascript:" + treeData.topFrame + ".checkNode('img_" + cur.id + "')\"><i class=\"fa fa-" + (cur.checked == 1 ? 'check-' : '') + 'square-o wecheckIcon" name="img_' + cur.id + '"></i></a>' +
								"<label id=\"lab_" + cur.id + "\"" + (cur.tooltip !== "" ? " title=\"" + (cur.tooltip ? cur.tooltip : cur.id) + "\"" : "") + " class=\"" + cur.getlayout() + (cur.class ? ' ' + cur.class : '') + "\" onclick=\"" + treeData.topFrame + ".checkNode('img_" + cur.id + "')\">" + cur.text + "</label>"
								);
				break;
			case treeData.tree_states.selectgroup:
				row += (cur.typ == "item" ?
								"<label id=\"lab_" + cur.id + "\"" + (cur.tooltip !== "" ? " title=\"" + (cur.tooltip ? cur.tooltip : cur.id) + "\"" : "") + " class=\"" + cur.getlayout() + (cur.class ? ' ' + cur.class : '') + "\">" + cur.text + "</label>" :
								"<a href=\"javascript:" + treeData.topFrame + ".checkNode('img_" + cur.id + "')\"><i class=\"fa fa-" + (cur.checked == 1 ? 'check-' : '') + 'square-o wecheckIcon" name="img_' + cur.id + '"></i></a>' +
								"<label id=\"lab_" + cur.id + "\"" + (cur.tooltip !== "" ? " title=\"" + (cur.tooltip ? cur.tooltip : cur.id) + "\"" : "") + " class=\"" + cur.getlayout() + (cur.class ? ' ' + cur.class : '') + "\" onclick=\"" + treeData.topFrame + ".checkNode('img_" + cur.id + "')\">" + cur.text + "</label>"
								);
				break;
			case treeData.tree_states.select:
				row += '<a href="javascript:' + treeData.topFrame + ".checkNode('img_" + cur.id + "')\"><i class=\"fa fa-" + (cur.checked == 1 ? 'check-' : '') + 'square-o wecheckIcon" name="img_' + cur.id + '"></i></a>' +
								"<label id=\"lab_" + cur.id + "\"" + (cur.tooltip !== "" ? " title=\"" + (cur.tooltip ? cur.tooltip : cur.id) + "\"" : "") + " class=\"" + cur.getlayout() + (cur.class ? ' ' + cur.class : '') + "\" onclick=\"" + treeData.topFrame + ".checkNode('img_" + cur.id + "')\">" + cur.text + "</label>";
				break;
			default:
				row += "<a ondragstart=\"treeStartDrag(event,'" + (cur.contenttype === 'folder' ? 'dragFolder' : 'dragItem') + "','" + cur.table + "'," + parseInt(cur.id) + ", '" + cur.contenttype + "')\" name=\"_" + cur.id + "\" href=\"javascript://\"  ondblclick=\"" + treeData.topFrame + ".wasdblclick=true;clearTimeout(" + treeData.topFrame + ".tout);" + treeData.topFrame + ".doClick('" + cur.id + "');return true;\" onclick=\"" + treeData.topFrame + ".tout=setTimeout('if(!" + treeData.topFrame + ".wasdblclick){" + treeData.topFrame + ".doClick(\\'" + cur.id + "\\'); }else{ " + treeData.topFrame + ".wasdblclick=false;}',300);return true;\" onmouseover=\"" + treeData.topFrame + ".info('ID:" + (cur.we_id ? cur.we_id : cur.id) + "')\" onmouseout=\"" + treeData.topFrame + ".info(' ');\"><label id=\"lab_" + cur.id + "\"" + (cur.tooltip !== "" ? " title=\"" + (cur.tooltip ? cur.tooltip : cur.id) + "\"" : "") + " class=\"" + cur.getlayout() + (cur.class ? ' ' + cur.class : '') + "\">" + cur.text + "</label></a>";
		}
	} else {
		row += "<label id=\"lab_" + cur.id + "\"" + (cur.tooltip !== "" ? " title=\"" + (cur.tooltip ? cur.tooltip : cur.id) + "\"" : "") + " class=\"" + cur.getlayout() + (cur.class ? ' ' + cur.class : '') + "\">" + cur.text + "</label>";
	}
	row += "<br/>";
	return row;
}

function drawItem(nf, ai) {
	return '<span class="treeKreuz ' + (ai == nf.len ? "kreuzungend" : "kreuzung") + '"></span>' + clickHandler(nf[ai]);
}

function drawThreeDots(nf, ai) {
	return '<span class="treeKreuz kreuzungend"></span>' +
					"<a name=\"_" + nf[ai].id + "\" href=\"javascript://\"  onclick=\"" + treeData.topFrame + ".setSegment('" + nf[ai].id + "');return true;\">" +
					'<span class="threedots"><i class="fa fa-' + (nf[ai].contenttype == 'arrowup' ? 'caret-up' : 'caret-down') + '"></i></span></a><br/>';
}

function drawGroup(nf, ai, zweigEintrag) {
	var newAst = zweigEintrag;

	row = "<a href=\"javascript:" + treeData.topFrame + ".setScrollY();" + treeData.topFrame + ".openClose('" + nf[ai].id + "')\"><span class='treeKreuz fa-stack " + (ai == nf.len ? "kreuzungend" : "kreuzung") + "'><i class='fa fa-square fa-stack-1x we-color'></i><i class='fa fa-" + (nf[ai].open === 0 ? "plus" : "minus") + "-square-o fa-stack-1x'></i></span></a>";

	row += clickHandler(nf[ai]);

	if (nf[ai].open == 1) {
		if (ai == nf.len) {
			newAst += "<span class=\"treeKreuz\"></span>";
		} else {
			newAst += '<span class="strich treeKreuz "></span>';
		}
		row += draw(nf[ai].id, newAst);
	}
	return row;
}

function indexOfEntry(id) {
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].id == id) {
			return ai;
		}
		ai++;
	}
	return -1;
}

function get(eintrag) {
	var nf = new container();
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].id == eintrag) {
			nf = treeData[ai];
		}
		ai++;
	}
	return nf;
}

function search(eintrag) {
	var nf = new container();
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].parentid == eintrag) {
			nf.add(treeData[ai]);
		}
		ai++;
	}
	return nf;
}

function add(object) {
	this[++this.len] = object;
}

function updateTreeAfterDel(ind) {
	if (ind !== 0) {
		ai = ind;
		while (ai <= treeData.len - 1) {
			treeData[ai] = treeData[ai + 1];
			ai++;
		}
		treeData.len[treeData.len] = null;
		treeData.len--;
		drawTree();
	}
}


function addSort(object) {
	this.len++;
	for (var i = this.len; i > 0; i--) {
		if (i > 1 && (this[i - 1].text.toLowerCase() > object.text.toLowerCase() || (this[i - 1].typ > object.typ))) {
			this[i] = this[i - 1];
		} else {
			this[i] = object;
			break;
		}
	}
}

function getLayout() {
	var layout_key = (this.typ == "group" ? "group" : "item");
	return treeData.node_layouts[layout_key];
}

function showSegment() {
	parentnode = frames.top.get(this.parentid);
	parentnode.clear();
	we_cmd("loadFolder", treeData.table, parentnode.id, "", "", "", this.offset);
	toggleBusy(1);
}

function openClose(id) {
	if (id == "") {
		return;
	}

	var eintragsIndex = indexOfEntry(id);

	openstatus = (treeData[eintragsIndex].open == 0 ? 1 : 0);

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
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].id == attribs["id"]) {
			for (aname in attribs) {
				treeData[ai][aname] = attribs[aname];
			}
		}
		ai++;
	}
}

function checkNode(imgName) {
	var object_name = imgName.substring(4, imgName.length);
	for (i = 1; i <= treeData.len; i++) {
		if (treeData[i].id != object_name) {
			continue;
		}
		if (treeData[i].checked == 1) {
			treeData[i].checked = 0;
			treeData[i].applylayout();
			try {
				eval("if(" + treeData.treeFrame + ".document.getElementsByName(imgName)){var tmp=" + treeData.treeFrame + ".document.getElementsByName(imgName)[0];tmp.classList.remove('fa-check-square-o');tmp.classList.add('fa-square-o');}");
			} catch (e) {
				self.Tree.setCheckNode(imgName);
			}
			break;
		} else {
			treeData[i].checked = 1;
			treeData[i].applylayout();
			try {
				eval("if(" + treeData.treeFrame + ".document.getElementsByName(imgName)){var tmp=" + treeData.treeFrame + ".document.getElementsByName(imgName)[0];tmp.classList.remove('fa-square-o');tmp.classList.add('fa-check-square-o');}");
			} catch (e) {
				self.Tree.setUnCheckNode(imgName);
			}
			break;
		}
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
			frames.top.we_scrollY[treeData.table] = (document.body.scrollTop === undefined ? pageYOffset : document.body.scrollTop);
		}
	}
}

function makeNewEntry(icon, id, pid, txt, open, ct, tab) {
	if (treeData.table == tab) {
		if (treeData[indexOfEntry(pid)]) {
			if (treeData[indexOfEntry(pid)].loaded) {

				var attribs = {
					"id": id,
					"icon": icon,
					"text": txt,
					"parentid": pid,
					"open": open,
					"typ": (ct == "folder" ? "group" : "item"),
					"table": tab,
					"tooltip": id,
					"contenttype": ct,
					"disabled": 0,
					"selected": 0
				};
				if (attribs["typ"] == "item") {
					attribs["published"] = 0;
				}

				treeData.addSort(new node(attribs));

				drawTree();
			}
		}
	}
}
