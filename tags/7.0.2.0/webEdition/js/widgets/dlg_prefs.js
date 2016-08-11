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

var _fo, _sInitCls, _oSctCls;
var _iInitCls = 0;

function initPrefs() {
	_oSctCls = _fo.elements.sct_cls;
	var iSctClsLen = _oSctCls.length;
	_sInitCls = _sCls_;
	for (var i = iSctClsLen - 1; i >= 0; i--) {
		if (_oSctCls.options[i].value == _sCls_) {
			_oSctCls.options[i].selected = true;
			_iInitCls = i;
		}
	}
}

function savePrefs() {
	opener.setTheme(_sObjId, _oSctCls[_oSctCls.selectedIndex].value);
}

function previewPrefs() {
	opener.setTheme(_sObjId, _oSctCls[_oSctCls.selectedIndex].value);
}

function exitPrefs() {
	var sTheme = _oSctCls[_oSctCls.selectedIndex].value;
	if (_sCls_ != sTheme) {
		sTheme = _oSctCls[_iInitCls].value;
		opener.setTheme(_sObjId, sTheme);
	}
}