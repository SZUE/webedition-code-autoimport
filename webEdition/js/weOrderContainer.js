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

var weOrderContainer = function (id) {
	this.container = id;
	this.elements = [];
	this.position = [];

	this.init = function() {
		this.setButtonsDisabled();
		this.reinitWysiwygEditors();
	};

	this.processCommand = function (win, cmd, id, afterid){
		switch(cmd){
			case 'add':
				afterid = afterid ? afterid : null;
				this.add(win.document, id, afterid);
				break;
			case 'reload':
				this.reload(win.document, id, win);
				break;
			case 'delete':
			case 'del':
				this.del(id);
				break;
			case 'moveup':
			case 'up':
				this.up(id);
				break;
			case 'movedown':
			case 'down':
				this.down(id);
		};
		this.setButtonsDisabled();
	};

	this.add = function (doc, id, afterid) {

		var child = null;
		var node = null;
		var div = null;
		var pos = this.position.length;
		var element = [];
		var i;

		element.id = id;
		this.elements[this.elements.length] = element;

		if (afterid !== null) {
			for (i = 0; i < this.position.length; i++) {
				if (this.position[i] === afterid) {
					pos = i + 1;
				}
			}
		}

		// hinten anhängen
		if (pos >= this.position.length) {
			pos = this.position.length;
			this.position.push(element.id);
			// vorne einfügen
		} else if (pos <= 0) {
			pos = 0;
			this.position.reverse();
			this.position.push(element.id);
			this.position.reverse();

			// einfügen
		} else {
			for (i = this.position.length; i > pos; i--) {
				this.position[i] = this.position[(i - 1)];
			}
			this.position[pos] = element.id;

		}

		child = document.getElementById(this.container).childNodes;

		if (doc === document) {
			node = doc.getElementById(id);
			div = node;
		} else {
			if (document.importNode) { // Safari or Mozilla
				node = document.importNode(doc.getElementById(id), true);
			} else { // Internet Explorer
				node = doc.getElementById(id).cloneNode(true);
			}
			div = this.createDIV(node);
		}

		if (this.position.length === 1 || pos >= this.position.length - 1) {
			document.getElementById(this.container).appendChild(div);
		} else {
			document.getElementById(this.container).insertBefore(div, child[pos]);
		}
		this.fixIESelectBug(doc, id);
	};

	this.reload = function (doc, id, win /*, selectedId, selectedValue*/) {

		var found = false;
		//var pos = this.position.length;
		var node = null;
		var div;

		for (var i = 0; i < this.position.length; i++) {
			if (this.position[i] === id) {
				//pos = i;
				found = true;
				break;
			}
		}

		if (found) {
			if (doc === document) {
				node = doc.getElementById(id);
				div = node;
			} else {
				if (document.importNode) { // Safari or Mozilla
					node = document.importNode(doc.getElementById(id), true);
				} else { // Internet Explorer
					node = doc.getElementById(id).cloneNode(true);
				}
				div = this.createDIV(node);
			}

			document.getElementById(id).innerHTML = div.innerHTML;

			var wysiwygConfigs;
			if((wysiwygConfigs = WE().util.getDynamicVar(win.document, 'loadVar_tinyConfigs', 'data-configurations'))){
				window.tinyMceRawConfigurations = window.tinyMceRawConfigurations ? window.tinyMceRawConfigurations : {};
				var config;
				for(var i = 0; i < wysiwygConfigs.length; i++){
					config = wysiwygConfigs[i];
					window.tinyMceRawConfigurations[config.weFieldName] = config;
					if(config.weEditorType === 'inlineTrue'){
						WE().layout.we_tinyMCE.functions.initEditor(window, config);
					}
				}
			}
		}

		this.fixIESelectBug(doc, id);
	};

	this.del = function (id) {
		var node = null,
			i, pos;
		for (i = 0; i < this.elements.length; i++) {
			if (this.elements[i].id === id) {
				this.elements.splice(i, 1);
				i = this.elements.length;
			}
		}

		for (i = 0; i < this.position.length; i++) {
			if (this.position[i] === id) {
				pos = i;
				this.position.splice(i, 1);
				i = this.position.length;
			}
		}

		node = document.getElementById(id);
		document.getElementById(this.container).removeChild(node);

	};


	this.up = function (id) {
		var up = null,
			down = null,
			temp;

		for (var i = 1; i < this.position.length; i++) {
			if (this.position[i] === id) {
				up = document.getElementById(this.position[i]);
				down = document.getElementById(this.position[(i - 1)]);
				temp = this.position[(i - 1)];
				this.position[(i - 1)] = this.position[i];
				this.position[i] = temp;
				i = this.position.length;
			}
		}

		if (up !== null && down !== null) {
			document.getElementById(this.container).removeChild(up);
			document.getElementById(this.container).insertBefore(up, down);
		}

		this.reinitWysiwygEditors();
	};


	this.down = function (id) {
		var up = null,
			down = null,
			temp;

		for (var i = 0; i < this.position.length - 1; i++) {
			if (this.position[i] === id) {
				up = document.getElementById(this.position[i + 1]);
				down = document.getElementById(this.position[i]);
				temp = this.position[(i + 1)];
				this.position[(i + 1)] = this.position[i];
				this.position[i] = temp;
				i = this.position.length;
			}
		}

		if (up !== null && down !== null) {
			document.getElementById(this.container).removeChild(up);
			document.getElementById(this.container).insertBefore(up, down);
		}

		this.reinitWysiwygEditors();
	};

	this.setButtonsDisabled = function(){
		var i, id;
		for(i = 0; i < this.position.length; i++) {
			id = this.position[i].replace(/entry_/, '');
	
			WE().layout.button.disable(document, "btn_direction_up_" + id, false);
			WE().layout.button.disable(document, "btn_direction_down_" + id, false);

			if(i === 0) {
				WE().layout.button.disable(document, "btn_direction_up_" + id, true);
			}
			if(i + 1 === this.position.length) {
				WE().layout.button.disable(document, "btn_direction_down_" + id, true);
			}
		}
	};
	
	

	this.reinitWysiwygEditors = function(){
		var configs;
		if((configs = window.tinyMceRawConfigurations)){
			for (var key in configs) {
				if (configs.hasOwnProperty(key) && typeof configs[key] === 'object' &&
							document.getElementById('we_' + window.doc.docName + '_input[' + key + ']') &&
							configs[key].weEditorType === 'inlineTrue'){
					WE().layout.we_tinyMCE.functions.initEditor(window, configs[key]);
				}
			}
		}
	};

	this.createDIV = function (node) {
		var div = document.createElement("div");
		var attr = document.createAttribute("id");

		attr.value = node.getAttribute("id");
		div.innerHTML = node.innerHTML;
		div.setAttributeNode(attr);

		return div;

	};


	// Bug in IE -> loses the selected attribute in option tags
	this.fixIESelectBug = function (doc, id) {
		var i, j;
		if (!document.importNode) {
			var node = (doc === document ? document : document.getElementById(id));

			for (j = 0; j < doc.getElementsByTagName("select").length; j++) {

				for (i = 0; i < doc.getElementsByTagName("select")[j].options.length; i++) {
					if (doc.getElementsByTagName("select")[j].options[i].selected) {
						node.getElementsByTagName("select")[j].selectedIndex = i;
					}
				}
			}

		}

	};

};

var orderContainer = new weOrderContainer("orderContainer");
$(function () {
	orderContainer.init();
});
