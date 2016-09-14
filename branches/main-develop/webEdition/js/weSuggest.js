/* global YAHOO, WE */

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

function weInputAppendClass(inp, cls) {
	inp.classList.add(cls);
}

function weInputRemoveClass(inp, cls) {
	inp.classList.remove(cls);
}

function weInputInArray(arr, val) {
	var _l = arr.length;
	for (var i = 0; i < _l; i++) {
		if (arr[i] === val) {
			return true;
		}
	}
	return false;
}

function dropzoneAddPreview(name, id, table, ct, path) {
	var specialmode = (name === 'Image' && id === -1);
	if (!specialmode && !(name && id && table && ct && path)) {
		return;
	}

	if (specialmode) {
		id = document.we_form.elements.yuiAcResultImage.value;
		path = document.we_form.elements.yuiAcInputImage.value;
		table = WE().consts.tables.FILE_TABLE;
		ct = WE().consts.contentTypes.IMAGE;
	}

	if (table === WE().consts.tables.FILE_TABLE && ct === WE().consts.contentTypes.IMAGE) {
		var src, img, preview, imgs;

		preview = top.document.getElementById('preview_' + name);
		imgs = preview.getElementsByTagName('IMG');
		if (imgs && imgs.length) {
			preview.removeChild(imgs[0]);
		}

		src = WE().consts.dirs.WEBEDITION_DIR + 'thumbnail.php?id=' + id + '&size[width]=100&size[height]=100&path=' + encodeURIComponent(path) + '&extension=.' + path.split('.').pop();
		img = document.createElement("IMG");
		img.src = src;
		img.style = "vertical-align:middle;";
		preview.appendChild(img);
	}

}

YAHOO.autocoml = {
	oACDS: [],
	selInputVal: [],
	oldInputVal: [],
	newInputVal: [],
	oAutoComp: [],
	oAutoCompRes: [],
	old: {},
	ajaxResponseCT: 0,
	ajaxResponseStep: 100,
	ajaxMaxResponseTime: 1500,
	doOnTextfieldBlur: function (x, y, i) {
		ret = true;
		//document.getElementById(this.yuiAcFields[i].id).blur();
		wsValid = true;
		if (this.yuiAcFields[i].workspace.length > 0) {
			wsValid = false;
			var wsPathInput = document.getElementById(this.yuiAcFields[i].id).value;
			for (var j = 0; j < this.yuiAcFields[i].workspace.length; j++) {
				if (wsPathInput.length >= this.yuiAcFields[i].workspace[j].length) {
					if (wsPathInput.substr(0, this.yuiAcFields[i].workspace[j].length) == this.yuiAcFields[i].workspace[j]) {
						wsValid = true;
					}
				}
			}
		}
		var rootDirValid = (this.yuiAcFields[i].rootDir !== '' && document.getElementById(this.yuiAcFields[i].id).value.indexOf(this.yuiAcFields[i].rootDir) !== 0) ? false : true;
		if (document.getElementById(this.yuiAcFields[i].id).value === '/' && (this.yuiAcFields[i].selector === 'dirSelector' || this.yuiAcFields[i].selector === 'Dirselector' || this.yuiAcFields[i].selector === 'selector') && wsValid && rootDirValid) {
			//FIXME: what about the rest?
			document.getElementById(this.yuiAcFields[i].fields_id[0]).value = '0';
			this.yuiAcFields[i].newval = '/';
			this.yuiAcFields[i].run = false;
			this.unmarkNotValid(i);
		} else if (document.getElementById(this.yuiAcFields[i].id).value === '' && (this.yuiAcFields[i].selector === 'docSelector' || this.yuiAcFields[i].selector === 'Docselector' || this.yuiAcFields[i].selector === 'dirSelector' || this.yuiAcFields[i].selector === 'Dirselector' || this.yuiAcFields[i].selector === 'selector') && this.yuiAcFields[i].mayBeEmpty) {
			//FIXME: what about the rest?
			document.getElementById(this.yuiAcFields[i].fields_id[0]).value = '';
			this.yuiAcFields[i].run = false;
			this.unmarkNotValid(i);
		} else {
			switch (true) {
				case (!rootDirValid):                              // ERROR: Not valid rootDir
					this.markNotValid(i);
					break;
				case (!wsValid):                                   // ERROR: Not valid workspace
					this.markNotValid(i);
					break;
				case (this.ajaxResponseCT > this.ajaxMaxResponseTime):          // ERROR: No respone - timeout
					this.markNotValid(i);
					break;
				case (this.yuiAcFields[i].run):                        // ERROR: Request is running
					this.ajaxResponseCT += this.ajaxResponseStep;
					setTimeout(this.doOnTextfieldBlur, this.ajaxResponseStep, 0, 0, i);
					break;
				case (this.yuiAcFields[i].found === 2):                 // ERROR: No result found
					this.markNotValid(i);
					break;
				case (this.yuiAcFields[i].found === 0):                 // ERROR: Nothing found
					this.newInputVal[i] = document.getElementById(this.yuiAcFields[i].id).value;
					if (this.newInputVal[i] != this.selInputVal[i] || this.newInputVal[i] != this.oldInputVal[i]) {
						this.yuiAcFields[i].run = true;
						this.doAjax({
							success: function (o) {
								this.ajaxSuccess(o, i);
							},
							failure: function (o) {
								this.ajaxFailure(o, i);
							}
						}, 'protocol=text&cmd=SelectorGetSelectedId&we_cmd[1]=' + encodeURIComponent(this.newInputVal[i]) + '&we_cmd[2]=' + encodeURIComponent(this.yuiAcFields[i].table) + '&we_cmd[3]=' + encodeURIComponent(this.yuiAcFields[i].cTypes) + '&we_cmd[4]=' + encodeURIComponent(this.yuiAcFields[i].checkValues) + '&we_cmd[5]=' + i);
						if (x === y && y === 0) {
							//call from timeout
						} else {
							setTimeout(this.doOnTextfieldBlur, this.ajaxResponseStep, 0, 0, i);
						}
					}
					break;
				case ((this.yuiAcFields[i].selector == 'docSelector' || this.yuiAcFields[i].selector == 'Docselector') && this.yuiAcFields[i].cType == 'folder') :   // ERROR: Wrong type
					this.markNotValid(i);
					break;
				default:
					this.checkFields();
			}
		}

		if (window._EditorFrame !== undefined && this.yuiAcFields[i].old != this.yuiAcFields[i].newval && this.yuiAcFields[i].newval !== null) {
			_EditorFrame.setEditorIsHot(true);
			//don't match again, since on save frame is not reloaded
			this.yuiAcFields[i].old = this.yuiAcFields[i].newval;
		}
		inputID = this.yuiAcFields[i].id;
		resultID = this.yuiAcFields[i].fields_id[0];
		if (this.yuiAcFields[i].blur !== undefined && this.yuiAcFields[i].blur) {
			//FIXME: eval
			eval(this.yuiAcFields[i].blur);
		}

		this.yuiAcFields[i].changed = false;

	},
	doOnDataRequestEvent: function (x, y, i) {
		this.yuiAcFields[i].found = 0;
		this.yuiAcFields[i].run = true;
		this.yuiAcFields[i].changed = true;
	},
	doOnDataErrorEvent: function (x, y, i) {
		this.yuiAcFields[i].run = false;
		this.yuiAcFields[i].valid = false;
	},
	doOnUnmatchedItemSelectEvent: function (x, y, i) {
		this.yuiAcFields[i].run = false;
	},
	doOnDataReturnEvent: function (x, y, i) {
		this.yuiAcFields[i].run = false;
	},
	doOnContainerCollapse: function (i) {
		//setTimeout('this.doOnTextfieldBlur_$i(0,0," . $i . ")',100);
	},
	ajaxSuccess: function (o, id) {
		if (o.responseText !== undefined && o.responseText) {
			var weResponse = JSON.parse(o.responseText);
			if (weResponse.Success) {
				if (weResponse.DataArray.data.contentType === 'folder' && (this.yuiAcFields[id].selector === 'docSelector' || this.yuiAcFields[id].selector === 'Docselector')) {
					document.getElementById(this.yuiAcFields[id].fields_id[0]).value = '';
					this.markNotValid(id);
					this.yuiAcFields[id].newval = '';
				} else {
					document.getElementById(this.yuiAcFields[id].fields_id[0]).value = weResponse.DataArray.data.value;
					this.unmarkNotValid(id);
					this.yuiAcFields[id].newval = document.getElementById(this.yuiAcFields[id].id).value;
				}
				this.yuiAcFields[id].found = 1;

			} else {
				//for (i=0; i < this.yuiAcFields[id].fields_id.length; i++) {
				document.getElementById(this.yuiAcFields[id].fields_id[0]).value = this.yuiAcFields[id].fields_val[0];
				//}
				this.yuiAcFields[id].found = 2;
				this.yuiAcFields[id].newval = '';
				this.markNotValid(id);
				this.yuiAcFields[id].newval = '';
			}
		}
		this.yuiAcFields[id].run = false;
	},
	ajaxFailure: function (o, id) {
		for (var i = 1; i < this.yuiAcFields[id].fields_id.length; i++) {
			document.getElementById(this.yuiAcFields[id].fields_id[i]).value = this.yuiAcFields[id].fields_val;
		}
		this.yuiAcFields[id].run = false;
		this.yuiAcFields[id].valid = false;
		this.markNotValid(id);
		this.yuiAcFields[id].newval = '';
	},
	setupInstance: function (i, select, check, myInput, myContainer) {
		this.oACDS[i] = new YAHOO.widget.DS_XHR(WE().consts.dirs.WEBEDITION_DIR + "rpc.php", ['\n', '\t']);
		this.oACDS[i].responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
		this.oACDS[i].maxCacheEntries = 60;
		this.oACDS[i].queryMatchSubset = false;
		this.oACDS[i].scriptQueryParam = "we_cmd[1]";
		this.oACDS[i].scriptQueryAppend = "protocol=text&cmd=SelectorSuggest&we_cmd[2]=" + this.yuiAcFields[i].table + "&we_cmd[3]=" + this.yuiAcFields[i].cTypes + "&we_cmd[4]=" + this.selfType + "&we_cmd[5]=" + this.selfID + "&we_cmd[6]=" + this.yuiAcFields[i].rootDir;

		if (this.oAutoComp[i] !== undefined) {
			this.oAutoComp[i].destroy();
		}
		this.oAutoComp[i] = new YAHOO.widget.AutoComplete(myInput, myContainer, this.oACDS[i]);
		this.oAutoComp[i].queryDelay = 0;
		this.oAutoComp[i].maxResultsDisplayed = this.yuiAcFields[i].maxResults;

		if (select) {
			this.oAutoComp[i].dataRequestEvent.subscribe(this.doOnDataRequestEvent, i);
			this.oAutoComp[i].unmatchedItemSelectEvent.subscribe(this.doOnUnmatchedItemSelectEvent, i);
			this.oAutoComp[i].dataErrorEvent.subscribe(this.doOnDataErrorEvent, i);
			this.oAutoComp[i].dataReturnEvent.subscribe(this.doOnDataReturnEvent, i);
			this.oAutoComp[i].itemSelectEvent.subscribe(this.doOnItemSelect, i);
		}
		if (check) {
			this.oAutoComp[i].containerCollapseEvent.subscribe(this.doOnContainerCollapse, i);
			this.oAutoComp[i].textboxFocusEvent.subscribe(this.doOnTextfieldFocus, i);
			this.oAutoComp[i].textboxBlurEvent.subscribe(this.doOnTextfieldBlur, i);
		}
		this.oAutoComp[i].formatResult = function (oResultItem, sQuery) {
			var sKey = oResultItem[0];
			var nQuantity = oResultItem[1];
			var sKeyQuery = sKey.substring(0, sQuery.length);
			if (sQuery.length > 10) {
				var path = sKeyQuery.split(' / ');
				var pPart = ' / ' + path[path.length - 1];
				if (pPart.length > (this.width / 15)) {
					pPart = pPart.substring(pPart.length - 10, pPart.length);
				}
				sKeyQuery = '&hellip;' + pPart;
			}
			var sKeyRemainder = sKey.substr(sQuery.length);
			if (this.oAutoCompRes[i] === undefined) {
				this.oAutoCompRes[i] = {};
			}
			this.oAutoCompRes[i][sKeyQuery] = oResultItem[2];
			var aMarkup = ['<div id=\"ysearchresult\"><div class=\"ysearchquery\">',
				//nQuantity,
				'</div><strong>',
				sKeyQuery,
				'</strong>',
				sKeyRemainder,
				'</div>'];
			return (aMarkup.join(''));
		};
	},
	doOnItemSelect: function (param1, param2, i) {
		param = param2.toString();
		params = param.split(',');

		if ((this.yuiAcFields[i].selector == 'docSelector' || this.yuiAcFields[i].selector == 'Docselector') && params[4] == 'folder') {
			this.yuiAcFields[i].valid = false;
			this.yuiAcFields[i].cType = params[4];
		} else {
			this.yuiAcFields[i].valid = true;
			this.yuiAcFields[i].sel = params[3];
			this.yuiAcFields[i].cType = params[4];
			this.unmarkNotValid(i);
			if (this.yuiAcFields[i].fields_id !== undefined && this.yuiAcFields[i].fields_id) {
				var yuiAcOnSelectField;
				for (var j = 0; j < this.yuiAcFields[i].fields_id.length; j++) {
					if ((yuiAcOnSelectField = document.getElementById(this.yuiAcFields[i].fields_id[j])) && (params[3] !== undefined)) {
						yuiAcOnSelectField.value = params[3];
					}
				}
			}
		}
		this.yuiAcFields[i].found = 1;
		this.yuiAcFields[i].run = false;
		this.selInputVal[i] = document.getElementById(this.yuiAcFields[i].id).value;
		this.yuiAcFields[i].newval = document.getElementById(this.yuiAcFields[i].id).value;
		inputID = this.yuiAcFields[i].id;
		resultID = this.yuiAcFields[i].fields_id[0];
		if (this.yuiAcFields[i].itemSelect !== undefined && this.yuiAcFields[i].itemSelect) {
			//FIXME: eval
			eval(this.yuiAcFields[i].itemSelect);
		}
	},
	doOnTextfieldFocus: function (x, y, i) {
		this.ajaxResponseCT = 0;
		this.oldInputVal[i] = document.getElementById(this.yuiAcFields[i].id).value;
		if (this.yuiAcFields[i].fields_id !== undefined) {
			var name;
			for (var j = 0; j < this.yuiAcFields[i].fields_id.length; j++) {
				name = this.yuiAcFields[i].fields_id[j];
				this.old[name] = document.getElementById(name).value;
			}
		}
		//	this.unmarkNotValid(i);
		if (parent && parent.weAutoCompetionFields) {
			parent.weAutoCompetionFields[this.yuiAcFields[i].id] = false;
		}
		this.yuiAcFields[i].sel = '';
	},
	doAjax: function (callback, postdata) {
		var request = YAHOO.util.Connect.asyncRequest('POST', WE().consts.dirs.WEBEDITION_DIR + "rpc.php", callback, postdata);
	},
	validateForm: function () {
		// Validate form inputs here
		return false;
	},
	checkFields: function () {
		for (var i = 0; i < this.yuiAcFields.length; i++) {
			set = this.yuiAcFields[i];
			if (!set.valid) {
				document.getElementById(set.fields_id[0] = '');
				this.markNotValid(i);
			}
		}
		return true;
	},
	checkRunnigProcess: function () {
		for (var i = 0; i < this.yuiAcFields.length; i++) {
			set = this.yuiAcFields[i];
			if (set.run) {
				return true;
			}
		}
		return false;
	},
	markNotValid: function (setNr) {
		set = this.yuiAcFields[setNr];
		set.valid = false;
		set.run = false;
		var _elem = document.getElementById(set.id);
		if (_elem !== null) {
			weInputAppendClass(_elem, 'weMarkInputError');
		}
		if (parent && parent.weAutoCompetionFields)
			parent.weAutoCompetionFields[setNr].valid = false;
	},
	unmarkNotValid: function (setNr) {
		set = this.yuiAcFields[setNr];
		set.valid = true;
		set.run = false;
		set.found = 1;
		var _elem = document.getElementById(set.id);
		if (_elem !== null) {
			weInputRemoveClass(_elem, 'weMarkInputError');
		}
		if (parent && parent.weAutoCompetionFields)
			parent.weAutoCompetionFields[setNr].valid = true;
	},
	checkACFields: function () {
		if (this.checkRunnigProcess())
			return {'running': true};
		for (var i = 0; i < this.yuiAcFields.length; i++) {
			set = this.yuiAcFields[i];
			if (!set.valid) {
				return {'running': false, 'valid': false};
			}
		}
		return {'running': false, 'valid': true};
	},
	selectorSetValid: function (setFieldId) {
		if (this.yuiAcFields === undefined) {
			return;
		}
		for (var i = 0; i < this.yuiAcFields.length; i++) {
			set = this.yuiAcFields[i];
			if (set.id == setFieldId) {
				this.unmarkNotValid(i);
			}
		}
	},
	checkOnContainerCollapse: function (setNr) {
		set = this.yuiAcFields[setNr];
		if (set.sel === '') {
		}
	},
	modifySetById: function (fId, param, value) {
		set = this.yuiAcFieldsById[fId];
		if (typeof param === 'object') {
			for (var name in param) {
				this.yuiAcFields[set][name] = this.yuiAcFields[set][name] !== undefined ? param[name] : this.yuiAcFields[set][name];
			}
		} else {
			this.yuiAcFields[set][param] = this.yuiAcFields[set][param] !== undefined ? value : this.yuiAcFields[set][param];
		}
		this.init(undefined, set);
	},
	getParamById: function (fId, param) {
		set = this.yuiAcFieldsById[fId];
		return this.yuiAcFields[set][param];
	},
	getYuiAcFields: function () {
		return this.yuiAcFields;
	},
	isValidById: function (fId) {
		if (fId) {
			if (this.counter < 10 && this.yuiAcFields[this.yuiAcFieldsById[fId]].run) {
				this.counter++;
				setTimeout(this.isValidById, 100, fId);
			} else {
				this.counter = 0;
				return this.yuiAcFields[this.yuiAcFieldsById[fId]].valid;
			}
		} else {
			return false;
		}
	},
	counter: 0,
	isValid: function () {
		var isValid = true;
		for (var fId in this.yuiAcFieldsById) {
			if (document.getElementById(fId).style.display != 'none' && !this.yuiAcFields[this.yuiAcFieldsById[fId]].valid) {
				isValid = false;
			}
		}
		return isValid;
	},
	isRunnigProcess: function () {
		var isRunning = false;
		for (var fId in this.yuiAcFieldsById) {
			if (document.getElementById(fId).style.display != 'none' && this.yuiAcFields[this.yuiAcFieldsById[fId]].run) {
				isRunning = true;
			}
		}
		return isRunning;
	},
	setValidById: function (fId) {
		this.unmarkNotValid(this.yuiAcFieldsById[fId]);
		this.yuiAcFields[this.yuiAcFieldsById[fId]].valid = true;
	},
	setNotValidById: function (fId) {
		this.markNotValid(this.yuiAcFieldsById[fId]);
		this.yuiAcFields[this.yuiAcFieldsById[fId]].valid = false;
	},
	restoreById: function (fId) {
		set = this.yuiAcFieldsById[fId];
		this.markValid(this.yuiAcFieldsById[fId]);
		document.getElementById(fId).value = this.yuiAcFields[this.yuiAcFieldsById[fId]].old;
		document.getElementById(this.yuiAcFields[this.yuiAcFieldsById[fId]].fields_id[0]).value = this.yuiAcFields[this.yuiAcFieldsById[fId]].fields_val[0];
	},
	setOldVal: function (set) {
	},
	init: function (param, inst) {
		//FIXME !!set old,fields_val(if fields_id) value in yuiAcFieldsById & yuiAcFields &
		inst = inst === undefined ? -1 : inst;
		for (var i = 0; i < this.yuiAcFields.length; ++i) {
			//set old
			this.yuiAcFields[i].old = document.getElementById(this.yuiAcFields[i].id).value;
			//set fields_val
			for (var j = 0; j < this.yuiAcFields[i].fields_id; j++) {
				this.yuiAcFields[i].fields_val.push(document.getElementById(this.yuiAcFields[i].fields_id[j]).value);
			}
			if (inst == -1 || inst == i) {
				var select = (this.yuiAcFields[i].fields_id !== undefined);
				var check = (this.yuiAcFields[i].checkField !== undefined);
				var myInput = document.getElementById(this.yuiAcFields[i].id);
				var myContainer = document.getElementById(this.yuiAcFields[i].container);
				this.setupInstance(i, select, check, myInput, myContainer);

				if (parent && parent.weAutoCompetionFields && !parent.weAutoCompetionFields[i]) {
					parent.weAutoCompetionFields[i] = {
						id: this.yuiAcFields[i].id,
						valid: true,
						cType: this.yuiAcFields[i].cType
					};
				}
			}
		}
		if (parent && parent.weAutoCompetionFields && parent.weAutoCompetionFields.length > 0) {
			for (i = 0; i < parent.weAutoCompetionFields.length; i++) {
				if (parent.weAutoCompetionFields[i] && parent.weAutoCompetionFields[i].id && !parent.weAutoCompetionFields[i].valid) {
					this.markNotValid(i);
				}
			}
		}
	},
	initFromLoad: function () {
		var yahoo = WE().util.getDynamicVar(document, 'loadVarWeSuggest', 'data-yahoo');
		try {
			this.width = yahoo.width;
			this.selfType = yahoo.selfType;
			this.selfID = yahoo.selfID;
			this.yuiAcFieldsById = yahoo.yuiAcFieldsById;
			this.yuiAcFields = yahoo.yuiAcFields;
			this.init();
		} catch (e) {
			//catch bug in IE
		}
	}
};

YAHOO.util.Event.addListener(window, "load", YAHOO.autocoml.initFromLoad);
