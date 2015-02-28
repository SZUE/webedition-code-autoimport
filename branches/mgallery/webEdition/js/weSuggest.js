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
var ajaxMaxResponseTime = 1500;
var ajaxResponseStep = 100;
var ajaxResponseCT = 0;
var countMark = 0;
var selInputVal=[];
var oldInputVal=[];
var newInputVal=[];

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
