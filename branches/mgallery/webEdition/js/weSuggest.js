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
		//document.getElementById(YAHOO.autocoml.yuiAcFields[i].id).blur();
		wsValid = true;
		if (YAHOO.autocoml.yuiAcFields[i].workspace.length > 0) {
			wsValid = false;
			var wsPathInput = document.getElementById(YAHOO.autocoml.yuiAcFields[i].id).value;
			for (j = 0; j < YAHOO.autocoml.yuiAcFields[i].workspace.length; j++) {
				if (wsPathInput.length >= YAHOO.autocoml.yuiAcFields[i].workspace[j].length) {
					if (wsPathInput.substr(0, YAHOO.autocoml.yuiAcFields[i].workspace[j].length) == YAHOO.autocoml.yuiAcFields[i].workspace[j]) {
						wsValid = true;
					}
				}
			}
		}
		var rootDirValid = (YAHOO.autocoml.yuiAcFields[i].rootDir !== '' && document.getElementById(YAHOO.autocoml.yuiAcFields[i].id).value.indexOf(YAHOO.autocoml.yuiAcFields[i].rootDir) !== 0) ? false : true;
		if (document.getElementById(YAHOO.autocoml.yuiAcFields[i].id).value === '/' && (YAHOO.autocoml.yuiAcFields[i].selector === 'dirSelector' || YAHOO.autocoml.yuiAcFields[i].selector === 'Dirselector' || YAHOO.autocoml.yuiAcFields[i].selector === 'selector') && wsValid && rootDirValid) {
			//FIXME: what about the rest?
			document.getElementById(YAHOO.autocoml.yuiAcFields[i].fields_id[0]).value = '0';
			YAHOO.autocoml.yuiAcFields[i].newval = '/';
			YAHOO.autocoml.yuiAcFields[i].run = false;
			YAHOO.autocoml.unmarkNotValid(i);
		} else if (document.getElementById(YAHOO.autocoml.yuiAcFields[i].id).value === '' && (YAHOO.autocoml.yuiAcFields[i].selector === 'docSelector' || YAHOO.autocoml.yuiAcFields[i].selector === 'Docselector' || YAHOO.autocoml.yuiAcFields[i].selector === 'dirSelector' || YAHOO.autocoml.yuiAcFields[i].selector === 'Dirselector' || YAHOO.autocoml.yuiAcFields[i].selector === 'selector') && YAHOO.autocoml.yuiAcFields[i].mayBeEmpty) {
			//FIXME: what about the rest?
			document.getElementById(YAHOO.autocoml.yuiAcFields[i].fields_id[0]).value = '';
			YAHOO.autocoml.yuiAcFields[i].run = false;
			YAHOO.autocoml.unmarkNotValid(i);
		} else {
			switch (true) {
				case (!rootDirValid):                              // ERROR: Not valid rootDir
					YAHOO.autocoml.markNotValid(i);
					break;
				case (!wsValid):                                   // ERROR: Not valid workspace
					YAHOO.autocoml.markNotValid(i);
					break;
				case (YAHOO.autocoml.ajaxResponseCT > YAHOO.autocoml.ajaxMaxResponseTime):          // ERROR: No respone - timeout
					YAHOO.autocoml.markNotValid(i);
					break;
				case (YAHOO.autocoml.yuiAcFields[i].run):                        // ERROR: Request is running
					YAHOO.autocoml.ajaxResponseCT += YAHOO.autocoml.ajaxResponseStep;
					setTimeout('YAHOO.autocoml.doOnTextfieldBlur(0,0,' + i + ')', YAHOO.autocoml.ajaxResponseStep);
					break;
				case (YAHOO.autocoml.yuiAcFields[i].found === 2):                 // ERROR: No result found
					YAHOO.autocoml.markNotValid(i);
					break;
				case (YAHOO.autocoml.yuiAcFields[i].found === 0):                 // ERROR: Nothing found
					YAHOO.autocoml.newInputVal[i] = document.getElementById(YAHOO.autocoml.yuiAcFields[i].id).value;
					if (YAHOO.autocoml.newInputVal[i] != YAHOO.autocoml.selInputVal[i] || YAHOO.autocoml.newInputVal[i] != YAHOO.autocoml.oldInputVal[i]) {
						YAHOO.autocoml.yuiAcFields[i].run = true;
						YAHOO.autocoml.doAjax({
							success: function (o) {
								YAHOO.autocoml.ajaxSuccess(o, i);
							},
							failure: function (o) {
								YAHOO.autocoml.ajaxFailure(o, i);
							}
						}, 'protocol=text&cmd=SelectorGetSelectedId&we_cmd[1]=' + encodeURIComponent(YAHOO.autocoml.newInputVal[i]) + '&we_cmd[2]=' + encodeURIComponent(YAHOO.autocoml.yuiAcFields[i].table) + '&we_cmd[3]=' + encodeURIComponent(YAHOO.autocoml.yuiAcFields[i].cTypes) + '&we_cmd[4]=' + encodeURIComponent(YAHOO.autocoml.yuiAcFields[i].checkValues) + '&we_cmd[5]=' + i);
						if (x === y && y === 0) {
							//call from timeout
						} else {
							setTimeout("YAHOO.autocoml.doOnTextfieldBlur(0, 0, " + i + ")", YAHOO.autocoml.ajaxResponseStep);
						}
					}
					break;
				case ((YAHOO.autocoml.yuiAcFields[i].selector == 'docSelector' || YAHOO.autocoml.yuiAcFields[i].selector == 'Docselector') && YAHOO.autocoml.yuiAcFields[i].cType == 'folder') :   // ERROR: Wrong type
					YAHOO.autocoml.markNotValid(i);
					break;
				default:
					YAHOO.autocoml.checkFields();
			}
		}

		if (window._EditorFrame !== undefined && YAHOO.autocoml.yuiAcFields[i].old != YAHOO.autocoml.yuiAcFields[i].newval && YAHOO.autocoml.yuiAcFields[i].newval !== null) {
			_EditorFrame.setEditorIsHot(true);
			//don't match again, since on save frame is not reloaded
			YAHOO.autocoml.yuiAcFields[i].old = YAHOO.autocoml.yuiAcFields[i].newval;
		}
		inputID = YAHOO.autocoml.yuiAcFields[i].id;
		resultID = YAHOO.autocoml.yuiAcFields[i].fields_id[0];
		if (YAHOO.autocoml.yuiAcFields[i].blur !== undefined) {
			YAHOO.autocoml.yuiAcFields[i].blur();
		}

		YAHOO.autocoml.yuiAcFields[i].changed = false;

	},
	doOnDataRequestEvent: function (x, y, i) {
		YAHOO.autocoml.yuiAcFields[i].found = 0;
		YAHOO.autocoml.yuiAcFields[i].run = true;
		YAHOO.autocoml.yuiAcFields[i].changed = true;
	},
	doOnDataErrorEvent: function (x, y, i) {
		YAHOO.autocoml.yuiAcFields[i].run = false;
		YAHOO.autocoml.yuiAcFields[i].valid = false;
	},
	doOnUnmatchedItemSelectEvent: function (x, y, i) {
		YAHOO.autocoml.yuiAcFields[i].run = false;
	},
	doOnDataReturnEvent: function (x, y, i) {
		YAHOO.autocoml.yuiAcFields[i].run = false;
	},
	doOnContainerCollapse: function (i) {
		//setTimeout('YAHOO.autocoml.doOnTextfieldBlur_$i(0,0," . $i . ")',100);
	},
	ajaxSuccess: function (o, id) {
		if (o.responseText !== undefined && o.responseText !== '') {
			eval(o.responseText);
			if (weResponse.type == 'error') {
				//for (i=0; i < YAHOO.autocoml.yuiAcFields[id].fields_id.length; i++) {
				document.getElementById(YAHOO.autocoml.yuiAcFields[id].fields_id[0]).value = YAHOO.autocoml.yuiAcFields[id].fields_val[0];
				//}
				YAHOO.autocoml.yuiAcFields[id].found = 2;
				YAHOO.autocoml.yuiAcFields[id].newval = '';
				YAHOO.autocoml.markNotValid(id);
				YAHOO.autocoml.yuiAcFields[id].newval = '';
			} else {
				if (weResponse.data.contentType == 'folder' && (YAHOO.autocoml.yuiAcFields[id].selector == 'docSelector' || YAHOO.autocoml.yuiAcFields[id].selector == 'Docselector')) {
					document.getElementById(YAHOO.autocoml.yuiAcFields[id].fields_id[0]).value = '';
					YAHOO.autocoml.markNotValid(id);
					YAHOO.autocoml.yuiAcFields[id].newval = '';
				} else {
					document.getElementById(YAHOO.autocoml.yuiAcFields[id].fields_id[0]).value = weResponse.data.value;
					YAHOO.autocoml.unmarkNotValid(id);
					YAHOO.autocoml.yuiAcFields[id].newval = document.getElementById(YAHOO.autocoml.yuiAcFields[id].id).value;
				}
				YAHOO.autocoml.yuiAcFields[id].found = 1;
			}
		}
		YAHOO.autocoml.yuiAcFields[id].run = false;
	},
	ajaxFailure: function (o, id) {
		for (i = 1; i < YAHOO.autocoml.yuiAcFields[id].fields_id.length; i++) {
			document.getElementById(YAHOO.autocoml.yuiAcFields[id].fields_id[i]).value = YAHOO.autocoml.yuiAcFields[id].fields_val;
		}
		YAHOO.autocoml.yuiAcFields[id].run = false;
		YAHOO.autocoml.yuiAcFields[id].valid = false;
		YAHOO.autocoml.markNotValid(id);
		YAHOO.autocoml.yuiAcFields[id].newval = '';
	},
	setupInstance: function (i, select, check, myInput, myContainer) {
		YAHOO.autocoml.oACDS[i] = new YAHOO.widget.DS_XHR(YAHOO.autocoml.ajaxURL, ['\n', '\t']);
		YAHOO.autocoml.oACDS[i].responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
		YAHOO.autocoml.oACDS[i].maxCacheEntries = 60;
		YAHOO.autocoml.oACDS[i].queryMatchSubset = false;
		YAHOO.autocoml.oACDS[i].scriptQueryParam = "we_cmd[1]";
		YAHOO.autocoml.oACDS[i].scriptQueryAppend = "protocol=text&cmd=SelectorSuggest&we_cmd[2]=" + YAHOO.autocoml.yuiAcFields[i].table + "&we_cmd[3]=" + YAHOO.autocoml.yuiAcFields[i].cTypes + "&we_cmd[4]=" + YAHOO.autocoml.selfType + "&we_cmd[5]=" + YAHOO.autocoml.selfID + "&we_cmd[6]=" + YAHOO.autocoml.yuiAcFields[i].rootDir;

		if (YAHOO.autocoml.oAutoComp[i] !== undefined) {
			YAHOO.autocoml.oAutoComp[i].destroy();
		}
		YAHOO.autocoml.oAutoComp[i] = new YAHOO.widget.AutoComplete(myInput, myContainer, YAHOO.autocoml.oACDS[i]);
		YAHOO.autocoml.oAutoComp[i].queryDelay = 0;
		YAHOO.autocoml.oAutoComp[i].maxResultsDisplayed = YAHOO.autocoml.yuiAcFields[i].maxResults;

		if (select) {
			YAHOO.autocoml.oAutoComp[i].dataRequestEvent.subscribe(YAHOO.autocoml.doOnDataRequestEvent, i);
			YAHOO.autocoml.oAutoComp[i].unmatchedItemSelectEvent.subscribe(YAHOO.autocoml.doOnUnmatchedItemSelectEvent, i);
			YAHOO.autocoml.oAutoComp[i].dataErrorEvent.subscribe(YAHOO.autocoml.doOnDataErrorEvent, i);
			YAHOO.autocoml.oAutoComp[i].dataReturnEvent.subscribe(YAHOO.autocoml.doOnDataReturnEvent, i);
			YAHOO.autocoml.oAutoComp[i].itemSelectEvent.subscribe(YAHOO.autocoml.doOnItemSelect, i);
		}
		if (check) {
			YAHOO.autocoml.oAutoComp[i].containerCollapseEvent.subscribe(YAHOO.autocoml.doOnContainerCollapse, i);
			YAHOO.autocoml.oAutoComp[i].textboxFocusEvent.subscribe(YAHOO.autocoml.doOnTextfieldFocus, i);
			YAHOO.autocoml.oAutoComp[i].textboxBlurEvent.subscribe(YAHOO.autocoml.doOnTextfieldBlur, i);
		}
		YAHOO.autocoml.oAutoComp[i].formatResult = function (oResultItem, sQuery) {
			var sKey = oResultItem[0];
			var nQuantity = oResultItem[1];
			var sKeyQuery = sKey.substring(0, sQuery.length);
			if (sQuery.length > 10) {
				var path = sKeyQuery.split(' / ');
				var pPart = ' / ' + path[path.length - 1];
				if (pPart.length > (YAHOO.autocoml.width / 15)) {
					pPart = pPart.substring(pPart.length - 10, pPart.length);
				}
				sKeyQuery = '&hellip;' + pPart;
			}
			var sKeyRemainder = sKey.substr(sQuery.length);
			if (YAHOO.autocoml.oAutoCompRes[i] === undefined) {
				YAHOO.autocoml.oAutoCompRes[i] = {};
			}
			YAHOO.autocoml.oAutoCompRes[i][sKeyQuery] = oResultItem[2];
			var aMarkup = ['<div id=\"ysearchresult\"><div class=\"ysearchquery\">',
				//nQuantity,
				'</div><span style=\"font-weight:bold\">',
				sKeyQuery,
				'</span>',
				sKeyRemainder,
				'</div>'];
			return (aMarkup.join(''));
		};
	},
	doOnItemSelect: function (param1, param2, i) {
		param = param2.toString();
		params = param.split(',');

		if ((YAHOO.autocoml.yuiAcFields[i].selector == 'docSelector' || YAHOO.autocoml.yuiAcFields[i].selector == 'Docselector') && params[4] == 'folder') {
			YAHOO.autocoml.yuiAcFields[i].valid = false;
			YAHOO.autocoml.yuiAcFields[i].cType = params[4];
		} else {
			YAHOO.autocoml.yuiAcFields[i].valid = true;
			YAHOO.autocoml.yuiAcFields[i].sel = params[3];
			YAHOO.autocoml.yuiAcFields[i].cType = params[4];
			YAHOO.autocoml.unmarkNotValid(i);
			if (YAHOO.autocoml.yuiAcFields[i].fields_id !== undefined) {
				var yuiAcOnSelectField;
				for (j = 0; j < YAHOO.autocoml.yuiAcFields[i].fields_id.length; j++) {
					if ((yuiAcOnSelectField = document.getElementById(YAHOO.autocoml.yuiAcFields[i].fields_id[j])) && (typeof (params[3]) != 'undefined')) {
						yuiAcOnSelectField.value = params[3];
					}
				}
			}
		}
		YAHOO.autocoml.yuiAcFields[i].found = 1;
		YAHOO.autocoml.yuiAcFields[i].run = false;
		YAHOO.autocoml.selInputVal[i] = document.getElementById(YAHOO.autocoml.yuiAcFields[i].id).value;
		YAHOO.autocoml.yuiAcFields[i].newval = document.getElementById(YAHOO.autocoml.yuiAcFields[i].id).value;
		inputID = YAHOO.autocoml.yuiAcFields[i].id;
		resultID = YAHOO.autocoml.yuiAcFields[i].fields_id[0];
		if (YAHOO.autocoml.yuiAcFields[i].itemSelect !== undefined) {
			YAHOO.autocoml.yuiAcFields[i].itemSelect(param1, param2, param, params);
		}
	},
	doOnTextfieldFocus: function (x, y, i) {
		YAHOO.autocoml.ajaxResponseCT = 0;
		YAHOO.autocoml.oldInputVal[i] = document.getElementById(YAHOO.autocoml.yuiAcFields[i].id).value;
		if (YAHOO.autocoml.yuiAcFields[i].fields_id !== undefined) {
			for (j = 0; j <= YAHOO.autocoml.yuiAcFields[i].fields_id.length; j++) {
				name = YAHOO.autocoml.yuiAcFields[i].fields_id[j];
				YAHOO.autocoml.old[name] = document.getElementById(name).value;
			}
		}
		//	YAHOO.autocoml.unmarkNotValid(i);
		if (parent && parent.weAutoCompetionFields){
			parent.weAutoCompetionFields[YAHOO.autocoml.yuiAcFields[i].id] = false;
		}
		YAHOO.autocoml.yuiAcFields[i].sel = '';
	},
	doAjax: function (callback, postdata) {
		var request = YAHOO.util.Connect.asyncRequest('POST', YAHOO.autocoml.ajaxURL, callback, postdata);
	},
	validateForm: function () {
		// Validate form inputs here
		return false;
	},
	checkFields: function () {
		for (i = 0; i < YAHOO.autocoml.yuiAcFields.length; i++) {
			set = YAHOO.autocoml.yuiAcFields[i];
			if (!set.valid) {
				document.getElementById(set.fields_id[0] = '');
				YAHOO.autocoml.markNotValid(i);
			}
		}
		return true;
	},
	checkRunnigProcess: function () {
		for (i = 0; i < YAHOO.autocoml.yuiAcFields.length; i++) {
			set = YAHOO.autocoml.yuiAcFields[i];
			if (set.run) {
				return true;
			}
		}
		return false;
	},
	markNotValid: function (setNr) {
		set = YAHOO.autocoml.yuiAcFields[setNr];
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
		set = YAHOO.autocoml.yuiAcFields[setNr];
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
		if (YAHOO.autocoml.checkRunnigProcess())
			return {'running': true};
		for (i = 0; i < YAHOO.autocoml.yuiAcFields.length; i++) {
			set = YAHOO.autocoml.yuiAcFields[i];
			if (!set.valid) {
				return {'running': false, 'valid': false};
			}
		}
		return {'running': false, 'valid': true};
	},
	selectorSetValid: function (setFieldId) {
		for (i = 0; i < YAHOO.autocoml.yuiAcFields.length; i++) {
			set = YAHOO.autocoml.yuiAcFields[i];
			if (set.id == setFieldId) {
				YAHOO.autocoml.unmarkNotValid(i);
			}
		}
	},
	checkOnContainerCollapse: function (setNr) {
		set = YAHOO.autocoml.yuiAcFields[setNr];
		if (set.sel === '') {
		}
	},
	modifySetById: function (fId, param, value) {
		set = YAHOO.autocoml.yuiAcFieldsById[fId];
		if (typeof param === 'object') {
			for (var name in param) {
				YAHOO.autocoml.yuiAcFields[set][name] = YAHOO.autocoml.yuiAcFields[set][name] !== undefined ? param[name] : YAHOO.autocoml.yuiAcFields[set][name];
			}
		} else {
			YAHOO.autocoml.yuiAcFields[set][param] = YAHOO.autocoml.yuiAcFields[set][param] !== undefined ? value : YAHOO.autocoml.yuiAcFields[set][param];
		}
		YAHOO.autocoml.init(undefined, set);
	},
	getParamById: function (fId, param) {
		set = YAHOO.autocoml.yuiAcFieldsById[fId];
		return YAHOO.autocoml.yuiAcFields[set][param];
	},
	getYuiAcFields: function () {
		return YAHOO.autocoml.yuiAcFields;
	},
	isValidById: function (fId) {
		if (fId) {
			if (YAHOO.autocoml.counter < 10 && YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById[fId]].run) {
				YAHOO.autocoml.counter++;
				setTimeout('YAHOO.autocoml.isValidById(\"' + fId + '\")', 100);
			} else {
				YAHOO.autocoml.counter = 0;
				return YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById[fId]].valid;
			}
		} else {
			return false;
		}
	},
	counter: 0,
	isValid: function () {
		var isValid = true;
		for (var fId in YAHOO.autocoml.yuiAcFieldsById) {
			if (document.getElementById(fId).style.display != 'none' && !YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById[fId]].valid) {
				isValid = false;
			}
		}
		return isValid;
	},
	isRunnigProcess: function () {
		var isRunning = false;
		for (var fId in YAHOO.autocoml.yuiAcFieldsById) {
			if (document.getElementById(fId).style.display != 'none' && YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById[fId]].run) {
				isRunning = true;
			}
		}
		return isRunning;
	},
	setValidById: function (fId) {
		YAHOO.autocoml.unmarkNotValid(YAHOO.autocoml.yuiAcFieldsById[fId]);
		YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById[fId]].valid = true;
	},
	setNotValidById: function (fId) {
		YAHOO.autocoml.markNotValid(YAHOO.autocoml.yuiAcFieldsById[fId]);
		YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById[fId]].valid = false;
	},
	restoreById: function (fId) {
		set = YAHOO.autocoml.yuiAcFieldsById[fId];
		YAHOO.autocoml.markValid(YAHOO.autocoml.yuiAcFieldsById[fId]);
		document.getElementById(fId).value = YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById[fId]].old;
		document.getElementById(YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById[fId]].fields_id[0]).value = YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById[fId]].fields_val[0];
	},
	setOldVal: function (set) {
	},
	init: function (param, inst) {
		inst = inst === undefined ? -1 : inst;
		for (i = 0; i < YAHOO.autocoml.yuiAcFields.length; ++i) {
			if (inst == -1 || inst == i) {
				var select = (YAHOO.autocoml.yuiAcFields[i].fields_id !== undefined);
				var check = (YAHOO.autocoml.yuiAcFields[i].checkField !== undefined);
				var myInput = document.getElementById(YAHOO.autocoml.yuiAcFields[i].id);
				var myContainer = document.getElementById(YAHOO.autocoml.yuiAcFields[i].container);
				YAHOO.autocoml.setupInstance(i, select, check, myInput, myContainer);

				if (parent && parent.weAutoCompetionFields && !parent.weAutoCompetionFields[i]) {
					parent.weAutoCompetionFields[i] = {
						'id': YAHOO.autocoml.yuiAcFields[i].id,
						'valid': true,
						'cType': YAHOO.autocoml.yuiAcFields[i].cType
					};
				}
			}
		}
		if (parent && parent.weAutoCompetionFields && parent.weAutoCompetionFields.length > 0) {
			for (i = 0;
							i < parent.weAutoCompetionFields.length;
							i++) {
				if (parent.weAutoCompetionFields[i] && parent.weAutoCompetionFields[i].id && !parent.weAutoCompetionFields[i].valid) {
					YAHOO.autocoml.markNotValid(i);
				}
			}
		}
	}

};