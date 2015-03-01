/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 9362 $
 * $Author: mokraemer $
 * $Date: 2015-02-20 16:26:39 +0100 (Fr, 20. Feb 2015) $
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
	if (inp.className) {
		var _class = inp.className;
		var _arr = _class.split(/ /);
		if (!weInputInArray(_arr, cls)) {
			_arr.push(cls);
		}
		var _newCls = _arr.join(' ');
		inp.className = _newCls;
	} else {
		inp.setAttribute('class', cls);
	}
}

function weInputRemoveClass(inp, cls) {
	if (inp.className) {
		var _class = inp.className;
		var _arr = _class.split(/ /);
		if (weInputInArray(_arr, cls)) {
			var _newArr = new Array();
			var _l = _arr.length;
			for (var i = 0; i < _l; i++) {
				if (_arr[i] !== cls) {
					_newArr.push(_arr[i]);
				}
			}

			if (_newArr.length > 0) {
				var _newCls = _newArr.join(' ');
				inp.className = _newCls;
			} else {
				inp.className = null;
			}
		}
	}
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


var oACDS = [];
var oAutoComp = [];
var oAutoCompRes = [];
var old = {};
var ajaxMaxResponseTime = 1500;
var ajaxResponseStep = 100;
var ajaxResponseCT = 0;
var countMark = 0;
var selInputVal = [];
var oldInputVal = [];
var newInputVal = [];

var protoSuggestObj = {
	doOnTextfieldBlur: function (i) {
		ret = true;
		//document.getElementById(yuiAcFields[i].id).blur();
		wsValid = true;
		if (yuiAcFields[i].workspace.length > 0) {
			wsValid = false;
			var wsPathInput = document.getElementById(yuiAcFields[i].id).value;
			for (i = 0; i < yuiAcFields[i].workspace.length; i++) {
				if (wsPathInput.length >= yuiAcFields[i].workspace[i].length) {
					if (wsPathInput.substr(0, yuiAcFields[i].workspace[i].length) == yuiAcFields[i].workspace[i]) {
						wsValid = true;
					}
				}
			}
		}
		var rootDirValid = (yuiAcFields[i].rootDir !== '' && document.getElementById(yuiAcFields[i].id).value.indexOf(yuiAcFields[i].rootDir) !== 0) ? false : true;
		if (document.getElementById(yuiAcFields[i].id).value == '/' && (yuiAcFields[i].selector == 'dirSelector' || yuiAcFields[i].selector == 'Dirselector' || yuiAcFields[i].selector == 'selector') && wsValid && rootDirValid) {
			//FIXME: what about the rest?
			document.getElementById(yuiAcFields[i].fields_id[0]).value = '0';
			yuiAcFields[i].newval = '/';
			yuiAcFields[i].run = false;
			YAHOO.autocoml.unmarkNotValid(i);
		} else if (document.getElementById(yuiAcFields[i].id).value == '' && (yuiAcFields[i].selector == 'docSelector' || yuiAcFields[i].selector == 'Docselector' || yuiAcFields[i].selector == 'dirSelector' || yuiAcFields[i].selector == 'Dirselector' || yuiAcFields[i].selector == 'selector') && yuiAcFields[i].mayBeEmpty) {
			//FIXME: what about the rest?
			document.getElementById(yuiAcFields[i].fields_id[0]).value = '';
			yuiAcFields[i].run = false;
			YAHOO.autocoml.unmarkNotValid(i);
		} else {
			switch (true) {
				case (!rootDirValid):                              // ERROR: Not valid rootDir
					YAHOO.autocoml.markNotValid(i);
					break;
				case (!wsValid):                                   // ERROR: Not valid workspace
					YAHOO.autocoml.markNotValid(i);
					break;
				case (ajaxResponseCT > ajaxMaxResponseTime):          // ERROR: No respone - timeout
					YAHOO.autocoml.markNotValid(i);
					break;
				case (yuiAcFields[i].run):                        // ERROR: Request is running
					ajaxResponseCT += ajaxResponseStep;
					setTimeout('YAHOO.autocoml.doOnTextfieldBlur_' + i + '(0,0,' + i + ')', ajaxResponseStep);
					break;
				case (yuiAcFields[i].found == 2):                 // ERROR: No result found
					YAHOO.autocoml.markNotValid(i);
					break;
				case (yuiAcFields[i].found == 0):                 // ERROR: Nothing found
					ret = false;
					break;
				case ((yuiAcFields[i].selector == 'docSelector' || yuiAcFields[i].selector == 'Docselector') && yuiAcFields[i].cType == 'folder') :   // ERROR: Wrong type
					YAHOO.autocoml.markNotValid(i);
					break;
				default:
					YAHOO.autocoml.checkFields();
			}
		}

		if (window._EditorFrame !== undefined && yuiAcFields[i].old != yuiAcFields[i].newval && yuiAcFields[i].newval != null) {
			_EditorFrame.setEditorIsHot(true);
			//don't match again, since on save frame is not reloaded
			yuiAcFields[i].old = yuiAcFields[i].newval;
		}
		inputID = yuiAcFields[i].id;
		resultID = yuiAcFields[i].fields_id[0];


		return ret;
	},
	doOnDataRequestEvent: function (x, y, i) {
		yuiAcFields[i].found = 0;
		yuiAcFields[i].run = true;
		yuiAcFields[i].changed = true;
	},
	doOnDataErrorEvent: function (x, y, i) {
		yuiAcFields[i].run = false;
		yuiAcFields[i].valid = false;
	},
	doOnUnmatchedItemSelectEvent: function (x, y, i) {
		yuiAcFields[i].run = false;
	},
	doOnDataReturnEvent: function (x, y, i) {
		yuiAcFields[i].run = false;
	},
	doOnContainerCollapse: function (i) {
		//setTimeout('YAHOO.autocoml.doOnTextfieldBlur_$i(0,0," . $i . ")',100);
	},
	ajaxSuccess: function (o, id) {
		if (o.responseText != undefined && o.responseText != '') {
			eval(o.responseText);
			if (weResponse.type == 'error') {
				//for (i=0; i < yuiAcFields[id].fields_id.length; i++) {
				document.getElementById(yuiAcFields[id].fields_id[0]).value = yuiAcFields[id].fields_val[0];
				//}
				yuiAcFields[id].found = 2;
				yuiAcFields[id].newval = '';
				YAHOO.autocoml.markNotValid(id);
				yuiAcFields[id].newval = '';
			} else {
				if (weResponse.data.contentType == 'folder' && (yuiAcFields[id].selector == 'docSelector' || yuiAcFields[id].selector == 'Docselector')) {
					document.getElementById(yuiAcFields[id].fields_id[0]).value = '';
					YAHOO.autocoml.markNotValid(id);
					yuiAcFields[id].newval = '';
				} else {
					document.getElementById(yuiAcFields[id].fields_id[0]).value = weResponse.data.value;
					YAHOO.autocoml.unmarkNotValid(id);
					yuiAcFields[id].newval = document.getElementById(yuiAcFields[id].id).value;
				}
				yuiAcFields[id].found = 1;
			}
		}
		yuiAcFields[id].run = false;
	},
	ajaxFailure: function (o, id) {
		for (i = 1; i < yuiAcFields[id].fields_id.length; i++) {
			document.getElementById(yuiAcFields[id].fields_id[i]).value = yuiAcFields[id].fields_val;
		}
		yuiAcFields[id].run = false;
		yuiAcFields[id].valid = false;
		YAHOO.autocoml.markNotValid(id);
		yuiAcFields[id].newval = '';
	},
	setupInstance: function (i, select, check, myInput, myContainer) {
		oACDS[i] = new YAHOO.widget.DS_XHR(ajaxURL, ['\n', '\t']);
		oACDS[i].responseType = YAHOO.widget.DS_XHR.TYPE_FLAT;
		oACDS[i].maxCacheEntries = 60;
		oACDS[i].queryMatchSubset = false;
		if (oAutoComp[i] !== undefined) {
			oAutoComp[i].destroy();
		}
		oAutoComp[i] = new YAHOO.widget.AutoComplete(myInput, myContainer, oACDS[i]);
		oAutoComp[i].queryDelay = 0;
		if (select) {
			oAutoComp[i].dataRequestEvent.subscribe(YAHOO.autocoml.doOnDataRequestEvent, i);
			oAutoComp[i].unmatchedItemSelectEvent.subscribe(YAHOO.autocoml.doOnUnmatchedItemSelectEvent, i);
			oAutoComp[i].dataErrorEvent.subscribe(YAHOO.autocoml.doOnDataErrorEvent, i);
			oAutoComp[i].dataReturnEvent.subscribe(YAHOO.autocoml.doOnDataReturnEvent, i);
		}
		if (check) {
			oAutoComp[i].containerCollapseEvent.subscribe(YAHOO.autocoml.doOnContainerCollapse, i);
			oAutoComp[i].textboxFocusEvent.subscribe(YAHOO.autocoml.doOnTextfieldFocus, i);
		}
		oAutoComp[i].formatResult = function (oResultItem, sQuery) {
			var sKey = oResultItem[0];
			var nQuantity = oResultItem[1];
			var sKeyQuery = sKey.substring(0, sQuery.length);
			if (sQuery.length > 10) {
				var path = sKeyQuery.split(' / ');
				var pPart = ' / ' + path[path.length - 1];
				if (pPart.length > (width / 15)) {
					pPart = pPart.substring(pPart.length - 10, pPart.length);
				}
				sKeyQuery = '&hellip;' + pPart;
			}
			var sKeyRemainder = sKey.substr(sQuery.length);
			if (oAutoCompRes[i] === undefined) {
				oAutoCompRes[i] = {};
			}
			oAutoCompRes[i][sKeyQuery] = oResultItem[2];
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

		if ((yuiAcFields[i].selector == 'docSelector' || yuiAcFields[i].selector == 'Docselector') && params[4] == 'folder') {
			yuiAcFields[i].valid = false;
			yuiAcFields[i].cType = params[4];
		} else {
			yuiAcFields[i].valid = true;
			yuiAcFields[i].sel = params[3];
			yuiAcFields[i].cType = params[4];
			YAHOO.autocoml.unmarkNotValid(i);
			if (yuiAcFields[i].fields_id !== undefined) {
				var yuiAcOnSelectField;
				for (j = 0; j < yuiAcFields[i].fields_id.length; j++) {
					if ((yuiAcOnSelectField = document.getElementById(yuiAcFields[i].fields_id[j])) && (typeof (params[3]) != 'undefined')) {
						yuiAcOnSelectField.value = params[3];
					}
				}
			}
		}
		yuiAcFields[i].found = 1;
		yuiAcFields[i].run = false;
		selInputVal[i] = document.getElementById(yuiAcFields[i].id).value;
		yuiAcFields[i].newval = document.getElementById(yuiAcFields[i].id).value;
		inputID = yuiAcFields[i].id;
		resultID = yuiAcFields[i].fields_id[0];
	},
	doOnTextfieldFocus: function (x, y, i) {
		ajaxResponseCT = 0;
		oldInputVal[i] = document.getElementById(yuiAcFields[i].id).value;
		if (yuiAcFields[i].fields_id !== undefined) {
			for (j = 0; j <= yuiAcFields[i].fields_id.length; j++) {
				name = yuiAcFields[i].fields_id[j];
				old[name] = document.getElementById(name).value;
			}
		}
		//	YAHOO.autocoml.unmarkNotValid(i);
		if (parent && parent.weAutoCompetionFields)
			parent.weAutoCompetionFields[yuiAcFields[i].id] = false;
		yuiAcFields[i].set = '';
	},
	doAjax: function (callback, postdata) {
		var request = YAHOO.util.Connect.asyncRequest('POST', ajaxURL, callback, postdata);
	},
	validateForm: function () {
		// Validate form inputs here
		return false;
	},
	checkFields: function () {
		for (i = 0; i < yuiAcFields.length; i++) {
			set = yuiAcFields[i];
			if (!set.valid) {
				document.getElementById(set.fields_id[0] = '');
				YAHOO.autocoml.markNotValid(i);
			}
		}
		return true;
	},
	checkRunnigProcess: function () {
		for (i = 0; i < yuiAcFields.length; i++) {
			set = yuiAcFields[i];
			if (set.run) {
				return true;
			}
		}
		return false;
	},
	markNotValid: function (setNr) {
		set = yuiAcFields[setNr];
		set.valid = false;
		set.run = false;
		var _elem = document.getElementById(set.id);
		if (_elem != null) {
			weInputAppendClass(_elem, 'weMarkInputError');
		}
		if (parent && parent.weAutoCompetionFields)
			parent.weAutoCompetionFields[setNr].valid = false;
	},
	unmarkNotValid: function (setNr) {
		set = yuiAcFields[setNr];
		set.valid = true;
		set.run = false;
		set.found = 1;
		var _elem = document.getElementById(set.id);
		if (_elem != null) {
			weInputRemoveClass(_elem, 'weMarkInputError');
		}
		if (parent && parent.weAutoCompetionFields)
			parent.weAutoCompetionFields[setNr].valid = true;
	},
	checkACFields: function () {
		if (YAHOO.autocoml.checkRunnigProcess())
			return {'running': true};
		for (i = 0; i < yuiAcFields.length; i++) {
			set = yuiAcFields[i];
			if (!set.valid) {
				return {'running': false, 'valid': false};
			}
		}
		return {'running': false, 'valid': true};
	},
	selectorSetValid: function (setFieldId) {
		for (i = 0; i < yuiAcFields.length; i++) {
			set = yuiAcFields[i];
			if (set.id == setFieldId) {
				YAHOO.autocoml.unmarkNotValid(i);
			}
		}
	},
	checkOnContainerCollapse: function (setNr) {
		set = yuiAcFields[setNr];
		if (set.set == '') {
		}
	},
	modifySetById: function (fId, param, value) {
		set = yuiAcFieldsById[fId].set;
		if (typeof param === 'object') {
			for (var name in param) {
				yuiAcFields[set][name] = yuiAcFields[set][name] !== undefined ? param[name] : yuiAcFields[set][name];
			}
		} else {
			yuiAcFields[set][param] = yuiAcFields[set][param] !== undefined ? value : yuiAcFields[set][param];
		}
		YAHOO.autocoml.init(undefined, set);
	},
	getParamById: function (fId, param) {
		set = yuiAcFieldsById[fId].set;
		return yuiAcFields[set][param];
	},
	getYuiAcFields: function () {
		return yuiAcFields;
	},
	isValidById: function (fId) {
		if (fId) {
			if (YAHOO.autocoml.counter < 10 && yuiAcFields[yuiAcFieldsById[fId].set]['run']) {
				YAHOO.autocoml.counter++;
				setTimeout('YAHOO.autocoml.isValidById(\"' + fId + '\")', 100);
			} else {
				YAHOO.autocoml.counter = 0;
				return yuiAcFields[yuiAcFieldsById[fId].set]['valid'];
			}
		} else {
			return false;
		}
	},
	counter: 0,
	isValid: function () {
		var isValid = true;
		for (fId in yuiAcFieldsById) {
			if (document.getElementById(fId).style.display != 'none' && !yuiAcFields[yuiAcFieldsById[fId].set]['valid']) {
				isValid = false;
			}
		}
		return isValid;
	},
	isRunnigProcess: function () {
		var isRunning = false;
		for (fId in yuiAcFieldsById) {
			if (document.getElementById(fId).style.display != 'none' && yuiAcFields[yuiAcFieldsById[fId].set]['run']) {
				isRunning = true;
			}
		}
		return isRunning;
	},
	setValidById: function (fId) {
		YAHOO.autocoml.unmarkNotValid(yuiAcFieldsById[fId].index);
		yuiAcFields[yuiAcFieldsById[fId].set]['valid'] = true;
	},
	setNotValidById: function (fId) {
		YAHOO.autocoml.markNotValid(yuiAcFieldsById[fId].index);
		yuiAcFields[yuiAcFieldsById[fId].set]['valid'] = false;
	},
	restoreById: function (fId) {
		set = yuiAcFieldsById[fId].set;
		YAHOO.autocoml.markValid(yuiAcFieldsById[fId].index);
		document.getElementById(fId).value = yuiAcFields[yuiAcFieldsById[fId].set]['old'];
		document.getElementById(yuiAcFields[yuiAcFieldsById[fId].set]['fields_id'][0]).value = yuiAcFields[yuiAcFieldsById[fId].set]['fields_val'][0];
	},
	setOldVal: function (set) {
	}

};