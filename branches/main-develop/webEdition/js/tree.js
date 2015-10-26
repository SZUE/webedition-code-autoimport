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
	setState: function () {
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
			if (this.selection !== "" && this.selection_table == this.table) {
				ind = this.indexOfEntry(this.selection);
				if (ind !== -1) {
					var oldnode = this.get(this.selection);
					oldnode.selected = 0;
					oldnode.applylayout();
				}
			}
			ind = this.indexOfEntry(arguments[0]);
			if (ind != -1) {
				var newnode = this.get(arguments[0]);
				newnode.selected = 1;
				newnode.applylayout();
			}
			this.selection = arguments[0];
			this.selection_table = this.table;
		}
	},
	unselectNode: function () {
		if (this.selection !== "" && this.table == this.selection_table) {
			var ind = this.indexOfEntry(this.selection);
			if (ind !== -1) {
				var node = this.get(this.selection);
				node.selected = 0;
				if (node.applylayout)
					node.applylayout();
			}
			this.selection = "";
		}
	},
	clear: function () {
		this.len = 0;
	},
	indexOfEntry: function (id) {
		for (var ai = 1; ai <= this.len; ai++) {
			if (this[ai].id == id) {
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
	},
	deleteEntry: function (id) {
		var ind = 0;
		for (var ai = 1; ai <= this.len; ai++) {
			if (this[ai].id == id) {
				ind = ai;
				break;
			}
		}
		if (ind !== 0) {
			for (ai = ind; ai <= this.len - 1; ai++) {
				this[ai] = this[ai + 1];
			}
			this.len[this.len] = null;
			this.len--;
			drawTree();
		}
	},
	setSegment: function (id) {
		var node = frames.top.treeData.get(id);
		node.showsegment();
	},
	drawThreeDots: function (nf, ai) {
		return '<span class="treeKreuz kreuzungend"></span>' +
						'<a name="_' + nf[ai].id + '" href="javascript://"  onclick="' + this.topFrame + ".treeData.setSegment('" + nf[ai].id + '\');return true;">' +
						'<span class="threedots"><i class="fa fa-' + (nf[ai].contenttype == 'arrowup' ? 'caret-up' : 'caret-down') + '"></i></span></a><br/>';
	},
	drawItem: function (nf, ai) {
		return '<span class="treeKreuz ' + (ai == nf.len ? "kreuzungend" : "kreuzung") + '"></span>' + this.clickHandler(nf[ai]);
	},
	drawGroup: function (nf, ai, zweigEintrag) {
		var newAst = zweigEintrag;
		row = "<a href=\"javascript:" + this.topFrame + ".setScrollY();" + this.topFrame + ".treeData.openClose('" + nf[ai].id + "')\"><span class='treeKreuz fa-stack " + (ai == nf.len ? "kreuzungend" : "kreuzung") + "'><i class='fa fa-square fa-stack-1x we-color'></i><i class='fa fa-" + (nf[ai].open ? "minus" : "plus") + "-square-o fa-stack-1x'></i></span></a>";
		row += this.clickHandler(nf[ai]);
		if (nf[ai].open) {
			newAst += (ai == nf.len ?
							"<span class=\"treeKreuz\"></span>" :
							'<span class="strich treeKreuz "></span>'
							);
			row += this.draw(nf[ai].id, newAst);
		}
		return row;
	},
	drawSort: function (nf, ai, zweigEintrag) {
		//overwritten
	},
	makeFoldersOpenString: function () {
		var op = "";
		for (i = 1; i <= treeData.len; i++) {
			if (treeData[i].typ == "group" && treeData[i].open) {
				op += treeData[i].id + ",";
			}
		}
		op = op.substring(0, op.length - 1);
		return op;
	},
	updateEntry: function (attribs) {
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
	},
	search: function (eintrag) {
		var nf = new container();
		for (var ai = 1; ai <= this.len; ai++) {
			if (this[ai].parentid == eintrag) {
				nf.add(this[ai]);
			}
		}
		return nf;
	},
	makeNewEntry: function (attribs) {
		if (table && this.table != table) {
			return;
		}
		var pos = this.indexOfEntry(attribs.parentid);
		if (this[pos] && this[pos].loaded) {
			attribs.typ = (attribs.contenttype === "folder" ? "group" : "item");
			attribs.tooltip = attribs.id;
			attribs.disabled = 0;
			attribs.selected = 0;
			if (attribs.typ == "item") {
				attribs.published = 0;
			}

			this.addSort(new node(attribs));
			drawTree();
		}
	},
	clickHandler: function (cur) {
		var row = "<span>";
		var href = false;
		var select = false;
		if (this.selection_table == this.table && cur.id == this.selection) {
			cur.selected = 1;
		}
		switch (cur.disabled ? -1 : this.state) {
			case -1:
				href = false;
				break;
			case this.tree_states.selectitem:
				href = cur.typ != "group";
				select = true;
				break;
			case this.tree_states.selectgroup:
				href = cur.typ != "item";
				select = true;
				break;
			case this.tree_states.select:
				href = true;
				select = true;
				break;
			default:
				href = true;
				row += "<a ondragstart=\"treeStartDrag(event,'" + (cur.contenttype === 'folder' ? 'dragFolder' : 'dragItem') + "','" + cur.table + "'," + parseInt(cur.id) + ", '" + cur.contenttype + "')\" name=\"_" + cur.id + "\" href=\"javascript://\"  ondblclick=\"" + this.topFrame + ".wasdblclick=true;clearTimeout(" + this.topFrame + ".tout);" + this.topFrame + ".doClick('" + cur.id + "');return true;\" onclick=\"" + this.topFrame + ".tout=setTimeout('if(!" + this.topFrame + ".wasdblclick){" + this.topFrame + ".doClick(\\'" + cur.id + "\\'); }else{ " + this.topFrame + ".wasdblclick=false;}',300);return true;\" onmouseover=\"" + this.topFrame + ".info('ID:" + cur.id + "')\" onmouseout=\"" + this.topFrame + ".info(' ');\">";
		}
		row += (select && href ? '<a href="javascript:' + this.topFrame + ".treeData.checkNode('img_" + cur.id + "')\">" : '') +
						WE().util.getTreeIcon(cur.contenttype, cur.open, cur.text.replace(/^.*\./, ".")) +
						(cur.inschedule > 0 ? '<i class="fa fa-clock-o"></i> ' : '') +
						(select && href ? '<i class="fa fa-' + (cur.checked ? 'check-' : '') + 'square-o wecheckIcon" name="img_' + cur.id + '"></i>' : '') +
						'<label id="lab_' + cur.id + '"' + (cur.tooltip !== "" ? ' title="' + (cur.tooltip ? cur.tooltip : cur.id) + '"' : "") + ' class="' + cur.getLayout() + (cur.class ? ' ' + cur.class : '') + '">' + cur.text + "</label>" +
						(href ? "</a>" : "") +
						"</span><br/>";
		return row;
	},
	draw: function (startEntry, zweigEintrag) {
		var nf = this.search(startEntry);
		var row = "";
		for (var ai = 1; ai <= nf.len; ai++) {
			row += zweigEintrag;
			var pind = this.indexOfEntry(nf[ai].parentid);
			if (pind != -1) {
				if (this[pind].open) {
					switch (nf[ai].typ) {
						case "item":
							row += this.drawItem(nf, ai);
							break;
						case "threedots":
							row += this.drawThreeDots(nf, ai);
							break;
						case "folder":
							row += this.drawFolder(nf, ai, zweigEintrag);
							break;
						case "group":
							row += this.drawGroup(nf, ai, zweigEintrag);
							break;
						case "sort":
							row += this.drawSort(nf, ai, zweigEintrag);
							break;
						case "shop":
							row += this.drawShop(nf, ai, zweigEintrag);
							break;
					}
				}
			}
		}
		return row;
	},
	openClose: function (id) {
		if (id == "") {
			return;
		}

		var eintragsIndex = this.indexOfEntry(id);
		var openstatus = (this[eintragsIndex].open ? 0 : 1);
		this[eintragsIndex].open = openstatus;
		if (openstatus && !this[eintragsIndex].loaded) {
			top.content.cmd.location = this.frameset + "?pnt=cmd&pid=" + id;
		} else {
			drawTree();
		}
		if (openstatus) {
			this[eintragsIndex].loaded = true;
		}
	},
	get: function (eintrag) {
		var nf = new container();
		for (var ai = 1; ai <= this.len; ai++) {
			if (this[ai].id == eintrag) {
				return this[ai];
			}
		}
		return nf;
	},
	checkNode: function (imgName) {
		var object_name = imgName.substring(4, imgName.length);
		for (var i = 1; i <= this.len; i++) {
			if (this[i].id != object_name) {
				continue;
			}
			if (this[i].checked) {
				this[i].checked = 0;
				this[i].applylayout();
				try {
					eval("if(" + this.treeFrame + ".document.getElementsByName(imgName)){var tmp=" + this.treeFrame + ".document.getElementsByName(imgName)[0];tmp.classList.remove('fa-check-square-o');tmp.classList.add('fa-square-o');}");
				} catch (e) {

				}
				break;
			}
			this[i].checked = 1;
			this[i].applylayout();
			try {
				eval("if(" + this.treeFrame + ".document.getElementsByName(imgName)){var tmp=" + this.treeFrame + ".document.getElementsByName(imgName)[0];tmp.classList.remove('fa-square-o');tmp.classList.add('fa-check-square-o');}");
			} catch (e) {

			}
			break;
		}

		if (!document.images) {
			drawTree();
		}
	},
	parentChecked: function (start) {
		for (var i = 1; i <= this.len; i++) {
			if (this[i].id == start) {
				if (this[i].checked == 1) {
					return true;
				}

				if (this[i].parentid !== 0) {
					this.parentChecked(this[i].parentid);
				}
			}
		}

		return false;
	}

};

function treeStartDrag(evt, type, table, id, ct) { // TODO: throw out setData
	top.dd.dataTransfer.text = type + ',' + table + ',' + id + ',' + ct;
	evt.dataTransfer.setData('text', type + ',' + table + ',' + id + ',' + ct);
}


function node(attribs) {
	for (var aname in attribs) {
		var val = attribs[aname];
		this[aname] = val;
	}

	return this;
}

node.prototype = {
	rootEntry: function (id, text, rootstat, offset) {
		return new node({
			id: id,
			text: text,
			open: 1,
			loaded: true,
			typ: "root",
			offset: offset,
			rootstat: rootstat
		});
	},
	getLayout: function () {
		var layout_key = (this.typ === "group" ? "group" : "item");
		return treeData.node_layouts[layout_key];
	},
	showSegment: function () {
		parentnode = frames.top.treeData.get(this.parentid);
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
			if (treeData[ai].contenttype === "group") {
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

function info(text) {
}

function setScrollY() {
	if (frames.top) {
		if (frames.top.we_scrollY) {
			frames.top.we_scrollY[treeData.table] = pageYOffset;
		}
	}
}

function setHot() {
	hot = 1;
}
function usetHot() {
	hot = 0;
}
